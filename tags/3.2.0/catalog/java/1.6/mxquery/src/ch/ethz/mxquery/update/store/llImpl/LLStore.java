/*   Copyright 2006 - 2009 ETH Zurich 
 *
 *   Licensed under the Apache License, Version 2.0 (the "License");
 *   you may not use this file except in compliance with the License.
 *   You may obtain a copy of the License at
 *
 *       http://www.apache.org/licenses/LICENSE-2.0
 *
 *   Unless required by applicable law or agreed to in writing, software
 *   distributed under the License is distributed on an "AS IS" BASIS,
 *   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *   See the License for the specific language governing permissions and
 *   limitations under the License.
 */

package ch.ethz.mxquery.update.store.llImpl;

import java.util.Stack;
import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.Identifier;
import ch.ethz.mxquery.datamodel.IdentifierFactory;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.Source;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.xdm.CommentToken;
import ch.ethz.mxquery.datamodel.xdm.NamedToken;
import ch.ethz.mxquery.datamodel.xdm.ProcessingInstrToken;
import ch.ethz.mxquery.datamodel.xdm.TextAttrToken;
import ch.ethz.mxquery.datamodel.xdm.TextToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.datamodel.xdm.XDMScope;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.functions.fn.Doc;
import ch.ethz.mxquery.model.EmptySequenceIterator;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.model.updatePrimitives.UpdateableStore;
import ch.ethz.mxquery.update.store.llImpl.LLStoreSet.StoreURIMapping;
import ch.ethz.mxquery.util.Set;
import ch.ethz.mxquery.xdmio.StoreSet;
import ch.ethz.mxquery.xdmio.XDMSerializer;
import ch.ethz.mxquery.xdmio.XDMSerializerSettings;
import ch.ethz.mxquery.xdmio.XMLSource;

/**
 * Saves elements of one XML store in a double linked list.
 * 
 * @author David Alexander Graf
 * 
 */
public class LLStore extends TokenList implements UpdateableStore {
	private String uri;
	private int idForNew;

	private boolean modified = false;
	
	private int referencers = 0;
	public LLStoreSet storeSet;
	private short level = 0;
	private Identifier last_identifier;

	private XDMIterator initialSource;
	private boolean materialized = false;
	
	private boolean assignNewIds = true;
	
	private Vector modifiedNodesToCheck = new Vector();
	
	public LLStore(LLStoreSet storeSet, XDMIterator initialDataIterator) {
		this.uri = null;
		this.idForNew = 1;
		this.storeSet = storeSet;
	}

	public LLStore(String uri, LLStoreSet storeSet, XDMIterator initialDataIterator) {
		this.uri = uri;
		this.idForNew = 1;
		this.storeSet = storeSet;
		initialSource = initialDataIterator;
	}

	public String getURI() {
		return this.uri;
	}

	/**
	 * Materializes the next Token.
	 * 
	 * @param it
	 *            Iterator of the materialized stream.
	 * @return materialized token
	 * @throws MXQueryException
	 */
	protected static Token getNextTokenCopy(XDMIterator it) throws MXQueryException {
		return  it.next().copy();
	}

	/**
	 * Returns the id for a new materialized token.
	 * 
	 * @return Identifier
	 */
	protected Identifier getNewIdForAppend() {
		return IdentifierFactory.createIdentifier(this.idForNew++, this, last_identifier,level);
	}

	/**
	 * Increases the passed <code>node</code> till it points on the next
	 * sibling or on the parent if it is the last sibling (it skips current
	 * item).
	 * 
	 * @param node
	 * @throws MXQueryException
	 */
	protected static Token skipCurrentItem(Token curToken, XDMIterator node)
			throws MXQueryException {
		int curDepth = 0;
		do {
			if (Type.isStartType(curToken.getEventType())) {
				curDepth++;
			} else if (Type.isEndType(curToken.getEventType())) {
				curDepth--;
			}
			curToken = node.next();
		} while (curDepth > 0);
		return curToken;
	}

	/**
	 * Checks if it is possible to insert a reference to the current node
	 * 
	 * @param node
	 *            current node
	 * @param tokenList
	 *            token list
	 * @param newElDepth
	 *            depth of the probably inserted node
	 * @param parents
	 *            parents stack
	 * @return Current Token if a reference could be created, else null
	 * @throws MXQueryException
	 */
	private Token checkForReferences(Token curToken, XDMIterator node, TokenList tokenList,
			int newElDepth, Stack parents) throws MXQueryException {
		LLRefToken srt = null;
		Identifier id = curToken.getId();
		// The Store of an identifier must be a 'Store' right now, and not a Source!!
		if (id != null && curToken.getEventType() != Type.START_DOCUMENT) {
			if (id.getStore() instanceof LLStore) {
				LLStore nodeSS = (LLStore) id.getStore();
				LLToken token = nodeSS.getToken(id);
				// there referenced node might already be deleted
				srt = new LLRefToken(token);
				curToken = LLStore.skipCurrentItem(curToken, node);
			}
		}
		if (srt == null) {
			return null;
		}

		srt.setDepth(newElDepth);
		if (parents.size() > 0) {
			srt.setParent((LLToken) parents.peek());
		} else {
			srt.setParent(this.head);
		}
		tokenList.addLast(srt);
		return curToken;
	}

	/**
	 * Materializes the node on which the passed iterator(<code>node</code>)
	 * is pointing to.
	 * 
	 * @param node
	 *            Iterator
	 * @param startDepth
	 *            depth of the root element
	 * @param insert
	 *            declares if this materialization happens for an
	 *            <code>append</code> or for an <code>insert</code>. The
	 *            difference is that for an insert, the resulting list must not
	 *            contain document-elements.
	 * @param blocksReferencing
	 *            blocks referencing
	 * @return materialized tokens in form of a token list
	 * @throws MXQueryException
	 */
	private TokenList materializeNode(XDMIterator iter, int startDepth,
			boolean insert, boolean blocksReferencing) throws MXQueryException {
		TokenList tokenList = new TokenList();
		// depth of the elements in the store
		int newElDepth = startDepth;
		// depth of the elements in the passed iterator
		int oldElDepth = startDepth;

		// Saves the parents of the current node in a stack
		Stack parents = new Stack();
		Token nextToken = Token.START_SEQUENCE_TOKEN;
		do {
			if (newElDepth == startDepth && !blocksReferencing) {
				Token newToken = null;
				if ((newToken = this.checkForReferences(nextToken, iter, tokenList, newElDepth,
						parents)) != null) {
					nextToken = newToken;
					continue;
				}
			}

			// ignore sequence- and document-elements
			int type = nextToken.getEventType();
			if (insert) {
				// ignore document tokens in case of a insert operation, just
				// update oldElDepth
				if (type == Type.START_DOCUMENT) {
					oldElDepth++;
					nextToken = LLStore.getNextTokenCopy(iter);
					continue;
				} else if (type == Type.END_DOCUMENT) {
					oldElDepth--;
					nextToken = LLStore.getNextTokenCopy(iter);
					continue;
				}
			}

			// ignore sequence tokens, just update oldElDepth
			if (type == Type.START_SEQUENCE) {
				oldElDepth++;
				if (assignNewIds)
					nextToken = LLStore.getNextTokenCopy(iter);
				else
					nextToken = iter.next();
				continue;
			} else if (type == Type.END_SEQUENCE) {
				oldElDepth--;
				if (assignNewIds)
					nextToken = LLStore.getNextTokenCopy(iter);
				else
					nextToken = iter.next();
				continue;
			}

			// an end-element has 'null' as id
			Identifier id = null;
			LLToken starter = null;
			if (Type.isEndType(type)) {
				newElDepth--;
				oldElDepth--;
				if (parents.size() >0) starter = (LLToken) parents.pop();
			} 
			if (assignNewIds) {
				id = this.getNewIdForAppend();
				nextToken.setId(id);
			}
			LLNormalToken snt = new LLNormalToken(nextToken, newElDepth);
			if (starter != null) {
				starter.setEndEl(snt);
			}
			if (parents.size() > 0) {
				snt.setParent((LLToken) parents.peek());
			} else {
				snt.setParent(this.head);
			}
			tokenList.addLast(snt);
			if (type == Type.START_TAG || type == Type.START_SEQUENCE
					|| type == Type.START_DOCUMENT) {
				newElDepth++;
				oldElDepth++;
				parents.push(snt);
			}
			nextToken = LLStore.getNextTokenCopy(iter);
		} while (oldElDepth > startDepth);

		if (oldElDepth < startDepth) {
			// if something gets wrong, it returns an empty token list
			return new TokenList();
		}
		return tokenList;
	}

	/**
	 * Searches the end tag that belongs to the passed start tag. If the passed
	 * StoreToken is not a start tag, the method returns the start tag.
	 * 
	 * @param startTag
	 *            start tag
	 * @return end tag
	 */
	private LLToken getEndTag(LLToken startTag) throws MXQueryException {
		if (startTag.getEndEl() == null) {
			return startTag;
		} else {
			return startTag.getEndEl();
		}
	}

	/**
	 * Deletes the content of the passed token (not the token itself!)
	 * 
	 * @param token
	 * @return Reinsert
	 */
	private Redelete deleteContent(LLToken token) throws MXQueryException {
		LLToken endTag = this.getEndTag(token);
		LLToken startTag = token;
		while (Type.isAttribute(startTag.getNext().getEventType())) {
			startTag = startTag.getNext();
		}
		if (startTag != endTag && startTag.getNext() != endTag) {
			Redelete ri = new Redelete(startTag.getNext(), startTag, endTag
					.getPrev(), endTag);
			startTag.setNext(endTag);
			endTag.setPrev(startTag);
			return ri;
		} else {
			return new Redelete();
		}
	}

	public void appendForInsert(XDMIterator node) throws MXQueryException {
		try {
			TokenList tokenList = this.materializeNode(node, 1, true, true);
			this.tail.insertBefore(tokenList);
		} catch (MXQueryException e) {
			this.tryGC();
			throw e;
		}
	}

//	public void append(Iterator node, boolean blockReferencing)
//			throws MXQueryException {
//		try {
//			TokenList tokenList = this.materializeNode(node, 1, false,
//					blockReferencing);
//			this.tail.insertBefore(tokenList);
//		} catch (MXQueryException e) {
//			this.tryGC();
//			throw e;
//		}
//	}

//	public void append(Iterator node) throws MXQueryException {
//		try {
//			TokenList tokenList = this.materializeNode(node, 1, false, false);
//			this.tail.insertBefore(tokenList);
//		} catch (MXQueryException e) {
//			this.tryGC();
//			throw e;
//		}
//	}
	
	public void materialize() throws MXQueryException {
		if (!materialized && initialSource != null)
		try {
			TokenList tokenList = this.materializeNode(initialSource, 1, false, true);
			this.tail.insertBefore(tokenList);
			if (initialSource instanceof Doc) {
				// this is a hack until the store management is improved
				// this store now represents this document
				// replace own uri with document URI
				// if this store is to be serialized, replace also the
				// the serialization mapping
				String docURI = ((Doc)initialSource).getDocURI();
				LLStoreSet set = (LLStoreSet)getStoreSet();
				Vector storesToSet = set.getStoresToSerialize();
				boolean added = false;
				for (int i=0;i<storesToSet.size();i++) {
					StoreURIMapping map = (StoreURIMapping)storesToSet.elementAt(i);
					if (map.uri.equals(uri)) {
						map.uri = docURI;
						added = true;
					}
				}
				if (set.serializeStores && !added) {
					set.addStoreToSerialize(this, docURI);
				}
				this.uri = docURI;
			}
			materialized = true;
		} catch (MXQueryException e) {
			this.tryGC();
			throw e;
		}
	}	
	

//	/**
//	 * TODO integrate the lazy materialisation (Problem: SIDE EFFECTS!!!!)
//	 * 
//	 * @param node
//	 * @param blockReferencing
//	 */
//	public void appendLazy(Iterator node, boolean blockReferencing) throws MXQueryException {
////		this.tail.insertBefore(new LLLazyToken(node, blockReferencing, this));
//		throw new MXQueryException("#", "Lazy materialization not supported yet!", null);
//	}
//
//	public void appendLazy(Iterator node) throws MXQueryException {
////		this.appendLazy(node, false);
//		throw new MXQueryException("#", "Lazy materialization not supported yet!", null);
//	}

	private void createError(String msg) throws DynamicException {
		throw new DynamicException(ErrorCodes.A0010_EC_LLSTORE_EXCEPTION,
				"UPDATE Error in LLStore: " + msg, null);
	}

	public void delete(Identifier targetId) throws MXQueryException {
		LLToken startTag = this.getToken(targetId);
		if (startTag == null) {
			// This happens e.g. when two deletes are executed on the same node
			return;
		}
		LLToken endTag = this.getEndTag(startTag);
		if (endTag == null) {
			this.createError("end node during deletion not found");
		}
		startTag.getPrev().setNext(endTag.getNext());
		endTag.getNext().setPrev(startTag.getPrev());

		if (this.storeSet.isAtomicMode()) {
			this.storeSet.addRollback(new Redelete(startTag, startTag.getPrev(),
					endTag, endTag.getNext()));
		}
	}

	public void insertAfter(Identifier targetId, UpdateableStore store)
			throws MXQueryException {
		materialize();
		LLStore sSource = (LLStore) store;
		LLToken startTag = this.getToken(targetId);
		if (startTag == null) {
			this.createError("insert position not found (insert after)");
		}
		LLToken endTag = this.getEndTag(startTag);
		if (endTag == null) {
			this.createError("insert position not found (insert after)");
		}
		int startDepth = endTag.getDepth();
		sSource.prepareForInsertion_After(startDepth, endTag, this, startTag
				.getParent());
		endTag.insertAfter(sSource);
		
		if (this.storeSet.isAtomicMode()) {
			this.storeSet.addRollback(new Reinsert(sSource));
		}
	}

	public void insertAttributes(Identifier targetId, UpdateableStore store)
			throws MXQueryException {
		materialize();
		LLStore sSource = (LLStore) store;
		LLToken targetNode = this.getToken(targetId);
		if (targetNode == null) {
			this.createError("insert position not found (insert attribute)");
		}
		LLToken origTarget = targetNode;
		while (targetNode.getNext().isAttribute()) {
			targetNode = targetNode.getNext();
		}
		sSource.prepareForInsertion_After(targetNode.getDepth() + 1,
				targetNode, this, targetNode);
				
		targetNode.insertAfter(sSource);

		modifiedNodesToCheck.addElement(origTarget);
		
		if (this.storeSet.isAtomicMode()) {
			this.storeSet.addRollback(new Reinsert(sSource));
		}
	}
	
	/**
	 * Check if the inserted store would create conflicting namespace binding or violate XDM constraints
	 */	
	
	public void checkNSXDM() throws MXQueryException {
		
		for (int i=0;i<modifiedNodesToCheck.size();i++) {
			int depth = 0;
			LLToken origTarget = (LLToken)modifiedNodesToCheck.elementAt(i);
			LLToken cur = origTarget.getNext();
			Set attNames = new Set();
			while (Type.isAttribute(cur.token.getEventType())) {
				Token tok = cur.token;
				if (Type.isAttribute(tok.getEventType()) && depth == 0) {
					String nm = tok.getName();
					if (attNames.contains(nm))
						throw new DynamicException(ErrorCodes.U0021_UPDATE_DYNAMIC_INVALID_XDM,"Multiple attributes with same name present after update",null);
					else
						attNames.add(nm);
				}
				cur = cur.getNext();
			}
		}
	}


	public void insertBefore(Identifier targetId, UpdateableStore store)
			throws MXQueryException {
		materialize();
		LLStore sSource = (LLStore) store;
		LLToken startTag = this.getToken(targetId);
		if (startTag == null) {
			this.createError("insert position not found (insert before)");
		}
		sSource.prepareForInsertion_Before(startTag.getDepth(), startTag, this,
				startTag.getParent());
		startTag.insertBefore(sSource);
		
		if (this.storeSet.isAtomicMode()) {
			this.storeSet.addRollback(new Reinsert(sSource));
		}
	}

	public void insertInto(Identifier targetId, UpdateableStore store)
			throws MXQueryException {
		this.insertIntoAsLast(targetId, store);
	}

	public void insertIntoAsFirst(Identifier targetId, UpdateableStore store)
			throws MXQueryException {
		materialize();
		LLToken targetNode = this.getToken(targetId);
		if (targetNode != null
				&& (targetNode.getEventType() == Type.START_TAG || targetNode
						.getEventType() == Type.START_SEQUENCE)) {
			LLStore sSource = (LLStore) store;
			while (Type.isAttribute(targetNode.getNext().getEventType())) {
				targetNode = targetNode.getNext();
			}
			sSource.prepareForInsertion_After(targetNode.getDepth() + 1,
					targetNode, this, targetNode);
			targetNode.insertAfter(sSource);
			
			if (this.storeSet.isAtomicMode()) {
				this.storeSet.addRollback(new Reinsert(sSource));
			}
		} else {
			// do nothing becauce wrong target event
			// or wrong type of input
			this
					.createError("insert position not found or at insert position is an not allowed element (insert into as first)");
		}
	}

	public void insertIntoAsLast(Identifier targetId, UpdateableStore store)
			throws MXQueryException {
		materialize();
		LLToken startNode = this.getToken(targetId);
		if (startNode != null
				&& (startNode.getEventType() == Type.START_TAG || startNode
						.getEventType() == Type.START_SEQUENCE || startNode.getEventType() == Type.START_DOCUMENT)) {
			LLStore sSource = (LLStore) store;
			LLToken endNode = this.getEndTag(startNode);
			sSource.prepareForInsertion_Before(startNode.getDepth() + 1,
					endNode, this, startNode);
			endNode.insertBefore(sSource);
			
			if (this.storeSet.isAtomicMode()) {
				this.storeSet.addRollback(new Reinsert(sSource));
			}
		} else {
			// do nothing becauce wrong target event
			this.createError("insert position not found or at insert position is an not allowed element (insert into as last)");
		}
	}

	public void rename(Identifier targetId, QName qname)
			throws MXQueryException {
		materialize();
		LLToken element = this.getToken(targetId);
		if (element == null
				|| (element.getEventType() != Type.START_TAG && !Type
						.isAttribute(element.getEventType()))&& 
						element.getEventType() != Type.PROCESSING_INSTRUCTION) {
			// do nothing, because it is only possible to rename start tags and
			// attributes
			this.createError("not allowed target (rename)");
		}

		if (element.getEventType() == Type.START_TAG) {
			LLToken endTag = this.getEndTag(element);
			if (endTag == null) {
				this.createError("end node not found (rename");
			}
			if (this.storeSet.isAtomicMode()) {
				this.storeSet.addRollback(new Rerename(element, endTag, new QName(element
						.getName())));
			}
			endTag.setName(qname);
		} else {
			if (this.storeSet.isAtomicMode()) {
				this.storeSet
						.addRollback(new Rerename(element, new QName(element.getName())));
			}
		}
		if (element.token instanceof NamedToken) {
			// Elements or Attributes
			NamedToken nt = (NamedToken)element.token;
			XDMScope oldScope = nt.getDynamicScope();
			if (qname.getNamespaceURI() != null) {
				String qnBinding = qname.getNamespaceURI();
				String oldNSBinding = oldScope.getNsURI(qname.getNamespacePrefix());
				if (oldNSBinding == null && qname.getNamespacePrefix() != null) {
					oldScope.addNamespace(qname.getNamespacePrefix(), qnBinding);
				}
				else if (!qnBinding.equals(oldNSBinding))
					throw new DynamicException(ErrorCodes.U0023_UPDATE_DYNAMIC_NEW_NAMESPACE_CONFLICT,"Conflicting namespace attributes due to insert",null);
			} 
		}
		element.setName(qname);
	}

	public void replaceNodeContent(Identifier targetId, UpdateableStore store)
			throws MXQueryException {
		materialize();
		LLToken startTag = this.getToken(targetId);
		if (startTag == null) {
			this.createError("target not found (replace node content)");
		}
		LLStore sSource = (LLStore) store;
		Redelete ri = this.deleteContent(startTag);
		sSource.prepareForInsertion_After(startTag.getDepth() + 1, startTag,
				this, startTag);
		while (Type.isAttribute(startTag.getNext().getEventType())) {
			startTag = startTag.getNext();
		}
		startTag.insertAfter(sSource);
		Reinsert rd = new Reinsert(sSource);
		
		if (this.storeSet.isAtomicMode()) {
			this.storeSet.addRollback(new Rereplace(rd, ri));
		}
	}

	public void replaceNode(Identifier targetId, UpdateableStore store)
			throws MXQueryException {
		materialize();
		LLToken targetNode = this.getToken(targetId);
		if (targetNode == null) {
			this.createError("target node not found (replace node)");
		}
		
		LLToken origTarget = targetNode.getParent();
		
		LLStore sSource = (LLStore) store;
		int depth = targetNode.getDepth();
		LLToken prevNode = targetNode.getPrev();

		// delete acutal content
		LLToken endNode = this.getEndTag(targetNode);
		if (endNode == null) {
			this.createError("end node not found (replace node)");
		}
		Redelete ri = new Redelete(targetNode, prevNode, endNode, endNode
				.getNext());
		prevNode.setNext(endNode.getNext());
		endNode.getNext().setPrev(prevNode);

		// add new content
		sSource.prepareForInsertion_After(depth, prevNode, this,
				targetNode.parent);
		prevNode.insertAfter(sSource);
		
		if (origTarget != null)
			modifiedNodesToCheck.addElement(origTarget);
		
		Reinsert rd = new Reinsert(sSource);
		
		if (this.storeSet.isAtomicMode()) {
			this.storeSet.addRollback(new Rereplace(rd, ri));
		}
	}

	public void replaceValue(Identifier targetId, String value)
			throws MXQueryException {
		materialize();
		LLToken targetNode = this.getToken(targetId);
		if (targetNode == null) {
			this.createError("target node found (replace value)");
		}
		if (this.storeSet.isAtomicMode()) {
			this.storeSet.addRollback(new RereplaceValue(targetNode, targetNode
					.getToken()));
		}
		Token old = targetNode.getToken();
		int ev = old.getEventType();
		Token newToken = null;
		switch (ev) {
		case Type.COMMENT:
			newToken = new CommentToken(old.getId(), value, old.getDynamicScope());
			break;
		case Type.PROCESSING_INSTRUCTION:
			newToken = new ProcessingInstrToken(old.getId(),value,old.getName(),old.getDynamicScope());
		default:
			// Does not consider stronger typing
			// TODO: Refine with schema typing
			if (Type.isAttribute(ev))
				newToken = new TextAttrToken(old.getId(), value, new QName(old.getName()),old.getDynamicScope());
			if (Type.isTextNode(ev))
				ev = Type.getTextNodeValueType(ev);
			if (Type.isAtomicType(ev, null))
				newToken = new TextToken(Type.createTextNodeType(ev),old.getId(),value,old.getDynamicScope());
		}
		if (newToken == null)
			throw new DynamicException(ErrorCodes.A0002_EC_NOT_SUPPORTED, "Replacing the value of type "+ Type.getTypeQName(ev, Context.getDictionary())+" not supported yet", null);
		targetNode.setToken(newToken);
	}

	public boolean containsTopAttrs() throws MXQueryException {
		materialize();
		LLToken st = this.head.getNext();
		while (st != this.tail) {
			if (st.getDepth() == 1 && Type.isAttribute(st.getEventType())) {
				return true;
			}
			st = st.getNext();
		}
		return false;
	}

	public boolean containsOnlyAttrs() throws MXQueryException {
		materialize();
		LLToken st = this.head.getNext();
		while (st != this.tail) {
			if (!Type.isAttribute(st.getEventType())) {
				return false;
			}
			st = st.getNext();
		}
		return true;
	}

	public UpdateableStore pullAttributes() throws MXQueryException {
		materialize();
		LLStore ss = new LLStore(this.storeSet,null);
		while (this.head.getNext() != this.tail
				&& Type.isAttribute(this.head.getNext().getEventType())) {
			ss.addLast(this.pullFirst());
		}
		return ss;
	}

	/**
	 * Finds the left element of the target position and invokes (indirectly)
	 * <code>prepareForInsertion</code>.
	 * 
	 * @param startDepth
	 * @param target
	 * @param newStore
	 * @param parent
	 */
	private void prepareForInsertion_Before(int startDepth, LLToken target,
			UpdateableStore newStore, LLToken parent) throws MXQueryException {
		this.prepareForInsertion_Preparation(startDepth, target.getPrev(),
				target, newStore, parent);
	}

	/**
	 * Finds the right element of the target position and invokes (indirectly)
	 * <code>prepareForInsertion</code>.
	 * 
	 * @param startDepth
	 * @param target
	 * @param newStore
	 * @param parent
	 */
	private void prepareForInsertion_After(int startDepth, LLToken target,
			UpdateableStore newStore, LLToken parent) throws MXQueryException {
		this.prepareForInsertion_Preparation(startDepth, target, target
				.getNext(), newStore, parent);
	}

	/**
	 * Preparation of the "Insert Preparation". Finds the identifier of the
	 * first element left ot the insertion position that has an id, the
	 * identifier of the first element right to the insertion position that has
	 * an id, and invokes <code>prepareForInsertion</code>.
	 * 
	 * @param startDepth
	 *            depth where the elements are inserted (means that the top
	 *            elements have <code>startDepth</code> as new depth).
	 * @param startToken
	 *            token left to the insertion position
	 * @param endToken
	 *            token right to the insertion position
	 * @param newStore
	 *            store where the elements are going to be inserted
	 * @param parent
	 *            new parent element of the top elements
	 */
	private void prepareForInsertion_Preparation(int startDepth,
			LLToken startToken, LLToken endToken, UpdateableStore newStore,
			LLToken parent) throws MXQueryException {
		materialize();
		while (!(startToken instanceof LLStartToken)
				&& startToken.getId() == null) {
			startToken = startToken.getPrev();
		}
		while (!(endToken instanceof LLEndToken) && endToken.getId() == null) {
			endToken = endToken.getNext();
		}
		this.prepareForInsertion(startDepth, startToken.getId(), endToken
				.getId(), newStore, parent);
	}

	/**
	 * Prepares elements of <code>this</code> store to be inserted into
	 * a new store (<code>newSource</code>): depth update, identifier
	 * update, and parent update (of the top elements).
	 * 
	 * @param startDepth
	 *            depth where the elements are inserted (means that the top
	 *            elements have <code>startDepth</code> as new depth).
	 * @param leftBoundId
	 *            the identifier of the first element left to the insertion
	 *            position that has an id.
	 * @param rightBoundId
	 *            the identifier of the first element right to the insertion
	 *            position that has an id.
	 * @param newStore
	 *            store where the elements are going to be inserted
	 * @param parent
	 *            new parent element of the top elements
	 */
	private void prepareForInsertion(int startDepth, Identifier leftBoundId,
			Identifier rightBoundId, UpdateableStore newStore, LLToken parent)
			throws MXQueryException {
		materialize();
		if (!this.isEmpty()) {
			int count = this.countIdiedEls();
			//short[] levels = this.countLevels(); FIXME: Currently broken, ask Julia
			Identifier[] newIds = IdentifierFactory.createInsertIdentifiers(
					leftBoundId, rightBoundId, count + 1, newStore, null /*levels*/);
			int diff = startDepth - this.getFirstToken().getDepth();

			LLToken cur = this.getFirstToken();
			
			int step = 0;
			int top = cur.getDepth();
			while (cur != this.tail) {
				int depth = cur.getDepth();
				//boolean nsScopeChanged = false;
				if (depth == top) {
					cur.setParent(parent);
					// Namespace scoping fix - first version
					if (Type.isAttribute(cur.token.getEventType())) {
						NamedToken nt = (NamedToken)cur.token;
						XDMScope parentScope = parent.getToken().getDynamicScope();
						if (nt.getPrefix() != null) {
							String attrNs = nt.getNS();
							String parentNSBinding = parentScope.getNsURI(nt.getPrefix());
							if (parentNSBinding == null) {
								// parent gets a new scope
								LLToken grandparent = parent.getParent();
								XDMScope gpScope = null;
								 if (grandparent != null && grandparent.token instanceof NamedToken) {
									NamedToken gpToken = (NamedToken) grandparent.token;
									gpScope = gpToken.getDynamicScope();
								}
								XDMScope newElemScope = new XDMScope(gpScope);
								newElemScope.addNamespace(nt.getPrefix(), attrNs);
								nt.setDynamicScope(newElemScope);
								((NamedToken)parent.getToken()).setDynamicScope(newElemScope);
								//TODO: updated up nested nodes of modified parent
							}
							else if (!attrNs.equals(parentNSBinding))
								throw new DynamicException(ErrorCodes.U0023_UPDATE_DYNAMIC_NEW_NAMESPACE_CONFLICT,"Conflicting namespace attributes due to insert",null);
							else
								nt.setDynamicScope(parentScope);
						} else 
							nt.setDynamicScope(parentScope);
						
					} else 
					if (cur.token instanceof NamedToken && parent.getToken() instanceof NamedToken) {
							NamedToken nt = (NamedToken)cur.token;
							XDMScope ns = nt.getDynamicScope();
							if (ns.getAllNamespaces().size() == 1) // no definitions around, use parent's scope
								nt.setDynamicScope(((NamedToken)parent.getToken()).getDynamicScope());
							else //FIXME: proper Scope merging, similar to XMLContent
								nt.getDynamicScope().setParent(((NamedToken)parent.getToken()).getDynamicScope());
							
					}
				}
				// on nested element
				if (depth > top && cur.token instanceof NamedToken) {
					NamedToken nt = (NamedToken) cur.token;
					XDMScope ns = nt.getDynamicScope();
					if (ns.getAllNamespaces().size() == 1) // no definitions around, use parent's scope
						nt.setDynamicScope(((NamedToken)parent.getToken()).getDynamicScope());
					else //FIXME: proper Scope merging
						nt.getDynamicScope().setParent(((NamedToken)parent.getToken()).getDynamicScope());
				}
				cur.setDepth(depth + diff);
				if (Type.isIdeedType(cur.getEventType()) || cur.getEventType() == Type.END_TAG) {
					cur.setId(newIds[step]);
					step++;
				}
				cur = cur.getNext();
			}
		}
	}
	
	public Window getIterator(Context ctx) throws MXQueryException{
		Window wnd = new LLStoreIterator(null,this);
			wnd.setContext(ctx, false);
		return wnd;
	}
	public Window getIteratorForId(Identifier identifier) throws MXQueryException {
		
		return new LLStoreIterator(identifier, this);
	}

	public XDMIterator getParentIterator (Identifier id) throws MXQueryException {
		LLToken targetNode = this.getToken(id);
		if (targetNode.parent != null) {
			LLStoreIterator llSt = new LLStoreIterator(targetNode.parent.getId(), this);
			return llSt.nextItem();
		}
		else
			return new EmptySequenceIterator(null,null);
	}
	
	/**
	 * Inserts <code>token</code> at first position.
	 * 
	 * @param token
	 */
	void insertFirst(LLToken token) throws MXQueryException {
		this.head.insertAfter(token);
	}

	/**
	 * Inserts <code>token</code> at last position.
	 * 
	 * @param token
	 */
	void insertLast(LLToken token) {
		this.tail.insertBefore(token);
	}

	synchronized void referenceAdded() {
		if (this.uri == null) {
			// Does nothing if the Source is not safed in a Store
			// (This is the case for Data that is inserted in another store)
			return;
		}
		this.referencers++;
		//System.out.println("Ref added for store "+uri+":"+referencers);
	}

	synchronized void referenceDeleted() {
		if (this.uri == null) {
			// Does nothing if the Source is not safed in a Store
			// (This is the case for Data that is inserted in another store)
			return;
		}
		this.referencers--;
		//System.out.println("Ref deleted for store "+uri+":"+referencers);

		
		this.tryGC();
	}

	synchronized void tryGC() {
		if (this.referencers == 0) {
			if (this.storeSet != null) {
				this.storeSet.removeStore(this);
			}
			this.storeSet = null;
//			if (this.uri.equals("SimpleStore_1")) {
//				System.out.println("GC run "+uri);
//			}
		}
	}

	/**
	 * Returns the number of StoreIterators that are referencing this source.
	 * 
	 * @return
	 */
	synchronized int getNrOfReferencers() {
		return this.referencers;
	}

	public StoreSet getStoreSet() {
		return this.storeSet;
	}

	public String toString(int addDepth) {
		XDMSerializerSettings ser = new XDMSerializerSettings();
		ser.setOmitXMLDeclaration(true);
		XDMSerializer ip = new XDMSerializer(ser);
		LLStoreIterator si = new LLStoreIterator(this.head.getId(), this);
		try {
			return  ip.eventsToXML(si);
		} catch (MXQueryException e) {
			return "ERROR: Could not generate string represenation of store";
		}
	}

	public String toString() {
		return this.toString(0);
	}
	
	public int compare(Source store) {
		if (store.getURI() != null) {
			return this.uri.compareTo(store.getURI());
		} else {
			return -2;
		}
	}
	
	public Source copySource(Context ctx, Vector nestedPredCtxStack) {
		return new LLStore(uri, (LLStoreSet) storeSet.copy(),null);
	}

	public boolean isModified() {
		return modified;
	}

	public void setModified(boolean modVal) {
		modified = modVal;
		
	}

	public String getSystemID() {
		if (initialSource != null && initialSource instanceof XMLSource) {
			return ((XMLSource)initialSource).getSystemID();
		}
		return null;
	}

	public String getPublicID() {
		if (initialSource != null && initialSource instanceof XMLSource) {
			return ((XMLSource)initialSource).getSystemID();
		}
		return null;
	}

	public String getDoctypeRootElem() {
		if (initialSource != null && initialSource instanceof XMLSource) {
			return ((XMLSource)initialSource).getRootElemDTD();
		}
		return null;
	}
	
	public void setAssignNewIds(boolean assignNewIds) {
		this.assignNewIds = assignNewIds;
	}
	
	public LLStore getNewStoreForSequence(int startPosition, int endPosition)
	throws MXQueryException {
		StoreSet store = getStoreSet();
		LLStore newSource = (LLStore) store.createUpdateableStore(null,null,true, false);

		LLToken cur = head.getNext();
		for (int i = 0; i < startPosition - 1; i++) {
			if ((cur = LLToken.getSibling(cur)) == null) {
				return newSource;
			}
		}

		for (int i = startPosition; i <= endPosition; i++) {
			if (cur == null) {
				return newSource;
			}
			LLRefToken ref;
			if (cur instanceof LLRefToken) {
				ref = new LLRefToken(((LLRefToken) cur).getRef());
			} else {
				ref = new LLRefToken(cur);
			}
			newSource.insertLast(ref);
			cur = LLToken.getSibling(cur);
		}
		return newSource;
	}

	public void mergeCleanTextNodes() {
		// TODO Auto-generated method stub
	}

	
}

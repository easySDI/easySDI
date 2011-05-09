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

/**
 * @author Rokas Tamosevicius
 */

package ch.ethz.mxquery.iterators;

import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.Namespace;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.BooleanToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.exceptions.StaticException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.CheckNodeType;
import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.util.KXmlSerializer;

public class SequenceTypeIterator extends CurrentBasedIterator {
	
	private boolean streaming = false;
	private boolean atomicTypeTest = true;
	private boolean checkTokensType = true;
	private TypeInfo tInfo = null;
	private boolean promote = false;

	private String node_name = null;
	private String node_name_uri = null;	
	private int itemCount = 0;

	
	/**
	 * @param ctx TODO
	 * 
	 */
	public SequenceTypeIterator(TypeInfo tInfo, boolean streaming, boolean promote, Context ctx, QueryLocation location){
		super(ctx, location);
		this.tInfo = tInfo;
		this.streaming = streaming;
		this.promote = promote;
		if ( ! Type.isAtomicType(tInfo.getType(), Context.getDictionary()) ) {
			atomicTypeTest  = false;
		}
	}

	public Token next() throws MXQueryException {

		if (this.endOfSeq) {
			return Token.END_SEQUENCE_TOKEN;
		}
		
		if (itemCount == 0) {
			this.current = subIters[0];		
		}
		
		if (streaming)
			return checkSequenceTypeStreaming();
		else {
			boolean res = checkSequenceTypeNonStreaming();
			endOfSeq = true;
			if (res)
				return BooleanToken.TRUE_TOKEN;
			else return BooleanToken.FALSE_TOKEN;
		}
		
	}

	/** method  copied  from ChildIterator and little bit modified */
	private void init() throws MXQueryException {

		node_name = tInfo.getName();
		node_name_uri = tInfo.getNameSpaceURI();				
			
		if (node_name == null)
			return;
		
			String str[] = QName.parseQName(node_name);
			node_name = str[1];
			if (str[0] != null) {
				if (str[0].equals("*")) {
					this.node_name_uri = "*";
				} else {
					Namespace ns = this.context.getNamespace(str[0]);
					if (ns != null)
						node_name_uri = ns.getURI();
					else
						throw new StaticException(ErrorCodes.E0081_STATIC_QUERY_UNKNOWN_NAMESPACE, "Namespace not known", loc);
				}
			}
	}//end
	
	/** Methods related to streaming type verification. E.g. for 'variable type declaration'  */
	
	private Token getItemsNextToken() throws MXQueryException {
		int prevDepth = this.depth;
		Token t = getNext();
		if (prevDepth == 0 && (this.depth == 0 || this.depth == 1) ){
			checkTokensType = true;
		}
		else {
			checkTokensType = false;
		}	
		return t;
	}	
		
	private Token checkSequenceTypeStreaming() throws MXQueryException {
		Token tok = null;
		if ( atomicTypeTest ) {
			tok = current.next();
		} else {
			tok = getItemsNextToken();
			if (!checkTokensType) return tok;
		}
	
		switch (itemCount) {
		case 0: {
			if ( !atomicTypeTest ) init();
			
			if ( !isFirstItemFine(tok)) {
				int typeAnn = tInfo.getTypeAnnotation();
				String name = node_name;
				String[] tokens = QName.parseQName(node_name);
				if (tokens.length > 1) name = tokens[1];
				if (typeAnn == 39 || typeAnn == 35 )
					throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Sequence Type Matching Failed",loc);
				else if (typeAnn != -1&& name!=null&& !name.equals("*"))
					throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Sequence Type Matching Failed: Expected  {"+node_name_uri+"}:"+name+" with type: "+Type.getTypeQName(tInfo.getTypeAnnotation(), Context.getDictionary())+", encountered  {"+tok.getNS()+"}:"+tok.getName()+" with type "+Type.getTypeQName(tok.getTypeAnnotation(), Context.getDictionary()), loc);
				else if (typeAnn != -1 && name =="*" || name ==null)
					throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Sequence Type Matching Failed: Incorrect type: Expected "+Type.getTypeQName(typeAnn, Context.getDictionary())+", encountered  type :"+Type.getTypeQName(tok.getTypeAnnotation(), Context.getDictionary()), loc);
				else throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Sequence Type Matching Failed : Incorrect type: Expected "+Type.getTypeQName(tInfo.getType(), Context.getDictionary())+" encountered: "+Type.getTypeQName(tok.getEventType(), Context.getDictionary()), loc);
			}
			else return tok;
		}	
		case 1: {
			if ( tok.getEventType() == Type.END_SEQUENCE) {
				endOfSeq = true;
				return tok;
			}
			
			if (!isSecondItemFine(tok))
				throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Inappropriate type", loc);
			else return tok;
		}	
		case 2: {
			if ( tok.getEventType() == Type.END_SEQUENCE) {
				endOfSeq = true;
				return tok;
			}
			
			if ( atomicTypeTest ) {
				if ( !isAtomicTypeItemFine(tok))
					throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Inappropriate type", loc);
				else return tok;
			} else {
				if ( !isNodeKindTypeItemFine(tok))
					throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Inappropriate type", loc);
				else return tok;				
			}	
		}
		default :
			throw new MXQueryException(ErrorCodes.A0009_EC_EVALUATION_NOT_POSSIBLE, "SequenceType: itemCount should be 0, 1 or 2", loc);
		}
	}	
	
	/** Methods related to immediate (non streaming) type verification. E.g. for 'instance of'  */
	
	// TODO Peter is it possible to use index ???
	/**  used for non streaming type verification */

	private Token moveToNextItem() throws MXQueryException {
		int prevDepth = this.depth;
		Token t = getNext();
		if ( prevDepth == 0 && (this.depth == 0 || this.depth == 1) )
			return t;
		
		while (this.depth > 0) {
			t = getNext();
		}
		
		if (t.getEventType() == Type.END_TAG || t.getEventType() == Type.END_DOCUMENT)
			t = getNext();
		return t;
	}
	
	private Token getNextTokenNonStreaming() throws MXQueryException {
		Token t = null;
		if ( atomicTypeTest ) {
			t = current.next();
		} else {
			t = moveToNextItem();
		}
		return t;
	}
	
	private boolean checkSequenceTypeNonStreaming() throws MXQueryException {
		Token tok;
		
		if ( !atomicTypeTest ) init();

		// 1-st item
		tok = getNextTokenNonStreaming();
		
		if ( ! isFirstItemFine(tok)) return false;
		
		// 2-nd item		
		tok = getNextTokenNonStreaming();
		
		if ( tok.getEventType() == Type.END_SEQUENCE) return true;
		
		if ( ! isSecondItemFine(tok)) return false;
			
		// 3-rd and subsequent items
		while ((tok = getNextTokenNonStreaming()).getEventType() != Type.END_SEQUENCE){

			if (atomicTypeTest) {
				if ( !isAtomicTypeItemFine(tok))
					return false;				
			
			} else {
				if ( !isNodeKindTypeItemFine(tok))
					return false;
			}
		}//while
		
		return true;
	}	
	
	/** Methods related to token's type verification:  */ 
	
	private boolean isFirstItemFine(Token tok) throws MXQueryException {
		if ( tok.getEventType() == Type.END_SEQUENCE) {
			if (tInfo.getType() == Type.TYPE_NK_EMPTY_SEQ_TEST)
				return true;
			
			if (tInfo.getOccurID() == Type.OCCURRENCE_IND_ZERO_OR_ONE ||
				tInfo.getOccurID() == Type.OCCURRENCE_IND_ZERO_OR_MORE ||
				tInfo.getOccurID() == Type.OCCURRENCE_IND_INFINITIVE )
				return true;
			else 
				return false;
		}	

		itemCount++;
		if (this.atomicTypeTest)
			return isAtomicTypeItemFine(tok);
		else
			return isNodeKindTypeItemFine(tok);
	}
	
	private boolean isSecondItemFine(Token tok) throws MXQueryException {	

		itemCount++;
		if (tInfo.getOccurID() == Type.OCCURRENCE_IND_ZERO_OR_ONE ||
			tInfo.getOccurID() == Type.OCCURRENCE_IND_EXACTLY_ONE)
			return false;

		if (this.atomicTypeTest)
			return isAtomicTypeItemFine(tok);
		else
			return isNodeKindTypeItemFine(tok);
	}
	
	private boolean isAtomicTypeItemFine(Token t) {
		int tokenType = t.getEventType();
		if( tInfo.getType() == tokenType)
			return true;
		if (promote) {
			if (Type.typePromoteableTo(tokenType, tInfo.getType(), Context.getDictionary()))
				return true;
		} else {
			if (Type.isSubTypeOf(tokenType, tInfo.getType(), Context.getDictionary()))
				return true;
		}
		return false;
	}

	private boolean isNodeKindTypeItemFine(Token tok) throws MXQueryException {
		int type = tInfo.getType();
		
		switch(type) {
		case Type.PROCESSING_INSTRUCTION:
			return CheckNodeType.checkPI(tok, tInfo.getName());
		case Type.COMMENT:
			return (CheckNodeType.checkComment(tok));
		case Type.TYPE_NK_TEXT_TEST:
			return CheckNodeType.checkText(tok);
		case Type.TYPE_NK_ANY_NODE_TEST:
			return CheckNodeType.checkNode(tok);
		case Type.TYPE_NK_SCHEMA_ELEM_TEST:
			return CheckNodeType.checkSchemaElement(tok, node_name_uri, node_name, Context.getDictionary(),loc);
		case Type.TYPE_NK_SCHEMA_ATTR_TEST:
			return CheckNodeType.checkSchemaAttribute(tok, node_name_uri, node_name, Context.getDictionary(),loc);	
		case Type.START_TAG:
			return CheckNodeType.checkElement(tok, node_name_uri, node_name,tInfo.getTypeAnnotation(), Context.getDictionary());
		case Type.TYPE_NK_DOC_TEST:
			return CheckNodeType.checkDocument(tok);
		case Type.ITEM:
			return true;
		case Type.TYPE_NK_EMPTY_SEQ_TEST:
			return false;			
		default:
			if (Type.isAttribute(type))
				return CheckNodeType.checkAttributeWithType(tok, null, node_name,tInfo.getTypeAnnotation(), Context.getDictionary());
			else if ((type & Type.TYPE_NK_DOC_TEST) != 0) { // DOCUMENT_ELEMENT test
				// for now, check document only
				System.out.println("Document_Element check!");
				return CheckNodeType.checkDocument(tok);

			}
			else {
				// Elements with additional type -> look at annotation
				boolean isCorrect = Type.isTypeOrSubTypeOf(tok.getTypeAnnotation(), type, Context.getDictionary());
				return isCorrect;
			}
		}
	}	
	

	protected void resetImpl() throws MXQueryException {
		// TODO Auto-generated method stub
		itemCount = 0;
		super.resetImpl();
	}	

	public TypeInfo getStaticType() {
		return tInfo;
	}		
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		SequenceTypeIterator copy = new SequenceTypeIterator(tInfo.copy(), streaming, promote, context,loc);
		copy.setSubIters(subIters);
		return copy;
	}
	protected KXmlSerializer createIteratorStartTag(KXmlSerializer serializer)
	throws Exception {
		super.createIteratorStartTag(serializer);
		serializer.attribute(null, "type", tInfo.toString());
		serializer.attribute(null, "promote", new Boolean(promote).toString());
		serializer.attribute(null, "streaming", new Boolean(streaming).toString());
		return serializer;
	}
	
}

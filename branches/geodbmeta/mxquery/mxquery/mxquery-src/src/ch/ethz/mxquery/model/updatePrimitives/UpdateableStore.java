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

package ch.ethz.mxquery.model.updatePrimitives;

import ch.ethz.mxquery.datamodel.Identifier;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.Source;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.xdmio.StoreSet;

/**
 * Store of an XQuery-Data-Model-tree.
 * 
 * @author David Alexander Graf
 * 
 */
public interface UpdateableStore extends Source {

	public void materialize() throws MXQueryException;
	
	/**
	 * Appends the passed not to the store. "ForInsert" means that this
	 * Store is used to insert data in another Store. The Difference
	 * to the normal append node is that an insert sequence must no contain
	 * document nodes => a document node is replaced by its children.
	 * 
	 * @param node
	 * @throws MXQueryException
	 */
	public void appendForInsert(XDMIterator node) throws MXQueryException;

	
	/**
	 * Returns Iterator that points on the token with the passed identifier in
	 * the store.
	 * 
	 * @param identifier
	 * @return Returns null if the token of the passed <code>identifier</code>
	 *         cannot be found.
	 * @throws MXQueryException
	 */
	public Window getIteratorForId(Identifier identifier)
			throws MXQueryException;

	/**
	 * Get a window/iterator of the parent 
	 * @param id Node to take as base for the parent
	 * @return Iterator providing the parent, EmptySequence if there is none
	 * @throws MXQueryException
	 */
	public XDMIterator getParentIterator (Identifier id) throws MXQueryException;
	
	/**
	 * Returns the Identifier of the event with the passed identifier.
	 * 
	 * @param identifier
	 * @return parent identifier
	 * @throws MXQueryException
	 */
	public Identifier getParentId(Identifier identifier)
			throws MXQueryException;

	/**
	 * Returns true if the event with the passed identifier has a parent.
	 * 
	 * @param identifier
	 * @return true for has parent
	 * @throws MXQueryException
	 */
	public boolean hasParent(Identifier identifier) throws MXQueryException;

	/**
	 * Inserts <code>node</code> before the element with the id
	 * <code>targetId</code> into the store.
	 * 
	 * @param targetId
	 *            element identifier
	 * @param store
	 *            some data (XQuery Data Model)
	 * @throws MXQueryException
	 */
	public void insertBefore(Identifier targetId, UpdateableStore store)
			throws MXQueryException;

	/**
	 * Inserts <code>node</code> after the element with the id
	 * <code>targetId</code> into the store.
	 * 
	 * @param targetId
	 *            element identifier
	 * @param store
	 *            some data (XQuery Data Model)
	 * @throws MXQueryException
	 */
	public void insertAfter(Identifier targetId, UpdateableStore store)
			throws MXQueryException;

	/**
	 * Inserts <code>node</code> into the element with the id
	 * <code>targetId</code>.
	 * 
	 * @param targetId
	 *            element identifier
	 * @param store
	 *            some data (XQuery Data Model)
	 * @throws MXQueryException
	 */
	public void insertInto(Identifier targetId, UpdateableStore store)
			throws MXQueryException;

	/**
	 * Inserts <code>node</code> into the element with the id
	 * <code>targetId</code> at first position.
	 * 
	 * @param targetId
	 *            element identifier
	 * @param store
	 *            some data (XQuery Data Model)
	 * 
	 * @throws MXQueryException
	 */
	public void insertIntoAsFirst(Identifier targetId, UpdateableStore store)
			throws MXQueryException;

	/**
	 * Inserts <code>node</code> into the element with the id
	 * <code>targetId</code> at last position.
	 * 
	 * @param targetId
	 *            element identifier
	 * @param store
	 *            some data (XQuery Data Model)
	 * @throws MXQueryException
	 */
	public void insertIntoAsLast(Identifier targetId, UpdateableStore store)
			throws MXQueryException;

	/**
	 * Inserts all attributes of <code>node</code> into the XML-start-tag that
	 * has the identifier <code>targetId</code>.
	 * 
	 * @param targetId
	 *            element identifier
	 * @param store
	 *            attributes to insert (one attribute or a sequence of
	 *            attributes)
	 * 
	 * @throws MXQueryException
	 */
	public void insertAttributes(Identifier targetId, UpdateableStore store)
			throws MXQueryException;

	/**
	 * Deletes the element that has the identifier <code>targetId</code>.
	 * 
	 * @param targetId
	 *            element identifier
	 * @throws MXQueryException
	 */
	public void delete(Identifier targetId) throws MXQueryException;

	/**
	 * Replaces the element with the identifier <code>targetId</code> with
	 * <code>node</code>.
	 * 
	 * @param targetId
	 *            element identifier
	 * @param store
	 *            some data (XQuery Data Model)
	 * @throws MXQueryException
	 */
	public void replaceNode(Identifier targetId, UpdateableStore store)
			throws MXQueryException;

	/**
	 * Replaces the string value of the element with the identifier
	 * <code>targetId</code> with <code>stringValue</code>.
	 * 
	 * @param targetId
	 *            element identifier (element must be an attribute, text,
	 *            comment, or processing instruction node)
	 * @param stringValue
	 *            data that contains a string
	 * @throws MXQueryException
	 */
	public void replaceValue(Identifier targetId, String stringValue)
			throws MXQueryException;

	/**
	 * Replaces the content of the element with the identifier
	 * <code>targetId</code> with <code>text</code>.
	 * 
	 * @param targetId
	 *            element identifier (element must be an XML-node)
	 * @param store
	 *            contains per facility only one text element (string, int, ...)
	 * @throws MXQueryException
	 */
	public void replaceNodeContent(Identifier targetId, UpdateableStore store)
			throws MXQueryException;

	/**
	 * Renames the element with the identifier <code>targetId</code>.
	 * 
	 * @param targetId
	 *            element identifier (element must be an XML-node, or processing
	 *            instruction node)
	 * @param qname
	 *            new name
	 * @throws MXQueryException
	 */
	public void rename(Identifier targetId, QName qname)
			throws MXQueryException;

	/**
	 * A store represents a sequence of elements. This methods returns a
	 * new store that contains all attributes that are at the beginning of
	 * this sequence (the sequence that is represented by <code>this</code>
	 * store) and deletes them in <code>this</code> store.
	 * 
	 * @return store of attributes
	 * @throws MXQueryException
	 */
	public UpdateableStore pullAttributes() throws MXQueryException;

	/**
	 * Returns true if the sequence that is represented by <code>this</code>
	 * store contains top attributes. Top means attributes that are saved
	 * directly in the sequence and not indirectly inside other elements.
	 * 
	 * @return true/false
	 * @throws MXQueryException
	 */
	public boolean containsTopAttrs() throws MXQueryException;

	/**
	 * Does this store only contain attributes?
	 * 
	 * @return true if this sequence contains only attributes
	 * @throws MXQueryException
	 */
	public boolean containsOnlyAttrs() throws MXQueryException;

	/**
	 * Is this store empty?
	 * 
	 * @return true if empty
	 * @throws MXQueryException
	 */
	public boolean isEmpty() throws MXQueryException;

	/**
	 * Counts the items that are saved in this store.
	 * 
	 * @return number of events
	 * @throws MXQueryException
	 */
	public int count() throws MXQueryException;
	
	/**
	 * Set the modification state of the store
	 */
	
	public void setModified(boolean modVal);
	/**
	 * Has this store been modified since it has been initially loaded?
	 * @return true if the store has been modified
	 */
	public boolean isModified();
	
	/**
	 * If the store contains a document with a docdecls, return the system id
	 * @return Contents of the System ID, if present, otherwise null
	 */
	public String getSystemID();
	/**
	 * If the store contains a document with a docdecls, return the public id
	 * @return Contents of the Public ID, if present, otherwise null
	 */
	public String getPublicID();
	
	/**
	 * If the store contains a document with a docdecls, return the root element definition
	 * @return Contents of the root element name, if present, otherwise null
	 */
	public String getDoctypeRootElem();
	
	/**
	 * Get the StoreSet that the store belongs to
	 * @return the owning StoreSet 
	 */
	StoreSet getStoreSet();

	/**
	 * Merges and/or removes multiple adjacent or empty text nodes under the same element
	 */
	public void mergeCleanTextNodes();
	
	/**
	 * Check if the store has conflicting namespace binding or violates XDM constraints
	 */	
	public void checkNSXDM() throws MXQueryException;

	
	
}

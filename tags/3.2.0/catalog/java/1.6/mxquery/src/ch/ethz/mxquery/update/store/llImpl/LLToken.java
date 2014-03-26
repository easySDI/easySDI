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

import ch.ethz.mxquery.datamodel.Identifier;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.xdm.NamedToken;
import ch.ethz.mxquery.datamodel.xdm.ProcessingInstrToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;

/**
 * Baseclass of the elements that a token list resp. an XML store can contain.
 * 
 * @author David Alexander Graf
 * 
 */
public abstract class LLToken {
	protected Token token;
	protected LLToken prev;
	protected LLToken next;
	// parent of this token
	protected LLToken parent;
	// if this is a starting event (start element, start document), then the end
	// token is the ending event.
	protected LLToken endToken;

	public LLToken(Token token) {
		this.token = token;
	}

	/**
	 * Sets token.
	 * 
	 * @param token
	 */
	void setToken(Token token) {
		this.token = token;
	}

	/**
	 * Gets token
	 * 
	 * @return current token
	 */
	Token getToken() {
		return this.token;
	}

	/**
	 * Constructor for SimpleStartToken and SimpleEndToken
	 * 
	 */
	public LLToken() {
	}

	public final LLToken getPrev() {
		return this.prev;
	}

	public abstract void setPrev(LLToken prev);

	/**
	 * During getNext must be checked if tokens must be materialized.
	 * 
	 * @return the next token
	 */
	public final LLToken getNext() throws MXQueryException {
//		while (this.next instanceof LLLazyToken) {
//			LLLazyToken lt = (LLLazyToken) this.next;
//			lt.materializeNext();
//		}
		return this.next;
	}

	public abstract void setNext(LLToken next);

	public void setParent(LLToken parent) {
		this.parent = parent;
	}

	public LLToken getParent() {
		return this.parent;
	}

	public void setEndEl(LLToken end) {
		this.endToken = end;
	}

	/**
	 * Returns End Token of this token. <br/> If the End Token is a lazy token =>
	 * materializes the next token till the End Token is not a lazy element
	 * anymore.
	 * 
	 * @return the end token of this (element) token
	 * @throws MXQueryException
	 */
	public LLToken getEndEl() throws MXQueryException {
//		while (this.endToken instanceof LLLazyToken) {
//			LLLazyToken lt = (LLLazyToken) this.endToken;
//			lt.materializeNext();
//		}
		return this.endToken;
	}

	public Identifier getId() {
		return this.token.getId();
	}

	void setId(Identifier id) {
		this.token.setId(id);
	}

	abstract int getEventType();

	// ////////////////////////////////////////
	// Methods for Iterator function support //
	// ////////////////////////////////////////
	public boolean isAttribute() {
		return false;
	}

	public int getDepth() {
		return 0;
	}

	public void setDepth(int depth) {
		// Does nothing. Sould not be invoked.
	}


	public String getValueAsString() throws MXQueryException {
		return null;
	}

	public String getName() {
		return null;
	}
	
	public String getNS() {
		return null;
	}
	
	public String getLocal() {
		return null;
	}

	public void setName(QName newName) throws MXQueryException {
		if (this.token instanceof NamedToken) {
			this.token = ((NamedToken)this.token).copy(newName);
		} else if (this.token instanceof ProcessingInstrToken) {
			this.token = new ProcessingInstrToken(token.getId(),token.getText(),newName.toString(),token.getDynamicScope());
		}
	}
	
	/**
	 * Inserts the passed token after <code>this</code> into the token list.
	 * 
	 * @param token
	 */
	public void insertAfter(LLToken token) throws MXQueryException {
		token.setNext(this.getNext());
		this.getNext().setPrev(token);
		token.setPrev(this);
		this.setNext(token);
	}

	/**
	 * Inserts the elements of the passed token list after <code>this</code>
	 * into the token list.
	 * 
	 * @param tokenList
	 */
	public void insertAfter(TokenList tokenList) throws MXQueryException {
		if (!tokenList.isEmpty()) {
			tokenList.getLastToken().setNext(this.getNext());
			this.getNext().setPrev(tokenList.getLastToken());
			tokenList.getFirstToken().setPrev(this);
			this.setNext(tokenList.getFirstToken());
		}
	}

	/**
	 * Inserts the passed token before <code>this</code> into the token list.
	 * 
	 * @param token
	 */
	public void insertBefore(LLToken token) {
		token.setPrev(this.getPrev());
		this.getPrev().setNext(token);
		token.setNext(this);
		this.setPrev(token);
	}

	/**
	 * Inserts the passed token list before <code>this</code> into the token
	 * list.
	 * 
	 * @param tokenList
	 */
	public void insertBefore(TokenList tokenList) throws MXQueryException {
		if (!tokenList.isEmpty()) {
			tokenList.getFirstToken().setPrev(this.getPrev());
			this.getPrev().setNext(tokenList.getFirstToken());
			tokenList.getLastToken().setNext(this);
			this.setPrev(tokenList.getLastToken());
		}
	}

	/**
	 * Deletes this token from the List (if this token is really in the List)
	 * 
	 */
	public void deleteMe() throws MXQueryException {
		LLToken prev = this.getPrev();
		LLToken next = this.getNext();
		if (prev != null && next != null) {
			prev.setNext(next);
			next.setPrev(prev);
			this.setPrev(null);
			this.setNext(null);
		}

	}

	public static LLToken getSibling(LLToken st) throws MXQueryException {
		int type = st.getEventType();
		if (Type.isEndType(type)) {
			return null;
		}
		LLToken sibling;
		if (Type.isStartType(type)) {
			sibling = st.getEndEl().getNext();
		} else {
			sibling = st.getNext();
		}
		if (Type.isEndType(sibling.getEventType())) {
			return null;
		} else {
			return sibling;
		}
	}
	
	public abstract LLToken copy() throws MXQueryException;
}

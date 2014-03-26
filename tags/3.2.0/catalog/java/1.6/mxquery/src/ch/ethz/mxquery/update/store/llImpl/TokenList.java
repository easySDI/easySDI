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
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;

/**
 * Double Linked List of Tokens.
 * 
 * @author David Alexander Graf
 * 
 */
public class TokenList {
	LLStartToken head;
	LLEndToken tail;

	public TokenList() {
		this.head = new LLStartToken();
		this.tail = new LLEndToken();
		this.head.setNext(this.tail);
		this.tail.setPrev(this.head);
	}

	public boolean isEmpty() throws MXQueryException {
		return this.head.getNext() == this.tail;
	}

	/**
	 * Counts all elements that have an id (that are not end elements):
	 * 
	 * @return number of elements that have an id.
	 */
	public int countIdiedEls() throws MXQueryException {
		int counter = 0;
		LLToken cur = this.head.getNext();
		while (cur != this.tail) {
			if (Type.isIdeedType(cur.getEventType())||cur.getEventType()==Type.END_TAG) {
				counter++;
			}
			cur = cur.getNext();
		}
		return counter;
	}
	
	public short[] countLevels() throws MXQueryException {
		short[] levels = new short[20];
		int counter = 0;
		LLToken cur = this.head.getNext();
		while (cur != this.tail) {
			
			if (Type.isIdeedType(cur.getEventType())){
				if (Type.START_TAG == cur.getEventType()) {
					levels[counter] = 1;
					counter++;
				}
				else if (Type.END_TAG == cur.getEventType()){
					levels[counter] = -1;
					counter++;
				}
				else{
					levels[counter]= 1;
					levels[++counter] = -1;
					counter++;
				}
			}
			cur = cur.getNext();
		}
		return levels;
	}

	public int count() throws MXQueryException {
		LLStoreIterator si = new LLStoreIterator(this.head.getId(), (LLStore)this);
		int counter = 0;
		while (si.next().getEventType() != Type.END_SEQUENCE) {
			counter++;
		}
		return counter;
	}

	public LLToken getFirstToken() throws MXQueryException {
		LLToken res = null;
		if (!this.isEmpty()) {
			res = this.head.getNext();
		}
		return res;
	}

	public LLToken getLastToken() throws MXQueryException {
		LLToken res = null;
		if (!this.isEmpty()) {
			res = this.tail.getPrev();
		}
		return res;
	}

	public LLToken pullFirst() throws MXQueryException {
		LLToken st = null;
		if (!this.isEmpty()) {
			st = this.head.getNext();
			this.deleteFirst();
		}
		return st;
	}

	public void deleteFirst() throws MXQueryException {
		if (!this.isEmpty()) {
			this.head.getNext().getNext().setPrev(this.head);
			this.head.setNext(this.head.getNext().getNext());
		}
	}

	public void deleteLast() throws MXQueryException {
		if (!this.isEmpty()) {
			this.tail.getPrev().getPrev().setNext(this.tail);
			this.tail.setPrev(this.tail.getPrev().getPrev());
		}
	}

	protected void addLast(LLToken storeToken) {
		this.tail.insertBefore(storeToken);
	}

	public Identifier getParentId(Identifier id) throws MXQueryException {
		// Identifier parent = null;
		LLToken token = this.getToken(id);
		LLToken parent = token.getParent();
		if (parent == null) {
			return null;
		} else {
			return token.getParent().getId();
		}
	}

	public boolean hasParent(Identifier id) throws MXQueryException {
		LLToken token = this.getToken(id);
		LLToken parent = token.getParent();
		// Being contained in a sequence does not means that there is a parent
		if (parent == null || parent.getToken() == Token.START_SEQUENCE_TOKEN)
			return false;
		else 
			return true;
	}

	public LLToken getToken(Identifier identifier) throws MXQueryException {
		if (this.isEmpty()) {
			return null;
		}
		LLToken cur = this.head.getNext();
		while (!Type.isEndType(cur.getEventType())) {
			if (cur instanceof LLRefToken) {
				// ignore reference tokens
				cur = cur.getNext();
			} else if (cur.getId().compare(identifier) < 0) {
				return null;
			} else if (cur.getId().compare(identifier) == 0) {
				return cur;
			} else if (Type.isStartType(cur.getEventType())) {
				LLToken sibling = cur.getEndEl().getNext();
				if (!Type.isEndType(sibling.getEventType())
						&& (sibling.getId().compare(identifier) >= 0)) {
					cur = sibling;
				} else {
					cur = cur.getNext();
				}
			} else {
				cur = cur.getNext();
			}
		}
		return null;
	}
}

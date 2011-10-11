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
 * Dummy end element of the token list.
 * 
 * @author David Alexander Graf
 * 
 */
public class LLEndToken extends LLToken {
	public LLEndToken() {
		this.prev = null;
		token = Token.END_SEQUENCE_TOKEN;
	}
	
	public Identifier getId() {
		return null;
	}
	
	public void setId(Identifier id) {
		// does nothing
	}

	public int getEventType() {
		return Type.END_SEQUENCE;
	}

	public void setPrev(LLToken prev) {
		this.prev = prev;
	}

	public void setNext(LLToken next) {
		// do nothing, should never be reached
	}

	public void insertAfter(LLToken token) {
		// do nothing, should never be reached
	}

	public void insertAfter(TokenList tokenList) {
		// do nothing, should never be reached
	}

	public String toString() {
		return "</mxSequence>";
	}
	
	public LLToken copy() throws MXQueryException {
		return new LLEndToken();
	}
}

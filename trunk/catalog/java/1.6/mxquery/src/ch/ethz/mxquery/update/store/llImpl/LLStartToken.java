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
 * Dummy start element of the token list.
 * 
 * @author David Alexander Graf
 * 
 */
public class LLStartToken extends LLToken {
	public LLStartToken() {
		this.next = null;
		token = Token.START_SEQUENCE_TOKEN;
	}
	
	public Identifier getId() {
		return null;
	}
	
	public void setId(Identifier id) {
		// do nothing
	}

	public int getEventType() {
		return Type.START_SEQUENCE;
	}

	public void setPrev(LLToken prev) {
		// do nothing, should never be reached
	}

	public void insertBefore(LLToken token) {
		// do nothing, should never be reached
	}

	public void insertBefore(TokenList tokenList) {
		// do nothing, should never be reached
	}

	public void setNext(LLToken next) {
		this.next = next;
	}

	public String toString() {
		return "<mxSequence>";
	}
	
	public LLToken copy() throws MXQueryException {
		return new LLStartToken();
	}
}

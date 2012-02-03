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

package ch.ethz.mxquery.datamodel.xdm;

import ch.ethz.mxquery.datamodel.Identifier;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;

public final class CommentToken extends Token {
	private final String value;

	public CommentToken(Identifier id, String value, XDMScope scope) throws DynamicException{
		super(Type.COMMENT, id, scope);
		if(!checkStringForDashes(value)){
			throw new DynamicException(ErrorCodes.E0072_DYNAMIC_CONTENT_WITHTWO_ADJACENT_HYPHENS_OR_ENDS_WITH_HYPHEN, "The result of the content expression of the computed contructor contains adjacent hypens or ends with a hyphen", null);
		}
		this.value = value;
	}
	
	public CommentToken(Token token) {
		super(token);
		this.value = token.getText();
	}

	public String getText() {
		return this.value;
	}
	
	public String getValueAsString() {
		return this.value;
	}
	
	public Token copy() {
		return new CommentToken(this);
	}
	
	private boolean checkStringForDashes(String s) {
		
		if(s.endsWith("-")){
			return false;
		} 
		int index = 0;
		while(index < s.length()){
			if(s.charAt(index) == '-' && s.charAt(index+1) == '-'){
				return false;
			}
			else 
				index++;
		}
		return true;
	}
	
}

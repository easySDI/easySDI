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

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.Identifier;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeLexicalConstraints;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;


public final class LongToken extends Token {
	private final long value;

	/** for any of xs:integer subtypes **/
	public LongToken(int type, Identifier id, long value) throws MXQueryException {
		super(type, id,null);
		
		if (! TypeLexicalConstraints.satisfyIntegerRange(type, value) )
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Integer value " + value +
                    " is out of range for the target type " + Type.getTypeQName(type, Context.getDictionary()), null);		
		
		this.value = value;
	}	


	public LongToken(LongToken token) {
		super(token);
		this.value = token.value;
	}	
	
	public long getLong(){
		return this.value;
	}
	
	public String getValueAsString() {
		return String.valueOf(this.value);
	}
	
	public Token toAttrToken(QName name, XDMScope scope) throws MXQueryException {
		LongAttrToken tempToken = new LongAttrToken(this.getEventType(), null, value, name, scope);
		return tempToken;
	}
	
	public Token copy() {
		return new LongToken(this);
	}
	
}

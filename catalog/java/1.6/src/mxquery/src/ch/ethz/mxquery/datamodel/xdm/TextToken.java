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
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;


public class TextToken extends Token {

	protected String value;
	private static final String errCode = ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR;

	
	public TextToken(Identifier id, String val) {
		super(Type.STRING, id, null);
		value = val;
	}
	
	/** for xs: string and any of it's subtypes **/
	/* types xs:untypedAtomic & xs:anyURI use xs:string implementation */
	public TextToken(int type, Identifier id, String val, XDMScope scope) throws MXQueryException {
		super(type, id, scope);
		value = val;
		
		if (!TypeLexicalConstraints.satisfyStringConstraints(type, value))
			throw new DynamicException(errCode, "Invalid value '" + val + "' for " + Type.getTypeQName(type, Context.getDictionary()), null);
	}
	
	public TextToken(TextToken token) {
		super(token);
		this.value = token.getText();
	}
	
	public String getText(){
		return value;
	}
	
	public String getValueAsString() {
		return value;
	}
	public Token toAttrToken(QName name, XDMScope scope) throws MXQueryException {
		TextAttrToken tempToken = new TextAttrToken(null, value, name, scope);
		return tempToken;
	}
	
	public Token copy() {
		return new TextToken(this);
	}
	
	
}

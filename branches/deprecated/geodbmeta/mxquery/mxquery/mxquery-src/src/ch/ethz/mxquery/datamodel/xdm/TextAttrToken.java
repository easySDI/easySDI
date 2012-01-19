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
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.exceptions.MXQueryException;

public class TextAttrToken extends NamedToken {
	private final String value;
	
	public TextAttrToken(Identifier id, String val, QName name, XDMScope scope) throws MXQueryException {
		super(Type.createAttributeType(Type.STRING), id, name, scope);
		this.value = val;
	}
	// types xs:untypedAtomic & xs:anyURI use xs:string implementation
	public TextAttrToken(int type, Identifier id, String val, QName name, XDMScope scope) throws MXQueryException {
		super(Type.createAttributeType(type), id, name, scope);
		this.value = val;
	}
	
	public TextAttrToken(TextAttrToken token) {
		super(token);
		this.value = token.getText();
	}
	
	public void setText(String value){
	
	}
	public String getText(){
		return this.value;
	}
	
	public String getValueAsString() {
		return this.value;
	}
	public Token toAttrToken() {
		return this;
	}
	
	public NamedToken copy(QName newName) throws MXQueryException {
		return new TextAttrToken(this.id, this.value, newName, dynamicScope);
	}
	
	public Token copy() {
		return new TextAttrToken(this);
	}
}

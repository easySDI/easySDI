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
import ch.ethz.mxquery.datamodel.MXQueryDouble;
import ch.ethz.mxquery.datamodel.MXQueryFloat;
import ch.ethz.mxquery.datamodel.MXQueryNumber;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.exceptions.MXQueryException;

public final class FloatAttrToken extends NamedToken {

	private final MXQueryFloat value;

	public FloatAttrToken(Identifier id, MXQueryFloat value, QName name, XDMScope scope) throws MXQueryException{
		super(Type.createAttributeType(Type.FLOAT), id, name, scope);
		this.value = value;
	}
	
	// currently xs:float & xs:decimal types use xs:double implementation
	public FloatAttrToken(int type, Identifier id, MXQueryFloat value, QName name, XDMScope scope) throws MXQueryException{
		super(Type.createAttributeType(type), id, name, scope);
		this.value = value;
	}
	
	public FloatAttrToken(FloatAttrToken token) {
		super(token);
		this.value = token.getFloat();
	}

	public MXQueryDouble getDouble() {
		return this.value.getDoubleValue();
	}
	
	public MXQueryFloat getFloatValue() {
		return this.value;
	}
	
	public MXQueryNumber getNumber() {
		return this.value;
	}	
	
	public String getValueAsString() {
		return this.value.toString();
	}
	public Token toAttrToken() {
		return this;
	}
	
	public NamedToken copy(QName newName) throws MXQueryException{
		return new FloatAttrToken(this.id, this.value, newName, dynamicScope);
	}
	
	public Token copy() {
		return new FloatAttrToken(this);
	}	
	
}

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
import ch.ethz.mxquery.datamodel.MXQueryBigDecimal;
import ch.ethz.mxquery.datamodel.MXQueryDouble;
import ch.ethz.mxquery.datamodel.MXQueryFloat;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.exceptions.MXQueryException;

public final class DecimalAttrToken extends NamedToken {
	private  MXQueryBigDecimal value;
	
	public DecimalAttrToken(Identifier id, MXQueryBigDecimal value, QName name, XDMScope scope) throws MXQueryException{
		super(Type.createAttributeType(Type.DECIMAL), id, name, scope);
		this.value = value;
	}
	
	public DecimalAttrToken(DecimalAttrToken token) throws MXQueryException {
		super(token);
		this.value = null;
		this.value = new MXQueryBigDecimal(token.getValueAsString());
	}

	public MXQueryDouble getDouble() {
		return this.value.getDoubleValue();
	}

	public MXQueryFloat getFloat() {
		return this.value.getFloatValue();
	}	
	
	public String getValueAsString() {
		return this.value.toString();
	}	
	
	public Token toAttrToken() {
		return this;
	}
	
	public NamedToken copy(QName newName) throws MXQueryException{
		return new DecimalAttrToken(this.id, this.value, newName,dynamicScope);
	}
	
	public Token copy() {
		try {
			return new DecimalAttrToken(this);
		} catch (MXQueryException me) { return null;}// error cannot show up, since values are well-defined
	}	
}

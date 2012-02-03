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

	/** @author Rokas Tamosevicius */

package ch.ethz.mxquery.datamodel.xdm;

import ch.ethz.mxquery.datamodel.Identifier;
import ch.ethz.mxquery.datamodel.MXQueryBinary;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.exceptions.MXQueryException;

public final class BinaryAttrToken extends NamedToken {
	private final MXQueryBinary value;

	public BinaryAttrToken(Identifier id, MXQueryBinary val, QName name, XDMScope scope) throws MXQueryException {
		super(Type.createAttributeType(val.getType()), id, name, scope);
		value = val;
	}
	
	public BinaryAttrToken(BinaryAttrToken token){
		super(token);
		this.value = token.getBinary();
	}

	public MXQueryBinary getBinary() {
		return this.value;
	}
	
	public String getValueAsString() {
		return this.value.toString();
	}
	
	public NamedToken copy(QName newName) throws MXQueryException {
		return new BinaryAttrToken(this.id, this.value, newName, dynamicScope);
	}
	
	public Token copy() {
		return new BinaryAttrToken(this);
	}
}






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
import ch.ethz.mxquery.datamodel.MXQueryTime;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.exceptions.MXQueryException;

public final class TimeToken extends Token {
	private final MXQueryTime value;

	public TimeToken(Identifier id, MXQueryTime value) {
		super(Type.TIME, id,null);
		this.value = value;
	}
	
	public TimeToken(TimeToken token) {
		super(token);
		this.value = token.getTime();
	}

	public MXQueryTime getTime() {
		return this.value;
	}
	
	public String getValueAsString() {
		return this.value.toString();
	}
	public Token toAttrToken(QName name, XDMScope scope) throws MXQueryException{
		TimeAttrToken tempToken = new TimeAttrToken(null, value, name, scope);
		return tempToken;
	}
	
	public Token copy() {
		return new TimeToken(this);
	}
}

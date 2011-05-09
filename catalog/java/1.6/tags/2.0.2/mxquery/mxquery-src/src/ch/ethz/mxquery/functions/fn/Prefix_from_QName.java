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
package ch.ethz.mxquery.functions.fn;

import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.QNameToken;
import ch.ethz.mxquery.datamodel.xdm.TextToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;

public class Prefix_from_QName extends TokenBasedIterator {
	
	protected void init() throws MXQueryException {
		if (subIters == null || subIters.length != 1) {
			throw new IllegalArgumentException();
		}
		
		Token t1 = subIters[0].next();
		if (t1 instanceof QNameToken) {
			String prefix = t1.getQNameTokenValue().getNamespacePrefix();
			if (prefix == null) {
				currentToken = Token.END_SEQUENCE_TOKEN;
			} else {	
				if (t1.getNS() == null && getContext().getNamespace(prefix) == null) {
					throw new TypeException(ErrorCodes.F0021_NO_NAMESPACE_FOUND, "No namespace found for prefix '" + prefix + "' !", loc);
				}
				currentToken = new TextToken(null, prefix);
			}
		} else {
			if (t1.getEventType() == Type.END_SEQUENCE) {
				currentToken = Token.END_SEQUENCE_TOKEN;
				return;
			}
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Invalid argument type for fn:prefix-from-QName!", loc);
		}
	}
	
	public TypeInfo getStaticType() {
		return new TypeInfo(Type.NCNAME,Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
	}
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new Prefix_from_QName();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;	
	}
}

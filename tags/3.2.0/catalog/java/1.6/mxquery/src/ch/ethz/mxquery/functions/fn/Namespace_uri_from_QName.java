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
import ch.ethz.mxquery.datamodel.Namespace;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.AnyURIToken;
import ch.ethz.mxquery.datamodel.xdm.QNameToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;

public class Namespace_uri_from_QName extends TokenBasedIterator {

	protected void init() throws MXQueryException {
		if (subIters == null || subIters.length != 1) {
			throw new IllegalArgumentException();
		}
		
		Token t1 = subIters[0].next();
		
		if (subIters[0].next() != Token.END_SEQUENCE_TOKEN)
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Single value expected for fn:namespace-uri-from-QName!", loc);			

		
		if (t1 instanceof QNameToken) {
			String uri = t1.getNS();
			if (uri == null) {
				Namespace ns = getContext().getNamespace(t1.getQNameTokenValue().getNamespacePrefix());
				if (ns != null) {
					uri = ns.getURI();
				}
				
				if (uri == null) {
					currentToken = new AnyURIToken(null, "");
					return;
				}
			} 
			currentToken = new AnyURIToken(null, uri);
		} else {
			if (t1.getEventType() == Type.END_SEQUENCE) {
				currentToken = Token.END_SEQUENCE_TOKEN;
				return;
			}
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Invalid argument type for fn:namespace-uri-from-QName!", loc);
		}
	}
	
	public TypeInfo getStaticType() {
		return new TypeInfo(Type.ANY_URI,Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
	}
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new Namespace_uri_from_QName();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;	
	}
}

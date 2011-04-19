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
import ch.ethz.mxquery.datamodel.xdm.AnyURIToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.datamodel.xdm.XDMScope;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;

public class Namespace_uri_for_prefix extends TokenBasedIterator {
	
	protected void init() throws MXQueryException {
		if (subIters == null || subIters.length != 2) {
			throw new IllegalArgumentException();
		}
		
		XDMIterator prefixIter = subIters[0];
		
		Token t1 = prefixIter.next();
		String prefixToTest;
		if (t1.getEventType() == Type.END_SEQUENCE)
			prefixToTest = "";
		else
			prefixToTest = t1.getText();
		
		Token t2 = subIters[1].next();
		if (t2.getEventType() == Type.START_TAG) {
			XDMScope scope = t2.getDynamicScope();
			String uri = scope.getNsURI(prefixToTest);
			if (uri != null)
				currentToken = new AnyURIToken(null, uri);
			else
				currentToken = Token.END_SEQUENCE_TOKEN;
		} else {
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Invalid argument type for fn:namespace-uri-for-prefix!", loc);
		}
	}
	
	public TypeInfo getStaticType() {
		return new TypeInfo(Type.ANY_URI, Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
	}		
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new Namespace_uri_for_prefix();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;	
	}
}

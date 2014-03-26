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
import ch.ethz.mxquery.datamodel.types.TypeLexicalConstraints;
import ch.ethz.mxquery.datamodel.xdm.AnyURIToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;

public class ResolveUri extends TokenBasedIterator {
	
	protected void init() throws MXQueryException {
		XDMIterator iter0 = subIters[0];
		Token inputToken1 = iter0.next(); 
		int type = inputToken1.getEventType();

		String base = null;
		
		if (subIters.length > 1) { 
		XDMIterator iter1 = subIters[1];
			Token inputToken2 = iter1.next();
			int type2 = inputToken2.getEventType();

			if (type2 != Type.END_SEQUENCE)
				base = inputToken2.getText();

		} else {
			base = context.getBaseURI();
		}
		
		String result = null;
		String resolv = "";
		
		if (type == Type.END_SEQUENCE) {
			currentToken = Token.END_SEQUENCE_TOKEN;
			return; 
		}
		
		resolv = inputToken1.getText();
		if (!TypeLexicalConstraints.isValidURI(resolv) || !TypeLexicalConstraints.isValidURI(base))
			throw new DynamicException(ErrorCodes.F0024_INVALID_ARGUMENT_TO_FN_RESOLVEURI, "Invalid URI", loc);
		if (TypeLexicalConstraints.isAbsoluteURI(resolv))
			result = resolv;
		else {
			base = base.trim();
			// If URI does not end with /, we need to go back 
			int lastSlash = base.lastIndexOf('/');
			if (lastSlash != base.length() -1 && lastSlash > 2 && base.charAt(lastSlash-1) != '/') {
				base = base.substring(0, lastSlash+1);
			}
			result = base + resolv;
		}
		
		
		currentToken = new AnyURIToken(null,result);
	}
	public TypeInfo getStaticType() {
		return new TypeInfo(Type.ANY_URI,Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
	}	
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new ResolveUri();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;	
	}
}

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
import ch.ethz.mxquery.datamodel.xdm.TextToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.util.Set;

public class SubstringBefore extends TokenBasedIterator {
	
	protected void init() throws MXQueryException {
		XDMIterator iter0 = subIters[0];
		XDMIterator iter1 = subIters[1];
		Token inputToken1 = iter0.next(); 
		int type = inputToken1.getEventType();
		Token inputToken2 = iter1.next();
		int type2 = inputToken2.getEventType();

		if (subIters.length > 2) {
			// Minimum collation test - raise error on all collations that are not codepoint
			XDMIterator collIter = subIters[2];
			Token collToken = collIter.next();
			if (collToken == Token.END_SEQUENCE_TOKEN || 
					!Type.isTypeOrSubTypeOf(collToken.getEventType(),Type.STRING, null))
				throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Wrong type for collation", loc);
			String collUri = collToken.getText();
			Set collations = context.getCollations();
			if (!collations.contains(collUri))
				throw new DynamicException(ErrorCodes.F0010_UNSUPPORTED_COLLATION, "Unsupported Collation", loc);
			
		}
		
		
		String result = null;
		
		String res2 = "";
		if (type2 != Type.END_SEQUENCE)
			res2 = inputToken2.getText();
				
		String res = "";
		if (type != Type.END_SEQUENCE)
			res = inputToken1.getText();
		
		if (res2.equals(""))
			result = "";
		else {
			int sPos = res.indexOf(res2);
			if (sPos > 0 ) {
				result = res.substring(0, sPos);
			}
			else 
				result = "";
		}
		currentToken = new TextToken(null, result);
	}
	public TypeInfo getStaticType() {
		return new TypeInfo(Type.STRING,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
	}	
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new SubstringBefore();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;	
	}
}

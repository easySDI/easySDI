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
import ch.ethz.mxquery.datamodel.xdm.BooleanToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;

public class CodePointEqual extends TokenBasedIterator {
	private Token t1, t2;
	
	protected void init() throws MXQueryException {
		if (subIters[0] == null || subIters.length != 2) {
			throw new IllegalArgumentException();
		}
		
		t1 = subIters[0].next();
		t2 = subIters[1].next();
		
		int t1Type = t1.getEventType(); 
		int t2Type = t2.getEventType();

		if (t1Type == Type.END_SEQUENCE || t2Type == Type.END_SEQUENCE) {
			// empty sequence
			currentToken = Token.END_SEQUENCE_TOKEN;
			return;
		}

		
		if (!Type.isTypeOrSubTypeOf(t1Type, Type.STRING, null) || !Type.isTypeOrSubTypeOf(t2Type, Type.STRING, null))
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Wrong type for codepoint-equal()", loc);
			
		
		int res = t1.compareTo(t2);
		if (res == 0)
			currentToken = BooleanToken.TRUE_TOKEN;
		else
			currentToken = BooleanToken.FALSE_TOKEN;
	}

	public TypeInfo getStaticType() {
		return new TypeInfo(Type.BOOLEAN,Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
	}	
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new CodePointEqual();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;
	}
}

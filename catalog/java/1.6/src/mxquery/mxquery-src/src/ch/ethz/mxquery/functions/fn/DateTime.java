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
import ch.ethz.mxquery.datamodel.MXQueryDateTime;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.DateTimeToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;

public class DateTime extends TokenBasedIterator {
	

	protected void init() throws MXQueryException {
		if (subIters[0] == null || subIters[1] == null) {
			throw new IllegalArgumentException();
		}
		
		XDMIterator iter1 = subIters[0];
		Token inputToken1 = iter1.next(); 
		int type1 = inputToken1.getEventType();

		XDMIterator iter2 = subIters[1];
		Token inputToken2 = iter2.next();
		int type2 = inputToken2.getEventType();
		
		if (type1 == Type.END_SEQUENCE || type2 == Type.END_SEQUENCE) {
			currentToken = Token.END_SEQUENCE_TOKEN;
		}
		else if (type1 == Type.DATE && type2 == Type.TIME) {
			MXQueryDateTime date = new MXQueryDateTime(inputToken1.getDate(), inputToken2.getTime());
			currentToken = new DateTimeToken(null, date);			
		}
		else
			throw new TypeException(ErrorCodes.F0028_INVALID_ARGUMENT_TYPE, "Invalid argument type"+Type.getTypeQName(type1, Context.getDictionary())+":"+Type.getTypeQName(type2, Context.getDictionary()), loc);
	}
	public TypeInfo getStaticType() {
		return new TypeInfo(Type.DATE_TIME,Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
	}	
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new DateTime();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;
	}
}

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

import ch.ethz.mxquery.bindings.WindowFactory;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.FnErrorException;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.model.XDMIterator;

public class Error extends TokenBasedIterator {
	
	public Error() {
		super();
		exprCategory = XDMIterator.EXPR_CATEGORY_VACUOUS;
	}

	protected void init() throws MXQueryException {
		if (subIters == null || subIters.length == 0) {
			throw new FnErrorException(loc);
		} else if (subIters != null && subIters.length > 0) {

			// check other possibilities of an empty error code value
			XDMIterator iter = subIters[0];
			Token codeToken = iter.next();
			if (codeToken == Token.END_SEQUENCE_TOKEN && subIters.length == 1)
				throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Empty Sequence is not allowed as error code in fn:error!", loc);				
			String codeString = codeToken.getValueAsString();
			if (codeString == null || codeString.equals("")) {
				codeString = ErrorCodes.F0001_UNIDENTIFIED_ERROR;
			}

			// check wrong error code value (TODO: find better possibility to do that)
			if (!codeString.startsWith("err")) {
				throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "argument istn't an error code: " + codeString, loc);
			}

			QName errorCode = new QName(codeString);
			if (subIters.length == 1) {
				throw new FnErrorException(errorCode, loc);
			} else {
				iter = subIters[1];
				String description = iter.next().getValueAsString();
				if (subIters.length == 2) {
					throw new FnErrorException(errorCode, description, loc);	
				} else if (subIters.length == 3) {
					// The possible Expression in the fn:error function must be executed here in this context!
					Window window = WindowFactory.getNewWindow(this.context, subIters[2]);
					throw new FnErrorException(errorCode, description, window, loc);
				} else {
					throw new IllegalArgumentException("Only 0 to 3 arguments are allowed in fn:error!");
				}
			}
		}
	}

	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new Error();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;	
	}

	public TypeInfo getStaticType() {
		//FIXME: Should be NONE/empty sequence
		return new TypeInfo(Type.ITEM,Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
	}		
}

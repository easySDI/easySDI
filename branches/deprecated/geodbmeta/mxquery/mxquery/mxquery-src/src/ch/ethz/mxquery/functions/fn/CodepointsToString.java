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
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.util.PlatformDependentUtils;

public class CodepointsToString extends TokenBasedIterator {

	protected void init() throws MXQueryException {
		StringBuffer res = new StringBuffer(); //
		XDMIterator input = subIters[0];
		Token inputToken =input.next();
		while (inputToken != Token.END_SEQUENCE_TOKEN) {
			if (Type.isTypeOrSubTypeOf(inputToken.getEventType(),Type.INTEGER, null)) {
				int codePoint = (int) inputToken.getLong();
				// XML 1.1 also allow 0x1 - 0x1F
				if ((codePoint >= 0x1 && codePoint <= 0xd7ff)||
						(codePoint >= 0xE000 && codePoint <= 0xFFFD) || (codePoint >= 0x10000 && codePoint <= 0x10FFFF))
					res.append(PlatformDependentUtils.codepointToChars(codePoint));
				else 
					throw new DynamicException(ErrorCodes.F0009_CODE_POINT_NOT_VALID, "Code Point not valid", loc);
			}
			else 
				throw new DynamicException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Invalid input to codepoints-to-string", loc);
			inputToken = input.next();
		}
		currentToken = new TextToken(null,res.toString());
	}

	public TypeInfo getStaticType() {
		return new TypeInfo(Type.STRING,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
	}	
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new CodepointsToString();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;	
	}
}

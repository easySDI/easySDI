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
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;

public class StringJoin extends TokenBasedIterator {
	
	protected void init() throws MXQueryException {
		
		Token inputToken2 = subIters[1].next();
		int type2 = inputToken2.getEventType();
				
		String res2 = "";
		if (type2 != Type.END_SEQUENCE)
			res2 = inputToken2.getText();
		else
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Empty Sequence not allowed as separator in string-join", loc);
		StringBuffer res = new StringBuffer();
		
		XDMIterator it = subIters[0];
		
		Token tok = it.next();
		
		String curString = tok.getValueAsString();
		if (curString == null) {
			curString = "";
		}
		res.append(curString);
		
		tok = it.next();
		
		while (tok.getEventType() != Type.END_SEQUENCE) {
			res.append(res2);
			curString = tok.getValueAsString();
			if (curString == null) {
				curString = "";
			}
			res.append(curString);
			tok = it.next();
		}
		currentToken = new TextToken(null, res.toString());
	}
	
	public TypeInfo getStaticType() {
		return new TypeInfo(Type.STRING,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
	}	
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new StringJoin();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;	
	}
}

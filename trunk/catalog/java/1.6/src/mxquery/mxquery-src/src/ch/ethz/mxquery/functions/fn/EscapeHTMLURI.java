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
import ch.ethz.mxquery.util.Utils;

public class EscapeHTMLURI extends TokenBasedIterator {
	private int sRange = 32;
	private int eRange = 126;
	
	protected void init() throws MXQueryException {
		XDMIterator input = subIters[0];
		Token uri = input.next();
		
		if (uri.getEventType() == Type.END_SEQUENCE) {
			currentToken = new TextToken(null, "");
			return;
		}
		
		if (!(Type.isTypeOrSubTypeOf(uri.getEventType(), Type.STRING,null) || uri.getEventType()==Type.UNTYPED_ATOMIC)) {
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Invalid argument type for fn:encode-for-uri!", loc);
		}
		
		currentToken = new TextToken(null, encode(uri.getText()));
	}
	
	private String encode(String input) {
		StringBuffer result = new StringBuffer();
		
		for (int i=0; i<input.length(); i++) {
			char character = input.charAt(i);
			
			if (isToEncode(character)) {
				result.append(Utils.getEncodedCharacter(character));
			} else {
				result.append(character);
			}
		}
		
		return result.toString();
	}
	
	private boolean isToEncode(char c) {
		if (c < sRange || c > eRange) {
			return true;
		}
		
		return false;
	}

	public TypeInfo getStaticType() {
		return new TypeInfo(Type.STRING,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
	}			
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new EscapeHTMLURI();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;	
	}
}

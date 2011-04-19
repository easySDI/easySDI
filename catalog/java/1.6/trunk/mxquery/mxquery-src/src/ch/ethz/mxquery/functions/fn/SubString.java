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
import ch.ethz.mxquery.datamodel.MXQueryDouble;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.TextToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;

public class SubString extends TokenBasedIterator {

	protected void init() throws MXQueryException {
		XDMIterator input = subIters[0];
		Token inputToken = input.next(); 
		int type = inputToken.getEventType();
		
		if (type == Type.END_SEQUENCE) {
			currentToken = new TextToken(null, "");
			return;
		}
		// use string constructor to work around bug in JDK 1.5 for offset	
		String res = new String(inputToken.getText());
		
		Token inputToken1 = subIters[1].next();
		int type1 = Type.getEventTypeSubstituted(inputToken1.getEventType(), Context.getDictionary());
		int sPos = -1;
		switch (type1) {
		case Type.INTEGER: 	
			sPos = (int)inputToken1.getLong();
			break;
		case Type.DOUBLE:
		case Type.FLOAT:
		case Type.DECIMAL:
			MXQueryDouble val = inputToken1.getDouble();
			if ( val.isNaN() || val.isNegativeInfinity() || val.isPositiveInfinity() ) {
				currentToken = new TextToken(null, "");
				return;
			}

			sPos = val.getIntValue();
			if ( val.hasFractionPart() ) sPos++;
		}
		
		if (subIters.length == 2) {
			if (sPos < 1)
				sPos = 1;
			
			int charStartPos; 
			if (sPos <= res.length())
				charStartPos = res.offsetByCodePoints(0, sPos-1);
			else 
				charStartPos = res.length();
			currentToken = new TextToken(null, res.substring(charStartPos));
			return;
		}
		if (subIters.length == 3) {
			Token inputToken2 = subIters[2].next();
			int type2 = Type.getEventTypeSubstituted(inputToken2.getEventType(), Context.getDictionary());			
			int sLen = -1;
			switch (type2) {
			case Type.INTEGER: 	
				sLen = (int)inputToken2.getLong();
				break;
			case Type.DOUBLE:
			case Type.FLOAT:
			case Type.DECIMAL:
				MXQueryDouble val = inputToken2.getDouble();
				if ( val.isNaN() || val.isNegativeInfinity()) {
					currentToken = new TextToken(null, "");
					return;
				}

				if ( val.isPositiveInfinity() ) {
					sLen = Integer.MAX_VALUE;
				}
				else {
					sLen = val.getIntValue();
					if ( val.hasFractionPart() ) sLen++;
				}
			}
			
			if (sLen < 1)
				sLen = 0;
			int endPos = sPos-1+sLen;
			if (sPos < 1)
				sPos = 1;
			if (endPos > res.length())
				endPos = res.length();
			int charStartPos = res.offsetByCodePoints(0, sPos-1);
			int charEndPos = res.offsetByCodePoints(0, endPos);
			currentToken = new TextToken(null, res.substring(charStartPos, charEndPos));
			return;
		}

		currentToken = new TextToken(null, "");
	}
	public TypeInfo getStaticType() {
		return new TypeInfo(Type.STRING,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
	}		
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new SubString();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;	
	}
}

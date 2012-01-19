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
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.TextToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;

public class Replace extends TokenBasedIterator {

	protected void init() throws MXQueryException {
		XDMIterator input = subIters[0];
		Token inputToken1 = input.next(); 
		int type = Type.getEventTypeSubstituted( inputToken1.getEventType(), Context.getDictionary() );
		
		if (type == Type.END_SEQUENCE) {
			currentToken = new TextToken(null, "");
			return;
		}
		
		if (!(type == Type.STRING||type == Type.UNTYPED_ATOMIC||type == Type.UNTYPED))
			throw new TypeException(ErrorCodes.F0028_INVALID_ARGUMENT_TYPE, "Invalid argument type"+type, loc);
		
		String res = inputToken1.getText();
		
		Token inputToken2 = subIters[1].next();
		if (inputToken2.getEventType() == Type.END_SEQUENCE)
			throw new DynamicException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Invalid pattern", loc);
		
		String pattern = inputToken2.getText();
		
		Token inputToken3 = subIters[2].next();
		if (inputToken3.getEventType() == Type.END_SEQUENCE)
			throw new DynamicException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Invalid replacement (empty squence)!", loc);
		String replacement = inputToken3.getText();
		
		int f = Matches.computeFlags(subIters, 4, loc);
		
		Pattern p = null;
		
		try {
			p = Pattern.compile(pattern, f);
		} catch (Exception e) {
			throw new DynamicException(ErrorCodes.F0032_INVALID_REGULAR_EXPRESSION, "Invalid regular expression!", loc);
		}
		
		Matcher m = p.matcher("");
		
		if (m.find())
			throw new DynamicException(ErrorCodes.F0033_REGULAR_EXPRESSION_MATCHES_EMPTY,"Pattern matches empty string",loc);
		
		//Check replacement:
		int slashPos =replacement.indexOf("\\"); 
		if (slashPos>=0) {
			if (slashPos==replacement.length()-1)
				throw new DynamicException(ErrorCodes.F0034_INVALID_REPLACEMENT_STRING, "Invalid replacement", loc);
			else {
				if (!(replacement.charAt(slashPos+1) == '$'||replacement.charAt(slashPos+1) == '\\')) 
					throw new DynamicException(ErrorCodes.F0034_INVALID_REPLACEMENT_STRING, "Invalid replacement", loc);
			}
		}
		int strPos =replacement.indexOf("$"); 
		if (strPos>=0) {
//				if (strPos==replacement.length()-1 || strPos==0) 
//					throw new DynamicException(ErrorCodes.F0034_INVALID_REPLACEMENT_STRING, "Invalid replacement", loc);
				if (!((strPos!=0 && replacement.charAt(strPos-1) == '\\') || (strPos!=replacement.length()-1 && replacement.substring(strPos+1, strPos+2).matches("[0-9]")))) 
					throw new DynamicException(ErrorCodes.F0034_INVALID_REPLACEMENT_STRING, "Invalid replacement", loc);

			}
		
		m = p.matcher(res);
		
		res = m.replaceAll(replacement);
		
		currentToken = new TextToken(null, res);
	}
	public TypeInfo getStaticType() {
		return new TypeInfo(Type.STRING,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
	}		
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new Replace();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;	
	}
}

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
import ch.ethz.mxquery.datamodel.xdm.BooleanToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;

public class Matches extends TokenBasedIterator {
	
	protected void init() throws MXQueryException {
		if (subIters == null || subIters.length == 1 || subIters.length > 3) {
			throw new IllegalArgumentException();
		}
		
		Token input = subIters[0].next();
		int ev = Type.getEventTypeSubstituted(input.getEventType(),Context.getDictionary());
		if (!(ev == Type.STRING || input == Token.END_SEQUENCE_TOKEN)) 
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Expected String, got "+Type.getTypeQName(ev, Context.getDictionary()),loc);
		
		String text;
		if (input == Token.END_SEQUENCE_TOKEN)
			text = "";
		else 
			text = input.getText();
		
		
		Token pattern = subIters[1].next();
		
		int f = computeFlags(subIters, 3, loc);
		
		String pat = pattern.getText();
		
		if (pat == null || pat.equals("")) {
			throw new DynamicException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Pattern must be a String value!", loc);
		}
		
		
		if ((f & Pattern.COMMENTS) != 0)
			pat = removeWsFromPattern(pat);
		
		Pattern p = null;
		try {
			p = Pattern.compile(pat, f);
		} catch (Exception e) {
			throw new DynamicException(ErrorCodes.F0032_INVALID_REGULAR_EXPRESSION, "Invalid regular expression!", loc);
		}
		
		if ((f & Pattern.CASE_INSENSITIVE) != 0)
			text = text.toLowerCase();
		Matcher m = p.matcher(text);
		if (m.find())
			currentToken = BooleanToken.TRUE_TOKEN;
		else
			currentToken = BooleanToken.FALSE_TOKEN;
	}

	private static String removeWsFromPattern(String pattern) {
		StringBuffer res = new StringBuffer(pattern.length());
		boolean inCharClass = false;
		// Simplified version of WS stripping, does not take [] into account
		for (int i=0;i<pattern.length();i++) {
			char chr = pattern.charAt(i);
			if (!Character.isWhitespace(chr) || inCharClass)
				res.append(chr);
			if (chr == '[')
				inCharClass = true;
			if (chr == ']')
				inCharClass = false;
		}
		return res.toString();
	}
	
	/**
	 * Extract and compute the flags for the regexp-based functions
	 * @param subIters 
	 * @param pos: Position of the flags parameter, counting as in XQuery (1...)
	 * @param loc: QueryLocation for error reporting
	 * @return
	 * @throws MXQueryException
	 */
	
	static int computeFlags(XDMIterator [] subIters, int pos, QueryLocation loc) throws MXQueryException {
		int f = 0;
		if (subIters.length == pos) {
			Token flags = subIters[pos-1].next();	
			String theFlags = flags.getText();
			
			if (theFlags == null) {
				throw new DynamicException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Regex Flags must be a String value!", loc);
			}
			
			if (!theFlags.equals("")) { // if flags are empty, skip
				if (!theFlags.matches("[smix]+")) {
					throw new DynamicException(ErrorCodes.F0031_INVALID_REGULAR_EXPRESSION_FLAGS, "Invalid regular expression flags!", loc);
				}
				
				if (theFlags.indexOf("s") >= 0) {
					f += Pattern.DOTALL;
				} 
				if (theFlags.indexOf('m') >= 0) {
					f += Pattern.MULTILINE;
				}
				if (theFlags.indexOf("i") >= 0) {
					f += Pattern.CASE_INSENSITIVE;
				}
				if (theFlags.indexOf("x") >= 0) {
					f += Pattern.COMMENTS;
				}	
			}			
		}
		return f;
	}
	
	public TypeInfo getStaticType() {
		return new TypeInfo(Type.BOOLEAN,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
	}	
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new Matches();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;	
	}
}

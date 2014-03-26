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
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.iterators.SequenceIterator;
import ch.ethz.mxquery.iterators.TokenIterator;
import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.EmptySequenceIterator;
import ch.ethz.mxquery.model.XDMIterator;

public class Tokenize extends CurrentBasedIterator {

	public Token next() throws MXQueryException {
		if (called == 0) {
			called++;
			init();
		}
		return current.next();
	}
	
	private void init() throws MXQueryException {
		XDMIterator input = subIters[0];
		Token tok1 = input.next();
		int type = tok1.getEventType();
		String res = tok1.getText();
		XDMIterator input2 = subIters[1];
		Token tok2 = input2.next();
		int type2 = Type.getEventTypeSubstituted( tok2.getEventType(), Context.getDictionary());
		String delimiter = tok2.getText();
		if (type2 != Type.STRING || delimiter.length() == 0)
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Invalid argument type"+Type.getTypeQName(type, Context.getDictionary()), loc);
						
		int f = Matches.computeFlags(subIters, 3, loc);
		
		
		Pattern p = null;
		
		try {
			p = Pattern.compile(delimiter, f);
		} catch (Exception e) {
			throw new DynamicException(ErrorCodes.F0032_INVALID_REGULAR_EXPRESSION, "Invalid regular expression!", loc);
		}
		
		Matcher m = p.matcher("");
		
		if (m.find())
			throw new DynamicException(ErrorCodes.F0033_REGULAR_EXPRESSION_MATCHES_EMPTY,"Pattern matches empty string",loc);
		
		switch (type) {
		case(Type.END_SEQUENCE): 
			current = new EmptySequenceIterator(context,loc);
			break;
		
		case Type.STRING:
		case Type.UNTYPED_ATOMIC:
			if (res.length() == 0)
				current = new EmptySequenceIterator(context,loc);
			else {
				//String [] splitted = Utils.split(res,delimiter);
				String [] splitted = p.split(res,-1);
				TokenIterator [] toks = new TokenIterator[splitted.length];
				for (int i =0;i<toks.length;i++)
					toks[i] = new TokenIterator(context, splitted[i],loc);
				current = new SequenceIterator(context, toks,loc);
			}
		break;
		default:
			throw new TypeException(ErrorCodes.F0028_INVALID_ARGUMENT_TYPE, "Invalid argument type"+Type.getTypeQName(type, Context.getDictionary()), loc);
		}
	}
	public TypeInfo getStaticType() {
		return new TypeInfo(Type.STRING,Type.OCCURRENCE_IND_ZERO_OR_MORE,null,null);
	}
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new Tokenize();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;	
	}
}

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
import ch.ethz.mxquery.datamodel.xdm.XDMScope;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;

public class Lang extends TokenBasedIterator {

	protected void init() throws MXQueryException {
		XDMIterator testLangIt = subIters[0];
		
		Token testLangTok = testLangIt.next();
		
		int ev = testLangTok.getEventType();
		
		String testLang;
		
		if (Type.isTypeOrSubTypeOf(ev, Type.STRING, Context.getDictionary())|| ev== Type.UNTYPED_ATOMIC) {
			testLang = testLangTok.getText().toLowerCase();
		} else if (testLangTok == Token.END_SEQUENCE_TOKEN) {
			testLang = "";
		}else 
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,"String expected",loc);
		
		
		
		if (testLangIt.next()!=Token.END_SEQUENCE_TOKEN)
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,"Single item expected",loc);
		
		XDMIterator idIt = getNodeIteratorOrContext(subIters, 2,context, loc);
		Token tok = idIt.next();
		
		ev = tok.getEventType();
		
		if (!(Type.isNode(ev)||ev == Type.END_SEQUENCE)) {
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,"Base-Uri only works on nodes", loc);
		}

		XDMScope nsScope = tok.getDynamicScope();
		String lang = nsScope.getLanguage();
		
		if (lang == null) {
			currentToken = BooleanToken.FALSE_TOKEN;
		} else {
			lang = lang.toLowerCase();
			if (lang.equals(testLang)) 
			currentToken = BooleanToken.TRUE_TOKEN;
		else {
			int splitPos = lang.indexOf('-');
			if (splitPos <2) // needs to be at least XX-XX
				currentToken = BooleanToken.FALSE_TOKEN;
			else {
				String langPrefix = lang.substring(0, splitPos);
				if (langPrefix.equals(testLang))
					currentToken = BooleanToken.TRUE_TOKEN;
				else
					currentToken = BooleanToken.FALSE_TOKEN;
			}				
		}
		}
	}

	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new Lang();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;	
	}

	public TypeInfo getStaticType() {
		return new TypeInfo(Type.BOOLEAN,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
	}

}

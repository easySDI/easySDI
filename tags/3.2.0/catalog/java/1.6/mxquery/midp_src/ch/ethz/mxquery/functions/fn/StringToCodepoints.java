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

import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.functions.fn.StringToCodepoints;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeDictionary;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.xdm.LongToken;
import ch.ethz.mxquery.datamodel.xdm.TextToken;
import ch.ethz.mxquery.datamodel.xdm.Token;

public class StringToCodepoints extends TokenBasedIterator {

	String toTranslate = null;
	protected void init() throws MXQueryException {
		XDMIterator input = DataValuesIterator.getDataIterator(subIters[0],context);
		Token inputToken =input.next();
		int type = Type.getEventTypeSubstituted( inputToken.getEventType(),null );
		
		switch (type) {
		case(Type.END_SEQUENCE):
			currentToken = new TextToken(null, "");
			break;
		case(Type.STRING):
		case(Type.UNTYPED_ATOMIC):
		case Type.UNTYPED:
			String res = inputToken.getText();
			toTranslate = res;
			break;
		default:
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Invalid argument type"+Type.getTypeQName(type,null),loc);
		}
	}
	public Token next() throws MXQueryException {
		if (called == 0)
			init();
		
		if (toTranslate == null || toTranslate.length() <= called) {
			this.close(false);
			called++;
			return Token.END_SEQUENCE_TOKEN;
		}
		currentToken = new LongToken(Type.INTEGER,null,toTranslate.charAt(called));
		called++;
		return currentToken;	
	}
	protected void resetImpl() throws MXQueryException {
		super.resetImpl();
		toTranslate = null;
		
	}
	
	public TypeInfo getStaticType() {
		return new TypeInfo(Type.INTEGER,Type.OCCURRENCE_IND_ZERO_OR_MORE,null,null);
	}			
	
	public XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new StringToCodepoints();
		copy.setContext(context,true);
		copy.setSubIters(subIters);
		return copy;	
	}
}

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
import ch.ethz.mxquery.datamodel.xdm.LongToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;
/**
 * 
 * @author Matthias Braun
 *
 */
public class String_Length extends TokenBasedIterator {
	
	protected void init() throws MXQueryException {
		XDMIterator input = getNodeIteratorOrContext(subIters, 1,context, loc);
		input = subIters[0];
		
		Token inputToken = input.next(); 
		int type = inputToken.getEventType();
		if (type == Type.START_DOCUMENT) {
			inputToken = input.next();
			type = inputToken.getEventType();
		}

		if ( Type.isAttribute(type) )
			type = Type.getAttributeValueType(type);

		if ( Type.isAtomicType(type, null) ){
			String content = inputToken.getValueAsString();
			int charCount = content.length();
			//TODO: performance improvement: annotate string token if they contain only non BMP-codepoints 
			//=> counting CP is unnecessary then
			int characterCount = content.codePointCount(0, charCount);
			currentToken = new LongToken(Type.INTEGER, null, characterCount );
		} else if (type == Type.END_SEQUENCE){
			currentToken = new LongToken(Type.INTEGER, null,0);
		}
	}
	
	public TypeInfo getStaticType() {
		return new TypeInfo(Type.INTEGER,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
	}		
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new String_Length();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;	
	}
}

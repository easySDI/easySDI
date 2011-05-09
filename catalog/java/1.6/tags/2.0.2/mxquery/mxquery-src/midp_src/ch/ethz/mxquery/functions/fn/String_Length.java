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

import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.model.VariableHolder;
import ch.ethz.mxquery.datamodel.xdm.LongToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
/**
 * 
 * @author Matthias Braun
 *
 */
public class String_Length extends TokenBasedIterator {
	
	protected void init() throws MXQueryException {
		XDMIterator input;
		if (subIters != null && subIters.length == 1) {
			input = DataValuesIterator.getDataIterator(subIters[0], context);
		} else {
			VariableHolder contextVarHolder = context.getContextItem();
			if (contextVarHolder != null) {
				input = contextVarHolder.getIter();
				if (input == null) {
					throw new DynamicException(ErrorCodes.E0002_DYNAMIC_NO_VALUE_ASSIGNED, "Context Item Iterator not set", loc);
				} else {
					input = DataValuesIterator.getDataIterator(input, context);
				}
			} else { 
				throw new RuntimeException("Context Item not set");
			}
		}
		
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
			currentToken = new LongToken(Type.INTEGER, null, charCount );
		} else if (type == Type.END_SEQUENCE){
			currentToken = new LongToken(Type.INTEGER, null,0);
		}
	}
	
	public TypeInfo getStaticType() {
		return new TypeInfo(Type.INTEGER,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
	}		
	
	public XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new String_Length();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;	
	}
}

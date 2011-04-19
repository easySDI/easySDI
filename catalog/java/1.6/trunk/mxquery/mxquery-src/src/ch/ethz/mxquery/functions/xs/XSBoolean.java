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

/**
 * author Rokas Tamosevicius 
 */

package ch.ethz.mxquery.functions.xs;

import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.MXQueryDouble;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.BooleanToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.XDMIterator;

public class XSBoolean extends XSConstructorIterator {
	
	protected void init() throws MXQueryException {
		if (subIters[0] == null) {
			throw new IllegalArgumentException();
		}
		
		XDMIterator input = subIters[0];
		Token inputToken = input.next(); 

		int type = Type.getEventTypeSubstituted(inputToken.getEventType(), Context.getDictionary());
		switch (type) {
			case Type.END_SEQUENCE:
					currentToken = Token.END_SEQUENCE_TOKEN;
			break;
			case Type.STRING:
			case Type.UNTYPED:
			case Type.UNTYPED_ATOMIC:	
				String val = inputToken.getText();
				if (val == null) throw new DynamicException(ErrorCodes.F0005_INVALID_LEXICAL_VALUE, "Lexical value undefined: NULL", loc); 
				
				val = val.trim();
				if ( val.equals("1") || val.equals("true") )
					currentToken = BooleanToken.TRUE_TOKEN;
				else
				if ( val.equals("0") || val.equals("false") )
					currentToken = BooleanToken.FALSE_TOKEN;
				else
					throw new TypeException(ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR, "Invalid lexical value: " + val, loc);
			break;	
			case Type.DECIMAL:
			case Type.DOUBLE:
			case Type.FLOAT:
				MXQueryDouble dVal = inputToken.getDouble();
				if ( dVal.equalsZero() || dVal.isNaN() )
					currentToken = BooleanToken.FALSE_TOKEN;
				else currentToken = BooleanToken.TRUE_TOKEN;
	
			break;
			case Type.INTEGER:
				long lVal = inputToken.getLong();
				if (lVal == 0) 					
					currentToken = BooleanToken.FALSE_TOKEN;
				else currentToken = BooleanToken.TRUE_TOKEN;	
			break;	

			case Type.BOOLEAN:
				if(inputToken.getBoolean()) {
					currentToken = BooleanToken.TRUE_TOKEN;
				}
				else {
					currentToken = BooleanToken.FALSE_TOKEN;
				}
			break;	
			default:
				throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Could not cast type " + Type.getTypeQName(type, Context.getDictionary()) + " to type " + Type.getTypeQName(Type.BOOLEAN, Context.getDictionary()), loc);
		}
		
		if ( input.next() != Token.END_SEQUENCE_TOKEN )
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Could not cast sequence to atomic type " + Type.getTypeQName(Type.BOOLEAN, Context.getDictionary()), loc);		
	}	
	
	public TypeInfo getStaticType() {
		return new TypeInfo(Type.BOOLEAN,Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
	}		
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new XSBoolean();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;
	}
}

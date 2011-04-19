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
/*
 * It takes two inputs, first one being the base and the second one as exponent!
 */
package ch.ethz.mxquery.extensionsModules.math;

import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.MXQueryDouble;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.DoubleToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;

public class Pow extends TokenBasedIterator {

	protected void init() throws MXQueryException {
	
		MXQueryDouble x = null;
		MXQueryDouble y = null;
		MXQueryDouble result = null;
		
		XDMIterator input = subIters[0];
		Token inputToken = input.next(); 
		int type = Type.getEventTypeSubstituted(inputToken.getEventType(), Context.getDictionary());		
		
		switch (type) {
		case Type.END_SEQUENCE:
			currentToken = Token.END_SEQUENCE_TOKEN;
			return;
			case Type.INTEGER:
				x = new MXQueryDouble(inputToken.getLong());	break;
			case Type.UNTYPED_ATOMIC:
			case Type.UNTYPED:
				x = new MXQueryDouble(inputToken.getText());	break;
			case Type.DOUBLE:
			case Type.FLOAT:
			case Type.DECIMAL:		
				x = inputToken.getDouble();				
				break;
		default:
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Invalid argument type"+Type.getTypeQName(type, Context.getDictionary()), loc);
		}
		
		XDMIterator input2 = subIters[1];
		Token inputToken2 = input2.next(); 
		int type2 = Type.getEventTypeSubstituted(inputToken2.getEventType(), Context.getDictionary());
		
		if ( Type.isAttribute(type2) ){
			type2 = Type.getAttributeValueType(type);
		}
		
		switch (type2) {
		case Type.END_SEQUENCE:
			currentToken = Token.END_SEQUENCE_TOKEN;
			return;
			case Type.INTEGER:
				y = new MXQueryDouble(inputToken2.getLong());	break;
			case Type.UNTYPED_ATOMIC:				
				y = new MXQueryDouble(inputToken2.getText());	break;
			case Type.DOUBLE:
			case Type.FLOAT:
			case Type.DECIMAL:
				y = inputToken2.getDouble();				
				break;
		default:
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Invalid argument type"+Type.getTypeQName(type, Context.getDictionary()), loc);
		}
		result = new MXQueryDouble(Math.pow(x.getValue(),y.getValue()));
		currentToken = new DoubleToken(null, result);
	}
	
	public TypeInfo getStaticType() {
		return new TypeInfo(Type.DOUBLE,Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
	}

	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new Pow();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;
	}
}

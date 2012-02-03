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
import ch.ethz.mxquery.functions.RequestTypeMulti;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;

public class TransMath extends TokenBasedIterator implements RequestTypeMulti{

	
	private static final int TRANSMATH_EXP = 0;
	private static final int TRANSMATH_LOG = 1;
	private static final int TRANSMATH_SIN = 2;
	private static final int TRANSMATH_COS = 3;
	private static final int TRANSMATH_TAN = 4;
	private static final int TRANSMATH_ASIN = 5;
	private static final int TRANSMATH_ACOS = 6;
	private static final int TRANSMATH_ATAN = 7;

	private int MATH_METHOD = TRANSMATH_EXP;
	
	public void setRequestType(String type) {
		if (type.equals("exp")) {
			MATH_METHOD = TRANSMATH_EXP;
		}
		if (type.equals("log")) {
			MATH_METHOD = TRANSMATH_LOG;
		}
		if (type.equals("sin")) {
			MATH_METHOD = TRANSMATH_SIN;
		}
		if (type.equals("cos")) {
			MATH_METHOD = TRANSMATH_COS;
		}
		if (type.equals("tan")) {
			MATH_METHOD = TRANSMATH_TAN;
		}
		if (type.equals("asin")) {
			MATH_METHOD = TRANSMATH_ASIN;
		}
		if (type.equals("acos")) {
			MATH_METHOD = TRANSMATH_ACOS;
		}
		if (type.equals("atan")) {
			MATH_METHOD = TRANSMATH_ATAN;
		}
	}
	
	private void setRequestType (int type) {
		this.MATH_METHOD = type;
	}
	
	protected void init() throws MXQueryException {
		MXQueryDouble x = null;
		MXQueryDouble result = null;
		
		XDMIterator input = subIters[0];
		Token inputToken = input.next(); 
		int type = Type.getEventTypeSubstituted(inputToken.getEventType(), Context.getDictionary());		
		
		switch (type) {
		case Type.END_SEQUENCE:
			currentToken = Token.END_SEQUENCE_TOKEN;
			return;
		case Type.INTEGER:
			x = new MXQueryDouble(inputToken.getLong());	
			break;
		case Type.UNTYPED_ATOMIC:
		case Type.UNTYPED:
			x = new MXQueryDouble(inputToken.getText());	
			break;
		case Type.DOUBLE:
		case Type.FLOAT:
		case Type.DECIMAL:		
			x = inputToken.getDouble();				
			break;
		default:
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Invalid argument type"+Type.getTypeQName(type, Context.getDictionary()), loc);
		}
		switch (MATH_METHOD) {
			case TRANSMATH_EXP:
				result = new MXQueryDouble(Math.exp(x.getValue()));
				break;
			case TRANSMATH_LOG:
				result = new MXQueryDouble(Math.log(x.getValue()));
				break;
			case TRANSMATH_SIN: 
				result = new MXQueryDouble(Math.sin(x.getValue()));
				break;
			case TRANSMATH_COS: 
				result = new MXQueryDouble(Math.cos(x.getValue()));
				break;
			case TRANSMATH_TAN: 
				result = new MXQueryDouble(Math.tan(x.getValue()));
				break;
			case TRANSMATH_ASIN: 
				result = new MXQueryDouble(Math.asin(x.getValue()));
				break;
			case TRANSMATH_ACOS: 
				result = new MXQueryDouble(Math.acos(x.getValue()));
				break;
			case TRANSMATH_ATAN: 
				result = new MXQueryDouble(Math.atan(x.getValue()));
				break;
		}

		currentToken = new DoubleToken(null, result);
	}

	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack)
			throws MXQueryException {
		TransMath copy = new TransMath();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		copy.setRequestType(MATH_METHOD);
		return copy;
	}
	public TypeInfo getStaticType() {
		return new TypeInfo(Type.DOUBLE,Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
	}

}

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
import ch.ethz.mxquery.datamodel.MXQueryBigDecimal;
import ch.ethz.mxquery.datamodel.MXQueryDouble;
import ch.ethz.mxquery.datamodel.MXQueryFloat;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.DecimalToken;
import ch.ethz.mxquery.datamodel.xdm.DoubleToken;
import ch.ethz.mxquery.datamodel.xdm.FloatToken;
import ch.ethz.mxquery.datamodel.xdm.LongToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;

public class Abs extends TokenBasedIterator {
	
	protected void init() throws MXQueryException {
		if (subIters[0] == null) {
			throw new IllegalArgumentException();
		}
		XDMIterator iter = subIters[0];
		//if (iter instanceof DataValuesIterator) ((DataValuesIterator)iter).setFnData(true);
		Token tok;
		try {
		 tok = iter.next();
		} catch (TypeException de) {
			if (de.getErrorCode().equals(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE))
			throw new DynamicException(ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR,de.getMessage(), loc);
			else throw de;
		}
		
		int type = Type.getEventTypeSubstituted(tok.getEventType(), Context.getDictionary());
		
		if ( Type.isAttribute(type) ){
			type = Type.getAttributeValueType(type);
		}
		
		switch (type) {
			case Type.END_SEQUENCE:
				currentToken = Token.END_SEQUENCE_TOKEN;
				break;
			case Type.INTEGER:
				long lVal = tok.getLong();
				if (lVal >= 0)
					currentToken = tok;
				else 
					currentToken = new LongToken(type, null, -lVal);
				break;
				
			case Type.DOUBLE:
				double val = Math.abs( tok.getDouble().getValue());
				currentToken = new DoubleToken(null,new MXQueryDouble(val));
				break;
			case Type.FLOAT:
				float valf = (float)Math.abs( tok.getDouble().getValue());
				currentToken = new FloatToken(null,new MXQueryFloat(valf));
				break;
			case Type.DECIMAL:
				MXQueryBigDecimal decVal = (MXQueryBigDecimal)tok.getNumber();
				if (decVal.isNaN() || decVal.isPositiveInfinity() || decVal.isNegativeInfinity())
					throw new TypeException(ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR,"NaN and Infinity Value not possible for Abs and decimal values", loc);
				if (decVal.compareTo(0) > 0)
					currentToken = new DecimalToken(null,decVal);
				else 
					currentToken = new DecimalToken(null,(MXQueryBigDecimal)decVal.negate());
				break;
			default:
				throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,"Invalid argument type", loc);
		}		

	}
	public TypeInfo getStaticType() {
		return new TypeInfo(Type.NUMBER,Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
	}	
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new Abs();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;	
	}
}

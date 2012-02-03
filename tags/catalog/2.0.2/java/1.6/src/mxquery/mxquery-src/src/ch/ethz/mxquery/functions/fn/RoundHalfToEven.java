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
import ch.ethz.mxquery.datamodel.MXQueryNumber;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.DecimalToken;
import ch.ethz.mxquery.datamodel.xdm.DoubleToken;
import ch.ethz.mxquery.datamodel.xdm.FloatToken;
import ch.ethz.mxquery.datamodel.xdm.LongToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;

public class RoundHalfToEven extends TokenBasedIterator {

	protected void init() throws MXQueryException {
		int precision = 0;
		if (subIters[0] == null) {
			throw new IllegalArgumentException();
		}
		XDMIterator iter = subIters[0];

		Token tok = iter.next();

		int type = tok.getEventType();

		if ( Type.isAttribute(type) ){
			type = Type.getAttributeValueType(type);
		}

		if (subIters.length > 1) {
			XDMIterator precIter = subIters[1];

			Token precTok = precIter.next();
			precision = (int)precTok.getLong();
		}

		long scale = 1;
		if (precision != 0) {
			if (precision > 0)
				for (int i=0;i<precision;i++)
					scale = scale*10;
			else 
				for (int i=precision;i<0;i++)
					scale = scale * 10;
		}

		switch (type) {
		case Type.END_SEQUENCE:
			currentToken = Token.END_SEQUENCE_TOKEN;
			break;
		case Type.DOUBLE:
			if (tok.getDouble().compareTo(0) == -1 && tok.getDouble().compareTo(Long.MAX_VALUE) == -1 ||
					tok.getDouble().compareTo(0) == 1 && tok.getDouble().compareTo(Long.MAX_VALUE) == 1)
				currentToken = tok;
			else
			{
				MXQueryDouble inputValue = tok.getDouble();
				if (precision > 0)
					inputValue = (MXQueryDouble)inputValue.multiply(scale);
				if (precision < 0)
					inputValue = (MXQueryDouble)inputValue.divide(scale);
				inputValue = inputValue.round();
				if (precision < 0)
					inputValue = (MXQueryDouble)inputValue.multiply(scale);
				if (precision > 0)
					inputValue = (MXQueryDouble)inputValue.divide(scale);
				currentToken = new DoubleToken(null,inputValue);

			}
			break;
		case Type.FLOAT:
			if (tok.getDouble().compareTo(0) == -1 && tok.getDouble().compareTo(Long.MAX_VALUE) == -1 ||
					tok.getDouble().compareTo(0) == 1 && tok.getDouble().compareTo(Long.MAX_VALUE) == 1)
				currentToken = tok;
			else {
				MXQueryFloat inputValue = tok.getFloat();
				if (precision > 0)
					inputValue = (MXQueryFloat)inputValue.multiply(scale);
				if (precision < 0)
					inputValue = (MXQueryFloat)inputValue.divide(scale);
				inputValue = inputValue.round();
				if (precision < 0)
					inputValue = (MXQueryFloat)inputValue.multiply(scale);
				if (precision > 0)
					inputValue = (MXQueryFloat)inputValue.divide(scale);

				currentToken = new FloatToken(null,inputValue);
			}
			break;
		case Type.DECIMAL:
			MXQueryNumber num = tok.getNumber();
			if (precision > 0)
				num = num.multiply(scale);
			if (precision < 0)
				num = num.divide(scale);

			int comp1 = num.compareTo(num.getLongValue());
			if (comp1 != 0) {
				int comp = 0;
				if (comp1 > 0 )
					comp = num.subtract(num.getLongValue()).compareTo(new MXQueryBigDecimal("0.5"));
				else 
					comp = num.subtract(num.getLongValue()).compareTo(new MXQueryBigDecimal("-0.5"));

				if (comp == 0) 
					if ((num.getLongValue()+1) % 2 == 0) // even
						num =  new MXQueryBigDecimal(num.getLongValue()+1);
					else
						num = new MXQueryBigDecimal(num.getLongValue());

				if (comp1 > 0)
					if (comp > 0 )
						num =  new MXQueryBigDecimal(num.getLongValue()+1);
					else 
						num = new MXQueryBigDecimal(num.getLongValue());
				else 
					if (comp > 0 )
						num =  new MXQueryBigDecimal(num.getLongValue());
					else 
						num = new MXQueryBigDecimal(num.getLongValue()-1);
			}
			if (precision < 0)
				num = num.multiply(scale);
			if (precision > 0)
				num = num.divide(scale);
			currentToken = new DecimalToken(null,(MXQueryBigDecimal)num);

			break;
		default:
			if (Type.isTypeOrSubTypeOf(type, Type.INTEGER, null))
				currentToken = new LongToken(Type.INTEGER, null, tok.getLong());
			else 
				throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,"Invalid argument type "+ Type.getTypeQName(type, Context.getDictionary()), loc);
		}		

	}
	public TypeInfo getStaticType() {
		return new TypeInfo(Type.NUMBER,Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
	}

	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new RoundHalfToEven();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;	
	}

}

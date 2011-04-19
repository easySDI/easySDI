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

public class Round extends TokenBasedIterator {

	protected void init() throws MXQueryException {
		if (subIters[0] == null) {
			throw new IllegalArgumentException();
		}
		XDMIterator iter = subIters[0];
		
		Token tok = iter.next();
		
		int type = tok.getEventType();
		
		if ( Type.isAttribute(type) ){
			type = Type.getAttributeValueType(type);
		}
		
		switch (type) {
			case Type.END_SEQUENCE:
				currentToken = Token.END_SEQUENCE_TOKEN;
				break;
			case Type.DOUBLE:
				if (tok.getDouble().compareTo(0) == -1 && tok.getDouble().compareTo(Long.MIN_VALUE) == -1 ||
						tok.getDouble().compareTo(0) == 1 && tok.getDouble().compareTo(Long.MAX_VALUE) == 1)
					currentToken = tok;
				else {
					currentToken = new DoubleToken(null,tok.getDouble().round());
				}
				break;
			case Type.FLOAT:
				if (tok.getDouble().compareTo(0) == -1 && tok.getDouble().compareTo(Long.MIN_VALUE) == -1 ||
						tok.getDouble().compareTo(0) == 1 && tok.getDouble().compareTo(Long.MAX_VALUE) == 1)
					currentToken = tok;
				else {
					currentToken = new FloatToken(null,tok.getFloat().round());
				}
				break;
			case Type.DECIMAL:
				MXQueryNumber num = tok.getNumber();
				
				int comp1 = num.compareTo(num.getLongValue());
				if (comp1 != 0) {
					int comp = 0;
					if (comp1 > 0 )
						comp = num.subtract(num.getLongValue()).compareTo(new MXQueryBigDecimal("0.5"));
					else 
						comp = num.subtract(num.getLongValue()).compareTo(new MXQueryBigDecimal("-0.5"));

					if (comp1 > 0)
						if (comp >= 0 )
							num =  new MXQueryBigDecimal(num.getLongValue()+1);
						else 
							num = new MXQueryBigDecimal(num.getLongValue());
					else 
						if (comp >= 0 )
							num =  new MXQueryBigDecimal(num.getLongValue());
						else 
							num = new MXQueryBigDecimal(num.getLongValue()-1);
				}
				
				if (num.equals(new MXQueryBigDecimal(num.getLongValue())))
					currentToken = new DecimalToken(null,(MXQueryBigDecimal)num);
				else 
					if (num.subtract(num.getLongValue()).compareTo(new MXQueryBigDecimal("0.5")) >= 0)
						currentToken = new DecimalToken(null,new MXQueryBigDecimal(num.getLongValue()+1));
					else
						currentToken = new DecimalToken(null,new MXQueryBigDecimal(num.getLongValue()));
				break;
			case Type.UNTYPED_ATOMIC:
				String s = tok.getValueAsString();
				MXQueryDouble mxqd = new MXQueryDouble(s);
				currentToken = new DoubleToken(null,mxqd.round());
				break;
			default:
				if (Type.isTypeOrSubTypeOf(type, Type.INTEGER, null))
					currentToken = new LongToken(Type.INTEGER, null, tok.getLong());
				else 
					throw new TypeException(ErrorCodes.F0028_INVALID_ARGUMENT_TYPE,"Invalid argument type "+ Type.getTypeQName(type, Context.getDictionary()), loc);
		}		

	}
	public TypeInfo getStaticType() {
		return new TypeInfo(Type.NUMBER,Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
	}	

	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new Round();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;	
	}
}

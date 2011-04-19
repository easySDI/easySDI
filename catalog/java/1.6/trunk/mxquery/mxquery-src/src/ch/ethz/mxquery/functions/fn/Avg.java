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
import ch.ethz.mxquery.datamodel.MXQueryDayTimeDuration;
import ch.ethz.mxquery.datamodel.MXQueryDouble;
import ch.ethz.mxquery.datamodel.MXQueryFloat;
import ch.ethz.mxquery.datamodel.MXQueryNumber;
import ch.ethz.mxquery.datamodel.MXQueryYearMonthDuration;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.DayTimeDurToken;
import ch.ethz.mxquery.datamodel.xdm.DecimalToken;
import ch.ethz.mxquery.datamodel.xdm.DoubleToken;
import ch.ethz.mxquery.datamodel.xdm.FloatToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.datamodel.xdm.YearMonthDurToken;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;

public class Avg extends TokenBasedIterator {
	
	protected void init() throws MXQueryException {
	if (subIters[0] == null) {
			throw new IllegalArgumentException();
		}
				
		XDMIterator iter = subIters[0];
		Token inputToken = iter.next();

		int type = Type.getEventTypeSubstituted(inputToken.getEventType(), Context.getDictionary());
		
		if ( Type.isAttribute(type) ){
			type = Type.getAttributeValueType(type);
		}
		
		switch (type) {
		case Type.END_SEQUENCE:
			currentToken = Token.END_SEQUENCE_TOKEN;
			break;
			case Type.INTEGER:
			case Type.DOUBLE:
			case Type.FLOAT:
			case Type.DECIMAL:
			case Type.UNTYPED_ATOMIC:	
			case Type.UNTYPED:
				evaluateNum(iter, inputToken);
				break;
			case Type.DAY_TIME_DURATION:
				evaluateDTDuration(iter, inputToken);
				break;
			case Type.YEAR_MONTH_DURATION:
				evaluateYMDuration(iter, inputToken);				
				break;
			default:
				throw new TypeException(ErrorCodes.F0028_INVALID_ARGUMENT_TYPE, "Invalid argument type", loc);
		}
	}
	
	/** evaluate number values */
	private void evaluateNum(XDMIterator iter, Token tok) throws MXQueryException {		
		
 
		MXQueryNumber avgValue = new MXQueryBigDecimal(0);
		long intAvgValue = 0;  
		int count = 0;

		int type = Type.getEventTypeSubstituted(tok.getEventType(), Context.getDictionary());
		int resType = Type.INTEGER; 
		
		while (type != Type.END_SEQUENCE) {
			
			if ( Type.isAttribute(type) ){
				type = Type.getAttributeValueType(type);
			}
			
			switch(type){
				case Type.INTEGER:
					intAvgValue = intAvgValue + tok.getLong();
					break;
				case Type.DOUBLE:
					resType = Type.getNumericalOpResultType(resType, type);
					avgValue = tok.getNumber().add(avgValue);	
					break;
				case Type.FLOAT:
				case Type.DECIMAL:
					resType = Type.getNumericalOpResultType(resType, type);
					avgValue = avgValue.add( tok.getNumber() );	
					break;
				case Type.UNTYPED_ATOMIC:
				case Type.UNTYPED:
					resType = Type.getNumericalOpResultType(resType, Type.DOUBLE);
					avgValue = new MXQueryDouble(tok.getValueAsString()).add(avgValue);	
					break;
				default:
					throw new TypeException(ErrorCodes.F0028_INVALID_ARGUMENT_TYPE, "Invalid argument type "+Type.getTypeQName(type, Context.getDictionary()), loc);
			}//switch
			
			count++;
			tok = iter.next();
			type = Type.getEventTypeSubstituted(tok.getEventType(), Context.getDictionary());			
		}
		
		// int div int -> decimal
		// float div int -> float
		// double div int -> double
		switch (resType) {
			case Type.INTEGER:
				MXQueryBigDecimal dec = (MXQueryBigDecimal)new MXQueryBigDecimal(intAvgValue).divide(count);
				currentToken = new DecimalToken(null, dec);	
			break;
			case Type.DECIMAL:
				dec = (MXQueryBigDecimal) avgValue.add(intAvgValue).divide(count);
				currentToken = new DecimalToken(null, dec);
			break;			
			case Type.DOUBLE:
				MXQueryDouble dbl = (MXQueryDouble)avgValue.add(intAvgValue).divide(count);
				currentToken = new DoubleToken(null, dbl);
				break;
			case Type.FLOAT:
				MXQueryFloat flt = (MXQueryFloat)avgValue.add(intAvgValue).divide(count);
				currentToken = new FloatToken(null, flt);
			break;
		}		

	}
	
	/** evaluate dayTimeDuration values */
	private void evaluateDTDuration(XDMIterator iter, Token tok) throws MXQueryException {
		
		MXQueryDayTimeDuration sumDur = new MXQueryDayTimeDuration("PT0S");
		int count = 0;

		while (tok.getEventType() != Type.END_SEQUENCE) {
			switch(tok.getEventType()){
				case Type.DAY_TIME_DURATION: {
					sumDur = sumDur.add( tok.getDayTimeDur() );
				}break;
				default:
					throw new TypeException(ErrorCodes.F0028_INVALID_ARGUMENT_TYPE, "Invalid argument type", loc);
			}//switch
			
			count++;
			tok = iter.next();
		}

		sumDur = sumDur.divide(count); 	
		
		currentToken = new DayTimeDurToken(null, sumDur);
	}		

	/** evaluate yearMonthDuration values */
	private void evaluateYMDuration(XDMIterator iter, Token tok) throws MXQueryException {
		
		MXQueryYearMonthDuration sumDur = new MXQueryYearMonthDuration("P0M");
		int count = 0;

		while (tok.getEventType() != Type.END_SEQUENCE) {
			switch(tok.getEventType()){
				case Type.YEAR_MONTH_DURATION: {
					sumDur = sumDur.add( tok.getYearMonthDur() );
				}break;
				default:
					throw new TypeException(ErrorCodes.F0028_INVALID_ARGUMENT_TYPE, "Invalid argument type", loc);
			}//switch
			
			count++;
			tok = iter.next();
		}

		sumDur = sumDur.divide(new MXQueryDouble(count)); 	
		
		currentToken = new YearMonthDurToken(null, sumDur);

	}
	
	public TypeInfo getStaticType() {
		return new TypeInfo(Type.ANY_ATOMIC_TYPE,Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
	}	
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new Avg();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;
	}
}

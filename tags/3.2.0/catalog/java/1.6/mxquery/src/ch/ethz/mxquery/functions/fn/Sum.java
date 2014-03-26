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
import ch.ethz.mxquery.datamodel.xdm.LongToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.datamodel.xdm.YearMonthDurToken;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;

public class Sum extends TokenBasedIterator {
	
	
	protected void init() throws MXQueryException {		
		int arity = 0;
		
//		if (subIters == null || subIters.length == 0)
//		{
//			currentToken = new LongToken(Type.INTEGER, null, 0);
//		}
		XDMIterator input = subIters[0];
		
		arity = 1;
		
		if ( subIters.length > 1 )
			arity = 2;		
		
		//Iterator resIt = null;
		boolean emptySeq = false;
		Token inputToken = input.next(); 
		int type = Type.getEventTypeSubstituted(inputToken.getEventType(), Context.getDictionary());
		
		if ( Type.isAttribute(type) ){
			type = Type.getAttributeValueType(type);
		}
		int resType = type;
		
		switch (type) {
			case (Type.END_SEQUENCE): 
				emptySeq = true;
			break;
			case Type.INTEGER:
				evalSum(input, resType, inputToken.getLong(), null);
			break;			
			case Type.UNTYPED_ATOMIC:
			case Type.UNTYPED:
				evalSum(input, Type.DOUBLE, 0, new MXQueryDouble (inputToken.getValueAsString()) );
			break;	
			case Type.DOUBLE:
			case Type.FLOAT:
			case Type.DECIMAL:
				evalSum(input, resType, 0, inputToken.getNumber());
			break; 
			case (Type.DAY_TIME_DURATION): 
				evalSum(input, inputToken.getDayTimeDur());
			break;
			case (Type.YEAR_MONTH_DURATION): 
				evalSum(input, inputToken.getYearMonthDur());
			break;
			
			default: 
				throw new TypeException(ErrorCodes.F0028_INVALID_ARGUMENT_TYPE, 
						"Type " + Type.getTypeQName(type, Context.getDictionary()) + " in function 'sum' is not allowed.", loc); 
		}//switch		
		
		if (emptySeq && arity == 1 ) {
			currentToken = new LongToken(Type.INTEGER, null, 0);
		}
		
		else if ( emptySeq && arity == 2 ) {
			currentToken = subIters[1].next();
		}
		
	}
	
	/** evaluate number values */
	private void evalSum(XDMIterator input, int resType, long pLong, MXQueryNumber pDouble) throws MXQueryException {
		
		long sumLong = pLong;
		MXQueryNumber sumDouble;
		
		if (pDouble != null) sumDouble = pDouble;
		else sumDouble = new MXQueryBigDecimal(0);
		
		int type;
		Token tok = input.next();
		while ((type = Type.getEventTypeSubstituted(tok.getEventType(), Context.getDictionary()) ) != Type.END_SEQUENCE) {				

			if ( Type.isAttribute(type) ){
				type = Type.getAttributeValueType(type);
			}
			
			switch(type){				
				case Type.INTEGER:
					sumLong += tok.getLong();
				break;
				case Type.UNTYPED_ATOMIC:
				case Type.UNTYPED:
					resType = Type.getNumericalOpResultType(resType, Type.DOUBLE);
					sumDouble = new MXQueryDouble (tok.getValueAsString()).add(sumDouble);
				break;
				case Type.DOUBLE:
					resType = Type.getNumericalOpResultType(resType, type);
					sumDouble = tok.getNumber().add(sumDouble);		
					break;
				case Type.FLOAT:
				case Type.DECIMAL:
					resType = Type.getNumericalOpResultType(resType, type);
					sumDouble = sumDouble.add( tok.getNumber() );
				break;
				default:
					throw new TypeException(ErrorCodes.F0028_INVALID_ARGUMENT_TYPE, 
							"Type " + Type.getTypeQName(type, Context.getDictionary()) + " in function 'sum' for numbers is not allowed.", loc); 
			}//switch
			tok = input.next();
		}					
		
		switch (resType) {
			case Type.INTEGER:
				currentToken = new LongToken(Type.INTEGER, null, sumLong);
			break;
			case Type.DECIMAL:
				currentToken = new DecimalToken(null, (MXQueryBigDecimal)sumDouble.add(sumLong));
			break;			
			case Type.DOUBLE:
				currentToken = new DoubleToken(null, (MXQueryDouble)sumDouble.add(sumLong) );
				break;
			case Type.FLOAT:
				currentToken = new FloatToken(null, (MXQueryFloat)sumDouble.add(sumLong) );
			break;
		}		
	}

	/** evaluate dayTimeDuration values */
	private void evalSum(XDMIterator input, MXQueryDayTimeDuration sumDuration) throws MXQueryException {
		
		int type;
		Token tok = input.next();
		while ((type = tok.getEventType()) != Type.END_SEQUENCE) {		
			switch(type){				
				case (Type.DAY_TIME_DURATION):{
					sumDuration = sumDuration.add( tok.getDayTimeDur() );
				}break;
				default:
					throw new TypeException(ErrorCodes.F0028_INVALID_ARGUMENT_TYPE, 
							"Type " + Type.getTypeQName(type, Context.getDictionary()) + " in function 'sum' for dayTimeDuration is not allowed.", loc); 
			}//switch
			tok = input.next();
		}					

		currentToken = new DayTimeDurToken(null, sumDuration);
	}

	/** evaluate yearMonthDuration values */
	private void evalSum(XDMIterator input, MXQueryYearMonthDuration sumDuration) throws MXQueryException {
		
		int type;
		Token tok = input.next();
		while ((type = tok.getEventType()) != Type.END_SEQUENCE) {		
			switch(type){				
				case (Type.YEAR_MONTH_DURATION):{
					sumDuration = sumDuration.add( tok.getYearMonthDur() );
				}break;
				default:
					throw new TypeException(ErrorCodes.F0028_INVALID_ARGUMENT_TYPE, 
							"Type " + Type.getTypeQName(type, Context.getDictionary()) + " in function 'sum' for yearMonthDuration is not allowed.", loc); 
			}//switch
			tok = input.next();
		}					
		currentToken = new YearMonthDurToken(null, sumDuration);
	}
	public TypeInfo getStaticType() {
		return new TypeInfo(Type.ANY_ATOMIC_TYPE,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
	}		
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new Sum();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;	
	}
}
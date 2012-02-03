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
import ch.ethz.mxquery.datamodel.MXQueryDate;
import ch.ethz.mxquery.datamodel.MXQueryDateTime;
import ch.ethz.mxquery.datamodel.MXQueryDayTimeDuration;
import ch.ethz.mxquery.datamodel.MXQueryDouble;
import ch.ethz.mxquery.datamodel.MXQueryFloat;
import ch.ethz.mxquery.datamodel.MXQueryNumber;
import ch.ethz.mxquery.datamodel.MXQueryTime;
import ch.ethz.mxquery.datamodel.MXQueryYearMonthDuration;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.DateTimeToken;
import ch.ethz.mxquery.datamodel.xdm.DateToken;
import ch.ethz.mxquery.datamodel.xdm.DayTimeDurToken;
import ch.ethz.mxquery.datamodel.xdm.DecimalToken;
import ch.ethz.mxquery.datamodel.xdm.DoubleToken;
import ch.ethz.mxquery.datamodel.xdm.FloatToken;
import ch.ethz.mxquery.datamodel.xdm.LongToken;
import ch.ethz.mxquery.datamodel.xdm.TextToken;
import ch.ethz.mxquery.datamodel.xdm.TimeToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.datamodel.xdm.YearMonthDurToken;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.util.Set;

public class MaxMin extends TokenBasedIterator {

	// default: computes MIN
	private boolean maxComputation = false;

	/**
	 * should be set in constructor, but then dynamic class instance creation from properties becomes harder
	 * @param max
	 *  param max = false -> compute MIN
	 *  param max = true -> compute MAX
	 */
	public void setMAXorMIN(boolean max) {
		maxComputation = max;	
	}
	
	protected void init() throws MXQueryException{
		
		if (subIters[0] == null) {
			throw new IllegalArgumentException();
		}
		XDMIterator iter = subIters[0];
		
		Token tok1 = iter.next();
		int type = tok1.getEventType();
		
		if ( tok1.isAttribute() ){
			type = Type.getAttributeValueType(type);
		}
		
		/* handle all subtypes of xs:integer uniformly */
		type = Type.getEventTypeSubstituted(type, Context.getDictionary());

		if (subIters.length > 1 && Type.isTypeOrSubTypeOf(type, Type.STRING, null)) {
			// Minimum collation test - raise error on all collations that are not codepoint
			XDMIterator collIter = subIters[1];
			Token collToken = collIter.next();
			if (collToken == Token.END_SEQUENCE_TOKEN || 
					!Type.isTypeOrSubTypeOf(collToken.getEventType(),Type.STRING, null))
				throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Wrong type for collation", loc);
			String collUri = collToken.getText();
			Set collations = context.getCollations();
			if (!collations.contains(collUri))
				throw new DynamicException(ErrorCodes.F0010_UNSUPPORTED_COLLATION, "Unsupported Collation", loc);
			
		}
		
		
		int resType = type;
		
		switch (type) {
			case Type.END_SEQUENCE:
				currentToken = Token.END_SEQUENCE_TOKEN;
				break;
			case Type.INTEGER:
				evaluateNum(iter, tok1.getLong(), null, resType);
				break;
			case Type.UNTYPED_ATOMIC:
				evaluateNum(iter, Long.MIN_VALUE, new MXQueryDouble (tok1.getValueAsString()), Type.DOUBLE );
				break;
			case Type.DOUBLE:
			case Type.FLOAT:
			case Type.DECIMAL:
				evaluateNum(iter, Long.MIN_VALUE, tok1.getNumber(), resType);
				break;
			case Type.DATE_TIME:
				evaluateDateTime(iter, tok1.getDateTime());
				break;
			case Type.DATE:
				evaluateDate(iter, tok1.getDate());
				break;
			case Type.TIME:
				evaluateTime(iter, tok1.getTime());
				break;
			case Type.DAY_TIME_DURATION:
				evaluateDayTimeDuration(iter, tok1.getDayTimeDur());
				break;
			case Type.YEAR_MONTH_DURATION:
				evaluateYearMonthDuration(iter, tok1.getYearMonthDur());
				break;
			case Type.STRING:
			case Type.ANY_URI:
				evaluateString(iter, tok1.getText(), type);
				break;
//				throw new DynamicException(ErrorCodes.A0002_EC_NOT_SUPPORTED,"String types not yet supported in Min/Max");
			default: {
				throw new TypeException(ErrorCodes.F0028_INVALID_ARGUMENT_TYPE, "Invalid argument type: " + type, loc);
			}	
		}
		
	}
	
	private void evaluateNum(XDMIterator iter, long pInt, MXQueryNumber pDouble, int resType) throws MXQueryException {
		long maxInt = pInt;
		MXQueryNumber maxNumber = pDouble;
		int maxType = resType;
		
//		if (maxNumber != null && maxNumber.isNaN()) {
//			currentToken = new DoubleToken(maxType, null, (MXQueryDouble)maxNumber);
//			return;
//		}
		
		long currInt;
		MXQueryNumber currNumber = null;

		int type;
		Token tok = iter.next();
		while ((type = tok.getEventType()) != Type.END_SEQUENCE) {			
			
			if ( tok.isAttribute() ){
				type = Type.getAttributeValueType(type);
			}			
			
			type = Type.getEventTypeSubstituted(type, Context.getDictionary());
			
			//Type.UNTYPED_ATOMIC handled later
			maxType = Type.getNumericalOpResultType( type, maxType);
			
//			System.out.println("Result type "+Type.getTypeQName(maxType));
			
			switch(type){
				
				case(Type.INTEGER): {
					currInt = tok.getLong();
					if (maxNumber == null || !maxNumber.isNaN()) {
						if (maxNumber != null) {
							if ( handleMINorMAX(maxNumber.compareTo(currInt) <= 0) ) {
								maxInt = currInt;							
								maxNumber = null;
							}
						} else {
							if ( handleMINorMAX(currInt > maxInt) ) {
								maxInt = currInt;
							}
						}
					}
				}break;
				case Type.DOUBLE:
				case Type.FLOAT:
				case Type.DECIMAL:
				case Type.UNTYPED_ATOMIC:			
				case Type.UNTYPED:
				{	
					int genType;
					if (type == Type.UNTYPED_ATOMIC || type == Type.UNTYPED) {
						currNumber = new MXQueryDouble(tok.getValueAsString() );
						genType = Type.DOUBLE;
						maxType = Type.getNumericalOpResultType(maxType,genType);
					}	
					else
						currNumber = tok.getNumber();
					if (currNumber.isNaN()) {
						if (maxNumber == null || !maxNumber.isNaN()) {
							maxNumber = currNumber;
							
						}
					}
					if (maxNumber != null) {
						if (!maxNumber.isNaN()) {
							if ( handleMINorMAX (maxNumber.compareTo(currNumber) < 0) ){
								maxNumber = currNumber;
							}
						}
					}	
					else if ( handleMINorMAX (currNumber.compareTo(maxInt) > 0)) {
							maxNumber = currNumber;
							maxInt = Long.MIN_VALUE;
						}
				}break;
				default: throw new TypeException(ErrorCodes.F0028_INVALID_ARGUMENT_TYPE, "Invalid argument type", loc);
			}//switch
			tok = iter.next();
		}
		
		switch (maxType) {
		case Type.INTEGER: 
			currentToken = new LongToken(maxType, null, maxInt);
			break;
		case Type.DOUBLE:
			if (maxNumber == null)
				currentToken = new DoubleToken(null, new MXQueryDouble(maxInt));
			else if (maxNumber instanceof MXQueryDouble)
				currentToken = new DoubleToken(null, (MXQueryDouble)maxNumber);
			else 
				currentToken = new DoubleToken(null, (MXQueryDouble)(new MXQueryDouble(0).add(maxNumber)));
			break;			
		case Type.FLOAT:
			if (maxNumber == null)
				currentToken = new FloatToken(null, new MXQueryFloat(maxInt));
			else if (maxNumber instanceof MXQueryFloat)
				currentToken = new FloatToken(null, (MXQueryFloat)maxNumber);
			else 
				currentToken = new FloatToken(null, (MXQueryFloat)(new MXQueryFloat(0).add(maxNumber)));
			break;
		case Type.DECIMAL:
			if (maxNumber == null)
				currentToken = new DecimalToken(null,(new MXQueryBigDecimal(maxInt)));
			else 
			currentToken = new DecimalToken(null, (MXQueryBigDecimal)maxNumber);;				
		break;
		}
	}
	
	private boolean handleMINorMAX(boolean val){
		if (maxComputation) return val;
		else return !val;
	}
	
	
	private void evaluateDateTime (XDMIterator iter, MXQueryDateTime pDate) throws MXQueryException {
	
		MXQueryDateTime maxDateTime = pDate;
		MXQueryDateTime currDateTime = null;
	
		int type;
		Token tok = iter.next();
		while ((type = tok.getEventType()) != Type.END_SEQUENCE) {	
			switch(type){
				case(Type.DATE_TIME): {
					currDateTime = tok.getDateTime();
					if ( handleMINorMAX(maxDateTime.compareTo(currDateTime) < 0) )
						maxDateTime = currDateTime;
				}break;
				default: throw new TypeException(ErrorCodes.F0028_INVALID_ARGUMENT_TYPE, "Invalid argument type", loc);
			}//switch
			tok = iter.next();
		}
		currentToken = new DateTimeToken(null, maxDateTime);
	}

	private void evaluateDate (XDMIterator iter, MXQueryDate pDate) throws MXQueryException {
		
		MXQueryDate maxDate = pDate;
		MXQueryDate currDate = null;
	
		int type;
		Token tok = iter.next();
		while ((type = tok.getEventType()) != Type.END_SEQUENCE) {	
			switch(type){
				case(Type.DATE): {
					currDate = tok.getDate();
					if ( handleMINorMAX(maxDate.compareTo(currDate) < 0) )
						maxDate = currDate;
				}break;
				default: throw new TypeException(ErrorCodes.F0028_INVALID_ARGUMENT_TYPE, "Invalid argument type", loc);
			}//switch
			tok = iter.next();
		}
		currentToken = new DateToken(null, maxDate);
	}

	private void evaluateTime (XDMIterator iter, MXQueryTime pTime) throws MXQueryException {
		
		MXQueryTime maxTime = pTime;
		MXQueryTime currTime = null;
	
		int type;
		Token tok = iter.next();
		while ((type = tok.getEventType()) != Type.END_SEQUENCE) {	
			switch(type){
				case(Type.TIME): {
					currTime = tok.getTime();
					if ( handleMINorMAX(maxTime.compareTo(currTime) < 0) )
						maxTime = currTime;
				}break;
				default: throw new TypeException(ErrorCodes.F0028_INVALID_ARGUMENT_TYPE, "Invalid argument type", loc);
			}//switch
			tok = iter.next();
		}
		currentToken = new TimeToken(null, maxTime);
	}
	
	
	
	private void evaluateDayTimeDuration (XDMIterator iter, MXQueryDayTimeDuration pDuration) throws MXQueryException {
		
		MXQueryDayTimeDuration maxDuration = pDuration;
		MXQueryDayTimeDuration currDuration = null;
	
		int type;
		Token tok = iter.next();
		while ((type = tok.getEventType()) != Type.END_SEQUENCE) {	
			switch(type){
				case(Type.DAY_TIME_DURATION): {
					currDuration = tok.getDayTimeDur();
					if ( handleMINorMAX (maxDuration.compareTo(currDuration) < 0) )
						maxDuration = currDuration;
				}break;
				default: throw new TypeException(ErrorCodes.F0028_INVALID_ARGUMENT_TYPE, "Invalid argument type", loc);
			}//switch
			tok = iter.next();
		}
		currentToken = new DayTimeDurToken(null, maxDuration);
	}
	
	private void evaluateYearMonthDuration (XDMIterator iter, MXQueryYearMonthDuration pDuration) throws MXQueryException {
		
		MXQueryYearMonthDuration maxDuration = pDuration;
		MXQueryYearMonthDuration currDuration = null;
	
		int type;
		Token tok = iter.next();
		while ((type = tok.getEventType()) != Type.END_SEQUENCE) {	
			switch(type){
				case(Type.YEAR_MONTH_DURATION): {
					currDuration = tok.getYearMonthDur();
					if ( handleMINorMAX (maxDuration.compareTo(currDuration) < 0) )
						maxDuration = currDuration;
				}break;
				default: throw new TypeException(ErrorCodes.F0028_INVALID_ARGUMENT_TYPE, "Invalid argument type", loc);
			}//switch
			tok = iter.next();
		}
		currentToken = new YearMonthDurToken(null, maxDuration);
	}	

	private void evaluateString(XDMIterator iter, String string, int resType) throws MXQueryException {
		String maxString = string;
		String currString = null;

		int type;
		Token tok = iter.next();
		while ((type = tok.getEventType()) != Type.END_SEQUENCE) {	
			currString = tok.getText();
			if (type != resType) {
				throw new TypeException(ErrorCodes.F0028_INVALID_ARGUMENT_TYPE, "Invalid argument type", loc);
			}
			
			if ( handleMINorMAX (maxString.compareTo(currString) < 0) ) {
				maxString = currString;
			}
			
			tok = iter.next();
		}
		currentToken = new TextToken(null, maxString);
	}
	
	public TypeInfo getStaticType() {
		return new TypeInfo(Type.ANY_ATOMIC_TYPE,Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
	}	

	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		MaxMin copy = new MaxMin();
		copy.setMAXorMIN(maxComputation);
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;	
	}
}

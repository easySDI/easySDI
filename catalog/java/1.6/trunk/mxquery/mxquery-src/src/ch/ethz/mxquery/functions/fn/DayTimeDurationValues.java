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
import ch.ethz.mxquery.datamodel.MXQueryYearMonthDuration;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.DecimalToken;
import ch.ethz.mxquery.datamodel.xdm.LongToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;


public class DayTimeDurationValues  extends TokenBasedIterator {
	
	public static final int DAYS 		= 1;
	public static final int HOURS 		= 2;
	public static final int MINUTES 	= 3;
	public static final int SECONDS 	= 4;
	public static final int MONTHS		= 5;
	public static final int YEARS		= 6;
	//public static final int MILISECONDS = 5;	
	
	private int typeOfRequest = 0;
	
	public DayTimeDurationValues() {}
		
	public void setTypeOfRequest(int type) throws MXQueryException{
		if (type < DAYS || type > YEARS)
			throw new MXQueryException(ErrorCodes.A0009_EC_EVALUATION_NOT_POSSIBLE, "Incorrect type for DayTimeDurationValues",null);
		typeOfRequest = type;
	}

	protected void init() throws MXQueryException {
		if (subIters[0] == null) {
			throw new IllegalArgumentException();
		}
		
		XDMIterator iter = subIters[0];
		Token inputToken = iter.next();
		int type = inputToken.getEventType();
		MXQueryDayTimeDuration dur = null;
		MXQueryYearMonthDuration ymDur = null;
		if (type == Type.DURATION) {
			switch(typeOfRequest) {
			case YEARS:
			case MONTHS:
				type = Type.YEAR_MONTH_DURATION;
				ymDur = inputToken.getDuration().getYearMonthDurationPart();
					break;	
			case DAYS:
			case HOURS:
			case MINUTES:
			case SECONDS:
				type = Type.DAY_TIME_DURATION;
				dur = inputToken.getDuration().getDayTimeDurationPart();
				break;
			}
		}
		switch (type) {
			case Type.END_SEQUENCE:
				currentToken = Token.END_SEQUENCE_TOKEN;
				break;
			case Type.DURATION:
				dur = inputToken.getDuration().getDayTimeDurationPart();
			case Type.DAY_TIME_DURATION:
				if (dur == null) {
					dur = inputToken.getDayTimeDur();
				}
				switch(typeOfRequest) {
					case YEARS:
					case MONTHS:
						currentToken = new LongToken(Type.INTEGER, null, 0);
						break;
					case (DAYS): {
						int val = dur.getDays();
						currentToken = new LongToken(Type.INTEGER, null, val);
						break;
					} 
					case (HOURS): {
						int val = dur.getHours();
						currentToken = new LongToken(Type.INTEGER, null, val);
						break;
					} 
					case (MINUTES): {
						int val = dur.getMinutes();
						currentToken = new LongToken(Type.INTEGER, null, val);
						break;
					} 
					case (SECONDS): {
						MXQueryDouble val = dur.getSecondsWithMili();
						long smoother = val.multiply(1000).getLongValue();
						currentToken = new DecimalToken(null, (MXQueryBigDecimal)new MXQueryBigDecimal(smoother).divide(1000));
						break;
					} 
				}//switch typeOfRequest
				break;
			case Type.YEAR_MONTH_DURATION:
				if (ymDur == null )
					ymDur = inputToken.getYearMonthDur();
				switch(typeOfRequest) {
					case YEARS:
						int val = ymDur.getYears(); 
						currentToken = new LongToken(Type.INTEGER, null, val);
						break;
					case MONTHS:
						val = ymDur.getMonths();
						currentToken = new LongToken(Type.INTEGER, null, val);
						break;
					case (DAYS): 
					case (HOURS): 
					case (MINUTES): 
						currentToken = new LongToken(Type.INTEGER, null, 0);
						break; 
					case (SECONDS): {
						currentToken = new DecimalToken(null, new MXQueryBigDecimal(0));
						break;
					} 
				}//switch typeOfRequest
				break;
			default:
				throw new TypeException(ErrorCodes.F0028_INVALID_ARGUMENT_TYPE, "Invalid argument type"+Type.getTypeQName(type, Context.getDictionary()), loc);
		}//switch type
		
	}
	public TypeInfo getStaticType() {
		int retType = Type.ITEM;
		switch (typeOfRequest) {
		case YEARS: 
		case  MONTHS:
		case  DAYS:
		case  HOURS: 
		case  MINUTES:
			retType = Type.INTEGER;
			break;
		case SECONDS:
			retType =  Type.DECIMAL;
			break;
		}
		return new TypeInfo(retType,Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
	}	
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		DayTimeDurationValues copy = new DayTimeDurationValues();
		copy.setTypeOfRequest(typeOfRequest);
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;
	}
}
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
import ch.ethz.mxquery.datamodel.MXQueryNumber;
import ch.ethz.mxquery.datamodel.MXQueryTime;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.DateTimeToken;
import ch.ethz.mxquery.datamodel.xdm.DateToken;
import ch.ethz.mxquery.datamodel.xdm.DayTimeDurToken;
import ch.ethz.mxquery.datamodel.xdm.DecimalToken;
import ch.ethz.mxquery.datamodel.xdm.LongToken;
import ch.ethz.mxquery.datamodel.xdm.TimeToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;


public class DateTimeValues  extends TokenBasedIterator {
	
	public static final int YEARS 		= 1; 
	public static final int MONTHS 		= 2;
	public static final int DAYS 		= 3;
	public static final int HOURS 		= 4;
	public static final int MINUTES 	= 5;
	public static final int SECONDS 	= 6;
	public static final int TIMEZONE    = 7;
	//public static final int MILISECONDS = 7;	
	
	private int typeOfRequest = 0;
	

	public void setTypeOfRequest(int type) {
		typeOfRequest = type;
	}

	protected void init() throws MXQueryException {
		if (subIters[0] == null) {
			throw new IllegalArgumentException();
		}
		XDMIterator iter = subIters[0];
	
		Token inputToken = iter.next(); 
		int type = inputToken.getEventType();
		int val = -5;
		// cleaner typecasting needed
		if (type == Type.UNTYPED || type == Type.UNTYPED_ATOMIC) {
			try {
				inputToken = new DateTimeToken(null, new MXQueryDateTime(inputToken.getText(),MXQueryDateTime.VALUE_TYPE_DATE_TIME));
			} catch (DynamicException se) {
				try {
					inputToken = new DateToken(null, new MXQueryDate(inputToken.getText()));
				} catch (DynamicException se1) {
					inputToken = new TimeToken(null, new MXQueryTime(inputToken.getText()));
				}
			}
			type = inputToken.getEventType();
		}
		
		switch (type) {
			case Type.END_SEQUENCE:
				currentToken = Token.END_SEQUENCE_TOKEN;
				break;
			case Type.DATE_TIME:
			case Type.TIME:
			case Type.DATE: {
				switch(typeOfRequest) {
					case (YEARS): {
						if (type == Type.DATE) 
							val = inputToken.getDate().getYear();
						else 
							val = inputToken.getDateTime().getYear();
						break;
					} 
					case (MONTHS): {
						if (type == Type.DATE) 
							val = inputToken.getDate().getMonth();
						else 
							val = inputToken.getDateTime().getMonth();
						break;
					} 
					case (DAYS): {
						if (type == Type.DATE) 
							val = inputToken.getDate().getDay();
						else 
							val = inputToken.getDateTime().getDay();
						break;
					} 
					case (HOURS): {
						if (type == Type.TIME) 
							val = inputToken.getTime().getHours();
						else 
							val = inputToken.getDateTime().getHours();
						break;
					} 
					case (MINUTES): {
						if (type == Type.TIME) 
							val = inputToken.getTime().getMinutes();
						else 
							val = inputToken.getDateTime().getMinutes();
						break;
					} 
					case (SECONDS): {
						MXQueryNumber dVal;
						if (type == Type.TIME) 
							dVal = inputToken.getTime().getSecondsWithMili();
						else 
							dVal = inputToken.getDateTime().getSecondsWithMili();
						long smoother = dVal.multiply(1000).getLongValue();
						currentToken = new DecimalToken(null, (MXQueryBigDecimal)new MXQueryBigDecimal(smoother).divide(1000));
						break;
					}
					case (TIMEZONE): {
						MXQueryDayTimeDuration dur = null;
						switch (type) {
						case Type.DATE:
							dur = inputToken.getDate().getTimezoneAsDuration();
							break;
						case Type.DATE_TIME:
							dur = inputToken.getDateTime().getTimezoneAsDuration();
							break;
						case Type.TIME:
							dur = inputToken.getTime().getTimezoneAsDuration();
							break;
						}
						if (dur.getMinutes() == Integer.MIN_VALUE)
							currentToken = Token.END_SEQUENCE_TOKEN;
							else
								currentToken = new DayTimeDurToken(null,dur);
						break;
					}
				}//switch typeOfRequest
				// assign if not yet assigned
				if (currentToken == null || currentToken.getEventType() == Type.START_SEQUENCE)
					currentToken = new LongToken(Type.INTEGER, null, val);
				break;
			}	
			default:
				throw new TypeException(ErrorCodes.F0028_INVALID_ARGUMENT_TYPE, "Invalid argument type"+Type.getTypeQName(type, Context.getDictionary()), loc);
		}//switch type
	}
	public TypeInfo getStaticType(){
		int retType = Type.ITEM;
		switch (typeOfRequest) {
		case YEARS: 
		case  MONTHS:
		case  DAYS:
		case  HOURS: 
		case  MINUTES:
			retType =  Type.INTEGER;
			break;
		case SECONDS:
			retType =  Type.DECIMAL;
			break;
		case TIMEZONE:
			retType = Type.DAY_TIME_DURATION;
			break;
		}
		return new TypeInfo(retType,Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
	}		
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		DateTimeValues copy = new DateTimeValues();
		copy.setTypeOfRequest(typeOfRequest);
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;
	}

}
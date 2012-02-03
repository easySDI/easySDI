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
import ch.ethz.mxquery.datamodel.MXQueryDayTimeDuration;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.DateTimeToken;
import ch.ethz.mxquery.datamodel.xdm.DateToken;
import ch.ethz.mxquery.datamodel.xdm.TimeToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;

public class DateTimeAdjustTimezone extends TokenBasedIterator {
	
	public static final int DATE_TIME_TIMEZONE 		= 1; 
	public static final int DATE_TIMEZONE 			= 2;
	public static final int TIME_TIMEZONE 			= 3;	
	
	private int typeOfRequest = 0;
	
	public void setTypeOfRequest(int type) throws MXQueryException{
		if (type < DATE_TIME_TIMEZONE || type > TIME_TIMEZONE)
			throw new MXQueryException(ErrorCodes.A0009_EC_EVALUATION_NOT_POSSIBLE, "Incorrect type for DateTimeAdjustTimezone", null);
		typeOfRequest = type;
	}

	protected void init() throws MXQueryException {
		
		if (subIters[0] == null) {
			throw new IllegalArgumentException();
		}
		XDMIterator argIt = subIters[0];
		
		XDMIterator timezoneIt = null;
		if (subIters.length == 2) {
			timezoneIt = subIters[1];
		}
		
		MXQueryDayTimeDuration tzDuration = null;
		
		if (timezoneIt == null) {
			tzDuration = context.getCurrentTimeZone();
		} else {
			Token tzToken = timezoneIt.next();
			if (tzToken.getEventType() == Type.DAY_TIME_DURATION ) {
				tzDuration = tzToken.getDayTimeDur();
				if ( ! tzDuration.isTimeZoneValid() )
					throw new DynamicException(ErrorCodes.F0020_INVALID_TIMEZONE_VALUE,"Timezone value is invalid", loc);
			}				
			else if (tzToken.getEventType() == Type.END_SEQUENCE )
				tzDuration = null; // to remove time zone
			else 
				throw new TypeException(ErrorCodes.F0028_INVALID_ARGUMENT_TYPE, "Invalid argument type"+Type.getTypeQName(tzToken.getEventType(), Context.getDictionary()), loc);
		}
		
		Token argToken = argIt.next();
		int type = argToken.getEventType();

		switch (type) {
			case Type.END_SEQUENCE:
				currentToken = Token.END_SEQUENCE_TOKEN;
			break;
			case Type.DATE_TIME: {
				if (typeOfRequest != DATE_TIME_TIMEZONE)
					throw new TypeException(ErrorCodes.F0028_INVALID_ARGUMENT_TYPE, "Invalid argument type"+Type.getTypeQName(type, Context.getDictionary()), loc);

				currentToken = new DateTimeToken(null, argToken.getDateTime().adjustTimeZone( tzDuration ));				
			} break;
			case Type.DATE: {
				if (typeOfRequest != DATE_TIMEZONE)
					throw new TypeException(ErrorCodes.F0028_INVALID_ARGUMENT_TYPE, "Invalid argument type"+Type.getTypeQName(type, Context.getDictionary()), loc);

				currentToken = new DateToken(null, argToken.getDate().adjustTimeZone( tzDuration ));				
			} break;
			case Type.TIME: {
				if (typeOfRequest != TIME_TIMEZONE)
					throw new TypeException(ErrorCodes.F0028_INVALID_ARGUMENT_TYPE, "Invalid argument type"+Type.getTypeQName(type, Context.getDictionary()), loc);

				currentToken = new TimeToken(null, argToken.getTime().adjustTimeZone( tzDuration ));				
			} break;
			default:
				throw new TypeException(ErrorCodes.F0028_INVALID_ARGUMENT_TYPE, "Invalid argument type"+Type.getTypeQName(type, Context.getDictionary()), loc);
		}//switch type
	}
	
	
	public TypeInfo getStaticType() {
		int retType = 0;
		switch(typeOfRequest){
		case DATE_TIME_TIMEZONE:
			retType = Type.DATE_TIME;
			break;
		case DATE_TIMEZONE:
			retType = Type.DATE; 
			break;
		case TIME_TIMEZONE:
			retType = Type.TIME;
			break;
		} 
		return new TypeInfo(retType,Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
	}	
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		DateTimeAdjustTimezone copy = new DateTimeAdjustTimezone();
		copy.setTypeOfRequest(typeOfRequest);
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;
	}
}

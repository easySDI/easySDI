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

package ch.ethz.mxquery.datamodel;

//import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Date;
import java.util.TimeZone;

import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.StaticException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.util.Utils;
import ch.ethz.mxquery.datamodel.MXQueryDayTimeDuration;
import ch.ethz.mxquery.datamodel.types.Type;

//
/**
 * @author rokast 
 * Specification at http://www.w3.org/TR/xmlschema11-2/#dateTime
 *	dateTimeLexicalRep ::= yearFrag '-' monthFrag '-' dayFrag 'T'((hourFrag ':' minuteFrag ':' secondFrag) | endOfDayFrag) timezoneFrag?
 *
 * Note: Current implementation ignores time zones. 
 * 
 * Implemented operators:
 * A + B xs:dateTime xs:dayTimeDuration op:add-dayTimeDuration-to-dateTime(A, B) xs:dateTime
 * A - B xs:dateTime xs:dateTime op:subtract-dateTimes(A, B) xs:dayTimeDuration
 * A - B xs:dateTime xs:dayTimeDuration op:subtract-dayTimeDuration-from-dateTime(A, B) xs:dateTime
 * A + B xs:dateTime xs:yearMonthDuration op:add-yearMonthDuration-to-dateTime(A, B) xs:dateTime 
 * A - B xs:dateTime xs:yearMonthDuration op:subtract-yearMonthDuration-from-dateTime(A, B) xs:dateTime  
 * 
 * A eq B xs:dateTime xs:dateTime op:datetime-equal(A, B) xs:boolean
 * A ne B xs:dateTime xs:dateTime fn:not(op:datetime-equal(A, B)) xs:boolean
 * A gt,lt,ge,le B xs:dateTime xs:dateTime  xs:boolean
 * 
 * @author Matthias Braun
 * CLDC Version does not support formatting a DateTime!
 */

public class MXQueryDateTime {	

	public static final int VALUE_TYPE_TIME 	= 1;
	public static final int VALUE_TYPE_DATE 	= 2;
	public static final int VALUE_TYPE_DATE_TIME 	= 3;

	public static final int NO_TIME_ZONE_DEFINED = Integer.MIN_VALUE;

	private Date val;
	private String timeZone = "";
	private Calendar c;
	private int type = VALUE_TYPE_DATE_TIME;
	private boolean removeEndZeros = true;	

	
	public MXQueryDateTime (int year, byte month, byte day, byte hour, byte minute, byte second, int microsecond,
			int timeZoneMin, int type){
		throw new RuntimeException("This MXQueryDateTime constructor in CLDC version should not be used !!!");
	}
	
	
	public MXQueryDateTime(Date d, String zone, int t) {
		this.type = t;		
		val = d;
		timeZone = zone;
		c = Calendar.getInstance();
		c.setTime(val);		
	}

	public MXQueryDateTime(Calendar cal, int tzMin, int t) {
		this(cal.getTime(), "", t);
		// timezone not supported !!! 
	}	
	
	public MXQueryDateTime(MXQueryDate datePart, MXQueryTime timePart) {
		//dFormat = new SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ss'.'SSS");
		type = VALUE_TYPE_DATE_TIME;
		c = Calendar.getInstance();
		c.set(Calendar.YEAR, datePart.getYear());
		c.set(Calendar.MONTH, datePart.getMonth()-1);
		c.set(Calendar.DATE, datePart.getDay());
		c.set(Calendar.HOUR_OF_DAY, timePart.getHours());
		c.set(Calendar.MINUTE, timePart.getMinutes());
		c.set(Calendar.SECOND, timePart.getSeconds());
		val = c.getTime();
	}
	
	public MXQueryDateTime(String input, int type) throws MXQueryException {	
		this.type = type;
		if (input == null || input.equals("")) {
			throw new StaticException(ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR, "Date parsing Exception",null);
		}
		String dateTime = null;
		try {
			dateTime = input.substring(0, Math.min(23, input.length()) );
		} catch (Exception e) {
			throw new StaticException(ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR, "Date parsing Exception",null);
		}
		
		//-- parse time zone
		int posZone = input.indexOf("Z");
		if(posZone > 0) {
			timeZone = input.substring(posZone);
			c = Calendar.getInstance(TimeZone.getTimeZone(timeZone));
		} else {
			c = Calendar.getInstance();
		}		
		
		// parse date and time
		if (posZone > 0) {
			try {
			dateTime = dateTime.substring(0, posZone);
			} catch (Exception e) {
				throw new StaticException(ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT, "Date parsing Exception",null);
			}
		}
			
		String[] components = Utils.split(dateTime, "T");
		
		if (components.length > 0) {
			// Date
			String[] dateFields = Utils.split(components[0], "-");
			if (dateFields.length == 3) {
				c.set(Calendar.YEAR, Integer.parseInt(dateFields[0]) );
				c.set(Calendar.MONTH, Integer.parseInt(dateFields[1]) );
				c.set(Calendar.DAY_OF_MONTH, Integer.parseInt(dateFields[2]) );
			} else {
				throw new StaticException(ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT, "Date parsing Exception",null);
			}
			
			// time
			if (components.length > 1) {
				String[] timeFields = Utils.split(components[1], ":");
				if (dateFields.length >= 3) {
					c.set(Calendar.HOUR_OF_DAY, Integer.parseInt(timeFields[0]) );
					c.set(Calendar.MINUTE, Integer.parseInt(timeFields[1]) );
					c.set(Calendar.SECOND, Integer.parseInt(timeFields[2]) );
					// TODO milliseconds?
				}
			}
		}
		
		val = c.getTime();
	}


	
	public String datePartToString() throws MXQueryException {
		switch(type) {
			case MXQueryDateTime.VALUE_TYPE_DATE:
				return c.toString().substring(0,10);
			case MXQueryDateTime.VALUE_TYPE_DATE_TIME:
				return  c.toString().substring(0,10);
			default :
				throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Incorrect type parameter for datePartAsString :"+type, null);
		}
	}	

	public String timePartToString() throws MXQueryException {
	
		switch(type) {
			case MXQueryDateTime.VALUE_TYPE_TIME:
				return c.toString().substring(11) + timeZone;
			case MXQueryDateTime.VALUE_TYPE_DATE_TIME:
				return c.toString().substring(11) + timeZone;
			default :
				throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Incorrect type parameter for datePartAsString :"+type, null);
		}
	}
	
	public MXQueryDateTime(String input) throws MXQueryException {
		this(input, MXQueryDateTime.VALUE_TYPE_DATE_TIME);
	}
	
	public String toString() {
		return c.toString() + timeZone;
	}

	public boolean equals(MXQueryDateTime d) {
		return this.val.equals(d.val);
	}	
		
	public boolean unequals(MXQueryDateTime d) {
		return !this.val.equals(d.val);
	}
	
	/**
	 * Compares 2 DateTime values
	 * @param d DateTime values to compare with
	 * @return
	 * 0 if the argument Date is equal to this Date
	 * a value less than 0 if this Date is before the Date argument
	 * a value greater than 0 if this Date is after the Date argument
	 */
	public int compareTo(MXQueryDateTime d) {
		return (int) (c.getTime().getTime() - d.getCalendar().getTime().getTime());
		
		
	}
	
	public Calendar getCalendar() {
		return c;
	}
	

	public MXQueryDateTime addDuration(MXQueryYearMonthDuration d) {
		int sign = d.getNegative();
		return addsubt(d, sign);
	}	
	
	public MXQueryDateTime subtractDuration(MXQueryYearMonthDuration d) {
		int sign = -1 * d.getNegative();
		return addsubt(d, sign);
	}
	
	private MXQueryDateTime addsubt(MXQueryYearMonthDuration d, int sign) {
		Calendar cal = Calendar.getInstance();
		cal.setTime(val);
		
		cal.set(Calendar.YEAR, sign*d.getYears() );
		cal.set(Calendar.MONTH, sign*d.getMonths() );
		
		return new MXQueryDateTime(cal.getTime(), this.timeZone, this.type);
	}	
	//-----


	public MXQueryDateTime addDuration(MXQueryDayTimeDuration d) {
		int sign = d.getNegative();
		return addsubt(d, sign);
	}	
	
	public MXQueryDateTime subtractDuration(MXQueryDayTimeDuration d) {
		int sign = -1 * d.getNegative();
		return addsubt(d, sign);
	}	

	
	private MXQueryDateTime addsubt(MXQueryDayTimeDuration d, int sign) {
		Calendar cal = Calendar.getInstance();
		cal.setTime(val);
		
		cal.set(Calendar.DAY_OF_MONTH, sign*d.getDays() );
		cal.set(Calendar.HOUR_OF_DAY, sign*d.getHours() );
		cal.set(Calendar.MINUTE, sign*d.getMinutes() );
		cal.set(Calendar.SECOND, sign*d.getSeconds() );
		cal.set(Calendar.MILLISECOND, sign*d.getMiliseconds() );
		
		return new MXQueryDateTime(cal.getTime(), this.timeZone, this.type);
	}	
	
	
	public MXQueryDayTimeDuration subtractDateTime(MXQueryDateTime d) {
		
		long d1ValInSec = this.getTimeInMilis();
		long d2ValInSec = d.getTimeInMilis();	
		
		// difference in miliseconds
		long resVal = d1ValInSec - d2ValInSec; 
		
		return MXQueryDayTimeDuration.microSecondsToDuration(resVal * 1000);
	}	
	
	
	public int getYear() {
		return c.get(Calendar.YEAR);
	}

	public int getMonth() {
		return c.get(Calendar.MONTH) +1;
	}

	public int getDay() {
		return c.get(Calendar.DAY_OF_MONTH);
	}
	
	public int getHours() {
		return c.get(Calendar.HOUR_OF_DAY);
	}
	
	public int getMinutes() {
		return c.get(Calendar.MINUTE);
	}

	public MXQueryDouble getSecondsWithMili() throws MXQueryException {
		return new MXQueryDouble(c.get(Calendar.SECOND)+"."+c.get(Calendar.MILLISECOND));
	}
	
	public int getSeconds() {
		return c.get(Calendar.SECOND);
	}
	
	public int getMiliseconds() {
		return c.get(Calendar.MILLISECOND);
	}	

	public void setType(int t) {
		this.type =t;
	}

	public long getTimeInMilis() {
		return this.val.getTime(); 
	}

	/************** TIMEZONE IS NOT SUPPORTED **************/
	public String getTimeZone() {
//		throw new RuntimeException("Timezone in CLDC version is not supported !!!");
		return timeZone;
	}

	 public static final void appendTimezone(StringBuffer sb, int tz) {	
//		 throw new RuntimeException("Timezone in CLDC version is not supported !!!");
	 }	 
	 
	public boolean hasTimezone() {
//		throw new RuntimeException("Timezone in CLDC version is not supported !!!");
	    return false;
	}
	
	public final int getTimezoneInMinutes(){
//		throw new RuntimeException("Timezone in CLDC version is not supported !!!");
	    return 0;
	} 	
	/************** TIMEZONE IS NOT SUPPORTED **************/	
	
    public final MXQueryDayTimeDuration getTimezoneAsDuration() {
    	
//    	throw new RuntimeException("Timezone in CLDC version is not supported !!!");
    	int negative = 1;
    	int tzMin = 0;
    	int tzHour = 0;
//    	if (tzMin < 0) {
//    		negative = -1;
//    		tzMin = - tzMin;
//    	}
//    	if (tzMin > 59) {
//    		tzHour = tzMin / 60;
//    		tzMin = tzMin % 60;
//    	}
    	return new MXQueryDayTimeDuration(negative,0,tzHour,tzMin,0,0);
    }
    
	/** Java and CLDC use different Calendar classes.
	 *  Calendar object inicialization is hided by this method, 
	 *  since both versions have their own version of this class.   
	 **/
	public static Calendar getNewCalendar() {
		return Calendar.getInstance(); 
	}    
	
	/* timezone is not supported */
	public MXQueryDateTime adjustTimeZone(MXQueryDayTimeDuration dur) {
		// throw Runtime Exception ??
		return this;
	}
	

	
	
	
}
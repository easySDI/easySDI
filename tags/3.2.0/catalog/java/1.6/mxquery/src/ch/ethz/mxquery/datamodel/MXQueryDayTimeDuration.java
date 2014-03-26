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

import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.StaticException;


/** 
 * @author rokast
 * Specification at http://www.w3.org/TR/xmlschema11-2/#dayTimeDuration
 * Note: duration might be negative  
 * 
 * Implemented operators:
 * A + B xs:dayTimeDuration xs:dayTimeDuration op:add-dayTimeDurations(A, B) xs:dayTimeDuration
 * A - B xs:dayTimeDuration xs:dayTimeDuration op:subtract-dayTimeDurations(A, B) xs:dayTimeDuration
 * A * B xs:dayTimeDuration numeric op:multiply-dayTimeDuration(A, B) xs:dayTimeDuration 
 * A div B xs:dayTimeDuration numeric op:divide-dayTimeDuration(A, B) xs:dayTimeDuration
 * A gt B xs:dayTimeDuration xs:dayTimeDuration op:dayTimeDuration-greater-than(A, B) xs:boolean
 * A lt B xs:dayTimeDuration xs:dayTimeDuration op:dayTimeDuration-less-than(A, B) xs:boolean
 *   ge,le
 * */

public class MXQueryDayTimeDuration {

	private static final int MICROSEC = 1000000;
	private int negative = 1;
	private int days, hours, minutes, seconds, microsecond = 0;                  
	
	/* constructors */
	public MXQueryDayTimeDuration() {	}
	
	/** for negative duration value is -1, for possitive 1 */
	public MXQueryDayTimeDuration(int negative, int days, int hours, int minutes, int seconds, int microseconds) {
		this.negative = negative;
		this.days = days;
		this.hours = hours;
		this.minutes = minutes;
		this.seconds = seconds;
		this.microsecond = microseconds;
	}	
	
	public MXQueryDayTimeDuration(String input) throws MXQueryException{
		parse(input);
		normalizeDuration();
	}
	
	
	private void parse(String input) throws MXQueryException{
		
		input = input.trim();
		
		if(input.indexOf("+") >= 0 || input.indexOf("-") >= 1 ) 
			throw new StaticException(ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR, "Incorrect format of the Duration value:" +input, null);
		
		try {
			int posP = input.indexOf("P");
			int posD = input.indexOf("D");
			int posT = input.indexOf("T");
			int posH = input.indexOf("H");
			int posM = input.indexOf("M");
			int posDot = input.indexOf(".");
			int posS = input.indexOf("S");
			
			if (posP > 1 || posP < 0)
				throw new StaticException(ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR, "Incorrect format of the dayTimeDuration value:" +input, null);
			
			if (posT < 0 && posD < 0) 
				throw new StaticException(ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR, "Incorrect format of the dayTimeDuration value:" +input,null);
			
			if (posT > 0 && posH < 0 && posM < 0 && posS < 0 ) 
				throw new StaticException(ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR, "Incorrect format of the dayTimeDuration value:" +input,null);
						
//			System.out.println(posP);
//			System.out.println(posS);
//			System.out.println(posD);
//			System.out.println(input.length());
			
			if (posP == 1) negative = -1;		
//			System.out.println("negative:"+negative);
			
			// days value
			if (posD > 0) {
				days = Integer.parseInt(input.substring(posP+1, posD));
//				System.out.println("days:"+days);
			}
			
			// time value defined
			if (posT > 0) {
				
				//hours value
				if (posH >0) {
					hours = Integer.parseInt(input.substring(posT+1, posH));
//					System.out.println("hours:"+hours);
				}

				//minutes value
				int start = Math.max(posT+1, posH+1);
				if (posM >0) {				
					minutes = Integer.parseInt(input.substring( start, posM));
//					System.out.println("min:"+minutes);
				}
				
				//seconds value
				start = Math.max(start, posM+1);
				int end;
				if (posS >0) {
					if (posDot > 0) end = posDot;
					else end = posS;				
//					System.out.println("start:"+start);
//					System.out.println("end:"+end);
					String secStr = input.substring( start, end);
					if (secStr.equals("")) secStr = "0";
					seconds = Integer.parseInt(secStr);
//					System.out.println("sec:"+seconds);
				}

				// microsecond value
				if (posDot >0) {
					MXQueryDouble fractionSec = new MXQueryDouble("0."+ input.substring(posDot+1, posS));
					this.microsecond = (int)fractionSec.multiply(MICROSEC).getLongValue();
//					System.out.println("fractionSec:"+fractionSec);
//					System.out.println("microsecond:"+microsecond);
				}
				
			}			
			
		} catch ( NumberFormatException nfe) {
			throw new StaticException(ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR,"Incorrect format of the dayTimeDuration value:" +input,null);
		}
		
	}

	public void setNegative() {
		negative = -1;
	}	
	
	/* getters */
	public int getDays() {
		return days*negative;
	}

	public int getHours() {
		return hours*negative;
	}

	public int getMinutes() {
		return minutes*negative;
	}

	public int getNegative() {
		return negative;
	}

	public int getSeconds() {
		return seconds*negative;
	}	

	public MXQueryDouble getSecondsWithMili() { // throws MXQueryException
		return (MXQueryDouble)(new MXQueryDouble(microsecond, MICROSEC)).add(seconds).multiply(negative);
//		MXQueryDouble val = (MXQueryDouble)fractionSec.add(seconds).multiply(negative);
//		return new MXQueryDouble(""+val);
	}
	
	/* without round */
	public int getMiliseconds() {
		return (int) (this.microsecond / 1000);
	}
	
	/**
	 * Returns true if all values are 0 
	 * */
	public boolean isNull() {
		return (days==0&&hours==0&&minutes==0&&seconds==0&&microsecond==0);
	}
	
	public String toString() {
		// normilize result
		//return secondsToDuration ( timeInSeconds() ).convertToString();
		return convertToString();
	}
	
	public String convertToString() {
		StringBuffer result = new StringBuffer();		
		if (negative == -1) result.append('-');
		
		//-- 0 seconds
		if (days == 0 && hours == 0 && minutes == 0 && seconds == 0 && microsecond == 0)
			return "PT0S";

		result.append('P');		
		if(days != 0){
			result.append(days);
			result.append('D');
		}
		if(hours != 0 ||minutes != 0 ||seconds != 0 || microsecond != 0){
			result.append("T");
		}
		
		if(hours != 0){
			result.append(hours);
			result.append('H');
		}
		if(minutes != 0){
			result.append(minutes);
			result.append('M');
		}
		if(seconds != 0 || microsecond != 0){
			
			result.append(seconds);
			
			if (microsecond != 0) {
	        	result.append('.');
	            int ms = microsecond;
	            int div = 100000;
	            while (ms > 0) {
	                int d = ms / div;
	                result.append((char)(d + '0'));
	                ms = ms % div;
	                div /= 10;
	            }
			}
			result.append('S');
		}
		
		return result.toString();
	}
	
	/** operators */
	
 	public boolean equals (Object o){
		if (o instanceof MXQueryDayTimeDuration) {
			return equals((MXQueryDayTimeDuration)o);
		}
		return false;
	}
	
	public boolean equals(MXQueryDayTimeDuration d) {
		return this.toString().equals(d.toString());
	}	
	
	public boolean unequals(MXQueryDayTimeDuration d) {
		return !this.toString().equals(d.toString());
	}
	
	public int compareTo(Object o) throws ClassCastException {
		if (o instanceof MXQueryDayTimeDuration)
			return (compareTo((MXQueryDayTimeDuration)o));
		throw new ClassCastException();
	}
	
	/**
	 * Compares 2 DayTimeDuration values
	 * @param d  - DayTimeDuration values to compare with
	 * @return
	 * 0 if the argument DayTimeDuration is equal to this DayTimeDuration
	 * -1 if this DayTimeDuration is less than DayTimeDuration argument
	 * 1 if this DayTimeDuration is greater than DayTimeDuration argument
	 */
	public int compareTo(MXQueryDayTimeDuration d) {
		
		if (isNull() && d.isNull())
			return 0;
		
		if (negative > d.negative)	return 1;
		if (negative < d.negative)	return -1;
		
		long thisValInMicroSec = this.timeInMicroSeconds(); 
		long argValInMicroSec = d.timeInMicroSeconds();
		
		// equal durations
		if (thisValInMicroSec == argValInMicroSec) return 0;
		
		if (thisValInMicroSec > argValInMicroSec)
			return 1;
		else 
			return -1;
	}

	/**
	 * Compares 2 DayTimeDuration values
	 * @param d  - DayTimeDuration values to compare with
	 * @return
	 * 0 if the argument DayTimeDuration is equal to this DayTimeDuration
	 * -1 if this DayTimeDuration is less than DayTimeDuration argument
	 * 1 if this DayTimeDuration is greater than DayTimeDuration argument
	 */
	public int compareTo(MXQueryYearMonthDuration d) {
		if (isNull() && d.isNull())
			return 0;
		else return -1;
//		if (negative > d.negative)	return 1;
//		if (negative < d.negative)	return -1;
//		
//		MXQueryDouble thisValInSec = this.timeInSeconds(); 
//		MXQueryDouble argValInSec = d.timeInSeconds();
//		
//		// equal durations
//		if (thisValInSec.equals(argValInSec)) return 0;
//		
//		// unequal duration
//		return thisValInSec.compareTo(argValInSec);				
	}	
	
	public MXQueryNumber divide(MXQueryDayTimeDuration d) throws MXQueryException {
		return new MXQueryDouble(timeInMicroSeconds()).divide(d.timeInMicroSeconds());
	}
	
	public MXQueryDayTimeDuration divide(double i) throws MXQueryException {
		if (i == 0) throw new DynamicException(ErrorCodes.F0019_OVERFLOW_UNDERFLOW_DURATION,"Division by 0 for xs:integer type is not allowed", null);
		
		long d1ValInSec = (long) (timeInMicroSeconds() / i);		
		
		return microSecondsToDuration(d1ValInSec);
	}
	
	public MXQueryDayTimeDuration multiply(double i) {
		long d1ValInSec = (long) (timeInMicroSeconds()*i);		
		
		return microSecondsToDuration(d1ValInSec);
	}
	
	public MXQueryDayTimeDuration add(MXQueryDayTimeDuration d) {
		return addsubt(this, d, 1);
	}
	
	public MXQueryDayTimeDuration subtract(MXQueryDayTimeDuration d) {
		return addsubt(this, d, -1);
	}	

	private MXQueryDayTimeDuration addsubt (MXQueryDayTimeDuration d1, MXQueryDayTimeDuration d2, int sign) {
		MXQueryDayTimeDuration dur = microSecondsToDuration(d1.timeInMicroSeconds()+(sign)*d2.timeInMicroSeconds());
		return dur;
	}	
	
	public long timeInMicroSeconds() {
		long dayssec = (long)days * 24 * 3600;
		long hoursec = (long)hours * 3600;
		long ressec = (dayssec + hoursec + (long)minutes * 60 + seconds)  ;
		long resmicrosec = (ressec * MICROSEC + microsecond) * negative;   
		
		return  resmicrosec;
	}	

	public static MXQueryDayTimeDuration microSecondsToDuration(long resValInMicroSec) {
		
		MXQueryDayTimeDuration result = new MXQueryDayTimeDuration();

		if (resValInMicroSec < 0) { 
			result.negative = -1;
			resValInMicroSec = resValInMicroSec * (-1); 
		}		
		
		long resValInSec = resValInMicroSec / MICROSEC;
		result.microsecond = (int) (resValInMicroSec - resValInSec * MICROSEC);
		
		//try { 		
			// count days
			result.days =  (int) (resValInSec / (long)(24 * 3600)) ;
			resValInSec = resValInSec - (((long)result.days) * 24 * 3600);
			
			// count hours
			result.hours =  (int)(resValInSec / (long) (3600)) ;
			resValInSec = resValInSec - (result.hours * 3600L);
	
			// count minutes
			result.minutes =  (int)(resValInSec /(long) 60) ;
			resValInSec = resValInSec - (result.minutes * 60L);
			
			// count seconds
			result.seconds =  (int)resValInSec;
			
			return result;
//		} catch (MXQueryException ex) { throw new RuntimeException(ex.toString()); }	
	}	
	
	private void normalizeDuration() {
		MXQueryDayTimeDuration normalized = microSecondsToDuration(this.timeInMicroSeconds());
		this.days = normalized.days;
		this.hours = normalized.hours;
		this.minutes = normalized.minutes;
		this.seconds = normalized.seconds;
		this.microsecond = normalized.microsecond;
		this.negative = normalized.negative;
	}	
	
	
	public boolean isTimeZoneValid() {
		if (this.hours > 14 || this.hours < -14 )
			return false;
		
		if (this.minutes % 30 != 0)
			return false;
		
		if (this.seconds != 0 || this.microsecond != 0)
			return false;
		
		
		return true;
	}

	/* (non-Javadoc)
	 * @see java.lang.Object#hashCode()
	 */
	public int hashCode() {
		return (((days*24 + hours)*60 + minutes)*60 + seconds)* negative ^ microsecond;
	} 
}



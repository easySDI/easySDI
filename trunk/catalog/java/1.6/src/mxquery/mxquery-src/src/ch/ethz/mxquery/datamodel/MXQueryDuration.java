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

import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.StaticException;


/** 
 * @author rokast
 * Specification at http://www.w3.org/TR/xmlschema11-2/#duration
 * Note: duration might be negative  
 * 
 * */

public class MXQueryDuration {

	private MXQueryYearMonthDuration yearMonthDur; 
	private MXQueryDayTimeDuration dayTimeDur;
	
	/* constructors */
	public MXQueryDuration() {	}
	
	public MXQueryDuration(MXQueryYearMonthDuration yD) {
		yearMonthDur = yD;
		dayTimeDur = new MXQueryDayTimeDuration();
	}
	
	public MXQueryDuration(MXQueryDayTimeDuration dD) {
		yearMonthDur = new MXQueryYearMonthDuration();
		dayTimeDur = dD;
	}

	public MXQueryDuration(String input) throws MXQueryException{
		parse(input);
	}
	
	// FIXME check all constraints properly !!!!
	private void parse(String input) throws MXQueryException{

		input = input.trim();
		
		if(input.indexOf("+") >= 0 || input.indexOf("-") >= 1 ) 
			throw new StaticException(ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR, "Incorrect format of the Duration value:" +input, null);
		
		int posP = input.indexOf("P");
		int posY = input.indexOf("Y");			
		int posM = input.indexOf("M");
		int posD = input.indexOf("D");
		int posT = input.indexOf("T");
		boolean yearMonthSet = false;
		
		if (posP > 1 || posP < 0)
			throw new StaticException(ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR, "Incorrect format of the Duration value:" +input, null);
		
		int posDayTimeDur = 0;
		// yearMonthDuration part present
		if ( posY > 0 || (posM > 0 && posM < posT) || (posM > 0 && posT < 0)  ) {
//			String yearMonth;
			if (posY > 0 && posM < 0) {
				posDayTimeDur = posY+1;
//				yearMonth = input.substring(0, posDayTimeDur);
//				System.out.println("1 yearMonth: " + yearMonth);
			}	
			else 
			if ( (posM > 0 && posM < posT) || (posM > 0 && posT < 0) ) {
				posDayTimeDur = posM+1;
//				yearMonth = input.substring(0, posDayTimeDur);
//				System.out.println("2 yearMonth: " + yearMonth);
			}
			else throw new StaticException(ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR, "#2 Incorrect format of the Duration value:" +input, null);
				
			yearMonthDur = new MXQueryYearMonthDuration(input.substring(0,posDayTimeDur));
			yearMonthSet = true;
		} else {
			// no yearMonth present = yearMonthDur = 0
			yearMonthDur = new MXQueryYearMonthDuration();
			yearMonthSet = false;
		}
		
		// dayTimeDuration part present			
		if (posD > 0 || posT > 0) {
			
			if( posDayTimeDur == 0 ) {
//				System.out.println(input);
				dayTimeDur = new MXQueryDayTimeDuration(input);
			}	
			else {
				String val = "P" + input.substring(posDayTimeDur);
//				System.out.println(val);
				if (yearMonthDur.getNegative() < 0)
					val = "-"+val;
				dayTimeDur = new MXQueryDayTimeDuration(val);
					
			}	
		} else if (yearMonthSet)
		{
			dayTimeDur = new MXQueryDayTimeDuration();
		}else 
			throw new StaticException(ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR, "#2 Incorrect format of the Duration value:" +input, null);
	
	}

	public String toString() {
		String res;
		
		if (!yearMonthDur.isNull() && !dayTimeDur.isNull()) {
		
			res = yearMonthDur.toString();
			String tDur = dayTimeDur.toString();
			int posP = tDur.indexOf("P");
			tDur = tDur.substring(posP+1);
			res = res + tDur;
//			System.out.println("res: " + res);
			return res;
		}

//		if (yearMonthDur.isNull() && dayTimeDur.isNull())
//			throw new RuntimeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE + " Value of Duration type is not initialized.");
		
		if (!yearMonthDur.isNull())
			return yearMonthDur.toString(); 
		else return dayTimeDur.toString();
	}
	
 	/* (non-Javadoc)
	 * @see java.lang.Object#hashCode()
	 */
	public int hashCode() {
		return dayTimeDur.hashCode() ^ yearMonthDur.hashCode();
	}

	public boolean equals (Object o){
		if (o instanceof MXQueryDuration) {
			return equals((MXQueryDuration)o);
		}
		return false;
	}	
	
	public boolean equals(MXQueryDuration d) {
		return this.toString().equals(d.toString());
	}	
	
	public boolean unequals(MXQueryDuration d) {
		return !this.toString().equals(d.toString());
	}	
	
	/** getters */
	public MXQueryYearMonthDuration getYearMonthDurationPart() {
		return yearMonthDur;
	}

	public MXQueryDayTimeDuration getDayTimeDurationPart() {
		return dayTimeDur;
	}		
	
	/**
	 * Compares 2 Duration values
	 * @param arg  - Duration value to compare with
	 * @return
	 * 0 if the argument Duration is equal to this Duration
	 * -1 if this YearMonthDuration is less than YearMonthDuration argument
	 * 1 if this YearMonthDuration is greater than YearMonthDuration argument
	 */
	public int compareTo(MXQueryDuration arg) {
		int ret = -200;
		if(yearMonthDur != null && arg.yearMonthDur !=null) {
			ret = yearMonthDur.compareTo(arg.yearMonthDur);
			if (ret == 0 && dayTimeDur != null && arg.dayTimeDur != null) {
				ret = dayTimeDur.compareTo(arg.dayTimeDur);
			}
			else {
				return ret;
			}
		}
		//FIXME: Other combinations of setting
		if (dayTimeDur != null && arg.dayTimeDur != null)
			ret = dayTimeDur.compareTo(arg.dayTimeDur);
		return ret;
	}
	
	/**
	 * Compares a duration value with a DayTimeDuration
	 * @param arg  - DayTimeDuration value to compare with
	 * @return
	 * 0 if the argument Duration is equal to this Duration
	 * ONLY FOR EQ/NEQ COMPARISON 
	 */
	public int compareTo(MXQueryDayTimeDuration arg) throws MXQueryException{
		if (!yearMonthDur.isNull())
			return -1; // -1 for neq

			return dayTimeDur.compareTo(arg);
	}
	
	/**
	 * Compares a duration value with a YearMonthDuration
	 * @param arg  - YearMonthDuration value to compare with
	 * @return
	 * 0 if the argument Duration is equal to this Duration, -1 not equal
	 * ONLY FOR EQ/NEQ COMPARISON 
	 */
	public int compareTo(MXQueryYearMonthDuration arg) throws MXQueryException{
		if (!dayTimeDur.isNull())
			return -1; // -1 for neq
		
			return yearMonthDur.compareTo(arg);	}
	
}



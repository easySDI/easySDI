/*   Copyright 2006 ETH Zurich 
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

/**
 * author Rokas Tamosevicius 
 */

package ch.ethz.mxquery.datamodel;

import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;

public class MXQueryGregorian {
	public static final int UNDEFINED = Integer.MIN_VALUE;
	private static final String errCode = ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR;
	
	private int type = UNDEFINED;	
	private int tzMin =  UNDEFINED;
	private int day   =  1;
	private int month =  1;
	private int year  =  2000;
	
	public MXQueryGregorian(int year, int month, int day, int tzMinutes, int type) throws MXQueryException {
		this.type = type;
		this.tzMin = tzMinutes;
		
		switch(type){
			case Type.G_DAY:
				this.day = day;
			break;
			case Type.G_MONTH:
				this.month = month;
			break;
			case Type.G_MONTH_DAY:
				this.day = day;
				this.month = month;
			break;
			case Type.G_YEAR:
				this.year = year;
			break;
			case Type.G_YEAR_MONTH:
				this.year = year;
				this.month = month;
			break;
		}
	}	
	
	public MXQueryGregorian(String s, int type) throws MXQueryException {
		this.type = type;
		s = s.trim();
		
		switch(type){
			case Type.G_DAY:
				parseDay(s);
			break;
			case Type.G_MONTH:
				parseMonth(s);
			break;
			case Type.G_MONTH_DAY:
				parseMonthDay(s);
			break;
			case Type.G_YEAR:
				parseYear(s);
			break;
			case Type.G_YEAR_MONTH:
				parseYearMonth(s);
			break;
		}
	}
	
	private void parseDay(String sParam) throws MXQueryException {
		String s = parseTimeZone(sParam);
		
		if (this.type == Type.G_DAY) {
			if (! s.startsWith("---"))
				throw new DynamicException(errCode, "Invalid value '" + sParam + "' for gMonthDay", null);
			
			s = s.substring(3);
		}
		
		if (s.length() != 2)
			throw new DynamicException(errCode, "Cannot parse '" + sParam + "' into day", null);
		
		try {
			this.day = Integer.parseInt(s);
		} catch (NumberFormatException e) {
			throw new DynamicException(errCode, "Cannot parse '" + sParam + "' into day", null);
		}
	
		if (day < 1 || day > 31 )
			throw new DynamicException(errCode, "Invalid value '" + sParam + "' for day", null);
	}
	
	private void parseMonth(String sParam) throws MXQueryException {
		String s = parseTimeZone(sParam);
		
		if (this.type == Type.G_MONTH || this.type ==  Type.G_MONTH_DAY) {
			if (! s.startsWith("--"))
				throw new DynamicException(errCode, "Invalid value '" + sParam + "' for gMonth", null);
			
			s = s.substring(2);
		}
		
		if (s.length() != 2)
			throw new DynamicException(errCode, "Cannot parse '" + sParam + "' into month", null);
		
		
		try {
			this.month = Integer.parseInt(s);
		} catch (NumberFormatException e) {
			throw new DynamicException(errCode, "Cannot parse '" + sParam + "' into month", null);
		}
	
		if (month < 1 || month > 12 )
			throw new DynamicException(errCode, "Invalid value '" + sParam + "' for month", null);
	}
	
	private void parseYear(String sParam) throws MXQueryException  {
		String s = parseTimeZone(sParam);

		int n;
		if ( s.startsWith("-") ) n = 5;
		else n = 4;
			
		if ( s.length() != n  )
			throw new DynamicException(errCode, "Invalid value '" + sParam + "' for year", null);
		
		try {
			this.year = Integer.parseInt(s);
		} catch (NumberFormatException e) {
			throw new DynamicException(errCode, "Cannot parse '" + sParam + "' into year", null);
		}
	
		if (year == 0 )
			throw new DynamicException(errCode, "Invalid value '" + sParam + "' for year", null);
	}
	
	private void parseYearMonth(String sParam) throws MXQueryException {
		String s = parseTimeZone(sParam);
		
		if (s.length() < 7)
			throw new DynamicException(errCode, "Invalid value '" + sParam + "' for gYearMonth", null);
		
		if ( s.startsWith("-") ) {
			parseYear(s.substring(0, 5) );  // negative year
			parseMonth(s.substring(6) );
		} else { 
			parseYear(s.substring(0, 4) );
			parseMonth(s.substring(5) );
		}	
	}
	
	private void parseMonthDay(String s) throws MXQueryException {
		s = parseTimeZone(s);
		
		if (s.length() < 7)
			throw new DynamicException(errCode, "Invalid value '" + s + "' for gMonthDay", null);
		
		parseMonth(s.substring(0, 4) );
		parseDay(s.substring(5) );
		
		int maxDay = 29;
		switch(month){
			case 4:
			case 6:
			case 9:
			case 11:
				maxDay = 30;
			break;	
			case 2:		
				maxDay = 29;
			break;	
			default:
				maxDay = 31;
		}
		
		if (day > maxDay)
			throw new DynamicException(errCode, "Invalid value '" + s + "' for gMonthDay", null);
	}

	private String parseTimeZone(String s) throws MXQueryException {
		if (s == null)
			throw new DynamicException(errCode, "Invalid value null", null); 
					
		if ( s.length() > 2 &&  s.indexOf('Z') > 0){
			s = s.substring(0, s.length()-1);
			tzMin = 0;
			return s;
		} else {
			if (s.length() > 6 && s.indexOf(':') > 0  ){
				String tzVal = s.substring( s.length()-6, s.length());
				//parse sign
				int sign = UNDEFINED;
				if ( tzVal.startsWith("+") ){
					sign = 1;
				} else if ( tzVal.startsWith("-") ){
					sign = -1;
				}
				else throw new DynamicException(errCode, "Invalid timezone value: " + s, null);

				int tzHours =0;
				try {
					tzHours = Integer.parseInt( tzVal.substring(1,3) );
				} catch (NumberFormatException e) {
					throw new DynamicException(errCode, "Cannot parse '" + s + "' timezone hours", null);
				}

				int tzM =0;
				try {
					tzM = Integer.parseInt( tzVal.substring(4,tzVal.length()) );
				} catch (NumberFormatException e) {
					throw new DynamicException(errCode, "Cannot parse '" + s + "' timezone minutes", null);
				}
				
				if (tzM > 59 )
					throw new DynamicException(errCode, "Timezone value with '" + s + "' incorrect minute part", null);
					
				if (tzHours * 60 + tzM > 14 * 60)  
					throw new DynamicException(errCode, "Timezone value '"+ s + "' is out of range", null);
				
				this.tzMin = sign * (tzHours * 60 + tzM);
				s = s.substring(0, s.length()-6);
			}	
		}
		
		return s;
	}
	
	
    public final boolean hasTimezone() {
        return tzMin != UNDEFINED;
    }		
	
	public int getDay() {
		return day;
	}

	public int getMonth() {
		return month;
	}

	public int getType() {
		return type;
	}

	public int getTzMin() {
		return tzMin;
	}

	public int getYear() {
		return year;
	}
		
	public String toString() {
		StringBuffer sb = new StringBuffer();
		switch(type){
		case Type.G_DAY:
			sb.append("---" + getValMonthDay(day));
		break;
		case Type.G_MONTH:
			sb.append("--" + getValMonthDay(month));
		break;
		case Type.G_MONTH_DAY:
			sb.append("--" + getValMonthDay(month) + "-" + getValMonthDay(day));
		break;
		case Type.G_YEAR:
			sb.append( getValYear(year) );
		break;
		case Type.G_YEAR_MONTH:
			sb.append( getValYear(year) + "-" + getValMonthDay(month));
		break;
	}		

		if ( hasTimezone() )  
			MXQueryDateTime.appendTimezone(sb, tzMin);
		
		return sb.toString();
	}
	
	private static final String getValMonthDay(int val){
		String tmp = ("0" + val);
		return tmp.substring( tmp.length()-2);
	}

	private static final  String getValYear(int val){
		String tmp;
		if (val < 0) {
			tmp = ("0000" + (-1 *val) );
			tmp = tmp.substring( tmp.length()-4);
			tmp = "-" + tmp; 
		} else {
			tmp = ("0000" + val);
			tmp = tmp.substring( tmp.length()-4);
		}
		
		return tmp;
	}

 	/* (non-Javadoc)
	 * @see java.lang.Object#hashCode()
	 */
	public int hashCode() {
		return (((year * 12)+month) *30 + day) ^ tzMin ^ type;
	}

	public boolean equals (Object o){
		if (o instanceof MXQueryGregorian) {
			return equals((MXQueryGregorian)o);
		}
		return false;
	}	
	
	/**
	 * Compares too Gregorian values of the same type 
	 * @param g  - Gregorian value to compare with
	 * @return true if the values are equal
	 */
	
	public boolean equals(MXQueryGregorian g) {
		if ( this.type != g.type){
			return false;
		}

		MXQueryDateTime date1 = new MXQueryDateTime 
		(this.year, ((byte)this.month), ((byte)this.day), ((byte)0), ((byte)0), ((byte)0), 0, this.tzMin, MXQueryDateTime.VALUE_TYPE_DATE_TIME);

		MXQueryDateTime date2 = new MXQueryDateTime 
		(g.year, ((byte)g.month), ((byte)g.day), ((byte)0), ((byte)0), ((byte)0), 0, g.tzMin, MXQueryDateTime.VALUE_TYPE_DATE_TIME);
		
		boolean res = date1.equals(date2);
		
		return res;
	} 
	
	/**
	 * Compares too Gregorian values of the same type 
	 * @param arg  - Gregorian value to compare with
	 * @return
	 * 0 if the argument Gregorian is equal to this Gregorian
	 * ONLY FOR EQ/NEQ COMPARISON 
	 */
	public int compareTo(MXQueryGregorian arg) throws MXQueryException{
		if (this.equals(arg))
			 return 0;
		else return -1;
	}	
	
}

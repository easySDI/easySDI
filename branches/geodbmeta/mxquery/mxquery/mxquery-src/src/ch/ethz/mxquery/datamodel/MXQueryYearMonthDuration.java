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
 * Specification at http://www.w3.org/TR/xmlschema11-2/#yearMonthDuration
 * Note: duration might be negative  
 * 
 * Implemented operators:
 * A + B xs:yearMonthDuration xs:yearMonthDuration op:add-yearMonthDurations(A, B) xs:yearMonthDuration
 * A - B xs:yearMonthDuration xs:yearMonthDuration op:subtract-yearMonthDurations(A, B) xs:yearMonthDuration
 * A * B xs:yearMonthDuration numeric op:multiply-yearMonthDuration(A, B) xs:yearMonthDuration 
 * A div B xs:yearMonthDuration numeric op:divide-yearMonthDuration(A, B) xs:yearMonthDuration
 * A gt B xs:yearMonthDuration xs:yearMonthDuration op:yearMonthDuration-greater-than(A, B) xs:boolean
 * A lt B xs:yearMonthDuration xs:yearMonthDuration op:yearMonthDuration-less-than(A, B) xs:boolean
 *   ge,le
 * */

public class MXQueryYearMonthDuration {

	private int negative = 1;
	private int years, months = 0;
	
	/* constructors */
	public MXQueryYearMonthDuration() {	}
	
	/** for negative duration value is -1, for positive 1 */
	private MXQueryYearMonthDuration(int negative, int years, int months) {
		this.negative = negative;
		this.years = years;
		this.months = months;
		//normalizeDuration();
	}	
	
	public MXQueryYearMonthDuration(String input) throws MXQueryException{
		parse(input);
		normalizeDuration();
	}
	
	// FIXME check all constraints properly !!!!
	private void parse(String input) throws MXQueryException{
		
		input = input.trim();
		
		if(input.indexOf("+") >= 0 || input.indexOf("-") >=	 1 ) 
			throw new StaticException(ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR, "Incorrect format of the Duration value:" +input, null);
		
		try {
			int posP = input.indexOf("P");
			int posY = input.indexOf("Y");			
			int posM = input.indexOf("M");
			int posD = input.indexOf("D");
			int posT = input.indexOf("T");
			
			if (posP > 1 || posP < 0 || posD > 0 || posT > 0)
				throw new StaticException(ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR, "Incorrect format of the yearMonthDuration value:" +input, null);
			
			if (posY > 0 && posM > 0 && posM < posY) 
				throw new StaticException(ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR, "Incorrect format of the yearMonthDuration value:" +input, null);
			
			if (posY < 0 && posM < 0) 
				throw new StaticException(ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR, "Incorrect format of the dayTimeDuration value:" +input, null);
			
			
//			System.out.println(posP);
//			System.out.println(posY);
//			System.out.println(posM);
//			System.out.println(input.length());
			
			if (posP == 1) negative = -1;		
//			System.out.println("negative:"+negative);
			
			
			// years value
			if (posY > 0) {
				years = Integer.parseInt(input.substring(posP+1, posY));
//				System.out.println("years:"+years);
			}
			
			// months value
			if (posM > 0) {
				int start = Math.max(posP+1, posY+1);
				months = Integer.parseInt(input.substring(start, posM));
//				System.out.println("months:"+months);
			}
			
		} catch ( NumberFormatException nfe) {
			throw new StaticException(ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR,"Incorrect format of the dayTimeDuration value:" +input, null);
		}
		
	}

	/* getters */
	public int getYears() {
		return years*negative;
	}

	public int getMonths() {
		return months*negative;
	}

	public int getNegative() {
		return negative;
	}	
	
	/**
	 * Returns true if all values are 0 
	 * */
	public boolean isNull() {
		return (years==0&&months==0);
	}

	
	public String toString() {
		StringBuffer result = new StringBuffer();		
		if (negative == -1) result.append('-');
		
		if (months > 11)
			normalizeDuration();
			
		//-- 0M
		if (years == 0 && months == 0 )
			return "P0M";

		result.append('P');		
		if(years != 0){
			result.append(years);
			result.append('Y');
		}
		
		if(months != 0){
			result.append(months);
			result.append('M');
		}
		
		
		
		return result.toString();
	}
	
	/** operators */
	
 	public boolean equals (Object o){
		if (o instanceof MXQueryYearMonthDuration) {
			return equals((MXQueryYearMonthDuration)o);
		}
		return false;
	}	
	
	public boolean equals(MXQueryYearMonthDuration d) {
		return this.toString().equals(d.toString());
	}	
	
	public boolean unequals(MXQueryYearMonthDuration d) {
		return !this.toString().equals(d.toString());
	}
	/**
	 * Compares 2 YearMonthDuration values
	 * @param arg  - YearMonthDuration values to compare with
	 * @return
	 * 0 if the argument YearMonthDuration is equal to this YearMonthDuration
	 * -1 if this YearMonthDuration is less than YearMonthDuration argument
	 * 1 if this YearMonthDuration is greater than YearMonthDuration argument
	 */
	public int compareTo(MXQueryYearMonthDuration arg) {
		if (isNull() && arg.isNull())
			return 0;
		if (this.negative > arg.negative) return 1;
		if (this.negative < arg.negative) return -1;
		
		int thisValInMonths = this.getDurationInMonths();  
		int argValInMonths = arg.getDurationInMonths();
		
		if (thisValInMonths == argValInMonths) return 0;
		
		if (thisValInMonths > argValInMonths) return 1;
		
		return -1;
	}

//	public MXQueryYearMonthDuration divide(int i) {
//		int valInMonths = this.getDurationInMonths();
//		valInMonths = valInMonths / i;
//			
//		return monthsToDuration(valInMonths);
//	}
//	
//	public MXQueryYearMonthDuration multiply(int i) {
//		int valInMonths = this.getDurationInMonths();
//		valInMonths = valInMonths * i;
//			
//		return monthsToDuration(valInMonths);
//	}


	public MXQueryYearMonthDuration add(MXQueryYearMonthDuration d) {
		return addsubt(this, d, 1);
	}


	public MXQueryYearMonthDuration subtract(MXQueryYearMonthDuration d) {
		return addsubt(this, d, -1);
	}	

	private MXQueryYearMonthDuration addsubt (MXQueryYearMonthDuration d1, MXQueryYearMonthDuration d2, int sign) {
		
		int d1ValInMonths = d1.getDurationInMonths();
		int d2ValInMonths = d2.getDurationInMonths();
		
		// add or subtract
		int resValInMonths = d1ValInMonths + (sign * d2ValInMonths); 
		
		return monthsToDuration(resValInMonths);
	}	
	
	public MXQueryYearMonthDuration multiply (MXQueryDouble d) throws MXQueryException {
		long months = ((MXQueryDouble)d.multiply(getDurationInMonths())).round().getLongValue();
		return monthsToDuration((int)months);
	}
	
	public MXQueryNumber divide(MXQueryYearMonthDuration d) throws MXQueryException {
		return new MXQueryDouble(getDurationInMonths()).divide(d.getDurationInMonths());
	}
	
	public MXQueryYearMonthDuration divide (MXQueryDouble d) throws MXQueryException{
		if (d.getValue() == 0) throw new DynamicException(ErrorCodes.F0019_OVERFLOW_UNDERFLOW_DURATION,"Division by 0 for xs:integer type is not allowed", null);
		long months = ((MXQueryDouble)new MXQueryDouble(getDurationInMonths()).divide(d)).round().getLongValue();
		return monthsToDuration((int)months);
	}

	
	private int getDurationInMonths() {
		return negative * (12 * this.years + this.months);
	}
	
	
	private void normalizeDuration() {
		MXQueryYearMonthDuration normalized = monthsToDuration( this.getDurationInMonths() );
		this.months = normalized.months;
		this.years = normalized.years;
	}	
		
	private MXQueryYearMonthDuration monthsToDuration(int pMonths) {
		int y, m = 0;
		int neg = 1;
		
		if (pMonths < 0) {
			neg = -1;
			pMonths = pMonths * neg;
		}
		
		y = pMonths / 12;
		m = pMonths - (y * 12);  
		
		return new MXQueryYearMonthDuration(neg, y, m);
	}

	/* (non-Javadoc)
	 * @see java.lang.Object#hashCode()
	 */
	public int hashCode() {
		return (years *12 + months)*negative;
	} 
	
}



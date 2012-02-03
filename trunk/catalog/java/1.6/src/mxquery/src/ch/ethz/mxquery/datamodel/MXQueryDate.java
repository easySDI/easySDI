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

import ch.ethz.mxquery.exceptions.MXQueryException;

/**
 * @author rokast 
 * Specification at http://www.w3.org/TR/xmlschema11-2/#date
 */

public class MXQueryDate {

	private MXQueryDateTime val;
	
	public MXQueryDate (MXQueryDateTime dt) {
		val = dt;
//		val.setType(MXQueryDateTime.VALUE_TYPE_DATE);
	}
	
	public MXQueryDate(String input) throws MXQueryException {
		val = new MXQueryDateTime(input, MXQueryDateTime.VALUE_TYPE_DATE);
	}
	
	/* methods */	
	public String toString() {
		return val.toString();
	}
	
	public int hashCode() {
		return val.hashCode();
	}

	public boolean equals (Object o){
		if (o instanceof MXQueryDate) {
			return equals((MXQueryDate)o);
		}
		return false;
	}
	
	public boolean equals(MXQueryDate d) {
		return this.val.equals(d.val);
	}	
		
	public boolean unequals(MXQueryDate d) {
		return !this.val.equals(d.val);
	}
	
	/**
	 * Compares 2 Date values
	 * @param d Date values to compare with
	 * @return
	 * 0 if the argument Date is equal to this Date
	 * a value less than 0 if this Date is before the Date argument
	 * a value greater than 0 if this Date is after the Date argument
	 */
	public int compareTo(MXQueryDate d) {
		return this.val.compareTo(d.val);
	}
	
	public MXQueryDate addDuration(MXQueryDayTimeDuration d) {
		return new MXQueryDate(val.addDuration(d));
	}	
	
	public MXQueryDate subtractDuration(MXQueryDayTimeDuration d) {
		return new MXQueryDate(val.subtractDuration(d));
	}	
	
	public MXQueryDate addDuration(MXQueryYearMonthDuration d) {
		return new MXQueryDate(val.addDuration(d));
	}	
	
	public MXQueryDate subtractDuration(MXQueryYearMonthDuration d) {
		return new MXQueryDate(val.subtractDuration(d));
	}	
	
	
	public MXQueryDayTimeDuration subtract(MXQueryDate d) {
		return this.val.subtractDateTime(d.val);
	}	
	
	public MXQueryDate adjustTimeZone(MXQueryDayTimeDuration d) {
		return new MXQueryDate(this.val.adjustTimeZone(d));
	}	
	
	public boolean hasTimezone(){
		return this.val.hasTimezone();
	}
		
	/** getters */
	public int getYear() {
		return val.getYear();
	}
	
	public int getMonth() {
		return val.getMonth();
	}

	public int getDay() {
		return val.getDay();
	}
	
	public String datePartToString() throws MXQueryException {
		return val.datePartToString();
	}	
	
	public String getTimeZone() {
		return val.getTimeZone();
	}

    public final int getTimezoneInMinutes() {
        return val.getTimezoneInMinutes();
    } 	
    public final MXQueryDayTimeDuration getTimezoneAsDuration() {
    	return val.getTimezoneAsDuration();
    }

}



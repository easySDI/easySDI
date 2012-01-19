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
 * Specification at http://www.w3.org/TR/xmlschema11-2/#time
 */

public class MXQueryTime {

	private MXQueryDateTime val;
	
	public MXQueryTime (MXQueryDateTime dt) {
		val = dt;
//		val.setType(MXQueryDateTime.VALUE_TYPE_TIME);
	}
	
	public MXQueryTime(String input) throws MXQueryException {
		val = new MXQueryDateTime(input, MXQueryDateTime.VALUE_TYPE_TIME);
	}
	
	/* methods */	
	public String toString() {
		return val.toString();
	}

 	/* (non-Javadoc)
	 * @see java.lang.Object#hashCode()
	 */
	public int hashCode() {
		return val.hashCode();
	}

	public boolean equals (Object o){
		if (o instanceof MXQueryTime) {
			return equals((MXQueryTime)o);
		}
		return false;
	}	
	
	public boolean equals(MXQueryTime d) {
		return this.val.equals(d.val);
	}	
		
	public boolean unequals(MXQueryTime d) {
		return !this.val.equals(d.val);
	}
	
	/**
	 * Compares 2 Time values
	 * @param d Time values to compare with
	 * @return
	 * 0 if the argument Time is equal to this Time
	 * a value less than 0 if this Time is before the Time argument
	 * a value greater than 0 if this Time is after the Time argument
	 */
	public int compareTo(MXQueryTime d) {
		return this.val.compareTo(d.val); 
	}
	
	public MXQueryTime addDuration(MXQueryDayTimeDuration d) {
		return new MXQueryTime(val.addDuration(d));
	}	
	
	public MXQueryTime subtractDuration(MXQueryDayTimeDuration d) {
		return new MXQueryTime(val.subtractDuration(d));
	}	
	
	public MXQueryDayTimeDuration subtract(MXQueryTime t) {
		return this.val.subtractDateTime(t.val);
	}	
	
	public MXQueryTime adjustTimeZone(MXQueryDayTimeDuration d) {
		return new MXQueryTime(this.val.adjustTimeZone(d));
	}	
	
	
	/** getters */
	public int getHours() {
		return val.getHours();
	}
	
	public int getMinutes() {
		return val.getMinutes();
	}

	public MXQueryDouble getSecondsWithMili() throws MXQueryException{
		return val.getSecondsWithMili();
	}
	
	public int getSeconds() {
		return val.getSeconds();
	}
	
	public int getMiliseconds() {
		return val.getMiliseconds();
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



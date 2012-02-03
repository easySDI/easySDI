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
 * @author trokas
 */

public interface MXQueryNumber {

	public abstract boolean equalsZero();
	
	public abstract MXQueryNumber add(MXQueryNumber d);
	
	public abstract MXQueryNumber add(long l);
	
	public abstract MXQueryNumber subtract(MXQueryNumber d);

	public abstract MXQueryNumber subtract(long l);	
	
	public abstract MXQueryNumber multiply(MXQueryNumber d);
		
	public abstract MXQueryNumber multiply(long l);
	
	public abstract MXQueryNumber divide(MXQueryNumber d) throws MXQueryException;

	public abstract MXQueryNumber divide(long l) throws MXQueryException;
	
	public abstract MXQueryNumber mod (MXQueryNumber d);

	public abstract MXQueryNumber mod (long l);

	public abstract long idiv(MXQueryNumber d) throws MXQueryException;
	
	public abstract long idiv(long l) throws MXQueryException;
	
	public abstract boolean equals(MXQueryNumber d);

	public abstract boolean unequals(MXQueryNumber d);	
	
	/* -1, 0 or 1 as this MXQueryNumber is numerically less than, equal to, or greater than parameter d */
	public abstract int compareTo(MXQueryNumber d);	

	public abstract int compareTo(long l);
	
	public abstract MXQueryNumber negate();
	
	public abstract MXQueryDouble getDoubleValue();	
	
	public abstract MXQueryFloat getFloatValue();
	
	public abstract long getLongValue() throws MXQueryException;
	
	public abstract String toString();
	
	public abstract String toDecimalString();
	
	public abstract int getType();
	
	public abstract boolean isNaN();
	
	public abstract boolean isNegativeInfinity();
	
	public abstract boolean isPositiveInfinity();
}

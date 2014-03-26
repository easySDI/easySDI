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
import ch.ethz.mxquery.datamodel.types.Type;

/**
 * @author trokas
 */

public class MXQueryBigDecimal implements MXQueryNumber {

	private MXQueryDouble val;
	
	/** Constructors: */
	public MXQueryBigDecimal(String s) throws MXQueryException{
		//3. is valid decimal number
		if (s.endsWith(".")) s = s.substring(0,s.length()-1);
			val = new MXQueryDouble(s);

	}
	
	public MXQueryBigDecimal(long l) {
		val = new MXQueryDouble(l);
	}	

	
	public MXQueryBigDecimal(MXQueryDouble d) {
		val = d;
	}	
	
	/** Methods: */
	public boolean equalsZero(){
		return val.equalsZero();
	} 
	
	public MXQueryNumber add(MXQueryNumber d){
		return this.getDoubleValue().add( d );
	} 
	
	public MXQueryNumber add(long l){
		return this.getDoubleValue().add( new MXQueryDouble(l) );
	}
	
	public MXQueryNumber subtract(MXQueryNumber d){
		return this.getDoubleValue().subtract( d );
	} 

	public MXQueryNumber subtract(long l){
		return this.getDoubleValue().subtract( new MXQueryDouble(l) );
	} 	
	
	public MXQueryNumber multiply(MXQueryNumber d){
		return this.getDoubleValue().multiply( d );
	} 
		
	public MXQueryNumber multiply(long l){
		return this.getDoubleValue().multiply( new MXQueryDouble(l) );
	}	
	
	public MXQueryNumber divide(MXQueryNumber d) throws MXQueryException{
		
		if ( d.getDoubleValue().equalsZero() ) {
	    	throw new DynamicException(ErrorCodes.F0002_DIVISION_BY_ZERO,"Division by 0 for xs:decimal type is not allowed",null);
	    }		
		
		return this.getDoubleValue().divide( d );
	}

	public MXQueryNumber divide(long l){		
		return this.getDoubleValue().divide( new MXQueryDouble(l) );
	} 	
	
	public MXQueryNumber mod (MXQueryNumber d){
		return this.getDoubleValue().mod( d );
	} 

	public MXQueryNumber mod (long l){
		return this.getDoubleValue().mod( new MXQueryDouble(l) );
	} 

	public long idiv(MXQueryNumber d) throws MXQueryException{
		if ( d.getDoubleValue().equalsZero() ) {
	    	throw new DynamicException(ErrorCodes.F0002_DIVISION_BY_ZERO,"Division by 0 for xs:decimal type is not allowed",null);
	    }
		return this.getDoubleValue().idiv( d );
	} 
	
	public long idiv(long l) throws MXQueryException {
		return this.getDoubleValue().idiv( new MXQueryDouble(l) );
	}	
	
	public boolean equals(MXQueryNumber d) {
		return (this.getDoubleValue().equals( d ) );
	}	

	public boolean unequals(MXQueryNumber d) {
		return ! this.equals( d );
	}	
	
	/* -1, 0 or 1 as this MXQueryBigDecimal is numerically less than, equal to, or greater than parameter d */
	public int compareTo(MXQueryNumber d) {
		return this.getDoubleValue().compareTo( d );
	}	

	public int compareTo(long l) {
		return this.getDoubleValue().compareTo(new MXQueryDouble(l));
	}	
	
	public MXQueryNumber negate() {
		return this.getDoubleValue().negate();
	} 
	
	public MXQueryDouble getDoubleValue() {
		return this.val;
	} 	

	public MXQueryFloat getFloatValue() {
		return this.val.getFloatValue();
	} 	
	
	public long getLongValue() throws MXQueryException {
		return this.val.getLongValue();
	}
	
	public String toString() {
		return this.val.toString();
	}
	
	public String toDecimalString() {
		return this.val.toDecimalString();
	}
		
	public boolean isNaN() { return false; }
	
	public boolean isNegativeInfinity()  { return false; }
	
	public boolean isPositiveInfinity()  { return false; }	
	
	public int getType() { return Type.DECIMAL;	}	
}

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

import ch.ethz.mxquery.exceptions.*;
import ch.ethz.mxquery.datamodel.MXQueryDouble;
import ch.ethz.mxquery.datamodel.MXQueryNumber;
import ch.ethz.mxquery.datamodel.types.Type;

/**
 * @author trokas
 */

public class MXQueryFloat  implements MXQueryNumber  {

	private long val;
	private boolean startsWithDot = false;
	private boolean negativeZero = false;
	public static String VALUE_NAN = "NaN";
	public static String VALUE_POS_INFINITY = "INF";
	public static String VALUE_NEG_INFINITY = "-INF";
	
	/*  XML Schema: http://www.w3.org/TR/xmlschema11-2/#double */
	/*  -.123  to -0.123 is OK! */
	/* (-|+)?(([0-9]+(.[0-9]*)?)|(.[0-9]+))((e|E)(-|+)?[0-9]+)?|-?INF|NaN  */
	
	/** Constructors: */
	public MXQueryFloat(String s) throws MXQueryException {
		
		//-- handle special values
		if ( s.equals(VALUE_NAN)  ) { 
			throw new StaticException(ErrorCodes.A0002_EC_NOT_SUPPORTED, "NaN is not supported", null);
		} 
		else  
			if ( s.equals(VALUE_POS_INFINITY) || s.equals(VALUE_NEG_INFINITY) ) 
				throw new StaticException(ErrorCodes.A0002_EC_NOT_SUPPORTED, "Infinity is not supported", null);
		//-- handle special values		
		
		if (s.startsWith(".")) {
			startsWithDot = true;
			s = "0" + s; 
		}
			
		try {
			//val = Double.parseDouble(s);
			val = Integer.parseInt(s);
		} catch (NumberFormatException nfe) { 
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Not a double value", null); 
		}	
	}
	
	public MXQueryFloat(int i) {
		val = i;
		//val = (double)i;
	}	
	
	/**
	 *  MXQueryDouble created by dividing 2 integers
	 * @param i1 tobe divided
	 * @param i2 divider
	 */
	public MXQueryFloat(int i1, int i2) {
		//val = (double)i1 / (double)i2 ;
		val = (int) (i1 / i2) ;
	}	

	/**
	 *  MXQueryDouble created by dividing 2 longs
	 * @param l1 to be divided
	 * @param l2 divider
	 */
	public MXQueryFloat(long l1, long l2) {
		val = l1 / l2 ;
	}
	
	public MXQueryFloat(long l) {
		val = l;
	}

	
	/** Methods: */
	public boolean equalsZero(){
		return (this.val == 0);
	} 
	
	public MXQueryNumber add(MXQueryNumber d){
		return new MXQueryFloat(this.val + d.getFloatValue().val);
	} 

	public MXQueryNumber add(int i){
		return new MXQueryFloat(this.val + i);
	} 
	public MXQueryNumber add(long l){
		return new MXQueryFloat(this.val + l);
	}
	
	public MXQueryNumber subtract(MXQueryNumber d){
		return new MXQueryFloat(this.val - d.getFloatValue().val);
	} 

	public MXQueryNumber subtract(int i){
		return new MXQueryFloat(this.val - i);
	} 
	
	public MXQueryNumber subtract(long i){
		return new MXQueryFloat(this.val - i);
	}	
	
	public MXQueryNumber multiply(MXQueryNumber d){
		return new MXQueryFloat(this.val * d.getFloatValue().val);
	} 
	
	public MXQueryNumber multiply(int i){
		return new MXQueryFloat(this.val * i);
	}	
	public MXQueryNumber multiply(long l){
		return new MXQueryFloat(this.val * l);
	}		
	public MXQueryNumber divide(MXQueryNumber d){
		return new MXQueryFloat(this.val / d.getFloatValue().val);
	} 

	public MXQueryNumber divide(int i){
		return new MXQueryFloat(this.val / i);
	} 
	public MXQueryNumber divide(long l){
		return new MXQueryFloat(this.val / l);
	} 
	public MXQueryNumber mod (MXQueryNumber d){
		return new MXQueryFloat(this.val % d.getFloatValue().val);
	} 

	public MXQueryNumber mod (int i){
		return new MXQueryFloat(this.val % i);
	} 
	public MXQueryNumber mod (long l){
		return new MXQueryFloat(this.val % l);
	} 
	public long idiv(MXQueryNumber d) throws MXQueryException{
		if (isNaN() || d.isNaN() || isNegativeInfinity() || d.isNegativeInfinity() ||
			isPositiveInfinity() || d.isPositiveInfinity()) 
			throw new DynamicException(ErrorCodes.F0003_OVERFLOW_UNDERFLOW_NUMERIC, "Invalid input for idiv", null);
		long res;
		try {
			res = ((long)(this.val / d.getFloatValue().val));
		} catch (ArithmeticException ae) {
			throw new MXQueryException(ErrorCodes.F0002_DIVISION_BY_ZERO,"Division by Zero",null);
		}
		return res;
	} 
	
	public long idiv(long i) throws MXQueryException {
		if (isNaN() || isNegativeInfinity() || isPositiveInfinity()) 
				throw new DynamicException(ErrorCodes.F0003_OVERFLOW_UNDERFLOW_NUMERIC, "Invalid input for idiv",null);
		
		long res;
		try {
			res = ( (long)(this.val / i) );
		} catch (ArithmeticException ae) {
			throw new MXQueryException(ErrorCodes.F0002_DIVISION_BY_ZERO,"Division by Zero",null);
		}
		return res;

	}		
	public boolean equals(MXQueryNumber d) {
		return (this.val == d.getFloatValue().val);
	}	

	public boolean unequals(MXQueryNumber d) {
		return (this.val != d.getFloatValue().val);
	}	
	
	public boolean isNaN() {
		return false; //( Double.isNaN(this.val));
	}	

	public boolean isNegativeInfinity() {
		//return (this.val == Double.NEGATIVE_INFINITY);
		return false;
	}	
	
	public boolean isPositiveInfinity() {
		//return (this.val == Double.POSITIVE_INFINITY);
		return false;
	}	

	public boolean hasFractionPart() {
		if ( isPositiveInfinity() || isNegativeInfinity() ) return false;
		return !(this.val == (int)this.val);
	}	
	
	/* -1, 0 or 1 as this MXQueryDouble is numerically less than, equal to, or greater than parameter d */
	public int compareTo(MXQueryNumber d) {
		if (this.val > d.getFloatValue().val)
			return 1;
		else if (this.val < d.getFloatValue().val)
			return -1;
		else
			return 0;
	}	
	
	public int compareTo(int i) {
		return this.compareTo(new MXQueryFloat(i));
	}

	public int compareTo(long l) {
		return this.compareTo(new MXQueryFloat(l));
	}	
	
	public MXQueryNumber negate() {
		return new MXQueryFloat(-1 * this.val);
	} 
	
	public double getValue() {
		return this.val;
	}
	
	// throws MXQueryException to be compatable with standart MXQuery edition
	public long getLongValue() throws MXQueryException {
		return (long)this.val;
	}
		
	// throws MXQueryException to be compatable with standart MXQuery edition	
	public int getIntValue()  throws MXQueryException {
		return (int)this.val;
	}
	
	public String toString() {
		
		if (startsWithDot) {
			String s = "" + val; 
			s = s.substring(1);
			if (negativeZero)
				s = "-"+s;
			return s;
		}
		
		// .0 is removed
		int iVal = (int) val;
		if (iVal == val) {
			String ret = "" + iVal;
			if (negativeZero)
				ret = "-"+ret;
			return ret;
		}
		String ret = "" + val;
		if (negativeZero)
			ret = "-"+ret;
		return ret;
	}

	public String toTypedString(boolean isFloat) {
		return toString();
	}
	
	public String toFormatString(int digit) {
		return "";
//		NumberFormat f = NumberFormat.getInstance(Locale.ENGLISH);
//		f.setMaximumFractionDigits(3);
//		return f.format(val);
		
	}

	public MXQueryDouble getDoubleValue() {
		return new MXQueryDouble(val);
	}

	public MXQueryFloat getFloatValue() {
		return this;
	}

	
	public String toDecimalString() {
		return toString();
	}

	public int getType() {
		return Type.DECIMAL;
	}
	public MXQueryFloat round() throws MXQueryException {
		return this;
	}
	// limit value space to float values
	public MXQueryFloat limitFloat() {
		return this;
	}	
	
	
}

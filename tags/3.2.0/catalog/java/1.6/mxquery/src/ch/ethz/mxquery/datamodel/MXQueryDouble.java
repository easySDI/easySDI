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

import java.text.NumberFormat;
import java.util.Locale;
import java.util.regex.Matcher;

import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.util.Utils;

/**
 * @author trokas
 */

public class MXQueryDouble implements MXQueryNumber {

	private double val;
	
	private boolean negativeZero = false;
	
	//private boolean startsWithDot = false;
	public final static String VALUE_NAN = "NaN";
	public final static String VALUE_POS_INFINITY = "INF";
	public final static String VALUE_NEG_INFINITY = "-INF";
	/*  XML Schema: http://www.w3.org/TR/xmlschema11-2/#double */
	/*  -.123  to -0.123 is OK! */
	/* (-|+)?(([0-9]+(.[0-9]*)?)|(.[0-9]+))((e|E)(-|+)?[0-9]+)?|-?INF|NaN  */
	
	/** Constructors: */
	public MXQueryDouble(String s) throws MXQueryException{
		s = Utils.replaceAll(s, "null", "");
		s = s.trim();
		
		boolean isNegative = false;
		
		if (s.startsWith("-"))
			isNegative = true;
		
		//-- handle special values
		if ( s.equals(VALUE_NAN) ) { 
			val = Double.NaN;
			return;
		} 
		else  
			if ( s.equals(VALUE_POS_INFINITY) ) { 
				val = Double.POSITIVE_INFINITY;
				return;
			} 
			else  
				if ( s.equals(VALUE_NEG_INFINITY) ) { 
					val = Double.NEGATIVE_INFINITY;
					return;
				} 
		//-- handle special values
		
		if (s.startsWith(".")) {
			//startsWithDot = true;
			s = "0" + s;
		}
			
		try {
			val = Double.parseDouble(s);
		} catch (NumberFormatException nfe) { throw new TypeException(ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR, "Not a double value", null); }
		if (val == 0.0)
			negativeZero = isNegative;
	}
	
	public MXQueryDouble(int i) {
		val = (double)i;
	}	
	
	/**
	 *  MXQueryDouble created by dividing 2 integers
	 * @param i1 tobe divided
	 * @param i2 divider
	 */
	public MXQueryDouble(int i1, int i2) {
		val = (double)i1 / (double)i2 ;
	}	
	
	/**
	 *  MXQueryDouble created by dividing 2 longs
	 * @param l1 to be divided
	 * @param l2 divider
	 */
	public MXQueryDouble(long l1, long l2) {
		val = (double)l1 / (double)l2 ;
	}	
		
	
	public MXQueryDouble(double d) {
		val = d;
	}

	/** Methods: */
	public boolean equalsZero(){
		return (this.val == 0);
	} 
	
	public MXQueryNumber add(MXQueryNumber d){
		return new MXQueryDouble(this.val + d.getDoubleValue().val);
	} 

	public MXQueryNumber add(long l){
		return new MXQueryDouble(this.val + l);
	}
	
	public MXQueryNumber subtract(MXQueryNumber d){
		return new MXQueryDouble(this.val - d.getDoubleValue().val);
	} 

	public MXQueryNumber subtract(long l){
		return new MXQueryDouble(this.val - l);
	} 
	
	public MXQueryNumber multiply(MXQueryNumber d){
		return new MXQueryDouble(this.val * d.getDoubleValue().val);
	} 
	
	public MXQueryNumber multiply(long l){
		return new MXQueryDouble(this.val * l);
	}
	
	public MXQueryNumber divide(MXQueryNumber d){
		return new MXQueryDouble(this.val / d.getDoubleValue().val);
	} 

	public MXQueryNumber divide(long l){
		return new MXQueryDouble(this.val / l);
	} 	
	
	public MXQueryNumber mod (MXQueryNumber d){
		return new MXQueryDouble(this.val % d.getDoubleValue().val);
	} 

	public MXQueryNumber mod (long l){
		return new MXQueryDouble(this.val % l);
	} 
	public long idiv(MXQueryNumber d) throws MXQueryException{
//		if (isNaN() || d.isNaN() || isNegativeInfinity() || d.isNegativeInfinity() ||
//			isPositiveInfinity() || d.isPositiveInfinity()) 
//			throw new DynamicException(ErrorCodes.F0003_OVERFLOW_UNDERFLOW_NUMERIC, "Invalid input for idiv");
		if (d.isNaN() || isNaN() || isNegativeInfinity() || isPositiveInfinity()) {
			throw new DynamicException(ErrorCodes.F0003_OVERFLOW_UNDERFLOW_NUMERIC, "Invalid input for idiv", null);
		}
		
		if (d.isNegativeInfinity()) {
			return -0;
		} else if (d.isPositiveInfinity()) {
			return 0;
		}
		
		long res;
		try {
			res = ((long)(this.val / d.getDoubleValue().val));
		} catch (ArithmeticException ae) {
			throw new MXQueryException(ErrorCodes.F0002_DIVISION_BY_ZERO,"Division by Zero", null);
		}
		return res;
	} 
	
	public long idiv(long i) throws MXQueryException {
		if (isNaN() || isNegativeInfinity() || isPositiveInfinity()) 
				throw new DynamicException(ErrorCodes.F0003_OVERFLOW_UNDERFLOW_NUMERIC, "Invalid input for idiv", null);
		
		long res;
		try {
			res = ( (long)(this.val / i) );
		} catch (ArithmeticException ae) {
			throw new MXQueryException(ErrorCodes.F0002_DIVISION_BY_ZERO,"Division by Zero",null);
		}
		return res;

	}	

	public boolean isNaN() {
		return ( Double.isNaN(this.val));
	}	

	public boolean isNegativeInfinity() {
		return (this.val <= Double.NEGATIVE_INFINITY);
	}	
	
	public boolean isPositiveInfinity() {
		return (this.val >= Double.POSITIVE_INFINITY);
	}	

	public boolean hasFractionPart() {
		if ( isPositiveInfinity() || isNegativeInfinity() ) return false;
		return !(Math.abs(this.val -(int)this.val) < 0.00000001 );
	}		
	
	public boolean equals(MXQueryNumber d) {
		return (Math.abs(this.val-d.getDoubleValue().val) < 0.00000001);
	}	

	public boolean unequals(MXQueryNumber d) {
		return (Math.abs(this.val-d.getDoubleValue().val) > 0.00000001);
	}	
	
	/* -1, 0 or 1 as this MXQueryDouble is numerically less than, equal to, or greater than parameter d */
	/* -2 and +2 if NaN is involved */
	public int compareTo(MXQueryNumber d) {
		// NaN handling
		if (isNaN() && d.isNaN())
			return -3;
		if (isNaN())
			return -2;
		if (d.isNaN())
			return 2;
		if (this.val > d.getDoubleValue().val)
			return 1;
		else if (this.val < d.getDoubleValue().val)
			return -1;
		else
			return 0;
	}	
	
	public int compareTo(long l) {
		return this.compareTo(new MXQueryDouble(l));
	}	
	
	public MXQueryNumber negate() {
		return new MXQueryDouble(-1 * this.val);
	} 
	
	public double getValue() {
		return this.val;
	} 	
	
	public int getIntValue() throws MXQueryException {
		if (isPositiveInfinity() || isNegativeInfinity() || isNaN() )
			throw new DynamicException(ErrorCodes.F0005_INVALID_LEXICAL_VALUE, "Not an integer value", null);		
	
		return (int)this.val;
	}
	
	public long getLongValue() throws MXQueryException {
		if (isPositiveInfinity() || isNegativeInfinity() || isNaN() )
			throw new DynamicException(ErrorCodes.F0005_INVALID_LEXICAL_VALUE, "Not an integer value", null);		
		
		double tmp = this.val -1;
		long res = (long)this.val;
			
		if (res < tmp)
			throw new DynamicException(ErrorCodes.A0002_EC_NOT_SUPPORTED, "Big Integer values are not supported.", null);
			
		return (long)this.val;
	}
	
	public MXQueryDouble round() throws MXQueryException {
		if (isNaN())
			return new MXQueryDouble(VALUE_NAN);
		double origValue = getValue();
		double val = Math.round(origValue);
		MXQueryDouble dbl = new MXQueryDouble(val);
		if (val == 0.0 && origValue < 0 || negativeZero)
			dbl.negativeZero = true;
		return dbl;
	}
	
	


    /**
     * Conversion of a double to a string
	 * @param value the actual double value
	 * @param javaStrValue the double value converted to string using the Java conventions. This value is adjusted as necessary to cater for the differences between the Java and XPath rules.
	 * @param isNegativeZero
	 * @return the value converted to a string, according to the XPath casting rules.
	 */
	
    public static String toString(double value, String javaStrValue, boolean isNegativeZero) {
        
    	if (value==0.0) {
            if (javaStrValue.charAt(0) == '-' || isNegativeZero) {
                return "-0";
            } else {
                return "0";
            }
        }
        
        if (Double.isInfinite(value)) {
            return (value > 0 ? "INF" : "-INF");
        }
        if (Double.isNaN(value)) {
            return "NaN";
        }
        
        final double absval = Math.abs(value);
        String s = javaStrValue;
        if (absval < 1.0e-6 || absval >= 1.0e+6) {
            if (s.indexOf('E')<0) {
                // need to use scientific notation, but Java isn't using it
                // (Java's cutoff is 1.0E7, while XPath's is 1.0E6)
                // So we have for example -2000000.0 rather than -2.0e6
                StringBuffer sb = new StringBuffer(32);
                Matcher matcher = nonExponentialPattern.matcher(s);
                
                if (matcher.matches()) {
                    sb.append(matcher.group(1));
                    sb.append('.');
                    sb.append(matcher.group(2));
                    final String fraction = matcher.group(4);
                    if ("0".equals(fraction)) {
                        sb.append("E" + (matcher.group(2).length() + matcher.group(3).length()));
                        return sb.toString();
                    } else {
                        sb.append(matcher.group(3));
                        sb.append(matcher.group(4));
                        sb.append("E" + (matcher.group(2).length() + matcher.group(3).length()));
                        return sb.toString();
                    }
                } else {
                    // fallback, this shouldn't happen
                    return s;
                }
            } else {
                return s;
            }
        }
        
        int len = s.length();
        if (s.endsWith("E0")) {
            s = s.substring(0, len - 2);
        }
        if (s.endsWith(".0")) {
            return s.substring(0, len - 2);
        }
        int e = s.indexOf('E');
        if (e < 0) {
            // For some reason, Double.toString() in Java can return strings such as "0.0040"
            // so we remove any trailing zeros
            while (s.charAt(len - 1) == '0' && s.charAt(len - 2) != '.') {
                s = s.substring(0, --len);
            }
            return s;
        }
        
        //-- E is present in the number
        int exp = Integer.parseInt(s.substring(e + 1));
        String sign;
        if (s.charAt(0) == '-') {
            sign = "-";
            s = s.substring(1);
            --e;
        } else {
            sign = "";
        }
        int nDigits = e - 2;
        if (exp >= nDigits) {
            return sign + s.substring(0, 1) + s.substring(2, e) + zeros(exp - nDigits);
        } else if (exp > 0) {
            return sign + s.substring(0, 1) + s.substring(2, 2 + exp) + '.' + s.substring(2 + exp, e);
        } else {
            while (s.charAt(e-1) == '0') e--;
            return sign + "0." + zeros(-1 - exp) + s.substring(0, 1) + s.substring(2, e);
        }
       //-- 
    }

    static String zeros(int n) {
        char[] buf = new char[n];
        for (int i = 0; i < n; i++)
            buf[i] = '0';
        return new String(buf);
    }	
	
    static java.util.regex.Pattern nonExponentialPattern =
        java.util.regex.Pattern.compile(
                "(-?[0-9])([0-9]+?)(0*)\\.([0-9]*)");
    
    
    public String toString() {
    	return MXQueryDouble.toString(val, ""+val, negativeZero);
    }

	public String toFormatString(int digit) {
	
		NumberFormat f = NumberFormat.getInstance(Locale.ENGLISH);
		f.setMaximumFractionDigits(3);
		return f.format(val);
		
	}
	public String toDecimalString() {
		NumberFormat f = NumberFormat.getInstance(Locale.ENGLISH);
		f.setMaximumFractionDigits(18);
		return f.format(val);
	}
	
	public int getType() {
		return Type.DOUBLE;
	}

	public MXQueryDouble getDoubleValue() {
		return this;
	}
	
	public MXQueryFloat getFloatValue() {
		return new MXQueryFloat ((float)val);
	}
}

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

import java.math.BigDecimal;
import java.math.BigInteger;
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

public class MXQueryFloat implements MXQueryNumber {

	private float val;
	private boolean negativeZero;
	
	//private boolean startsWithDot = false;
	public final static String VALUE_NAN = "NaN";
	public final static String VALUE_POS_INFINITY = "INF";
	public final static String VALUE_NEG_INFINITY = "-INF";
	/*  XML Schema: http://www.w3.org/TR/xmlschema11-2/#double */
	/*  -.123  to -0.123 is OK! */
	/* (-|+)?(([0-9]+(.[0-9]*)?)|(.[0-9]+))((e|E)(-|+)?[0-9]+)?|-?INF|NaN  */
	
	/** Constructors: */
	public MXQueryFloat(String s) throws MXQueryException{
		s = Utils.replaceAll(s, "null", "");
		s = s.trim();
		
		boolean isNegative = false;
		
		if (s.startsWith("-"))
			isNegative = true;
		
		//-- handle special values
		if ( s.equals(VALUE_NAN) ) { 
			val = Float.NaN;
			return;
		} 
		else  
			if ( s.equals(VALUE_POS_INFINITY) ) { 
				val = Float.POSITIVE_INFINITY;
				return;
			} 
			else  
				if ( s.equals(VALUE_NEG_INFINITY) ) { 
					val = Float.NEGATIVE_INFINITY;
					return;
				} 
		//-- handle special values
		
		if (s.startsWith(".")) {
			//startsWithDot = true;
			s = "0" + s;
		}
			
		try {
			val = Float.parseFloat(s);
		} catch (NumberFormatException nfe) { throw new TypeException(ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR, "Not a double value", null); }
		if (val == 0.0)
			negativeZero = isNegative;
	}
	
	public MXQueryFloat(int i) {
		val = (float)i;
	}	
	
	/**
	 *  MXQueryDouble created by dividing 2 integers
	 * @param i1 tobe divided
	 * @param i2 divider
	 */
	public MXQueryFloat(int i1, int i2) {
		val = (float)i1 / (float)i2 ;
	}	
	
	/**
	 *  MXQueryDouble created by dividing 2 longs
	 * @param l1 to be divided
	 * @param l2 divider
	 */
	public MXQueryFloat(long l1, long l2) {
		val = (float)l1 / (float)l2 ;
	}	
		
	
	public MXQueryFloat(float f) {
		val = f;
	}

	/** Methods: */
	public boolean equalsZero(){
		return (this.val == 0);
	} 
	
	public MXQueryNumber add(MXQueryNumber d){
		return new MXQueryFloat(this.val + d.getFloatValue().val);
	} 

	public MXQueryNumber add(long l){
		return new MXQueryFloat(this.val + l);
	}
	
	public MXQueryNumber subtract(MXQueryNumber d){
		return new MXQueryFloat(this.val - d.getFloatValue().val);
	} 

	public MXQueryNumber subtract(long l){
		return new MXQueryFloat(this.val - l);
	} 
	
	public MXQueryNumber multiply(MXQueryNumber d){
		return new MXQueryFloat(this.val * d.getFloatValue().val);
	} 
	
	public MXQueryNumber multiply(long l){
		return new MXQueryFloat(this.val * l);
	}
	
	public MXQueryNumber divide(MXQueryNumber d){
		return new MXQueryFloat(this.val / d.getFloatValue().val);
	} 

	public MXQueryNumber divide(long l){
		return new MXQueryFloat(this.val / l);
	} 	
	
	public MXQueryNumber mod (MXQueryNumber d){
		return new MXQueryFloat(this.val % d.getFloatValue().val);
	} 

	public MXQueryNumber mod (long l){
		return new MXQueryFloat(this.val % l);
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
			res = ((long)(this.val / d.getFloatValue().val));
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
		return (this.val == Float.NEGATIVE_INFINITY);
	}	
	
	public boolean isPositiveInfinity() {
		return (this.val == Float.POSITIVE_INFINITY);
	}	

	public boolean hasFractionPart() {
		if ( isPositiveInfinity() || isNegativeInfinity() ) return false;
		return !(Math.abs(this.val -(int)this.val) < .0000001 );
	}		
	
	public boolean equals(MXQueryNumber d) {
		return (Math.abs(this.val-d.getFloatValue().val) < .0000001);
	}	

	public boolean unequals(MXQueryNumber d) {
		return (Math.abs(this.val-d.getFloatValue().val) > .0000001);
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
		if (this.val > d.getFloatValue().val)
			return 1;
		else if (this.val < d.getFloatValue().val)
			return -1;
		else
			return 0;
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
	
	public MXQueryFloat round() throws MXQueryException {
		if (isNaN())
			return new MXQueryFloat(VALUE_NAN);
		double origValue = getValue();
		float val = Math.round(origValue);
		MXQueryFloat flt = new MXQueryFloat(val);
		if (val == 0.0 && origValue < 0 || negativeZero)
			flt.negativeZero = true;
		return flt;
	}
	

	private static double roundForOutput(float fl) {
		if (fl < (double)1e10 && fl > (double)-1e10)
			return fl;

		int precision = -8;
		
		if (fl > (double)1e15 || fl < (double)-1e15)
			precision = -10;
		
		long scale = 1;
		if (precision != 0) {
			if (precision > 0)
				for (int i=0;i<precision;i++)
					scale = scale*10;
			else 
				for (int i=precision;i<0;i++)
					scale = scale * 10;
		}
		
		BigDecimal scaleBd = new BigDecimal(scale);
		
		double ivd = (double)fl;
		
		BigDecimal iv = new BigDecimal(ivd);
		BigDecimal iv1 = iv;
		
		if (precision > 0)
			iv1 = iv.multiply(scaleBd);
		if (precision < 0)
			iv1 = iv.divide(scaleBd,BigDecimal.ROUND_HALF_EVEN);
		BigInteger intv = iv1.toBigInteger();
		BigDecimal nofrac1 = new BigDecimal(intv);
		if (precision < 0)
			nofrac1 = nofrac1.multiply(scaleBd);
		if (precision > 0)
			nofrac1 = nofrac1.divide(scaleBd,BigDecimal.ROUND_HALF_EVEN);

		BigDecimal diff = iv.subtract(nofrac1).abs();

		//double val = Math.round(inputValue.getValue())-inputValue.getIntValue();

		BigDecimal eps = iv.divide(new BigDecimal(scale/1000),BigDecimal.ROUND_HALF_EVEN).abs();
		
		if (diff.compareTo(eps)==-1)
			return nofrac1.doubleValue();
		else
			return fl;

	}
	
    /**
     * Conversion of a double to a string
     * @param the actual double value
     * @param the double value converted to string using the Java conventions.
     * This value is adjusted as necessary to cater for the differences between the Java and XPath rules.
     * @return the value converted to a string, according to the XPath casting rules.
     */

    public static String toString(float val, String javaStrValue, boolean negativeZero) {
        
    
    	
    	if (val==0.0) {
            if (javaStrValue.charAt(0) == '-'|| negativeZero) {
                return "-0";
            } else {
                return "0";
            }
        }
        
        if (Float.isInfinite(val)) {
            return (val > 0 ? "INF" : "-INF");
        }
        if (Float.isNaN(val)) {
            return "NaN";
        }
        
    	double value = roundForOutput(val);
       
    	 String s = javaStrValue;
         if (val != value)
         	s = ""+value;
    	
        //final float absval = (float)Math.abs(val);
        if ((val > 0 && (val < (1.0e-6)-5e-10 || val >= 1.0e+6))||
        		(val < 0 && (val > (-1.0e-6)+5e-10 || val <= -1.0e+6))) {
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
    	return MXQueryFloat.toString(val, ""+val, negativeZero);
    }

    public String toTypedString(boolean isFloat) {
    	if (isFloat)
    		return MXQueryFloat.toString((float)val, ""+(float)val, negativeZero);
    	else
    		return MXQueryFloat.toString(val, ""+val, negativeZero);
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
		return Type.FLOAT;
	}

	public MXQueryDouble getDoubleValue() {
		return new MXQueryDouble (val);
	}

	public MXQueryFloat getFloatValue() {
		return this;
	}	
}

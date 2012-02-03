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
import java.text.NumberFormat;
import java.util.Locale;
import java.util.regex.Pattern;

import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;

/**
 * @author trokas
 */

public class MXQueryBigDecimal implements MXQueryNumber {

	private static final int DIVIDE_PRECISION = 18;
	
	private BigDecimal val;
	
	// needed for 1.4, 1.5 has BigDecimal.ZERO
	private static final BigDecimal NUM_ZERO = new BigDecimal((double)(0));
	
	private static final Pattern decimalPattern = Pattern.compile("(\\-|\\+)?((\\.[0-9]+)|([0-9]+(\\.[0-9]*)?))");
	
	/** Constructors: */
	public MXQueryBigDecimal(String s) throws MXQueryException{
		
		s = s.trim();
		//3. is valid decimal number
		if (s.endsWith(".")) s = s.substring(0,s.length()-1);
		
        if (!decimalPattern.matcher(s).matches()) {
        	throw new TypeException(ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR ,"String doesn't match xs:decimal pattern", null);
        }
		
		try {
			val = new BigDecimal(s);
		} catch(Exception e){
			throw new TypeException(ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR ,"BigDecimal parsing Exception", null); 
		}

	}
	
	public MXQueryBigDecimal(long l) {
		// 1.4 does not have BigDecimal(Long), only BigDecimal(double)
		// using it with a long value may cause a rounding errors, since the implicit cast to double
		// loses precision
		val = new BigDecimal(Long.toString(l));  
	}	

	
	public MXQueryBigDecimal(BigDecimal d) {
		val = d;
	}	
	
	public MXQueryBigDecimal(MXQueryDouble d) throws MXQueryException {
		
		try {
			val = new BigDecimal(d.getValue());
		} catch(Exception e){
			throw new DynamicException(ErrorCodes.F0005_INVALID_LEXICAL_VALUE, "Not a decimal value", null);
		}
	}	
	
	/** Methods: */
	public boolean equalsZero(){
		return val.signum() == 0;
	} 
	
	public MXQueryNumber add(MXQueryNumber d){
		int t = d.getType();
		if (t == Type.DECIMAL)
			return new MXQueryBigDecimal( this.val.add( ((MXQueryBigDecimal) d).val) );
		else 
			if (t== Type.FLOAT) {
				return this.getFloatValue().add(d);
			}
			return this.getDoubleValue().add(d);
	}	
	
	public MXQueryNumber add(long l){
		return new MXQueryBigDecimal( this.val.add( new BigDecimal(l) ) );
	}
	
	public MXQueryNumber subtract(MXQueryNumber d){
		int t = d.getType();
		if (t == Type.DECIMAL)
			return new MXQueryBigDecimal( this.val.subtract(((MXQueryBigDecimal) d).val) );
		else if (t== Type.FLOAT) {
			return this.getFloatValue().subtract(d);
		}
			return this.getDoubleValue().subtract(d);
	} 

	public MXQueryNumber subtract(long l){
		return new MXQueryBigDecimal( this.val.subtract( new BigDecimal(l) ) );
	} 
	
	
	public MXQueryNumber multiply(MXQueryNumber d){
		int t = d.getType();
		if (t == Type.DECIMAL)
			return new MXQueryBigDecimal(val.multiply(((MXQueryBigDecimal) d).val) );
		if (t== Type.FLOAT)
			return this.getFloatValue().multiply(d);
		else
			return this.getDoubleValue().multiply(d);
	} 

		
	public MXQueryNumber multiply(long l){
		return new MXQueryBigDecimal( this.val.multiply( new BigDecimal(l) ));
	}	

	
	public MXQueryNumber divide(MXQueryNumber d) throws MXQueryException {		
		int t = d.getType();
		if (t == Type.DECIMAL) {
			if ( d.equalsZero() ) { 
		    	throw new DynamicException(ErrorCodes.F0002_DIVISION_BY_ZERO,"Division by 0 for xs:decimal type is not allowed", null);
		    }
			
			int scale = Math.max(DIVIDE_PRECISION, Math.max(val.scale(), ((MXQueryBigDecimal) d).val.scale()));
		    BigDecimal result = val.divide(((MXQueryBigDecimal) d).val, scale, BigDecimal.ROUND_HALF_DOWN);
			return new MXQueryBigDecimal( result );
		}
		else
			return this.getDoubleValue().divide(d);
	} 


	public MXQueryNumber divide(long l) throws MXQueryException {
		return this.divide(new MXQueryBigDecimal(l));
	} 	
	
	public MXQueryNumber mod (MXQueryNumber d){
		
		int t = d.getType();
		if (t == Type.DECIMAL) {
		    BigDecimal quotient = val.divide(((MXQueryBigDecimal) d).val, 0, BigDecimal.ROUND_DOWN);
		    BigDecimal remainder = val.subtract(quotient.multiply( ((MXQueryBigDecimal) d).val ));
		    return new MXQueryBigDecimal(remainder);
		}
		if (t==Type.FLOAT)
			return this.getFloatValue().mod(d);
		else 
			return this.getDoubleValue().mod(d);
		
	} 

	public MXQueryNumber mod (long l){
		return this.mod(new MXQueryBigDecimal(l));
	} 

	public long idiv(MXQueryNumber d) throws MXQueryException{		
		
	    if ( d.equalsZero() ) {
	    	throw new DynamicException(ErrorCodes.F0002_DIVISION_BY_ZERO,"Integer division by 0 for xs:decimal type is not allowed", null);
	    }
		
		int t = d.getType();
		if (t == Type.DECIMAL) {
		    BigDecimal res = val.divide( ((MXQueryBigDecimal) d).val, 0, BigDecimal.ROUND_DOWN) ;
		    //return res.longValueExact();
		    return res.longValue();
		}
		if (t==Type.FLOAT)
			return this.getFloatValue().idiv(d);
		else 
			return this.getDoubleValue().idiv(d);
	} 
	
	public long idiv(long l) throws MXQueryException {
		return this.idiv(new MXQueryBigDecimal(l));
	}	

	
	public boolean equals(MXQueryNumber d) {
		int t = d.getType();
		if (t == Type.DECIMAL) {
			return (this.val.equals(((MXQueryBigDecimal) d).val) );
		}
		if (t== Type.FLOAT)
			return this.getFloatValue().equals(d);
		else return this.getDoubleValue().equals(d);
	}	

	public boolean unequals(MXQueryNumber d) {
		return !(this.val.equals(((MXQueryBigDecimal) d).val) );
	}	
	
	/* -1, 0 or 1 as this MXQueryBigDecimal is numerically less than, equal to, or greater than parameter d */
	public int compareTo(MXQueryNumber d) {	
		int t = d.getType();
		if (t == Type.DECIMAL)
			return this.val.compareTo(((MXQueryBigDecimal) d).val);
		else
			if (t==Type.FLOAT)
				return this.getFloatValue().compareTo(d);
			else
				return this.getDoubleValue().compareTo(d);
	}		

	public int compareTo(long l) {
		return this.compareTo(new MXQueryBigDecimal(l));
	}	
	
	public MXQueryNumber negate() {
		return new MXQueryBigDecimal( this.val.negate() );
	} 
	
	public MXQueryDouble getDoubleValue() {
		return new MXQueryDouble (this.val.doubleValue());
	} 	
	
	public MXQueryFloat getFloatValue() {
		return new MXQueryFloat (this.val.floatValue());
	}
	
	public long getLongValue() {
		return this.val.longValue();  //longValueExact() ???
	}	
	
	/* BigDecimal in java 1.5  has special method */
	public String toString() {

		if (val.compareTo(NUM_ZERO)== 0)
			return "0";
		// removing trailing 0
		int scale = this.val.scale();
		if ( scale > 0  ) {
			for (int i=0; i < scale; i++) {
				if ( this.val.setScale(i, BigDecimal.ROUND_DOWN).setScale(scale).compareTo(this.val) == 0){
					return this.val.setScale(i).toString();
				}
			}  
		}  
		return this.val.toString();
	}
	
	public String toDecimalString() {
		NumberFormat f = NumberFormat.getInstance(Locale.ENGLISH);
		f.setMaximumFractionDigits(18);
		return f.format(val);
	}
	
	public boolean isNaN() { return false; }
	
	public boolean isNegativeInfinity()  { return false; }
	
	public boolean isPositiveInfinity()  { return false; }	
	
	public int getType() { return Type.DECIMAL;	}
	
}

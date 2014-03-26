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
import ch.ethz.mxquery.util.Base64;

public class MXQueryBinary {
	public static final int UNDEFINED = Integer.MIN_VALUE;
	private static final String errCode = ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR;	
	
	private byte[] val = null;
	private int type = UNDEFINED;
	
	public MXQueryBinary(byte[] value, int type) {
		this.val = value;
		this.type = type;
	}

	public MXQueryBinary(String value, int type) throws MXQueryException  {
		this.type = type;
		value = value.trim();
		
		switch(type){
			case Type.HEX_BINARY:
				parseBase16(value);
			break;
			case Type.BASE64_BINARY:
				parseBase64(value);
			break;
		}	
	}	

	private void parseBase16(String s) throws MXQueryException {
		if (s.length() % 2 != 0)
			throw new DynamicException(errCode, "Invalid value '" + s + "' for hexBinary", null);
		
		int size = s.length() / 2;
		byte[] res = new byte[size];

		try {
			for (int i = 0; i < s.length(); i=i+2) {
				String curr = s.substring(i, i+2);
				int val = Integer.parseInt(curr, 16);
				res[i/2] = (byte)val;
			}
		} catch (NumberFormatException e) {
			throw new DynamicException(errCode, "Cannot parse '" + s+ "' into hexBinary", null);
		}

		this.val = res;
	}
	
	
	private void parseBase64(String s) throws MXQueryException {
		this.val = Base64.decode(s);
	}
	
	
	
	/* (non-Javadoc)
	 * @see java.lang.Object#hashCode()
	 */
	public int hashCode() {
		int res = type;
		if (val != null)
			for (int i=0;i<val.length;i++)
				res = (res + val[i])%Integer.MAX_VALUE-1;
		return res;
	}

	public boolean equals (Object o){
		if (o instanceof MXQueryBinary) {
			return equals((MXQueryBinary)o);
		}
		return false;
	}
	
	/**
	 * Compares two Binary values of the same type 
	 * @param b  - Binary value to compare with
	 * @return true if the values are equal
	 */
	
	public boolean equals(MXQueryBinary b) {
		if ( this.type != b.type){
			return false;
		}
		
		boolean res = (new String(this.val)).equals( new String(b.val) );
		return res;
	} 
	
	/**
	 * Compares too Binary values of the same type 
	 * @param arg  - Binary value to compare with
	 * @return
	 * 0 if the argument Binary is equal to this Binary
	 * ONLY FOR EQ/NEQ COMPARISON 
	 */
	public int compareTo(MXQueryBinary arg) throws MXQueryException{
		if (this.equals(arg))
			 return 0;
		else return -1;
	} 
	
	public String toString() {
		StringBuffer sb = new StringBuffer();
		
		switch(type){
			case Type.HEX_BINARY:
		        for( int i = 0; i < val.length; i++ ) {
		        	int tmp = val[i];
		        	if (tmp < 0) tmp = 256 + tmp; // no negative integers
		        	
		        	if (tmp < 16) sb.append("0" + Integer.toHexString(tmp)); 
		        	else sb.append(Integer.toHexString(tmp));
		        }
		    return (sb.toString()).toUpperCase();
			case Type.BASE64_BINARY:
				return Base64.encodeBytes(this.val);
			default:
				throw new RuntimeException("Binary type '" + type + "' is incorrect");
		}
	}
	
	public int getType() {
		return type;
	}

	public void setType(int t) {
		type = t;
	}
	
	public byte[] getValue() {
		return val;
	}
	
	
}

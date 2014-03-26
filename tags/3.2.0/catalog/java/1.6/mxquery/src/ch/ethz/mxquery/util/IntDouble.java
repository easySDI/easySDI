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

package ch.ethz.mxquery.util;

import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;

/**
 * Double implementation on an integer. <br />
 * NaN, Inf, and Exponential representation (e.g. 2e-4) are not supported
 * 
 * @author David Alexander Graf
 * 
 */
public class IntDouble {
	private int value;
	private int zero;
	public static final int maxDecimalPlaces = 9;
	private int decimalPlaces;
	private int intMax;

	/**
	 * Computes n * 10^i
	 * 
	 * @param n
	 * @param i
	 * @return n * 10^i
	 */
	private int addPowTen(int n, int i) {
		int value = 1;
		for (int j = 0; j < i; j++) {
			value *= 10;
		}
		return value * n;
	}

	/**
	 * Fills the str with commas at the begining till
	 * <code>str.length() == commaLength</code>.
	 * 
	 * @param str
	 * @param commaLength
	 * @return the string filled up with zeros
	 */
	public static String addStartZerosToComma(String str, int commaLength) {
		for (int i = (commaLength - str.length()); i > 0; i--) {
			str = "0" + str;
		}
		return str;
	}

	/**
	 * Deletes all zeros at the end of str.
	 * 
	 * @param str
	 * @return the string with the zeros removed
	 */
	public static String deleteEndZeros(String str) {
		int index = str.length() - 1;

		while (index > 0 && str.charAt(index) == '0') {
			index--;
		}
		return str.substring(0, index + 1);
	}

	/**
	 * Counts the prefix zeros.
	 * 
	 * @param str
	 * @return the number of prefix zeros
	 */
	public static int countStartZeros(String str) {
		int index = 0;
		while (index < str.length() && str.charAt(index) == '0') {
			index++;
		}
		return index;
	}

	/**
	 * Returns the last digit of the passed value.
	 * 
	 * @param value
	 * @return the last digit
	 */
	public static int getLastDigit(int value) {
		int cur = (value / 10) * 10;
		return value - cur;
	}

	/**
	 * Sets up all constants
	 * 
	 * @param decimalPlaces
	 *            The thing that the user must define
	 */
	private void setUp(int decimalPlaces) {
		if (decimalPlaces > IntDouble.maxDecimalPlaces) {
			throw new NumberFormatException("Too many decimal places!");
		} else if (decimalPlaces < 0) {
			throw new NumberFormatException("No negativ decimal places!");
		}
		this.decimalPlaces = decimalPlaces;
		this.zero = this.addPowTen(1, this.decimalPlaces);
		this.intMax = Integer.MAX_VALUE / this.zero - 1;

	}

	/**
	 * Constructor
	 * 
	 * @param decimalPlaces
	 */
	public IntDouble(int decimalPlaces) {
		this.setUp(decimalPlaces);
		this.value = 0;
	}

	/**
	 * Constructor
	 * 
	 * @param str
	 *            tries to parse this string
	 * @param decimalPlaces
	 */
	public IntDouble(String str, int decimalPlaces) {
		this.setUp(decimalPlaces);
		int intValue = 0;
		int commaValue = 0;

		boolean negative = false;

		if (str.startsWith("-")) {
			negative = true;
			str = str.substring(1);
		} else if (str.startsWith("+")) {
			str = str.substring(1);
		}
		if (str.indexOf(".") >= 0) {
			if (str.startsWith(".")) {
				str = "0" + str;
			}
			
			int dotIndex = str.indexOf(".");
			String strInt = str.substring(0, dotIndex);
			String strComma = str.substring(dotIndex + 1, str.length());

			strComma = IntDouble.deleteEndZeros(strComma);
			if (strComma.length() > this.decimalPlaces) {
				throw new NumberFormatException("Overflow");
			}
			commaValue = Integer.parseInt(strComma);
			if (commaValue != 0) {
				int commaZeros = IntDouble.countStartZeros(strComma);
				commaValue = this.addPowTen(commaValue, this.decimalPlaces
						- String.valueOf(commaValue).length() - commaZeros);
			}
			intValue = Integer.parseInt(strInt);
		} else {
			intValue = Integer.parseInt(str);
		}
		if (intValue > this.intMax) {
			throw new NumberFormatException("Overflow");
		}

		this.value = intValue * this.zero + commaValue;
		if (negative) {
			this.value = -this.value;
		}
	}

	/**
	 * Constructor
	 * 
	 * @param init
	 *            initial value (only integer)
	 * @param decimalPlaces
	 */
	public IntDouble(int init, int decimalPlaces) {
		this.setUp(decimalPlaces);
		this.value = init * this.zero;
	}

	private IntDouble(int init, int decimalPlaces, boolean origValue) {
		this.setUp(decimalPlaces);
		if (origValue) {
			this.value = init;
		} else {
			this.value = init * this.zero;
		}
	}

	public boolean equalsZero() {
		return this.value == 0;
	}

	public IntDouble add(IntDouble id) {
		int value = this.value + id.value;
		return new IntDouble(value, this.decimalPlaces, true);
	}

	public IntDouble add(int i) {
		int value = this.value + i * this.zero;
		return new IntDouble(value, this.decimalPlaces, true);
	}

	public IntDouble subtract(IntDouble id) {
		int value = this.value - id.value;
		return new IntDouble(value, this.decimalPlaces, true);
	}

	public IntDouble subtract(int i) {
		int value = this.value - i * this.zero;
		return new IntDouble(value, this.decimalPlaces, true);
	}

	public IntDouble multiply(IntDouble id) {
		int val = 0;
		int cur = this.value;
		int foreignValue = id.value;
		int multiplier = 1;
		boolean negativ = false;
		
		if (cur < 0) {
			cur = -cur;
			negativ = !negativ;
		}
		if (foreignValue < 0) {
			foreignValue = -foreignValue;
			negativ = !negativ;
		}

		for (int i = 0; i < 10; i++) {
			if (cur == 0) {
				break;
			}
			int digit = IntDouble.getLastDigit(cur);
			if (i < this.decimalPlaces) {
				val += foreignValue * digit;
				val /= 10;
			} else {
				val += multiplier * foreignValue * digit;
				multiplier *= 10;
			}
			cur = cur / 10;
		}

		if (negativ) {
			val = -val;
		}
		
		return new IntDouble(val, this.decimalPlaces, true);
	}

	public IntDouble multiply(int i) {
		int value = this.value * i;
		return new IntDouble(value, this.decimalPlaces, true);
	}

	public IntDouble divide(IntDouble id) throws MXQueryException {
		int index = 1000000000;
		int restValue = this.value;
		int rest = 0;
		int result = 0;
		int foreignValue = id.value;
		boolean negativ = false;
		if (restValue < 0) {
			restValue = -restValue;
			negativ = !negativ;
		}
		if (foreignValue < 0) {
			foreignValue = -foreignValue;
			negativ = !negativ;
		}

		for (int i = 0; i < this.decimalPlaces; i++) {
			int cur = restValue / index;
			restValue -= cur * index;
			cur += 10 * rest;
			if (cur / foreignValue > 0) {
				throw new DynamicException(ErrorCodes.F0003_OVERFLOW_UNDERFLOW_NUMERIC, "overflow", null);
			}
			rest = cur % foreignValue;
			index /= 10;
		}

		int i2 = 1000000000;
		for (int i = 0; i < 10; i++) {
			int cur = 0;
			if (index > 0) {
				cur = restValue / index;
				restValue -= cur * index;
			}
			cur += 10 * rest;
			int res = cur / foreignValue;
			if (i == 0) {
				if (res > 2) {
					throw new DynamicException(ErrorCodes.F0003_OVERFLOW_UNDERFLOW_NUMERIC, "overflow", null);
				}
			}
			result += i2 * res;
			rest = cur % foreignValue;
			index /= 10;
			i2 /= 10;
		}

		if (negativ) {
			result = -result;
		}
		return new IntDouble(result, this.decimalPlaces, true);
	}

	public IntDouble divide(int i) {
		int value = this.value / i;
		return new IntDouble(value, this.decimalPlaces, true);
	}
	
	public IntDouble mod(IntDouble id) {
		int value = this.value % id.value;
		return new IntDouble(value, this.decimalPlaces, true);
	}
	
	public IntDouble mod(int i) {
		int value = this.value % (i * this.zero);
		return new IntDouble(value, this.decimalPlaces, true);
	}
	
	public int idiv(IntDouble id) throws MXQueryException {
		IntDouble divd = this.divide(id);
		return divd.getIntValue();
	}
	
	public int idiv(int i) {
		IntDouble d = this.divide(i);
		return d.getIntValue();
	}

	public IntDouble negate() {
		int value = -this.value;
		return new IntDouble(value, this.decimalPlaces, true);
	}

	public int hashCode() {
		return value ^ zero;
	}	
	
	public boolean equals(Object o) {
		if (o instanceof IntDouble) {
			return equals((IntDouble)o);
		}
		return false;
	}
	
	public boolean equals(IntDouble id) {
		return this.value == id.value;
	}
	
	public boolean unequals(IntDouble id) {
		return this.value != id.value;
	}
	
	public int compareTo(IntDouble id) {
		if (this.value > id.value) {
			return 1;
		} else if (this.value < id.value) {
			return -1;
		} else {
			return 0;
		}
	}
	
	public int compareTo(int i) {
		return this.compareTo(new IntDouble(i, this.decimalPlaces));
	}

	private int getCommaValue() {
		int commaVal = this.value % this.zero;
		if (commaVal < 0) {
			return -commaVal;
		} else {
			return commaVal;
		}
	}

	public int getIntValue() {
		return this.value / this.zero;
	}

	public String toString() {
		String str = "";
		int intValue = this.getIntValue();
		if (intValue == 0 && this.value < 0) {
			str += "-";
		}
		return str
				+ String.valueOf(intValue)
				+ "."
				+ IntDouble.deleteEndZeros(IntDouble.addStartZerosToComma(
						String.valueOf(this.getCommaValue()), this.decimalPlaces));
	}
}

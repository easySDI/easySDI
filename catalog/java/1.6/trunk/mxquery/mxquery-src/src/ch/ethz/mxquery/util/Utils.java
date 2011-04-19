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

import java.io.IOException;
import java.io.InputStream;
import java.util.Vector;

import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.sms.ftstore.DiacriticsUtils;


public class Utils {

	/**
	 * 
	 * @param name - full class name 
	 * @return - class name without package name
	 */
	public static String getSimpleClassName(String name) {
		int lastDot = name.lastIndexOf('.') + 1;
		return name.substring(lastDot);
	}

	/**
	 * Splits a string into an array of Strings
	 * @param source	The source string
	 * @param delimeter	The delimeter String, that seperates the parts of the sequence
	 * @return			An array of all elements of the splitted sequence
	 */
	public static String[] split(String source, String delimeter) {
		int next = 0;
		Vector parts = new Vector();
		
		while ((next = source.indexOf(delimeter)) > -1) {
			if (source.substring(0, next).length() > 0) {
				parts.addElement(source.substring(0, next));
			}
			
			source = source.substring(next+delimeter.length());
		}
		
		if (source.length() > 0) {
			parts.addElement(source);	
		}
		
		String[] strs = new String[parts.size()];
		parts.copyInto(strs);
		
		return strs;
	}	
	
	
	public static String readString(InputStream is) {
		String text;
		// Extract data in 256 byte chunks.
		try {
		byte[] data = new byte[256];
		int len = 0;
		StringBuffer raw = new StringBuffer();
		while ( -1 != (len = is.read(data)) ) {
		raw.append(new String(data, 0, len));
		}
		text = raw.toString();
		} catch (IOException ex) {
		text = ex.toString();
		};
		return text;
	}
	
	/**
	 * Splits a string into an array of Strings
	 * @param source	The source string
	 * @param delimiters The delimiters, that seperate the parts of the sequence
	 * @return			An array of all elements of the splitted sequence
	 */
	
	public static String[] split(String source, String[] delimiters){
		
		int begin = 0;
		Vector parts = new Vector();
		String newElement = "";
		boolean delimitersFound = false;
		
		for (int i= 0; i < source.length(); i++){
			
			for (int j = 0; j < delimiters.length;j++){
				if (source.charAt(i)== delimiters[j].toCharArray()[0]){
					
					delimitersFound = true;
					
					if (source.substring(begin,i).length() > 0) {
						
						if (begin != 0){
							
							newElement = source.substring(begin+1,i);
							if (newElement.trim().length() > 0){
								parts.addElement(newElement);
							}
							
						}
						else{
							newElement = source.substring(begin,i);
							if (newElement.length() > 0){
								parts.addElement(newElement);
							}
						}
						begin = i;
						
					}
					else{
						begin = i+1;
					}
				}
				
			}	
			
		}
		
		if (delimitersFound){
			if (source.length() > 0) {
				parts.addElement(source.substring(begin+1));	
			}
		}
		else{
			parts.addElement(source.substring(begin));
		}
		
		
		String[] strs = new String[parts.size()];
		parts.copyInto(strs);
		return strs;
		
	}
	
	public static String replaceAll(String original, String oldExpr, String replacement) {
		String ret = "";
		int pos = 0, index;
		while ((index = original.indexOf(oldExpr)) >= 0) {
			ret += original.substring(pos, index);
			ret += replacement;
			original = original.substring(index + 1);
		}
		ret += original;
		return ret;
	}
	
	public static String expandCharRefs(String res) {
		StringBuffer normTarget = new StringBuffer(res.length());
		int origPos = 0;
		while (origPos < res.length()) {
			if (res.charAt(origPos) == '&' && origPos+3 < res.length() && res.charAt(origPos+1)=='#') {
				// translate remaining WS char refs in order to prevent them from being "eaten"
				int endCharRef = res.indexOf(';', origPos);
				if (endCharRef > 0 && endCharRef < origPos + 10) {
					String toTranslate = res.substring(origPos+2,endCharRef);
					int codePoint = -1;
					if (toTranslate.indexOf('x') >=0) {
						// Hex digits
						String charRef = toTranslate.substring(1);
						if (charRef.length() != 0)
							codePoint = Integer.parseInt(charRef, 16);
					} else { //Decimal digits
						String charRef = toTranslate;
						if (charRef.length() != 0)
							codePoint = Integer.parseInt(charRef,10);
					}
					if (codePoint >= 0) {
						normTarget.append(PlatformDependentUtils.codepointToChars(codePoint));
						origPos = endCharRef+1;
					} else {
						normTarget.append(res.charAt(origPos));
						origPos++;							
					}
				}
			} else {
				normTarget.append(res.charAt(origPos));
				origPos++;
			}
		}
		return normTarget.toString();
	}
	
	/**
	 * Implementation of normalization in attributes and element content - remove whitespace at beginning and end + reduce whitespace in the middle to one, but only translate xD,x9 etc.
	 * @param res String to be normalized
	 * @return the normalized String
	 */
	public static String normalizeStringContent (String res, boolean isIdAttr) {
		StringBuffer normTarget = new StringBuffer(res.length());
		int origPos = 0;
		// eat all whitespace at the beginning
		while (origPos < res.length() && isWhiteSpace(res.charAt(origPos))){
			if (!isIdAttr)
				normTarget.append(' ');
			origPos++;
		}
		if (origPos == res.length() && res.length() != 0)
			return " ";
		while(origPos < res.length()) {
			// append non-whitespace characters
			while (origPos < res.length() && !isWhiteSpace(res.charAt(origPos))) {
				if (res.charAt(origPos) == '&' && origPos+3 < res.length() && res.charAt(origPos+1)=='#') {
					// translate remaining WS char refs in order to prevent them from being "eaten"
					int endCharRef = res.indexOf(';', origPos);
					if (endCharRef > 0 && endCharRef < origPos + 10) {
						String toTranslate = res.substring(origPos+2,endCharRef);
						int codePoint = -1;
						if (toTranslate.indexOf('x') >=0) {
							// Hex digits
							String charRef = toTranslate.substring(1);
							if (charRef.length() != 0)
								codePoint = Integer.parseInt(charRef, 16);
						} else { //Decimal digits
							String charRef = toTranslate;
							if (charRef.length() != 0)
								codePoint = Integer.parseInt(charRef,10);
						}
						if (codePoint >= 0) {
							normTarget.append(PlatformDependentUtils.codepointToChars(codePoint));
							origPos = endCharRef+1;
						} else {
							normTarget.append(res.charAt(origPos));
							origPos++;							
						}
					}
				} else {
					normTarget.append(res.charAt(origPos));
					origPos++;
				}
			}
			// skip whitespaces
			while (origPos < res.length() && isWhiteSpace(res.charAt(origPos))) {
					normTarget.append(' ');
				origPos++;
			}
			// append a single whitespace if not at end
//			if (origPos < res.length())
//				normTarget.append(' ');
		}
		return normTarget.toString();
	}	
	
	/**
	 * Implementation of F&O normalize-string function - remove whitespace at beginning and end + reduce whitespace in the middle to one
	 * @param res String to be normalized
	 * @return the normalized string 
	 */
	public static String normalizeString (String res) {
		StringBuffer normTarget = new StringBuffer(res.length());
		int origPos = 0;
		// eat all whitespace at the beginning
		while (origPos < res.length() && isWhiteSpace(res.charAt(origPos))){
			origPos++;
		}
		while(origPos < res.length()) {
			// append non-whitespace characters
			while (origPos < res.length() && !isWhiteSpace(res.charAt(origPos))) {
				normTarget.append(res.charAt(origPos));
				origPos++;
			}
			// skip whitespaces
			while (origPos < res.length() && isWhiteSpace(res.charAt(origPos))) {
				origPos++;
			}
			// append a single whitespace if not at end
			if (origPos < res.length())
				normTarget.append(' ');
		}
		return normTarget.toString();
	}
	
	public static boolean isWhiteSpace(char cToCheck) {
		if (cToCheck == ' '|| cToCheck == '\r' || cToCheck == '\n' || cToCheck == '\t') {
			return true;
		}
		return false;
	}
	

	public final static boolean isLetter (char ch) {
		if ((ch >= 'a' && ch <= 'z') || (ch >= 'A' && ch <= 'Z'))
			return true;
		return false;
	}

	public static boolean isLowerCase(String text){
		for (int i=0; i < text.length(); i++){
			int c = text.charAt(i);
			if (!(c <= 122 && 97 <= c)){
				if (!DiacriticsUtils.isLowerCaseDiacritics(c)){
					return false;
				}
			}
		}
		return true;
	}

	public static boolean isUpperCase(String text){
		for (int i=0; i < text.length(); i++){
			int c = text.charAt(i);
			if (!isUpperCaseLetter(c)){
				return false;
			}
		}
		return true;
	}

	public static boolean isUpperCaseLetter(int chr) {
		if ((chr <= 90 && 65 <= chr) || DiacriticsUtils.isUpperCaseDiacritics(chr))
			return true;
		return false;
	}
	
	public static boolean sameCase(String text, String text2){

		for (int i=0; i < text.length(); i++){
			int c = text.charAt(i);
			int c2 = text2.charAt(i);
			if (!((isUpperCaseLetter(c)) && (isUpperCaseLetter(c2)) || (c <= 122 && 97 <= c) && (c2 <= 122 && 97 <= c2))){
				return false;
			}
		}

		return true;
	}

	
	private static String hex(int c) {
		return Integer.toHexString(c);
	}
	
	public static String stripLeadingWhitespace(String val) {
		int wsPos = 0;
		while (wsPos < val.length() && isWhiteSpace(val.charAt(wsPos))) {
			wsPos++;
		}
		if (wsPos < val.length())
			return val.substring(wsPos);
		return val;
	}
	
	/** For character encoding in URL/URI/IRI */
	
	public static String getEncodedCharacter(char c) {
		StringBuffer result = new StringBuffer();
		if (c < 128) {
			result.append("%");
			result.append(hex(c));
		} else if (c > 127 && c < 2048) {
			result.append("%");
			result.append(hex((c >> 6) | 0xC0));
			
			result.append("%");
			result.append(hex((c & 0x3F) | 0x80));
		} else if (c > 2047 && c < 65536) {
			result.append("%");
			result.append(hex((c >> 12) | 0xE0));
			
			result.append("%");
			result.append(hex(((c >> 6) & 0x3F) | 0x80));
			
			result.append("%");
			result.append(hex((c & 0x3F) | 0x80));
		} else if (c > 65535) {
			result.append("%");
			result.append(hex((c >> 18) | 0xF0));
			
			result.append("%");
			result.append(hex(((c >> 12) & 0x3F) | 0x80));
			
			result.append("%");
			result.append(hex(((c >> 6) & 0x3F) | 0x80));	
			
			result.append("%");
			result.append(hex((c & 0x3F) | 0x80));				
		}
		
		return result.toString().toUpperCase();
	}
	
	/**
	 * This method is for helping to retain the order of comparators and compareTypes
	 * @param i1
	 * @param i2
	 * @return the order relationship: 0 equals, -1 if i1<i2, -1 if i1>i2
	 */
	public static int compareComparator(int i1, int i2){
		if(i1 == i2){
			return 0;
		}else if (i1 < i2){
			return -1;
		}else{
			return 1;
		}
	}
	
	/**
	 * Get the cartesian product of values
	 * @param combinations Helper Vector, usually empty at top-level invocation
	 * @param cur Helper Vector, usually empty at top-level invocation
	 * @param vectors Items that should be combined
	 * @param len Length of items to look at 
	 * @return the certesian product of vectors with length of len
	 */
	public static Vector getCombinations(Vector combinations, Vector cur, Vector vectors, int len) {
		if (len == vectors.size()){
			Vector newVector = new Vector();
			for (int i=0; i < cur.size(); i++){
				newVector.addElement(cur.elementAt(i));
			}
			
			combinations.addElement(newVector);
		}
		else{
			Vector vector = (Vector)vectors.elementAt(len);

			for (int i=0; i < vector.size(); i++){
				cur.addElement(vector.elementAt(i));
				len = len+1;
				getCombinations(combinations, cur, vectors, len);
				len = len -1;
				cur.removeElement(vector.elementAt(i));
			}
		}
		return combinations;
	}	
	
	/**
	 * Add the contents of one vector to the second - needed for CLDC
	 * @param from
	 * @param to
	 * @return the combination of the contents of both vectors
	 * @throws MXQueryException
	 */
	// add elements of vector to another vector
	public static Vector addToVector(Vector from, Vector to) throws MXQueryException{

		for (int i=0; i < from.size(); i++){
			to.addElement(from.elementAt(i));
		}

		return to;
	}
	
	/** for testing purposes */
	/*public static void main(String[] args){
		String test = "this";
		String[] delimiters = {",", "(", ")", ";", ".", "!","?", "{", "}","\"", "[", "]", "|", "#", "%", "&", "/", " "};
		String[] result = split(test,delimiters);
		
		for (int j=0; j < result.length; j++){
			System.out.println(result[j]);
		}
		
	}*/	
}

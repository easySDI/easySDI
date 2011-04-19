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

package ch.ethz.mxquery.sms.ftstore;

import java.util.Vector;

/**
 * Helper class for Diacritics usage
 * @author jimhof
 */
	
public class DiacriticsUtils {
	
	//TODO: Look into standalone diacritics symbols and combination beyond ISO 8859-1
	//TODO: Should these functions become language-dependent?
	
	/**
	 * returns the word without diacritics
	 */
	public static String getWordWithoutDiacritics(String w) {
		StringBuffer withoutDiacritics = new StringBuffer(w.length());
		for (int i=0; i < w.length(); i++){
			char c = w.charAt(i);
			if (isADiacritics(c)){
				c = getCharWithoutDiacritic(c);
			}
			withoutDiacritics = withoutDiacritics.append(c);
		}
		return withoutDiacritics.toString();
	}
	/**
	 * returns the words without diacritics
	 */
	public static Vector getWordsWithoutDiacritics(Vector v) {
		
		Vector newWords = new Vector();
		
		for (int i=0; i < v.size(); i++){
			String withDiacritics = (String)v.elementAt(i);
			StringBuffer withoutDiacritics = new StringBuffer(withDiacritics.length());
			for (int j=0; j <  withDiacritics.length(); j++){
				char c = withDiacritics.charAt(j);
				if (isADiacritics(c)){
					c = getCharWithoutDiacritic(c);
				}
				withoutDiacritics = withoutDiacritics.append(c);
			}
			newWords.addElement(withoutDiacritics.toString());	
		}
		
		return newWords;
	}
	
	
	// helper method for c with diacritics -> c without diacritics
	public static char getCharWithoutDiacritic(char c) {
		if (c == 'e' || (c >= 232 && c <= 235)){
			return 'e';
		}
		else if ((c >= 200 && c <= 203) || c == 'E'){
			return 'E';
		}
		else if (c == 'a' || (c >= 224 && c <= 229)){
			return 'a';
		}
		else if ( c == 'A' || (c >= 192 && c <= 197)){
			return 'A';
		}
		else if (c == 'o' || (c >= 242 && c <= 246) || c == 248 ){
			return 'o';
		}
		else if((c >= 210 && c <= 214) || c == 216 || c == 'O'){
			return 'o';
		}
		else if (c == 'c' || c == 231){
			return 'c';
		}
		else if (c == 199 || c == 'C'){
			return 'C';
		}
		else if ((c >= 236 && c <= 239) || c == 'i'){
			return 'i';
		}
		else if ((c >= 204 && c <= 207)|| c == 'I'){
			return 'I';
		}
		else if (c == 'u' || (c >= 249 && c <= 252)){
			return 'u';
		}
		else if (c == 'U' || (c >= 217 && c <= 220)){
			return 'U';
		}
		else if (c == 253 || c == 255 || c == 'y'){
			return 'y';
		}
		else if (c == 221 || c == 'Y'){
			return 'Y';
		}
		else if (c == 241 || c == 'n'){
			return 'n';
		}
		else if (c == 'N' ||  c == 249){
			return 'N';
		}
		else{
			return 0;
		}
	}

	// checks whether c contains a diacritics
	public static boolean isADiacritics(char c) {
		// all versions of e according to ISO 8859-1
		if (c == 'e' || (c >= 232 && c <= 235) || (c >= 200 && c <= 203) || c =='E'){
			return true;
		}
		// all versions of a according to ISO 8859-1
		else if (c == 'a' || (c >= 224 && c <= 229) || c == 'A' || (c >= 192 && c <= 197)){
			return true;
		}
		// all versions of o according to ISO 8859-1
		else if (c == 'o' || (c >= 242 && c <= 246) || c == 248 || (c >= 210 && c <= 214) || c == 216 || c == 'O' ){
			return true;
		}
		// all versions of c according to ISO 8859-1
		else if (c == 'c' || c == 231 || c == 199){
			return true;
		}
		// all versions of i according to ISO 8859-1
		else if (c == 'I' || (c >= 204 && c <= 207) || c == 'i' || (c >= 236 && c <= 239)){
			return true;
		}
		// all version of u according to ISO 8859-1
		else if (c == 'u' || c == 'U' || (c >= 217 && c <= 220) || (c >= 249 && c <= 252)){
			return true;
		}
		// all version of y according to ISO 8859-1
		else if (c == 253 || c == 255 || c == 'y' || c == 221){
			return true;
		}
		// all version of n according to ISO 8859-1
		else if (c == 241 || c == 'n' || c == 209){
			return true;
		}
		return false;
	}

	public static boolean isLowerCaseDiacritics(int c) {
		if (c == 'e' || (c >= 232 && c <= 235)){
			return true;
		}
		else if (c == 'a' || (c >= 224 && c <= 229)){
			return true;
		}
		else if (c == 'o' || (c >= 242 && c <= 246) || c == 248){
			return true;
		}
		else if (c == 'c' || c == 231){
			return true;
		}
		else if (c == 'i' || (c >= 236 && c <= 239)){
			return true;
		}
		else if ((c >= 249 && c <= 252) || c == 'u'){
			return true;
		}
		else if (c == 253 || c == 255 || c == 'y' ){
			return true;
		}
		else if (c == 241 || c == 'n'){
			return true;
		}
		
		return false;
	}

	public static boolean isUpperCaseDiacritics(int c) {
		
		if ((c >= 200 && c <= 203) || c =='E'){
			return true;
		}
		else if (c == 'A' || (c >= 192 && c <= 197)){
			return true;
		}
		else if (c == 'O' || (c >= 210 && c <= 214) || c == 216){
			return true;
		}
		else if (c == 199){ // C with ,
			return true;
		}
		else if (c == 'U' ||(c >= 217 && c <= 220)){
			return true;
		}
		else if (c == 221){ // Y with ´
			return true;
		}
		else if (c == 209){ // N with ~
			return true;
		}
		
		return false;
	}

}

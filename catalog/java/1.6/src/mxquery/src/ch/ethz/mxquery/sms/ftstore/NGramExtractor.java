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
 * Implementation of an NGram Extractor
 * @author jimhof
 *
 */

public class NGramExtractor {
	
	
	public NGramExtractor(){};

	/**
	 * extracts NGrams out of word "word" of size 2 to "size"
	 */
	
	public Vector extractNGrams(String word, int size, String position){
		
		Vector temp = null;
		Vector result = new Vector();
		String gramWord = "";
		
		if (position.equals("suffix")){
			gramWord = word+"$";
		}
		else if (position.equals("prefix")){
			gramWord = "$"+word;
		}
		else if (position.equals("whole")){
			gramWord = "$"+word+"$";
		}
		else if (position.equals("")){
			gramWord = word;
		}
		
		
		for (int i= 2; i < size+1; i++){
			temp = null;
			temp = extract(gramWord,i);
			for (int j=0; j < temp.size(); j++){
				result.addElement(temp.elementAt(j));
			}
		}
		return result;
	}
	
	private Vector extract(String word, int size){
		
		Vector temp = new Vector();
//  		int counter = 0;
		
		int length = word.length() - size;
		
		for (int i=0; i < length+1; i++){
            temp.addElement(word.substring(i, i+size));
//			char[] ngram = new char[size];
//			counter = 0;
//			for (int j= i; j < i+ size; j++){
//				ngram[counter++] = word.charAt(j);
//			}
//			String sngram = String.valueOf(ngram);
//			temp.addElement(sngram);
		}
		return temp;
	}
}

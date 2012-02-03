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

import ch.ethz.mxquery.util.ObjectObjectPair;

/**
 * Helper class for Wildcard usage
 * FIXME: can be done more efficient when using regex, but not available with CLDC API
 * @author jimhof
 */

public class WildcardUtils {

	/**
	 * checks for wildcards in the phrase "phrase"
	 */
	public boolean checkForWildcards(String phrase) {

		for (int i=0; i < phrase.length(); i++){
			char c = phrase.charAt(i);
			if (c =='.'){
				if (i-1 >= 0){
					if (phrase.charAt(i-1) != '\\'){
						return true;
					}
				}
				return true;
			}
		}

		return false;
	}
	
	/**
	 * returns wildcards in "word" (if mutiple wildcards are contained in one word
	 */
	public Vector parseWord(String word) {

		Vector pairs = new Vector();
		String subword = word;

		// check for prefix
		if (word.startsWith(".*")){
			subword = subword.substring(2,subword.length());
			pairs.addElement(new ObjectObjectPair(".*","prefix"));
		}
		else if (word.startsWith(".+")){
			subword = subword.substring(2,subword.length());
			pairs.addElement(new ObjectObjectPair(".+","prefix"));

		}
		else if (word.startsWith(".?")){
			subword = subword.substring(2,subword.length());
			pairs.addElement(new ObjectObjectPair(".?","prefix"));
		}
		else if (word.startsWith(".")){
			if (word.startsWith(".{")){
				subword = subword.substring(6,subword.length());
				pairs.addElement(new ObjectObjectPair(word.substring(0,6),"prefix"));
			}
			else{
				subword = subword.substring(1,subword.length());
				pairs.addElement(new ObjectObjectPair(".","prefix"));
			}

		}

		// check for suffix
		if (word.endsWith(".*")){
			subword = subword.substring(0,subword.length()-2);
			pairs.addElement(new ObjectObjectPair(".*","suffix"));
		}
		else if (word.endsWith(".+")){
			subword = subword.substring(0,subword.length()-2);
			pairs.addElement(new ObjectObjectPair(".+","suffix"));
		}
		else if (word.endsWith(".?")){
			subword = subword.substring(0,subword.length()-2);
			pairs.addElement(new ObjectObjectPair(".?","suffix"));
		}
		else if (word.endsWith(".")){
			subword = subword.substring(0,subword.length()-1);
			pairs.addElement(new ObjectObjectPair(".","suffix"));
		}
		else if (word.endsWith("}")){
			subword = subword.substring(0,subword.length()-6);
			pairs.addElement(new ObjectObjectPair(word.substring(word.length()-6,word.length()),"suffix"));
		}

		Vector parts = new Vector();
		//check for infix(es)
		if (contains(subword,".")){
			StringBuffer part = new StringBuffer();

			int i = 0;
			int len = subword.length();
			while (i < len){
				char c = subword.charAt(i);
				if (c == '.'){
					if (subword.charAt(i+1)== '*'){
						pairs.addElement(new ObjectObjectPair(".*","infix"));
						subword = subword.substring(i+2,subword.length());


					}
					else if (subword.charAt(i+1)== '+'){
						pairs.addElement(new ObjectObjectPair(".+","infix"));
						subword = subword.substring(i+2,subword.length());
					}
					else if (subword.charAt(i+1)== '?'){
						pairs.addElement(new ObjectObjectPair(".?","infix"));
						subword = subword.substring(i+2,subword.length());

					}
					else if (subword.charAt(i+1)== '{'){
						String wildcard = subword.substring(i,i+6);
						pairs.addElement(new ObjectObjectPair(wildcard,"infix"));
						subword = subword.substring(i+6,subword.length());

					}
					else {
						pairs.addElement(new ObjectObjectPair(".","infix"));
						subword = subword.substring(i+1,subword.length());

					}
					len = subword.length();
					i=0;
					parts.addElement(part.toString());
					part = new StringBuffer();
				}
				else{
					String s = String.valueOf(c);
					part = part.append(s);
					i++;
				}
			}
			parts.addElement(part.toString());
			pairs.insertElementAt(parts, 0);
		}
		else{
			parts.addElement(subword);
			pairs.insertElementAt(parts, 0);
		}
		return pairs;
	}

	/**
	 * returns the position of the wildcards (infix, prefix or suffix)
	 */
	public Vector getWildcardPosition(String word) {

		Vector positions = new Vector();
		boolean suffix = false;

		if (word.startsWith(".")){
			positions.addElement("prefix");
			word = word.substring(1);
		}
		if (word.endsWith(".?") || word.endsWith(".+") || word.endsWith(".*") || word.endsWith(".")){
			suffix = true;
			word = word.substring(0,word.length()-2);
		}
		if (word.endsWith("}")){
			suffix = true;
			word = word.substring(0,word.length()-6);
		}

		while (contains(word,".")){
			positions.addElement("infix");
			word = word.substring(word.indexOf(".")+1,word.length());
		}

		if (suffix == true){
			positions.addElement("suffix");
		}


		return positions;
	}

	/**
	 * returns the wildcards contained in word "word"
	 */
	public Vector getWildcards(String word){
		char c;
		char nc;
		StringBuffer wildcard = null;
		Vector wildcards = new Vector();
		int i=0;
		while (i < word.length()){
			c = word.charAt(i);
			if (c == '.'){
				wildcard = new StringBuffer();
				wildcard = wildcard.append(c);
				if (i+1 < word.length()){
					nc = word.charAt(i+1);
					if (nc == '?' || nc == '+' || nc == '*'){
						wildcard = wildcard.append(nc);
					}
					else if (nc == '{'){
						wildcard = wildcard.append(nc).append(word.substring(i+2,i+6));
						i+=5;
					}
				}

			}
			if (wildcard != null){
				wildcards.addElement(wildcard.toString());
			}
			wildcard = null;
			i++;
		}
		return wildcards;
	}

	/**
	 * returns the prefix of word "word" containing wildcard "wildcard"
	 */
	public String getPrefix(String word, String wildcard){

		if (wildcard.equals(".?") || wildcard.equals(".+") || wildcard.equals(".*")){
			return word.substring(0, word.length()-2);
		}
		else if (wildcard.startsWith(".{")){
			return word.substring(0,word.length()-7);
		}
		else{
			return word.substring(0,word.length()-1);
		}
	}
	
	/**
	 * returns the suffix of word "word" containing wildcard "wildcard"
	 */
	public String getSuffix(String word, String wildcard){

		if (wildcard.equals(".?") || wildcard.equals(".+") || wildcard.equals(".*")){
			return word.substring(2, word.length());
		}
		else if (wildcard.startsWith(".{")){
			return word.substring(6,word.length());
		}
		else{
			return word.substring(1,word.length());
		}
	}

	/**
	 * returns the prefix and suffix of word "word" containing wildcard "wildcard"
	 */
	public String[] getPrefixAndSuffix(String word, String wildcard){

		String[] prefixAndSuffix = new String[2];
		StringBuffer prefix = new StringBuffer();
		StringBuffer suffix = new StringBuffer();
		int i=0;
		char c = word.charAt(0);
		// find prefix
		while (c != '.'){
			prefix = prefix.append(c);
			i++;
			c = word.charAt(i);
		}

		if (wildcard.equals(".?") || wildcard.equals(".+") || wildcard.equals(".*")){
			i = i+2;
		}
		else if (wildcard.equals(".")){
			i = i+1;
		}
		if (wildcard.startsWith(".{")){
			i = i+6;
		}

//		c = word.charAt(i);
		while (i < word.length()){
			suffix = suffix.append(word.charAt(i));
			i++;
		}

		prefixAndSuffix[0] = prefix.toString();
		prefixAndSuffix[1] = suffix.toString();

		return prefixAndSuffix;
	}
	
	/**
	 * returns true if string subs is contained in string s
	 */
	public boolean contains(String s, String subs){
		for (int i=0; i < s.length(); i++){
			if (i+subs.length() < s.length()+1){
				String substring = s.substring(i, i+subs.length());
				if (substring.equals(subs)){
					return true;
				}
			}
		}
		return false;
	}
	
	/**
	 * checks the found words against the wildcards and their positions
	 * needs to be done as n-gram index returns false positives
	 */
	public Vector checkAgainstOriginalQueryMultipleWildcard(Vector words, Vector wildcardPositionPairs) {
		Vector results = new Vector();
		int size = wildcardPositionPairs.size()-1;
		int infixCounter = 0;
		int counter;
		for (int i=0; i < words.size(); i++){
			counter = 0;
			String word = (String)words.elementAt(i);
			infixCounter = 0;
			for (int j=1; j < wildcardPositionPairs.size(); j++){
				ObjectObjectPair pair = (ObjectObjectPair) wildcardPositionPairs.elementAt(j);
				if (((String)pair.getSecond()).equals("infix")){
					infixCounter++;
				}
				if (fulfills(word,pair,(Vector)wildcardPositionPairs.elementAt(0),infixCounter)){
					counter++;
				}
			}
			if (counter == size){
				results.addElement(word);
			}
		}
		return results;

	}

	// helper method for checkAgainstOriginalQuery
	private boolean fulfills(String word, ObjectObjectPair pair, Vector originalQuery, int infixCounter) {
		String wildcard = (String)pair.getFirst();
		String position = (String)pair.getSecond();
		String oquery;

		if (position.equals("prefix")){

			oquery = (String)originalQuery.elementAt(0);
			if (wildcard.equals(".*")){
				if (contains(word,oquery)){
					return true;
				}
			}
			else if (wildcard.equals(".+")){
				if (contains(word,oquery) && !word.startsWith(oquery)){
					return true;
				}
			}
			else if (wildcard.equals(".?")){
				if (contains(word,oquery)&& (contains(word,oquery) || (word.substring(1, word.length())).startsWith(oquery))){
					return true;
				}
			}
			else if (wildcard.equals(".")){
				if (contains(word,oquery) && (word.substring(1, word.length())).startsWith(oquery)){
					return true;
				}
			}
			else{
				String sfrom = wildcard.substring(2,3);
				String sto = wildcard.substring(4,5);
				int from = Integer.parseInt(sfrom);
				int to = Integer.parseInt(sto);


				if (contains(word,oquery)){
					if (from < to){
						for (int i= from; i < to+1; i++ ){
							if (word.substring(i,word.length()).startsWith(oquery)){
								return true;
							}
						}
					}
				}

			}

		}
		else if (position.equals("suffix")){

			oquery = (String)originalQuery.elementAt(originalQuery.size()-1);
			if (wildcard.equals(".*")){
				if (contains(word,oquery)){
					return true;
				}
			}
			else if (wildcard.equals(".+")){
				if (contains(word,oquery) && !word.endsWith(oquery)){
					return true;
				}
			}
			else if (wildcard.equals(".?")){
				if (contains(word,oquery)&& (word.endsWith(oquery) || (word.substring(0, word.length()-1)).endsWith(oquery))){
					return true;
				}
			}
			else if (wildcard.equals(".")){
				if (contains(word,oquery) && (word.substring(0, word.length()-1)).endsWith(oquery)){
					return true;
				}
			}
			else{
				String sfrom = wildcard.substring(2,3);
				String sto = wildcard.substring(4,5);
				int from = Integer.parseInt(sfrom);
				int to = Integer.parseInt(sto);


				if (contains(word,oquery)){
					if (from < to){
						for (int i= from; i < to+1; i++ ){
							if (word.substring(0,word.length()-i).endsWith(oquery)){
								return true;
							}
						}
					}
				}

			}

		}
		// infix
		else{

			String oqueryPrefix = (String)originalQuery.elementAt(infixCounter-1);
			String oquerySuffix = (String)originalQuery.elementAt(infixCounter);


			int prefixIndex = word.indexOf(oqueryPrefix)+oqueryPrefix.length()-1;
			int suffixIndex = word.indexOf(oquerySuffix);
			if (wildcard.equals(".*")){
				if (prefixIndex <= suffixIndex){
					return true;
				}
			}
			else if (wildcard.equals(".+")){
				if (prefixIndex+1 < suffixIndex){
					return true;
				}
			}
			else if (wildcard.equals(".?")){

				if (contains(word,oqueryPrefix) && contains(word,oquerySuffix)){
					if (prefixIndex + 2==suffixIndex || prefixIndex+1 == suffixIndex){
						return true;
					}
				}
			}
			else if (wildcard.equals(".")){
				if (prefixIndex + 2==suffixIndex){
					return true;
				}
			}
			else{
				String sfrom = wildcard.substring(2,3);
				String sto = wildcard.substring(4,5);
				int from = Integer.parseInt(sfrom);
				int to = Integer.parseInt(sto);
				int distance = suffixIndex - prefixIndex - 1;

				if (from < to){
					if ((distance <= to) && (distance >= from)){
						return true;
					}
				}				
			}

		}


		return false;
	}

}

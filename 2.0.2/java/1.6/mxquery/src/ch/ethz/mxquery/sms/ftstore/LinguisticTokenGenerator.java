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

import java.io.InputStream;
import java.util.Vector;

import ch.ethz.mxquery.datamodel.DeweyIdentifier;
import ch.ethz.mxquery.datamodel.Identifier;
import ch.ethz.mxquery.datamodel.adm.LinguisticToken;
import ch.ethz.mxquery.datamodel.xdm.TextToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.util.Utils;

/**
 * A class for preprocessing the documents: Tokenization and conversion of XDM tokens into Linguistic Tokens
 * 
 * @author jimhof
 * 
 */
public class LinguisticTokenGenerator {
	
	protected int sentenceCounter;
	protected int paragraphCounter;
	protected int positionCounter = 1;
	static TrieNode abbrevTrie;
	protected int called = 0;
	protected Vector linguisticToken;
	protected Vector tokens;
	
	public LinguisticTokenGenerator() throws MXQueryException {
	}
	
	
	/**
	 * 
	 * @return vector of Linguistic Tokens (of the XDM TextToken)
	 */
	public Vector getLinguisticToken(Token textToken){
        return convertToLinguisticTokens(textToken);
	}
	
	public void incrementParagraphCounter(){
		this.paragraphCounter++;
	}
	
	/**
	 * 
	 * @return true if there are still TextTokens
	 */
	
	private Vector convertToLinguisticTokens(Token token) {
		
		boolean isStartingToken = true;
		boolean isEndingToken = false;
		
		if (!(token instanceof TextToken))
			return null;
		TextToken texttoken = (TextToken) token;

		String text = texttoken.getText();
		if (contains(text, "\n") || contains(text,"\r")){
			StringBuffer t = filter(text);
			String filteredText = t.toString();
			if (filteredText.trim().length() == 0)
				return null;
		}
		
		Identifier identifier = texttoken.getId();

		if (identifier instanceof DeweyIdentifier){

			DeweyIdentifier deweyId = (DeweyIdentifier) identifier;

			int level = deweyId.getDeweyLevel();
			int nextLevel = level+1;

			int[] parentDeweyId = copyArray(deweyId.getDeweyId(),0);

			Vector words = tokenize(text);
			if (words.size() > 0){
				Vector tokens = new Vector();

				int size = words.size();


				for (int i=0; i < size ; i++){

					if (i == size-1){
						isEndingToken = true;
					}

					int startPos = ((SentenceToken) words.elementAt(i)).getStartingPosition();
					int endPos = ((SentenceToken) words.elementAt(i)).getEndPosition();

					int[] dewey_id = copyArray(parentDeweyId,1);
					dewey_id[nextLevel] = positionCounter;

					DeweyIdentifier did = null;
					did = new DeweyIdentifier(dewey_id,level+1,deweyId.getStore());
					int sentenceCount = ((SentenceToken) words.elementAt(i)).getSentenceCount();

					LinguisticToken lingu_token = new LinguisticToken(startPos,endPos,did,texttoken,sentenceCount,paragraphCounter,isStartingToken,isEndingToken);
					
					tokens.addElement(lingu_token);
					positionCounter++;

					isStartingToken = false;


				}

				return tokens;
			}
		}
		return null;
	}
	
	private StringBuffer filter(String text) {
		StringBuffer result = new StringBuffer("");
		for (int i=0; i < text.length(); i++){
			char c = text.charAt(i);
			if ((c != '\n')||(c != '\r')){
				result = result.append(c);
			}
				
		}
		return result;
	}


	private boolean contains(String s, String subs){
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
	
	
	/*a very simple tokenization algorithm
	 * if word. Capital letter -> new sentence
	 * if Word. Capital letter -> not new sentence e.g. P. M. Fischer or Dr. Fischer
	 * @param text
	 * @return
	 */
	public Vector tokenize(String text){
				
		Vector words = new Vector();
		char [] delimiters = {',', '(', ')', ';', '.', '!','?', '{', '}','"', '[', ']', '|', '#', '%', '&', '/', ':' ,'\'' ,' ','\t','\n','\r'};
		int begin = 0;		
		String newElement;
		boolean delimitersFound = false;
		boolean newSentence = false;
		int startPos = 0;
		
		for (int i= 0; i < text.length(); i++){

			for (int j = 0; j < delimiters.length;j++){

				char c = delimiters[j];
				char t = text.charAt(i);

				if (t== c){
					delimitersFound = true;
					newSentence = checkSentenceStart(text, i, c);

					if (text.substring(begin,i).length() > 0) {
						boolean genToken = false;
						if (begin != 0){
							newElement = text.substring(begin+1,i);
							if ((newElement.trim().length() > 0)){
								genToken = true;
							}								
						}
						else{
							newElement = text.substring(begin,i);
							if ((newElement.length() >= 1)){
								genToken = true;
							}
						}
						if (genToken) {
							SentenceToken sentenceToken = new SentenceToken(sentenceCounter,startPos,i);
							words.addElement(sentenceToken);
							startPos = i;
							if (newSentence){
								newSentence = false;
								sentenceCounter++;
							}
						}
						begin = i;
					}
					else{
						begin = i+1;
					}
					startPos++;
					break;
				}
			}
		}
			
		if (delimitersFound){
			if (text.length() > 0) {
				newElement = "";
				if (begin+1 <= text.length()){
					newElement = text.substring(begin+1);
				}
				if (newElement.trim().length() >= 1){
					SentenceToken sentenceToken = new SentenceToken(sentenceCounter,begin+1,text.length());
					words.addElement(sentenceToken);
				}
			}
		}
		else{
			newElement = text.substring(begin);
			if (newElement.trim().length() >= 1){
				SentenceToken sentenceToken = new SentenceToken(sentenceCounter,begin,text.length());
				words.addElement(sentenceToken);
			}
		}

		return words;
	}


	private boolean checkSentenceStart(String text, int i,
			char c) {
		boolean newSentence = false;
		if ((c == '.') || (c == '?') || (c == '!')){
			int nextPos = i+1;
			int nextNextPos = i+2;
			if (nextPos < text.length()){
				if (Utils.isWhiteSpace(text.charAt(nextPos))){
					if (nextNextPos < text.length()){
						int asciiCode = text.charAt(nextNextPos);
						// capital letter ?
						if (Utils.isUpperCaseLetter(asciiCode)){
							newSentence = checkPrevWordForSentence(text, i);
						}
					}
				}
			} else {
				// end of text might also be end of sentence if there is . or ? or !
				newSentence = checkPrevWordForSentence(text, i);
			}
		}
		return newSentence;
	}


	private boolean checkPrevWordForSentence(String text, int i) {
		boolean newSentence = false;
		int asciiCode;
		// check whether the word began with a capital letter
		int k = i;
		StringBuffer word = new StringBuffer(100);
		while ((k > 0) && (!Utils.isWhiteSpace(text.charAt(k)))){//Should this be a delimiter?
			word.append(text.charAt(k));
			k--;
		}
		
		if (k+1 >= text.length())
			return false;
		
		asciiCode = text.charAt(k+1);
		if ((65 >= asciiCode) || (asciiCode <= 117)){
			if (abbrevTrie != null){
				if (traverseAbbrevTrie(String.valueOf(word),word.length()) == false){
						newSentence = true;
				}
			}
			else{
				newSentence = true;
			}
		}
		else{
			newSentence = true;
		}
		return newSentence;
	}
	
	// builds a trie out of file
	private void buildAbbrevTrie() throws MXQueryException {
		
		abbrevTrie = new TrieNode();
		try{
			InputStream is = this.getClass().getResourceAsStream("abbreviations.txt");
			String strLine = Utils.readString(is);
			String [] words = Utils.split(strLine, "\n");
			for (int j=0;j<words.length;j++)  {
				String currentWord = words[j].trim();
				TrieNode node = abbrevTrie;
				for (int i=0; i < currentWord.length(); i++){
					char c = currentWord.charAt(i);
					node = node.buildTrie(c);
				}
				node.setBool();
	       }
	       //Close the input stream
	       is.close();
	    }catch (Exception e){//Catch exception if any
	         throw new MXQueryException(ErrorCodes.A0009_EC_EVALUATION_NOT_POSSIBLE,"Failed to intiate abbreviations for FT support: "+e.getMessage(), null);
	    }
	}
	
	

	// to traverse the trie and check whether text is an abbreviation
	private boolean traverseAbbrevTrie(String text, int length){
		int i=length-1;
		TrieNode temp = abbrevTrie;
		while ((temp != null) && (i > 0)){
			char c = text.charAt(i);
			if (temp.getChild(c)== null){
					return false;
			}
			temp = temp.getChild(c);
			i--;
		}
		return true;
	}
	
	private static int[] copyArray(int[] id, int l){
		int[] s = new int[id.length+l];
		for (int i=0; i< id.length; i++){
			s[i] = id[i];
		}
		return s;
	}
	// initialize abbrev trie for all instances
	{
		buildAbbrevTrie();
	}
}

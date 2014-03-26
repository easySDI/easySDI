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

package ch.ethz.mxquery.datamodel.adm;

import ch.ethz.mxquery.datamodel.DeweyIdentifier;
import ch.ethz.mxquery.datamodel.xdm.TextToken;
import ch.ethz.mxquery.exceptions.MXQueryException;
//import ch.ethz.mxquery.model.Context;

/**
 * Linguistic Token: for each word in the text there exists a Linguistic Token
 * Additional Information: position of term in text, Dewey Identifier, sentence and paragraph information etc.
 * @author jimhof
 *
 */

public class LinguisticToken implements FTToken {
	
	/** relative position of the term in document order*/
	protected int position;	
	/** dewey position of the token */
	protected DeweyIdentifier dewey_id;
	/** in which sentence the word is */
	protected int sentenceID;
	/** in which paragraph the word is */
	protected int paraID;
	/** corresponding xquery token*/
	protected TextToken xq_token;
	/** startPosition in xq_token */
	protected int startPosition;
	/** endPosition in xq_token */
	protected int endPosition;
	/** first token in element? */
	protected boolean isStartingToken;
	/** last token in element? */
	protected boolean isEndingToken;
	


	public LinguisticToken(int startPos, int endPos, DeweyIdentifier did, TextToken token, int sentenceID, int paraID, boolean startingToken, boolean endingToken) {
		
		this.startPosition = startPos;
		this.endPosition = endPos;
		this.xq_token = token;
		this.dewey_id = did;
		this.sentenceID = sentenceID;
		this.paraID = paraID;
		this.isStartingToken = startingToken;
		this.isEndingToken = endingToken;
		int level = did.getDeweyLevel();
		int[] id = did.getDeweyId();
		this.position = id[level];
		
		//dumpToken();
	}

	// for debugging purposes
//	private void dumpToken() {
//		System.out.println(this.getText());
//		System.out.println("position "+ this.position);
//		System.out.println("sentence " + this.sentenceID);
//		System.out.println("paragraph " + this.paraID);
//	}

	/** getter methods */
	
	public String getText(){
		return xq_token.getText().substring(this.startPosition,this.endPosition);
	}

	public int getPosition(){
		return position;
	}
	
	public DeweyIdentifier getDeweyId(){
		return dewey_id;
	}
	
	public TextToken getTextToken(){
		return xq_token;
	}
	
	public int getSentence(){
		return sentenceID;
	}
	
	public int getParagraph(){
		return paraID;
	}
	
	public LinguisticToken copy() throws MXQueryException {
		return new LinguisticToken(startPosition, endPosition, dewey_id, xq_token,sentenceID,paraID,isStartingToken,isEndingToken);
	}

	public int compareTo(LinguisticToken value) {	
		return (this.getDeweyId().compare(value.getDeweyId()));
	}

	public boolean equals(Object obj) {
		if (obj instanceof LinguisticToken) {
			LinguisticToken peer = (LinguisticToken)obj;
			return peer.dewey_id.equals(dewey_id);
		}
		return false;
	}

	public int hashCode() {
		return dewey_id.hashCode();
	}


}

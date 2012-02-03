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

import java.util.Vector;

import ch.ethz.mxquery.datamodel.MXQueryDouble;

/**
 * Represents a StringMatch of the Fulltext Data Model: Roughly speaking, a
 * match of query tokens with document tokens
 * 
 * @author Peter
 * 
 */

public class StringMatch {
    LinguisticToken[] myTokens;
    int queryPos;
    MXQueryDouble score = new MXQueryDouble(0);

    public StringMatch(Vector myTokens, int queryPos) {
	LinguisticToken[] toks = new LinguisticToken[myTokens.size()];
	for (int i = 0; i < myTokens.size(); i++) {
	    toks[i] = (LinguisticToken) myTokens.elementAt(i);
	}
	this.myTokens = toks;
	this.queryPos = queryPos;
    }

    public StringMatch(LinguisticToken[] myTokens, int queryPos) {
	this.myTokens = myTokens;
	this.queryPos = queryPos;
    }

    /**
     * Get the start position of the StringMatch
     * 
     * @return the integer position of the start
     */
    public int getStartPos() {
	return myTokens[0].getPosition();
    }

    /**
     * Get the end position of the StringMatch
     * 
     * @return the integer position of the end
     */
    public int getEndPos() {
	return myTokens[myTokens.length - 1].getPosition();
    }

    /**
     * Get the start sentence of the StringMatch
     * 
     * @return the integer position of the start
     */
    public int getStartSentence() {
	return myTokens[0].getSentence();
    }

    /**
     * Get the end sentence of the StringMatch
     * 
     * @return the integer position of the end
     */
    public int getEndSentence() {
	return myTokens[myTokens.length - 1].getSentence();
    }

    /**
     * Get the start paragraph of the StringMatch
     * 
     * @return the integer position of the start
     */
    public int getStartParagraph() {
	return myTokens[0].getParagraph();
    }

    /**
     * Get the end paragraph of the StringMatch
     * 
     * @return the integer position of the end
     */
    public int getEndParagraph() {
	return myTokens[myTokens.length - 1].getParagraph();
    }

    public void setScore(MXQueryDouble score) {
	this.score = score;
    }

    public MXQueryDouble getScore() {
	return this.score;
    }

}

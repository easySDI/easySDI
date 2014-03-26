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

/**
 * implementation of a Phrase containing several Matchs (for each Linguistic
 * Token that matches a word of the phrase)
 * 
 * @author jimhof
 */

public class Phrase implements FTToken {

    private Vector linguisticTokens;

    public Phrase(LinguisticToken token) {
	this.linguisticTokens = new Vector();
	linguisticTokens.addElement(token);
    }

    public Phrase() {
	this.linguisticTokens = new Vector();
    }

    public Phrase(Phrase p) {
	this.linguisticTokens = new Vector();
	Vector temp = p.getPhrase();
	for (int i = 0; i < temp.size(); i++) {
	    this.linguisticTokens.addElement(temp.elementAt(i));
	}
    }

    public void addLightLinguisticToken(LinguisticToken token) {
	if (!this.linguisticTokens.contains(token)) {
	    this.linguisticTokens.addElement(token);
	}
    }

    public LinguisticToken getLastInsertedElement() {
	return (LinguisticToken) this.linguisticTokens
		.elementAt(linguisticTokens.size() - 1);
    }

    public int phraseSize() {
	return this.linguisticTokens.size();
    }

    public Vector getPhrase() {
	return this.linguisticTokens;
    }

    public LinguisticToken getFirstElement() {
	return (LinguisticToken) this.linguisticTokens.elementAt(0);
    }

    public void removeElements(int posInPhrase) {
	int size = linguisticTokens.size() - posInPhrase;
	while (size > 0) {
	    linguisticTokens.removeElementAt(posInPhrase);
	    size--;
	}
    }
}

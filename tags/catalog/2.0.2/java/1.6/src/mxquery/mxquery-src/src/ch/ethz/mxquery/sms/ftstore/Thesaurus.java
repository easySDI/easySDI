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

import java.io.IOException;
import java.io.InputStream;
import java.util.Vector;

import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.model.ws.SoapInvoker;
import ch.ethz.mxquery.util.Utils;

/**
 * Implementation of Local Thesaurus
 * 
 * @author Kyumars Sheik Esmaili
 */

public class Thesaurus {

    // helper methods for queries containing a thesaurus
    public TrieNode posTrie;

    public Thesaurus() throws IOException {
	buildPosTrie();
    }

    // return synoyms of text
    public Vector findSynonyms(String text, String location)
	    throws MXQueryException {
	String synonyms = "";
	Vector vsynonyms = new Vector();
	Vector poss = getPOS(text, text.length());
	String pos = "";
	if (poss != null) {
	    if (poss.size() != 0) {
		pos = getPosForWordNet((String) poss.elementAt(0));
	    }
	}
	try {
	    synonyms = Thesaurus.getSynsets(location, text, pos);
	} catch (IOException e) {
	    throw new MXQueryException(ErrorCodes.A0007_EC_IO, "I/O Error: "
		    + e.toString(), null);
	}
	String[] delimiters = { ",", "." };
	String[] synonym = Utils.split(synonyms, delimiters);
	for (int j = 0; j < synonym.length; j++) {
	    String s = synonym[j].trim();
	    if (!vsynonyms.contains(s) && !s.equals("")) {
		vsynonyms.addElement(s);
	    }
	}
	return vsynonyms;
    }

    private static String getPosForWordNet(String pos) {

	if (pos.equals("NoC") || pos.equals("NoP")) {
	    return "noun";
	} else if (pos.equals("Verb") || (pos.equals("VMod"))) {
	    return "verb";
	} else if (pos.equals("Adj")) {
	    return "adjective";
	} else if (pos.equals("Adv")) {
	    return "adverbe";
	} else if (pos.equals("Prep")) {
	    return "preposition";
	} else {
	    return "";
	}
    }

    private void buildPosTrie() throws IOException {

	posTrie = new TrieNode();
	InputStream is = this.getClass().getResourceAsStream("pos.txt");
	String strLine = Utils.readString(is);
	String[] triple = Utils.split(strLine, "\n");
	for (int j = 0; j < triple.length; j++) {
	    String t = triple[j].trim();
	    String[] words = Utils.split(t, "\t");
	    String word = words[0];
	    TrieNode node = posTrie;
	    for (int i = 0; i < word.length(); i++) {
		char c = word.charAt(i);
		if (c != ' ' || c != ';') {
		    TrieNode lastNode = node;
		    node = node.buildTrie(c);
		    if (node == null) {
			node = lastNode;
		    }
		}
	    }
	    node.setWord();
	    node.addPos(words[1]);
	}
	// Close the input stream
	is.close();
    }

    // to get the POS of a word
    public Vector getPOS(String word, int length) {
	int i = 0;
	TrieNode temp = posTrie;
	while ((temp != null) && i < word.length()) {
	    char c = word.charAt(i);
	    if (temp.getChild(c) == null) {
		return new Vector();
	    }
	    temp = temp.getChild(c);
	    i++;
	}
	return temp.getPos();
    }

    public static String getSynsets(String location, String term, String type)
	    throws MXQueryException, IOException {
	// NOTE: if the WordNetServer is not an axis service, the following part
	// might need to be modified [due to encoding,..]
	String soapEnv = "<SOAP-ENV:Envelope xmlns:SOAP-ENV=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\">"
		+ "<SOAP-ENV:Body>"
		+ "<getTerm xmlns=\"http://localhost:8080/axis/WordNetService.jws\" SOAP-ENV:encodingStyle=\"http://schemas.xmlsoap.org/soap/encoding/\">"
		+ "<term xsi:type=\"xsd:string\">"
		+ term
		+ "</term>"
		+ "<type xsi:type=\"xsd:string\">"
		+ type
		+ "</type>"
		+ "</getTerm>" + "</SOAP-ENV:Body>" + "</SOAP-ENV:Envelope>";

	SoapInvoker si = new SoapInvoker(location, null, "", soapEnv);
	String soapResult = si.query(QueryLocation.OUTSIDE_QUERY_LOC, true);
	// TODO: better to extract by an XMLParser!
	String synsets = soapResult.substring(soapResult
		.indexOf("<getTermReturn xsi:type=\"xsd:string\">") + 37,
		soapResult.indexOf("</getTermReturn>"));
	// TODO: convert the result to an array of Strings [they are delimited
	// by ',']
	return synsets;
    }
}

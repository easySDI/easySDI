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

import java.util.Enumeration;
import java.util.Hashtable;
import java.util.Vector;

import ch.ethz.mxquery.bindings.WindowBuffer;
import ch.ethz.mxquery.datamodel.DeweyIdentifier;
import ch.ethz.mxquery.datamodel.Identifier;
import ch.ethz.mxquery.datamodel.IdentifierFactory;
import ch.ethz.mxquery.datamodel.Source;
import ch.ethz.mxquery.datamodel.adm.LinguisticToken;
import ch.ethz.mxquery.datamodel.adm.Phrase;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.xdm.NamedToken;
import ch.ethz.mxquery.datamodel.xdm.TextToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.model.ft.FTLangDepFactory;
import ch.ethz.mxquery.sms.MMimpl.TokenBufferStore;
import ch.ethz.mxquery.sms.btree.BTree;
import ch.ethz.mxquery.sms.btree.BTreeNode;
import ch.ethz.mxquery.util.ObjectObjectPair;
import ch.ethz.mxquery.util.QuickSort;
import ch.ethz.mxquery.util.Utils;

/**
 * Storage of Tokens (materialized), Linguistic Tokens and indexes on them
 * 
 * @author jimhof
 * 
 */

public class FTTokenBufferStore extends TokenBufferStore implements
	FullTextStore {

    private String uri;

    private int called = 0;
    private Vector linguisticTokens;
    private LinguisticTokenGenerator generator;

    // indexes
    private Hashtable invertedList;
    private Hashtable ngramIndex;
    private Hashtable frequencyTable;
    private Hashtable nextWordIndex;
    private Hashtable stemIndex;
    private BTree btree;
    private BTreeNode btreeRoot;

    // parameters
    private int nGramSize = 3;
    private int nextWordSize = 3;
    private int kstar = 3;
    private int k = 3;
    private int f = 1;

    /**
     * Creates a new FTTokenBuffer for a stream with standard parameters
     * 
     * @param sourceStream
     *                Source Stream
     */
    public FTTokenBufferStore(XDMIterator sourceStream, int id,
	    WindowBuffer container) throws MXQueryException {

	super(sourceStream, id, container);
	generateNodeIds = true;
	this.linguisticTokens = new Vector();
	this.generator = new LinguisticTokenGenerator();

    }

    public FTTokenBufferStore(int id, WindowBuffer container)
	    throws MXQueryException {
	super(id, container);
	generateNodeIds = true;
	this.linguisticTokens = new Vector();
	this.generator = new LinguisticTokenGenerator();
    }

    public Token get(int tokenId) throws MXQueryException {

	if (called == 0) {
	    super.get(Integer.MAX_VALUE);
	    called++;
	    initIndexes();

	}
	return super.get(tokenId);

    }

    public Token get(int tokenId, int maxNodeId) throws MXQueryException {

	if (called == 0) {
	    super.get(Integer.MAX_VALUE);
	    called++;
	    initIndexes();

	}
	return super.get(tokenId, maxNodeId);

    }

    public int getTokenIdForNode(int nodeId) throws MXQueryException {

	if (called == 0) {
	    super.get(Integer.MAX_VALUE);
	    called++;

	    initIndexes();

	}
	return super.getTokenIdForNode(nodeId);
    }

    private void initIndexes() throws MXQueryException {

	// converts the text tokens into linguistic tokens
	initLinguisticTokenStore();

	// initiates the inverted index
	initHashtableIndex();

	// initiates the ngram index
	initNGramIndex();

	// init B+ Tree
	initBPTreeIndex();

	// initiates the nextword index
	initNextwordIndex();

	// initiates the stem index
	initStemIndex();

    }

    private void initLinguisticTokenStore() {

	int i = 0;
	while (i < tokenBuffer.length) {
	    Token token = tokenBuffer[i];
	    if (token instanceof TextToken) {

		TextToken textToken = (TextToken) token;
		Vector linToken = generator.getLinguisticToken(textToken);
		if (linToken != null) {
		    for (int j = 0; j < linToken.size(); j++) {
			linguisticTokens.addElement(linToken.elementAt(j));
		    }
		}
	    } else if (token instanceof NamedToken) {
		if (token.getName().equals("p")
			&& token.getEventType() == Type.START_TAG) {
		    generator.incrementParagraphCounter();
		}
	    }
	    i++;

	}
    }

    /**
     * Initiates the ngram index: a hashtable: ngram -> list of words that
     * contain that ngram
     */

    private void initNGramIndex() throws MXQueryException {
	Vector ngrams;
	Vector list;
	NGramExtractor extractor;

	ngramIndex = new Hashtable();
	extractor = new NGramExtractor();
	for (int i = 0; i < linguisticTokens.size(); i++) {
	    LinguisticToken token = (LinguisticToken) linguisticTokens
		    .elementAt(i);
	    String text = token.getText().toLowerCase();
	    text = DiacriticsUtils.getWordWithoutDiacritics(text);

	    // extract all ngrams out of "text"
	    ngrams = extractor.extractNGrams(text, this.nGramSize, "whole");

	    // for each n-gram:
	    for (int j = 0; j < ngrams.size(); j++) {
		// if it is contained in the hashtable, retrieve the list
		// and add the new word
		Object o = ngrams.elementAt(j);
		if (ngramIndex.containsKey(o)) {
		    Object olist = ngramIndex.get(o);
		    Vector old_list = (Vector) olist;
		    old_list.addElement(text);
		    // list = new Vector();
		    list = old_list;
		    ngramIndex.remove(o);
		}
		// if it is not contained in the hashtable, create a new list
		else {
		    list = new Vector();
		    list.addElement(text);
		}
		ngramIndex.put(o, list);
	    }
	}
    }

    /**
     * Initiates the nextword index: hashtable: firstword -> list of its
     * nextwords
     */
    private void initNextwordIndex() throws MXQueryException {
	this.nextWordIndex = new Hashtable();
	Vector topNWords = getTopNWords();
	for (int i = 0; i < topNWords.size(); i++) {
	    Vector nextWords = new Vector();
	    String word = (String) topNWords.elementAt(i);
	    word = DiacriticsUtils.getWordWithoutDiacritics(word.toLowerCase());
	    Vector words = (Vector) invertedList.get(word);
	    for (int j = 0; j < words.size(); j++) {
		LinguisticToken firsttoken = (LinguisticToken) words
			.elementAt(j);
		DeweyIdentifier firstWordDid = firsttoken.getDeweyId();
		LinguisticToken nexttoken = null;
		if (btree != null) {
		    nexttoken = btree.getNext(firstWordDid, btreeRoot);
		}
		if (nexttoken != null) {
		    ObjectObjectPair pair = new ObjectObjectPair(firsttoken,
			    nexttoken);
		    nextWords.addElement(pair);
		}
	    }
	    nextWordIndex.put(word, nextWords);
	}

    }

    /**
     * Initiates the stem index: Hashtable: stem -> list of words that have that
     * stem
     */

    private void initStemIndex() throws MXQueryException {
	Vector list;

	stemIndex = new Hashtable();
	for (int i = 0; i < linguisticTokens.size(); i++) {
	    LinguisticToken token = (LinguisticToken) linguisticTokens
		    .elementAt(i);
	    String text = token.getText().toLowerCase();
	    Stemmer porter_stemmer = FTLangDepFactory.getStemmer("en");
	    String stem = porter_stemmer.findStem(text);
	    stem = DiacriticsUtils.getWordWithoutDiacritics(stem);
	    text = DiacriticsUtils.getWordWithoutDiacritics(text);
	    if (stemIndex.containsKey(stem)) {

		// hashtable word -> list of LT
		Object olist = stemIndex.get(stem);
		Vector old_list = (Vector) olist;
		old_list.addElement(text);
		// list = new Vector();
		list = old_list;
		stemIndex.remove(stem);
	    }

	    // if the word is not contained, create a new list
	    else {
		list = new Vector();
		list.addElement(text);

	    }

	    stemIndex.put(stem, list);

	}

    }

    // returns the top n (= nextword size) words
    // if less than n words are available, the available number is returned
    private Vector getTopNWords() throws MXQueryException {
	int i = 0;

	// for sequential reading of Hashtable
	Enumeration elemEnum = frequencyTable.elements();
	Enumeration keyEnum = frequencyTable.keys();

	// put elements into an array for sorting
	Object[] array = new Object[frequencyTable.size()];
	while (elemEnum.hasMoreElements()) {
	    Object elem = elemEnum.nextElement();
	    Object key = keyEnum.nextElement();
	    ObjectObjectPair pair = new ObjectObjectPair(elem, key);
	    array[i] = pair;
	    i++;
	}

	// sort words according their frequency
	ElemComparator intComp = new ElemComparator();

	QuickSort.sort(array, intComp);

	// return top n words
	Vector topWords = new Vector();
	int retNum = nextWordSize;
	if (array.length < retNum)
	    retNum = array.length;
	for (int j = 0; j < retNum; j++) {
	    topWords
		    .addElement(((ObjectObjectPair) array[array.length - 1 - j])
			    .getSecond());
	}
	return topWords;
    }

    /**
     * Initiates the B+ Tree by bulkloading it keys: dewey identifiers values:
     * LightLinguisticTokens
     * 
     * @param f
     */

    private void initBPTreeIndex() throws MXQueryException {
	btree = new BTree(this.k, this.kstar);
	if (linguisticTokens != null) {
	    btreeRoot = btree.bulkLoad(new PhraseIterator(linguisticTokens,
		    PhraseIterator.FLAT_PHRASE), this.f);
	} else {
	    throw new MXQueryException("", "BPTree not initated", null);
	}
    }

    /**
     * Initiates the inverted list: Hashtable: word -> list of corresponding
     * Light Linguistic Tokens
     */
    private void initHashtableIndex() throws MXQueryException {
	Vector list;
	Integer frequencyCount;
	invertedList = new Hashtable();
	frequencyTable = new Hashtable();

	for (int i = 0; i < linguisticTokens.size(); i++) {
	    LinguisticToken token = (LinguisticToken) linguisticTokens
		    .elementAt(i);

	    String text = token.getText().toLowerCase();
	    text = DiacriticsUtils.getWordWithoutDiacritics(text);
	    // insert the word into the inverted list
	    // if the word is contained, retrieve the list, add position and
	    // remove the old entry
	    if (invertedList.containsKey(text)) {

		// hashtable word -> list of LT
		Object olist = invertedList.get(text);
		Vector old_list = (Vector) olist;
		old_list.addElement(token);
		// list = new Vector();
		list = old_list;
		invertedList.remove(text);

		// hashtable word -> frequency
		Integer ofrequencyCount = (Integer) frequencyTable.get(text);
		int old_count = ofrequencyCount.intValue();
		old_count++;
		frequencyCount = new Integer(old_count);
		frequencyTable.remove(text);

	    }

	    // if the word is not contained, create a new list
	    else {
		list = new Vector();
		list.addElement(token);
		frequencyCount = new Integer(1);

	    }
	    invertedList.put(text, list);
	    frequencyTable.put(text, frequencyCount);
	}
    }

    public double getInverseDocumentFrequency(String word) {
	return 0;
    }

    // keyword methods

    public Vector getWordsExact(String word) {
	Vector list = (Vector) invertedList.get(word);
	if (list == null) {
	    return new Vector();
	}
	Vector words = new Vector();
	for (int i = 0; i < list.size(); i++) {
	    String s = ((LinguisticToken) list.elementAt(i)).getText();
	    if (!words.contains(s)) {
		words.addElement(s);
	    }
	}
	return words;

    }

    /**
     * returns all words for the word with the stem "stem" in a vector
     */
    public Vector getWordsForStem(String stem) {
	Vector results = (Vector) stemIndex.get(stem);
	if (results == null) {
	    return new Vector();
	} else {
	    return results;
	}

    }

    /**
     * returns all Linguistic Tokens for the word with prefix "prefix" and
     * suffix "suffix" and fulfilling the wildcard predicate
     */
    public PhraseIterator getLinguisticTokensWithInfix(String prefix,
	    String suffix, String wildcard) {
	Vector prefixNgrams = null;
	Vector suffixNgrams = null;
	Vector ngrams = new Vector();
	Vector words = null;
	Vector results = null;
	Vector linguisticTokens = new Vector();
	NGramExtractor extractor = new NGramExtractor();
	suffixNgrams = extractor.extractNGrams(suffix, 2, "suffix");
	prefixNgrams = extractor.extractNGrams(prefix, 2, "prefix");

	for (int i = 0; i < prefixNgrams.size(); i++) {
	    ngrams.addElement(prefixNgrams.elementAt(i));
	}

	for (int j = 0; j < suffixNgrams.size(); j++) {
	    ngrams.addElement(suffixNgrams.elementAt(j));
	}

	words = computeIntersection(ngrams);
	if (words != null) {
	    String[] originalQuery = new String[2];
	    originalQuery[0] = prefix;
	    originalQuery[1] = suffix;
	    results = checkAgainstOriginalQuery(words, originalQuery, "infix",
		    wildcard);
	    if (results != null) {
		for (int i = 0; i < results.size(); i++) {
		    Vector temp = (Vector) invertedList.get(results
			    .elementAt(i));
		    for (int j = 0; j < temp.size(); j++) {
			linguisticTokens.addElement(temp.elementAt(j));
		    }
		}
		return new PhraseIterator(linguisticTokens,
			PhraseIterator.FLAT_PHRASE);
	    }
	}
	return new PhraseIterator(null, PhraseIterator.FLAT_PHRASE);
    }

    /**
     * returns all words for the word with prefix "prefix" and suffix "suffix"
     * and fulfilling the wildcard predicate
     */
    public Vector getWordsWithInfix(String prefix, String suffix,
	    String wildcard) {
	Vector prefixNgrams = null;
	Vector suffixNgrams = null;
	Vector ngrams = new Vector();
	Vector words = null;
	NGramExtractor extractor = new NGramExtractor();
	suffixNgrams = extractor.extractNGrams(suffix, 2, "suffix");
	prefixNgrams = extractor.extractNGrams(prefix, 2, "prefix");

	for (int i = 0; i < prefixNgrams.size(); i++) {
	    ngrams.addElement(prefixNgrams.elementAt(i));
	}

	for (int j = 0; j < suffixNgrams.size(); j++) {
	    ngrams.addElement(suffixNgrams.elementAt(j));
	}

	words = computeIntersection(ngrams);
	if (words != null) {
	    String[] originalQuery = new String[2];
	    originalQuery[0] = prefix;
	    originalQuery[1] = suffix;
	    return checkAgainstOriginalQuery(words, originalQuery, "infix",
		    wildcard);

	}
	return new Vector();
    }

    /**
     * returns all Linguistic Tokens for the word with suffix "suffix" and
     * fulfilling the wildcard predicate
     */
    public PhraseIterator getLinguisticTokensWithPrefix(String suffix,
	    String wildcard) {
	Vector ngrams = null;
	Vector words = null;
	Vector results = null;
	Vector linguisticTokens = new Vector();
	NGramExtractor extractor = new NGramExtractor();
	ngrams = extractor.extractNGrams(suffix, 2, "suffix");
	words = computeIntersection(ngrams);
	if (words != null) {
	    String[] originalQuery = new String[1];
	    originalQuery[0] = suffix;
	    results = checkAgainstOriginalQuery(words, originalQuery, "prefix",
		    wildcard);
	    if (results != null) {
		for (int i = 0; i < results.size(); i++) {
		    Vector temp = (Vector) invertedList.get(results
			    .elementAt(i));
		    for (int j = 0; j < temp.size(); j++) {
			linguisticTokens.addElement(temp.elementAt(j));
		    }
		}
		return new PhraseIterator(linguisticTokens,
			PhraseIterator.FLAT_PHRASE);
	    }
	}
	return new PhraseIterator(null, PhraseIterator.FLAT_PHRASE);
    }

    /**
     * returns all words for the word with suffix "suffix" and fulfilling the
     * wildcard predicate
     */
    public Vector getWordsWithPrefix(String suffix, String wildcard) {
	Vector ngrams = null;
	Vector words = null;
	NGramExtractor extractor = new NGramExtractor();
	ngrams = extractor.extractNGrams(suffix, 2, "suffix");
	words = computeIntersection(ngrams);
	if (words != null) {
	    String[] originalQuery = new String[1];
	    originalQuery[0] = suffix;
	    return checkAgainstOriginalQuery(words, originalQuery, "prefix",
		    wildcard);
	}
	return new Vector();
    }

    /**
     * returns all Linguistic Tokens for the word with prefix "prefix" and
     * fulfilling the wildcard predicate
     */
    public PhraseIterator getLinguisticTokensWithSuffix(String prefix,
	    String wildcard) {
	Vector ngrams = null;
	Vector words = null;
	Vector results = null;
	Vector linguisticTokens = new Vector();
	NGramExtractor extractor = new NGramExtractor();
	ngrams = extractor.extractNGrams(prefix, 2, "prefix");
	words = computeIntersection(ngrams);
	if (words != null) {
	    String[] originalQuery = new String[1];
	    originalQuery[0] = prefix;
	    results = checkAgainstOriginalQuery(words, originalQuery, "suffix",
		    wildcard);
	    if (results != null) {
		for (int i = 0; i < results.size(); i++) {
		    Vector temp = (Vector) invertedList.get(results
			    .elementAt(i));
		    for (int j = 0; j < temp.size(); j++) {
			linguisticTokens.addElement(temp.elementAt(j));
		    }
		}
		return new PhraseIterator(linguisticTokens,
			PhraseIterator.FLAT_PHRASE);
	    }
	}
	return new PhraseIterator(null, PhraseIterator.FLAT_PHRASE);
    }

    /**
     * returns all words for the word with prefix "prefix" and fulfilling the
     * wildcard predicate
     */
    public Vector getWordsWithSuffix(String prefix, String wildcard) {
	Vector ngrams = null;
	Vector words = null;
	NGramExtractor extractor = new NGramExtractor();
	ngrams = extractor.extractNGrams(prefix, 2, "prefix");
	words = computeIntersection(ngrams);
	if (words != null) {
	    String[] originalQuery = new String[1];
	    originalQuery[0] = prefix;
	    return checkAgainstOriginalQuery(words, originalQuery, "suffix",
		    wildcard);

	}
	return new Vector();
    }

    /**
     * returns all words for the word "text" and fulfilling the wildcard
     * predicate
     */

    public Vector getWordsForWildcards(String text) {

	WildcardUtils utils = new WildcardUtils();
	Vector wildcards = utils.getWildcards(text);
	Vector positions = utils.getWildcardPosition(text);
	Vector words = new Vector();

	// more than one wildcard
	if (wildcards.size() > 1) {
	    words = getWordsForMultipleWildCard(text);
	}
	// single wildcard
	else if (utils.contains(text, ".")) {
	    String wildcardPosition = (String) positions.elementAt(0);
	    String wildcard = (String) wildcards.elementAt(0);
	    if (wildcardPosition.equals("prefix")) {
		words = getWordsWithPrefix(utils.getSuffix(text, wildcard),
			wildcard);
	    } else if (wildcardPosition.equals("infix")) {
		String[] prefixAndSuffix = utils.getPrefixAndSuffix(text,
			wildcard);
		words = getWordsWithInfix(prefixAndSuffix[0],
			prefixAndSuffix[1], wildcard);
	    } else if (wildcardPosition.equals("suffix")) {
		words = getWordsWithSuffix(utils.getPrefix(text, wildcard),
			wildcard);
	    }
	}
	// no wildcard
	else {
	    words = getWordsExact(text);
	    words.addElement(text);
	}

	return words;
    }

    // phrase methods
    /**
     * 
     * @param v
     *                phrase (each word as an element)
     * @return all the combinations of the different words that have the same
     *         stem as the original words in the phrase
     */
    public Vector getPhraseWithStemming(Vector v) {

	Vector words = new Vector();
	for (int i = 0; i < v.size(); i++) {
	    String word = (String) v.elementAt(i);
	    Vector wordsForStem = (Vector) stemIndex.get(word);
	    if (wordsForStem != null) {
		int j = 0;
		Vector vector = new Vector();
		while (j < wordsForStem.size()) {
		    String wordForStem = (String) wordsForStem.elementAt(j);
		    if (!vector.contains(wordForStem)) {
			vector.addElement(wordForStem);
		    }
		    j++;
		}
		words.addElement(vector);
	    } else {
		Vector newVector = new Vector();
		newVector.addElement(word);
		words.addElement(newVector);
	    }
	}

	return Utils.getCombinations(new Vector(), new Vector(), words, 0);
    }

    /**
     * 
     * @param v
     *                phrase (each word as an element)
     * @return a Phrase Iterator with all the phrases that contain all the words
     *         of v
     */

    public PhraseIterator getPhraseExact(Vector v, DeweyIdentifier[] ignoreId)
	    throws MXQueryException {

	WildcardUtils utils = new WildcardUtils();

	String phrase = "";
	for (int i = 0; i < v.size(); i++) {
	    String s = (String) v.elementAt(i);
	    phrase = phrase + s + " ";
	}

	phrase = phrase.trim();

	String[] words;
	Vector phraseTokens = new Vector();
	Vector wordLists = new Vector();
	Vector nextWords = new Vector();
	Vector results = new Vector();
	int phraseElementCounter = 0;

	if (utils.checkForWildcards(phrase)) {
	    return getPhraseWithWildcard(phrase, ignoreId);
	} else {
	    String[] delimiters = { ",", "(", ")", ";", ".", "!", "?", "{",
		    "}", "\"", "[", "]", "|", "#", "%", "&", "/", ":", "'", " " };
	    words = Utils.split(phrase, delimiters);
	    phraseElementCounter = words.length;
	    for (int i = 0; i < words.length; i++) {
		PhraseToken token = new PhraseToken(words[i]);
		phraseTokens.addElement(token);
	    }
	}

	if (nextWordIndex == null) {
	    initNextwordIndex();
	}

	int i = 0;
	while (i < phraseTokens.size()) {
	    // check whether word is a top word and a nextword list exists for
	    // it

	    PhraseToken token = (PhraseToken) phraseTokens.elementAt(i);
	    String word = token.getWord().toLowerCase();
	    Vector nextWordList = (Vector) nextWordIndex.get(word);

	    // no nextword list exists
	    if (nextWordList == null) {

		String txt = ((PhraseToken) phraseTokens.elementAt(i))
			.getWord().toLowerCase();
		Vector firstWordList = (Vector) invertedList.get(txt);

		// word is not contained, so phrase is not contained
		if (firstWordList == null) {
		    return new PhraseIterator(null,
			    PhraseIterator.NESTED_PHRASE);
		} else {
		    token.setList(firstWordList);
		    wordLists.addElement(token);
		}
	    } else {
		boolean found = false;
		// go through the list and check whether the nextword is the
		// next word in phrase
		if (i + 1 < phraseTokens.size()) {
		    String nextWordInPhrase = ((PhraseToken) phraseTokens
			    .elementAt(i + 1)).getWord().toLowerCase();
		    for (int j = 0; j < nextWordList.size(); j++) {
			ObjectObjectPair pair = (ObjectObjectPair) nextWordList
				.elementAt(j);
			if (((LinguisticToken) pair.getSecond()).getText()
				.toLowerCase().equals(nextWordInPhrase)) {
			    nextWords.addElement(pair);
			    if (found) {
				i++;
				found = true;
			    }
			}

		    }

		    token.setNextWordFlag();
		    token.setList(nextWords);

		} else {
		    Vector firstWordList = (Vector) invertedList
			    .get(((PhraseToken) phraseTokens.elementAt(i))
				    .getWord().toLowerCase());
		    token.setList(firstWordList);
		    wordLists.addElement(token);
		}

	    }
	    results.addElement(token);
	    i++;
	}

	Vector phrases = findPhrases(results, phraseElementCounter, ignoreId);

	return new PhraseIterator(phrases, PhraseIterator.NESTED_PHRASE);
    }

    // helper methods for wildcard queries

    // helper method for phrases with words containing wildcards:
    // depending on where the wildcards stand and what kind of wildcards there
    // are,
    // the right method is invoked and the corresponding iterator returned
    private PhraseIterator getWildcardIterator(String word) {

	WildcardUtils utils = new WildcardUtils();
	Vector wildcards = utils.getWildcards(word);
	Vector positions = utils.getWildcardPosition(word);
	PhraseIterator iter = null;
	if (wildcards.size() > 1) {
	    iter = getLinguisticTokensForMultipleWildCard(word);
	} else {
	    String wildcard = (String) wildcards.elementAt(0);
	    String position = (String) positions.elementAt(0);

	    if (position.equals("suffix")) {
		iter = getLinguisticTokensWithSuffix(utils.getPrefix(word,
			wildcard), wildcard);
	    } else if (position.equals("prefix")) {
		iter = getLinguisticTokensWithPrefix(utils.getSuffix(word,
			wildcard), wildcard);
	    } else {
		String[] prefixAndSuffix = utils.getPrefixAndSuffix(word,
			wildcard);
		iter = getLinguisticTokensWithInfix(prefixAndSuffix[0],
			prefixAndSuffix[1], wildcard);
	    }
	}
	return iter;
    }

    // needs to be done as n-gram index returns false positives
    private Vector checkAgainstOriginalQuery(Vector words,
	    String[] originalQuery, String position, String wildcard) {
	Vector result = new Vector();
	String oquery = originalQuery[0];

	if (wildcard.equals(".*")) {
	    if (position.equals("prefix")) {
		for (int i = 0; i < words.size(); i++) {
		    String word = (String) words.elementAt(i);
		    if (word.endsWith(oquery)) {
			result.addElement(word);
		    }
		}
		return result;
	    } else if (position.equals("suffix")) {
		for (int i = 0; i < words.size(); i++) {
		    String word = (String) words.elementAt(i);
		    if (word.startsWith(oquery)) {
			result.addElement(word);
		    }
		}
		return result;
	    } else if (position.equals("infix")) {
		String oquery2 = originalQuery[1];
		for (int i = 0; i < words.size(); i++) {
		    String word = (String) words.elementAt(i);
		    if (word.startsWith(oquery) && word.endsWith(oquery2)) {
			result.addElement(word);
		    }
		}
		return result;
	    }

	} else if (wildcard.equals(".?")) {
	    if (position.equals("prefix")) {
		for (int i = 0; i < words.size(); i++) {
		    String word = (String) words.elementAt(i);
		    if (word.endsWith(oquery)) {
			int len = oquery.length();
			int wlen = word.length();
			if (wlen == len || wlen == len + 1) {
			    result.addElement(word);
			}
		    }
		}
		return result;
	    } else if (position.equals("suffix")) {
		for (int i = 0; i < words.size(); i++) {
		    String word = (String) words.elementAt(i);
		    if (word.startsWith(oquery)) {
			int len = oquery.length();
			int wlen = word.length();
			if (wlen == len || wlen == len + 1) {
			    result.addElement(word);
			}
		    }
		}
		return result;
	    } else if (position.equals("infix")) {
		String oquery2 = originalQuery[1];
		for (int i = 0; i < words.size(); i++) {
		    String word = (String) words.elementAt(i);
		    if ((word.startsWith(oquery) && word.endsWith(oquery2))) {
			int len = oquery.length() + oquery2.length();
			int wlen = word.length();
			if (wlen == len || wlen == len + 1) {
			    result.addElement(word);
			}
		    }
		}
		return result;
	    }

	} else if (wildcard.equals(".+")) {
	    if (position.equals("prefix")) {
		for (int i = 0; i < words.size(); i++) {
		    String word = (String) words.elementAt(i);
		    if (word.endsWith(oquery)) {
			if (word.length() > oquery.length()) {
			    result.addElement(word);
			}
		    }
		}
		return result;
	    } else if (position.equals("suffix")) {
		for (int i = 0; i < words.size(); i++) {
		    String word = (String) words.elementAt(i);
		    if (word.startsWith(oquery)) {
			if (word.length() > oquery.length()) {
			    result.addElement(word);
			}
		    }
		}
		return result;
	    } else if (position.equals("infix")) {
		String oquery2 = originalQuery[1];
		for (int i = 0; i < words.size(); i++) {
		    String word = (String) words.elementAt(i);
		    if (word.startsWith(oquery) && word.endsWith(oquery2)) {
			if (word.length() > oquery.length() + oquery2.length()) {
			    result.addElement(word);
			}
		    }
		}
		return result;
	    }
	} else if (wildcard.equals(".")) {
	    if (position.equals("prefix")) {
		for (int i = 0; i < words.size(); i++) {
		    String word = (String) words.elementAt(i);
		    if (word.endsWith(oquery)) {
			if (word.length() == oquery.length() + 1) {
			    result.addElement(word);
			}
		    }
		}
		return result;
	    } else if (position.equals("suffix")) {
		for (int i = 0; i < words.size(); i++) {
		    String word = (String) words.elementAt(i);
		    if (word.startsWith(oquery)) {
			if (word.length() == oquery.length() + 1) {
			    result.addElement(word);
			}
		    }
		}
		return result;
	    } else if (position.equals("infix")) {
		String oquery2 = originalQuery[1];
		for (int i = 0; i < words.size(); i++) {
		    String word = (String) words.elementAt(i);
		    if (word.startsWith(oquery) && word.endsWith(oquery2)) {
			if (word.length() == (oquery.length() + oquery2
				.length()) + 1) {
			    result.addElement(word);
			}
		    }
		}
		return result;
	    }
	}
	// wildcard is something like .{x,y}
	else {
	    String sfrom = wildcard.substring(2, 3);
	    String sto = wildcard.substring(4, 5);
	    int from = Integer.parseInt(sfrom);
	    int to = Integer.parseInt(sto);
	    int olen = oquery.length();

	    if (from < to) {
		if (position.equals("prefix")) {
		    for (int i = 0; i < words.size(); i++) {
			String word = (String) words.elementAt(i);
			if (word.endsWith(oquery)) {
			    int wlen = word.length();
			    if (wlen >= olen + from && wlen <= olen + to) {
				result.addElement(word);
			    }
			}
		    }
		    return result;
		} else if (position.equals("suffix")) {
		    for (int i = 0; i < words.size(); i++) {
			String word = (String) words.elementAt(i);
			if (word.startsWith(oquery)) {
			    int wlen = word.length();
			    if (wlen >= olen + from && wlen <= olen + to) {
				result.addElement(word);
			    }
			}
		    }
		    return result;
		} else if (position.equals("infix")) {
		    String oquery2 = originalQuery[1];
		    int olen2 = originalQuery[1].length();
		    for (int i = 0; i < words.size(); i++) {
			String word = (String) words.elementAt(i);
			if (word.startsWith(oquery) && word.endsWith(oquery2)) {
			    int wlen = word.length();
			    if (wlen >= olen + olen2 + from
				    && wlen <= olen + olen2 + to) {
				result.addElement(word);
			    }
			}
		    }
		    return result;
		}
	    }

	}

	return null;
    }

    // checks whether the LinguisticTokens appear next to each other
    private Vector findPhrases(Vector results, int elementInPhrase,
	    DeweyIdentifier[] ignoreId) {

	PhraseToken firstToken = (PhraseToken) results.elementAt(0);
	Vector firstList = firstToken.getList();
	Vector phrases = new Vector();
	Vector resultPhrases = new Vector();
	int startPos = 0;

	if (firstList != null) {
	    if (firstToken.isNextWordPhraseToken()) {
		for (int i = 0; i < firstList.size(); i++) {
		    ObjectObjectPair pair = (ObjectObjectPair) firstList
			    .elementAt(i);
		    Phrase newPhrase = new Phrase((LinguisticToken) pair
			    .getFirst());
		    newPhrase.addLightLinguisticToken((LinguisticToken) pair
			    .getSecond());
		    phrases.addElement(newPhrase);
		    startPos = 2;
		}
	    } else {
		for (int i = 0; i < firstList.size(); i++) {
		    Phrase newPhrase = new Phrase((LinguisticToken) firstList
			    .elementAt(i));
		    phrases.addElement(newPhrase);
		    startPos = 1;
		}
	    }
	} else {
	    return new Vector();
	}

	for (int l = 0; l < phrases.size(); l++) {

	    Phrase currentPhrase = (Phrase) phrases.elementAt(l);

	    for (int j = startPos; j < results.size(); j++) {

		LinguisticToken lastInsertedToken = currentPhrase
			.getLastInsertedElement();
		int lastInsertedTokensPosition = lastInsertedToken
			.getPosition();

		PhraseToken token = (PhraseToken) results.elementAt(j);
		if (!token.isNextWordPhraseToken()) {

		    Vector list = token.getList();
		    if (list != null) {
			for (int k = 0; k < list.size(); k++) {
			    LinguisticToken currentToken = (LinguisticToken) list
				    .elementAt(k);

			    int currentPosition = currentToken.getPosition();
			    if (lastInsertedTokensPosition + 1 == currentPosition) {
				currentPhrase
					.addLightLinguisticToken(currentToken);
			    }
			}
		    }
		} else {
		    Vector list = token.getList();
		    for (int k = 0; k < list.size(); k++) {
			ObjectObjectPair pair = (ObjectObjectPair) list
				.elementAt(k);
			LinguisticToken currentToken = (LinguisticToken) pair
				.getFirst();
			LinguisticToken currentNextToken = (LinguisticToken) pair
				.getSecond();
			int currentPosition = currentToken.getPosition();
			if (lastInsertedTokensPosition + 1 == currentPosition) {
			    currentPhrase.addLightLinguisticToken(currentToken);
			    currentPhrase
				    .addLightLinguisticToken(currentNextToken);

			}
		    }
		    j++;
		}
	    }

	    if (currentPhrase.phraseSize() == elementInPhrase) {
		resultPhrases.addElement(currentPhrase);
	    } else if (ignoreId != null) {
		// scan and check whether the phrase can be found
		LinguisticToken lastFoundToken = currentPhrase
			.getLastInsertedElement();
		DeweyIdentifier lastFoundTokenId = lastFoundToken.getDeweyId();
		DeweyIdentifier parentId = (DeweyIdentifier) lastFoundToken
			.getTextToken().getId();
		DeweyIdentifier parentElId = new DeweyIdentifier(parentId
			.getDeweyId(), parentId.getDeweyLevel() - 1, parentId
			.getStore());

		Vector siblings = btree.getSiblings(lastFoundTokenId,
			parentElId, btreeRoot, ignoreId);

		int posInPhrase = currentPhrase.phraseSize();
		int i = 0;
		int j = posInPhrase;

		LinguisticToken lastToken = null;

		while (i < siblings.size() && j < results.size()) {
		    LinguisticToken token = (LinguisticToken) siblings
			    .elementAt(i);
		    String text = token.getText();
		    String wordPosInPhrase = ((PhraseToken) results
			    .elementAt(j)).getWord();
		    if (text.equals(wordPosInPhrase)) {

			// first token found
			if (lastToken == null) {
			    if (token.getPosition() == lastFoundToken
				    .getPosition() + 1) {
				posInPhrase++;
			    }
			    currentPhrase.addLightLinguisticToken(token);
			    lastToken = token;
			}

			else if (lastToken != null) {
			    currentPhrase.addLightLinguisticToken(token);
			    lastToken = token;
			}

			j++;
		    } else {
			// look further in siblings
			if (lastToken != null) {
			    currentPhrase.removeElements(posInPhrase);
			}
			j = posInPhrase;
			lastToken = null;
		    }
		    i++;

		}
		if (currentPhrase.phraseSize() == elementInPhrase) {
		    resultPhrases.addElement(currentPhrase);
		}
	    }
	}

	return resultPhrases;
    }

    /**
     * 
     * @param phrase
     *                phrase (as String)
     * @return a Phrase Iterator with all the phrases that contain all the words
     *         of the phrase and fulfills the Wildcard predicate
     * @throws MXQueryException
     */
    public PhraseIterator getPhraseWithWildcard(String phrase,
	    DeweyIdentifier[] ignoreId) throws MXQueryException {

	WildcardUtils utils = new WildcardUtils();
	Vector phraseTokens = new Vector();
	Vector phrases = new Vector();
	Vector results; // = new Vector();
	int phraseElementCounter = 0;
	PhraseIterator iterator = null;

	// separation of the words
	String currentWord = "";
	int i = 0;
	int wordCounter = 0;
	while (i < phrase.length()) {
	    char c = phrase.charAt(i);
	    // new word
	    if (c == ' ') {
		wordCounter++;
		PhraseToken ptoken = new PhraseToken(currentWord);
		phraseTokens.addElement(ptoken);
		currentWord = "";
	    } else {
		currentWord = currentWord + c;
	    }
	    i++;
	}
	// the last word in the phrase
	wordCounter++;
	PhraseToken ptoken = new PhraseToken(currentWord);
	phraseTokens.addElement(ptoken);

	phraseElementCounter = wordCounter;
	int j = 0;

	// go through every token in the phrase and check whether the word
	// contains a wildcard
	while (j < phraseTokens.size()) {
	    PhraseToken token = (PhraseToken) phraseTokens.elementAt(j);
	    String word = token.getWord();

	    // word contains a wildcard and all matching words fulfilling it
	    // need to be checked for the phrase
	    if (utils.checkForWildcards(word)) {
		iterator = getWildcardIterator(word);

		Vector listOfLists = new Vector();
		while (iterator.hasNext()) {
		    LinguisticToken lltoken = null;
		    lltoken = (LinguisticToken) iterator.next();
		    String wordWithWildcard = lltoken.getText();

		    Vector invertedIndex = (Vector) invertedList
			    .get(wordWithWildcard);
		    if (invertedIndex == null) {
			return new PhraseIterator(null,
				PhraseIterator.NESTED_PHRASE);
		    } else {
			for (int l = 0; l < invertedIndex.size(); l++) {
			    if (!listOfLists.contains(invertedIndex
				    .elementAt(l))) {
				listOfLists.addElement(invertedIndex
					.elementAt(l));
			    }
			}
		    }
		}
		token.setList(listOfLists);
		phrases.addElement(token);
	    }

	    // the word contains no wildcard
	    else {

		// check whether it is a top word
		Vector nextWordList = (Vector) nextWordIndex.get(word);
		if (nextWordList != null) {
		    // go through the list and check whether the nextword is the
		    // next word in phrase
		    // word is not the last word in the phrase
		    if (j + 1 < phraseTokens.size()) {
			String nextWordInPhrase = ((PhraseToken) phraseTokens
				.elementAt(j + 1)).getWord();

			// next word in phrase contains no wildcard
			if (!utils.checkForWildcards(nextWordInPhrase)) {
			    Vector nextWords = new Vector();
			    for (int k = 0; k < nextWordList.size(); k++) {
				ObjectObjectPair pair = (ObjectObjectPair) nextWordList
					.elementAt(k);
				if (((LinguisticToken) pair.getSecond())
					.getText().equals(nextWordInPhrase)) {
				    nextWords.addElement(pair);
				    j++;
				}
			    }
			    token.setNextWordFlag();
			    token.setList(nextWords);
			}
			// next word in phrase contains wildcard(s)
			else {
			    // get all the words that might match
			    PhraseIterator iter = getWildcardIterator(nextWordInPhrase);
			    Vector nextWords = new Vector();
			    // check whether one of these words is in the
			    // nextword list
			    if (iter.hasNext()) {
				LinguisticToken nextToken = (LinguisticToken) iter
					.next();
				String nextTokenText = nextToken.getText();
				for (int l = 0; l < nextWordList.size(); l++) {
				    ObjectObjectPair pair = (ObjectObjectPair) nextWordList
					    .elementAt(l);
				    if (((LinguisticToken) pair.getSecond())
					    .getText().equals(nextTokenText)) {
					nextWords.addElement(pair);
					j++;
				    }
				}
			    }
			    token.setNextWordFlag();
			    token.setList(nextWords);
			    phrases.addElement(token);

			}
		    }
		    // word is the last element in phrase
		    else {
			Vector normalList = (Vector) invertedList.get(word);
			token.setList(normalList);
			phrases.addElement(token);
		    }
		}
		// no top word, so get its list
		else {
		    Vector normalList = (Vector) invertedList.get(word);
		    token.setList(normalList);
		    phrases.addElement(token);
		}
	    }
	    j++;

	}

	results = findPhrases(phrases, phraseElementCounter, ignoreId);
	return new PhraseIterator(results, PhraseIterator.NESTED_PHRASE);
    }

    /**
     * 
     * @param v
     *                phrase (each word as an element)
     * @return a Phrase Iterator with all the phrases that contain all the words
     *         of v fulfilling the wildcard predicate
     */
    public Vector getPhraseWithWildcards(Vector v) {

	WildcardUtils utils = new WildcardUtils();
	Vector vectors = new Vector();
	int j = 0;

	// go through every token in the phrase and check whether the word
	// contains a wildcard
	while (j < v.size()) {

	    String word = (String) v.elementAt(j);

	    // word contains a wildcard and all matching words fulfilling it
	    // need to be checked for the phrase
	    Vector list = new Vector();
	    if (utils.checkForWildcards(word)) {
		Vector vector = getWordsForWildcards(word);
		int k = 0;
		while (k < vector.size()) {
		    if (!list.contains(vector.elementAt(k))) {
			list.addElement(vector.elementAt(k));
		    }
		    k++;
		}
	    }
	    // the word contains no wildcard
	    else {

		Vector normalList = (Vector) invertedList.get(word);
		if (normalList != null) {
		    for (int k = 0; k < normalList.size(); k++) {
			LinguisticToken tok = (LinguisticToken) normalList
				.elementAt(k);
			String text = tok.getText();
			text = text.toLowerCase();
			text = DiacriticsUtils.getWordWithoutDiacritics(text);

			if (!list.contains(text)) {
			    list.addElement(text);
			}
		    }
		} else {
		    if (!list.contains(word)) {
			list.addElement(word);
		    }
		}

	    }

	    vectors.addElement(list);
	    j++;
	}

	return Utils.getCombinations(new Vector(), new Vector(), vectors, 0);
    }

    public LinguisticToken getLinguisticTokens(DeweyIdentifier did) {

	if (btree != null) {
	    LinguisticToken result = btree.get(did, btreeRoot);
	    Vector token = new Vector();
	    token.addElement(result);
	    return result;
	} else {
	    return null;
	}

    }

    public PhraseIterator getLinguisticTokensExact(String word) {
	Vector list = (Vector) invertedList.get(word);
	if (list == null) {
	    return new PhraseIterator(null, PhraseIterator.FLAT_PHRASE);
	}
	return new PhraseIterator(list, PhraseIterator.FLAT_PHRASE);

    }

    public PhraseIterator getLinguisticTokensForMultipleWildCard(String word) {
	WildcardUtils utils = new WildcardUtils();
	Vector wildcardPositionPairs = utils.parseWord(word);
	Vector positions = utils.getWildcardPosition(word);
	NGramExtractor extractor = new NGramExtractor();
	Vector ngrams = null;
	Vector words = null;
	// there is only a prefix and a suffix wildcard
	Vector strings = (Vector) wildcardPositionPairs.elementAt(0);
	if (strings.size() == 1) {
	    String w = (String) strings.elementAt(0);
	    ngrams = extractor.extractNGrams(w, 2, "");
	}
	// there are prefix, suffix and infix(es) wildcards
	else {
	    Vector parts = (Vector) wildcardPositionPairs.elementAt(0);
	    ngrams = new Vector();
	    Vector temp = null;
	    boolean prefix = false;

	    for (int i = 0; i < parts.size(); i++) {
		if (extractor != null) {
		    if (positions.elementAt(i).equals("infix")) {
			if ((prefix == false)) {
			    temp = extractor.extractNGrams((String) parts
				    .elementAt(i), 2, "prefix");
			    for (int j = 0; j < temp.size(); j++) {
				ngrams.addElement(temp.elementAt(j));
			    }
			} else if (prefix == true && parts.size() <= 2) {
			    temp = extractor.extractNGrams((String) parts
				    .elementAt(i), 2, "suffix");
			    for (int j = 0; j < temp.size(); j++) {
				ngrams.addElement(temp.elementAt(j));
			    }
			} else {
			    temp = extractor.extractNGrams((String) parts
				    .elementAt(i), 2, "");
			    for (int j = 0; j < temp.size(); j++) {
				ngrams.addElement(temp.elementAt(j));
			    }
			}
		    } else if (positions.elementAt(i).equals("suffix")) {
			temp = extractor.extractNGrams((String) parts
				.elementAt(i), 2, "");
			for (int j = 0; j < temp.size(); j++) {
			    ngrams.addElement(temp.elementAt(j));
			}
		    } else if (positions.elementAt(i).equals("prefix")) {
			prefix = true;
			temp = extractor.extractNGrams((String) parts
				.elementAt(i), 2, "");
			for (int j = 0; j < temp.size(); j++) {
			    ngrams.addElement(temp.elementAt(j));
			}
		    }
		}
	    }
	}
	words = computeIntersection(ngrams);
	Vector results = utils.checkAgainstOriginalQueryMultipleWildcard(words,
		wildcardPositionPairs);
	Vector lltokenResults = new Vector();
	for (int i = 0; i < results.size(); i++) {
	    String s = (String) results.elementAt(i);
	    Vector v = (Vector) invertedList.get(s);
	    for (int j = 0; j < v.size(); j++) {
		LinguisticToken element = (LinguisticToken) v.elementAt(j);
		if (!lltokenResults.contains(element)) {
		    lltokenResults.addElement(element);
		}
	    }
	}
	return new PhraseIterator(lltokenResults, PhraseIterator.FLAT_PHRASE);
    }

    public Vector getWordsForMultipleWildCard(String word) {
	WildcardUtils utils = new WildcardUtils();
	Vector wildcardPositionPairs = utils.parseWord(word);
	Vector positions = utils.getWildcardPosition(word);
	NGramExtractor extractor = new NGramExtractor();
	Vector ngrams = null;
	Vector words = null;
	// there is only a prefix and a suffix wildcard
	Vector strings = (Vector) wildcardPositionPairs.elementAt(0);
	if (strings.size() == 1) {
	    String w = (String) strings.elementAt(0);
	    if (extractor != null) {
		ngrams = extractor.extractNGrams(w, 2, "");
	    }
	}
	// there are prefix, suffix and infix(es) wildcards
	else {
	    Vector parts = (Vector) wildcardPositionPairs.elementAt(0);
	    ngrams = new Vector();
	    Vector temp = null;
	    boolean prefix = false;

	    for (int i = 0; i < parts.size(); i++) {
		if (extractor != null) {
		    if (positions.elementAt(i).equals("infix")) {
			if ((prefix == false)) {
			    temp = extractor.extractNGrams((String) parts
				    .elementAt(i), 2, "prefix");
			    for (int j = 0; j < temp.size(); j++) {
				ngrams.addElement(temp.elementAt(j));
			    }
			} else if (prefix == true && parts.size() <= 2) {
			    temp = extractor.extractNGrams((String) parts
				    .elementAt(i), 2, "suffix");
			    for (int j = 0; j < temp.size(); j++) {
				ngrams.addElement(temp.elementAt(j));
			    }
			} else {
			    temp = extractor.extractNGrams((String) parts
				    .elementAt(i), 2, "");
			    for (int j = 0; j < temp.size(); j++) {
				ngrams.addElement(temp.elementAt(j));
			    }
			}
		    } else if (positions.elementAt(i).equals("suffix")) {
			temp = extractor.extractNGrams((String) parts
				.elementAt(i), 2, "");
			for (int j = 0; j < temp.size(); j++) {
			    ngrams.addElement(temp.elementAt(j));
			}
		    } else if (positions.elementAt(i).equals("prefix")) {
			prefix = true;
			temp = extractor.extractNGrams((String) parts
				.elementAt(i), 2, "");
			for (int j = 0; j < temp.size(); j++) {
			    ngrams.addElement(temp.elementAt(j));
			}
		    }
		}
	    }
	}
	words = computeIntersection(ngrams);
	return utils.checkAgainstOriginalQueryMultipleWildcard(words,
		wildcardPositionPairs);

    }

    // helper methods

    public int getNumberOfDescendants() {
	return descendantCounter;
    }

    private Vector computeIntersection(Vector ngrams) {

	Vector list = (Vector) ngramIndex.get(ngrams.elementAt(0));
	if (list != null) {
	    for (int i = 1; i < ngrams.size(); i++) {
		Vector currentList = (Vector) ngramIndex.get(ngrams
			.elementAt(i));
		if (currentList != null) {
		    list = intersect(list, currentList);
		} else {
		    return null;
		}
	    }

	    return list;
	}
	return null;
    }

    private Vector intersect(Vector list1, Vector list2) {

	Vector result = new Vector();

	for (int i = 0; i < list1.size(); i++) {
	    for (int j = 0; j < list2.size(); j++) {
		if ((list1.elementAt(i)).equals(list2.elementAt(j))) {
		    if (!result.contains(list1.elementAt(i))) {
			result.addElement(list1.elementAt(i));
		    }
		}
	    }
	}

	return result;
    }

    // creation of identifiers
    protected Identifier createNextTokenId() {
	if (generateNodeIds) {
	    last_identifier = IdentifierFactory.createIdentifier(nodeIdCount++,
		    this, last_identifier, (short) (level - 1),
		    IdentifierFactory.ID_DEWEY);
	    return last_identifier;
	} else
	    return null;
    }

    public String getURI() {
	return uri;
    }

    public void setUri(String uri) {
	this.uri = uri;
    }

    public int compare(Source store) {
	if (store.getURI() != null) {
	    return this.uri.compareTo(store.getURI());
	} else {
	    return -2;
	}
    }

}

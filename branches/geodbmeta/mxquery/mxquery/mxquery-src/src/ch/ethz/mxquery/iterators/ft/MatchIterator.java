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

package ch.ethz.mxquery.iterators.ft;

import java.util.Vector;

import ch.ethz.mxquery.bindings.WindowFactory;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.DeweyIdentifier;
import ch.ethz.mxquery.datamodel.MXQueryDouble;
import ch.ethz.mxquery.datamodel.MXQueryNumber;
import ch.ethz.mxquery.datamodel.Source;
import ch.ethz.mxquery.datamodel.adm.AllMatch;
import ch.ethz.mxquery.datamodel.adm.FTToken;
import ch.ethz.mxquery.datamodel.adm.LinguisticToken;
import ch.ethz.mxquery.datamodel.adm.Match;
import ch.ethz.mxquery.datamodel.adm.Phrase;
import ch.ethz.mxquery.datamodel.adm.StringMatch;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.StaticException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.functions.fn.DataValuesIterator;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.model.ft.AnyAllOption;
import ch.ethz.mxquery.model.ft.FTCaseMatchOption;
import ch.ethz.mxquery.model.ft.FTLangDepFactory;
import ch.ethz.mxquery.model.ft.FTLanguageMatchOption;
import ch.ethz.mxquery.model.ft.FTThesaurusMatchOption;
import ch.ethz.mxquery.model.ft.MatchOption;
import ch.ethz.mxquery.sms.ftstore.DiacriticsUtils;
import ch.ethz.mxquery.sms.ftstore.FTTokenBufferStore;
import ch.ethz.mxquery.sms.ftstore.PhraseIterator;
import ch.ethz.mxquery.sms.ftstore.Stemmer;
import ch.ethz.mxquery.util.Utils;

/**
 * Implementation of a FTPrimaryWithOptions (Match Option)
 * @author jimhof
 */

public class MatchIterator extends FTBaseIterator{


	private XDMIterator varIter;
	private XDMIterator searchCondIter;
	private AnyAllOption anyAllOption;

	private DeweyIdentifier id;

	private Vector allMatches = new Vector();

	private FTTokenBufferStore store = null;
	private boolean isWildcard = false;
	private boolean isStemming = false;
	private boolean isDiacritics = false;
	private boolean isThesaurus = false;
	private boolean isScoring = false;
	private String location = "";
	private int level = 0;
	private int numberOfElements = 0;
	private DeweyIdentifier [] ignoreId = null;
	private int caseType;
	private int queryPos = 0; 
	
	
	public MatchIterator(Context ctx, final Vector subIters, Vector options, AnyAllOption anyAllOption, boolean useScoring, int qPos) throws MXQueryException {

		super(ctx,(FTIteratorInterface[])null);
		queryPos = qPos;
		if (subIters != null){
			varIter = (XDMIterator)subIters.elementAt(0);
			searchCondIter = DataValuesIterator.getDataIterator((XDMIterator)subIters.elementAt(1),ctx);		
		}
		caseType = context.getFTCase();
		isDiacritics = context.isFTDiacriticsSensitive();
		isStemming = context.isFTStemming();
		isWildcard = context.isFTWildcard();
		isThesaurus = context.getFTThesaurus().isOptionValue();
		
		if (options != null)
			processOptions(options);

		this.anyAllOption = anyAllOption;
		if (this.anyAllOption == null)
			this.anyAllOption = new AnyAllOption(AnyAllOption.ANY_ALL_OPT_ANY);
		isScoring = useScoring;
		
	}

	private void init() throws MXQueryException{

		
		varIter.setResettable(true);
		// get context
		Token tok = varIter.next();
		id = (DeweyIdentifier) tok.getId();

		if (id == null)
			return;
		
		// check whether some content is ignored
		if (ignoreOption != null){ 
			Vector ignIds = new Vector();
			while (ignoreOption.hasNextItem()) {
				Token igToken = ignoreOption.nextItem().next();
				if (!(Type.isNode(igToken.getEventType())))
					throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,"Ignore option requires a node type",null);
				DeweyIdentifier curId = (DeweyIdentifier)igToken.getId();
				ignIds.addElement(curId);
			}
			if (ignIds.size() > 0) {
				ignoreId = new DeweyIdentifier[ignIds.size()];
				for (int i=0;i<ignIds.size();i++) {
					ignoreId[i] = (DeweyIdentifier)ignIds.elementAt(i);
				}
			}
		}
		
		// set store
		if (store == null){
			Source source;
			source = id.getStore();
			if (source instanceof FTTokenBufferStore){
				store = (FTTokenBufferStore) source;
			}
		}
		
						
		Vector textParts = new Vector();
		Vector origTextParts = new Vector();

		Token curWordToken = searchCondIter.next();

		while (curWordToken != Token.END_SEQUENCE_TOKEN) {
			if (!(curWordToken.getEventType() == Type.UNTYPED_ATOMIC|| Type.isTypeOrSubTypeOf(curWordToken.getEventType(), Type.STRING, Context.getDictionary()))) // 
				throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,"String type expected for Match phrase",null); 
			textParts.addElement(curWordToken.getText().toLowerCase());
			origTextParts.addElement(curWordToken.getText());
			curWordToken = searchCondIter.next();
		}

		if (textParts.size() == 0) {
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,"String type expected for Match phrase, got empty sequence	",null);
		}


		initAnyAllOption(textParts, origTextParts);
	}

	
	private PhraseIterator checkForCaseDiacritics(PhraseIterator iterator, Vector words, String text) throws MXQueryException {

		Vector results = new Vector();

		while (iterator.hasNext()){
			if (iterator.getType() == PhraseIterator.FLAT_PHRASE){
				LinguisticToken token = (LinguisticToken) iterator.next();
				String tokenText = token.getText();
				if (checkDiacritics(tokenText,text) && checkCase(token,text)){
					results.addElement(token);
				}
			} else{
				Phrase phrase = (Phrase) iterator.next();
				Vector tokens = phrase.getPhrase();
				Phrase newPhrase = new Phrase();
				for (int i=0; i < tokens.size(); i++){
					String word = (String)words.elementAt(i);
					LinguisticToken token = (LinguisticToken)tokens.elementAt(i);
					if (checkDiacritics(token.getText(),word) && checkCase(token,word)){
						newPhrase.addLightLinguisticToken(token);
					}
				}
				if (newPhrase.phraseSize() == phrase.phraseSize()){
					results.addElement(newPhrase);
				}
			}			
		}
		return new PhraseIterator(results, iterator.getType());
	}
	
	private boolean checkDiacritics(String tokenText, String text) {
		if (!isDiacritics)
			return true;
		int i=0;
		while (i < tokenText.length()){
			char c  = tokenText.charAt(i);
			if (DiacriticsUtils.isADiacritics(c)){
				char tc = text.charAt(i);
				if (c != tc){
					return false;
				}
			}
			i++;
		}
		
		return true;
	}

	private boolean checkCase(LinguisticToken token, String word) {
		String tokenText = token.getText();
		boolean correctVal = false;
		switch (caseType) {
		case FTCaseMatchOption.CASE_SENSITIVE:
			correctVal = Utils.sameCase(tokenText,word);
			break;
		case FTCaseMatchOption.CASE_LOWERCASE:
			correctVal =Utils.isLowerCase(tokenText);
			break;
		case FTCaseMatchOption.CASE_UPPERCASE:
			correctVal = Utils.isUpperCase(tokenText);
			break;
		default:
			correctVal = true;
		break;
		}
		return correctVal;
	}


	// keyword search
	private PhraseIterator initKeywordSearch(String t, String originalWord) throws MXQueryException{
		Vector extendedQuery = new Vector();
		extendedQuery.addElement(t);
		
		int size = extendedQuery.size();

		// if we only have wildcards
		if (isWildcard){
			size = extendedQuery.size();
			for (int l= 0; l< size; l++){
				String wordToExpand = (String)extendedQuery.elementAt(l);
				if (wordToExpand.indexOf('.') >= 0) {
					Vector wildcardWords = store.getWordsForWildcards(wordToExpand);
					extendedQuery = Utils.addToVector(wildcardWords,extendedQuery);
				}
			}
		}
		
		// if with stemming: get stem of keyword
		if (isStemming){
			// first get whole words than stem
			size = extendedQuery.size();
			for (int k=0; k < size; k++){
				String wordForStemming = (String)extendedQuery.elementAt(0);
				extendedQuery.removeElementAt(0);
				String sresult = findStem(wordForStemming);
				Vector exact = store.getWordsExact(sresult);
				extendedQuery = Utils.addToVector(exact, extendedQuery);
				Vector stem = store.getWordsForStem(sresult);
				extendedQuery = Utils.addToVector(stem, extendedQuery);
			}
		}

		if (isThesaurus){
			size = extendedQuery.size();
			Vector v = new Vector();			
			for (int k=0; k < size; k++){
				String w = (String)extendedQuery.elementAt(k);
				if (!v.contains(w)){
					v.addElement(w);
				}
			}
			size = v.size();
			for (int k=0; k < size; k++){
				extendedQuery = Utils.addToVector(FTLangDepFactory.getThesaurus("en").findSynonyms((String)v.elementAt(k), location),v);
			}
		}

		Vector allTokens = new Vector();

		for (int l= 0; l< extendedQuery.size(); l++){
			PhraseIterator iter = store.getLinguisticTokensExact((String)extendedQuery.elementAt(l));
			allTokens = addToVector(iter,allTokens);
		}	

		PhraseIterator lr = new PhraseIterator(allTokens, PhraseIterator.FLAT_PHRASE);
		return checkForCaseDiacritics(lr,null,originalWord);
	}


	// phrase search
	private PhraseIterator initPhraseSearch(Vector words, Vector originalWords) throws MXQueryException{
		// Vector of vectors (for each possible phrase there is a vector)
		Vector extendedQuery = new Vector();

		if (extendedQuery.size() == 0){
			extendedQuery.addElement(words);
		}			

		int size =  extendedQuery.size();
		
		// only wildcard
		if (isWildcard && !isStemming){
			size = extendedQuery.size();
			for (int l=0; l < size; l++){
				Vector phrase = (Vector)extendedQuery.elementAt(0);
				extendedQuery.removeElementAt(0);
				Vector phraseWithWildcard = store.getPhraseWithWildcards(phrase);
				for (int k=0; k < phraseWithWildcard.size(); k++){
					extendedQuery.addElement(phraseWithWildcard.elementAt(k));
				}
			}
		}
		
		if (isStemming){
			Vector temp = new Vector();
			// stemming and wildcard
			if (isWildcard){
				Vector vectors = new Vector();
				for (int j=0; j < extendedQuery.size(); j++){
					Vector phrase = (Vector) extendedQuery.elementAt(j);
					for (int k=0; k < phrase.size(); k++){
						String word = (String)phrase.elementAt(k);
						Vector wordsWithWildcards = store.getWordsForWildcards(word);
						Vector wordsWithStem = new Vector();
						wordsWithStem.addElement(word);
						for (int l=0; l < wordsWithWildcards.size(); l++){
							// find stem
							String wordWithWildcard = (String)wordsWithWildcards.elementAt(l);
							String sresult = findStem(wordWithWildcard);
							wordsWithStem.addElement(sresult);
						}
						wordsWithStem = DiacriticsUtils.getWordsWithoutDiacritics(wordsWithStem);
						vectors.addElement(wordsWithStem);							
					}
				}
				Vector combinations = Utils.getCombinations(new Vector(), new Vector(), vectors,0);
				extendedQuery = Utils.addToVector(combinations, extendedQuery);
			}
			else{
				for (int k=0; k < words.size(); k++){
					String word = (String)words.elementAt(k);
					String sresult = findStem(word);
					temp.addElement(sresult);
				}
				temp = DiacriticsUtils.getWordsWithoutDiacritics(temp);
				extendedQuery.addElement(temp);

				// also include the original version
				words = DiacriticsUtils.getWordsWithoutDiacritics(words);
				extendedQuery.addElement(words);
			}
			size = extendedQuery.size();
			for (int l= 0; l< size; l++){
				Vector phrase = (Vector)extendedQuery.elementAt(0);
				extendedQuery.removeElementAt(0);
				Vector phrases = store.getPhraseWithStemming(phrase);
				Vector newVector = new Vector();
				int psize = phrases.size();
				for (int k=0; k < psize; k++){
					if (phrases.elementAt(k) instanceof Vector){
						extendedQuery.addElement(phrases.elementAt(k));
					}
					else{
						newVector.addElement(phrases.elementAt(k));
					}
				}
				if (newVector.size() > 1){
					extendedQuery.addElement(newVector);
				}
			}
		}


		if (isThesaurus){

			size = extendedQuery.size();
			Vector vectors = new Vector();
			for (int l=0; l < words.size(); l++){
				vectors.addElement(new Vector());
			}
			for (int l=0; l < size; l++){
				Vector phrase = (Vector)extendedQuery.elementAt(l);
				for (int m=0; m < phrase.size(); m++){
					String pelement = (String)phrase.elementAt(m);
					if (isDiacritics){
						pelement = DiacriticsUtils.getWordWithoutDiacritics(pelement);
					}
					if (!((Vector)vectors.elementAt(m)).contains(pelement)){
						((Vector)vectors.elementAt(m)).addElement(pelement);
						Vector synonyms = FTLangDepFactory.getThesaurus("en").findSynonyms(pelement, location);
						for (int k=0; k < synonyms.size(); k++){
							String synonym = (String)synonyms.elementAt(k);
							if (!synonym.equals("")){
								if (!((Vector)vectors.elementAt(m)).contains(synonym)){
									((Vector)vectors.elementAt(m)).addElement(synonym);
								}
							}
						}
					}
				}

			}
			Vector combinations = Utils.getCombinations(new Vector(), new Vector(), vectors, 0);
			extendedQuery = Utils.addToVector(combinations, extendedQuery);
		}

		Vector allPhrases = new Vector();
		
		for (int l= 0; l< extendedQuery.size(); l++){
			PhraseIterator iter = store.getPhraseExact((Vector)extendedQuery.elementAt(l),ignoreId);
			allPhrases = addToVector(iter,allPhrases);
		}
		
		PhraseIterator pi = new PhraseIterator(allPhrases, PhraseIterator.NESTED_PHRASE);
		return checkForCaseDiacritics(pi, originalWords, "");
	}


	private void initAnyAllOption(Vector textParts, Vector origTextParts) throws MXQueryException{
		String text = "";
		
		Vector inputWords = new Vector();
		Vector originalInputWords = new Vector();
		
		switch (anyAllOption.getAnyAllValue()) {
		case AnyAllOption.ANY_ALL_OPT_PHRASE:
		case AnyAllOption.ANY_ALL_OPT_ANYWORD:
		case AnyAllOption.ANY_ALL_OPT_ALLWORDS:
			// each of them needs to input broken into a single set of individual words
			for (int i=0;i<textParts.size();i++) {
				Vector splitWords = splitIntoWords((String)textParts.elementAt(i));
				for (int j=0;j<splitWords.size();j++)
					inputWords.addElement(splitWords.elementAt(j));
				Vector origSplitWords = splitIntoWords((String)origTextParts.elementAt(i));
				for (int j=0;j<origSplitWords.size();j++)
					originalInputWords.addElement(origSplitWords.elementAt(j));

			}
		}
		Vector resMatches = new Vector();
		switch (anyAllOption.getAnyAllValue()) {
		case AnyAllOption.ANY_ALL_OPT_PHRASE:
			Vector smsp = ltPhraseToStringMatches(initPhraseSearch(inputWords,originalInputWords));
			for (int j=0;j<smsp.size();j++) {
				StringMatch sm = (StringMatch)smsp.elementAt(j);
				resMatches.addElement(new Match(new StringMatch[]{sm}));
			}
			break;
		case AnyAllOption.ANY_ALL_OPT_ANYWORD:
			for (int i=0; i < inputWords.size(); i++){
				text = (String)inputWords.elementAt(i);
				PhraseIterator iter = initKeywordSearch(DiacriticsUtils.getWordWithoutDiacritics(text),(String)originalInputWords.elementAt(i));
				Vector sms = ltPhraseToStringMatches(iter);
				for (int j=0;j<sms.size();j++) {
					StringMatch sm = (StringMatch)sms.elementAt(j);
					resMatches.addElement(new Match(new StringMatch[]{sm}));
				}
			}
		break;
		case AnyAllOption.ANY_ALL_OPT_ANY: 
			for (int i=0; i < textParts.size(); i++){
				String phrase = (String)textParts.elementAt(i);
				Vector words = DiacriticsUtils.getWordsWithoutDiacritics(splitIntoWords(phrase));
				Vector owords = splitIntoWords((String)origTextParts.elementAt(i));
				PhraseIterator iter = null;
				if (words.size() == 1) {
					iter = initKeywordSearch((String)words.elementAt(0),(String)owords.elementAt(0));
				} else  {
					iter = initPhraseSearch(words, owords);
				}
				Vector sms = ltPhraseToStringMatches(iter);
				for (int j=0;j<sms.size();j++) {
					StringMatch sm = (StringMatch)sms.elementAt(j);
					resMatches.addElement(new Match(new StringMatch[]{sm}));
				}
			}
			break;
		case AnyAllOption.ANY_ALL_OPT_ALL:
			Vector phrases = new Vector();
			for (int i=0; i < textParts.size(); i++){
				String phrase = (String)textParts.elementAt(i);
				Vector words = splitIntoWords(phrase);
				Vector originalWords = splitIntoWords((String)origTextParts.elementAt(i));
				PhraseIterator iter = initPhraseSearch(words, originalWords);
				if (iter.getNumberOfElements() == 0) {
					break;
				}
				// init phrases				
				if (phrases.size() == 0){
					while(iter.hasNext()){
						Phrase nextPhrase = (Phrase)iter.next();
						if (checkContextIgnore(nextPhrase.getFirstElement().getDeweyId())) {
							Vector mcand = new Vector();
							mcand.addElement(nextPhrase);
							level  = nextPhrase.getFirstElement().getDeweyId().getDeweyLevel();
							phrases.addElement(mcand);
						}
					}
				}
				else{
					int size = phrases.size();
					for (int j=0; j <size; j++){
						Vector mcand = (Vector)phrases.elementAt(0);
						while (iter.hasNext()){
							Phrase nextPhrase = (Phrase)iter.next();
							if (checkContextIgnore(nextPhrase.getFirstElement().getDeweyId())){
								Vector newMCand = new Vector();
								Utils.addToVector(mcand, newMCand);
								newMCand.addElement(nextPhrase);
								phrases.addElement(newMCand);
							}			
						}
						iter.reset();
						phrases.removeElementAt(0);
					}
				}	
			}
			numberOfElements += phrases.size();
			for (int k=0; k < phrases.size();k++){
				Vector mCand = (Vector)phrases.elementAt(k);
				if (mCand.size() == textParts.size()){
					StringMatch [] sm = new StringMatch[mCand.size()];
					for (int l=0;l<mCand.size();l++) {
					Vector toks = ((Phrase)mCand.elementAt(l)).getPhrase();
						sm[l] = new StringMatch(toks,queryPos);
						if (isScoring){
							computeScore(sm[l]);
						}
					}
					Match ma = new Match(sm);
					resMatches.addElement(ma);
				}
			}
			break;
		case AnyAllOption.ANY_ALL_OPT_ALLWORDS:
			Vector phrases1 = new Vector();
			for (int i=0; i < inputWords.size(); i++){
				String step = (String)inputWords.elementAt(i);
				PhraseIterator iter = initKeywordSearch(step, (String)originalInputWords.elementAt(i));
				if (iter.getNumberOfElements() == 0)
					break;
				if (phrases1.size() == 0) {
					while (iter.hasNext()){
						LinguisticToken lt = (LinguisticToken)iter.next();
						level  = lt.getDeweyId().getDeweyLevel();
						if (checkContextIgnore(lt.getDeweyId())) {
							phrases1.addElement(new Phrase(lt));
						}
					}
				}
				else{
					int size = phrases1.size();
					for (int j=0; j <size; j++){
						Phrase p = (Phrase)phrases1.elementAt(0);
						while (iter.hasNext()){
							LinguisticToken nextToken = (LinguisticToken)iter.next();
							if (checkContextIgnore(nextToken.getDeweyId())){
								Phrase newPhrase = new Phrase(p);
								newPhrase.addLightLinguisticToken(nextToken);
								phrases1.addElement(newPhrase);
							}
						}
						iter.reset();
						phrases1.removeElementAt(0);
					}
				}
			}
			// check whether each of the "phrases" has all tokens
			// build StringMatches+Matches
			for (int k=0; k < phrases1.size();k++){
				Phrase phrase = (Phrase)phrases1.elementAt(k);
				if (phrase.phraseSize() == inputWords.size()){
					numberOfElements += 1;
					StringMatch [] sm = new StringMatch[phrase.phraseSize()];
					Vector toks = phrase.getPhrase();
					for (int l=0;l<toks.size();l++) {
						sm[l] = new StringMatch(new LinguisticToken[]{(LinguisticToken)toks.elementAt(l)},queryPos);
						if (isScoring){
							computeScore(sm[l]);
						}
					}
					Match ma = new Match(sm);
					resMatches.addElement(ma);
				}
			}
			break;
		}
		if (resMatches.size() > 0) {
			AllMatch allm = new AllMatch(resMatches);
			allMatches.addElement(allm);
		}
	}
	/**
	 * Return the list of string matches corresponding to the output of pe
	 * Context scope and 
	 * @param pe PhraseIterator, either creating a sequence o
	 * @return
	 */
	private Vector ltPhraseToStringMatches(PhraseIterator pe) throws MXQueryException {
		Vector res = new Vector();
		
		
		while (pe.hasNext()){
			// phrase
			Phrase phrase = null;
			LinguisticToken linguisticToken;
			
			if (pe.getType() == PhraseIterator.NESTED_PHRASE){
				phrase = (Phrase) pe.next();
				linguisticToken = phrase.getFirstElement();
			}else {
				linguisticToken = (LinguisticToken) pe.next();					
			}
			DeweyIdentifier did = linguisticToken.getDeweyId();
			if (level == 0){
				level = did.getDeweyLevel();
			}
			numberOfElements += pe.getNumberOfElements();
			
			if (checkContextIgnore(did)) {
				StringMatch sm;
				if (phrase != null) {
					sm = new StringMatch(phrase.getPhrase(),queryPos);
				} else {
					sm = new StringMatch(new LinguisticToken[]{linguisticToken},queryPos);
				}
				if (isScoring){
					computeScore(sm);
				}

				res.addElement(sm);
			}
		}
		return res;
	}

	private void computeScore(StringMatch sm) {
		MXQueryNumber termFrequency = new MXQueryDouble(1).divide(numberOfElements);
		int idLevel = id.getDeweyLevel();
		MXQueryNumber deweyDocFrequency = new MXQueryDouble(idLevel).divide(level);
		MXQueryNumber score = termFrequency.multiply(deweyDocFrequency);
		sm.setScore(score.getDoubleValue());
	}	

	private boolean checkContextIgnore(DeweyIdentifier did) {
		boolean isIgnored = false;
		if (ignoreId != null){				
			for (int i=0;i<ignoreId.length;i++) {
				if (ignoreId[i].isAncestorOf(did))
					isIgnored = true;
			}
		}
		if (!isIgnored) {
			if (id.isAncestorOf(did)){
				return true;
			}
		}
		return false;
	}
	
	// add elements of iterator to vector
	private Vector addToVector(PhraseIterator iterator, Vector v) throws MXQueryException{
		while (iterator.hasNext()){
			FTToken next = iterator.next();
			if (!v.contains(next)){
				v.addElement(next);
			}
		}
		return v;
	}

	// returns stem of text
	private String findStem(String text){

		Stemmer stemmer = FTLangDepFactory.getStemmer("en");
		return stemmer.findStem(text);
	}



	private void processOptions(Vector options) throws MXQueryException {

		int i= 0;
		while (i < options.size()){

			switch (((MatchOption)(options.elementAt(i))).getMatchOptionType()) { 
			case MatchOption.MATCH_OPTION_TYPE_STOPWORD:
				if (((MatchOption)(options.elementAt(i))).isOptionValue())
					throw new StaticException(ErrorCodes.FTST006_FTStopWordOption_RESTRICTION_NOT_OBEYED,"FTStopWordOption not supported",null);
				break;
			case MatchOption.MATCH_OPTION_TYPE_LANGUAGE:
				FTLanguageMatchOption langOption = (FTLanguageMatchOption) options.elementAt(i);
				if (!context.getRootContext().getFTLanguage().equals(langOption.getLanguage())){
					throw new StaticException(ErrorCodes.FTST0013_FTLanguageOption_SINGLE_RESTRICTION_NOT_OBEYED,"Language in FTOptionDecl() and body of query must be the same",null);
				}
				break;
			case MatchOption.MATCH_OPTION_TYPE_WILDCARD:
				isWildcard = ((MatchOption)(options.elementAt(i))).isOptionValue();
				break;
			case MatchOption.MATCH_OPTION_TYPE_STEMMING:
				isStemming = ((MatchOption)(options.elementAt(i))).isOptionValue();
				break;
			case MatchOption.MATCH_OPTION_TYPE_THESAURUS:
				isThesaurus = ((MatchOption)(options.elementAt(i))).isOptionValue();
				if (isThesaurus){
					location = ((FTThesaurusMatchOption)options.elementAt(i)).getLocation();
					if (location.equals("default")){
						location = "http://localhost:8080/axis/WordNetService.jws?wsdl";
					}
				}
				break;
			case MatchOption.MATCH_OPTION_TYPE_DIACRITICS:
				isDiacritics = ((MatchOption)(options.elementAt(i))).isOptionValue();
				break;
			case MatchOption.MATCH_OPTION_TYPE_CASE:
				FTCaseMatchOption caseOption = (FTCaseMatchOption)options.elementAt(i);
				caseType = caseOption.getCaseType();
				break;
			case MatchOption.MATCH_OPTION_TYPE_EXTENSION:
				// ignore
				break;
			}
			i++;
		}
	}

	private static Vector splitIntoWords(String text){
		// TODO: replace with general tokenization function
		Vector words = new Vector();
		String[] delimiters = {" "};

		String[] tokens = Utils.split(text, delimiters);

		for (int i=0; i < tokens.length; i++){
			words.addElement(tokens[i].trim());
		}
		return words;
	}


	protected FTIteratorInterface copy(Context context, FTIteratorInterface [] subIters, Vector nestedPredCtxStack)throws MXQueryException {
		MatchIterator nm = new MatchIterator(context,null,null, this.anyAllOption, isScoring,queryPos);
		nm.isWildcard = this.isWildcard;
		nm.isStemming = this.isStemming;
		nm.isDiacritics = this.isDiacritics;
		nm.isThesaurus = this.isThesaurus;
		nm.isScoring = this.isScoring;
		nm.location = this.location;
		nm.caseType = this.caseType;
		nm.searchCondIter = searchCondIter.copy(context,this.context.getParent(),false, nestedPredCtxStack);
		nm.varIter = varIter.copy(context,this.context.getParent(),false, nestedPredCtxStack);
		return nm;
	}


	public AllMatch next() throws MXQueryException{
		if (this.called == 0) {
			this.init();

		}

		if (allMatches.size() == 0){
			return AllMatch.END_ALL_MATCH_SEQUENCE;
		}

		if (called < allMatches.size()){
			AllMatch am = (AllMatch) allMatches.elementAt(called);
			called++;
			return am;
		}
		return AllMatch.END_ALL_MATCH_SEQUENCE;

	}

	public void reset() throws MXQueryException {

		super.reset();
		if (varIter != null){
			varIter.reset();	
		}
		if (searchCondIter != null){
			searchCondIter.reset();
		}
		id = null;
		level = 0;
		allMatches = new Vector();
		if (ignoreOption != null) {
			ignoreOption.reset();
		}
		store = null;
	}


	public void setContext(Context ctx) throws MXQueryException {
		context = ctx;
		if (ctx != null && varIter != null){
			varIter.setContext(ctx, true);
		}
	}

	public void setIgnoreOption(XDMIterator ignoreOption) throws MXQueryException {
		this.ignoreOption = WindowFactory.getNewWindow(context, ignoreOption);
		this.ignoreOption.setResettable(true);
	}
	public void setResettable(boolean r) throws MXQueryException {
		if (varIter != null)
			varIter.setResettable(r);
		if (searchCondIter != null)
			searchCondIter.setResettable(r);
		if (ignoreOption != null)
			ignoreOption.setResettable(r);
	}
}

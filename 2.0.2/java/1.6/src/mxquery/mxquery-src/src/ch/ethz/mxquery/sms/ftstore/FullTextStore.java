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

import ch.ethz.mxquery.datamodel.DeweyIdentifier;
import ch.ethz.mxquery.datamodel.Source;
import ch.ethz.mxquery.datamodel.adm.LinguisticToken;
import ch.ethz.mxquery.exceptions.MXQueryException;

/**
 * Interface for a FullTextStore
 * @author jimhof
 *
 */
public interface FullTextStore extends Source {
	
	/**
	 * 
	 * @param did
	 * @return iterator over the Linguistic Tokens sequentially following the linguistic token with dewey id "did".
	 */
	public LinguisticToken getLinguisticTokens(DeweyIdentifier did);

	/**
	 * 
	 * @param word
	 * @return iterator over the corresponding Linguistic Token of word "word".
	 */
	public PhraseIterator getLinguisticTokensExact(String word);
	
	
	/**
	 * 
	 * @param prefix
	 * @return iterator over the corresponding Linguistic Token of words that have "prefix" as prefix.
	 */
	public PhraseIterator getLinguisticTokensWithSuffix(String prefix, String wildcard);
	
	/**
	 * 
	 * @param suffix
	 * @return iterator over the corresponding Linguistic Token of words that have "suffix" as suffix.
	 */
	public PhraseIterator getLinguisticTokensWithPrefix(String suffix, String wildcard);
	/**
	 * 
	 * @param prefix
	 * @param suffix
	 * @return iterator over the corresponding Linguistic Token of words that have "prefix" as prefix and "suffix" as suffix
	 */
	public PhraseIterator getLinguisticTokensWithInfix(String prefix, String suffix, String wildcard);
	
	// methods returning words
	
	/**
	 * 
	 * @param word
	 * @return set of words that fulfill "word" with its wildcards.
	 */
	public Vector getWordsForMultipleWildCard(String word); 
	
	/**
	 * 
	 * @param stem
	 * @return iterator over the Linguistic Tokens that have stem "stem".
	 */
	
	/**
	 * 
	 * @param prefix
	 * @return set of words that have "prefix" as prefix.
	 */
	public Vector getWordsWithSuffix(String prefix, String wildcard);
	
	/**
	 * 
	 * @param suffix
	 * @return set of words that have "suffix" as suffix.
	 */
	public Vector getWordsWithPrefix(String suffix, String wildcard);
	/**
	 * 
	 * @param prefix
	 * @param suffix
	 * @return set of words that have "prefix" as prefix and "suffix" as suffix
	 */
	public Vector getWordsWithInfix(String prefix, String suffix, String wildcard);
	
	/**
	 * 
	 * @param stem
	 * @return words that have stem "stem".
	 */
	public Vector getWordsForStem(String stem);
	
	
	// methods returning phrase iterators
	
	/**
	 * 
	 * @param phrase
	 * @return iterator over the sets of Linguistic Tokens that fulfill the phrase predicate "phrase".
	 * @throws MXQueryException 
	 */
	public PhraseIterator getPhraseExact(Vector phrase, DeweyIdentifier [] ignoreId) throws MXQueryException;
	
	/**
	 * 
	 * @param phrase
	 * @return iterator over the sets of Linguistic Tokens that fulfill the phrase predicate "phrase" containing wildcards.
	 * @throws MXQueryException 
	 */
	public PhraseIterator getPhraseWithWildcard(String phrase, DeweyIdentifier [] ignoreId) throws MXQueryException;
	
	
	/**
	 * 
	 * @param word
	 * @return the precomputed idf for document collections
	 */
	public double getInverseDocumentFrequency(String word);
}
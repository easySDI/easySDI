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

package ch.ethz.mxquery.model.ft;

/**
 * MatchOption: implementation of MatchOption
 * @author jimhof
 */
public class MatchOption {
	
	public final static int MATCH_OPTION_TYPE_WILDCARD = 1;
	public final static int MATCH_OPTION_TYPE_DIACRITICS = 2;
	public final static int MATCH_OPTION_TYPE_STEMMING = 3;
	public final static int MATCH_OPTION_TYPE_THESAURUS = 4;
	public final static int MATCH_OPTION_TYPE_LANGUAGE = 5;
	public final static int MATCH_OPTION_TYPE_EXTENSION = 6;
	public final static int MATCH_OPTION_TYPE_CASE = 7;
	public final static int MATCH_OPTION_TYPE_STOPWORD = 8;
	public final static int MATCH_OPTION_TYPE_ANYALL = 9;
	
	protected int matchOptionType = 0;
	
	private boolean optionValue;
	
	public MatchOption(final int type, final boolean value) {
		matchOptionType = type;
		optionValue = value;
	}

	public int getMatchOptionType() {
		return matchOptionType;
	}

	public boolean isOptionValue() {
		return optionValue;
	}

	
	public MatchOption copy() {
		return new MatchOption(matchOptionType,optionValue);
	}

}

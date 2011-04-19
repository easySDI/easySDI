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
 * Case Match Option: upper case, lower case, case sensitive, case insensitive
 * @author jimhof
 *
 */

public class FTCaseMatchOption extends MatchOption {

	public static final int CASE_SENSITIVE = 1;
	public static final int CASE_LOWERCASE = 2;
	public static final int CASE_UPPERCASE = 3;
	public static final int CASE_INSENSITIVE = 0; 
	
	int caseType = CASE_INSENSITIVE;
	
	public FTCaseMatchOption(boolean handleCase, int caseType) {
		super(MatchOption.MATCH_OPTION_TYPE_CASE, handleCase);
		this.caseType = caseType;
	}

	public int getCaseType() {
		return caseType;
	}

}

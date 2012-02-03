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
 * AllMatch: item that contains the Matches of a full-text query¨
 * @author jimhof
 */

public class AllMatch {
	
	public final static AllMatch END_ALL_MATCH_SEQUENCE = new AllMatch();
	
	private Match [] matches;
	// if the AllMatch contains Matchs that make up a phrase
	
	private AllMatch() {
		this((Match[])null);
	}
	
	public AllMatch(Match [] m){
		matches = m;
	}
	
	public AllMatch(Vector matches) {
		Match [] mat = new Match[matches.size()];
		for (int i= 0; i < matches.size(); i++){
			mat[i] = (Match)matches.elementAt(i);
		}
		this.matches = mat;	}

	public Match[] getMatches(){
		return matches;
	}
	
	/**
	 * Compute the score as the maximum score of the Matches
	 * @return The score for this AllMatch 
	 */
	public MXQueryDouble getScore(){
		MXQueryDouble sc = new MXQueryDouble(0);
		for (int i=0;i<matches.length;i++) {
			MXQueryDouble itemScore = matches[i].getScore(); 
			if (itemScore.compareTo(sc) == 1)
				sc = itemScore; 
		}
		return sc;
	}
}

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

import ch.ethz.mxquery.datamodel.MXQueryDouble;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.util.QuickSort;

/**
 * Match: Representation of Match in Fulltext Data Model
 * Roughly speaking: A conjunction of StringMatches (for now just includes, later also excludes)
 * @author jimhof
 */
public class Match {
	
	private StringMatch [] includes;
	
	public Match(StringMatch [] inc){
		includes = inc;
	}
	
	public StringMatch[] getIncludes() {
		return includes;
	}

	public void sortStringMatches() throws MXQueryException {
		QuickSort.sort(includes, new StringMatchCompare());
	}
	
	public int getMinIncludePosition() {
		int min = includes[0].getStartPos();
		for (int i=1;i<includes.length;i++) {
			int cur = includes[i].getStartPos();
			if ( cur < min)
				min = cur;
		}
		return min;
	}

	public int getMaxIncludePosition() {
		int max = includes[0].getEndPos();
		for (int i=1;i<includes.length;i++) {
			int cur = includes[i].getEndPos();
			if ( cur > max)
				max = cur;
		}
		return max;
	}

	public int getMinIncludeSentence() {
		int min = includes[0].getStartSentence();
		for (int i=1;i<includes.length;i++) {
			int cur = includes[i].getStartSentence();
			if ( cur < min)
				min = cur;
		}
		return min;
	}

	public int getMaxIncludeSentence() {
		int max = includes[0].getEndSentence();
		for (int i=1;i<includes.length;i++) {
			int cur = includes[i].getEndSentence();
			if ( cur > max)
				max = cur;
		}
		return max;
	}

	public int getMinIncludeParagraph() {
		int min = includes[0].getStartParagraph();
		for (int i=1;i<includes.length;i++) {
			int cur = includes[i].getStartParagraph();
			if ( cur < min)
				min = cur;
		}
		return min;
	}

	public int getMaxIncludeParagraph() {
		int max = includes[0].getEndParagraph();
		for (int i=1;i<includes.length;i++) {
			int cur = includes[i].getEndParagraph();
			if ( cur > max)
				max = cur;
		}
		return max;
	}	
	/**
	 * Compute the score as the minimum score of the StringMatches
	 * @return The score of this Match 
	 */
	public MXQueryDouble getScore(){
		MXQueryDouble sc = new MXQueryDouble(1);
		for (int i=0;i<includes.length;i++) {
			MXQueryDouble itemScore = includes[i].getScore(); 
			if (itemScore.compareTo(sc) == -1)
				sc = itemScore; 
		}
		return sc;
	}
}

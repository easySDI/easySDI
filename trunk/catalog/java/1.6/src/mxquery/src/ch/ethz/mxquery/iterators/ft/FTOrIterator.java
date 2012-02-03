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

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.adm.AllMatch;
import ch.ethz.mxquery.datamodel.adm.Match;
import ch.ethz.mxquery.exceptions.MXQueryException;
/**
 * Implementation of an FTOr
 * @author jimhof
 */
public class FTOrIterator extends FTBaseIterator {


	private Vector allMatches = null;
	
	public FTOrIterator(Context context, FTIteratorInterface [] subIters){
		super(context,subIters);
	}
	
	public FTOrIterator(Context context, Vector subIters){
		super(context,subIters);
	}
	
	protected FTIteratorInterface copy(Context context, FTIteratorInterface [] subIters, Vector nestedPredCtxStack)throws MXQueryException {
		return new FTOrIterator(context,subIters);
	}
	
	public void init() throws MXQueryException{
		allMatches = new Vector();
		Vector observedMatches = new Vector();
		for (int i=0;i<subIters.length;i++) {
			FTIteratorInterface cur = subIters[i];
			AllMatch next = cur.next();
			while (next != AllMatch.END_ALL_MATCH_SEQUENCE) {
				Match [] curMatches = next.getMatches();
				for (int j=0;j<curMatches.length;j++) {
					observedMatches.addElement(curMatches[j]);
				}
				next = cur.next();
			}
		}
		if (observedMatches.size()>0) {
			Match [] newMatch = new Match[observedMatches.size()];
			for (int i=0;i<newMatch.length;i++) {
				newMatch[i] = (Match)observedMatches.elementAt(i);
			}
			allMatches.addElement(new AllMatch(newMatch));
		}		
	}
	
	public AllMatch next() throws MXQueryException {
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
		allMatches = new Vector();
	}
}

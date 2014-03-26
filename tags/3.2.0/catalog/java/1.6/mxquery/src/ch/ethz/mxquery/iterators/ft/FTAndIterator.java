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
import ch.ethz.mxquery.datamodel.adm.StringMatch;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.util.Utils;

/**
 * Implementation of an FTAnd
 * @author jimhof
 */
public class FTAndIterator extends FTBaseIterator{

	private Vector allMatches = new Vector();
	private boolean isNotAnd = false;

	public FTAndIterator(Context context, FTIteratorInterface [] subIters, boolean ftnotFlag){
		super(context,subIters);
		this.isNotAnd = ftnotFlag;
	}

	public FTAndIterator(Context context, Vector subIters, boolean ftnotFlag){
		super(context,subIters);
		this.isNotAnd = ftnotFlag;
	}

	
	protected FTIteratorInterface copy(Context context, FTIteratorInterface [] subIters, Vector nestedPredCtxStack)throws MXQueryException {
		return new FTAndIterator(context,subIters,this.isNotAnd);
	}

	public void init() throws MXQueryException{


		if (isNotAnd){
			// first iterator
			FTIteratorInterface first = subIters[0];
			AllMatch fam = first.next();
			while (fam != AllMatch.END_ALL_MATCH_SEQUENCE){
				Vector temp = new Vector();
				temp.addElement(fam);
				FTIteratorInterface next = subIters[1];
				AllMatch nam = next.next();

				// no result: ftnot -> there is a result
				if (nam.equals(AllMatch.END_ALL_MATCH_SEQUENCE)){
					Utils.addToVector(temp, allMatches);
				}
				fam = AllMatch.END_ALL_MATCH_SEQUENCE;

			}
		}
		else{

			Vector allIters = new Vector();
			for (int i=0; i < subIters.length; i++){
				Vector v = new Vector();
				FTIteratorInterface iter = subIters[i];
				AllMatch am = iter.next();
				while (am != AllMatch.END_ALL_MATCH_SEQUENCE){
					Match [] m = am.getMatches();
					for (int j=0;j<m.length;j++) {
						v.addElement(m[j]);
					}
					am = iter.next();
				}
				if (v.size() > 0){
					allIters.addElement(v);
				}

			}
			if (allIters.size() == subIters.length){
				Vector combinations = new Vector();
				combinations = Utils.getCombinations(combinations, new Vector(), allIters, 0);

				Match [] comb= new Match[combinations.size()];
				for (int i=0; i < combinations.size(); i++){
					Vector combination = (Vector)combinations.elementAt(i);
					Vector sms = new Vector();

					for (int j=0; j < combination.size(); j++){
						Match cm = (Match)combination.elementAt(j);
						StringMatch [] curSm = cm.getIncludes();
						for (int k =0;k<curSm.length;k++) {
							sms.addElement(curSm[k]);
						}
						//TODO: also handle excludes
					}
					StringMatch [] sm = new StringMatch[sms.size()];
					for (int j=0;j< sms.size();j++) {
						sm[j] = (StringMatch)sms.elementAt(j);
					}
					comb[i] = new Match(sm);	
				}
				allMatches.addElement(new AllMatch(comb));
			}

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

	public void setNotFlag(boolean isNotFlag){
		this.isNotAnd = isNotFlag;
	}

}

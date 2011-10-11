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

import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.adm.AllMatch;
import ch.ethz.mxquery.datamodel.adm.Match;
import ch.ethz.mxquery.datamodel.adm.StringMatch;
import ch.ethz.mxquery.exceptions.MXQueryException;


/**
 * FTOrder: whether Linguistic Tokens are in ascending order
 * @author jimhof
 *
 */

public class FTOrder extends FTPositional{

	private String ordered;
	
	public FTOrder(String ordered) {
		super(ordered);
		this.ordered = ordered;
	}

	public AllMatch checkPosConstraint(AllMatch next) {

		Match [] allMatches = next.getMatches();
		
		
		Vector resMatches = new Vector();
		for (int i=0;i<allMatches.length;i++) {
			// in the current execution strategy, string matches are added in query order, 
			// so we do not need to check the query pos explicitly
			StringMatch [] mat = allMatches[i].getIncludes();
			boolean ordered = true;
			for (int j=1;j<mat.length;j++) {
				if (mat[j].getStartPos() < mat[j-1].getStartPos()) {
					ordered = false;
					break;
				}
			}
			if (ordered)
				resMatches.addElement(allMatches[i]);
				
		}

		if (resMatches.size() > 0) {
			Match [] resM = new Match[resMatches.size()];
			for (int i=0;i<resM.length;i++)
				resM[i] = (Match)resMatches.elementAt(i);
			return new AllMatch(resM);
		}
		return null;
	}
	
	public String getOrdered(){
		return this.ordered;
	}

	public void reset() throws MXQueryException {
		// TODO Auto-generated method stub
		
	}

	public void setContext(Context ctx) throws MXQueryException {
		// TODO Auto-generated method stub
		
	}

	public void setResettable(boolean r) throws MXQueryException {
		// TODO Auto-generated method stub
		
	}

	public FTPositional copy(Context ctx, Vector nestedPredCtxStack)
			throws MXQueryException {
		return new FTOrder(ordered);
	}

}

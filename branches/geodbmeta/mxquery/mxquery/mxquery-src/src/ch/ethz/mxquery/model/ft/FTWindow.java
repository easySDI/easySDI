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
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.XDMIterator;

/**
 * FTWindow (Positional Filter): window i words, paragraph, sentences 
 * @author jimhof
 *
 */

public class FTWindow extends FTPositional {

	private XDMIterator additiveExpr;
	private Token distVal = null;
	
	public FTWindow(XDMIterator additiveExpr, String unit) {
		super(unit);
		this.additiveExpr = additiveExpr;
	}

	public AllMatch checkPosConstraint(AllMatch am) throws MXQueryException {
		if (distVal == null) {
		distVal = additiveExpr.next();
		if (!Type.isTypeOrSubTypeOf(distVal.getEventType(), Type.INTEGER, null)) 
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,"Integer Type expected",null);
		}
		long value = distVal.getLong();
		
		Match [] allMatches = am.getMatches();
		
		
		Vector resMatches = new Vector();
		for (int i=0;i<allMatches.length;i++) {
			if (performCheck(value, 0, DIST_RANGE_WINDOW,
					allMatches[i])){
				resMatches.addElement(allMatches[i]);
			}	
		}

		if (resMatches.size() > 0) {
			Match [] resM = new Match[resMatches.size()];
			for (int i=0;i<resM.length;i++)
				resM[i] = (Match)resMatches.elementAt(i);
			return new AllMatch(resM);
		}
		return null;
	}
	
	public void reset() throws MXQueryException {
		additiveExpr.reset();
		distVal = null;
	}
	
	public void setResettable(boolean r) throws MXQueryException {
		additiveExpr.setResettable(r);
	}

	public void setContext(Context ctx) throws MXQueryException {
		additiveExpr.setContext(ctx, true);
	}

	public FTPositional copy(Context ctx, Vector nestedPredCtxStack) throws MXQueryException{
		XDMIterator newAddIterator = additiveExpr.copy(ctx, null, false, nestedPredCtxStack);
		return new FTWindow(newAddIterator,ftUnit);
		
	}	
}

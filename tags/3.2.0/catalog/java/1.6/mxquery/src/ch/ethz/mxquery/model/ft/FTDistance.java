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
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.XDMIterator;

/**
 * FTDistance (Positional Filter): distance at least, at most, exactly i, from i to j words, sentences, paragraphs 
 * @author jimhof
 *
 */

public class FTDistance extends FTPositional {

	private Range range;
	private Token distVal = null;
	private Token distVal1 = null;
	
	public FTDistance(Range range, String ftUnit) {
		super(ftUnit);
		this.range = range;
	}
	
	
	public AllMatch checkPosConstraint(AllMatch am) throws MXQueryException{
		String srange = range.getRange();
		XDMIterator addExpr = null;
		XDMIterator addFrom = null;
		XDMIterator addTo = null;
		long value = 0;
		long from = 0;
		long to = 0;
		
		if (distVal == null) {
			if (srange.equals("from")){
				addFrom = range.getFromAddExpr();
				addTo = range.getToAddExpr();
				distVal1 = addTo.next();
				distVal = addFrom.next();
				if (!Type.isTypeOrSubTypeOf(distVal.getEventType(), Type.INTEGER, null)||!Type.isTypeOrSubTypeOf(distVal1.getEventType(), Type.INTEGER, null)) 
					throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,"Integer Type expected",null);

			}
			else{
				addExpr = range.getAddExpr();
				distVal = addExpr.next();
				if (!Type.isTypeOrSubTypeOf(distVal.getEventType(), Type.INTEGER, null)) 
					throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,"Integer Type expected",null);
			}
			
		}
		
		if (srange.equals("from")){
			from = distVal.getLong();
			to = distVal1.getLong();
		} else{
			value = distVal.getLong();
		}
		
		if (srange.equals("exactly")){
			return checkDist(am,value, 0, DIST_RANGE_EXACTLY);
		}
		else if (srange.equals("least")){
			return checkDist(am,value, 0, DIST_RANGE_LEAST);
		}
		else if (srange.equals("most")){
			return checkDist(am,value, 0, DIST_RANGE_MOST);
		}
		else if (srange.equals("from")){
			return checkDist(am, from, to, DIST_RANGE_FROM_TO);	
		}
		return null;
		
	}
			
	private AllMatch checkDist(AllMatch am, long value, long value2, int type) throws MXQueryException {
		Match [] allMatches = am.getMatches();
		
		
		Vector resMatches = new Vector();
		for (int i=0;i<allMatches.length;i++) {
			if (performCheck(value, value2, type,
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
	
	protected boolean performCheck(long value, long value2, int type,
			Match mat) throws MXQueryException{
		boolean isValidResult = true;
		mat.sortStringMatches();
		StringMatch[] sm = mat.getIncludes();
		for (int i=1;i<sm.length;i++) {
			int difference = 0;
			if (ftUnit.equals("words")){
				difference = getAbsValue(sm[i].getStartPos()-sm[i-1].getEndPos())-1;
			}
			else if (ftUnit.equals("sentences")){
				difference = getAbsValue(sm[i].getStartSentence()-sm[i-1].getEndSentence())-1;
			}
			else if (ftUnit.equals("paragraphs")){
				difference = getAbsValue(sm[i].getStartParagraph()-sm[i-1].getEndParagraph())-1;
			}
			switch (type) {
			case DIST_RANGE_EXACTLY:
				if (value != difference){
					isValidResult = false;
				}
				break;
			case DIST_RANGE_LEAST:
				if (difference < value){
					isValidResult = false;
				}
				break;
			case DIST_RANGE_MOST:
				if (difference > value){
					isValidResult = false;
				}
				break;										
			case DIST_RANGE_FROM_TO:
				if (difference > value2 ||  difference < value){
					isValidResult = false;
				}
				break;											
			}
		}
		return isValidResult;
	}
	
	public void reset() throws MXQueryException {
		range.reset();
		distVal = null;
		distVal1 = null;
	}
	
	public void setResettable(boolean r) throws MXQueryException {
		range.setResettable(r);
	}

	public void setContext(Context ctx) throws MXQueryException {
		range.setContext(ctx);
	}


	public FTPositional copy(Context ctx, Vector nestedPredCtxStack)
			throws MXQueryException {
		return new FTDistance(range.copy(ctx, nestedPredCtxStack),ftUnit);
	}	
}

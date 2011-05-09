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
import ch.ethz.mxquery.exceptions.MXQueryException;

public abstract class FTPositional{
	
	/**
	 * Helper class for Positional Filters
	 * @author jimhof
	 *
	 */
	
	protected static final int DIST_RANGE_EXACTLY = 0;
	protected static final int DIST_RANGE_MOST = 1;
	protected static final int DIST_RANGE_LEAST = 2;
	protected static final int DIST_RANGE_FROM_TO = 3;
	protected static final int DIST_RANGE_WINDOW = 4;

	public static final int POS_SCOPE_SAME =5;  
	public static final int POS_SCOPE_DIFFERENT =6;
	
	public static final int POS_CONTENT_START =7;  
	public static final int POS_CONTENT_END =8;
	public static final int POS_CONTENT_ALL =9;
	
	protected String ftUnit;
	
	public FTPositional(String unit) {
		ftUnit = unit;
	}

	public abstract AllMatch checkPosConstraint(AllMatch am) throws MXQueryException;
	
	protected int getAbsValue(int i){
		if (i < 0){
			return (i * -1);
		}
		else{
			return i;
		}
	}
	
	protected boolean performCheck(long value, long value2, int type,
			Match mat) throws MXQueryException {
		boolean isValidResult = false;
		int difference = 0;
		if (ftUnit.equals("words")){
			difference = getAbsValue(mat.getMaxIncludePosition()-mat.getMinIncludePosition())-1;
		}
		else if (ftUnit.equals("sentences")){
			difference = getAbsValue(mat.getMaxIncludeSentence()-mat.getMinIncludeSentence())-1;
		}
		else if (ftUnit.equals("paragraphs")){
			difference = getAbsValue(mat.getMaxIncludeParagraph()-mat.getMinIncludeParagraph())-1;
		}
		switch (type) {
		case DIST_RANGE_EXACTLY:
			if (value == difference){
				isValidResult = true;
			}
			break;
		case DIST_RANGE_LEAST:
			if (difference >= value){
				isValidResult = true;
			}
			break;
		case DIST_RANGE_MOST:
		case DIST_RANGE_WINDOW:
			if (difference <= value){
				isValidResult = true;
			}
			break;										
		case DIST_RANGE_FROM_TO:
			if (difference <= value2 &&  difference >= value){
				isValidResult = true;
			}
			break;											
		}
		return isValidResult;
	}
	
	public abstract void reset() throws MXQueryException;
	
	public abstract void setResettable(boolean r) throws MXQueryException;

	public abstract void setContext(Context ctx) throws MXQueryException;
	
	public abstract FTPositional copy (Context ctx, Vector nestedPredCtxStack) throws MXQueryException;
}

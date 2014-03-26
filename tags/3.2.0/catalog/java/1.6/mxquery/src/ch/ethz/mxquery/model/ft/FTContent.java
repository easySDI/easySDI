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
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.exceptions.StaticException;

public class FTContent extends FTPositional {

	int contentType = 0;
	QueryLocation loc;
	
	public FTContent(int contentType, QueryLocation loc) throws StaticException {
		super("");
		this.contentType = contentType;
		this.loc = loc;
		throw new StaticException(ErrorCodes.FTST0012_FTContentOperator_NOT_SUPPORTED,
						"at start not supported",loc);
	}
	
	public AllMatch checkPosConstraint(AllMatch am) throws MXQueryException {
		// TODO Implement
		return AllMatch.END_ALL_MATCH_SEQUENCE;
	}

	public FTPositional copy(Context ctx, Vector nestedPredCtxStack)
			throws MXQueryException {
		return new FTContent(contentType,loc);
	}

	public void reset() throws MXQueryException {
	}

	public void setContext(Context ctx) throws MXQueryException {
	}

	public void setResettable(boolean r) throws MXQueryException {
	}

}

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

package ch.ethz.mxquery.functions.fn;

import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.LongToken;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;

/**
 * 
 * @author Matthias Braun
 *
 */
public class Last extends TokenBasedIterator {
	protected void init() throws MXQueryException {
		// Just retrieve the last value that has been computed in the related FFLWOR predicate expression
		if (subIters.length < 1)
			throw new DynamicException(ErrorCodes.E0002_DYNAMIC_NO_VALUE_ASSIGNED,"Context for last() not set", loc);
		currentToken = new LongToken(Type.INTEGER, null, subIters[0].next().getLong());
	}
	public TypeInfo getStaticType() {
		return new TypeInfo(Type.INTEGER,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
	}
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new Last();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;	
	}
}

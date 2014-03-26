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

/**
 *  author RokasT.
 */

package ch.ethz.mxquery.iterators;

import java.util.Vector;

import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;


public class InstanceOfIterator extends TokenBasedIterator {

	public InstanceOfIterator(Context ctx, XDMIterator seqTypeIt, QueryLocation location) throws MXQueryException {
		super(ctx,  new XDMIterator[] {seqTypeIt}, location);
		if ( seqTypeIt == null)
			throw new MXQueryException(ErrorCodes.A0009_EC_EVALUATION_NOT_POSSIBLE,"Parameter iterator is null!",loc);
	}
	
	protected void init () throws MXQueryException {
		
		// currently parser for 'instance of' uses non streaming approach
		currentToken = this.subIters[0].next();
	}
	
	public TypeInfo getStaticType() {
		return new TypeInfo(Type.BOOLEAN,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
	}			
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		return new InstanceOfIterator(context, subIters[0], loc);
	}
}

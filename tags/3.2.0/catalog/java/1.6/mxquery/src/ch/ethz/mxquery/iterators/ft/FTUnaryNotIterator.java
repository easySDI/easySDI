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
import ch.ethz.mxquery.exceptions.MXQueryException;

/**
 * Implementation of an FTUnaryNot
 * @author jimhof
 */
public class FTUnaryNotIterator extends FTBaseIterator {
	
	public FTUnaryNotIterator(Context ctx, FTIteratorInterface [] subIters) {
		super(ctx, subIters);
		
	}

	public AllMatch next() throws MXQueryException {
		return subIters[0].next();
	}
	
	protected FTIteratorInterface copy(Context context, FTIteratorInterface [] subIters, Vector nestedPredCtxStack)throws MXQueryException {
		return new FTUnaryNotIterator(context,subIters);
	}
	
}

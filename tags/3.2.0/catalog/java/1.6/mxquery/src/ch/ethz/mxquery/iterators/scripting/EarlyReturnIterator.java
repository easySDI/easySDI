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
package ch.ethz.mxquery.iterators.scripting;

import java.util.Vector;

import ch.ethz.mxquery.bindings.WindowFactory;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.model.CFException;
import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;

public class EarlyReturnIterator extends CurrentBasedIterator {

	public EarlyReturnIterator(Context ctx, XDMIterator returnExpr, QueryLocation location) {
		super(ctx, location);
		exprCategory = XDMIterator.EXPR_CATEGORY_SEQUENTIAL;
		this.subIters = new XDMIterator[]{returnExpr};
	}
		
	public Token next() throws MXQueryException {
		throw new CFException(CFException.CF_EARLY_RETURN, WindowFactory.getNewWindow_Eager(this.context, this.subIters[0]));
	}
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		return new EarlyReturnIterator(context, subIters[0],loc);
	}
}

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

package ch.ethz.mxquery.query.parser;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.iterators.FFLWORIterator;
import ch.ethz.mxquery.iterators.OrderByIterator;
import ch.ethz.mxquery.iterators.PFFLWORIterator;
import ch.ethz.mxquery.iterators.forseq.ForseqIterator;
import ch.ethz.mxquery.iterators.forseq.ForseqWindowEarlyBindingParallel;
import ch.ethz.mxquery.model.Iterator;
import ch.ethz.mxquery.model.WindowVariable;
import ch.ethz.mxquery.model.XDMIterator;


/**
 * Extended version of the parser that enables features and optimizations that are only available on J2SE 1.5 and higher
 * @author Peter M. Fischer
 *
 */


public class J56Parser extends SEParser {
	protected Iterator generateFLWOR(XDMIterator where, OrderByIterator orderBy, XDMIterator ret, Context outerFFLWORScope, boolean containsForseq, GroupHelper gh, XDMIterator[] iters) throws MXQueryException {
		Iterator flwor;
		if (parallelExecution && fflworIndex == 0 && containsForseq && ret.getExpressionCategoryType(co.isScripting()) == XDMIterator.EXPR_CATEGORY_SIMPLE) {
			flwor = new PFFLWORIterator(outerFFLWORScope, iters, where, orderBy, ret, getCurrentLoc());
		} else {
			flwor = new FFLWORIterator(outerFFLWORScope, iters, where, orderBy, ret, getCurrentLoc());
		}

//		if (gh != null) {
//			for(int i=0;i < gh.bySources.length;i++){
//				gh.bySources[i] = DataValuesIterator.getDataIterator(gh.bySources[i], getCurrentContext());
//			}
//			flwor = new GroupByIndexIterator(getCurrentContext(), (FFLWORIterator) flwor, gh.sourceQName, gh.targetQName, gh.bySources, gh.byTargets, gh.lets, gh.where, getCurrentLoc());
//		}
		return flwor;
	}

	protected ForseqIterator generateForseqIterator(
			ch.ethz.mxquery.datamodel.QName varQName, TypeInfo type,
			XDMIterator seq, int windowType, Context outerContext,
			WindowVariable[] startVars, XDMIterator startExpr, boolean forceEnd,
			WindowVariable[] endVars, boolean onNewStart, XDMIterator endExpr)
			throws MXQueryException {
		
		ForseqIterator res;
		
		if (windowType == ForseqIterator.SLIDING_WINDOW && parallelExecution) {
			res = new ForseqWindowEarlyBindingParallel(outerContext, windowType, varQName, type, seq, startVars, startExpr, endVars, endExpr, forceEnd, onNewStart, ForseqIterator.ORDER_MODE_END, getCurrentLoc());
		} else {
			res = super.generateForseqIterator(varQName, type, seq, windowType,
				outerContext, startVars, startExpr, forceEnd, endVars, onNewStart,
				endExpr); 
		}
		if (parallelExecution)
			res.setParallelAccess(true);
		return res;
	}
}

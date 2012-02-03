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

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.EmptySequenceIterator;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.model.XDMIterator;

/**
 * SEQUENCE OF EXPRESSIONS (that are evaluated sequential)
 * 
 * @author David Alexander Graf
 * 
 */
public class ApplyExprIterator extends CurrentBasedIterator {

	
	/**
	 * Constructor
	 * 
	 * @param items
	 *            elements of the sequential execution
	 */
	public ApplyExprIterator(Context ctx, XDMIterator[] items, QueryLocation location) {
		super(ctx, location);
		exprCategory = XDMIterator.EXPR_CATEGORY_SEQUENTIAL;
		this.subIters = items;
	}

	private void init() throws MXQueryException {
		for (int i = 0; i < this.subIters.length-1; i++) {
			while (subIters[i].next() != Token.END_SEQUENCE_TOKEN) {
			}
			if (this.subIters[i].getExpressionCategoryType(true) == XDMIterator.EXPR_CATEGORY_UPDATING) {
				this.subIters[i].getPendingUpdateList().apply();
			}
		}
		XDMIterator lastIt = this.subIters[this.subIters.length - 1];
		if (lastIt.getExpressionCategoryType(true) == XDMIterator.EXPR_CATEGORY_UPDATING) {
			lastIt.next();
			lastIt.getPendingUpdateList().apply();
			this.current = new EmptySequenceIterator(context, loc);
		} else {
			this.current = lastIt;
		}
	}

	protected void freeResources(boolean restartable) throws MXQueryException {
		super.freeResources(restartable);
		if (this.current instanceof Window) {
			((Window) this.current).destroyWindow();
		}
	}

	public Token next() throws MXQueryException {
		if (this.endOfSeq) {
			return Token.END_SEQUENCE_TOKEN;
		}
		if (this.called == 0) {
			this.init();
		}
		this.called++;
		Token tok = this.current.next();
		if (tok.getEventType() == Type.END_SEQUENCE) {
			this.freeResources(false);
		}
		return tok;
	}
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		return new ApplyExprIterator(context, subIters, loc);
	}
	
	protected void checkExpressionTypes(boolean isScripting) throws MXQueryException {
		// Do a check on all subIters if they are consistens, 
		// ignore the results, since all types are acceptable
		if (subIters != null && subIters.length > 0) {
			for (int i=0;i<subIters.length;i++) {
				subIters[i].getExpressionCategoryType(true);
			}
		}

		exprCategory = XDMIterator.EXPR_CATEGORY_SEQUENTIAL;
	}	
}

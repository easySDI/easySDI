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

package ch.ethz.mxquery.iterators;

import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.model.updatePrimitives.PendingUpdateList;
import ch.ethz.mxquery.util.KXmlSerializer;

/**
 * 
 * 
 * @author Matthias Braun
 * 
 */
public class IfThenElseIterator extends CurrentBasedIterator {
	
	/***************************************************************************
	 * methods *
	 **************************************************************************/

	/**
	 * Constructor for the IfThenElseIterator
	 * 
	 * @param subIters
	 *            Array of Expressions: first Expression is the IF condition;
	 *            second Expression is the THEN expression; third Expression is
	 *            the ELSE expression.
	 */
	public IfThenElseIterator(Context ctx, XDMIterator[] subIters, QueryLocation location) throws MXQueryException {
		super(ctx, subIters, location);
		if (subIters == null || subIters.length == 0 || subIters.length != 3) {
			throw new IllegalArgumentException();
		}
	}

	public Token next() throws MXQueryException {
		if (current == null) {
			init();
			}
		return  this.current.next();
	}

	protected void mergeChildPULS() throws MXQueryException {
		if (subIters[0].getExpressionCategoryType(true) == EXPR_CATEGORY_UPDATING) {
			this.pendingUpdateList.merge(subIters[0].getPendingUpdateList());
		}
		if (current.getExpressionCategoryType(true) == EXPR_CATEGORY_UPDATING) {
			this.pendingUpdateList.merge(current.getPendingUpdateList());
		}

	}
	
	private void init() throws MXQueryException {
		Token condToken = subIters[0].next(); 
		if (condToken.getEventType() == Type.BOOLEAN
				&& condToken.getBoolean()) {
			current = subIters[1];
		} else {
			current = subIters[2];
		}
	}

	public PendingUpdateList getPendingUpdateList() throws MXQueryException {
		if (isScripting) {
			if (pendingUpdateList == null)
				pendingUpdateList = new PendingUpdateList(loc);
			mergeChildPULS();
			return this.pendingUpdateList;
		} else
			return current.getPendingUpdateList();
	}
	
	public KXmlSerializer traverseIteratorTree(KXmlSerializer serializer)
			throws Exception {
		createIteratorStartTag(serializer);
		serializer.startTag(null, "ifExpr");
		subIters[0].traverseIteratorTree(serializer);
		serializer.endTag(null, "ifExpr");
		serializer.startTag(null, "thenExpr");
		subIters[1].traverseIteratorTree(serializer);
		serializer.endTag(null, "thenExpr");
		serializer.startTag(null, "elseExpr");
		subIters[2].traverseIteratorTree(serializer);
		serializer.endTag(null, "elseExpr");
		createIteratorEndTag(serializer);
		return serializer;
	}
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		return new IfThenElseIterator(context, subIters,loc);
	}
	
	protected void checkExpressionTypes() throws MXQueryException {
		if (!isScripting) {
			checkExprSimpleOnly(new XDMIterator [] {subIters[0]},isScripting);
		} else {
			checkExprNoSequential(new XDMIterator [] {subIters[0]},isScripting);
		}
		XDMIterator [] branch = new XDMIterator [2];
		branch[0] = subIters[1];
		branch[1] = subIters[2];
		exprCategory = checkExprDefault(branch,isScripting);
	}

//	public TypeInfo getReturnType() {
//		// TODO Auto-generated method stub
//		return super.getReturnType();
//	}	
//	
}

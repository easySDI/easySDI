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
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.util.KXmlSerializer;

/**
 * BLOCK
 * 
 * @author David Alexander Graf
 * 
 */
public class BlockIterator extends CurrentBasedIterator {
	private XDMIterator sequentialExpr;
	public BlockIterator(Context ctx, XDMIterator[] declarations, XDMIterator sequentialExpr, QueryLocation location) {
		super(ctx, location);
		exprCategory = XDMIterator.EXPR_CATEGORY_SEQUENTIAL;
		this.sequentialExpr = sequentialExpr;
		this.subIters = declarations;
	}
	
	protected void freeResources(boolean restartable) throws MXQueryException {
		// FIXME
//		throw new RuntimeException("David Fix Me!");
//		super.freeResources();
//		this.myContext.freeResources();
	}

	public Token next() throws MXQueryException {
		if (this.endOfSeq) {
			return Token.END_SEQUENCE_TOKEN;
		}
		if (this.called == 0) {
			for (int i = 0; i < this.subIters.length; i++) {
				// invoke the block declarations
				while (this.subIters[i].next().getEventType() != Type.END_SEQUENCE) {}
			}
			this.current = this.sequentialExpr;
		}
		this.called++;
		Token tok = this.sequentialExpr.next();
		if (tok.getEventType() == Type.END_SEQUENCE) {
			this.freeResources(false);
		}
		return tok;
	}

	protected void resetImpl() throws MXQueryException {
		super.resetImpl();
		this.sequentialExpr.reset();
	}

	public void setResettable(boolean r) throws MXQueryException {
		super.setResettable(r);
		this.sequentialExpr.setResettable(r);
	}
	
	public XDMIterator[] getAllSubIters() {
		XDMIterator[] iter = new XDMIterator[this.subIters.length + 1];
		iter[0] = this.sequentialExpr;
		for (int i = 0; i < this.subIters.length; i++) {
			iter[i + 1] = this.subIters[i];
		}
		return iter;
	}

	public KXmlSerializer traverseIteratorTree(KXmlSerializer serializer)
			throws Exception {
		this.createIteratorStartTag(serializer);
		for (int i = 0; i < this.subIters.length; i++) {
			this.subIters[i].traverseIteratorTree(serializer);
		}
		this.sequentialExpr.traverseIteratorTree(serializer);
		this.createIteratorEndTag(serializer);
		return serializer;
	}
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		return new BlockIterator(context, 
				subIters, 
				sequentialExpr.copy(context, null, false, nestedPredCtxStack), loc);
	}	
}

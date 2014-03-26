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
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.Iterator;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.util.KXmlSerializer;

/**
 * Represents a try expression.
 * 
 * @author David Graf
 * 
 */
public class TryIterator extends CurrentBasedIterator {
	private XDMIterator tryBlock;
	private CatchIterator[] tryCatches;
	
	public TryIterator(Context ctx, XDMIterator tryBlock,
			CatchIterator[] tryCatches, QueryLocation location) {
		super(ctx,location);
		this.tryBlock = tryBlock;
		this.tryCatches = tryCatches;
	}
	
	public XDMIterator[] getAllSubIters() {
		int add = 1;

		XDMIterator[] iters = new XDMIterator[this.tryCatches.length + add];
		iters[0] = this.tryBlock;
		for (int i = 1; i <= (iters.length - add); i++) {
			iters[i] = this.tryCatches[i - 1];
		}
		return iters;
	}

	/**
	 * try, resp. catch in case of exception must be materialized! Else it could
	 * be the case that an Iterator returns first results from the try (till the
	 * exception) and then the complete catch block.
	 * 
	 */
	private void init() throws MXQueryException {
		try {
			this.current = WindowFactory.getNewWindow_Eager(this.context, this.tryBlock);
		} catch (MXQueryException e) {
			this.tryBlock.close(false);
			QName qname = null;
			if (e.getErrorCode() != null) {
				String str[] = QName.parseQName(e.getErrorCode());
				if(str[0]!=null){
					this.context.getNamespace(str[0]);
				}
				qname = new QName(e.getErrorCode());
			}
			int i = 0;
			while (this.tryCatches.length > i && !this.tryCatches[i].compareErrCodes(qname)) {
				i++;
			}
			if (this.tryCatches.length <= i) {
					throw e;
			} else {
				this.tryCatches[i].init(e);
				this.current = WindowFactory.getNewWindow(this.context, this.tryCatches[i]);
			}
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
		Token inputToken = this.current.next();
		if (inputToken.getEventType() == Type.END_SEQUENCE) {
			this.freeResources(false);
		}
		return inputToken;
	}
	
	public void setContext(Context context, boolean recursive) throws MXQueryException {
		this.context = context;
		this.tryBlock.setContext(context, true);
		for (int i = 0; i < this.tryCatches.length; i++) {
			this.tryCatches[i].setContext(context, true);
		}
	}
	
	public void setResettable(boolean r) throws MXQueryException {
		this.resettable = r;
		this.tryBlock.setResettable(r);
		for (int i = 0; i < this.tryCatches.length; i++) {
			this.tryCatches[i].setResettable(r);
		}
	}
	
	protected void resetImpl() throws MXQueryException {
		super.resetImpl();
		this.tryBlock.reset();
		for (int i = 0; i < this.tryCatches.length; i++) {
			this.tryCatches[i].reset();
		}
	}
	
	protected void freeResources(boolean restartable) throws MXQueryException {
		super.freeResources(restartable);
		if (this.current instanceof Window) {
			((Window)this.current).destroyWindow();
		}
	}
	
	public KXmlSerializer traverseIteratorTree(KXmlSerializer serializer) throws Exception {
		createIteratorStartTag(serializer);
		serializer.startTag(null, "Try");
		this.tryBlock.traverseIteratorTree(serializer);
		serializer.endTag(null, "Try");
		for (int i = 0; i < this.tryCatches.length; i++) {
			this.tryCatches[i].traverseIteratorTree(serializer);
		}
		createIteratorEndTag(serializer);
		return serializer;
	}
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		return new TryIterator(context, 
				tryBlock.copy(context, null, false, nestedPredCtxStack), 
				(CatchIterator[]) Iterator.copyIterators(context, tryCatches, nestedPredCtxStack),loc);
	}

	
	
	protected void checkExpressionTypes(boolean isScripting) throws MXQueryException {
		exprCategory = tryBlock.getExpressionCategoryType(isScripting);
	}
}

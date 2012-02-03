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
import ch.ethz.mxquery.contextConfig.XQStaticContext;
import ch.ethz.mxquery.datamodel.adm.AllMatch;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.model.XDMIterator;

/**
 * Base Full-text iterator
 * @author jimhof
 */

public abstract class FTBaseIterator implements FTIteratorInterface {

	
	protected FTIteratorInterface [] subIters;
	protected Context context;
	public Window ignoreOption;
	
	protected int called = 0;
	
	public FTBaseIterator(Context ctx, FTIteratorInterface [] subIters){
		if (subIters == null)
			this.subIters = new FTIteratorInterface[] {};
		else
			this.subIters = subIters;
		this.context = ctx;
	}
	
	FTBaseIterator(Context ctx, Vector subIters) {
		FTIteratorInterface [] its = new FTIteratorInterface [subIters.size()];
		for (int i=0;i<its.length;i++) {
			its[i] = (FTIteratorInterface)subIters.elementAt(i);
		}
		this.subIters = its;
		this.context = ctx;
	}
	
	
	public AllMatch next() throws MXQueryException {
		return null;
	}

	public void reset() throws MXQueryException {
		for (int i=0; i < subIters.length; i++){
			subIters[i].reset();
			}
		called = 0;
	}

	public void setContext(Context ctx) throws MXQueryException {
		for (int i=0; i < subIters.length; i++){
			subIters[i].setContext(ctx);
		}
	}	
	
	public void setIgnoreOption(XDMIterator ignoreOption) throws MXQueryException{
		for (int i=0; i < subIters.length; i++){
				subIters[i].setIgnoreOption(ignoreOption);
			}
	}
	
	public void setResettable(boolean r) throws MXQueryException {
		for (int i=0;i<subIters.length;i++) {
			subIters[i].setResettable(r);
		}
		if (ignoreOption != null) {
			ignoreOption.setResettable(r);
		}
	}

	/**
	 * Prepares copying the Iterator. 
	 * The context is copied (if necessary) here.
	 * The subIterators are copied (if existing) here.
	 * Calls copy(Context context, Iterator[] subIters)!
	 * 
	 * 
	 * @param parentIterContext		The new parent context 
	 * @param prevParentIterContext The previous context of the parent iterator 
	 * @param copyContext		Copy the context or use the parent context
	 * @return					A copy of this Iterator
	 * 
	 * @throws MXQueryException	
	 */

	public FTIteratorInterface copy(Context parentIterContext, XQStaticContext prevParentIterContext, boolean copyContext, Vector nestedPredCtxStack) throws MXQueryException {
		// initialize new context and subIterators
		Context newContext = parentIterContext;
		FTIteratorInterface[] newSubIters = null;
		if (subIters != null) {
			newSubIters = new FTIteratorInterface[subIters.length]; 
		}
		
		if (context == null) {
			throw new RuntimeException("DEBUG: Clone problem - context is null");
		}
		
		// copy the context
		
		if (prevParentIterContext == context) {
			newContext = parentIterContext;
		}
		
		if (copyContext) {
			newContext = context.copy();
			newContext.setParent(parentIterContext);
			
		}
		
		// copy the subIterators
		if (subIters != null) {
			for (int i=0; i<subIters.length; i++) {
				XQStaticContext subCtx = subIters[i].getContext();
				boolean cctx = false;
				if (subCtx != null && context != null) {
					if (context.getParent() == subCtx) {
						// Location steps have "inverted nesting"
						newSubIters[i] = subIters[i].copy(parentIterContext, this.context, true, nestedPredCtxStack);
						newContext = new Context(newSubIters[i].getContext());
					} else {
						if (subCtx == prevParentIterContext){
							newSubIters[i] = subIters[i].copy(parentIterContext, this.context, false, nestedPredCtxStack);
						}
						else {
							cctx = subIters[i].getContext().equals(context)?false:true;
							newSubIters[i] = subIters[i].copy(newContext, this.context, cctx, nestedPredCtxStack);
						}
					}
				} else {
					throw new RuntimeException("DEBUG: Clone problem - context is null");
				}
					
			}
		}
		
		return copy(newContext, newSubIters, nestedPredCtxStack);

	}

	protected abstract FTIteratorInterface copy(Context context, FTIteratorInterface [] subIters, Vector nestedPredCtxStack)throws MXQueryException;
	
	public Context getContext() {
		return context;
	}

	
}

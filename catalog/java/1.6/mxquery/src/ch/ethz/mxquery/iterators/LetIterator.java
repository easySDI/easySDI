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

import ch.ethz.mxquery.bindings.WindowFactory;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.contextConfig.XQStaticContext;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.VariableHolder;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.util.KXmlSerializer;

public class LetIterator extends CurrentBasedIterator {

//	private QName variable;
//
//	private boolean init = false;
	
	private QName variable;
	
	private VariableHolder varHolder;
	
	private boolean firstInit = true;
	
	private boolean needMat = true;


	public LetIterator(Context ctx, XDMIterator[] subIters, QName var, TypeInfo t, QueryLocation location)
			throws MXQueryException {
		super(ctx, location);
		if (subIters == null || subIters.length == 0 || subIters.length != 1) {
			throw new IllegalArgumentException();
		}
		this.subIters = subIters;
		variable = var;
	}
	
	public Token next() throws MXQueryException {
		if (called==0) {
			init();
			called++;
			return Token.START_SEQUENCE_TOKEN;
		} else {
			if(needMat){
				(( Window) current).destroyWindow();
			}
			this.current = null;
			return Token.END_SEQUENCE_TOKEN;
		}

	}
	
	private void init() throws MXQueryException {
		if(firstInit){
			firstInit = false;
			varHolder = context.getVariable(variable);
			needMat = varHolder.needsMaterialization();	
		}
		if(needMat){
			this.current = WindowFactory.getNewWindow(context, subIters[0]);
		}else{
			this.current = subIters[0];
			current.setResettable(true);
		}
		varHolder.setIter(current);
	}
	
	protected void resetImpl() throws MXQueryException {
		freeResources(true);
		super.resetImpl();
	}
	
	protected void freeResources(boolean restartable) throws MXQueryException {
		super.freeResources(restartable);
		if(needMat && current != null){
			((Window) current).destroyWindow();
		}
	}
	
	protected KXmlSerializer createIteratorStartTag(KXmlSerializer serializer) throws Exception {
		super.createIteratorStartTag(serializer);
		serializer.attribute(null, "var", "$" +  variable.toString());
		serializer.attribute(null, "varNeedMat", "" +  this.needMat);
		return serializer;
	}

	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		return new LetIterator(context, subIters, variable.copy(), null, loc);
	}
	
	public final XDMIterator copy(Context parentIterContext, XQStaticContext newParentIterContext, boolean copyContext, Vector nestedPredCtxStack) throws MXQueryException {
		// initialize new context and subIterators
		Context newContext = parentIterContext;
		XDMIterator[] newSubIters = null;
		if (subIters != null) {
			newSubIters = new XDMIterator[subIters.length]; 
		}
		
		// Let is always in its own context
		newContext = context.copy();
		newContext.setParent(parentIterContext);
		if (subIters != null)			
			for (int i=0; i<subIters.length; i++) {

				XQStaticContext subContext = subIters[i].getContext();
				XQStaticContext parCtx = context.getParent();
				// Due to the scoping rules of Let/For/etc, 
				//the parent and the producing expression of let should have the same context
				if (subContext != null && subContext == parCtx) {
					newSubIters[i] = subIters[i].copy(parentIterContext, null, false, nestedPredCtxStack);
					//newContext = newSubIters[i].getContext(); // special FFLWORIterator context setting				
				} else {
					newSubIters[i] = subIters[i].copy(parentIterContext, null, true, nestedPredCtxStack);
				}

			}
		LetIterator lt = (LetIterator)copy(newContext, newSubIters, nestedPredCtxStack);
		lt.exprCategory = this.exprCategory;
		return lt;
		
	}	

	
}

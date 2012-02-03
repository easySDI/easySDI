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
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.VariableHolder;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.util.ContextPair;
import ch.ethz.mxquery.util.KXmlSerializer;

public class ForIterator extends CurrentBasedIterator {
	//private static String vi = "ch.ethz.mxquery.model.iterators.VariableIterator";

	private QName variable;
	private QName posQName;
	private int pos=0;

	boolean setContextItem;
	
	private boolean init = false;

	Window windowInterface;
	
	Window currentVar;
	//Window currentPosVar;
	
	private VariableHolder varHolder;
	private VariableHolder varPosHolder;
	//private VariableHolder contextItem;

	public ForIterator(Context ctx, XDMIterator[] subIters, QName var, TypeInfo t, QName pos, boolean setContextItem, QueryLocation location) 
	throws MXQueryException {
		super(ctx, location);
		
		if (subIters == null || subIters.length == 0 || subIters.length != 1) {
			throw new IllegalArgumentException();
		}
		variable = var;
		posQName = pos;
		this.subIters = subIters;
		this.setContextItem = setContextItem;
	}

	public ForIterator(Context ctx, XDMIterator[] subIters, QName var, TypeInfo t, QName pos, QueryLocation location) 
	throws MXQueryException {
		this(ctx,  subIters, var, t, pos, false, location);
	}
	
	public Token next() throws MXQueryException {
		if (!init) {
			init();
			init = true;
		}
		return assignVar();
	}
	
	protected void freeResources(boolean restartable) throws MXQueryException {
		super.freeResources(restartable);
		if (this.windowInterface != null) {
			this.windowInterface.destroyWindow();
			this.windowInterface = null;
		}
		this.current = null;
		
		if(currentVar != null){
			currentVar.destroyWindow();
//			if(currentPosVar != null){
//				currentPosVar.destroyWindow();
//			}
		}
	}
	
	// FIXME: START_DOCUMENT!!!
	private Token assignVar() throws MXQueryException {
		if(currentVar != null){
			currentVar.destroyWindow();
//			if(currentPosVar != null){
//				currentPosVar.destroyWindow();
//			}
		}


		if (windowInterface.hasNextItem()) {
			currentVar = (Window) windowInterface.nextItem();
			varHolder.setIter(currentVar);
			pos++;
			Context posCheckContext = subIters[0].getContext();
			if (posCheckContext == context)
				posCheckContext = posCheckContext.getParent(); 
			int cPos = posCheckContext.getPosition();
			if (cPos > 0)
				context.setPosition(cPos);
			else
				context.setPosition(pos);
			//context.getVariable(variable).setValue(currentVar);
			if(posQName!=null){
				//currentPosVar = WindowFactory.getNewWindow(this.context, new TokenIterator(pos));
				varPosHolder.setIter(new TokenIterator(context, pos, Type.INTEGER,loc));
				//context.getVariable(posQName).setValue(currentPosVar);
			}

			// FIXME: This is not correct, but at the moment necessary for child
			// iterators
			if(setContextItem){
				context.setContextItem(currentVar);
			}
			return Token.START_SEQUENCE_TOKEN;
		} else {
			this.freeResources(false);
			return Token.END_SEQUENCE_TOKEN;
		}		
	}

	protected void resetImpl() throws MXQueryException {
		this.freeResources(true);
		super.resetImpl();
		pos=0;
		this.init = false;

	}

	private void init() throws MXQueryException {
		windowInterface = WindowFactory.getNewWindow(context, subIters[0]);
		varHolder = context.getVariable(variable);
		if(posQName!=null){
				varPosHolder = context.getVariable(posQName);
		}
		current = windowInterface;
	}

	protected KXmlSerializer createIteratorStartTag(KXmlSerializer serializer) throws Exception {
		super.createIteratorStartTag(serializer);
		serializer.attribute(null, "var", "$" +  variable.toString());
		return serializer;
	}
	
	public final XDMIterator copy(Context parentIterContext, XQStaticContext newParentIterContext, boolean copyContext, Vector nestedPredCtxStack) throws MXQueryException {
		// initialize new context and subIterators
		Context newContext = parentIterContext;
		
		if (copyContext || newParentIterContext != context) {
			newContext = context.copy();
			newContext.setParent(parentIterContext);
		}
		else
			newContext = parentIterContext;
		
		XDMIterator[] newSubIters = null;
		if (subIters != null) {
			newSubIters = new XDMIterator[subIters.length]; 					
			for (int i=0; i<subIters.length; i++) {

				XQStaticContext subContext = subIters[i].getContext();
				XQStaticContext parCtx = context.getParent();
				boolean inNestedStack = false;
				ContextPair cp = null;
				for (int k = 0;k<nestedPredCtxStack.size();k++) {
					cp = (ContextPair)nestedPredCtxStack.elementAt(k);
					if (cp.prevContext == subContext) {
						inNestedStack = true;
						break;
					}		
				}

				if (inNestedStack) {
					newSubIters[i] = subIters[i].copy(cp.newContext, cp.prevContext, false, nestedPredCtxStack);
				} else {
					// Due to the scoping rules of Let/For/etc, 
					//the parent and the producing expression of let should have the same context
					if (subContext != null && subContext == parCtx) {
						newSubIters[i] = subIters[i].copy(parentIterContext, null, false, nestedPredCtxStack);				
					} else {
						//newSubIters[i] = subIters[i].copy(newContext, null, true, nestedPredCtxStack);
						newSubIters[i] = subIters[i].copy(parentIterContext, null, true, nestedPredCtxStack);
					}
				}
			}
		}
		ForIterator fr = (ForIterator)copy(newContext, newSubIters, nestedPredCtxStack);
		fr.exprCategory = this.exprCategory;
		return fr;
	}		
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		return new ForIterator(
				context, 
				subIters, 
				variable.copy(), 
				null, 
				posQName == null?null:posQName.copy(), 
				setContextItem, loc);
	}
}

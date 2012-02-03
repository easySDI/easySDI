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

package ch.ethz.mxquery.iterators.forseq;

import java.util.Vector;

import ch.ethz.mxquery.bindings.WindowFactory;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.contextConfig.XQStaticContext;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.VariableHolder;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.model.XDMIterator;

/**
 * The main abstract class for FORSEQ. 
 * It basically contains a collection of helper methods
 * @author Tim Kraska (<a href="mailto:mail@tim-kraska.de">mail@tim-kraska.de</a>)
 *
 */
public abstract class ForseqIterator extends TokenBasedIterator {
	
	public static final int GENERAL_WINDOW=0;
	public static final int TUMBLING_WINDOW=1;
	public static final int SLIDING_WINDOW=2;
	public static final int LANDMARK_WINDOW=3;
	public static final int ORDER_MODE_NONE=1;
	public static final int ORDER_MODE_END=1;
	public static final int ORDER_MODE_ENDSTART=2;
	

	protected QName var;
	protected TypeInfo varType;
	
	//The materialized source
	protected Window seq;
	protected int windowType=-1;
	 
	protected int currentPosition=0;
	protected boolean endOfStream = false;
	protected boolean firstInit = true;
	
	protected VariableHolder varHolder;
	
	/**
	 * is there parallel access on the bindings results of the Forseq iterator?
	 */
	protected boolean parallelAccess = false;
	
	
	/**
	 * Every FORSEQ (Window and General) has those properties in common
	 * @param windowType
	 * @param var
	 * @param t
	 * @param seq
	 */
	public ForseqIterator(Context ctx, int windowType, QName var, TypeInfo t, XDMIterator seq, int orderMode, QueryLocation location) {
		super(ctx, new XDMIterator[]{seq}, location);
		if (seq == null || var == null) {
			throw new IllegalArgumentException();
		}
		this.windowType = windowType;
		this.var = var; 
		this.varType = t;
	}
	
	/**
	 * A skeleton for next. It takes care, that init is called and that the windows are destroyed for 
	 * the garbage collection
	 */
	public Token next() throws MXQueryException {
		if(called==0){
			init();
			called++;
		}
		
		if(currentToken == Token.END_SEQUENCE_TOKEN){
			return currentToken;
		}
		
		if(varHolder.getIter() != null){
			((Window)varHolder.getIter()).destroyWindow();
		}
		
		Window newWindow = assignWindow();
		
		varHolder.setIter(newWindow);
		
		if(newWindow == null){
			return Token.END_SEQUENCE_TOKEN;
		}else{
			return Token.START_SEQUENCE_TOKEN;
		}
	}
	
	/**
	 * Returns the QName of the forseq variable
	 * @return	The QName of the forseq variable
	 */
	public QName getForseqVariable() {
		return var;
	}
	
	/**
	 * This method is called for every next() call and has to return a the next window or null in the case, 
	 * that all bindings are already done. Every FORSEQ implementation has to overwrite this method.
	 * @return the window that has been assigned
	 * @throws MXQueryException
	 */
	protected abstract Window assignWindow()  throws MXQueryException;
	
	/**
	 * Register the variable in the context
	 * @throws MXQueryException
	 */
	//TODO: Do this stuff in FFLWOR
	protected void init() throws MXQueryException {
		seq = WindowFactory.getNewWindow(context, this.subIters[0],parallelAccess);
		if(firstInit){
			firstInit = false;
			varHolder = context.getVariable(var);
		}
	}
	
	/**
	 * Increases the currentPosition in the source stream and sets endOfStream if the source ended
	 * This method is called once in init!
	 *
	 */
	protected void increaseCurrentPosition() throws MXQueryException {
		currentPosition++;
		if (!seq.hasItem(currentPosition)){
			endOfStream=true;
		} 
	}

	/**
	 * Resets all the basic settings. 
	 */
	protected void resetImpl() throws MXQueryException {
		super.resetImpl();
		freeResources(true);
		currentPosition=0;
		endOfStream=false;
		seq = null;
		currentToken = Token.START_SEQUENCE_TOKEN;
	}
	
	protected void freeResources(boolean restartable) throws MXQueryException {
		if(varHolder.getIter() != null){
			((Window)varHolder.getIter()).destroyWindow();
		}
	}

	public final XDMIterator copy(Context parentIterContext, XQStaticContext newParentIterContext, boolean copyContext, Vector nestedPredCtxStack) throws MXQueryException {
		// initialize new context and subIterators
		
		// Forseq is always in its own context
		Context newContext = context.copy();
		newContext.setParent(parentIterContext);
		
		
		XDMIterator[] newSubIters = null;
		
		if (subIters != null) {
			newSubIters = new XDMIterator[subIters.length]; 
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
		}
		ForseqIterator fq = (ForseqIterator)copy(newContext, newSubIters, nestedPredCtxStack);
		fq.exprCategory = this.exprCategory;
		return fq;
	}
	public boolean isParallelAccess() {
		return parallelAccess;
	}

	public void setParallelAccess(boolean parallelAccess) {
		this.parallelAccess = parallelAccess;
	}	
}




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

/**
 *  author RokasT.
 */

package ch.ethz.mxquery.iterators;

import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.contextConfig.XQStaticContext;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.model.updatePrimitives.PendingUpdateList;


public class TypeSwitchIterator extends CurrentBasedIterator {

	XDMIterator mainBinding; 
	XDMIterator switchExpr;
	Vector vCases; 
	XDMIterator [] vDefault;
	boolean init = false;
	
	public TypeSwitchIterator(Context ctx, XDMIterator mainBinding, XDMIterator switchExpr, Vector vCases, XDMIterator [] vDefault, QueryLocation location) throws MXQueryException {
		super(ctx, location);
		this.switchExpr = switchExpr;
		this.mainBinding = mainBinding;
		this.vCases = vCases;
		this.vDefault = vDefault;
	}

	private void init() throws MXQueryException {
		init = true;
		boolean finished = false;
		
		//-- bind main variable for typeswitch (let iterator)
		Token startSeqT = mainBinding.next();
		if (startSeqT.getEventType() != Type.START_SEQUENCE )
			throw new MXQueryException(ErrorCodes.A0009_EC_EVALUATION_NOT_POSSIBLE, "TypeSwitchIterator: main binding could not be performed", loc);
		
		/** vCase vector structure:
		 * 0: return expression (Iterator)
		 * 1: SequenceType Iterator
		 * 2: variable binding Iterator, if exists
		*/
		XDMIterator [] vCase;
		for (int i=0; i < vCases.size(); i++) {
			vCase = (XDMIterator []) vCases.elementAt(i);
			XDMIterator seqTypeIt = vCase[1];
			seqTypeIt.setSubIters(switchExpr);
			
			Token tok = seqTypeIt.next();
			
			if ( tok.getBoolean() ){
				// bind variable if exists
				if (vCase.length == 3) {
					vCase[2].next();
				}
				finished = true; 
				this.current = vCase[0];
				break;
			}
			else {
				switchExpr.reset();
			}
		}
		
		if (!finished ) {
			/** vDefault vector structure:
			 * 0: return expression (Iterator)
			 * 1: variable binding Iterator, if exists
			*/
			finished = true;
			if (vDefault.length == 2) {
				vDefault[1].next();
			}
			this.current = vDefault[0];
		}
	}
	
	public Token next() throws MXQueryException {
		if ( !init ) init();
			return current.next();
	}
	
	protected void resetImpl() throws MXQueryException {
		super.resetImpl();
		init = false;
		
		mainBinding.reset();
		switchExpr.reset();
		
		XDMIterator [] vCase;
		for (int i=0; i < vCases.size(); i++) {
			vCase = (XDMIterator []) vCases.elementAt(i);
			for (int j=0; j < vCase.length; j++) {
				vCase[j].reset();
			}
		}
		
		for (int i=0; i < vDefault.length; i++) {
			vDefault[i].reset();
		}		
	}

	public void setResettable(boolean r) throws MXQueryException {
		super.setResettable(r);
		switchExpr.setResettable(true);
		mainBinding.setResettable(r);
		
		XDMIterator [] vCase;
		for (int i=0; i < vCases.size(); i++) {
			vCase = (XDMIterator []) vCases.elementAt(i);
			for (int j=0; j < vCase.length; j++) {
				vCase[j].setResettable(r);
			}
		}
		
		for (int i=0; i < vDefault.length; i++) {
			vDefault[i].setResettable(r);
		}
		
		
	}

	/* (non-Javadoc)
	 * @see ch.ethz.mxquery.model.iterators.Iterator#setContext(ch.ethz.mxquery.query.Context)
	 */
	public void setContext(Context context, boolean recursive) throws MXQueryException {
		//if (!recursive)
			this.context.setParent(context);
		//else super.setContext(context, recursive);	
		//this.context = context;
	}

	protected void checkExpressionTypes() throws MXQueryException {
		XDMIterator [] checkIters = new XDMIterator [vCases.size()+1];
		for (int i=0;i<vCases.size();i++) {
			XDMIterator [] vCase = (XDMIterator []) vCases.elementAt(i);
			checkIters[i] = vCase[0];
		}
		checkIters[checkIters.length-1] = vDefault[0];
		
		exprCategory = checkExprDefault(checkIters,isScripting);
		if (!isScripting)
			checkExprSimpleOnly(new XDMIterator[] {mainBinding},isScripting);
		else
			checkExprNoSequential(new XDMIterator[] {mainBinding},isScripting);
	}

	public PendingUpdateList getPendingUpdateList() throws MXQueryException {
		PendingUpdateList curPUL = current.getPendingUpdateList();
		if (isScripting && mainBinding.getExpressionCategoryType(isScripting) == EXPR_CATEGORY_UPDATING)
			curPUL.merge(mainBinding.getPendingUpdateList());
		return curPUL;
	}

	public XDMIterator copy(Context parentIterContext, XQStaticContext newParentIterContext, boolean copyContext, Vector nestedPredCtxStack) throws MXQueryException {
		// initialize new context and subIterators
		Context newContext = parentIterContext;
		
		// copy the context
		if (copyContext && context != null) {
			newContext = context.copy();
			newContext.setParent(parentIterContext);

		}
		
		XDMIterator newMainBinding = mainBinding.copy(newContext, null, false, nestedPredCtxStack);
		
		Context parContext = newMainBinding.getContext();
		
		XDMIterator newSwitchExpr = switchExpr.copy(parContext, null, false, nestedPredCtxStack);
		
		// copy cases
		Vector newCases = new Vector(vCases.size());
		for (int i=0;i<vCases.size();i++) {
			XDMIterator [] caseToCopy = (XDMIterator [])vCases.elementAt(i);
			XDMIterator [] newCase = new XDMIterator[caseToCopy.length];
			Context newCaseContext = new Context(parContext);
			for (int j=1;j<caseToCopy.length;j++) {
				newCase[j] = caseToCopy[j].copy(newCaseContext, null, false, nestedPredCtxStack); 
			}
			Context retClauseContext = newCaseContext;
			// if an in-scope variable has been defined, the return expression needs to be executed in the context of that let clause
			if (caseToCopy.length == 3) {
				retClauseContext = newCase[2].getContext();
			} else {
				
			}
			newCase[0] = caseToCopy[0].copy(retClauseContext, null, false, nestedPredCtxStack);
			newCases.addElement(newCase);
		}
		
		// copy default 
		XDMIterator [] newDefault = new XDMIterator [vDefault.length];
	
		Context newDefaultContext = new Context(parContext);
		for (int j=vDefault.length-1;j>=0;j--) {
			if (vDefault.length == 2 && j==0)
				newDefaultContext = newDefault[1].getContext();
			newDefault[j] = vDefault[j].copy(newDefaultContext, null, false, nestedPredCtxStack); 
		}
		
		TypeSwitchIterator ts = new TypeSwitchIterator(newContext,newMainBinding, newSwitchExpr ,newCases,newDefault,loc);
		ts.exprCategory = this.exprCategory; 
		return ts;
	}

	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack)
			throws MXQueryException {
		throw new RuntimeException("TypeSwitchIterator.copy(context, iterators) should not be called!");
	}			
}

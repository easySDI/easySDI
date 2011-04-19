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

import ch.ethz.mxquery.bindings.WindowFactory;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.contextConfig.XQStaticContext;
import ch.ethz.mxquery.datamodel.MXQueryDouble;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.adm.AllMatch;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.BooleanToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.iterators.TokenIterator;
import ch.ethz.mxquery.iterators.VariableIterator;
import ch.ethz.mxquery.model.Iterator;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.util.ContextPair;

/**
 * Implementation of the FTContainsExpr
 * @author jimhof
 */
public class FTContainsIterator extends Iterator {
	
	FTIteratorInterface iterator;
	boolean isScoring;
	
	public FTContainsIterator(Context ctx, XDMIterator[] subIters, FTIteratorInterface iter, QueryLocation location, boolean genScore){
		super(ctx, subIters,location);
		iterator = iter;
		isScoring = genScore;
		
	}
	

	
	public Token next() throws MXQueryException {
	
		if (called > 0)
			return Token.END_SEQUENCE_TOKEN;
		else
			called++;
		
		while (true) {
		
			Token tok = subIters[0].next();
			
			// If empty seq or no match found until end-of-seq => false
			if (tok == Token.END_SEQUENCE_TOKEN) {
				if (isScoring){
					context.setVariableValue(new QName(".ft",".score"), new TokenIterator(context,new MXQueryDouble(0),loc));
				}
				return BooleanToken.FALSE_TOKEN;
			}
			// push ignore option down
			if (subIters.length > 1){
				iterator.setIgnoreOption(WindowFactory.getNewWindow(context, subIters[1]));

			}
			if (isScoring){
				AllMatch am = iterator.next();
				if (am != AllMatch.END_ALL_MATCH_SEQUENCE){
					context.setVariableValue(new QName(".ft",".score"), new TokenIterator(context,am.getScore(),loc));
					return BooleanToken.TRUE_TOKEN;
				}
			}
			else{
				if (iterator.next() != AllMatch.END_ALL_MATCH_SEQUENCE){
					return BooleanToken.TRUE_TOKEN;
				}	
			}
			iterator.reset();
		}
	}



	public void setContext(Context context, boolean recursive) throws MXQueryException {
			
			Vector searchCtxArgs = getAllSubItersRecursive();
			for (int i=0;i<searchCtxArgs.size();i++) {
				Iterator it = (Iterator)searchCtxArgs.elementAt(i);
				if (it instanceof VariableIterator) {
					VariableIterator var = (VariableIterator)it;
					if (var.getVarQName().equals(Context.CONTEXT_ITEM)) {
						Context ctx = var.getContext();
						if (ctx != this.context && !ctx.isAnecstorContext(this.context) && ctx.isAnecstorContext(this.context.getParent())) {
							var.setContext(context, true);
						}
					}
				}
			}
			this.context.setParent(context);
	}
	
	
	public void resetImpl() throws MXQueryException
	{
		super.resetImpl();
		iterator.reset();
	}

	public TypeInfo getStaticType() {
		return new TypeInfo(Type.BOOLEAN,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
	}		
	
	
	public void setResettable(boolean r) throws MXQueryException {
		super.setResettable(true);
		iterator.setResettable(true);
	}

	public XDMIterator copy(Context parentIterContext, XQStaticContext prevParentIterContext, boolean copyContext, Vector nestedPredCtxStack) throws MXQueryException {
		/*
		 * Special implementation of copy, since this bridges between XDM and ADM iterators
		 * TODO: Maybe more refactoring in this area to undo copy & paste
		 */
		// initialize new context and subIterators
		Context newContext = parentIterContext;
		XDMIterator[] newSubIters = null;
		if (subIters != null) {
			newSubIters = new XDMIterator[subIters.length]; 
		}
		
		FTIteratorInterface newFTSubIter = null;
		
		if (context == null) {
			throw new RuntimeException("DEBUG: Clone problem - context is null");
		}
		
		// copy the context
		
		if (prevParentIterContext == context) {
			newContext = parentIterContext;
		}
		// Always copy the context, since there are the local variables for FTITEM around

			newContext = context.copy();
			newContext.setParent(parentIterContext);
		 nestedPredCtxStack.addElement(new ContextPair(context,newContext));

		// copy the subIterators
			for (int i=0;i<subIters.length;i++) {
				XQStaticContext subCtx = subIters[0].getContext();
				boolean cctx = false;
				if (subCtx != null) {
					if (context.getParent() == subCtx) {
						// Location steps have "inverted nesting"
						newSubIters[i] = subIters[i].copy(parentIterContext, this.context, true, nestedPredCtxStack);
						if (i==0)
							newFTSubIter = iterator.copy(parentIterContext, this.context, true, nestedPredCtxStack);
						newContext = new Context(newSubIters[0].getContext());
					} else {
						if (subCtx == prevParentIterContext){
							newSubIters[i] = subIters[i].copy(parentIterContext, this.context, false, nestedPredCtxStack);
							if (i==0)
								newFTSubIter = iterator.copy(parentIterContext, this.context, false, nestedPredCtxStack);
						}
						else {
							cctx = subIters[0].getContext().equals(context)?false:true;
							newSubIters[i] = subIters[i].copy(newContext, this.context, cctx, nestedPredCtxStack);
							if (i==0)
								newFTSubIter = iterator.copy(newContext, this.context, cctx, nestedPredCtxStack);
						}
					}
				} else {
					throw new RuntimeException("DEBUG: Clone problem - context is null");
				}
			}
			nestedPredCtxStack.removeElementAt(nestedPredCtxStack.size()-1);
			FTContainsIterator fc = new FTContainsIterator(newContext,newSubIters,newFTSubIter,loc,isScoring);
			fc.exprCategory = this.exprCategory;
			return fc;
	}	
	

	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		throw new MXQueryException(ErrorCodes.A0009_EC_EVALUATION_NOT_POSSIBLE,"Copying FTContains not directly supported",loc);
	}

}

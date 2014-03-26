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
import ch.ethz.mxquery.contextConfig.XQStaticContext;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.BooleanToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;


public class QuantifiedIterator extends TokenBasedIterator {
	boolean every = false;
	//TypeInfo [] seqTypes;
	
	
	public QuantifiedIterator(Context ctx, XDMIterator[] subIters, boolean every, QueryLocation location) {
		super(ctx, subIters, location);
		this.every = every;
		//seqTypes = types;
	}



	protected void init() throws MXQueryException {
		int nestedLevel = 0;
		int len = 0;
		boolean value = false;
		
		for (int i=1;i<subIters.length;i++)
			subIters[i].setResettable(true);
		while (true) {
			Token tok = subIters[nestedLevel].next();

			if (tok.getEventType() == Type.END_SEQUENCE) {
				if (nestedLevel == 0) {
					break;
				} else {
					// This is only important if the binding is over
					if (subIters[nestedLevel].isResettable()) {
						subIters[nestedLevel].reset();
					} else {
						nestedLevel = 0;
						break;
					}
					nestedLevel--;
				}
			} else {
				if (nestedLevel == subIters.length - 2) {
					Token resToken = subIters[subIters.length-1].next();

					value = resToken.getBoolean();

					subIters[subIters.length-1].reset();
					// if every, continue until false,
					// if !every, continue until true
					len++;
					if (value != every)
						break;
				} else {
					nestedLevel++;
				}

			}
		}
		if (value == true || (len == 0 && every == true)) 
			currentToken = BooleanToken.TRUE_TOKEN;
		else
			currentToken = BooleanToken.FALSE_TOKEN;
	}
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		return new QuantifiedIterator(context, subIters, every,loc);
	}
	
	public final XDMIterator copy(Context parentIterContext, XQStaticContext newParentIterContext, boolean copyContext, Vector nestedPredCtxStack) throws MXQueryException {

		// initialize new context and subIterators
		Context newContext = parentIterContext;
		XDMIterator[] newSubIters = null;
		if (subIters != null) {
			newSubIters = new XDMIterator[subIters.length]; 
		}
		
		// copy the context
		if (copyContext && context != null) {
			newContext = context.copy();
			newContext.setParent(parentIterContext);
		
		}
		
		// copy the subIterators
		if (subIters != null) {
			Context prevContext = newContext;
			for (int i=0; i<subIters.length-1; i++) {
				newSubIters[i] = subIters[i].copy(prevContext, null, false, nestedPredCtxStack);
				prevContext = newSubIters[i].getContext(); 
			}
			newSubIters[subIters.length-1] = subIters[subIters.length-1].copy(prevContext, null, false, nestedPredCtxStack);
		}
		
		QuantifiedIterator qn = (QuantifiedIterator)copy(newContext, newSubIters, nestedPredCtxStack);
		qn.exprCategory = this.exprCategory;
		return qn;
	}

	public TypeInfo getStaticType() {
		return new TypeInfo(Type.BOOLEAN,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
	}



	public void setContext(Context context, boolean recursive)
			throws MXQueryException {
		this.context = context;
		if (subIters != null && subIters.length > 0){
				subIters[0].getContext().setParent(context);
		}
	}		
	
	
}

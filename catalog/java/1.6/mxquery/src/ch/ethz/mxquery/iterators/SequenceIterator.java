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

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.Iterator;
import ch.ethz.mxquery.model.XDMIterator;

import java.util.Vector;

/**
 * 
 * @author Matthias Braun
 * 
 */
public class SequenceIterator extends CurrentBasedIterator {
	private int index = -1;
	private boolean inSubIterator = false;
	
	
	public SequenceIterator(Context ctx, XDMIterator[] subIters, QueryLocation location) {
		this(ctx,subIters,Iterator.EXPR_CATEGORY_UNDETERMINED,location);
	}

	protected SequenceIterator(Context ctx, XDMIterator[] subIters, int exprType,QueryLocation location) {
		super(ctx, subIters, location);
		exprCategory = exprType;
		if (subIters != null && subIters.length > 0) {
			current = subIters[0];
		}		
	}
	
	public SequenceIterator(Context ctx, QueryLocation location) {
		super(ctx, location);
		
	}
	
	public void setSubIters(Vector subIt) {
		subIters = new XDMIterator[subIt.size()];
		for (int i =0; i < subIt.size(); i++ ) {
			subIters[i] = (XDMIterator)subIt.elementAt(i);
		}	
		current = subIters[0];
	}
	
	protected XDMIterator[] toArray() {
		if (subIters != null) {
			current = subIters[0];
			return subIters;
		}
		return null;
	}

	public Token next() throws MXQueryException {
		// TODO: Remove recursion
		if (subIters != null && subIters.length > index) {
			Token next;
			
			if (inSubIterator) {
				next = current.next(); // handleSubIterator
			}
			else {
			// handle new sub iterator	
				index++;
				if (index < subIters.length) {
					current = subIters[index];
					inSubIterator = true;
					next = current.next(); // handleSubIterator
				}
				else {
					// job done
					return Token.END_SEQUENCE_TOKEN;
				}
			}
			
			if (next.getEventType() == Type.END_SEQUENCE) {
				if (exprCategory == EXPR_CATEGORY_UPDATING && !isScripting) 
					//in scripting, the "child merge" already takes care of this 
					this.getPendingUpdateList().merge(
							current.getPendingUpdateList());
				inSubIterator = false;
				return next();
				
			} else {
				return next;
			}			
		}
		return Token.END_SEQUENCE_TOKEN;
	}

	protected void resetImpl() throws MXQueryException {
		super.resetImpl();
		if (subIters != null && subIters.length > 0) {
			current = subIters[0];
		}

		index = -1;
		inSubIterator = false;
	}
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		return new SequenceIterator(context, subIters, exprCategory, loc);
	}

	protected void checkExpressionTypes() throws MXQueryException {
		exprCategory = checkExprDefault(subIters,isScripting);
	}

	public TypeInfo getStaticType() {
		if (subIters != null && subIters.length == 1)
			return subIters[0].getStaticType();
		else
			return super.getStaticType();
		//TODO: We can be smarter here, determining the common supertype
	}
	
}

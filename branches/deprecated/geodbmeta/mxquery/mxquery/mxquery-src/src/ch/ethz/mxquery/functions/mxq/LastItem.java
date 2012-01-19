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

package ch.ethz.mxquery.functions.mxq;

import java.util.Vector;

import ch.ethz.mxquery.bindings.WindowFactory;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.iterators.SequenceIterator;
import ch.ethz.mxquery.iterators.VariableIterator;
import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.model.XDMIterator;

/**
 * 
 * @author Rokas T.
 *
 */
public class LastItem extends CurrentBasedIterator {
	
	public Token next() throws MXQueryException {
		if (called == 0) {
			init();
			called++;
		}
		return current.next();
	}
	
	/**
	 *  returns last item of the sequence; 
	 *  equivalent to positional predicate [last()] 
	 * */
	protected void init() throws MXQueryException {
		
		XDMIterator param = subIters[0];  
		XDMIterator resIt = null;
		
		if (param instanceof SequenceIterator ) {
			XDMIterator[] subParams =  param.getAllSubIters();
			resIt = subParams[ subParams.length -1]; 
		}
		else 
		{	
			Window it;
			if (param instanceof VariableIterator) {
				it = ((VariableIterator) param).getUnderlyingIterator();
			} else if (param instanceof Window) {
				it = (Window) param;
			} else {
				it = WindowFactory.getNewWindow(param.getContext(), param);
			}
			
			while ( it.hasNextItem() )
				resIt = it.nextItem();
		}	
	
		current = resIt;
	}
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new LastItem();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;
	}
}

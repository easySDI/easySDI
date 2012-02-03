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

package ch.ethz.mxquery.functions.fn;

import java.util.Vector;

import ch.ethz.mxquery.bindings.WindowFactory;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.model.XDMIterator;

public class Exactly_one extends CurrentBasedIterator {
	
	
	protected void init() throws MXQueryException {
		if (subIters[0] == null || subIters.length != 1) {
			throw new IllegalArgumentException();
		}
		
		Window buffer;
		
		buffer = WindowFactory.getNewWindow(getContext(), subIters[0]);
		
		if (!buffer.hasItem(1) || buffer.hasItem(2)) { // starts with 1
			throw new DynamicException(ErrorCodes.F0027_EXACTLY_ONE_WITH_ZERO_OR_MULTI, "Only exactly one item expected!", loc);
		}
		current = buffer.getItem(1);
	}
	
	public Token next() throws MXQueryException {
		if (called == 0) {
			init();
			called = 1;
		}
		
		return current.next();
	}

	public TypeInfo getStaticType() {
		return new TypeInfo(Type.ITEM,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
	}		
	
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new Exactly_one();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;	
	}
}

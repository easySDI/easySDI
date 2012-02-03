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

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.Source;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.EmptySequenceIterator;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.model.updatePrimitives.UpdateableStore;

public class Root extends CurrentBasedIterator {

	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack)
			throws MXQueryException {
		// TODO Auto-generated method stub
		XDMIterator it =new Root();
		it.setContext(context, false);
		it.setSubIters(subIters);
		return it;
	}

	private void init () throws MXQueryException{
		XDMIterator it = getNodeIteratorOrContext(subIters, 1,context, loc);
		Token tok = it.next();
		if (!(Type.isNode(tok.getEventType()) || tok == Token.END_SEQUENCE_TOKEN)) {
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Context Item is not a node", loc);
		}
		
		if (tok == Token.END_SEQUENCE_TOKEN) { 
			current = new EmptySequenceIterator(context,loc);
			return;
		}
		
//		if (!context.getRootContext().isUseUpdateableVariables())
//			throw new DynamicException(ErrorCodes.A0002_EC_NOT_SUPPORTED,"To use the fn:root(), please switch to updateable variables (for now)",loc);
		
		Source src = tok.getId().getStore();
		
		XDMIterator rootIter;
		
		if (src instanceof UpdateableStore) {
			// Create a new window on a store 
			rootIter = ((UpdateableStore)src).getIterator(context);
		}  else {
			rootIter = (XDMIterator)src;
			rootIter.setResettable(true);
			rootIter.reset();
		}
		
		current = rootIter;		
	}
	
	public Token next() throws MXQueryException {
		
		if (called == 0) {
			init();
		}
		called++;
		return current.next();
	}

	public TypeInfo getStaticType() {
		return new TypeInfo(Type.NODE,Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
	}
}
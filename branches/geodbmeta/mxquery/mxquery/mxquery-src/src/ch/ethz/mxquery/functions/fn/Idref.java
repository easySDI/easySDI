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
import ch.ethz.mxquery.datamodel.Source;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.iterators.DescendantOrSelfIterator;
import ch.ethz.mxquery.model.CheckNodeType;
import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.model.updatePrimitives.UpdateableStore;
import ch.ethz.mxquery.util.Utils;

public class Idref extends CurrentBasedIterator {

	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack)
			throws MXQueryException {
		XDMIterator res = new Idref();
		res.setContext(context, false);
		res.setSubIters(subIters);
		return res;
	}

	Vector idRefs = new Vector();
	
	private void init() throws MXQueryException {

		XDMIterator idIt = subIters[0];
		Token idTok = idIt.next();
		
		while (idTok!=Token.END_SEQUENCE_TOKEN) {
		if (!(Type.isTypeOrSubTypeOf(idTok.getEventType(), Type.STRING, Context.getDictionary())||
				idTok.getEventType()==Type.UNTYPED_ATOMIC)) {
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Expected String as first parameter of id(), got "+Type.getTypeQName(idTok.getEventType(), Context.getDictionary()), loc);
		}
		
		String idString = idTok.getText();
		String [] currIds = Utils.split(idString," ");
		for (int i=0;i<currIds.length;i++)
			idRefs.addElement(currIds[i]);
		idTok = idIt.next();
		}

		XDMIterator it = getNodeIteratorOrContext(subIters, 2,context, loc);
		Token tok = it.next();
		if (tok.getEventType() != Type.START_TAG && tok.getEventType() != Type.START_DOCUMENT) {
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Context Item is not a node", loc);
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
		Window wnd = WindowFactory.getNewWindow(context, rootIter);
		wnd.setResettable(true);
		Token docStart = wnd.next();
		if (docStart.getEventType() != Type.START_DOCUMENT)
			throw new DynamicException(ErrorCodes.F0013_NO_CONTEXT_DOCUMENT, "fn:IdRef() only works with documents",loc);
		wnd.reset();
		current = new DescendantOrSelfIterator(context,idRefs,CheckNodeType.CHECK_IDTYPE_IDREF,loc);
		current.setSubIters(wnd);

	}

	
	public Token next() throws MXQueryException {
		if (called == 0) {
			init();
			called++;
		}
		
		return current.next();
		//return Token.END_SEQUENCE_TOKEN;
	}

	public TypeInfo getStaticType() {
		return new TypeInfo(Type.NODE,Type.OCCURRENCE_IND_ZERO_OR_MORE,null,null);
	}
	
}

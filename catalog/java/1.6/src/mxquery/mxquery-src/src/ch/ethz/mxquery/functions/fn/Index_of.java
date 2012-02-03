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
import ch.ethz.mxquery.datamodel.xdm.LongToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.util.Set;

public class Index_of extends TokenBasedIterator {
	private Window win;
	private Token srchParam;
	
	
	protected void init() throws MXQueryException {		
		win = WindowFactory.getNewWindow(getContext(), subIters[0]);
		srchParam = subIters[1].next();
		if (subIters.length > 2 ){
			String coll = null;
			Set collations;
			XDMIterator collIter = subIters[2];
			Token collToken = collIter.next();
			if (collToken == Token.END_SEQUENCE_TOKEN || 
					!Type.isTypeOrSubTypeOf(collToken.getEventType(),Type.STRING, null))
				throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Wrong type for collation", loc);
			coll = collToken.getText();
			collations = context.getCollations();
			if (coll != null && Type.isTypeOrSubTypeOf(srchParam.getEventType(), Type.STRING, null) && !collations.contains(coll))
				throw new DynamicException(ErrorCodes.F0010_UNSUPPORTED_COLLATION, "Unsupported Collation", loc);
		}
	}
	
	public Token next() throws MXQueryException {
		if (called == 0) {
			init();
			called = 1;
		}
		
		Token tok;
		
		while(win.hasNextItem()) {
			tok = win.next();
			try {
			if (tok != Token.END_SEQUENCE_TOKEN && tok.compareTo(srchParam) == 0) {
				return new LongToken(Type.INT, null, win.getPosition());
			}
			} catch (TypeException te) {
				continue;
			}
		}
		
		return Token.END_SEQUENCE_TOKEN;
	}

	public TypeInfo getStaticType() {
		return new TypeInfo(Type.INTEGER,Type.OCCURRENCE_IND_ZERO_OR_MORE,null,null);
	}		
	
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new Index_of();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;	
	}
}
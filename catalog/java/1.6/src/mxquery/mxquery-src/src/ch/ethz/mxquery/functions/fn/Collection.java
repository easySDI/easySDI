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
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeLexicalConstraints;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;

public class Collection extends CurrentBasedIterator {

	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack)
			throws MXQueryException {
		XDMIterator copy = new Collection();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;
	}
	private int index = -1;
	private boolean inSubIterator = false;
	XDMIterator [] subs;
	
	protected void init() throws MXQueryException {
		boolean defaultCollection = false;
		Token tok = null;
		XDMIterator[] nodes;
		if (subIters == null || subIters.length == 0)
			defaultCollection = true;
		else {
			XDMIterator it = subIters[0];
			tok = it.next();
		}
		if (defaultCollection || tok == Token.END_SEQUENCE_TOKEN ) {
			nodes = context.getStores().getCollection(Context.DEFAULT_COLLECTION_URI);
			if (nodes == null)
				throw new DynamicException(ErrorCodes.F0014_ERROR_RETRIEVING_RESOURCE,"Default collection is not set",loc);
		}
		else {
			String uri = tok.getText();
			if (!TypeLexicalConstraints.isValidURI(uri))
				throw new DynamicException(ErrorCodes.F0014_ERROR_RETRIEVING_RESOURCE,"Invalid URI "+uri+" for fn:collection",loc);
			nodes = context.getStores().getCollection(uri);
			if (nodes == null) 
				throw new DynamicException(ErrorCodes.F0016_INVALID_ARGUMENT_TO_FN_COLLECTION,"Collection for URI "+uri+" not set",loc);
		}
		subs = nodes;
	}
	
	public Token next() throws MXQueryException {
		if (called ==0) {
			init();
			called++;
		}
		// TODO: Remove recursion
		if (subs.length > index) {
			Token next;

			if (inSubIterator) {
				next = current.next(); // handleSubIterator
			}
			else {
				// handle new sub iterator	
				index++;
				if (index < subs.length) {
					current = subs[index];
					inSubIterator = true;
					next = current.next(); // handleSubIterator
				}
				else {
					// job done
					return Token.END_SEQUENCE_TOKEN;
				}
			}

			if (next.getEventType() == Type.END_SEQUENCE) {
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
		index = -1;
		inSubIterator = false;
		if (subs != null)
			for (int i=0;i<subs.length;i++)
					subs[i].reset();
	}

}

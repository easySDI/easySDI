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
import ch.ethz.mxquery.datamodel.Identifier;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.AnyURIToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;

public class DocumentUri extends TokenBasedIterator {

	protected void init() throws MXQueryException {
		Token tok = subIters[0].next();
		if (tok.getEventType() == Type.START_DOCUMENT) {
			// Take advantage of our Node ID implementation -
			// each node ID has a reference to the store it comes from,
			// and stores orginating in a external document carry the document URL
			Identifier id = tok.getId();
			String uri = id.getStore().getURI();
			if (uri.indexOf("http://www.mxquery.org/nodeconstruction") >= 0)
				currentToken = Token.END_SEQUENCE_TOKEN;
			else
				currentToken = new AnyURIToken(null,uri);
		} else {
			currentToken = Token.END_SEQUENCE_TOKEN;
		}

	}

	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new DocumentUri();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;	
	}

	public TypeInfo getStaticType() {
		return new TypeInfo(Type.ANY_URI,Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
	}

}

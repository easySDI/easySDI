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
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.AnyURIToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.datamodel.xdm.XDMScope;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;

public class BaseUri extends TokenBasedIterator {

	protected void init() throws MXQueryException {
		XDMIterator idIt = getNodeIteratorOrContext(subIters, 1,context, loc);
		Token tok = idIt.next();
		
		int ev = tok.getEventType();
		
		if (!(Type.isNode(ev)||ev == Type.END_SEQUENCE)) {
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,"Base-Uri only works on nodes", loc);
		}

		switch (ev) {
		case Type.START_DOCUMENT:
		case Type.START_TAG:
		case Type.PROCESSING_INSTRUCTION:
		case Type.COMMENT:
			XDMScope nsScope = tok.getDynamicScope();
			String baseUri = nsScope.getBaseURI();
			if (baseUri != null)
				currentToken = new AnyURIToken(null,baseUri);
			else 
				currentToken = Token.END_SEQUENCE_TOKEN;
			break;
		default:
			if (Type.isAttribute(ev)) {
				nsScope = tok.getDynamicScope();
				baseUri = nsScope.getBaseURI();
				if (baseUri != null)
					currentToken = new AnyURIToken(null,baseUri);
				else 
					currentToken = Token.END_SEQUENCE_TOKEN;
			}
			else
			currentToken = Token.END_SEQUENCE_TOKEN;
		break;
				
		}
	}

	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new BaseUri();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;	
	}

	public TypeInfo getStaticType() {
		return new TypeInfo(Type.ANY_URI,Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
	}

}

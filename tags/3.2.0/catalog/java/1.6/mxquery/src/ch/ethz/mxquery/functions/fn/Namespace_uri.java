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
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;

public class Namespace_uri extends TokenBasedIterator {
	
	protected void init() throws MXQueryException {
		XDMIterator input = getNodeIteratorOrContext(subIters, 1,context, loc);
		//input = DataValuesIterator.getDataIterator(it, context);
		
		Token tok = input.next();
		if (!(Type.isNode(tok.getEventType())||tok == Token.END_SEQUENCE_TOKEN)) {
			throw new DynamicException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Context Item is not a Node!", loc);
		}		
		
		// namespace in attribute?
		if (tok.getNS() != null) {
			currentToken = new AnyURIToken(null, tok.getNS());
			return;
		}
		
		// namespace from context
		String nsString = "";
//		Namespace ns = context.getNamespace(new QName(tok.getName()).getNamespacePrefix());
//		
//		if (ns != null && ns.getURI() != null && ns.getURI().length() > 0) {
//			nsString = ns.getURI();
//		}
		currentToken = new AnyURIToken(null, nsString);
	}

	public TypeInfo getStaticType() {
		return new TypeInfo(Type.ANY_URI,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
	}		
	
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new Namespace_uri();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;	
	}
}

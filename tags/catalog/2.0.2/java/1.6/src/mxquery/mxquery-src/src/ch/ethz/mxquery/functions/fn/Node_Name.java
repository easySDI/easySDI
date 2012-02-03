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
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.QNameToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;

public class Node_Name extends TokenBasedIterator {
	protected void init() throws MXQueryException {
		if (subIters == null || subIters.length == 0) {
			currentToken = Token.END_SEQUENCE_TOKEN;
			return;
		}
	
		XDMIterator input = subIters[0];
		Token inputToken = input.next();
		
		int i = inputToken.getEventType();
		
		if (!(Type.isNode(i)||inputToken == Token.END_SEQUENCE_TOKEN))
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,"fn:node-name() only works on nodes, not atomic values",loc);
		if (Type.isAttribute(i)) {
			String qname = inputToken.getName();
			String nsUri = inputToken.getNS();
			QName qn = new QName(qname);
			qn.setNamespaceURI(nsUri);
			currentToken = new QNameToken(null,qn);
			return;
		}
		if (Type.isTextNode(i)) {
			currentToken = Token.END_SEQUENCE_TOKEN;
			return;
		}
		switch (i) {
		case Type.START_TAG:
		case Type.PROCESSING_INSTRUCTION:
			String qname = inputToken.getName();
			QNameToken qn = new QNameToken(null,new QName(qname));
			if (inputToken.getNS() != null)
				qn.setNS(inputToken.getNS());
			currentToken = qn;
			break;
		case Type.START_DOCUMENT:
		case Type.STRING:
		case Type.COMMENT:
		case Type.END_SEQUENCE:
		{
			currentToken = Token.END_SEQUENCE_TOKEN;
			break;
		}
		default:
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Invalid argument type for fn:node-name()!", loc);
		}
	}
	public TypeInfo getStaticType() {
		return new TypeInfo(Type.QNAME,Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
	}		
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new Node_Name();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;	
	}
}

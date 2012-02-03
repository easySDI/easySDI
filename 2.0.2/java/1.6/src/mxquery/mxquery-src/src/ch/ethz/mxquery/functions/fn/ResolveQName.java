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
import ch.ethz.mxquery.datamodel.xdm.XDMScope;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;

public class ResolveQName  extends TokenBasedIterator {

	protected void init() throws MXQueryException {
		if (subIters[0] == null) {
			throw new IllegalArgumentException();
		}

		XDMIterator iter = subIters[0];
		Token inputToken = iter.next(); 
				
		int type = Type.getEventTypeSubstituted( inputToken.getEventType(), Context.getDictionary() );
		
		switch (type) {
			case Type.END_SEQUENCE:
					currentToken = Token.END_SEQUENCE_TOKEN;
			break;
			case Type.STRING:
			case Type.UNTYPED_ATOMIC:
				String val =  inputToken.getText().trim();
				if (val.equals(""))
					throw new TypeException(ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR, "Invalid lexical value: " + val, loc);
				
				Token contextNode = subIters[1].next();
				
				QName res = new QName(val);
				
				currentToken = new QNameToken(null, res);
				
				if (contextNode.getEventType() == Type.START_TAG) {
					XDMScope ns = contextNode.getDynamicScope();
					String nm = ns.getNsURI(res.getNamespacePrefix());
					if (nm != null) {
						res.setNamespaceURI(nm);
					} else if (res.getNamespacePrefix() != null) {
						throw new DynamicException(ErrorCodes.F0021_NO_NAMESPACE_FOUND,"No namespace found for the given prefix and context node",loc);
					}
					
				} else
					throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,"Need element node for fn-resolve-qname()",loc);

				
			break;
			default:
				throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,"Could not cast type " + Type.getTypeQName(type, Context.getDictionary()) + " to type " + Type.getTypeQName(Type.QNAME, Context.getDictionary()), loc);
		}
		
		if ( iter.next() != Token.END_SEQUENCE_TOKEN )
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,"Could not cast sequence to atomic type " + Type.getTypeQName(Type.QNAME, Context.getDictionary()), loc);	
	}
	public TypeInfo getStaticType() {
		return new TypeInfo(Type.QNAME,Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
	}		
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new ResolveQName();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;
	}
}
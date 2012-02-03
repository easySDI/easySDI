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

import java.util.Enumeration;
import java.util.Hashtable;
import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.Namespace;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.TextToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.datamodel.xdm.XDMScope;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;

public class In_scope_prefixes extends CurrentBasedIterator {
	private Token[] tokens = null;
	private int i = 0;
	
	protected void init() throws MXQueryException {
		if (subIters == null || subIters.length != 1) {
			throw new IllegalArgumentException();
		}
		
		Token t1 = subIters[0].next();
		if (t1.getEventType() == Type.START_TAG) {
			
			XDMScope scope = t1.getDynamicScope();
			
			Hashtable namespaces = scope.getAllNamespaces();
			
			int resSize = namespaces.size();			
									
			if (resSize > 0) {
				tokens = new Token[resSize];
				Enumeration nsEnum = namespaces.elements();
				int pos = 0;
				while (nsEnum.hasMoreElements()) {
					Namespace ns = (Namespace)nsEnum.nextElement();
					if (!(ns.getNamespacePrefix().equals("") && ns.getURI().equals("")))
						tokens[pos++] = new TextToken(null, ns.getNamespacePrefix());
				}
				if (pos < tokens.length) {
					Token [] newTokens = new Token[pos];
					for (int i=0;i<newTokens.length;i++)
						newTokens[i] = tokens[i];
					tokens = newTokens;
				}
				return;
			} else {
				current = null;
				return;
			}
		} else {
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Invalid argument type for fn:in-scope-prefixes!", loc);
		}
	}
	
	public Token next() throws MXQueryException {
		if (called == 0) {
			init();
		}
		called++;
		
		if (tokens == null || tokens.length == 0 || i >= tokens.length) {
			return Token.END_SEQUENCE_TOKEN;
		}
		
		if (tokens[i] != null) {
			return tokens[i++];
		}
		
		return Token.END_SEQUENCE_TOKEN;
	}

	public TypeInfo getStaticType() {
		return new TypeInfo(Type.STRING,Type.OCCURRENCE_IND_ZERO_OR_MORE,null,null);
	}		
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new In_scope_prefixes();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;	
	}
}

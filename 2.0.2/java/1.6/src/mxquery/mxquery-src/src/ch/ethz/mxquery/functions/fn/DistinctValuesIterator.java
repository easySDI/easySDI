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

import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.util.Set;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;

/**
 * Implements a distinct-value Iterator with memoization.
 * @author Tim Kraska
 *
 */
public class DistinctValuesIterator extends CurrentBasedIterator {
	private Set distinctValues = new Set();
	private String coll = null;
	Set collations;
	
/** FIXME: fix distinct value implementation: not string value comparison 
 * test suit case: distinct-duration-equal-1  
 	fn:distinct-values((10, '10')) -> 10 10
 *  
 */
	
	public Token next() throws MXQueryException {
		if (called == 0) {
			current = subIters[0];
			if (subIters.length > 1 ){
				XDMIterator collIter = subIters[1];
				Token collToken = collIter.next();
				if (collToken == Token.END_SEQUENCE_TOKEN || 
						!Type.isTypeOrSubTypeOf(collToken.getEventType(),Type.STRING, null))
					throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Wrong type for collation", loc);
				coll = collToken.getText();
				collations = context.getCollations();
			}
			called++;
		}
		
		String key;
		Token tok = Token.END_SEQUENCE_TOKEN;;
		do{
			tok = current.next();
			if (tok.getEventType() == Type.END_SEQUENCE){
				return Token.END_SEQUENCE_TOKEN;
			}	
			String keyVal = tok.getValueAsString();
			String keyType = null;
			int type = tok.getEventType();
			if (Type.isTypeOrSubTypeOf(type, Type.DECIMAL, null) || 
					type == Type.FLOAT || type == Type.DOUBLE)
				keyType = "NUMBER";
			else if (Type.isTypeOrSubTypeOf(type, Type.STRING, null) || 
					type == Type.UNTYPED_ATOMIC || type == Type.UNTYPED) {
				keyType = "STRING";
				if (coll != null && !collations.contains(coll))
					throw new DynamicException(ErrorCodes.F0010_UNSUPPORTED_COLLATION, "Unsupported Collation", loc);
			}
			else 
				keyType = Type.getTypeQName(type, Context.getDictionary()).toString();
			key = keyVal + keyType;
		}while(distinctValues.contains(key));
		
		distinctValues.add(key);
		return tok;
	}
	
	protected void resetImpl() throws MXQueryException {
		super.resetImpl();
		coll = null;
		distinctValues = new Set();
	}
	
	public TypeInfo getStaticType() {
		return new TypeInfo(Type.ANY_ATOMIC_TYPE,Type.OCCURRENCE_IND_ZERO_OR_MORE,null,null);
	}	
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new DistinctValuesIterator();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;	
	}
}

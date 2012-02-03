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
import ch.ethz.mxquery.datamodel.MXQueryDouble;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.DoubleToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;

public class Number extends TokenBasedIterator {
	protected void init() throws MXQueryException {
		XDMIterator input = null;
		
		XDMIterator it = getNodeIteratorOrContext(subIters, 1,context, loc);
		input = DataValuesIterator.getDataIterator(it, context);
		
		Token tok = input.next();
		// all subtypes handled uniformly
		int type = Type.getEventTypeSubstituted( tok.getEventType(), Context.getDictionary() ); 
		switch (type) { 
			case Type.INTEGER:
				currentToken = new DoubleToken(null, new MXQueryDouble(tok.getLong()));
				break;
			case Type.DECIMAL:
			case Type.FLOAT:
				currentToken = new DoubleToken(null, tok.getDouble());
				break;
			case Type.DOUBLE:
				currentToken = tok;	
				break;
			case Type.STRING:
			case Type.UNTYPED_ATOMIC:
			case Type.UNTYPED:
				try {
					currentToken = new DoubleToken(null, new MXQueryDouble(tok.getText()));	
				} catch (MXQueryException ex) {
					currentToken = new DoubleToken(null, new MXQueryDouble("NaN"));
				}
				break;
			default:
				currentToken = new DoubleToken(null, new MXQueryDouble("NaN"));
		}
	}
	
	public TypeInfo getStaticType() {
		return new TypeInfo(Type.DOUBLE,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
	}		
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new Number();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;	
	}
}

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

package ch.ethz.mxquery.extensionsModules.math;

import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.MXQueryBigDecimal;
import ch.ethz.mxquery.datamodel.MXQueryDouble;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.DecimalToken;
import ch.ethz.mxquery.datamodel.xdm.DoubleToken;
import ch.ethz.mxquery.datamodel.xdm.FloatToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;

public class Sqrt extends TokenBasedIterator {

	protected void init() throws MXQueryException {
		MXQueryDouble x = null;
		MXQueryDouble result = null;
		
		XDMIterator input = subIters[0];
		Token inputToken = input.next(); 
		int type = Type.getEventTypeSubstituted(inputToken.getEventType(), Context.getDictionary());		
		
		switch (type) {
		case Type.END_SEQUENCE:
			currentToken = Token.END_SEQUENCE_TOKEN;
			return;
			case Type.INTEGER:
				x = new MXQueryDouble(inputToken.getLong());	
				break;
			case Type.UNTYPED_ATOMIC:
			case Type.UNTYPED:
				x = new MXQueryDouble(inputToken.getText());	
				break;
			case Type.DOUBLE:
			case Type.FLOAT:
			case Type.DECIMAL:		
				x = inputToken.getDouble();				
				break;
		default:
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Invalid argument type"+Type.getTypeQName(type, Context.getDictionary()), loc);
		}
		
		result = new MXQueryDouble(Math.sqrt(x.getValue()));
		switch (type) {
			case Type.DOUBLE:
			case Type.UNTYPED_ATOMIC:
			case Type.UNTYPED:	
				currentToken = new DoubleToken(null, result);
				break;
			case Type.FLOAT:
				currentToken = new FloatToken(null, result.getFloatValue());
				break;
			case Type.DECIMAL:
			case Type.INTEGER:
				currentToken = new DecimalToken(null, new MXQueryBigDecimal(result));
				break;
		}
	}

	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack)
			throws MXQueryException {
		XDMIterator copy = new Sqrt();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;
	}
	public TypeInfo getStaticType() {
		return new TypeInfo(Type.NUMBER,Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
	}

}

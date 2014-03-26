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

import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.functions.fn.Replace;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.xdm.TextToken;
import ch.ethz.mxquery.datamodel.xdm.Token;

public class Replace extends TokenBasedIterator {

	protected void init() throws MXQueryException {
		XDMIterator input = DataValuesIterator.getDataIterator(subIters[0],context);
		Token tok = input.next();
		
		int type = tok.getEventType();
		
		if (type == Type.END_SEQUENCE) {
			currentToken = new TextToken(null,"");
			return;
		}
		if (type != Type.STRING)
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Invalid argument type"+type,loc);
		String res = tok.getText();
		Token tok1 = subIters[1].next();
		
		String pattern = tok1.getText();
		
		Token tok2 = subIters[2].next();
		
		String replacement = tok2.getText();
		//currentToken.setText(res.replaceAll(pattern, replacement));
		//FIXME: Dummy implementation
		currentToken = new TextToken(null,res);
	}
	
	public TypeInfo getStaticType() {
		return new TypeInfo(Type.STRING,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
	}		
	
	public XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new Replace();
		copy.setContext(context,true);
		copy.setSubIters(subIters);
		return copy;	
	}
	
}

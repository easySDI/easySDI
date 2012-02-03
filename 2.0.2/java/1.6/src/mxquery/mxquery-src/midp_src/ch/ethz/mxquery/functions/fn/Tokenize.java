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
import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.iterators.SequenceIterator;
import ch.ethz.mxquery.iterators.TokenIterator;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeDictionary;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.util.Utils;
import ch.ethz.mxquery.datamodel.xdm.Token;

public class Tokenize extends CurrentBasedIterator {

	public Token next() throws MXQueryException {
		if (called == 0) {
			called++;
			init();
		}
		return current.next();
	}
	
	private void init() throws MXQueryException {
		XDMIterator input = DataValuesIterator.getDataIterator(subIters[0],context);
		Token tok1 = input.next();
		int type = tok1.getEventType();
		String res = tok1.getText();
		XDMIterator input2 = DataValuesIterator.getDataIterator(subIters[1],context);
		Token tok2 = input2.next();
		int type2 = tok2.getEventType();
		String delimiter = tok2.getText();
		if (type2 != Type.STRING || delimiter.length() == 0)
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Invalid argument type"+Type.getTypeQName(type,null),loc);
		switch (type) {
		case(Type.END_SEQUENCE): 
			current = new TokenIterator(context, "", loc);
			break;
		
		case(Type.STRING):
			if (res.length() == 0)
				current = new TokenIterator(context, "",loc);
			else {
				String [] splitted = Utils.split(res,delimiter);
				TokenIterator [] toks = new TokenIterator[splitted.length];
				for (int i =0;i<toks.length;i++)
					toks[i] = new TokenIterator(context, splitted[i],loc);
				current = new SequenceIterator(null, toks,loc);
			}
		break;
		default:
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Invalid argument type"+type,loc);
		}
	}
	
	public TypeInfo getStaticType() {
		return new TypeInfo(Type.STRING,Type.OCCURRENCE_IND_ZERO_OR_MORE,null,null);
	}	
	
	public XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new Tokenize();
		copy.setContext(context,true);
		copy.setSubIters(subIters);
		return copy;	
	}
}
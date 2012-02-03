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
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;

public class Remove extends CurrentBasedIterator {
	private long rPos = -1; 
	private long position = 0;
	
	public Token next() throws MXQueryException {
		Token retToken;
		if (called == 0) {
			called++;
			init();
		}
		
		if (endOfSeq)
			return Token.END_SEQUENCE_TOKEN;
		
		retToken = sub0Next();

		// skip all tokens of at the position to remove
		while (position == rPos && retToken.getEventType() != Type.END_SEQUENCE) {
			retToken = sub0Next();
		}
		if (retToken.getEventType() == Type.END_SEQUENCE){
			this.freeResources(false);
			//current = null;
		}
		return retToken;

	}

	private Token sub0Next() throws MXQueryException {
		if (depth == 0) {
			position++;
		}
		return super.getNext();
	}	
	
	private void init() throws MXQueryException {
			
			Token tok1 = subIters[1].next();
			int type1 = Type.getEventTypeSubstituted(tok1.getEventType(), Context.getDictionary());
			
			current = subIters[0];
			
			if (Type.isTypeOrSubTypeOf(type1, Type.INTEGER, Context.getDictionary())) {
				rPos = tok1.getLong();
			} else {
				throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,"Expected integer type",loc);
			}
	}
	
	protected void resetImpl() throws MXQueryException {
		super.resetImpl();
		position = 0;
	}	
	public TypeInfo getStaticType() {
		return new TypeInfo(Type.ITEM,Type.OCCURRENCE_IND_ZERO_OR_MORE,null,null);
	}		
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new Remove();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;	
	}
}

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
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.EmptySequenceIterator;
import ch.ethz.mxquery.model.XDMIterator;

public class SubSequenceIterator extends CurrentBasedIterator {

	private long startPos = -1; 
	private long len = -1;
	private long position = 0;
	
	public Token next() throws MXQueryException {
		Token retToken;
		
		if (endOfSeq)
			return Token.END_SEQUENCE_TOKEN;
		
		if (called == 0) {
			called++;
			retToken = init();
		}
		else
			retToken = sub0Next();

		if((position < startPos+len || len < 0)) {
			if (retToken.getEventType() == Type.END_SEQUENCE){
				this.freeResources(false);
				current = null;
			}
			return retToken;
		}
		else  {
			this.freeResources(false);
			//current = null;
			return Token.END_SEQUENCE_TOKEN;
		}
	}

	private Token sub0Next() throws MXQueryException {
		if (depth == 0) {
			position++;
		}
		return super.getNext();
	}	
	
	private Token init() throws MXQueryException {
			int type;// = parameters[0].next();
			
			Token tok1 = subIters[1].next();
			
			if (tok1 == Token.END_SEQUENCE_TOKEN)
				throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Value expected",loc);
			
			int type1 = Type.getEventTypeSubstituted(tok1.getEventType(), Context.getDictionary());
			
			long sPos = -1;
			switch (type1) {
			case Type.INTEGER: 	
				sPos = tok1.getLong();
				break;
			case Type.DOUBLE:
			case Type.FLOAT:
			case Type.DECIMAL:
				MXQueryDouble val = tok1.getDouble().round();
				if ( val.isNaN() || val.isNegativeInfinity() || val.isPositiveInfinity() ) {
					current = new EmptySequenceIterator(context,loc);
					return current.next();
				}
				sPos = val.getIntValue();
				if ( val.hasFractionPart() ) sPos++;
				break;
				default:
					throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Numveric value expected",loc);
			}
			if (subIters.length == 2) {
				if (sPos < 1)
					sPos = 1;
				startPos = sPos;
				len = -1;
				return skipToStart();
			}
			if (subIters.length == 3) {
				Token tok2 = subIters[2].next();
				
				if (tok2 == Token.END_SEQUENCE_TOKEN)
					throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Value expected",loc);

				type = Type.getEventTypeSubstituted(tok2.getEventType(), Context.getDictionary());				
				long sLen = -1;
				switch (type) {
				case Type.INTEGER: 	
					sLen = tok2.getLong();
					break;
				case Type.DOUBLE:
				case Type.FLOAT:
				case Type.DECIMAL:
					MXQueryDouble val = tok2.getDouble().round();
					if ( val.isNaN() || val.isNegativeInfinity()) {
						current = new EmptySequenceIterator(context,loc);
						return current.next();
					}
					sLen = val.getLongValue();
					if ( val.hasFractionPart() ) sLen++;
					break;
				default:
					throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Numveric value expected",loc);
				}
				startPos = sPos;
				
				if (startPos < 1) {
					startPos = 1;
					sLen -= startPos - sPos;
				}
				if (sLen < 1)
					sLen = 0;
				len = sLen;
				return skipToStart();
			}
			current = new EmptySequenceIterator(context,loc);
			return current.next();
		
	}
	private Token skipToStart() throws MXQueryException{
		current = subIters[0];
		while(true) {
			Token tok = sub0Next();
			if(position == startPos || tok.getEventType() == Type.END_SEQUENCE)
				return tok;
		}
	}
	
	protected void resetImpl() throws MXQueryException {
		super.resetImpl();
		startPos = -1; 
		len = -1;
		position = 0;
	}	
	public TypeInfo getStaticType() {
		return new TypeInfo(Type.ITEM,Type.OCCURRENCE_IND_ZERO_OR_MORE,null,null);
	}		
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new SubSequenceIterator();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;	
	}
}

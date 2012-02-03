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
package ch.ethz.mxquery.iterators;

import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.xdm.LongToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;

public class RangeIterator extends TokenBasedIterator {
	
	private long cur, end;
	
	public RangeIterator(Context ctx, XDMIterator[] subIters, QueryLocation location) {
		super(ctx, subIters,location);
	}

	private LongToken getInteger(Token t) throws MXQueryException  {
		long val;
		LongToken valT;
		try {
			val = Long.parseLong(t.getText());	
		} catch (NumberFormatException e) {
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,"Invalid types", loc);
		} 
		valT = new LongToken(Type.INTEGER, null, val);
		
		return valT;
	}
	
	protected void init() throws MXQueryException {
		Token first  = subIters[0].next();
		Token second = subIters[1].next();

		if (first.getEventType() == Type.UNTYPED_ATOMIC || first.getEventType() == Type.UNTYPED )
			first = getInteger(first);
		
		if (second.getEventType() == Type.UNTYPED_ATOMIC || second.getEventType() == Type.UNTYPED)
			second = getInteger(second);
		
			
		if (first == Token.END_SEQUENCE_TOKEN || second == Token.END_SEQUENCE_TOKEN) {
			currentToken = Token.END_SEQUENCE_TOKEN;
		}
		else if (!Type.isTypeOrSubTypeOf(first.getEventType(), Type.INTEGER, null) || !Type.isTypeOrSubTypeOf(second.getEventType(), Type.INTEGER, null)) {
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,"Invalid types", loc);
		}
		else {
			cur = first.getLong();
			end = second.getLong();
			increment();
		}
	}
	
	public Token next() throws MXQueryException {
		switch (called) {
		case 0:
			init();
			called++;
			break;		
		default:
			increment();
		}
		
		return currentToken;
	}
	
	private void increment() throws MXQueryException {
		if (cur > end) {
			currentToken = Token.END_SEQUENCE_TOKEN;
		}
		else {
			currentToken = new LongToken(Type.INTEGER, null, cur);
			cur++;
		}		
	}

	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		return new RangeIterator(context, subIters,loc);
	}
}

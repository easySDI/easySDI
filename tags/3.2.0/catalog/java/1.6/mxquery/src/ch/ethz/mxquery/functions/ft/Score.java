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

package ch.ethz.mxquery.functions.ft;

import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.MXQueryDouble;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.xdm.BooleanToken;
import ch.ethz.mxquery.datamodel.xdm.DoubleToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.iterators.VariableIterator;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;

/**
 * Implementation of Scoring for a let clause
 * @author jimhof
 */

public class Score extends TokenBasedIterator{
	private Vector scores = null;
	
	public Score(XDMIterator inIt, Context ctx, QueryLocation loc) {
		super(ctx,new XDMIterator[]{inIt, new VariableIterator(ctx,new QName(".ft",".score"),false,loc)},loc);
		this.scores = new Vector();
	}

	protected void init() throws MXQueryException {
		XDMIterator iter = subIters[0];
		XDMIterator scoreVar = subIters[1];
	
		Token token = iter.next();
		
		while (token != Token.END_SEQUENCE_TOKEN && token != BooleanToken.FALSE_TOKEN) {
			Token tk = scoreVar.next();
			if (tk != Token.END_SEQUENCE_TOKEN)
				scores.addElement(tk);
			token =iter.next();
		};
		// if no score could be retrieved
		if (scores.size() == 0) {
			// try to get score value anyway
			Token tk;
			try {
				tk = scoreVar.next();
			} catch (DynamicException dn) {
				// if that still fails, set score to 0
				tk = new DoubleToken(null, new MXQueryDouble(0));
			}	
			scores.addElement(tk);
		}
	}
	
	public Token next() throws MXQueryException{
		
		if (this.called == 0) {
			init();
		}
		if (this.called < scores.size()){
			return (Token) scores.elementAt(called++);
		}
		
		return Token.END_SEQUENCE_TOKEN;
		
	}

	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new Score(subIters[0], context, loc);
		return copy;
	}

	protected void resetImpl() throws MXQueryException {
		super.resetImpl();
		scores = new Vector();
	}
	
}

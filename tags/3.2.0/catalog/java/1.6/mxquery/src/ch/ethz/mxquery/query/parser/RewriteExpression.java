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

package ch.ethz.mxquery.query.parser;

import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.model.Iterator;
import ch.ethz.mxquery.model.XDMIterator;
/**
 * General encapsulation class for expression that will not turn into an explicit 
 * expression, but rewritten/integrated to other expressions 
 * @author petfisch
 *
 */
public class RewriteExpression extends Iterator {

	public static final int ENCLOSED_EXPRESSION = 0;
	public static final int CHAR_REF = 1;
	public static final int CDATA = 2;
	public static final int FUNCTION = 3;
	private final int expressionType;
	
	
	
	public RewriteExpression(int expressionType, Context ctx, Iterator []  subIters, QueryLocation location) {
		super(ctx, subIters, location);
		this.expressionType = expressionType;
	}

	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack)
			throws MXQueryException {
		throw new RuntimeException("RewriteExpressions should never be copied");
	}

	public Token next() throws MXQueryException {
		throw new RuntimeException("RewriteExpressions should never be called");
	}

	public int getExpressionType() {
		return expressionType;
	}
	
}

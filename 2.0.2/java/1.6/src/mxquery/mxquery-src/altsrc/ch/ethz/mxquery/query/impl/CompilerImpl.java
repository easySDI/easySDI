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
package ch.ethz.mxquery.query.impl;

import ch.ethz.mxquery.contextConfig.CompilerOptions;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.query.XQCompiler;
import ch.ethz.mxquery.query.PreparedStatement;
import ch.ethz.mxquery.query.parser.Parser;
import ch.ethz.mxquery.query.parser.SEParser;

public class CompilerImpl implements XQCompiler {
	
	public PreparedStatement compile(Context ctx, String query, CompilerOptions co) throws MXQueryException{
		
		Parser parser = new SEParser();
		parser.setParallelExecution(co.isParallelExecution());
		
		XDMIterator iter = parser.parse(ctx, query, co);	
		int exprCategory = iter.getExpressionCategoryType(co.isScripting());
		
		if (exprCategory == XDMIterator.EXPR_CATEGORY_UPDATING && ! ctx.getStores().isUseUpdateStores() )
			throw new MXQueryException(ErrorCodes.A0009_EC_EVALUATION_NOT_POSSIBLE,"Updating expression, but updateable variables are not used - please set the relevant parameter in the context or on the command line",iter.getLoc());
		
		iter.setResettable(false);
		return new PreparedStatementImpl(ctx, iter,co);
	}
}

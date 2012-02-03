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

package ch.ethz.mxquery.iterators.scripting;

import java.util.Vector;

import ch.ethz.mxquery.bindings.WindowFactory;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.exceptions.StaticException;
import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.VariableHolder;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.util.KXmlSerializer;

/**
 * ASSIGNEMENT
 * 
 * @author David Alexander Graf
 * 
 */
public class AssignIterator extends CurrentBasedIterator {
	private QName varName;

	/**
	 * Constructor
	 * 
	 * @param varName
	 *            variable name (where the <code>sourceExpr</code> is assigned
	 *            to)
	 * @param sourceExpr
	 * @throws MXQueryException
	 */
	public AssignIterator(Context ctx, QName varName, XDMIterator sourceExpr, QueryLocation location)
			throws MXQueryException {
		super(ctx, location);
		exprCategory = XDMIterator.EXPR_CATEGORY_SEQUENTIAL;
		this.varName = varName;
		this.subIters = new XDMIterator[] { sourceExpr };
	}

	public int getEventType() {
		if (this.called > 0) {
			return Type.END_SEQUENCE;
		} else {
			return Type.START_SEQUENCE;
		}
	}
	
	public Token next() throws MXQueryException {
		if (this.called == 0) {
			VariableHolder var = this.context.getVariable(this.varName);
			if (var == null) {
				throw new DynamicException(ErrorCodes.E0002_DYNAMIC_NO_VALUE_ASSIGNED,
						"Variable of an Assignment Expression is not declared!", loc);
			}
			if (!var.isAssignable())
				throw new DynamicException(ErrorCodes.P0001_DYNAMIC_ERROR_IN_SCRIPTING,"Cannot assign a constant or a variable bound in a FLWOR expression",loc);
			Window wi = WindowFactory.getNewWindow_Eager(this.context, this.subIters[0]);
			var.setIter(wi);
			//var.setValue(wi);
		}
		this.called++;
		return Token.END_SEQUENCE_TOKEN;
	}

	protected KXmlSerializer createIteratorStartTag(KXmlSerializer serializer)
			throws Exception {
		super.createIteratorStartTag(serializer);
		serializer.attribute(null, "varname", this.varName.toString());
		return serializer;
	}
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		return new AssignIterator(context, varName.copy(), subIters[0],loc);
	}
	
	
	
	protected void checkExpressionTypes(boolean isScripting) throws MXQueryException {
		if (subIters[0].getExpressionCategoryType(isScripting) == XDMIterator.EXPR_CATEGORY_UPDATING) {
			throw new StaticException(
					ErrorCodes.U0002_UPDATE_STATIC_NONUPDATING_EXPRESSION_NOT_ALLOWED_HERE,
					"The right-hand side of an assignment expression must not be an updating expression!", loc);
		}
	}
}

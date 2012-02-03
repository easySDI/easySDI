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

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.exceptions.StaticException;
import ch.ethz.mxquery.model.Iterator;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.util.KXmlSerializer;

/**
 * BLOCK DECLARATION
 * 
 * @author David Alexander Graf
 * 
 */
public class BlockDeclIterator extends Iterator {
	private QName[] varNames;


	/**
	 * Constructor
	 * 
	 * @param sourceExprs
	 *            Array of initial values for the declared variable (the array
	 *            contains <code>null</code> if a variable has no initial
	 *            value)
	 * @param varNames
	 *            array of declared names
	 * @throws MXQueryException
	 */
	public BlockDeclIterator(Context ctx, XDMIterator[] sourceExprs, QName[] varNames, QueryLocation location)
			throws MXQueryException {
		super(ctx, location);
		this.subIters = sourceExprs;
		this.varNames = varNames;
	}
	
	public Token next() throws MXQueryException {
		if (this.called == 0) {
			for (int i = 0; i < this.varNames.length; i++) {
				if (this.subIters[i] != null)
					context.bindVariableValue(this.varNames[i], this.subIters[i]);
			}
		}
		this.called++;
		return Token.END_SEQUENCE_TOKEN;
	}


	protected void resetImpl() throws MXQueryException {
		this.called = 0;
		for (int i = 0; i < this.subIters.length; i++) {
			// Must be checked because a declared variable need not to be
			// initialized.
			if (this.subIters[i] != null) {
				this.subIters[i].reset();
			}
//			this.context.destroyVariable(this.varNames[i]);
		}
	}

	public void setContext(Context context, boolean recursive) throws MXQueryException {
		this.context = context;
		for (int i = 0; i < this.subIters.length; i++) {
			if (this.subIters[i] != null) {
				this.subIters[i].setContext(context, true);
			}
		}
	}

	public void setResettable(boolean r) throws MXQueryException {
		this.resettable = r;
		for (int i = 0; i < this.subIters.length; i++) {
			if (this.subIters[i] != null) {
				this.subIters[i].setResettable(r);
			}
		}
	}

	public KXmlSerializer traverseIteratorTree(KXmlSerializer serializer)
			throws Exception {
		this.createIteratorStartTag(serializer);
		for (int i = 0; i < this.varNames.length; i++) {
			serializer.startTag(null, "declaration");
			serializer.attribute(null, "varname", this.varNames[i].toString());
			if (this.subIters[i] != null) {
				this.subIters[i].traverseIteratorTree(serializer);
			}
			serializer.endTag(null, "declaration");
		}
		this.createIteratorEndTag(serializer);
		return serializer;
	}
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		return new BlockDeclIterator(context, subIters, Iterator.copyQNames(varNames), loc);
	}

	protected void checkExpressionTypes(boolean isScripting) throws MXQueryException {
		if (subIters != null && subIters.length > 0) {
			for (int i=0;i<subIters.length;i++) {
				if (subIters[i] == null)
					continue;
				int currType = subIters[i].getExpressionCategoryType(isScripting);
				if (currType==EXPR_CATEGORY_UPDATING) {
						throw new StaticException(ErrorCodes.U0001_UPDATE_STATIC_UPDATING_EXPRESSION_NOT_ALLOWED_HERE,
					"No updating expressions allowed here", subIters[i].getLoc());
				}
			}
		}
	}
}

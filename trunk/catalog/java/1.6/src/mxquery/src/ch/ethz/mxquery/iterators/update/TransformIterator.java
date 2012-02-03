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

package ch.ethz.mxquery.iterators.update;

import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.contextConfig.XQStaticContext;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.exceptions.StaticException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.iterators.SequenceTypeIterator;
import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.Iterator;
import ch.ethz.mxquery.model.VariableHolder;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.model.updatePrimitives.PendingUpdateList;
import ch.ethz.mxquery.model.updatePrimitives.UpdateableStore;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.util.KXmlSerializer;
import ch.ethz.mxquery.util.Set;

/**
 * Represents the expression TRANSFORM.
 * 
 * @author David Alexander Graf
 * 
 */
public class TransformIterator extends CurrentBasedIterator {

	private QName[] copyVars;
	private XDMIterator modifyExpr;
	private XDMIterator returnExpr;
	private Set sources;


	/**
	 * Constructor
	 * 
	 * @param copyExprs
	 *            copy expression
	 * @param copyVars
	 *            variables in which the copy expression are safed.
	 * @param modifyExpr
	 *            modifying expression
	 * @param returnExpr
	 *            return expression
	 */
	public TransformIterator(Context ctx, XDMIterator[] copyExprs, QName[] copyVars,
			XDMIterator modifyExpr, XDMIterator returnExpr, QueryLocation location) {
		super(ctx, copyExprs, location);
		this.copyVars = copyVars;
		this.modifyExpr = modifyExpr;
		this.returnExpr = returnExpr;
		sources = new Set();
	}

	/**
	 * Initializes the copy variables (sets the copy expressions to the
	 * corresponding variable):
	 * 
	 * @throws MXQueryException
	 */
	private void initializeCopyVars() throws MXQueryException {
		TypeInfo singleNodeTypeTI = new TypeInfo(Type.NODE,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
		for (int i = 0; i < this.subIters.length; i++) {
			SequenceTypeIterator seqCheck = new SequenceTypeIterator(singleNodeTypeTI,true, false, context,loc);
			seqCheck.setSubIters(this.subIters[i]);
			XDMIterator stIt = new StripTypeIterator(context,new XDMIterator[]{seqCheck},loc);
			Window itCopy = null;
			try {
				
				UpdateableStore newSource = context.getStores().createUpdateableStore(null,stIt,true, false);
				newSource.materialize();
				itCopy = newSource.getIterator(context);
				
				if (!sources.contains(newSource.getURI()))
						sources.add(newSource.getURI());
			} catch (TypeException te) {
				if (te.getErrorCode().equals(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE))
					throw new TypeException(ErrorCodes.U0013_UPDATE_TYPE_TRANSFORM_SOURCE_SINGLE_NODE_EXPECTED, "Single node expected in result of copy", loc);
				else
					throw te;
			}
			VariableHolder var = this.context.getVariable(this.copyVars[i]);
			var.setIter(itCopy);
		}
	}

	/**
	 * Excecutes updates.
	 * 
	 * @throws MXQueryException
	 */
	private void computeUpdate() throws MXQueryException {
		this.modifyExpr.next();
		PendingUpdateList pul = this.modifyExpr.getPendingUpdateList();
		if (pul.checkStoreUsage(sources))
			pul.apply();
		else
			throw new DynamicException(ErrorCodes.U0014_UPDATE_DYNAMIC_TRANSFORM_WRONG_VARIABLE_MODIFIED,"Attemp to modify data that was not copied in transform",loc);
	}

	/**
	 * Checks if the return expression is correct.
	 * 
	 * @throws MXQueryException
	 */
	public void setResettable(boolean r) throws MXQueryException {
		this.resettable = r;
		for (int i = 0; i < this.subIters.length; i++) {
			this.subIters[i].setResettable(r);
		}
		this.modifyExpr.setResettable(r);
		this.returnExpr.setResettable(this.resettable);
	}

	protected void freeResources(boolean restartable) throws MXQueryException {
//		FIXME
//		throw new RuntimeException("David fix me");
//		super.freeResources();
//		this.myContext.freeResources();
	}

	public Token next() throws MXQueryException {
		if (this.endOfSeq) {
			return Token.END_SEQUENCE_TOKEN;
		} else if (this.called == 0) {
			this.initializeCopyVars();
			this.computeUpdate();
			this.current = this.returnExpr;
		}
		this.called++;

		Token retToken = this.returnExpr.next();
		if (retToken.getEventType() == Type.END_SEQUENCE) {
			this.freeResources(true);
		}
		return retToken;
	}

	protected void resetImpl() throws MXQueryException {
		super.resetImpl();
		for (int i = 0; i < this.subIters.length; i++) {
			this.subIters[i].reset();
		}
		this.modifyExpr.reset();
		this.returnExpr.reset();
	}

	public XDMIterator[] getAllSubIters() {
		XDMIterator[] iter = new XDMIterator[this.subIters.length + 2];
		iter[0] = this.modifyExpr;
		iter[1] = this.returnExpr;
		for (int i = 0; i < this.subIters.length; i++) {
			iter[i + 2] = this.subIters[i];
		}
		return iter;
	}

	public KXmlSerializer traverseIteratorTree(KXmlSerializer serializer)
			throws Exception {
		this.createIteratorStartTag(serializer);
		for (int i = 0; i < this.subIters.length; i++) {
			serializer.startTag(null, "copy");
			serializer.attribute(null, "varname", this.copyVars[i].toString());
			this.subIters[i].traverseIteratorTree(serializer);
			serializer.endTag(null, "copy");
		}
		serializer.startTag(null, "modify");
		this.modifyExpr.traverseIteratorTree(serializer);
		serializer.endTag(null, "modify");
		serializer.startTag(null, "return");
		this.returnExpr.traverseIteratorTree(serializer);
		serializer.endTag(null, "return");
		this.createIteratorEndTag(serializer);
		return serializer;
	}

	public XDMIterator copy(Context parentIterContext,
			XQStaticContext prevParentIterContext, boolean copyContext,
			Vector nestedPredCtxStack) throws MXQueryException {
		// always create own context for Transform
		return super.copy(parentIterContext, prevParentIterContext, true,
				nestedPredCtxStack);
	}

	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		return new TransformIterator(context, 
				subIters, 
				Iterator.copyQNames(copyVars), 
				modifyExpr.copy(context, null, false, nestedPredCtxStack), 
				returnExpr.copy(context, null, false, nestedPredCtxStack), loc);
	}
	
	protected void checkExpressionTypes() throws MXQueryException {
		// copy expression need to be simple
		super.checkExpressionTypes();
		// return: simple
		int retExprType = returnExpr.getExpressionCategoryType(isScripting);
		int modExprType = modifyExpr.getExpressionCategoryType(isScripting);
		if (!isScripting) {
			exprCategory = EXPR_CATEGORY_SIMPLE;
			if (retExprType != XDMIterator.EXPR_CATEGORY_SIMPLE)
				throw new StaticException(ErrorCodes.U0001_UPDATE_STATIC_UPDATING_EXPRESSION_NOT_ALLOWED_HERE, "Return expression in Transform must not be updating", loc);
			// modify: updating or vacuous
			if (!(modExprType == XDMIterator.EXPR_CATEGORY_UPDATING || modExprType == XDMIterator.EXPR_CATEGORY_VACUOUS))
				throw new StaticException(ErrorCodes.U0002_UPDATE_STATIC_NONUPDATING_EXPRESSION_NOT_ALLOWED_HERE,"Modify expression must be updating or vacuous", loc);
		} else {
			if (modExprType == EXPR_CATEGORY_SEQUENTIAL)
				throw new StaticException(ErrorCodes.A0009_EC_EVALUATION_NOT_POSSIBLE, "Modify expression in Transform must not be sequential", loc);
			if (modExprType == EXPR_CATEGORY_SIMPLE) {
				exprCategory = retExprType;
			} else {
				if (modExprType == EXPR_CATEGORY_SEQUENTIAL)
					throw new StaticException(ErrorCodes.A0009_EC_EVALUATION_NOT_POSSIBLE, "REturn expression in Transform must not be sequential if modify expression is not simple", loc);				
				exprCategory = EXPR_CATEGORY_UPDATING;
			}
		}
	}	
	
}

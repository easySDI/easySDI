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

package ch.ethz.mxquery.model;

import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.contextConfig.XQDynamicContext;
import ch.ethz.mxquery.contextConfig.XQStaticContext;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.exceptions.StaticException;
import ch.ethz.mxquery.model.updatePrimitives.PendingUpdateList;
import ch.ethz.mxquery.util.ContextPair;
import ch.ethz.mxquery.util.KXmlSerializer;
import ch.ethz.mxquery.util.Utils;

/**
 * 
 * @author Matthias Braun
 * 
 * This is the base interface for all iterator implementations.
 * 
 */
public abstract class Iterator implements XDMIterator {
	
	/**
	 * Expression types as of the XQSF draft
	 */
	protected static final int EXPR_CATEGORY_UNDETERMINED = 0;
	/***************************************************************************
	 * variables *
	 **************************************************************************/
	protected XDMIterator[] subIters = null;

	protected Iterator[] preds = null;

	protected Context context = null;

	protected boolean resettable = false;

	protected PendingUpdateList pendingUpdateList;

	protected QueryLocation loc = null;
	
	protected int called = 0;
	protected boolean endOfSeq = false;
	
	protected int depth = 0;
	
	protected boolean constModePreserve = true;
	
	protected boolean isScripting = false;

	protected int exprCategory = EXPR_CATEGORY_UNDETERMINED;
	
	public Iterator(Context ctx, QueryLocation location) {
		this.context = ctx;
		called = 0;
		loc = location;
	}

	public Iterator(Context ctx, int minExpected, XDMIterator[] subIters, QueryLocation location)
			throws IllegalArgumentException {
		if (subIters == null || subIters.length == 0
				|| subIters.length < minExpected) {
			throw new IllegalArgumentException();
		}
		this.subIters = subIters;
		this.context = ctx;
		called = 0;
		loc = location;
	}

	public Iterator(Context ctx, XDMIterator[] subIters, int expected, QueryLocation location)
			throws IllegalArgumentException {
		if (subIters == null || subIters.length == 0
				|| subIters.length != expected) {
			throw new IllegalArgumentException();
		}
		this.subIters = subIters;
		this.context = ctx;
		called = 0;
		loc = location;		
	}

	public Iterator(Context ctx, XDMIterator[] subIters, QueryLocation location) {
		this.subIters = subIters;
		this.context = ctx;
		called = 0;
		loc = location;
	}

	// predicate handling
	public void addPredicates(Iterator[] predIters) {
		preds = predIters;
	}

	/* (non-Javadoc)
	 * @see ch.ethz.mxquery.model.XDMIterator#getPredicates()
	 */
	public Iterator[] getPredicates() {
		return preds;
	}

	public void removePredicates() {
		preds = null;
	}

	public boolean hasPredicates() {
		return preds != null && preds.length > 0;
	}

	/* (non-Javadoc)
	 * @see ch.ethz.mxquery.model.XDMIterator#setContext(ch.ethz.mxquery.contextConfig.Context, boolean)
	 */
	public void setContext(Context context, boolean recursive) throws MXQueryException {
		if (context != null) {
			this.context = context;
			if (recursive && subIters != null)
				for (int i=0;i<subIters.length;i++)
					subIters[i].setContext(context, recursive);
		}
	}
	
	/* (non-Javadoc)
	 * @see ch.ethz.mxquery.model.XDMIterator#setSubIters(java.util.Vector)
	 */
	public void setSubIters(Vector subIt) {
		subIters = new Iterator[subIt.size()];
		for (int i =0; i < subIt.size(); i++ ) {
			subIters[i] = (Iterator)subIt.elementAt(i);
		}	
	}

	/* (non-Javadoc)
	 * @see ch.ethz.mxquery.model.XDMIterator#setSubIters(ch.ethz.mxquery.model.Iterator[])
	 */
	public void setSubIters(XDMIterator[] subIt) throws MXQueryException {
		subIters = subIt;
	}	
	
	/* (non-Javadoc)
	 * @see ch.ethz.mxquery.model.XDMIterator#setSubIters(ch.ethz.mxquery.model.Iterator)
	 */
	public void setSubIters(XDMIterator subIt) {
		if (subIt != null) {
			subIters = new XDMIterator[1];
			subIters[0] = subIt;
		}	
	}		

	/* (non-Javadoc)
	 * @see ch.ethz.mxquery.model.XDMIterator#getSubIters()
	 */
	public XDMIterator[] getSubIters() throws MXQueryException {
		return subIters;
	}	
	
	/* (non-Javadoc)
	 * @see ch.ethz.mxquery.model.XDMIterator#setReturnExpr(ch.ethz.mxquery.model.Iterator)
	 */
	public void setReturnExpr(XDMIterator retExpr) {}
	
	/* (non-Javadoc)
	 * @see ch.ethz.mxquery.model.XDMIterator#setOrderByExpr(ch.ethz.mxquery.model.XDMIterator)
	 */
	public void setOrderByExpr(XDMIterator orderByExpr){}
	
	/* (non-Javadoc)
	 * @see ch.ethz.mxquery.model.XDMIterator#setWhereExpr(ch.ethz.mxquery.model.Iterator)
	 */
	public void setWhereExpr(XDMIterator whereExpr){}
	
	/* (non-Javadoc)
	 * @see ch.ethz.mxquery.model.XDMIterator#setParam(java.lang.String, java.lang.String)
	 */
	public void setParam(String name, String value) throws MXQueryException {
		throw new StaticException(ErrorCodes.A0002_EC_NOT_SUPPORTED, "setParam(name, value) needs to be implemented for \"" + this.getClass().getName() + "\"", loc);
	}

	/* (non-Javadoc)
	 * @see ch.ethz.mxquery.model.XDMIterator#getContext()
	 */
	public Context getContext() {
		return context;
	}


	// If an iterator is resetable has significant influence on the garbage
	// collection.
	/* (non-Javadoc)
	 * @see ch.ethz.mxquery.model.XDMIterator#setResettable(boolean)
	 */
	public void setResettable(boolean r) throws MXQueryException {
		resettable = r;
		if (subIters != null) {
			for (int i = 0; i < subIters.length; i++) {
				subIters[i].setResettable(r);
			}
		}
	}

	/* (non-Javadoc)
	 * @see ch.ethz.mxquery.model.XDMIterator#isResettable()
	 */
	public boolean isResettable() throws MXQueryException {
		return resettable;
	}
	
	/* (non-Javadoc)
	 * @see ch.ethz.mxquery.model.XDMIterator#isExprParameter(int, boolean)
	 */
	public boolean isExprParameter(int valueToCheck, boolean recursive) {
		return false;
	}
	
	/* (non-Javadoc)
	 * @see ch.ethz.mxquery.model.XDMIterator#reset()
	 */
	public final void reset() throws MXQueryException {
		if (resettable) {
			resetImpl();
		} else {
			throw new DynamicException(ErrorCodes.A0009_EC_EVALUATION_NOT_POSSIBLE, "Reset is not allowed for this iterator", loc);
		}
	}

	/**
	 * Resets the Iterator to its original state. Iterator-specific
	 * implementation is done here.
	 * @throws MXQueryException 
	 * 
	 * @throws MXQueryException
	 */
	protected void resetImpl() throws MXQueryException {
		called = 0;
		this.endOfSeq = false;
		if (exprCategory == EXPR_CATEGORY_UPDATING && pendingUpdateList != null) {
			pendingUpdateList.clear();
		}
		if (subIters != null) {
			for (int i = 0; i < subIters.length; i++) {
				subIters[i].reset();
			}
		}
		depth = 0;
	}

	/* (non-Javadoc)
	 * @see ch.ethz.mxquery.model.XDMIterator#close(boolean)
	 */
	public final void close(boolean restartable) throws MXQueryException {
		this.freeResources(restartable);
		final XDMIterator[] iters = this.getAllSubIters();
		for (int i = 0; i < iters.length; i++) {
			if (iters[i] != null)
				iters[i].close(restartable);
		}
	}

	/**
	 * Frees the resources of the current iterator.
	 * @param restartable make the iterator restartable (otherwise the iterator will stay closed with additional next calls)
	 * @throws MXQueryException 
	 * 
	 */
	protected void freeResources(boolean restartable) throws MXQueryException {
		if (restartable)
			called = 0;
		else
			this.endOfSeq = true;	
	}

	/* (non-Javadoc)
	 * @see ch.ethz.mxquery.model.XDMIterator#getReturnType()
	 */
	public TypeInfo getStaticType(){
		return new TypeInfo(Type.ITEM,Type.OCCURRENCE_IND_ZERO_OR_MORE,null,null);
	}

	/* (non-Javadoc)
	 * @see ch.ethz.mxquery.model.XDMIterator#getExpressionCategoryType(boolean)
	 */
	
	public int getExpressionCategoryType(boolean scripting) throws MXQueryException{
		this.isScripting = scripting;
		if (exprCategory == EXPR_CATEGORY_UNDETERMINED)
			checkExpressionTypes();
		return getExprTypeImpl();		
	}
	/**
	 * Perform the actual checking if the (sub)iterators are consistent in terms of the expression category rules laid out by the XQUF and the XQSF
	 * The default implementation follows the XQuery 1.0/1.1 rules and just accepts vacuous and simple statements 
	 * @param isScripting TODO
	 * @throws MXQueryException
	 */
	protected void checkExpressionTypes() throws MXQueryException {
		if (!isScripting)
			checkExprSimpleOnly(subIters,isScripting);
			if (exprCategory == EXPR_CATEGORY_UNDETERMINED)
				exprCategory = EXPR_CATEGORY_SIMPLE;
		else {
			boolean isUpdating = checkExprNoSequential(subIters,isScripting);
			if (isUpdating)
				exprCategory = EXPR_CATEGORY_UPDATING;
			else 
				exprCategory = EXPR_CATEGORY_SIMPLE;
		}
	}
	
	protected static void checkExprSimpleOnly(XDMIterator [] its,boolean isScripting) throws MXQueryException {
		if (its != null && its.length > 0) {
			for (int i=0;i<its.length;i++) {
				int currType = its[i].getExpressionCategoryType(isScripting);
				if (!(currType==EXPR_CATEGORY_SIMPLE || currType ==EXPR_CATEGORY_VACUOUS)) {
						throw new StaticException(ErrorCodes.U0001_UPDATE_STATIC_UPDATING_EXPRESSION_NOT_ALLOWED_HERE,
					"Only simple expressions allowed here", its[i].getLoc());
				}
			}
		}
	}
	/**
	 * Check if any of the iterators contains a sequential expression
	 * @param its Iterators to check
	 * @return true, if any of the iterators is an updating function, false if only simple/vacuous
	 * @throws MXQueryException Static exception if a sequential expression is found
	 */
	protected static boolean checkExprNoSequential(XDMIterator [] its,boolean isScripting) throws MXQueryException {
		boolean hasUpdating = false;
		if (its != null && its.length > 0) {
			for (int i=0;i<its.length;i++) {
				int currType = its[i].getExpressionCategoryType(isScripting);
				if (currType == XDMIterator.EXPR_CATEGORY_SEQUENTIAL) {
						throw new StaticException(ErrorCodes.A0009_EC_EVALUATION_NOT_POSSIBLE,
					"No sequential expressions allowed here", its[i].getLoc());
				}
				if (currType == XDMIterator.EXPR_CATEGORY_UPDATING)
					hasUpdating = true;
			}
		}
		return hasUpdating;
	}
	
	/**
	 * Common implementation for "mixed" environment, as outlined in the XQSF draft
	 * @param its Iterators to check
	 * @return the actual return type, if consistent
	 * @throws MXQueryException a static exception related to the inconsistency, if one exists
	 */
	
	protected static int checkExprDefault(XDMIterator [] its, boolean isScripting) throws MXQueryException {
		if (its == null || its.length == 0)
			return XDMIterator.EXPR_CATEGORY_VACUOUS;
		else {
			int expType = its[0].getExpressionCategoryType(isScripting);
			
			for (int i=1;i<its.length;i++) {
				int curType = its[i].getExpressionCategoryType(isScripting);
				if (expType != curType) {
					switch (expType) {
					case XDMIterator.EXPR_CATEGORY_VACUOUS:
						expType = curType;
						break;
	
					case XDMIterator.EXPR_CATEGORY_UPDATING:
						if (curType == XDMIterator.EXPR_CATEGORY_SEQUENTIAL || curType == XDMIterator.EXPR_CATEGORY_SIMPLE && !isScripting)
							throw new StaticException(ErrorCodes.U0001_UPDATE_STATIC_UPDATING_EXPRESSION_NOT_ALLOWED_HERE,"If one expression in a sequence is updating, all others have to be, too (or empty)", its[i].getLoc());
						break;
					case XDMIterator.EXPR_CATEGORY_SEQUENTIAL:
						if (curType == XDMIterator.EXPR_CATEGORY_UPDATING) {
							throw new StaticException(ErrorCodes.U0002_UPDATE_STATIC_NONUPDATING_EXPRESSION_NOT_ALLOWED_HERE,"If one expression in a sequence is sequential, no other can be updating", its[i].getLoc());
						}
						break;
				
					case XDMIterator.EXPR_CATEGORY_SIMPLE:
						switch (curType) {
							case XDMIterator.EXPR_CATEGORY_SEQUENTIAL:
								expType = XDMIterator.EXPR_CATEGORY_SEQUENTIAL;
								break;
							case XDMIterator.EXPR_CATEGORY_UPDATING:
								if (!isScripting)
								throw new StaticException(ErrorCodes.U0001_UPDATE_STATIC_UPDATING_EXPRESSION_NOT_ALLOWED_HERE,"If one expression in a sequence is simple, no other can be updating", its[i].getLoc());
						}
						break;
					}
				}
			}
			return expType;
		}

	}	
	
	
	/**
	 * Define the expression category type
	 * @return expression category type for this expression
	 */
	protected int getExprTypeImpl() {
		return exprCategory;
	}
	

	/* (non-Javadoc)
	 * @see ch.ethz.mxquery.model.XDMIterator#getAllSubIters()
	 */
	public XDMIterator[] getAllSubIters() {
		if (subIters != null) {
			return subIters;
		} else {
			return new Iterator[] {};
		}
	}

	/* (non-Javadoc)
	 * @see ch.ethz.mxquery.model.XDMIterator#getAllSubItersRecursive()
	 */
	public Vector getAllSubItersRecursive() {
		Vector res = new Vector();
		XDMIterator [] direct = getAllSubIters();
		for (int i=0;i<direct.length;i++) {
			res.addElement(direct[i]);
			Vector rec = direct[i].getAllSubItersRecursive();
			for (int j=0;j<rec.size();j++)
				res.addElement(rec.elementAt(j));
		}
		return res;
	}
	
	public KXmlSerializer traverseIteratorTree(KXmlSerializer serializer)
			throws Exception {
		createIteratorStartTag(serializer);
		serializer.startTag(null, "subIters");
		XDMIterator[] subIters = getAllSubIters();
		for (int i = 0; i < subIters.length; i++) {
			subIters[i].traverseIteratorTree(serializer);
		}
		serializer.endTag(null, "subIters");
		createIteratorEndTag(serializer);
		return serializer;
	}

	public KXmlSerializer traverse(KXmlSerializer serializer) {
		try {
			return traverseIteratorTree(serializer);
		} catch (Exception err) {
			throw new RuntimeException(err.toString());
		}
	}

	protected KXmlSerializer createIteratorStartTag(KXmlSerializer serializer)
			throws Exception {
		serializer.startTag(null, Utils
				.getSimpleClassName(getClass().getName()));
		return serializer;
	}

	protected KXmlSerializer createIteratorEndTag(KXmlSerializer serializer)
			throws Exception {
		serializer.endTag(null, Utils.getSimpleClassName(getClass().getName()));
		return serializer;
	}
	
	/* (non-Javadoc)
	 * @see ch.ethz.mxquery.model.XDMIterator#isOpen()
	 */
	public boolean isOpen() {
		return called != 0;
	}

	/* (non-Javadoc)
	 * @see ch.ethz.mxquery.model.XDMIterator#getPendingUpdateList()
	 */
	public PendingUpdateList getPendingUpdateList() throws MXQueryException {
		if (this.pendingUpdateList == null) {
			this.pendingUpdateList = new PendingUpdateList(loc);
		}
		if (isScripting)
			mergeChildPULS();
		return this.pendingUpdateList;
	}
	/**
	 * Merge the the PULs of the child iterators (i.e. subiters)
	 * This methods needs to be overwritten, if not all sub-iterators should contribute,
	 * or if additional iterators need to contribute;
	 * @throws MXQueryException 
	 */
	protected void mergeChildPULS() throws MXQueryException {
		if (subIters != null && subIters.length > 0) {
			for (int i=0;i<subIters.length;i++) {
				if (subIters[i].getExpressionCategoryType(true) == EXPR_CATEGORY_UPDATING) {
					this.pendingUpdateList.merge(subIters[i].getPendingUpdateList());
				}
			}
		}
		
	}

	/**
	 * Method interface for the Iterators. 
	 * Each iterator must implement this, to ensure that it is copied correctly.
	 * 
	 * @param context	The context for the copy (already the correct one)
	 * @param subIters	The subIterator for the copy (already the correct ones)
	 * @param nestedPredCtxStack TODO
	 * @return			A copy of this Iterator
	 * 
	 * @throws MXQueryException
	 */
	protected abstract XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException;
	
	/* (non-Javadoc)
	 * @see ch.ethz.mxquery.model.XDMIterator#copy(ch.ethz.mxquery.contextConfig.Context, ch.ethz.mxquery.contextConfig.XQStaticContext, boolean, java.util.Vector)
	 */

	public XDMIterator copy(Context parentIterContext, XQStaticContext prevParentIterContext, boolean copyContext, Vector nestedPredCtxStack) throws MXQueryException {
		// initialize new context and subIterators
		Context newContext = parentIterContext;
		XDMIterator[] newSubIters = null;
		if (subIters != null) {
			newSubIters = new Iterator[subIters.length]; 
		}
		
		if (context == null) {
			throw new RuntimeException("DEBUG: Clone problem - context is null");
		}
		
		// copy the context
		
		if (prevParentIterContext == context) {
			newContext = parentIterContext;
		}
		
		if (copyContext) {
			newContext = context.copy();
			newContext.setParent(parentIterContext);
			
		}
		
		// copy the subIterators
		if (subIters != null) {
			for (int i=0; i<subIters.length; i++) {
				XQStaticContext subCtx = subIters[i].getContext();
				boolean inNestedStack = false;
				ContextPair cp = null;
				for (int k = 0;k<nestedPredCtxStack.size();k++) {
					cp = (ContextPair)nestedPredCtxStack.elementAt(k);
					if (cp.prevContext == subCtx) {
						inNestedStack = true;
						break;
					}		
				}
				if (subCtx != null) {
					if (context.getParent() == subCtx) {
						// Location steps have "inverted nesting"
						if (inNestedStack) {
							newSubIters[i] = subIters[i].copy(cp.newContext, cp.prevContext, false, nestedPredCtxStack);
						}
						else {
							newSubIters[i] = subIters[i].copy(parentIterContext, this.context, true, nestedPredCtxStack);
						}
						newContext = new Context(newSubIters[i].getContext());
					} else {
						if (subCtx == prevParentIterContext){
							newSubIters[i] = subIters[i].copy(parentIterContext, this.context, false, nestedPredCtxStack);
						} 
						else {
							if (subCtx == context) 
								newSubIters[i] = subIters[i].copy(newContext, this.context, false, nestedPredCtxStack);
							else {	
								if (inNestedStack) {
									newSubIters[i] = subIters[i].copy(cp.newContext, cp.prevContext, false, nestedPredCtxStack);
								}
								else {
									newSubIters[i] = subIters[i].copy(newContext, this.context, true, nestedPredCtxStack);
								}		
							}					
						}
					}
				} else {
					throw new RuntimeException("DEBUG: Clone problem - context is null");
				}
			}
		}
		Iterator cp = (Iterator)copy(newContext, newSubIters, nestedPredCtxStack);
		cp.exprCategory = this.exprCategory;
		return cp;
	}	
	
	public static XDMIterator[] copyIterators(Context context, XDMIterator[] iters, Vector nestedPredCtxStack) throws MXQueryException {
		if (iters == null) {
			return null;
		}
		
		XDMIterator[] newSubs = new XDMIterator[iters.length];
		for (int i=0; i<newSubs.length; i++) {
			newSubs[i] = iters[i].copy(context, null, false, nestedPredCtxStack);
		}
		
		return newSubs;
	}
	
	public static String[] copyStrings(String[] strings) {
		if (strings == null) {
			return null;
		}
		
		String[] newStrings = new String[strings.length];
		for (int i=0; i<strings.length; i++) {
			newStrings[i] = strings[i];
		}
		
		return newStrings;
	}
	
	public static int[] copyInts(int[] ints) {
		if (ints == null) {
			return null;
		}
		
		int[] newInts = new int[ints.length];
		for (int i=0; i<ints.length; i++) {
			newInts[i] = ints[i];
		}
		
		return newInts;
	}
	
	public static TypeInfo[] copyTypeInfos(TypeInfo[] infos) {
		if (infos == null) {
			return null;
		}
		
		TypeInfo[] newInfos = new TypeInfo[infos.length];
		for (int i=0; i<infos.length; i++) {
			newInfos[i] = infos[i].copy();
		}
		
		return newInfos;
	}
	
	public static QName[] copyQNames(QName[] qnames) {
		if (qnames == null) {
			return null;
		}
		
		QName[] newStrings = new QName[qnames.length];
		for (int i=0; i<qnames.length; i++) {
			newStrings[i] = qnames[i].copy();
		}
		
		return newStrings;
	}	
	
//	public void debug(int block, String pre) {
//		System.out.print("#");
//		for (int i=0; i<block; i++) {
//			System.out.print("\t");
//		}
//		
//		if (pre != null) {
//			System.out.print(pre + ": ");
//		}
//		
//		System.out.println(this + " (" + (context==null?-1:context.hashCode()) + ")");
//		
//		int newblock = block+1;
//		
//		if (subIters != null && subIters.length > 0) {
//			for (int i=0; i<subIters.length; i++) {
//				subIters[i].debug(newblock, null);	
//			}
//		}
//	}
	/* (non-Javadoc)
	 * @see ch.ethz.mxquery.model.XDMIterator#getLoc()
	 */
	public QueryLocation getLoc() {
		return loc;
	}

	/* (non-Javadoc)
	 * @see ch.ethz.mxquery.model.XDMIterator#setLoc(ch.ethz.mxquery.exceptions.QueryLocation)
	 */
	public void setLoc(QueryLocation loc) {
		this.loc = loc;
	}
	/* (non-Javadoc)
	 * @see ch.ethz.mxquery.model.XDMIterator#isConstModePreserve()
	 */
	public boolean isConstModePreserve() {
		return constModePreserve;
	}
	/* (non-Javadoc)
	 * @see ch.ethz.mxquery.model.XDMIterator#setConstModePreserve(boolean)
	 */
	public void setConstModePreserve(boolean constModePreserve) {
		this.constModePreserve = constModePreserve;
	}
	
	/**
	 * Helper method for either getting a node a given parameter or the context item
	 * @param subIts SubIterators to check
	 * @param pos position of node parameters in the subIts, needs to be last 
	 * @param ctx Context to resolve the item against
	 * @param loc Query Location of the calling iterator
	 * @return Iterator to get node or context item content
	 * @throws MXQueryException
	 * @throws DynamicException
	 */
	protected static XDMIterator getNodeIteratorOrContext(XDMIterator [] subIts, int pos,XQDynamicContext ctx, QueryLocation loc) throws MXQueryException,
			DynamicException {
		XDMIterator it;
		if (subIts != null && subIts.length == pos) {
			it = subIts[pos-1];
		} else {
			VariableHolder contextVarHolder = ctx.getContextItem();
			if (contextVarHolder != null) {
				it = contextVarHolder.getIter();
				if (it == null) 
					throw new DynamicException(ErrorCodes.E0002_DYNAMIC_NO_VALUE_ASSIGNED, "Context Item Iterator not set", loc);
			} else { 
				throw new RuntimeException("Context Item not set");
			}
		}
		return it;
	}
	
}

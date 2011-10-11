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
import ch.ethz.mxquery.contextConfig.XQStaticContext;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.model.updatePrimitives.PendingUpdateList;
import ch.ethz.mxquery.util.KXmlSerializer;
import ch.ethz.mxquery.util.Traversable;

/**
 * The interface of all iterators producing XDM and/or PUL
 * The computation of XDM/PUL is done lazily, whenever possible
 * Iterators form a tree structure, representing the XQuery expressions 
 * @author Peter Fischer
 *
 */

public interface XDMIterator extends Traversable{

	public static final int EXPR_CATEGORY_SIMPLE = 1;
	public static final int EXPR_CATEGORY_UPDATING = 2;
	public static final int EXPR_CATEGORY_SEQUENTIAL = 3;
	public static final int EXPR_CATEGORY_VACUOUS = 4;

	/**
	 * Produces the next XDM token
	 * 
	 * This method typically implements the base functionality of an Iterator and
	 * computes the value of the next token.
	 * @return The XDM token representing the next fragment of the XDM instance
	 * @throws If any error occurs during the computation of this token 
	 */
	public abstract Token next() throws MXQueryException;

	/**
	 * Resets the Iterator to its original state, without discarding expensive state 
	 * 
	 * @throws MXQueryException
	 */
	public abstract void reset() throws MXQueryException;

	/**
	 * Frees the state of the iterator and all subiterators.
	 * @param restartable make the iterator restartable (otherwise the iterator will stay closed with additional next calls)
	 * @throws MXQueryException 
	 * 
	 */
	public abstract void close(boolean restartable) throws MXQueryException;

	/**
	 * Returns the complete Pending Update List (PUL) of this iterator. Is
	 * <code>null</code> for non-updating expressions. Since the PUL is computed gradually (especially in a scripting environment, this function should only be called when the evaluation is complete.
	 * For scripting, this function also merges the PULs of the child iterators, so 
	 * 
	 * @return pending update list
	 * @throws MXQueryException 
	 */
	public abstract PendingUpdateList getPendingUpdateList()
			throws MXQueryException;

	/**
	 * Return the return type if it can be statically determined
	 * 
	 * @return a Type info carrying the most precise static type that could be determined. If no specific type could be determined, the type is ITEM*
	 */
	public abstract TypeInfo getStaticType();

	/**	
	 * Get and (check) the expression category type, as defined in the XQSF draft
	 * Expression categories are: SIMPLE, UPDATING, SEQUENTIAL, VACUOUS
	 * If the categories are incompatible, the related exception is thrown
	 * The check is only done when requested by calling this function
	 * The implementation covers the common case (simple expressions, no checking) and needs to be overwritten for checking and also by updating, sequential and vacuous expressions
	 * @param scripting TODO
	 * @return the most stringent expression type
	 * @throws MXQueryException: if the categories are incompatible, raise the related exception
	 */

	public abstract int getExpressionCategoryType(boolean scripting)
			throws MXQueryException;

	/**
	 * Copies the Iterator. 
	 * The context is copied (if necessary) here.
	 * The subIterators are copied (if existing) here.
	 * 
	 * 
	 * @param parentIterContext		The new parent context 
	 * @param prevParentIterContext The previous context of the parent iterator 
	 * @param copyContext		Copy the context or use the parent context
	 * @param nestedPredCtxStack The Stack of nested predicate context, needed for nested rewritten predicates
	 * @return					A copy of this Iterator
	 * 
	 * @throws MXQueryException	
	 */

	public abstract XDMIterator copy(Context parentIterContext,
			XQStaticContext prevParentIterContext, boolean copyContext,
			Vector nestedPredCtxStack) throws MXQueryException;
	
		
	/**
	 * Set this Iterator (and its subiterators) resetable. 
	 * Resetable iterators may require more space (trading buffered data against recomputation)
	 * @param r true make this iterator resetable, false make it not resetable
	 * @throws MXQueryException
	 */
	public abstract void setResettable(boolean r) throws MXQueryException;
	/**
	 * Retrieve if this iterator is resetable
	 * @return true this iterator is resetable, false it is not
	 * @throws MXQueryException
	 */
	public abstract boolean isResettable() throws MXQueryException;
	/**
	 * Retrieve the (unified) context of this iterators, carrying both dynamic and static information
	 * @return a Context object
	 */
	public abstract Context getContext();
	/**
	 * Assigns a context for this iterator (and possibly for its subIterators), replacing the existing context
	 * @param context the context to assign
	 * @param recursive true perform the assignment also subiteratos, false only replace on the current iterator
	 * @throws MXQueryException
	 */
	public abstract void setContext(Context context, boolean recursive)
	throws MXQueryException;

	/**
	 * Get the location in the XQuery source where this operator comes from
	 * @return the QueryLocation object representing the location
	 */
	public abstract QueryLocation getLoc();
	/**
	 * Set the location in the XQuery source where this operator comes from
	 * @param loc the QueryLocation object representing the location
	 */
	public abstract void setLoc(QueryLocation loc);

	
	public static final int EXPR_PARAM_VARIABLE = 1;
	public static final int EXPR_PARAM_WINDOW = 2;
	public static final int EXPR_PARAM_CHEAPEVAL = 3;
	public static final int EXPR_PARAM_XDMGEN = 4;
	/**
	 * Optimization check 
	 * @param valueToCheck
	 * @param recursive
	 * @return if iterator fulfills the requested criteria
	 */
	public abstract boolean isExprParameter(int valueToCheck, boolean recursive);

	public abstract void setSubIters(XDMIterator[] subIt) throws MXQueryException;

	public abstract void setSubIters(XDMIterator subIt);

	public abstract XDMIterator[] getSubIters() throws MXQueryException;

	public abstract XDMIterator[] getAllSubIters();

	public abstract Vector getAllSubItersRecursive();

	public abstract boolean isOpen();

	public abstract boolean isConstModePreserve();

	public abstract void setConstModePreserve(boolean constModePreserve);

	public KXmlSerializer traverseIteratorTree(KXmlSerializer serializer) throws Exception;

}
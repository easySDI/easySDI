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

import java.util.Enumeration;
import java.util.Vector;

import ch.ethz.mxquery.bindings.WindowFactory;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.contextConfig.XQStaticContext;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.exceptions.StaticException;
import ch.ethz.mxquery.iterators.forseq.ForseqIterator;
import ch.ethz.mxquery.model.CFException;
import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.EmptySequenceIterator;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.util.ContextPair;
import ch.ethz.mxquery.util.KXmlSerializer;

public class FFLWORIterator extends CurrentBasedIterator {	
	protected boolean init;
	protected XDMIterator whereExpr;
	private OrderByIterator orderByExpr;
	protected XDMIterator returnExpr;
	private int nestedLevel = 0;
	private int pos = 0;

	public FFLWORIterator(Context ctx, XDMIterator[] subIters, XDMIterator whereExpr,
			OrderByIterator orderByExpr, XDMIterator returnExpr, QueryLocation location)
			throws MXQueryException {
		super(ctx, location);
				
		this.subIters = subIters;
//		if (whereExpr != null) {
//			this.whereExpr = BooleanIterator.createEBVIterator(whereExpr,subIters[subIters.length-1].getContext());
//		}
		this.whereExpr = whereExpr;
		this.orderByExpr = orderByExpr;
		this.returnExpr = returnExpr;
		// Necessary that the subIters are initialized correct;
		setResettable(false);
	}

	public void setReturnExpr(XDMIterator retExpr) {
		returnExpr = retExpr;
	}
	
	public void setWhereExpr(XDMIterator wExpr) {
		whereExpr = wExpr;
	}
	
	public void setOrderByExpr(OrderByIterator oExpr) {
		orderByExpr = oExpr;
	}
	
	/**
	 * This Adapter is needed to enable break and continue during
	 * return-materialization in order by. The only thing this class does it to
	 * return Iterator.END_SEQUENCE when a break/continue happens => it catches
	 * the error. If a break or catch happend is accessible over getters.
	 * 
	 * @author David Alexander Graf
	 * 
	 */
	public class CFAdapter extends CurrentBasedIterator {
		private boolean breakCatched = false;
		private boolean continueCatched = false;

		public CFAdapter(XDMIterator iter) {
			current = iter;
		}

		public Token next() throws MXQueryException {
			if (endOfSeq) {
				return Token.END_SEQUENCE_TOKEN;
			}
			Token tok;
			try {
				tok = current.next();
			} catch (CFException e) {
				if (e.isBreak() || e.isContinue()) {
					breakCatched = e.isBreak();
					continueCatched = e.isContinue();
					tok = Token.END_SEQUENCE_TOKEN;
					endOfSeq = true;
				} else {
					throw e;
				}
			}
			return tok;
		}

		public boolean isBreakCatched() {
			return breakCatched;
		}

		public boolean isContinueCatched() {
			return continueCatched;
		}

		protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
			return new CFAdapter(current.copy(context, null, false, nestedPredCtxStack));
		}
	}

	/**
	 * Materializes every return result in a Window an sorts them.
	 * 
	 * @return
	 */
	private XDMIterator orderByInit() throws MXQueryException {
		Vector orderElements = new Vector();
		if (!doNextBinding()) {
			freeResources(false);
			current = null;
			return new EmptySequenceIterator(context, loc);
		}
		while (true) {
			// CFAdapter is used to catch break or continue during
			// materialization.
			CFAdapter adapter = new CFAdapter(returnExpr);
			// materialization of an return expression
			

			Window newWindow = WindowFactory.getNewWindow_Eager(context, adapter);
			
			if (exprCategory == EXPR_CATEGORY_UPDATING) {
				this.getPendingUpdateList().merge(this.returnExpr.getPendingUpdateList());
			}

			// generation of the key of the current iteration (materialization
			// of the order by expressions)
			Token[] key = orderByExpr.getCurrentKey();
			OrderByIterator.OrderElement oe = orderByExpr.new OrderElement(
					newWindow, key);
			orderElements.addElement(oe);
			orderByExpr.reset();
			if (adapter.isBreakCatched() || !doNextBinding()) {
				freeResources(false);
				break;
			} else {
				returnExpr.reset();
			}
		}
		OrderByIterator.OrderElement[] arrOE = new OrderByIterator.OrderElement[orderElements
				.size()];
		orderElements.copyInto(arrOE);
		orderByExpr.setup(arrOE);
		return orderByExpr;
	}

	protected Token orderByNext() throws MXQueryException {
		if (!init) {
			init = true;
			current = orderByInit();
		}
		return current.next();
	}

	protected Token normalNext() throws MXQueryException {
		if (endOfSeq) {
			return Token.END_SEQUENCE_TOKEN;
		}
		Token tok;
		if (!init) {
			init = true;
			super.current = returnExpr;

			if (!doNextBinding()) {
				freeResources(false);
				current = null;
				return Token.END_SEQUENCE_TOKEN;
			} else {
				pos++;
				context.setPosition(pos);
			}
		}
		while (true) {
			try {
				tok = returnExpr.next();
			} catch (CFException e) {
				if (e.isBreak()) {
					returnExpr.close(false);
					current = null;
					return Token.END_SEQUENCE_TOKEN;
				} else if (e.isContinue()) {
					if (exprCategory == EXPR_CATEGORY_UPDATING) {
						this.getPendingUpdateList().merge(this.returnExpr.getPendingUpdateList());
					}
					if (!doNextBinding()) {
						returnExpr.close(false);
						current = null;
						return Token.END_SEQUENCE_TOKEN;
					} else {
						returnExpr.reset();
						continue;
					}
				} else {
					throw e;
				}
			}
			if (tok.getEventType() == Type.END_SEQUENCE) {
				if (exprCategory == EXPR_CATEGORY_UPDATING) {
					this.getPendingUpdateList().merge(this.returnExpr.getPendingUpdateList());
				}
				if (!doNextBinding()) {
					freeResources(false);
					return Token.END_SEQUENCE_TOKEN;
				} else {
					returnExpr.reset();
					pos++;
					context.setPosition(pos);
				}
			} else {
				return tok;
			}
		}
	}
	
	public Token next() throws MXQueryException {
		if (orderByExpr == null) {
			return normalNext();
		} else {
			return orderByNext();
		}
	}
	
	protected void freeResources(boolean restartable) throws MXQueryException {
		super.freeResources(restartable);
		// It is necessary to tell the let iterators that they can destroy their
		// variable.
		for (int i = 0; i < subIters.length; i++) {
			if (subIters[i] instanceof LetIterator
					|| subIters[i] instanceof ForseqIterator) {
				subIters[i].close(false);
			}
		}
	}


	/**
	 * Starts the binding. Returns true if successful otherwise
	 * false (means the last binding was done)
	 * 
	 * @return can the next binding be performed
	 * @throws MXQueryException
	 */
	public boolean doNextBinding() throws MXQueryException {
		while (true) {
			Token tok = subIters[nestedLevel].next();
			
			if (tok.getEventType() == Type.END_SEQUENCE) {
				if (nestedLevel == 0) {
					return false;
				} else {
					// This is only important if the binding is over
					if (subIters[nestedLevel].isResettable()) {
						subIters[nestedLevel].reset();
					} else {
						nestedLevel = 0;
						return false;
					}
					nestedLevel--;
				}
			} else {
				if (nestedLevel == subIters.length - 1) {
					if (testWhereClause()) {
						return true;
					}
				} else {
					nestedLevel++;
				}
			}
		}
	}

	private boolean testWhereClause() throws MXQueryException {
		if (whereExpr == null) {
			return true;
		}

		boolean value = false;
		Token tok = whereExpr.next();
		if (tok.getEventType() == Type.BOOLEAN) {
			value = tok.getBoolean();
		}
		if (whereExpr.isResettable()) {
			whereExpr.reset();
		}
		return value;
	}

	public void setContext(Context context, boolean recursive) throws MXQueryException {
		this.context = context;
		setResettable(super.resettable);
	}

	protected void resetImpl() throws MXQueryException {
		super.resetImpl();
		nestedLevel = 0;
		pos = 0;
		init = false;
		returnExpr.reset();
		// whereExpr is automatically reset
	}

	public void setResettable(boolean r) throws MXQueryException {
		super.resettable = r;
		boolean firstFor = false;
		for (int i = 0; i < subIters.length; i++) {
			if (resettable) {
				firstFor = true;
				subIters[i].setResettable(resettable);
			} else {
				subIters[i].setResettable(firstFor);
				if (!(subIters[i] instanceof LetIterator) || orderByExpr != null) {
					firstFor = true;
				}
			}
		}
		if (whereExpr != null) {
			whereExpr.setResettable(firstFor);
		}
		if (orderByExpr != null) {
			orderByExpr.setResettable(firstFor);
		}
		returnExpr.setResettable(firstFor);
	}

	// group by
	public XDMIterator[] getSubIters() {
		return subIters;
	}

	public XDMIterator[] getAllSubIters() {
		Vector vector = new Vector();
		for (int i = 0; i < subIters.length; i++) {
			vector.addElement(subIters[i]);
		}
		if (whereExpr != null) {
			vector.addElement(whereExpr);
		}
		if (orderByExpr != null) {
			vector.addElement(orderByExpr);
		}
		vector.addElement(returnExpr);
		XDMIterator[] arr = new XDMIterator[vector.size()];
		Enumeration iter = vector.elements();
		int i = 0;
		while (iter.hasMoreElements()) {
			arr[i] = (XDMIterator) iter.nextElement();
			i++;
		}
		return arr;
	}

	public XDMIterator getOrderByExpr() {
		return orderByExpr;
	}

	public XDMIterator getReturnExpr() {
		return returnExpr;
	}

	public XDMIterator getWhereExpr() {
		return whereExpr;
	}

	public KXmlSerializer traverseIteratorTree(KXmlSerializer serializer)
			throws Exception {
		createIteratorStartTag(serializer);
		serializer.startTag(null, "subIters");
		for (int i = 0; i < subIters.length; i++) {
			subIters[i].traverseIteratorTree(serializer);
		}
		serializer.endTag(null, "subIters");
		if (whereExpr != null) {
			serializer.startTag(null, "whereExpr");
			whereExpr.traverseIteratorTree(serializer);
			serializer.endTag(null, "whereExpr");
		}
		if (orderByExpr != null) {
			orderByExpr.traverseIteratorTree(serializer);
		}
		serializer.startTag(null, "returnExpr");
		returnExpr.traverseIteratorTree(serializer);
		serializer.endTag(null, "returnExpr");
		createIteratorEndTag(serializer);
		return serializer;
	}

	public XDMIterator copy(Context parentIterContext, XQStaticContext newParentIterContext, boolean copyContext, Vector nestedPredCtxStack) throws MXQueryException {
		// initialize new context and subIterators
		Context newContext = parentIterContext;
		XDMIterator[] newSubIters = null;
		if (subIters != null) {
			newSubIters = new XDMIterator[subIters.length]; 
		}
		
		// copy the context
		if (copyContext && context != null) {
			newContext = context.copy();
			newContext.setParent(parentIterContext);
		
		}
		
		// copy the subIterators
		if (subIters != null) {
			Context prevContext = newContext;
			for (int i=0; i<subIters.length; i++) {
				newSubIters[i] = subIters[i].copy(prevContext, null, true, nestedPredCtxStack);
				prevContext = newSubIters[i].getContext(); 
			}
		}
		else throw new MXQueryException(ErrorCodes.A0009_EC_EVALUATION_NOT_POSSIBLE,"Incorrect subiterator nesting",loc);
		FFLWORIterator fl = (FFLWORIterator) copy(newContext, newSubIters, nestedPredCtxStack);
		fl.exprCategory = this.exprCategory;
		return fl;
	}		
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		boolean copyRetContext = false; 
		 if (this.subIters[subIters.length-1].getContext() != returnExpr.getContext())
			 copyRetContext = true;
		 XDMIterator newWhere = null;
		 if (whereExpr != null) {
			 nestedPredCtxStack.addElement(new ContextPair(this.subIters[subIters.length-1].getContext(),subIters[subIters.length-1].getContext()));
			 newWhere = whereExpr.copy(subIters[subIters.length-1].getContext(), null, false, nestedPredCtxStack);
			 nestedPredCtxStack.removeElementAt(nestedPredCtxStack.size()-1);
		 }
		FFLWORIterator ret = new FFLWORIterator(
				context, 
				subIters, 
				newWhere, 
				orderByExpr == null?null:(OrderByIterator) orderByExpr.copy(subIters[subIters.length-1].getContext(), null, false, nestedPredCtxStack), 
				returnExpr.copy(subIters[subIters.length-1].getContext(), this.subIters[subIters.length-1].getContext(), copyRetContext, nestedPredCtxStack), loc);
		ret.exprCategory = exprCategory;
		return ret;
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
//		// subiters
//		if (subIters != null && subIters.length > 0) {
//			for (int i=0; i<subIters.length; i++) {
//				subIters[i].debug(newblock, null);	
//			}
//		}
//		
//		// where
//		if (whereExpr != null) {
//			whereExpr.debug(newblock, "where");
//		}
//		
//		// order by
//		if (orderByExpr != null) {
//			orderByExpr.debug(newblock, "orderBy");
//		}
//		
//		// return
//		returnExpr.debug(newblock, "return");
//	}
	
	protected void checkExpressionTypes() throws MXQueryException {
		if (!isScripting) {
			checkExprSimpleOnly(subIters,isScripting);
			if (whereExpr != null)
				checkExprSimpleOnly(new XDMIterator [] {whereExpr},isScripting);
			if (orderByExpr != null)
				checkExprSimpleOnly(new XDMIterator [] {orderByExpr},isScripting);
			exprCategory = checkExprDefault(new XDMIterator[] {returnExpr},isScripting);
		} else {
			boolean hasUpdates = checkExprNoSequential(subIters,isScripting);
			if (whereExpr != null)
				hasUpdates = checkExprNoSequential(new XDMIterator [] {whereExpr},isScripting) || hasUpdates;
			if (orderByExpr != null)
				hasUpdates = checkExprNoSequential(new XDMIterator [] {orderByExpr},isScripting) || hasUpdates;

			exprCategory = checkExprDefault(new XDMIterator[] {returnExpr},isScripting);
			if (hasUpdates) {
				if (exprCategory == XDMIterator.EXPR_CATEGORY_SEQUENTIAL)
					throw new StaticException(ErrorCodes.A0009_EC_EVALUATION_NOT_POSSIBLE,"FLOWR with updating for/let/where/order must not have sequential return",loc);
				exprCategory = XDMIterator.EXPR_CATEGORY_UPDATING;
			}
		}
	}

	public TypeInfo getStaticType() {
		TypeInfo returnExprType = returnExpr.getStaticType();
		return new TypeInfo(returnExprType.getType(),Type.OCCURRENCE_IND_ZERO_OR_MORE,returnExprType.getName(),returnExprType.getNameSpaceURI());
	}		
}

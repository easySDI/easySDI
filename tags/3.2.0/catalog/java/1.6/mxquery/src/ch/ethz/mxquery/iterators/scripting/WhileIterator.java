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
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.exceptions.StaticException;
import ch.ethz.mxquery.model.CFException;
import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.util.KXmlSerializer;

/**
 * WHILE
 * 
 * @author David Alexander Graf
 * 
 */
public class WhileIterator extends CurrentBasedIterator {
	private XDMIterator condition;
	private XDMIterator body;

	/**
	 * Constructor
	 * 
	 * @param condition
	 *            while-condition
	 * @param body
	 *            while-body
	 */
	public WhileIterator(Context ctx, XDMIterator condition, XDMIterator body, QueryLocation location)
			throws MXQueryException {
		super(ctx, location);
		exprCategory = XDMIterator.EXPR_CATEGORY_SEQUENTIAL;
		this.condition = condition; 
		this.body = body;
		this.condition.setResettable(true);
		this.body.setResettable(true);
	}

	/**
	 * Checks the condition before each iteration.
	 * 
	 * @return true if condition true
	 * @throws MXQueryException
	 */
	private boolean checkCondition() throws MXQueryException {
		boolean value = false;

		Token tok = this.condition.next();
		if (tok.getEventType() == Type.BOOLEAN) {
			value = tok.getBoolean();
		}
		this.condition.reset();
		return value;
	}

	private Token nonUpdateNext() throws MXQueryException {
		Token inputToken = Token.END_SEQUENCE_TOKEN;
		if (this.endOfSeq) {
			return Token.END_SEQUENCE_TOKEN;
		}
		if (this.called == 0) {
			this.called = 1;
			this.current = this.body;
			if (!this.checkCondition()) {
				this.endOfSeq = true;
				return Token.END_SEQUENCE_TOKEN;
			}
		}
		while (true) {
			try {
				inputToken = this.body.next();
			} catch (CFException e) {
				if (e.isBreak()) {
					this.body.close(false);
					this.endOfSeq = true;
					return Token.END_SEQUENCE_TOKEN;
				} else if (e.isContinue()) {
					if (!this.checkCondition()) {
						this.endOfSeq = true;
						return Token.END_SEQUENCE_TOKEN;
					} else {
						this.body.reset();
					}
				} else {
					throw e;
				}
			}
			if (inputToken.getEventType() == Type.END_SEQUENCE) {
				if (!this.checkCondition()) {
					this.endOfSeq = true;
					return Token.END_SEQUENCE_TOKEN;
				} else {
					this.body.reset();
				}
			} else {
				return inputToken;
			}
		}
	}

	private Token updateNext() throws MXQueryException {
		if (this.called == 0) {
			this.called = 1;
			while (this.checkCondition()) {
				try {
					this.body.next();
				} catch (CFException e) {
					if (e.isBreak()) {
						this.body.getPendingUpdateList().apply();
						this.body.close(false);
						return Token.END_SEQUENCE_TOKEN;
					} else if (e.isContinue()) {
						// do nothing
					} else {
						throw e;
					}
				}
				this.body.getPendingUpdateList().apply();
				this.body.reset();
			}
		}
		return Token.END_SEQUENCE_TOKEN;
	}

	public Token next() throws MXQueryException {
		if (this.body.getExpressionCategoryType(true) == XDMIterator.EXPR_CATEGORY_UPDATING) {
			return this.updateNext();
		} else {
			return this.nonUpdateNext();
		}
	}

	protected void resetImpl() throws MXQueryException {
		this.current = null;
		this.called = 0;
		this.endOfSeq = false;
		this.condition.reset();
		this.body.reset();
	}

	public XDMIterator[] getAllIterators() {
		return new XDMIterator[] { this.condition, this.body };
	}

	public void setContext(Context context, boolean recursive) throws MXQueryException {
		this.context = context;
		this.condition.setContext(context, true);
		this.body.setContext(context, true);
		this.condition.setResettable(true);
		this.body.setResettable(true);
	}

	public void setResettable(boolean r) {
		this.resettable = r;
	}

	public KXmlSerializer traverseIteratorTree(KXmlSerializer serializer)
			throws Exception {
		this.createIteratorStartTag(serializer);
		serializer.startTag(null, "Condition");
		this.condition.traverseIteratorTree(serializer);
		serializer.endTag(null, "Condition");
		serializer.startTag(null, "Body");
		this.body.traverseIteratorTree(serializer);
		serializer.endTag(null, "Body");
		this.createIteratorEndTag(serializer);
		return serializer;
	}
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		return new WhileIterator(context, 
				condition.copy(context, null, false, nestedPredCtxStack), 
				body.copy(context, null, false, nestedPredCtxStack),loc);
	}
	
	protected void checkExpressionTypes(boolean isScripting) throws MXQueryException {
		if (condition.getExpressionCategoryType(isScripting) == XDMIterator.EXPR_CATEGORY_UPDATING)
			throw new StaticException(ErrorCodes.U0001_UPDATE_STATIC_UPDATING_EXPRESSION_NOT_ALLOWED_HERE,"Updating expression not allow in while condition", loc);
	}	
}

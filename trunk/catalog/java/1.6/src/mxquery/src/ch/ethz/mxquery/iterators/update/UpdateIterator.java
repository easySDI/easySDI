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
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.model.Iterator;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.model.updatePrimitives.UpdatePrimitive;
import ch.ethz.mxquery.util.KXmlSerializer;
import ch.ethz.mxquery.util.Utils;

/**
 * Base class of the iterators that represents an (absolute) update expression.
 * 
 * @author David Alexander Graf
 * 
 */
public abstract class UpdateIterator extends Iterator {
	protected int mtype;

	/**
	 * Constructor
	 * 
	 * @param mtype
	 *            update type
	 */
	public UpdateIterator(Context ctx, int mtype, QueryLocation location) {
		super(ctx, location);
		this.mtype = mtype;
	}

	/**
	 * Creates the pending update list.
	 * TODO: Replace it with init
	 * 
	 * @throws MXQueryException
	 */
	protected abstract void createUpdateList() throws MXQueryException;
	
	public Token next() throws MXQueryException {
		if (this.called == 0) {
			this.getPendingUpdateList().clear();
			this.createUpdateList();
		}
		this.called++;
		return Token.END_SEQUENCE_TOKEN;
	}

	/**
	 * Increases the iterator till it points to the sibling of the actual
	 * iterator position. TODO: This method can be executed more efficient with
	 * a better store structure!
	 * 
	 * @param iterator
	 * @return 
	 * @throws MXQueryException
	 */
	protected Token putIteratorToNext(XDMIterator iterator, Token current)
			throws MXQueryException {
		int type;
		if (current.getEventType() == Type.START_SEQUENCE) {
			return null;
		}
		int depth = 0;
		while ((type = current.getEventType()) != Type.END_SEQUENCE) {
			if (type == Type.START_DOCUMENT || type == Type.START_TAG) {
				depth++;
			} else if (type == Type.END_DOCUMENT
					|| type == Type.END_TAG) {
				depth--;
			}
			current = iterator.next();
			type = current.getEventType();
			if (depth == 0 && type != Type.END_SEQUENCE) {
				return current;
			}

		}
		return null;
	}

	/**
	 * Checks if the element on which the iterator points to has no siblings.
	 * TODO more efficient.
	 * 
	 * @param iterator
	 * @return
	 * @throws MXQueryException
	 */
	protected boolean isLastNode(XDMIterator iterator, Token tok) throws MXQueryException {
		int type;
		int depth = 0;
		type = tok.getEventType();
		while (type != Type.END_SEQUENCE) {
			if (type == Type.START_DOCUMENT || type == Type.START_TAG) {
				depth++;
			} else if (type == Type.END_DOCUMENT
					|| type == Type.END_TAG) {
				depth--;
			}
			tok = iterator.next();
			type = tok.getEventType();
			if (depth == 0 && type != Type.END_SEQUENCE) {
				return false;
			}

		}
		return true;
	}

	protected KXmlSerializer createIteratorStartTag(KXmlSerializer serializer)
			throws Exception {
		serializer.startTag(null, Utils.getSimpleClassName(this.getClass()
				.getName()));
		String updateType = null;
		switch (this.mtype) {
		case UpdatePrimitive.INSERT_INTO_AS_FIRST:
			updateType = "insert into as first";
			break;
		case UpdatePrimitive.INSERT_INTO_AS_LAST:
			updateType = "insert into as last";
			break;
		case UpdatePrimitive.INSERT_INTO:
			updateType = "insert into";
			break;
		case UpdatePrimitive.INSERT_AFTER:
			updateType = "insert after";
			break;
		case UpdatePrimitive.INSERT_BEFORE:
			updateType = "insert before";
			break;
		case UpdatePrimitive.REPLACE_VALUE:
			updateType = "replace value";
			break;
		case UpdatePrimitive.REPLACE_NODE:
			updateType = "replace node";
			break;
		case UpdatePrimitive.PUT:
			updateType = "put";
			break;			
		}
		if (updateType != null) {
			serializer.attribute(null, "type", updateType);
		}
		return serializer;
	}

	public KXmlSerializer traverseIteratorTree(KXmlSerializer serializer)
			throws Exception {
		this.createIteratorStartTag(serializer);
		serializer.startTag(null, "target");
		this.subIters[0].traverseIteratorTree(serializer);
		serializer.endTag(null, "target");
		if (this.subIters.length > 1) {
			serializer.startTag(null, "source");
			this.subIters[1].traverseIteratorTree(serializer);
			serializer.endTag(null, "source");
		}
		this.createIteratorEndTag(serializer);
		return serializer;
	}

	
	
	protected void checkExpressionTypes() throws MXQueryException {
		super.checkExpressionTypes();
		exprCategory = XDMIterator.EXPR_CATEGORY_UPDATING;
	}

	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack)
			throws MXQueryException {
		// TODO Auto-generated method stub
		return null;
	}

	/* (non-Javadoc)
	 * @see ch.ethz.mxquery.model.iterators.Iterator#setContext(ch.ethz.mxquery.query.Context)
	 */
	public void setContext(Context context, boolean recursive) throws MXQueryException {
		this.context = context;
	}	
}

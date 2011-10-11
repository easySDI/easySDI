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
import ch.ethz.mxquery.datamodel.Identifier;
import ch.ethz.mxquery.datamodel.Source;
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
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.model.updatePrimitives.InsertAfter;
import ch.ethz.mxquery.model.updatePrimitives.InsertAttributes;
import ch.ethz.mxquery.model.updatePrimitives.InsertBefore;
import ch.ethz.mxquery.model.updatePrimitives.InsertInto;
import ch.ethz.mxquery.model.updatePrimitives.InsertIntoAsFirst;
import ch.ethz.mxquery.model.updatePrimitives.InsertIntoAsLast;
import ch.ethz.mxquery.model.updatePrimitives.UpdateableStore;
import ch.ethz.mxquery.model.updatePrimitives.UpdatePrimitive;
import ch.ethz.mxquery.util.KXmlSerializer;

/**
 * Represents the updating expression INSERT:
 * 
 * @author David Alexander Graf
 * 
 */
public class InsertIterator extends UpdateIterator {

	/**
	 * Constructor
	 * 
	 * @param insertType
	 *            insert type
	 * @param targetExpr
	 *            where the new expression is inserted
	 * @param sourceExpr
	 *            new expression
	 */
	public InsertIterator(Context ctx, int insertType, XDMIterator targetExpr,
			XDMIterator sourceExpr, QueryLocation location) throws StaticException{
		super(ctx, insertType,location);
		TypeInfo tiTarget = new TypeInfo(Type.NODE,Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
		SequenceTypeIterator typeCheckIt = new SequenceTypeIterator(tiTarget,true,false, context,location);
		typeCheckIt.setSubIters(targetExpr);
		this.subIters = new XDMIterator[]{typeCheckIt, sourceExpr};
	}

	protected void createUpdateList() throws MXQueryException {
		Token targetToken = null;
		try {
			targetToken = this.subIters[0].next();
			int initialdepth = depth;
			Token tok = targetToken;
			do {
				tok = subIters[0].next();
			} while (initialdepth >= depth && tok!=Token.END_SEQUENCE_TOKEN);
			if (tok!=Token.END_SEQUENCE_TOKEN)
				if (this.mtype == UpdatePrimitive.INSERT_AFTER
						|| this.mtype == UpdatePrimitive.INSERT_BEFORE)
					throw new DynamicException(
							ErrorCodes.U0006_UPDATE_TYPE_INSERT_SINGLE_ELEM_TEXT_CO_PI_EXPECTED,
							"Single Element, Doc, Comment or PI node expected", loc);				
	
			else 
				throw new TypeException(ErrorCodes.U0005_UPDATE_TYPE_SINGLE_ELEM_DOC_EXPECTED, "Single Element or Doc node expected", loc);
		} catch (TypeException te) {
			if (this.mtype == UpdatePrimitive.INSERT_AFTER
					|| this.mtype == UpdatePrimitive.INSERT_BEFORE)
				throw new DynamicException(
						ErrorCodes.U0006_UPDATE_TYPE_INSERT_SINGLE_ELEM_TEXT_CO_PI_EXPECTED,
						"Single Element, Doc, Comment or PI node expected", loc);				
			else
				throw new TypeException(ErrorCodes.U0005_UPDATE_TYPE_SINGLE_ELEM_DOC_EXPECTED, "Single Element or Doc node expected", loc);
		}
		
		if (targetToken == Token.END_SEQUENCE_TOKEN)
			throw new DynamicException(ErrorCodes.U0027_UPDATE_DYNAMIC_TARGET_EMPTY, "Empty Target for Insert", loc);

		int targetType = targetToken.getEventType();

		
		Identifier targetId = targetToken.getId();

		if (this.mtype == UpdatePrimitive.INSERT_AFTER
				|| this.mtype == UpdatePrimitive.INSERT_BEFORE) {
			if (targetType == Type.START_DOCUMENT || Type.isAttribute(targetType)) {
				throw new DynamicException(
						ErrorCodes.U0006_UPDATE_TYPE_INSERT_SINGLE_ELEM_TEXT_CO_PI_EXPECTED,
						"If before or after is specified, the target node must "
								+ "not be a document or an attribute", loc);				
			}
		} 

		UpdateableStore dsSource = this.context.getStores()
				.createTransactionStore(Thread.currentThread().toString().hashCode());
		
		XDMIterator stIt = subIters[1];
		if (context.getRootContext().getConstructionMode().equals(XQStaticContext.STRIP))
			stIt = new StripTypeIterator(context,new XDMIterator[]{subIters[1]},loc);

		dsSource.appendForInsert(stIt);
		UpdateableStore dsAttr = dsSource.pullAttributes();
		if (dsSource.containsTopAttrs()) {
			throw new TypeException(ErrorCodes.U0004_UPDATE_TYPE_ATTRIBUTE_NOT_ALLOWED_HERE,
					"In an insert sequence, it is not allowed to have attributes after "
							+ "other elements in an input sequence!", loc);
		}

		if (!dsAttr.isEmpty()) {
			Identifier attrTargetId;
			switch (this.mtype) {
			case UpdatePrimitive.INSERT_AFTER:
			case UpdatePrimitive.INSERT_BEFORE:
				Source src = targetId.getStore();
			
				
				if (src instanceof UpdateableStore) {
				XDMIterator parentIter = ((UpdateableStore)src).getParentIterator(targetId);
				int parentType = parentIter.next().getEventType();
				if (!Type.isTypeOrSubTypeOf(parentType, Type.START_TAG, Context.getDictionary())) {
					throw new DynamicException(
							ErrorCodes.U0030_UPDATE_DYNAMIC_INSERT_BEFORE_AFTER_ATTRIBUTE_DOC,
							"If before or after is specified with an attribute, the target node must "
							+ "be a single element node whose parent property is not empty!", loc);
				}
				} else {
					if (!this.context.getStores().hasParent(targetId)) {
						throw new DynamicException(
								ErrorCodes.U0030_UPDATE_DYNAMIC_INSERT_BEFORE_AFTER_ATTRIBUTE_DOC,
								"If before or after is specified with an attribute, the target node must "
										+ "be a single element node whose parent property is not empty!", loc);
						}
				}
				attrTargetId = this.context.getStores().getParentId(targetId);
				if (attrTargetId == null) {
					throw new DynamicException(ErrorCodes.U0030_UPDATE_DYNAMIC_INSERT_BEFORE_AFTER_ATTRIBUTE_DOC, "No parent for an element found!", loc);
				}
				break;
			case UpdatePrimitive.INSERT_INTO:
			case UpdatePrimitive.INSERT_INTO_AS_FIRST:
			case UpdatePrimitive.INSERT_INTO_AS_LAST:
				if (targetType != Type.START_TAG)
					throw new DynamicException(ErrorCodes.U0022_UPDATE_TYPE_INSERT_ATTRIBUTE_DOC, "Attributes can only be inserted into element nodes!", loc);
				attrTargetId = targetId;
				break;
			default:
				throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Not supported insert type used!", loc);
			}
			UpdatePrimitive up = new InsertAttributes(attrTargetId, dsAttr);
			this.getPendingUpdateList().add(up);
		} else {
			switch (this.mtype) {
			case UpdatePrimitive.INSERT_AFTER:
			case UpdatePrimitive.INSERT_BEFORE:
				if (!this.context.getStores().hasParent(targetId)) {
					throw new DynamicException(
							ErrorCodes.U0029_UPDATE_DYNAMIC_INSERT_BEFORE_AFTER_NO_PARENT,
							"If before or after is specified, the target node must "
							+ "be a single element node whose parent property is not empty!", loc);
				}
				break;
			case UpdatePrimitive.INSERT_INTO:
			case UpdatePrimitive.INSERT_INTO_AS_FIRST:
			case UpdatePrimitive.INSERT_INTO_AS_LAST:
				if (!(targetType == Type.START_TAG|| targetType == Type.START_DOCUMENT)) {
					throw new DynamicException(ErrorCodes.U0005_UPDATE_TYPE_SINGLE_ELEM_DOC_EXPECTED,"Insert Into is only allowed for a element or document target",loc);
				}
			}
		}

		if (!dsSource.isEmpty()) {
			UpdatePrimitive up;
			switch (this.mtype) {
			case UpdatePrimitive.INSERT_AFTER:
				up = new InsertAfter(targetId, dsSource);
				break;
			case UpdatePrimitive.INSERT_BEFORE:
				up = new InsertBefore(targetId, dsSource);
				break;
			case UpdatePrimitive.INSERT_INTO:
				up = new InsertInto(targetId, dsSource);
				break;
			case UpdatePrimitive.INSERT_INTO_AS_FIRST:
				up = new InsertIntoAsFirst(targetId, dsSource);
				break;
			case UpdatePrimitive.INSERT_INTO_AS_LAST:
				up = new InsertIntoAsLast(targetId, dsSource);
				break;
			default:
				throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Not supported insert type used!", loc);
			}
			this.getPendingUpdateList().add(up);
		}
	}
	
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		return new InsertIterator(context, mtype, subIters[0], subIters[1],loc);
	}
	
	private String getInsertModeName() {
		switch (mtype) {
		case UpdatePrimitive.INSERT_AFTER:
			return "Insert After";
		case UpdatePrimitive.INSERT_ATTRIBUTES:
			return "Insert Attributes";			
		case UpdatePrimitive.INSERT_BEFORE:
			return "Insert Before";
		case UpdatePrimitive.INSERT_INTO:
			return "Insert Into";
		case UpdatePrimitive.INSERT_INTO_AS_FIRST:
			return "Insert Into First";
		case UpdatePrimitive.INSERT_INTO_AS_LAST:
			return "Insert Into Last";
			default:
				return "";
		}
	}
	
	protected KXmlSerializer createIteratorStartTag(KXmlSerializer serializer) throws Exception {
		super.createIteratorStartTag(serializer);
		serializer.attribute(null, "insertType", "$" +  getInsertModeName());
		return serializer;
	}	
	
	
}

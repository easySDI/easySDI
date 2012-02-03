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
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.exceptions.StaticException;
import ch.ethz.mxquery.exceptions.TypeException;
//import ch.ethz.mxquery.functions.fn.DataValuesIterator;
import ch.ethz.mxquery.iterators.SequenceTypeIterator;
import ch.ethz.mxquery.iterators.TokenIterator;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.model.updatePrimitives.ReplaceNode;
import ch.ethz.mxquery.model.updatePrimitives.ReplaceNodeContent;
import ch.ethz.mxquery.model.updatePrimitives.ReplaceValue;
import ch.ethz.mxquery.model.updatePrimitives.UpdateableStore;
import ch.ethz.mxquery.model.updatePrimitives.UpdatePrimitive;
import ch.ethz.mxquery.util.KXmlSerializer;

/**
 * Represents the updating expression REPLACE.
 * 
 * @author David Alexander Graf
 * 
 */
public class ReplaceIterator extends UpdateIterator {

	/**
	 * Constructor
	 * 
	 * @param replaceType
	 *            type of replacement
	 * @param targetExpr
	 *            replace expression
	 * @param sourceExpr
	 *            insert expression
	 */
	public ReplaceIterator(Context ctx, int replaceType, XDMIterator targetExpr,
			XDMIterator sourceExpr, QueryLocation location) throws StaticException{
		super(ctx, replaceType, location);
		TypeInfo tiTarget = new TypeInfo(Type.NODE,Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
		SequenceTypeIterator typeCheckIt = new SequenceTypeIterator(tiTarget,true,false, context,loc);
		typeCheckIt.setSubIters(targetExpr);
		// refine with more dynamic checks
		this.subIters = new XDMIterator[]{typeCheckIt, sourceExpr};
	}

	private ReplaceIterator(Context ctx, int replaceType, XDMIterator typeCheckIt, XDMIterator sourceExpr, boolean copy, QueryLocation location) {
		super(ctx, replaceType, location);
		subIters = new XDMIterator[]{typeCheckIt, sourceExpr};
	}

	/**
	 * Replaces node.
	 * 
	 * @throws MXQueryException
	 */
	private void replaceNode() throws MXQueryException {
		UpdateableStore dsSource = this.context.getStores()
		.createTransactionStore(Thread.currentThread().toString().hashCode());
		XDMIterator stIt = subIters[1];
		if (context.getRootContext().getConstructionMode().equals(XQStaticContext.STRIP))
			stIt = new StripTypeIterator(context,new XDMIterator[]{subIters[1]},loc);

		dsSource.appendForInsert(stIt);
		
		Token targetToken = null;
		try {
			targetToken = this.subIters[0].next();
			int initialdepth = depth;
			Token tok;
			do {
				tok = subIters[0].next();
			} while (initialdepth >= depth && tok!=Token.END_SEQUENCE_TOKEN);
			if (tok!=Token.END_SEQUENCE_TOKEN)
				throw new TypeException(ErrorCodes.U0008_UPDATE_TYPE_REPLACE_SINGLE_ELEM_TEXT_CO_PI_EXPECTED,
						"The target of a replace must be a single element, attribute, comment, PI, text node", loc);
		} catch (TypeException te) {
			throw new TypeException(ErrorCodes.U0008_UPDATE_TYPE_REPLACE_SINGLE_ELEM_TEXT_CO_PI_EXPECTED,
					"The target of a replace must be a single element, attribute, comment, PI, text node", loc);
		}
		if (targetToken == Token.END_SEQUENCE_TOKEN)
			throw new DynamicException(ErrorCodes.U0027_UPDATE_DYNAMIC_TARGET_EMPTY, "Empty Target for Rename", loc);

		int targetType = targetToken.getEventType();
		Identifier targetId = targetToken.getId();
		if (targetType == Type.START_DOCUMENT) {
			throw new TypeException(ErrorCodes.U0008_UPDATE_TYPE_REPLACE_SINGLE_ELEM_TEXT_CO_PI_EXPECTED,
					"The target of a replace must not be a document", loc);
		}
		Identifier parent = null;
		if (Type.isAttribute(targetType)) {
			if (!dsSource.containsOnlyAttrs()) {
				throw new TypeException(ErrorCodes.U0011_UPDATE_TYPE_REPLACE_ATTRIBUTE_EXPECTED,
						"If the target of a replace is an Attribte, "
						+ "then the source must only contain Attribute nodes.", loc);
			}
			parent = context.getStores().getParentId(targetId);
		} else {
			if (dsSource.containsTopAttrs()) {
				throw new TypeException(ErrorCodes.U0010_UPDATE_TYPE_REPLACE_ELEM_TEXT_CO_PI_EXPECTED,
						"If the target of a replace is not an Attribute, "
						+ "then the source must not contain Attribute nodes.", loc);
			}
		}
		if (!this.context.getStores().hasParent(targetId)) {
			throw new DynamicException(ErrorCodes.U0009_UPDATE_DYNAMIC_REPLACE_NO_PARENT,
					"The target of a replace must be a single node whose parent "
					+ "property is not empty!", loc);
		} 		
		ReplaceNode rn = new ReplaceNode(targetId, parent, dsSource);
		this.getPendingUpdateList().add(rn);
	}

	/**
	 * Replaces value of a node.
	 * 
	 * @throws MXQueryException
	 */
	private void replaceValueOfNode() throws MXQueryException {
		Token targetToken = null;
		try {
			targetToken = this.subIters[0].next();
			int initialdepth = depth;
			Token tok;
			do {
				tok = subIters[0].next();
			} while (initialdepth >= depth && tok!=Token.END_SEQUENCE_TOKEN);
			if (tok!=Token.END_SEQUENCE_TOKEN)
				throw new TypeException(ErrorCodes.U0008_UPDATE_TYPE_REPLACE_SINGLE_ELEM_TEXT_CO_PI_EXPECTED,
						"The target of a replace must be a single element, attribute, comment, PI, text node", loc);
		} catch (TypeException te) {
			throw new TypeException(ErrorCodes.U0008_UPDATE_TYPE_REPLACE_SINGLE_ELEM_TEXT_CO_PI_EXPECTED,
					"The target of a replace must be a single element, attribute, comment, PI, text node", loc);
		}
		if (targetToken == Token.END_SEQUENCE_TOKEN)
			throw new DynamicException(ErrorCodes.U0027_UPDATE_DYNAMIC_TARGET_EMPTY, "Empty Target for Replace", loc);

		int targetType = targetToken.getEventType();
		Identifier targetId = targetToken.getId();
		if (targetType == Type.START_DOCUMENT) {
			throw new TypeException(ErrorCodes.U0008_UPDATE_TYPE_REPLACE_SINGLE_ELEM_TEXT_CO_PI_EXPECTED,
					"The target of a replace must not be a document", loc);
		}
		
		StringBuffer  textVal = new StringBuffer();
		
		Token tok;
		
		while ((tok = subIters[1].next()) != Token.END_SEQUENCE_TOKEN ) {
			if (textVal.length() > 0)
				textVal.append(" ");
			textVal.append(tok.getValueAsString());
		}
		
		if (targetType == Type.START_TAG) {
			UpdateableStore dsSource = this.context.getStores()
			.createTransactionStore(Thread.currentThread().toString().hashCode());
			dsSource.appendForInsert(new TokenIterator(context,textVal.toString(),Type.UNTYPED_ATOMIC,loc));
			this.getPendingUpdateList().add(new ReplaceNodeContent(targetId,
					dsSource));
		} else {
			this.getPendingUpdateList().add(new ReplaceValue(targetId, textVal.toString()));
		}
	}

	protected void createUpdateList() throws MXQueryException {
		switch (this.mtype) {
		case UpdatePrimitive.REPLACE_NODE:
			this.replaceNode();
			break;
		case UpdatePrimitive.REPLACE_VALUE:
			this.replaceValueOfNode();
			break;
		default:
			throw new StaticException(ErrorCodes.A0009_EC_EVALUATION_NOT_POSSIBLE,
					"Not supported insert type used!", loc);
		}
	}

	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		return new ReplaceIterator(context, mtype, subIters[0], subIters[1], true, loc);
	}
	
	private String getReplaceModeName() {
		switch (mtype) {
		case UpdatePrimitive.REPLACE_NODE:
			return "Replace Node";
		case UpdatePrimitive.REPLACE_NODE_CONTENT:
			return "Replace Node";
		case UpdatePrimitive.REPLACE_VALUE:
			return "Replace Value";
					
			default:
				return "";
		}
	}
	
	protected KXmlSerializer createIteratorStartTag(KXmlSerializer serializer) throws Exception {
		super.createIteratorStartTag(serializer);
		serializer.attribute(null, "replaceType", "$" +  getReplaceModeName());
		return serializer;
	}		
	
}

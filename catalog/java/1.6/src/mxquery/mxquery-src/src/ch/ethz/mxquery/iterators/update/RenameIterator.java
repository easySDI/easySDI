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
import ch.ethz.mxquery.datamodel.Identifier;
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
import ch.ethz.mxquery.iterators.XMLContent;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.model.updatePrimitives.Rename;
import ch.ethz.mxquery.model.updatePrimitives.UpdatePrimitive;

/**
 * Represents the updating expression RENAME.
 * 
 * @author David Alexander Graf
 * 
 */
public class RenameIterator extends UpdateIterator {

	/**
	 * Constructor
	 * 
	 * @param targetExpr
	 *            target
	 * @param newNameExpr
	 *            new name
	 */
	public RenameIterator(Context ctx, XDMIterator targetExpr, XDMIterator newNameExpr, QueryLocation location) throws StaticException{
		super(ctx, UpdatePrimitive.RENAME, location);
		TypeInfo tiTarget = new TypeInfo(Type.NODE,Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
		SequenceTypeIterator typeCheckIt = new SequenceTypeIterator(tiTarget,true,false, context, location);
		typeCheckIt.setSubIters(targetExpr);
		this.subIters = new XDMIterator[]{typeCheckIt, newNameExpr};
	}

	/**
	 * Constructor (needed for copy())
	 * 
	 * @param ctx				context
	 * @param typeCheckIt		TypeCheckIterator
	 * @param caQName			CastAsIterator
	 * @param copy				
	 * @throws StaticException
	 */
	private RenameIterator(Context ctx, XDMIterator typeCheckIt, XDMIterator caQName, boolean copy, QueryLocation location) throws StaticException{
		super(ctx, UpdatePrimitive.RENAME, location);
		this.subIters = new XDMIterator[]{typeCheckIt, caQName};
	}
	
	protected void createUpdateList() throws MXQueryException {
		Token targetToken;
		try {
			targetToken = this.subIters[0].next();
			int initialdepth = depth;
			Token tok = targetToken;
			do {
				tok = subIters[0].next();
			} while (initialdepth >= depth && tok!=Token.END_SEQUENCE_TOKEN);
			if (tok!=Token.END_SEQUENCE_TOKEN)
				throw new TypeException(ErrorCodes.U0012_UPDATE_TYPE_RENAME_SINGLE_ELEM_ATTR_PI_EXPECTED, "The target of a rename must be a single element, "
						+ "attribute or processing instruction node!", loc);
		} catch (TypeException te) {
			throw new TypeException(ErrorCodes.U0012_UPDATE_TYPE_RENAME_SINGLE_ELEM_ATTR_PI_EXPECTED, "The target of a rename must be a single element, "
							+ "attribute or processing instruction node!", loc);
		}
		if (targetToken == Token.END_SEQUENCE_TOKEN)
			throw new DynamicException(ErrorCodes.U0027_UPDATE_DYNAMIC_TARGET_EMPTY, "Empty Target for Rename", loc);
		int targetType = targetToken.getEventType();
		Identifier targetId = targetToken.getId();
		if (targetType != Type.START_TAG
						&& !Type.isAttribute(targetType) && 
						targetType != Type.PROCESSING_INSTRUCTION) {
			throw new TypeException(ErrorCodes.U0012_UPDATE_TYPE_RENAME_SINGLE_ELEM_ATTR_PI_EXPECTED, 
					"The target of a rename must be a single element, "
					+ "attribute or processing instruction node!", loc);
		}
		
		QName newName = XMLContent.computeElemQName(context, subIters[1], loc);		
		
		if (targetType == Type.PROCESSING_INSTRUCTION && newName.getNamespacePrefix() != null)
			throw new DynamicException(ErrorCodes.U0025_UPDATE_DYNAMIC_RENAME_PI_WRONG_QNAME, "New name for a PI must not contain a namespace prefix", loc);
		//TODO: Namespace checks
		
		Identifier parent = null;
		
		if (Type.isAttribute(targetType))
				parent = context.getStores().getParentId(targetId);
		
		this.getPendingUpdateList().add(new Rename(targetId, parent, newName));
	}
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		return new RenameIterator(context, subIters[0], subIters[1], true, loc);
	}
}

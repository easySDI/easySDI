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
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.exceptions.StaticException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.iterators.SequenceTypeIterator;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.model.updatePrimitives.Delete;
import ch.ethz.mxquery.model.updatePrimitives.UpdatePrimitive;

/**
 * Represents the updating expression DELETE.
 * 
 * @author David Alexander Graf
 * 
 */
public class DeleteIterator extends UpdateIterator {
	/**
	 * Constructor
	 * 
	 * @param targetExpr
	 *            delete expression
	 */
	public DeleteIterator(Context ctx, XDMIterator targetExpr, QueryLocation location) throws StaticException{
		super(ctx, UpdatePrimitive.DELETE, location);
		TypeInfo ti = new TypeInfo(Type.NODE,Type.OCCURRENCE_IND_ZERO_OR_MORE,null,null);
		SequenceTypeIterator typeCheckIt = new SequenceTypeIterator(ti,true,false, context, location);
		typeCheckIt.setSubIters(targetExpr);
		this.subIters = new XDMIterator [] {typeCheckIt};
	}

	protected void createUpdateList() throws MXQueryException {
		Token tok = null;
		try {
			tok = this.subIters[0].next();
		} catch (TypeException te) {
			throw new TypeException(ErrorCodes.U0007_UPDATE_TYPE_ZERO_OR_MORE_NODES_EXPECTED, "Zero or more nodes expected", loc);
		}
		
		if (tok == Token.END_SEQUENCE_TOKEN)
			return;
		
		Identifier targetId = tok.getId();
		while (targetId != null) {
			this.getPendingUpdateList().add(new Delete(targetId));
			if ((tok = this.putIteratorToNext(this.subIters[0], tok)) != null) {
				targetId = tok.getId();
			} else {
				targetId = null;
			}
		}
	}
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		return new DeleteIterator(context, subIters[0], loc);
	}
}

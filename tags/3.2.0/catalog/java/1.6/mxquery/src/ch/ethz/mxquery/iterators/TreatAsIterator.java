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

import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;

public class TreatAsIterator extends CurrentBasedIterator {

	TypeInfo type; 	
	public TreatAsIterator(Context ctx, XDMIterator subIter, TypeInfo type, QueryLocation location) {
		super(ctx, location);
		this.type = type;
		setSubIters(new SequenceTypeIterator(type, true, false, ctx, location )); 
		
		subIters[0].setSubIters(subIter);
	}

	public Token next() throws MXQueryException {
		if (current == null)
			current = subIters[0];
		try {
			return current.next();
		} catch (TypeException te) {
			if (te.getErrorCode().equals(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE))
				throw new DynamicException(ErrorCodes.E0050_DYNAMIC_TREAT_OPERAND_NOT_A_SEQUENCE_TYPE,"Dynamic Type incorrect in Treat as", loc);
			else
				throw te;
		}
		
	}

	public TypeInfo getStaticType() {
		return type;
	}

	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		return new TreatAsIterator(context, 
				subIters[0], 
				type, loc);
	}
	
}


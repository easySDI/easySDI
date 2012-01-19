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
package ch.ethz.mxquery.extensionsModules.util;

import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.LongToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;

public class Random extends TokenBasedIterator {
		
	protected void init() throws MXQueryException {
		
		long seed;
		if (subIters.length == 0) {
			seed = context.getCurrentTime().getMiliseconds()+context.getCurrentTime().getHours();
		} else {
			Token tok = subIters[0].next();
			if (tok == Token.END_SEQUENCE_TOKEN || !Type.isTypeOrSubTypeOf(tok.getEventType(),Type.INTEGER,Context.getDictionary()))
					throw new DynamicException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,"Single integer value expected",loc);
			seed = tok.getLong();
			tok = subIters[0].next();
			if (tok != Token.END_SEQUENCE_TOKEN)
				throw new DynamicException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,"Single integer value expected",loc);
		}
		java.util.Random random = new java.util.Random(seed);
		currentToken =  new LongToken(Type.INTEGER, null, random.nextInt());
	}

	public TypeInfo getStaticType() {
		return new TypeInfo(Type.INTEGER,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
	}		
	
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new Random();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;
	}
}

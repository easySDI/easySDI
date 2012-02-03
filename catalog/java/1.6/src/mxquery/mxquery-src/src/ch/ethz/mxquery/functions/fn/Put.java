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
package ch.ethz.mxquery.functions.fn;

import java.io.File;
import java.net.URI;
import java.net.URISyntaxException;
import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.Identifier;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.types.TypeLexicalConstraints;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.iterators.SequenceTypeIterator;
import ch.ethz.mxquery.iterators.update.UpdateIterator;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.model.updatePrimitives.UpdatePrimitive;

public class Put extends UpdateIterator {

	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack)
			throws MXQueryException {
		XDMIterator copy = new Put();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;	

	}

	public Put() {
		super(null, UpdatePrimitive.PUT, null);
	}
	
	protected void createUpdateList() throws MXQueryException {
		TypeInfo ti = new TypeInfo(Type.NODE,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
		XDMIterator nodeParam = new SequenceTypeIterator(ti,true, false, context,loc);
		Token tok;
		nodeParam.setSubIters(subIters[0]);
		nodeParam.setResettable(true);
		Identifier targetID;
		try {
			tok = nodeParam.next();
		} catch (TypeException te) {
			if (te.getErrorCode().equals(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE))
				throw new DynamicException(ErrorCodes.UF0001_UPDATE_FUNCTION_UNSUPPORTED_NODE,"Cannot store this data with put:", loc);
			else throw te;
		}
		if (!(tok.getEventType() == Type.START_DOCUMENT || tok.getEventType() == Type.START_TAG))
			throw new DynamicException(ErrorCodes.UF0001_UPDATE_FUNCTION_UNSUPPORTED_NODE, "Cannot store this data with put: "+Type.getTypeQName(tok.getEventType(),Context.getDictionary()), loc);
		targetID = tok.getId();
		nodeParam.reset();
		
		
		XDMIterator uriIt = subIters[1];
		String add = uriIt.next().getText();
		
		URI uri = null;
		
		try {
			if (!TypeLexicalConstraints.isRelativeURI(add)) {
				uri = new URI(add);
			} else {
				String base = context.getBaseURI();
				String add1 = add;
				if (add1.startsWith("/"))
					add1 = add1.substring(1);
				uri = new URI(base + add1);
			}
		} catch (URISyntaxException se) {			
			throw new DynamicException(ErrorCodes.UF0002_UPDATE_FUNCTION_INVALID_TARGET_URI, "Invalid target for put():"+ add, loc);
		}
		
		File fn = new File(uri);
				
		this.getPendingUpdateList().add(new ch.ethz.mxquery.model.updatePrimitives.Put(targetID,nodeParam,fn.toURI().toString()));

	}
}

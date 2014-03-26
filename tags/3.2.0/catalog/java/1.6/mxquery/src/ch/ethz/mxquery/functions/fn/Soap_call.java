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

import java.io.IOException;
import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.iterators.NodeIterator;
import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.model.ws.SoapInvoker;
import ch.ethz.mxquery.util.StringReader;

import ch.ethz.mxquery.xdmio.XDMInputFactory;
import ch.ethz.mxquery.xdmio.XDMSerializer;
import ch.ethz.mxquery.xdmio.XMLSource;

public class Soap_call extends CurrentBasedIterator {
	public Token next() throws MXQueryException{
		if (called == 0) {
			init();
			called++;
		}		
		return current.next();
	}
	
	protected void init() throws MXQueryException {
		if (subIters[0] == null) {
			throw new IllegalArgumentException();
		}
		XDMIterator iter = subIters[0];
		Token tok;
		try {
		 tok = iter.next();
		} catch (TypeException de) {
			if (de.getErrorCode().equals(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE))
			throw new DynamicException(ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR,de.getMessage(), loc);
			else throw de;
		}
		String location = tok.getValueAsString();

		if (subIters[1] == null) {
			throw new IllegalArgumentException();
		}
		iter = subIters[1];
		try {
		 tok = iter.next();
		} catch (TypeException de) {
			if (de.getErrorCode().equals(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE))
			throw new DynamicException(ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR,de.getMessage(), loc);
			else throw de;
		}
		String method = tok.getValueAsString();

		if (subIters[2] == null) {
			throw new IllegalArgumentException();
		}
		iter = subIters[2];
		try {
		 tok = iter.next();
		} catch (TypeException de) {
			if (de.getErrorCode().equals(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE))
			throw new DynamicException(ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR,de.getMessage(), loc);
			else throw de;
		}
		//String header = tok.getValueAsString();
		
		
		if (subIters[3] == null) {
			throw new IllegalArgumentException();
		}
		iter = subIters[3];
		XDMSerializer is = new XDMSerializer();
		String soapEnv = is.eventsToXML(iter);
		
		//TODO: using the header information!
		SoapInvoker si = new SoapInvoker(location,method, null, soapEnv);
		String soapResult = null;
		try {
			soapResult = si.query(loc, false);
		} catch (IOException e) {
			throw new DynamicException(ErrorCodes.A0007_EC_IO,"I/O Error when invoking SOAP "+e.toString(),loc);
		}
		XMLSource xmlIt = XDMInputFactory.createXMLInput(context, new StringReader(soapResult), false, context.getInputValidationMode(), loc);
		xmlIt.setURI(location);
		current = new NodeIterator(context, xmlIt,loc);
		
		//currentToken = new TextToken(null, soapResult);
	}
	public TypeInfo getStaticType() {
		return new TypeInfo(Type.NUMBER,Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
	}	
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new Soap_call();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;	
	}
}

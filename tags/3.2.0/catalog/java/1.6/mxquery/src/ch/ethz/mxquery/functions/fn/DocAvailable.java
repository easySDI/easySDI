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
import java.io.IOException;
import java.io.InputStream;
import java.net.MalformedURLException;
import java.net.URI;
import java.net.URISyntaxException;
import java.net.URL;
import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.types.TypeLexicalConstraints;
import ch.ethz.mxquery.datamodel.xdm.BooleanToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;

public class DocAvailable extends TokenBasedIterator {

	protected void init() throws MXQueryException {
		XDMIterator parameter = subIters[0];
		if (parameter == null) {
			// this should never happen!!!
			throw new IllegalArgumentException("No parameter for fn:doc-available() function given!");
		}
		Token tok = parameter.next(); 
		int type = Type.getEventTypeSubstituted(tok.getEventType(), Context.getDictionary());

		if (type == Type.END_SEQUENCE) {
			currentToken = BooleanToken.FALSE_TOKEN;
			return;
		}
		if (type == Type.STRING || type == Type.UNTYPED_ATOMIC || type == Type.UNTYPED) {
			String add=tok.getText();
			URI uri;
			
			if (!TypeLexicalConstraints.isValidURI(add))
				throw new DynamicException(ErrorCodes.F0017_INVALID_ARGUMENT_TO_FN_DOC,"Invalid URI given to fn:doc-available", loc);
			try {
				if (TypeLexicalConstraints.isAbsoluteURI(add)) {
					uri = new URI(add);
				} else {
					String base = context.getBaseURI();
					String add1 = add;
					if (add1.startsWith("/"))
						add1 = add1.substring(1);
					uri = new URI(base + add1);
				}
			} catch (URISyntaxException se) {
				throw new DynamicException(ErrorCodes.F0017_INVALID_ARGUMENT_TO_FN_DOC,"Invalid URI given to fn:doc-available", loc);
			} 
			if(add.startsWith("http://")){
				
					URL url;
					try {
						url = uri.toURL();
					} catch (MalformedURLException e) {
						throw new DynamicException(ErrorCodes.F0017_INVALID_ARGUMENT_TO_FN_DOC,"Invalid URI given to fn:doc-available", loc);
					}
					try {
						InputStream in = url.openStream();
						in.close();
					} catch (IOException e) {
						currentToken = BooleanToken.FALSE_TOKEN;
						return;
					}
					currentToken = BooleanToken.TRUE_TOKEN;
//				}
//				catch(Exception e){
//					throw new DynamicException(ErrorCodes.A0006_EC_URI_NOT_FOUND, "Remote Data cannot be accessed: " + e);
//				}
			}
			else {
				File xml;
				try {
					xml = new File(uri);
				}catch(IllegalArgumentException ia) {
					try {
						xml = new File(add);
					} catch (IllegalArgumentException ia2) {
						throw new DynamicException(ErrorCodes.F0017_INVALID_ARGUMENT_TO_FN_DOC,"Invalid URI given to fn:doc-available", loc);
					}
				}
				if (xml.exists()) {
					currentToken = BooleanToken.TRUE_TOKEN;
				} else {
					currentToken = BooleanToken.FALSE_TOKEN;
				}
			} 
		}
		else
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,"Invalid argument given to fn:doc-available", loc);
	}

	public TypeInfo getStaticType() {
		return new TypeInfo(Type.BOOLEAN,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
	}		
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack)
			throws MXQueryException {
		XDMIterator copy = new DocAvailable();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;	
	}

}

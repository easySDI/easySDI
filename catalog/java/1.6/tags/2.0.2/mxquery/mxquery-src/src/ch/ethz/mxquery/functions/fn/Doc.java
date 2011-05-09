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
import java.io.InputStream;
import java.io.Reader;
import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.Source;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.EmptySequenceIterator;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.util.IOLib;
import ch.ethz.mxquery.util.URIUtils;
import ch.ethz.mxquery.xdmio.XDMInputFactory;
import ch.ethz.mxquery.xdmio.XMLSource;

/**
 * 
 * @author Matthias Braun
 *
 */
public class Doc extends CurrentBasedIterator {
	private boolean inValidateExpression = false;
    private boolean tidyInput = false;
    private boolean createStore = true;
    private String docURI = null;
    
    private InputStream ins = null; // keep around to close
    
	public Doc() {
		super(null, null);
	}

	public boolean isInValidateExpression() {
		return inValidateExpression;
	}

	public void setInValidateExpression(boolean inValidateExpression) {
		this.inValidateExpression = inValidateExpression;
	}	

	public void setTidyInput (boolean tidyInput) {
		this.tidyInput = tidyInput;
	}
	
	public Token next() throws MXQueryException {
		if (called == 0) {
			init();
			called++;
		}
		
		Token tok;
		
		try {
			tok = current.next();
		} catch (MXQueryException e) {
			throw new DynamicException(ErrorCodes.F0014_ERROR_RETRIEVING_RESOURCE,"Error generating XML from file "+e.getMessage(),loc);
		} 
		if (tok == Token.END_SEQUENCE_TOKEN) {
			try {
				if (ins != null)
				ins.close();
				ins = null;
			} catch (IOException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
		}
		return tok;
	}

	
	protected void init() throws MXQueryException {
		XDMIterator parameter = subIters[0];
		if (parameter == null) {
			// this should never happen!!!
			throw new IllegalArgumentException("No parameter for fn:doc() function given!");
		}
		Token tok = parameter.next();
		int type = Type.getEventTypeSubstituted(tok.getEventType(), Context.getDictionary());

		if (type == Type.END_SEQUENCE) {
			current = new EmptySequenceIterator(context, loc);
			return;
		}
		if (type == Type.STRING || type == Type.UNTYPED_ATOMIC || type == Type.UNTYPED) {

			String add = tok.getText();
			String uri = null;
			try {
				uri = URIUtils.resolveURI(context.getBaseURI(),add,loc);
			} catch (DynamicException de) {
				if (de.getErrorCode().equals(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE)) {
					throw new DynamicException(ErrorCodes.F0017_INVALID_ARGUMENT_TO_FN_DOC, "Invalid URI given to fn:doc(): "+add, loc);
				}
				else 
					throw de;
			}
			Reader rd = IOLib.getInput(uri, loc);
			XMLSource cur;
			try {
			if (isInValidateExpression())
				cur = XDMInputFactory.createXMLInput(context, rd, true, Context.SCHEMA_VALIDATION_STRICT, loc);
			else if (tidyInput)
				cur = XDMInputFactory.createTidyInput(context, rd, loc);			
			else cur = XDMInputFactory.createXMLInput(context, rd, true, context.getInputValidationMode(), loc);
			docURI = uri;
			createStore(uri, cur);
//			String strUri = xml.toURI().toString();
//			createStore(strUri, cur);
			} catch (MXQueryException me) {
				try {
					ins.close();
				} catch (IOException ie) {
					//
				}
				throw me;
			}

		} 
	}
	private void createStore(String uri, XMLSource cur)
			throws MXQueryException, DynamicException {
		if (createStore) {
		cur.setURI(uri);
		Source newSource = context.getStores().createStore(uri,cur, true);
		current = newSource.getIterator(context);
		} else {
			current = cur;
		}
	}


	public TypeInfo getStaticType() {
		return new TypeInfo(Type.START_DOCUMENT, Type.OCCURRENCE_IND_ZERO_OR_ONE, null, null);
	}

	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		Doc copy = new Doc();
		copy.setContext(context, true);
		copy.setInValidateExpression(inValidateExpression);
		copy.setConstModePreserve(this.constModePreserve);
		copy.setSubIters(subIters);
		return copy;
	}

	public Window getStoreWindow() throws MXQueryException {
		if (called == 0) {
			init();
			called++;
		}
		return (Window) current;
	}

	public void setCreateStore(boolean cs) {
		createStore = cs;
	}
	
	public String getDocURI() {
		return docURI;
	}
	
	protected void freeResources(boolean restartable) throws MXQueryException {
		super.freeResources(restartable);
		try {
			if (ins != null)
			ins.close();
			ins = null;
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
	}	
}

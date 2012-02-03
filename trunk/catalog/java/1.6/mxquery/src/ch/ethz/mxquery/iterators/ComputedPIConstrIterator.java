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

/**
 *  author RokasT.
 */

package ch.ethz.mxquery.iterators;

import java.util.Vector;

import ch.ethz.mxquery.bindings.WindowFactory;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.IdentifierFactory;
import ch.ethz.mxquery.datamodel.Source;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeLexicalConstraints;
import ch.ethz.mxquery.datamodel.xdm.ProcessingInstrToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.datamodel.xdm.XDMScope;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.model.XDMIterator;


public class ComputedPIConstrIterator extends TokenBasedIterator implements Source{

	XDMScope curNsScope;
	private final static String uri = "http://www.mxquery.org/nodeconstruction/text";
	static int docs = 0;
	private String docId = uri+docs++;
	
	
	public ComputedPIConstrIterator(Context ctx, XDMIterator [] subIters, QueryLocation location, XDMScope nsScope) throws MXQueryException {
		super(ctx, subIters, location);
		if ( subIters.length < 1 || subIters.length > 2)
			throw new MXQueryException(ErrorCodes.A0009_EC_EVALUATION_NOT_POSSIBLE, "Invalid number of parameter iterators", loc);
		curNsScope = nsScope;
	}
	
	protected void init() throws MXQueryException {
		// evaluate PI target 
		Token t = subIters[0].next();
		
		if ( Type.getEventTypeSubstituted( t.getEventType(), Context.getDictionary()) != Type.STRING )
			if( t.getEventType() != Type.UNTYPED_ATOMIC )  // specification requires xs:NCName type
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,"Incorrect type of the parameter in Computed Processing Instruction Constructor: " + Type.getTypeQName(t.getEventType(), Context.getDictionary()), loc );
		
		String piTarget = t.getText().trim(); 
		if((subIters[0].next().getEventType() != Type.END_SEQUENCE) || piTarget == null){
			throw new DynamicException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "The name expression in comment expression cannot be cast to xs:NCName", loc);
		}
		String temp = piTarget.toLowerCase();
		if(temp.equals("xml")){
			throw new DynamicException(ErrorCodes.E0064_DYNAMIC_PI_NAME_EQUALS_XML, "Target NCName for Constructed PI cannot be 'xml'", loc);
		}
		
		if (!TypeLexicalConstraints.validate_NCNAME(Type.NCNAME, piTarget)) {
			throw new DynamicException(ErrorCodes.E0041_DYNAMIC_NAME_EXPRESSION_NOT_CASTABLE_TO_NCNAME, "The name expression in comment expression cannot be cast to xs:NCName", loc);
		}	
		
		String piContent = null;
		
		if ( subIters.length > 1 ) {
			StringBuffer content = new StringBuffer();
			Token tok2 = subIters[1].next(); 
			// Strip Document node
			if (tok2.getEventType() == Type.START_DOCUMENT)
				tok2 =  subIters[1].next();
			while(tok2.getEventType() != Type.END_SEQUENCE) {
				content.append((tok2.getValueAsString() + " "));
				tok2 = subIters[1].next();
			}
			piContent = content.toString();
			if (content.length() != 0)
				piContent = piContent.substring(0, piContent.length()-1);
			piContent = piContent.trim();//Utils.stripLeadingWhitespace(piContent);
			
		}
				
		currentToken = new ProcessingInstrToken(IdentifierFactory.createIdentifier(0, this, null, (short)0), piContent, piTarget, curNsScope);

		this.close(false);
	}

	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		return new ComputedPIConstrIterator(context, subIters, loc, curNsScope);
	}
	public int compare(Source store) {
		if (store.getURI() != null) {
			return uri.compareTo(store.getURI());
		} else {
			return -2;
		}
	}

	public String getURI() {
		return docId;
	}

	public Source copySource(Context ctx, Vector nestedPredCtxStack) throws MXQueryException {
		return (Source) copy(ctx, null, false, nestedPredCtxStack);
	}

	public Window getIterator(Context ctx) throws MXQueryException {
		return WindowFactory.getNewWindow(context, this);
	}		
}

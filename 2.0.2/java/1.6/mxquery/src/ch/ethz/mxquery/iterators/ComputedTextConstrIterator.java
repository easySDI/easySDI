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

import ch.ethz.mxquery.bindings.WindowFactory;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.IdentifierFactory;
import ch.ethz.mxquery.datamodel.Source;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.xdm.TextToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.datamodel.xdm.XDMScope;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.model.XDMIterator;

public class ComputedTextConstrIterator extends TokenBasedIterator implements Source{

	XDMScope curScope;
	
	private final static String uri = "http://www.mxquery.org/nodeconstruction/text";
	static int docs = 0;
	private String docId = uri+docs++;

	
	public ComputedTextConstrIterator(Context ctx, XDMIterator [] subIters, QueryLocation location, XDMScope scope) throws MXQueryException {
		super(ctx, subIters, location);
		if ( subIters.length != 1)
			throw new MXQueryException(ErrorCodes.A0009_EC_EVALUATION_NOT_POSSIBLE, "Invalid number of parameter iterators", loc);
		curScope = scope;
	}
	
	public ComputedTextConstrIterator(Context ctx, QueryLocation location) {
		super(ctx, location);
	}

	
	protected void init () throws MXQueryException {
		// evaluate comment content
		StringBuffer content = new StringBuffer();
		Token inputToken = subIters[0].next(); 
		
		if (inputToken.getEventType() == Type.END_SEQUENCE) {
			currentToken = Token.END_SEQUENCE_TOKEN;
			return;
		}
			
		do {
			content.append((inputToken.getValueAsString() + " "));
			inputToken = subIters[0].next();
		} while(inputToken.getEventType() != Type.END_SEQUENCE);
		
		String s = content.toString();
		if (content.length() != 0)
			s = s.substring(0, s.length()-1);
		
		currentToken = new TextToken(Type.TEXT_NODE_UNTYPED_ATOMIC,IdentifierFactory.createIdentifier(0, this, null, (short)0), s, curScope );

		this.close(false);
	}
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		return new ComputedTextConstrIterator(context, subIters, loc, curScope);
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

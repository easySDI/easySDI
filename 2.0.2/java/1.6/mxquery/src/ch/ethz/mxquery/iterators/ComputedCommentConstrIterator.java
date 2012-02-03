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
import ch.ethz.mxquery.datamodel.xdm.CommentToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.datamodel.xdm.XDMScope;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.model.XDMIterator;



public class ComputedCommentConstrIterator extends TokenBasedIterator implements Source{

	XDMScope curNsScope;
	private final static String uri = "http://www.mxquery.org/nodeconstruction/comment";
	static int docs = 0;
	private String docId = uri+docs++;
	
	public ComputedCommentConstrIterator(Context ctx, XDMIterator [] subIters, QueryLocation location, XDMScope scope) throws MXQueryException {
		super(ctx, subIters,location);
		if ( subIters.length != 1)
			throw new MXQueryException(ErrorCodes.A0009_EC_EVALUATION_NOT_POSSIBLE, "Invalid number of parameter iterators", null);
		curNsScope = scope;
	}

	protected void init() throws MXQueryException {
		StringBuffer content = new StringBuffer();
		Token tok = subIters[0].next(); 
		while(tok.getEventType() != Type.END_SEQUENCE) {
			content.append((tok.getValueAsString() + " "));
			tok = subIters[0].next();
		}
	
		String s = content.toString();
		if (content.length() != 0)
			s = s.substring(0, s.length()-1);
		
		currentToken = new CommentToken(IdentifierFactory.createIdentifier(0, this, null, (short)0), s,curNsScope);

		this.close(false);
	}
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		return new ComputedCommentConstrIterator(context, subIters, loc,curNsScope);
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

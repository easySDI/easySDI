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

package ch.ethz.mxquery.model;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;


public abstract class CurrentBasedIterator extends Iterator {
	
	protected XDMIterator current = null;
	
	public CurrentBasedIterator(){
		this(null, null, null);
	}
	
	public CurrentBasedIterator(Context ctx, QueryLocation location) {
		this(ctx, null, location);
	}
	
	public CurrentBasedIterator(Context ctx, XDMIterator[] subIters, QueryLocation location) {
		super(ctx, subIters, location);
	}
	
	protected void resetImpl() throws MXQueryException {
		super.resetImpl();
		current = null;
	}
	
	protected Token getNext() throws MXQueryException {
		Token tok = current.next();
		int i = tok.getEventType();
		if (i == Type.START_TAG || i == Type.START_DOCUMENT) {
			depth++;
		} else if (i == Type.END_TAG || i == Type.END_DOCUMENT) {
			depth--;
		}

		return tok;
	}
}


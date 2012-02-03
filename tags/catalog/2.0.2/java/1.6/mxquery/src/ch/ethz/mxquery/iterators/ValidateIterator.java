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

import java.io.Reader;
import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.util.StringReader;
import ch.ethz.mxquery.xdmio.XDMInputFactory;
import ch.ethz.mxquery.xdmio.XDMSerializer;

public class ValidateIterator extends CurrentBasedIterator {

	private QueryLocation loc;

	private int mode;

	public ValidateIterator(int mode, Context ctx, XDMIterator[] subIters, QueryLocation loc) {
		super(ctx, subIters, null);
		this.subIters = subIters;
		this.loc = loc;
		this.mode = mode;
	}

	protected void init() throws MXQueryException {
		XDMSerializer ip = new XDMSerializer();
		String strResult = ip.eventsToXML(subIters[0]);
		Reader reader = new StringReader(strResult);
		current = XDMInputFactory.createXMLInput(context, reader, false, mode, loc);
	}

	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		return new ValidateIterator(this.mode, this.context, subIters, loc);
	}

	public Token next() throws MXQueryException {
		if (called == 0) {
			init();
			called++;
		}
		Token tok = current.next();
		return tok;
	}

}

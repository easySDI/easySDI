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

import java.util.Vector;

import ch.ethz.mxquery.bindings.WindowFactory;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.model.XDMIterator;

public class Reverse extends CurrentBasedIterator  {
	
	private Window buffer;
	private int curPos;
	
	protected void init() throws MXQueryException {
		if (subIters[0] == null) {
			throw new IllegalArgumentException();
		}
		buffer = WindowFactory.getNewWindow(getContext(), subIters[0]);
		
		int pos = 1;
		while (buffer.hasItem(pos)) {
			pos++;
		}
		curPos = pos - 1;
		current = buffer.getItem(curPos);
	}
	
	public Token next() throws MXQueryException {
		if (called == 0) {
			init();
			called = 1;
		}
		return nextToken(); 
	}
	
	private Token nextToken() throws MXQueryException {
		Token cur = current.next();
		while (cur == Token.END_SEQUENCE_TOKEN) {
			if (curPos < 1) {
				return Token.END_SEQUENCE_TOKEN;
			}
			else {
				curPos--;
				current = buffer.getItem(curPos);
			}
			cur = current.next();
		}
		return cur;
	}	
	
	public TypeInfo getStaticType() {
		return new TypeInfo(Type.ITEM,Type.OCCURRENCE_IND_ZERO_OR_MORE,null,null);
	}	

	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new Reverse();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;	
	}
}

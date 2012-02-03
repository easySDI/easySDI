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

package ch.ethz.mxquery.sms.iterators;

import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.model.Iterator;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.sms.interfaces.StreamStore;

public class ResultDataIterator extends Iterator{
	
	private StreamStore buffer = null;
	private int currentPos = 0;
	private int endPos = 0;
	private Token activeToken = null;
	
	public ResultDataIterator(StreamStore sb, int startPos, int endPos){
		super(null,null);
		this.currentPos = startPos;
		this.buffer = sb;
		this.endPos = endPos;
	}
	
	public Token next() throws MXQueryException {
		
		if ( currentPos >= endPos ){
			activeToken = Token.END_SEQUENCE_TOKEN;
			return activeToken;
		}
		
		this.activeToken = buffer.get(currentPos);
		
		//this should never happen, but better be safe than sorry :)
		if ( this.activeToken == null ){
			activeToken = Token.END_SEQUENCE_TOKEN;
			return activeToken;
		}
		
		currentPos++;
		
		return activeToken;
	}

	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack)
			throws MXQueryException {
		throw new RuntimeException("Not implemented yet");
	}
}

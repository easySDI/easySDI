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
package ch.ethz.mxquery.bindings;

import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.util.IntegerList;

/**
 * Dummy iterator to represent an empty window
 * @author Tim Kraska (<a href="mailto:mail@tim-kraska.de">mail@tim-kraska.de</a>)
 *
 */
public final class WindowEmptySequenceIterator extends WindowIterator{
	 
	protected int getNodeId() {
		return END_OF_STREAM_NODE;
	}

	public WindowEmptySequenceIterator(WindowBuffer mat, int windowId){
		super(mat, windowId);
	}

	public XDMIterator getItem(int position) throws MXQueryException {
		return mat.getEmptySequence();
	}

	// public WindowIterator getNewItemWindowIterator(IntegerList values) {
	public Window getNewItemWindow(IntegerList values) {
		return mat.getEmptySequence();
	}

	public Window getNewWindow(int startPosition, int endPosition) {
		return mat.getEmptySequence();
	}

	public Token next() throws MXQueryException {
		return Token.END_SEQUENCE_TOKEN;
	}

	public boolean hasItem(int position) throws MXQueryException {
		return false;
	}

	public boolean hasNextItem() throws MXQueryException {
		return false;
	}

	public XDMIterator nextItem() throws MXQueryException {
		return mat.getEmptySequence();
	}

	protected void resetImpl() {
		//nothing has to be done here
	}	
	
	public int getStartNode(){
		return 0;
	}
	
	public int getPosition() { return 0; }
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		WindowEmptySequenceIterator copy = new WindowEmptySequenceIterator(mat.copy(context, nestedPredCtxStack),windowId);
		
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		
		return copy;
	}	
}

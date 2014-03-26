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

import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;

public abstract class WindowTokenIterator  extends WindowIterator {
	protected int currentNodeId;
	protected int nextNodeId;
	protected int tokenId;
	protected boolean tokenIdOutdated=true; 
	protected boolean init=false;;
	protected int startNodeId;
	protected Token activeToken = Token.START_SEQUENCE_TOKEN;	
	protected boolean endOfStream = false;
	
	/**
	 * Creates a new window item iterator. In contrast to a WindowSequenceIterator it allows to define 
	 * sequences which don't exist only of ongoing numbers. But the positions in nodeIds have to be orders, 
	 * such that for the position p1, p2, p3 ... pn the following is valic p1 < p2 <p 3 < .. <pn
	 * @param mat underlying window buffer
	 * @param startNodeId start position
	 * @param id

	 */
	public WindowTokenIterator(WindowBuffer mat, int startNodeId, int id){
		super(mat, id);
		this.startNodeId=startNodeId;
		currentNodeId = startNodeId-1;
		nextNodeId = startNodeId;
	}
	
	protected int getNodeId() {
		if(nextNodeId -1 < startNodeId){
			return -1;
		}
		return nextNodeId -1;
	}
	
	protected void jumpToNodeId(int nodeId){
		nextNodeId = nodeId;
		tokenIdOutdated = true;
	}

	public int getPosition(){
		throw new RuntimeException("This method shouldn't be used anymore");
	}

	protected void catchNextToken() throws MXQueryException {	
		if(tokenIdOutdated){
			tokenId = mat.getTokenIdFromNodeId(nextNodeId) - 1;
			currentNodeId = nextNodeId - 1;
			tokenIdOutdated=false;
		}
		tokenId++;
		activeToken = mat.next(this, tokenId, WindowIterator.END_OF_STREAM_NODE);
		int type = activeToken.getEventType();
		switch(type){
		case Type.START_TAG:
			if(depth == 0){
				currentNodeId++;
			}
			depth++;
			break;
		case Type.END_TAG:
			depth--;
			if(depth == 0){
				nextNodeId++;
			}
			break;
		case Type.END_SEQUENCE:
			endOfStream = true;
		default:
			if(depth == 0){
				currentNodeId++;
				nextNodeId++;
			}
		}
	}

	protected void resetImpl() throws MXQueryException {
		super.resetImpl();
		activeToken = Token.START_SEQUENCE_TOKEN;	
		nextNodeId = startNodeId;
		currentNodeId = startNodeId -1;
		endOfStream=false;
		
	}
	
	public int getStartNode() {
		return this.startNodeId;
	}
	
	/*
	 * xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
	 * Token standard methods
	 * xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
	 */

	protected int min(int a, int b) {
		if (a > b && b >= 0) {
			return b;
		} else {
			return a;
		}
	}

}

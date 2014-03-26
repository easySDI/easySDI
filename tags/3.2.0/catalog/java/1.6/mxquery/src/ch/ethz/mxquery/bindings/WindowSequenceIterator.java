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
import ch.ethz.mxquery.datamodel.Identifier;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.model.VariableHolder;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.model.WindowVariable;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.util.IntegerList;

/**
 * Window Iterators are only created by a WindowBuffer or by a existing
 * WindowIterator. All window iterators operate on window buffer and have a
 * defined range on this buffer. <br>
 * Important definitions:<br>
 * item: Is a item in a sequence <br>
 * position: Position as in XQuery. First position is 1. <br>
 * nodeId: Like Position but starting with 0 <br>
 * tokenId: Id of each token. Also starting with 0 <br>
 * <br>
 * 
 * @author Tim Kraska (<a href="mailto:mail@tim-kraska.de">mail@tim-kraska.de</a>)
 * 
 */
public final class WindowSequenceIterator extends WindowIterator {

	private int startNode;

	private int endNode;
	
	private int activeTokenId=-1;
	
	private Token activeToken = Token.START_SEQUENCE_TOKEN;	
	
	private boolean endOfStream = false;
	
	//The nodeId or the activeTokenId can be outdated but never both!
	private boolean nodeIdOutdated = false;
	private boolean tokenIdOutdated = false;

	
//	/**
//	 * This method should only be called, if the window Buffer allows jumping
//	 */
//	public Token jumpToNextAttribute(String attrName)  throws MXQueryException {
//		if (endOfStream) {
//			return Token.END_SEQUENCE_TOKEN;
//		}
//		if(nodeIdOutdated ){
//			return null;
//		}
//		int	tokenId = mat.getAttributePosFromNodeId(attrName, nodeId);
//		
//		Token token = testTokenJump(attrName, tokenId);
//		if(token != null){
//			activeToken = token;
//			tokenIdOutdated = false;
//			nodeIdOutdated = false;
//			nodeId++;
//			if(nodeId > this.endNode){
//				nodeId = endNode;
//				endOfStream = true;
//			}
//			activeTokenId = tokenId;
//			return token;
//		}else{
//			return null;
//		}
//	}
	
//	private Token testTokenJump(String attrName, int tokenId) throws MXQueryException{
//		if(tokenId == -1){
//			return null;
//		}
//		Token token = mat.next(this, tokenId, endNode);
//		if(Type.isAttribute(token.getEventType())){
//			if(token.getName().equals(attrName)){
//				return token;
//			}else{
//				return null;
//			}
//		}else if(token.getEventType() == Type.END_SEQUENCE){
//			return token;
//		}else{
//			return null;
//		}
//	}

	/**
	 * Creates a new window iterator. The window is defined on a range on the
	 * underlying buffer. For a complete binding startNode = 0 and endNode =
	 * END_OF_STREAM should be used. This constructor should be only called by
	 * window buffer
	 * 
	 * @param mat
	 *            underlying window buffer
	 * @param id
	 *            window id
	 * @param startNode
	 *            first node
	 * @param endNode
	 *            last node
	 * @return a new WindowSequence Iterator with the given parameters
	 */
	protected WindowSequenceIterator(WindowBuffer mat, int id, int startNode, int endNode) {
		super(mat, id);
		this.startNode = startNode;
		this.endNode = endNode;
		this.nodeId = startNode;
		tokenIdOutdated = true;
	}

	public boolean hasItem(int position) throws MXQueryException {
		int node = position + startNode - 1;
		if (startNode <= node && node <= endNode) {
			return mat.hasNode(node);
		} else {
			return false;
		}
	}

	/**
	 * Checks if a next item exists. Especially used in for's or forseq's
	 * 
	 * @return true if the next items exists
	 * @throws MXQueryException
	 */
	public boolean hasNextItem() throws MXQueryException {
		if (endOfStream || nodeId > endNode) {
			return false;
		} else {
			return mat.hasNode(nodeId);
		}
	}
	

	/**
	 * Returns the next item if exist
	 * 
	 * @return an iterator representing the next item, if it exists, otherwise an empty sequence iterator
	 * @throws MXQueryException
	 */
	public XDMIterator nextItem() throws MXQueryException {
		if (hasNextItem()) {
			tokenIdOutdated = true;
			WindowIterator window = mat.getNewWindowIteratorWithNodeIds(nodeId, nodeId);
			if (nodeId > endNode) {
				endOfStream = true;
			} else {
				nodeId++;
			}
			return window;
		} else {
			return mat.getEmptySequence();
		}
	}

	/**
	 * Returns a new window inside this window. Technically this window operates
	 * on the underlying buffer directly but it inherits the border of the
	 * current window
	 * 
	 * @param startPosition
	 *            start position inside the current window
	 * @param endPosition
	 *            end position inside the current window
	 * @return new window iterator
	 */
	public Window getNewWindow(int startPosition, int endPosition) {
		return getNewWindowIteratorWithNodes(startPosition - 1, endPosition-1);
	}

	/**
	 * The same as getNewWindowIterator but instead of position's, nodes id's
	 * are used
	 * 
	 * @param startNode
	 *            start node inside the current window
	 * @param endNode
	 *            end node inside the current window
	 * @return new window iterator
	 */
	private WindowIterator getNewWindowIteratorWithNodes(int startNode, int endNode) {
		if(!isWindowInUse() && startNode == 0 && endNode == END_OF_STREAM_NODE ){
			setWindowInUse(true);
			return this;
		}else{
			return mat.getNewWindowIteratorWithNodeIds(this.startNode + startNode, min(this.endNode, this.startNode + endNode));
		}
	}
	
	public Window getNewEarlyWindowInterface(int startPosition, XDMIterator startExpr, WindowVariable[] startVars, VariableHolder[] startVarHolders, XDMIterator endExpr, WindowVariable[] endVars, VariableHolder[] endVarHolders){
		return mat.getNewEarlyWindowInterface(startPosition, startExpr, startVars, startVarHolders, endExpr, endVars, endVarHolders);
	}

	public Window getNewEarlyParallelWindowInterface(int startPosition, WindowVariable[] startVars, VariableHolder[] startVarHolders, XDMIterator endExpr, WindowVariable[] endVars, VariableHolder[] endVarHolders){
		return mat.getNewEarlyParallelWindowInterface(startPosition, startVars, startVarHolders, endExpr, endVars, endVarHolders);
	}	
	
	public Window getNewItemWindow(IntegerList values) {
		/*if(this.getWindowId() == 1){
			System.out.println("Try to get window from 1");
		}*/
		IntegerList correctedList = new IntegerList();
		for (int i = 0; i < values.size(); i++) {
			int value = values.get(i);
			//if ((this.startNode < value) && value < (this.endNode + 2)) {
			if ((this.startNode <= value) && value <= (this.endNode)) {
				correctedList.add(value);
			}
		} 
		if (correctedList.size() > 0) {
			return mat.getNewWindowIterator(correctedList);
		} else {
			return mat.getEmptySequence();
		}
	}

	private int min(int a, int b) {
		if (a > b && b >= 0) {
			return b;
		} else {
			return a;
		}
	}
	

	/**
	 * xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
	 * Iterator standard methods
	 * xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
	 */

	public Token next() throws MXQueryException {
		called++;
		if (endOfStream) {
			return Token.END_SEQUENCE_TOKEN;
		}
		if(tokenIdOutdated){
			activeTokenId = mat.getTokenIdFromNodeId(nodeId)-1;
			tokenIdOutdated=false;
		}
		nodeIdOutdated = true;
		activeTokenId++;
		activeToken = null;
		activeToken = mat.next(this, activeTokenId, endNode);
		if (activeToken == Token.END_SEQUENCE_TOKEN) {
			endOfStream = true;
		}
		return activeToken;
	}



	protected int getEndNode() {
		return endNode;
	}


	protected int getLastKnowNodeId() {
		return nodeId;
	}

	protected int getNodeId() {
		try{
			if (nodeIdOutdated) {
				nodeId = mat.getNodeIdFromTokenId(activeTokenId);
				nodeIdOutdated = false;
			}
		}catch(MXQueryException err){
			throw new RuntimeException(err.toString());
		}
		return nodeId;
	}

	protected void setNodeId(int nodeId) {
		this.nodeId = nodeId;
		super.setPosition(nodeId - startNode + 1);
		nodeIdOutdated = false;
		tokenIdOutdated=true;
	}

	public int getPosition() {
		if (nodeIdOutdated) {
			getNodeId();
		}
		return min(nodeId, endNode) - startNode + 1;
	}

	public int getStartNode() {
		return startNode;
	}

	public XDMIterator getItem(int position) throws MXQueryException {
		int nodeId = position + startNode - 1;
		if (hasItem(position)) {
			return mat.getNewWindowIteratorWithNodeIds(nodeId, nodeId);
		} else {
			return mat.getEmptySequence();
		}
	}
	
	public WindowBuffer getMat(){
		return mat;
	}
	
	
//	public void increasePosition() {
//		tokenIdOutdated = true;
//		getNodeId();
//		nodeId++;
//		super.increasePosition();
//	}
//
//	
//	public void setPosition(int position) {
//		nodeIdOutdated = false;
//		tokenIdOutdated = true;
//		nodeId = position + startNode - 1;
//		endOfStream = false;
//		super.setPosition(position);
//	}
	
	protected void resetImpl() throws MXQueryException {
		super.resetImpl();
		nodeId = startNode;
		nodeIdOutdated = false;
		tokenIdOutdated=true;
		endOfStream = false;
		activeToken = Token.START_SEQUENCE_TOKEN;
	}

	public String getAttributeType(int i) {
		// TODO: change Exception type to StaticException.NOT_SUPPORTED_ERROR ?
		throw new RuntimeException("getAttributeType for WindowIterator not supported yet");
	}

	public boolean isAttributeDefault(int i) {
		throw new RuntimeException("isAttributeDefault for WindowIterator not supported yet");
	}
	
	public Identifier getId() {
		if (this.activeToken == null) {
			return null;
		} else {
			return this.activeToken.getId();
		}
	}
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		WindowSequenceIterator copy = new WindowSequenceIterator(mat.copy(context, nestedPredCtxStack), super.getWindowId(), startNode, endNode);
		
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		
		return copy;
	}	
	
}

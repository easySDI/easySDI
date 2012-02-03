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

public final class WindowItemIterator extends WindowTokenIterator {
	private IntegerList nodeIds = null;
	private int currentItem = 0;
	
	private boolean jumpToNextNode=false;
	private boolean incrNode=false;
	
	public WindowItemIterator(WindowBuffer mat, int id,  IntegerList nodeIds){
		super(mat, nodeIds.get(0), id);
		if(nodeIds.size() == 0){
			throw new IllegalArgumentException("It is not allowed to use a empty IntegerList here");
		}
		this.nodeIds = nodeIds;
		currentItem = -1;
		incrNode=true;
		jumpToNextNode=true;
	}


	
	protected int getNodeId() {
		if(currentItem > -1){
			return nodeIds.get(currentItem);
		}else{
			return nodeIds.get(0);
		}
	}



	public Token next() throws MXQueryException {
		called++;
		if(incrNode){
			incrNode=false;
			currentItem++;
		}
		if(endOfStream){
			activeToken = Token.END_SEQUENCE_TOKEN;
			return activeToken;
		}
		if(jumpToNextNode){
			jumpToNextNode=false;
			jumpToNodeId(nodeIds.get(currentItem));	
		}
		catchNextToken();
		if(currentNodeId != nextNodeId){
			int nextItem = currentItem +1;
			if(nextItem == nodeIds.size()){
				endOfStream = true;
			}else if(nextNodeId != nodeIds.get(nextItem) ){
				jumpToNextNode=true;
			}
			incrNode=true;
		}
		
		//return type;
		return activeToken;
	}
	
	public boolean hasItem(int position) throws MXQueryException {
		int id = position - 1;
		if(id < nodeIds.size()){
			return mat.hasNode(nodeIds.get(id));
		}else{
			return false;
		}
	}
	
	public XDMIterator nextItem() throws MXQueryException {
		if(hasNextItem()){
			currentItem++;
			return mat.getNewWindowIteratorWithNodeIds(nodeIds.get(currentItem), nodeIds.get(currentItem));
		}else{
			return mat.getEmptySequence();
		}
	}

	

	public boolean hasNextItem() throws MXQueryException {
		int nextNodeId= currentItem + 1 ;
		if(nextNodeId < nodeIds.size()){
			return hasItem(nextNodeId + 1);
		}else{
			return false;
		}
	}

	public XDMIterator getItem(int position) throws MXQueryException {
		int id = position -1;
		if(hasItem(position)){
			return mat.getNewWindowIterator(nodeIds.get(id)+1, nodeIds.get(id)+1);
		}else{
			return mat.getEmptySequence();
		}
	}



	public Window getNewItemWindow(IntegerList values) {
		throw new RuntimeException("This feature is not supported yet!");
	}

	public Window getNewWindow(int startPosition, int endPosition) throws MXQueryException {
		IntegerList newNodes;
		if(startPosition == 1 && endPosition == WindowIterator.END_OF_STREAM_POSITION){
			if(!isWindowInUse() ){
				setWindowInUse(true);
				return this;
			}
			newNodes = nodeIds;
		}else{
			newNodes = nodeIds.copy();
			//Minus -2 because one to get the node ids and one more for the positions
			int toNodeId = startPosition - 2;
			if(0 < toNodeId){
				newNodes.removeRange(0, toNodeId);
			}
			toNodeId = nodeIds.size()-1;
			if(endPosition -1 <  toNodeId){
				newNodes.removeRange(endPosition -1 , toNodeId);
			}
		}
		return mat.getNewWindowIterator(newNodes);
	}

	protected void resetImpl() throws MXQueryException {
		super.resetImpl();
		currentItem = -1;
		incrNode=true;
		jumpToNextNode=true;
	}

	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		WindowItemIterator copy = new WindowItemIterator(mat.copy(context, nestedPredCtxStack), super.getWindowId(), nodeIds.copy());
		
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		
		return copy;
	}

}

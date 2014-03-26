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

package ch.ethz.mxquery.sms.MMimpl;

import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;

public final class BufferItem {
	
	private BufferItem next = null;
	
	private SyncAppendTokenBuffer content = null;
	
	private int firstTokenId = 0;
	private int lastTokenId = 0;
	
	private int firstItemId = 0;
	private int lastItemId = 0;
	
	private boolean isEmpty;
	
	public BufferItem(int firstItem, int firstToken, int gran){
		
		content = new SyncAppendTokenBuffer(gran);
		
		this.firstItemId = firstItem;
		this.firstTokenId = firstToken;
		
		this.lastItemId = firstItem;
		this.lastTokenId = firstToken;
		this.isEmpty = true;
	}
	
	public int getNodeIdFromTokenId(int lastKnownNodeId, int activeTokenId) {
		return content.getNodeIdFromTokenId(lastKnownNodeId-firstItemId,activeTokenId-firstTokenId)+firstItemId;		
	}
	
	public int getTokenIdForNode(int nodeId) throws MXQueryException{	
		return content.getTokenIdForNode(nodeId-firstItemId)+firstTokenId;
	}
	
	public Token get(int tokenId,int endNode) {		
		return content.get(tokenId-firstTokenId,endNode-firstItemId);
	}
	
	public Token get(int tokenId) {		
		return content.get(tokenId-firstTokenId);
	}
	
	public boolean hasNode(int nodeId){
		return content.hasNode(nodeId-firstItemId);
	}
	
	public boolean isEmpty(){
		return isEmpty;
	}
	
	public void setNext(BufferItem ib){
		next = ib;
	}
		
	public BufferItem getNext(){
		return next;
	}
	
	public int getLastNodeId(){
		return lastItemId;
	}
	
	public void setLastNodeId(int node){
		lastItemId = node;
	}
	
	public void setFirstNodeId(int first){
		firstItemId = first;
	}
	
	public int getLastTokenId(){
		return lastTokenId;
	}
	
	public void setLastTokenId(int token){
		lastTokenId = token;
	}
	
	public void setFirstTokenId(int first){
		firstTokenId = first;
	}
	
	public int getFirstTokenId(){
		return firstTokenId;
	}
	
	public int getFirstItemId(){
		return firstItemId;
	}
	
	public void clear(){
		isEmpty = true;
		firstItemId = 0;
		firstTokenId = 0;
		lastItemId = 0;
		lastTokenId = 0;
		content.clear();
	}
	
	public void indexNewNode(){
		content.indexNewNode();
		lastItemId++;
		isEmpty = false;
	}
	
	public void bufferToken(Token tok){
		content.bufferToken(tok);
		lastTokenId++;
		isEmpty = false;
	}
	
	public boolean isEndOfStream(){
		return content.isEndOfStream();
	}
}

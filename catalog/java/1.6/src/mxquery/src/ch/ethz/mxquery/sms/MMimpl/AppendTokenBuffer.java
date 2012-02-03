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

import java.util.concurrent.atomic.AtomicBoolean;
import java.util.concurrent.atomic.AtomicInteger;

import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;

public class AppendTokenBuffer {
	
	private int initialCapacity = 50;		
	private double capacityIncrement = 1.5;		
	private int nodeIndexCapacity = 10;		
	private double nodeIndexIncrement = 1.5;
	
	private AtomicInteger tokenI = new AtomicInteger(0);
	private AtomicInteger nodeI = new AtomicInteger(0);
	private AtomicBoolean endOfStream = new AtomicBoolean(false);
	
	private int[] nodeIndex;
	private Token[] tokenBuffer;
	
	private static final int ItemSize = 13; //HACK: need to know the highest number of tokens in an item
	
	public AppendTokenBuffer(int gran) {
		this.initialCapacity = gran*ItemSize;
		this.nodeIndexCapacity = gran;
		tokenBuffer = new Token[initialCapacity];
		nodeIndex = new int[nodeIndexCapacity];
	}

	public int getTokenIdForNode(int nodeId) throws MXQueryException{		
		if (nodeId < nodeI.get() ) {
			
			return nodeIndex[nodeId];
		} else {
			if (endOfStream.get()) {
				return tokenI.get() - 1;
			} 
			throw new MXQueryException(ErrorCodes.A0009_EC_EVALUATION_NOT_POSSIBLE,"Item Index out of range",null);
		}
	}

	public boolean hasNode(int node) {		
		if (node < nodeI.get() ) {
			return true;
		} else {
			return false;
		}
	}
	
	public int getNodeIdFromTokenId(int tokenId) {
		return getNodeIdFromTokenId(0, tokenId);
	}
	
	public int getNodeIdFromTokenId(int minNodeId, int tokenId)  {
		if (endOfStream.get() && (tokenId >= tokenI.get())) {
			return nodeI.get();
		}		
		int i = (minNodeId + 1);
		
		if (i < 0)
			i = 1;
		
		while (i < nodeI.get() && nodeIndex[i] <= tokenId ) {
			i++;
		}
		return i - 1;
	}
	
	public Token get(int tokenId) {
		if (tokenI.get() > tokenId) {
			return tokenBuffer[tokenId];
		} else {
			return tokenBuffer[tokenI.get() - 1];
		}
	}
	
	public Token get(int tokenId, int maxNodeId)  {
		
		if ((maxNodeId + 1) < nodeI.get() && (maxNodeId + 1 >= 0 )){
			if (tokenId == nodeIndex[(maxNodeId + 1)]) {
				return Token.END_SEQUENCE_TOKEN;
			}
		}
		
		Token token = get(tokenId);
		
		return token;
	}
	
	public void indexNewNode() {
		
		if (nodeI.get() == nodeIndex.length) {
//			System.out.println("INCREASE SIZE FOR INDEX");
			nodeIndexCapacity = (int) (nodeIndexCapacity * nodeIndexIncrement);
			int[] newIndex = new int[nodeIndexCapacity];
			System.arraycopy(nodeIndex, 0, newIndex, 0, nodeI.get());
			nodeIndex = newIndex;
		}
		nodeIndex[nodeI.get()] = tokenI.get();
		nodeI.getAndIncrement();
	}
	
	public void bufferToken(Token token) { 
		
		if (tokenI.get() == tokenBuffer.length) {	
//			System.out.println("INCREASE SIZE FOR BUFFER");
			initialCapacity = (int) (initialCapacity * capacityIncrement);
			Token[] newTokenBuffer = new Token[initialCapacity];
			System.arraycopy(tokenBuffer, 0, newTokenBuffer, 0, tokenI.get());
			tokenBuffer = newTokenBuffer;
		}
		
		tokenBuffer[tokenI.get()] = token;
		tokenI.getAndIncrement();
		
		if ( token.getEventType() == Type.END_SEQUENCE ){
			endOfStream.set(true);
		}
	}
	
	public int getSize(){
		return tokenI.get();
	}
	
	public boolean isEndOfStream(){
		return endOfStream.get();
	}
	
	public void clear(){
		tokenI = new AtomicInteger(0);
		nodeI =  new AtomicInteger(0);
	}
}

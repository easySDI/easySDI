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
import java.util.concurrent.locks.ReentrantReadWriteLock;

import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;

public final class SyncAppendTokenBuffer {
	
	private int initialCapacity = 50;		
	private double capacityIncrement = 1.5;		
	private int nodeIndexCapacity = 10;		
	private double nodeIndexIncrement = 1.5;
	private AtomicInteger tokenI = new AtomicInteger(0);
	private AtomicInteger nodeI = new AtomicInteger(0);
	private int[] nodeIndex;
	private Token[] tokenBuffer;
	private AtomicBoolean endOfStream = new AtomicBoolean(false);
	//private AtomicBoolean empty = new AtomicBoolean(false);
	private static final int ItemSize = 12;
	//private AtomicInteger delItems = new AtomicInteger(0);
	
	private ReentrantReadWriteLock itemLock = new ReentrantReadWriteLock();
	private ReentrantReadWriteLock tokenLock = new ReentrantReadWriteLock();
	
	public SyncAppendTokenBuffer(int gran) {
		this.initialCapacity = gran*ItemSize;
		this.nodeIndexCapacity = gran;
		tokenBuffer = new Token[initialCapacity];
		nodeIndex = new int[nodeIndexCapacity];
	}

	public int getTokenIdForNode(int nodeId) throws MXQueryException {		
		if (nodeId < nodeI.get() ) {
			
			int tokid = 0;
			
			itemLock.readLock().lock();
			tokid = nodeIndex[nodeId];
			itemLock.readLock().unlock();
			
			return tokid;
		} else {
			if (endOfStream.get()) {
				return tokenI.get() - 1;
			} 
			throw new MXQueryException(ErrorCodes.A0009_EC_EVALUATION_NOT_POSSIBLE, "This shouldn't happen",QueryLocation.OUTSIDE_QUERY_LOC);
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
		
		while (i < nodeI.get()){
			int tokid = 0;
			
			itemLock.readLock().lock();
			tokid = nodeIndex[i];
			itemLock.readLock().unlock();
			
			if ( tokid <= tokenId ) {
				i++;
			}
			else
				break;
		}
		return i - 1;
	}
	
	public Token get(int tokenId) {
		
		Token tok = null;
		
		if (tokenI.get() > tokenId) {
			
			tokenLock.readLock().lock();
			tok = tokenBuffer[tokenId];
			tokenLock.readLock().unlock();
			
			return tok;
		} else {
			
			tokenLock.readLock().lock();
			tok = tokenBuffer[tokenI.get() - 1];
			tokenLock.readLock().unlock();
			
			return tok;
		}
	}
	
	public Token getNoWait(int tokenId) throws MXQueryException {
		
		Token tok = null;
		
		if (tokenI.get() > tokenId) {
			
			tokenLock.readLock().lock();
			tok = tokenBuffer[tokenId];
			tokenLock.readLock().unlock();
			
			return tok;
		}
		
		return null;
	}
	
	public Token get(int tokenId, int maxNodeId){
		
		if ((maxNodeId + 1) < nodeI.get() && (maxNodeId + 1 >= 0 )){
			
			int tokid = 0;
			
			itemLock.readLock().lock();
			tokid = nodeIndex[(maxNodeId + 1)];
			itemLock.readLock().unlock();
			
			
			if (tokenId == tokid ) {
				return Token.END_SEQUENCE_TOKEN;
			}
		}
		
		Token token = get(tokenId);
		
		return token;
	}
	
	public void indexNewNode() {
		
		if (nodeI.get() == nodeIndex.length) {
			System.out.println("INCREASE SIZE FOR INDEX");
			nodeIndexCapacity = (int) (nodeIndexCapacity * nodeIndexIncrement);
			int[] newIndex = new int[nodeIndexCapacity];
			
			itemLock.readLock().lock();
			System.arraycopy(nodeIndex, 0, newIndex, 0, nodeI.get());
			itemLock.readLock().unlock();
			
			itemLock.writeLock().lock();
			nodeIndex = newIndex;
			itemLock.writeLock().unlock();
		}
		
		itemLock.writeLock().lock();
		nodeIndex[nodeI.get()] = tokenI.get();
		itemLock.writeLock().unlock();
		
		nodeI.getAndIncrement();
	}
	
	public void bufferToken(Token token) { 
		
		if (tokenI.get() == tokenBuffer.length) {	
			System.out.println("INCREASE SIZE FOR BUFFER");
			initialCapacity = (int) (initialCapacity * capacityIncrement);
			Token[] newTokenBuffer = new Token[initialCapacity];
			
			tokenLock.readLock().lock();
			System.arraycopy(tokenBuffer, 0, newTokenBuffer, 0, tokenI.get());
			tokenLock.readLock().unlock();
			
			tokenLock.writeLock().lock();
			tokenBuffer = newTokenBuffer;
			tokenLock.writeLock().unlock();
		}
		
		tokenLock.writeLock().lock();
		tokenBuffer[tokenI.get()] = token;
		tokenLock.writeLock().unlock();
		
		tokenI.getAndIncrement();
		
		if ( token == Token.END_SEQUENCE_TOKEN ){
			endOfStream.set(true);
		}
	}
	
	public int getSize(){
		return tokenI.get();
	}
	
	public int getMaxNodeId() {
		return nodeI.get();
	}
	
	public int getMaxTokenId() {
		return tokenI.get();
	}
	
	public boolean isEndOfStream(){
		return endOfStream.get();
	}
	
	public void clear(){
		tokenI = new AtomicInteger(0);
		nodeI =  new AtomicInteger(0);
	}
	
	public int getCurrentTokenId(){
		return tokenI.get();
	}
}



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

import ch.ethz.mxquery.bindings.WindowBuffer;
import ch.ethz.mxquery.sms.interfaces.RandomRead;

public class SeqFIFOStore extends FIFOStore implements RandomRead {
	
	
	private BufferItem first = null;
	private BufferItem last = null;

	BufferItem cache = null;
	
	public SeqFIFOStore(int id, int blockSize, WindowBuffer container){
		super(id, container);
		granularity = blockSize;
	}
	
	public void start(){
		
		//this.readThread = new ReadDataThread(this.iterator);
		this.readThread.init(this);
		this.readThread.setName("MAIN-READ-DATA-THREAD_"+getMyId());
		try {
			newToken.acquire();
		} catch (InterruptedException e) {
			e.printStackTrace();
		}
		try {
			newItem.acquire();
		} catch (InterruptedException e) {
			e.printStackTrace();
		}
		this.readThread.start();
	}
	
	protected void addNewBufferNode(){
		int crtItem = 0;
		int crtToken = 0;
		if (current != null ){
			crtItem = current.getLastNodeId();
			crtToken = current.getLastTokenId();
		}
		BufferItem tb = new BufferItem(crtItem,crtToken,granularity);

		if ( first == null ){
			first = tb;
			last = tb;
		}
		else{			
			last.setNext(tb);
			last = tb;
		}
		tb.setNext(first);
		
		current = tb;
		size++;

	}
	
	protected BufferItem bufferToToken(int activeTokenId){
		BufferItem currentBuffer = null;
		
		
		currentBuffer = cache;
		
		if ( currentBuffer == null )
			currentBuffer = first;
		
		if ( !(activeTokenId >= currentBuffer.getFirstTokenId() && activeTokenId < currentBuffer.getLastTokenId())) {
			BufferItem tmpBuff = currentBuffer;
			currentBuffer = currentBuffer.getNext();
			
			while (!(activeTokenId >= currentBuffer.getFirstTokenId() && activeTokenId < currentBuffer.getLastTokenId()) 
					&& currentBuffer != tmpBuff){
				//System.out.println("go to next");
				currentBuffer = currentBuffer.getNext();
			}
		
			//cacheList.put(name,currentBuffer);
			
			cache = currentBuffer;			
		}		
		return currentBuffer;
	}
	
	protected BufferItem bufferToItem(int nodeId) {
		BufferItem currentBuffer = null;
		
		currentBuffer = cache;
		
		if ( currentBuffer == null )
			currentBuffer = first;
		
		if ( !(nodeId >= currentBuffer.getFirstItemId() && nodeId < currentBuffer.getLastNodeId()) ) {

			//BufferItem tmpBuff = currentBuffer;
			currentBuffer = currentBuffer.getNext();
			cache = currentBuffer;
		}
		if (currentBuffer == null )
			System.out.print("");
		
		return currentBuffer;
	}
	
	protected void freeBuffers(){		
		
		if ( deleteFrom <= 1 || first == last)
			return;
		
		boolean firstTime = true;
		
		
		while ( first.getLastNodeId() < deleteFrom && first!=last){	
			
			if (!firstTime){
				first.clear();
				first = first.getNext();
				last.setNext(first);
				size--;
			}
			else
			{
				
				int minNodeId = last.getLastNodeId();
				int minTokId = last.getLastTokenId();
				
				first = first.getNext();
				last = last.getNext();
				
				current = last;
				current.clear();
				current.setFirstNodeId(minNodeId);
				current.setFirstTokenId(minTokId);
				current.setLastNodeId(minNodeId);
				current.setLastTokenId(minTokId);
								
				firstTime = false;
				isFreeBuffer = true;
				
			}
		}	
	}	
}

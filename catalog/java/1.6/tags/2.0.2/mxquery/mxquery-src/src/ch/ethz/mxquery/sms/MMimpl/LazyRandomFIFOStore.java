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

import java.util.Map;
import java.util.Set;
import java.util.TreeMap;
import java.util.concurrent.locks.ReentrantReadWriteLock;

import ch.ethz.mxquery.bindings.WindowBuffer;
import ch.ethz.mxquery.sms.interfaces.RandomRead;

public class LazyRandomFIFOStore extends LazyFIFOStore implements RandomRead {
	
	private BufferItem first = null;
	private BufferItem last = null;
	
	private Map itemStarts = null;
	private Map tokenStarts = null;

//	private Map<Integer,BufferItem> itemStarts = null;
//	private Map<Integer,BufferItem> tokenStarts = null;


	private ReentrantReadWriteLock itemLock = new ReentrantReadWriteLock();
	
	private boolean sameSchema = true;
	
	//Map<String,BufferItem> cacheList = Collections.synchronizedMap(new TreeMap<String,BufferItem>());
	
	BufferItem cache = null;
	
	public LazyRandomFIFOStore(int id, int blockSize, WindowBuffer container){
		super(id, container);
		itemStarts = new TreeMap(new ItemIdCompare());	
		tokenStarts = new TreeMap(new ItemIdCompare());	
		granularity = blockSize;
	}
	
	public void start(){

	}
	
//	private void printItemStarts(){
//		System.out.println("Print Item starts");
//		Set itemS = itemStarts.keySet();
//		java.util.Iterator iter = itemS.iterator();
//		
//		while (iter.hasNext()){
//			Integer crtVal = (Integer)iter.next();
//			BufferItem crtBuff = (BufferItem)itemStarts.get(crtVal);
//			System.out.print(crtBuff.getFirstItemId()+ " ");
//		}
//		
//		System.out.println();
//	}
//	
//	private void printTokenStarts(){
//		System.out.println("Print token starts");
//		Set itemS = tokenStarts.keySet();
//		java.util.Iterator iter = itemS.iterator();
//		
//		while (iter.hasNext()){
//			Integer crtVal = (Integer)iter.next();
//			BufferItem crtBuff = (BufferItem)tokenStarts.get(crtVal);
//			System.out.print(crtBuff.getFirstItemId()+ " ");
//		}
//		
//		System.out.println();
//	}
//	
//	private void printBufferContents(){
//		System.out.println("Print buffers contents");
//		BufferItem bi = first;
//		
//		while (true){
//			System.out.print("["+bi.getFirstItemId()+ "," + bi.getLastNodeId()+"]");
//			if ( bi == last )
//				break;
//			bi = bi.getNext();
//		}
//		
//		System.out.println();
//	}
	
	protected void addNewBufferNode(){
		
		//System.out.println(Thread.currentThread().getName() + "Add new buffer node");
		
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
		
		itemLock.writeLock().lock();
		itemStarts.put(new Integer(crtItem/granularity),tb);
		if ( sameSchema ){
			//System.out.println("Put new token start : "+crtToken);
			//System.out.println("Item length : "+itemLength);
			tokenStarts.put(new Integer(crtToken/(granularity*itemLength)),tb);
		}
		itemLock.writeLock().unlock();
		
		current = tb;
		size++;
	}
	
	protected BufferItem bufferToToken(int activeTokenId){
		BufferItem currentBuffer = null;
		
		//String name = Thread.currentThread().getName();
		
		//currentBuffer = (BufferItem)cacheList.get(name);
		
		currentBuffer = cache;
		
		if ( currentBuffer == null || !(activeTokenId >= currentBuffer.getFirstTokenId() && activeTokenId < currentBuffer.getLastTokenId()) ){
			
			if ( sameSchema){
				itemLock.readLock().lock();				
				currentBuffer = (BufferItem)tokenStarts.get(new Integer(activeTokenId/(granularity*itemLength)));				
				itemLock.readLock().unlock();
			}
			else{			
				itemLock.readLock().lock();
				Set itemS = itemStarts.keySet();
				java.util.Iterator iter = itemS.iterator();
				
				while (iter.hasNext()){
					Integer crtVal = (Integer)iter.next();
					BufferItem crtBuff = (BufferItem)itemStarts.get(crtVal);
					if ( (crtBuff.getFirstTokenId()<=activeTokenId ) && crtBuff.getLastTokenId() > activeTokenId){
						currentBuffer = crtBuff;
						break;
					}
				}
				itemLock.readLock().unlock();
			}
			//cacheList.put(name,currentBuffer);
			
			cache = currentBuffer;
		}
		
//		if ( currentBuffer == null || !(activeTokenId >= currentBuffer.getFirstTokenId() && activeTokenId < currentBuffer.getLastTokenId()) ){
//			System.out.println("Request for token id : "+activeTokenId);
//			printItemStarts();
//			printTokenStarts();
//			printBufferContents();
//			System.out.print("");
//		}
		
		return currentBuffer;
	}
	
	protected BufferItem bufferToItem(int nodeId) {
		BufferItem currentBuffer = null;
		
		//String name = Thread.currentThread().getName();
		//currentBuffer = (BufferItem)cacheList.get(name);
		currentBuffer = cache;
		
		if ( currentBuffer == null || !(nodeId >= currentBuffer.getFirstItemId() && nodeId < currentBuffer.getLastNodeId()) ){
			itemLock.readLock().lock();
			currentBuffer = (BufferItem)itemStarts.get(new Integer(nodeId/granularity));
			//System.out.println("Request for "+nodeId+" got buffer item start : "+currentBuffer.getFirstItemId());
			itemLock.readLock().unlock();
			//cacheList.put(name,currentBuffer);
			cache = currentBuffer;
		}
		
//		if ( currentBuffer == null || !(nodeId >= currentBuffer.getFirstItemId() && nodeId < currentBuffer.getLastNodeId())){
//			System.out.println("Request for item id : "+nodeId);
//			printItemStarts();
//			printBufferContents();
//			System.out.print("");
//		}
		
		return currentBuffer;
	}
	
	public void freeBuffers(){		
		
		if ( deleteFrom <= 1 || first == last)
			return;
		
		boolean firstTime = true;
		
		
		while ( (first.getLastNodeId() < deleteFrom) && (first != last) ){
			
			if ( firstTime ){
				
				itemLock.writeLock().lock();
				
				itemStarts.remove(new Integer(first.getFirstItemId()/granularity));
				if ( sameSchema )
					tokenStarts.remove(new Integer(first.getFirstTokenId()/(granularity*itemLength)));
				
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
				
				if ( sameSchema)
					tokenStarts.put(new Integer(minTokId/(granularity*itemLength)),last);
				itemStarts.put(new Integer(minNodeId/granularity),last);
				itemLock.writeLock().unlock();
				
				firstTime = false;
				isFreeBuffer = true;
			}
			else{
				itemLock.writeLock().lock();
				itemStarts.remove(new Integer(first.getFirstItemId()/granularity));
				if ( sameSchema )
					tokenStarts.remove(new Integer(first.getFirstTokenId()/(granularity*itemLength)));
				itemLock.writeLock().unlock();
				
				first.clear();
				first = first.getNext();
				//last.setNext(first);
				size--;
			}
		}
		
		if ( !isFreeBuffer ) {
			if ( current.getNext() != first ){
				itemLock.writeLock().lock();
				
				int minNodeId = last.getLastNodeId();
				int minTokId = last.getLastTokenId();
				
				//first = first.getNext();
				last = last.getNext();
				
				current = last;
				current.clear();
				current.setFirstNodeId(minNodeId);
				current.setFirstTokenId(minTokId);
				current.setLastNodeId(minNodeId);
				current.setLastTokenId(minTokId);
				
				if ( sameSchema)
					tokenStarts.put(new Integer(minTokId/(granularity*itemLength)),last);
				itemStarts.put(new Integer(minNodeId/granularity),last);
				itemLock.writeLock().unlock();
	
				isFreeBuffer = true;
			}
		}
		
		//printItemStarts();
		
			
		
//		while ( first.getLastNodeId() < deleteFrom && first!=last){	
//			
//			if (!firstTime){
//				itemLock.writeLock().lock();
//				itemStarts.remove(new Integer(first.getFirstItemId()/granularity));
//				
//				if ( sameSchema )
//					tokenStarts.remove(new Integer(first.getFirstTokenId()/(granularity*itemLength)));
//				
//				itemLock.writeLock().unlock();
//				first.clear();
//				first = first.getNext();
//				last.setNext(first);
//				size--;
//			}
//			else
//			{
//				itemLock.writeLock().lock();
//				itemStarts.remove(new Integer(first.getFirstItemId()/granularity));
//				
//				if ( sameSchema )
//					tokenStarts.remove(new Integer(first.getFirstTokenId()/(granularity*itemLength)));
//				
//				int minNodeId = last.getLastNodeId();
//				int minTokId = last.getLastTokenId();
//				
//				first = first.getNext();
//				last = last.getNext();
//				
//				current = last;
//				current.clear();
//				current.setFirstNodeId(minNodeId);
//				current.setFirstTokenId(minTokId);
//				current.setLastNodeId(minNodeId);
//				current.setLastTokenId(minTokId);
//				
//				if ( sameSchema)
//					tokenStarts.put(new Integer(minTokId/(granularity*itemLength)),last);
//				itemStarts.put(new Integer(minNodeId/granularity),last);
//				itemLock.writeLock().unlock();
//				
//				firstTime = false;
//				isFreeBuffer = true;
//				
//			}
//		}
	}
}

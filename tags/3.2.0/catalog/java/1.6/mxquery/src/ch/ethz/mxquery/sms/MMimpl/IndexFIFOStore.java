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

import java.util.HashMap;
import java.util.LinkedList;
import java.util.List;
import java.util.Map;
import java.util.Set;

import java.util.concurrent.Semaphore;
import java.util.concurrent.atomic.AtomicInteger;
import java.util.concurrent.locks.ReentrantReadWriteLock;


import ch.ethz.mxquery.bindings.WindowBuffer;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.opt.index.IndexSchema;
import ch.ethz.mxquery.opt.index.SimpleEqIndex;

import ch.ethz.mxquery.sms.activeStore.ReadThreadUpdate;
import ch.ethz.mxquery.sms.interfaces.IndexRead;
import ch.ethz.mxquery.sms.iterators.ResultDataItemsIterator;

import ch.ethz.mxquery.util.IntegerList;

public class IndexFIFOStore extends RandomFIFOStore implements IndexRead {
	
	private int schemaIndex = 0;
	private Map indexes = null;

	private boolean done = false;
	
	private ReadThreadUpdate pullThread = null;
	
	private AtomicInteger itemSize = new AtomicInteger(0);
	private AtomicInteger waiting = new AtomicInteger(0);
	private AtomicInteger lastIndexedMinute = new AtomicInteger(0);
	
	private final Semaphore full = new Semaphore(0, true);
	
	private final ReentrantReadWriteLock lock = new ReentrantReadWriteLock();
	
	private XDMIterator it = null;
	
	private String name;
	
	private int lastMinute = 0;
	
	private int currentMinute = 2;
	
	private int level = 0;
	
	private Token[] currentItem = null;
	private int crtItemI = 0;
	private int itemCapacity = 100;
	private boolean buffer = true;
	private boolean wait = false;
	List prevIndexedItems = null;
	

	public IndexFIFOStore(int id, int blockSize, WindowBuffer container) {
		super(id, blockSize, container);
		this.indexes = new HashMap();
	}
	
	public IndexFIFOStore(XDMIterator it, int secondsToStoreData, String name, boolean wait, int blockSize, WindowBuffer container){
		super(0, blockSize, container);
		this.it = it;
		this.indexes = new HashMap();
		this.lastIndexedMinute.set(secondsToStoreData);
		this.name = name;
		this.wait = wait;
		this.prevIndexedItems = new LinkedList();
	}
	
	public void start(){		
		pullThread = new ReadThreadUpdate(it,this);
		//pullThread.init(this);
		pullThread.setName(name);
		pullThread.start();		
	}
	
	public XDMIterator retrieve(IndexSchema schema, Token[] tokens){
		int lastRequestedMinute = (int)tokens[tokens.length - 1].getLong();
	
		while (lastIndexedMinute.get() < lastRequestedMinute ){
			try {
				waiting.getAndIncrement();
				if ( lastIndexedMinute.get() < lastRequestedMinute )
					full.acquire();
				waiting.getAndDecrement();
			} catch (InterruptedException e) {
				e.printStackTrace();
			}
		}
		
		lock.readLock().lock();
		IntegerList il = ((SimpleEqIndex)indexes.get(schema)).retreive(tokens);
		lock.readLock().unlock();
		
		XDMIterator retIt = new ResultDataItemsIterator(this,il,itemSize.get());
		
		if (done){
			try {
				if ( pullThread != null )				
					pullThread.join();
			} catch (InterruptedException e) {
				e.printStackTrace();
			}
			done = false;
		}
		
		return retIt;
	}
	
	public IndexSchema registerIndex (IndexSchema schema){
		schemaIndex++;
		schema.setId(schemaIndex);
		indexes.put(schema,new SimpleEqIndex());
		return schema;
	}
	
	/**
	 * materialize a new token from the stream; event for avoiding subsequent check for type
	 * @throws MXQueryException 
	 */
	public void putElement(Token tok, int event) throws MXQueryException{	
		
		if ( event == Type.START_TAG ){
			level++;
		}	
		
		if ( event == Type.END_TAG ){
			//consider nested elements
			if ( level == 2 )
				currentItem[crtItemI++] = tok;
			level--;
		}	

		if ( currentItem == null )
			currentItem = new Token[itemCapacity];
		
		if ( level == 2 )
		{
			/* "Not-accident" report, it should not be stored */
			if ( tok.getLong() == -1 )
				buffer = false;
			
			currentItem[crtItemI++] = tok;
		}
		
		if ( tok.getEventType() == Type.END_SEQUENCE){	
			//System.out.println("End Sequence");
			if ( currentItem != null ){	
				bufferItem(currentItem,crtItemI);
				if ( waiting.get() > 0)
					full.release(waiting.get());
			}
			done = true;			
			return;
		}
		
		if ( level == 1 ){					
			//buffer current read item
			if ( currentItem != null && crtItemI != 0){		
				//for more items per "minute", wait till the end of the minute
				if ( !wait ){
					indexValues(currentItem,getCurrentTokenId());
					bufferItem(currentItem,crtItemI);
					itemSize.getAndSet(crtItemI);
				}
				else{
					//current item is to be buffered (there are "not-buffer" items: e.g., accidents)
					if ( buffer ){
						if ( firstTime(currentItem, crtItemI) ){					
							indexValues(currentItem,getCurrentTokenId());
							bufferItem(currentItem,crtItemI);
							itemSize.getAndSet(crtItemI);
						}
					}
					else
						buffer = true;
				}
				
				lastMinute = getMinute(currentItem,crtItemI);
			}
			
			/* help gc */
			currentItem = null;
			
			if (crtItemI != 0)
				currentItem = new Token[crtItemI];
			else
				currentItem = new Token[itemCapacity];
			
			crtItemI = 0;
		}
		
		if ( level == 0 ){			
			if (wait){
				if ( currentMinute >= lastMinute ){
//					update last indexed minutes
					lastIndexedMinute.getAndSet(lastMinute);
//					notify all waiting requests
					if (waiting.get() > 0)
						full.release(waiting.get());
						prevIndexedItems = null;
						prevIndexedItems = new LinkedList();
					
				}
				else{
					currentMinute = lastMinute;
				}
			}
			else{			
//				update last indexed minutes
				lastIndexedMinute.getAndSet(lastMinute);
//				notify all waiting requests
				if (waiting.get() > 0)
					full.release(waiting.get());
			}			
		}
	}
	
	public void bufferItem(Token[] toks, int size) throws MXQueryException{
		newItem();
		for ( int i=0; i<size; i++ ){
			
			int event = toks[i].getEventType();
			if ( event == Type.END_SEQUENCE )
				event = -1;
				
				buffer(toks[i],event);
		}
	}
	
	/**
	 * in case of more items per "minute", eliminate duplicates
	 * @param tokens New item
	 * @param crtItemI Item's size
	 * @return
	 */
	private boolean firstTime(Token[] tokens, int crtItemI){
		String currentItem = tokens[1].getLong()+","+tokens[2].getLong()+","+tokens[3].getLong()+","+tokens[4].getLong();
		
		if (prevIndexedItems.contains(currentItem))
			return false;
		
		prevIndexedItems.add(currentItem);
		
		return true;
	}
	
	/* "minute" attribute is considered to be first */ 
	private int getMinute(Token[] crtItem, int size){
		if ( size == 0 )
			return Integer.MAX_VALUE;
		
		return (int)crtItem[1].getLong();
	}
	
	/**
	 * Index given values
	 * @param crtItem
	 * @param id
	 */
	private void indexValues(Token[] crtItem, int id){	
		
		if ( crtItem.length == 0 )
			return;
		
		Set indxs = indexes.keySet();
		java.util.Iterator indxIt = indxs.iterator();
		
		while(indxIt.hasNext()){
			IndexSchema currentSchema = (IndexSchema)indxIt.next();	
			
			SimpleEqIndex index = (SimpleEqIndex)indexes.get(currentSchema);
			
			IntegerList indexPos = prepareForIndex(currentSchema,crtItem);	
			
			if (indexPos.size() == 0)
				continue;
			
			Token[] tokens = new Token[indexPos.size()];
			
			for ( int i=0; i<indexPos.size(); i++ ){
				tokens[i] = crtItem[indexPos.get(i)];
			}	
			
			lock.writeLock().lock();
			index.index(tokens,id);
			lock.writeLock().unlock();
		}
	}
	
	/**
	 * extracts the attributes which participate in the index from the current item
	 * @param schema Index schema
	 * @param item New item
	 * @return
	 */
	private IntegerList prepareForIndex(IndexSchema schema, Token[] item){
		
		IntegerList pos = new IntegerList();
		
		for ( int j=0; j<schema.size(); j++){
			for ( int i=0; i<crtItemI; i++ ){
				int type = item[i].getEventType();
				if (Type.isAttribute(type) && Type.isTypeOrSubTypeOf(type, Type.INTEGER, null)) {
					if ( item[i].getName().compareTo(schema.getColumnName(j)) == 0 ){
						pos.add(i);
						break;
					}					
				}
			}
		}
				
		return pos;
	}
}

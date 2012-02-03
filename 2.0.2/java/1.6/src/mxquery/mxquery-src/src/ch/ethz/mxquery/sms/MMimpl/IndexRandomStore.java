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
import java.util.Vector;
//import java.util.Set;
import java.util.concurrent.Semaphore;
import java.util.concurrent.atomic.AtomicInteger;
import java.util.concurrent.locks.ReentrantReadWriteLock;

import ch.ethz.mxquery.bindings.WindowBuffer;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.Source;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.opt.index.IndexSchema;
import ch.ethz.mxquery.opt.index.SimpleEqIndex;
import ch.ethz.mxquery.sms.activeStore.ReadDataThread;
import ch.ethz.mxquery.sms.interfaces.ActiveStore;
import ch.ethz.mxquery.sms.interfaces.IndexRead;
import ch.ethz.mxquery.sms.iterators.ResultDataItemsIterator;

import ch.ethz.mxquery.util.IntegerList;


/**
 * 
 * TO DO
 * @author icarabus
 *
 */
public class IndexRandomStore extends RandomStore implements IndexRead, ActiveStore {
	
	private XDMIterator it;
	
	private ReadDataThread pullThread = null;
	private Map indexes = null;
	private int level = 0;
	
//	private SimpleBuffer crtBuffer = null;	
	//private Store crtBuffer = null;
	
	private Token[] currentItem = null;
	private int crtItemI = 0;
	private int itemCapacity = 100;
	
	private AtomicInteger itemSize = new AtomicInteger(0);
	
	private boolean done = false;
	
	private AtomicInteger lastIndexedMinute = new AtomicInteger(0);
	
	private int lastMinute = 0;
	
	private int currentMinute = 2;
	
	private final Semaphore full = new Semaphore(0, true);
	
	private final ReentrantReadWriteLock lock = new ReentrantReadWriteLock();

	private String name = null;
	
	private AtomicInteger waiting = new AtomicInteger(0);
	
	private boolean wait = false;
	
	private boolean buffer = true;
	
	//private int blockSize;
	
	List prevIndexedItems = null;
	WindowBuffer cont;

	/**
	 * Store constructor
	 * @param it Iterator to pull the data from
	 * @param secondsToStoreData Wait until access to storage is permitted (supposes the existence of a "miunute" attr)
	 * @param name Store's name
	 * @param wait Synchronization condition (minute) - more than one items per "minute"
	 */
	public IndexRandomStore(XDMIterator it, int secondsToStoreData, String name, boolean wait, int size, WindowBuffer container){
		super(0);
		this.it = it;
		this.indexes = new HashMap();
		this.lastIndexedMinute.set(secondsToStoreData);
		this.name = name;
		this.wait = wait;
		this.prevIndexedItems = new LinkedList();
		//this.blockSize = size;
		cont = container;
		
	}
	
	public IndexRandomStore(XDMIterator it, int secondsToStoreData, String name){
		
		super(0);
		this.it = it;
		this.indexes = new HashMap();
		this.lastIndexedMinute.set(secondsToStoreData);
		this.name = name;
		this.wait = false; // default no wait
	}	
	
	/**
	 * register index schemas
	 */
	public IndexSchema registerIndex (IndexSchema schema){
		id++;
		schema.setId(id);
		indexes.put(schema,new SimpleEqIndex());
		return schema;
	}
	
	/*
	 * 
	 * @see ch.ethz.mxquery.model.store.lroad.ILRoadStore#start()
	 */
	public void start(){
		pullThread = new ReadDataThread(it);
		pullThread.init(this);
		pullThread.setName(name);
		pullThread.start();
	}
	
	/*
	 * Retreive data from the store using index given by "schema" with actual values "tokens"
	 * Synchronization on attribute "minute" (assuumption -> last value)
	 * @see ch.ethz.mxquery.model.store.lroad.LRStoreInterface#retrieve(ch.ethz.mxquery.util.index.IndexSchema, ch.ethz.mxquery.util.tokens.Token[])
	 */ 
	public XDMIterator retrieve(IndexSchema schema, Token[] tokens){
		
		int lastRequestedMinute = (int)tokens[tokens.length - 1].getLong();
		
		//System.out.println("Request for minute : "+lastRequestedMinute);
				
//		if ( prevMin.get() != lastRequestedMinute){
//			System.out.println(Thread.currentThread().getName()+" Request for minute : "+lastRequestedMinute+" from "+name+" at : "+SimulationClock.getInstance().getCurrentTime());
//			prevMin.set(lastRequestedMinute);
//			print = true;
//		}
		
		//wait until requested data is available
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
		
		//retreive data from the store using an index
		lock.readLock().lock();
		IntegerList il = ((SimpleEqIndex)indexes.get(schema)).retreive(tokens);
		lock.readLock().unlock();
			
		//create a interator for returned data
//		Iterator retIt = new LRoadStoreIndexIterator(crtBuffer,il,itemSize.get());
		XDMIterator retIt = new ResultDataItemsIterator(null,il,itemSize.get()); // first param was crtBuffer
		
//		if ( print ){
//			System.out.println(Thread.currentThread().getName()+" End request for minute : "+lastRequestedMinute+" from "+name+" at : "+SimulationClock.getInstance().getCurrentTime());
//			print = false;
//		}
		
		//END_SEQUENCE - no more data in the input iterator given to the store
		if (done){
			try {
				pullThread.join();
			} catch (InterruptedException e) {
				e.printStackTrace();
			}
			done = false;
		}
		
		return retIt;
	}
	
	/**
	 * materialize a new token from the stream; event for avoiding subsequent check for type
	 */
	public void buffer(Token tok, int event){	
		
		//first time creation of the simple buffer
//		if (crtBuffer == null){
//			//crtBuffer = new RandomFIFOStore(this.getMyId());
//		}
		
//		if ( Thread.currentThread().getName().compareTo("TOLL_STORAGE") == 0 )
//			System.out.println("Buffer size : "+crtBuffer.size());
		
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
			System.out.println("End Sequence");
			if ( currentItem != null ){	
				//crtBuffer.bufferItem(currentItem,crtItemI);
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
					//indexValues(currentItem,crtBuffer.getCurrentTokenId());
					//crtBuffer.bufferItem(currentItem,crtItemI);
					itemSize.getAndSet(crtItemI);
				}
				else{
					//current item is to be buffered (there are "not-buffer" items: e.g., accidents)
					if ( buffer ){
						if ( firstTime(currentItem, crtItemI) ){					
							//indexValues(currentItem,crtBuffer.getCurrentTokenId());
							//crtBuffer.bufferItem(currentItem,crtItemI);
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
	
//	/**
//	 * Index given values
//	 * @param crtItem
//	 * @param id
//	 */
//	private void indexValues(Token[] crtItem, int id){	
//		
//		if ( crtItem.length == 0 )
//			return;
//		
//		Set indxs = indexes.keySet();
//		java.util.Iterator indxIt = indxs.iterator();
//		
//		while(indxIt.hasNext()){
//			IndexSchema currentSchema = (IndexSchema)indxIt.next();	
//			
//			SimpleEqIndex index = (SimpleEqIndex)indexes.get(currentSchema);
//			
//			IntegerList indexPos = prepareForIndex(currentSchema,crtItem);	
//			
//			if (indexPos.size() == 0)
//				continue;
//			
//			Token[] tokens = new Token[indexPos.size()];
//			
//			for ( int i=0; i<indexPos.size(); i++ ){
//				tokens[i] = crtItem[indexPos.get(i)];
//			}	
//			
//			lock.writeLock().lock();
//			index.index(tokens,id);
//			lock.writeLock().unlock();
//		}
//	}
//	
//	/**
//	 * extracts the attributes which participate in the index from the current item
//	 * @param schema Index schema
//	 * @param item New item
//	 * @return
//	 */
//	private IntegerList prepareForIndex(IndexSchema schema, Token[] item){
//		
//		IntegerList pos = new IntegerList();
//		
//		for ( int j=0; j<schema.size(); j++){
//			for ( int i=0; i<crtItemI; i++ ){
//				int type = item[i].getEventType();
//				if (Type.isAttribute(type) && Type.isTypeOrSubTypeOf(type, Type.INTEGER, null)) {
//					if ( item[i].getName().compareTo(schema.getColumnName(j)) == 0 ){
//						pos.add(i);
//						break;
//					}					
//				}
//			}
//		}
//				
//		return pos;
//	}
	
	public void printDebugAccidents() {
		if (prevIndexedItems == null) return;
				
		for (int i=0; i < prevIndexedItems.size(); i++ ) {
			String res = (String)prevIndexedItems.get(i);
			System.out.println(res);
		}	
	} 	
	
	public void delete(IndexSchema schema, Token[] values){
		
	}
	
	public void deleteItems(int[] ids){
		
	}
	
	public Token get(int pos) throws MXQueryException{
		throw new RuntimeException("Not implemented");
		//return  this.crtBuffer.get(pos);
	}
	
	public void newItem(){
		
	}

	public int compare(Source store) {
		// TODO Auto-generated method stub
		return 0;
	}

	public Source copySource(Context ctx, Vector nestedPredCtxStack) throws MXQueryException {
		// TODO Auto-generated method stub
		return null;
	}

	public String getURI() {
		// TODO Auto-generated method stub
		return null;
	}
	public Window getIterator(Context ctx) throws MXQueryException{
		Window wnd =  cont.getNewWindowIterator(1, Window.END_OF_STREAM_POSITION);
		wnd.setContext(ctx, false);
		return wnd;
	}

	public void setIterator(XDMIterator it) {
		this.it = it;
	}
	
}

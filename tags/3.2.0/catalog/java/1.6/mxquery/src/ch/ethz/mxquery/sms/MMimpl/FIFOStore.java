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
import java.util.Vector;
import java.util.concurrent.Semaphore;
import java.util.concurrent.atomic.AtomicBoolean;
import java.util.concurrent.atomic.AtomicInteger;


import ch.ethz.mxquery.bindings.WindowBuffer;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.contextConfig.XQStaticContext;
import ch.ethz.mxquery.datamodel.Source;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.sms.activeStore.ReadDataThread;
import ch.ethz.mxquery.sms.interfaces.ActiveStore;

public abstract class FIFOStore implements MXQueryAppendUpdate, ActiveStore {
	
	protected WindowBuffer cont;
	
	private int id = -1;
	
	protected BufferItem current = null; 
	
	protected int granularity = 10000;
	
	public XDMIterator iterator = null;
	
	protected int size = 0;
	protected XQStaticContext context = null;
	
	protected HashMap attributes = new HashMap();
	
	protected final Semaphore newToken = new Semaphore(1, true);
	protected final Semaphore newItem = new Semaphore(1, true);
	
	protected AtomicInteger currentItem = new AtomicInteger(0);
	protected AtomicInteger currentToken = new AtomicInteger(0);
	
	public ReadDataThread readThread = null; 
	protected GarbageCollector garbColl = null; 
	
	protected AtomicBoolean endOfStream = new AtomicBoolean(false);
	protected boolean createNewBuffer = false;
	
	protected int deleteFrom = 0;
	protected int itemLength = 12; //?????
	
	protected boolean isFreeBuffer = false;
	
	protected AtomicInteger tokenWaiting = new AtomicInteger(0);
	protected AtomicInteger itemWaiting = new AtomicInteger(0);
	
	protected boolean doneAttr = false;
	
	FIFOStore(int id, WindowBuffer container){
		this.id = id;
		cont = container;
	}
	
	public int getMyId(){
		return id;
	}
	
	public void setContainer(WindowBuffer buf) {
		cont = buf;
	}
	
	protected abstract void addNewBufferNode();
	
	protected abstract BufferItem bufferToToken(int activeTokenId);
	
	protected abstract BufferItem bufferToItem(int nodeId);
	
	protected abstract void freeBuffers();
	
	public void setIterator(XDMIterator it){
		this.iterator = it;
		this.readThread = new ReadDataThread(it);
	}
	
	public void setContext(Context context) throws MXQueryException{
		this.readThread.setContext(context);
	}
	
	public Token get(int activeTokenId) throws MXQueryException{
		
		while (currentToken.get() <= activeTokenId && !endOfStream.get()){
			try {
				tokenWaiting.getAndIncrement();				
				if (currentToken.get() <= activeTokenId && !endOfStream.get())
					newToken.acquire();
				tokenWaiting.getAndDecrement();
			} catch (InterruptedException e) {
				e.printStackTrace();
			}
		}
		
		//if ( Thread.currentThread().getName().compareTo("[#2 ACCIDENT-ALERTS-TO-FILE]") == 0 )
			//System.out.println("GET TOKEN WITH ID : "+activeTokenId);
		
		if (endOfStream.get() && currentToken.get() <= activeTokenId)
			return Token.END_SEQUENCE_TOKEN;
		
		BufferItem bi = bufferToToken(activeTokenId);
		
		if ( bi == null || !(activeTokenId >= bi.getFirstTokenId() && activeTokenId < bi.getLastTokenId() ))
			return Token.END_SEQUENCE_TOKEN;
		
		//if ( Thread.currentThread().getName().compareTo("[#2 ACCIDENT-ALERTS-TO-FILE]") == 0 )
			//System.out.println("GET TOKEN WITH ID : "+activeTokenId);
				
		return bi.get(activeTokenId);
	}
	
	public Token get(int activeTokenId,int endNode) throws MXQueryException{
		while (currentToken.get() <= activeTokenId && !endOfStream.get()){
			try {
				tokenWaiting.getAndIncrement();				
				if (currentToken.get() <= activeTokenId && !endOfStream.get())
					newToken.acquire();
				tokenWaiting.getAndDecrement();
			} catch (InterruptedException e) {
				e.printStackTrace();
			}
		}
		
		if (endOfStream.get() && currentToken.get() <= activeTokenId)
			return Token.END_SEQUENCE_TOKEN;
		
		BufferItem bi = bufferToToken(activeTokenId);
		
		
		if ( bi == null )
			return Token.END_SEQUENCE_TOKEN;
				
		return bi.get(activeTokenId,endNode);
	}
	
	public int getNodeIdFromTokenId(int lastKnownNodeId, int activeTokenId) throws MXQueryException {
		while ( currentToken.get() <= activeTokenId && !endOfStream.get()){
			try {
				tokenWaiting.getAndIncrement();
				if (currentToken.get() <= activeTokenId && !endOfStream.get())
					newToken.acquire();
				tokenWaiting.getAndDecrement();
			} catch (InterruptedException e) {
				e.printStackTrace();
			}
		}

		return bufferToToken(activeTokenId).getNodeIdFromTokenId(lastKnownNodeId,activeTokenId);
	}
	
	public boolean hasNode(int nodeId) throws MXQueryException {
		while (currentItem.get() <= nodeId && !endOfStream.get()){
			try {
				itemWaiting.getAndIncrement();
				if ((currentItem.get()<= nodeId && !endOfStream.get()))
					newItem.acquire();	
				itemWaiting.getAndDecrement();
			} catch (InterruptedException e) {
				e.printStackTrace();
			}
		}
		
		BufferItem bi = bufferToItem(nodeId);
		
		if ( bi == null ) {
			return false;
		}
		
		return bi.hasNode(nodeId);
		
	}
	
	public int getTokenIdForNode(int nodeId) throws MXQueryException{
		
		while (currentItem.get() <= nodeId && !endOfStream.get()){
			try {
				itemWaiting.getAndIncrement();
				if ((currentItem.get() <= nodeId && !endOfStream.get()))
					newItem.acquire();	
				itemWaiting.getAndDecrement();
			} catch (InterruptedException e) {
				e.printStackTrace();
			}
		}
		
		return bufferToItem(nodeId).getTokenIdForNode(nodeId);
		
	}
	
	public void deleteItems(int nodeId) throws MXQueryException{
		deleteFrom = nodeId;
	}
	
	public int getSize(){
		return size;
	}
	
	public void newItem(){		
		
		if ( current == null ){
			
			//if ( Thread.currentThread().getName().compareTo("ACCIDENT_STORAGE") == 0 || Thread.currentThread().getName().compareTo("TOLL_STORAGE") == 0 )
				//System.out.println("Debug position");
			
			addNewBufferNode();
			
			//if ( current == null)
				//System.out.println("Still NULL");
		}	
		
		if (createNewBuffer){
			freeBuffers();	
			if (!isFreeBuffer){
				addNewBufferNode();
			}
			isFreeBuffer = false;
			createNewBuffer = false;
		}
		
		try {
			current.indexNewNode();
		} catch (Exception e) {
			// TODO Auto-generated catch block
			System.out.println("Thread generating exception : "+Thread.currentThread().getName());
			e.printStackTrace();
		}
		
		if ( currentItem.get() == 1 ){
			itemLength = currentToken.get();
//			System.out.println("Item length : "+itemLength);
		}
		
		currentItem.getAndIncrement();
		
		if ( currentItem.get() % granularity == 0 )
			createNewBuffer = true;
		
		if (itemWaiting.get() > 0)
			newItem.release(itemWaiting.get());
	}
	
	public void buffer(Token token, int event) throws MXQueryException{
		if ( current == null ){
			addNewBufferNode();
		}
		
		current.bufferToken(token);		
		if ( event == -1 ){
			endOfStream.set(true);
		}
		currentToken.getAndIncrement();
		
		if ( tokenWaiting.get() > 0 )
			newToken.release(tokenWaiting.get());
				
		if ( token.isAttribute() )
			addAttribute(token.getName());
		
	}
	
	private void addAttribute(String attrName) throws MXQueryException{
		if (doneAttr)
			return;
		if (attributes.containsKey(attrName)){
			doneAttr = true;
			return;
		}
		
		int lastStartToken = current.getTokenIdForNode(currentItem.get()-1);
		int offset = currentToken.get() - lastStartToken;
		attributes.put(attrName,new Integer(offset));
	}
	
	public int getAttributePosFromNodeId(String attrName,int nodeId) throws MXQueryException{
		
		if (!attributes.containsKey(attrName))
			return -1;
		
		int offset = ((Integer)attributes.get(attrName)).intValue();
		
		int tokenId = getTokenIdForNode(nodeId);
		
		Token tok = get(tokenId+offset,nodeId);
		
		if( tok == Token.END_SEQUENCE_TOKEN )
			return -1;
		
		if (tokenId+offset >= currentToken.get())
			return -1;
		
		return tokenId+offset;
		
	}
	
	public int getAttributePosFromTokenId(String attrName, int activeTokenId) throws MXQueryException{return -1;}
	
	public int getCurrentTokenId(){
		return currentToken.get();
	}
	public Window getIterator(Context ctx) throws MXQueryException{
		Window wnd =  cont.getNewWindowIterator(1, Window.END_OF_STREAM_POSITION);
		wnd.setContext(ctx, false);
		return wnd;
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
	
}

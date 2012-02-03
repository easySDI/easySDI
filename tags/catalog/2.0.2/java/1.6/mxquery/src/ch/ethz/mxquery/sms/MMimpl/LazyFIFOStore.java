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

import ch.ethz.mxquery.bindings.WindowBuffer;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.contextConfig.XQStaticContext;
import ch.ethz.mxquery.datamodel.Source;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.model.XDMIterator;

public abstract class LazyFIFOStore implements MXQueryAppendUpdate {
	
	protected WindowBuffer cont;
	
	private int id = -1;
	
	protected BufferItem current = null; 
	
	protected int granularity = 10000;
	
	protected XDMIterator iterator = null;
	
	protected int size = 0;
	protected XQStaticContext context = null;
	
	protected HashMap attributes = new HashMap();
	//protected HashMap<String,Integer> attributes = new HashMap<String,Integer>();
	
	//protected final Semaphore newToken = new Semaphore(1, true);
	//protected final Semaphore newItem = new Semaphore(1, true);
	
	//protected AtomicInteger currentItem = new AtomicInteger(0);
	//protected AtomicInteger currentToken = new AtomicInteger(0);
	
	protected int currentToken = 0;
	protected int currentItem = 0;
		
	protected boolean endOfStream = false;
	protected boolean createNewBuffer = false;
	
	protected int deleteFrom = 0;
	protected int itemLength = 12; //?????
	
	protected boolean isFreeBuffer = false;
	
	//protected AtomicInteger tokenWaiting = new AtomicInteger(0);
	//protected AtomicInteger itemWaiting = new AtomicInteger(0);
	
	StreamStoreInput si;
	
	protected boolean doneAttr = false;
	
	LazyFIFOStore(int id, WindowBuffer container){
		cont = container;
		this.id = id;
		si = new StreamStoreInput(this);
	}
	
	public int getMyId(){
		return id;
	}
	
	protected abstract void addNewBufferNode();
	
	protected abstract BufferItem bufferToToken(int activeTokenId);
	
	protected abstract BufferItem bufferToItem(int nodeId);
	
	public abstract void freeBuffers();
	
	public abstract void start();
	
	
	public void setIterator(XDMIterator it){
		this.iterator = it;
		//this.readThread = new ReadDataThread(it);
		//this.garbColl = new GarbageCollector();
	}
	
	public void setContext(XQStaticContext context){
		//this.readThread.setContext(context);
	}
	
	public void readToken(){
		try {
			Token token = iterator.next();

			endOfStream = si.bufferNext(token);
			if ( endOfStream )
				return;
			
		} catch (Exception e) {
			e.printStackTrace();
		}
		
	}
	
	public Token get(int activeTokenId){
		
		boolean alreadyThere = true;
		
		while (currentToken <= activeTokenId && !endOfStream){
			alreadyThere = false;
			readToken();
		}
		
		if ( endOfStream && currentToken <= activeTokenId  )
			return Token.END_SEQUENCE_TOKEN;
		
		if (!alreadyThere)
			return current.get(activeTokenId);
		else{
		
			BufferItem bi = bufferToToken(activeTokenId);
		
			if ( bi == null || !(activeTokenId >= bi.getFirstTokenId() && activeTokenId < bi.getLastTokenId() ))
				return Token.END_SEQUENCE_TOKEN;
	
			return bi.get(activeTokenId);
		}
	}
	
	public Token get(int activeTokenId,int endNode){
		
		boolean alreadyThere = true;
		
		while (currentToken <= activeTokenId && !endOfStream){
			alreadyThere = false;
			readToken();
		}
		
		if ( endOfStream && currentToken <= activeTokenId )
			return Token.END_SEQUENCE_TOKEN;
		
		if (!alreadyThere)
			return current.get(activeTokenId,endNode);
		else{
		
			BufferItem bi = bufferToToken(activeTokenId);
		
			if ( bi == null || !(activeTokenId >= bi.getFirstTokenId() && activeTokenId < bi.getLastTokenId() ))
				return Token.END_SEQUENCE_TOKEN;
	
			return bi.get(activeTokenId,endNode);
		}
		
	}
	
	public int getNodeIdFromTokenId(int lastKnownNodeId, int activeTokenId) {
		
		
		boolean alreadyThere = true;
		
		while (currentToken <= activeTokenId && !endOfStream){
			alreadyThere = false;
			readToken();
		}
		
		if ( endOfStream && currentToken <= activeTokenId )
			return currentItem;
		
		if (!alreadyThere)
			return current.getNodeIdFromTokenId(lastKnownNodeId,activeTokenId);
		else{
		
			BufferItem bi = bufferToToken(activeTokenId);
	
			return bi.getNodeIdFromTokenId(lastKnownNodeId,activeTokenId);
		}
		
//		while ( currentToken.get() <= activeTokenId && !endOfStream.get()){
//			try {
//				tokenWaiting.getAndIncrement();
//				if (currentToken.get() <= activeTokenId && !endOfStream.get())
//					newToken.acquire();
//				tokenWaiting.getAndDecrement();
//			} catch (InterruptedException e) {
//				e.printStackTrace();
//			}
//		}
//
//		return bufferToToken(activeTokenId).getNodeIdFromTokenId(lastKnownNodeId,activeTokenId);
	}
	
	public boolean hasNode(int nodeId) {
		
		
		boolean alreadyThere = true;
		
		while (currentItem <= nodeId && !endOfStream){
			alreadyThere = false;
			readToken();
		}
		
		if (alreadyThere && !endOfStream)
			return true;
		else{
		
			BufferItem bi = bufferToItem(nodeId);
			
			if ( bi == null ) {
				return false;
			}
			
			return bi.hasNode(nodeId);
		}
		
//		while (currentItem.get() <= nodeId && !endOfStream.get()){
//			try {
//				itemWaiting.getAndIncrement();
//				if ((currentItem.get()<= nodeId && !endOfStream.get()))
//					newItem.acquire();	
//				itemWaiting.getAndDecrement();
//			} catch (InterruptedException e) {
//				e.printStackTrace();
//			}
//		}
//		
//		BufferItem bi = bufferToItem(nodeId);
//		
//		if ( bi == null ) {
//			return false;
//		}
//		
//		return bi.hasNode(nodeId);
		
	}
	
	public int getTokenIdForNode(int nodeId) throws MXQueryException{
		
		
		boolean alreadyThere = true;
		
		while (currentItem <= nodeId && !endOfStream){
			alreadyThere = false;
			readToken();
		}
		
		if (!alreadyThere)
			return current.getTokenIdForNode(nodeId);
		else{
		
			BufferItem bi = bufferToItem(nodeId);
			
			if ( bi == null )
				return currentToken;
	
			return bi.getTokenIdForNode(nodeId);
		}
		
//		while (currentItem.get() <= nodeId && !endOfStream.get()){
//			try {
//				itemWaiting.getAndIncrement();
//				if ((currentItem.get() <= nodeId && !endOfStream.get()))
//					newItem.acquire();	
//				itemWaiting.getAndDecrement();
//			} catch (InterruptedException e) {
//				e.printStackTrace();
//			}
//		}
//		
//		return bufferToItem(nodeId).getTokenIdForNode(nodeId);
		
	}
	
	public void deleteItems(int nodeId){
		deleteFrom = nodeId;
	}
	
	public int getSize(){
		return size;
	}
	
	public void newItem(){		
		
		if ( current == null ){
			addNewBufferNode();
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
			System.out.println("Thread generating exception : "+Thread.currentThread().getName());
			e.printStackTrace();
		}
		
		if ( currentItem == 1 ){
			itemLength = currentToken;
		}
		
		currentItem++;
		
		if ( currentItem % granularity == 0 )
			createNewBuffer = true;
		
		
	}
	
	public void buffer(Token token, int event) throws MXQueryException{
		if ( current == null ){
			addNewBufferNode();
		}
		
		current.bufferToken(token);		
		if ( event == -1 ){
			endOfStream = true;
		}
		currentToken++;
				
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
		
		int lastStartToken = current.getTokenIdForNode(currentItem-1);
		int offset = currentToken - lastStartToken;
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
		
		if (tokenId+offset >= currentToken)
			return -1;
		
		return tokenId+offset;
		
	}
	
	public int getAttributePosFromTokenId(String attrName, int activeTokenId){return -1;}
	
	public int getCurrentTokenId(){
		return currentToken;
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

	public void setContainer(WindowBuffer buf) {
		cont = buf;
	}

	public void setContext(Context context) throws MXQueryException {
	}
	
	
}

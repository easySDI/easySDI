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

import java.util.Enumeration;
import java.util.Hashtable;
import java.util.Vector;

import ch.ethz.mxquery.util.Set;

import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.sms.ftstore.FTTokenBufferStore;
import ch.ethz.mxquery.sms.interfaces.StreamStore;
import ch.ethz.mxquery.datamodel.Source;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.bindings.WindowBuffer;
import ch.ethz.mxquery.bindings.WindowEarlyBindingParallel;
//import ch.ethz.mxquery.model.factories.StoreFactory;
import ch.ethz.mxquery.model.WindowVariable;
import ch.ethz.mxquery.iterators.VariableIterator;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.contextConfig.Context;
//import ch.ethz.mxquery.sms.MMimpl.FIFOStore;
//import ch.ethz.mxquery.sms.MMimpl.MXQueryAppendUpdate;
import ch.ethz.mxquery.sms.MMimpl.TokenBufferStore;
import ch.ethz.mxquery.model.VariableHolder;
import ch.ethz.mxquery.util.IntegerList;
import ch.ethz.mxquery.datamodel.xdm.Token;

/**
 * Based on a TokenBuffer this class is a factory for window iterators. Each
 * window iterator which uses the same WindowBuffer operates on the same
 * materialized stream. Additionally this class provides some access
 * functionalities for window iterators.
 * 
 * <br>
 * Important definitions:<br>
 * item: Is a item in a sequence <br>
 * position: Position as in XQuery. First position is 1. <br>
 * nodeId: Like Position but starting with 0 <br>
 * tokenId: Id of each token. Also starting with 0 <br>
 * <br>
 * 
 * @author Tim Kraska (<a href="mailto:mail@tim-kraska.de">mail@tim-kraska.de</a>)
 * 
 */
public final class WindowBuffer implements Source{

	private int garbCollTreshold = 1000000;

	private Hashtable winds = new Hashtable();

	private final WindowIterator emptySequence = new WindowEmptySequenceIterator(this, -1);

	private  TokenBufferStore tokenBuffer;

	private int windowId = 0;

	private static int runningBufferId = 1;

	private int bufferId;

	private int garbTime = 0;

	private Object lock = new Object();

	private boolean debug = false;
	
	private XDMIterator source;
	
	private boolean sync = false;
	
	//private LinkedHashMap debugWinds;

	public int getBufferId(){
		return this.bufferId;
	}
	
	public StreamStore getBuffer(){
		return tokenBuffer;
	}


	public WindowBuffer(StreamStore streamStore) {
		tokenBuffer = (TokenBufferStore)streamStore;
		tokenBuffer.setContainer(this);
		
	}
	
	public WindowBuffer(XDMIterator sourceIter, boolean sync, int size, int garbColl, boolean sameSchema) throws MXQueryException {
		if (debug){
			//this.debugWinds = new LinkedHashMap();
		}
		
		garbCollTreshold = garbColl;
		
		source = sourceIter;
		this.bufferId = runningBufferId;
		runningBufferId++;

		this.sync = sync;
		
		if (sync) {
			throw new RuntimeException("Sync Buffers currently not supported on CLDC");
//			System.out.println("Create block buffer store");
//			tokenBuffer = (Store) StoreFactory.createNewBlockBufferStore(sourceIter, 30, bufferId);
//			((BlockBufferStore)tokenBuffer).setId();
//			((BlockBufferStore) tokenBuffer).initialize();
		} else{
			tokenBuffer = (TokenBufferStore)ch.ethz.mxquery.sms.StoreFactory.createStore(ch.ethz.mxquery.sms.StoreFactory.TOKEN_BUFFER, size,this);
			tokenBuffer.setIterator(sourceIter);
		}
	}

	
	public WindowBuffer(XDMIterator sourceIter, boolean full_text,String uri) throws MXQueryException {
		
		this.bufferId = runningBufferId;
		runningBufferId++;
		source = sourceIter;
		
		tokenBuffer = (TokenBufferStore)ch.ethz.mxquery.sms.StoreFactory.createFTStore(this);
		tokenBuffer.setIterator(sourceIter);
		((FTTokenBufferStore)tokenBuffer).setUri(uri);

	}
	
	
	
	/**
	 * Returns a emptySequence iterator. Especially useful if you ask for a non
	 * existing node
	 * 
	 * @return
	 */
	public WindowIterator getEmptySequence() {
		return emptySequence;
	}

	/**
	 * Returns a new window iterator for the given range
	 * 
	 * @param startPosition
	 * @param endPosition
	 * @return
	 */
	public WindowIterator getNewWindowIterator(int startPosition, int endPosition) {
		return getNewWindowIteratorWithNodeIds(startPosition - 1, endPosition - 1);
	}

	/**
	 * Returns a new window iterator for the given range
	 * 
	 * @param startPosition
	 * @param endPosition
	 * @return
	 */
	public WindowIterator getNewWindowIterator(IntegerList positions) {
		WindowIterator window = null;

		synchronized (lock) {
			window = new WindowItemIterator(this, windowId, positions);

			winds.put(new Integer(windowId), window);
	
			if(debug){
				//debugWinds.put(new Integer(windowId), window);				
			}
			
			windowId++;
		}
		return window;
	}
	
	public WindowEarlyBinding getNewEarlyWindowInterface(int startPosition, XDMIterator startExpr, WindowVariable[] startVars, VariableHolder[] startVarHolders, XDMIterator endExpr, WindowVariable[] endVars, VariableHolder[] endVarHolders){
		WindowEarlyBinding window = null;
		synchronized (lock) {
			window = new WindowEarlyBinding(this, windowId, startPosition, startExpr, startVars, startVarHolders, endExpr, endVars, endVarHolders);
			winds.put(new Integer(windowId), window);
			windowId++;
		}
		return window;
	}

	public WindowEarlyBindingParallel getNewEarlyParallelWindowInterface(int startPosition, WindowVariable[] startVars, VariableHolder[] startVarHolders, XDMIterator endExpr, WindowVariable[] endVars, VariableHolder[] endVarHolders){
		WindowEarlyBindingParallel window = null;
		synchronized (lock) {
			window = new WindowEarlyBindingParallel(this, windowId, startPosition, startVars, startVarHolders, endExpr, endVars, endVarHolders);
			winds.put(new Integer(windowId), window);
			windowId++;
		}
		return window;
	}	

	
	/**
	 * Returns a new window iterator for the given range with node id's (node
	 * id's start with 0 not 1)
	 * 
	 * @param startNodeId
	 * @param endNodeId
	 * @return
	 */
	protected WindowIterator getNewWindowIteratorWithNodeIds(int startNodeId, int endNodeId) {
		WindowSequenceIterator window = null;
		synchronized (lock) {
			window = new WindowSequenceIterator(this, windowId, startNodeId, endNodeId);
			 
			winds.put(new Integer(windowId), window);
	

			windowId++;
		}
		return window;
	}

	// TODO: Destroy window is not completly integrated
	/**
	 * Destroys a window.
	 * 
	 * @param id
	 */
	void destroyWindow(int id) {
		synchronized (lock) {
			WindowIterator window = (WindowIterator)winds.remove(new Integer(id));
		}
	}

//	int getAttributePosFromTokenId(String attrName, int activeTokenId) throws MXQueryException {
//		return tokenBuffer.getAttributePosFromTokenId(attrName, activeTokenId);
//	}
//
//	int getAttributePosFromNodeId(String attrName, int nodeId) throws MXQueryException {
//		return tokenBuffer.getAttributePosFromNodeId(attrName, nodeId);
//
//	}

	/**
	 * Returns the current NodeId for the activeTokenId. To make the search
	 * faster, this method use the last known nodeId.
	 * 
	 * @param iter
	 * @return
	 */
	protected int getNodeIdFromTokenId(int activeTokenId) throws MXQueryException {
		return getNodeIdFromTokenId(activeTokenId, 0);
	}

	/**
	 * Returns the current NodeId for the activeTokenId. To make the search
	 * faster, this method use the last known nodeId.
	 * 
	 * @param iter
	 * @return
	 */
	protected int getNodeIdFromTokenId(int activeTokenId, int lastKnownNodeId) throws MXQueryException {
		return tokenBuffer.getNodeIdFromTokenId(lastKnownNodeId, activeTokenId);
	}

	protected int getTokenIdFromNodeId(int nodeId) throws MXQueryException {
		return tokenBuffer.getTokenIdForNode(nodeId);
	}

	/**
	 * Tests if the underlying source iterator has the node
	 * 
	 * @param node
	 * @return
	 * @throws MXQueryException
	 */
	boolean hasNode(int node) throws MXQueryException {

		return tokenBuffer.hasNode(node);
	}

	/**
	 * Sets the context. Especially used for external vars.
	 * 
	 * @param context
	 */
	public void setContext(Context context) throws MXQueryException {
		tokenBuffer.setContext(context);
	}

	/**
	 * Returns the next Token for the active window
	 * 
	 * @param window
	 *            Asking window
	 * @param endNode
	 *            Max node (range)
	 * @param restart
	 *            Restarts for a new node. Can happend if you jump over tokens
	 * @return
	 * @throws MXQueryException
	 */
	Token next(WindowIterator window, int activeTokenId, int endNode) throws MXQueryException {
		
		//		
		garbTime++;

		if (garbTime > garbCollTreshold) {
			synchronized (lock) {
				int windsSize = 0;
				windsSize = winds.size();
				if (windsSize > 0) {
					doGarbageCollection();
				}
				garbTime = 0;
			}
		}

		return tokenBuffer.get(activeTokenId, endNode);

	}

	/**
	 * This method returns a new Window Iterator. If the underlying iterator is
	 * already a window iterator, the same window buffer is used.
	 * 
	 * !!! Make sure that the sourceIter is reseted! Otherwise you might get
	 * strange results!
	 * 
	 * @param sourceIter
	 * @return
	 */
	public static Window getNewWindowInterface(XDMIterator sourceIter, int startPosition, int endPosition,
			boolean sync, int size, int garbColl, boolean sameSchema) throws MXQueryException {
		
		if (sourceIter instanceof VariableIterator) {
			VariableIterator var = (VariableIterator)sourceIter;
			Window window = var.getUnderlyingIterator();
			if(var.isResettable()){
				window.setResettable(true);
			}
			return window;
			
		} else if(sourceIter instanceof WindowIterator){
			WindowIterator iter = (WindowIterator)sourceIter;
			return iter.getNewWindow(startPosition, endPosition);
		} else  {
			WindowBuffer buffer = new WindowBuffer(sourceIter, sync, size, garbColl, sameSchema);
			runningBufferId++;
			return buffer.getNewWindowIterator(startPosition, endPosition);
		}
	}
	

	/**
	 * This method returns a new Window Iterator. If the underlying iterator is
	 * already a window iterator, the same window buffer is used.
	 * 
	 * @param sourceIter
	 * @return
	 */
	public static Window getNewFullTextWindowInterface(XDMIterator sourceIter, int startPosition, int endPosition, String uri) throws MXQueryException {

		WindowBuffer buffer = new WindowBuffer(sourceIter,true, uri);
		runningBufferId++;
		return buffer.getNewWindowIterator(startPosition, endPosition);

	}
	
	private int call; 
	private int debugBuffer= -2;
	private boolean garbDebugPrint=false;
	private void doGarbageCollection() throws MXQueryException {
		
		if (debug) {
			System.out.println("!!!! Buffer: " + bufferId + " TokenBuffer:" + tokenBuffer.getMyId()
					+ " should be garbage collected - Call " + call);
			garbDebugPrint=true;
		}

		int lowestNodeId = Window.END_OF_STREAM_POSITION;

			Enumeration iter = winds.keys();
			while (iter.hasMoreElements()) {
				WindowIterator queryIterator = (WindowIterator) winds.get((Integer) iter.nextElement());
				if (queryIterator.isResettable()) {
					lowestNodeId = min(queryIterator.getStartNode(), lowestNodeId);
					if(garbDebugPrint && debugBuffer == this.bufferId)
						System.out.println("B" + this.bufferId + 
								": Window:" + queryIterator.getWindowId() + 
								" inUse:" + queryIterator.isWindowInUse() + 
								" startNode:" + queryIterator.getStartNode() +
								" resetable:" + queryIterator.isResettable() +
								//" class:" + queryIterator.getClass().getSimpleName() + 
								" lowestNodeId:" + lowestNodeId);
				} else {
					lowestNodeId = min(queryIterator.getNodeId(), lowestNodeId);
					
					if(garbDebugPrint && debugBuffer == this.bufferId)
						System.out.println("B" + this.bufferId + 
								": Window:" + queryIterator.getWindowId() + 
								" inUse:" + queryIterator.isWindowInUse() + 
								" lowestNodeId:" + queryIterator.getNodeId() + 
								" resetable:" + queryIterator.isResettable() + 
								//" class:" + queryIterator.getClass().getSimpleName() + 
								" lowestNodeId:" + lowestNodeId);
				}
	
		}
			
			
		
		if (lowestNodeId >= 2) {
			tokenBuffer.deleteItems(lowestNodeId - 1);
		}else{
//			if (call % 10 == 0){
//				System.out.println("~~~~ Buffer: " + bufferId + " NOT DELETED ~~~~");
//			}
		}
		call++;
	}

	private int min(int a, int b) {

		if (a < b) {
			return a;
		} else {
			return b;
		}
	}
	public WindowBuffer copy(Context context, Vector nestedPredCtxStack) throws MXQueryException {
		if (tokenBuffer instanceof FTTokenBufferStore)
			return new WindowBuffer(source.copy(context, null, false, nestedPredCtxStack), true, tokenBuffer.getURI());
		else 
			return new WindowBuffer(source.copy(context, null, false, nestedPredCtxStack), sync, 0, garbCollTreshold, false);
	}	
	
	public int compare(Source store) {
		return tokenBuffer.compare(store);
	}

	public Source copySource(Context ctx, Vector nestedPredCtxStack)
			throws MXQueryException {
		return null;
	}

	public Window getIterator(Context ctx) throws MXQueryException {
		return tokenBuffer.getIterator(ctx);
	}

	public String getURI() {
		return tokenBuffer.getURI();
	}
}
	
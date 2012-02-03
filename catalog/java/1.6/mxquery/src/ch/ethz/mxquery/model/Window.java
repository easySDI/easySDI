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

package ch.ethz.mxquery.model;

import ch.ethz.mxquery.datamodel.Source;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.util.IntegerList;

/** 
 * A window iterator works always on a store, which materializes the tokens.
 * A window can either be a window with a start and end edge or (also the name states something else)
 * selected items form the store. 
 * @author Tim Kraska (<a href="mailto:mail@tim-kraska.de">mail@tim-kraska.de</a>)
 * @author David Graf (XQueryP-Extension)
 *
 */
public abstract class Window extends Iterator implements IndexIterator { 
	
	//	 Defines the end of stream node id
	public static final int END_OF_STREAM_POSITION = Integer.MAX_VALUE - 1 - 10;
	public static final int END_OF_STREAM_NODE = END_OF_STREAM_POSITION -1;
		
	public Window() {
		super(null,null);
	}
	
	public Window getUnderlyingWindow() throws MXQueryException {
		return this.getNewWindow(1, END_OF_STREAM_POSITION);
	}

	/**
	 * Returns a new iterator consisting of the given items in the integer list. If a position is outside
	 * the underlying sequence, the position is skipped.
	 * @param values Each value in the integer list has to be a position
	 * @return a new Window for the given list of positions
	 * @throws MXQueryException 
	 */
	public abstract Window getNewItemWindow(IntegerList values) throws MXQueryException;
	
	public Window getNewEarlyWindowInterface(int startPosition, XDMIterator startExpr, WindowVariable[] startVars, VariableHolder[] startVarHolders, XDMIterator endExpr, WindowVariable[] endVars, VariableHolder[] endVarHolders){
		throw new RuntimeException("This binding is very new and not everywhere supported");
	}

	public Window getNewEarlyParallelWindowInterface(int startPosition, WindowVariable[] startVars, VariableHolder[] startVarHolders, XDMIterator endExpr, WindowVariable[] endVars, VariableHolder[] endVarHolders){
		throw new RuntimeException("This binding is very new and not everywhere supported");
	}	
	/**
	 * Jumps to the next attribute if possible in the form of /@attributeName
	 * If the jump is not possible, -1 is returned
	 * @param attributeName
	 * @return The event id
	 */
	public Token jumpToNextAttribute(String attributeName)  throws MXQueryException {
		return null;
	}
	
	
	/**
	 * Returns a new iterator for a given start and end position. If the end is outside the underlying
	 * sequence the iterator throws no error but only returns items until the sequence ends
	 * @param startPosition
	 * @param endPosition
	 * @return a new window for the given start and end positions
	 */
	public abstract Window getNewWindow(int startPosition,
			int endPosition) throws MXQueryException;

	/**
	 * Returns the first node id of the window
	 * @return returns the first node id of this window
	 * @throws MXQueryException 
	 */
	public abstract int getStartNode() throws MXQueryException;

	/**
	 * Destroys the connection to the window buffer and gives the used item free for garbage collection
	 *
	 */
	public abstract void destroyWindow();

	public abstract boolean equals(final Object other);

	/**
	 * Every window has a unique id given by the buffer the window is working on.
	 * @return the identifier of this window
	 */
	public abstract int getWindowId();

	/**
	 * Returns the current node id
	 * @return the id of the current node
	 * @throws MXQueryException 
	 */
	protected abstract int getNodeId() throws MXQueryException;

	/**
	 * If a window is not in use it is a schema for other windows.
	 * E.g. a LET/FOR/FORSEQ binds a window to a variable the window is not in use yet.
	 * But in the moment the first variable reference is using the window the window is in used
	 * @return true if the window is use, false otherwise
	 */
	public abstract boolean isWindowInUse();

	/**
	 * Specifies if a window can be used again. 
	 * This method is normally only used by the variable iterator. Be carefully with it, as 
	 * it is mainly responsible to allow proper garbage collection.
	 * @param windowInUse
	 */
	public abstract void setWindowInUse(boolean windowInUse);


	/* position handling (moved from the Iterator interface) */
	private int position = 0;

	public void increasePosition() {
		position++;
	}

	public void setPosition(int p) {
		position = p;
	}
	
	public abstract int getPosition();

	public boolean isWindow() {
		return true;
	}

	public boolean isExprParameter(int valueToCheck, boolean recursive) {
		switch (valueToCheck) {
		case EXPR_PARAM_WINDOW:
			return true;
		default:
			return super.isExprParameter(valueToCheck, recursive);
		}
	}
	
	public int getEndPosition() {
		// TODO Auto-generated method stub
		return 0;
	}

	public int getNextWindowStartPosition() {
		// TODO Auto-generated method stub
		return 0;
	}	
	
	public abstract Source getStore();
}

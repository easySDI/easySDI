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

package ch.ethz.mxquery.update.store.llImpl;

import java.util.Stack;
import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.Identifier;
import ch.ethz.mxquery.datamodel.Source;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.StaticException;
import ch.ethz.mxquery.model.EmptySequenceIterator;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.util.IntegerList;
import ch.ethz.mxquery.xdmio.StoreSet;

/**
 * Iterator hides the XML Store functionality and behaves like the Iterator from
 * the MXQuery.
 * 
 * @author David Alexander Graf
 * 
 */
public class LLStoreIterator extends Window {
	/**
	 * Identifier of the start element
	 */
	
	protected Identifier startID;
	
	//protected Identifier endID;
	
	/**
	 * Start element of this iterator
	 */
	protected LLToken start;

	/**
	 * current element
	 */
	protected LLToken currentElement;

	/**
	 * It is possible to iterator item per item of a sequence (not event per
	 * event). <code>currentItem</code> represents the acutal item. The start
	 * element of the first item is identical with the first event of the normal
	 * iteration.
	 */
	private LLToken currentItem;

	/**
	 * If the iterator reaches a reference, it jumps to the referenced token and
	 * puts the reference on this stack. If it jumps back, the reference is
	 * popped from the stack. Must be done because it is possible to have
	 * references to references.
	 */
	private Stack refs = new Stack();

	/**
	 * If the iterator jumps to a referenced token, the depth of the referenced
	 * token is not correct for the actual context. => After a jump, it computes
	 * the difference that must be added to the depth of the referenced token
	 * and puts the difference to this stack. When jumping back from a
	 * reference, the difference is popped from the stack.
	 */
	private Stack depthDiffs = new Stack();

	private LLStore source;

	private boolean destroyed = false;

	private int windowId;

	private static int newWindowId = 0;

	/**
	 * Constructor.
	 * 
	 * @param startID the id of the node on which the iteration should start
	 * @param store store on which to generate the 
	 */
	public LLStoreIterator(Identifier startID, LLStore store) {
		super();
		this.windowId = LLStoreIterator.newWindowId++;
		this.source = store;
		this.startID = startID;
		if (this.source != null) {
			this.source.referenceAdded();
		}
	}

	private void init() throws MXQueryException {
		source.materialize();
		if (startID != null)
			start =  source.getToken(startID).prev;
		else 
			start = source.head;
		currentElement = start;
		pushRefs();
	}
	
	/**
	 * Walks along <code>this.current</code> till it doesn't point on a
	 * reference token.
	 */
	protected void pushRefs() {
		while (this.currentElement instanceof LLRefToken) {
			LLRefToken srt = (LLRefToken) this.currentElement;
			this.refs.push(this.currentElement);
			int newDiff;
			if (this.depthDiffs.size() > 0) {
				int lastDiff = ((Integer) this.depthDiffs.peek()).intValue();
				newDiff = lastDiff + srt.getDepthDiff();
			} else {
				newDiff = srt.getDepthDiff();
			}
			this.depthDiffs.push(new Integer(newDiff));
			this.currentElement = srt.getRef();
		}
	}

	public Token next() throws MXQueryException {
		if (called == 0) {
			init();
			called++;
		}
		if (this.refs.size() == 0) {
			// non reference mode (the current element is not from a reference)
			// if the current is not already the END_SEQUENCE
			if (this.currentElement.getEventType() != Type.END_SEQUENCE) {
				// goes to the next element and checks if it is a reference
				this.currentElement = this.currentElement.getNext();
				this.pushRefs();
			}
		} else {
			// gets the actual reference from the stack
			LLRefToken srf = (LLRefToken) this.refs.peek();
			if (this.currentElement != srf.getEndForIter()) {
				// If current does not point on the last event of the reference,
				// goes to next and checks if it is a reference.
				this.currentElement = this.currentElement.getNext();
				this.pushRefs();
			} else {
				this.refs.pop();
				this.depthDiffs.pop();
				// checks if srf is reference directly by another reference =>
				// in this case the next reference can also be popped.
				while (true) {
					if (this.refs.size() == 0) {
						break;
					}
					LLRefToken sub = (LLRefToken) this.refs.peek();
					if (sub.getEndEl() != srf) {
						break;
					}
					srf = sub;
					this.refs.pop();
					this.depthDiffs.pop();
				}
				this.currentElement = srf.getNext();
				this.pushRefs();
			}
		}
		return this.currentElement.getToken();
	}

	protected void resetImpl() {
		this.currentElement = this.start;
		this.currentItem = null;
		if (this.depthDiffs == null) {
			this.depthDiffs = new Stack();
		} else {
			this.depthDiffs.removeAllElements();
		}
		if (this.refs == null) {
			this.refs = new Stack();
		} else {
			this.refs.removeAllElements();
		}

	}

	XDMIterator getParent() {
		if (currentItem.parent != null)
			return new LLStoreIterator(currentItem.parent.getId(), this.source);
		else
			return new EmptySequenceIterator(null,null);
	}
	
	// ///////////////////////////////////////////
	// Overwriting default Iterator Operations //
	// ///////////////////////////////////////////
	int getDepth() {
		int diff = 0;
		if (this.depthDiffs.size() > 0) {
			diff = ((Integer) this.depthDiffs.peek()).intValue();
		}
		return this.currentElement.getDepth() + diff;
	}


	protected int getNodeId() throws MXQueryException {
		throw new StaticException(ErrorCodes.A0002_EC_NOT_SUPPORTED, "Not supported in Store Iterator!", loc);
	}

	public int getStartNode() throws MXQueryException {
		throw new StaticException(ErrorCodes.A0002_EC_NOT_SUPPORTED, "Not supported in Store Iterator!", loc);
	}

	public boolean isEmpty() throws MXQueryException {
		return (this.start.getEventType() == Type.END_SEQUENCE)
				|| (this.start.getNext().getEventType() == Type.END_SEQUENCE);
	}

	public XDMIterator getItem(int position) throws MXQueryException {
		if (called == 0) {
			init();
			called++;
		}
		if (position <= 0 || this.isEmpty()) {
			return null;
		} else {
			LLToken cur = this.start.getNext();
			for (int i = 0; i < position - 1; i++) {
				if ((cur = LLToken.getSibling(cur)) == null) {
					return new EmptySequenceIterator(null,loc);
				}
			}
			return this.createNewRefSource(cur);
		}

	}

	public Window getNewWindow(int startPosition, int endPosition)
			throws MXQueryException {
		if (startPosition == 1
				&& endPosition == Window.END_OF_STREAM_POSITION) {
			Window ret = new LLStoreIterator(this.startID, this.source);
			ret.setContext(context,false);
			return ret;
		} else {
			return source.getNewStoreForSequence(startPosition, endPosition).getIterator(context);
		}
	}



	/**
	 * Creates a new Source that contains only a reference token that points on
	 * the passed token.<br />
	 * If the passed <code>llToken</code> is already a reference token, than
	 * the newly create reference token points on the same token as
	 * <code>llToken</code> does.
	 * 
	 * @param llToken
	 * @return StoreIterator that points to the newly created source
	 * @throws MXQueryException
	 */
	private Window createNewRefSource(LLToken llToken) throws MXQueryException {
		LLRefToken ref;
		if (llToken instanceof LLRefToken) {
			ref = new LLRefToken(((LLRefToken) llToken).getRef());
		} else {
			ref = new LLRefToken(llToken);
		}

		StoreSet store = this.source.getStoreSet();
		LLStore newSource = (LLStore) store.createTransactionStore(Thread.currentThread().toString().hashCode());
		newSource.insertFirst(ref);
		return newSource.getIterator(context);
	}

	public boolean hasItem(int position) throws MXQueryException {
		if (called == 0) {
			init();
			called++;
		}
		if (position <= 0 || this.isEmpty()) {
			return false;
		} else if (position == 1) {
			return true;
		} else {
			LLToken cur = this.start.getNext();
			for (int i = 0; i < position - 1; i++) {
				if ((cur = LLToken.getSibling(cur)) == null) {
					return false;
				}
			}
			return true;
		}
	}

	/**
	 * Returns true if there is an item at all or if the actual item as a
	 * sibling.
	 */
	public boolean hasNextItem() throws MXQueryException {
		if (called == 0) {
			init();
			called++;
		}
		if (this.currentItem == null) {
			return !this.isEmpty();
		}
		return LLToken.getSibling(this.currentItem) != null;
	}

	public XDMIterator nextItem() throws MXQueryException {
		if (called == 0) {
			init();
			called++;
		}
		if (this.currentItem == null) {
			if (this.isEmpty()) {
				return null;
			}
			this.currentItem = this.start.getNext();
		} else if (this.hasNextItem()) {
			this.currentItem = LLToken.getSibling(this.currentItem);
		}

		return this.createNewRefSource(this.currentItem);
	}

	public void setContext(Context context, boolean recursive) {
		if (this.context == null)
			this.context = context;
	}

	public void destroyWindow() {
		if (!this.destroyed) {
			this.destroyed = true;
			this.currentElement = null;
			this.currentItem = null;
			this.refs = null;
			this.start = null;
			this.depthDiffs = null;
			this.source.referenceDeleted();
			this.source = null;
		}
	}

	public boolean equals(Object other) {
		if (other != this)
			return false;
		return true;
	}

	public Window getNewItemWindow(IntegerList values) {
		throw new RuntimeException("Not supported yet!");
	}

	public int getWindowId() {
		return this.windowId;
	}

	public int hashCode() {
		throw new RuntimeException("Not supported in Store Iterator!");
	}

	public boolean isWindowInUse() {
		throw new RuntimeException("Not supported in Store Iterator!");
	}

	public void setWindowInUse(boolean windowInUse) {
		throw new RuntimeException("Not supported in Store Iterator!");
	}

	public String getSourceURI() {
		return this.source.getURI();
	}

//	public String toString() {
//		return this.currentElement.toString();
//	}

	public int getPosition() {
		return 0;
	} // TODO
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator it = new LLStoreIterator(startID, source);
		it.setContext(context, false);
		return it;
	}
	
	public Source getStore() {
		return source;
	}
	
}

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


import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.Source;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.model.Window;

/** 
 * Implements the WindowInterface and provides the functionalities the WindowItem- and the WindowSequence-Iterator have
 * in common
 * @author Tim Kraska (<a href="mailto:mail@tim-kraska.de">mail@tim-kraska.de</a>)
 *
 */
public abstract class WindowIterator extends Window {
	
	protected WindowBuffer mat;
	
	protected int windowId;
	
	protected int nodeId = -1;
	
	private boolean windowInUse=false;
	
	public WindowIterator(WindowBuffer mat, int windowId) {
		this.mat = mat;
		this.windowId =windowId;
	}
		
	public abstract int getStartNode();
	
	/**
	 * Deregister a window in the window buffer (and gives the items free for garbage collection if
	 * their are not used by other windows). 
	 * Important: Never use a window again after destroying it!
	 */
	public void destroyWindow(){
		mat.destroyWindow(windowId);
	}
	
	public boolean equals(final Object other) {
		if (!(other instanceof WindowIterator))
			return false;
		WindowIterator castOther = (WindowIterator) other;
		if (windowId == castOther.hashCode()) {
			return true;
		} else {
			return false;
		}
	}
	
	public int getWindowId() {
		return windowId;
	}
	
	protected abstract int getNodeId();
	
	public int hashCode() {
		return windowId;
	}

	
	public void setContext(Context context) throws MXQueryException {
		mat.setContext(context);
	}


	public boolean isWindowInUse() {
		return windowInUse;
	}


	public void setWindowInUse(boolean windowInUse) {
		this.windowInUse = windowInUse;
	}
	
	public Source getStore() {
		return mat;
	}
	
}

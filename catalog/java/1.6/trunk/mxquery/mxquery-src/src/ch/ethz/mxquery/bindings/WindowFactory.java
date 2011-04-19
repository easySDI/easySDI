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
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.iterators.VariableIterator;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.model.updatePrimitives.UpdateableStore;



public class WindowFactory {
	/**
	 * Returns a new window iterator for a source sequence. When the source sequence is already by an 
	 * WindowBuffer the returned window works than on the same buffer 
	 * @param sourceIter 
	 * @param startPosition
	 * @param endPosition
	 * @param sync
	 * @return a new Window for the iterator, spanning the given start and end positions
	 * @throws MXQueryException
	 */	
	public static Window getNewWindow(Context runtime, XDMIterator sourceIter,
			int startPosition, int endPosition, boolean sync)
			throws MXQueryException {
		Window ret = WindowBuffer.getNewWindowInterface(sourceIter,
				startPosition, endPosition, sync, 0,10000, false);
		ret.setContext(runtime, true);
		return ret;
	}
			
	/**
	 * TODO Can be deleted as soon as the eager materialization of irinas store works.
	 * @param sourceIter
	 * @return an eagerly (i.e. completely on first call) materializing window
	 * @throws MXQueryException
	 */
	public static Window getNewWindow_Eager(Context runtime, XDMIterator sourceIter) throws MXQueryException {
		if (sourceIter instanceof Window) {
			return ((Window)sourceIter).getNewWindow(1, Window.END_OF_STREAM_POSITION);
		} else if (sourceIter instanceof VariableIterator) {
			return ((VariableIterator)sourceIter).getUnderlyingIterator();
		} else {
			UpdateableStore newSource = runtime.getStores()
					.createUpdateableStore(null,sourceIter, false, false);
			newSource.materialize();
			Window ret = newSource.getIterator(runtime);
			return ret;
		}
	}

	/**
	 * Returns a new window iterator on the source and creates a store (if there is not already one)
	 * @param context
	 * @param sourceIter
	 * @return A window for the given input iterator
	 * @throws MXQueryException
	 */
	public static Window getNewWindow(Context context, XDMIterator sourceIter)
			throws MXQueryException {
			return WindowFactory.getNewWindow(context, sourceIter, 1,
					Window.END_OF_STREAM_POSITION);
	}
	
	
	/**
	 * The same as getNewWindowInterface(Iterator sourceIter) but with synhronization for LR
	 * @param context
	 * @param sourceIter
	 * @param sync
	 * @return a window for the given input iterator 
	 * @throws MXQueryException
	 */	
	public static Window getNewWindow(Context context, XDMIterator sourceIter,
			boolean sync) throws MXQueryException {
			return WindowFactory.getNewWindow(context, sourceIter, 1,
					Window.END_OF_STREAM_POSITION, sync); 
	}

	/**
	 * Returns a new window iterator on the source with specific start and end positions
	 * @param context
	 * @param sourceIter
	 * @param startPosition start of the window
	 * @param endPosition end of the window
	 * @return a window for this input, spanning the given positions
	 * @throws MXQueryException
	 */
	public static Window getNewWindow(Context context, XDMIterator sourceIter,
			int startPosition, int endPosition) throws MXQueryException {
			return WindowFactory.getNewWindow(context, sourceIter, startPosition,
					endPosition, false);
	}
		
}

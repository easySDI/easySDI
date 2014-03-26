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

package ch.ethz.mxquery.datamodel;

import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.model.Window;

/**
 * Interface for sources that can provide nodes with IDs
 */

public interface Source {
	public int compare(Source store);
	/**
	 * Returns the URI of the source.
	 * 
	 * @return a String representing the URI/Identifier of this source
	 */
	public String getURI();
	
	/**
	 * Returns Iterator that points on the first token in the store.
	 * 
	 * @return Iterator a (Window) Iterator to access the contents of this source
	 * @throws MXQueryException
	 */
	public Window getIterator(Context ctx) throws MXQueryException;
	
	public Source copySource(Context ctx, Vector nestedPredCtxStack) throws MXQueryException;
}

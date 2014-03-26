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

package ch.ethz.mxquery.sms.interfaces;

import ch.ethz.mxquery.datamodel.Source;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.model.XDMIterator;

public interface StreamStore extends Source{
	
	/**
	 * Returns the store id (mostly for debugging)
	 * @return id of the store given at creation time
	 */
	public int getMyId();
	
	/**
	 * Get Token at given position
	 * @param pos Unique id given to the token inside this store instance (token position 
	 * @return the Token at the given position
	 * @throws MXQueryException 
	 */
	public Token get(int pos) throws MXQueryException;
	
	/**
	 * Materialize the given token into the store instance
	 * @param tok
	 * @param event
	 */
	public void buffer(Token tok, int event) throws MXQueryException;
	
	/**
	 * Specify the beginning of a new item
	 *
	 */
	public void newItem();
	/**
	 * For pull stores, add the iterator from which they should pull their data
	 * @param it
	 * @throws MXQueryException
	 */
	public void setIterator(XDMIterator it) throws MXQueryException;
}

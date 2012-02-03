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

import ch.ethz.mxquery.exceptions.MXQueryException;

/**
 * If an iterator implements this class, it should have at least an index on items to allow
 * fast access for each item. 
 * @author Tim Kraska
 *
 * TODO: Maybe this interface can be completly replaced by the WindowIterator abstract class
 */
public interface IndexIterator {

	/**
	 * Checks if the given position is availabe
	 * @param position Position in the sequence (starting with 1)
	 * @return True if the next item is not end_sequence otherwise false
	 * @throws MXQueryException
	 */
	public boolean hasItem(int position) throws MXQueryException;
	
	/**
	 * Returns the item on the given Position. If the item is not evailabe, an empty sequence item
	 * is returned
	 * @param position Position in the sequence (starting with 1)
	 * @return  Item or empty sequence iterator
	 * @throws MXQueryException
	 */
	public XDMIterator getItem(int position) throws MXQueryException;
	
	/**
	 * Checks if a nextItem exists
	 * @return True if the next item is not end_sequence otherwise false
	 * @throws MXQueryException
	 */
	public boolean hasNextItem()  throws MXQueryException ;
	
	/**
	 * Returns the next item. If the item is not evailabe, an empty sequence item
	 * is returned
	 * @return Item or empty sequence iterator
	 * @throws MXQueryException
	 */
	public XDMIterator nextItem()  throws MXQueryException ;
	
//	/**
//	 * Returns the position of the last items 
//	 * @return 
//	 * @throws MXQueryException
//	 */
//	
//	public int lastItemPos() throws MXQueryException ;
}

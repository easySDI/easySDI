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

package ch.ethz.mxquery.opt.index;

import java.util.Vector;

import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.util.IntegerList;

public interface Index {

	/**
	 * Registered a new Index.
	 * @param index the index schema
	 * @return If the Regestration was successfull
	 */
	public boolean registerIndex(IndexSchema index);

	public void compileIndex();
	
	/**
	 * Indexes the value without indexValues :-)
	 * @param value >= 0
	 */
	public void index(int value);
	
	/**
	 * 
	 * @param index
	 * @param indexValues
	 * @param value >= 0
	 */
	public void index(IndexSchema index, Token[] indexValues, int value);
	
	/**
	 * Like a simple value index but for general comparisons
	 * @param index
	 * @param indexValues
	 * @param value >= 0
	 */
	public void index(IndexSchema index, Token[][] indexValues, int value);
	 
	/**
	 * 
	 * @param index
	 * @param indexValues
	 * @return Returns -1 if no window is found
	 */
	public IntegerList retreive(IndexSchema index, Token[] indexValues) throws MXQueryException;
	public IntegerList retreiveAndRemove(IndexSchema index, Token[] indexValues) throws MXQueryException;
	
	/**
	 * Like a simple value index but for general comparisons
	 * @param index
	 * @param indexValues
	 * @return the list of matches, -1 if not found
	 */
	public IntegerList retreive(IndexSchema index, Token[][] indexValues) throws MXQueryException;
	public IntegerList retreiveAndRemove(IndexSchema index, Token[][] indexValues) throws MXQueryException;
	
	public int[] getAll();
	
	public int[] getAndRemoveAll();
	
	/**
	 * Returns the min value
	 * @return the minimal value
	 */
	public int get();
	
	/**
	 * Returns the value at position
	 * @param i
	 * @return the value at i
	 */
	public int get(int i);
	
	public int getAndRemove(int i);
	
	/**
	 * Returns the first value
	 * @param value
	 */
	public void remove(int value);
	
	/**
	 * Returns the min value and removes it
	 * @return the value
	 */
	public int getAndRemove();
	
	/**
	 * Gets the number of elements in the index
	 * @return the number of elements
	 */
	public int size();
	
	public void clear();
	
	public Vector getGroups(IndexSchema schema);
	
	public Vector getValues(IndexSchema schema);
		
}

/**
 * Copyright 2006-2007 ETH Zurich, The iMeMex Project Team
 * see http://www.iMeMex.org for more information on this project
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

package ch.ethz.mxquery.sms.btree;

import java.util.Vector;

import ch.ethz.mxquery.datamodel.DeweyIdentifier;
import ch.ethz.mxquery.datamodel.adm.LinguisticToken;

/**
 * Helper for b-tree leaves. Has to be taylored to different key and value types
 * manually as Generics would use Complex type (inefficent) instead of native
 * types.
 * 
 * @author jens extended by christian/julia
 * 
 * adapted to be CLDC conform: jimhof
 */
public class LeafArrayMap{

	protected Vector keys;

	protected Vector values;

	protected int currentSize = 0;
	
	protected int maxSize;

	protected static final int STOP = 0;

	protected static final int CONTINUE_WITH_BINSEARCH = 1;

	protected static final int CONTINUE_WITH_SCAN = 2;
	
	protected static final int KEY_IN_THIS_LEAF = 3;
	
	protected static final int KEY_IN_PREV_LEAF = 4;
	
	/**
	 * This binary search method is modified to guarantee that, in the presence
	 * of duplicate keys, we will always return the first occurrence of a found
	 * key in the portion of the array being searched (from-to).
	 * 
	 * @param a
	 * @param key
	 * @param from
	 * @param to
	 * @return the position of the key
	 */
	
	public int binarySearch(Vector a, DeweyIdentifier key, int from, int to) {
		
		int low = from;
		int high = to;

		for (; low <= high;) {
				
			int mid = (low + high) >> 1;
			DeweyIdentifier midVal = (DeweyIdentifier)a.elementAt(mid);

			if (midVal.compare(key) < 0) low = mid + 1;
			else if (midVal.compare(key) > 0) high = mid - 1;
			else {
				// key found: search for first occurrence linearly
				// this search is necessary in the presence of duplicates
				int pos = mid - 1;
				while (pos >= from && ((DeweyIdentifier)a.elementAt(pos)).compare(key) == 0) {
					pos--;
				}
				// return last valid position
				return pos + 1;
			}

		}

		
		return -(low + 1); // key not found.
	}

	/**
	 * Instantiates a Leaf Array Map
	 * 
	 * @param n the size of the LeafArrayMap
	 */
	
	public LeafArrayMap(int n) {
		maxSize = n;
		keys = new Vector();
		values = new Vector();
	}
	


	public DeweyIdentifier getMidKey() {
		return (DeweyIdentifier)keys.elementAt(currentSize / 2);
	}

	/**
	 * Splits this map. The split operation will attempt to split this map in
	 * the middle key.
	 * 
	 * @return the right node
	 */
	public LeafArrayMap split() {
//		LeafArrayMap newMap = new LeafArrayMap(keys.size());
//		final int mid = currentSize / 2;
//		int count = 0;
//		for (int i = mid; i < currentSize; i++) {
//			newMap.keys.add(count,keys.elementAt(i));
//			newMap.values.add(count, values.elementAt(i));
//			count++;
//		}
//
//		newMap.currentSize = currentSize - mid;
//		currentSize = mid;
//		return newMap;
		return null;
	}

	/**
	 * Associates the given key with the given value in this array map, if the
	 * insertion point for the key may be found here. If that is not the case,
	 * the method will return true, indicating that the search for the insertion
	 * point should be continued on the next array.
	 * 
	 * @param key
	 * @param value
	 * @return true if the search should continue
	 */
	public boolean tryAdd(DeweyIdentifier key, LinguisticToken value) {
//		if (currentSize == 0) {
//			// insert in this node: we assume overflow leaves are
//			// garbage-collected on deletions
//			keys.add(0, key);
//			values.add(0,value);
//			currentSize++;
//			return false;
//		} else {
//			int pos = binarySearch(keys, key, 0, currentSize - 1);
//			if (pos < 0) {
//				// calculate insertion point
//				pos = -(pos + 1);
//			}
//
//			// find insertion point
//			while (pos < currentSize && ((DeweyIdentifier)keys.elementAt(pos)).compareTo(key) <= 0) {
//				pos++;
//			}
//			/* maxSize) { // was == currentSize, which is nonsens, because then 
//			 * a Leaf will never have more than 1 value..
//			 * But only in LeafArrayMapTest. Leaf will insert if no next.. */
//			if (pos == currentSize) { 
//				// continue search for insertion point in next leaf
//				return true;
//			} else {
//				// well, the insertion point is here
//				addAtPos(key, value, pos);
//				return false;
//			}
//		}
		return false;
	}

	/**
	 * Adds the given mapping at the given position in this array.
	 * 
	 * @param key
	 * @param value
	 * @param pos
	 */
	public void addAtPos(DeweyIdentifier key, LinguisticToken value, int pos) {
		if (pos < currentSize) {

			Vector tempkeys = new Vector();
			Vector tempvalues = new Vector();
			
			int i=0;
			while (i < pos){
				tempkeys.addElement(keys.elementAt(i));
				tempvalues.addElement(values.elementAt(i));
				i++;
			}
			tempkeys.addElement(key);
			tempvalues.addElement(value);
			i++;
			while (i < currentSize){
				tempkeys.addElement(keys.elementAt(i));
				tempvalues.addElement(values.elementAt(i));
				i++;
			}
			currentSize++;
		} else {
			keys.addElement(key);
			values.addElement(value);
			currentSize++;
		}
	}

	/**
	 * Gets the values for the specified key and pushes them in the given push
	 * operator. If we have scanned until the last position of this array, then
	 * maybe more values can be found in the next array. This method returns
	 * true in this situation.
	 */
	public int get(DeweyIdentifier key, BtreePushOperator results) {
		
		if (currentSize == 0) {
			
			// continue search in next leaf
			
			return CONTINUE_WITH_BINSEARCH;
		
		} else {
		
			int pos = binarySearch(keys, key, 0, currentSize - 1);
			
			if (pos < 0) {
				// key not found: if we are at the end of the array, maybe key
				// is in the next one
				pos = -(pos + 1);
				return pos == currentSize ? CONTINUE_WITH_BINSEARCH : STOP;
			
			} else {
				// get values corresponding to key
				return continueGet(pos, key, results);
			}
		}
	}

	public int getFirstKeyAfter(DeweyIdentifier key, int[] atPos) {
		
		if (currentSize == 0) {
			
			// continue search in next leaf
			
			return CONTINUE_WITH_BINSEARCH;
		
		} else {
			
			// try to find the key in the current leaf
			int pos = binarySearch(keys, key, 0, currentSize - 1);
			
			if (pos < 0) {
				
				// key not found: if we are at the end of the array, maybe key
				// is in the next one
				
				pos = -(pos + 1);
				
				// pos either 0 or current size?
				
				if (pos == currentSize) {
					
					// all keys in this leaf are smaller than given key
					// ==> pos must be current size
					// ==> continue search in next leaves until bigger key is found
					// atPos[0] is still INVALID_KEY_INDEX
					
					return CONTINUE_WITH_BINSEARCH;
					
				} else if (pos == 0 ) {
				
					// all keys in this leaf are bigger than the key
					// ==> position must be 0 
					
					atPos[0] = 0;
					return KEY_IN_THIS_LEAF;
				}			else {

					// the key should be somewhere in the current leaf
					
					atPos[0] = pos;
					return KEY_IN_THIS_LEAF;
				
				}
				
			} else {

				// given key must be among keys in this leaf
				// here we will return the pointer to the leaf and the position
				
				atPos[0] = pos;
				return KEY_IN_THIS_LEAF;
				
			}
		}
	}	

	public int getLastKeyBefore(DeweyIdentifier key, int[] atPos) {
		
		if (currentSize == 0) {
			
			// continue search in next leaf
			
			return CONTINUE_WITH_BINSEARCH;
		
		} else {
			
			// try to find the key in the current leaf
			int pos = binarySearch(keys, key, 0, currentSize - 1);
			
			if (pos < 0) {
				
				// key not found: if we are at the end of the array, maybe key
				// is in the next one
				
				pos = -(pos + 1);
				
				// pos either 0 or current size?
				
				if (pos == currentSize) {
					
					// all keys in this leaf are smaller than given key
					// ==> pos must be current size
					// ==> continue search in next leaves until bigger key is found
					// atPos[0] is still INVALID_KEY_INDEX
					
					return CONTINUE_WITH_BINSEARCH;
					
				} else if (pos == 0 ) {
				
					// all keys in this leaf are bigger than the key
					// ==> position must be 0 
					
					atPos[0] = BTree.INVALID_KEY_INDEX;
					
					return KEY_IN_PREV_LEAF;
				
				} else {

					// the key should be somewhere in the current leaf
					
					atPos[0] = pos;
					return KEY_IN_THIS_LEAF;
				
				}
				
			} else {

				// given key must be among keys in this leaf
				// here we will return the pointer to the leaf and the position
				
				atPos[0] = pos;
				return KEY_IN_THIS_LEAF;
				
			}
		}
	}	

	public int continueGet(int pos, DeweyIdentifier key, BtreePushOperator results) {
		while (pos < currentSize && ((DeweyIdentifier)keys.elementAt(pos)).compare(key) == 0) {
			results.pass(values.elementAt(pos));
			pos++;
		}
		return pos == currentSize ? CONTINUE_WITH_SCAN : STOP;
	}

	/**
	 * Remove mappings with the given key. If a value is given, then only a
	 * single mapping is removed. If BTreeConstants.ALL_MAPPINGS is given, then
	 * all mappings for the given key are removed. Returns true if removal must
	 * continue searching on the next leaf.
	 * 
	 * @param key
	 * @param value
	 * @return true if the key was found, false otherwise
	 */
	public boolean remove(DeweyIdentifier key, LinguisticToken value) {
		if (currentSize == 0) {
			// continue search
			return true;
		}
		int pos = binarySearch(keys, key, 0, currentSize - 1);
		if (pos < 0) {
			// key does not exist here, check if we should go to next array
			pos = -(pos + 1);
			return pos == currentSize;
			//return pos == maxSize;
		} else {
			// key exists, delete:
			// first find occurrence range of key in this array
			int firstOccurrence = -1; // pos
			int lastOccurrence;

			while (pos < currentSize && ((DeweyIdentifier)keys.elementAt(pos)).compare(key) == 0) {

				if (value == null) { // was BTreeConstants.ALL_MAPPINGS) (removed for generics)
					// mark first occurrence
					if (firstOccurrence == -1) {
						firstOccurrence = pos;
					}
				} else {
					if (((LinguisticToken)values.elementAt(pos)).compareTo(value) == 0) {
						// found desired mapping: only that mapping should be
						// removed
						firstOccurrence = pos;
						break;
					}
				}
				// continue scanning
				pos++;
			}
			// fix last occurrence
			if (value == null) {// BTreeConstants.ALL_MAPPINGS) {
				lastOccurrence = pos - 1;
			} else {
				lastOccurrence = firstOccurrence;
			}
			// here too, null was BTreeConstants.ALL_MAPPINGS
			// maxSize was currentSize
			boolean continueSearch = (value == null) || (firstOccurrence == -1) ? (pos == currentSize)
					: false;

			// now delete all occurrences in one move, if necessary
			if (firstOccurrence != -1) {
				//System.arraycopy(keys, lastOccurrence + 1, keys, firstOccurrence, currentSize - (lastOccurrence + 1));
				//System.arraycopy(values, lastOccurrence + 1, values, firstOccurrence, currentSize
						//- (lastOccurrence + 1));
				
				int numOfOccurrences = lastOccurrence-firstOccurrence+1;

				
				Vector newKeys = new Vector();
				Vector newValues = new Vector();
				
				for (int i=0; i < firstOccurrence; ++i){
					newKeys.addElement(keys.elementAt(i));
					newValues.addElement(values.elementAt(i));
				}
				
				//for (int i= (lastOccurrence+1); i < currentSize - (lastOccurrence + 1);i++ ){
				for (int i= lastOccurrence+1; i < currentSize;i++ ){

					newKeys = addElementAtPos(newKeys, i-numOfOccurrences, keys.elementAt(i));
					newValues = addElementAtPos(newValues, i-numOfOccurrences, values.elementAt(i));
					//newKeys.add(i-numOfOccurrences, keys.elementAt(i));
					//newValues.add(i-numOfOccurrences, values.elementAt(i));
				}
				
				keys = newKeys;
				values = newValues;
				
				currentSize -= numOfOccurrences;
			}

			return continueSearch;
		}
	}

	public String toString() {
		StringBuffer sb = new StringBuffer();

		for (int i = 0; i < currentSize; i++) {
			sb.append(keys.elementAt(i) + "=");
			sb.append(values.elementAt(i));
			if (i + 1 < currentSize) sb.append(", ");

		}

		return sb.toString();
	}

	public int size() {
		return currentSize;
	}

	/**
	 * Obtains all values mapped to the given key range in this array. If the
	 * search stops in the last position of the array, then this method returns
	 * true, flagging that continuing to the next array is necessary.
	 * 
	 * @param lowKey
	 * @param highKey
	 * @param results
	 * @return the status of the search: {@link #CONTINUE_WITH_SCAN}, {@link #CONTINUE_WITH_BINSEARCH}
	 */
	public int queryRange(DeweyIdentifier lowKey, DeweyIdentifier highKey, BtreePushOperator results) {
		
		if (currentSize == 0) {
			
			return CONTINUE_WITH_BINSEARCH; // maybe leaf was emptied by deletions
		
		} else {
		
			int pos = binarySearch(keys, lowKey, 0, currentSize - 1);
			
			if (pos < 0) {

				// key not found: get starting point for scan
				pos = -(pos + 1);
			}

			// scan from given position onwards
			return continueScan(pos, highKey, results);
		}
	}
	
	public int continueScan(int pos, DeweyIdentifier highKey, BtreePushOperator results) {
		
		boolean returnedSomething = false;
		
		while (pos < currentSize && ((DeweyIdentifier)keys.elementAt(pos)).compare(highKey) <= 0) {
			results.pass(values.elementAt(pos));
			pos++;
			returnedSomething = true;
		}
		//again, currentSize should be maxSize
		return pos == currentSize ? (returnedSomething ? CONTINUE_WITH_SCAN : CONTINUE_WITH_BINSEARCH) : STOP;
	}

	/**
	 * Deletes the value and key at position i
	 * 
	 * @param i the position the key and value is deleted at
	 */
	
	public void deleteAtPos(int i) {
		
		Vector newKeys = new Vector();
		Vector newValues = new Vector();
		
		
		for (int j=0;j<i;j++){
			newKeys = addElementAtPos(newKeys, j, keys.elementAt(j));
			newValues = addElementAtPos(newValues, j, values.elementAt(j));
			//newKeys.set(j,keys.elementAt(j));
			//newValues.set(j,values.elementAt(j));
		}
		
		for (int j=i+1;j<currentSize;j++){
			newKeys = addElementAtPos(newKeys, j-1, keys.elementAt(j));
			newValues = addElementAtPos(newValues, j-1, values.elementAt(j));
			//newKeys.set(j-1,keys.elementAt(j));
			//newValues.set(j-1,values.elementAt(j));
		}
		
		keys = newKeys;
		values = newValues;
		
		currentSize--;
	}
	
	private Vector addElementAtPos(Vector a, int pos, Object element){
		Vector temp = new Vector();
		for (int i=0; i < pos; i++){
			temp.addElement(a.elementAt(i));
		}
		temp.addElement(element);
		for (int i=pos; i<a.size(); i++){
			temp.addElement(a.elementAt(i));
		}
		return temp;
	}
	
}

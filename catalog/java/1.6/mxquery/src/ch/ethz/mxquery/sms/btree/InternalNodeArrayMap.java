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

/**
 * Helper for b-tree nodes. This is a typed version similar to LeafArrayMap.
 * Generics not used due to inefficient use of complex types in Java 5.
 * 
 * @author jens extended by christian/julia
 * 
 * adapted by jimhof (adapted to be CLDC conform)
 */
public class InternalNodeArrayMap {

	/** n keys stored on this internal node */
	protected Vector keys;

	/**
	 * n + 1 pointers stored in this internal node - left is index-aligned with
	 * keys
	 */
	protected Vector nodes;

	/**
	 * number of keys in this map. Note that we have one extra pointer to the
	 * left (position 0).
	 */
	protected int currentSize = 0;

	public int binarySearch(Vector a, DeweyIdentifier key, int from, int to) {
		int low = from;
		int high = to;

		for (; low <= high;) {
			int mid = (low + high) >> 1;
			DeweyIdentifier midVal = (DeweyIdentifier)a.elementAt(mid);

			if (midVal.compare(key) < 0) low = mid + 1;
			else if (midVal.compare(key) > 0) high = mid - 1;
			else return mid; // key found

		}
		return -(low + 1); // key not found.
	}

	/** Initiates the InternalNodeArrayMap*/
	public InternalNodeArrayMap(int n) {
		keys = new Vector();
		nodes = new Vector();
		//nodes.add(0,null);
		nodes = addElementAtPos(nodes, 0, null);
	}

	
	public DeweyIdentifier getMidKey() {
		return (DeweyIdentifier)keys.elementAt(currentSize / 2);
	}

	/**
	 * Splits this map, keeps entries from 0 to (mid-1) and returns a new map
	 * with entries from (mid+1) to (currentSize-1). The key mid is no longer
	 * present in either map and thus should be promoted.
	 * 
	 * @return the right node map
	 */
	public InternalNodeArrayMap split() {
		InternalNodeArrayMap newMap = new InternalNodeArrayMap(keys.size());
		final int mid = currentSize / 2;
		int count = 0;
		//newMap.nodes.add(0,nodes.elementAt(mid + 1));
		newMap.nodes = addElementAtPos(newMap.nodes, 0, nodes.elementAt(mid + 1));
		for (int i = mid + 1; i < currentSize; i++) {
			newMap.keys = addElementAtPos(newMap.keys, count,keys.elementAt(i));
			newMap.nodes = addElementAtPos(newMap.nodes, ++count, nodes.elementAt(i + 1));
//			newMap.keys.add(count,keys.elementAt(i));
//			newMap.nodes.add(++count,nodes.elementAt(i + 1));
		}

		// to allow garbage collection, nullify remaining pointers in original
		// map
		for (int i = mid; i < currentSize; i++) {
			//nodes.set(i + 1,null);
			nodes = addElementAtPos(nodes, i+1, null);
		}

		newMap.currentSize = currentSize - mid - 1;
		currentSize = mid;
		return newMap;
	}

	/**
	 * Puts the given key to rightNode association in the node array map.
	 * 
	 * @param key
	 * @param rightNode
	 */
	public void put(DeweyIdentifier key, BTreeNode rightNode) {
		if (currentSize == 0) {
			keys = addElementAtPos(keys, 0, key);
			nodes = addElementAtPos(nodes, 1, rightNode);
			//keys.add(0,key);
			//nodes.add(1,rightNode);
			currentSize++;
			return;
		}
		int pos = binarySearch(keys, key, 0, currentSize - 1);
		if (pos >= 0) { // key exists, replace:
			keys = addElementAtPos(keys, pos, key);
			nodes = addElementAtPos(nodes, pos+1, rightNode);
			//keys.set(pos,key);
			//nodes.set(pos + 1,rightNode);
		} else { // key does not exist, insert:
			pos = -(pos + 1);

			if (pos < currentSize) {
				keys = addElementAtPos(keys, pos, key);
				nodes = addElementAtPos(nodes, pos+1, rightNode);
				//keys.add(pos,key);
				//nodes.add(pos+1,rightNode);
				currentSize++;
			} else {
				keys = addElementAtPos(keys, currentSize, key);
				nodes = addElementAtPos(nodes, currentSize+1, rightNode);
				//keys.add(currentSize,key);
				//nodes.add(currentSize + 1,rightNode);
				currentSize++;
			}
		}
	}

	/**
	 * Returns the node corresponding to the interval in which the provided key
	 * falls.
	 * 
	 * @param key
	 * @return the node for this key
	 */
	public BTreeNode get(DeweyIdentifier key) {
		int pos = getIntervalPosition(key);
		if (pos == -1) return null;
		else return (BTreeNode)nodes.elementAt(pos);
	}

	/**
	 * Obtains the position in the nodes array that represents the interval in
	 * which the provided key falls.
	 * 
	 * @param key
	 * @return the position if found, -1 otherwise
	 */
	public int getIntervalPosition(DeweyIdentifier key) {
		if (currentSize == 0) {
			return -1;
		} else {
			int pos = binarySearch(keys, key, 0, currentSize - 1);

			// we are left-aligned, so we take equal to the right, non-equal at
			// insertion point
			if (pos < 0) {
				// key not found: calculate insertion point
				pos = -(pos + 1);
			} else {
				// key found: take right path
				pos++;
			}
			return pos;
		}
	}

	/**
	 * Returns false if key was not found. This method does not touch the
	 * left-most node in the array map, as the left-most node property of having
	 * keys smaller than the key of the left-most key will be kept if that key
	 * is deleted.
	 * 
	 * @param key
	 * @return true if found, false if not
	 */
	public boolean delete(DeweyIdentifier key) {
		if (currentSize == 0) {
			return false;
		}
		int pos = binarySearch(keys, key, 0, currentSize - 1);
		if (pos >= 0) { // key exists, delete:
			deleteAtPos(pos);
			return true;
		} else { // key does not exist, return false:
			return false;
		}
	}

	/**
	 * Deletes the key-node mapping at the given position.
	 * 
	 * @param pos
	 */
	public void deleteAtPos(int pos) {
		Vector  newKeys = new Vector();
		Vector  newNodes = new Vector();
		
		for (int i=0;i<pos-1;i++){
			newKeys.addElement(keys.elementAt(i));
			newNodes.addElement(nodes.elementAt(i));
		}
		newNodes.addElement(nodes.elementAt(pos-1));
		
		
//		for (int i=pos;i<currentSize-1;i++){
//			newKeys.add(i, keys.elementAt(i+1));
//			newNodes.add(i+1,nodes.elementAt(i+2));
//		}
		
		//nodes.set(currentSize,null); // allow garbage-collection -- since we swap the pointers, this is not necessary anymore?
		keys=newKeys;
		nodes=newNodes;
		currentSize--;
	}

	public String toString() {
		StringBuffer sb = new StringBuffer();

		if (nodes.elementAt(0) == null) {
			sb.append("NULL | ");
		} else {
			String nodeValue = nodes.elementAt(0) == null ? null : Integer.toString(nodes.elementAt(0).hashCode());
			sb.append(nodeValue + " | ");
		}
		for (int i = 0; i < currentSize; i++) {
			sb.append(keys.elementAt(i) + " | ");

			String nodeValue = nodes.elementAt(i + 1) == null ? null : Integer.toString(nodes.elementAt(i + 1).hashCode());
			sb.append(nodeValue);
			if (i + 1 < currentSize) sb.append(" | ");

		}

		return sb.toString();
	}

	public int size() {
		return currentSize;
	}
	
	/**
	 * Adds the given mapping at the given position in this array.
	 * 
	 * @param key
	 * @param rightNode
	 * @param pos
	 */

	public void addAtPos(DeweyIdentifier key, int pos, BTreeNode rightNode) {
		if (currentSize == 0) {
			keys = addElementAtPos(keys, 0, key);
			nodes = addElementAtPos(nodes, 1, rightNode);
			//keys.add(0,key);
			//nodes.add(1,rightNode);
			currentSize++;
			return;
		}
		/* should look for existing key. 
		 * but since only used by bulkAdd, just add.
		 * without the workaround of arraycopy */ 
			keys = addElementAtPos(keys, pos, key);
			nodes = addElementAtPos(nodes, pos+1, rightNode);
			//keys.add(pos,key);
			//nodes.add(pos + 1,rightNode);
			currentSize++;
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

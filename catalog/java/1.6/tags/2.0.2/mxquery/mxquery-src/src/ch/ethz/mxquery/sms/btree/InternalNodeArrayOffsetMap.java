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
 * holds offsets into file instead of BTreeNodes. same functionality as
 * InternalNodeArrayMap. Watch out for *not to use* functions that make
 * no sense since there are no InternalNodes but only offsets. Still they
 * must be there to allow type hierarchy.
 * 
 * @author christian, adapted by jimhof (adapted to be CLDC conform)
 */
public class InternalNodeArrayOffsetMap extends InternalNodeArrayMap{

	/**
	 * n + 1 pointers stored in this internal node - left is index-aligned with
	 * keys
	 */
	protected Vector mynodes;
	
	/** Initiates the InternalNodeArrayMap*/
	public InternalNodeArrayOffsetMap(int n) {
		super(n);
		mynodes = new Vector();
	}

	/**
	 * Splits this map, keeps entries from 0 to (mid-1) and returns a new map
	 * with entries from (mid+1) to (currentSize-1). The key mid is no longer
	 * present in either map and thus should be promoted.
	 * 
	 * @return the right map
	 */
	public InternalNodeArrayMap split() {
		InternalNodeArrayOffsetMap newMap = new InternalNodeArrayOffsetMap(keys.size());
		final int mid = currentSize / 2;
		int count = 0;
		//newMap.mynodes.add(0,mynodes.elementAt(mid + 1));
		newMap.mynodes = addElementAtPos(newMap.mynodes,0,mynodes.elementAt(mid + 1));
		for (int i = mid + 1; i < currentSize; i++) {
			newMap.keys = addElementAtPos(newMap.keys, count,keys.elementAt(i));
			newMap.mynodes = addElementAtPos(newMap.mynodes, ++count, mynodes.elementAt(i+1));
			//newMap.keys.add(count,keys.elementAt(i));
			//newMap.mynodes.add(++count,mynodes.elementAt(i + 1));
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
	public void put(DeweyIdentifier key, Long rightNode) {
		if (currentSize == 0) {
			keys = addElementAtPos(keys, 0, key);
			//keys.add(0,key);
			mynodes = addElementAtPos(mynodes,1,rightNode);
			//mynodes.add(1,rightNode);
			
			currentSize++;
			return;
		}
		int pos = binarySearch(keys, key, 0, currentSize - 1);
		if (pos >= 0) { // key exists, replace:
			keys = addElementAtPos(keys, pos, key);
			mynodes = addElementAtPos(mynodes, pos+1, rightNode);
//			keys.set(pos,key);
//			mynodes.set(pos + 1,rightNode);
		} else { // key does not exist, insert:
			pos = -(pos + 1);

			if (pos < currentSize) {
				keys = addElementAtPos(keys, pos, key);
				mynodes = addElementAtPos(mynodes, pos+1, rightNode);
//				keys.add(pos,key);
//				mynodes.add(pos+1,rightNode);
				currentSize++;
			} else {
				keys = addElementAtPos(keys, currentSize, key);
				mynodes = addElementAtPos(mynodes, currentSize+1, rightNode);
//				keys.add(currentSize,key);
//				mynodes.add(currentSize + 1,rightNode);
				currentSize++;
			}
		}
	}

	/**
	 * Returns the offset of the node corresponding to the interval
	 * in which the provided key falls.
	 * 
	 * @param key
	 * @return the offset
	 */
	public Long getOffset(DeweyIdentifier key) {
		int pos = getIntervalPosition(key);
		if (pos == -1) return null;
		else return (Long)mynodes.elementAt(pos);
	}
	
	public Long getNodeOffset(int pos) {
		if (pos == -1) return null;
		else return (Long)mynodes.elementAt(pos);
	}

	/**
	 * Returns the node corresponding to the interval in which the provided key
	 * falls.
	 * 
	 * @param key
	 * @return the node for this key
	 */
	public BTreeNode get(DeweyIdentifier key) {
		return null;
	}

	/**
	 * Deletes the key-node mapping at the given position.
	 * @param pos
	 */
	public void deleteAtPos(int pos) {
		
		Vector  newKeys = new Vector();
		Vector  newNodes = new Vector();
		
		for (int i=0;i<pos;i++){
			newKeys = addElementAtPos(newKeys, i, keys.elementAt(i));
			newNodes = addElementAtPos(newNodes, i, mynodes.elementAt(i));
			//newKeys.add(i, keys.elementAt(i));
			//newNodes.add(i,mynodes.elementAt(i));
		}
		newNodes = addElementAtPos(newNodes, pos, mynodes.elementAt(pos));
		//newNodes.add(pos,mynodes.elementAt(pos));
		
		
		for (int i=pos;i<currentSize-1;i++){
			newKeys = addElementAtPos(newKeys, i, keys.elementAt(i+1));
			newNodes = addElementAtPos(newNodes, i+1, mynodes.elementAt(i+2));
			//newKeys.add(i, keys.elementAt(i+1));
			//newNodes.add(i+1,mynodes.elementAt(i+2));
		}
		
		keys=newKeys;
		mynodes=newNodes;
		currentSize--;
	}


	public String toString() {
		StringBuffer sb = new StringBuffer();

		if (mynodes.elementAt(0) == null) {
			sb.append("NULL | ");
		} else {
			String nodeValue = mynodes.elementAt(0) == null ? null : Long.toString(mynodes.elementAt(0).hashCode());
			sb.append(nodeValue + " | ");
		}
		for (int i = 0; i < currentSize; i++) {
			sb.append(keys.elementAt(i) + " | ");

			String nodeValue = mynodes.elementAt(i + 1) == null ? null : Long.toString(mynodes.elementAt(i + 1).hashCode());
			sb.append(nodeValue);
			if (i + 1 < currentSize) sb.append(" | ");

		}

		return sb.toString();
	}
	
	/**
	 * Adds the given mapping at the given position in this array.
	 * 
	 * @param key
	 * @param rightNode
	 * @param pos
	 */

	public void addAtPos(DeweyIdentifier key, int pos, Long rightNode) {
		if (currentSize == 0) {
			keys = addElementAtPos(keys, 0, key);
			mynodes = addElementAtPos(mynodes, 1, rightNode);
			//keys.add(0,key);
			//mynodes.add(1,rightNode);
			currentSize++;
			return;
		}
		/* should look for existing key. 
		 * but since only used by bulkAdd, just add.
		 * without the workaround of arraycopy */
		keys = addElementAtPos(keys, pos, key);
		mynodes = addElementAtPos(mynodes, pos+1, rightNode);
		//keys.add(pos,key);
		//mynodes.add(pos + 1,rightNode);
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

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

package ch.ethz.mxquery.util;

public class IntegerList {

	private static final int DEFAULT_CAPACITY = 10;

	private int size;

	private int[] data;
	
	private int modCount;

	public IntegerList(int capacity) {
		if (capacity < 0)
			throw new IllegalArgumentException();
		data = new int[capacity];
	}

	public IntegerList() {
		this(DEFAULT_CAPACITY);
	}


	/**
	 * Guarantees a minimum size
	 */
	public void ensureCapacity(int minCapacity) {
		int current = data.length;

		if (minCapacity > current) {
			int[] newData = new int[Math.max(current * 2, minCapacity)];
			System.arraycopy(data, 0, newData, 0, size);
			data = newData;
		}
	}

	/**
	 * Returns the number of elements in this list.
	 * 
	 * @return the list size
	 */
	public int size() {
		return size;
	}

	/**
	 * Checks if the list is empty.
	 * 
	 * @return true if there are no elements
	 */
	public boolean isEmpty() {
		return size == 0;
	}

	/**
	 * Returns true iff element is in this ArrayList.
	 * 
	 * @param e
	 *            the element whose inclusion in the List is being tested
	 * @return true if the list contains e
	 */
	public boolean contains(int e) {
		return indexOf(e) != -1;
	}

	/**
	 * Returns the lowest index at which element appears in this List, or -1 if
	 * it does not appear.
	 * 
	 * @param e
	 *            the element whose inclusion in the List is being tested
	 * @return the index where e was found
	 */
	public int indexOf(int e) {
		for (int i = 0; i < size; i++)
			if (i == data[i])
				return i;
		return -1;
	}

	/**
	 * Returns the highest index at which element appears in this List, or -1 if
	 * it does not appear.
	 * 
	 * @param e
	 *            the element whose inclusion in the List is being tested
	 * @return the index where e was found
	 */
	public int lastIndexOf(int e) {
		for (int i = size - 1; i >= 0; i--)
			if (e == data[i])
				return i;
		return -1;
	}

	/**
	 * Creates a shallow copy of this ArrayList (elements are not cloned).
	 * 
	 * @return the cloned object
	 */
	public IntegerList copy() {
		IntegerList clone = new IntegerList();
		clone.size = this.size;
		clone.modCount = this.modCount;
		clone.data = (int[]) data.clone();
		return clone;
	}

	/**
	 * Returns an Object array containing all of the elements in this ArrayList.
	 * The array is independent of this list.
	 * 
	 * @return an array representation of this list
	 */
	public int[] toArray() {
		int[] array = new int[size];
		System.arraycopy(data, 0, array, 0, size);
		return array;
	}
	
	public void addAll(int[] all){
		int neededSize = size + all.length;
		if(!(data.length > neededSize)){
			int[] array = new int[neededSize];
			System.arraycopy(data, 0, array, 0, size);
			System.arraycopy(all, 0, array, size, all.length);
			data = array;
		}else{
			System.arraycopy(all, 0, data, size, all.length);
		}
		size = neededSize;
	}

	public void addAll(IntegerList all){
		addAll(all.toArray());
	}

	/**
	 * Retrieves the element at the user-supplied index.
	 * 
	 * @param index
	 *            the index of the element we are fetching
	 * @throws IndexOutOfBoundsException
	 *             if index &lt; 0 || index &gt;= size()
	 */
	public int get(int index) {
		checkBoundExclusive(index);
		return data[index];
	}


	public int set(int index, int e) {
		checkBoundExclusive(index);
		int result = data[index];
		data[index] = e;
		return result;
	}
	
	/**
	 * Removes the item positions in integerList from this list
	 * @param e
	 */
	public void remove(IntegerList e){
		//TODO: This can be handled better
		for(int i = e.size -1 ; i >= 0 ;i--){
			remove(e.get(i));
		}
	}
	
//	private int min(int a, int b){
//		if(a > b){
//			return b;
//		}else{
//			return a;
//		}
//	}

	/**
	 * Appends the supplied element to the end of this list. The element, e, can
	 * be an object of any type or null.
	 * 
	 * @param e
	 *            the element to be appended to this list
	 * @return true, the add will always succeed
	 */
	public boolean add(int e) {
		modCount++;
		if (size == data.length)
			ensureCapacity(size + 1);
		data[size++] = e;
		return true;
	}


	/**
	 * Adds the supplied element at the specified index, shifting all elements
	 * currently at that index or higher one to the right. The element, e, can
	 * be an object of any type or null.
	 * 
	 * @param index
	 *            the index at which the element is being added
	 * @param e
	 *            the item being added
	 * @throws IndexOutOfBoundsException
	 *             if index &lt; 0 || index &gt; size()
	 */
	public void add(int index, int e) {
		checkBoundInclusive(index);
		modCount++;
		if (size == data.length)
			ensureCapacity(size + 1);
		if (index != size)
			System.arraycopy(data, index, data, index + 1, size - index);
		data[index] = e;
		size++;
	}

	/**
	 * Removes the element at the user-supplied index.
	 * 
	 * @param index
	 *            the index of the element to be removed
	 * @return the removed Object
	 * @throws IndexOutOfBoundsException
	 *             if index &lt; 0 || index &gt;= size()
	 */
	public int remove(int index) {
		checkBoundExclusive(index);
		int r = data[index];
		modCount++;
		if (index != --size)
			System.arraycopy(data, index + 1, data, index, size - index);
		return r;
	}

	/**
	 * Removes all elements from this List
	 */
	public void clear() {
		if (size > 0) {
			modCount++;
			size = 0;
		}
	}


	/**
	 * Removes all elements in the half-open interval [fromIndex, toIndex). Does
	 * nothing when toIndex is equal to fromIndex.
	 * 
	 * @param fromIndex
	 *            the first index which will be removed
	 * @param toIndex
	 *            one greater than the last index which will be removed
	 * @throws IndexOutOfBoundsException
	 *             if fromIndex &gt; toIndex
	 */
	public void removeRange(int fromIndex, int toIndex) {
		int change = toIndex - fromIndex;
		if (change > 0) {
			modCount++;
			System.arraycopy(data, toIndex, data, fromIndex, size - toIndex);
			size -= change;
		} else if (change < 0)
			throw new IndexOutOfBoundsException();
	}

	/**
	 * Checks that the index is in the range of possible elements (inclusive).
	 * 
	 * @param index
	 *            the index to check
	 * @throws IndexOutOfBoundsException
	 *             if index &gt; size
	 */
	private void checkBoundInclusive(int index) {
		// Implementation note: we do not check for negative ranges here, since
		// use of a negative index will cause an ArrayIndexOutOfBoundsException,
		// a subclass of the required exception, with no effort on our part.
		if (index > size)
			throw new IndexOutOfBoundsException("Index: " + index + ", Size: " + size);
	}

	/**
	 * Checks that the index is in the range of existing elements (exclusive).
	 * 
	 * @param index
	 *            the index to check
	 * @throws IndexOutOfBoundsException
	 *             if index &gt;= size
	 */
	private void checkBoundExclusive(int index) {
		// Implementation note: we do not check for negative ranges here, since
		// use of a negative index will cause an ArrayIndexOutOfBoundsException,
		// a subclass of the required exception, with no effort on our part.
		if (index >= size)
			throw new IndexOutOfBoundsException("Index: " + index + ", Size: " + size);
	}
	
	public void removeValue(int value){
		
		int i =0;
		while (data[i++]!=value);
		i--;
		remove(i);
		
	}

}

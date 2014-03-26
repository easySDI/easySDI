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

import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.util.Comparator;

/**
 * Quick sort. Sorts an array of objects with a corresponding compare class.
 * 
 * @author David Alexander Graf
 * 
 */
public class QuickSort {
	private Object[] arr;
	private Comparator comp;

	private QuickSort(Object[] arr, Comparator comp) {
		this.arr = arr;
		this.comp = comp;
	}

	public static void sort(Object[] arr, Comparator comp) throws MXQueryException {
		if (arr == null || arr.length == 0) {
			return;
		}
		QuickSort qs = new QuickSort(arr, comp);
		qs.sort(0, arr.length - 1);
	}

	private void sort(int left, int right) throws MXQueryException {
		int l1, l2, r1, r2;
		Object key;
		l1 = left;
		r2 = right;

		key = this.arr[left];

		while (left <= right) {
			while (this.comp.compare(this.arr[left], key) < 0 && left < arr.length-1) {
				left++;
			}
			while (this.comp.compare(this.arr[right], key) > 0 && right > 0) {
				right--;
			}

			if (left <= right) {
				Object temp = this.arr[left];
				this.arr[left] = this.arr[right];
				this.arr[right] = temp;

				left++;
				right--;
			}
		}

		r1 = right;
		l2 = left;

		if (l1 < r1) {
			this.sort(l1, r1);
		}

		if (l2 < r2) {
			this.sort(l2, r2);
		}
	}
}

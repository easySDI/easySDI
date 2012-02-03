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

/**
 * Stores information about a deletion occurred at a lower level of the b-tree.
 * 
 * @author marcos, adapted by jimhof (adapted to be CLDC conform)
 */
public class RemoveInfo {

	int removedValue;

	boolean redistribution;

	int pivot; // for redistributions

	boolean merge;
	
	boolean operationWithNext;
	
	public RemoveInfo(int removedValue, boolean redistribution, int pivot, boolean merge, boolean operationWithNext) {
		this.removedValue = removedValue;
		this.redistribution = redistribution;
		this.pivot = pivot;
		this.merge = merge;
		this.operationWithNext = operationWithNext;
	}
	
}

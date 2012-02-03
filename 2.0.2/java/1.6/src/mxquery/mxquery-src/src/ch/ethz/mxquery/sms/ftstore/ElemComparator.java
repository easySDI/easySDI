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

package ch.ethz.mxquery.sms.ftstore;

import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.util.Comparator;
import ch.ethz.mxquery.util.ObjectObjectPair;

/**
 * Helper class for FTTokenBufferStore: to calculate top n words
 * @author jimhof
 *
 */

public class ElemComparator implements Comparator {

	public int compare(Object o1, Object o2) throws MXQueryException {
		Object obj1 = ((ObjectObjectPair) o1).getFirst();
		Object obj2 = ((ObjectObjectPair) o2).getFirst();
		
		if (obj1 instanceof Integer && obj2 instanceof Integer){
		
			Integer int1 = (Integer) obj1;
			Integer int2 = (Integer) obj2;
			
			int i1 = int1.intValue();
			int i2 = int2.intValue();
			
			if (i1 == i2){
				return 0;
			}
			else if (i1 > i2){
				return 1;
			}
			else {
				return -1;
			}
		}
		return Integer.MAX_VALUE;
	}

}

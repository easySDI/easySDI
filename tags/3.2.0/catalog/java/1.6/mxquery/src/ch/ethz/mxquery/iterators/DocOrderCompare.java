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
package ch.ethz.mxquery.iterators;

import ch.ethz.mxquery.datamodel.Identifier;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.util.Comparator;
/**
 * An object of this class is used for compare in sorting the document order
 * @author anands
 *
 */
public class DocOrderCompare implements Comparator {

	public int compare(Object o1, Object o2) throws MXQueryException {
		Identifier i0 = ((DocOrderObject)o1).getIdentifier();
		Identifier i1 = ((DocOrderObject)o2).getIdentifier();
		
//		//FIXME cheap hack around the problem that elements without type (and therefore ID) need to be sorted in doc order 
//		
//		if(i0 == null && i1 == null){
//			return 0;
//		}else if(i0 == null){
//			return -1;
//		}else if(i1 == null){
//			return 1;
//		}
		int c = i0.getStore().compare(i1.getStore());
		if(c == 0){
			int i = i1.compare(i0);
			if(i==0)
				return 0;
			else if(i==1)
				return 1;
			else
				return -1;
		}
		else
			return c;
	}

}

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

import ch.ethz.mxquery.util.Traversable;

public abstract class IndexSchema implements Traversable {

	private  boolean groupByIndex = false;
	protected boolean simpleValueIndex = false;
		
	private int indexNb;
	
	public IndexSchema(int indexNb){
		this.indexNb = indexNb;
	}
	
	public abstract int size();
	
	public abstract String getColumnName(int position);
	
	public abstract int getComparator(int position);
	
	public abstract int getCompareType(int position);
	
	public int getId(){
		return indexNb;
	}
	
	//TODO: At some point the index schema comparison should be done via a hashcode
	/**
	 * In some cases it is not possible to give a unique id at front
	 * @param indexNb
	 */
	public void setId(int indexNb){
		this.indexNb = indexNb;
	}

	public boolean isSimpleValueIndex() {
		return simpleValueIndex;
	}

	public void setSimpleValueIndex(boolean simpleValueIndex) {
		this.simpleValueIndex = simpleValueIndex;
	}

	public void setGroupByIndex(boolean groupByIndex) {
		this.groupByIndex = groupByIndex;
	}

	public boolean isGroupByIndex() {
		return groupByIndex;
	}
	
}

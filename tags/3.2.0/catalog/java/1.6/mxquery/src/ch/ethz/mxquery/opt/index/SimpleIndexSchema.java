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

//import java.util.List;
import java.util.Vector;
import ch.ethz.mxquery.util.IntegerList;
import ch.ethz.mxquery.util.KXmlSerializer;
//import ch.ethz.mxquery.util.Traversable;

public class SimpleIndexSchema extends IndexSchema {

	private IntegerList comparators = new IntegerList();
	private IntegerList compareTypes = new IntegerList();
	private Vector columnNames = new Vector();
	
	public SimpleIndexSchema(int indexNb){
		super(indexNb);
	}
	
	public void registerValue(int comparator, int compareType, String columnName){
		comparators.add(comparator);
		compareTypes.add(compareType);
		columnNames.addElement(columnName);
	}
	
	public int size(){
		return comparators.size();
	}
	
	public String getColumnName(int position) {
		return (String)columnNames.elementAt(position);
	}

	public int getComparator(int position) {
		return comparators.get(position);
	}

	public int getCompareType(int position) {
		return compareTypes.get(position);
	}

	public KXmlSerializer traverse(KXmlSerializer serializer) {
		try{
			serializer.startTag(null, "Index");
			serializer.attribute(null, "indexNb", ""+ getId());
			for(int i = 0; i< size(); i++){
				serializer.startTag(null, "value");
				serializer.attribute(null, "comparator", "" + comparators.get(i));
				serializer.attribute(null, "compareTypes", "" + compareTypes.get(i));
				if(columnNames.elementAt(i) != null){
					serializer.attribute(null, "columnNames", "" + columnNames.elementAt(i));
				}else{
					serializer.attribute(null, "columnNames", "null");
				}
				serializer.endTag(null, "value");
			}
			serializer.endTag(null, "Index");
			return serializer;
		}catch(Exception err){
			throw new RuntimeException(err.getMessage());
		}
	}
	
}

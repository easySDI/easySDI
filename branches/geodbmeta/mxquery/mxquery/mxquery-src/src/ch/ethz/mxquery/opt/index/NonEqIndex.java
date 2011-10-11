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

import java.util.HashMap;
import java.util.Map;
import java.util.Set;
import java.util.TreeMap;

import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.model.Constants;
import ch.ethz.mxquery.util.IntegerList;
/**
 * supposes that there is only one value and that is Time :(
 * @author icarabus
 *
 */
public class NonEqIndex{
	
	//private IndexSchema schema = null;
	private int currentValue = 0;
	private Map ind = null;
	private static final int granularity = 30;
	private Map current = null;
	private int comparator = 0;
	
	TreeMap startsPos = null;
	private int size = 0;
	
	/**
	 * 
	 * @param schema
	 */
	public NonEqIndex(IndexSchema schema){
		//this.schema = schema;
		this.ind = new HashMap();
		comparator = schema.getComparator(0);
		startsPos = new TreeMap();
	}
	
	// Note: works only with java integers (no java long support)
	public void index(Token[] values, int windowId){	
		
		currentValue = (int)values[0].getLong();
		
		//System.out.println("Index value : "+currentValue);
		
		Integer val = new Integer(currentValue);
		//Integer wind = new Integer(windowId);
		
		if ( current == null ){
			IntegerList li = new IntegerList();
			li.add(windowId);
			current = new TreeMap();
			current.put(val,li);
			ind.put(val,current);		
			startsPos.put(val,values[0]);
			size++;
			return;
		}
		
		if ( currentValue%granularity == 0 ){			
			if ( !current.containsKey(val) ){
				IntegerList li = new IntegerList();
				li.add(windowId);
				current = new TreeMap();
				current.put(val,li);
				ind.put(val,current);	
				//startPos.add(values[0].getIntValue());
				
				startsPos.put(val,values[0]);
				size++;
			}
			else{
				IntegerList cLi = (IntegerList)current.get(val);
				cLi.add(windowId);
			}
		}
		else{
			if (current.containsKey(val)){
				IntegerList cLi = (IntegerList)current.get(val);
				cLi.add(windowId);
			}
			else{
				IntegerList newLi = new IntegerList();
				newLi.add(windowId);
				current.put(val,newLi);
			}
		}
		
	}	
	
	public IntegerList retreive(Token[] values){		
		return retreiveRemove(values,false);
	}
	
	public IntegerList retreiveAndRemove(Token[] values){
		return retreiveRemove(values,true);
	}
	
	// Note: works only with java integers (no java long support)
	private IntegerList retreiveRemove(Token[] values, boolean remove){
		int pos = 0;
		
//		while(true){
			
			int crtInt = getMatchingHM(values,pos);
			if ( crtInt == -1 )
				return null;
			
			Integer crtInteger = new Integer(crtInt);
			
			Map crt = (TreeMap)ind.get(crtInteger);
			
			Integer val = new Integer( (int)values[0].getLong());
			
			if ( crt == null )
				return null;
			
			Set keyset = crt.keySet();
			Object[] intList = keyset.toArray();
			
			if (intList.length == 0 ){
				if (remove){
					ind.remove(crtInteger);
					startsPos.remove(crtInteger);
				}
				pos++;
				return null;
//				continue;
			}
			
			Integer crtVal = (Integer)intList[0];
			
			switch(comparator){
			case Constants.COMP_LT:
				for (int j=0; j<intList.length; j++){
					Integer crtValList = (Integer)intList[j];
					if ( val.compareTo(crtValList) < 0 ){
						crtVal = crtValList;
						break;
					}
				}
				
				if ( val.compareTo(crtVal) >= 0 ){
					pos++;
					return null;
//					continue;
				}
				break;
			case Constants.COMP_GE:
				for (int j=0; j<intList.length; j++){
					Integer crtValList = (Integer)intList[j];
					if ( val.compareTo(crtValList) >= 0 ){
						crtVal = crtValList;
						break;
					}
				}
				
				if ( val.compareTo(crtVal) < 0 ){
					pos++;
					return null;
//					continue;
				}
				break;
			case Constants.COMP_GT:
				for (int j=0; j<intList.length; j++){
					Integer crtValList = (Integer)intList[j];
					if ( val.compareTo(crtValList) > 0 ){
						crtVal = crtValList;
						break;
					}
				}
				
				if ( val.compareTo(crtVal) <= 0 ){
					pos++;
					return null;
//					continue;
				}
				break;
			case Constants.COMP_LE:
				
				for (int j=0; j<intList.length; j++){
					Integer crtValList = (Integer)intList[j];
					if ( val.compareTo(crtValList) <= 0 ){
						crtVal = crtValList;
						break;
					}
				}
				
				if ( val.compareTo(crtVal) > 0 ){
					pos++;
					return null;
//					continue;
				}
				break;			
			}
			
			IntegerList li = (IntegerList)crt.get(crtVal);
			if ( li == null || li.size() == 0 ){
				if (remove){
					crt.remove(crtVal);
					if ( crt.keySet().size() == 0 ){
						ind.remove(crtInteger);
						startsPos.remove(crtInteger);
					}
				}
				pos++;
				return null;
//				continue;
			}
			else{
				if (remove){
					crt.remove(crtVal);
					if ( crt.keySet().size() == 0 ){
						ind.remove(crtInteger);
						startsPos.remove(crtInteger);
					}
					return li;
				}
				else{					
					return li;
				}
			}
//		}
		//return null;	
		
	}
	
	// Note: works only with java integers (no java long support)
	private int getMatchingHM(Token[] values, int pos){
		
		int val = (int)values[0].getLong();		
		int retVal = -1;
		
		Object[] intArray = startsPos.keySet().toArray();
		int crtSize = intArray.length;
		
		if ( pos >= crtSize )
			return -1;
		
		switch(comparator){
		case Constants.COMP_GT:
			for ( int i = pos ; i < crtSize; i++ ){
				int crtV = ((Integer)intArray[i]).intValue();
				if ( crtV < val ){
					retVal = crtV;
					break;
				}
			}
			break;
		case Constants.COMP_GE:
			for ( int i = pos ; i < crtSize; i++ ){
				int crtV = ((Integer)intArray[i]).intValue();				
				if ( crtV <= val ){
					retVal = crtV;
					break;
				}
			}
			break;
		case Constants.COMP_LT:
			for ( int i = crtSize - 1 ; i >=pos ; i-- ){
				int crtV = ((Integer)intArray[i]).intValue();				
				if ( crtV > val ){
					retVal = crtV;
					break;
				}
			}
			break;
		case Constants.COMP_LE:
			for ( int i = crtSize - 1 ; i >=pos ; i-- ){
				int crtV = ((Integer)intArray[i]).intValue();
				if ( crtV >= val ){
					retVal = crtV;
					break;
				}
			}
			break;
		}
		
		//intArray = null;
		
		if (retVal == -1 ){
			return -1;
		}
		
		return retVal;
	}
	
}

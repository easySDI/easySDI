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

import java.util.Hashtable;
//import java.util.Map;
import java.util.Map;
import java.util.Set;
import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.model.Constants;
import ch.ethz.mxquery.util.IntegerList;

public class EqIndex{
	
	private Hashtable ind = null;
	private IndexSchema schema = null;
	private static final int gran = 500;
	private Vector groups = null;
	private Vector valueGroups = null;
	
	public EqIndex(IndexSchema schema){
		this.schema = schema;
		this.ind = new Hashtable();
		this.groups = new Vector();
		this.valueGroups = new Vector();
	}

	// Note: works only with java integers (no java long support)  
	private int getTokensIntValue(Token t) {
		
		int type = Type.getEventTypeSubstituted(t.getEventType(), Context.getDictionary());

		switch (type){
		case Type.INTEGER:
			return (int)t.getLong(); 
		// in case attribute value
		case Type.UNTYPED_ATOMIC:
			return new Integer(t.getText()).intValue();
		default:
			// TODO implement index for strings (used in some test cases e.g. test_SEQ_TimePerPerson_day1) 
			//throw new RuntimeException ("Type " + type + " is not supported in equality index");
			return 0;
		} 
	}
	
	public void index(Token[] values, int windowId) {

		int intVal = getTokensIntValue(values[0]);
		Integer valO = new Integer( intVal );
		
		int index = intVal %gran;
		Integer indexO = new Integer(index);
		IntegerList vals = null;
		boolean add = false;
		
		
		Hashtable currentHM = null;
		
		if (values.length == 1 ){
			
			if (!ind.containsKey(indexO)){
				currentHM = new Hashtable();
				ind.put(indexO,currentHM);
				
				IntegerList intL = new IntegerList();
				if (schema.isGroupByIndex()){
					groups.addElement(intL);
					vals = new IntegerList();
					vals.add( intVal );
					valueGroups.addElement(vals);
				}
				currentHM.put(valO,intL);
			}
			else{
				currentHM = (Hashtable)ind.get(indexO);
				if (!currentHM.containsKey(valO)){
					IntegerList intList = new IntegerList();
					if (schema.isGroupByIndex()){
						groups.addElement(intList);
						vals = new IntegerList();
						vals.add( intVal );
						valueGroups.addElement(vals);
					}
					currentHM.put(valO,intList);
				}
			}
			
			((IntegerList)currentHM.get(valO)).add(windowId);
			
			return;
		}
		
		if (!ind.containsKey(indexO)){
			currentHM = new Hashtable();
			ind.put(indexO,currentHM);
		}
		else{
			currentHM = (Hashtable)ind.get(indexO);
		}
		
		Hashtable current = null;
		
		if (!currentHM.containsKey(valO)){
			current = new Hashtable();
			currentHM.put(valO,current);			
		}
		else
		{
			current = (Hashtable)currentHM.get(valO);
		}
		
		for ( int i=1; i<values.length-1; i++){
			
			Integer crtWin = new Integer( getTokensIntValue( values[i] ) );
			
			if ( current.containsKey(crtWin)){
				current = (Hashtable)current.get(crtWin);
			}
			else
			{
				Hashtable newHM = new Hashtable();
				current.put(crtWin,newHM);				
				current = newHM;
			}
		}
		
		Integer lastEntry = new Integer( getTokensIntValue(values[values.length-1]) );
		
		if (current.containsKey(lastEntry)){
			IntegerList li = (IntegerList)current.get(lastEntry);
			li.add(windowId);
		}
		else{
			IntegerList newLi = new IntegerList();
			if (schema.isGroupByIndex()){
				groups.addElement(newLi);
				
				vals = new IntegerList();
				
				for ( int j=0; j<values.length; j++ ){
					vals.add( getTokensIntValue(values[j]) );
				}
				add = true;
			}
			newLi.add(windowId);
			current.put(lastEntry,newLi);
		}
		
		if (add)
			valueGroups.addElement(vals);
	}
	
	public IntegerList retreive(Token[] values){
		return retreiveRemove(values,false);
	}
	
	public IntegerList retreiveAndRemove(Token[] values){
		
		return retreiveRemove(values,true);
	}
	
	private IntegerList retreiveRemove(Token[] values, boolean remove){
		
		int inde = getTokensIntValue(values[0])%gran;
		
		Hashtable indHash = (Hashtable)ind.get(new Integer(inde));
		Integer valO = new Integer(  getTokensIntValue(values[0]) );
		
		if ( indHash == null )
			return null;
		
		if (values.length == 1){
			switch (schema.getComparator(0)){
			case Constants.COMP_EQ :
					IntegerList val = (IntegerList)indHash.get(valO);
					if ( remove )
						indHash.remove(valO);
					return val;
			case Constants.COMP_NE :	
				Set entrySet = indHash.entrySet();
				java.util.Iterator iter = entrySet.iterator();
				int v = valO.intValue();
				while (iter.hasNext()){
					Map.Entry entry = (Map.Entry)iter.next();
					Integer key = (Integer)entry.getKey();
					if ( key.intValue() != v ){
						IntegerList wind = (IntegerList)entry.getValue();
						if (remove)
							indHash.remove(key);
						return wind;	
					}
				}
				break;
			}
			return null;
		}
		
		Hashtable current = indHash;
		
		for ( int i=0; i<values.length; i++){
			switch (schema.getComparator(i)){
			case Constants.COMP_EQ :
				
				Integer v = new Integer(  getTokensIntValue(values[i]));
				if ( i!=values.length - 1 ){					
					current = (Hashtable)current.get(v);					
				}
				else{
					if ( current.containsKey(v) ){
						IntegerList ret = null;
						ret = (IntegerList)current.get(v);
						if (remove)
							current.remove(v);
						if ( current.size() == 0 )
							ind.remove(current);						
						return ret;
					}
					else
						return null;
				}
				break;
			case Constants.COMP_NE :		
				Integer v1 = new Integer( getTokensIntValue(values[i]));
				if ( i!=values.length - 1 ){
					Set keyset = current.keySet();
					
					Object[] intList = keyset.toArray();
					
					for (int j=0; j<intList.length; j++){
						if (((Integer)intList[j]).compareTo(v1) != 0){
							current = (Hashtable)current.get(intList[j]);
							break;
						}
					}
				}
				else{
					if ( current == null )
						return null;
					Set keyset = current.keySet();
					Object[] intList = keyset.toArray();
					
					for (int j=0; j<intList.length; j++){
						if (((Integer)intList[j]).compareTo(v1) != 0){
							
							IntegerList retVal = null;
							retVal = (IntegerList)current.get(intList[j]);
							
							if (remove)
								current.remove(intList[j]);
							if ( current.size() == 0 )
								ind.remove(current);
							
							return retVal;
						}
					}
					intList = null;
					return null;
				}
				break;
			}
		}
		return null;
	}
	
	public Vector getGroups(){
		return groups;
	}
	
	public Vector getValues(){
		return valueGroups;
	}
}

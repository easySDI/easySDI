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

import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.util.IntegerList;

public class SimpleEqIndex{
	
	private Map ind = null;
	
	public SimpleEqIndex(){
		ind = new HashMap();
	}
	
	// Note: works only with java integers (no java long support)
	public void index(Token[] values, int index){
	
		if ( values.length == 0 )
			return;
		
		Integer valO = new Integer( (int)values[0].getLong());
		Map current = null;
		Integer pos = new Integer(index);

		if (values.length == 1 ){
			
			if (!ind.containsKey(valO)){
				IntegerList il = new IntegerList();
				il.add(pos.intValue());
				ind.put(valO,il);
			}
			else
			{
				((IntegerList)ind.get(valO)).add(pos.intValue());
			}
			return;
		}
		
		if (!ind.containsKey(valO)){
			current = new HashMap();
			ind.put(valO,current);
		}
		else
		{
			current = (HashMap)ind.get(valO);
		}
		
		for ( int i=1; i<values.length-1; i++){
			
			Integer crtWin = new Integer( (int)values[i].getLong());
			
			if ( current.containsKey(crtWin)){
				current = (HashMap)current.get(crtWin);
			}
			else
			{
				HashMap newHM = new HashMap();
				current.put(crtWin,newHM);
				current = newHM;
			}
		}
		
		Integer lastEntry = new Integer((int)values[values.length-1].getLong());
		
		if (current.containsKey(lastEntry)){
			IntegerList li = (IntegerList)current.get(lastEntry);
			li.add(pos.intValue());
		}
		else{
			IntegerList newLi = new IntegerList();
			newLi.add(pos.intValue());
			current.put(lastEntry,newLi);
		}
	}
	
	// Note: works only with java integers (no java long support)
	public IntegerList retreive(Token[] values){
		
		Map indHash = ind;
		Integer valO = new Integer((int)values[0].getLong());
		
		if ( indHash == null )
			return null;
		
		if (values.length == 1){
			IntegerList val = (IntegerList)indHash.get(valO);
			return val;
		}
		
		Map current = indHash;
		
		for ( int i=0; i<values.length; i++){
				Integer v = new Integer((int)values[i].getLong());
				if ( i!=values.length - 1 ){
					if ( !current.containsKey(v)){
						return null;
					}
					current = (HashMap)current.get(v);
				}
				else{
					if ( current.containsKey(v) ){
						return (IntegerList)current.get(v);
					}
					else
						return null;
				}
		}
		return null;
	}
	
	public void clear(){
		ind = null;
		ind = new HashMap();
	}
}

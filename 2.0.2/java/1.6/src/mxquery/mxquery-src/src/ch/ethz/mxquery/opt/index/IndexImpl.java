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
import java.util.TreeMap;
import java.util.Vector;

import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.StaticException;
import ch.ethz.mxquery.model.Constants;
import ch.ethz.mxquery.util.IntegerList;

public class IndexImpl implements Index, Cloneable{
	
	private Map indexes = null;
	TreeMap startsPos = null;
	private int windows = 0;
	
	public IndexImpl(){
		this.indexes = new HashMap();
		startsPos = new TreeMap();
	}
	
	public boolean registerIndex(IndexSchema index){		
		if (isEqIndex(index)){
			//System.out.println("Register EQ index");
			indexes.put(index,new EqIndex(index));
			return true;
		}
		else{
			//System.out.println("Register NEQ index");
			indexes.put(index,new NonEqIndex(index));
			return true;
		}		
	}
	
	private boolean isEqIndex(IndexSchema index){
		for ( int i=0; i<index.size(); i++ ){
			if ( index.getComparator(i)!= Constants.COMP_EQ && index.getComparator(i)!= Constants.COMP_NE )
				return false;
		}		
		return true;
	}
	
	public void index(int value){
		Integer window = new Integer(value);
		if (!startsPos.containsKey(window)){
			startsPos.put(window,window);
			windows++;		
		}
	}
	
	public void remove(){		
		if ( windows == 0 )
			return;
		
		startsPos.remove(startsPos.firstKey());
		windows--;
	}
	
	public int getAndRemove(){		
		if ( windows == 0 )
			return -1;		
		Integer fWin = (Integer)startsPos.remove(startsPos.firstKey());
		if ( fWin == null )
			return -1;		
		windows--;
		return fWin.intValue();
	}
	
	
	public int get(){		
		if ( windows == 0 )
			return -1;		
		Integer fWin = (Integer)startsPos.firstKey();
		if ( fWin == null )
			return -1;		
		return fWin.intValue();
	}
	
	public void remove(int value){				
		if ( value == -1 )
			return;		
		if ( windows == 0 )
			return;		
		startsPos.remove(new Integer(value));
		windows--;		
	}
	
	public void index(IndexSchema index, Token[][] indexValues, int value){}
	
	public void index(IndexSchema index, Token[] values, int windowId){
		
		if ( values.length == 0 )
			return;
		
		if ( indexes.get(index) instanceof EqIndex)
			eqIndex(index, values, windowId);
		else
			neqIndex(index, values, windowId);
		
		index(windowId);
	
	}
	
	public void eqIndex(IndexSchema index, Token[] values, int windowId){
		EqIndex ind = (EqIndex)indexes.get(index);
		if ( ind == null )
			return;
		ind.index(values,windowId);
	}
	
	public void neqIndex(IndexSchema index, Token[] values, int windowId){
		NonEqIndex ind = (NonEqIndex)indexes.get(index);
		if ( ind == null )
			return;
		ind.index(values,windowId);
	}
	
	public IntegerList eqIndexRetrieve(IndexSchema index, Token[] values){
		EqIndex ind = (EqIndex)indexes.get(index);
		if ( ind == null )
			return null;
		
		return ind.retreive(values);
	}
	
	public IntegerList neqIndexRetrieve(IndexSchema index, Token[] values){
		NonEqIndex ind = (NonEqIndex)indexes.get(index);
		if ( ind == null )
			return null;
		
		return ind.retreive(values);
	}
	
	public IntegerList retreive(IndexSchema index, Token[] values){
		
		if ( values.length == 0 )
			return null;
		
		if ( indexes.get(index) instanceof EqIndex)
			return eqIndexRetrieve(index, values);
		else
			return neqIndexRetrieve(index, values);
				
	}
	
	public IntegerList eqIndexRetreiveAndRemove(IndexSchema index, Token[] values){
		EqIndex ind = (EqIndex)indexes.get(index);
		if ( ind == null )
			return null;
		
		return ind.retreiveAndRemove(values);
	}
	
	public IntegerList neqIndexRetreiveAndRemove(IndexSchema index, Token[] values){
		NonEqIndex ind = (NonEqIndex)indexes.get(index);
		if ( ind == null )
			return null;
		
		return ind.retreiveAndRemove(values);
	}
		
	public IntegerList retreiveAndRemove(IndexSchema index, Token[][] indexValues) throws MXQueryException {		
		throw new StaticException(ErrorCodes.A0002_EC_NOT_SUPPORTED, "Not supported yet", null);
	}
	
	public IntegerList retreiveAndRemove(IndexSchema index, Token[] values){
		IntegerList winId = null;
		
		if ( values.length == 0 )
			return null;
		
		if ( indexes.get(index) instanceof EqIndex)
			winId = eqIndexRetreiveAndRemove(index, values);
		else
			winId = neqIndexRetreiveAndRemove(index, values);
		
		if ( winId == null )
			return null;
		
		IntegerList tmpWin = new IntegerList();
		
		for ( int j=0; j<winId.size(); j++){
			tmpWin.add(winId.get(j));
		}
		
		for ( int i=0;i<winId.size(); i++){
			if (!startsPos.containsKey(new Integer(winId.get(i))))
				tmpWin.removeValue(winId.get(i));
			else
				remove(winId.get(i));
		}
		return tmpWin;
	}
	
	public int size(){
		return windows;
	}
	
	public IntegerList retreive(IndexSchema index, Token[][] indexValues) throws MXQueryException {
		throw new StaticException(ErrorCodes.A0002_EC_NOT_SUPPORTED, "Not supported yet", null);
	}

	public int get(int i) {
		int[] array = getAll();
		return array[i];
	}

	public void compileIndex() {
		// TODO Auto-generated method stub
		
	}

	public int[] getAll() {		
		Object[] arrayList = startsPos.keySet().toArray();
		int[] array = new int[arrayList.length];
		
		for (int i=0; i<arrayList.length; i++){
			array[i] = ((Integer)arrayList[i]).intValue();
		}
		
		return array;
	}
	
	public int getAndRemove(int i){
		windows--;
		
		int[] array = getAll();
		startsPos.remove(new Integer(array[i]));
		
		int retVal = array[i];
		//array = null;
		
		return retVal;
	}
	
	public int[] getAndRemoveAll(){		
		int[] array = getAll();
		startsPos.clear();
		windows = 0;
		return array;
	}
	
	public void clear(){
		startsPos.clear();
		windows = 0;
	}
	
	public Vector getGroups(IndexSchema schema){
		EqIndex ind = (EqIndex)indexes.get(schema);
		if ( ind == null )
			return null;
		return ind.getGroups();
	}
	
	public Vector getValues(IndexSchema schema){
		EqIndex ind = (EqIndex)indexes.get(schema);
		if ( ind == null )
			return null;
		return ind.getValues();
	}

	protected Object clone() throws CloneNotSupportedException {
		IndexImpl ret = (IndexImpl)super.clone();
		return ret;
	}
}

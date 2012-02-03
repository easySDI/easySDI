package ch.ethz.mxquery.opt.index;
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

//package ch.ethz.mxquery.util.index;
//
//import ch.ethz.mxquery.util.IntegerList;
//import ch.ethz.mxquery.util.tokens.Token;
//
//public class DummyIndex implements Index {
//	
//	IntegerList list = new IntegerList();
//
//	public boolean registerIndex(IndexSchema index) {
//		// TODO Auto-generated method stub
//		return false;
//	}
//
//	public void compileIndex() {
//		// TODO Auto-generated method stub
//
//	}
//
//	public void index(int value) {
//		list.add(value);
//
//	}
//
//	public int retreive(int indexNb, Token[] indexValues) {
//		// TODO Auto-generated method stub
//		return 0;
//	}
//
//	public int retreiveAndRemove(int indexNb, Token[] indexValues) {
//		// TODO Auto-generated method stub
//		return 0;
//	}
//
//	public int retreive(int indexNb, Token[][] indexValues) {
//		// TODO Auto-generated method stub
//		return 0;
//	}
//
//	public int retreiveAndRemove(int indexNb, Token[][] indexValues) {
//		// TODO Auto-generated method stub
//		return 0;
//	}
//
//	public int[] getAll() {
//		return list.toArray();
//	}
//	
//	public int[] getAndRemoveAll(){
//		int[] array = list.toArray();
//		list.clear();
//		return array;
//	}
//
//	public int get() {
//		return list.get(0);
//	}
//
//	public int get(int i) {
//		return list.get(i);
//	}
//	
//	public int getAndRemove(int i){
//		return list.remove(i);
//	}
//
//	public void remove(int value) {
//		for(int i = 0;i<list.size();i++){
//			if(list.get(i) == value){
//				list.remove(i);
//			}
//		}
//
//	}
//
//	public int getAndRemove() {
//		return list.remove(0);
//	}
//
//	public int size() {
//		return list.size();
//	}
//
//	public void clear() {
//		list.clear();
//		
//	}
//
//	public void index(IndexSchema index, Token[] indexValues, int value) {
//		// TODO Auto-generated method stub
//		
//	}
//
//	public void index(IndexSchema index, Token[][] indexValues, int value) {
//		// TODO Auto-generated method stub
//		
//	}
//
//	public int retreive(IndexSchema index, Token[] indexValues) {
//		// TODO Auto-generated method stub
//		return 0;
//	}
//
//	public int retreive(IndexSchema index, Token[][] indexValues) {
//		// TODO Auto-generated method stub
//		return 0;
//	}
//
//	public int retreiveAndRemove(IndexSchema index, Token[] indexValues) {
//		// TODO Auto-generated method stub
//		return 0;
//	}
//
//	public int retreiveAndRemove(IndexSchema index, Token[][] indexValues) {
//		// TODO Auto-generated method stub
//		return 0;
//	}
//
//}

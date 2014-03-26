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

package ch.ethz.mxquery.xqj;

import java.util.Vector;

import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.model.XDMIterator;

public class ItemAccessor {
	
	private XDMIterator iter;
	//private Iterator retIter;
	
//	private boolean startTagStarted = false;
//	private boolean endTagFound = false;
	
	public ItemAccessor(XDMIterator iter){
		this.iter = iter;
	}

//	public boolean hasNext() {
//		// TODO Auto-generated method stub
//		return false;
//	}

	public Object next() throws MXQueryException {
		int depth = 0;
		int curDepth;
		boolean flag = true;
		
//		startTagStarted = false;
//		endTagFound = false;
		
		Vector v = new Vector();
		
		//StringBuffer myBuffer = new StringBuffer(); 
		
		//this.retIter = this.iter.
		
		Token tok = iter.next(); 
		curDepth = depth;
		
		if(Type.isAtomicType(tok.getEventType(), null)){
			return new FlatItem(tok);
		}
		
		if(tok.getEventType() != Type.END_SEQUENCE){
		
			while(tok.getEventType() !=Type.END_SEQUENCE && flag){
						
				if (Type.isStartType(tok.getEventType())){
					depth++;
				} else if (Type.isEndType(tok.getEventType())){
					depth--;
				}
				
			
				v.add(tok);
				
				if(depth != curDepth){
					tok = iter.next();
				} else {
					flag = false;
				}
			}
			return new TreeItem(v);
			
		}
		
		return null;
		
	}
	
	public XDMIterator getIterator(){
		return iter;
	}

	public void remove() {
		// TODO Auto-generated method stub

	}

}

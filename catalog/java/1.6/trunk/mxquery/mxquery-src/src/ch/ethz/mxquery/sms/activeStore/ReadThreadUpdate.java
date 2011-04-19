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

package ch.ethz.mxquery.sms.activeStore;

import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.sms.MMimpl.IndexFIFOStore;

/**
 * Thread for reading data from the input stream for materialization in the store
 * @author Irina Botan
 *
 */
public final class ReadThreadUpdate extends Thread{
	
	private XDMIterator iter = null;
	
	private IndexFIFOStore store = null;
	
	private static final int changeBuffer = -1;
		
	public ReadThreadUpdate(XDMIterator it, IndexFIFOStore store){
		this.iter = it;
		this.store = store;
	}
	
	public void run(){
		
		while(true){
			try{
				//System.out.println(Thread.currentThread().getName()+" Try to get data");
				Token tok = iter.next();
				
				if ( tok == Token.START_SEQUENCE_TOKEN ){
					continue;
				}
				
				if ( tok == Token.END_SEQUENCE_TOKEN ){
					System.out.println("END SEQUENCE FOR "+Thread.currentThread().getName());
					store.putElement(tok,changeBuffer);
					System.out.println("END SEQUENCE FOR "+Thread.currentThread().getName());
					return;
				}
				
				store.putElement(tok,tok.getEventType());
			}
			catch (MXQueryException e){
				e.printStackTrace();
				return;
			}
		}
	}
}

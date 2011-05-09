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

package ch.ethz.mxquery.sms.MMimpl;

public class GarbageCollector extends Thread{

	private FIFOStore buffer = null;
	
	public GarbageCollector(){
	}
	
	public void init(FIFOStore buffer){
		this.buffer = buffer;
	}
	

	public void run(){
		while (true){
			try {

				Thread.sleep(1000);
				buffer.freeBuffers();
				
				
			} catch (Exception e) {
				e.printStackTrace();
			}
		}
	}
}

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
package ch.ethz.mxquery.util;

import java.util.Vector;

/**
 * Implementation of a Stack based on a Vector.
 * 
 * @author dagraf
 *
 */
public class Stack {

	private Vector depths = new Vector();

	public void push(int depth) {
		this.depths.addElement(new Integer(depth));
	}
	
	public int pop() {
		Integer val = (Integer)depths.elementAt(depths.size()-1);
		depths.removeElementAt(depths.size()-1);
		return val.intValue();
	}
	
	public int peek() {
		Integer val = (Integer)depths.elementAt(depths.size()-1);
		return val.intValue();
	}
	
}
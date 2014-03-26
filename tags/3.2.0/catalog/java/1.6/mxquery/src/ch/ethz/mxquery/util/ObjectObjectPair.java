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

/**
 * Helper class: Pair of two objects
 * @author jimhof
 */
public class ObjectObjectPair {

	private Object first;
	private Object second;
	
	public ObjectObjectPair(Object first, Object second) {
		this.first = first;
		this.second = second;
	}
	
	public Object getFirst(){
		return this.first;
	}
	
	public Object getSecond(){
		return this.second;
	}
}

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

package ch.ethz.mxquery.datamodel;




/**
 * Interface for the XDM node iden
 * 
 * @author David Alexander Graf
 * 
 */
public interface Identifier {
	/**
	 * Compares this token identifier with an other one.
	 * 
	 * @param identifier
	 * @return 0 if equal, -1 if <code>identifier</code> is smaller, 1 if
	 *         <code>identifier</code> is bigger, and -2 if the two Identifier
	 *         do not belong to the same data source.
	 */
	public int compare(Identifier identifier);

	/**
	 * Returns the DataSource to which the Identifier belongs to.
	 * 
	 * @return the Source/Store for this identifier
	 */
	public Source getStore();
}

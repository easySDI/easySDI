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

package ch.ethz.mxquery.update.store.llImpl;

import ch.ethz.mxquery.datamodel.xdm.Token;

/**
 * Saves information to rollback a replace of a value.
 * 
 * @author David Alexander Graf
 * 
 */
public class RereplaceValue implements RollbackItem {
	private LLToken target;
	private Token oldValue;

	/**
	 * Constructor
	 * 
	 * @param target
	 * @param oldValue
	 */
	public RereplaceValue(LLToken target, Token oldValue) {
		this.target = target;
		this.oldValue = oldValue;
	}

	public void apply() {
		this.target.setToken(oldValue);
	}
}

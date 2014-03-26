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

import ch.ethz.mxquery.exceptions.MXQueryException;

/**
 * Saves informations to rollback a replace (replace node and replace node
 * content).
 * 
 * @author David Alexander Graf
 * 
 */
public class Rereplace implements RollbackItem {
	private Reinsert redelete;
	private Redelete reinsert;

	/**
	 * Constructor (replace == delete + insert => rereplace == redelete +
	 * reinsert)
	 * 
	 * @param redelete
	 * @param reinsert
	 */
	public Rereplace(Reinsert redelete, Redelete reinsert) {
		this.redelete = redelete;
		this.reinsert = reinsert;
	}

	public void apply() throws MXQueryException {
		this.redelete.apply();
		this.reinsert.apply();
	}
}

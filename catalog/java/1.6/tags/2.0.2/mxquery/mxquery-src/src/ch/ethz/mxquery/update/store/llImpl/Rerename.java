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

import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.exceptions.MXQueryException;

/**
 * Saves informations to rollback a rename.
 * 
 * @author David Alexander Graf
 * 
 */
public class Rerename implements RollbackItem {
	private LLToken target;
	private LLToken targetEnd = null;
	private QName oldName;

	/**
	 * Constructor
	 * 
	 * @param target
	 * @param oldName
	 */
	public Rerename(LLToken target, QName oldName) {
		this.target = target;
		this.oldName = oldName;
	}

	/**
	 * Constructor for a rename of an Event that have an end Event (e.g. start
	 * tag).
	 * 
	 * @param target
	 * @param targetEnd
	 * @param oldName
	 */
	public Rerename(LLToken target, LLToken targetEnd, QName oldName) {
		this(target, oldName);
		this.targetEnd = targetEnd;
	}

	public void apply() {
		try {
			this.target.setName(this.oldName);
		if (this.targetEnd != null) {
			this.targetEnd.setName(this.oldName);
		}
		} catch (MXQueryException e) {
			throw new RuntimeException("Illegal QName used in ReRename");
		}

	}
}

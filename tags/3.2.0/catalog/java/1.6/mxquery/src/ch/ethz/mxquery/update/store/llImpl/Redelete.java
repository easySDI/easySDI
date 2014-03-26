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


/**
 * Saves informations to rollback a deletion.
 * 
 * @author David Alexander Graf
 * 
 */
public class Redelete implements RollbackItem {
	private LLToken start, prev, end, next;
	private boolean dummyRollback = false;

	/**
	 * Dummy Constructor for deletions that delete nothing.
	 * 
	 */
	public Redelete() {
		this.dummyRollback = true;
	}

	/**
	 * Constructor
	 * 
	 * @param start
	 *            start of deleted sequence
	 * @param prev
	 *            SimpleToken before <code>start</code>
	 * @param end
	 *            end of deleted sequence
	 * @param next
	 *            SimpleToken after <code>end</code>
	 */
	public Redelete(LLToken start, LLToken prev, LLToken end,
			LLToken next) {
		this.start = start;
		this.prev = prev;
		this.end = end;
		this.next = next;
	}

	public void apply() {
		if (!this.dummyRollback) {
			this.prev.setNext(this.start);
			this.start.setPrev(this.prev);
			this.end.setNext(this.next);
			this.next.setPrev(this.end);
		}
	}
}

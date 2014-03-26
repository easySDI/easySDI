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

import ch.ethz.mxquery.datamodel.Identifier;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;

/**
 * Represents a reference from the corresponding SimpleSource to an element of
 * another SimpleSource. <br />
 * Important: A reference token must not point to another reference token!!
 * 
 * @author David Alexander Graf
 * 
 */
public class LLRefToken extends LLToken {
	private int depth;
	private LLToken reference;

	/**
	 * SimpleToken on which this reference points.
	 * 
	 * @param reference
	 */
	public LLRefToken(LLToken reference) throws MXQueryException {
		if (reference instanceof LLRefToken) {
			throw new DynamicException(ErrorCodes.A0010_EC_LLSTORE_EXCEPTION,
					"A reference token in the Linked List"
							+ " must not point to another reference token!", null);
		}
		this.reference = reference;
	}

	/**
	 * Returns the SimpleToken on which this reference points.
	 * 
	 * @return reference (SimpleToken)
	 */
	public LLToken getRef() {
		return this.reference;
	}

	/**
	 * Returns the difference between the depth of the referenced token and
	 * <code>this</code>. Useful for printing the store as formated xml.
	 * 
	 * @return
	 */
	int getDepthDiff() {
		return this.depth - this.reference.getDepth();
	}

	/**
	 * A reference is defined by the start of an event (e.g. a reference to a
	 * start-tag-event). This method returns the corresponding end event (e.g. a
	 * reference to a end-tag-event) or the start itself if it is no start
	 * element (e.g. attribute).
	 * 
	 * @return
	 */
	LLToken getEndForIter() throws MXQueryException {
		LLToken end = this.reference.getEndEl();
		if (end == null) {
			return this.reference;
		} else {
			return end;
		}
	}

	public Identifier getId() {
		throw new RuntimeException(
				"Get Identifier on Reference! Should not be possible!");
	}

	public void setDepth(int depth) {
		this.depth = depth;
	}

	public void setNext(LLToken next) {
		this.next = next;
	}

	public void setPrev(LLToken prev) {
		this.prev = prev;
	}

	public int getEventType() {
		return Type.ANY_TYPE;
	}
	
	public LLToken copy() throws MXQueryException {
		return new LLRefToken(reference.copy());
	}
}

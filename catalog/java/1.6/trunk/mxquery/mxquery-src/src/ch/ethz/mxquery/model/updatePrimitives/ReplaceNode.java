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

package ch.ethz.mxquery.model.updatePrimitives;

import ch.ethz.mxquery.datamodel.Identifier;
import ch.ethz.mxquery.datamodel.Source;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;

public class ReplaceNode implements UpdatePrimitive {
	private Identifier targetId;
	Identifier parent; /* not part of spec, but makes work easier */
	private UpdateableStore store;

	public ReplaceNode(Identifier targetId, Identifier parent, UpdateableStore sourceStore) {
		this.targetId = targetId;
		this.store = sourceStore;
		this.parent = parent;
	}

	public void applyUpdate() throws MXQueryException {
		Source src = targetId.getStore();
		if (src instanceof UpdateableStore) {
			UpdateableStore ds = (UpdateableStore)this.targetId.getStore();
			ds.replaceNode(this.targetId, this.store);
			ds.setModified(true);
		}
		else 
			throw new DynamicException(ErrorCodes.A0009_EC_EVALUATION_NOT_POSSIBLE,"Update performed on a non-updateable store",QueryLocation.OUTSIDE_QUERY_LOC);										
	}

	public Identifier getTargetId() {
		return this.targetId;
	}

	public int getType() {
		return UpdatePrimitive.REPLACE_NODE;
	}
	public UpdateableStore getStore() {
		return store;
	}
}

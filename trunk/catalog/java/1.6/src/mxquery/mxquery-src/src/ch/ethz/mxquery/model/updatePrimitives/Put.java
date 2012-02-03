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
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.model.XDMIterator;

public class Put implements UpdatePrimitive {

	private Identifier targetId;
	private XDMIterator nodeSrc;
	private String uri;
	
	public Put (Identifier targetId, XDMIterator src, String uri) {
		this.targetId = targetId;
		this.uri = uri;
		this.nodeSrc = src;
	}
	
	public int getType() {
		return UpdatePrimitive.PUT;
	}
	
	public Identifier getTargetId() {
		return targetId;
	}
	
	public void applyUpdate() throws MXQueryException {
		UpdateableStore putStore = null;
		if (targetId.getStore() instanceof UpdateableStore)
			putStore = ((UpdateableStore)targetId.getStore()).getStoreSet().getNewStoreForItem(targetId, uri, true);
		else			
			putStore = nodeSrc.getContext().getStores().createUpdateableStore(uri,nodeSrc,true, true);
		putStore.setModified(true);

	}
	
	public String getURI() {
		return uri;
	}
	
	public UpdateableStore getStore() {
		return null;
	}

	
}

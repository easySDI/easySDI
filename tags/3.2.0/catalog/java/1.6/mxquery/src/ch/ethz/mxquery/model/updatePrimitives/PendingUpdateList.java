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

import java.util.Enumeration;
import java.util.Hashtable;
import java.util.Vector;

import ch.ethz.mxquery.datamodel.Identifier;
import ch.ethz.mxquery.datamodel.Namespace;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.Source;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.xdm.NamedToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.util.LinkedList;
import ch.ethz.mxquery.util.Set;
import ch.ethz.mxquery.xdmio.StoreSet;

public class PendingUpdateList {
	private LinkedList insertIntos = new LinkedList();
	private LinkedList insertAttributes = new LinkedList();
	private LinkedList replaceValues = new LinkedList();
	private LinkedList renames = new LinkedList();
	private LinkedList deletes = new LinkedList();
	private LinkedList insertBefores = new LinkedList();
	private LinkedList insertAfters = new LinkedList();
	private LinkedList insertIntoAsFirst = new LinkedList();
	private LinkedList insertIntoAsLast = new LinkedList();
	private LinkedList replaceNodes = new LinkedList();
	private LinkedList replaceNodeContents = new LinkedList();
	private LinkedList put = new LinkedList();
	// This could be improved by given the PUL entries a location
	private QueryLocation loc = QueryLocation.OUTSIDE_QUERY_LOC;
	


	public PendingUpdateList(QueryLocation loc) {
		//this.storeSet = storeSet;
	}

	private boolean hasTwiceIds(LinkedList updates) {
		Set set = new Set();
		Enumeration it = updates.elements();
		while (it.hasMoreElements()) {
			UpdatePrimitive up = (UpdatePrimitive) it.nextElement();
			if (set.contains(up.getTargetId())) {
				return true;
			}
			set.add(up.getTargetId());
		}
		return false;
	}

	private Set apply(LinkedList updates) throws MXQueryException {
		Enumeration it = updates.elements();
		Set stores = new Set();
		Set storeSets = new Set();
		while (it.hasMoreElements()) {
			UpdatePrimitive up = (UpdatePrimitive) it.nextElement();
			up.applyUpdate();
			if (up.getTargetId().getStore()!=null && up.getTargetId().getStore() instanceof UpdateableStore)
				stores.add(up.getTargetId().getStore());
		}
		updates.clear();
		if (stores.size()!=0) {
			Enumeration storeEnum = stores.elements();
			while (storeEnum.hasMoreElements()) {
				UpdateableStore str = (UpdateableStore)storeEnum.nextElement();
				str.setModified(true);
				storeSets.add(str.getStoreSet());
			}
		}
		return storeSets;
	}

	public void add(UpdatePrimitive up) throws MXQueryException {
		switch (up.getType()) {
		case UpdatePrimitive.INSERT_INTO:
			this.insertIntos.add(up);
			break;
		case UpdatePrimitive.INSERT_ATTRIBUTES: {
			this.insertAttributes.add(up);
			Hashtable opOnSameNode = new Hashtable(); 
			groupByTarget(this.insertAttributes, opOnSameNode);
			checkNSBindingConflictsAttr(opOnSameNode);
		}
			break;
		case UpdatePrimitive.REPLACE_VALUE:
			this.replaceValues.add(up);
			break;
		case UpdatePrimitive.RENAME:
			this.renames.add(up);
			break;
		case UpdatePrimitive.DELETE:
			this.deletes.add(up);
			break;
		case UpdatePrimitive.INSERT_BEFORE:
			this.insertBefores.add(up);
			break;
		case UpdatePrimitive.INSERT_AFTER:
			this.insertAfters.add(up);
			break;
		case UpdatePrimitive.INSERT_INTO_AS_FIRST:
			this.insertIntoAsFirst.add(up);
			break;
		case UpdatePrimitive.INSERT_INTO_AS_LAST:
			this.insertIntoAsLast.add(up);
			break;
		case UpdatePrimitive.REPLACE_NODE: {
			this.replaceNodes.add(up);
			Hashtable opOnSameNode = new Hashtable(); 
			groupByTarget(this.replaceNodes, opOnSameNode);
			checkNSBindingConflictsAttr(opOnSameNode);
		}
			break;
		case UpdatePrimitive.REPLACE_NODE_CONTENT:
			this.replaceNodeContents.add(up);
			break;
		case UpdatePrimitive.PUT:
			this.put.add(up);
			break;
		default:
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,
					"Not supported update type used!", null);
		}
	}

	/**
	 * Applies all updates. Important: Because an update should not be executed
	 * twice, the list is cleared afterwards. 
	 */
	public void apply() throws MXQueryException {
		if (this.hasTwiceIds(this.replaceNodes)) {
			throw new DynamicException(ErrorCodes.U0016_UPDATE_DYNAMIC_REPLACE_MULTIPLE_ON_SAME_NODE,
					"Conflicting 'Replace Node' operations on same node detected", loc);
		}
		if (this.hasTwiceIds(this.replaceValues)) {
			throw new DynamicException(ErrorCodes.U0017_UPDATE_DYNAMIC_REPLACE_VALUE_MULTIPLE_ON_SAME_NODE,
			"Conflicting 'Replace Node Value' operations on same node detected", loc);
		}
		if (this.hasTwiceIds(this.replaceNodeContents)) {
			throw new DynamicException(ErrorCodes.U0017_UPDATE_DYNAMIC_REPLACE_VALUE_MULTIPLE_ON_SAME_NODE,
			"Conflicting 'Replace Node Content' operations on same node detected", loc);
		}
		if (this.hasTwiceIds(this.renames)) {
			throw new DynamicException(ErrorCodes.U0015_UPDATE_DYNAMIC_RENAME_MULTIPLE_ON_SAME_NODE, 
					"Conflicting 'Rename' operations on same node detected", loc);
		}

		if (hasConflictingURIs(this.put))
			throw new DynamicException(ErrorCodes.U0031_UPDATE_DYNAMIC_MULTIPLE_PUT_SAME_URI,"Multiple put() with same URI",loc);
		
		Set [] storeSets = new Set[12];
		
		storeSets[0] = this.apply(this.insertIntos);
		storeSets[1] = this.apply(this.insertAttributes);
		storeSets[2] = this.apply(this.replaceValues);
		storeSets[3] = this.apply(this.renames);
		/*
		 * theoretically, here should be the marking of the nodes which should
		 * be deleted. But with our technique (delete nodes with a certain ids)
		 * it works by deleting them directly at the end.
		 */
		storeSets[4] = this.apply(this.insertBefores);
		storeSets[5] = this.apply(this.insertAfters);
		storeSets[6] = this.apply(this.insertIntoAsFirst);
		storeSets[7] = this.apply(this.insertIntoAsLast);
		storeSets[8] = this.apply(this.replaceNodes);
		storeSets[9] = this.apply(this.replaceNodeContents);
		storeSets[10] = this.apply(this.deletes);
		
		// merge text nodes and check XDM constraints/namespace on all stores/nodes that have been modified
		for (int i=0;i<storeSets.length-1;i++) {
			if (storeSets[i] != null) {
				Enumeration storeEnum = storeSets[i].elements();
				while (storeEnum.hasMoreElements()) {
					StoreSet sSet = (StoreSet)storeEnum.nextElement();
					UpdateableStore [] curStores = sSet.getUpdateableStores();
					for (int j=0;j<curStores.length;j++) {
						if (curStores[j].isModified()) {
							curStores[j].mergeCleanTextNodes();
							curStores[j].checkNSXDM();
						}
					}
				}
			}
		}
		
		
		storeSets[11] = this.apply(this.put);
		
		for (int i=0;i<storeSets.length;i++) {
			if (storeSets[i] != null) {
				Enumeration storeEnum = storeSets[i].elements();
				while (storeEnum.hasMoreElements()) {
					StoreSet sSet = (StoreSet)storeEnum.nextElement();
					sSet.cleanTransationStores(Thread.currentThread().toString().hashCode());
				}
			}
		}
	}

	/**
	 * Removes all update primitives from this pending update list.
	 * 
	 */
	public void clear() {
		this.deletes.clear();
		this.insertAfters.clear();
		this.insertAttributes.clear();
		this.insertBefores.clear();
		this.insertIntoAsFirst.clear();
		this.insertIntoAsLast.clear();
		this.insertIntos.clear();
		this.renames.clear();
		this.replaceNodeContents.clear();
		this.replaceNodes.clear();
		this.replaceValues.clear();
		this.put.clear();
	}

	/**
	 * Merges the a pending updating list with this list
	 * 
	 * @param pul
	 *            pending update list that has to be merge to <code>this</code>.
	 */
	public void merge(PendingUpdateList pul) throws MXQueryException {
		this.deletes.addAll(pul.deletes);
		this.insertAfters.addAll(pul.insertAfters);
		this.insertAttributes.addAll(pul.insertAttributes);
		this.insertBefores.addAll(pul.insertBefores);
		this.insertIntoAsFirst.addAll(pul.insertIntoAsFirst);
		this.insertIntoAsLast.addAll(pul.insertIntoAsLast);
		this.insertIntos.addAll(pul.insertIntos);
		this.renames.addAll(pul.renames);
		this.replaceNodeContents.addAll(pul.replaceNodeContents);
		this.replaceNodes.addAll(pul.replaceNodes);
		this.replaceValues.addAll(pul.replaceValues);
		this.put.addAll(pul.put);

		if (this.hasTwiceIds(this.replaceNodes)) {
			throw new DynamicException(ErrorCodes.U0016_UPDATE_DYNAMIC_REPLACE_MULTIPLE_ON_SAME_NODE,
					"Conflicting 'Replace Node' operations on same node detected", loc);
		}
		if (this.hasTwiceIds(this.replaceValues)) {
			throw new DynamicException(ErrorCodes.U0017_UPDATE_DYNAMIC_REPLACE_VALUE_MULTIPLE_ON_SAME_NODE,
			"Conflicting 'Replace Node Value' operations on same node detected", loc);
		}
		if (this.hasTwiceIds(this.replaceNodeContents)) {
			throw new DynamicException(ErrorCodes.U0017_UPDATE_DYNAMIC_REPLACE_VALUE_MULTIPLE_ON_SAME_NODE,
			"Conflicting 'Replace Node Content' operations on same node detected", loc);
		}
		if (this.hasTwiceIds(this.renames)) {
			throw new DynamicException(ErrorCodes.U0015_UPDATE_DYNAMIC_RENAME_MULTIPLE_ON_SAME_NODE, 
					"Conflicting 'Rename' operations on same node detected", loc);
		}
		
		if (hasConflictingURIs(this.put))
			throw new DynamicException(ErrorCodes.U0031_UPDATE_DYNAMIC_MULTIPLE_PUT_SAME_URI,"Multiple put() with same URI",loc);
		
		Hashtable opOnSameNode = new Hashtable(); 
		groupByTarget(insertAttributes, opOnSameNode);
		groupByParentTarget(renames, opOnSameNode);
		groupByParentTarget(replaceNodes, opOnSameNode);

		checkNSBindingConflictsAttr(opOnSameNode);
	}
	private boolean hasConflictingURIs(LinkedList put2) {
		Hashtable set = new Hashtable();
		Enumeration it = put2.elements();
		while (it.hasMoreElements()) {
			UpdatePrimitive up = (UpdatePrimitive) it.nextElement();
			String uri = ((Put)up).getURI();
			String setTest = (String)set.get(new Integer(uri.hashCode()));
			if (setTest != null && uri.equals(setTest)) {
				return true;
			}
			set.put(new Integer(uri.hashCode()),uri);
		}
		return false;
	}

	/**
	 * Check if all the updates contained in the PUL are working against the stores given as parameter
	 * @param stores Set of stores to check
	 * @return true if all updates work (only) against the stores given in stores, false otherwise 
	 */
	public boolean checkStoreUsage(Set stores){
		if (!checkStoreUsagePerClass(stores,deletes))
			return false;
		if (!checkStoreUsagePerClass(stores,insertAfters))
			return false;
		if (!checkStoreUsagePerClass(stores,insertAttributes))
			return false;
		if (!checkStoreUsagePerClass(stores,insertBefores))
			return false;
		if (!checkStoreUsagePerClass(stores,insertIntoAsFirst))
			return false;
		if (!checkStoreUsagePerClass(stores,insertIntoAsLast))
			return false;
		if (!checkStoreUsagePerClass(stores,insertIntos))
			return false;
		if (!checkStoreUsagePerClass(stores,renames))
			return false;	
		if (!checkStoreUsagePerClass(stores,replaceNodeContents))
			return false;
		if (!checkStoreUsagePerClass(stores,replaceNodes))
			return false;
		if (!checkStoreUsagePerClass(stores,replaceValues))
			return false;
		return true;
	}
	
	private boolean checkStoreUsagePerClass(Set stores, LinkedList updates) {
		Enumeration it = updates.elements();
		while (it.hasMoreElements()) {
			UpdatePrimitive up = (UpdatePrimitive) it.nextElement();
			Source sc = up.getTargetId().getStore();
			if (!stores.contains(sc.getURI()))
				return false;
		}
		return true;
	}
	
	private void checkNSBindingConflictsAttr(Hashtable opOnSameNode) throws MXQueryException{
	
		// for each node, do the check
		
		Enumeration nodes = opOnSameNode.elements();
		while (nodes.hasMoreElements()) {
			Vector ops = (Vector)nodes.nextElement();
//			if (ops.size() < 2)
//				continue;
//			else {
				Hashtable seenBindings = new Hashtable();
				Set seenAttrNames = new Set();
				for (int i=0;i<ops.size();i++) {	
					// if insert attribute or replace, get the node and its namespace definition 
					UpdateableStore sr = ((UpdatePrimitive)ops.elementAt(i)).getStore();
					if (sr!=null) {
						Window rootIter = sr.getIterator(null);
						while (rootIter.hasNextItem()) {
							
							Token root = rootIter.nextItem().next();
							if (root == Token.END_SEQUENCE_TOKEN || !(root instanceof NamedToken)) 
								continue;

							NamedToken tok = (NamedToken)root;
							if (Type.isAttribute(tok.getEventType())){
								// duplicate attribute name tests 
								QName attName = new QName(tok.getName());
								attName.setNamespaceURI(tok.getNS());
								if (seenAttrNames.contains(attName))
									throw new DynamicException(ErrorCodes.U0021_UPDATE_DYNAMIC_INVALID_XDM,"Duplicate attribute names in updates",null);
								else
									seenAttrNames.add(attName);
							}
							// Namespace binding conflicts test
							Hashtable nms = tok.getDynamicScope().getLocalNamespaces();
							Enumeration enumNms = nms.elements();
							while (enumNms.hasMoreElements()) { 
								Namespace nm = (Namespace)enumNms.nextElement();
								if (seenBindings.containsKey(nm.getNamespacePrefix())) {
									String uri = (String)seenBindings.get(nm.getNamespacePrefix());
									if (!uri.equals(nm.getURI()))
										throw new DynamicException(ErrorCodes.U0024_UPDATE_DYNAMIC_NEW_NAMESPACE_ATTRIBUTE_CONFLICT,"Conflicting set of namespace bindings in updates",null);
								} else { 
									seenBindings.put(nm.getNamespacePrefix(), nm.getURI());
								}
							}
						}
					} else {
					//rename
						QName ren = ((Rename)ops.elementAt(i)).name;
						if (ren.getNamespaceURI() != null)
							if (seenBindings.containsKey(ren.getNamespacePrefix())) {
								String uri = (String)seenBindings.get(ren.getNamespacePrefix());
								if (!uri.equals(ren.getNamespaceURI()))
									throw new DynamicException(ErrorCodes.U0024_UPDATE_DYNAMIC_NEW_NAMESPACE_ATTRIBUTE_CONFLICT,"Conflicting set of namespace bindings in updates",null);
							} else { 
								seenBindings.put(ren.getNamespacePrefix(), ren.getNamespaceURI());
							}

					}
				}
//			}
		}
		
	}
	
	/**
	 * Group a list of update primitives along the target id
	 * @param updPrimList List of update primitives to cluster
	 * @param opOnSameNode Hashtable to take the clusters (may already contain entries)
	 */
	private void groupByTarget(LinkedList updPrimList, Hashtable opOnSameNode) {
		Enumeration it = updPrimList.elements();
		while (it.hasMoreElements()) {
			UpdatePrimitive up = (UpdatePrimitive) it.nextElement();
			Vector ops;
			if (opOnSameNode.containsKey(up.getTargetId())) {
				ops = (Vector) opOnSameNode.get(up.getTargetId());
				
			}
			else {
				ops = new Vector();
				opOnSameNode.put(up.getTargetId(), ops);
			}
				ops.addElement(up);
		}
	}
	/**
	 * Group a list of update primitives along the target id if not an attribute, otherwise by its parents id
	 * @param updPrimList List of update primitives to cluster
	 * @param opOnSameNode Hashtable to take the clusters (may already contain entries)
	 */
	private void groupByParentTarget(LinkedList updPrimList, Hashtable opOnSameNode) {
		Enumeration it = updPrimList.elements();
		while (it.hasMoreElements()) {
			UpdatePrimitive up = (UpdatePrimitive) it.nextElement();
			Identifier toOperate;
			switch (up.getType()) {
			case UpdatePrimitive.RENAME:
				Identifier par = ((Rename)up).parent;
				if (par != null)
					toOperate = par;
				else
					toOperate = up.getTargetId();
				break;
			case UpdatePrimitive.REPLACE_NODE:
				par = ((ReplaceNode)up).parent;
				if (par != null)
					toOperate = par;
				else
					toOperate = up.getTargetId();
				break;
			default:
					toOperate = up.getTargetId();
			}
			
			Vector ops;
			if (opOnSameNode.containsKey(toOperate)) {
				ops = (Vector) opOnSameNode.get(toOperate);
				
			}
			else {
				ops = new Vector();
				opOnSameNode.put(toOperate, ops);
			}
				ops.addElement(up);
		}
	}	
}

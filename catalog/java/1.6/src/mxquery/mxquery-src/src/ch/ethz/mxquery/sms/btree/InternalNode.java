/**
 * Copyright 2006-2007 ETH Zurich, The iMeMex Project Team
 * see http://www.iMeMex.org for more information on this project
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

package ch.ethz.mxquery.sms.btree;

import java.util.Vector;

import ch.ethz.mxquery.datamodel.DeweyIdentifier;
import ch.ethz.mxquery.datamodel.adm.LinguisticToken;
import ch.ethz.mxquery.util.ObjectObjectPair;

/**
 * Represents a b-tree directory node.
 * 
 * @author jens/marcos extended by christian/julia
 *  
 *  adapted to be CLDC conform: jimhof
 */

public class InternalNode implements BTreeNode{
	
	public boolean isOffsetNode;

	//protected BTreePersister persister;

	protected InternalNodeArrayMap entries;

	protected int k;

	protected double f;

	protected InternalNode nextNode;

	protected InternalNode parentNode;

	protected DeweyIdentifier lowestKey;
	
	//private static Log log = LogFactory.getLog(InternalNode.class);

	/**
	 * Constructors for the Internal Node
	 */
	
	public InternalNode(int k, InternalNodeArrayMap entries){ //, BTreePersister persister) {
		this.k = k;
		this.entries = entries;
		//this.persister = persister;
		lowestKey = getNode(0).getLowestKey();
	}

	public InternalNode(int k, InternalNodeArrayMap entries,
			InternalNode parent){ //BTreePersister persister) {
		this(k, entries);
		this.parentNode = parent;
	}

	public InternalNode(BTreeNode leftChild, DeweyIdentifier pivot,
			BTreeNode rightChild, int k){ //BTreePersister persister) {
		this.k = k;
		this.entries = new InternalNodeArrayMap(2 * k);
		//this.persister = persister;
		//entries.nodes.add(0, leftChild);
		entries.nodes = addElementAtPos(entries.nodes, 0, leftChild);
		entries.put(pivot, rightChild);
		leftChild.setParent(this);
		rightChild.setParent(this);
		lowestKey = leftChild.getLowestKey();
	}

	public InternalNode(int k) {// BTreePersister persister) {

		this.k = k;
		//this.persister = persister;
	}

	/**
	 * Add (generally updates) is not supported in the BTree
	 */
	public SplitInfo add(DeweyIdentifier key, LinguisticToken value, DeweyIdentifier lowKey, DeweyIdentifier highKey,
			LeafCarrier leafCarrier) {
		//throw new UnsupportedOperationException("BTree permits no updates");
		System.out.println("BTree permits no updates");
		return null;
	}

	/**
	 * Remove (generally updates) is not supported in the BTree
	 */
	public void remove(DeweyIdentifier key, LinguisticToken value, DeweyIdentifier lowKey, DeweyIdentifier highKey) {
		//throw new UnsupportedOperationException("BTree permits no updates");
		System.out.println("BTree permits no updates");
	}

	public String toString() {
		if (isOffsetNode)
			return "["
					+ ((InternalNodeArrayOffsetMap) entries).toString()
					+ "]";
		else
			return "[" + entries.toString() + "]";
	}
	

	/**
	 * Obtains all values mapped to the given key range (low and high,
	 * inclusive)
	 * 
	 * @param lowKey
	 * @param highKey
	 * @param results
	 */
	
	public void queryRange(DeweyIdentifier lowKey, DeweyIdentifier highKey, BtreePushOperator results) {
		// look for lowKey and delegate
//		BTreeNode next = elementAt(lowKey);
//		next.queryRange(lowKey, highKey, results);
	}

	public boolean isLeaf() {
		return false;
	}

	public boolean isEmpty() {
		return entries.size() == 0 && getNode(0) == null;
	}

	/**
	 * adds a data pair (key,BTreeNode)
	 * if the internal node is full, create new internal node and return value and internal node
	 * else insert key into internal node
	 */

	public ObjectObjectPair bulkAdd(ObjectObjectPair data, int k, double F) {

		DeweyIdentifier key = (DeweyIdentifier)data.getFirst();
		BTreeNode rightNode = (BTreeNode)data.getSecond();
		InternalNode newNode;
		ObjectObjectPair newPair;
		f = F;

		/*
		 * must not just create new node with one entry ([foo |])!!!! So move
		 * 2*k'th element to new node as well
		 */

		/*
		 * What about "notsofull* loading? Better decide for new node if full,
		 * so we can add later without "splitting attacks"?
		 */

		if (entries.size() >= 2 * k * F) {
			newNode = new InternalNode(getNode(entries.size()), key,
					rightNode, k); //persister);
			newPair = new ObjectObjectPair(newNode.getLowestKey(),
					newNode);

			entries.deleteAtPos(entries.size());

			//try {
				if (((BTreeNode)this.entries.nodes.elementAt(0)).isLeaf()) {
					//entries = persister.writeBulkLoadedInternalNode(this);
					isOffsetNode = true;
				} //else
					//persister.writeBulkLoadedInternalNode(this);
				// }
			//} catch (IOException e) {
				//log.error("I/O Exception", e);
				//System.out.println("I/O Exception "+e);
			//}

			if (parentNode == null) {
				new InternalNode(this, newNode.getLowestKey(), newNode,
						k); // persister);
			} else
				parentNode.bulkAdd(newPair, k, F);

		} else {
			entries.addAtPos(key, entries.currentSize, rightNode);
			rightNode.setParent(this);
		}

		return null;
	}

	public BTreeNode getRoot() {
		if (parentNode == null) {
			return this;
		}
		return parentNode.getRoot();
	}

	public void setParent(InternalNode parentNode) {
		this.parentNode = parentNode;
	}

	public DeweyIdentifier getLowestKey() {
		return lowestKey;
	}

	public boolean isFull() {
		return (entries.size() >= 2 * k * f);
	}
	/**
	 * Returns the node at a given position
	 * @param pos
	 * @return
	 */
	protected BTreeNode getNode(int pos) {
//		if (isOffsetNode) {
//			Long offset = ((InternalNodeArrayOffsetMap) entries)
//					.getNodeOffset(pos);
//			if (offset == null)
//				return null;
//			else
//				try {
//					if (offset < 1) {
//						return persister.readLeaf(offset);
//					} else
//						return persister.readInternalNode(offset);
//				} catch (IOException e) {
//					//log.error("I/O Exception", e);
//					System.out.println("I/O Exception" + e);
//				}
		//} else {
			if (entries.nodes.elementAt(0) instanceof Leaf){
				return (Leaf)entries.nodes.elementAt(pos);
			}
			else{
				return (InternalNode)entries.nodes.elementAt(pos);
			}
		//}
		//return null;
	}

	public BTreeNode getBTreeNode(DeweyIdentifier key) {
			
		int l = 0;
		int r = entries.size();
		int m = (l+r)/2;
		int prevm = 0;
		int prevprevm = 0;
		DeweyIdentifier did = (DeweyIdentifier) entries.keys.elementAt(m);
		while (m < entries.size() && m >0 && prevm != m && prevprevm != m){

			did = (DeweyIdentifier) entries.keys.elementAt(m);
			
			// the key is in the left half
			if ( (did).compare(key) < 0){
				prevprevm = prevm;
				prevm = m;
				r = m;
			}
			// the key is in the right half
			else if ((did).compare(key) > 0){
				prevprevm = prevm;
				prevm = m;
				l = m;
			}
			else{
				return this.getNode(m+1);
			}
			m = (l+r)/2;
			
		}
		if (m==0){
			did = (DeweyIdentifier)entries.keys.elementAt(0);
			if (key.compare(did) == 1){
				return this.getNode(0);
			}
			else if (did.compare(key)== 1){
				return this.getNode(1);
			}
			else{
				return this.getNode(1);
			}
		}
		
		
		if (prevm == m){
			if (key.compare(did) == 1){
				return this.getNode(m);
			}
			else if (did.compare(key) == 1){
				return this.getNode(m+1);
			}
		}
		if (prevprevm == m){
			did = (DeweyIdentifier) entries.keys.elementAt(m);
			if (key.compare(did) == 1){
				return this.getNode(m);
			}
			else if (did.compare(key) == 1){
				return this.getNode(m+1);
			}
		}
		return null;
		
	}

	public void getFirstKeyAfter(DeweyIdentifier key, Leaf[] inLeaf, int[] atPos) {

//		BTreeNode next = get(key);	
//		
//		next.getFirstKeyAfter(key, inLeaf, atPos);
		
	}

	public void getLastKeyBefore(DeweyIdentifier key, Leaf[] inLeaf, int[] atPos) {

//		BTreeNode next = get(key);
//		next.getLastKeyBefore(key, inLeaf, atPos);
	}
	
	private Vector addElementAtPos(Vector a, int pos, Object element){
		Vector temp = new Vector();
		for (int i=0; i < pos; i++){
			temp.addElement(a.elementAt(i));
		}
		temp.addElement(element);
		for (int i=pos; i<a.size(); i++){
			temp.addElement(a.elementAt(i));
		}
		return temp;
	}

}

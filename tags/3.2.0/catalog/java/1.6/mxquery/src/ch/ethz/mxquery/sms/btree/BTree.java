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
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.sms.ftstore.PhraseIterator;
import ch.ethz.mxquery.util.ObjectObjectPair;

/**
 * A simple btree implementation. (key equal or less than pivot -> go right) Has
 * to be taylored to different key and value types manually as Generics would
 * use Complex type (inefficent) instead of native types. We have pointers among
 * leaves and also only store key/value mappings on the leaves. Therefore, this
 * is a B+-tree implementation. The implementation allows us to store duplicate
 * keys in the leaf level of the tree. However, a strict tree interval invariant
 * is kept. To handle duplicate keys, this means that overflows due to
 * duplication are handled by adding special overflow leaves that are not
 * pointed to by any parent node. These leaves are created via splitting,
 * however no pivot is promoted on such a split. The delete strategy implemented
 * in this tree is to free at empty. Thus, there is no logic to merge at nodes
 * at half occupation and nodes may become underutilized. This may be monitored
 * by calculating the utilization at the leaf level.
 * 
 * @author jens / marcos extended by christian / julia
 * 
 * adapted to be CLDC conform: jimhof
 * 
 */
public class BTree {

	//private static Log log = LogFactory.getLog(BTree.class);

	/** the root of the b-tree */
	protected BTreeNode root = null;

	/** the left-most leaf on the b-tree */
	private Leaf firstLeaf = null;

	/** the degree of the b-tree (internal nodes) */
	protected int k;

	/** the degree of the leaves */
	protected int k_star;

	/** BTree Persister- persists the nodes during bulk loading */
	//protected BTreePersister bTreePersister;

	/** invalid value for atPos index returned from getFirstKeyAfter */
	public static final int INVALID_KEY_INDEX = -1;

	/**
	 * Instantiates a new BTree. Does not create persister. Has to be set
	 * manually afterwards, since every BTree must have a persister.
	 * <p>
	 * TODO remove this constructor.
	 * 
	 * @param k
	 * @param k_star
	 */
	public BTree(int k, int k_star) {
		this.k = k;
		this.k_star = k_star;
		//this.bTreePersister = new BTreePersister(this,null,null,"");
	}

	/**
	 * Instantiates a new BTree. Creates persister using the given two persister
	 * for keys and values. persists into files filename+Leafs.txt and
	 * filename+Nodes.txt. empties the files before writing.
	 * 
	 * @param k
	 * @param k_star
	 * @param kPersister - a persister for the key type
	 * @param vPersister - a persister for the value type
	 * @param filename - will be used as filebasename for the persister files
	 */
//	public BTree(int k, int k_star, Persister kPersister, Persister vPersister, String fileName) {
//		this(k, k_star, kPersister, vPersister, fileName, true);
//	}

	/**
	 * Instantiates a new BTree like the one without "deletefiles", but can be
	 * told to not empty the files before writing.
	 * 
	 * @param k
	 * @param k_star
	 * @param kPersister - a persister for the key type
	 * @param vPersister - a persister for the value type
	 * @param filename - will be used as filebasename for the persister files
	 * @param deletefiles - tells if files have to be set to zero before writing
	 */
//	public BTree(int k, int k_star, Persister kPersister, Persister vPersister, String fileName,
//			boolean deletefiles) {
//
//		this.k = k;
//		this.k_star = k_star;
//		bTreePersister = new BTreePersister(this, kPersister, vPersister, fileName);
//		bTreePersister.reOpenFiles(deletefiles, false);
//		// TODO read data as well
//	}

	/**
	 * Instantiates a new BTree like the one without "deletefiles", but can be
	 * given a PullOperator with sorted key-value Pairs and a leaf usage to
	 * bulkload the btree while creating.
	 * 
	 * @param k
	 * @param k_star
	 * @param kPersister - a persister for the key type
	 * @param vPersister - a persister for the value type
	 * @param filename - will be used as filebasename for the persister files
	 * @param input as Pulloperator of <key,value> Tuples, sorted by keys
	 * @param F denoting the leaf usage
	 */
//	public BTree(int k, int k_star, Persister kPersister, Persister vPersister, String fileName,
//			LinguisticTokenIterator input, int F) {
//		this(k, k_star, kPersister, vPersister, fileName);
//		bulkLoad(input, F);
//	}

	/**
	 * Bulkloads an empty BTree fill leaves from left to right until certain
	 * percentage. If percentage reached begin next leaf and create parent
	 * InternalNode if needed.
	 * 
	 * @param input as Pulloperator of <key,value> Tuples, sorted by keys
	 * @param F denoting the leaf usage
	 * @throws MXQueryException 
	 */
	public BTreeNode bulkLoad(PhraseIterator input, int F) throws MXQueryException {
		Leaf curLeaf;
		ObjectObjectPair curInput;
		ObjectObjectPair overflowInfo;
		int F_help = 2 * k_star * F;

		// LinkedList<Pair<K,V>> inputPairs = new LinkedList<Pair<K,V>>();
		Vector inputPairs = new Vector();

		// tell persister to use bulkLoadUtils
		//bTreePersister.setBulkLoad(this);

		//bTreePersister = null;
		
		curLeaf = new Leaf(k_star); //, bTreePersister);
		firstLeaf = curLeaf;


		
		while (input.hasNext()) {
			// fetch the next F*2*k_star input pairs
			for (int i = 0; i < F_help; ++i) {
				if (input.hasNext()) {
					LinguisticToken next = (LinguisticToken) input.next();
					inputPairs.addElement(new ObjectObjectPair(next.getDeweyId(),next));
				} else {
					break;
				}
			}
			// if there are enough for a next legal leaf, add them by
			// bulkloading
			if (inputPairs.size() >= k_star) {
				for (int i=0; i < inputPairs.size(); i++){

					overflowInfo = curLeaf.bulkAdd((ObjectObjectPair)inputPairs.elementAt(i), k, F);
					/*
					 * new Leaf has been built (should be the case for every
					 * first item per iteration, except the really first)
					 */
					if (overflowInfo != null)
						curLeaf = (Leaf) overflowInfo.getSecond();
				}
				// clear the list
				inputPairs = new Vector();
			}
		}

		root = curLeaf.getRoot();

		/*
		 * if there have been not enough input pairs for a legal leaf
		 */
		if (!inputPairs.isEmpty()) {
			for (int i=0; i < inputPairs.size(); i++){
				curInput = (ObjectObjectPair)inputPairs.elementAt(i);
				overflowInfo = curLeaf.bulkAdd(curInput, k, F);
				if (overflowInfo != null)
					curLeaf = (Leaf) overflowInfo.getSecond();
			}
		}
		// persist non-full BTreeNodes
		InternalNode curParent = curLeaf.parentNode;
		//try {
			// TODO this is a bit hackish:-)
			if (curParent == null) {
				// only one (non-full) leaf in this bulk load
				//bTreePersister.writeKandKStart(k, k_star);
			}
			curLeaf.nextOffset = 2;
			//bTreePersister.writeLeaf(curLeaf);
			while (curParent != null) {
				if (curParent.getNode(0).isLeaf()) {
					//curParent.entries = bTreePersister.writeBulkLoadedInternalNode(curParent);
					curParent.isOffsetNode = true;
					
				}
				curParent = curParent.parentNode;
					//bTreePersister.writeBulkLoadedInternalNode(curParent);
			}
		//} 
		//catch (IOException e) {
			//log.error("I/O Exception", e);
			//System.out.println("I/O Exception "+ e);
		//}

		// get the root of the bulkloaded tree
		//root = curLeaf.getRoot();
//		try {
//			bTreePersister.flushFiles();
//		} catch (IOException e) {
//			//log.error("I/O Exception", e);
//			System.out.println("I/O Exception "+ e);
//		}
		// tell persister to use non-bulkloadUtils
		//bTreePersister.unsetBulkLoad(this);
		root = curLeaf.getRoot();
		//dumpLeafs();
		return root;
	}

	/**
	 * Adds a mapping from key to value in the b-tree. Duplicate mappings are
	 * allowed.
	 * 
	 * @param key
	 * @param value
	 * @param leafCarrier
	 */
	public void add(DeweyIdentifier key, LinguisticToken value, LeafCarrier leafCarrier) {

		//throw new UnsupportedOperationException("BTree add is not supported");
		System.out.println("BTree add is not supported");
	}

	/**
	 * Gets the value currently mapped to the given key.
	 * 
	 * @param key
	 * @param currentRoot
	 */
	public LinguisticToken get(DeweyIdentifier key, BTreeNode currentRoot) {
		
		
		BTreeNode node = currentRoot.getBTreeNode(key);
		
		while (node instanceof InternalNode){
			node = node.getBTreeNode(key);
		}
		Leaf leaf = (Leaf) node;
		Vector keys = leaf.entries.keys;
		int l = 0;
		int r = keys.size();
		int m = (l+r)/2;
		while (m < keys.size() && m >=0 ){
			DeweyIdentifier mdid = (DeweyIdentifier)keys.elementAt(m);
			// key is in the right half
			if (mdid.isSmallerThan(key)){
				l = m;
			}
			// key is in the left half
			else if (key.isSmallerThan(mdid)){
				r = m;
			}
			else{
				return (LinguisticToken)leaf.entries.values.elementAt(m);
			}
			m = (l+r)/2;
		}
		
		return null;
	}
	
	public LinguisticToken getNext(DeweyIdentifier key, BTreeNode currentRoot) {
		
		
		BTreeNode node = currentRoot.getBTreeNode(key);
		
		while (node instanceof InternalNode){
			node = node.getBTreeNode(key);
		}
		Leaf leaf = (Leaf) node;
		Vector keys = leaf.entries.keys;
		int l = 0;
		int r = keys.size();
		int m = (l+r)/2;
		while (m < keys.size() && m >=0 ){
			DeweyIdentifier mdid = (DeweyIdentifier)keys.elementAt(m);
			// key is in the right half
			if (mdid.compare(key) == 1){
				l = m;
			}
			// key is in the left half
			else if (key.compare(mdid) == 1){
				r = m;
			}
			else{
				if (m == leaf.entries.values.size()-1){
					if (leaf.getNextLeaf() != null){
						return (LinguisticToken) leaf.getNextLeaf().entries.values.elementAt(0);
					}
					else{
						return null;
					}
				}
				else{
					return (LinguisticToken)leaf.entries.values.elementAt(m+1);
				}
			}
			m = (l+r)/2;
		}
		return null;
	}
	
	
	public Vector getSiblings(DeweyIdentifier from, DeweyIdentifier parent, BTreeNode currentRoot, DeweyIdentifier [] ignoreId){
		
		BTreeNode node = currentRoot.getBTreeNode(from);
		int startPos = 0;
		Vector siblings = new Vector();
		
		while (node instanceof InternalNode){
			node = node.getBTreeNode(from);
		}
		Leaf leaf = (Leaf) node;
		Vector keys = leaf.entries.keys;
		int l = 0;
		int r = keys.size();
		int m = (l+r)/2;
		while (m < keys.size() && m >=0 ){
			DeweyIdentifier mdid = (DeweyIdentifier)keys.elementAt(m);
			// key is in the right half
			if (mdid.compare(from) == 1){
				l = m;
			}
			// key is in the left half
			else if (from.compare(mdid) == 1){
				r = m;
			}
			else{
				if (m == leaf.entries.values.size()-1){
					if (leaf.getNextLeaf() != null){
						startPos = 1;
						break;
					}
					else{
						return null;
					}
				}
				else{
					startPos = m+1;
					break;
				}
			}
			m = (l+r)/2;
		}
		
		boolean isSibling = true;
		
		while (leaf != null && isSibling){
			while (startPos < leaf.entries.values.size()){
				LinguisticToken sibling = (LinguisticToken)leaf.entries.values.elementAt(startPos);
				DeweyIdentifier did = sibling.getDeweyId();
				if (parent.isAncestorOf(did)){
					boolean isIgnored = false;
					for (int i=0;i<ignoreId.length;i++) {
						if (ignoreId[i].isAncestorOf(did))
							isIgnored = true;
					}
					if (!isIgnored){
						siblings.addElement(sibling);
					}
					startPos++;
					if (startPos == leaf.entries.values.size()){
						leaf = leaf.getNextLeaf();
						startPos = 0;
					}
					if (leaf == null){
						break;
					}
				}
				else{
					isSibling = false;
					break;
				}
			}
			if (leaf != null){
				leaf = leaf.getNextLeaf();
			}
		}
		if (leaf != null){
			if (leaf.getNextLeaf() == null && startPos < leaf.entries.values.size() && isSibling){
				while (startPos < leaf.entries.values.size()){
					LinguisticToken sibling = (LinguisticToken)leaf.entries.values.elementAt(startPos);
					DeweyIdentifier did = sibling.getDeweyId();
					if (parent.isAncestorOf(did)){
						boolean isIgnored = false;
						for (int i=0;i<ignoreId.length;i++) {
							if (ignoreId[i].isAncestorOf(did))
								isIgnored = true;
						}
						if (!isIgnored){
							siblings.addElement(sibling);
						}
						startPos++;
					}
					else{
						break;
					}
				}
			}
		}
		return siblings;
	}
	
	/**
	 * Gets the key value in the given leaf at the given position
	 * 
	 * @param l leaf in which key is looked up
	 * @param pos position inside leaf where key is at
	 * @return the identifier at l, pos
	 */
	public DeweyIdentifier getKey(Leaf l, int pos) {
		if (l == null) {
			return null;
		}
		return (DeweyIdentifier)l.entries.keys.elementAt(pos);
	}

	/**
	 * This method takes two arrays as out parameters and inserts in them
	 * pointers to the leaf and position of the smallest key currently in tree
	 * which is either equal to <code>key</code> (if <code>key</code> is
	 * present in btree) or smallest key larger than <code>key</code> (if
	 * <code>key</code> is not present in btree):<br>
	 * 
	 * @param key
	 * @param inLeaf points to the leaf where to get the key
	 * @param atPos points to the position of the key inside the leaf
	 */

	public void getFirstKeyAfter(DeweyIdentifier key, Leaf[] inLeaf, int[] atPos) {

		if (key.compare(this.getLastKey()) > 0) {

			return;
		}

		root.getFirstKeyAfter(key, inLeaf, atPos);
	}

	/**
	 * This method takes two arrays as out parameters and inserts in them
	 * pointers to the leaf and position of the largest key currently in tree
	 * which is either equal to <code>key</code> (if <code>key</code> is
	 * present in btree) or largest key smaller than <code>key</code> (if
	 * <code>key</code> is not present in btree):<br>
	 * 
	 * @param key the search key
	 * @param inLeaf points to the leaf where to get the key
	 * @param atPos points to the position of the key inside the leaf
	 */

	public void getLastKeyBefore(DeweyIdentifier key, Leaf[] inLeaf, int[] atPos) {

		if (key.compare(this.getFirstKey()) < 0) {

			return;
		}

		root.getLastKeyBefore(key, inLeaf, atPos);
	}

	/**
	 * This method takes two arrays as out parameters and inserts in them
	 * pointers to the leaf and position of the largest key currently in tree
	 * which is either equal to <code>key</code> (if <code>key</code> is
	 * present in btree) or largest key smaller than <code>key</code> (if
	 * <code>key</code> is not present in btree). This method implements the
	 * same functionality as getLastKeyBefore but uses leaf traversal to find
	 * the position of the key.<br>
	 * 
	 * @param toKey the search key
	 * @param startLeaf
	 * @param startPos
	 * @param inLeaf points to the leaf where to get the key
	 * @param atPos points to the position of the key inside the leaf
	 */

	public void getLastKeyBeforeLeafTraversal(DeweyIdentifier toKey, Leaf startLeaf, int startPos, Leaf[] inLeaf,
			int[] atPos) {

		Leaf currentLeaf = startLeaf;
		DeweyIdentifier currentKey = (DeweyIdentifier)currentLeaf.entries.keys.elementAt(startPos);
		int posInLeafKeys = startPos;

		// go through all keys < toKey
		while (currentKey.compare(toKey) < 0) {

			posInLeafKeys++;

			// if we are at the end of current leaf get next

			if (posInLeafKeys == currentLeaf.entries.currentSize) {

				posInLeafKeys = 0;
				currentLeaf = currentLeaf.getNextLeaf();

			}

			currentKey = (DeweyIdentifier)currentLeaf.entries.keys.elementAt(posInLeafKeys);

		}

		// get next key, leaf, pos
		// input parameters will be used for output

		atPos[0] = posInLeafKeys;
		inLeaf[0] = currentLeaf;

	}

	/**
	 * Read data of this B-tree. Remember to assign its return value!
	 * 
	 * @return
	 * @throws IOException
	 */
//	public BTree readData() throws IOException {
//		return bTreePersister.readBTree();
//	}

	/**
	 * Removes all mappings corresponding to the given key from the b-tree.
	 * 
	 * @param key
	 */
	public void remove(DeweyIdentifier key) {
		root.remove(key, null, null, null);
	}

	/**
	 * Removes one instance of the given key-value mapping from the b-tree. Note
	 * that even if multiple instances of that mapping exist, only a single
	 * instance will be removed.
	 * 
	 * @param key
	 * @param value
	 */
	public void remove(DeweyIdentifier key, LinguisticToken value) {
		root.remove(key, value, null, null);
	}

	/**
	 * Returns all the values mapped in the given key range through the provided
	 * push operator. We include values that correspond to the lowKey and also
	 * include values that correspond to the highKey.
	 * 
	 * @param lowKey
	 * @param highKey
	 * @param results PushOperator to be filled.
	 */
	public void queryRange(DeweyIdentifier lowKey, DeweyIdentifier highKey, BtreePushOperator results) {
		root.queryRange(lowKey, highKey, results);
	}

	/**
	 * Prints the root of the tree as a string.
	 */
	public String toString() {
		if (root == null) {
			return "b-tree not initialized!";
		} else {
			return root.toString();
		}
	}

	/**
	 * Close this b-tree. That is make it persistent first.
	 */
	public void close() {
//		try {
//			bTreePersister.closeFiles();
//		} catch (IOException e) {
//			//log.error("Could not close b-tree: " + e.getMessage(), e);
//			System.out.println("Could not close b-tree: " + e.getMessage()+ e);
//		}
	}

	

	/**
	 * prints statistical infos about the tree
	 */
	public void printStats() {
//		Leaf currentLeaf = getFirstLeaf();
//		int leafCount = 0;
//		int elemCount = 0;
//		while (currentLeaf != null) {
//			leafCount++;
//			elemCount += currentLeaf.entries.size();
//
//			currentLeaf = currentLeaf.getNextLeaf();
//		}
//
//		double utilization = (double) elemCount / (double) (leafCount * 2 * k_star);
//
//		System.out.print("leafUtilization:\t" + utilization + "\tleafCount:\t" + leafCount + "\telementCount:\t"
//				+ elemCount + "\t");
	}

	/**
	 * @return number of keys in an initialized this b-tree.
	 */
	public int size() {
		
		int elemCount = 0;

		if( (root != null) && (!root.isEmpty())) {
			
			Leaf currentLeaf = getFirstLeaf();
			while (currentLeaf != null) {
				elemCount += currentLeaf.entries.size();
				currentLeaf = currentLeaf.getNextLeaf();
			}
			
		}
		
		return elemCount;
		
	}

	/**
	 * Gets the root of the btree
	 * 
	 * @return the root of the btree
	 */
	public BTreeNode getRoot() {
		return root;
	}

	/**
	 * gets the first leaf of the btree
	 * 
	 * @return the first leaf of the btree or null if no leaf exists
	 */
	public Leaf getFirstLeaf() {

		if (firstLeaf != null) {
			return firstLeaf;
		} 
		return null;
//		else {
//			try {
//				if (bTreePersister.existDataFiles()) {
//					return bTreePersister.readLeaf(0);
//				} else {
//					return null;
//				}
//			} catch (IOException e) {
//				//log.error("I/O Exception", e);
//				System.out.println("I/O Exception "+e);
//				return null;
//			}
		}


	public Leaf getLastLeaf() {

		BTreeNode currentNode = root;

		while (!currentNode.isLeaf()) {

			InternalNode currentInternalNode = (InternalNode) currentNode;

			currentNode = currentInternalNode.getNode(currentInternalNode.entries.currentSize);

		}

		Leaf leaf = (Leaf) currentNode;

		return leaf;
	}

	/**
	 * This method returns the last(largest) key present in the tree
	 * 
	 * @return the largest key in the tree
	 */

	public DeweyIdentifier getLastKey() {

		Leaf leaf = getLastLeaf();

		return (DeweyIdentifier)leaf.entries.keys.elementAt(leaf.entries.currentSize - 1);

		/*
		 * BTreeNode<K,V> currentNode = root; while (!currentNode.isLeaf()){
		 * InternalNode<K,V> currentInternalNode = (InternalNode<K,V>)
		 * currentNode; currentNode =
		 * currentInternalNode.getNode(currentInternalNode.entries.currentSize); }
		 * Leaf<K,V> leaf = (Leaf<K,V>) currentNode; return
		 * leaf.entries.keys.get(leaf.entries.currentSize - 1);
		 */
		/*
		 * alternative implementation Leaf<K,V> leaf = getFirstLeaf(); Leaf<K,V>
		 * nextLeaf = getFirstLeaf(); while (nextLeaf != null){ leaf = nextLeaf;
		 * nextLeaf = leaf.getNextLeaf(); } return
		 * leaf.entries.keys.get(leaf.entries.currentSize-1);
		 */
	}

	/**
	 * This method returns the first(smallest) currently in the tree. If the
	 * tree is empty the method returns null.
	 * 
	 * @return the smaller key
	 */

	public DeweyIdentifier getFirstKey() {

		// go to first lead directly and get the smallest key
		Leaf leaf = getFirstLeaf();

		if (leaf != null)
			return (DeweyIdentifier)leaf.entries.keys.elementAt(0);
		else
			return null;

	}
	
//	private void dumpLeafs(){
//		
//		Leaf temp = firstLeaf;
//		
//		while(temp != null){
//			LeafArrayMap entries = temp.entries;
//			Vector keys = entries.keys;
//			for (int i=0; i < keys.size(); i++){
//				DeweyIdentifier did = (DeweyIdentifier)keys.elementAt(i);
//				did.dumpID();
//			}
//			temp = temp.getNextLeaf();
//		}
//		
//	}
}

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


import ch.ethz.mxquery.datamodel.DeweyIdentifier;
import ch.ethz.mxquery.datamodel.adm.LinguisticToken;
import ch.ethz.mxquery.util.ObjectObjectPair;

/**
 * Represents a node from the b-tree (either directory node or leaf).
 * 
 * @author jens/marcos extended by christian/julia
 * adapted by jimhof (adapted to be CLDC conform)
 */
public interface BTreeNode{
	
	/**
	 * for bulk loading
	 */
	public ObjectObjectPair bulkAdd(ObjectObjectPair data, int k, double F);
	
	/**
	 * To get the root of the btree this node is sitting in
	 * 
	 * @return BTreeNode - the root
	 */
	public BTreeNode getRoot();
	
	/**
	 * Indicates if this node is a leaf.
	 * 
	 * @return true if a leaf
	 */
	public boolean isLeaf();

	/**
	 * Returns the internal node/ leaf that contains the key "key"
	 * @param key
	 * @return BTreeNode
	 */
	public BTreeNode getBTreeNode(DeweyIdentifier key);
	
	/**
	 * Creates pointers to the given key or if the key is not in the tree,
	 * to the smallest key larger than the given one that is 
	 * in the tree. 
	 * 
	  * @param key
	 * @param inLeaf points to the leaf where to get the key
	 * @param atPos	points to the position of the key inside the leaf
	 */
	public void getFirstKeyAfter(DeweyIdentifier key, Leaf[] inLeaf, int[] atPos);

	
	/**
	* Creates pointers to the given key or if the key is not in the tree,
	 * to the largest key smaller than the given one that is 
	 * in the tree. 
	 * 
	 * @param key
	 * @param inLeaf points to the leaf where to get the key
	 * @param atPos	points to the position of the key inside the leaf
	 */
	
	public void getLastKeyBefore(DeweyIdentifier key, Leaf[] inLeaf, int[] atPos);
	
	/**
	 * Obtains all values mapped to the given key range (low and high,
	 * inclusive). Values are delivered to the provided push operator.
	 * 
	 * @param lowKey
	 * @param highKey
	 * @param results
	 */
	public void queryRange(DeweyIdentifier lowKey, DeweyIdentifier highKey, BtreePushOperator results);

	/**
	 * Adds the given mapping to the node.
	 * 
	 * @param key
	 * @param value
	 * @return SplitInfo if BtreeNode had to be split to keep k or k_star. else null
	 */
	public SplitInfo add(DeweyIdentifier key, LinguisticToken value, DeweyIdentifier lowKey, DeweyIdentifier highKey, LeafCarrier leafCarrier);

	/**
	 * Removes a single instance of the key-value mapping from the node. If the
	 * value given is equal to BTreeConstants.ALL_MAPPINGS then all mappings
	 * associated to the given key will be removed.
	 * 
	 * @param key
	 * @param lowKey
	 * @param highKey
	 */
	public void remove(DeweyIdentifier key, LinguisticToken value, DeweyIdentifier lowKey, DeweyIdentifier highKey);

	/**
	 * Indicates if this node is empty.
	 * 
	 * @return true if empty 
	 */
	public boolean isEmpty();

	
	/**
	 * sets the parentNode.
	 * 
	 * @param parentNode
	 */
	public void setParent(InternalNode parentNode);
	
	/**
	 * gets the lowest key recursively
	 * 
	 * @return the smallest key
	 */
	public DeweyIdentifier getLowestKey();
}

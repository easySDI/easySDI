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

//import java.io.DataOutput;
import java.util.Vector;
//import java.io.OutputStream;
//import java.util.ArrayList;
//import java.util.LinkedHashSet;
//import java.util.Set;
//import java.util.Map.Entry;

import ch.ethz.mxquery.datamodel.DeweyIdentifier;
import ch.ethz.mxquery.datamodel.adm.LinguisticToken;
import ch.ethz.mxquery.util.ObjectObjectPair;

//import org.apache.commons.logging.Log;
//import org.apache.commons.logging.LogFactory;


/**
 * Represents a b-tree leaf node.
 * 
 * @author jens/marcos extended by christian/julia
 * 
 * adapted to be CLDC conform: jimhof
 */
public class Leaf implements BTreeNode {
	
	//private static Log log = LogFactory.getLog(Leaf.class);
	
	protected LeafArrayMap entries;

	protected int k_star;

	private Leaf nextLeaf;
	
	protected long nextOffset = 1;

	private Leaf prevLeaf;
	
	protected long prevOffset = 1;
	
	protected InternalNode parentNode;
	
	//protected DataOutput internalNodeOutput;
	
	//protected BTreePersister persister;

	public Leaf(int k_star, LeafArrayMap entries){//, BTreePersister bTreePersister) {
		this.k_star = k_star;
		this.entries = entries;
		this.nextLeaf = null;
		this.prevLeaf = null;
		//this.persister = bTreePersister;
		this.nextOffset = 1;
		this.prevOffset = 1;
	}

	public Leaf(int k_star, LeafArrayMap entries, InternalNode parent){//,BTreePersister bTreePersister) {
		this(k_star,entries);
		this.parentNode = parent;
	}

	public Leaf(int k_star){ //BTreePersister bTreePersister) {
		this(k_star, new LeafArrayMap(2 * k_star));
	}

	/**
	 * Add (generally updates) is not supported in the BTree
	 */
	public SplitInfo add(DeweyIdentifier key, LinguisticToken value, DeweyIdentifier lowKey, DeweyIdentifier highKey, LeafCarrier leafCarrier) {
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
		return "[" + entries.toString() + "]";
	}

	public BTreeNode get(DeweyIdentifier key) {
		return this;
	}
	
	
	
	/**
	 * Obtains all values mapped to the given key range (low and high,
	 * inclusive)
	 * 
	 * @param lowKey
	 * @param highKey
	 * @param results
	 */

	public void queryRange(final DeweyIdentifier lowKey, final DeweyIdentifier highKey, BtreePushOperator results) {
		
		// start with a query range on this leaf and proceed to next leaf if
		// necessary
		
		int continueSearch = entries.queryRange(lowKey, highKey, results);
		
		Leaf currentLeaf = this.getNextLeaf();
		
		while (continueSearch != LeafArrayMap.STOP && currentLeaf != null) {
			
			if (continueSearch == LeafArrayMap.CONTINUE_WITH_SCAN) continueSearch = currentLeaf.entries.continueScan(0,
					highKey, results);
			else continueSearch = currentLeaf.entries.queryRange(lowKey, highKey, results);

			currentLeaf = currentLeaf.getNextLeaf();
		}
		
		results.thatsallfolks();
	}

	public boolean isLeaf() {
		return true;
	}

	public boolean isEmpty() {
		return entries.size() == 0;
	}

	public void removeValue(LinguisticToken value) {
		// search for value
		boolean foundValue = false;
		Leaf currentLeaf = this;

		while (!foundValue && currentLeaf != null) {
			for (int i = 0; i < currentLeaf.entries.size(); i++) {
				if (((LinguisticToken)currentLeaf.entries.values.elementAt(i)).compareTo(value)==0) {
					currentLeaf.entries.deleteAtPos(i);
					foundValue = true;
				}
			}
			currentLeaf = currentLeaf.getNextLeaf();
		}
	}
		
	/**
	 * adds a data pair (key,value)
	 * if the leaf is full, create new leaf and return value and leaf
	 * else insert value into leaf
	 * @param data
	 * @param k
	 * @param F
	 * @return the split leaf
	 */
	
	public ObjectObjectPair bulkAdd(ObjectObjectPair data, int k, double F) {
		Leaf newLeaf;
		ObjectObjectPair newPair;
		DeweyIdentifier key = (DeweyIdentifier)data.getFirst();
		LinguisticToken value = (LinguisticToken)data.getSecond();
		
		/* What about "notsofull* loading? Better decide for new node if 
		 * full, so we can add later without "splitting attacks"? */

		if (this.entries.size()>= 2*k_star*F){
			
			LeafArrayMap newEntries = new LeafArrayMap(2 * k_star);
			newEntries.addAtPos(key,value,0);
			newLeaf = new Leaf(k_star,newEntries,parentNode); //,persister);
			newPair = new ObjectObjectPair(key,newLeaf);
			this.nextLeaf = newLeaf;
			newLeaf.prevLeaf = this;
			
			//set prevOffset if the previous leaf is in the writeoffsetmap
			if (0 < prevOffset) {
				//TODO to really get the prevleaf we look for it in the writeoffsetmap. This will not work everytime..
				if (getPrevLeaf() != null){
					//Long readOffsetOfprevLeaf = (Long)persister.writeOffsetmap.get(getPrevLeaf());
					//if (readOffsetOfprevLeaf != null) prevOffset = readOffsetOfprevLeaf;
				}
					
			}

//			if (((DeweyIdentifier)newLeaf.entries.keys.elementAt(0)).comparePosition((DeweyIdentifier)entries.keys.elementAt(entries.size() - 1)) > 0) {
//				if (parentNode == null) {
//					
//					new InternalNode(this,key,newLeaf,k); //persister);
//				}
//				else{
//					parentNode.bulkAdd(newPair,k,F);
//					
//				}
//			}
			if (((DeweyIdentifier)newLeaf.entries.keys.elementAt(0)).compare((DeweyIdentifier)entries.keys.elementAt(entries.size() - 1)) < 0) {
				if (parentNode == null) {
					
					new InternalNode(this,key,newLeaf,k); //persister);
				}
				else{
					parentNode.bulkAdd(newPair,k,F);
					
				}
			
			}
			
			else{
				/* the first leaf needs special treatment, so create new 
				 * parent even with overflow leaf but remove the overflow
				 * instantly. -> parentnode set for all Leafs after that.
				 * */
				if (parentNode == null){
					//InternalNode<K,V> newNode = new InternalNode<K,V>(this,key,newLeaf,k,persister);
					//newNode.entries.deleteAtPos(0);
					
				}
				
			}
			
//		if (persister != null){
//				// write the full leaf
//				try {
//					persister.writeLeaf(this);
//					entries = null;
//				} catch (IOException e) {
//					System.out.println("I/O exception "+e);
//					//log.error("I/O exception", e);
//				}
//			}
//			
			
			return newPair;
		}
		else{
			this.entries.addAtPos(key, value, entries.currentSize);
		}
		
		return null;
	}
	
	public BTreeNode getRoot() {
		if (parentNode == null){
			return this;
		}
		return parentNode.getRoot();
	}
	
	public void setParent(InternalNode parentNode){
		this.parentNode = parentNode;
	}
	
	public DeweyIdentifier getLowestKey(){
		return (DeweyIdentifier)entries.keys.elementAt(0);
	}
	
	public DeweyIdentifier getHighestKey(){
		return (DeweyIdentifier)entries.keys.elementAt(entries.currentSize - 1);
	}
	
	public void setNextLeaf(Leaf nextLeaf){
		this.nextLeaf = nextLeaf;
	}
	
	public Leaf getNextLeaf(){
		
		if (nextLeaf != null){
			return nextLeaf;
		}
		else{
			if (0 < nextOffset){
				return null;
			}
			else{
//				try {
//					next = persister.readLeaf(nextOffset);
//					
//				} catch (IOException e) {
//					System.out.println("readLeaf(offset) could not read enough");
//					return null;
//				}
//				nextLeaf = next;
				return this.nextLeaf;
			}
		}
	}
	
	public Leaf getPrevLeaf(){
		
		if (prevLeaf != null){
			return prevLeaf;
		}
		else{
			if (0 < prevOffset){
				return null;
			}
			else{
//				try {
//					prev = persister.readLeaf(prevOffset);
//				} catch (IOException e) {
//					System.out.println("readLeaf(offset) could not read enough");
//					return null;
//				}
//				prevLeaf = prev;
				return this.prevLeaf;
			}
		}
	}
	
	public boolean isEqual(Leaf leaf){
		
		Vector thisKeys = this.entries.keys;
		Vector thisValues = this.entries.values;
		int thisKeySize = thisKeys.size();
		int thisValueSize = thisValues.size();
		
		Vector keys = leaf.entries.keys;
		Vector values = leaf.entries.values;
		int keySize = keys.size();
		int valueSize = values.size();
		
		if ((thisKeySize == keySize) && (thisValueSize == valueSize)){
			for (int i=0; i < keySize;i++){
				if ((((DeweyIdentifier)keys.elementAt(i)).compare((DeweyIdentifier)thisKeys.elementAt(i))!=0) || (((LinguisticToken)values.elementAt(i)).compareTo(((LinguisticToken)thisValues.elementAt(i)))!=0)){
					return false;
				}
			}
			return true;
		}
		return false;
	}
	

		
	public void getFirstKeyAfter(DeweyIdentifier key, Leaf[] inLeaf, int[] atPos) {

		// search in entries
		
		int continueSearch = entries.getFirstKeyAfter(key, atPos);
		
		if (continueSearch == LeafArrayMap.KEY_IN_THIS_LEAF){
			
			inLeaf[0] = this;
			return;
 		}
		
		Leaf currentLeaf = getNextLeaf();
		
		while (continueSearch != LeafArrayMap.KEY_IN_THIS_LEAF && currentLeaf != null) {
		
			// i.e. continueSearch == LeafArrayMap.CONTINUE_WITH_BINSEARCH
			
			continueSearch = currentLeaf.entries.getFirstKeyAfter(key, atPos);
	
			if (continueSearch == LeafArrayMap.KEY_IN_THIS_LEAF) {
				// this is the leaf we are looking for
				// the leaf traversal is gonna stop after this iteration
				inLeaf[0] = currentLeaf;
			}
			
			currentLeaf = currentLeaf.getNextLeaf();
		}
						
		// if no key bigger than the given one was found at the tree
		// inLeaf is null and atPos[0] == Btree.INVALID_KEY_INDEX
	
	}

	public void getLastKeyBefore(DeweyIdentifier key, Leaf[] inLeaf, int[] atPos) {

		// search in entries
		
		int continueSearch = entries.getLastKeyBefore(key, atPos);
		
		if (continueSearch == LeafArrayMap.KEY_IN_THIS_LEAF){
			
			inLeaf[0] = this;
			return;
 		}
		
		Leaf currentLeaf = getNextLeaf();
		
		while (continueSearch != LeafArrayMap.KEY_IN_THIS_LEAF && continueSearch != LeafArrayMap.KEY_IN_PREV_LEAF && currentLeaf != null) {
		
			// i.e. continueSearch == LeafArrayMap.CONTINUE_WITH_BINSEARCH
			
			continueSearch = currentLeaf.entries.getLastKeyBefore(key, atPos);
	
			if (continueSearch == LeafArrayMap.KEY_IN_THIS_LEAF) {
				// this is the leaf we are looking for
				// the leaf traversal is gonna stop after this iteration
				inLeaf[0] = currentLeaf;
				break;
			}

			if (continueSearch == LeafArrayMap.KEY_IN_PREV_LEAF) {
				// iterate to find first existing thing before that elements
				
				Leaf prevLeafje = currentLeaf.getPrevLeaf();
				
				while(prevLeaf.entries.currentSize == 0 ){
					
					prevLeafje = prevLeafje.getPrevLeaf();
				}
				
				inLeaf[0] = prevLeafje;
				atPos[0] = prevLeafje.entries.currentSize - 1;
				break;
			}
			
			currentLeaf = currentLeaf.getNextLeaf();
		}
						
		// if no key bigger than the given one was found at the tree
		// inLeaf is null and atPos[0] == Btree.INVALID_KEY_INDEX		
		
		return;
		
		/*
		getFirstKeyAfter(key, inLeaf, atPos);
		Leaf<K,V> prevLeaf = null; 
		
		
		if ((inLeaf[0] != null) && (atPos[0] != BTree.INVALID_KEY_INDEX)){
		
			if (atPos[0] != 0){
				
				atPos[0] = atPos[0]-1; 
				
			} else {
			
				// got to previous leaf whose size is not 0
				prevLeaf = this.getPrevLeaf();
				
				while(prevLeaf.entries.currentSize == 0 ){
					
					prevLeaf = prevLeaf.getPrevLeaf();
				}
				
				// get last element
				inLeaf[0] = prevLeaf;
				atPos[0] = prevLeaf.entries.currentSize - 1;
				
			}
	
		}
*/
		
		
	}

	
	
	public BTreeNode getBTreeNode(DeweyIdentifier key) {
		// TODO Auto-generated method stub
		return this;
	}
	
}

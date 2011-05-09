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

package ch.ethz.mxquery.datamodel;

import ch.ethz.mxquery.exceptions.MXQueryException;

public class DeweyIdentifier implements Identifier  {

	private int[] deweyIdentifier;
	private int dewey_level;
	private Source store;


	public DeweyIdentifier(Identifier lastID, int level, Source source) {
		DeweyIdentifier did = (DeweyIdentifier) lastID;
		store = source;
		int[] last_id;
		
		if (did.getDeweyLevel() < level){
			last_id = copyArray(did.getDeweyId(),1); 
		}
		else{
			last_id = copyArray(did.getDeweyId(),0); 
		}
		
		//if (level < last_id.length-1){
			if (last_id[level]!=0){
				int entry = last_id[level];
				// only odd numbers
				entry = entry+2;
				int new_entry = entry;
				last_id[level] = new_entry;
				deweyIdentifier = copyArray(remove(level,did.getDeweyLevel(),last_id),0);
				dewey_level = level;
			}
			else{
				last_id[level] = 1;
				deweyIdentifier = last_id;
				dewey_level = level;
			}
		}
	
		
//		else{
//			System.out.println("level out of bound");
//		}
		
	//}
	
	private int[] remove(int level, int old_level, int[] last_id) {

		int[] temp = new int[level+1];
		int i=0;
		if (old_level > level){
			while (i <= level){
				temp[i] = last_id[i];
				i++;
			}
			return temp;
		}
		else{
			return last_id;
		}

		
	}

	public DeweyIdentifier(int[] dewey_id2, int level, Source store2) {
			store = store2;
			deweyIdentifier = copyArray(clean(level,dewey_id2),0);
			dewey_level = level;
			
	}

	// all entries after level+1 need to be zero
	private int[] clean(int level, int[] updated_id){
		int i = level;
		i = i+1;
		int[] deweyId = updated_id;
		
		while (i < deweyId.length && updated_id[i] != 0){
			deweyId[i]=updated_id[i];
			i++;
		}
		return deweyId;
	}

	/**
	 * Compares this token identifier with an other one.
	 * 
	 * @param toCompare another Identifier to compare
	 * @return 0 if equal, -1 if <code>identifier</code> is smaller, 1 if
	 *         <code>identifier</code> is bigger, and -2 if the two Identifier
	 *         do not belong to the same data source.
	 */
	public int compare(Identifier toCompare) {
		if (toCompare == null) {
			throw new RuntimeException("Invalid node ID comparison");
		}
	
		if (toCompare.getStore() != this.store && toCompare.getStore().compare(store) != 0) {
			return toCompare.getStore().compare(store);
		}
		if (toCompare instanceof DeweyIdentifier) {
			DeweyIdentifier id = (DeweyIdentifier) toCompare;
			if (this.isSmallerThan(id)){
				return 1;
			}
			else if (id.isSmallerThan(this)){
				return -1;
			}
			else{
				return 0;
			}
		} else {
			throw new RuntimeException("Invalid node ID comparison");
		}
	}
	
	public int comparePosition(Identifier toCompare){
		if (toCompare == null) {
			throw new RuntimeException("Invalid node ID comparison");
		}
	
		if (toCompare.getStore() != this.store && toCompare.getStore().compare(store) != 0) {
			return toCompare.getStore().compare(store);
		}
		if (toCompare instanceof DeweyIdentifier) {
			DeweyIdentifier id = (DeweyIdentifier) toCompare;
			int[] did = id.getDeweyId();
			int position = did[id.getDeweyLevel()];
			
			if (this.getDeweyId()[getDeweyLevel()] < position){
				return -1;
			}
			else if (this.getDeweyId()[getDeweyLevel()] > position){
				return 1;
			}
			else{
				return 0;
			}
		} else {
			throw new RuntimeException("Invalid node ID comparison");
		}
	}

	/** returns the source of the identifier*/
	public Source getStore(){
		return this.store;
	}
	

	
	/** returns true if this is ancestor of did*/
	public boolean isAncestorOf(DeweyIdentifier did){
		
		int[] deweyId = copyArray(did.getDeweyId(),0);
		int l = did.getDeweyLevel();
		int i=0;
		
		// check which of the dewey ids is longer
		if (dewey_level > l){
			return false;
		}
		else{
			if (l == dewey_level){
				return false;
			}
			while (i <= dewey_level){
				if (deweyId[i] != deweyIdentifier[i]){
					// there are nodes careted in
					return false;
				}
				i++;
			}
			
			return true;
		}
		
	}
	
	/** returns true if this is parent (direct ancestor) of did*/
	public boolean isParentOf(DeweyIdentifier did){
		
		int idd = did.getDeweyLevel();
		int difference = (idd-dewey_level);
		
		if ((this.isAncestorOf(did)) && (difference == 1)){
			return true;
		}
		return false;
	}
	
	/** returns true if this is descendant of did*/
	public boolean isDescendantOf(DeweyIdentifier did){
		int[] deweyId = copyArray(did.getDeweyId(),0);
		int l = did.getDeweyLevel();
		int i=0;
		
		// check which of the dewey ids is longer
		if (dewey_level < l){
			return false;
		}
		else{
			while (i < l){
				if (deweyId[i] != deweyIdentifier[i]){
					return false;
				}
				i++;
			}
			
			if (dewey_level == l){
				return false;
			}
			
			return true;
		}
	}
	
	/** returns true if this is child (direct descendant) of did*/
	public boolean isChildOf(DeweyIdentifier did){
		
		int l = did.getDeweyLevel();
		int difference = (dewey_level-l);
		
		if ((this.isDescendantOf(did)) && (difference == 1)){
			return true;
		}
		return false;
	}

	/** returns true if this is smaller than did (order)*/
	public boolean isSmallerThan(DeweyIdentifier did){
		int l = did.getDeweyLevel();
		int[] deweyId = copyArray(did.getDeweyId(),0);
		
		int i= 0;
		if (l <= dewey_level ){
			while (i <= l){
				if (deweyIdentifier[i] < deweyId[i]){
					return true;
				}
				else if (deweyId[i] < deweyIdentifier[i]){
					return false;
				}
				i++;
			}
			return false;
		}
		else {
			
			while (i <= dewey_level){
				if (deweyIdentifier[i] < deweyId[i]){
					return true;
				}
				else if (deweyIdentifier[i] > deweyId[i]){
					return false;
				}
				i++;
			}
			return true;
			
		}
	}
	
	
	/** returns the Dewey Id of this*/
	public int[] getDeweyId(){
		return deweyIdentifier;
	}
	
	/** returns the length (level) of this */
	public int getDeweyLevel(){
		return dewey_level;
	}
	
	/**
	 * Creates an identifier for a particular store at a level starting from a previous identifier
	 * @param last_identifier
	 * @param level
	 * @param store
	 * @return a new Dewey Identifier
	 */
	public static Identifier createIdentifier(Identifier last_identifier, int level, Source store) {
		return new DeweyIdentifier(last_identifier,level,store);
	}
	

	
	/**
	 * Creates Identifiers for Tokens that are between the ids
	 * <code>leftBoundId</code> and <code>rightBoundId</code>.
	 * 
	 * @param leftBoundId
	 *            left bound
	 * @param rightBoundId
	 *            right bound
	 * @param number
	 *            number of ids that the method must produce
	 * @param source
	 *            data source of the newly created ids
	 * @return array of identifiers
	 */
	public static Identifier[] createInsertIdentifiers(Identifier leftBoundId,
			Identifier rightBoundId, int number, Source source, int[] levels)
			throws MXQueryException {
		
		int[] leftBound, rightBound;
		int leftLevel,rightLevel;
		Identifier[] newIds = new Identifier[number];
		int[] newId = null;
		int level = 0;
		
		// arbitrary ordpath insertion
		if ((leftBoundId != null) && (rightBoundId != null)){
			
			leftBound = ((DeweyIdentifier) leftBoundId).getDeweyId();
			leftLevel= ((DeweyIdentifier) leftBoundId).getDeweyLevel();
			rightBound = ((DeweyIdentifier) rightBoundId).getDeweyId();
			rightLevel= ((DeweyIdentifier) rightBoundId).getDeweyLevel();
			
			// right and left bound should be different
			if (((DeweyIdentifier) leftBoundId).compare(rightBoundId) == 0){
				return newIds;
			}
			
			// the nodes are siblings: caret in
			if (leftLevel == rightLevel){
				
				// check whether there is some space for "normal" dewey identifiers
				if ((rightBound[rightLevel]-leftBound[leftLevel]) <= 2) {
					int caret = (rightBound[rightLevel]+leftBound[leftLevel])>>> 1;
					
					// determine "root" of inserted node
					newId = copyArray(leftBound,0);
					level = leftLevel+1;
					newId[leftLevel] = caret;
					newId[level] = 1;
					newIds[0] = new DeweyIdentifier(newId,leftLevel+1,source);
				}
				else{
					// determine "root" of inserted node
					newId = copyArray(leftBound,0);
					level = leftLevel;
					newId[level] = (leftBound[leftLevel]+2);
					newIds[0] = new DeweyIdentifier(newId,leftLevel,source);
				}
			}
			// the nodes are siblings (but there is a caret that makes one dewey id longer)
			else{
				if (leftLevel < rightLevel){
					// determine "root" of inserted node
					int oldEntry = rightBound[rightLevel];
					oldEntry = oldEntry-2;
					newId = copyArray(rightBound,0);
					newId[rightLevel] = oldEntry;
					newIds[0] = new DeweyIdentifier(newId,rightLevel,source);
					level = rightLevel;
				}
				else{
					// determine "root" of inserted node
					int oldEntry = leftBound[leftLevel];
					oldEntry = oldEntry+2;
					newId = copyArray(leftBound,0);
					newId[leftLevel] = oldEntry;
					newIds[0] = new DeweyIdentifier(newId,leftLevel,source);	
					level = leftLevel;
				}
			}
		}
		else{
			if ((leftBoundId == null) && (rightBoundId != null)) {	
				// determine "root" of inserted node
				rightBound = ((DeweyIdentifier) rightBoundId).getDeweyId();
				rightLevel= ((DeweyIdentifier) rightBoundId).getDeweyLevel();
				int oldEntry = rightBound[rightLevel];
				oldEntry = oldEntry-2;
				newId = copyArray(rightBound,0);
				newId[rightLevel] = oldEntry;
				newIds[0] = new DeweyIdentifier(newId,rightLevel,source);
				level = rightLevel;
			} 
			// the node is inserted to the right of a set of existing siblings: go on with the siblings numbering
			else if ((rightBoundId == null) && (leftBoundId != null)) {
				leftBound = ((DeweyIdentifier) leftBoundId).getDeweyId();
				leftLevel= ((DeweyIdentifier) leftBoundId).getDeweyLevel();
				int oldEntry = leftBound[leftLevel+1];
				if (oldEntry == 0){
					oldEntry = 1;
				}
				else{
					oldEntry= oldEntry +2;
				}
				newId = copyArray(leftBound,0);
				newId[leftLevel+1] = oldEntry;
				newIds[0] = new DeweyIdentifier(newId,leftLevel+1,source);	
				level = leftLevel+1;
				
			}
			// no bounds: no nodes in the document ?
			else{
				newId = new int[50];
				newId[0] = 1;
				newIds[0] = new DeweyIdentifier(newId,1,source);
			}
		}
		
		// determine the rest of the dewey ids
		int[] did = copyArray(newId,0);
		int idsCounter = 1;
		for (int i=1; i < levels.length; i++){
			if (levels[i] == 1){
				int oldEntry = did[++level];
				int newEntry = 0;
				if (oldEntry == 0){
					newEntry = 1;
				}
				else{
					newEntry=oldEntry+2;
				}
				did[level] = newEntry;
				newIds[idsCounter] = new DeweyIdentifier(did,level,source);
				idsCounter++;
			}
			else if (levels[i] == -1){
				level--;
			}
		}
		return newIds;
		
	}
		
	public void dumpID(){
		
		for (int i=0; i< deweyIdentifier.length; i++){
			System.out.print(deweyIdentifier[i]);
		}
		System.out.println("new id");
		
	}
	
	private static int[] copyArray(int[] id, int l){
		int[] s = new int[id.length+l];
		for (int i=0; i< id.length; i++){
			s[i] = id[i];
		}
		return s;
	}
	
	
	
//	public DeweyIdentifier copy(Context ctx) throws MXQueryException {
//		return new DeweyIdentifier((int[]) deweyIdentifier.clone(), dewey_level, store.copySource(ctx, nestedPredCtxStack));
//	}	
}

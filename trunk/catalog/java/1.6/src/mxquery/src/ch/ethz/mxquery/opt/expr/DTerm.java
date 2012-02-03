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
package ch.ethz.mxquery.opt.expr;


import ch.ethz.mxquery.exceptions.MXQueryException;

/**
 * Represents a disjunction term, with consists of conjunction terms - needed for the max indexes
 * @author Tim Kraska (<a href="mailto:mail@tim-kraska.de">mail@tim-kraska.de</a>)
 *
 */
public final class DTerm  extends Term{

	
	public DTerm(){
		
	}
	

	/**
	 * Adds a new conjunction term 
	 * @param cList
	 */
	public void addCTerm(CTerm cList){
		super.add(cList);
	}
	
	/**
	 * Combines two disjunctin terms to one
	 * @param dList
	 */
	public void addAll(DTerm dList){
		super.addAll(dList);
	}
	
	/**
	 * Returns the conjunction term at position i
	 * @param i 
	 * @return the i-th CTerm
	 */
	public CTerm getCTerm(int i){
		return (CTerm) get(i);
	}
	
	/**
	 * Sortts the conjunctions terms according to the dependencies
	 */
	public void sort() {
		for(int i = 0; i < size();i++){
			getCTerm(i).sort();
		}
		super.sort();
	}
	
	
	/**
	 * Merges two dList in the form that every element from one list is combined with every element in the
	 * second list. If one of the lists is empty, the other list is returned;
	 * @param l2
	 * @return the combination of the lists
	 */
	public DTerm combineLists(DTerm l2){
		if(size() == 0){
			return l2 ;
		}else if(l2.size() == 0){
			return this;
		}
		DTerm dResult = new DTerm();
		for(int i = 0; i < size();i++){
			CTerm cList1 = getCTerm(i);
			for(int n = 0; n < l2.size(); n++){
				CTerm cResult= new CTerm();
				CTerm cList2 = l2.getCTerm(n);
				cResult.addAll(cList1);
				cResult.addAll(cList2);
				dResult.addCTerm(cResult); 
			}
		}
		return dResult;
	}

	
	/**
	 * Checks the condition and also removes false literals
	 */
	public int evaluate(int level) throws MXQueryException{
		if(result == RESULT_UNKNOWN){
			if(isLowerDependency(this.currentEvalLevel, level)){
				currentEvalLevel = level;
				result = RESULT_FALSE;
				for (int i = 0; i < list.size(); i++) {
					int tmpResult = this.get(i, list).evaluate(level);
					if (tmpResult == RESULT_TRUE) {
						this.result = RESULT_TRUE;
						return result;
					} else if (tmpResult == RESULT_UNKNOWN) {
						this.result = RESULT_UNKNOWN;
					}
				}
			}
		}
		return result;
	}
	
	/**
	 * Resets the evaluation of the conjunction term up to a certain level
	 * of dependency
	 */
	public int reset(int level){
		if(isLowerDependency(level, currentEvalLevel)){
			currentEvalLevel = level;
			this.result = RESULT_FALSE;
			for(int i = 0; i < size();i++){
				int tmpResult = get(i).reset(level);
				if (tmpResult == RESULT_TRUE) {
					this.result = RESULT_TRUE;
				} else if (tmpResult == RESULT_UNKNOWN && tmpResult != RESULT_TRUE) {
					this.result = RESULT_UNKNOWN;
				}
			}
			return result;
		}
		return result;
	}


}

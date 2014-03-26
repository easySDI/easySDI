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
 * This term presents a conjunction term. 
 * It also implements the logic for conjunctions to allow short circuit
 * @author Tim Kraska (<a href="mailto:mail@tim-kraska.de">mail@tim-kraska.de</a>)
 *
 */
public final class CTerm extends Term{

	public boolean indexed;
	
	private boolean completeIndexable=false;
	
	
	public CTerm() {

	} 
	
	/**
	 * Returns if this conjunction term is indexed
	 * @return true if indexed
	 */
	public boolean isIndexed() {
		return indexed;
	}

	/**
	 * Sets if this conjunction term is indexed
	 */
	public void setIndexed(boolean indexed) {
		this.indexed = indexed;
	}

	
	/**
	 * Adds a new literal to the conjunction term
	 * @param literal
	 */
	public void addLiteral(Literal literal) {
		this.add(literal);
	}

	/**
	 * Returns the literal from position i from this conjunction term 
	 * @param i position of the literal
	 */
	public Literal getLiteral(int i) {
		return  (Literal) super.get(i);
	}
	
	/**
	 * Evaluates the conjunction term up to a certain dependency level. 
	 * Additional this evaluate makes use of short circuit
	 */
	public int evaluate(int level) throws MXQueryException{
		//If this conjunction term is indexed - we retrieve the value
		//in a different way!
		if(indexed && level == DEPENDENCY_STARTEND){
			return RESULT_FALSE;
		}
		
		if (this.result == RESULT_UNKNOWN) {
			if(isLowerDependency(this.currentEvalLevel, level)){
				currentEvalLevel = level;
				this.result = RESULT_TRUE;
				for (int i = 0; i < size(); i++) {
					int tmpResult = get(i).evaluate(level);
					if (tmpResult == RESULT_FALSE) {
						this.result = RESULT_FALSE;
						return result;
					} else if (tmpResult == RESULT_UNKNOWN) {
						this.result = RESULT_UNKNOWN;
					}
				}
			}
		}
		return result;
	}
	

	public int reset(int level){
		if(isLowerDependency(level, currentEvalLevel)){
			currentEvalLevel = level;
			this.result = RESULT_TRUE;
			for(int i = 0; i < size();i++){
				int tmpResult = get(i).reset(level);
				if (tmpResult == RESULT_FALSE) {
					this.result = RESULT_FALSE;
				} else if (tmpResult == RESULT_UNKNOWN && tmpResult != RESULT_FALSE) {
					this.result = RESULT_UNKNOWN;
				}
			}
			return result;
		}
		return result;
	}
	
	/*
	 * Returns an index Schema for a conjunction term because we apply max indexes
	 */
	public CompareLiteralIndexSchema getIndexSchema(int indexNb){
		completeIndexable=true;
		if(isIndexable()){
			CompareLiteralIndexSchema schema = new CompareLiteralIndexSchema(indexNb);
			for(int i = 0; i < size(); i++){
				Literal literal = getLiteral(i);
				if(literal instanceof CompareLiteral){
					CompareLiteral cLit = (CompareLiteral) literal;
					if(cLit.isIndexable()){
						schema.registerValue(cLit);
					}else{
						completeIndexable=false;
					}
				}else{
					completeIndexable=false;
				}
			}
			return schema;
		}else{
			completeIndexable=false;
			return null;
		}
	}
	
	/**
	 * Checks if the CTerm is complete indexable
	 * @return true if all components can be indexed, false otherwise
	 */
	public boolean isCompleteIndexable(){
		//This is just a trick to make sure, that the completeIndexable is set.
		//If the value is true, it has to be set already
		if(!completeIndexable){
			getIndexSchema(1);
		}
		return completeIndexable;
	}

	/**
	 * Evaluates the expression up to the given level and returns if shortCircuit should be done
	 * @param level
	 * @param list
	 * @return
	 * @throws MXQueryException
	 */
	protected boolean checkResult(int level) throws MXQueryException {
		for (int i = 0; i < size(); i++) {
			int tmpResult = get(i).evaluate(level);
			if (tmpResult == RESULT_FALSE) {
				this.result = RESULT_FALSE;
				return true;
			} else if (tmpResult == RESULT_UNKNOWN) {
				this.result = RESULT_UNKNOWN;
			}
		}
		return false;
	}



}

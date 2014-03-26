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

import java.util.ArrayList;
import java.util.Collections;
import java.util.List;

import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.util.KXmlSerializer;

/**
 * A term for the optimization. A term consits again of logical units, which can
 * be in turn again terms, literal etc.
 * @author Tim Kraska (<a href="mailto:mail@tim-kraska.de">mail@tim-kraska.de</a>)
 *
 */
public abstract class Term extends LogicalUnit{
	
	protected List list = new ArrayList();
	
	private boolean sorted = false;
	
	public LogicalUnit get(int i){
		return (LogicalUnit) list.get(i);
	}
	
	protected LogicalUnit get(int i, List l){
		return (LogicalUnit) l.get(i);
	}
	
	/**
	 * Adds a list of terms into this term. 
	 * @param term
	 */
	public void addAll(Term term) {
		if (sorted) {
			throw new RuntimeException("The List is already sorted");
		}
		for (int i = 0; i < term.size(); i++) {
			add(term.get(i));
		}
	}
	
	/**
	 * Returns the number of logical units in this term
	 * @return the number of logical units
	 */
	public int size(){
		return list.size();
	}
	
	/**
	 * Adds a new logical unit to the term
	 * @param unit
	 */
	public void add(LogicalUnit unit){
		if (sorted) {
			throw new RuntimeException("The List is already sorted");
		}
		setIndexable(unit.indexable);
		this.addDependency(unit.dependency);
		list.add(unit);
	}
	
	/**
	 * Sorts the result list according to the used variables
	 * 
	 */
	public void sort() {
		Collections.sort(list);
		sorted = true;
	}
	

	public abstract int evaluate(int level) throws MXQueryException;
	

	public abstract int reset(int level);

	
	/**
	 * Creates a copy.
	 */
	public Object clone() {
		Term newCTerm = (Term) super.clone();
		/*
		 * //deep copy newCTerm.literals = new ArrayList(); for(int i = 0; i<
		 * literals.size();i++){ Literal copyLiteral =
		 * (Literal)getLiteral(i).clone(); newCTerm.addLiteral(copyLiteral); }
		 */
		return newCTerm;
	}
	
	public KXmlSerializer traverse(KXmlSerializer serializer){
		createStartTag(serializer);
		for (int i = 0; i < size(); i++) {
			LogicalUnit unit = get(i);
			unit.traverse(serializer);
		}
		createEndTag(serializer);
		return serializer;
		
	}

}

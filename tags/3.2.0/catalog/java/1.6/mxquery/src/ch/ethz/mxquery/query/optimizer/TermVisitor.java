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
package ch.ethz.mxquery.query.optimizer;

import java.util.List;

import ch.ethz.mxquery.functions.fn.DataValuesIterator;
import ch.ethz.mxquery.iterators.AttributeIterator;
import ch.ethz.mxquery.iterators.ChildIterator;
import ch.ethz.mxquery.iterators.VariableIterator;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.opt.expr.Literal;
import ch.ethz.mxquery.opt.expr.LogicalUnit;

/**
 * This is a helper class to traverse a term and find the dependencies on variables
 * @author Tim Kraska (<a href="mailto:mail@tim-kraska.de">mail@tim-kraska.de</a>)
 *
 */
public final class TermVisitor {
	private List startVars;
	private List endVars;
	
	private String columnName;
//	private String stepNames;
//	
//	private boolean onlyStepsAndVars=true;
	
	
	private boolean dependsOnStartVars=false;
	
	private boolean dependsOnEndVars=false;

	/**
	 * Creates a new term visitor, to identify the dependencies on variables
	 * @param term
	 * @param startVars
	 * @param endVars
	 */
	public TermVisitor(Literal term, List startVars, List endVars) {
		super();
		this.startVars = startVars;
		this.endVars = endVars;
		this.columnName = getColumnName(term.getIter());
		checkVars(term.getIter());
		
	}
	
	
	public TermVisitor(Literal term, List startVars) {
		this(term, startVars, null);
	}

	
	/**
	 * Checks the iterator according the variables used in the iterator
	 * @param expr
	 */
	protected void checkVars(XDMIterator expr){
		if(expr == null){
			return;
		}
		XDMIterator[] subIters = expr.getAllSubIters();
		for(int i = 0; i < subIters.length; i++){
			VariableIterator varIter = getVariableIterator(subIters[i]);
			if(varIter != null){
				if(dependsOnVars(varIter, startVars)){
					dependsOnStartVars=true;
				}
				if(dependsOnVars(varIter, endVars)){
					dependsOnEndVars=true;
				}
				//if both is true, we dont need to lock further
				if(dependsOnStartVars && dependsOnEndVars){
					return;
				}
			}else{
				checkVars(subIters[i]);
			}
		}
	}
	
	/**
	 * Tries to create a meaningful column name which can be used by the index
	 * to discover overlapping indexes
	 * @param expr
	 * @return
	 */
	private String getColumnName(XDMIterator expr){
		XDMIterator iter = removeDataValuesIterator(expr);

		String stepName = null;
		ChildIterator childIter = getChildIterator(iter);
		XDMIterator nextIter=null;
		if (childIter != null) {
			stepName = childIter.getStepName();
			nextIter = childIter.getAllSubIters()[0];
		}
		AttributeIterator attrIter = getAttributeIterator(iter);
		if (attrIter != null) {
			stepName = attrIter.getAttrStep();
			nextIter = attrIter.getAllSubIters()[0];
		}

		if (stepName != null) {
			String nextStep = getColumnName(nextIter);
			if (nextStep != null) {
				return nextStep + "/" + stepName;
			} else {
				return stepName;
			}
		}

		VariableIterator varIter = getVariableIterator(iter);
		if (varIter != null) {
			if (varIter.getVarQName().getLocalPart().equals(".")) {
				return null;
			} else {
				return varIter.getVarQName().getLocalPart();
			}
		}
			
		//onlyStepsAndVars = false;
		return null;
	}
	
	/**
	 * Removes a DataValuesIterator from expr
	 * @param expr
	 * @return
	 */
	private XDMIterator removeDataValuesIterator(XDMIterator expr){
		if(expr instanceof DataValuesIterator){
			expr = ((DataValuesIterator)expr).getAllSubIters()[0];
			return removeDataValuesIterator(expr);
		}else{
			return expr;
		}
	}
	
	/**
	 * Returns a child iterator if expr is a ChildIterator, otherwise null
	 * @param expr
	 * @return
	 */
	private ChildIterator getChildIterator(XDMIterator expr){
		if (expr instanceof ChildIterator){
			return (ChildIterator)expr;
		}else{
			return null;
		}
	}
	
	/**
	 * Returns a attribute iterator if expr is a AttributeIterator, otherwise null
	 * @param expr
	 * @return
	 */
	private AttributeIterator getAttributeIterator(XDMIterator expr){
		if (expr instanceof AttributeIterator){
			return (AttributeIterator)expr;
		}else{
			return null;
		}
	}
	
	/**
	 * Returns a variable iterator if expr is a VariableIterator, otherwise null
	 * @param expr
	 * @return
	 */
	private VariableIterator getVariableIterator(XDMIterator expr){
		if (expr instanceof VariableIterator){
			return (VariableIterator)expr;
		}else{
			return null;
		}
	}
	
	/**
	 * Checks if the variable is used in the window variabe list
	 * @param varIter The variable iterator 
	 * @param windowVars The window variable to check 
	 * @return
	 */
	private boolean dependsOnVars(VariableIterator varIter, List windowVars){
		if(windowVars != null){
			java.util.Iterator iter = windowVars.iterator();
			while(iter.hasNext()){
				VarSearchTerms var = (VarSearchTerms)iter.next();
				if(var.compareQNames(varIter.getVarQName())){
					return true;
				}
			}
		}
		return false;
	}

	public int getDependency() {
		return LogicalUnit.getDependencyNb(this.dependsOnStartVars, this.dependsOnEndVars);
	}

	public String getColumnName() {
		return columnName;
	}


}

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

import java.util.ArrayList;
import java.util.List;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.iterators.VariableIterator;
import ch.ethz.mxquery.model.WindowVariable;

/**
 * Just a wrapper class for searching for specific variables. 
 * @author Tim Kraska (<a href="mailto:mail@tim-kraska.de">mail@tim-kraska.de</a>)
 *
 */
public final class VarSearchTerms {
	public static final QName CONTEXT_ITEM = Context.CONTEXT_ITEM;
	public static final QName ALL_VARS_EXCEPT_CONTEXT_ITEM = new QName("*","*"); 
	public static final QName ALL_VARS = new QName("**","**"); 
	
	private QName varName;
	
	/**
	 * Creates a new VarSearchTerm. Additional also  ALL_VARS_EXCEPT_CONTEXT_ITEM 
	 * and ALL_VARS can be used as valid VarSearchTerms
	 * @param varName
	 */
	public VarSearchTerms(QName varName){
		this.varName = varName;
	}
	
	/**
	 * Compares two QNames.
	 * @param var
	 * @return true if equals under the given comparison settings
	 */
	public boolean compareQNames(QName var){
		if(this.varName == ALL_VARS_EXCEPT_CONTEXT_ITEM){
			return !var.equals(CONTEXT_ITEM);
		}else if (this.varName == ALL_VARS_EXCEPT_CONTEXT_ITEM){
			return true;
		}else{
			return var.equals(varName);
		}
	}
	
	/**
	 * Compare the variable with the QName in the object
	 * @param varIter
	 * @return the comparison result of the QNames
	 */
	public boolean compareVariables(VariableIterator varIter){
		return compareQNames(varIter.getVarQName());
	}
	
	/**
	 * Factory to create new VarSearchTerms from a variable
	 * @param varIter
	 * @return A VarSearchTerm based on the variable names
	 */
	public static VarSearchTerms createVarSearchTerms(VariableIterator varIter){
		return new VarSearchTerms(varIter.getVarQName());
	}
	
	/**
	 * Creates VarSearchTerms from a list of Variable's
	 * @param windowVars
	 * @return a list of VarSearchTerms for the variables
	 */
	public static List createVarSearchTerms(WindowVariable[] windowVars){
		List list = new ArrayList();
		for(int i = 0; i < windowVars.length; i++){
			list.add(new VarSearchTerms(windowVars[i].getQName()));
		}
		return list;
	}
	
}

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

package ch.ethz.mxquery.contextConfig;

public class CompilerOptions {

	private boolean scripting = false;
	private boolean fulltext = false;
	private boolean update = false;
	private boolean schemaAwareness = false;
	private boolean xquery11 = false;
	private boolean continuousXQ = false;
	private boolean parallelExecution = false;
	
	/**
	 * Check if XQuery 1.1 expressions should be considered 
	 * @return true if yes, false if not
	 */
	public boolean isXquery11() {
		return xquery11;
	}
	/**
	 * Determine if XQuery 1.1 expressions should be used
	 * @param xquery11 true: use XQuery 1.1 expressions, false: don't use
	 */
	public void setXquery11(boolean xquery11) {
		this.xquery11 = xquery11;
	}
	/**
	 * Check if Continuous XQuery expressions (infinite XDM sequences) should be used
	 * @return true if infinite XQuery expressions should be used
	 */
	public boolean isContinuousXQ() {
		return continuousXQ;
	}
	/**
	 * Determine if Continuous XQuery expressions (infinite XDM) should be used
	 * @param continuousxq true: use Continuous XQuery expressions, false: don't use
	 */
	public void setContinuousXQ(boolean continuousxq) {
		this.continuousXQ = continuousxq;
	}
	/**
	 * Check if XQuery Scripting expressions should be considered
	 * @return true if yes, false if not
	 */
	public boolean isScripting() {
		return scripting;
	}
	/**
	 * Determine if XQuery Scripting expressions should be used
	 * @param scripting true: use XQuery scripting expressions, false: don't use
	 */
	public void setScripting(boolean scripting) {
		this.scripting = scripting;
		this.update = true;
	}
	/**
	 * Check if XQuery/XPath Full Text expressions should be considered
	 * @return true if yes, false if not
	 */
	public boolean isFulltext() {
		return fulltext;
	}
	/**
	 * Determine if XQuery/XPath Full Text expressions should be used
	 * @param fulltext true: use XQuery/XPath Full Text expressions, false don't
	 */
	public void setFulltext(boolean fulltext) {
		this.fulltext = fulltext;
	}
	/**
	 * Check if XQuery Update Facility expressions should be considered
	 * @return true if yes, false if not
	 */
	public boolean isUpdate() {
		return update;
	}
	/**
	 * Determine if XQuery Update Facility expressions should be used
	 * @param update true: use XQuery Update Facility expressions, false don't
	 */
	public void setUpdate(boolean update) {
		this.update = update;
	}
	/**
	 * Check if XML Schema related expressions should be use
	 * @return true if yes, false if not
	 */
	public boolean isSchemaAwareness() {
		return schemaAwareness;
	}
	/**
	 * Determine if XML Schema related expressions should be used
	 * @param schemaAwareness true: use XML Schema related expressions, false don't
	 */
	public void setSchemaAwareness(boolean schemaAwareness) {
		this.schemaAwareness = schemaAwareness;
	}
	/**
	 * Check if parallel execution should be used (experimental)
	 * @return true if yes, false if not
	 */
	public boolean isParallelExecution() {
		return parallelExecution;
	}
	/**
	 * Determine if parallel execution should be used (experimental)
	 * @param parallelExecution true: use parallel forseq/groupby, false don't
	 */
	public void setParallelExecution(boolean parallelExecution) {
		this.parallelExecution = parallelExecution;
	}	
	
}

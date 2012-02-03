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

package ch.ethz.mxquery.cmdline;

public class LocalSetting {
	
	private String input;			// Can be a query (inline or from file) or a plan file.
	private boolean fromPlan;		// Flag: is input a query plan?
	
	private String output;
	private boolean discardResult;	// Flag: discard the result?
	private boolean toFile;			// Flag: print result to file? (if not print to standard out)
	
	private boolean updateFiles;
	private boolean backupBeforeUpdate;
	
	private boolean explain;
	private boolean serializePlan;
	private String 	serializationPlan;
	private boolean printStores;
	private boolean timing;
	
	
	public String getInput() {
		return input;
	}
	public void setInput(String str) {
		this.input = str;
	}
	public boolean isFromPlan() {
		return fromPlan;
	}
	public void setIsFromPlan(boolean bool) {
		fromPlan = bool;
	}
	
	public String getOutput() {
		return output;
	}
	public void setOutput(String str) {
		output = str;
	}
	public boolean isDiscardResult() {
		return discardResult;
	}
	public void setIsDiscardResult(boolean bool) {
		discardResult = bool;
	}
	public boolean isToFile() {
		return toFile;
	}
	public void setIsToFile(boolean bool) {
		toFile = bool;
	}	
	
	public boolean isExplain() {
		return explain;
	}
	public void setIsExplain(boolean bool) {
		explain = bool;
	}
	public boolean isSerializePlan() {
		return serializePlan;
	}
	public void setIsSerializePlan(boolean bool) {
		serializePlan = bool;
	}
	public String getSerializationPlan() {
		return serializationPlan;
	}
	
	public void setSerializationPlan(String s) {
		serializationPlan = s;
	}
	
	public boolean isPrintStores() {
		return printStores;
	}
	public void setIsPrintStores(boolean bool) {
		printStores = bool;
	}
	public boolean isTiming() {
		return timing;
	}
	public void setIsTiming(boolean bool) {
		timing = bool;
	}

	public boolean isUpdateFiles() {
		return updateFiles;
	}
	public void setUpdateFiles(boolean updateFiles) {
		this.updateFiles = updateFiles;
	}
	public boolean isBackupBeforeUpdate() {
		return backupBeforeUpdate;
	}
	public void setBackupBeforeUpdate(boolean backupBeforeUpdate) {
		this.backupBeforeUpdate = backupBeforeUpdate;
	}
	public void printDebug() {
		System.out.println("############################ verbose ############################");
		// input
		System.out.print("Input mode: ");
		if(!fromPlan)
			System.out.println("Query");
		else
			System.out.println("Plan");
		System.out.println(input+"\n==============================");
		// output
		System.out.print("Output mode: ");
		if(discardResult)
			System.out.println("Discard");
		else if(!toFile)
			System.out.println("Standard out \n==============================");
		else 
			System.out.println("File (" + output + ") \n==============================");
		// print options
		System.out.println("Print Options:");
		System.out.println("Explain: " + explain);
		
		if (serializePlan) {
			System.out.println("Serialize Plan: " + serializationPlan);
		} else {
			System.out.println("Serialize Plan: " + serializePlan);
		}
		System.out.println("Print Stores: " + printStores);
		System.out.println("Timing: " + timing +"\n==============================");
		System.out.println("############################ verbose ############################");
	
	}
}

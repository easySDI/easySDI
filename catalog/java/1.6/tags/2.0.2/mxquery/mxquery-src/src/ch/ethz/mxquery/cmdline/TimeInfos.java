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

/**
 * Used to return parsing and execution time informations
 * 
 * @author dagraf
 *
 */
public class TimeInfos {
	private long preparationStart = 0, preparationEnd = 0;
	private long compileStart = 0, compileEnd = 0;
	private long execStart = 0, execEnd = 0;
	
	public long getExecEnd() {
		return execEnd;
	}
	public void setExecEnd(long execEnd) {
		this.execEnd = execEnd;
	}
	public long getExecStart() {
		return execStart;
	}
	public void setExecStart(long execStart) {
		this.execStart = execStart;
	}
	public long getCompileEnd() {
		return compileEnd;
	}
	public void setCompileEnd(long parsingEnd) {
		this.compileEnd = parsingEnd;
	}
	public long getCompileStart() {
		return compileStart;
	}
	public void setCompileStart(long parsingStart) {
		this.compileStart = parsingStart;
	}
	public long getPreparationEnd() {
		return preparationEnd;
	}
	public void setPreparationEnd(long preparationEnd) {
		this.preparationEnd = preparationEnd;
	}
	public long getPreparationStart() {
		return preparationStart;
	}
	public void setPreparationStart(long preparationStart) {
		this.preparationStart = preparationStart;
	}
	
	
}

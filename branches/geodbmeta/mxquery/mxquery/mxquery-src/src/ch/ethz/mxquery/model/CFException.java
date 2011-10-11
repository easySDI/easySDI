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
package ch.ethz.mxquery.model;

import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;

/**
 * Control Flow Exception <br/>
 * used to handle breaks, continues, and early returns
 * @author dagraf
 *
 */
public class CFException extends MXQueryException {
	/**
	 * 
	 */
	private static final long serialVersionUID = 1L;
	public final static int CF_BREAK = 1;
	public final static int CF_CONTINUE = 2;
	public final static int CF_EARLY_RETURN = 3;
	
	private int type;
	private transient Window returnValue;
	
	public CFException(int type, Window returnValue) {
		super(ErrorCodes.A0012_EC_XQUERYP_EXCEPTION, "#", null);
		this.type = type;
		this.returnValue = returnValue;
	}
	
	public CFException(int type) {
		super(ErrorCodes.A0012_EC_XQUERYP_EXCEPTION, "#", null);
		this.type = type;
	}
	
	public boolean isBreak() {
		return (this.type == CFException.CF_BREAK);
	}
	
	public boolean isContinue() {
		return (this.type == CFException.CF_CONTINUE);
	}
	
	public boolean isEarlyReturn() {
		return (this.type == CFException.CF_EARLY_RETURN);
	}
	
	public Window getReturnValue() {
		return this.returnValue;
	}
}

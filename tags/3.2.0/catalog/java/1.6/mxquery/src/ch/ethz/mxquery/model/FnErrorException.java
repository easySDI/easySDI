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

import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.QueryLocation;

/**
 * TODO: Replace by fn:error
 * @author David Graf
 *
 */
public class FnErrorException extends DynamicException {
	private transient XDMIterator errorObject = null;
	/**
	 * Represents an Exceptions that is thrown from the user in the xqueryp
	 * code.
	 */
	private static final long serialVersionUID = 1L;

	public FnErrorException(QueryLocation loc) {
		super(ErrorCodes.F0001_UNIDENTIFIED_ERROR, "fn:error", loc);		
	}
	
	public FnErrorException(QName qname, QueryLocation loc) {
		super(qname.toString(), "fn:error", loc);
	}
	
	public FnErrorException(QName qname, String description, QueryLocation loc) {
		super(qname.toString(), description, loc);
	}
	
	public FnErrorException(QName qname, String description, XDMIterator errorObject, QueryLocation loc) {
		super(qname.toString(), description, loc);
		this.errorObject = errorObject;
	}
	
	public void printStackTrace() {
		System.err.print("fn:error invoked! ");
		if (this.getErrorCode() != null) {
			System.err.print(" Error Code:'" + this.getErrorCode() + "'");
		}
		if (this.getMessage() != null) {
			System.err.print(" Message:'" + this.getMessage() + "'");
		}
		System.err.println();
	}
	
	public XDMIterator getErrorObject() {
		return this.errorObject;
	}
}

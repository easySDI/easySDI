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

package ch.ethz.mxquery.exceptions;


public class MXQueryException extends Exception {
	private static final long serialVersionUID = -8905382788994553201L;
	private static final String FORWARDED = "FORWARDED EXCEPTION! To get the original Exception, use getCause()!";	

	protected String code;
	protected String message;
	private Throwable cause = this;
	protected QueryLocation loc;
	
	public MXQueryException(String code, Throwable cause, String message, QueryLocation location) {
		super(FORWARDED);
		this.code = code;
		initCause(cause);
		this.message = message;
		loc = location;
	}
	
	public MXQueryException(String code, String message, QueryLocation location) {
		super("[" + code + "]: " + message);
		this.code = code;
		this.message = message;
		loc = location;
	}
	
	
	public String getErrorCode() {
		return code;
	}
	
	public String getMessage() {
		return message;
	}

	public Throwable getCause() {
		return (cause==this ? null : cause);
	}
	
	public Throwable initCause(Throwable cause) {
		if (this.cause != this) {
			throw new IllegalStateException("Can't overwrite cause");
		}
		if (cause == this) {
			throw new IllegalArgumentException("Self-causation not permitted");
		}
		
		this.cause = cause;
	    return this;
	}
	/**
	 * Return the location in the query "text" where this exception originated"
	 * @return The QueryLocation object representing the location
	 */
	public QueryLocation getLocation() {
		if (loc != null)
			return loc;
		else 
			return QueryLocation.OUTSIDE_QUERY_LOC;
	}	
	
	public static void printErrorPosition(String query, QueryLocation loc){
		if (loc == QueryLocation.OUTSIDE_QUERY_LOC) {
			System.err.println("Error position could not be determined within the query");
			return; 
		}
		int position = loc.getStartIndex();
		System.err.println("Before the error position:");
		for(int i =0;i< position;i++){
			System.err.print(query.charAt(i));
		}
		System.err.println("");
		System.err.println("Likely error position:");
		for(int i =position;i< query.length();i++){
			System.err.print(query.charAt(i));
		}
		System.err.println("");
	}
	
	public static String getErrorPosition(String query, QueryLocation loc) {
		if (loc == QueryLocation.OUTSIDE_QUERY_LOC) {
			return "Error position could not be determined within the query";
		}

		int position = loc.getStartIndex();
		String str = "Before the error position:\n";
		for(int i =0;i< position;i++){
			str += query.charAt(i);
		}
		str += "\n";
		str += "Likely error position:\n";
		for(int i =position;i< query.length();i++){
			str += query.charAt(i);
		}
		str += "\n";
		return str;
	}
	
}

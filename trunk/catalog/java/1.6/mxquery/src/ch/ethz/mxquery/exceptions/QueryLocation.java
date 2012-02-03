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

/**
 * Represents a textual location/region in an XQuery file
 * @author Peter M. Fischer
 *
 */
public class QueryLocation{

	/**
	 * 
	 */
	//private static final long serialVersionUID = -317458756322902272L;
	private int flatIndexStart;
	private int flatIndexEnd;
	private int lineBegin;
	private int lineEnd;
	private int columnBegin;
	private int columnEnd;
	private String file;
	
	public static final QueryLocation OUTSIDE_QUERY_LOC = new QueryLocation();
	
	public QueryLocation(){flatIndexEnd = -1; flatIndexStart = -1;};
	/**
	 * Generate a location based on the flat character indexes
	 * @param indexStart
	 * @param indexEnd
	 */
	public QueryLocation(int indexStart, int indexEnd) {
		flatIndexStart = indexStart;
		flatIndexEnd = indexEnd;
	}
	
	public QueryLocation(int lineBegin, int lineEnd, int columnBegin,
			int columnEnd, String file) {
		super();
		this.lineBegin = lineBegin;
		this.lineEnd = lineEnd;
		this.columnBegin = columnBegin;
		this.columnEnd = columnEnd;
		this.file = file;
	}
	
	public int getLineBegin() {
		return lineBegin;
	}
	public void setLineBegin(int lineBegin) {
		this.lineBegin = lineBegin;
	}
	public int getLineEnd() {
		return lineEnd;
	}
	public void setLineEnd(int lineEnd) {
		this.lineEnd = lineEnd;
	}
	public int getColumnBegin() {
		return columnBegin;
	}
	public void setColumnBegin(int columnBegin) {
		this.columnBegin = columnBegin;
	}
	public int getColumnEnd() {
		return columnEnd;
	}
	public void setColumnEnd(int columnEnd) {
		this.columnEnd = columnEnd;
	}
	public String getFile() {
		return file;
	}
	public void setFile(String file) {
		this.file = file;
	}
	
	public int getStartIndex() {
		return flatIndexStart;
	}
	
	public int getEndIndex() {
		return flatIndexEnd;
	}
}

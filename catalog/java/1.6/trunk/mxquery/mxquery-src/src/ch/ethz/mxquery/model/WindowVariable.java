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
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;

public class WindowVariable {
	public static final int WINDOW_VAR_POSITION=0;
	public static final int WINDOW_VAR_CUR_ITEM=1;
	public static final int WINDOW_VAR_NEXT_ITEM=2;
	public static final int WINDOW_VAR_PREV_ITEM=3;
	
	private QName name = null;
	private int type;
	
	
	public WindowVariable(QName qname, int type, QueryLocation loc) throws MXQueryException {
		this.name = qname;
		this.type = type;
	}


	public int getType() {
		return type;
	}
	
	public QName getQName() {
		return name;
	}
	
	public WindowVariable copy() throws MXQueryException {
		return new WindowVariable(getQName().copy(), type, null);
	}
}

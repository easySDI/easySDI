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

package ch.ethz.mxquery.model.updatePrimitives;

import ch.ethz.mxquery.datamodel.Identifier;
import ch.ethz.mxquery.exceptions.MXQueryException;

public interface UpdatePrimitive {
	
	public static final int INSERT_BEFORE = 2048;
	public static final int INSERT_AFTER = 2049;
	public static final int INSERT_INTO = 2050;
	public static final int INSERT_INTO_AS_FIRST = 2051;
	public static final int INSERT_INTO_AS_LAST = 2052;
	public static final int INSERT_ATTRIBUTES = 2053;
	public static final int DELETE = 2054;
	public static final int REPLACE_NODE = 2055;
	public static final int REPLACE_VALUE = 2056;
	public static final int REPLACE_NODE_CONTENT = 2057;
	public static final int RENAME = 2058;
	public static final int PUT = 2059;
	
	public int getType();
	
	public Identifier getTargetId();
	
	public void applyUpdate() throws MXQueryException;
	
	UpdateableStore getStore();
	
}

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

package ch.ethz.mxquery.sms.interfaces;


import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.opt.index.IndexSchema;

public interface IndexUpdate extends IndexInterface, StreamStore{
	
	/**
	 * Update the store with new data
	 * @param schema Index schema used in finding the items that are to be updated
	 * @param toks Actual values for the attributes to be used in searching
	 * @param update Iterator with data to be updated
	 * @throws MXQueryException 
	 */
	public void update(IndexSchema schema, Token[] update, Token[] toks) throws MXQueryException;
}

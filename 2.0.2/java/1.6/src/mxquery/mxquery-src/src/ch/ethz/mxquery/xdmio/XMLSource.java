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

package ch.ethz.mxquery.xdmio;

import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.datamodel.Source;

public interface XMLSource extends XDMIterator, Source {

	public abstract void setURI(String uriToSet);
	/**
	 * Provides access to the DTD SYSTEM ID
	 * @return the system Id if the source was validated against a DTD 
	 */
	public abstract String getSystemID();
	/**
	 * Provides access to the DTD PUBLIC ID
	 * @return the public Id if the source was validated against a DTD 
	 */
	public abstract String getPublicID();
	/**
	 * Provides access to the DTD Root element name
	 * @return the root element name if the source was validated against a DTD 
	 */
	public abstract String getRootElemDTD();
	
}

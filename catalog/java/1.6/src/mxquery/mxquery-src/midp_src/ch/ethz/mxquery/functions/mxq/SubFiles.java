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
package ch.ethz.mxquery.functions.mxq;


import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.StaticException;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;

/**
 * 
 * @author David Graf
 * 
 */
public class SubFiles extends CurrentBasedIterator {

	protected void init() throws MXQueryException {
		throw new StaticException("#", "Function mxq:SubFiles is not supported under CLDC/MIDP",null);
	}	

	public Token next() throws MXQueryException {
		throw new StaticException("#", "Function mxq:SubFiles is not supported under CLDC/MIDP",null);

	}
	
	public TypeInfo getStaticType() {
		return new TypeInfo(Type.STRING,Type.OCCURRENCE_IND_ZERO_OR_MORE,null,null);
	}			
	
	public XDMIterator copy(Context context, XDMIterator[] subIters) throws MXQueryException {
		XDMIterator copy = new SubFiles();
		copy.setContext(context);
		copy.setSubIters(subIters);
		return copy;
	}
}

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

package ch.ethz.mxquery.iterators.ft;

import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.contextConfig.XQStaticContext;
import ch.ethz.mxquery.datamodel.adm.AllMatch;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.model.XDMIterator;

/**
 * Interface of the FT Iterators
 * @author jimhof
 */
public interface FTIteratorInterface {

	public AllMatch next() throws MXQueryException;
	
	public void reset() throws MXQueryException;
	
	public void setResettable(boolean r) throws MXQueryException;

	public void setContext(Context ctx) throws MXQueryException;
	
	public Context getContext();
	
	public FTIteratorInterface copy(Context parentIterContext, XQStaticContext prevParentIterContext, boolean copyContext, Vector nestedPredCtxStack) throws MXQueryException;
	
	public void setIgnoreOption(XDMIterator ignoreOption) throws MXQueryException;

}

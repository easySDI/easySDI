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

package ch.ethz.mxquery.model.ft;

import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.contextConfig.XQStaticContext;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.model.XDMIterator;

/**
 * implementation of a Range in FT 
 * @author jimhof
 */

public class Range {
	
	XDMIterator [] subIters;
	
	private XQStaticContext context = null;
	private String range = null;
	
	public Range(XQStaticContext ctx, XDMIterator[] addExpr, String ftRange){
		context = ctx;
		
		if (addExpr == null || addExpr.length == 0 || addExpr.length > 2)
			throw new RuntimeException("Invalid number of arguments to Range");
		
		subIters = addExpr;		
		range = ftRange;
	}

	public String getRange(){
		return range;
	}
	
	public XDMIterator getAddExpr(){
		return subIters[0];
	}
	
	public XDMIterator getFromAddExpr(){
		return subIters[0];
	}
	
	public XDMIterator getToAddExpr(){
		return subIters[1];
	}
	
	public XQStaticContext getContext(){
		return context;
	}
	public void reset() throws MXQueryException {
		for (int i=0;i<subIters.length;i++)
			subIters[i].reset();
	}
	
	public void setResettable(boolean r) throws MXQueryException {
		for (int i=0;i<subIters.length;i++)
			subIters[i].setResettable(r);
	}

	public void setContext(Context ctx) throws MXQueryException {
		for (int i=0;i<subIters.length;i++)
			subIters[i].setContext(ctx, true);
		context = ctx;	
	}

	public Range copy (Context ctx, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator [] newSubs = new XDMIterator[subIters.length];
		for (int i=0;i<subIters.length;i++)
			newSubs[i] = subIters[i].copy(ctx, null, false, nestedPredCtxStack);
		return new Range(ctx,newSubs,range);
	}
	
}

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

package ch.ethz.mxquery.iterators;

/**
 * User defined function where the actual binding happens at runtime, not at compile time
 * Needed to support forward references in function declarations and recursive calls
 */

import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.model.updatePrimitives.PendingUpdateList;
import ch.ethz.mxquery.util.KXmlSerializer;

public class UserdefFuncCallLateBinding extends CurrentBasedIterator {
	public UserdefFuncCallLateBinding(Context ctx, QName name, int ar) throws MXQueryException {
		funcName = name;
		arity = ar;
		context = ctx;
	}

	QName funcName;
	int arity;

	protected void init() throws MXQueryException {
		// Lookup function
		if (current == null) {
			lookup();		
		}
	}

	public void lookup() throws MXQueryException {
		UserdefFuncCall func = (UserdefFuncCall)context.getFunction(funcName, arity).getFunctionImplementation(context);
		if (func == null)
			throw new MXQueryException(ErrorCodes.A0009_EC_EVALUATION_NOT_POSSIBLE,"Function lookup has failed", loc);
		current = func;
		if (current != null) {
			current.setSubIters(subIters);
			current.setResettable(resettable);
		}
		func.getExprTypeShallow();
	}

	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new UserdefFuncCallLateBinding(context, funcName, arity);
		copy.setSubIters(subIters);
		return copy;
		
	}

	public Token next() throws MXQueryException {
		if (called == 0) {
			init();
			called++;
		}
		return current.next();
	}	
	
	public void setResettable(boolean r) throws MXQueryException {
		if (current != null)
			current.setResettable(r);
		super.setResettable(r);
	}
	
	protected int getExprTypeImpl() {
		try {
			boolean clearCurrent = false;
			if (current == null)
				clearCurrent = true;
			lookup();
			 int res = ((UserdefFuncCall)current).getExprTypeShallow();
			 if (clearCurrent)
				 current = null;
			 return res;
		} catch (MXQueryException e) {
		}
		// fallback in case of failures
		return XDMIterator.EXPR_CATEGORY_SIMPLE;
	}

	public QName getFunctionName() {
		return funcName;
	}

	public XDMIterator[] getAllSubIters() {
		
		int curCount = 0;
		if (current != null)
			curCount = 1;
		
			if (subIters != null) {
				XDMIterator [] res = new XDMIterator [subIters.length+curCount];
				for (int i=0;i<subIters.length;i++) 
					res[i] = subIters[i];
				if (curCount > 0)
					res[res.length-1] = current;
				return res;
			}
			else {
				XDMIterator [] res = new XDMIterator[curCount];
				if (curCount > 0)
					res[res.length-1] = current;
				return res; 
			}
	}

	public UserdefFuncCall getResolvedFunc() {
		return (UserdefFuncCall)current;
	}
	
	public PendingUpdateList getPendingUpdateList() throws MXQueryException {
		if (current != null)
			return current.getPendingUpdateList();
		else
			return new PendingUpdateList(loc);
	}	
	protected KXmlSerializer createIteratorStartTag(KXmlSerializer serializer)
	throws Exception {
		super.createIteratorStartTag(serializer);
		serializer.attribute(null, "functionName", funcName.toString());
		serializer.attribute(null, "arity", Integer.toString(arity));
		return serializer;
	}	
	
}

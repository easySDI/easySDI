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

import java.util.Vector;

import ch.ethz.mxquery.bindings.WindowFactory;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.contextConfig.XQStaticContext;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.exceptions.StaticException;
import ch.ethz.mxquery.functions.fn.DataValuesIterator;
import ch.ethz.mxquery.model.CFException;
import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.VariableHolder;
import ch.ethz.mxquery.model.Iterator;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.model.updatePrimitives.PendingUpdateList;
import ch.ethz.mxquery.util.KXmlSerializer;

public class UserdefFuncCall extends CurrentBasedIterator {
	protected QName[] paramNames;

	protected XDMIterator function;
	protected XDMIterator resultSeqTypeIt;
	
	protected TypeInfo[] paramTypes;

	protected TypeInfo returnType;
	
	private int sigExpressionCategory = EXPR_CATEGORY_SIMPLE;
	
	private QName functionName;
	
	public UserdefFuncCall(Context ctx, QName fName, QName[] paramNames, TypeInfo[] paramTypes,
			XDMIterator function, TypeInfo returnType, XDMIterator returnSeqTypeIt, int exprCategory, QueryLocation location) throws MXQueryException {
		super(ctx, location);
		this.functionName = fName;
		this.function = function;
		this.resultSeqTypeIt = returnSeqTypeIt;
		this.paramNames = paramNames;
		this.returnType = returnType;
		this.paramTypes = paramTypes;
		this.sigExpressionCategory = exprCategory;
		function.setResettable(true);
	}

	protected void init() throws MXQueryException {
		
		if (resultSeqTypeIt != null) {
			resultSeqTypeIt.setResettable(true);
			resultSeqTypeIt.reset();
		}
		function.setResettable(true);
		for (int i = 0; i < this.subIters.length; i++) {
			VariableHolder vh = this.context.getVariable(this.paramNames[i]);
			Window window = WindowFactory.getNewWindow_Eager(this.context, this.subIters[i]);
			vh.setIter(window);
		}
		Window window;
		XDMIterator func = this.function;
		try {
			if (resultSeqTypeIt != null) { 
				resultSeqTypeIt.setSubIters(insertAtomizationCast(returnType, function));
				resultSeqTypeIt.setContext(context, true);
				func = resultSeqTypeIt;
			}	

			window = WindowFactory.getNewWindow_Eager(this.context,
					func);
		} catch (CFException cfe) {
			if (cfe.isEarlyReturn()) {
				window = cfe.getReturnValue();
			} else {
				throw cfe;
			}
		}
		this.current = window;
	}

	public Token next() throws MXQueryException {
		if (this.called == 0) {
			this.init();
		}
		this.called++;
		return this.current.next();
	}

	public void setContext(Context context, boolean recursive) throws MXQueryException {
		if (subIters != null && recursive) {
			for (int i=0;i<subIters.length;i++)
				subIters[i].setContext(context, recursive);
		}
		
	}

	public void setResettable(boolean r) throws MXQueryException {
		for (int i = 0; i < this.subIters.length; i++) {
			this.subIters[i].setResettable(r);
		}
		resettable = r;
	}

	protected void resetImpl() throws MXQueryException {
		for (int i = 0; i < this.subIters.length; i++) {
			this.subIters[i].reset();
		}
		function.reset();
		super.resetImpl();
	}

	public void setSubIters(XDMIterator[] subIt) throws MXQueryException {
		if (subIt == null) {
			return;
		}
		if (paramTypes != null) {
			// Insert fn:data if input is expected to be atomic
			for (int i = 0;i<subIt.length;i++) {
				subIt[i] = insertAtomizationCast(paramTypes[i], subIt[i]);
			}

		}		
		subIters = subIt;
	}

	private XDMIterator insertAtomizationCast(TypeInfo parType, XDMIterator toAdapt) {
		if (parType != null && parType.getType() != TypeInfo.UNDEFINED 
				&& Type.isAtomicType(parType.getType(), null))
			toAdapt = DataValuesIterator.getDataIterator(toAdapt, toAdapt.getContext());

		if (parType != null && parType.getType() != TypeInfo.UNDEFINED && 
				(parType.getType() != Type.END_SEQUENCE) &&
				parType.getType() != Type.ANY_ATOMIC_TYPE && !Type.isNode(parType.getType()) 
				&& !Type.isTypeOrSubTypeOf(toAdapt.getStaticType().getType(),parType.getType(), null) && parType.getType() != Type.ITEM)
			toAdapt = new CastAsIterator(toAdapt.getContext(), toAdapt, parType, false, true,loc);
		return toAdapt;
	}

	public PendingUpdateList getPendingUpdateList() throws MXQueryException {
		return function.getPendingUpdateList();
	}	
	
	public final XDMIterator copy(Context parentIterContext, XQStaticContext newParentIterContext, boolean copyContext, Vector nestedPredCtxStack) throws MXQueryException {
		// a new context is always needed for the parameters of the UDF
		Context newContext = context.copy();
			newContext.setParent(parentIterContext);
		
		XDMIterator[] newSubIters = null;
		if (subIters != null) {
			newSubIters = new XDMIterator[subIters.length]; 		
			for (int i=0; i<subIters.length; i++) {

				XQStaticContext subContext = subIters[i].getContext();
				//Context parCtx = context.getParent();
				// Due to the scoping rules of Let/For/etc, 
				//the parent and the producing expression of let should have the same context
				if (subContext != null ) { //&& subContext == parCtx
					newSubIters[i] = subIters[i].copy(parentIterContext, null, false, nestedPredCtxStack);
					//newContext = newSubIters[i].getContext(); // special FFLWORIterator context setting				
				} else {
					throw new RuntimeException("User Def context nesting wrong - terminating");
				}

			}
		}
		UserdefFuncCall cp = (UserdefFuncCall)copy(newContext, newSubIters, nestedPredCtxStack);
		cp.exprCategory = this.exprCategory;
		return cp;
	}	
	
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		boolean bodyDiffCtx = false;
		if (function.getContext() != this.context)
			bodyDiffCtx = true;
		UserdefFuncCall copy = new UserdefFuncCall(
				context, 
				functionName,
			Iterator.copyQNames(paramNames), 
			Iterator.copyTypeInfos(paramTypes), 
			function.copy(context, null, bodyDiffCtx, nestedPredCtxStack), 
			returnType.copy(), 
			resultSeqTypeIt==null?null:resultSeqTypeIt.copy(context, null, false, nestedPredCtxStack), exprCategory,loc);
		
		copy.exprCategory = exprCategory;
		copy.sigExpressionCategory = sigExpressionCategory;
		copy.subIters = subIters;
		
		return copy;
	}
	
	public int getExprTypeShallow() throws MXQueryException {
		boolean hasUpdates = false;
		if (subIters != null) {
			hasUpdates = checkArguments(hasUpdates,isScripting);
		}
		
		if (hasUpdates)
			exprCategory = EXPR_CATEGORY_UPDATING;
		else 
			exprCategory = sigExpressionCategory;
		return exprCategory;
	}
	
	protected void checkExpressionTypes(boolean isScripting) throws MXQueryException {	
		boolean hasUpdates = false;
		if (subIters != null) {
			hasUpdates = checkArguments(hasUpdates,isScripting);
		}
		
		if (exprCategory == EXPR_CATEGORY_UNDETERMINED) {
			try {
			int funcCat = function.getExpressionCategoryType(isScripting);
			if (funcCat != sigExpressionCategory)
				if(!isScripting) {
					if (funcCat != XDMIterator.EXPR_CATEGORY_VACUOUS)
						switch (sigExpressionCategory) {
						case XDMIterator.EXPR_CATEGORY_UPDATING:
							throw new StaticException(ErrorCodes.U0002_UPDATE_STATIC_NONUPDATING_EXPRESSION_NOT_ALLOWED_HERE,"Simple body not allowed in updating/sequential function", loc);
						case XDMIterator.EXPR_CATEGORY_SIMPLE:
							throw new StaticException(ErrorCodes.U0001_UPDATE_STATIC_UPDATING_EXPRESSION_NOT_ALLOWED_HERE,"Updating body not allowed in non-updating function", loc);
						}
				} else {
					switch (sigExpressionCategory) {
					case EXPR_CATEGORY_SIMPLE:
						if (funcCat != EXPR_CATEGORY_VACUOUS) //TODO: specific error code for sequential body
							throw new StaticException(ErrorCodes.U0001_UPDATE_STATIC_UPDATING_EXPRESSION_NOT_ALLOWED_HERE,"Updating/Sequential body not allowed in non-updating function", loc);
						break;
					case EXPR_CATEGORY_UPDATING:
						if (funcCat == EXPR_CATEGORY_SEQUENTIAL) //TODO: specific error code for sequential body
							throw new StaticException(ErrorCodes.A0009_EC_EVALUATION_NOT_POSSIBLE,"Sequential body not allowed in non-updating function", loc);
						break;
					case EXPR_CATEGORY_SEQUENTIAL:
						if (funcCat == EXPR_CATEGORY_UPDATING) //TODO: specific error code for sequential body
							throw new StaticException(ErrorCodes.A0009_EC_EVALUATION_NOT_POSSIBLE,"Updating body not allowed in sequential function", loc);
					}					
			}
			}catch (NullPointerException ne) {
				// could not look up the function
			}
		}
		if (hasUpdates && sigExpressionCategory != EXPR_CATEGORY_UPDATING)
			exprCategory = EXPR_CATEGORY_UPDATING;
		else 
			exprCategory = sigExpressionCategory;
	}

	private boolean checkArguments(boolean hasUpdates,boolean isScripting) throws MXQueryException {
		if (!isScripting)
			checkExprSimpleOnly(subIters,isScripting);
		else { 
			switch (sigExpressionCategory) {
			case EXPR_CATEGORY_SIMPLE:
			case EXPR_CATEGORY_UPDATING:
				hasUpdates = checkExprNoSequential(subIters,isScripting);
				break;
			case EXPR_CATEGORY_SEQUENTIAL:
				checkExprSimpleOnly(subIters,isScripting);
			}
		}
		return hasUpdates;
	}	
	

	//It was not overwritten!
	public TypeInfo getStaticType(){
		return returnType;
	}

	public QName getFunctionName() {
		return functionName;
	}

	public XDMIterator[] getAllSubIters() {
		if (subIters != null) {
			XDMIterator [] res = new XDMIterator [subIters.length+1];
			for (int i=0;i<subIters.length;i++) 
				res[i] = subIters[i];
			res[res.length-1] = function;
			return res;
		}
		else 
			return new XDMIterator[] {function};
	}
	
	public int getExpressionCategoryType(boolean scripting) throws MXQueryException{
		checkExpressionTypes(scripting);
		return getExprTypeImpl();		
	}
	
	protected KXmlSerializer createIteratorStartTag(KXmlSerializer serializer)
	throws Exception {
		super.createIteratorStartTag(serializer);
		serializer.attribute(null, "functionName", functionName.toString());
		serializer.attribute(null, "returnType", returnType.toString());
		for (int i=0;i<paramTypes.length;i++) {
			serializer.attribute(null, "paramType"+i, paramTypes[i].toString());
		}
		for (int i=0;i<paramNames.length;i++) {
			serializer.attribute(null, "paramNames"+i, paramNames[i].toString());
		}
		return serializer;
	}
	
}

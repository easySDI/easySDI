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

import java.util.Vector;

import ch.ethz.mxquery.bindings.WindowFactory;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.contextConfig.XQStaticContext;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.exceptions.MXQueryException;

public class VariableHolder {
	private XDMIterator iter;
	private boolean external;
	private boolean declared=false;
	private int useCounter=0;
	private boolean resetable=false;
	private boolean assignable=true;
	private boolean updateable=false;
	private Context ctx;
	private XDMIterator seqTypeIt = null;
	
	
	
	public void setSeqTypeIt(XDMIterator seqTypeIt) throws MXQueryException {
		this.seqTypeIt = seqTypeIt;
	}

	public VariableHolder(Context ctx, boolean external){
		this(ctx, null, external);
	}
	
	public VariableHolder(Context ctx, XDMIterator iter){
		this(ctx, iter, false);
	}
	
	public VariableHolder(Context ctx, XDMIterator iter, boolean external){
		this.external = external;
		this.iter = iter;
		this.ctx = ctx;
	}

	public void setIter(XDMIterator iter) throws MXQueryException {
		this.iter = iter;
		if(iter == null){
			return;
		}
		if(seqTypeIt != null){
			seqTypeIt.setResettable(true);
			seqTypeIt.reset();
		}
		//external variables are always materialized. At some point this should be changed			
		if(needsMaterialization()  ){
			if(iter.isExprParameter(XDMIterator.EXPR_PARAM_WINDOW, false)){
				if(this.seqTypeIt != null){
					seqTypeIt.setSubIters(iter);
					seqTypeIt.setContext( ctx, true ); //RT!!!!!!!!!!!!!!!

					this.iter = WindowFactory.getNewWindow(ctx, seqTypeIt);
				}else{
					this.iter = iter;
				}
			}else{
				if(iter.isExprParameter(XDMIterator.EXPR_PARAM_CHEAPEVAL, false) && seqTypeIt == null ){
					this.iter = iter;
					this.setResetable(true);
				}else{
					if(this.seqTypeIt != null){
						seqTypeIt.setSubIters(iter);
						seqTypeIt.setContext( ctx, true );
						this.iter = seqTypeIt;
					}else{
						this.iter = iter;
					}
					this.iter = WindowFactory.getNewWindow(ctx, this.iter);
				}
			}
		}else{
			if(this.seqTypeIt != null){
				seqTypeIt.setSubIters(iter);
				seqTypeIt.setContext( ctx, true ); //RT!!!!!!!!!!!!!!!
				this.iter = seqTypeIt;
			}else{

				this.iter = iter;
			}
		}
	}
	
	public boolean isAssignable() {
		return assignable;
	}

	public void setAssignable(boolean assignable) {
		this.assignable = assignable;
	}

	public boolean isUpdatable() {
		return updateable;
	}

	public void setUpdatable(boolean updatable) {
		this.updateable = updatable;
	}

	public TypeInfo getType() {
		if (seqTypeIt != null)
			return seqTypeIt.getStaticType();
		else 
			if (iter != null)
				return iter.getStaticType();
		return new TypeInfo(Type.ITEM,Type.OCCURRENCE_IND_ZERO_OR_MORE,null,null);
	}
	
	public XDMIterator getIter() {
		return iter;
	}
	
	public boolean isExternal() {
		return external;
	}
	
	public void incUseCounter(){
		useCounter++;
	}
	
	public void setUseCounter(int c) {
		useCounter = c;
	}
	
	public int getUsage(){
		return useCounter;
	}

	public boolean isDeclared() {
		return declared;
	}

	public void setDeclared() {
		this.declared = true;
	}
	
	public boolean isResetable(){
		return resetable;
	}
	
	public void setResetable(boolean resetable) throws MXQueryException{
		if(resetable){
			this.resetable = true;
		}
		if (iter != null)
			iter.setResettable(resetable);
	}
	
	public void destroyVariable(){
		iter = null;
		declared = false;
	}
	
	
	/**
	 * This is a conservative, because there are cases where it can be reseted and nevertheless 
	 * no materialization is needed.
	 * @return true if materialization is needed
	 */
	public boolean needsMaterialization(){
		if(useCounter > 0 || resetable){
			return true;
		}else{
			return false;
		}
	}
	
	public VariableHolder copy(Context context, Vector nestedPredCtxStack) throws MXQueryException {
		VariableHolder copy = new VariableHolder(
				context, 
				iter==null?null:iter.copy(context, null, false, nestedPredCtxStack), 
				external);
		copy.setResetable(resetable);
		copy.useCounter = useCounter;
		if (seqTypeIt != null)
		copy.setSeqTypeIt(seqTypeIt.copy(context, null, false, nestedPredCtxStack));
		
		if (declared) {
			copy.setDeclared();
		}
		
		return copy;
	}

	public XQStaticContext getContext() {
		return ctx;
	}

}

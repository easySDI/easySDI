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

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.VariableHolder;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.util.KXmlSerializer;

/**
 * 
 * @author Matthias Braun
 * 
 */
public class VariableIterator extends CurrentBasedIterator {
	protected QName varname;

	private boolean firstInit = true;
	
	private int retType = Type.ITEM;

	//private int windowSchemaId;

	private VariableHolder valueHolder;

	private Window window;

	private boolean resolveVHonReset = false;
	
	public QName getVarQName() {
		return varname;
	}

	public VariableIterator(Context ctx, QName varname, boolean resolveOnReset, QueryLocation location) {
		super(ctx, location);
		if (varname == null) {
			throw new IllegalArgumentException();
		}

		this.varname = varname;
		resolveVHonReset = resolveOnReset;
	}

	public VariableIterator(Context ctx, QueryLocation location) {
		super(ctx, location);
		
	}

	public void setParam(String name, String value) {
		if (name.equals("varname") && value != null) {
			try {
				this.varname = new QName(value);
			} catch (MXQueryException e) {
				throw new RuntimeException(e.toString());
			}
		} else {
			throw new IllegalArgumentException();
		}
	}

	public Token next() throws MXQueryException {
		if (called == 0) {
			init();
		}

		return current.next();
	}

	protected void firstInit() throws MXQueryException {
		valueHolder = context.getVariable(varname);
		this.resettable = valueHolder.isResetable();
		firstInit = false;
	}

	public void init() throws MXQueryException {
		called++;
		if (firstInit) {
			firstInit();
		}
		
		if (resolveVHonReset) {
			valueHolder = context.getVariable(varname);
			this.resettable = valueHolder.isResetable();
		}
		
		if (valueHolder.getIter() == null) {
			throw new DynamicException(ErrorCodes.E0002_DYNAMIC_NO_VALUE_ASSIGNED, "Variable value " + varname
					+ " is not bound to a value	!", loc);
		}
		if (valueHolder.getIter().isExprParameter(EXPR_PARAM_WINDOW, false)) {
			window = (Window) valueHolder.getIter();
			//windowSchemaId = window.getWindowId();
			Window wnd = window;
			window = window.getNewWindow(1, Window.END_OF_STREAM_POSITION);
			window.setResettable(resettable);
			window.setContext(wnd.getContext(), false);
			current = window;
		} else {
			current = valueHolder.getIter();
			if (current.isOpen()) {
				current.reset();
			}
		}
	}

	public Window getUnderlyingIterator() throws MXQueryException {
		if(called==0){
			init();
		}
		if(valueHolder.getIter().isExprParameter(EXPR_PARAM_WINDOW, false)){
			return (Window)current;
		}else{
			return null;
		}
	}

	protected final void resetImpl()  throws MXQueryException {
		super.resetImpl();
		freeResources(true);
	}

//	protected void freeResources(boolean restartable) throws MXQueryException {
//		super.freeResources(restartable);
		
		// FIXME causes errors if the windows is used somewhere else
//		try{
//			currentToken = Token.START_SEQUENCE_TOKEN; 
//			if (window != null ){
//				if(windowSchemaId != window.getWindowId()){
//					window.destroyWindow();
//				}
//				window = null;
//			}	
//			window = null;
//		}catch(Exception err){
//			throw new RuntimeException(err.toString());
//		}
//	}

	public void setResettable(boolean r)throws MXQueryException {
		if(context == null){
			throw new NullPointerException("Context not initialized");
		}
		if (firstInit) {
			firstInit();
		};
		
		valueHolder.setResetable(r);
		r = valueHolder.isResetable();
		
		super.setResettable(r);
	}

	public boolean isResettable() throws MXQueryException {
		if (!firstInit) {
			firstInit();
		}
		return super.resettable;
	}

	protected KXmlSerializer createIteratorStartTag(KXmlSerializer serializer) throws Exception {
		super.createIteratorStartTag(serializer);
		serializer.attribute(null, "varname", varname.toString());
		return serializer;
	}

	/* (non-Javadoc)
	 * @see ch.ethz.mxquery.model.iterators.Iterator#getReturnType()
	 */
	public TypeInfo getStaticType() {
		return new TypeInfo(retType,Type.OCCURRENCE_IND_ZERO_OR_MORE,null,null);
	}
	
	public void setReturnType(int type) {
		retType = type;
	}

	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		VariableIterator copy = new VariableIterator(context, varname.copy(), false,loc);
		copy.setConstModePreserve(this.constModePreserve);
		copy.setSubIters(subIters);
		return copy;
	}
	
	public boolean hasSharedAccess() {
		if (valueHolder != null)
			return (valueHolder.getUsage() > 1);
		else
			return true;
	}
}

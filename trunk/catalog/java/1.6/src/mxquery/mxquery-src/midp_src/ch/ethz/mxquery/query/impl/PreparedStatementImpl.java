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
package ch.ethz.mxquery.query.impl;

import java.io.IOException;
import java.io.OutputStreamWriter;
import java.util.Enumeration;
import java.util.Hashtable;
import java.util.Vector;

import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.iterators.TokenIterator;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.model.VariableHolder;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.contextConfig.CompilerOptions;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.contextConfig.XQDynamicContext;
import ch.ethz.mxquery.query.PreparedStatement;
import ch.ethz.mxquery.xdmio.StoreSet;
import ch.ethz.mxquery.xdmio.XDMSerializer;
import ch.ethz.mxquery.xdmio.XDMInputFactory;
import ch.ethz.mxquery.util.StringReader;
import ch.ethz.mxquery.query.impl.PreparedStatementImpl;
import ch.ethz.mxquery.query.webservice.WSServer;

public class PreparedStatementImpl implements PreparedStatement {
	Context ctx;
	XDMIterator iter;
	CompilerOptions co;
	boolean activated = false;
	
	public PreparedStatementImpl(Context ctx, XDMIterator iter, CompilerOptions cop){
		this.ctx = ctx;
		this.iter = iter;
		co = cop;
	}
	
	public XDMIterator evaluate() throws MXQueryException{
		activated = true;
		ctx.setCurrentTime(null);
		Vector modContexts = ctx.getModuleContexts();
		if (modContexts != null)
		for (int i=0;i<modContexts.size();i++) {
			Context modCtx = (Context)modContexts.elementAt(i);
			modCtx.setCurrentTime(null);
		}
		return iter;
	}
	
	public XQDynamicContext getContext(){
		return ctx;
	}

	public void addExternalResource(QName varname, XDMIterator resource) throws MXQueryException {
		ctx.setVariableValue(varname, resource, false, true);
	}

	public void addExternalResource(String varname, String resource) throws MXQueryException {
		activated = true;
		ctx.setVariableValue(new QName(varname), XDMInputFactory.createXMLInput(ctx, 
				new StringReader(resource), true, ctx.getInputValidationMode(), null), false, true);
	}

	public boolean isWebService(){
		return ctx.isWebService();
	}
		
	public StoreSet getStores(){
		return ctx.getStores();
	}

	public TypeInfo getStaticReturnType() {
		TypeInfo ret = new TypeInfo(Type.ITEM, Type.OCCURRENCE_IND_ZERO_OR_MORE, null,null);
		if (iter != null) {
			ret =  iter.getStaticType();
		}
		return ret;
	}

	public void applyPUL() throws MXQueryException {
		if (iter.getExpressionCategoryType(co.isScripting()) == XDMIterator.EXPR_CATEGORY_UPDATING) {
			iter.getPendingUpdateList().apply();
		}
		
	}

	public void serializeStores(boolean createBackup) throws IOException, MXQueryException {
		ctx.getRootContext().getStores().serializeStores(createBackup, ctx.getRootContext().getBaseURI());
	}

	public Vector getExternalVariables() {
		Hashtable vars = ctx.getAllVariables();
		Vector res = new Vector();
		for (Enumeration varKeys = vars.keys();varKeys.hasMoreElements();) {
			QName qn = (QName)varKeys.nextElement();
			VariableHolder vh = (VariableHolder)vars.get(qn);
			if (vh.isExternal())
				res.addElement(qn);
		}
		return res;
	}

	public Vector getUnresolvedExternalVariables() {
		Hashtable vars = ctx.getAllVariables();
		Vector res = new Vector();
		for (Enumeration varKeys = vars.keys();varKeys.hasMoreElements();) {
			QName qn = (QName)varKeys.nextElement();
			VariableHolder vh = (VariableHolder)vars.get(qn);
			if (vh.isExternal() && vh.getIter() == null)
				res.addElement(qn);
		}
		return res;
	}
	public StringBuffer handleSOAP(String inputSoap) {
		return new StringBuffer();
	}
	public boolean isModuleDecl(){
		return false;
	}
	
	public void exposeModule(){
		;
	}
	
	public String generateWSDL(String serverURL) throws MXQueryException{
		return "";
	}
	
	public void close() throws MXQueryException {
		iter.close(true);
		//iter.reset();
	}
	public PreparedStatement copy() throws MXQueryException {
		if (activated)
			throw new MXQueryException(ErrorCodes.A0009_EC_EVALUATION_NOT_POSSIBLE,"The prepared statement has been used already and cannot be copied",QueryLocation.OUTSIDE_QUERY_LOC);
		Context rootCtx = iter.getContext().getRootContext().copy();
		XDMIterator cp = iter.copy(rootCtx, null, true, new Vector());
		cp.setResettable(false);
		return new PreparedStatementImpl(rootCtx,cp,co);
	}
	
	public void setContextItem(XDMIterator resource) throws MXQueryException {
		ctx.setContextItem(resource);
	}

}

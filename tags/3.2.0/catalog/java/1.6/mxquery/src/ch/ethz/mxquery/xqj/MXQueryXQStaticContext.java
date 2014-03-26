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

package ch.ethz.mxquery.xqj;

import java.util.Enumeration;
import java.util.Hashtable;
import java.util.Vector;

import javax.xml.namespace.QName;
import javax.xml.xquery.XQConstants;
import javax.xml.xquery.XQException;
import javax.xml.xquery.XQItemType;
import javax.xml.xquery.XQQueryException;
import javax.xml.xquery.XQSequenceType;
import javax.xml.xquery.XQStaticContext;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.exceptions.MXQueryException;

public class MXQueryXQStaticContext implements XQStaticContext {
	
	Context runtime;
    private int holdability = XQConstants.HOLDTYPE_HOLD_CURSORS_OVER_COMMIT;
    private int scrollability = XQConstants.SCROLLTYPE_FORWARD_ONLY;
    private int bindingMode = XQConstants.BINDING_MODE_IMMEDIATE;
	
	
	public MXQueryXQStaticContext(Context runtime) {
		this.runtime = runtime;
	}
	
	public MXQueryXQStaticContext(MXQueryXQStaticContext copyFrom) throws XQException{
		try {
			this.runtime = copyFrom.runtime.copy();
		} catch (MXQueryException e) {
			throw new XQException("Error creating static context from existing static context");
		}
		this.holdability = copyFrom.holdability;
		this.scrollability = copyFrom.scrollability;
		this.bindingMode = copyFrom.bindingMode;
	}

	public Context getEngineContext() {
		return runtime;
	}
	
	public void declareNamespace(String prefix, String uri) throws XQException {
		try {
			runtime.addNamespace(prefix, uri);
			if(uri.equals("")){
				runtime.removeNamespace(prefix);
			}
		} catch (MXQueryException me) {
				throw new XQQueryException(me.getMessage(),new QName(me.getErrorCode()));
		}
	}

	public int getBindingMode() {
		// TODO Auto-generated method stub
		return bindingMode;
	}


	public XQItemType getContextItemStaticType() {
		// TODO Auto-generated method stub
		return null;
	}

	/**
	 * Scrollability for XQJ connection
	 * @return the scrollability property
	 * @throws XQException
	 */
	public int getScrollability(){
       return scrollability;
	}
	
	public void setScrollability(int scrollability) throws XQException {
        switch (scrollability) {
            case XQConstants.SCROLLTYPE_FORWARD_ONLY:
            case XQConstants.SCROLLTYPE_SCROLLABLE:
                this.scrollability = scrollability;
                break;
            default:
            	throw new XQException("Invalid scrollability value");
        }
	}
	
	
	public int getHoldability(){
	       return holdability;
	}
		
	public void setHoldability(int holdability) throws XQException {
		switch (holdability) {
		case XQConstants.HOLDTYPE_HOLD_CURSORS_OVER_COMMIT:
		case XQConstants.HOLDTYPE_CLOSE_CURSORS_AT_COMMIT:
			this.holdability = holdability;
			break;
		default:
			throw new XQException("Invalid holdability value set");
		}
	}
	

	public String[] getNamespacePrefixes() {
		Hashtable ht = this.runtime.getAllNamespaces();
		Enumeration e = ht.keys();
		Vector v = new Vector();
		while(e.hasMoreElements()){
			v.add(e.nextElement());
		}
		String str[] = new String[v.size()];
		for(int i = 0; i < v.size(); i++)
			str[i] = (String)v.get(i);
		return str;
//		String[] prefixes = {"xs", "xdt", "local", "xsi", "xml", "fn"};
//		return prefixes;
	}

	public int getQueryLanguageTypeAndVersion() {
		return XQConstants.LANGTYPE_XQUERY;
	}

	public int getQueryTimeout() {
		// TODO Auto-generated method stub
		return 0;
	}


	public void setBaseURI(String baseUri) throws XQException {
		if(baseUri == null){
			throw new XQException("Base URI can not be null!");
		}
		runtime.setBaseURI(baseUri);
	}

	public void setBindingMode(int bindingMode) throws XQException {
		if (bindingMode == XQConstants.BINDING_MODE_DEFERRED || 
				bindingMode == XQConstants.BINDING_MODE_IMMEDIATE)
			this.bindingMode = bindingMode;
		else
			throw new XQException("Invalid binding mode specified");

	}

	public void setBoundarySpacePolicy(int policy) throws XQException {
		if (policy == XQConstants.BOUNDARY_SPACE_PRESERVE)
			runtime.setConstructionMode(ch.ethz.mxquery.contextConfig.XQStaticContext.PRESERVE);
		else if (policy == XQConstants.BOUNDARY_SPACE_STRIP)
			runtime.setConstructionMode(ch.ethz.mxquery.contextConfig.XQStaticContext.STRIP);
			else
				throw new XQException("Invalid Boundary Space policy specified");

	}

	public void setConstructionMode(int mode) throws XQException {
		if(mode == XQConstants.CONSTRUCTION_MODE_PRESERVE){
			runtime.setConstructionMode(ch.ethz.mxquery.contextConfig.XQStaticContext.PRESERVE);
		} else if (mode == XQConstants.CONSTRUCTION_MODE_STRIP){
			runtime.setConstructionMode(ch.ethz.mxquery.contextConfig.XQStaticContext.STRIP);
		} else {
			throw new XQException("Incorrenct construction mode!");
		}
	}

	public void setContextItemStaticType(XQItemType contextItemType) {
		// TODO Auto-generated method stub

	}

	public void setCopyNamespacesModeInherit(int mode) throws XQException {
		switch(mode){
		case XQConstants.COPY_NAMESPACES_MODE_INHERIT: this.runtime.setCopyNamespacesMode(this.runtime.getCopyNamespacesPreserveMode(), true); break;
		case XQConstants.COPY_NAMESPACES_MODE_NO_INHERIT: this.runtime.setCopyNamespacesMode(this.runtime.getCopyNamespacesPreserveMode(), false); break;
			default: throw new XQException("Incorrect copy namespace mode inherit speified!");
		}
	}

	public void setCopyNamespacesModePreserve(int mode) throws XQException {
		switch(mode){
		case XQConstants.COPY_NAMESPACES_MODE_PRESERVE: this.runtime.setCopyNamespacesMode(true, this.runtime.getCopyNamespacesInheritMode()); break;
		case XQConstants.COPY_NAMESPACES_MODE_NO_PRESERVE: this.runtime.setCopyNamespacesMode(false, this.runtime.getCopyNamespacesInheritMode()); break;
			default: throw new XQException("Incorrect copy namespace mode preserve speified!");
		}
	}

	public void setDefaultCollation(String uri) throws XQException {
		if(uri == null){
			throw new XQException("Default collation can not be null!");
		}
		try {
			runtime.setDefaultCollation(uri);
		} catch (MXQueryException me) {
			throw new XQQueryException(me.getMessage(),new QName(me.getErrorCode()));
		}
	}

	public void setDefaultElementTypeNamespace(String uri) throws XQException {
		if(uri == null){
			throw new XQException("Default element/type namespace can not be null!");
		}
		try {
			runtime.setDefaultElementNamespace(uri);
		} catch (MXQueryException me) {
			throw new XQQueryException(me.getMessage(),new QName(me.getErrorCode()));
		}
	}

	public void setDefaultFunctionNamespace(String uri) throws XQException {
		if(uri == null){
			throw new XQException("Default function namespace can not be null!");
		}
		try {
			runtime.setDefaultFunctionNamespace(uri);
		} catch (MXQueryException me) {
			throw new XQQueryException(me.getMessage(),new QName(me.getErrorCode()));
		}
	}

	public void setDefaultOrderForEmptySequences(int order) throws XQException {
		if(order == XQConstants.DEFAULT_ORDER_FOR_EMPTY_SEQUENCES_GREATEST){
			try {
				runtime.setDefaultOrderEmptySequence(ch.ethz.mxquery.contextConfig.XQStaticContext.ORDER_GREATEST);
			} catch (MXQueryException me) {
				throw new XQQueryException(me.getMessage(),new QName(me.getErrorCode()));
			}
		} else if (order == XQConstants.DEFAULT_ORDER_FOR_EMPTY_SEQUENCES_LEAST){
			try {
				runtime.setDefaultOrderEmptySequence(ch.ethz.mxquery.contextConfig.XQStaticContext.ORDER_LEAST);
			} catch (MXQueryException me) {
				throw new XQQueryException(me.getMessage(),new QName(me.getErrorCode()));
			}
		} else {
			throw new XQException("Incorrenct construction mode!");
		}
	}


	public void setOrderingMode(int mode) throws XQException {
		if(mode == XQConstants.ORDERING_MODE_ORDERED){
			try {
				runtime.setOrderingMode(ch.ethz.mxquery.contextConfig.XQStaticContext.ORDERED);
			} catch (MXQueryException me) {
				throw new XQQueryException(me.getMessage(),new QName(me.getErrorCode()));
			}
		} else if (mode == XQConstants.ORDERING_MODE_UNORDERED){
			try {
				runtime.setOrderingMode(ch.ethz.mxquery.contextConfig.XQStaticContext.UNORDERED);
			} catch (MXQueryException me) {
				throw new XQQueryException(me.getMessage(),new QName(me.getErrorCode()));
			}
		} else {
			throw new XQException("Incorrenct construction mode!");
		}
	}

	public void setQueryLanguageTypeAndVersion(int langType) throws XQException {
		if (langType != XQConstants.LANGTYPE_XQUERY)
			throw new XQException("Only XQuery is supported");

	}

	public void setQueryTimeout(int seconds) throws XQException {
		if (seconds < 0 )
			throw new XQException("Invalid Query Timeout");
	}

	public String getBaseURI() {
		return runtime.getBaseURI();
	}

	public int getBoundarySpacePolicy() {
        if (runtime.getConstructionMode().equals(ch.ethz.mxquery.contextConfig.XQStaticContext.PRESERVE))
        	return XQConstants.CONSTRUCTION_MODE_PRESERVE;
        else 
        	return XQConstants.CONSTRUCTION_MODE_STRIP;
	}

	public int getConstructionMode() {
		String mode = runtime.getConstructionMode();
		if(mode.equals(ch.ethz.mxquery.contextConfig.XQStaticContext.PRESERVE)){
			return XQConstants.CONSTRUCTION_MODE_PRESERVE;
		} else {
			return XQConstants.CONSTRUCTION_MODE_STRIP;
		}
	}

	public int getCopyNamespacesModeInherit() {
		if(this.runtime.getCopyNamespacesInheritMode())
			return XQConstants.COPY_NAMESPACES_MODE_INHERIT;
		else
			return XQConstants.COPY_NAMESPACES_MODE_NO_INHERIT;
	}

	public int getCopyNamespacesModePreserve() {
		if(this.runtime.getCopyNamespacesPreserveMode())
			return XQConstants.COPY_NAMESPACES_MODE_PRESERVE;
		else
			return XQConstants.COPY_NAMESPACES_MODE_NO_PRESERVE;
	}

	public String getDefaultCollation() {
		return runtime.getDefaultCollation();
	}

	public String getDefaultElementTypeNamespace() {

		String mxqVal =  runtime.getDefaultElementNamespace();
		if (mxqVal == null)
			return "";
		else
			return mxqVal;
	}

	public String getDefaultFunctionNamespace() {
		return runtime.getDefaultFunctionNamespace();
	}

	public int getDefaultOrderForEmptySequences() {
		String mode = runtime.getDefaultOrderEmptySequence();
		if(mode == null)
			return XQConstants.DEFAULT_ORDER_FOR_EMPTY_SEQUENCES_GREATEST;
		if(mode.equals(ch.ethz.mxquery.contextConfig.XQStaticContext.ORDER_GREATEST)){
			return XQConstants.DEFAULT_ORDER_FOR_EMPTY_SEQUENCES_GREATEST;
		} else {
			return XQConstants.DEFAULT_ORDER_FOR_EMPTY_SEQUENCES_LEAST;
		}
	}

	public String[] getInScopeNamespacePrefixes() throws XQException {
		String[] prefixes = {"xs", "xdt", "local", "xsi", "xml", "fn"};
		return prefixes;
//		Hashtable ht = this.runtime.getAllNamespaces();
//		Enumeration e = ht.keys();
//		Vector v = new Vector();
//		while(e.hasMoreElements()){
//			v.add(e.nextElement());
//		}
//		String str[] = new String[v.size()];
//		for(int i = 0; i < v.size(); i++)
//			str[i] = (String)v.get(i);
//		return str;
	}

	public String getNamespaceURI(String prefix) throws XQException {
		if(prefix == null){
    		throw new XQException("Prefix cann't be null!");
    	}
		String str[] = this.getNamespacePrefixes();
		int i;
		for(i=0; i<str.length; i++){
			if(str[i].equals(prefix))
				break;
		}
		if(i == str.length){
			throw new XQException("No namespace is bound to prefix " + prefix);
		}
		String namespaceURI = (this.runtime.getNamespace(prefix)).getURI();
		return namespaceURI;
//		if (prefix.equals("xs")) {
//            return Context.URI_XS;
//        } else if (prefix.equals("xdt")) {
//            return Context.URI_XDT;
//        } else if (prefix.equals("xsi")) {
//            return Context.URI_XSI;
//        } else if (prefix.equals("xml")) {
//            return Context.URI_XML;
//        } else if (prefix.equals("fn")) {
//            return Context.URI_FN;
//        } else if(prefix.equals("local")){
//        	return Context.URI_LOCAL;
//        } else {
//        	if(this.runtime.getNamespace(prefix) != null){
//        		String namespaceURI = ((Namespace)this.runtime.getNamespace(prefix)).getURI();
//    			return namespaceURI;
//    		}
//    		throw new XQException("No namespace is bound to prefix " + prefix);
//        }
	}

	public int getOrderingMode() {
		String mode = runtime.getOrderingMode();
		if(mode == null)
			return XQConstants.ORDERING_MODE_ORDERED;
		if(mode.equals(ch.ethz.mxquery.contextConfig.XQStaticContext.ORDERED)){
			return XQConstants.ORDERING_MODE_ORDERED;
		} else {
			return XQConstants.ORDERING_MODE_UNORDERED;
		}
	}

	public QName[] getStaticInScopeVariableNames() throws XQException {
		// TODO Auto-generated method stub
		return null;
	}

	public XQSequenceType getStaticInScopeVariableType(QName varname)
			throws XQException {
		// TODO Auto-generated method stub
		return null;
	}

	
}

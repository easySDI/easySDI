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

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.Reader;
import java.util.Vector;

import javax.xml.namespace.QName;

import ch.ethz.mxquery.query.XQCompiler;
import ch.ethz.mxquery.query.PreparedStatement;
import ch.ethz.mxquery.query.impl.CompilerImpl;
import ch.ethz.mxquery.contextConfig.CompilerOptions;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.exceptions.MXQueryException;
import javax.xml.xquery.XQConnection;
import javax.xml.xquery.XQException;
import javax.xml.xquery.XQExpression;
import javax.xml.xquery.XQMetaData;
import javax.xml.xquery.XQPreparedExpression;
import javax.xml.xquery.XQQueryException;
import javax.xml.xquery.XQStaticContext;

public class MXQueryXQConnection extends MXQueryXQDataFactory implements
		XQConnection {

		MXQueryXQStaticContext runtime;
		//private XQCommonHandler commonHandler;
	    private Vector expStore = new Vector();
		CompilerOptions co = new CompilerOptions();
	    
	 public MXQueryXQConnection(MXQueryXQDataSource src) {
		 // Query root context (=? static context) per connections
		 runtime = new MXQueryXQStaticContext(new Context());
	 }
	    
	
	public void clearWarnings() throws XQException {
		checkNotClosed();
		// TODO Auto-generated method stub

	}

	public void close() throws XQException {
		for(int i = 0; i < this.expStore.size(); i ++){
			if(this.expStore.get(i) instanceof MXQueryXQExpression)
				((MXQueryXQExpression) this.expStore.get(i)).close();
			else
				((MXQueryXQPreparedExpression) this.expStore.get(i)).close();
		}
		closed = true;
	}

	public void commit() throws XQException {
        checkNotClosed();
		// TODO NoOp

	}

	public XQExpression createExpression(XQStaticContext properties)
	throws XQException {
		if (properties == null) {
			throw new XQException("Static Context is null");
		}
		checkNotClosed();
		MXQueryXQExpression mxExp = new MXQueryXQExpression(this, (MXQueryXQStaticContext)properties);
		this.expStore.add(mxExp);
		return mxExp;		
}
	
	public XQExpression createExpression() throws XQException {
		checkNotClosed();
		MXQueryXQExpression mxExp = new MXQueryXQExpression(this, new MXQueryXQStaticContext(runtime));
		this.expStore.add(mxExp);
		return mxExp;
	}

	public XQMetaData getMetaData() throws XQException {
	     checkNotClosed();
	     return new MXQueryXQMetaData(this);
	}

	public String getMetaDataProperty(String key) throws XQException {
        checkNotClosed();
        throw new UnsupportedOperationException("Metadata is not yet implemented");
	}

	public String[] getSupportedMetaDataPropertyNames() throws XQException {
        checkNotClosed();
        throw new UnsupportedOperationException("Metadata is not yet implemented");
	}

	public boolean isClosed() {
        return closed;
	}

	public XQPreparedExpression prepareExpression(InputStream xquery)
			throws XQException {
		if (xquery == null)
    		throw new XQException("Query is null");
        return prepareExpression(xquery, new MXQueryXQStaticContext(runtime));
	}

	public XQPreparedExpression prepareExpression(InputStream xquery,
			XQStaticContext properties) throws XQException {
		if (xquery == null)
    		throw new XQException("Query is null");
		return prepareExpression(new InputStreamReader(xquery), properties);
	}

	public XQPreparedExpression prepareExpression(Reader xquery)
			throws XQException {
		checkNotClosed();
		if (xquery == null)
    		throw new XQException("Query is null");
        return prepareExpression(xquery, new MXQueryXQStaticContext(runtime));
	}

	public XQPreparedExpression prepareExpression(Reader xquery,
			XQStaticContext properties) throws XQException {
		checkNotClosed();
		if (xquery == null)
			throw new XQException("Input is a null value");
		String expr = "";
		String str;
		try {
			BufferedReader br = new BufferedReader(xquery);
			while((str = br.readLine()) != null){
				expr += str;
			}
		} catch (IOException e) {
			throw new XQQueryException(e.toString());
		}
		return prepareExpression(expr, properties);
		
	}

	public XQPreparedExpression prepareExpression(String xquery)
			throws XQException {
		if (xquery == null)
    		throw new XQException("Query is null");
        return prepareExpression(xquery, new MXQueryXQStaticContext(runtime));
	}
	
	public XQPreparedExpression prepareExpression(String xquery,
			XQStaticContext properties) throws XQException {
	      checkNotClosed();
	  	if (xquery == null)
    		throw new XQException("Query is null");
	  		MXQueryXQStaticContext ctx = runtime;
	    	if (properties != null)
	    		ctx = (MXQueryXQStaticContext)properties;
	    	else
	    		throw new XQException("Static context is null");
	        try {
	        	XQCompiler compiler = new CompilerImpl();
	        	PreparedStatement statement = compiler.compile(ctx.runtime, xquery,co);
	        	MXQueryXQPreparedExpression mxPreExp = new MXQueryXQPreparedExpression(this, statement);
	        	this.expStore.add(mxPreExp);
	            return mxPreExp;
	        } catch (MXQueryException e) {
	            throw new XQQueryException(e.toString(),new QName(e.getErrorCode()));
	        }
	}

	public void rollback() throws XQException {
        checkNotClosed();
	}


	public boolean getAutoCommit() throws XQException {
		return true;
	}


	public XQStaticContext getStaticContext() throws XQException {
		checkNotClosed();
		return new MXQueryXQStaticContext(runtime);
	}


	public void setAutoCommit(boolean autoCommit) throws XQException {
		if (autoCommit == false)
			throw new XQException("Disabling autocommit not supported");
		
	}


	public void setStaticContext(XQStaticContext properties) throws XQException {
		checkNotClosed();
		if (properties == null)
			throw new XQException("Static Context is null");
		runtime = ((MXQueryXQStaticContext)properties);
	}

}

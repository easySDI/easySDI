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

import ch.ethz.mxquery.contextConfig.CompilerOptions;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.exceptions.MXQueryException;

import javax.xml.namespace.QName;
import javax.xml.xquery.XQConstants;
import javax.xml.xquery.XQException;
import javax.xml.xquery.XQExpression;
import javax.xml.xquery.XQQueryException;
import javax.xml.xquery.XQResultSequence;
import javax.xml.xquery.XQStaticContext;
//import javax.xml.xquery.XQWarning;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.query.XQCompiler;
import ch.ethz.mxquery.query.PreparedStatement;
import ch.ethz.mxquery.query.impl.CompilerImpl;

public class MXQueryXQExpression extends MXQueryXQDynamicContext implements
		XQExpression {

	MXQueryXQConnection conn;
	private boolean cancel;
	MXQueryXQStaticContext runtime;
	private Thread t;
	MXQueryXQSequence seq;
	CompilerOptions co = new CompilerOptions();
	
	public MXQueryXQExpression(MXQueryXQConnection connection, MXQueryXQStaticContext properties) {
		conn = connection;
		runtime = properties;
	}
	
    protected void checkNotClosed() throws XQException {
        if (conn.isClosed()) {
            close();
        }
        if (isClosed()) {
            throw new XQException("Expression has been closed");
        }
    }	
	
	public void cancel() throws XQException {
        checkNotClosed();
        if(t != null)
        this.cancel = true;
	}

	public void close() throws XQException {
		if(seq != null)
			this.seq.close();
        closed = true;
	}

	public void executeCommand(Reader command) throws XQException {
		   checkNotClosed();
	       throw new XQException("MXQuery does not recognize any non-XQuery commands");
	}

	public void executeCommand(String command) throws XQException {
		   checkNotClosed();
	       throw new XQException("MXQuery does not recognize any non-XQuery commands");
	}

	public XQResultSequence executeQuery(InputStream query) throws XQException {
		if (query == null)
			throw new XQException("query is null");
		return executeQuery(new InputStreamReader(query));
	}

	public XQResultSequence executeQuery(Reader query) throws XQException {
		checkNotClosed();
		try {
    	 	t = Thread.currentThread();
    	 	String expr = "";
        	String str;
        	BufferedReader br = new BufferedReader(query);
        	while((str = br.readLine()) != null){
        		expr += str;
        	}
    	 	XQCompiler compiler = new CompilerImpl();
    	 	PreparedStatement statement = compiler.compile(runtime.getEngineContext(), expr,co);
            XDMIterator iter;
            if(this.cancel){
            	this.cancel = false;
            	throw new XQException("Expression has been closed");
            } else {
            	iter = statement.evaluate();
            }
            if(this.cancel){
            	this.cancel = false;
            	throw new XQException("Expression has been closed");
            } else {
            	t = null;
            	if(this.conn.getStaticContext().getScrollability() == XQConstants.SCROLLTYPE_FORWARD_ONLY)
	            	return new MXQueryXQForwardSequence(iter, conn, this);
            	Vector store = new Vector();
            	int i = 0;
            	MXQueryXQForwardSequence mSeq = new MXQueryXQForwardSequence(iter, conn,this);
            	while(mSeq.next()){
            		store.add(i++,mSeq.getItem());
            	}
            	this.seq = new MXQueryXQSequence(store, this.conn);
            	return seq;
            }
        } catch (MXQueryException e) {
            throw new XQQueryException(e.toString(),new QName(e.getErrorCode()));
        } catch (IOException e) {
        	throw new XQQueryException(e.toString());
		}
	}

	public XQResultSequence executeQuery(String query) throws XQException {
		checkNotClosed();
	     try {
	    	 	t = Thread.currentThread();
	    	 	XQCompiler compiler = new CompilerImpl();
	    	 	PreparedStatement statement = compiler.compile(runtime.getEngineContext(), query,co);
	            XDMIterator iter;
	            if(this.cancel){
	            	this.cancel = false;
	            	throw new XQException("Expression has been closed");
	            } else {
	            	iter = statement.evaluate();
	            }
	            if(this.cancel){
	            	this.cancel = false;
	            	throw new XQException("Expression has been closed");
	            } else {
	            	t = null;
	            	if(this.conn.getStaticContext().getScrollability() == XQConstants.SCROLLTYPE_FORWARD_ONLY)
	            	return new MXQueryXQForwardSequence(iter, conn, this);
	            	Vector store = new Vector();
	            	int i = 0;
	            	MXQueryXQForwardSequence mSeq = new MXQueryXQForwardSequence(iter, conn,this);
	            	while(mSeq.next()){
	            		store.add(i++,mSeq.getItem());
	            	}
	            	this.seq = new MXQueryXQSequence(store, this.conn);
	            	return seq;
	            }
	        } catch (MXQueryException e) {
	            throw new XQQueryException(e.toString(),new QName(e.getErrorCode()));
	        }
	}

	   public int getQueryLanguageTypeAndVersion() throws XQException {
	        return XQConstants.LANGTYPE_XQUERY;
	    }

	    public int getQueryTimeout() throws XQException {
	        checkNotClosed();
	        return 0;  
	    }

	    public boolean isClosed() {
	        if (conn.isClosed()) {
	            closed = true;
	        }
	        return closed;
	    }

	    public void setQueryTimeout(int seconds) throws XQException {
	        checkNotClosed();
	        //To change body of implemented methods use File | Settings | File Templates.
	    }


		//@Override
		protected Context getRuntime() {
			if(this.runtime != null){
				return this.runtime.getEngineContext();
			}
			return null;
		}

		public XQStaticContext getStaticContext() throws XQException {
			checkNotClosed();
			return runtime;
		}

}

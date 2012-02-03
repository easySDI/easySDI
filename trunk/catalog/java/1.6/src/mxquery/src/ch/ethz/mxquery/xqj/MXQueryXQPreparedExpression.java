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

import java.util.Vector;

import javax.xml.namespace.QName;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.exceptions.MXQueryException;
import javax.xml.xquery.XQConstants;
import javax.xml.xquery.XQException;
import javax.xml.xquery.XQPreparedExpression;
import javax.xml.xquery.XQQueryException;
import javax.xml.xquery.XQResultSequence;
import javax.xml.xquery.XQSequenceType;
import javax.xml.xquery.XQStaticContext;
//import javax.xml.xquery.XQWarning;
import ch.ethz.mxquery.model.VariableHolder;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.query.PreparedStatement;
import ch.ethz.mxquery.query.impl.PreparedStatementImpl;

public class MXQueryXQPreparedExpression extends MXQueryXQDynamicContext
		implements XQPreparedExpression {

	PreparedStatement exp;
	PreparedStatement pristineCopy;
    //private boolean scrollable;
    private MXQueryXQConnection connection;
    MXQueryXQSequence seq;
	
	protected MXQueryXQPreparedExpression(MXQueryXQConnection connection,
			PreparedStatement expression) throws XQException {
		this.connection = connection;
		this.pristineCopy = expression;
		try {
			exp = pristineCopy.copy();
		} catch (MXQueryException e) {
			throw new XQException("Could not create Expression"+e);
		}
		//this.scrollable = connection.getStaticContext().getScrollability() == XQConstants.SCROLLTYPE_SCROLLABLE;
	}
	
	protected void checkNotClosed() throws XQException {
		if (isClosed()) {
			throw new XQException("Expression has been closed");
		}
	}
	
	public void cancel() throws XQException {
		checkNotClosed();
	}

	public void clearWarnings() throws XQException {
		// TODO Auto-generated method stub

	}

	public void close() throws XQException {
		if(seq != null)
			this.seq.close();
		if (exp!=null)
			try {
				exp.close();
			} catch (MXQueryException e) {
				throw new XQQueryException(e.toString());
			}
		closed = true;
	}

	public XQResultSequence executeQuery() throws XQException {
		checkNotClosed();
		  try {
	            XDMIterator iter = exp.evaluate();
            	exp = pristineCopy.copy();
	            if(connection.getStaticContext().getScrollability() == XQConstants.SCROLLTYPE_FORWARD_ONLY)
	            	return new MXQueryXQForwardSequence(iter, connection,this);
	            Vector store = new Vector();
            	int i = 0;
            	MXQueryXQForwardSequence mSeq = new MXQueryXQForwardSequence(iter, connection,this);
            	while(mSeq.next()){
            		store.add(i++,mSeq.getItem());
            	}
            	this.seq = new MXQueryXQSequence(store, connection);
            	return seq;
	        } catch (MXQueryException de) {
	        	throw new XQQueryException(de.toString(),new QName(de.getErrorCode())); 
	        }
	}

	public QName[] getAllExternalVariables() throws XQException {
		checkNotClosed();
		Vector vec = exp.getExternalVariables();
		QName [] ret = new QName[vec.size()];
		for (int i=0;i<vec.size();i++) {
			ch.ethz.mxquery.datamodel.QName qn = (ch.ethz.mxquery.datamodel.QName)vec.elementAt(i);
			if (qn.getNamespacePrefix() == null || qn.getNamespacePrefix().equals(""))
				ret[i] = new QName(qn.getLocalPart());
			else 
				ret[i] = new QName("",qn.getLocalPart(),qn.getNamespacePrefix());
		}
		return ret;
	}

	public int getQueryTimeout() throws XQException {
		checkNotClosed();
        return 0;
	}

	public XQSequenceType getStaticResultType() throws XQException {
		checkNotClosed();
		TypeInfo mxqType = exp.getStaticReturnType();
		return new MXQueryXQType(mxqType);
	}

	public XQSequenceType getStaticVariableType(QName name) throws XQException {
		// TODO Auto-generated method stub
		checkNotClosed();
		if(name == null){
			throw new XQException("Variable name cannot be null");
		}
		QName[] allVar = this.getAllExternalVariables();
		//TypeInfo type = new TypeInfo();
		int i=0;
		for(i=0; i < allVar.length; i++){
			if(allVar[i].equals(name)){
				try {
					VariableHolder var;
					if(name.getPrefix() == "")
						var = this.getRuntime().getVariable(new ch.ethz.mxquery.datamodel.QName(name.getNamespaceURI(),null, name.getLocalPart()));
					else 
						var = this.getRuntime().getVariable(new ch.ethz.mxquery.datamodel.QName(name.getNamespaceURI(), name.getPrefix(), name.getLocalPart()));
					TypeInfo ti = var.getType();
					return new MXQueryXQType(ti);
				} catch (MXQueryException e) {
					throw new XQException(e.toString());
				}
			}
		}
		if(i >= allVar.length)
			throw new XQException("the variable does not exist in the static context of the expression");
		return null;
	}

	public boolean isClosed() {
		return closed;
	}

	public void setQueryTimeout(int seconds) throws XQException {
		if (seconds < 0)
			throw new XQException("Invalid value for Query Timeout");

	}


	//@Override
	protected Context getRuntime() {
		if(exp instanceof PreparedStatementImpl){
			return (Context)((PreparedStatementImpl)this.exp).getContext();
		}
		return null;
	}

	public QName[] getAllUnboundExternalVariables() throws XQException {
		checkNotClosed();
		Vector vec = exp.getUnresolvedExternalVariables();
		QName [] ret = new QName[vec.size()];
		for (int i=0;i<vec.size();i++) {
			ch.ethz.mxquery.datamodel.QName qn = (ch.ethz.mxquery.datamodel.QName)vec.elementAt(i);
			if (qn.getNamespacePrefix() == null || qn.getNamespacePrefix().equals(""))
				ret[i] = new QName(qn.getLocalPart());
			else 
				ret[i] = new QName(qn.getNamespaceURI(),qn.getLocalPart(),qn.getNamespacePrefix());
		}
		return ret;
	}

	public XQStaticContext getStaticContext() throws XQException {
		checkNotClosed();
		return new MXQueryXQStaticContext(getRuntime());
	}

}

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

import java.io.PrintWriter;
import java.sql.Connection;
import java.util.Properties;

//import javax.xml.xquery.XQCommonHandler;
import javax.xml.xquery.XQConnection;
import javax.xml.xquery.XQDataSource;
import javax.xml.xquery.XQException;

public class MXQueryXQDataSource implements
		XQDataSource {
	
	PrintWriter logger;
	
	public MXQueryXQDataSource() {	
	}
	
	
	public XQConnection getConnection() throws XQException {
		return new MXQueryXQConnection(this);
	}

	public XQConnection getConnection(Connection con) throws XQException {
		throw new XQException("MXQuery cannot connect to a JDBC data source");
	}

	public XQConnection getConnection(String username, String password)
			throws XQException {
		return getConnection();
	}

	public PrintWriter getLogWriter() {
		return logger;
	}

	public int getLoginTimeout() {
		return 0;
	}

	public String getProperty(String name) throws XQException {
		throw new XQException("Getting property "+name+" not supported");
	}

	public String[] getSupportedPropertyNames() {
        String[] names =
        { };
       return names;
	}

	public void setLogWriter(PrintWriter out) throws XQException {
		logger = out;
	}

	public void setLoginTimeout(int seconds) throws XQException {
		// No-Op
	}

	public void setProperties(Properties props) throws XQException {
		throw new XQException("Setting properties not supported");
	}

	public void setProperty(String name, String value) throws XQException {
		throw new XQException("Setting property "+name+" not supported");

	}

}

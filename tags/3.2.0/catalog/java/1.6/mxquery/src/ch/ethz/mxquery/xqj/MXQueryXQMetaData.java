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

import java.util.Set;

import javax.xml.xquery.XQException;
import javax.xml.xquery.XQMetaData;

public class MXQueryXQMetaData implements XQMetaData {

	MXQueryXQConnection connection;
	
	public MXQueryXQMetaData(MXQueryXQConnection connection) {
		this.connection = connection;
	}

	public int getMaxExpressionLength() throws XQException {
		// TODO Auto-generated method stub
		connection.checkNotClosed();
		return 0;
	}

	public int getMaxUserNameLength() throws XQException {
		// TODO Auto-generated method stub
		connection.checkNotClosed();
		return 0;
	}

	public int getProductMajorVersion() throws XQException {
		connection.checkNotClosed();
		return 1;
	}

	public int getProductMinorVersion() throws XQException {
		connection.checkNotClosed();
		return 4;
	}

	public String getProductName() throws XQException {
		connection.checkNotClosed();
		return "MXQuery";
	}

	public String getProductVersion() throws XQException {
		connection.checkNotClosed();
		return "0.6.0";
	}

	public Set getSupportedXQueryEncodings() throws XQException {
		// TODO Auto-generated method stub
		connection.checkNotClosed();
		return null;
	}

	public String getUserName() throws XQException {
		// TODO Auto-generated method stub
		connection.checkNotClosed();
		return null;
	}

	public int getXQJMajorVersion() throws XQException {
		connection.checkNotClosed();
		return 1;
	}

	public int getXQJMinorVersion() throws XQException {
		connection.checkNotClosed();
		return 0;
	}

	public String getXQJVersion() throws XQException {
		connection.checkNotClosed();
		return "1.0";
	}

	public boolean isFullAxisFeatureSupported() throws XQException {
		connection.checkNotClosed();
		return false;
	}

	public boolean isModuleFeatureSupported() throws XQException {
		connection.checkNotClosed();
		return true;
	}

	public boolean isReadOnly() throws XQException {
		//TODO: Real checking
		connection.checkNotClosed();
		return true;
	}

	public boolean isSchemaImportFeatureSupported() throws XQException {
		connection.checkNotClosed();
		return true;
	}

	public boolean isSchemaValidationFeatureSupported() throws XQException {
		connection.checkNotClosed();
		return true;
	}

	public boolean isSerializationFeatureSupported() throws XQException {
		connection.checkNotClosed();
		return true;
	}

	public boolean isStaticTypingExtensionsSupported() throws XQException {
		connection.checkNotClosed();
		return false;
	}

	public boolean isStaticTypingFeatureSupported() throws XQException {
		connection.checkNotClosed();
		return false;
	}

	public boolean isTransactionSupported() throws XQException {
		connection.checkNotClosed();
		return false;
	}

	public boolean isUserDefinedXMLSchemaTypeSupported() throws XQException {
		connection.checkNotClosed();
		return false;
	}

	public boolean isXQueryEncodingDeclSupported() throws XQException {
		// TODO Auto-generated method stub
		connection.checkNotClosed();
		return false;
	}

	public boolean isXQueryEncodingSupported(String encoding)
			throws XQException {
		// TODO Auto-generated method stub
		connection.checkNotClosed();
		return false;
	}

	public boolean isXQueryXSupported() throws XQException {
		connection.checkNotClosed();
		return false;
	}

	public boolean wasCreatedFromJDBCConnection() throws XQException {
		connection.checkNotClosed();
		return false;
	}

}

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

import java.io.IOException;
import java.io.OutputStream;
import java.io.OutputStreamWriter;
import java.io.Writer;
import java.net.URI;
import java.util.Properties;

import org.w3c.dom.Node;
import org.xml.sax.ContentHandler;

import javax.xml.stream.XMLStreamReader;
import javax.xml.transform.Result;
import javax.xml.transform.sax.SAXResult;
import javax.xml.transform.stream.StreamResult;
import javax.xml.xquery.XQConnection;
import javax.xml.xquery.XQException;
import javax.xml.xquery.XQItemType;
import javax.xml.xquery.XQResultItem;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.xdmio.xmlAdapters.Token2StaxAdapter;

public class MXQueryXQItem implements XQResultItem {

	XDMIterator it;
	MXQueryXQConnection conn;
	Item curIt;
	boolean closed = false;
	
	MXQueryXQItem(XDMIterator iterator, MXQueryXQConnection connection, Item it){
		this.it = iterator;
		conn = connection;
			curIt = it;
	}
	
    protected void checkNotClosed() throws XQException {
        if (conn.isClosed()) {
            close();
        }
        if (isClosed()) {
            throw new XQException("Expression has been closed");
        }
    }	
	

	public void close() throws XQException {
		closed = true;
	}

	public boolean isClosed() {
		return closed;
	}

	public String getAtomicValue() throws XQException {
		return this.curIt.getAtomicValue();
	}

	public boolean getBoolean() throws XQException {
		return this.curIt.getBoolean();	
	}

	public byte getByte() throws XQException {
		return this.curIt.getByte();
	}

	public double getDouble() throws XQException {
		return this.curIt.getDouble();
	}

	public float getFloat() throws XQException {
		return this.curIt.getFloat();
	}

	public int getInt() throws XQException {
		return this.curIt.getInt();
	}

	public XQItemType getItemType() throws XQException {
		return new MXQueryXQType(curIt.getType());
	}

	public long getLong() throws XQException {
		return this.curIt.getLong();
	}

	public Node getNode() throws XQException {
		// TODO Auto-generated method stub
		return null;
	}

	public URI getNodeUri() throws XQException {
		// TODO Auto-generated method stub
		return null;
	}

	public Object getObject() throws XQException {
		return this.curIt.getItemAsString();
	}

	public short getShort() throws XQException {
		return this.curIt.getShort();
	}

	public boolean instanceOf(XQItemType type) throws XQException {
		TypeInfo ti = this.curIt.getType();
		int mxqType = ((Integer)MXQueryXQDataFactory.XQJtoMXQueryBT.get(new Integer(type.getBaseType()))).intValue();
		if (Type.isTypeOrSubTypeOf(ti.getType(),mxqType, null))
			return true;
		else return false;
	}

	public void writeItem(OutputStream os, Properties props) throws XQException {
		if (os == null)
			throw new XQException("Writer is null");
		OutputStreamWriter ow = new OutputStreamWriter(os);
		try {
			String res = curIt.getItemAsString();
			ow.write(res,0,res.length());
			ow.flush();
		} catch (IOException e) {
			throw new XQException("Could not write to writer: "+e.getMessage());
		}
	}

	public void writeItem(Writer ow, Properties props) throws XQException {
		if (ow == null)
			throw new XQException("Writer is null");
		try {
			String res = curIt.getItemAsString();
			ow.write(res,0,res.length());
			ow.flush();
		} catch (IOException e) {
			throw new XQException("Could not write to writer: "+e.getMessage());
		}

	}

	public void writeItemToSAX(ContentHandler saxHandler) throws XQException {
		// TODO Auto-generated method stub

	}

	public XQConnection getConnection() throws XQException {
		checkNotClosed();
		return conn;
	}

	public XMLStreamReader getItemAsStream() throws XQException {
		return new Token2StaxAdapter(new Context(), curIt.getAsIterator(), true);
	}

	public String getItemAsString(Properties props) throws XQException {
		return this.curIt.getItemAsString();
	}

	public void writeItemToResult(Result result) throws XQException {
		if (result == null)
			throw new XQException("Cannot write item into null Result");
		
		if (result instanceof StreamResult) {
			StreamResult str = (StreamResult) result;
			if (str.getOutputStream() != null)
				writeItem(str.getOutputStream(),null);
			if (str.getWriter() != null) {
				Writer ow = str.getWriter();
				try {
					String res = curIt.getItemAsString();
					ow.write(res,0,res.length());
					ow.flush();
				} catch (IOException e) {
					throw new XQException("Could not write to writer: "+e.getMessage());
				}
			}
			return;
		}
		if (result instanceof SAXResult) {
			SAXResult sar = (SAXResult) result;
			writeItemToSAX(sar.getHandler());
			return;
		}
		throw new XQException("Unsupported type of Result requested - please use StreamResult");
	}

}

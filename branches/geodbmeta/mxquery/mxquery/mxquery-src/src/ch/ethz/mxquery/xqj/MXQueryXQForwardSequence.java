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
import java.io.Writer;
import java.net.URI;
import java.util.Properties;
import java.util.Vector;

import org.w3c.dom.Node;
import org.xml.sax.ContentHandler;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.exceptions.MXQueryException;
//import javax.xml.xquery.XQCommonHandler;
import javax.xml.namespace.QName;
import javax.xml.stream.XMLStreamReader;
import javax.xml.transform.Result;
import javax.xml.xquery.XQConnection;
import javax.xml.xquery.XQException;
import javax.xml.xquery.XQItem;
import javax.xml.xquery.XQItemType;
import javax.xml.xquery.XQQueryException;
import javax.xml.xquery.XQResultSequence;
//import javax.xml.xquery.XQWarning;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.xdmio.xmlAdapters.Token2StaxAdapter;

public class MXQueryXQForwardSequence implements XQResultSequence {

	//private Iterator iterator;
	ItemAccessor ia;
	private MXQueryXQConnection connection;
	private MXQueryXQDynamicContext expression;
	int position = 0;   // set to -count when positioned after the end
	boolean closed = false;
	boolean retrieved = false;
	
	private Vector store = new Vector();

	protected XQItem resultItem;

	protected MXQueryXQForwardSequence(XDMIterator iterator, MXQueryXQConnection connection, MXQueryXQDynamicContext expr) {
		//this.iterator = iterator;
		ia = new ItemAccessor(iterator);
		this.connection = connection;
		expression = expr;
	}

	
	
	public XQConnection getConnection() throws XQException{
		checkNotClosed();
		return connection;
	}

	public String getAtomicValue() throws XQException {
		
		return getCurrentXQItem(true).getAtomicValue();
	}

	public boolean getBoolean() throws XQException {
		
		return getCurrentXQItem(true).getBoolean();
	}

	public byte getByte() throws XQException {
		
		return getCurrentXQItem(true).getByte();
	}

	public double getDouble() throws XQException {
		
		return getCurrentXQItem(true).getDouble();
	}

	public float getFloat() throws XQException {
		
		return getCurrentXQItem(true).getFloat();
	}

	public int getInt() throws XQException {
		
		return getCurrentXQItem(true).getInt();
	}

	public XQItemType getItemType() throws XQException {
		
		return getCurrentXQItem(false).getItemType();
	}

	public long getLong() throws XQException {
		
		return getCurrentXQItem(true).getLong();
	}

	public Node getNode() throws XQException {
		
		return getCurrentXQItem(true).getNode();
	}

	public URI getNodeUri() throws XQException {
		
		return getCurrentXQItem(true).getNodeUri();
	}

	public Object getObject() throws XQException {
		
		return getCurrentXQItem(true).getObject();
	}


	public short getShort() throws XQException {
		
		return getCurrentXQItem(true).getShort();
	}

	public boolean instanceOf(XQItemType type) throws XQException {
		return getCurrentXQItem(false).instanceOf(type);
	}
	
	public boolean absolute(int itempos) throws XQException {
		throw new XQException("Sequence is forwards-only");
	}

	public void afterLast() throws XQException {
		throw new XQException("Sequence is forwards-only");
	}
	public void beforeFirst() throws XQException {
		throw new XQException("Sequence is forwards-only");
	}

	public void close() throws XQException {
		closed = true;
		for(int i = 0; i < this.store.size(); i++){
			((MXQueryXQItem)store.get(i)).close();
		}
		this.store.removeAllElements();
		ia = null;
	}

	public int count() throws XQException {
		throw new XQException("Sequence is forwards-only");
	}

	public boolean first() throws XQException {
		throw new XQException("Sequence is forwards-only");
	}

	public XQItem getItem() throws XQException {
		checkNotClosed();
		checkAndSetRetrieved();
		if(this.resultItem == null)
			throw new XQException("Error in retrieving item!");
		return resultItem;
	}

	public int getPosition() throws XQException {
		throw new XQException("Sequence is forwards-only");
	}

	public String getSequenceAsString(Properties props) throws XQException {
		checkNotClosed();
		StringBuffer sb = new StringBuffer();
		if(this.retrieved){
			throw new XQException("Forward only sequence, a get or write method has already been invoked on the current item");
		}
		if(this.position == 0)
			next();
		sb.append(getCurrentXQItem(true).getItemAsString(props));
		while (next()) {
        	sb.append(" " + getCurrentXQItem(true).getItemAsString(props));
        }
		return sb.toString();
	}

	public boolean isAfterLast() throws XQException {
		throw new XQException("Sequence is forwards-only");
	}

	public boolean isBeforeFirst() throws XQException {
		throw new XQException("Sequence is forwards-only");
	}

	public boolean isClosed() {
		  if (connection.isClosed() || expression.isClosed()) {
	            closed = true;
	        }
		  return closed;
	}

	public boolean isFirst() throws XQException {
		throw new XQException("Sequence is forwards-only");
	}

	public boolean isLast() throws XQException {
		throw new XQException("Sequence is forwards-only");
	}

	public boolean isOnItem() throws XQException {
		checkNotClosed();
		return position > 0;
	}

	public boolean isScrollable() throws XQException {
		checkNotClosed();
		return false;
	}

	public boolean last() throws XQException {
		throw new XQException("Sequence is forwards-only");
	}

	public boolean next() throws XQException {
		checkNotClosed();
		if (position < 0) {
			return false;
		}
		try {
			Item it = (Item) ia.next();
			if(it == null){
				position = -1;
				return false;
			} else {
				position++;
				resultItem = new MXQueryXQItem( ia.getIterator(), connection, it);
				this.store.add(resultItem);
				retrieved = false;
				return true;
			}
//			if (tok.getEventType() == Type.END_SEQUENCE) {
//				position = -1;
//				return false;
//			} else {
//				position++;
//				resultItem = new MXQueryXQItem(iterator, connection, tok);
//				return true;
//			}
		} catch (MXQueryException e) {
			throw new XQQueryException(e.toString(),new QName(e.getErrorCode()));
		}
	}

	public boolean previous() throws XQException {
		throw new XQException("Sequence is forwards-only");
	}

	public boolean relative(int itempos) throws XQException {
		throw new XQException("Sequence is forwards-only");
	}

	public void writeItem(OutputStream os, Properties props) throws XQException {
		checkNotClosed();
		getCurrentXQItem(true).writeItem(os, props);
	}

	public void writeItem(Writer ow, Properties props) throws XQException {
		checkNotClosed();
		getCurrentXQItem(true).writeItem(ow, props);
	}

	public void writeItemToSAX(ContentHandler saxHandler) throws XQException {
		getCurrentXQItem(true).writeItemToSAX(saxHandler);

	}

	public void writeItemToResult(Result result) throws XQException {
		getCurrentXQItem(true).writeItemToResult(result);
		
	}
	
	public void writeSequence(OutputStream os, Properties props)
	throws XQException {
		checkNotClosed();
		if(this.position == 0)
			this.next();
		do {
			getCurrentXQItem(true).writeItem(os, props);
			try {
				os.write(' ');
			} catch (IOException e) {
				throw new XQQueryException("Could not write sequence "+e);
			}
		} while(next());
	}

	public void writeSequence(Writer ow, Properties props) throws XQException {
		checkNotClosed();
		if(this.position == 0)
			this.next();
		do {
			getCurrentXQItem(true).writeItem(ow, props);
			try {
				ow.write(' ');
			} catch (IOException e) {
				throw new XQQueryException("Could not write sequence "+e);
			}
		} while(next());			
	}

	public void writeSequenceToSAX(ContentHandler saxhdlr) throws XQException {
		checkNotClosed();
		if (position == 0) {
			next();
		}
		if (position < 0) {
			throw new XQException("The XQSequence is positioned after the last item");
		}
		do {
			checkAndSetRetrieved();
			resultItem.writeItemToSAX(saxhdlr);
		} while(next());
	}
    protected void checkNotClosed() throws XQException {
        if (connection.isClosed()) {
            close();
        }
        if (isClosed()) {
            throw new XQException("Sequence has been closed");
        }
    }	
    
    protected void checkAndSetRetrieved() throws XQException{
    	if (retrieved) {
    		throw new XQException("Item has already been retrieved");
    	}
    	retrieved = true;
    }
    
	private XQItem getCurrentXQItem(boolean retrievedCheck) throws XQException {
		checkNotClosed();
		if (retrievedCheck)
			checkAndSetRetrieved();
		if (position == 0) {
			throw new XQException("The XQSequence is positioned before the first item");
		} else if (position < 0) {
			throw new XQException("The XQSequence is positioned after the last item");
		}
		return resultItem;
	}

	public XMLStreamReader getSequenceAsStream() throws XQException {
		checkNotClosed();
		checkAndSetRetrieved();
		position = -1;
		return new Token2StaxAdapter(new Context(), ia.getIterator(), true);
	}

	public void writeSequenceToResult(Result result) throws XQException {
		checkNotClosed();
		if (position == 0) {
			next();
		}
		if (position < 0) {
			throw new XQException("The XQSequence is positioned after the last item");
		}
		do {
			checkAndSetRetrieved();
			resultItem.writeItemToResult(result);
		} while(next());
	}

	public XMLStreamReader getItemAsStream() throws XQException {
		checkNotClosed();
		if (position == 0) {
			throw new XQException("The XQSequence is positioned before the first item");
		} else if (position < 0) {
			throw new XQException("The XQSequence is positioned after the last item");
		}		 
		checkAndSetRetrieved();
		return resultItem.getItemAsStream();
	}

	public String getItemAsString(Properties props) throws XQException {
		// TODO Auto-generated method stub
		return getCurrentXQItem(true).getItemAsString(props);
	}


}

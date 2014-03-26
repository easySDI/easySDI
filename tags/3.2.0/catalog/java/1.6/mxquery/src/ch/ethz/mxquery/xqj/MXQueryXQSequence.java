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

//import javax.xml.xquery.XQCommonHandler;
import javax.xml.stream.XMLStreamReader;
import javax.xml.transform.Result;
import javax.xml.xquery.XQConnection;
import javax.xml.xquery.XQException;
import javax.xml.xquery.XQItem;
import javax.xml.xquery.XQItemType;
import javax.xml.xquery.XQQueryException;
import javax.xml.xquery.XQResultSequence;
//import javax.xml.xquery.XQWarning;

public class MXQueryXQSequence implements XQResultSequence {
	
	private int position = 0;
	private boolean closed = false;
	
	private MXQueryXQConnection connection;
	private XQItem currentItem;
	private Vector store;
	
	MXQueryXQSequence(Vector s, MXQueryXQConnection conn){
		
		this.store = s;
		this.connection = conn;
	}

	public void clearWarnings() {
		// TODO Auto-generated method stub

	}

	public XQConnection getConnection() throws XQException {
		this.checkNotClosed();
		return connection;
		
	}
	
	public String getAtomicValue() throws XQException {
		return getCurrentXQItem().getAtomicValue();
	}

	public boolean getBoolean() throws XQException {
		return getCurrentXQItem().getBoolean();
	}

	public byte getByte() throws XQException {
		return getCurrentXQItem().getByte();
	}

	public double getDouble() throws XQException {
		return getCurrentXQItem().getDouble();
	}

	public float getFloat() throws XQException {
		return getCurrentXQItem().getFloat();
	}

	public int getInt() throws XQException {
		return getCurrentXQItem().getInt();
	}

	public XQItemType getItemType() throws XQException {
		return getCurrentXQItem().getItemType();
	}

	public long getLong() throws XQException {
		return getCurrentXQItem().getLong();
	}

	public Node getNode() throws XQException {
		return getCurrentXQItem().getNode();
	}

	public URI getNodeUri() throws XQException {
		return getCurrentXQItem().getNodeUri();
	}

	public Object getObject() throws XQException {
		return getCurrentXQItem().getObject();
	}

	public short getShort() throws XQException {
		return getCurrentXQItem().getShort();
	}

	public boolean instanceOf(XQItemType type) throws XQException {
		return getCurrentXQItem().instanceOf(type);
	}

	public void writeItem(OutputStream os, Properties props) throws XQException {
		getCurrentXQItem().writeItem(os, props);

	}

	public void writeItem(Writer ow, Properties props) throws XQException {
		checkNotClosed();
		getCurrentXQItem().writeItem(ow, props);
	}

	public void writeItemToSAX(ContentHandler saxHandler) throws XQException {
		getCurrentXQItem().writeItemToSAX(saxHandler);
	}

	public boolean absolute(int itempos) throws XQException {
		checkNotClosed();
		if(itempos > 0){
			this.position = itempos;			
		} else if (itempos == 0){
			this.position = itempos;
		} else {
			this.position = this.store.size() + itempos + 1;
		}
		if(this.position < 1 || this.position > this.store.size()){
			return false;
		}
		return true;
	}

	public void afterLast() throws XQException {
		checkNotClosed();
		position = this.store.size()+1;
	}

	public void beforeFirst() throws XQException {
		checkNotClosed();
		this.position = 0;
	}

	public void close() throws XQException {
		closed = true;
		for(int i = 0; i < this.store.size(); i++){
			((MXQueryXQItem)store.get(i)).close();
		}
		this.store.removeAllElements();
	}

	public int count() throws XQException {
		checkNotClosed();
		return this.store.size();
	}

	public boolean first() throws XQException {
		checkNotClosed();
		if(this.store.size() < 1){
			return false;
		}
		this.position = 1;
		return true;
	}

	public XQItem getItem() throws XQException {
		checkNotClosed();
		if(this.position < 1 || this.position > this.store.size()){
			throw new XQException("Error in retrieving item!");
		}
		this.currentItem = (XQItem) this.store.get(position-1);
		return this.currentItem;
	}

	public int getPosition() throws XQException {
		checkNotClosed();
		return this.position;
	}

	public String getSequenceAsString(Properties props) throws XQException {
		checkNotClosed();
		StringBuffer sb = new StringBuffer();
		if(this.position == 0)
			next();
		sb.append(getCurrentXQItem().getItemAsString(props));
		while (next()) {
        	sb.append(" " + getCurrentXQItem().getItemAsString(props));
        }
		return sb.toString();
	}

	public boolean isAfterLast() throws XQException {
		checkNotClosed();
		if(this.position == this.store.size()+1){
			return true;
		}
		return false;
	}

	public boolean isBeforeFirst() throws XQException {
		checkNotClosed();
		if(this.store.size() == 0){
			return false;
		}
		if(this.position == 0){
			return true;
		}
		return false;
	}

	public boolean isClosed() {
		return closed;
	}

	public boolean isFirst() throws XQException {
		checkNotClosed();
		if(this.position == 1){
			return true;
		}
		return false;
	}

	public boolean isLast() throws XQException {
		checkNotClosed();
		if(this.store.size() == 0){
			return false;
		}
		if(this.position == this.store.size()){
			return true;
		}
		return false;
	}

	public boolean isOnItem() throws XQException {
		checkNotClosed();
		if(this.position < 1 || this.position > this.store.size()){
			return false;
		}
		return true;
	}

	public boolean isScrollable() throws XQException {
		checkNotClosed();
		return true;
	}

	public boolean last() throws XQException {
		checkNotClosed();
		if(this.store.size() < 1){
			return false;
		}
		this.position = this.store.size();
		return true;
	}

	public boolean next() throws XQException {
		checkNotClosed();
		this.position++;
		if(this.position < 1 || this.position > this.store.size()){
			return false;
		}
		return true;
	}

	public boolean previous() throws XQException {
		checkNotClosed();
		if(this.position == -1){
			this.position = this.store.size()+1;
		}
		if(this.position <= 1){
			return false;
		}
		this.position--; 
		return true;
	}

	public boolean relative(int itempos) throws XQException {
		checkNotClosed();
		this.position = this.position + itempos;
		if(this.position < 1){ 
			this.position = 0;
			return false;
		}
		if(this.position > this.store.size()){
			this.position = this.store.size() + 1;
			return false;
		}
		//this.position = pos;
		return true;
	}

	public void writeSequence(OutputStream os, Properties props)
			throws XQException {
		checkNotClosed();
		if(this.position == 0)
			this.next();
		do {
			getCurrentXQItem().writeItem(os, props);
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
			getCurrentXQItem().writeItem(ow, props);
			try {
				ow.write(' ');
			} catch (IOException e) {
				throw new XQQueryException("Could not write sequence "+e);
			}
		} while(next());
	}

	public void writeSequenceToSAX(ContentHandler saxhdlr) throws XQException {
		// TODO Auto-generated method stub

	}
	
	private void checkNotClosed() throws XQException {
		if (closed || this.connection.isClosed()) {
			throw new XQException("The XQSequence has been closed");
		}
	}
	private XQItem getCurrentXQItem() throws XQException {
		checkNotClosed();
		if (position == 0) {
			throw new XQException("The XQSequence is positioned before the first item");
		} else if (position < 0) {
			throw new XQException("The XQSequence is positioned after the last item");
		}
		this.currentItem = (XQItem) this.store.get(position-1);
		return this.currentItem;
	}

	public XMLStreamReader getSequenceAsStream() throws XQException {
		checkNotClosed();
		position = this.count() + 1;
		return null;
	}

	public void writeSequenceToResult(Result result) throws XQException {
		// TODO Auto-generated method stub
		
	}

	public XMLStreamReader getItemAsStream() throws XQException {
		checkNotClosed();
		if (isOnItem()) {
			return getCurrentXQItem().getItemAsStream();
		} else
			throw new XQException("The XQSequence is not positioned on an item");
	}

	public String getItemAsString(Properties props) throws XQException {
		this.checkNotClosed();
		return getCurrentXQItem().getItemAsString(props);
	}

	public void writeItemToResult(Result result) throws XQException {
		getCurrentXQItem().writeItemToResult(result);
	}

}

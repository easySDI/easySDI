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

import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import javax.xml.xquery.XQException;

import ch.ethz.mxquery.model.TokenSequenceIterator;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.util.Utils;
import ch.ethz.mxquery.xdmio.XDMSerializer;
import ch.ethz.mxquery.xdmio.XDMSerializerSettings;

public class TreeItem implements Item {
	
	Vector v;
	//boolean startTagStarted;
	//boolean endTagFound;
	private String result;
	private boolean closed = false;
	
	public TreeItem(Vector v){
		this.v = v;
	}

	public void close() {
		this.closed = true;
		v=null;
	}

	public String getAtomicValue() throws XQException {
		throw new XQException("Not an Atomic Item");
	}

	public boolean getBoolean() throws XQException {
		throw new XQException("Not an Atomic Item");
	}

	public byte getByte() throws XQException {
		throw new XQException("Not an Atomic Item");
	}

	public double getDouble() throws XQException {
		throw new XQException("Not an Atomic Item");
	}

	public float getFloat() throws XQException {
		throw new XQException("Not an Atomic Item");
	}

	public int getInt() throws XQException {
		throw new XQException("Not an Atomic Item");
	}

	public String getItemAsString() throws XQException{
		if(this.result != null){
			return this.result;
		}
		XDMSerializerSettings ser = new XDMSerializerSettings();
		ser.setOmitXMLDeclaration(true);
		XDMSerializer ip = new XDMSerializer(ser);
		TokenSequenceIterator to = new TokenSequenceIterator(v);
		String sb;
		try {
			sb = ip.eventsToXML(to);
		} catch (MXQueryException e) {
			throw new XQException("Error getting string value");
		}
		result = sb;
		return result;
	}
	
	public XDMIterator getAsIterator() {
		return new TokenSequenceIterator(v);
	}
	
	public String getSerializedValue(Token tok) throws MXQueryException {

		int t = tok.getEventType();
		boolean isAttribute = Type.isAttribute(t);
		
		if ( isAttribute )
			t = Type.getAttributeValueType(t);
		else
		if ( Type.isTextNode(t) )
			t = Type.getTextNodeValueType(t);
		
		if ( t == Type.STRING || t == Type.UNTYPED_ATOMIC || t == Type.ANY_URI || t == Type.UNTYPED) {
			if (isAttribute) 
				return changeEntityRef( tok.getText(), true );
			else
				return changeEntityRef( tok.getText(), false );		
		}
		else return tok.getValueAsString();
			
	}
	
	private String changeEntityRef(String val, boolean attribute){

//		System.out.println("val: " + val);
		
		val = Utils.replaceAll(val, "&", "&amp;");
		val = Utils.replaceAll(val, ">", "&gt;");
		val = Utils.replaceAll(val, "<", "&lt;");
		//val = Utils.replaceAll(val, "'", "&apos;");
		if (attribute)
			val = Utils.replaceAll(val, "\"", "&quot;");
		
		return val;		
	}

	public long getLong() throws XQException {
		throw new XQException("Not an Atomic Item");
	}

	public short getShort() throws XQException {
		throw new XQException("Not an Atomic Item");
	}

	public boolean isClosed() {
		return this.closed;
	}

	public TypeInfo getType() {
		return new TypeInfo(((Token)v.elementAt(0)).getEventType(),Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
	}

}

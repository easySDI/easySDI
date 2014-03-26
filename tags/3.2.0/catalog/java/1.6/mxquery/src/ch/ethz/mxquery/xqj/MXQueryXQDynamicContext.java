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

import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.Reader;
//import java.util.Date;
import java.util.SimpleTimeZone;
import java.util.TimeZone;

import javax.xml.namespace.QName;
import javax.xml.stream.XMLStreamReader;
import javax.xml.transform.Source;
import javax.xml.transform.dom.DOMSource;
import javax.xml.transform.sax.SAXSource;
import javax.xml.transform.stream.StreamSource;

import org.w3c.dom.Node;
import org.xml.sax.InputSource;
import org.xml.sax.XMLReader;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.MXQueryDouble;
import ch.ethz.mxquery.datamodel.MXQueryFloat;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.iterators.TokenIterator;
import ch.ethz.mxquery.util.StringReader;
import ch.ethz.mxquery.xdmio.XDMInputFactory;
import ch.ethz.mxquery.xdmio.XMLSource;

import javax.xml.xquery.XQDynamicContext;
import javax.xml.xquery.XQException;
import javax.xml.xquery.XQItem;
import javax.xml.xquery.XQItemType;
import javax.xml.xquery.XQQueryException;
import javax.xml.xquery.XQSequence;


public abstract class MXQueryXQDynamicContext implements XQDynamicContext {
	
	protected boolean closed;
	
	protected abstract void checkNotClosed() throws XQException;
	
	protected abstract Context getRuntime();
	
	protected TimeZone tz;

	public boolean isClosed() {
		return closed;
	}
	
	public void bindAtomicValue(QName varName, String value, XQItemType type)
			throws XQException {
		checkNotClosed();
		if (varName == null || value == null || type == null)
			throw new XQException("varName is null");
		if(Type.BOOLEAN  == MXQueryXQDataFactory.getMXQueryType(type)){
			try{
				this.bindBoolean(varName, (new Boolean(value)).booleanValue(), type);
			} catch(Exception e){
				throw new XQException("Value cannot be casted to desired type!");
			}
		} else if(Type.isTypeOrSubTypeOf(Type.INT, MXQueryXQDataFactory.getMXQueryType(type), null)){
			long lv = 0L;
			try {
				lv = Long.parseLong(value);
			} catch (NumberFormatException e) {
				throw new XQException("Could not case to requested type "+e.toString());
			}
			this.bindIntegerVal(varName, lv, type);
		} else if(Type.isTypeOrSubTypeOf(MXQueryXQDataFactory.getMXQueryType(type), Type.STRING, null)){
			this.bindString(varName, value, type);
		} else if(Type.FLOAT == MXQueryXQDataFactory.getMXQueryType(type)){
			try{
				this.bindFloat(varName, Float.parseFloat(value), type);
			} catch(Exception e){
				throw new XQException("Value cannot be casted to desired type!");
			}
		} else if(Type.DOUBLE == MXQueryXQDataFactory.getMXQueryType(type)){
			try{
				this.bindDouble(varName, Double.parseDouble(value), type);
			} catch(Exception e){
				throw new XQException("Value cannot be casted to desired type!");
			}
		} else {
			throw new XQException("Not an atomic type!");
		}
	}

	public void bindBoolean(QName varName, boolean value, XQItemType type)
			throws XQException {
		checkNotClosed();
		if (varName == null)
			throw new XQException("VarName to bind is null");
		if(type == null || (Type.BOOLEAN  == MXQueryXQDataFactory.getMXQueryType(type))){
			try {
				if(this instanceof MXQueryXQPreparedExpression)
					this.getRuntime().setVariableValue(new ch.ethz.mxquery.datamodel.QName(varName.toString()), 
						new TokenIterator(getRuntime(),value,null), true, true);
				else 
					this.getRuntime().setVariableValue(new ch.ethz.mxquery.datamodel.QName(varName.toString()), 
							new TokenIterator(getRuntime(),value,null), false, true);
			} catch (MXQueryException e) {
				throw new XQQueryException(e.toString(),new QName(e.getErrorCode()));
			}
		} else {
			throw new XQException("Wrong type");
		}
	}

	public void bindByte(QName varName, byte value, XQItemType type)
			throws XQException {
		bindIntegerVal(varName, value, type);
	}

	public void bindContextItem(XQItem contextitem) throws XQException {
		checkNotClosed();
		// TODO Auto-generated method stub

	}

	public void bindDocument(QName varName, InputSource source)
			throws XQException {
		checkNotClosed();
		if (varName == null)
			throw new XQException("varName is null");

		// TODO Auto-generated method stub

	}

	public void bindDouble(QName varName, double value, XQItemType type)
			throws XQException {
		checkNotClosed();
		if (varName == null)
			throw new XQException("varName is null");
		if(type == null || (Type.DOUBLE == MXQueryXQDataFactory.getMXQueryType(type))){
			try {
				if(this instanceof MXQueryXQPreparedExpression)
					this.getRuntime().setVariableValue(new ch.ethz.mxquery.datamodel.QName(varName.toString()), new TokenIterator(getRuntime(),new MXQueryDouble(value),null), true, true);
				else 
					this.getRuntime().setVariableValue(new ch.ethz.mxquery.datamodel.QName(varName.toString()), new TokenIterator(getRuntime(),new MXQueryDouble(value),null), false, true);
			} catch (MXQueryException e) {
				throw new XQQueryException(e.toString(),new QName(e.getErrorCode()));
			}
		} else {
			throw new XQException("Wrong type");
		}
	}
	public void bindFloat(QName varName, float value, XQItemType type)
			throws XQException {
		checkNotClosed();
		if (varName == null)
			throw new XQException("varName is null");
		if(type == null || (Type.FLOAT == MXQueryXQDataFactory.getMXQueryType(type))){
			try {
				if(this instanceof MXQueryXQPreparedExpression)
					this.getRuntime().setVariableValue(new ch.ethz.mxquery.datamodel.QName(varName.toString()), new TokenIterator(getRuntime(),new MXQueryFloat(value),Type.FLOAT,null), true, true);
				else 
					this.getRuntime().setVariableValue(new ch.ethz.mxquery.datamodel.QName(varName.toString()), new TokenIterator(getRuntime(),new MXQueryFloat(value),Type.FLOAT,null), false, true);
			} catch (MXQueryException e) {
				throw new XQQueryException(e.toString(),new QName(e.getErrorCode()));
			}
		} else {
			throw new XQException("Wrong type");
		}
	}

	public void bindInt(QName varName, int value, XQItemType type)
			throws XQException {
		bindIntegerVal(varName, value, type);
	}

	public void bindItem(QName varName, XQItem value) throws XQException {
		// TODO Auto-generated method stub
		checkNotClosed();
		if (varName == null || value == null)
			throw new XQException("varName is null");
		

	}

	public void bindLong(QName varName, long value, XQItemType type)
			throws XQException {
		bindIntegerVal(varName, value, type);
	}

	public void bindNode(QName varName, Node value, XQItemType type)
			throws XQException {		
		checkNotClosed();
		if (varName == null)
			throw new XQException("varName is null");
		if(type == null || type.getBaseType() == XQItemType.XQITEMKIND_DOCUMENT_ELEMENT || type.getBaseType() == XQItemType.XQITEMKIND_DOCUMENT_SCHEMA_ELEMENT){
			
			try {
				XMLSource xdm = XDMInputFactory.createDOMInput(new Context(), value, null);
					if(this instanceof MXQueryXQPreparedExpression)
						this.getRuntime().setVariableValue(new ch.ethz.mxquery.datamodel.QName(varName.toString()), xdm,true,true);
					else
						this.getRuntime().setVariableValue(new ch.ethz.mxquery.datamodel.QName(varName.toString()), xdm,false,true);				
				} catch (MXQueryException e) {
					throw new XQQueryException(e.toString(),new QName(e.getErrorCode()));
				}
			
		} else {
			throw new XQException("Wrong type");
		}
	}

	public void bindObject(QName varName, Object value, XQItemType type)
			throws XQException {
		checkNotClosed();
		if (varName == null)
			throw new XQException("varName is null");
		if (value == null)
			throw new XQException("value is null");
		if(type == null){
			if(value.getClass() == Boolean.class)
				this.bindBoolean(varName, Boolean.getBoolean(value.toString()), type);
			if(value.getClass() == Integer.class)
				this.bindInt(varName, Integer.parseInt("" + value), type);
			if(value.getClass() == Float.class)
				this.bindFloat(varName, Float.parseFloat("" + value), type);
			if(value.getClass() == Double.class)
				this.bindDouble(varName, Double.parseDouble("" + value), type);
			return;
		}
		this.bindAtomicValue(varName, (String)value, type);

	}

	public void bindSequence(QName varName, XQSequence value)
			throws XQException {
		// TODO Auto-generated method stub
		checkNotClosed();
		if (varName == null)
			throw new XQException("varName is null");

	}

	public void bindShort(QName varName, short value, XQItemType type)
			throws XQException {
		bindIntegerVal(varName, value, type);
	}

	public TimeZone getImplicitTimeZone() throws XQException {
		TimeZone tz = null;
		checkNotClosed();
		
		if(this.tz != null)
			return this.tz;
		
		try {
			this.getRuntime().setCurrentTime(null);
			tz = new SimpleTimeZone(getRuntime().getCurrentTimeZone().getSecondsWithMili().getIntValue(),getRuntime().getCurrentTimeZone().convertToString());
		} catch (MXQueryException me) {
			throw new XQQueryException(me.getMessage(),new QName(me.getErrorCode()));
		}
		return tz;
	}

	public void setImplicitTimeZone(TimeZone implicitTimeZone)
			throws XQException {
		checkNotClosed();
		if(implicitTimeZone == null){
			throw new XQException("implicitTimeZone value is null"); 
		}
		this.tz = implicitTimeZone;
		this.getRuntime().setCurrentTimeZone(tz);
		

	}

	public void bindDocument(QName varName, String value, String baseURI,
			XQItemType type) throws XQException {
		// TODO Auto-generated method stub
		checkNotClosed();	
		if (varName == null)
			throw new XQException("varName is null");
		if(type == null || type.getBaseType() == XQItemType.XQITEMKIND_DOCUMENT_ELEMENT || type.getBaseType() == XQItemType.XQITEMKIND_DOCUMENT_SCHEMA_ELEMENT){
			
			try {
				XMLSource xdm = XDMInputFactory.createXMLInput(new Context(), new StringReader(value), true, getRuntime().getInputValidationMode(), QueryLocation.OUTSIDE_QUERY_LOC);

					if(this instanceof MXQueryXQPreparedExpression)
						this.getRuntime().setVariableValue(new ch.ethz.mxquery.datamodel.QName(varName.toString()), xdm,true,true);
					else
						this.getRuntime().setVariableValue(new ch.ethz.mxquery.datamodel.QName(varName.toString()), xdm,false,true);				
				} catch (MXQueryException e) {
					throw new XQQueryException(e.toString(),new QName(e.getErrorCode()));
				}
			
		} else {
			throw new XQException("Wrong type");
		}
	}

	public void bindDocument(QName varName, Reader value, String baseURI,
			XQItemType type) throws XQException {
		// TODO Auto-generated method stub
		checkNotClosed();
		if (varName == null)
			throw new XQException("varName is null");
		if(type == null || type.getBaseType() == XQItemType.XQITEMKIND_DOCUMENT_ELEMENT || type.getBaseType() == XQItemType.XQITEMKIND_DOCUMENT_SCHEMA_ELEMENT){
			
			try {
				XMLSource xdm = XDMInputFactory.createXMLInput(new Context(), value, true, getRuntime().getInputValidationMode(), QueryLocation.OUTSIDE_QUERY_LOC);

					if(this instanceof MXQueryXQPreparedExpression)
						this.getRuntime().setVariableValue(new ch.ethz.mxquery.datamodel.QName(varName.toString()), xdm,true,true);
					else
						this.getRuntime().setVariableValue(new ch.ethz.mxquery.datamodel.QName(varName.toString()), xdm,false,true);				
				} catch (MXQueryException e) {
					throw new XQQueryException(e.toString(),new QName(e.getErrorCode()));
				}
			
		} else {
			throw new XQException("Wrong type");
		}
	}

	public void bindDocument(QName varName, InputStream value, String baseURI,
			XQItemType type) throws XQException {
		// TODO Auto-generated method stub
		checkNotClosed();
		if (varName == null)
			throw new XQException("varName is null");
		if(type == null || type.getBaseType() == XQItemType.XQITEMKIND_DOCUMENT_ELEMENT || type.getBaseType() == XQItemType.XQITEMKIND_DOCUMENT_SCHEMA_ELEMENT){
			
			try {
				XMLSource xdm = XDMInputFactory.createXMLInput(new Context(), new InputStreamReader(value), true, getRuntime().getInputValidationMode(), QueryLocation.OUTSIDE_QUERY_LOC);
					if(this instanceof MXQueryXQPreparedExpression)
						this.getRuntime().setVariableValue(new ch.ethz.mxquery.datamodel.QName(varName.toString()), xdm,true,true);
					else
						this.getRuntime().setVariableValue(new ch.ethz.mxquery.datamodel.QName(varName.toString()), xdm,false,true);				
				} catch (MXQueryException e) {
					throw new XQQueryException(e.toString(),new QName(e.getErrorCode()));
				}
			
		} else {
			throw new XQException("Wrong type");
		}
	}

	public void bindDocument(QName varName, XMLReader value, XQItemType type)
			throws XQException {
		checkNotClosed();
		if (varName == null)
			throw new XQException("varName is null");
		if(type == null || type.getBaseType() == XQItemType.XQITEMKIND_DOCUMENT_ELEMENT || type.getBaseType() == XQItemType.XQITEMKIND_DOCUMENT_SCHEMA_ELEMENT){
			
			try {
				XMLSource xdm = XDMInputFactory.createSAXInput(new Context(), value, null);
					if(this instanceof MXQueryXQPreparedExpression)
						this.getRuntime().setVariableValue(new ch.ethz.mxquery.datamodel.QName(varName.toString()), xdm,true,true);
					else
						this.getRuntime().setVariableValue(new ch.ethz.mxquery.datamodel.QName(varName.toString()), xdm,false,true);				
				} catch (MXQueryException e) {
					throw new XQQueryException(e.toString(),new QName(e.getErrorCode()));
				}
			
		} else {
			throw new XQException("Wrong type");
		}
	}

	public void bindDocument(QName varName, XMLStreamReader value,
			XQItemType type) throws XQException {
		checkNotClosed();
		if (varName == null)
			throw new XQException("varName is null");
		if(type == null || type.getBaseType() == XQItemType.XQITEMKIND_DOCUMENT_ELEMENT || type.getBaseType() == XQItemType.XQITEMKIND_DOCUMENT_SCHEMA_ELEMENT){
			
			try {
				XMLSource xdm = XDMInputFactory.createStaxInput(new Context(), value, null);
					if(this instanceof MXQueryXQPreparedExpression)
						this.getRuntime().setVariableValue(new ch.ethz.mxquery.datamodel.QName(varName.toString()), xdm,true,true);
					else
						this.getRuntime().setVariableValue(new ch.ethz.mxquery.datamodel.QName(varName.toString()), xdm,false,true);				
				} catch (MXQueryException e) {
					throw new XQQueryException(e.toString(),new QName(e.getErrorCode()));
				}
			
		} else {
			throw new XQException("Wrong type");
		}
	}

	public void bindDocument(QName varName, Source value, XQItemType type)
			throws XQException {
		checkNotClosed();
		if (varName == null)
			throw new XQException("varName is null");
		if (value == null)
			throw new XQException("null value not a valid input");
		if(type == null || type.getBaseType() == XQItemType.XQITEMKIND_DOCUMENT_ELEMENT || type.getBaseType() == XQItemType.XQITEMKIND_DOCUMENT_SCHEMA_ELEMENT){	
			try {
				XMLSource xdm = null;
				if (value instanceof StreamSource) {
					StreamSource  sm = (StreamSource) value;			
					xdm = XDMInputFactory.createXMLInput(new Context(), sm.getReader(), true, getRuntime().getInputValidationMode(), QueryLocation.OUTSIDE_QUERY_LOC);
					
				}
				if (value instanceof SAXSource) {
					xdm = XDMInputFactory.createSAXInput(new Context(), ((SAXSource)value).getXMLReader(), null);					
				}
//				if (value instanceof javax.xml.transform.stax.StAXSource) {
//					javax.xml.transform.stax.StAXSource st = (javax.xml.transform.stax.StAXSource) value;
//					xdm = XDMInputFactory.createStaxInput(new Context(), null, st.getXMLStreamReader());										
//				}
				
				if (value instanceof DOMSource) {
					DOMSource ds = (DOMSource) value;
					xdm = XDMInputFactory.createDOMInput(new Context(), ds.getNode(), null);											
				}
				
				if(this instanceof MXQueryXQPreparedExpression)
					this.getRuntime().setVariableValue(new ch.ethz.mxquery.datamodel.QName(varName.toString()), xdm,true,true);
				else
					this.getRuntime().setVariableValue(new ch.ethz.mxquery.datamodel.QName(varName.toString()), xdm,false,true);				
			} catch (MXQueryException e) {
				throw new XQException(e.toString());
			}
			
		} else {
			throw new XQException("Wrong type");
		}		
		
		

	}

	public void bindString(QName varName, String value, XQItemType type)
			throws XQException {
		checkNotClosed();
		if (varName == null)
			throw new XQException("varName is null");
		if(type == null || Type.isTypeOrSubTypeOf(MXQueryXQDataFactory.getMXQueryType(type), Type.STRING, null)){
			int retType = Type.STRING;
			if (type != null)
				retType = MXQueryXQDataFactory.getMXQueryType(type);
			try {
				if(this instanceof MXQueryXQPreparedExpression)
					this.getRuntime().setVariableValue(new ch.ethz.mxquery.datamodel.QName(varName.toString()), new TokenIterator(getRuntime(),value, retType, null),true,true);
				else
					this.getRuntime().setVariableValue(new ch.ethz.mxquery.datamodel.QName(varName.toString()), new TokenIterator(getRuntime(),value, retType, null),false,true);
			} catch (MXQueryException e) {
				throw new XQQueryException(e.toString(),new QName(e.getErrorCode()));
			}
		} else {
			throw new XQException("Wrong type");
		}
	}
	
	private void bindIntegerVal(QName varName, long value, XQItemType type)
	throws XQException {
		checkNotClosed();
		if (varName == null)
			throw new XQException("varName is null");
		if(type == null || Type.isTypeOrSubTypeOf(Type.INT, MXQueryXQDataFactory.getMXQueryType(type), null)){
			try {
				if(this instanceof MXQueryXQPreparedExpression)
					this.getRuntime().setVariableValue(new ch.ethz.mxquery.datamodel.QName(varName.toString()), new TokenIterator(getRuntime(),value, Type.INTEGER, null), true, true);
				else 
					this.getRuntime().setVariableValue(new ch.ethz.mxquery.datamodel.QName(varName.toString()), new TokenIterator(getRuntime(),value, Type.INTEGER, null), false, true);
			} catch (MXQueryException e) {
				throw new XQQueryException(e.toString(),new QName(e.getErrorCode()));
			}
		} else {
			throw new XQException("Wrong type");
		}
	}
}

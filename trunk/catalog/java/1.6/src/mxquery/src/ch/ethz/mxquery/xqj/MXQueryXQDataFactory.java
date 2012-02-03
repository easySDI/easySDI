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
import java.net.URI;
import java.util.Hashtable;
import java.util.Iterator;
import java.util.Vector;

import javax.xml.namespace.QName;
import javax.xml.stream.XMLStreamReader;
import javax.xml.transform.Source;
import javax.xml.transform.dom.DOMSource;
import javax.xml.transform.sax.SAXSource;
import javax.xml.transform.stream.StreamSource;

import org.w3c.dom.Node;
import org.xml.sax.InputSource;
import org.xml.sax.XMLReader;

import javax.xml.xquery.XQDataFactory;
import javax.xml.xquery.XQException;
import javax.xml.xquery.XQItem;
import javax.xml.xquery.XQItemType;
import javax.xml.xquery.XQSequence;
import javax.xml.xquery.XQSequenceType;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.MXQueryDouble;
import ch.ethz.mxquery.datamodel.MXQueryFloat;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.BooleanToken;
import ch.ethz.mxquery.datamodel.xdm.DoubleToken;
import ch.ethz.mxquery.datamodel.xdm.FloatToken;
import ch.ethz.mxquery.datamodel.xdm.LongToken;
import ch.ethz.mxquery.datamodel.xdm.TextToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.util.StringReader;
import ch.ethz.mxquery.xdmio.XDMInputFactory;
import ch.ethz.mxquery.xdmio.XMLSource;

public class MXQueryXQDataFactory implements XQDataFactory {
	
	static final Hashtable XQJtoMXQueryBT = new Hashtable();
	static final Hashtable MXQuerytoXQJBT = new Hashtable();
	
	static final Hashtable XQJtoMXQueryIK = new Hashtable();
	static final Hashtable MXQuerytoXQJIK = new Hashtable();
	
	protected boolean closed;
	
	private static void map(int x, int y){
		XQJtoMXQueryBT.put(new Integer(x), new Integer(y));
		MXQuerytoXQJBT.put(new Integer(y), new Integer(x));
	}
	
	private static void mapIK(int x, int y){
		XQJtoMXQueryIK.put(new Integer(x), new Integer(y));
		MXQuerytoXQJIK.put(new Integer(y), new Integer(x));
	}
	
	
	static{
		map(XQItemType.XQBASETYPE_ANYSIMPLETYPE, Type.ANY_SIMPLE_TYPE);
		map(XQItemType.XQBASETYPE_ANYTYPE, Type.ANY_TYPE);
		map(XQItemType.XQBASETYPE_ANYURI, Type.ANY_URI);
		map(XQItemType.XQBASETYPE_BOOLEAN, Type.BOOLEAN);
		map(XQItemType.XQBASETYPE_BASE64BINARY, Type.BASE64_BINARY);
		map(XQItemType.XQBASETYPE_BYTE, Type.BYTE);
		map(XQItemType.XQBASETYPE_DATE, Type.DATE);
		map(XQItemType.XQBASETYPE_DATETIME, Type.DATE_TIME);
		map(XQItemType.XQBASETYPE_DECIMAL, Type.DECIMAL);
		map(XQItemType.XQBASETYPE_DOUBLE, Type.DOUBLE);
		map(XQItemType.XQBASETYPE_DURATION, Type.DURATION);
		map(XQItemType.XQBASETYPE_ENTITIES, Type.ENTITIES);
		map(XQItemType.XQBASETYPE_ENTITY, Type.ENTITY);
		map(XQItemType.XQBASETYPE_FLOAT, Type.FLOAT);
		map(XQItemType.XQBASETYPE_GDAY, Type.G_DAY);
		map(XQItemType.XQBASETYPE_GMONTH, Type.G_MONTH);
		map(XQItemType.XQBASETYPE_GMONTHDAY, Type.G_MONTH_DAY);
		map(XQItemType.XQBASETYPE_GYEAR, Type.G_YEAR);
		map(XQItemType.XQBASETYPE_GYEARMONTH, Type.G_YEAR_MONTH);
		map(XQItemType.XQBASETYPE_HEXBINARY, Type.HEX_BINARY);
		map(XQItemType.XQBASETYPE_ID, Type.ID);
		map(XQItemType.XQBASETYPE_IDREF, Type.IDREF);
		map(XQItemType.XQBASETYPE_IDREFS, Type.IDREFS);
		map(XQItemType.XQBASETYPE_INT, Type.INT);
		map(XQItemType.XQBASETYPE_INTEGER, Type.INTEGER);
		map(XQItemType.XQBASETYPE_LANGUAGE, Type.LANGUAGE);
		map(XQItemType.XQBASETYPE_LONG, Type.LONG);
		map(XQItemType.XQBASETYPE_NAME, Type.NAME);
		map(XQItemType.XQBASETYPE_NCNAME, Type.NCNAME);
		map(XQItemType.XQBASETYPE_NEGATIVE_INTEGER, Type.NEGATIVE_INTEGER);
		map(XQItemType.XQBASETYPE_NMTOKENS, Type.NMTOKENS);
		map(XQItemType.XQBASETYPE_NONNEGATIVE_INTEGER, Type.NON_NEGATIVE_INTEGER);
		map(XQItemType.XQBASETYPE_NONPOSITIVE_INTEGER, Type.NON_POSITIVE_INTEGER);
		map(XQItemType.XQBASETYPE_NORMALIZED_STRING, Type.NORMALIZED_STRING);
		map(XQItemType.XQBASETYPE_NOTATION, Type.NOTATION);
		map(XQItemType.XQBASETYPE_POSITIVE_INTEGER, Type.POSITIVE_INTEGER);
		map(XQItemType.XQBASETYPE_QNAME, Type.QNAME);
		map(XQItemType.XQBASETYPE_SHORT, Type.SHORT);
		map(XQItemType.XQBASETYPE_STRING, Type.STRING);
		map(XQItemType.XQBASETYPE_TIME, Type.TIME);
		map(XQItemType.XQBASETYPE_TOKEN, Type.TOKEN);
		map(XQItemType.XQBASETYPE_UNSIGNED_BYTE, Type.UNSIGNED_BYTE);
		map(XQItemType.XQBASETYPE_UNSIGNED_INT, Type.UNSIGNED_INT);
		map(XQItemType.XQBASETYPE_UNSIGNED_LONG, Type.UNSIGNED_LONG);
		map(XQItemType.XQBASETYPE_UNSIGNED_SHORT, Type.UNSIGNED_SHORT);
		map(XQItemType.XQBASETYPE_ANYATOMICTYPE, Type.ANY_ATOMIC_TYPE);
		map(XQItemType.XQBASETYPE_DAYTIMEDURATION, Type.DAY_TIME_DURATION);
		map(XQItemType.XQBASETYPE_UNTYPED, Type.UNTYPED);
		map(XQItemType.XQBASETYPE_UNTYPEDATOMIC, Type.UNTYPED_ATOMIC);
		map(XQItemType.XQBASETYPE_YEARMONTHDURATION, Type.YEAR_MONTH_DURATION);
//		map(XQItemType.XQBASETYPE_XQJ_COMPLEX, Type.);
//		map(XQItemType.XQBASETYPE_XQJ_LISTTYPE, Type.);
		mapIK(XQItemType.XQITEMKIND_ATOMIC, Type.ANY_ATOMIC_TYPE);
////		map(XQItemType.XQITEMKIND_ATTRIBUTE, Type.);
		mapIK(XQItemType.XQITEMKIND_COMMENT, Type.COMMENT);
		mapIK(XQItemType.XQITEMKIND_DOCUMENT, Type.START_DOCUMENT);
////		map(XQItemType.XQITEMKIND_DOCUMENT_ELEMENT, Type.);
		mapIK(XQItemType.XQITEMKIND_ELEMENT, Type.START_TAG);
		mapIK(XQItemType.XQITEMKIND_ITEM, Type.ITEM);
		mapIK(XQItemType.XQITEMKIND_NODE, Type.NODE);
		mapIK(XQItemType.XQITEMKIND_PI, Type.PROCESSING_INSTRUCTION);
////		map(XQItemType.XQITEMKIND_TEXT, Type.);
	}

    void checkNotClosed() throws XQException {
        if (closed) {
            throw new XQException("Connection has been closed");
        }
    }
	
	public XQItem createItem(XQItem item) throws XQException {
		checkNotClosed();
		if (item == null)
			throw new XQException("Cannot create an item from null");
		if (item instanceof MXQueryXQItem) {
			MXQueryXQItem itm = (MXQueryXQItem) item;
			return new MXQueryXQItem(itm.it,itm.conn,itm.curIt);
			
		}
		return null;
	}

	public XQItem createItemFromAtomicValue(String value, XQItemType type)
			throws XQException {
		if (closed) {
            throw new XQException("Connection has been closed");
        }
		if(value == null || type == null){
			throw new XQException("Value or item type for creating item from atomic value can not be null");
		}
		if(type.getBaseType() == XQItemType.XQBASETYPE_BOOLEAN){
			if(value.equals("1") || value.equals("true")){
				return createItemFromBoolean(true, type);
			} if(value.equals("0") || value.equals("false")){
				return createItemFromBoolean(false, type);
			} else {
				throw new XQException("The conversion of the value to boolean item type failed");
			}
		} else if (type.getBaseType() == XQItemType.XQBASETYPE_BYTE){
			try{
				byte val = (byte) Integer.parseInt(value);
				return createItemFromByte(val, type);
			} catch (Exception e){
				throw new XQException("The conversion of the value to byte item type failed");
			}
		} else if (type.getBaseType() == XQItemType.XQBASETYPE_DOUBLE){
			try{
				double val = (double) Integer.parseInt(value);
				return createItemFromDouble(val, type);
			} catch (Exception e){
				throw new XQException("The conversion of the value to double item type failed");
			}
		}else if (type.getBaseType() == XQItemType.XQBASETYPE_FLOAT){
			try{
				float val = (float) Integer.parseInt(value);
				return createItemFromFloat(val, type);
			} catch (Exception e){
				throw new XQException("The conversion of the value to float item type failed");
			}
		}else if (type.getBaseType() == XQItemType.XQBASETYPE_INT){
			try{
				int val = Integer.parseInt(value);
				return createItemFromInt(val, type);
			} catch (Exception e){
				throw new XQException("The conversion of the value to int item type failed");
			}
		}else if (type.getBaseType() == XQItemType.XQBASETYPE_LONG){
			try{
				long val = Long.parseLong(value);
				return createItemFromLong(val, type);
			} catch (Exception e){
				throw new XQException("The conversion of the value to long item type failed");
			}
		}else if (type.getBaseType() == XQItemType.XQBASETYPE_SHORT){
			try{
				short val = (short) Integer.parseInt(value);
				return createItemFromShort(val, type);
			} catch (Exception e){
				throw new XQException("The conversion of the value to short item type failed");
			}
		} else if (type.getBaseType() == XQItemType.XQBASETYPE_STRING){
			try{
				return createItemFromString(value, type);
			} catch (Exception e){
				throw new XQException("The conversion of the value to string item type failed");
			}
		} else {
			throw new XQException("Type is not atomic type");
		}
	}

	public XQItem createItemFromBoolean(boolean value, XQItemType type)
			throws XQException {
		checkNotClosed();
		if(type == null || type.getBaseType() == XQItemType.XQBASETYPE_BOOLEAN){
			BooleanToken res;
			if (value)
				res = BooleanToken.TRUE_TOKEN;
			else
				res = BooleanToken.FALSE_TOKEN;
			return new MXQueryXQItem(null, null, new FlatItem(res));
		} else {
			throw new XQException("Wrong type");
		}
	}

	public XQItem createItemFromByte(byte value, XQItemType type) throws XQException {
		checkNotClosed();
		if(type == null || Type.isTypeOrSubTypeOf(Type.INT, getMXQueryType(type), null)){
			LongToken ln;
			try {
				ln = new LongToken(Type.INTEGER,null,value);
			} catch (MXQueryException e) {
				// TODO Auto-generated catch block
				throw new XQException("Invalid value");
			}
			return new MXQueryXQItem(null, null, new FlatItem(ln));
		} else {
			throw new XQException("Wrong type");
		}
	}

	public XQItem createItemFromDocument(InputSource value) throws XQException,
			IOException {
		// TODO Auto-generated method stub
		return null;
	}

	public XQItem createItemFromDouble(double value, XQItemType type)
			throws XQException {
		checkNotClosed();
		if(type == null || Type.isTypeOrSubTypeOf(Type.DOUBLE, getMXQueryType(type), null)){
			DoubleToken ln;
			ln = new DoubleToken(null,new MXQueryDouble(value));
			return new MXQueryXQItem(null, null, new FlatItem(ln));
		} else {
			throw new XQException("Wrong type");
		}
	}

	public XQItem createItemFromFloat(float value, XQItemType type)
			throws XQException {
		checkNotClosed();
		if(type == null || Type.isTypeOrSubTypeOf(Type.FLOAT, getMXQueryType(type), null)){
			FloatToken ln;
			ln = new FloatToken(null,new MXQueryFloat(value));
			return new MXQueryXQItem(null, null, new FlatItem(ln));
		} else {
			throw new XQException("Wrong type");
		}
	}

	public XQItem createItemFromInt(int value, XQItemType type)
			throws XQException {
		checkNotClosed();
		if(type == null || Type.isTypeOrSubTypeOf(Type.INT, getMXQueryType(type), null)){
			LongToken ln;
			try {
				ln = new LongToken(Type.INTEGER,null,value);
			} catch (MXQueryException e) {
				throw new XQException("Invalid value");
			}
			return new MXQueryXQItem(null, null, new FlatItem(ln));
		} else {
			throw new XQException("Wrong type");
		}
	}

	public XQItem createItemFromLong(long value, XQItemType type)
			throws XQException {
		checkNotClosed();
		if(type == null || Type.isTypeOrSubTypeOf(Type.INT, getMXQueryType(type), null)){
			LongToken ln;
			try {
				ln = new LongToken(Type.INTEGER,null,value);
			} catch (MXQueryException e) {
				throw new XQException("Invalid value");
			}
			return new MXQueryXQItem(null, null, new FlatItem(ln));
		} else {
			throw new XQException("Wrong type");
		}
	}

	public XQItem createItemFromNode(Node value, XQItemType type)
			throws XQException {
		checkNotClosed();
		if (value == null)
			throw new XQException("null value not a valid input");
		if(type == null || type.getBaseType() == XQItemType.XQITEMKIND_DOCUMENT_ELEMENT || type.getBaseType() == XQItemType.XQITEMKIND_DOCUMENT_SCHEMA_ELEMENT){
			try {
				XMLSource xdm = XDMInputFactory.createDOMInput(new Context(), value, QueryLocation.OUTSIDE_QUERY_LOC);
				Vector v = new Vector();
				Token tok;
				while((tok = xdm.next()) != Token.END_SEQUENCE_TOKEN){
					v.add(tok);
				}
				v.add(tok);
				return new MXQueryXQItem(null, null, new TreeItem(v));
			} catch (MXQueryException e) {
				throw new XQException(e.toString());
			}
			
		} else {
			throw new XQException("Wrong type");
		}
	}

	public XQItem createItemFromObject(Object value, XQItemType type)
			throws XQException {
		checkNotClosed();
			if(value == null){
				throw new XQException("Invalid value");
			}
			//TODO Implement
		return null;
	}

	public XQItem createItemFromShort(short value, XQItemType type)
			throws XQException {
		checkNotClosed();
		if(type == null || Type.isTypeOrSubTypeOf(Type.INT, getMXQueryType(type), null)){
			LongToken ln;
			try {
				ln = new LongToken(Type.INTEGER,null,value);
			} catch (MXQueryException e) {
				throw new XQException("Invalid value: "+value);
			}
			return new MXQueryXQItem(null, null, new FlatItem(ln));
		} else {
			throw new XQException("Wrong type");
		}
	}

	static int getMXQueryType(XQItemType type) throws XQException {
		return (((Integer)XQJtoMXQueryBT.get(new Integer(type.getBaseType())))).intValue();
	}

	public XQItemType createItemType(int itemkind, int basetype, QName nodename)
			throws XQException {
		// TODO Auto-generated method stub
		return null;
	}

	public XQItemType createItemType(int itemkind, int basetype,
			QName nodename, QName typename, URI schemaURI, boolean nillable)
			throws XQException {
		// TODO Auto-generated method stub
		return null;
	}

	public XQSequence createSequence(Iterator i) throws XQException {
		// TODO Not completely applicable due to lack of complete documentation
		checkNotClosed();
		if(i == null)
			throw new XQException("Sequence can not be created as the XQSequence parameter is null!!");
		Vector store = new Vector();
		
		XQItem item;
		Object o;
		while (i.hasNext()) {
			o = i.next();
			if(o instanceof XQItem)
				item = (XQItem) o;
			else if(o instanceof String)
				item = createItemFromString((String) o, null);
			else 
				item = null;
			if(item != null)
				store.add(item);
	    }	
		return new MXQueryXQSequence(store, new MXQueryXQConnection(null));
		
	}

	public XQSequence createSequence(XQSequence s) throws XQException {
		checkNotClosed();
		if(s == null)
			throw new XQException("Sequence can not be created as the XQSequence parameter is null!!");
		
		Vector store = new Vector();

		while (s.next()) {
	           store.add( s.getItem());
	    }	
		if(s instanceof MXQueryXQForwardSequence){
			return new MXQueryXQSequence(store, (MXQueryXQConnection) ((MXQueryXQForwardSequence)s).getConnection());
		}
		throw new XQException("Sequence can not be created!!");
		
	}

	public XQSequenceType createSequenceType(XQItemType item, int occurrence)
			throws XQException {
		checkNotClosed();
		if(item == null && occurrence != XQSequenceType.OCC_EMPTY)
				throw new XQException("The item is null  and the occurance is not XQSequenceType.OCC_EMPTY!");
		if(occurrence == XQSequenceType.OCC_EMPTY && item != null)
			throw new XQException("The item is not null  and the occurance is XQSequenceType.OCC_EMPTY!");
		if(occurrence != XQSequenceType.OCC_ZERO_OR_ONE && occurrence != XQSequenceType.OCC_EXACTLY_ONE && occurrence != XQSequenceType.OCC_ZERO_OR_MORE && occurrence != XQSequenceType.OCC_ONE_OR_MORE && occurrence != XQSequenceType.OCC_EMPTY)
			throw new XQException("The occurence is not one of: XQSequenceType.OCC_ZERO_OR_ONE, XQSequenceType.OCC_EXACTLY_ONE, XQSequenceType.OCC_ZERO_OR_MORE, XQSequenceType.OCC_ONE_OR_MORE, XQSequenceType.OCC_EMPTY!");
		return new MXQueryXQType(item, occurrence);
	}

	public XQItemType createAtomicType(int baseType) throws XQException {
		checkNotClosed();
		int mxqType;
		if(MXQueryXQDataFactory.XQJtoMXQueryBT.get(new Integer(baseType)) == null){
			throw new XQException("Unknown base type " + baseType);
		} else {
			mxqType = ((Integer)MXQueryXQDataFactory.XQJtoMXQueryBT.get(new Integer(baseType))).intValue();
		}
		return new MXQueryXQType(new TypeInfo(mxqType,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null));

	}

	public XQItemType createAtomicType(int basetype, QName typename,
			URI schemaURI) throws XQException {
		return createAtomicType(basetype);
	}

	public XQItemType createAttributeType(QName nodename, int basetype)
			throws XQException {
		return createAttributeType(nodename, basetype,null,null);
	}

	public XQItemType createAttributeType(QName nodename, int basetype,
			QName typename, URI schemaURI) throws XQException {
		checkNotClosed();
		int mxqType;
		if(MXQueryXQDataFactory.XQJtoMXQueryBT.get(new Integer(basetype)) == null){
			throw new XQException("Unknown base type " + basetype);
		} else {
			mxqType = ((Integer)MXQueryXQDataFactory.XQJtoMXQueryBT.get(new Integer(basetype))).intValue();
		}
		
		
		try  {
		return new MXQueryXQType(new TypeInfo(Type.createAttributeType(mxqType),Type.OCCURRENCE_IND_EXACTLY_ONE,nodename.toString(),null));
		} catch (TypeException te) {
			throw new XQException("Invalid type for attribute");
		}
	}

	public XQItemType createCommentType() throws XQException {
		checkNotClosed();
		return new MXQueryXQType(new TypeInfo(Type.COMMENT,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null));
	}

	public XQItemType createDocumentElementType(XQItemType elementType)
			throws XQException {
		checkNotClosed();
		if (elementType == null)
			throw new XQException("Null value is not an allowed type");
		
		TypeInfo mxqTi = new TypeInfo(Type.START_DOCUMENT|Type.START_TAG ,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
		
		return new MXQueryXQType(mxqTi, ((Integer)XQJtoMXQueryBT.get(new Integer(elementType.getBaseType()))).intValue());
	}

	public XQItemType createDocumentSchemaElementType(XQItemType elementType)
			throws XQException {
		checkNotClosed();
		if (elementType == null)
			throw new XQException("Null value is not an allowed type");
		//TODO: Implement
		return null;
	}

	public XQItemType createDocumentType() throws XQException {
		checkNotClosed();
		return new MXQueryXQType(new TypeInfo(Type.START_DOCUMENT,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null));
	}

	public XQItemType createElementType(QName nodename, int basetype)
			throws XQException {
		checkNotClosed();
		int mxqType;
		if(MXQueryXQDataFactory.XQJtoMXQueryBT.get(new Integer(basetype)) == null){
			throw new XQException("Unknown base type " + basetype);
		} else {
			mxqType = ((Integer)MXQueryXQDataFactory.XQJtoMXQueryBT.get(new Integer(basetype))).intValue();
		}
		return new MXQueryXQType(new TypeInfo(Type.START_TAG,Type.OCCURRENCE_IND_EXACTLY_ONE,nodename.toString(),null), mxqType);

	}

	public XQItemType createElementType(QName nodename, int basetype,
			QName typename, URI schemaURI, boolean allowNill)
			throws XQException {
		checkNotClosed();
		int mxqType;
		if(MXQueryXQDataFactory.XQJtoMXQueryBT.get(new Integer(basetype)) == null){
			throw new XQException("Unknown base type " + basetype);
		} else {
			mxqType = ((Integer)MXQueryXQDataFactory.XQJtoMXQueryBT.get(new Integer(basetype))).intValue();
		}
		if(typename != null){
			if (!(Type.isAtomicType(mxqType, null) || Type.isAttribute(mxqType) || Type.isUserDefinedType(mxqType)))
				throw new XQException("Getting the type name not possible"); 
			ch.ethz.mxquery.datamodel.QName qn;
			if(Type.isAttribute(mxqType))
				qn = Type.getTypeQName(Type.getPrimitiveAtomicType(mxqType), Context.getDictionary());
			else
				qn = Type.getTypeQName(mxqType, Context.getDictionary());
			if(!typename.getLocalPart().equals(qn.getLocalPart()))
				throw new XQException("The typename refers to a predefinied type and does not match basetype!");
		} else if(schemaURI != null){
			throw new XQException("SchemaURI is specified but the typename is not specified!");
		}
		if(typename != null)
			return new MXQueryXQType(new TypeInfo(Type.START_TAG,Type.OCCURRENCE_IND_EXACTLY_ONE,nodename.toString(),null), mxqType, typename, allowNill);
		else 
			return new MXQueryXQType(new TypeInfo(Type.START_TAG,Type.OCCURRENCE_IND_EXACTLY_ONE,nodename.toString(),null), mxqType, allowNill);
	}

	public XQItem createItemFromDocument(String value, String baseURI,
			XQItemType type) throws XQException {
		checkNotClosed();
		if (value == null)
			throw new XQException("null value not a valid input");
		if(type != null)
		if(type.getItemKind() != XQItemType.XQITEMKIND_DOCUMENT_ELEMENT || type.getItemKind() != XQItemType.XQITEMKIND_DOCUMENT_SCHEMA_ELEMENT){
			throw new XQException("Invalid type of the value to be bound!");
		}
		XMLSource it = null;
		Vector toks = new Vector();
		try {
			it = XDMInputFactory.createXMLInput(new Context(), new StringReader(value), true, Context.getGlobalContext().getInputValidationMode(), QueryLocation.OUTSIDE_QUERY_LOC);
			Token tok;
			do {
				tok = it.next();
				toks.add(tok);
			} while (tok != Token.END_SEQUENCE_TOKEN);
			
		} catch (MXQueryException e) {
			throw new XQException(e.toString());
		}
		return new MXQueryXQItem(null, null, new TreeItem(toks));
	}

	public XQItem createItemFromDocument(Reader value, String baseURI,
			XQItemType type) throws XQException {
		checkNotClosed();
		if (value == null)
			throw new XQException("null value not a valid input");
		BufferedReader br = new BufferedReader(value);
		String str;
		String inputStr = "";
		try {
			while((str = br.readLine()) != null){
				inputStr += str;
			}
		} catch (IOException e) {
			throw new XQException(e.toString());
		}
		return createItemFromDocument(inputStr, baseURI, type);
	}

	public XQItem createItemFromDocument(InputStream value, String baseURI,
			XQItemType type) throws XQException {
		checkNotClosed();
		if (value == null)
			throw new XQException("null value not a valid input");
		BufferedReader br = new BufferedReader(new InputStreamReader(value));
		String str;
		String inputStr = "";
		try {
			while((str = br.readLine()) != null){
				inputStr += str;
			}
		} catch (IOException e) {
			throw new XQException(e.toString());
		}
		return createItemFromDocument(inputStr, baseURI, type);
	}

	public XQItem createItemFromDocument(XMLStreamReader value, XQItemType type)
			throws XQException {
		checkNotClosed();
		if (value == null)
			throw new XQException("null value not a valid input");
		if(type == null || type.getBaseType() == XQItemType.XQITEMKIND_DOCUMENT_ELEMENT || type.getBaseType() == XQItemType.XQITEMKIND_DOCUMENT_SCHEMA_ELEMENT){
			try {
				XMLSource xdm = XDMInputFactory.createStaxInput(new Context(), value, null);
				Vector v = new Vector();
				Token tok;
				while((tok =xdm.next()) != Token.END_SEQUENCE_TOKEN){
					v.add(tok);
				}
				v.add(tok);
				return new MXQueryXQItem(null, null, new TreeItem(v));
			} catch (MXQueryException e) {
				throw new XQException(e.toString());
			}
			
		} else {
			throw new XQException("Wrong type");
		}		
	}

	public XQItem createItemFromDocument(XMLReader value, XQItemType type)
			throws XQException {
		checkNotClosed();
		if (value == null)
			throw new XQException("null value not a valid input");
		if(type == null || type.getBaseType() == XQItemType.XQITEMKIND_DOCUMENT_ELEMENT || type.getBaseType() == XQItemType.XQITEMKIND_DOCUMENT_SCHEMA_ELEMENT){
			try {
				XMLSource xdm = XDMInputFactory.createSAXInput(new Context(), value, null);
				Vector v = new Vector();
				Token tok;
				while((tok =xdm.next()) != Token.END_SEQUENCE_TOKEN){
					v.add(tok);
				}
				v.add(tok);
				return new MXQueryXQItem(null, null, new TreeItem(v));
			} catch (MXQueryException e) {
				throw new XQException(e.toString());
			}
			
		} else {
			throw new XQException("Wrong type");
		}		
	}

	public XQItem createItemFromDocument(Source value, XQItemType type)
			throws XQException {
		checkNotClosed();
		if (value == null)
			throw new XQException("null value not a valid input");
		if(type == null || type.getBaseType() == XQItemType.XQITEMKIND_DOCUMENT_ELEMENT || type.getBaseType() == XQItemType.XQITEMKIND_DOCUMENT_SCHEMA_ELEMENT){
			
			try {
				XMLSource xdm = null;
				if (value instanceof StreamSource) {
					StreamSource  sm = (StreamSource) value;			
					xdm = XDMInputFactory.createXMLInput(new Context(), sm.getReader(), true, Context.getGlobalContext().getInputValidationMode(), QueryLocation.OUTSIDE_QUERY_LOC);
					
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
				
				Vector v = new Vector();
				Token tok;
				while((tok =xdm.next()) != Token.END_SEQUENCE_TOKEN){
					v.add(tok);
				}
				v.add(tok);
				return new MXQueryXQItem(null, null, new TreeItem(v));
			} catch (MXQueryException e) {
				throw new XQException(e.toString());
			}
			
		} else {
			throw new XQException("Wrong type");
		}	
	}

	public XQItem createItemFromString(String value, XQItemType type)
			throws XQException {
		checkNotClosed();
		if (value == null)
			throw new XQException("Value for creating item from string can not be null");
		int mxqType; 
		if(type != null)
			mxqType = getMXQueryType(type);
		else 
			mxqType = (((Integer)XQJtoMXQueryBT.get(new Integer(XQItemType.XQBASETYPE_STRING)))).intValue();
		if(type == null || Type.isTypeOrSubTypeOf(mxqType, Type.STRING, null)){
			TextToken ln;
			try {
				ln = new TextToken(mxqType, null, value,null);
			} catch (MXQueryException e) {
				throw new XQException("Invalid value");
			}
			return new MXQueryXQItem(null, null, new FlatItem(ln));
		} else {
			throw new XQException("Wrong type");
		}
	}

	public XQItemType createItemType() throws XQException {
		checkNotClosed();
		return new MXQueryXQType(new TypeInfo(Type.ITEM,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null));
	}

	public XQItemType createNodeType() throws XQException {
		checkNotClosed();
		return new MXQueryXQType(new TypeInfo(Type.NODE,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null));
	}

	public XQItemType createProcessingInstructionType(String piTarget)
			throws XQException {
		checkNotClosed();
		return new MXQueryXQType(new TypeInfo(Type.PROCESSING_INSTRUCTION,Type.OCCURRENCE_IND_EXACTLY_ONE,piTarget ,null));
	}

	public XQItemType createSchemaAttributeType(QName nodename, int basetype,
			URI schemaURI) throws XQException {
		// TODO Auto-generated method stub
		return null;
	}

	public XQItemType createSchemaElementType(QName nodename, int basetype,
			URI schemaURI) throws XQException {
		// TODO Auto-generated method stub
		return null;
	}

	public XQItemType createTextType() throws XQException {
		checkNotClosed();
		return new MXQueryXQType(new TypeInfo(Type.createTextNodeType(Type.ITEM),Type.OCCURRENCE_IND_EXACTLY_ONE,null ,null));
	}

}

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

package ch.ethz.mxquery.xdmio;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.MXQueryBigDecimal;
import ch.ethz.mxquery.datamodel.MXQueryBinary;
import ch.ethz.mxquery.datamodel.MXQueryDate;
import ch.ethz.mxquery.datamodel.MXQueryDateTime;
import ch.ethz.mxquery.datamodel.MXQueryDouble;
import ch.ethz.mxquery.datamodel.MXQueryDuration;
import ch.ethz.mxquery.datamodel.MXQueryFloat;
import ch.ethz.mxquery.datamodel.MXQueryGregorian;
import ch.ethz.mxquery.datamodel.MXQueryTime;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.xdm.AnyURIToken;
import ch.ethz.mxquery.datamodel.xdm.BooleanToken;
import ch.ethz.mxquery.datamodel.xdm.DateTimeToken;
import ch.ethz.mxquery.datamodel.xdm.DateToken;
import ch.ethz.mxquery.datamodel.xdm.DecimalToken;
import ch.ethz.mxquery.datamodel.xdm.DoubleToken;
import ch.ethz.mxquery.datamodel.xdm.DurationToken;
import ch.ethz.mxquery.datamodel.xdm.FloatToken;
import ch.ethz.mxquery.datamodel.xdm.GregorianToken;
import ch.ethz.mxquery.datamodel.xdm.LongToken;
import ch.ethz.mxquery.datamodel.xdm.QNameToken;
import ch.ethz.mxquery.datamodel.xdm.TextToken;
import ch.ethz.mxquery.datamodel.xdm.TimeToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.datamodel.xdm.UntypedAtomicToken;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.iterators.TokenIterator;
import ch.ethz.mxquery.model.XDMIterator;

/**
 * Factory to create items of all atomic types in XDM
 * @author Peter Fischer
 *
 */
public class XDMAtomicItemFactory {
	/** 
	 * Creates an AnyURI Item 
	 * @param uri the String to be represented as anyURI item
	 * @return an XDM Iterator representing the constructed AnyURI Item 
	 * @throws MXQueryException
	 */
	public static XDMIterator createAnyURI(final String uri) throws MXQueryException{
		return createTextTypeItem(null, uri, Type.ANY_URI);
	}
	/**
	 * Creates a Base 64 Binary Item
	 * @param base64Val String representation of a Base 64 value 
	 * @return an XDM Iterator representing the constructed Base 64 Binary Item
	 * @throws MXQueryException
	 */
	 public static XDMIterator createBase64Binary(String base64Val) throws MXQueryException {
		 MXQueryBinary bin = new MXQueryBinary(base64Val,Type.BASE64_BINARY);
		 return new TokenIterator(null,bin,QueryLocation.OUTSIDE_QUERY_LOC);
	 }
	 /**
	  * Creates a Base 64 Binary Item
	  * @param binValue binary values to be represented as Base 64
	  * @return an XDM Iterator representing the constructed Base 64 Binary Item
	  * @throws MXQueryException
	  */
	 public static XDMIterator createBase64Binary(byte [] binValue) throws MXQueryException {
		 MXQueryBinary bin = new MXQueryBinary(binValue,Type.BASE64_BINARY);
		 return new TokenIterator(null,bin,QueryLocation.OUTSIDE_QUERY_LOC);
	 }

	 /**
	  * Creates a Boolean Item 
	  * @param val the boolean value for the item
	  * @return an XDM Iterator representing the constructed Boolean Item
	  * @throws MXQueryException
	  */
	 public static XDMIterator createBoolean(boolean val) throws MXQueryException{
		 BooleanToken myToken;
		 if (val)
				myToken = BooleanToken.TRUE_TOKEN;
			else 
				myToken = BooleanToken.FALSE_TOKEN;
		 return new TokenIterator(null,myToken,QueryLocation.OUTSIDE_QUERY_LOC);
	 }
	 /**
	  * Creates a Byte Item
	  * @param bVal the byte value for the item
	  * @return an XDM Iterator representing the constructed Byte Item
	  * @throws MXQueryException
	  */
	 public static XDMIterator createByte(byte bVal) throws MXQueryException {
		 LongToken myToken = new LongToken(Type.BYTE,null,bVal);
		 return new TokenIterator(null,myToken,QueryLocation.OUTSIDE_QUERY_LOC);
	 } 
	/**
	 * Creates a Date Item
	 * @param dVal a String expressing the date for this item (XML Schema/XQuery format)
	 * @return an XDM Iterator representing the constructed Date Item
	 * @throws MXQueryException
	 */
	 public static XDMIterator createDate(String dVal) throws MXQueryException{
		 MXQueryDate date = new MXQueryDate(dVal);
		 return new TokenIterator(null,new DateToken(null,date),QueryLocation.OUTSIDE_QUERY_LOC);
	 }
	 /**
	  * Creates a DateTime Item
	  * @param dTimeVal a String expressing the dateTime for this item (XML Schema/XQuery format)
	  * @return an XDM Iterator representing the constructed DateTim Item
	  * @throws MXQueryException
	  */
	 public static XDMIterator createDateTime(String dTimeVal) throws MXQueryException{
		 MXQueryDateTime dateTime = new MXQueryDateTime(dTimeVal);
		 return new TokenIterator(null,new DateTimeToken(null,dateTime),QueryLocation.OUTSIDE_QUERY_LOC);
	 }
	/**
	 * Creates a Duration Item
	 * @param durVal a String expressing the duration for this item (XML Schema/XQuery format)
	 * @return an XDM Iterator representing the constructed Duration Item
	 * @throws MXQueryException
	 */
	 public static XDMIterator createDuration(String durVal) throws MXQueryException{
		 MXQueryDuration dur = new MXQueryDuration(durVal);
		 return new TokenIterator(null,new DurationToken(null,dur),QueryLocation.OUTSIDE_QUERY_LOC);
	 }
	 /**
	  * Creates a Decimal Item
	  * @param dVal a MXQueryBigDecimal expressing the decimal value for this item
	  * @return an XDM Iterator representing the constructed Decimal Item
	  * @throws MXQueryException
	  */
	 public static XDMIterator createDecimal(MXQueryBigDecimal dVal) throws MXQueryException{
		 return new TokenIterator(null, new DecimalToken(null,dVal),QueryLocation.OUTSIDE_QUERY_LOC);
	 } 
	 /**
	  * Creates a Double Item
	  * @param dVal a MXQueryDouble expressing the double value for this item
	  * @return an XDM Iterator representing the constructed Double Item
	  * @throws MXQueryException
	  */
	 public static XDMIterator createDouble(MXQueryDouble dVal) throws MXQueryException{
		 return new TokenIterator(null,new DoubleToken(null,dVal),QueryLocation.OUTSIDE_QUERY_LOC);
	 }	 
	 /**
	  * Creates a Float Item
	  * @param fVal a MXQueryFloat expressing the float value for this item
	  * @return an XDM Iterator representing the constructed Float Item
	  * @throws MXQueryException
	  */
	 public static XDMIterator createFloat(MXQueryFloat fVal) throws MXQueryException{
		 return new TokenIterator(null,new FloatToken(null,fVal),QueryLocation.OUTSIDE_QUERY_LOC);
	 }	
	 /**
	  * Creates a Gregorian Day Item
	  * @param gDayVal the day number (1-31) expressing the Gregorian Day for this item 
	  * @return an XDM Iterator representing the constructed Gregorian Day Item
	  * @throws MXQueryException
	  */
	 public static XDMIterator createGDay(int gDayVal) throws MXQueryException{
		 MXQueryGregorian gDay = new MXQueryGregorian(0,0,gDayVal,0,Type.G_DAY);
		 return new TokenIterator(null,new GregorianToken(null,gDay),QueryLocation.OUTSIDE_QUERY_LOC);
	 }	
	 /**
	  * Creates a Gregorian Month Item
	  * @param gMonthVal the month number (1-12) expressing the Gregorian Month for this item
	  * @return an XDM Iterator representing the constructed Gregorian Month Item
	  * @throws MXQueryException
	  */
	 public static XDMIterator createGMonth(int gMonthVal) throws MXQueryException{
		 MXQueryGregorian gMonth = new MXQueryGregorian(0,gMonthVal,0,0,Type.G_MONTH);
		 return new TokenIterator(null,new GregorianToken(null,gMonth),QueryLocation.OUTSIDE_QUERY_LOC);
	 }	
	 /**
	  * Creates a Gregorian MonthDay Item
	  * @param gMonthVal gMonthVal the month number (1-12) expressing the Gregorian Month for this item
	  * @param gDayVal the day number (1-31) expressing the Gregorian Day for this item
	  * @return an XDM Iterator representing the constructed Gregorian MonthDay Item
	  * @throws MXQueryException
	  */
	 public static XDMIterator createGMonthDay(int gMonthVal, int gDayVal) throws MXQueryException {
		 MXQueryGregorian gMonthDay = new MXQueryGregorian(0,gMonthVal,gDayVal,0,Type.G_MONTH_DAY);
		 return new TokenIterator(null,new GregorianToken(null,gMonthDay),QueryLocation.OUTSIDE_QUERY_LOC);
	 }	
	 /**
	  * Creates a Gregorian Year Item
	  * @param gYearVal the year number expressing the Gregorian Year for this item
	  * @return an XDM Iterator representing the constructed Gregorian Year Item
	  * @throws MXQueryException
	  */
	 public static XDMIterator createGYear(int gYearVal) throws MXQueryException {
		 MXQueryGregorian gYear = new MXQueryGregorian(gYearVal,0,0,0,Type.G_YEAR);
		 return new TokenIterator(null,new GregorianToken(null,gYear),QueryLocation.OUTSIDE_QUERY_LOC);
	 }	
	 /**
	  * Creates a Gregorian YearMonth Item
	  * @param gYearVal gYearVal the year number expressing the Gregorian Year for this item
	  * @param gMonthVal gMonthVal the month number (1-12) expressing the Gregorian Month for this item
	  * @return an XDM Iterator representing the constructed Gregorian YearMonth Item
	  * @throws MXQueryException
	  */
	 public static XDMIterator createGYearMonth(int gYearVal,int gMonthVal) throws MXQueryException {
		 MXQueryGregorian gYearMonth = new MXQueryGregorian(gYearVal,gMonthVal,0,0,Type.G_YEAR_MONTH);
		 return new TokenIterator(null,new GregorianToken(null,gYearMonth),QueryLocation.OUTSIDE_QUERY_LOC);
	 }	
	 /**
	  * Creates a Hex Binary Item from a string representation 
	  * @param hexVal a string expressing the hex binary value for this item
	  * @return an XDM Iterator representing the constructed HexBinary Item
	  * @throws MXQueryException
	  */
	 public static XDMIterator createHexBinary(String hexVal) throws MXQueryException {
		 MXQueryBinary bin = new MXQueryBinary(hexVal,Type.HEX_BINARY);
		 return new TokenIterator(null,bin,QueryLocation.OUTSIDE_QUERY_LOC);
	 }
	 /**
	  * Creates a Hex Binary Item from a binary representation 
	  * @param binValue a byte array expressing the hex binary value for this item
	  * @return an XDM Iterator representing the constructed HexBinary Item
	  * @throws MXQueryException
	  */
	 public static XDMIterator createHexBinary(byte [] binValue) throws MXQueryException {
		 MXQueryBinary bin = new MXQueryBinary(binValue,Type.HEX_BINARY);
		 return new TokenIterator(null,bin,QueryLocation.OUTSIDE_QUERY_LOC);
	 }
	 /**
	  * Creates an Int Item 
	  * @param intVal an integer expressing the int value for this item 
	  * @return an XDM Iterator representing the constructed Int Item
	  * @throws MXQueryException
	  */
	 public static XDMIterator createInt(int intVal) throws MXQueryException {
		 LongToken myToken = new LongToken(Type.INT,null,intVal);
		 return new TokenIterator(null,myToken,QueryLocation.OUTSIDE_QUERY_LOC);
	 }	
	 /**
	  * Creates an Integer Item
	  * @param intVal an integer expressing the integer value for this item
	  * @return an XDM Iterator representing the constructed Integer Item
	  * @throws MXQueryException
	  */
	 public static XDMIterator createInteger(long intVal) throws MXQueryException {
		 LongToken myToken = new LongToken(Type.INTEGER,null,intVal);
		 return new TokenIterator(null,myToken,QueryLocation.OUTSIDE_QUERY_LOC);
	 }	
	 /**
	  * Creates a Long Item
	  * @param longVal a long expressing the long value for this item
	  * @return an XDM Iterator representing the constructed Long Item
	  * @throws MXQueryException
	  */
	 public static XDMIterator createLong(long longVal) throws MXQueryException{
		 LongToken myToken = new LongToken(Type.LONG,null,longVal);
		 return new TokenIterator(null,myToken,QueryLocation.OUTSIDE_QUERY_LOC);
	 }	
	 /**
	  * Creates a NCName Item
	  * @param ncname a String expressing the NCNAME value for this item
	  * @return an XDM Iterator representing the constructed NCNAME Item
	  * @throws MXQueryException
	  */
	 public static XDMIterator createNCName(final String ncname) throws MXQueryException{
			return createTextTypeItem(null, ncname, Type.NCNAME);
	 }	
	 /**
	  * Creates a Negative Integer Item
	  * @param longVal a long expressing the negative integer value for this item
	  * @return an XDM Iterator representing the constructed Negative Integer Item
	  * @throws MXQueryException
	  */
	 public static XDMIterator createNegativeInteger(long longVal) throws MXQueryException{
		 LongToken myToken = new LongToken(Type.NEGATIVE_INTEGER,null,longVal);
		 return new TokenIterator(null,myToken,QueryLocation.OUTSIDE_QUERY_LOC);
	 }	
	 /**
	  * Creates a Non-Negative Integer Item
	  * @param longVal a long expressing the non-negative integer value for this item
	  * @return an XDM Iterator representing the constructed Non-Negative Integer Item
	  * @throws MXQueryException
	  */
	 public static XDMIterator createNonNegativeInteger(long longVal) throws MXQueryException{
		 LongToken myToken = new LongToken(Type.NON_NEGATIVE_INTEGER,null,longVal);
		 return new TokenIterator(null,myToken,QueryLocation.OUTSIDE_QUERY_LOC);
	 }	
	 /**
	  * Creates a Non-Positive Integer Item
	  * @param longVal a long expressing the non-positive integer value for this item
	  * @return an XDM Iterator representing the constructed Non-Positive Integer Item
	  * @throws MXQueryException
	  */
	 public static XDMIterator createNonPositiveInteger(long longVal) throws MXQueryException{
		 LongToken myToken = new LongToken(Type.NON_POSITIVE_INTEGER,null,longVal);
		 return new TokenIterator(null,myToken,QueryLocation.OUTSIDE_QUERY_LOC);
	 }	
	 /**
	  * Creates a Positive Integer Item
	  * @param longVal a long expressing the positive integer value for this item
	  * @return an XDM Iterator representing the constructed Positive Integer Item
	  * @throws MXQueryException
	  */
	 public static XDMIterator createPositiveInteger(long longVal) throws MXQueryException{
		 LongToken myToken = new LongToken(Type.POSITIVE_INTEGER,null,longVal);
		 return new TokenIterator(null,myToken,QueryLocation.OUTSIDE_QUERY_LOC);
	 }	
	 /**
	  * Creates a QName Item
	  * @param prefix a string representing the namespace prefix
	  * @param localName a string representing the local name
	  * @return an XDM Iterator representing the constructed QName Item
	  * @throws MXQueryException
	  */
	 public static XDMIterator createQName(String prefix, String localName) throws MXQueryException{
		 QName qn = new QName(prefix,localName);
		 return new TokenIterator(null,new QNameToken(null,qn),QueryLocation.OUTSIDE_QUERY_LOC);
	 }
	 /**
	  * Creates a qualified QName Item
	  * @param namespace a string representing the namespace URI
	  * @param prefix a string representing the namespace prefix
	  * @param localName a string representing the local name
	  * @return an XDM Iterator representing the constructed QName Item
	  * @throws MXQueryException
	  */
	 public static XDMIterator createQName(String namespace, String prefix, String localName) throws MXQueryException{
		 QName qn = new QName(namespace, prefix,localName);
		 return new TokenIterator(null,new QNameToken(null,qn),QueryLocation.OUTSIDE_QUERY_LOC);
	 }	
	 /**
	  * Creates a Short Item
	  * @param shortVal a short expressing the short value for this item
	  * @return an XDM Iterator representing the constructed Short Item
	  * @throws MXQueryException
	  */
	 public static XDMIterator createShort(short shortVal) throws MXQueryException{
		 LongToken myToken = new LongToken(Type.SHORT,null,shortVal);
		 return new TokenIterator(null,myToken,QueryLocation.OUTSIDE_QUERY_LOC);
	 }	
	 /**
	  * Creates a String Item
	  * @param str a String expressing the string value for this item
	  * @return an XDM Iterator representing the constructed String Item
	  * @throws MXQueryException
	  */
	 public static XDMIterator createString(final String str) throws MXQueryException{
			return createTextTypeItem(null, str, Type.STRING);
	 }	
	 /**
	  * Creates a Time Item
	  * @param timeVal a string expressing the time value for this item (XML Schema/XQuery format)
	  * @return an XDM Iterator representing the constructed Time Item
	  * @throws MXQueryException
	  */
	 public static XDMIterator createTime(String timeVal) throws MXQueryException{
		 MXQueryTime time = new MXQueryTime(timeVal);
		 return new TokenIterator(null, new TimeToken(null,time),QueryLocation.OUTSIDE_QUERY_LOC);
	 }	
	 /**
	  * Creates an Unsigned Byte Item
	  * @param ubVal a short expressing the unsigned byte value for this item
	  * @return an XDM Iterator representing the constructed Unsigned Byte Item
	  * @throws MXQueryException
	  */
	 public static XDMIterator createUnsignedByte(short ubVal) throws MXQueryException{
		 LongToken myToken = new LongToken(Type.UNSIGNED_BYTE,null,ubVal);
		 return new TokenIterator(null,myToken,QueryLocation.OUTSIDE_QUERY_LOC);
	 }	
	 /**
	  * Creates an Unsigned Int Item
	  * @param usVal a long expressing the unsigned int value for this item
	  * @return an XDM Iterator representing the constructed Unsigned Int Item
	  * @throws MXQueryException
	  */
	 public static XDMIterator createUnsignedInt(long usVal) throws MXQueryException{
		 LongToken myToken = new LongToken(Type.UNSIGNED_INT,null,usVal);
		 return new TokenIterator(null,myToken,QueryLocation.OUTSIDE_QUERY_LOC);
	 }	
	 /**
	  * Creates an Unsigned Long Item. Note: Unsigned Long is limited to the signed long space in MXQuery
	  * @param ulVal a long expressing the unsigned long value for this item
	  * @return an XDM Iterator representing the constructed Unsigned Long Item
	  * @throws MXQueryException
	  */
	 public static XDMIterator createUnsignedLong(long ulVal) throws MXQueryException {
		 LongToken myToken = new LongToken(Type.UNSIGNED_LONG,null,ulVal);
		 return new TokenIterator(null,myToken,QueryLocation.OUTSIDE_QUERY_LOC);
	 }	
	 /**
	  * Creates an Unsigned Short Item
	  * @param uiVal an integer expressing the unsigned short value for this item
	  * @return an XDM Iterator representing the constructed Unsigned Short Item
	  * @throws MXQueryException
	  */
	 public static XDMIterator createUnsignedShort(int uiVal) throws MXQueryException{
		 LongToken myToken = new LongToken(Type.UNSIGNED_SHORT,null,uiVal);
		 return new TokenIterator(null,myToken,QueryLocation.OUTSIDE_QUERY_LOC);
	 }	

	 
	private static XDMIterator createTextTypeItem (Context ctx, String value, int type) throws MXQueryException {
		
		Token myToken;
		
		int checkType = Type.getEventTypeSubstituted(type, Context.getDictionary());
		
		switch (checkType) {
		case Type.STRING:
		case Type.UNTYPED:
			myToken = new TextToken(type, null, value,null);
		break;

		case Type.UNTYPED_ATOMIC:
			myToken = new UntypedAtomicToken(null, value);
		break;
		
		case Type.ANY_URI:
			myToken = new AnyURIToken(null, value);
		break;
		default:
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,"Incorrect type passed: " + Type.getTypeQName(type, Context.getDictionary()),QueryLocation.OUTSIDE_QUERY_LOC );
		}
		return new TokenIterator(ctx,myToken,QueryLocation.OUTSIDE_QUERY_LOC);
	}
		
}

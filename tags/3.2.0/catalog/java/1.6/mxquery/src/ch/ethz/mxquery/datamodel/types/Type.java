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

/**
 * author Rokas Tamosevicius
 */

package ch.ethz.mxquery.datamodel.types;

import java.util.Hashtable;

import org.apache.xerces.xs.XSConstants;
import org.apache.xerces.xs.XSSimpleTypeDefinition;
import org.apache.xerces.xs.XSTypeDefinition;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.contextConfig.XQStaticContext;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.exceptions.StaticException;
import ch.ethz.mxquery.exceptions.TypeException;

public class Type {

	/***************************************************************************
	 * constants *
	 **************************************************************************/

	public final static String NAMESPACE_XS = "xs";

	public final static String NAMESPACE_MXQ = "mxq";

	public final static int ITEM = 0;

	public final static int NODE = 2; // 0000000000000000000000000000010

	/** Token types */
	// document node kind
	public static final int START_DOCUMENT = 67108906; // 0000100000000000000000000101110

	public static final int END_DOCUMENT = 46; // 0000000000000000000000000101110


	// element node kind
	public static final int START_TAG = 201326592; //0001100000000000000000000000000
	
	public static final int END_TAG = 34; // 0000000000000000000000000100010

	public final static int NAMESPACE = 50; // not used

	public static final int PROCESSING_INSTRUCTION = 54;

	public static final int COMMENT = 58;

	public static final int ENTITY_REF = 14;

	// --

	public static final int START_SEQUENCE = 67108928; // 0000100000000000000000001000000

	public static final int END_SEQUENCE = 64;

	public final static int ANY_TYPE = 3;

	public static final int UNTYPED = 11;

	public final static int ANY_SIMPLE_TYPE = 7;

	public final static int ANY_ATOMIC_TYPE = 15;

	public final static int NUMBER = 527;

	public final static int DECIMAL = 4623;

	public final static int INTEGER = 12815;

	public final static int NON_POSITIVE_INTEGER = 78351;

	public final static int NEGATIVE_INTEGER = 209423;

	public final static int LONG = 111119;

	public final static int INT = 242191;

	public final static int SHORT = 504335;

	public final static int BYTE = 1028623;

	public final static int NON_NEGATIVE_INTEGER = 94735;

	public final static int POSITIVE_INTEGER = 356879;

	public final static int UNSIGNED_LONG = 487951;

	public final static int UNSIGNED_INT = 1012239;

	public final static int UNSIGNED_SHORT = 2060815;

	public final static int UNSIGNED_BYTE = 4157967;

	public final static int DOUBLE = 5647;

	public final static int FLOAT = 6671;

	public final static int DURATION = 783;

	public final static int YEAR_MONTH_DURATION = 2831;

	public final static int DAY_TIME_DURATION = 3855;

	public static final int STRING = 543;

	public final static int DATE = 559;

	public final static int TIME = 591;

	public final static int DATE_TIME = 655;

	public final static int BOOLEAN = 575;

	public final static int QNAME = 607;

	public final static int UNTYPED_ATOMIC = 623;

	public final static int ANY_URI = 687;

	public final static int BASE64_BINARY = 719;

	public final static int HEX_BINARY = 847;

	public final static int NOTATION = 911;

	public final static int G_DAY = 671;

	public final static int G_MONTH = 799;

	public final static int G_YEAR = 815;

	public final static int G_YEAR_MONTH = 751;

	public final static int G_MONTH_DAY = 639;

	// subtypes of xs:string
	public final static int NORMALIZED_STRING = 1567;

	public final static int TOKEN = 3615;

	public final static int LANGUAGE = 19999;

	public final static int NMTOKEN = 28191;

	public final static int NMTOKENS = 93727;

	public final static int NAME = 24095;

	public final static int NCNAME = 89631;

	public final static int ID = 613919;

	public final static int IDREF = 744991;

	public final static int IDREFS = 1793567;

	public final static int ENTITY = 876063;

	public final static int ENTITIES = 1924639;

	/** node kind test types */
	//public static final int TYPE_NK_ITEM_TEST = 0;

	public static final int TYPE_NK_ANY_NODE_TEST = 2;

	public static final int TYPE_NK_ATTR_TEST = 33554432; //Integer.parseInt("0000010000000000000000000000000", 2); // Same as attribute mask

	public static final int TYPE_NK_SCHEMA_ATTR_TEST = 39;

	public static final int TYPE_NK_SCHEMA_ELEM_TEST = 35;

	public static final int TYPE_NK_DOC_TEST = 46; 

	public static final int TYPE_NK_TEXT_TEST = 42;

	public static final int TYPE_NK_EMPTY_SEQ_TEST = 64;

	/** occurrence indicators consts */
	public final static int OCCURRENCE_IND_EXACTLY_ONE = 1;

	public final static int OCCURRENCE_IND_ZERO_OR_ONE = 2;

	public final static int OCCURRENCE_IND_ZERO_OR_MORE = 3;

	public final static int OCCURRENCE_IND_ONE_OR_MORE = 4;

	public final static int OCCURRENCE_IND_INFINITIVE = 5; // extension of

    /** MASKS */

	public final static int MASK_GET_START_TAG = Integer.parseInt("0001110000000000000000000000000", 2);
	public final static int MASK_CLEAN_START_TAG = Integer.parseInt("1110001111111111111111111111111", 2);

	private final static int MASK_CHECK_START_BIT = Integer.parseInt("0000100000000000000000000000000", 2);

	private final static int MASK_CHECK_ATTRIBUTE_NODE = Integer.parseInt("0000010000000000000000000000000", 2);
	private final static int MASK_STRIP_ATTRIBUTE_NODE = Integer.parseInt("1111101111111111111111111111111", 2);

	private final static int MASK_CHECK_TEXT_NODE = Integer.parseInt("0000001000000000000000000000000", 2);
    private final static int MASK_STRIP_TEXT_NODE = Integer.parseInt("1111110111111111111111111111111", 2);
    private final static int MASK_CHECK_ANN_NODE = Integer.parseInt("0001111000000000000000000000000", 2); //element || attribute || text ||
    private final static int MASK_CHECK_SIMPLE_NODE = Integer.parseInt("0000000000000000000000000000010", 2); //element || attribute || text ||


	// TODO: not used yet - needs to be figured out
	//private final static int MASK_CHECK_ELEMENT_NODE = Integer.parseInt("0000000100000000000000000000000", 2);

	// clean reserved bits (30 - 26)
	private final static int MASK_CLEAN_ADDITIONAL_INFO = Integer.parseInt("1000000111111111111111111111111", 2);

	private final static int MASK_USER_DEFINED_TYPE = Integer.parseInt("1000000000000000000000000000000", 2);

	public final static int MASK_USER_DEFINED_TYPE_INDEX = Integer.parseInt("0000000001111111111111111000000", 2);

	private final static int MASK_CHECK_NILLABLE = Integer.parseInt("0000000010000000000000000000000", 2);
	private final static int MASK_STRIP_NILLABLE = Integer.parseInt("1111111101111111111111111111111", 2);

	private final static String ALL_0_BITS = "0000000000000000000000000000000";

	// private final static int ALL_1_BITS = Integer.MAX_VALUE;

	/** User defined type bit (1 - user defined type, 0 - built-in type) */
	private final static int BIT_POSITION_UDT = 31;

	// Convenience combination for common cases
	public final static int TEXT_NODE_UNTYPED_ATOMIC = createTextNodeType(UNTYPED_ATOMIC);
	

	// TODO on request: public static final int getCommonSuperType(int type1,
	// int type2)

	private static Hashtable typeQNameMappingHT = null;

	private static void initTypeQNameMappingStore() throws MXQueryException {
		typeQNameMappingHT = new Hashtable();
		typeQNameMappingHT.put(getTypeQName(Type.ANY_TYPE, Context.getDictionary()).toString(), new Integer(Type.ANY_TYPE));
		typeQNameMappingHT.put(getTypeQName(Type.ANY_SIMPLE_TYPE, Context.getDictionary()).toString(), new Integer(Type.ANY_SIMPLE_TYPE));
		typeQNameMappingHT.put(getTypeQName(Type.ANY_ATOMIC_TYPE, Context.getDictionary()).toString(), new Integer(Type.ANY_ATOMIC_TYPE));
		typeQNameMappingHT.put(getTypeQName(Type.UNTYPED_ATOMIC, Context.getDictionary()).toString(), new Integer(Type.UNTYPED_ATOMIC));
		typeQNameMappingHT.put(getTypeQName(Type.NUMBER, Context.getDictionary()).toString(), new Integer(Type.NUMBER));
		typeQNameMappingHT.put(getTypeQName(Type.INTEGER, Context.getDictionary()).toString(), new Integer(Type.INTEGER));
		typeQNameMappingHT.put(getTypeQName(Type.LONG, Context.getDictionary()).toString(), new Integer(Type.LONG));
		typeQNameMappingHT.put(getTypeQName(Type.INT, Context.getDictionary()).toString(), new Integer(Type.INT));
		typeQNameMappingHT.put(getTypeQName(Type.SHORT, Context.getDictionary()).toString(), new Integer(Type.SHORT));
		typeQNameMappingHT.put(getTypeQName(Type.BYTE, Context.getDictionary()).toString(), new Integer(Type.BYTE));
		typeQNameMappingHT.put(getTypeQName(Type.NON_POSITIVE_INTEGER, Context.getDictionary()).toString(), new Integer(Type.NON_POSITIVE_INTEGER));
		typeQNameMappingHT.put(getTypeQName(Type.NEGATIVE_INTEGER, Context.getDictionary()).toString(), new Integer(Type.NEGATIVE_INTEGER));
		typeQNameMappingHT.put(getTypeQName(Type.NON_NEGATIVE_INTEGER, Context.getDictionary()).toString(), new Integer(Type.NON_NEGATIVE_INTEGER));
		typeQNameMappingHT.put(getTypeQName(Type.POSITIVE_INTEGER, Context.getDictionary()).toString(), new Integer(Type.POSITIVE_INTEGER));
		typeQNameMappingHT.put(getTypeQName(Type.UNSIGNED_LONG, Context.getDictionary()).toString(), new Integer(Type.UNSIGNED_LONG));
		typeQNameMappingHT.put(getTypeQName(Type.UNSIGNED_INT, Context.getDictionary()).toString(), new Integer(Type.UNSIGNED_INT));
		typeQNameMappingHT.put(getTypeQName(Type.UNSIGNED_SHORT, Context.getDictionary()).toString(), new Integer(Type.UNSIGNED_SHORT));
		typeQNameMappingHT.put(getTypeQName(Type.UNSIGNED_BYTE, Context.getDictionary()).toString(), new Integer(Type.UNSIGNED_BYTE));
		typeQNameMappingHT.put(getTypeQName(Type.DECIMAL, Context.getDictionary()).toString(), new Integer(Type.DECIMAL));
		typeQNameMappingHT.put(getTypeQName(Type.DOUBLE, Context.getDictionary()).toString(), new Integer(Type.DOUBLE));
		typeQNameMappingHT.put(getTypeQName(Type.FLOAT, Context.getDictionary()).toString(), new Integer(Type.FLOAT));
		typeQNameMappingHT.put(getTypeQName(Type.DURATION, Context.getDictionary()).toString(), new Integer(Type.DURATION));
		typeQNameMappingHT.put(getTypeQName(Type.YEAR_MONTH_DURATION, Context.getDictionary()).toString(), new Integer(Type.YEAR_MONTH_DURATION));
		typeQNameMappingHT.put(getTypeQName(Type.DAY_TIME_DURATION, Context.getDictionary()).toString(), new Integer(Type.DAY_TIME_DURATION));
		typeQNameMappingHT.put(getTypeQName(Type.STRING, Context.getDictionary()).toString(), new Integer(Type.STRING));
		typeQNameMappingHT.put(getTypeQName(Type.DATE, Context.getDictionary()).toString(), new Integer(Type.DATE));
		typeQNameMappingHT.put(getTypeQName(Type.TIME, Context.getDictionary()).toString(), new Integer(Type.TIME));
		typeQNameMappingHT.put(getTypeQName(Type.DATE_TIME, Context.getDictionary()).toString(), new Integer(Type.DATE_TIME));
		typeQNameMappingHT.put(getTypeQName(Type.BOOLEAN, Context.getDictionary()).toString(), new Integer(Type.BOOLEAN));
		typeQNameMappingHT.put(getTypeQName(Type.QNAME, Context.getDictionary()).toString(), new Integer(Type.QNAME));
		typeQNameMappingHT.put(getTypeQName(Type.ANY_URI, Context.getDictionary()).toString(), new Integer(Type.ANY_URI));

		typeQNameMappingHT.put(getTypeQName(Type.BASE64_BINARY, Context.getDictionary()).toString(), new Integer(Type.BASE64_BINARY));
		typeQNameMappingHT.put(getTypeQName(Type.HEX_BINARY, Context.getDictionary()).toString(), new Integer(Type.HEX_BINARY));
		typeQNameMappingHT.put(getTypeQName(Type.G_DAY, Context.getDictionary()).toString(), new Integer(Type.G_DAY));
		typeQNameMappingHT.put(getTypeQName(Type.G_MONTH, Context.getDictionary()).toString(), new Integer(Type.G_MONTH));
		typeQNameMappingHT.put(getTypeQName(Type.G_YEAR, Context.getDictionary()).toString(), new Integer(Type.G_YEAR));
		typeQNameMappingHT.put(getTypeQName(Type.G_YEAR_MONTH, Context.getDictionary()).toString(), new Integer(Type.G_YEAR_MONTH));
		typeQNameMappingHT.put(getTypeQName(Type.G_MONTH_DAY, Context.getDictionary()).toString(), new Integer(Type.G_MONTH_DAY));

		typeQNameMappingHT.put(getTypeQName(Type.NORMALIZED_STRING, Context.getDictionary()).toString(), new Integer(Type.NORMALIZED_STRING));
		typeQNameMappingHT.put(getTypeQName(Type.TOKEN, Context.getDictionary()).toString(), new Integer(Type.TOKEN));
		typeQNameMappingHT.put(getTypeQName(Type.LANGUAGE, Context.getDictionary()).toString(), new Integer(Type.LANGUAGE));
		typeQNameMappingHT.put(getTypeQName(Type.NAME, Context.getDictionary()).toString(), new Integer(Type.NAME));
		typeQNameMappingHT.put(getTypeQName(Type.NCNAME, Context.getDictionary()).toString(), new Integer(Type.NCNAME));
		typeQNameMappingHT.put(getTypeQName(Type.UNTYPED, Context.getDictionary()).toString(), new Integer(Type.UNTYPED));

		// currently not supported
		typeQNameMappingHT.put(getTypeQName(Type.NOTATION, Context.getDictionary()).toString(), new Integer(Type.NOTATION));
		typeQNameMappingHT.put(getTypeQName(Type.NMTOKEN, Context.getDictionary()).toString(), new Integer(Type.NMTOKEN));
		typeQNameMappingHT.put(getTypeQName(Type.NMTOKENS, Context.getDictionary()).toString(), new Integer(Type.NMTOKENS));
		typeQNameMappingHT.put(getTypeQName(Type.ID, Context.getDictionary()).toString(), new Integer(Type.ID));
		typeQNameMappingHT.put(getTypeQName(Type.IDREF, Context.getDictionary()).toString(), new Integer(Type.IDREF));
		typeQNameMappingHT.put(getTypeQName(Type.IDREFS, Context.getDictionary()).toString(), new Integer(Type.IDREFS));
		typeQNameMappingHT.put(getTypeQName(Type.ENTITY, Context.getDictionary()).toString(), new Integer(Type.ENTITY));
		typeQNameMappingHT.put(getTypeQName(Type.ENTITIES, Context.getDictionary()).toString(), new Integer(Type.ENTITIES));

		// to be removed in item version of the engine
		typeQNameMappingHT.put(getTypeQName(Type.START_DOCUMENT, Context.getDictionary()).toString(), new Integer(Type.START_DOCUMENT));
		typeQNameMappingHT.put(getTypeQName(Type.END_DOCUMENT, Context.getDictionary()).toString(), new Integer(Type.END_DOCUMENT));
		typeQNameMappingHT.put(getTypeQName(Type.START_TAG, Context.getDictionary()).toString(), new Integer(Type.START_TAG));
		typeQNameMappingHT.put(getTypeQName(Type.END_TAG, Context.getDictionary()).toString(), new Integer(Type.END_TAG));
		typeQNameMappingHT.put(getTypeQName(Type.START_SEQUENCE, Context.getDictionary()).toString(), new Integer(Type.START_SEQUENCE));
		typeQNameMappingHT.put(getTypeQName(Type.END_SEQUENCE, Context.getDictionary()).toString(), new Integer(Type.END_SEQUENCE));
	}

	public static int getTypeFootprint(QName q, TypeDictionary dictionary) throws MXQueryException {
		if (q == null)
			throw new RuntimeException("QName == null!");

		if (typeQNameMappingHT == null)
			initTypeQNameMappingStore();

		Integer iType;
		if (q.getNamespacePrefix() == null || !q.getNamespacePrefix().equals(Type.NAMESPACE_XS) && dictionary != null) {
			String namespace = q.getNamespacePrefix();
			
			if (namespace == null)
				throw new StaticException(ErrorCodes.E0081_STATIC_QUERY_UNKNOWN_NAMESPACE, "Prefix " + q.getNamespacePrefix() + " has not been bound to a namespace", null);

			iType = dictionary.getTypeAsInteger(getUDTName(q));
			if (iType == null)
				throw new StaticException(ErrorCodes.E0036_STATIC_MODULE_UNDECLARED_SCHEMA_TYPE, "Type {" + namespace + "}" + q.getLocalPart() + " unknown !", null);
			int t = iType.intValue();
			iType = Integer.valueOf(String.valueOf(setTypeBit(t, BIT_POSITION_UDT, '1'))); // Integer.valueOf(int)
			// not
			// supported
			// in
			// CLDC
			// 1.0
		} else {
			iType = (Integer) typeQNameMappingHT.get(q.toString());

			// such type doesn't exist
			if (iType == null) {
				throw new StaticException(ErrorCodes.E0051_STATIC_QNAME_AS_ATOMICTYPE_NOT_DEFINED_AS_ATOMIC, "Type " + q.toString() + " is not valid !", null);
			}
		}

		int type = iType.intValue();
//		switch (type) {
//		case NMTOKENS:
//		case ENTITIES:
//		case IDREFS:
//			throw new StaticException(ErrorCodes.E0051_STATIC_QNAME_AS_ATOMICTYPE_NOT_DEFINED_AS_ATOMIC, "Unknown atomic type " + q.toString(), null);
//		}

		return type;
	}

	/**
	 * 
	 * @param q
	 *            the QName corresponding to a type
	 * @return the name of the type in the form {namespace uri}typeName
	 */
	public static String getUDTName(QName q) {
		return "{" + q.getNamespacePrefix() + "}" + q.getLocalPart();
	}

	/**
	 * @param type any type
	 * @param dict the dictionary of user-defined types
	 * @return xs:integer for all it's subtypes xs:string for all it's subtypes
	 *         the unchanged type for all other types
	 * 
	 */
	public static int getEventTypeSubstituted(int type, TypeDictionary dict) {

		int eventType = type & MASK_CLEAN_ADDITIONAL_INFO;

		/* handle all subtypes of xs:integer uniformly */
		if (Type.isTypeOrSubTypeOf(eventType, Type.INTEGER, dict)) {
			return Type.INTEGER;
		}
		
		/* handle all subtypes of xs:string uniformly */
		if (Type.isTypeOrSubTypeOf(eventType, Type.STRING, dict)) {
			return Type.STRING;
		}

		return type;
	}

	
	public static QName getTypeQName(int type, TypeDictionary dictionary) {
		QName q;

		if (Type.isAttribute(type))
			return new QName(null, "attribute()");
		else if (Type.isTextNode(type))
			return new QName(null, "text()");
		if (Type.isUserDefinedType(type)){
			int udtType = type & MASK_USER_DEFINED_TYPE_INDEX;
			XSTypeDefinition typeDef = (XSTypeDefinition) dictionary.lookUpByIntegerRepresentation(udtType);
			if (typeDef.getName() != null)
			return new QName("{"+typeDef.getNamespace()+"}",typeDef.getName());
			else { String anonymousType = dictionary.getTypeNameByIndex(udtType);
			int index = anonymousType.indexOf("#");
			String ns_uri = anonymousType.substring(0,index);
			String localPart = anonymousType.substring(index);
			return new QName(ns_uri,localPart);
			/*String tokens[] = anonymousType.split("#");
			return new QName(tokens[0],"#"+tokens[1]);*/ 
			}
		} 
		if ((type & MASK_CLEAN_START_TAG) != 0) {
			 type = type & MASK_CLEAN_START_TAG; // element with type annotation => type annotation
		}
		
		
		switch (type) {
		case ITEM:
			q = new QName(null, "item()");
			break;
		case NODE:
			q = new QName(null, "node()");
			break;
		case START_DOCUMENT:
		case END_DOCUMENT:
			q = new QName(null, "document-node()");
			break;
		case START_TAG:
			q = new QName(null, "element_start()");
			break;
		case END_TAG:
			q = new QName(null, "element_end()");
			break;
		case COMMENT:
			q = new QName(null, "comment()");
			break;
		case TYPE_NK_TEXT_TEST:
			q = new QName(null, "text()");
			break;

		case NAMESPACE:
			q = new QName(null, "namespace()");
			break;
		case PROCESSING_INSTRUCTION:
			q = new QName(null, "processing-instruction()");
			break;
		case ANY_TYPE:
			q = new QName(NAMESPACE_XS, "anyType");
			break;
		case ANY_SIMPLE_TYPE:
			q = new QName(NAMESPACE_XS, "anySimpleType");
			break;
		case ANY_ATOMIC_TYPE:
			q = new QName(NAMESPACE_XS, "anyAtomicType");
			break;
		case UNTYPED_ATOMIC:
			q = new QName(NAMESPACE_XS, "untypedAtomic");
			break;
		case NUMBER:
			q = new QName(NAMESPACE_MXQ, "number");
			break;
		case DECIMAL:
			q = new QName(NAMESPACE_XS, "decimal");
			break;
		case INTEGER:
			q = new QName(NAMESPACE_XS, "integer");
			break;
		case NON_POSITIVE_INTEGER:
			q = new QName(NAMESPACE_XS, "nonPositiveInteger");
			break;
		case NEGATIVE_INTEGER:
			q = new QName(NAMESPACE_XS, "negativeInteger");
			break;
		case LONG:
			q = new QName(NAMESPACE_XS, "long");
			break;
		case INT:
			q = new QName(NAMESPACE_XS, "int");
			break;
		case SHORT:
			q = new QName(NAMESPACE_XS, "short");
			break;
		case BYTE:
			q = new QName(NAMESPACE_XS, "byte");
			break;
		case NON_NEGATIVE_INTEGER:
			q = new QName(NAMESPACE_XS, "nonNegativeInteger");
			break;
		case POSITIVE_INTEGER:
			q = new QName(NAMESPACE_XS, "positiveInteger");
			break;
		case UNSIGNED_LONG:
			q = new QName(NAMESPACE_XS, "unsignedLong");
			break;
		case UNSIGNED_INT:
			q = new QName(NAMESPACE_XS, "unsignedInt");
			break;
		case UNSIGNED_SHORT:
			q = new QName(NAMESPACE_XS, "unsignedShort");
			break;
		case UNSIGNED_BYTE:
			q = new QName(NAMESPACE_XS, "unsignedByte");
			break;
		case DOUBLE:
			q = new QName(NAMESPACE_XS, "double");
			break;
		case FLOAT:
			q = new QName(NAMESPACE_XS, "float");
			break;
		case DURATION:
			q = new QName(NAMESPACE_XS, "duration");
			break;
		case YEAR_MONTH_DURATION:
			q = new QName(NAMESPACE_XS, "yearMonthDuration");
			break;
		case DAY_TIME_DURATION:
			q = new QName(NAMESPACE_XS, "dayTimeDuration");
			break;
		case STRING:
			q = new QName(NAMESPACE_XS, "string");
			break;
		case DATE:
			q = new QName(NAMESPACE_XS, "date");
			break;
		case TIME:
			q = new QName(NAMESPACE_XS, "time");
			break;
		case DATE_TIME:
			q = new QName(NAMESPACE_XS, "dateTime");
			break;
		case BOOLEAN:
			q = new QName(NAMESPACE_XS, "boolean");
			break;
		case QNAME:
			q = new QName(NAMESPACE_XS, "QName");
			break;
		case ANY_URI:
			q = new QName(NAMESPACE_XS, "anyURI");
			break;

		case BASE64_BINARY:
			q = new QName(NAMESPACE_XS, "base64Binary");
			break;
		case HEX_BINARY:
			q = new QName(NAMESPACE_XS, "hexBinary");
			break;
		case G_DAY:
			q = new QName(NAMESPACE_XS, "gDay");
			break;
		case G_MONTH:
			q = new QName(NAMESPACE_XS, "gMonth");
			break;
		case G_YEAR:
			q = new QName(NAMESPACE_XS, "gYear");
			break;
		case G_YEAR_MONTH:
			q = new QName(NAMESPACE_XS, "gYearMonth");
			break;
		case G_MONTH_DAY:
			q = new QName(NAMESPACE_XS, "gMonthDay");
			break;

		case NOTATION:
			q = new QName(NAMESPACE_XS, "NOTATION");
			break;
		case NMTOKEN:
			q = new QName(NAMESPACE_XS, "NMTOKEN");
			break;
		case NMTOKENS:
			q = new QName(NAMESPACE_XS, "NMTOKENS");
			break;
		case ID:
			q = new QName(NAMESPACE_XS, "ID");
			break;
		case IDREF:
			q = new QName(NAMESPACE_XS, "IDREF");
			break;
		case IDREFS:
			q = new QName(NAMESPACE_XS, "IDREFS");
			break;
		case ENTITY:
			q = new QName(NAMESPACE_XS, "ENTITY");
			break;
		case ENTITIES:
			q = new QName(NAMESPACE_XS, "ENTITIES");
			break;

		case NORMALIZED_STRING:
			q = new QName(NAMESPACE_XS, "normalizedString");
			break;
		case TOKEN:
			q = new QName(NAMESPACE_XS, "token");
			break;
		case LANGUAGE:
			q = new QName(NAMESPACE_XS, "language");
			break;
		case NAME:
			q = new QName(NAMESPACE_XS, "Name");
			break;
		case NCNAME:
			q = new QName(NAMESPACE_XS, "NCName");
			break;
		case UNTYPED:
			q = new QName(NAMESPACE_XS, "untyped");
			break;

		case START_SEQUENCE:
			q = new QName(null, "START_SEQUENCE");
			break;
		case END_SEQUENCE:
			q = new QName(null, "END_SEQUENCE");
			break;

		default:
			throw new RuntimeException("Incorrect type passed. Type " + type + " is not supported in method getTypeQName");
		}
		return q;	}

	public static char getOccurSymbol(int occurID){
		switch (occurID){
		case OCCURRENCE_IND_ZERO_OR_MORE: return '*';
		case OCCURRENCE_IND_ONE_OR_MORE: return '+';
		case OCCURRENCE_IND_ZERO_OR_ONE: return '?';
		case OCCURRENCE_IND_EXACTLY_ONE: return ' ';
		default: return ' ';	//TODO: what about infinity?
		}
	}
	public static int getOccurID(char symbol){
		switch (symbol){
		case '*': return OCCURRENCE_IND_ZERO_OR_MORE;
		case '+': return OCCURRENCE_IND_ONE_OR_MORE;
		case '?': return OCCURRENCE_IND_ZERO_OR_ONE;
		case ' ': return OCCURRENCE_IND_EXACTLY_ONE;
		default: return OCCURRENCE_IND_EXACTLY_ONE; //TODO:what about infinity? 
		}
	}

	/**
	 * @param type
	 *            attribute type
	 * @return type of the attribute value (e.g. xs:integer, etc.)
	 */
	public static int getAttributeValueType(int type) {
		return type & MASK_STRIP_ATTRIBUTE_NODE;
	}

	/**
	 * @param type
	 *            text node type
	 * @return type of the text node value (e.g. xs:integer, etc.)
	 */
	public static int getTextNodeValueType(int type) {
		return type & MASK_STRIP_TEXT_NODE;
	}

	/**
	 * @param type
	 *            any atomic type
	 * @return type marked as attribute type
	 * @throws TypeException if type is not an atomic type
	 */
	public static int createAttributeType(int type) throws TypeException {
		if (!Type.isSimpleType(type, Context.getDictionary()))
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,"Cannot create an attribute type from a non-atomic type",QueryLocation.OUTSIDE_QUERY_LOC);
		return type | MASK_CHECK_ATTRIBUTE_NODE;
	}

	/**
	 * @param type
	 *            any atomic type
	 * @return type marked as text node type
	 */
	public static int createTextNodeType(int type) {
		type = type | MASK_CHECK_TEXT_NODE;
		return type;
	}
	/**
	 * Checks if a type is simple (i.e. a subtype of xs:anySimpleType) Convenience Method + possibly faster than explicit subtype check.
	 * @param type Integer representation of type
	 * @return true, if subtype of xs:anySimpleType, else false
	 */
	public static boolean isSimpleType(int type, TypeDictionary dictionary) {
		if (isUserDefinedType(type)) {
			int udtType = type & MASK_USER_DEFINED_TYPE_INDEX;
			XSTypeDefinition typeDef = (XSTypeDefinition) dictionary.lookUpByIntegerRepresentation(udtType);
			if (typeDef == null)
				return false;
			else 
				if (typeDef.getTypeCategory() == XSTypeDefinition.SIMPLE_TYPE)
					return true;
				else
					return false;
		} else {
			return isTypeOrSubTypeOf(type, Type.ANY_SIMPLE_TYPE, dictionary);
		}
	}
	
	/**
	 * 
	 * @param type
	 *            atomic type
	 * @param dictionary
	 * @return true if the actual type is an atomic type, else false
	 */
	public static boolean isAtomicType(int type, TypeDictionary dictionary) {
		if (Type.isUserDefinedType(type))
			return isUDTAtomic(type, dictionary);
		else
			return isTypeOrSubTypeOf(type, Type.ANY_ATOMIC_TYPE, dictionary);
	}

	/**
	 * Checks whether a UDT is atomic
	 * @param type
	 * @param dictionary
	 * 
	 * @return true if the actual UDT type is an atomic type
	 */
	public static boolean isUDTAtomic(int type, TypeDictionary dictionary) {
		int udtType = type & MASK_USER_DEFINED_TYPE_INDEX;
		XSTypeDefinition typeDef = (XSTypeDefinition) dictionary.lookUpByIntegerRepresentation(udtType);

		if (typeDef == null)
			return false;
		else if (typeDef.getTypeCategory() == XSTypeDefinition.SIMPLE_TYPE)
			if (((XSSimpleTypeDefinition) typeDef).getPrimitiveType() != null)
				return true;
			else
				return false;
		return false;
	}

	
	/**
	 * @param type
	 *            attribute type
	 * @return true if the actual type is an attribute node kind, else false
	 */
	public static boolean isAttribute(int type) {
		return (MASK_CHECK_ATTRIBUTE_NODE & type) == MASK_CHECK_ATTRIBUTE_NODE;
	}

	/**
	 * Returns true if the passed type is END_TAG, END_SEQUENCE, or
	 * END_DOCUMENT.
	 * 
	 * @param type
	 *            event type
	 * @return true/false
	 */
	public static boolean isEndType(int type) {
		return type == END_TAG || type == END_SEQUENCE || type == END_DOCUMENT;
	}

	/**
	 * Returns true if the passed type is START_TAG, START_SEQUENCE, or
	 * START_DOCUMENT.
	 * 
	 * @param type
	 *            event type
	 * @return true/false
	 */
	public static boolean isStartType(int type) {
		return (type & MASK_CHECK_START_BIT) == MASK_CHECK_START_BIT;
	}

	/**
	 * Returns true if the passed type has/needs an id or not.
	 * 
	 * @param type
	 * @return true if the type can carry an identifer
	 */
	public static boolean isIdeedType(int type) {
		return !isEndType(type);
	}

	/**
	 * Returns true if the passed type is NUMERIC
	 * 
	 * @param type
	 *            event type
	 * @return true/false
	 */
	public static boolean isNumericPrimitiveType(int type) {
		// clean additional info bits
		type = type & MASK_CLEAN_ADDITIONAL_INFO;
		return isSubTypeOf(type, DECIMAL, null) || type == DOUBLE || type == FLOAT || type == DECIMAL;

		// return isSubTypeOf(type, NUMBER);
	}

	/**
	 * cleans up additional info and returns basic type
	 * 
	 * @param type
	 * @return the basic atomic type of the given type 
	 */
	public static int getPrimitiveAtomicType(int type) {
		// clean additional info bits
		type = type & MASK_CLEAN_ADDITIONAL_INFO;
		return type;
	}

	/**
	 * Returns true if the passed type is Node type, else false
	 * 
	 * @param type
	 *            event type
	 * @return true/false
	 */
	public static boolean isNode(int type) {
		return ((type & MASK_CHECK_ANN_NODE) != 0) || (((type & MASK_CHECK_SIMPLE_NODE) != 0) && ((type & 1) == 0));
	}

	/**
	 * Returns true if the passed type is Text node kind type
	 * 
	 * @param type
	 *            event type
	 * @return true/false
	 */
	public static boolean isTextNode(int type) {
		return (MASK_CHECK_TEXT_NODE & type) == MASK_CHECK_TEXT_NODE;
	}

	/***************************************************************************
	 * Is parameter type1 is the same type as type or a subtype of parameter
	 * type2
	 * 
	 * @param type1
	 * @param type2
	 * @param dictionary
	 *            TODO
	 * @return true if yes, false if no
	 */

	public static boolean isTypeOrSubTypeOf(int type1, int type2, TypeDictionary dictionary) {
		return type1 == type2 || isSubTypeOf(type1, type2, dictionary);
	}

	/***************************************************************************
	 * Is parameter type1 subtype of parameter type2
	 * 
	 * @param type1
	 * @param type2
	 * @param dictionary
	 * @return true if yes, false if no
	 */
	public static boolean isSubTypeOf(int type1, int type2, TypeDictionary dictionary) {
		if (Type.isUserDefinedType(type2) && !Type.isUserDefinedType(type1))
			return false;
		else if (Type.isUserDefinedType(type1))
			return isUDTsubtypeof(type1, type2, dictionary);
		else {
			if (isNode(type1) || isNode(type2))
				return isNodeSubTypeOf(type1, type2);

			// clean reserved bits
			type1 = type1 & MASK_CLEAN_ADDITIONAL_INFO;
			type2 = type2 & MASK_CLEAN_ADDITIONAL_INFO;

			if (type1 == type2 || type2 == ITEM)
				return true;
			
			int sizeT1 = highestOneBit(type1);
			int sizeT2 = highestOneBit(type2);

			if (sizeT1 <= sizeT2)
				return false;

			int mask = sizeT2 * 2 - 1;

			return (mask & type1) == type2;
		}
	}

	/***************************************************************************
	 * Is parameter type1 subtype of parameter type2 (at least type1 is UDT)
	 * 
	 * @param type1
	 * @param type2
	 * @param dictionary
	 *            TODO
	 * @return true if yes, false if no
	 */
	public static boolean isUDTsubtypeof(int type1, int type2, TypeDictionary dictionary) {
		int type1Idx = type1 & MASK_USER_DEFINED_TYPE_INDEX;
		// type1 = type1 & MASK_CLEAN_ADDITIONAL_INFO;
		// type1 = Type.setTypeBit(type1, BIT_POSITION_UDT, '0');

		XSTypeDefinition typeDef1 = (XSTypeDefinition) dictionary.lookUpByIntegerRepresentation(type1Idx);

		if (Type.isUserDefinedType(type2)) {
			type2 = type2 & MASK_USER_DEFINED_TYPE_INDEX;
			/*
			 * type2 = type2 & MASK_CLEAN_ADDITIONAL_INFO; type2 =
			 * Type.setTypeBit(type2, BIT_POSITION_UDT, '0');
			 */

			XSTypeDefinition typeDef2 = (XSTypeDefinition) dictionary.lookUpByIntegerRepresentation(type2);

			return typeDef1.derivedFromType(typeDef2, XSConstants.DERIVATION_NONE);
		} else {
			switch (type2) {
			// Special check, since derivedFrom() with simple, but not atomic types gives false
			case ANY_SIMPLE_TYPE:
				return isSimpleType(type1,dictionary);
			default:
				QName q = getTypeQName(type2, Context.getDictionary());
			return typeDef1.derivedFrom(XQStaticContext.URI_XS, q.getLocalPart(), XSConstants.DERIVATION_NONE);

			}
		}
	}

	/**
	 * Can parameter type1 be promoted to parameter type Also includes subtype
	 * substitution
	 * 
	 * @param type1
	 * @param type2
	 * @param dictionary TODO
	 * @return true if yes, false if no
	 */
	public static boolean typePromoteableTo(int type1, int type2, TypeDictionary dictionary) {
		type1 = type1 & MASK_CLEAN_ADDITIONAL_INFO;
		type2 = type2 & MASK_CLEAN_ADDITIONAL_INFO;

		int type1a = type1;

		if (isSubTypeOf(type1, DECIMAL, dictionary))
			type1a = DECIMAL;
		if ((type1a == FLOAT) && (type2 == DOUBLE))
			return true;
		if ((type1a == DECIMAL) && (type2 == FLOAT))
			return true;
		if ((type1a == DECIMAL) && (type2 == DOUBLE))
			return true;		
		if (type1a == ANY_URI && type2 == STRING)
			return true;
		return isSubTypeOf(type1, type2, dictionary);
	}

	private static boolean isNodeSubTypeOf(int type1, int type2) {
		if (type2 == NODE || type2 == ITEM) {
			if (type2 == ITEM && type1 == NODE)
				return true;
			if (type2 == NODE && type1 != NODE && isNode(type1))
				return true;
		}

		return false;
	}

	/***************************************************************************
	 * 
	 * @param type1
	 * @param type2
	 * @return true if types are comparable, false if no
	 */
	public static boolean areComparable(int type1, int type2) {

		if (type1 == type2)
			return true;

		if (type1 == STRING || type1 == UNTYPED_ATOMIC || type1 == ANY_URI || type1 == UNTYPED) {
			if (type2 == Type.STRING || type2 == Type.UNTYPED_ATOMIC || type2 == ANY_URI || type1 == UNTYPED)
				return true;
			else
				return false;
		}
		// Duration types can be partially compared
		// same type => captured above
		// Duration + other types => depending on values in duration
		// day_time + year_month => if both are null
		// => so in any case the comparison needs to go into value comparison
		if ((type1 == DURATION || type1 == DAY_TIME_DURATION || type1 == YEAR_MONTH_DURATION) && (type2 == DURATION || type2 == DAY_TIME_DURATION || type2 == YEAR_MONTH_DURATION))
			return true;

		if (isNumericPrimitiveType(type1)) {
			if (isNumericPrimitiveType(type2))
				return true;
			else
				return false;
		}

		return false;
	}

	/***************************************************************************
	 * Is parameter type a user defined type
	 * 
	 * @param type a type (user-defined or built-in)
	 * @return true if yes, false if no
	 */
	public static boolean isUserDefinedType(int type) {
		if (type < 0)
			return false;
		else
			// throw new RuntimeException("User defined types currently are not
			// supported.");
			return ((type & MASK_USER_DEFINED_TYPE) == MASK_USER_DEFINED_TYPE);
	}

	/***************************************************************************
	 * Number conversion rules Subtype propogation: INTEGER -> DECIMAL Type
	 * promotion: DECIMAL -> FLOAT DECIMAL -> DOUBLE FLOAT -> DOUBLE
	 * 
	 * @param arg1Type
	 *            type of the first argument
	 * @param arg2Type
	 *            type of the second argument
	 * @return type of the numerical op result (evaluated expression)
	 */
	public static int getNumericalOpResultType(int arg1Type, int arg2Type) {

		if (arg1Type == arg2Type)
			return arg1Type;

		if (arg1Type == Type.DOUBLE || arg2Type == Type.DOUBLE)
			return Type.DOUBLE;

		if (arg1Type == Type.FLOAT || arg2Type == Type.FLOAT)
			return Type.FLOAT;

		if (arg1Type == Type.DECIMAL || arg2Type == Type.DECIMAL)
			return Type.DECIMAL;

		return Type.INTEGER;
	}

	/** methods for type encoding manipulation */
	private static int setTypeBit(int type, int position, char value) {

		String s = Integer.toString(type, 2);
		s = (ALL_0_BITS + s).substring(s.length());

		char[] ca = s.toCharArray();
		ca[31 - position] = value;
		s = new String(ca);

		return Integer.parseInt(s, 2);
	}

	/**
	 * Find the highest set bit in value, and return a new value with only that
	 * bit set.
	 * 
	 * @param value
	 *            the value to examine JAVA 1.5 source code
	 */
	private static int highestOneBit(int value) {
		value |= value >>> 1;
		value |= value >>> 2;
		value |= value >>> 4;
		value |= value >>> 8;
		value |= value >>> 16;
		return value ^ (value >>> 1);
	}

	/**
	 * Sets the bit corresponding to the "nillable" attribute
	 * 
	 * @param type
	 */
	public static int setIsNilled(int type) {
		return type | MASK_CHECK_NILLABLE;
	}

	/**
	 * Sets the bit corresponding to the "nillable" attribute to '0'
	 * 
	 * @param type
	 */
	public static int setNilledFalse(int type) {
		return type & MASK_STRIP_NILLABLE;
	}
	public static boolean isNilled(int type) {
		return ((type & MASK_CHECK_NILLABLE) == MASK_CHECK_NILLABLE);
	}
}

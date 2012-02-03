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

package ch.ethz.mxquery.functions.fn;

import java.util.Vector;

import org.apache.xerces.xs.ShortList;
import org.apache.xerces.xs.XSConstants;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.contextConfig.XQStaticContext;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeDictionary;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.NamedToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.datamodel.xdm.UntypedAtomicToken;
import ch.ethz.mxquery.datamodel.xdm.XDMScope;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.functions.Function;
import ch.ethz.mxquery.functions.xs.XSBinary;
import ch.ethz.mxquery.functions.xs.XSBoolean;
import ch.ethz.mxquery.functions.xs.XSConstructorIterator;
import ch.ethz.mxquery.functions.xs.XSDate;
import ch.ethz.mxquery.functions.xs.XSDateTime;
import ch.ethz.mxquery.functions.xs.XSDayTimeDuration;
import ch.ethz.mxquery.functions.xs.XSDecimal;
import ch.ethz.mxquery.functions.xs.XSDouble;
import ch.ethz.mxquery.functions.xs.XSDuration;
import ch.ethz.mxquery.functions.xs.XSFloat;
import ch.ethz.mxquery.functions.xs.XSGregorian;
import ch.ethz.mxquery.functions.xs.XSInteger;
import ch.ethz.mxquery.functions.xs.XSQName;
import ch.ethz.mxquery.functions.xs.XSString;
import ch.ethz.mxquery.functions.xs.XSTime;
import ch.ethz.mxquery.functions.xs.XSYearMonthDuration;
import ch.ethz.mxquery.iterators.SequenceIterator;
import ch.ethz.mxquery.iterators.TokenIterator;
import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.EmptySequenceIterator;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.util.Utils;

public class DataValuesIterator extends CurrentBasedIterator {

    private boolean evalComplete = false;

    int level = 0;

    private boolean fnData = true;

    private boolean first = true;

    private boolean functionIsSet = false;

    private boolean isListType = false;

    private ShortList listValueTypes;

    DataValuesIterator(Context ctx, XDMIterator iter, QueryLocation location) {
	super(ctx, location);
	if (iter == null) {
	    throw new IllegalArgumentException();
	}
	this.subIters = new XDMIterator[] { iter };
    }

    private DataValuesIterator(XDMIterator iter, QueryLocation location) {

	super(null, location);
	this.fnData = true;
	if (iter == null) {
	    throw new IllegalArgumentException();
	}
	this.subIters = new XDMIterator[] { iter };
    }

    public DataValuesIterator() {
	super(null, null);
    }

    public Token next() throws MXQueryException {
	// Order of if's is important!

	if (!evalComplete) {
	    if (!(current instanceof SequenceIterator))
		current = getValue(subIters[0]);
	    Token e = current.next();
	    if (e.getEventType() == Type.END_SEQUENCE) {
		evalComplete = true;
	    }
	    return e;
	} else {
	    return Token.END_SEQUENCE_TOKEN;
	}
    }

    /**
     * This method helps to atomize values and elements. This method is
     * especially optimized for the common cases: It's directly an atomic value
     * or its an non nested element.
     * 
     * @param iter
     *                Returns a Atomic or EmptySequence iterator
     * @return
     * @throws MXQueryException
     */
    private XDMIterator getValue(XDMIterator iter) throws MXQueryException {
	boolean noAttrsAllowed = false;
	TokenIterator currentIter = null;
	boolean singleValue = true;
	XDMIterator func = null;
	XDMScope nsScope = null;

	StringBuffer string = null;
	Token tok;
	int contentType = -1;

	do {
	    tok = iter.next();
	    // System.out.println(tok.getText());
	    int typeAnnotation = -1;

	    int type = tok.getEventType();
	    if (tok instanceof NamedToken) {
		nsScope = tok.getDynamicScope();
		typeAnnotation = tok.getTypeAnnotation();
		contentType = TypeDictionary.findContentType(typeAnnotation,
			Context.getDictionary());
		// debugContentType(contentType, typeAnnotation);
		if (first
			&& (!(typeAnnotation == Type.UNTYPED
				|| typeAnnotation == Type.ANY_TYPE || contentType == TypeDictionary.COMPLEX_MIXED) || contentType == -1))
		    switch (contentType) {
		    case TypeDictionary.COMPLEX_ELEMENT_ONLY:
			if (fnData)
			    throw new TypeException(
				    ErrorCodes.F0035_AGUMENT_NOD_NO_TYPED_VALUE,
				    "Cannot get the typed value of an element with element-only content",
				    loc);
			break;
		    case TypeDictionary.COMPLEX_EMPTY:
			return new EmptySequenceIterator(context, loc);
		    case TypeDictionary.COMPLEX_SIMPLE:
		    case TypeDictionary.SIMPLE:
			if (!fnData && tok.getSchemaNormalizedValue() != null) {
			    Token tk = tok;
			    tok = iter.next();
			    // evalComplete = true;
			    return new TokenIterator(context, tk
				    .getSchemaNormalizedValue(),
				    Type.UNTYPED_ATOMIC, loc);
			} else if (Type.isNilled(typeAnnotation))
			    return new EmptySequenceIterator(context, loc);
			else if (typeAnnotation == Type.ANY_SIMPLE_TYPE
				|| typeAnnotation == Type.ANY_ATOMIC_TYPE) {
			    Token tk = tok;
			    tok = iter.next();
			    return new TokenIterator(context, tk
				    .getSchemaNormalizedValue(),
				    Type.UNTYPED_ATOMIC, loc);
			}

			else if (fnData
				&& !(typeAnnotation == Type.UNTYPED || typeAnnotation == Type.UNTYPED_ATOMIC)) {
			    QName qName = Type.getTypeQName(typeAnnotation,
				    Context.getDictionary());
			    if (Type.isUserDefinedType(typeAnnotation)) {
				String namespace = qName.getNamespacePrefix()
					.substring(
						1,
						qName.getNamespacePrefix()
							.indexOf("}"));
				String prefix = context.getPrefix(namespace);
				qName = new QName(namespace, prefix, qName
					.getLocalPart());
			    } else
				qName = new QName(XQStaticContext.URI_XS,
					Type.NAMESPACE_XS, qName.getLocalPart());
			    Function fks = null;
			    if (qName.getLocalPart() != null)
				fks = context.getFunction(qName, 1);
			    if (fks == null) {
				if (qName.getLocalPart().indexOf("#AnonType") != -1)
				    func = getBaseTypeConstructor(context,
					    typeAnnotation, Context
						    .getDictionary());
				else if ((listValueTypes = ((NamedToken) tok)
					.getListValueTypes()) != null) {
				    functionIsSet = true;
				    isListType = true;
				    break;
				} else
				    break;
			    } else {
				func = fks.getFunctionImplementation(context);
			    }
			    if (nsScope != null && func instanceof XSQName)
				((XSQName) func).setNsScope(nsScope);
			    func.setLoc(loc);
			    functionIsSet = true;

			} else
			    break;
		    default:
			break;
		    }
	    }
	    if (typeAnnotation == Type.ANY_TYPE)
		first = false;
	    // xs:untyped , xs:anyType, COMPLEX_MIXED
	    while (noAttrsAllowed && Type.isAttribute(type)) {
		tok = iter.next();
		type = tok.getEventType();
	    }

	    if (Type.isAttribute(type)) {

		if (functionIsSet) {
		    if (isListType) {
			isListType = false;
			func = getIteratorForList(context, tok
				.getValueAsString(), listValueTypes, loc);
		    } else
			func.setSubIters(new TokenIterator(context, tok
				.getValueAsString(), Type.STRING, loc));
		    return func;
		}
		currentIter = new TokenIterator(context,
			tok.getValueAsString(), Type.UNTYPED_ATOMIC, loc);
		return currentIter;
	    }

	    if (Type.isTextNode(type)) {
		type = Type.getTextNodeValueType(type);
	    }

	    /* handle all subtypes of xs:integer / xs:string uniformly */
	    int checkType = Type.getEventTypeSubstituted(type, Context
		    .getDictionary());

	    switch (checkType) {
	    case Type.STRING:
	    case Type.UNTYPED_ATOMIC:
	    case Type.UNTYPED:
	    case Type.ANY_URI:
		if (singleValue) {
		    if (type == Type.UNTYPED)
			type = Type.UNTYPED_ATOMIC;

		    if (functionIsSet && isListType) {
			func = getIteratorForList(context, tok.getText(),
				listValueTypes, loc);
			isListType = false;
			return func;
		    } else
			currentIter = new TokenIterator(context, tok.getText(),
				type, loc);
		    singleValue = false;
		} else {
		    if (string == null) {
			string = new StringBuffer();
		    }
		    string.append(tok.getText());
		}
		break;
	    case Type.BOOLEAN:
		if (singleValue) {
		    currentIter = new TokenIterator(context, tok.getBoolean(),
			    loc);
		    singleValue = false;
		} else {
		    if (string == null) {
			string = new StringBuffer();
		    }
		    string.append(tok.getBoolean());
		}
		break;
	    case Type.INTEGER:
		if (singleValue) {
		    currentIter = new TokenIterator(context, tok.getLong(),
			    type, loc);
		    singleValue = false;
		} else {
		    if (string == null) {
			string = new StringBuffer();
		    }
		    string.append(tok.getLong());
		}
		break;
	    case Type.DOUBLE:
	    case Type.FLOAT:
	    case Type.DECIMAL:
		if (singleValue) {
		    currentIter = new TokenIterator(context, tok.getNumber(),
			    type, loc);
		    singleValue = false;
		} else {
		    if (string == null) {
			string = new StringBuffer();
		    }
		    string.append(tok.getValueAsString());
		}
		break;
	    case Type.DATE_TIME:
		if (singleValue) {
		    currentIter = new TokenIterator(context, tok.getDateTime(),
			    loc);
		    singleValue = false;
		} else {
		    if (string == null) {
			string = new StringBuffer();
		    }
		    string.append(tok.getDateTime());
		}
		break;
	    case Type.DATE:
		if (singleValue) {
		    currentIter = new TokenIterator(context, tok.getDate(), loc);
		    singleValue = false;
		} else {
		    if (string == null) {
			string = new StringBuffer();
		    }
		    string.append(tok.getDate());
		}
		break;
	    case Type.TIME:
		if (singleValue) {
		    currentIter = new TokenIterator(context, tok.getTime(), loc);
		    singleValue = false;
		} else {
		    if (string == null) {
			string = new StringBuffer();
		    }
		    string.append(tok.getTime());
		}
		break;
	    case Type.DAY_TIME_DURATION:
		if (singleValue) {
		    currentIter = new TokenIterator(context, tok
			    .getDayTimeDur(), loc);
		    singleValue = false;
		} else {
		    if (string == null) {
			string = new StringBuffer();
		    }
		    string.append(tok.getDayTimeDur());
		}
		break;
	    case Type.YEAR_MONTH_DURATION:
		if (singleValue) {
		    currentIter = new TokenIterator(context, tok
			    .getYearMonthDur(), loc);
		    singleValue = false;
		} else {
		    if (string == null) {
			string = new StringBuffer();
		    }
		    string.append(tok.getYearMonthDur());
		}
		break;
	    case Type.DURATION:
		if (singleValue) {
		    currentIter = new TokenIterator(context, tok.getDuration(),
			    loc);
		    singleValue = false;
		} else {
		    if (string == null) {
			string = new StringBuffer();
		    }
		    string.append(tok.getDuration());
		}
		break;
	    case Type.QNAME:
		if (singleValue) {
		    currentIter = new TokenIterator(context, tok
			    .getQNameTokenValue(), loc);
		    singleValue = false;
		} else {
		    if (string == null) {
			string = new StringBuffer();
		    }
		    string.append(tok.getQNameTokenValue());
		}
		break;

	    case Type.G_DAY:
	    case Type.G_MONTH:
	    case Type.G_YEAR:
	    case Type.G_MONTH_DAY:
	    case Type.G_YEAR_MONTH:

		if (singleValue) {
		    currentIter = new TokenIterator(context,
			    tok.getGregorian(), loc);
		    singleValue = false;
		} else {
		    if (string == null) {
			string = new StringBuffer();
		    }
		    string.append(tok.getGregorian());
		}
		break;

	    case Type.HEX_BINARY:
	    case Type.BASE64_BINARY:

		if (singleValue) {
		    currentIter = new TokenIterator(context, tok.getBinary(),
			    loc);
		    singleValue = false;
		} else {
		    if (string == null) {
			string = new StringBuffer();
		    }
		    string.append(tok.getBinary());
		}
		break;

	    case Type.COMMENT:
		if (level > 0)
		    continue;
		if (singleValue) {
		    currentIter = new TokenIterator(context, tok.getText(), loc);
		    singleValue = false;
		} else {
		    if (string == null) {
			string = new StringBuffer();
		    }
		    string.append(tok.getText());
		}
		break;
	    case Type.PROCESSING_INSTRUCTION:
		if (level > 0)
		    continue;
		if (singleValue) {
		    currentIter = new TokenIterator(context, tok.getText(), loc);
		    singleValue = false;
		} else {
		    if (string == null) {
			string = new StringBuffer();
		    }
		    string.append(tok.getText());
		}
		break;

	    case Type.START_TAG:
	    case Type.START_DOCUMENT:
		level++;
		// if you want the data of a node, you do not want to see
		// attributes.
		noAttrsAllowed = true;
		break;
	    case Type.END_TAG:
	    case Type.END_DOCUMENT:
		level--;
		break;
	    case Type.END_SEQUENCE:
		return new EmptySequenceIterator(context, loc);
	    }
	} while (level > 0);

	// no attribute handling because attributes are always single values
	if (!singleValue && !Type.isAttribute(tok.getEventType())) {
	    if (string == null) {
		if (functionIsSet && tok instanceof NamedToken) {
		    tok = currentIter.next();
		    func.setSubIters(new TokenIterator(context, tok
			    .getValueAsString(), Type.STRING, loc));
		    return func;
		} else
		    return currentIter;
	    } else {
		String value = "";

		tok = currentIter.next();
		if (Type.isAtomicType(tok.getEventType(), Context
			.getDictionary())) {
		    value += (tok.getValueAsString());
		}

		if (functionIsSet) {
		    if (isListType) {
			isListType = false;
			func = getIteratorForList(context, value,
				listValueTypes, loc);
		    } else
			func.setSubIters(new TokenIterator(context, value,
				Type.STRING, loc));
		    return func;
		} else
		    // since Typed XML is not supported result is of type
		    // xs:untypedAtomic
		    return new TokenIterator(context,
			    value + string.toString(), Type.UNTYPED_ATOMIC, loc);
	    }
	} else {
	    return new TokenIterator(context, new UntypedAtomicToken(null, ""),
		    null, loc);
	}
    }

    private XDMIterator getIteratorForList(Context context,
	    String valueAsString, ShortList listValueTypes, QueryLocation loc)
	    throws MXQueryException {
	// Transform Xerces data type representations 2 MXQuery Type System
	// representations
	// String[] values = valueAsString.split(" ");
	String[] values = Utils.split(valueAsString, " ");
	int length = listValueTypes.getLength();
	XDMIterator[] subIters = new XDMIterator[length];
	for (int i = 0; i < listValueTypes.getLength(); i++) {
	    short type = listValueTypes.item(i);
	    int typeAnnotation;
	    switch (type) {
	    case XSConstants.ANYSIMPLETYPE_DT:
		typeAnnotation = Type.ANY_SIMPLE_TYPE;
		break;
	    case XSConstants.ANYURI_DT:
		typeAnnotation = Type.ANY_URI;
		break;
	    case XSConstants.BASE64BINARY_DT:
		typeAnnotation = Type.BASE64_BINARY;
		break;
	    case XSConstants.BOOLEAN_DT:
		typeAnnotation = Type.BOOLEAN;
		break;
	    case XSConstants.BYTE_DT:
		typeAnnotation = Type.BYTE;
		break;
	    case XSConstants.DATE_DT:
		typeAnnotation = Type.DATE;
		break;
	    case XSConstants.DATETIME_DT:
		typeAnnotation = Type.DATE_TIME;
		break;
	    case XSConstants.DECIMAL_DT:
		typeAnnotation = Type.DECIMAL;
		break;
	    case XSConstants.DOUBLE_DT:
		typeAnnotation = Type.DOUBLE;
		break;
	    case XSConstants.DURATION_DT:
		typeAnnotation = Type.DURATION;
		break;
	    case XSConstants.ENTITY_DT:
		typeAnnotation = Type.ENTITY;
		break;
	    case XSConstants.FLOAT_DT:
		typeAnnotation = Type.FLOAT;
		break;
	    case XSConstants.GDAY_DT:
		typeAnnotation = Type.G_DAY;
		break;
	    case XSConstants.GMONTH_DT:
		typeAnnotation = Type.G_MONTH;
		break;
	    case XSConstants.GMONTHDAY_DT:
		typeAnnotation = Type.G_MONTH_DAY;
		break;
	    case XSConstants.GYEAR_DT:
		typeAnnotation = Type.G_YEAR;
		break;
	    case XSConstants.GYEARMONTH_DT:
		typeAnnotation = Type.G_YEAR_MONTH;
		break;
	    case XSConstants.HEXBINARY_DT:
		typeAnnotation = Type.HEX_BINARY;
		break;
	    case XSConstants.ID_DT:
		typeAnnotation = Type.ID;
		break;
	    case XSConstants.IDREF_DT:
		typeAnnotation = Type.IDREF;
		break;
	    case XSConstants.INT_DT:
		typeAnnotation = Type.INT;
		break;
	    case XSConstants.INTEGER_DT:
		typeAnnotation = Type.INTEGER;
		break;
	    case XSConstants.NAME_DT:
		typeAnnotation = Type.NAME;
		break;
	    case XSConstants.NCNAME_DT:
		typeAnnotation = Type.NCNAME;
		break;
	    case XSConstants.NEGATIVEINTEGER_DT:
		typeAnnotation = Type.NEGATIVE_INTEGER;
		break;
	    case XSConstants.NMTOKEN_DT:
		typeAnnotation = Type.NMTOKEN;
		break;
	    case XSConstants.NONNEGATIVEINTEGER_DT:
		typeAnnotation = Type.NON_NEGATIVE_INTEGER;
		break;
	    case XSConstants.NONPOSITIVEINTEGER_DT:
		typeAnnotation = Type.NON_POSITIVE_INTEGER;
		break;
	    case XSConstants.NORMALIZEDSTRING_DT:
		typeAnnotation = Type.NORMALIZED_STRING;
		break;
	    case XSConstants.NOTATION_DT:
		typeAnnotation = Type.NOTATION;
		break;
	    case XSConstants.POSITIVEINTEGER_DT:
		typeAnnotation = Type.POSITIVE_INTEGER;
		break;
	    case XSConstants.QNAME_DT:
		typeAnnotation = Type.QNAME;
		break;
	    case XSConstants.SHORT_DT:
		typeAnnotation = Type.SHORT;
		break;
	    case XSConstants.STRING_DT:
		typeAnnotation = Type.STRING;
		break;
	    case XSConstants.TIME_DT:
		typeAnnotation = Type.TIME;
		break;
	    case XSConstants.TOKEN_DT:
		typeAnnotation = Type.TOKEN;
		break;
	    default:
		typeAnnotation = -1;
		break;
	    }
	    subIters[i] = getBaseTypeConstructor(context, typeAnnotation,
		    Context.getDictionary());
	    subIters[i].setLoc(loc);
	    subIters[i].setSubIters(new TokenIterator(context, values[i],
		    Type.STRING, loc));
	}
	SequenceIterator seqIter = new SequenceIterator(context, loc);
	seqIter.setSubIters(subIters);
	return seqIter;

    }

    // private void debugContentType(int contentType, int typeAnnotation) {
    // switch (contentType) {
    // case -1:
    // System.err.println("Type :" + Type.getTypeQName(typeAnnotation,
    // Context.getDictionary()));
    // break;
    // case TypeDictionary.SIMPLE:
    // System.err.println("Simple type :" + Type.getTypeQName(typeAnnotation,
    // Context.getDictionary()));
    // break;
    // case TypeDictionary.COMPLEX_ELEMENT_ONLY:
    // System.err.println("Complex type Element only content :" +
    // Type.getTypeQName(typeAnnotation, Context.getDictionary()));
    // break;
    // case TypeDictionary.COMPLEX_EMPTY:
    // System.err.println("Complex type Empty content :" +
    // Type.getTypeQName(typeAnnotation, Context.getDictionary()));
    // break;
    // case TypeDictionary.COMPLEX_MIXED:
    // System.err.println("Complex type Mixed content :" +
    // Type.getTypeQName(typeAnnotation, Context.getDictionary()));
    // break;
    // case TypeDictionary.COMPLEX_SIMPLE:
    // System.err.println("Complex type Simple content :" +
    // Type.getTypeQName(typeAnnotation, Context.getDictionary()));
    // break;

    // default:
    // System.err.println("Could not specify content type");
    // break;
    // }
    // }

    /*
     * private int findContentType(int typeAnnotation,TypeDictionary dict) { if
     * (Type.isUserDefinedType(typeAnnotation)){ //
     * System.err.println(Type.getTypeQName(typeAnnotation, dict));
     * typeAnnotation = typeAnnotation & Type.MASK_USER_DEFINED_TYPE_INDEX;
     * XSTypeDefinition typeDef = (XSTypeDefinition)
     * dict.lookUpByIntegerRepresentation(typeAnnotation); if
     * (typeDef.getTypeCategory() == XSTypeDefinition.COMPLEX_TYPE){
     * XSComplexTypeDefinition complexTypeDef = (XSComplexTypeDefinition)
     * typeDef; return complexTypeDef.getContentType(); } else return
     * COMPLEX_SIMPLE; } else if (Type.isAtomicType(typeAnnotation,dict)) return
     * SIMPLE; else return -1; }
     */
    protected void resetImpl() throws MXQueryException {
	super.resetImpl();
	evalComplete = false;
	current = null;
    }

    /**
     * This method returns a dataValuesIterator if the underlying iterator
     * doesn't promise to give back a typed value! Otherwise the underlying
     * iterator is given back
     * 
     * @param iter
     *                underlying iterator
     * @param ctx
     *                TODO
     * @return An iterator which returns a sequence of atomic values
     * @throws MXQueryException
     */
    public static XDMIterator getDataIterator(XDMIterator iter, Context ctx) {

	TypeInfo returnType = iter.getStaticType();

	// Attribute values are atomized to values of type xs:untypedAtomic
	// if ( Type.isAttribute(returnType) )
	// returnType = Type.getAttributeValueType(returnType);

	if (iter instanceof EmptySequenceIterator
		|| Type.isAtomicType(returnType.getType(), Context
			.getDictionary())) {
	    return iter;
	} else {
	    XDMIterator ret = new DataValuesIterator(ctx, iter, iter.getLoc());
	    return ret;
	}
    }

    public void setFnData(boolean fnData) {
	this.fnData = fnData;
    }

    // reused to build for CLDC without using SchemaParser
    private XDMIterator getBaseTypeConstructor(Context ctx, int typeFootprint,
	    TypeDictionary dict) throws MXQueryException {
	XSConstructorIterator it;
	if (Type.isTypeOrSubTypeOf(typeFootprint, Type.YEAR_MONTH_DURATION,
		dict)) {
	    it = new XSYearMonthDuration();
	} else if (Type.isTypeOrSubTypeOf(typeFootprint,
		Type.DAY_TIME_DURATION, dict)) {
	    it = new XSDayTimeDuration();
	} else if (Type.isTypeOrSubTypeOf(typeFootprint, Type.INTEGER, dict)) {
	    it = new XSInteger();
	} else if (Type.isTypeOrSubTypeOf(typeFootprint, Type.FLOAT, dict)) {
	    it = new XSFloat();
	} else if (Type.isTypeOrSubTypeOf(typeFootprint, Type.DOUBLE, dict)) {
	    it = new XSDouble();
	} else if (Type.isTypeOrSubTypeOf(typeFootprint, Type.DECIMAL, dict)) {
	    it = new XSDecimal();
	} else if (Type.isTypeOrSubTypeOf(typeFootprint, Type.DURATION, dict)) {
	    it = new XSDuration();
	} else if (Type.isTypeOrSubTypeOf(typeFootprint, Type.G_DAY, dict)
		|| Type.isTypeOrSubTypeOf(typeFootprint, Type.G_MONTH, dict)
		|| Type
			.isTypeOrSubTypeOf(typeFootprint, Type.G_MONTH_DAY,
				dict)
		|| Type.isTypeOrSubTypeOf(typeFootprint, Type.G_YEAR, dict)
		|| Type.isTypeOrSubTypeOf(typeFootprint, Type.G_YEAR_MONTH,
			dict)) {
	    it = new XSGregorian();
	} else if (Type.isTypeOrSubTypeOf(typeFootprint, Type.QNAME, dict)
		|| Type.isTypeOrSubTypeOf(typeFootprint, Type.NOTATION, dict)) {
	    it = new XSQName();
	} else if (Type.isTypeOrSubTypeOf(typeFootprint, Type.TIME, dict)) {
	    it = new XSTime();
	} else if (Type.isTypeOrSubTypeOf(typeFootprint, Type.BASE64_BINARY,
		dict)) {
	    it = new XSBinary();
	} else if (Type.isTypeOrSubTypeOf(typeFootprint, Type.BOOLEAN, dict)) {
	    it = new XSBoolean();
	    return it;
	} else if (Type.isTypeOrSubTypeOf(typeFootprint, Type.DATE, dict)) {
	    it = new XSDate();
	} else if (Type.isTypeOrSubTypeOf(typeFootprint, Type.DATE_TIME, dict)) {
	    it = new XSDateTime();
	} else if (Type.isTypeOrSubTypeOf(typeFootprint, Type.DATE, dict)) {
	    it = new XSDate();
	} else {
	    it = new XSString();
	}
	it.setTargetType(typeFootprint);
	it.setContext(ctx, false);
	return it;
    }

    public TypeInfo getStaticType() {
	return new TypeInfo(Type.ANY_ATOMIC_TYPE,
		Type.OCCURRENCE_IND_ZERO_OR_MORE, null, null);
    }

    protected XDMIterator copy(Context context, XDMIterator[] subIters,
	    Vector nestedPredCtxStack) throws MXQueryException {
	XDMIterator copy = new DataValuesIterator(subIters[0], loc);
	copy.setContext(context, false);
	return copy;
    }
}

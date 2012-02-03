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

package ch.ethz.mxquery.datamodel.xdm;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.Identifier;
import ch.ethz.mxquery.datamodel.MXQueryBinary;
import ch.ethz.mxquery.datamodel.MXQueryDate;
import ch.ethz.mxquery.datamodel.MXQueryDateTime;
import ch.ethz.mxquery.datamodel.MXQueryDayTimeDuration;
import ch.ethz.mxquery.datamodel.MXQueryDouble;
import ch.ethz.mxquery.datamodel.MXQueryDuration;
import ch.ethz.mxquery.datamodel.MXQueryFloat;
import ch.ethz.mxquery.datamodel.MXQueryGregorian;
import ch.ethz.mxquery.datamodel.MXQueryNumber;
import ch.ethz.mxquery.datamodel.MXQueryTime;
import ch.ethz.mxquery.datamodel.MXQueryYearMonthDuration;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;

public class Token {
    protected int eventType;

    protected int typeAnnotation;

    protected Identifier id;

    protected String schemaNormalizedValue;

    public final static Token END_SEQUENCE_TOKEN = new Token(Type.END_SEQUENCE,
	    null, null);

    public final static Token START_SEQUENCE_TOKEN = new Token(
	    Type.START_SEQUENCE, null, null);

    public final static int MASK_GET_START_TAG = Integer.parseInt(
	    "0001110000000000000000000000000", 2);
    public final static int MASK_CLEAN_START_TAG = Integer.parseInt(
	    "1110001111111111111111111111111", 2);

    protected XDMScope dynamicScope;

    public Token(int eventType, Identifier id, XDMScope dynScope) {
	this.eventType = eventType;
	this.id = id;
	this.dynamicScope = dynScope;
    }

    /**
     * Copy Constructor
     * 
     * @param token
     */
    public Token(Token token) {
	this.eventType = token.getEventType();
	this.id = token.getId();
	this.dynamicScope = token.dynamicScope;
    }

    public boolean isAttribute() {
	return Type.isAttribute(this.eventType);
    }

    public String getSchemaNormalizedValue() {
	return schemaNormalizedValue;
    }

    public void setSchemaNormalizedValue(String schemaNormalizedValue) {
	this.schemaNormalizedValue = schemaNormalizedValue;
    }

    public void setEventType(int eventType) {
	this.eventType = eventType;
    }

    public int getTypeAnnotation() {

	int type = this.eventType;
	if (type == -1)
	    return Type.UNTYPED;
	type = type & MASK_CLEAN_START_TAG;
	if (type == 0)
	    return Type.UNTYPED;

	else
	    return type;
    }

    public QName getQNameTokenValue() {
	return null;
    }

    public void setNS(String name) {
    }

    public String getName() {
	return null;
    }

    public String getNS() {
	return null;
    }

    public String getLocal() {
	return null;
    }

    public void setId(Identifier id) {
	this.id = id;
    }

    public Identifier getId() {
	return this.id;
    }

    public int getEventType() {

	// System.out.println("itan"+Type.getTypeQName(this.eventType));
	int type = this.eventType;
	type = type & MASK_GET_START_TAG;

	if (type == Type.START_TAG) {
	    return Type.START_TAG;
	} else {
	    return this.eventType;
	}
    }

    public boolean getBoolean() {
	return false;
    }

    public MXQueryDate getDate() {
	return null;
    }

    public MXQueryTime getTime() {
	return null;
    }

    public MXQueryDateTime getDateTime() {
	return null;
    }

    public MXQueryDuration getDuration() {
	return null;
    }

    public MXQueryYearMonthDuration getYearMonthDur() {
	return null;
    }

    public MXQueryDayTimeDuration getDayTimeDur() {
	return null;
    }

    public MXQueryDouble getDouble() {
	return null;
    }

    public MXQueryFloat getFloat() {
	return null;
    }

    public MXQueryGregorian getGregorian() {
	return null;
    }

    public MXQueryBinary getBinary() {
	return null;
    }

    public MXQueryNumber getNumber() {
	return null;
    }

    // FIXME throw Exception ???
    public long getLong() {
	return 0;
    }

    public String getText() {
	return null;
    }

    public String getValueAsString() {
	return null;
    }

    public Token toAttrToken(QName name, XDMScope scope)
	    throws MXQueryException {
	return null;
    }

    public void setDynamicScope(XDMScope ns) {
	dynamicScope = ns;
    }

    public XDMScope getDynamicScope() {
	return dynamicScope;
    }

    public static int compare(Token t0, Token t1) {
	try {
	    return compare(t0, t1, t0.getEventType(), t1.getEventType());
	} catch (MXQueryException me) {
	    throw new RuntimeException(me.getMessage());
	}
    }

    /*
     * (non-Javadoc)
     * 
     * @see java.lang.Object#hashCode()
     */
    public int hashCode() {
	return toString().hashCode();
    }

    public boolean equals(Object o) {
	if (o instanceof Token) {
	    try {
		return equals((Token) o);
	    } catch (MXQueryException me) {
		return false;
	    }
	}

	return false;
    }

    public boolean equals(Token o) throws MXQueryException {
	boolean status = false;
	int type1, type2;
	while ((type1 = getEventType()) != Type.END_SEQUENCE
		&& (type2 = o.getEventType()) != Type.END_SEQUENCE) {

	    // if ( Type.isAttribute(type1) ) {
	    // type1 = Type.UNTYPED_ATOMIC;
	    // }
	    //
	    // if ( Type.isAttribute(type2) ) {
	    // type2 = Type.UNTYPED_ATOMIC;
	    // }

	    if (Type.areComparable(type1, type2)) {
		if (this.compareTo(o) == 0)
		    status = true;
		else
		    status = false;
	    }

	    if (!status) {
		return false;
	    }
	    // next();
	    // o.next();
	}

	return status;
    }

    public int compareTo(Token o) throws MXQueryException {
	int type1 = getEventType();
	int type2 = o.getEventType();
	// System.out.println(Type.getTypeQName(type1,Context.getDictionary()));
	if (Type.isTextNode(type1))
	    type1 = Type.getTextNodeValueType(type1);
	if (Type.isTextNode(type2))
	    type2 = Type.getTextNodeValueType(type2);

	return compare(this, o, type1, type2);
    }

    public static int compare(Token comp1, Token comp2, int type1, int type2)
	    throws MXQueryException {
	/* handle all subtypes of xs:integer / xs:string uniformly */
	if (!Type.isTypeOrSubTypeOf(type1, Type.NOTATION, Context
		.getDictionary()))
	    type1 = Type
		    .getEventTypeSubstituted(type1, Context.getDictionary());
	if (!Type.isTypeOrSubTypeOf(type2, Type.NOTATION, Context
		.getDictionary()))
	    type2 = Type
		    .getEventTypeSubstituted(type2, Context.getDictionary());

	if (Type.areComparable(type1, type2)) {

	    if (type1 == type2) {
		switch (type1) {
		case Type.STRING:
		case Type.UNTYPED_ATOMIC:
		case Type.UNTYPED:
		case Type.ANY_URI:
		    // return (getText().length() - o.getText().length());
		    // String ordering should follow collation rules, not string
		    // length
		    // For now, we approximate this by the regular
		    // String.compareTo
		    // method
		    // Only the long run, we should switch to Collator.compareTo
		    // (overhead in CPU/memory?)
		    int res = comp1.getValueAsString().compareTo(
			    comp2.getValueAsString());
		    if (res > 1)
			res = 1;
		    if (res < -1)
			res = -1;
		    return res;
		case Type.QNAME:
		    QName q1 = ((QNameToken) comp1).getQNameTokenValue();
		    QName q2 = ((QNameToken) comp2).getQNameTokenValue();
		    return q1.compareTo(q2);
		case Type.INTEGER: {
		    long c1 = comp1.getLong();
		    long c2 = comp2.getLong();
		    if (c1 < c2)
			return -1;
		    else if (c1 == c2)
			return 0;
		    else
			return 1;
		}
		case Type.DOUBLE:
		case Type.FLOAT:
		case Type.DECIMAL:
		    return comp1.getDouble().compareTo(comp2.getDouble());
		case Type.BOOLEAN:
		    return (comp1.getBoolean() == comp2.getBoolean() ? 0
			    : (comp1.getBoolean() ? 1 : -1));
		case Type.DATE_TIME:
		    return comp1.getDateTime().compareTo(comp2.getDateTime());
		case Type.DATE:
		    return comp1.getDate().compareTo(comp2.getDate());
		case Type.TIME:
		    return comp1.getTime().compareTo(comp2.getTime());
		case Type.DAY_TIME_DURATION:
		    return comp1.getDayTimeDur().compareTo(
			    comp2.getDayTimeDur());
		case Type.YEAR_MONTH_DURATION:
		    return comp1.getYearMonthDur().compareTo(
			    comp2.getYearMonthDur());
		case Type.DURATION:
		    return comp1.getDuration().compareTo(comp2.getDuration());

		case Type.G_DAY:
		case Type.G_MONTH:
		case Type.G_YEAR:
		case Type.G_MONTH_DAY:
		case Type.G_YEAR_MONTH:
		    return comp1.getGregorian().compareTo(comp2.getGregorian());

		case Type.HEX_BINARY:
		case Type.BASE64_BINARY:
		    return comp1.getBinary().compareTo(comp2.getBinary());

		}// switch

	    } else {
		// integer & double/float/decimal comparison
		if (type1 == Type.INTEGER || type2 == Type.INTEGER)
		    return (new MXQueryDouble(comp1.getValueAsString())
			    .compareTo(new MXQueryDouble(comp2
				    .getValueAsString())));

		// double/float/decimal & double/float/decimal comparison
		if (type1 == Type.DOUBLE || type1 == Type.FLOAT
			|| type1 == Type.DECIMAL) {
		    int compVal = comp1.getNumber()
			    .compareTo(comp2.getNumber());
		    // if (compVal == 0 && type1 != type2)
		    // return 3;
		    return compVal;
		}
		// untypedAtomic & text
		if (type1 == Type.STRING || type1 == Type.UNTYPED_ATOMIC
			|| type1 == Type.ANY_URI)
		    return comp1.getValueAsString().compareTo(
			    comp2.getValueAsString());
		// all duration types
		if ((type1 == Type.DURATION || type1 == Type.DAY_TIME_DURATION || type1 == Type.YEAR_MONTH_DURATION)
			&& (type2 == Type.DURATION
				|| type2 == Type.DAY_TIME_DURATION || type2 == Type.YEAR_MONTH_DURATION)) {
		    // TODO: "normalize" comparison - use duration on the left,
		    // if no duration present, use day_time_duration on the left
		    // result don't need to be corrected, since only equality
		    // test possible
		    if (type2 == Type.DURATION) {
			int typeSave = type1;
			type1 = type2;
			type2 = typeSave;
			Token tokSav = comp1;
			comp1 = comp2;
			comp2 = tokSav;
		    } else if (type2 == Type.DAY_TIME_DURATION
			    && !(type1 == Type.DURATION)) {
			int typeSave = type1;
			type1 = type2;
			type2 = typeSave;
			Token tokSav = comp1;
			comp1 = comp2;
			comp2 = tokSav;
		    }

		    if (type1 == Type.DURATION) {
			if (type2 == Type.DAY_TIME_DURATION)
			    return comp1.getDuration().compareTo(
				    comp2.getDayTimeDur());
			else
			    return comp1.getDuration().compareTo(
				    comp2.getYearMonthDur());

		    }
		    if (type1 == Type.DAY_TIME_DURATION) {
			return comp1.getDayTimeDur().compareTo(
				comp2.getYearMonthDur());
		    }

		}

	    }
	}

	throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,
		"Types " + Type.getTypeQName(type1, Context.getDictionary())
			+ " and type "
			+ Type.getTypeQName(type2, Context.getDictionary())
			+ " can not be compared", null);
    }

    public Token copy() {
	return new Token(this);
    }
}

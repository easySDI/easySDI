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

package ch.ethz.mxquery.iterators;

import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.MXQueryBigDecimal;
import ch.ethz.mxquery.datamodel.MXQueryDayTimeDuration;
import ch.ethz.mxquery.datamodel.MXQueryDouble;
import ch.ethz.mxquery.datamodel.MXQueryDuration;
import ch.ethz.mxquery.datamodel.MXQueryNumber;
import ch.ethz.mxquery.datamodel.MXQueryYearMonthDuration;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.DayTimeDurToken;
import ch.ethz.mxquery.datamodel.xdm.DecimalToken;
import ch.ethz.mxquery.datamodel.xdm.DoubleToken;
import ch.ethz.mxquery.datamodel.xdm.DurationToken;
import ch.ethz.mxquery.datamodel.xdm.FloatToken;
import ch.ethz.mxquery.datamodel.xdm.LongToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.datamodel.xdm.YearMonthDurToken;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.Iterator;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.util.KXmlSerializer;


/**
 * 
 * @author Matthias Braun
 * 
 * This class represents the Multiplicative Expression of XQuery.
 * 
 * For further description see <a href="http://www.w3.org/TR/xquery/">http://www.w3.org/TR/xquery</a>
 */
public class MultiplicativeIterator extends TokenBasedIterator {
	private static final int MULTIPLY = 1;
	private static final int DIV = 2;
	private static final int IDIV = 3;
	private static final int MOD = 4;
	
	private int getConnector(String c) throws MXQueryException {
		if (c.equals("*")) {
			return MULTIPLY;
		} else if (c.equals("div")) {
			return DIV;
		} else if (c.equals("idiv")) {
			return IDIV;
		} else if (c.equals("mod")) {
			return MOD;
		} else {
			throw new DynamicException(ErrorCodes.A0002_EC_NOT_SUPPORTED, "Connector unknown in Multiplicative Iterator!", loc);
		}
	}
	
	private String[] connectors = null;
	
	/**
	 * Constructor for the MultiplicativeIterator
	 * 
	 * @param c			Array of connectors (* | idiv | div | mod)
	 * @param subIters	Array of Expressions, which are connected with the connectors in c
	 * @throws MXQueryException
	 */
	public MultiplicativeIterator(Context ctx, String[] c, XDMIterator[] subIters, QueryLocation location) throws MXQueryException {
		super(ctx, subIters, c.length+1, location);
		connectors = c;
	}
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		return new MultiplicativeIterator(context, Iterator.copyStrings(connectors), subIters,loc);
	}
	
	public TypeInfo getStaticType() {
		return new TypeInfo(Type.ANY_ATOMIC_TYPE,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
	}
	
	private void evaluateDayTimeDurationWithNumber(int[] types, Object[] values, String conn, int index) throws MXQueryException {
		if (getConnector(conn) != MULTIPLY && index == 1) {
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "DayTimeDuration cannot be divisor!", loc);
		}
		
		int second = index==0?1:0;
		MXQueryDayTimeDuration firstOperand = (MXQueryDayTimeDuration) values[index];
		MXQueryNumber secondOperand = (MXQueryNumber) values[second];
		
		if (getConnector(conn) != DIV && (secondOperand.isNegativeInfinity() || secondOperand.isPositiveInfinity())) {
			throw new TypeException(ErrorCodes.F0019_OVERFLOW_UNDERFLOW_DURATION, "+/-INF not allowed in combination with DayTimeDuration!", loc);
		} else if (secondOperand.toString().equals(MXQueryDouble.VALUE_NAN)) {
			throw new TypeException(ErrorCodes.F0007_NAN, "NaN not allowed in combination with DayTimeDuration!", loc);
		}
		
		types[0] = types[index];
		
		switch(getConnector(conn)) {
			case MULTIPLY:
				values[0] = firstOperand.multiply(secondOperand.getDoubleValue().getValue());
				break;
			case DIV:
				values[0] = firstOperand.divide(secondOperand.getDoubleValue().getValue());
				break;
			case IDIV:
			case MOD:
				throw new DynamicException(ErrorCodes.A0002_EC_NOT_SUPPORTED, "Connectors IDIV and MOD with MXQueryDayTimeDuration not supported in Multiplicative Iterator!", loc);
		}
	}
	
	private void evaluateDayTimeDurations(int[] types, Object[] values, String conn) throws MXQueryException {
		MXQueryDayTimeDuration firstOperand = (MXQueryDayTimeDuration) values[0];
		MXQueryDayTimeDuration secondOperand = (MXQueryDayTimeDuration) values[1];
		
		types[0] = Type.getNumericalOpResultType(types[0], types[1]);
		switch(getConnector(conn)) {
			case MULTIPLY:
				throw new DynamicException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Connector MULTIPLY between MXQueryDayTimeDuration and MXQueryDayTimeDuration not supported in Multiplicative Iterator!", loc);
			case DIV:
				values[0] = firstOperand.divide(secondOperand);
				types[0] = Type.DECIMAL;
				break;
			case IDIV:
			case MOD:
				throw new DynamicException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Connectors IDIV and MOD with MXQueryDayTimeDuration not supported in Multiplicative Iterator!", loc);
		}
	}
	
	private void evaluateNumbers(int[] types, Object[] values, String conn) throws MXQueryException {
		MXQueryNumber firstOperand = (MXQueryNumber) values[0];
		MXQueryNumber secondOperand = (MXQueryNumber) values[1];

		types[0] = Type.getNumericalOpResultType(types[0], types[1]);
		switch(getConnector(conn)) {
			case MULTIPLY:
				values[0] = firstOperand.multiply(secondOperand);
				break;
			case DIV:
				values[0] = firstOperand.divide(secondOperand);
				
				if (((MXQueryNumber) values[0]).toString().indexOf(".") >= 0 && types[0]!=Type.DOUBLE && types[0]!= Type.FLOAT) {
					types[0] = Type.DECIMAL;
				}
				break;
			case IDIV:
				if (Type.isTypeOrSubTypeOf(types[0], Type.INTEGER, null) && Type.isTypeOrSubTypeOf(types[1], Type.INTEGER, null)) {
					if (secondOperand.getLongValue() == 0) {
						throw new DynamicException(ErrorCodes.F0002_DIVISION_BY_ZERO,"Division by 0 for xs:integer type is not allowed", loc);
					}
					values[0] = new MXQueryBigDecimal(firstOperand.getLongValue() / secondOperand.getLongValue());
				} else {
					values[0] = new MXQueryDouble(firstOperand.idiv(secondOperand));
				}
				
				types[0] = Type.INTEGER;
				break;
			case MOD:
				if (Type.isTypeOrSubTypeOf(types[0], Type.DECIMAL, null) && Type.isTypeOrSubTypeOf(types[1], Type.DECIMAL, null)) {
					if (secondOperand.getLongValue() == 0) {
						throw new DynamicException(ErrorCodes.F0002_DIVISION_BY_ZERO,"Division by 0 for xs:decimal type is not allowed", loc);
					}
				}
				values[0] = firstOperand.mod(secondOperand);
				
				if (((MXQueryNumber) values[0]).toString().indexOf(".")>=0) {
					types[0] = Type.DOUBLE;
				}
				break;
		}
	}
	
	private void evaluateYearMonthDurationWithNumber(int[] types, Object[] values, String conn, int index) throws MXQueryException {
		if (getConnector(conn) != MULTIPLY && index == 1) {
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "YearMonthDuration cannot be divisor!", loc);
		}
		
		int second = index==0?1:0;
		MXQueryYearMonthDuration firstOperand = (MXQueryYearMonthDuration) values[index];
		MXQueryNumber secondOperand = (MXQueryNumber) values[second];

		if (getConnector(conn) != DIV && (secondOperand.isNegativeInfinity() || secondOperand.isPositiveInfinity())) {
			throw new TypeException(ErrorCodes.F0019_OVERFLOW_UNDERFLOW_DURATION, "+/-INF not allowed in combination with YearMonthDuration!", loc);
		} else if (secondOperand.toString().equals(MXQueryDouble.VALUE_NAN)) {
			throw new TypeException(ErrorCodes.F0007_NAN, "NaN not allowed in combination with YearMonthDuration!", loc);
		}
		
		types[0] = types[index];
		
		switch(getConnector(conn)) {
			case MULTIPLY:
				values[0] = firstOperand.multiply(secondOperand.getDoubleValue());
				break;
			case DIV:
				values[0] = firstOperand.divide(secondOperand.getDoubleValue());
				break;
			case IDIV:
			case MOD:
				throw new DynamicException(ErrorCodes.A0002_EC_NOT_SUPPORTED, "Connectors IDIV and MOD with MXQueryYearMonthDuration not supported in Multiplicative Iterator!", loc);
		}
	}
	
	private void evaluateYearMonthDurations(int[] types, Object[] values, String conn) throws MXQueryException {
		MXQueryYearMonthDuration firstOperand = (MXQueryYearMonthDuration) values[0];
		MXQueryYearMonthDuration secondOperand = (MXQueryYearMonthDuration) values[1];
		
		types[0] = Type.getNumericalOpResultType(types[0], types[1]);
		switch(getConnector(conn)) {
			case MULTIPLY:
				throw new DynamicException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Connector MULTIPLY between MXQueryYearMonthDuration and MXQueryYearMonthDuration not supported in Multiplicative Iterator!", loc);
			case DIV:
				values[0] = firstOperand.divide(secondOperand);
				types[0] = Type.DECIMAL;
				break;
			case IDIV:
			case MOD:
				throw new DynamicException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Connectors IDIV and MOD with MXQueryYearMonthDuration not supported in Multiplicative Iterator!", loc);
		}
	}
	
	private Token generateToken(int type, Object value) throws MXQueryException {
		switch (type) {
		case (Type.END_SEQUENCE):		
			return Token.END_SEQUENCE_TOKEN;
		case (Type.INTEGER):			
			return new LongToken(
					type, 
					null, 
					((MXQueryNumber)value).getLongValue());
		case (Type.UNTYPED_ATOMIC):
		case Type.UNTYPED:
			return new DoubleToken(
					null, 
					new MXQueryDouble(value.toString())); 
		case (Type.DOUBLE):
			return new DoubleToken(
					null, 
					((MXQueryNumber)value).getDoubleValue());			
		case (Type.FLOAT):
			return new FloatToken(
					null, 
					((MXQueryNumber)value).getFloatValue());
		case (Type.DECIMAL):
			MXQueryBigDecimal d = null;
			
			if (value instanceof MXQueryDouble) {
				d = new MXQueryBigDecimal((MXQueryDouble) value);
			} else {
				d = new MXQueryBigDecimal(((MXQueryNumber) value).toString());
			}
			return new DecimalToken(null, d);
		case (Type.DURATION):
			return new DurationToken(null, (MXQueryDuration) value);
		case (Type.DAY_TIME_DURATION):
			return new DayTimeDurToken(null, (MXQueryDayTimeDuration) value);
		case (Type.YEAR_MONTH_DURATION):
			return new YearMonthDurToken(null, (MXQueryYearMonthDuration) value);
		default:
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, 
				"Type " + Type.getTypeQName(type, Context.getDictionary()) + " in MultiplicativeIterator is not allowed.", loc);
		}
	}
	
	private void generateValue(Token t, int[] types, Object[] values, int index) throws MXQueryException {
		types[index] = Type.getEventTypeSubstituted(t.getEventType(), Context.getDictionary());
		
		switch (types[index]) {
			case (Type.END_SEQUENCE):		
				currentToken = Token.END_SEQUENCE_TOKEN;
				return;
			case (Type.INTEGER):			
				values[index] = new MXQueryBigDecimal(t.getLong()); // MXQueryBigDecimal
				break;
			case (Type.UNTYPED_ATOMIC):
			case Type.UNTYPED:
				types[index] = Type.getNumericalOpResultType(types[index], Type.DOUBLE);
				values[index] = new MXQueryDouble(t.getValueAsString()); // MXQueryDouble
				break;		
			case (Type.DOUBLE):
			case (Type.FLOAT):
			case (Type.DECIMAL):
				values[index] = t.getNumber(); // MXQueryNumber
				break;
			case (Type.DURATION):
				values[index] = t.getDuration(); // MXQueryDuration
				break;
			case (Type.DAY_TIME_DURATION):
				values[index] = t.getDayTimeDur(); // MXQueryDayTimeDuration
				break;
			case (Type.YEAR_MONTH_DURATION):
				values[index] = t.getYearMonthDur(); // MXQueryYearMonthDuration
				break;
			default:
				throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, 
					"Type " + Type.getTypeQName(types[index], Context.getDictionary()) + " in MultiplicativeIterator is not allowed.", loc);
		}
	}
	
	private void operate(int[] types, Object[] values, String conn) throws MXQueryException {
		if (types[0] == types[1]) {
			operateEqualTypes(types, values, conn);
		} else {
			operateDifferentTypes(types, values, conn);			
		}
	}
	
	private void operateDifferentTypes(int[] types, Object[] values, String conn) throws MXQueryException {
		if (Type.isNumericPrimitiveType(types[0]) && Type.isNumericPrimitiveType(types[1])) {
			evaluateNumbers(types, values, conn);
			return;
		} else {
			int first;
			if ((first = typeCheck(types, Type.DAY_TIME_DURATION, Type.NUMBER)) > -1) {
				evaluateDayTimeDurationWithNumber(types, values, conn, first);
				return;
			} else if ((first = typeCheck(types, Type.YEAR_MONTH_DURATION, Type.NUMBER)) > -1) {
				evaluateYearMonthDurationWithNumber(types, values, conn, first);
				return;
			}
			
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, 
					"Types " + Type.getTypeQName(types[0], Context.getDictionary()) + " and " + Type.getTypeQName(types[1], Context.getDictionary()) + " in MultiplicativeIterator is not allowed.", loc);
		}
	}
	
	private void operateEqualTypes(int[] types, Object[] values, String conn) throws MXQueryException {
		switch(types[0]) {		
			case Type.INTEGER: 
			case Type.DOUBLE:
			case Type.FLOAT: 			
			case Type.DECIMAL:
				evaluateNumbers(types, values, conn);
				break;
			case Type.DURATION:
				throw new DynamicException(ErrorCodes.A0002_EC_NOT_SUPPORTED,"Duration Type not yet supported in Multiplicative Iterator", loc);
			case Type.DAY_TIME_DURATION:
				evaluateDayTimeDurations(types, values, conn);
				break;
			case Type.YEAR_MONTH_DURATION:
				evaluateYearMonthDurations(types, values, conn);
				break;
			default:
				throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, 
						"Type " + Type.getTypeQName(types[0], Context.getDictionary()) + " in MultiplicativeIterator is not allowed.", loc);
		}
	}

	private int typeCheck(int[] types, int t1, int t2) {
		if (Type.isTypeOrSubTypeOf(types[0], t1, null)) {
			if (Type.isTypeOrSubTypeOf(types[1], t2, null)) {
				return 0;
			}
		} else if (Type.isTypeOrSubTypeOf(types[1], t1, null)) {
			if (Type.isTypeOrSubTypeOf(types[0], t2, null)) {
				return 1;
			}
		}
		return -1;
	}
	
	protected KXmlSerializer createIteratorStartTag(KXmlSerializer serializer) throws Exception {
		super.createIteratorStartTag(serializer);
		for(int i =0; i < connectors.length;i++){
			serializer.attribute(null, "connector_" + i, connectors[i]);
		}
		return serializer;
	}
	
	protected void init() throws MXQueryException {
		int types[] = new int[2]; 
		Object values[] = new Object[2];
		
		// set value of first operand
		generateValue(subIters[0].next(), types, values, 0);
		if (types[0] == Type.END_SEQUENCE) {
			currentToken = Token.END_SEQUENCE_TOKEN;
			return;
		}
		if (subIters[0].next() != Token.END_SEQUENCE_TOKEN)
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Expected single value, got multiple",loc);
		
		// evaluate two operands
		for (int i = 1; i < subIters.length; i++) {
			generateValue(subIters[i].next(), types, values, 1);
			if (types[1] == Type.END_SEQUENCE) {
				currentToken = Token.END_SEQUENCE_TOKEN;
				return;
			}
			if (subIters[i].next() != Token.END_SEQUENCE_TOKEN)
				throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Expected single value, got multiple",loc);
			operate(types, values, connectors[i-1]);
		}
		
		currentToken = generateToken(types[0], values[0]);	
	}
}

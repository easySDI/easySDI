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
import ch.ethz.mxquery.datamodel.MXQueryDate;
import ch.ethz.mxquery.datamodel.MXQueryDateTime;
import ch.ethz.mxquery.datamodel.MXQueryDayTimeDuration;
import ch.ethz.mxquery.datamodel.MXQueryDouble;
import ch.ethz.mxquery.datamodel.MXQueryFloat;
import ch.ethz.mxquery.datamodel.MXQueryNumber;
import ch.ethz.mxquery.datamodel.MXQueryTime;
import ch.ethz.mxquery.datamodel.MXQueryYearMonthDuration;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.DateTimeToken;
import ch.ethz.mxquery.datamodel.xdm.DateToken;
import ch.ethz.mxquery.datamodel.xdm.DayTimeDurToken;
import ch.ethz.mxquery.datamodel.xdm.DecimalToken;
import ch.ethz.mxquery.datamodel.xdm.DoubleToken;
import ch.ethz.mxquery.datamodel.xdm.FloatToken;
import ch.ethz.mxquery.datamodel.xdm.LongToken;
import ch.ethz.mxquery.datamodel.xdm.TimeToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.datamodel.xdm.YearMonthDurToken;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.Iterator;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.util.KXmlSerializer;
import ch.ethz.mxquery.util.Utils;

/**
 * 
 * @author Matthias Braun
 * 
 * This class represents the Additive Expression of XQuery.
 * 
 * For further description see <a
 * href="http://www.w3.org/TR/xquery/">http://www.w3.org/TR/xquery</a>
 */
public class AdditiveIterator extends TokenBasedIterator {
	/*************
	 * variables *
	 *************/
	private String[] connectors = null;

	/***********
	 * methods *
	 ***********/

	/**
	 * Constructor for the AdditiveIterator
	 * 
	 * @param c
	 *            Array of connectors (+ | -)
	 * @param subIters
	 *            Array of Expressions, which are connected with the connectors
	 *            in c
	 * @throws MXQueryException
	 */
	public AdditiveIterator(Context ctx, String[] c, XDMIterator[] subIters, QueryLocation location) throws MXQueryException {
		super(ctx, subIters,c.length+1,location);
		connectors = c;
	}
	
	public void setParam(String name, String value) {
		String[] con = Utils.split(name,"_");
		int i = Integer.parseInt(con[1]);
		if(connectors == null) {
			connectors = new String[i+1];
		}
		connectors[i] = value;
	}
	
	/**
	 * Implemented operations:
	 *  DATE_TIME - DATE_TIME -> DAY_TIME_DURATION
	 *  DATE_TIME +- DAY_TIME_DURATION -> DATE_TIME
	 *  DATE_TIME +- YEAR_MONTH_DURATION -> DATE_TIME
	 *
	 *  DAY_TIME_DURATION +- DAY_TIME_DURATION  -> DAY_TIME_DURATION  
	 *  YEAR_MONTH_DURATION +- YEAR_MONTH_DURATION -> YEAR_MONTH_DURATION
	 *  
	 *  DATE - DATE -> DAY_TIME_DURATION
	 *  TIME - TIME -> DAY_TIME_DURATION
	 *  
	 *  DATE +- DAY_TIME_DURATION -> DATE
	 *  DATE +- YEAR_MONTH_DURATION -> DATE
	 *  
	 *  TIME+- DAY_TIME_DURATION -> TIME
	 *  
	 * @throws MXQueryException
	 */
	private void evaluateDateTime(Token inToken) throws MXQueryException {
		
		MXQueryDateTime resDateTime = null;
		MXQueryDate 	resDate = null;
		MXQueryTime		resTime = null;
		
		MXQueryDayTimeDuration resDTduration = null;
		MXQueryYearMonthDuration resYMduration = null;
		
		int resType = inToken.getEventType(); 
		
		switch (resType) {
			case Type.DATE_TIME :
				resDateTime = inToken.getDateTime();
				break;	
			case Type.DAY_TIME_DURATION:
				resDTduration = inToken.getDayTimeDur();
				break;				
			case Type.YEAR_MONTH_DURATION:
				resYMduration = inToken.getYearMonthDur();
				break;	
			case Type.DATE:
				resDate = inToken.getDate();
				break;
			case Type.TIME:
				resTime = inToken.getTime();
				break;
		}		
		
		for (int i = 1; i < subIters.length; i++) {
			int plus = connectors[i - 1].equals("+") ? 1 : -1;
			Token inToken2 = subIters[i].next(); 
			int type = inToken2.getEventType();
			
			switch(type){
			
			case (Type.END_SEQUENCE):{			
				currentToken = Token.END_SEQUENCE_TOKEN;
				return;
			}
			
			case (Type.DATE_TIME):{
				switch(resType) {
					case Type.DATE_TIME: {
						if (plus == -1) {
							resDTduration = resDateTime.subtractDateTime(inToken2.getDateTime());
							resType = Type.DAY_TIME_DURATION;
						}
						else 
							throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, 
									"Addition of two operands of type "+Type.getTypeQName(type, Context.getDictionary())+" in AdditiveIterator is not allowed.", loc); 
					break;	
					}
					case Type.DAY_TIME_DURATION: {
						if (plus == -1) 
							throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, 
									"Subtraction "+Type.getTypeQName(resType, Context.getDictionary())+" - "+Type.getTypeQName(type, Context.getDictionary())+" in AdditiveIterator is not allowed.", loc); 
						else resDateTime = inToken2.getDateTime().addDuration(resDTduration);	
						resType = Type.DATE_TIME;					
					break;
					}
					case Type.YEAR_MONTH_DURATION: {
						if (plus == -1)
							throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, 
									"Subtraction "+Type.getTypeQName(resType, Context.getDictionary())+" - "+Type.getTypeQName(type, Context.getDictionary())+" in AdditiveIterator is not allowed.", loc); 
						else 
							resDateTime = inToken2.getDateTime().addDuration(resYMduration);	
						resType = Type.DATE_TIME;
					break;	
					}
					default:
						throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, 
								"Operation between types "+Type.getTypeQName(type, Context.getDictionary())+ " and " +Type.getTypeQName(resType, Context.getDictionary())+ " in AdditiveIterator is not allowed.", loc); 
				}
			}break;
						
			case (Type.DAY_TIME_DURATION):{
				switch(resType) {
					case Type.DATE_TIME: {				
						if (plus == -1) resDateTime = resDateTime.subtractDuration(inToken2.getDayTimeDur());
						else resDateTime = resDateTime.addDuration(inToken2.getDayTimeDur());
						resType = Type.DATE_TIME;
					break;	
					}
					case Type.DAY_TIME_DURATION: {
						if (plus == -1) resDTduration = resDTduration.subtract(inToken2.getDayTimeDur());
						else resDTduration = resDTduration.add(inToken2.getDayTimeDur());
						resType = Type.DAY_TIME_DURATION;
					break;	
					}
					case Type.DATE: {				
						if (plus == -1) resDate = resDate.subtractDuration(inToken2.getDayTimeDur());
						else resDate = resDate.addDuration(inToken2.getDayTimeDur());
						resType = Type.DATE;
					break;	
					}
					case Type.TIME: {				
						if (plus == -1) resTime = resTime.subtractDuration(inToken2.getDayTimeDur());
						else resTime = resTime.addDuration(inToken2.getDayTimeDur());
						resType = Type.TIME;
					break;	
					}
					default:
						throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, 
								"Operation between types "+Type.getTypeQName(type, Context.getDictionary())+ " and " +Type.getTypeQName(resType, Context.getDictionary())+ " in AdditiveIterator is not allowed.", loc); 
				}	
			}break;
			
			case (Type.YEAR_MONTH_DURATION):{
				switch(resType) {
					case Type.DATE_TIME: {				
						if (plus == -1) resDateTime = resDateTime.subtractDuration(inToken2.getYearMonthDur());
						else resDateTime = resDateTime.addDuration(inToken2.getYearMonthDur());
						resType = Type.DATE_TIME;
					break;	
					}
					case Type.YEAR_MONTH_DURATION: {
						if (plus == -1) resYMduration = resYMduration.subtract(inToken2.getYearMonthDur());
						else resYMduration = resYMduration.add(inToken2.getYearMonthDur());
						resType = Type.YEAR_MONTH_DURATION;
					break;	
					}
					case Type.DATE: {				
						if (plus == -1) resDate = resDate.subtractDuration(inToken2.getYearMonthDur());
						else resDate = resDate.addDuration(inToken2.getYearMonthDur());
						resType = Type.DATE;
					break;	
					}
					default:
						throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, 
								"Operation between types "+Type.getTypeQName(type, Context.getDictionary())+ " and " +Type.getTypeQName(resType, Context.getDictionary())+ " in AdditiveIterator is not allowed.", loc); 
				}	
			}break;			
			
			case (Type.DATE):{
				switch(resType) {
					case Type.DATE: {
						if (plus == -1) {
							resDTduration = resDate.subtract(inToken2.getDate());
							resType = Type.DAY_TIME_DURATION;
						}
						else 
							throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, 
									"Addition of two operands of type "+Type.getTypeQName(type, Context.getDictionary())+" in AdditiveIterator is not allowed.", loc); 
					break;	
					}
					case Type.DAY_TIME_DURATION: {
						if (plus == -1) 
							throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, 
									"Subtraction "+Type.getTypeQName(resType, Context.getDictionary())+" - "+Type.getTypeQName(type, Context.getDictionary())+" in AdditiveIterator is not allowed.", loc);
						else 
							resDate = inToken2.getDate().addDuration(resDTduration);	
						resType = Type.DATE;
					break;
					}
					case Type.YEAR_MONTH_DURATION: {
						if (plus == -1) 
							throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, 
									"Subtraction "+Type.getTypeQName(resType, Context.getDictionary())+" - "+Type.getTypeQName(type, Context.getDictionary())+" in AdditiveIterator is not allowed.", loc);
						else 
							resDate = inToken2.getDate().addDuration(resYMduration);	
						resType = Type.DATE;
					break;	
					}
					default:
						throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, 
								"Operation between types "+Type.getTypeQName(type, Context.getDictionary())+ " and " +Type.getTypeQName(resType, Context.getDictionary())+ " in AdditiveIterator is not allowed.", loc); 
				}
			}break;			
			
			case (Type.TIME):{
				switch(resType) {
					case Type.TIME: {
						if (plus == -1) {
							resDTduration = resTime.subtract(inToken2.getTime());
							resType = Type.DAY_TIME_DURATION;
						}
						else
							throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, 
									"Addition of two operands of type "+Type.getTypeQName(type, Context.getDictionary())+" in AdditiveIterator is not allowed.", loc); 
					break;	
					}
					case Type.DAY_TIME_DURATION: {
						if (plus == -1) 
							throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, 
									"Subtraction "+Type.getTypeQName(resType, Context.getDictionary())+" - "+Type.getTypeQName(type, Context.getDictionary())+" in AdditiveIterator is not allowed.", loc);
						else 
							resTime = inToken2.getTime().addDuration(resDTduration);	
						resType = Type.TIME;					
					break;
					}
					default:
						throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, 
								"Operation between types "+Type.getTypeQName(type, Context.getDictionary())+ " and " +Type.getTypeQName(resType, Context.getDictionary())+ " in AdditiveIterator is not allowed.", loc); 
				}
			}break;			
			
			default:
				throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, 
						"Operation between types "+Type.getTypeQName(type, Context.getDictionary())+ " and " +Type.getTypeQName(resType, Context.getDictionary())+ " in AdditiveIterator is not allowed.", loc); 
			}//switch
			
		}//for
		
		switch (resType) {
			case Type.DATE_TIME :
				currentToken =  new DateTimeToken(null, resDateTime);
				break;	
			case Type.DAY_TIME_DURATION:
				currentToken = new DayTimeDurToken(null, resDTduration);
				break;				
			case Type.YEAR_MONTH_DURATION:
				currentToken = new YearMonthDurToken(null, resYMduration);
				break;	
			case Type.DATE:
				currentToken =  new DateToken(null, resDate);
				break;
			case Type.TIME:
				currentToken =  new TimeToken(null, resTime);
			break;
		}
		
	} 
	
	
	private void evaluateNum(long pLong, MXQueryNumber pDouble, int resultType) throws MXQueryException {
		
		long resLong = pLong;
		MXQueryNumber argDouble;
		MXQueryNumber resDouble = pDouble;
		if (resDouble == null) resDouble = new MXQueryBigDecimal(0);
		
		for (int i = 1; i < subIters.length; i++) {
			int plus = connectors[i - 1].equals("+") ? 1 : -1;
			Token input2 = subIters[i].next(); 
			int type = Type.getEventTypeSubstituted(input2.getEventType(), Context.getDictionary());

			switch(type){				
			case (Type.END_SEQUENCE):{			
				currentToken = Token.END_SEQUENCE_TOKEN;
				return;
			}
			case (Type.UNTYPED_ATOMIC): 
			case Type.UNTYPED:
			{				
				resultType = Type.getNumericalOpResultType(resultType, Type.DOUBLE);
				argDouble = new MXQueryDouble (input2.getValueAsString()).multiply(plus);
				resDouble =	resDouble.add( argDouble );
			}break;
			
			case Type.INTEGER:{
				resLong += plus * (input2.getLong());
			}break;
			
			case (Type.DOUBLE):
				resultType = Type.getNumericalOpResultType(resultType, type);
				argDouble = input2.getNumber().multiply(plus);
				resDouble =	argDouble.add(resDouble);
			break;
			case (Type.FLOAT):
			case (Type.DECIMAL): 
			{				
				resultType = Type.getNumericalOpResultType(resultType, type);
				argDouble = input2.getNumber().multiply(plus);
				resDouble =	resDouble.add( argDouble );
			}break;
			default:
				throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, 
						"Type " + Type.getTypeQName(type, Context.getDictionary()) + " in AdditiveIterator is not allowed.", loc); 
			}//switch
			
		}//for

		switch (resultType) {
			case Type.INTEGER: 
				currentToken = new LongToken(resultType, null, resLong);
				break;
			case Type.DOUBLE:
				currentToken = new DoubleToken(null, (MXQueryDouble)resDouble.add(resLong));
				break;
			case Type.FLOAT:
				currentToken = new FloatToken(null, (MXQueryFloat)resDouble.add(resLong));
				break;
			case Type.DECIMAL:
				currentToken = new DecimalToken(null, (MXQueryBigDecimal)resDouble.add(resLong));				
			break;
		}
		
	} 
	
	protected KXmlSerializer createIteratorStartTag(KXmlSerializer serializer) throws Exception {
		super.createIteratorStartTag(serializer);
		for(int i =0; i < connectors.length;i++){
			serializer.attribute(null, "connector_" + i, connectors[i]);
		}
		return serializer;
	}

	protected void init() throws MXQueryException {
		Token inputToken = subIters[0].next(); 
		int type = Type.getEventTypeSubstituted(inputToken.getEventType(), Context.getDictionary());
		
		// optimization possible
//		if ( Type.isAttribute(type) )
//			type = Type.getAttributeValueType(type);
				
		switch(type){
		case (Type.END_SEQUENCE):{			
			currentToken = Token.END_SEQUENCE_TOKEN;
			break;
		}
		case (Type.UNTYPED_ATOMIC):
		case Type.UNTYPED:
		{
			evaluateNum(0, new MXQueryDouble (inputToken.getValueAsString() ), Type.DOUBLE);
			break;
		}		
		case Type.INTEGER:
			evaluateNum(inputToken.getLong(), null, type);
			break;
		case (Type.DOUBLE):
		case (Type.FLOAT):
		case (Type.DECIMAL):
		{
			evaluateNum(0, inputToken.getNumber(), type);
			break;
		}
		case (Type.DATE_TIME):
		case (Type.DATE):
		case (Type.TIME):
		case (Type.DAY_TIME_DURATION):
		case (Type.YEAR_MONTH_DURATION):
		{	
			evaluateDateTime(inputToken);
			break;
		}
		default:
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, 
					"Type " + Type.getTypeQName(type, Context.getDictionary()) + " in AdditiveIterator  is not allowed.", loc); 
		}//switch
		
		// Per Specification, summands of an addition mustn't not be sequences with more then one element
		for (int i = 0; i < this.subIters.length; i++) {
			if (type != Type.END_SEQUENCE && this.subIters[i].next() != Token.END_SEQUENCE_TOKEN) {
				throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, 
						"An operand of an addition must not be a sequence with greater than one!", loc);
			}
		}
		
		this.close(false);
	}

	/* (non-Javadoc)
	 * @see ch.ethz.mxquery.model.iterators.Iterator#getReturnType()
	 */
	public TypeInfo getStaticType() {
		// TODO: can be made more precise if the input data types are known
		return new TypeInfo(Type.ANY_ATOMIC_TYPE,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
	}
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		return new AdditiveIterator(context, Iterator.copyStrings(connectors), subIters,loc);
	}
}

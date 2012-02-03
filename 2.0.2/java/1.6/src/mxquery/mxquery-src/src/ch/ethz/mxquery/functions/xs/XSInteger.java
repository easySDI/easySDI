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
package ch.ethz.mxquery.functions.xs;

import java.util.Vector;

import org.apache.xerces.impl.dv.xs.IntegerDV;
import org.apache.xerces.xs.XSObjectList;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.LongToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.functions.fn.DataValuesIterator;
import ch.ethz.mxquery.model.XDMIterator;

public class XSInteger extends XSConstructorIterator {
	private int targetType = -1;	


	/**
	 * sets the result type of the constructor function
	 * @param targetType xs:integer or it's subtype
	 */
	public void setTargetType(int targetType) {
		this.targetType = targetType; 
	}

	
	protected void init() throws MXQueryException {
		if (subIters[0] == null) {
			throw new IllegalArgumentException();
		}
		XDMIterator input = subIters[0];
		if (input instanceof DataValuesIterator)((DataValuesIterator)input).setFnData(true);
		Token inputToken = input.next(); 
	//	System.out.println("casting "+Type.getTypeQName(inputToken.getTypeAnnotation(),Context.getDictionary())+" to "+Type.getTypeQName(targetType, Context.getDictionary()));
		int type = Type.getEventTypeSubstituted( inputToken.getEventType(), Context.getDictionary() );
		XSObjectList facetsList = getFacetsList();
		switch (type) {
		case Type.END_SEQUENCE:
				currentToken = Token.END_SEQUENCE_TOKEN;
		break;
		case Type.INTEGER:
			try {
			if ( inputToken.getEventType() == targetType )
				currentToken = inputToken;	
			else {
				currentToken = new LongToken(targetType, null, inputToken.getLong());
				if ((facetsList != null) && facetsList.getLength() >0)
					checkFacets(new IntegerDV(),currentToken.getValueAsString());
			}
			} catch (TypeException te) {
				if (te.getErrorCode().equals(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE))
					throw new TypeException(ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR,"Could not cast type " + Type.getTypeQName(type, Context.getDictionary()) + " to type " + Type.getTypeQName(targetType, Context.getDictionary()) + " with value " + inputToken.getLong(), loc );
				else throw te;
			}
		break;
		case Type.BOOLEAN:
			try {
			if (inputToken.getBoolean() ) 
				currentToken = new LongToken(targetType, null,1);
			else 
				currentToken = new LongToken(targetType, null,0);
			if ((facetsList != null) && facetsList.getLength() >0)
				checkFacets(new IntegerDV(),currentToken.getValueAsString());
			} catch (TypeException te) {
				if (te.getErrorCode().equals(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE))
					throw new TypeException(ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR,"Could not cast type " + Type.getTypeQName(type, Context.getDictionary()) + " to type " + Type.getTypeQName(targetType, Context.getDictionary()) + " with value " + inputToken.getBoolean(), loc );
				else throw te;
			}
		break;
		case Type.DECIMAL:
		case Type.DOUBLE:
		case Type.FLOAT:
			try {
			currentToken = new LongToken(targetType, null, inputToken.getDouble().getLongValue());
			if ((facetsList != null) && facetsList.getLength() >0)
				checkFacets(new IntegerDV(),currentToken.getValueAsString());
			} catch (TypeException te) {
				if (te.getErrorCode().equals(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE))
					throw new TypeException(ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR,"Could not cast type " + Type.getTypeQName(type, Context.getDictionary()) + " to type " + Type.getTypeQName(targetType, Context.getDictionary()) + " with value " + inputToken.getValueAsString(), loc );
				else throw te;
			}
		break;	
		case Type.UNTYPED_ATOMIC :
		case Type.UNTYPED:
		case Type.STRING :
			String dataVal = inputToken.getValueAsString();
			
			if (facetsList != null && facetsList.getLength() > 0) 
				dataVal = applyWhitespaceFacet(dataVal);
			try {
				dataVal = dataVal.trim();
				if (dataVal.length() > 1 && dataVal.charAt(0) == '+')
					dataVal = dataVal.substring(1);
				long longVal = Long.parseLong(dataVal);
				currentToken = new LongToken(targetType,null,longVal);
				if ((facetsList != null) && facetsList.getLength() >0)
					checkFacets(new IntegerDV(),currentToken.getValueAsString());
			} catch (NumberFormatException nl) {
				if (type == Type.UNTYPED) {
					try {
						// for values containing a dot, just parse the part before
						int dotPos = dataVal.indexOf('.');
						// only do this if the node has not untyped atomic and there is no exponent (e.g, 1.0E5)
						if (dotPos >= 0 && type != Type.UNTYPED_ATOMIC && dataVal.indexOf('E') < 0) {
							String dataVal1 = dataVal.substring(0, dotPos);
							long longVal = Long.parseLong(dataVal1);
							currentToken = new LongToken(targetType,null,longVal);
							if ((facetsList != null) && facetsList.getLength() >0)
								checkFacets(new IntegerDV(),currentToken.getValueAsString());
						}
						else
							throw new TypeException(ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR,"Could not cast type " + Type.getTypeQName(type, Context.getDictionary()) + " to type " + Type.getTypeQName(targetType, Context.getDictionary()) + " with value " + dataVal, loc );

					} catch (NumberFormatException nl1) { 
						TypeException te = new TypeException(ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR,"Could not cast type " + Type.getTypeQName(type, Context.getDictionary()) + " to type " + Type.getTypeQName(targetType, Context.getDictionary()) + " with value " + dataVal, loc );
						throw te;
					}
				}
				else
					throw new TypeException(ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR,"Could not cast type " + Type.getTypeQName(type, Context.getDictionary()) + " to type " + Type.getTypeQName(targetType, Context.getDictionary()) + " with value " + dataVal, loc );

			} catch (TypeException te) {
				if (te.getErrorCode().equals(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE))
					throw new TypeException(ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR,"Could not cast type " + Type.getTypeQName(type, Context.getDictionary()) + " to type " + Type.getTypeQName(targetType, Context.getDictionary()) + " with value " + dataVal, loc );
				else 
					throw te;
			}

		break;
		default:
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Could not cast type " + Type.getTypeQName(type, Context.getDictionary()) + " to type " + Type.getTypeQName(targetType, Context.getDictionary()), loc);
		}
		
		if ( input.next() != Token.END_SEQUENCE_TOKEN )
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Could not cast sequence to atomic type " + Type.getTypeQName(targetType, Context.getDictionary()), loc);
	}
	public TypeInfo getStaticType() {
		return new TypeInfo(targetType,Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
	}	
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XSInteger copy = new XSInteger();
		copy.setTargetType(targetType);
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		copy.setFacetsList(getFacetsList());
		copy.setMfacetsList(getMfacetsList());
		return copy;
	}
}

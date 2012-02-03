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

import org.apache.xerces.impl.dv.xs.Base64BinaryDV;
import org.apache.xerces.impl.dv.xs.HexBinaryDV;
import org.apache.xerces.xs.XSObjectList;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.MXQueryBinary;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.BinaryToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.functions.fn.DataValuesIterator;
import ch.ethz.mxquery.model.XDMIterator;

public class XSBinary extends XSConstructorIterator {
	
	private int targetType = -1;
	
	/**
	 * sets the result type of the constructor function
	 * @param targetType one of the binary types (e.g. hexBinary) 
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

		int inputType = Type.getEventTypeSubstituted(inputToken.getEventType(), Context.getDictionary());
		XSObjectList facetsList = getFacetsList();
		MXQueryBinary bVal;
		switch( inputType ){
			case Type.END_SEQUENCE:
				currentToken = Token.END_SEQUENCE_TOKEN;
			break;	
			case Type.HEX_BINARY:
				
				if ( targetType == inputType)
					currentToken = inputToken;
				else {
					bVal = inputToken.getBinary();
					bVal.setType(Type.BASE64_BINARY);
					currentToken = new BinaryToken(null, bVal,null);
					if ((facetsList != null) && facetsList.getLength() >0)
						checkFacets(new Base64BinaryDV(),currentToken.getValueAsString());
				}
			break;
			case Type.BASE64_BINARY:
				
				if ( targetType == inputType)
					currentToken = inputToken;
				else {
					bVal = inputToken.getBinary();
					bVal.setType(Type.HEX_BINARY);
					currentToken = new BinaryToken(null, bVal,null);
					if ((facetsList != null) && facetsList.getLength() >0)
						checkFacets(new HexBinaryDV(),currentToken.getValueAsString());
				}
			break;	
			case Type.UNTYPED_ATOMIC :
			case Type.UNTYPED:
			case Type.STRING :
				String dataVal = inputToken.getValueAsString();
				
				if (facetsList != null && facetsList.getLength() > 0) 
					dataVal = applyWhitespaceFacet(dataVal);
				bVal = new MXQueryBinary(dataVal, targetType); 
				currentToken = new BinaryToken(null, bVal,null);
				if ((facetsList != null) && facetsList.getLength() >0)
					checkFacets(new Base64BinaryDV(),currentToken.getValueAsString());
				
			break;
			default:
				throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Could not cast type " + Type.getTypeQName(inputType, Context.getDictionary()) + " to type " + Type.getTypeQName(targetType, Context.getDictionary()), loc);
		}
		
		if ( input.next() != Token.END_SEQUENCE_TOKEN )
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,"Could not cast sequence to atomic type " + Type.getTypeQName(targetType, Context.getDictionary()), loc);
	}	

	public TypeInfo getStaticType() {
		return new TypeInfo(targetType,Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
	}		
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XSBinary copy = new XSBinary();
		copy.setTargetType(targetType);
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		copy.setFacetsList(getFacetsList());
		copy.setMfacetsList(getMfacetsList());
		return copy;
	}
}

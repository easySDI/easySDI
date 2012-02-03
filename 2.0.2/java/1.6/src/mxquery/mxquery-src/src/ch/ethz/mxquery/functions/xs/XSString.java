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

import org.apache.xerces.impl.dv.xs.StringDV;
import org.apache.xerces.xs.XSObjectList;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.AnyURIToken;
import ch.ethz.mxquery.datamodel.xdm.TextToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.datamodel.xdm.UntypedAtomicToken;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.functions.fn.DataValuesIterator;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.util.Utils;

public class XSString extends XSConstructorIterator {

//	private int resultType = -1;
	
	/**
	 * sets the result type of the constructor function
	 * @param type xs:string, xs:untypedAtomic, xs:anyURI or one of the xs:string subtypes 
	 */
	/*public void setTargetType(int targetType) {
		this.resultType = targetType; 
	}	
	
*/	protected void init() throws MXQueryException {
		if (subIters[0] == null) {
			throw new IllegalArgumentException();
		}
		XDMIterator input = subIters[0];
		if (input instanceof DataValuesIterator)((DataValuesIterator)input).setFnData(true);
		
		Token inputToken = input.next();
		int type = Type.getEventTypeSubstituted( resultType, Context.getDictionary() );
		
		
		if (  inputToken == Token.END_SEQUENCE_TOKEN)
				currentToken = Token.END_SEQUENCE_TOKEN;
		else {
			XSObjectList facetsList = getFacetsList();
			String tokenValue = inputToken.getValueAsString();
			
			if (Type.isTypeOrSubTypeOf(resultType, Type.TOKEN, Context.getDictionary())||
					Type.isTypeOrSubTypeOf(resultType, Type.ANY_URI, Context.getDictionary())) {
				tokenValue = Utils.normalizeString(tokenValue);
			}
			
			currentToken  = null;
			switch (type) {
			case Type.STRING:
				if (facetsList != null && facetsList.getLength() > 0) 
					tokenValue = applyWhitespaceFacet(tokenValue);
				currentToken=new TextToken(resultType, null, tokenValue,null);
			
				if ((facetsList != null) && facetsList.getLength() >0)
				checkFacets(new StringDV(),currentToken.getValueAsString());
				break;
			case Type.UNTYPED_ATOMIC:
				currentToken=new UntypedAtomicToken(null, tokenValue);
				if ((facetsList != null) && facetsList.getLength() >0)
					checkFacets(new StringDV(),currentToken.getValueAsString());
				break;
			case Type.ANY_URI:
				int t = inputToken.getEventType();
				if ( t == Type.ANY_URI || t == Type.UNTYPED_ATOMIC || 
						t == Type.UNTYPED ||t == Type.STRING ){
					//currentToken=new AnyURIToken(null, inputToken.getValueAsString());
					currentToken=new AnyURIToken(resultType,null,tokenValue,null);
					if ((facetsList != null) && facetsList.getLength() >0)
						checkFacets(new StringDV(),currentToken.getValueAsString());
				}
				else 
					throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Could not cast type " + Type.getTypeQName(t, Context.getDictionary()) + " to type " + Type.getTypeQName(resultType, Context.getDictionary()), loc);
				break;
			}
		}
		if ( input.next() != Token.END_SEQUENCE_TOKEN )
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,"Could not cast sequence to atomic type " + Type.getTypeQName(resultType, Context.getDictionary()), loc);
	}
	

	public TypeInfo getStaticType() {
		return new TypeInfo(resultType,Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
	}	
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XSString copy = new XSString();
		copy.setTargetType(resultType);
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		copy.setFacetsList(getFacetsList());
		copy.setMfacetsList(getMfacetsList());
		return copy;
	}
}

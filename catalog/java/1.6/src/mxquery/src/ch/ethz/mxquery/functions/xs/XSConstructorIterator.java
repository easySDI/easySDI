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

import org.apache.xerces.impl.dv.InvalidDatatypeValueException;
import org.apache.xerces.impl.dv.xs.TypeValidator;
import org.apache.xerces.impl.dv.xs.XSSimpleTypeDecl;
import org.apache.xerces.impl.xpath.regex.RegularExpression;
import org.apache.xerces.xs.StringList;
import org.apache.xerces.xs.XSFacet;
import org.apache.xerces.xs.XSMultiValueFacet;
import org.apache.xerces.xs.XSObject;
import org.apache.xerces.xs.XSObjectList;
import org.apache.xerces.xs.XSSimpleTypeDefinition;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.model.TokenBasedIterator;

public abstract class XSConstructorIterator extends TokenBasedIterator {
	private XSObjectList facetsList;
	private XSObjectList mFacetsList;
	private static final String errCode = ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR;
	
	protected int resultType = -1;
	
	/**
	 * sets the result type of the constructor function
	 * @param targetType xs:string, xs:untypedAtomic, xs:anyURI or one of the xs:string subtypes 
	 */
	public void setTargetType(int targetType) {
		this.resultType = targetType; 
	}	
	
	public void setFacetsList(XSObjectList facetsList) {
		this.facetsList = facetsList;
	}
	
	public XSObjectList getFacetsList() {
		return facetsList;
	}
	
public XSObjectList getMfacetsList() {
	return mFacetsList;
}

public void setMfacetsList(XSObjectList mfacetsList) {
	this.mFacetsList = mfacetsList;
}
	
	protected void checkFacets(TypeValidator val,String valueAsString) throws DynamicException {
		for (int i = 0; i < facetsList.getLength(); i++) {
			XSObject facet = facetsList.item(i);
			checkFacet(val,valueAsString,facet);
		}
		if (mFacetsList != null)
		for (int i = 0; i < mFacetsList.getLength(); i++) {
			XSObject facet = mFacetsList.item(i);
			checkFacet(val,valueAsString,facet);
		}
	}
	/**
	 * Normalize token value based on whitespace facet from XML Schema
	 * @param value
	 * @return a normalized string
	 */
	public String applyWhitespaceFacet(String value){
		XSFacet facet = (XSFacet) facetsList.item(0);
		String lexicalFacetValue = facet.getLexicalFacetValue();
		if (lexicalFacetValue.equals("preserve"))
			value = XSSimpleTypeDecl.normalize(value,XSSimpleTypeDecl.WS_PRESERVE);
			else if (lexicalFacetValue.equals("replace"))
			 value = 	XSSimpleTypeDecl.normalize(value,XSSimpleTypeDecl.WS_REPLACE);
			 else if (lexicalFacetValue.equals("collapse"))
				value=	XSSimpleTypeDecl.normalize(value,XSSimpleTypeDecl.WS_COLLAPSE);
			  return value;
	}
	
	/**
	 * Check if the facets of the target type are satisfied
	 * @param val
	 * @param value
	 * @param facetKind
	 * @param lexicalFacetValue
	 * @throws DynamicException
	 */
	private void checkFacet(TypeValidator val,String value,XSObject facet) throws DynamicException {
		short facetKind =-1;
		String lexicalFacetValue ="";
		if (facet instanceof XSFacet){
		facetKind = ((XSFacet) facet).getFacetKind();
		lexicalFacetValue = ((XSFacet)facet).getLexicalFacetValue();
		}
		else if (facet instanceof XSMultiValueFacet)
			facetKind = ((XSMultiValueFacet)facet).getFacetKind();
		
		switch (facetKind) {
		case XSSimpleTypeDefinition.FACET_ENUMERATION:
			boolean found = false;
			XSMultiValueFacet mfacet = (XSMultiValueFacet) facet;
			
			StringList list = mfacet.getLexicalFacetValues();
			
			for (int i = 0; i < list.getLength(); i++) {
				String member = list.item(i);
				//try {
				//	if (val.getActualValue(value, null).equals(val.getActualValue(member,null))){
					if (value.equals(member)){
						found = true;
						break;
					}
//				} catch (InvalidDatatypeValueException e) {
//					// TODO Auto-generated catch block
//					e.printStackTrace();
//				} 
			}
			if (!found) throw new DynamicException(errCode, "Invalid value '" + value +"' for type "+Type.getTypeQName(resultType,Context.getDictionary())+",Value not member of enumeration type",loc);
			break;
		case XSSimpleTypeDefinition.FACET_FRACTIONDIGITS:
			try {
					if (Integer.parseInt(String.valueOf(val.getFractionDigits(val.getActualValue(value, null)))) > Integer.parseInt(String.valueOf(val.getActualValue(lexicalFacetValue,null))))
						throw new DynamicException(errCode, "Invalid value '" + value +"' for type "+Type.getTypeQName(resultType,Context.getDictionary())+", Fraction digits  should be "+lexicalFacetValue,loc);
					
				} catch (InvalidDatatypeValueException e) {
					throw new DynamicException(errCode, "Invalid value '" + value +"' for type "+Type.getTypeQName(resultType,Context.getDictionary())+", Fraction digits  should be "+lexicalFacetValue,loc);				}
				break;
		case XSSimpleTypeDefinition.FACET_LENGTH:
			try {
				if (Integer.parseInt(String.valueOf(val.getDataLength(val.getActualValue(value, null)))) != Integer.parseInt(String.valueOf(val.getActualValue(lexicalFacetValue,null))))
					throw new DynamicException(errCode, "Invalid value '" + value +"' for type "+Type.getTypeQName(resultType,Context.getDictionary())+", Length  should be "+lexicalFacetValue,loc);
			} catch (InvalidDatatypeValueException e) {
				throw new DynamicException(errCode, "Invalid value '" + value +"' for type "+Type.getTypeQName(resultType,Context.getDictionary())+", Length  should be "+lexicalFacetValue,loc);
			}
			break;
		case XSSimpleTypeDefinition.FACET_MAXEXCLUSIVE:
			try {
				if (val.compare(val.getActualValue(value, null),val.getActualValue(lexicalFacetValue,null))== TypeValidator.GREATER_THAN ||
						val.compare(val.getActualValue(value, null),val.getActualValue(lexicalFacetValue,null))== TypeValidator.EQUAL)
					throw new DynamicException(errCode, "Invalid value '" + value +"' for type "+Type.getTypeQName(resultType,Context.getDictionary())+", Max value  should be "+lexicalFacetValue,loc);
			} catch (InvalidDatatypeValueException e) {
				throw new DynamicException(errCode, "Invalid value '" + value +"' for type "+Type.getTypeQName(resultType,Context.getDictionary())+", Max value  should be "+lexicalFacetValue,loc);
			}
			break;		
			case XSSimpleTypeDefinition.FACET_MAXINCLUSIVE:
				try {
					if (val.compare(val.getActualValue(value, null),val.getActualValue(lexicalFacetValue,null))== TypeValidator.GREATER_THAN)
						throw new DynamicException(errCode, "Invalid value '" + value +"' for type "+Type.getTypeQName(resultType,Context.getDictionary())+", Max value  should not be greater than "+lexicalFacetValue,loc);
					
				} catch (InvalidDatatypeValueException e) {
					throw new DynamicException(errCode, "Invalid value '" + value +"' for type "+Type.getTypeQName(resultType,Context.getDictionary())+", Max value  should not be greater than "+lexicalFacetValue,loc);
				}
				break;	
		case XSSimpleTypeDefinition.FACET_MAXLENGTH:
			try {
				
				if (Integer.parseInt(String.valueOf(val.getDataLength(val.getActualValue(value, null)))) > Integer.parseInt(String.valueOf(val.getActualValue(lexicalFacetValue,null))))
					throw new DynamicException(errCode, "Invalid value '" + value +"' for type "+Type.getTypeQName(resultType,Context.getDictionary())+", Max length  should be "+lexicalFacetValue,loc);
			} catch (InvalidDatatypeValueException e) {
				throw new DynamicException(errCode, "Invalid value '" + value +"' for type "+Type.getTypeQName(resultType,Context.getDictionary())+", Max length  should be "+lexicalFacetValue,loc);
			}
			break;
		case XSSimpleTypeDefinition.FACET_MINEXCLUSIVE:
			try {
				if (val.compare(val.getActualValue(value, null),val.getActualValue(lexicalFacetValue,null))== TypeValidator.LESS_THAN ||
						val.compare(val.getActualValue(value, null),val.getActualValue(lexicalFacetValue,null))== TypeValidator.EQUAL)
					throw new DynamicException(errCode, "Invalid value '" + value +"' for type "+Type.getTypeQName(resultType,Context.getDictionary())+", Min value  should be "+lexicalFacetValue,loc);
			} catch (InvalidDatatypeValueException e) {
				throw new DynamicException(errCode, "Invalid value '" + value +"' for type "+Type.getTypeQName(resultType,Context.getDictionary())+", Min value  should be "+lexicalFacetValue,loc);
			}
			break;
		case XSSimpleTypeDefinition.FACET_MININCLUSIVE:
			try {
				if (val.compare(val.getActualValue(value, null),val.getActualValue(lexicalFacetValue,null))== TypeValidator.LESS_THAN)
					throw new DynamicException(errCode, "Invalid value '" + value +"' for type "+Type.getTypeQName(resultType,Context.getDictionary())+", Min value  should  be greater than "+lexicalFacetValue,loc);
				
			} catch (InvalidDatatypeValueException e) {
				throw new DynamicException(errCode, "Invalid value '" + value +"' for type "+Type.getTypeQName(resultType,Context.getDictionary())+", Min value  should  be greater than "+lexicalFacetValue,loc);
			}
			break;	
		case XSSimpleTypeDefinition.FACET_MINLENGTH:
			try {
				if (Integer.parseInt(String.valueOf(val.getDataLength(val.getActualValue(value, null)))) < Integer.parseInt(String.valueOf(val.getActualValue(lexicalFacetValue,null))))
					throw new DynamicException(errCode, "Invalid value '" + value +"' for type "+Type.getTypeQName(resultType,Context.getDictionary())+", Min length  should  be "+lexicalFacetValue,loc);
			} catch (InvalidDatatypeValueException e) {
				throw new DynamicException(errCode, "Invalid value '" + value +"' for type "+Type.getTypeQName(resultType,Context.getDictionary())+", Min length  should  be "+lexicalFacetValue,loc);
			}
			break;
		case XSSimpleTypeDefinition.FACET_NONE:
		case XSSimpleTypeDefinition.FACET_WHITESPACE:
			break;
		case XSSimpleTypeDefinition.FACET_PATTERN:
			 mfacet = (XSMultiValueFacet) facet;
			list = mfacet.getLexicalFacetValues();
			
			for (int i = 0; i < list.getLength(); i++) {
				String expression = list.item(i);
				RegularExpression regExp = new RegularExpression(expression,"X");
				if (!regExp.matches(value))
					throw new DynamicException(errCode, "Invalid value '" + value +"' for type "+Type.getTypeQName(resultType,Context.getDictionary())+", It does not match pattern "+expression,loc);
			}
			break;
		case XSSimpleTypeDefinition.FACET_TOTALDIGITS:
		try{
			if (Integer.parseInt(String.valueOf(val.getTotalDigits(val.getActualValue(value, null)))) > Integer.parseInt(String.valueOf(val.getActualValue(lexicalFacetValue,null))))
				throw new DynamicException(errCode, "Invalid value '" + value +"' for type "+Type.getTypeQName(resultType,Context.getDictionary())+", Total digits  should be "+lexicalFacetValue,loc);
		} catch (InvalidDatatypeValueException e) {
			throw new DynamicException(errCode, "Invalid value '" + value +"' for type "+Type.getTypeQName(resultType,Context.getDictionary())+", Total digits  should be "+lexicalFacetValue,loc);
		}
		break;
		default:
			throw new DynamicException(errCode,"Invalid facet specified",loc);
		}
	}
	
}

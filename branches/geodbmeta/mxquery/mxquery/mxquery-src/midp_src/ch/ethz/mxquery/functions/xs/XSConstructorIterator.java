package ch.ethz.mxquery.functions.xs;

import org.apache.xerces.impl.dv.xs.TypeValidator;
import org.apache.xerces.xs.XSObjectList;


import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.model.Iterator;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.contextConfig.Context;

public abstract class XSConstructorIterator extends TokenBasedIterator {
	private XSObjectList facetsList;
	private XSObjectList MfacetsList;
	private static final String errCode = ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR;
	
	protected int resultType = -1;
	
	/**
	 * sets the result type of the constructor function
	 * @param type xs:string, xs:untypedAtomic, xs:anyURI or one of the xs:string subtypes 
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
	return MfacetsList;
}

public void setMfacetsList(XSObjectList mfacetsList) {
	this.MfacetsList = mfacetsList;
}
	
	protected void checkFacets(TypeValidator val,String valueAsString){
	}
	/**
	 * Normalize token value based on whitespace facet from XML Schema
	 * @param value
	 * @return
	 */
	public String applyWhitespaceFacet(String value){
			  return value;
	}
	
	
}

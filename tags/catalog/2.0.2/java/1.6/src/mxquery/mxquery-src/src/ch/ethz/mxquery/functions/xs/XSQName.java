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
import ch.ethz.mxquery.datamodel.Namespace;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.QNameToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.datamodel.xdm.XDMScope;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.functions.fn.DataValuesIterator;
import ch.ethz.mxquery.model.XDMIterator;

public class XSQName  extends XSConstructorIterator {

	private XDMScope nsScope;
	
	public void setNsScope(XDMScope nsScope) {
		this.nsScope = nsScope;
	}
	
	protected void init() throws MXQueryException {
		if (subIters[0] == null) {
			throw new IllegalArgumentException();
		}

		XDMIterator iter = subIters[0];
		if (iter instanceof DataValuesIterator)((DataValuesIterator)iter).setFnData(true);
		Token inputToken = iter.next(); 
		int type = Type.getEventTypeSubstituted( inputToken.getEventType(), Context.getDictionary() );
		XSObjectList facetsList = getFacetsList();
		switch (type) {
			case Type.END_SEQUENCE:
					currentToken = Token.END_SEQUENCE_TOKEN;
			break;
			case Type.STRING:
				String val =  inputToken.getText().trim();
				if (val.equals(""))
					throw new TypeException(ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR, "Invalid lexical value: " + val, loc);
				if (facetsList != null && facetsList.getLength() > 0) 
					val = applyWhitespaceFacet(val);
				QName res = new QName(val);
				try {
					if (nsScope != null) {
						String prefix = res.getNamespacePrefix(); 
						if (prefix !=null){
							Namespace ns = nsScope.getNamespace(prefix);
							if( ns == null) throw new MXQueryException(ErrorCodes.E0081_STATIC_QUERY_UNKNOWN_NAMESPACE,"",loc);
							else res = new QName(ns.getURI(),res.getNamespacePrefix(),res.getLocalPart());
						}		
					}
					else
					res = res.resolveQNameNamespace(context);
				} catch (MXQueryException me) {
					if (me.getErrorCode().endsWith(ErrorCodes.E0081_STATIC_QUERY_UNKNOWN_NAMESPACE)) 
						throw new DynamicException(ErrorCodes.F0021_NO_NAMESPACE_FOUND,"Namespace prefix not bound", loc);
				}
				currentToken = new QNameToken(null, res);
				if ((facetsList != null) && facetsList.getLength() >0)
					checkFacets(new StringDV(),currentToken.getValueAsString());
			break;
			case Type.QNAME:
				currentToken = new QNameToken(null, inputToken.getQNameTokenValue() );
				if ((facetsList != null) && facetsList.getLength() >0)
					checkFacets(new StringDV(),currentToken.getValueAsString());
			break;
			default:
				throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,"Could not cast type " + Type.getTypeQName(type, Context.getDictionary()) + " to type " + Type.getTypeQName(Type.QNAME, Context.getDictionary()), loc);
		}
		
		if ( iter.next() != Token.END_SEQUENCE_TOKEN )
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,"Could not cast sequence to atomic type " + Type.getTypeQName(Type.QNAME, Context.getDictionary()), loc);	
	}
	public TypeInfo getStaticType() {
		return new TypeInfo(Type.QNAME,Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
	}		
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XSQName copy = new XSQName();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		copy.setFacetsList(getFacetsList());
		copy.setMfacetsList(getMfacetsList());
		copy.setNsScope(this.nsScope);
		return copy;
	}
}
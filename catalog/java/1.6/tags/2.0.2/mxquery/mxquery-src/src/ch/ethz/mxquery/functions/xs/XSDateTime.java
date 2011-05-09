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

import org.apache.xerces.impl.dv.xs.DateTimeDV;
import org.apache.xerces.xs.XSObjectList;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.MXQueryDate;
import ch.ethz.mxquery.datamodel.MXQueryDateTime;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.DateTimeToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.functions.fn.DataValuesIterator;
import ch.ethz.mxquery.model.XDMIterator;

public class XSDateTime  extends XSConstructorIterator {

	protected void init() throws MXQueryException {
	if (subIters[0] == null) {
			throw new IllegalArgumentException();
		}
		
		XDMIterator iter = subIters[0];
		if (iter instanceof DataValuesIterator)((DataValuesIterator)iter).setFnData(true);
		Token inputToken = iter.next(); 
		int type = Type.getEventTypeSubstituted(inputToken.getEventType(), Context.getDictionary());
		XSObjectList facetsList = getFacetsList();
		switch (type) {
			case Type.END_SEQUENCE:
					currentToken = Token.END_SEQUENCE_TOKEN;
			break;
			case Type.STRING:
			case Type.UNTYPED:
			case Type.UNTYPED_ATOMIC:
				String dataVal = inputToken.getValueAsString();
				if (facetsList != null && facetsList.getLength() > 0) 
					dataVal = applyWhitespaceFacet(dataVal);
				MXQueryDateTime dateTime = new MXQueryDateTime(dataVal);
				currentToken = new DateTimeToken(null, dateTime);
				if ((facetsList != null) && facetsList.getLength() >0)
					checkFacets(new DateTimeDV(),currentToken.getValueAsString());
			break;
			case Type.DATE_TIME:
				currentToken = inputToken;
				if ((facetsList != null) && facetsList.getLength() >0)
					checkFacets(new DateTimeDV(),currentToken.getValueAsString());
			break;
			case Type.DATE:
				MXQueryDate date = inputToken.getDate();
				currentToken = new DateTimeToken(null, new MXQueryDateTime( date.datePartToString() + "T00:00:00" + date.getTimeZone()));
				if ((facetsList != null) && facetsList.getLength() >0)
					checkFacets(new DateTimeDV(),currentToken.getValueAsString());
			break;	
				
			default:
				throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,"Could not cast type " + Type.getTypeQName(type, Context.getDictionary()) + " to type " + Type.getTypeQName(Type.DATE_TIME, Context.getDictionary()), loc);
		}
		
		if ( iter.next() != Token.END_SEQUENCE_TOKEN ) {
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,"Could not cast sequence to atomic type " + Type.getTypeQName(Type.DATE_TIME, Context.getDictionary()), loc);
		}
	}
	public TypeInfo getStaticType() {
		return new TypeInfo(Type.DATE_TIME,Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
	}		
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XSDateTime copy = new XSDateTime();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		copy.setFacetsList(getFacetsList());
		copy.setMfacetsList(getMfacetsList());
		return copy;
	}
}
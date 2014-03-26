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

package ch.ethz.mxquery.functions.fn;

import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.QNameToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;

public class FNQName extends TokenBasedIterator {

    protected void init() throws MXQueryException {
	if (subIters[0] == null) {
	    throw new IllegalArgumentException();
	}
	XDMIterator paramQNameIt;
	Token nsToken = subIters[0].next();
	paramQNameIt = subIters[1];
	String nsString = null;
	if (!(nsToken.getText() == null || nsToken.getText().equals("")))
	    nsString = nsToken.getText();
	if (!(Type.getEventTypeSubstituted(nsToken.getEventType(), Context
		.getDictionary()) == Type.STRING
		|| nsToken.getEventType() == Type.UNTYPED_ATOMIC
		|| nsToken.getEventType() == Type.UNTYPED || nsToken
		.getEventType() == Type.END_SEQUENCE))
	    throw new DynamicException(
		    ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,
		    "Wrong data type for fn:QName", loc);
	Token paramToken = paramQNameIt.next();
	int type = Type.getEventTypeSubstituted(paramToken.getEventType(),
		Context.getDictionary());

	switch (type) {
	case Type.END_SEQUENCE:
	    currentToken = Token.END_SEQUENCE_TOKEN;
	    break;
	case Type.STRING:
	case Type.UNTYPED_ATOMIC:
	case Type.UNTYPED:
	    String qNameText = paramToken.getText();
	    if (qNameText.equals(""))
		throw new DynamicException(
			ErrorCodes.F0005_INVALID_LEXICAL_VALUE,
			"Empty QName not possible", loc);
	    QName qToGen = new QName(qNameText);
	    if (qToGen.getNamespacePrefix() != null
		    && (nsToken == null
			    || nsToken.getEventType() == Type.END_SEQUENCE || nsToken
			    .getText().equals("")))
		throw new DynamicException(
			ErrorCodes.F0005_INVALID_LEXICAL_VALUE,
			"Prefix not allow with empty namespace", loc);
	    qToGen.setNamespaceURI(nsString);
	    currentToken = new QNameToken(null, qToGen);
	    break;
	default:
	    throw new DynamicException(
		    ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,
		    "Wrong data type for fn:QName", loc);
	}

	if (paramQNameIt.next() != Token.END_SEQUENCE_TOKEN)
	    throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,
		    "Could not cast sequence to atomic type "
			    + Type.getTypeQName(Type.QNAME, Context
				    .getDictionary()), loc);
    }

    public TypeInfo getStaticType() {
	return new TypeInfo(Type.QNAME, Type.OCCURRENCE_IND_EXACTLY_ONE, null,
		null);
    }

    protected XDMIterator copy(Context context, XDMIterator[] subIters,
	    Vector nestedPredCtxStack) throws MXQueryException {
	XDMIterator copy = new FNQName();
	copy.setContext(context, true);
	copy.setSubIters(subIters);
	return copy;
    }
}
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

import ch.ethz.mxquery.bindings.WindowFactory;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.IdentifierFactory;
import ch.ethz.mxquery.datamodel.MXQueryBigDecimal;
import ch.ethz.mxquery.datamodel.MXQueryBinary;
import ch.ethz.mxquery.datamodel.MXQueryDate;
import ch.ethz.mxquery.datamodel.MXQueryDateTime;
import ch.ethz.mxquery.datamodel.MXQueryDayTimeDuration;
import ch.ethz.mxquery.datamodel.MXQueryDouble;
import ch.ethz.mxquery.datamodel.MXQueryDuration;
import ch.ethz.mxquery.datamodel.MXQueryFloat;
import ch.ethz.mxquery.datamodel.MXQueryGregorian;
import ch.ethz.mxquery.datamodel.MXQueryNumber;
import ch.ethz.mxquery.datamodel.MXQueryTime;
import ch.ethz.mxquery.datamodel.MXQueryYearMonthDuration;
import ch.ethz.mxquery.datamodel.Namespace;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.Source;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.AnyURIToken;
import ch.ethz.mxquery.datamodel.xdm.BinaryToken;
import ch.ethz.mxquery.datamodel.xdm.BooleanToken;
import ch.ethz.mxquery.datamodel.xdm.DateTimeToken;
import ch.ethz.mxquery.datamodel.xdm.DateToken;
import ch.ethz.mxquery.datamodel.xdm.DayTimeDurToken;
import ch.ethz.mxquery.datamodel.xdm.DecimalToken;
import ch.ethz.mxquery.datamodel.xdm.DoubleToken;
import ch.ethz.mxquery.datamodel.xdm.DurationToken;
import ch.ethz.mxquery.datamodel.xdm.FloatToken;
import ch.ethz.mxquery.datamodel.xdm.GregorianToken;
import ch.ethz.mxquery.datamodel.xdm.LongToken;
import ch.ethz.mxquery.datamodel.xdm.QNameToken;
import ch.ethz.mxquery.datamodel.xdm.TextToken;
import ch.ethz.mxquery.datamodel.xdm.TimeToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.datamodel.xdm.UntypedAtomicToken;
import ch.ethz.mxquery.datamodel.xdm.YearMonthDurToken;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.exceptions.StaticException;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.util.KXmlSerializer;

/**
 * 
 * represents a single Token.
 * Replaces the old AtomicIterator!
 * 
 * 
 */
public class TokenIterator extends TokenBasedIterator implements Source{
	private Token myToken;
	private final static String uri = "http://www.mxquery.org/nodeconstruction/token";
	static int docs = 0;
	private String docId = uri+docs++;
	//private boolean enc = false;
	
	protected void init() {
		currentToken = myToken;
	};
	
	public TokenIterator(Context ctx, Token tok, QueryLocation location) throws MXQueryException{
		super(ctx, location);
		setResettable(true);
		myToken = tok;
		if (tok.getId() == null)
			tok.setId(IdentifierFactory.createIdentifier(1, this, null, (short)0));
	}
	
	
	public TokenIterator(Context ctx, Token tok, Namespace xmlns, QueryLocation location) throws MXQueryException{
		// TODO: handle the xmlns (never implemented)
		super(ctx, location);
		setResettable(true);
		myToken = tok;
		if (tok.getId() == null)
			tok.setId(IdentifierFactory.createIdentifier(1, this, null, (short)0));
	}
	
	public TokenIterator(Context ctx, QName value, QueryLocation location) throws MXQueryException{
		super(ctx, location);
		setResettable(true);
		myToken = new QNameToken(null, value);
	}
	
	public TokenIterator(Context ctx, MXQueryGregorian value, QueryLocation location) throws MXQueryException{
		super(ctx, location);
		setResettable(true);
		myToken = new GregorianToken(null, value);
	}	
	
	public TokenIterator(Context ctx, MXQueryBinary value, QueryLocation location) throws MXQueryException {
		super(ctx,location);
		setResettable(true);
		myToken = new BinaryToken(null, value,null);
	}		
	
	public TokenIterator(Context ctx, long value, int type, QueryLocation location) throws MXQueryException {
		super(ctx, location);
		setResettable(true);
		myToken = new LongToken(type, null, value);
	}

	public void setParam(String name, String value) throws MXQueryException {
		
		//name.toUpperCase();
		int type = Type.DOUBLE;
		
		switch (type) {		
		case Type.DOUBLE: {
			MXQueryDouble val = new MXQueryDouble(value);
			myToken = new DoubleToken(null, val);
			break;
		}
		case Type.DAY_TIME_DURATION: {

			MXQueryDayTimeDuration val;
			val = new MXQueryDayTimeDuration(value);
			myToken = new DayTimeDurToken(null, val);
			break;
		}
		case Type.DATE_TIME: {
			MXQueryDateTime val = new MXQueryDateTime(value);
			myToken = new DateTimeToken(null, val);
			break;
		}
		case Type.BOOLEAN: {
			if(value.equals("true")) {
				myToken = BooleanToken.TRUE_TOKEN;
			} else
				myToken = BooleanToken.FALSE_TOKEN;
			break;
		}
		case Type.STRING: {
			myToken = new TextToken(null, value);
			break;
		}
		}
	}

	public TokenIterator(Context ctx, MXQueryDouble value, QueryLocation location) throws MXQueryException {
		super(ctx, location);
		setResettable(true);
		myToken = new DoubleToken(null, value);
	}	
	
//	 currently only float type uses double implementation
	public TokenIterator(Context ctx, MXQueryNumber value, int type, QueryLocation location) throws MXQueryException {
		super(ctx, location);
		setResettable(true);
		switch (type) {
		case Type.DOUBLE:
			myToken = new DoubleToken(null, (MXQueryDouble)value);
		break;

		case Type.FLOAT:
			myToken = new FloatToken(null, (MXQueryFloat)value);
		break;
		
		case Type.DECIMAL:
			myToken = new DecimalToken(null, (MXQueryBigDecimal)value);
		break;
		
		default:
			throw new StaticException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Incorrect type passed to the TokenIterator: " + Type.getTypeQName(type, Context.getDictionary()), loc);
		}
		
	}		
	
	public TokenIterator(Context ctx, MXQueryDuration value, QueryLocation location) throws MXQueryException {
		super(ctx, location);
		setResettable(true);
		myToken = new DurationToken(null, value);
	}	
	
	public TokenIterator(Context ctx, MXQueryDayTimeDuration value, QueryLocation location) throws MXQueryException {
		super(ctx, location);
		setResettable(true);
		myToken = new DayTimeDurToken(null, value);
	}
	
	public TokenIterator(Context ctx, MXQueryYearMonthDuration value, QueryLocation location) throws MXQueryException {
		super(ctx, location);
		setResettable(true);
		myToken = new YearMonthDurToken(null, value);
	}	
	
	public TokenIterator(Context ctx, MXQueryDate value, QueryLocation location) throws MXQueryException {
		super(ctx, location);
		setResettable(true);
		myToken = new DateToken(null, value);
	}

	public TokenIterator(Context ctx, MXQueryTime value, QueryLocation location) throws MXQueryException {
		super(ctx, location);
		setResettable(true);
		myToken = new TimeToken(null, value);
	}
	
	public TokenIterator(Context ctx, MXQueryDateTime value, QueryLocation location) throws MXQueryException {
		super(ctx, location);
		setResettable(true);
		myToken = new DateTimeToken(null, value);
	}
	
	public TokenIterator(Context ctx, boolean value, QueryLocation location) throws MXQueryException {
		super(ctx, location);
		setResettable(true);
		if (value)
			myToken = BooleanToken.TRUE_TOKEN;
		else 
			myToken = BooleanToken.FALSE_TOKEN;
	}

	public TokenIterator(Context ctx, String value, QueryLocation location) throws MXQueryException {
		super(ctx, location);
		setResettable(true);
		myToken = new TextToken(null, value);
	}
	
	public TokenIterator(Context ctx, String value, int type, QueryLocation location) throws MXQueryException {
		super(ctx, location);
		setResettable(true);
		int checkType = Type.getEventTypeSubstituted(type, Context.getDictionary());
		
		switch (checkType) {
		case Type.STRING:
		case Type.UNTYPED:
			myToken = new TextToken(type, null, value,null);
		break;

		case Type.UNTYPED_ATOMIC:
			myToken = new UntypedAtomicToken(null, value);
		break;
		
		case Type.ANY_URI:
			myToken = new AnyURIToken(null, value);
		break;
		default:
			throw new RuntimeException("Incorrect type passed to the TokenIterator: " + Type.getTypeQName(type, Context.getDictionary()) );
		}
		
	}		
	
	
	public TypeInfo getStaticType() {
		return new TypeInfo(myToken.getEventType(),Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
	}	
	
	public void setToken(Token tok) {
		myToken = tok;
	}
	
	public boolean cheapEval()  {
		return true;
	}

	public boolean isExprParameter(int valueToCheck, boolean recursive) {
		switch (valueToCheck) {
		case EXPR_PARAM_CHEAPEVAL:
			return true;
		default:
			return super.isExprParameter(valueToCheck, recursive);
		}
	}	
	public final Token getToken() {
		return myToken;
	}		

	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		return new TokenIterator(context, myToken.copy(), null,loc);
	}

	public int compare(Source store) {
		if (store.getURI() != null) {
			return uri.compareTo(store.getURI());
		} else {
			return -2;
		}
	}

	public String getURI() {
		return docId;
	}

	public Source copySource(Context ctx, Vector nestedPredCtxStack) throws MXQueryException {
		return (Source) copy(ctx, null, false, nestedPredCtxStack);
	}
	
	protected KXmlSerializer createIteratorStartTag(KXmlSerializer serializer) throws Exception {
		super.createIteratorStartTag(serializer);
		String content = myToken.getValueAsString();
		if (content == null) {
			content = Type.getTypeQName(myToken.getEventType(), null).toString();
		}
		serializer.attribute(null, "token", content);
		return serializer;
	}
	public Window getIterator(Context ctx) throws MXQueryException {
		return WindowFactory.getNewWindow(context, this);
	}	
}

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
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.BooleanToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;
/**
 * 
 * @author Matthias Braun
 *
 */
public class BooleanIterator extends TokenBasedIterator {
	private boolean value;

	public BooleanIterator(Context ctx, XDMIterator[] subIters, QueryLocation location) {
		super(ctx,location);
		if (subIters == null || subIters.length == 0 || subIters.length != 1) {
			throw new IllegalArgumentException();
		}
		this.subIters = subIters;
	}

	public BooleanIterator() {super(null,null);};
	
	protected void init() throws MXQueryException {

		XDMIterator input = subIters[0];
		Token inputToken = input.next(); 

		int type = Type.getEventTypeSubstituted(inputToken.getEventType(), Context.getDictionary());
		
		if ( Type.isAttribute(type) ){
			type = Type.getAttributeValueType(type);
		}
		
		boolean isNode = false;
		
		if (Type.isNode(type))
			isNode = true;
		
		if ( Type.isTextNode(type) )  {
			type = Type.getTextNodeValueType(type);
		}	
		
		switch(type){
			case Type.START_TAG:
				value = true;
				isNode = true;
			break;	
			case Type.BOOLEAN:
				currentToken = inputToken;
				return;
			case Type.INTEGER:	
				if (inputToken.getLong() != 0) {
					value = true;
				} else {
					value = false;
				}
				break;
			case Type.DOUBLE:
			case Type.FLOAT:
			case Type.DECIMAL:
				if (inputToken.getDouble().equalsZero() || inputToken.getDouble().isNaN()) {
					value = false;
				} else {
					value = true;
				}
				break;
			case Type.STRING:
			case Type.UNTYPED_ATOMIC:
			case Type.UNTYPED:
			case Type.ANY_URI:
				if (inputToken.getText().length() > 0) {
					value = true;
				} else {
					value = false;
				}
				break;
			case Type.END_SEQUENCE:
				value=false;
				break;
			default:
				throw new TypeException(ErrorCodes.F0028_INVALID_ARGUMENT_TYPE, "Type " + Type.getTypeQName(type, Context.getDictionary()) + " could not be casted to destination type " + Type.getTypeQName(Type.BOOLEAN, Context.getDictionary()), loc);
		}
		
		Token tmp = input.next();
		if (!isNode && tmp.getEventType() != Type.END_SEQUENCE) //input.next().getEventType()
			throw new DynamicException(ErrorCodes.F0028_INVALID_ARGUMENT_TYPE,"fn:boolean on a sequence with more than one (non-node) items", loc);
		
		if (value)
			currentToken = BooleanToken.TRUE_TOKEN;
		else
			currentToken = BooleanToken.FALSE_TOKEN;
		
		
	}
	
	/**
	 * Creates a EffectiveBooleanIterator that can never return an empty sequence
	 * @param iter
	 * @return a boolean iterator, unless iter already produces either true or false
	 */

	public static XDMIterator createEBVIterator(XDMIterator iter, Context ctx) {
		if(iter.getStaticType().getType() == Type.BOOLEAN && iter.getStaticType().getOccurID() == Type.OCCURRENCE_IND_EXACTLY_ONE){
			return iter;
		}else{
			return new BooleanIterator(ctx, new XDMIterator[]{iter},iter.getLoc());
		}
	}

	public TypeInfo getStaticType() {
		return new TypeInfo(Type.BOOLEAN,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
	}
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		BooleanIterator copy = new BooleanIterator();
		copy.setContext(context, false);
		copy.setSubIters(subIters);
		copy.loc = loc;
		return copy;
	}
}

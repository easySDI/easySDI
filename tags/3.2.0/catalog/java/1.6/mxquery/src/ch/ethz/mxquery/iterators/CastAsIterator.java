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

/**
 *  author RokasT.
 */

package ch.ethz.mxquery.iterators;

//import org.apache.xerces.xs.XSSimpleTypeDefinition;

import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.BooleanToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.functions.Function;
import ch.ethz.mxquery.functions.xs.XSBinary;
import ch.ethz.mxquery.functions.xs.XSBoolean;
import ch.ethz.mxquery.functions.xs.XSDate;
import ch.ethz.mxquery.functions.xs.XSDateTime;
import ch.ethz.mxquery.functions.xs.XSDayTimeDuration;
import ch.ethz.mxquery.functions.xs.XSDecimal;
import ch.ethz.mxquery.functions.xs.XSDouble;
import ch.ethz.mxquery.functions.xs.XSDuration;
import ch.ethz.mxquery.functions.xs.XSFloat;
import ch.ethz.mxquery.functions.xs.XSGregorian;
import ch.ethz.mxquery.functions.xs.XSInteger;
import ch.ethz.mxquery.functions.xs.XSQName;
import ch.ethz.mxquery.functions.xs.XSString;
import ch.ethz.mxquery.functions.xs.XSTime;
import ch.ethz.mxquery.functions.xs.XSYearMonthDuration;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;

/**
 * Implements cast as TYPE (single atomic value) and 
 * casting sequences of atomic values (for function parameters)
 * Implemented using the xs:TYPE casting functions
 * @author Peter Fischer
 *
 */

public class CastAsIterator extends TokenBasedIterator {

	TypeInfo typeInfo = null; // type to cast expression to 
	boolean onlyCheckCastable = true;
	boolean onlyCastUntyped = false;
	XDMIterator func = null; 
	TokenIterator tokIt = null;
	
	//boolean endOfSeq;
	
	public CastAsIterator(Context ctx, XDMIterator subIters, TypeInfo typeInfo, boolean onlyCheckCastable, boolean onlyCastUntyped, QueryLocation location) {
		super(ctx, new XDMIterator[] {subIters},location);
		
		//this.subIters[0] = DataValuesIterator.getDataIterator(this.subIters[0], context);
		
		this.typeInfo = typeInfo;
		this.onlyCheckCastable = onlyCheckCastable;
		this.onlyCastUntyped = onlyCastUntyped;
	}

	protected void init() throws MXQueryException {
		int t = typeInfo.getType();
		int typeAnnotation = typeInfo.getTypeAnnotation();
		
		Function fks = null;
		
		if (Type.isUserDefinedType(typeAnnotation) && !(typeAnnotation == Type.UNTYPED || typeAnnotation == Type.UNTYPED_ATOMIC)){
			QName qName = Type.getTypeQName(typeAnnotation,Context.getDictionary());
		String namespace = qName.getNamespacePrefix().substring(1, qName.getNamespacePrefix().indexOf("}"));
		String prefix = context.getPrefix(namespace);
		qName = new QName(namespace,prefix,qName.getLocalPart());
//		int typeAnn = typeAnnotation & Type.MASK_USER_DEFINED_TYPE_INDEX;
		//XSSimpleTypeDefinition typeDef = (XSSimpleTypeDefinition) Context.getDictionary().lookUpByIntegerRepresentation(typeAnn);
		fks = context.getFunction(qName, 1); 
		}
	//	facetsList = typeDef.getFacets();
		/**
		 * Set up the appropriate casting function
		 */
		if (fks == null){
		
		//System.out.println("casting "+Type.getTypeQName(t,Context.getDictionary()));
		/* handle all subtypes uniformly */
		int checkType = Type.getEventTypeSubstituted(t, Context.getDictionary()); 
				
		switch (checkType) {
			case Type.UNTYPED_ATOMIC :	
			case Type.STRING :
			case Type.ANY_URI:
				func = new XSString();
				((XSString)func).setTargetType(t);
			break;
			case Type.BOOLEAN :
				func = new XSBoolean();
			break;
			case Type.INTEGER :
				func = new XSInteger();
				((XSInteger)func).setTargetType(t);
			break;
			case Type.DATE :
				func = new XSDate();
			break;
			case Type.DATE_TIME :
				func = new XSDateTime();
			break;
			case Type.DAY_TIME_DURATION :
				func = new XSDayTimeDuration();
			break;
			case Type.DOUBLE :
				func = new XSDouble();
				break;
			case Type.FLOAT :
				func = new XSFloat();
			break;
			case Type.DECIMAL :			
				func = new XSDecimal();
			break;	
			case Type.DURATION :
				func = new XSDuration();
			break;
			case Type.QNAME:
				func = new XSQName();
			break;			
			case Type.TIME :
				func = new XSTime();
			break;
			case Type.YEAR_MONTH_DURATION :
				func = new XSYearMonthDuration();
			break;
			case Type.G_DAY:
			case Type.G_MONTH:
			case Type.G_YEAR:
			case Type.G_MONTH_DAY:
			case Type.G_YEAR_MONTH:
				func = new XSGregorian();
				((XSGregorian)func).setTargetType(checkType);
			break;	
			case Type.HEX_BINARY:
			case Type.BASE64_BINARY:
				func = new XSBinary();
				((XSBinary)func).setTargetType(checkType);
			break;	
			case Type.ANY_ATOMIC_TYPE:
			case Type.NOTATION:
				throw new TypeException(ErrorCodes.E0080_STATIC_CAST_CASTABLE_TARGET_TYPE_IS_NOTATION_OR_ANYATOMIC, "Cast to xs:anyAtomic not allowed", loc);
			default:
				throw new TypeException(ErrorCodes.E0051_STATIC_QNAME_AS_ATOMICTYPE_NOT_DEFINED_AS_ATOMIC, "Invalid value for cast: " + Type.getTypeQName(t, Context.getDictionary()), loc); 
		}
		} else {
			func = fks.getFunctionImplementation(context);
		}
		func.setResettable(true);
		func.setContext(context, false);
		tokIt = new TokenIterator(context, Token.END_SEQUENCE_TOKEN,null,loc);
		func.setSubIters(tokIt);

	}	
	
	void castSingleValue() throws MXQueryException{
		Token tok;
		try {
			func.reset();
			tok = func.next();
		} catch(Exception e) {			
			
			if (onlyCheckCastable) {
				currentToken = BooleanToken.FALSE_TOKEN;
				this.close(false);
				return;		
			}
			if (e instanceof MXQueryException) {
				MXQueryException mqe = (MXQueryException)e;
				if (mqe.getErrorCode().equals(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE) 
						|| mqe.getErrorCode().equals(ErrorCodes.F0005_INVALID_LEXICAL_VALUE)
						|| mqe.getErrorCode().equals(ErrorCodes.E0002_DYNAMIC_NO_VALUE_ASSIGNED))
					throw mqe;
				else
					throw new DynamicException(ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR,mqe.toString(), loc);
			}
			else if (e instanceof TypeException){
				throw (MXQueryException)e;
			}
			else throw new MXQueryException (ErrorCodes.A0009_EC_EVALUATION_NOT_POSSIBLE, e.getMessage() , loc );
		}
		if (onlyCheckCastable) 
			currentToken = BooleanToken.TRUE_TOKEN;
		else
			currentToken = tok;		
	}
	
	boolean checkOcc(Token tok, int t) throws MXQueryException{
		int oi = typeInfo.getOccurID();
		if (tok == Token.END_SEQUENCE_TOKEN) {
			this.close(false);	
			endOfSeq = true;
			if (called == 0 && (oi == Type.OCCURRENCE_IND_EXACTLY_ONE || oi == Type.OCCURRENCE_IND_ONE_OR_MORE)) {
				if (onlyCheckCastable) {
					currentToken = BooleanToken.FALSE_TOKEN;
					return false;
				}
				else 
					throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,"Could not cast empty sequence to atomic type " + Type.getTypeQName(t, Context.getDictionary()), loc);
			} else {
				return true;
			}
		} else {
		 // occurrence indicator ('exactly one' / 'zero or one')
			if (called >= 1 && (oi == Type.OCCURRENCE_IND_EXACTLY_ONE || oi == Type.OCCURRENCE_IND_ZERO_OR_ONE)){
				if (onlyCheckCastable){
					endOfSeq = true;
					currentToken = BooleanToken.FALSE_TOKEN;
					return false;
				}
				else 
					throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,"Could not cast a sequence with more than one value to a atomic type " + Type.getTypeQName(t, Context.getDictionary()), loc);
			
			}					
		}
		return true;

	}
	
	public Token next() throws MXQueryException {
		Token tok;
		int t = typeInfo.getType();

		if (endOfSeq) {
			close(false);
			return Token.END_SEQUENCE_TOKEN;
		}
			
		
		if (called == 0) {
			init();
			// get next Token from "below", 
			// assign to current Token to be checked
			tok = subIters[0].next();
			tokIt.setToken(tok);
		//	System.out.println(Type.getTypeQName(tok.getEventType(),Context.getDictionary()));
			// do check for occurence/EOS
			// check occurrence indicator constraint

			if (!checkOcc(tok,t))
				return currentToken;		
			called++;
		}
		
	
		
		
		tok = tokIt.getToken();
			
		// check for castability
		int checkType = Type.getEventTypeSubstituted(t, Context.getDictionary()); 
		
		if (onlyCastUntyped) {
			int tokType = tok.getEventType();
			if (!Type.isTypeOrSubTypeOf(tokType,checkType,Context.getDictionary()) && !(tokType == Type.UNTYPED || tokType == Type.UNTYPED_ATOMIC || tokType == Type.END_SEQUENCE ||
					Type.typePromoteableTo(tokType, checkType, Context.getDictionary()))) {
				throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Type not allowed", loc);
			}
		}		
				
		// perform cast on single atomic token

		castSingleValue();
		// get next Token from "below", 
		// assign to current Token to be checked
		tok = subIters[0].next();
		tokIt.setToken(tok);
		// do check for occurence/EOS
		// check occurrence indicator constraint

		checkOcc(tok,t);		
		called++;
		return currentToken;	
	}

	public TypeInfo getStaticType() {
		return typeInfo;
	}		
	
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		return new CastAsIterator(context, 
				subIters[0], 
				typeInfo.copy(), 
				onlyCheckCastable, 
				onlyCastUntyped,loc);
	}
	
}

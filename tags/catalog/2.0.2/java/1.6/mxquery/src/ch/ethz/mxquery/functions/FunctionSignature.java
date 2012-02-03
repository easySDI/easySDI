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

package ch.ethz.mxquery.functions;

import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.model.XDMIterator;

/**
 * Holds information on the signature (Name, parameters, classification) of an XQuery function
 * @author Peter Fischer
 *
 */

public class FunctionSignature {
	
	protected QName qname;

	TypeInfo [] paramTypes;
	
	protected int type;
	
	protected int expressionType; 
	
	protected boolean nondet;
	
	//Undefined is just for locking up values
	private static final int UNDEFINED_TYPE=0;
	
	public static final int SYSTEM_FUNCTION=1;
	public static final int USER_DEFINED_FUNCTION=2;
	public static final int EXTERNAL_FUNCTION=3;
	//public static final int LOCAL_SCOPE_FUNCTION=4;
	
	FunctionSignature(QName qname, TypeInfo [] parameterType){
		this(qname, parameterType, UNDEFINED_TYPE, XDMIterator.EXPR_CATEGORY_SIMPLE, false);
	}
	
	public FunctionSignature(QName qname, TypeInfo[] parameterType, int type, int exprType, boolean nondeterministic){
		this.qname = qname;
		this.paramTypes = parameterType;
		this.type = type;
		expressionType = exprType;
		nondet = nondeterministic;
	}
	/**
	 * Checks the equality of Function Signatures. 
	 * Function Signatures are equals if they have the same name and number of parameters
	 * The type of parameters is irrelevant, as no overloading on parameter types is possible
	 */
	public boolean equals(Object obj) {
		if(obj instanceof FunctionSignature){
			FunctionSignature signature = (FunctionSignature) obj;
			if(signature.getArity() == getArity() && signature.qname.equals(qname)){
				return true;
			}
		}
		return false;
	}
	/**
	 * Computes the hash code of a Function Signature 
	 * Only the name and number of parameters are considered,
	 * since the type of parameters is irrelevant
	 */
	public int hashCode() {
		return (qname.hashCode() << 3) | paramTypes.length;
	}
	/**
	 * Returns the name of this function as qualified QName
	 * @return the name of the function
	 */
	public QName getName() {
		return this.qname;
	}
	/**
	 * Returns the number of parameters (=arity) of this function
	 * @return The number of parameters
	 */
	public int getArity() {
		return this.paramTypes.length;
	}
	/**
	 * Returns the type information for each of the parameters
	 * @return A type information for each parameter
	 */
	public TypeInfo [] getParameterTypes() {
		return paramTypes;
	}
	/**
	 * What is the expression type of this function (as in XQUF, XQSF): SIMPLE, UPDATING, SCRIPTING, VACUOUS
	 * @return The expression type of this function
	 */
	public int getExpressionCategory() {
		return expressionType; 
	}
	/**
	 * Is this a deterministic or non-deterministic functions (as defined in XQuery 1.1)
	 * @return true if the function is non-deterministic
	 */
	public boolean isNonDeterministic() {
		return nondet;
	}
	
	public FunctionSignature copy() {
		return new FunctionSignature(qname.copy(), paramTypes, type, expressionType, nondet);		
	}
}


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

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.BooleanToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.exceptions.StaticException;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.util.KXmlSerializer;
/**
 * 
 * @author Matthias Braun
 *
 */
public class LogicalIterator extends TokenBasedIterator {
	/*************
	 * constants *
	 *************/
	public static final int AND = 1;

	public static final int OR = 2;

	/*************
	 * variables *
	 *************/
	private int lType;

	/***********
	 * methods *
	 ***********/

	/**
	 * Constructor for the LogicalIterator
	 * 
	 * @param type
	 *            indicates the type of the logical expression (AND | OR)
	 * @param subIters
	 *            Array of Expressions, which are connected with AND / OR
	 *            depending on type.
	 * @throws MXQueryException 
	 */
	public LogicalIterator(Context ctx, int type, XDMIterator[] subIters, QueryLocation location) throws MXQueryException {
		super(ctx, 2,subIters,location);
		this.lType = type;		
	}

	/**
	 * Initializes the LogicalIterator and computes the logical connection of
	 * the given Iterators with the AND / OR connector.
	 * 
	 * @return the event type of the first Token of the connection result.
	 * @throws MXQueryException
	 */
	protected void init() throws MXQueryException {
		boolean v;
		if (lType == AND) {
			v = true;
		} else if (lType == OR) {
			v = false;
		} else {
			throw new StaticException(ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
					"Type of LogicalExpression must be AND or OR", loc);
		}		

		for (int i = 0; i < subIters.length; i++) {
			Token inToken = subIters[i].next(); 
			if(inToken.getEventType() != Type.END_SEQUENCE){
				if (inToken.getBoolean() == !v) {
					v = !v;
					break;
				}
			}else{
				if(v){
					v = false;
					break;
				}
			}
			
		}
		if (v)
			currentToken = BooleanToken.TRUE_TOKEN;
		else
			currentToken = BooleanToken.FALSE_TOKEN;
	}

	public TypeInfo getStaticType() {
		return new TypeInfo(Type.BOOLEAN,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
	}

	protected KXmlSerializer createIteratorStartTag(KXmlSerializer serializer) throws Exception {
		super.createIteratorStartTag(serializer);
		if(lType == OR){
			serializer.attribute(null, "type", "OR");
		}else{
			serializer.attribute(null, "type", "AND");
		}
		return serializer;
	}
	
	public int getOperatorType(){
		return this.lType;
	}
	
	public XDMIterator getLeftChild(){
		return subIters[0];
	}
	
	public XDMIterator getRightChild(){
		return subIters[1];
	}
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		return new LogicalIterator(context, lType, subIters,loc);
	}
}

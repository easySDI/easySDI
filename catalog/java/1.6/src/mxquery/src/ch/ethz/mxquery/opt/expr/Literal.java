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
package ch.ethz.mxquery.opt.expr;

import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.util.KXmlSerializer;

/**
 * A literal in this optimizer has always exactly one iterator on which it depends. 
 * So logical iterators are split in this optimizer tree
 * @author Tim Kraska (<a href="mailto:mail@tim-kraska.de">mail@tim-kraska.de</a>)
 *
 */
public class Literal extends LogicalUnit{
	
	protected XDMIterator iter;

	public Literal(XDMIterator iter){
		this.iter = iter;
	}

	public XDMIterator getIter() {
		return iter;
	}
	
	
	/**
	 * Evaluates the literator up to a specific level. The level distinguish how many dependencies should be evalueted
	 */
	public int evaluate(int level) throws MXQueryException{
		if(iter == null){
			result = RESULT_FALSE;
			return result;
		}
		if(result == RESULT_UNKNOWN){
			currentEvalLevel = level;
			if(isLowerOrEqualDependency(dependency, level)){
				Token tok = iter.next();
				int eventType = tok.getEventType();
				if(eventType == Type.BOOLEAN){
					if(tok.getBoolean()){
						result = RESULT_TRUE;
					}else{
						result = RESULT_FALSE;
					}
				}else if(eventType == Type.END_SEQUENCE){
					result = RESULT_FALSE;
				}else{
					throw new RuntimeException("Type not supported for Literals");
				}
				iter.reset();
			}
		}
		return result;
	}


	/**
	 * Clones the Literal but the underlying Iterator isn't cloned
	 */
	public Object clone() {
		return super.clone ();
		
	}
	
	public KXmlSerializer traverse(KXmlSerializer serializer){
		try{
		createStartTag(serializer);
		if(iter != null){
			iter.traverseIteratorTree(serializer);
		}
		createEndTag(serializer);
		}catch(Exception err){
			throw new RuntimeException(err);
		}
		return serializer;
	}
	

	
}

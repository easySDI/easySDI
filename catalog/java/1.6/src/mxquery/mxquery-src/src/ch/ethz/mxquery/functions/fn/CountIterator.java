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
/**
 * @author trokas
 */

import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.Identifier;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.LongToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.iterators.VariableIterator;
import ch.ethz.mxquery.model.IndexIterator;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;

public class CountIterator extends TokenBasedIterator {
	private IndexIterator indexIter; 
	
	private final Token getCountIndexed() throws MXQueryException {

		int i=1;
		while ( indexIter.hasItem(i) ) {
			i++;
		}
		i = i-1;
		
		// System.out.println("i: " + i);
		LongToken ret = new LongToken(Type.INTEGER, (Identifier)null, i); 
		return ret;
	}	
	
	/* count for iterator without index */
	private Token getCount() throws MXQueryException {

		int count 	= 0;
		Token tok = getItem(0); 
		while (tok.getEventType() != Type.END_SEQUENCE) {
			tok = getItem(0);
			count++;
		}
		
		LongToken ret = new LongToken(Type.INTEGER, (Identifier)null, count); 
		return ret;
	}

	private Token getItem(int level) throws MXQueryException {
		Token tok = null;
		do {
			tok = subIters[0].next(); 
			switch (tok.getEventType()) {
			case Type.START_TAG:
			case Type.START_DOCUMENT:
				level++;
				break;
			case Type.END_TAG:
			case Type.END_DOCUMENT:
				level--;
				break;
			// for debugging
			//default:
			//	System.out.println("type: " + iter.getEventType());
			}
		}while(level > 0);
		return tok;	
	}


	protected void init() throws MXQueryException {
		if(subIters[0] instanceof IndexIterator){
			indexIter = (IndexIterator)subIters[0];
		}else if(subIters[0] instanceof VariableIterator){
			indexIter = ((VariableIterator)subIters[0]).getUnderlyingIterator();
		}	
		if (indexIter != null) 	
			currentToken = getCountIndexed();
		else 
			currentToken = getCount();
	}
	public TypeInfo getStaticType() {
		return new TypeInfo(Type.INTEGER,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
	}
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new CountIterator();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;	
	}
}

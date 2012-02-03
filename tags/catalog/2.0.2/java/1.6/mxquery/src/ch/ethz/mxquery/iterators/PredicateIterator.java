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
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.exceptions.StaticException;
import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.EmptySequenceIterator;
import ch.ethz.mxquery.model.IndexIterator;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.model.XDMIterator;
/**
 * 
 * @author Matthias Braun
 *
 */
public class PredicateIterator extends CurrentBasedIterator {	
	
	private int matchInt;

	private int position = 0;

	
	private IndexIterator indexIter = null; //used for index access, if underlying iterator supports it
	
	public PredicateIterator(Context ctx, XDMIterator[] subIters, QueryLocation location) throws MXQueryException {
		super(ctx, subIters,location);
		if (subIters == null || subIters.length == 0 || subIters.length != 2) {
			throw new IllegalArgumentException();
		}
	}
	
	public Token next() throws MXQueryException {		
		if (this.endOfSeq) {
			return Token.END_SEQUENCE_TOKEN;
		}
		
		if(this.called == 0){
			init();
		}

		this.called++;
		if(indexIter != null) {
			if (current != null) {
				Token retToken = current.next();
				if (retToken.getEventType() == Type.END_SEQUENCE) {
					this.freeResources(false);
				}
				return retToken;
			} else {
				this.freeResources(false);
				return Token.END_SEQUENCE_TOKEN;
			}
		} else {
			while(true) {
				current = subIters[0];
				Token tok = sub0Next();
				
				if(position == matchInt) {
					current = subIters[0];
					int retType = tok.getEventType();
					if (retType == Type.END_SEQUENCE){
						this.freeResources(false);
					}
					return tok;
				}else if(tok.getEventType() == Type.END_SEQUENCE){
					break;
				}
			}
		}
		this.freeResources(false);

		return Token.END_SEQUENCE_TOKEN;
	}
	
	private Token sub0Next() throws MXQueryException {
		if (depth == 0) {
			position++;
		}
		return super.getNext();
	}
	
	private void init() throws MXQueryException {
		if(subIters[0] instanceof IndexIterator){
			indexIter = (IndexIterator)subIters[0];
		} else if(subIters[0] instanceof VariableIterator){
			indexIter = ((VariableIterator)subIters[0]).getUnderlyingIterator();
		}
		
		//context.setContextItem(subIters[0]);
		
		Token tok = subIters[1].next();		
		
		if (Type.isTypeOrSubTypeOf(tok.getEventType(),Type.INTEGER,Context.getDictionary())) {	
			matchInt = (int)tok.getLong();
			this.subIters[1].close(false);
		} else {
			throw new StaticException(ErrorCodes.A0002_EC_NOT_SUPPORTED, "We support only position predicates in the iterator tree", loc);
		}
		if(indexIter !=null){
			if (indexIter.hasItem(matchInt)){
				current = indexIter.getItem(matchInt);
			}
			else {
				current = new EmptySequenceIterator(context,loc);
			}
		}
	}
	
	
	protected void freeResources(boolean restartable) throws MXQueryException {
		super.freeResources(restartable);
		if (this.current instanceof Window) {
			((Window)this.current).destroyWindow();
		}
		if (this.indexIter instanceof Window) {
			((Window)this.indexIter).destroyWindow();
		}
	}
	
	protected void resetImpl() throws MXQueryException {
		super.resetImpl();
		current = null;
		position = 0;
	}

	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		return new PredicateIterator(context, subIters,loc);
	}
}

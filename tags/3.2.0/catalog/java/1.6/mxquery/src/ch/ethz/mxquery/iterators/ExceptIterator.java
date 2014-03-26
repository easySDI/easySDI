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
import ch.ethz.mxquery.datamodel.Identifier;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.util.Set;

public class ExceptIterator extends CurrentBasedIterator {
	private boolean scanIter = true;
	private boolean firstInvocation = true;
	private Set set[];
	
	private int curDepth = 0;
	
	
	/**
	 * 
	 *  Input are two iterators on which except opration is performed
	 *  
	 * @param iter1 
	 * @param iter2 
	 * @throws MXQueryException 
	 */
	
	public ExceptIterator(Context ctx, XDMIterator iter1, XDMIterator iter2, QueryLocation location) throws MXQueryException{
		super(ctx, location);
		this.subIters = new XDMIterator[]{iter1, iter2};
		this.set = new Set[2];
		this.set[0] = new Set();
		this.set[1] = new Set();		
	}
	
	private Token nextDepth(XDMIterator iter) throws MXQueryException {
		Token tok = iter.next();
		int type = tok.getEventType();
		if (type == Type.START_DOCUMENT || type == Type.START_TAG) {
			this.curDepth++;
		} else if (type == Type.END_DOCUMENT || type == Type.END_TAG) {
			this.curDepth--;
		}
		return tok;
	}

	/**
	 * Precondition: inputs are sorted in the document order
	 * */
	
	public Token next() throws MXQueryException {
		if (this.curDepth > 0) {
			return this.nextDepth(this.current);
		}
		int type;
		
		if(this.firstInvocation){
			Token tok = this.nextDepth(this.subIters[1]);
			type = tok.getEventType();
			if(type == Type.END_SEQUENCE){
					this.firstInvocation = false;
				return this.next();
			}
			
			if (!Type.isNode(type))
				throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,"Non-node items not possible in except", loc);
			
			Identifier id = tok.getId();
			
			//FIXME: Remove this once we have better node ID support
			
			if(set[0].contains(id)){
				while (this.curDepth > 0) {
					this.nextDepth(this.subIters[1]);
				}
				return this.next();
			}
			else{
				set[0].add(id);	
				while (this.curDepth > 0) {
					this.nextDepth(this.subIters[1]);
				}
				return this.next();
			}
		}
		
		if (this.endOfSeq) {
			return Token.END_SEQUENCE_TOKEN;
		} 
		if(scanIter){
			Token tok = this.nextDepth(this.subIters[0]);
			type = tok.getEventType();
			if(type == Type.END_SEQUENCE){
					scanIter = false;
				return this.next();
			}
			
			if (!Type.isNode(type))
				throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,"Non-node items not possible in except", loc);
			
			Identifier id = tok.getId();
			//FIXME: Remove this once we have better node ID support
			if(set[0].contains(id)){
				while (this.curDepth > 0) {
					this.nextDepth(this.subIters[0]);
				}
				return this.next();
			}
			else{
				if(!set[1].contains(id)){
					this.current = this.subIters[0];
					set[0].add(id);				
					return tok;
				} else{
					while (this.curDepth > 0) {
						this.nextDepth(this.subIters[0]);
					}
					return this.next();
				}
			}
		}
		this.endOfSeq = true;
		return Token.END_SEQUENCE_TOKEN;
	}

	protected void resetImpl() throws MXQueryException {
		super.resetImpl();
		this.set = new Set[2];
		this.set[0] = new Set();
		this.set[1] = new Set();
		this.scanIter = true;
		this.firstInvocation = true;		
		this.curDepth = 0;		
	}	
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		return new ExceptIterator(context, subIters[0], subIters[1], loc);
	}
}
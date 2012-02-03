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

public class IntersectIterator extends CurrentBasedIterator {
	private int iterSwitch=1;
	private boolean scanIter1 = true;
	private boolean scanIter2 = true;
	private Set set[];
	
	private int curDepth = 0;
	
	
	/**
	 * 
	 *  Input are two iterators on which intersect needs to be done
	 *  
	 * @param iter1 
	 * @param iter2 
	 */
	public IntersectIterator(Context ctx, XDMIterator iter1, XDMIterator iter2, QueryLocation location){
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

		if (this.endOfSeq) {
			
			return Token.END_SEQUENCE_TOKEN;
		} 
		if(scanIter1 || scanIter2){
			if(scanIter1 && scanIter2)
				iterSwitch = (iterSwitch+1)%2;
			else if(scanIter1)
				iterSwitch = 0;
			else 
				iterSwitch = 1;
			Token tok = this.nextDepth(this.subIters[iterSwitch]);
			if(tok.getEventType() == Type.END_SEQUENCE){
				if(iterSwitch==0)
					scanIter1 = false;
				else
					scanIter2 = false;
				return this.next();
			}
			
			if (!Type.isNode(tok.getEventType()))
				throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,"Non-node items not possible in except", loc);
			
			Identifier id = tok.getId();
			
			if(set[iterSwitch].contains(id)){
				tok = this.nextDepth(this.subIters[iterSwitch]);
				while (this.curDepth > 0) {
					tok = this.nextDepth(this.subIters[iterSwitch]);
				}
				return this.next();
			}
			else{
				set[iterSwitch].add(id);
				if(set[(iterSwitch+1)%2].contains(id)){
					this.current = this.subIters[iterSwitch];
					return tok;
				} else{
					while (this.curDepth > 0) {
						this.nextDepth(this.subIters[iterSwitch]);
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
		this.iterSwitch=1;
		this.scanIter1 = true;
		this.scanIter2 = true;
		this.curDepth = 0;		
	}
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		return new IntersectIterator(context, subIters[0], subIters[1],loc);
	}
}
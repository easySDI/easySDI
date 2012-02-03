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

package ch.ethz.mxquery.sms.iterators;

import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;

import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.EmptySequenceIterator;
import ch.ethz.mxquery.model.XDMIterator;

import ch.ethz.mxquery.sms.interfaces.StreamStore;

import ch.ethz.mxquery.util.IntegerList;

public class ResultDataItemsIterator extends CurrentBasedIterator {
	
	private StreamStore buffer = null;
	private IntegerList posList = null;
	private int size = 0;
	private int crtPos = 0;
	private static XDMIterator empty = new EmptySequenceIterator(null, null);
	
	public ResultDataItemsIterator(StreamStore sb, IntegerList il, int size){
		super();
		this.buffer = sb;
		this.posList = il;
		this.size = size;
	}
	
	public Token next()throws MXQueryException{
		if ( posList == null || buffer==null){
			current = empty;
			return current.next();
		}



		if ( crtPos >= posList.size()  ){
			return Token.END_SEQUENCE_TOKEN;
		}

		if ( current!=null ){
			Token tok = current.next(); 
			if ( tok != Token.END_SEQUENCE_TOKEN )
				return tok;
			else{
				crtPos++;
				if ( crtPos == posList.size() )
					return Token.END_SEQUENCE_TOKEN;
			}
		}

		//if ( Thread.currentThread().getName().compareTo("[#2 ACCIDENT-ALERTS-TO-FILE]") == 0 )
		//System.out.println("Start : "+posList.get(crtPos) + " End : "+ posList.get(crtPos)+size);
		current = new ResultDataIterator(buffer,posList.get(crtPos),posList.get(crtPos)+size);

		return current.next();
	}

	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack)
			throws MXQueryException {
		// TODO Auto-generated method stub
		throw new RuntimeException("Not implemented");
		//return null;
	}
}

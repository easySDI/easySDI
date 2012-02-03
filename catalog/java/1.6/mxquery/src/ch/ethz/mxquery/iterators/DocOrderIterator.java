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

import ch.ethz.mxquery.bindings.WindowFactory;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.Identifier;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.util.QuickSort;

public class DocOrderIterator extends CurrentBasedIterator {
	
	private DocOrderObject doc_array[];
	private int number=-1;
	private Identifier lastID  = null;
	
	
	/**
	 * Returns document in document order
	 * @param iter
	 */
	public DocOrderIterator(Context ctx, XDMIterator iter, QueryLocation location){
		super(ctx, location);
		this.subIters = new XDMIterator[]{iter};
	}
	
	private void init() throws MXQueryException{
		Window w0 = WindowFactory.getNewWindow(this.context, this.subIters[0]);
		Vector vector = new Vector();
		while (w0.hasNextItem()) {
			XDMIterator it = w0.nextItem();
			
			
			Window w = WindowFactory.getNewWindow(this.context, it);
			w.setResettable(true);
			Token curTok = w.next();
			Identifier id = curTok.getId();
			w.reset();
			DocOrderObject temp = new DocOrderObject(w, id);
			vector.addElement(temp);
		}
		this.doc_array = new DocOrderObject[vector.size()];
		vector.copyInto(this.doc_array);
		QuickSort.sort(this.doc_array, new DocOrderCompare());
	}

	public Token next() throws MXQueryException {
		if(this.called == 0){
			init();
			called++;
		}

		if(this.endOfSeq){
			return Token.END_SEQUENCE_TOKEN;
		} else if (this.current != null) {
			Token tok = this.current.next();
			if(tok.getEventType() != Type.END_SEQUENCE){
				return tok;
			}
		}
		
		if(number < this.doc_array.length-1){
			
			number++;
			this.current = this.doc_array[number].getWindow();
			
			Token tok = this.current.next();
			while ((tok == Token.END_SEQUENCE_TOKEN || (lastID != null && lastID.compare(tok.getId())== 0)) && number < this.doc_array.length) {
				
				if(number == doc_array.length -1){
					endOfSeq = true;
					return Token.END_SEQUENCE_TOKEN;
				}		
			
				number++;
				this.current = this.doc_array[number].getWindow();
				current.reset();
				tok = this.current.next();
			} 
			lastID = tok.getId();
			if (tok != Token.END_SEQUENCE_TOKEN)
				return tok;
		}
		this.endOfSeq = true;
		return Token.END_SEQUENCE_TOKEN;
	}

	protected void resetImpl() throws MXQueryException {
		super.resetImpl();
		number = -1;
		lastID = null;
		doc_array = null;
		
	}

	protected void freeResources(boolean restartable) throws MXQueryException {
		super.freeResources(restartable);
		number = -1;
		lastID = null;
		doc_array = null;
	}

	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		return new DocOrderIterator(context, subIters[0], loc);
	}
}

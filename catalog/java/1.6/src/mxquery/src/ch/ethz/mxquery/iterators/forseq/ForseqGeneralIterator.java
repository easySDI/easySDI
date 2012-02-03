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

package ch.ethz.mxquery.iterators.forseq;

import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.util.IntegerList;
import ch.ethz.mxquery.util.KXmlSerializer;

/**
 * This was the first implementation of General FORSEQ. Unfortunatly this implementnation is not
 * already order by start end. 
 * @author Tim Kraska (<a href="mailto:mail@tim-kraska.de">mail@tim-kraska.de</a>)
 *
 */
public final class ForseqGeneralIterator extends ForseqIterator{
	
	private int generalWindowNb=0;
	
	public ForseqGeneralIterator(Context ctx, int windowType, QName var, TypeInfo t, XDMIterator seq, int orderMode, QueryLocation location) {
		super(ctx, windowType, var, t, seq, orderMode, location);
		if (windowType != GENERAL_WINDOW) {
			throw new IllegalArgumentException("This iterator is only allowed for general forseqs");
		}
		if(orderMode == ORDER_MODE_ENDSTART){
			throw new IllegalArgumentException("The order mode ENDSTART is not supported by this iterator");
		}
	}
	 
	/**
	 * Generating all subsequences by the use of the binary integer representation
	 */
	public Window assignWindow() throws MXQueryException{
		generalWindowNb++;
		if((generalWindowNb >> currentPosition)> 0 ){
			increaseCurrentPosition();
		}
		int i=0;
		IntegerList items = new IntegerList();
		if(!endOfStream){
			while((generalWindowNb >> i) > 0 ){
				if((generalWindowNb & ( 1 << i)) > 0){
					items.add(i);
				}
				i++;
			}
			return seq.getNewItemWindow(items);
		}else{
			return null;
		}
	}

	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		// TODO: order mode?
		return new ForseqGeneralIterator(context, 
				windowType, 
				var.copy(), 
				varType.copy(), 
				subIters[0], 
				ForseqIterator.ORDER_MODE_END, loc);
	}
	
	protected KXmlSerializer createIteratorStartTag(KXmlSerializer serializer) throws Exception {
		super.createIteratorStartTag(serializer);
		serializer.attribute(null, "var", "$" +  var.toString());
		return serializer;
	}

}

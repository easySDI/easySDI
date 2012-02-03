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

package ch.ethz.mxquery.iterators.ft;

import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.model.ft.AnyAllOption;

/**
 * Implementation of FTWordsValue
 * @author jimhof
 */

public class Words extends FTBaseIterator{
	
	private XDMIterator varIter;
	private XDMIterator tokenIter;
	private AnyAllOption anyAllOption;
	
	public Words(Context ctx, Vector subIters){
		super(ctx,(FTIteratorInterface [])null);
		this.context = ctx;
		varIter = (XDMIterator)subIters.elementAt(0);
		tokenIter = (XDMIterator)subIters.elementAt(1);
	}
	
	public XDMIterator getVarIter(){
		return varIter;
	}
	
	public XDMIterator getTokenIter(){
		return tokenIter;
	}
	
	public Context getContext(){
		return this.context;
	}
	
	public Vector getSubIterators(){
		Vector res = new Vector();
		res.addElement(varIter);
		res.addElement(tokenIter);
		return res;
	}

	public void setAnyAllOption(AnyAllOption option){
		this.anyAllOption = option;
	}

	public AnyAllOption getAnyAllOption(){
		return this.anyAllOption;
	}
	protected FTIteratorInterface copy(Context context, FTIteratorInterface [] subIters, Vector nestedPredCtxStack)throws MXQueryException {
		//TODO: Copy subIters
		return new Words(context, getSubIterators());
	}
}


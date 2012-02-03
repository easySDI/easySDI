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
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.model.WindowVariable;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.parallel.ThreadPool;

public final class ForseqWindowEarlyBinding extends ForseqWindowIterator {
	private Window earlyWindow;

	public ForseqWindowEarlyBinding(Context ctx, int windowType, QName var, TypeInfo t, XDMIterator seq, WindowVariable[] startVars, XDMIterator startExpr,  WindowVariable[] endVars, XDMIterator endExpr, boolean forceEnd, boolean onNewStart, int orderMode, QueryLocation location) throws MXQueryException {
		super(ctx, windowType, var, t, seq, startVars, startExpr,  endVars, endExpr, forceEnd, onNewStart, orderMode, location);
		if (seq == null || var == null || startExpr == null || (endExpr == null && !onNewStart)) {
			throw new IllegalArgumentException();
		}
		if (forceEnd) {
			throw new IllegalArgumentException("Force end not allowed with early binding");
		}
		if (onNewStart) {
			throw new IllegalArgumentException("onNewStart not allowed with early binding");
		}
	}

	protected Window assignWindow(boolean startNewWhenActive, boolean closeWindowWhenMatch) throws MXQueryException {
		if (ThreadPool.isDebug()) ThreadPool.log(this, "EarlyBinding.assignWindow() called");
		
		if(earlyWindow != null){
			if(windowType == TUMBLING_WINDOW && earlyWindow != null){
				currentPosition = earlyWindow.getEndPosition();
				setSeqPositionForGarbageCollection(currentPosition);
			}else if(earlyWindow.getNextWindowStartPosition() != -1){
				currentPosition = earlyWindow.getNextWindowStartPosition();
				earlyWindow = seq.getNewEarlyWindowInterface(currentPosition, startExpr, startVars, startVarsHolder, endExpr, endVars, endVarsHolder);
				setSeqPositionForGarbageCollection(currentPosition-1);
				return earlyWindow;
			}
		}
		
		while(!endOfStream){
			increaseCurrentPosition();
			if(checkStartExpr(currentPosition)){
				setSeqPositionForGarbageCollection(currentPosition-1);
				if(windowType == TUMBLING_WINDOW){
					earlyWindow = seq.getNewEarlyWindowInterface(currentPosition, null, null, null, endExpr, endVars, endVarsHolder);
				}else{
					earlyWindow = seq.getNewEarlyWindowInterface(currentPosition, startExpr, startVars, startVarsHolder, endExpr, endVars, endVarsHolder);
				}
				return earlyWindow;
			}
		}
		
		earlyWindow=null;
		return null;
	}

	 
	/**
	 * Checks the start expression
	 * @param startPosition
	 * @return
	 * @throws MXQueryException
	 */
	private boolean checkStartExpr(int startPosition)  throws MXQueryException {
		assignVars(startVars, startVarsHolder, startPosition);
		
		boolean value=false;
		Token startTok = startExpr.next(); 
		if(startTok.getEventType() == Type.BOOLEAN){
			value = startTok.getBoolean();
		}else{
			throw new RuntimeException("This should never happen because the condition is always a boolean expression.");
		}
		startExpr.reset();
		
		return value;
	}
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		return new ForseqWindowEarlyBinding(
			context, 
			windowType, 
			var.copy(), 
			varType.copy(), 
			//seq.copy(context, false), 
			subIters[0],
			copyWindowVariables(startVars), 
			startExpr.copy(context, null, false, nestedPredCtxStack), 
			copyWindowVariables(endVars), 
			endExpr.copy(context, null, false, nestedPredCtxStack), 
			forceEnd, 
			onNewStart,  
			ForseqIterator.ORDER_MODE_NONE, // TODO: order mode not used yet
			loc
		);
	}
}

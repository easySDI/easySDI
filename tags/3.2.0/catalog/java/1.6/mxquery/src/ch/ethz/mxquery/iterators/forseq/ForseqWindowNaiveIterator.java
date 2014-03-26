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

//import java.util.List;

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
import ch.ethz.mxquery.util.IntegerList;

/**
 * The first naive implementation of the FORSEQ Window
 * @author Tim Kraska (<a href="mailto:mail@tim-kraska.de">mail@tim-kraska.de</a>)
 *
 */
public final class ForseqWindowNaiveIterator extends ForseqWindowIterator {

	private IntegerList openWindows;
	
	private IntegerList processedWindows = null;
	
	private int currentWindow;
	 
	
	public ForseqWindowNaiveIterator(Context ctx, int windowType, QName var, TypeInfo t, XDMIterator seq, WindowVariable[] startVars, XDMIterator startExpr,  WindowVariable[] endVars, XDMIterator endExpr, boolean forceEnd, boolean onNewStart, int orderMode, QueryLocation location) throws MXQueryException {
		super(ctx, windowType, var, t, seq, startVars, startExpr,  endVars, endExpr, forceEnd, onNewStart, orderMode,location);
		if (seq == null || var == null || startExpr == null || (endExpr == null && !onNewStart)) {
			throw new IllegalArgumentException();
		}
	
		openWindows = new IntegerList();
		processedWindows = new IntegerList();		
	}

	/**
	 * Additional comment: The order by endstart is already guranteed by the way the open windows are handled
	 */
	protected Window assignWindow(boolean startNewWhenActive, boolean closeWindowWhenMatch) throws MXQueryException {
		while(!endOfStream){
			//Return all open window for onNewStart but not the new open one
			if(onNewStart){
				if (processedWindows.size() < (openWindows.size() -1)){
					return checkOpenWindows(currentPosition -1, closeWindowWhenMatch, true);					
				}
			}else{
				if (!onNewStart && processedWindows.size() != openWindows.size()){
					Window iter = checkOpenWindows(currentPosition, closeWindowWhenMatch, false);
					if(iter != null){
						return iter;
					}
				}
			}
			increaseCurrentPosition();
			currentWindow=0;
			if(!endOfStream){
				if(!closeWindowWhenMatch){
					processedWindows.clear();
				}
				if(startNewWhenActive || openWindows.size() == 0 || onNewStart ){
					if(checkStartExpr(currentPosition)){
						openWindows.add(currentPosition);
					}
				}
			}
			
		}
		if(!forceEnd){
			//Make sure that window at the end of the stream are not binded twice
			if(!closeWindowWhenMatch){
				processedWindows.clear();
				removeOpenWindows(processedWindows);
			}
			Window iter = checkOpenWindows(currentPosition-1, true, true);
			return iter;
		}else{
			return null;
		}
	}
	


	/**
	 * Checks all open windows. If for a specific window the end expression matches the window is returned
	 * @param endPosition
	 * @param forceToClose
	 * @param closeWindows If this is true, make sure the processedWindows list is initialised
	 * @return
	 */
	protected Window checkOpenWindows(int endPos,  boolean closeWindow, boolean forceToCreate) throws MXQueryException {
		while(currentWindow < openWindows.size()){
			int startPos = openWindows.get(currentWindow);
			if(forceToCreate){
				//Still we have to make sure, that the other windows can use the right context!
				assignVars(startVars, startVarsHolder, startPos);
				Window iter = seq.getNewWindow(startPos, endPos);
				removeOpenWindow(currentWindow);
				
				return iter;
			}else{
				if(checkEndExpr(startPos, endPos)){
					Window iter = seq.getNewWindow(startPos, endPos);
					if(closeWindow){
						removeOpenWindow(currentWindow);
					}else{
						processedWindows.add(currentWindow);
						currentWindow++;
					}
					return iter;
				}else{
					currentWindow++;
				}
			}
		}
		return null;
	}
	
	/**
	 * Removes an open window and checks the position for the garbage colelction
	 * @param windowId
	 */
	protected void removeOpenWindow(int windowId){
		openWindows.remove(windowId);
		//Importand for garbage collection
		if(openWindows.size() > 0){
			setSeqPositionForGarbageCollection(openWindows.get(0));
		}else{
			setSeqPositionForGarbageCollection(currentPosition);
		}
	}
	
	/**
	 * Removes an open window and checks the position for the garbage colelction
	 * @param windowId
	 */
	private void removeOpenWindows(IntegerList array){
		openWindows.remove(array);
		if(openWindows.size() > 0){
			setSeqPositionForGarbageCollection(openWindows.get(0));
		}else{
			setSeqPositionForGarbageCollection(currentPosition);
		}
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
	
	/**
	 * Checks the end expressen for a given start and end position
	 * @param startPosition
	 * @param endPosition
	 * @return
	 * @throws MXQueryException
	 */
	private boolean checkEndExpr(int startPosition, int endPosition)  throws MXQueryException {
		assignVars(startVars, startVarsHolder, startPosition);
		assignVars(endVars, endVarsHolder, endPosition);
		boolean value=false;
		Token endTok = endExpr.next();
		if(endTok.getEventType() == Type.BOOLEAN){
			value = endTok.getBoolean();
		}else{
			throw new RuntimeException("This should never happen because the condition is always a boolean expression.");
		}
		endExpr.reset();
		return value;
	}
	
	
	//TODO: Do this stuff in FFLWOR
	protected void init() throws MXQueryException {
		super.init();
		if(windowType == ForseqIterator.LANDMARK_WINDOW){
			processedWindows = new IntegerList();
		}
	}

	protected void resetImpl()  throws MXQueryException{
		super.resetImpl();
		openWindows.clear();
		if(processedWindows != null){
			processedWindows.clear();
		}
		
	}
		
	public void setContext(Context context, boolean recursive)throws MXQueryException {
		super.setContext(context, recursive);
		this.context = context;
		if(startExpr != null){
			startExpr.setContext(context, true);
		}
		
		if(endExpr != null){
			endExpr.setContext(context, true);
		}
		setResettable(super.isResettable());
	}

	public void setResettable(boolean r) throws MXQueryException{
		super.setResettable(r);
		if(startExpr != null){
			startExpr.setResettable(true);
		}
		
		if(endExpr != null){
			endExpr.setResettable(true);
		}
	}
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator newEndExpr = null;
		XDMIterator newStartExpr = startExpr.copy(context, null, true, nestedPredCtxStack);
		if (endExpr != null)
			newEndExpr = endExpr.copy(newStartExpr.getContext(), null, true, nestedPredCtxStack);
		return new ForseqWindowNaiveIterator(
			context, 
			windowType, 
			var.copy(), 
			varType.copy(), 
			//seq.copy(context, false), 
			subIters[0],
			copyWindowVariables(startVars), 
			newStartExpr, 
			copyWindowVariables(endVars), 
			newEndExpr, 
			forceEnd, 
			onNewStart,  
			ForseqIterator.ORDER_MODE_NONE, // TODO: order mode not used yet
			loc
		);
	}
}

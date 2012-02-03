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
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.model.Iterator;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.model.WindowVariable;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.opt.expr.CompareLiteralIndexSchema;
import ch.ethz.mxquery.opt.expr.DTerm;
import ch.ethz.mxquery.opt.expr.LogicalUnit;
import ch.ethz.mxquery.opt.index.Index;
import ch.ethz.mxquery.opt.index.IndexImpl;
import ch.ethz.mxquery.util.IntegerList;
import ch.ethz.mxquery.util.KXmlSerializer;
import ch.ethz.mxquery.util.Traversable;
//import ch.ethz.mxquery.util.index.DummyIndex;

/**
 * The index version of the Window FORSEQ for sliding and landmark windows.
 * Additionally to the reverse index on windows also memorization is applied.
 * 
 * @author Tim Kraska (<a href="mailto:mail@tim-kraska.de">mail@tim-kraska.de</a>)
 *
 */
public final class ForseqWindowIndexIterator extends ForseqWindowIterator {
	//All Terms from the analysis;
	private DTerm startTerms;
	private DTerm endTerms;	
	
	private Index index = new IndexImpl();
	
	private CompareLiteralIndexSchema[] indexes;
	
	private boolean startEndDependenciesWithoutIndex=false;
	 
	//Turns on some additional debugging System.out printings
	private final static boolean debug = false;
	
	//Memorization constants for the start expression
	private int statusStart = STATUS_START_UNKNOWN;
	
	private static final int STATUS_START_NEVER_TRUE = 0;
	private static final int STATUS_START_ALWAYS_TRUE = 1;
	private static final int STATUS_START_UNKNOWN = 2;
	
	
	//	Memorization constants for the end expression
	private int statusEnd = STATUS_END_UNKNOWN;
	
	private static final int STATUS_END_NEVER_TRUE = 0;
	private static final int STATUS_END_ALWAYS_TRUE= 1;
	private static final int STATUS_END_FOR_CURRENT_POS_TRUE = 2;
	private static final int STATUS_END_UNKNOWN = 3;
	
	private boolean registerStartVars = true;
	private boolean registerEndVars = true;
	
	private IntegerList windowsToProcess=new IntegerList();
	
	private boolean usePrevPositionToProcess;
	
	int callid=0;
		

	public ForseqWindowIndexIterator(Context ctx, int windowType, QName var, TypeInfo t, XDMIterator seq, WindowVariable[] startVars, Iterator startExpr, WindowVariable[] endVars, Iterator endExpr, 
			boolean forceEnd, boolean onNewStart, int orderMode, QueryLocation location, 
			DTerm startTerms, DTerm endTerms, Index index, CompareLiteralIndexSchema [] indexes, boolean startEndDependenciesWithoutIndex) throws MXQueryException {
		super(ctx, windowType, var, t, seq, startVars, startExpr, endVars, endExpr, forceEnd, onNewStart, orderMode, location);
		//FIXME: Because of an error in the index, we restricted - for the moment - the use to
		// sliding windows, although the principle (and already this class) is able to
		// handle landmark windows as well
		if(windowType != SLIDING_WINDOW){
			throw new IllegalArgumentException("This index window iterator allows only sliding windows at the moment until the bug with the index is solved");
		}
		
		this.startTerms = startTerms;
		this.endTerms = endTerms;
		this.index = index;
		this.indexes= indexes;
		this.startEndDependenciesWithoutIndex = startEndDependenciesWithoutIndex;
		
		//First we split the expression to find possibilities to apply an index
//		ExpressionSplitter splitter = new ExpressionSplitter(VarSearchTerms.createVarSearchTerms(this.startVars), VarSearchTerms.createVarSearchTerms(this.endVars));
//		this.startTerms = splitter.splitExpression(this.startExpr);
//		this.endTerms = splitter.splitExpression(this.endExpr);
//
//		List indexSchemas = new ArrayList();
		debugPrintTerms("Terms");
//		for(int i = 0 ; i < endTerms.size(); i++){
//			CTerm cTerm = endTerms.getCTerm(i);
//			CompareLiteralIndexSchema schema = cTerm.getIndexSchema(i);
//			//If it is indexable try to apply an index
//			if(schema != null){
//				//If we have a tumbling window - we know there is only one window open per time
//		 		//if the index is not accepted, you have still to check the predicates
//				if(windowType != TUMBLING_WINDOW && this.index.registerIndex(schema)){
//					indexSchemas.add(schema);
//					cTerm.setIndexed(true);
//				}else{
//					startEndDependenciesWithoutIndex = true;
//				}
//			}else{
//				if(cTerm.getDependency() == CTerm.DEPENDENCY_STARTEND){
//					startEndDependenciesWithoutIndex = true;
//				}
//			}
//		}	
//		index.compileIndex();
//		
//		this.indexes = new CompareLiteralIndexSchema[indexSchemas.size()];
//		for(int i = 0 ; i < indexSchemas.size();i++){
//			this.indexes[i] = (CompareLiteralIndexSchema)indexSchemas.get(i);
//		}

		debugPrintIndexes("CONSTRUCTION_Indexes");
		
	}
	
	/**
	 * Checks the start expression for a current position
	 * @param startPosition
	 * @return
	 * @throws MXQueryException
	 */
	private boolean checkStartExpr(int startPosition)  throws MXQueryException {
		debugPrintTerms("startExpr");
		switch(startTerms.getResult()){
		case LogicalUnit.RESULT_TRUE:
		return true;
		case LogicalUnit.RESULT_FALSE:
			throw new RuntimeException("If the startExpression is always false, we don't produce windows at all");
		case LogicalUnit.RESULT_UNKNOWN:
			assignVars(startVars, startVarsHolder, startPosition);
			registerStartVars=false;
			int result = startTerms.evaluate(LogicalUnit.DEPENDENCY_START);
			startTerms.reset(LogicalUnit.DEPENDENCY_NO);
			switch (result){
			case LogicalUnit.RESULT_TRUE:
				return true;
			case LogicalUnit.RESULT_FALSE:
				return false;
			default:
				throw new RuntimeException("That should never happen");
			}
		default:
			throw new RuntimeException("That should never happen");
		}
	}
	
	/**
	 * Registers the values in the index
	 * @param startPosition
	 * @throws MXQueryException
	 */
	private void registerValueAtIndex(int startPosition) throws MXQueryException {
		index.index(startPosition);
		if(registerStartVars){
			registerStartVars=false;
			assignVars(startVars, startVarsHolder, startPosition);
		}
		for(int i  = 0; i < indexes.length; i++){
			//Depending on if it is a single value a sequence of values, differnt
			//index techniques are used
			if(indexes[i].isSimpleValueIndex()){
				index.index(indexes[i], indexes[i].getStartTokens(), startPosition);				
			}else{
				index.index(indexes[i], indexes[i].getStartValues(), startPosition);
			}
			indexes[i].resetStartPart();
		} 
		
	}
	

	protected Window assignWindow(boolean startNewWhenActive, boolean closeWindowWhenMatch) throws MXQueryException {
		
		while(!endOfStream || windowsToProcess.size() > 0){
			//First return outstanding windows
			if(windowsToProcess.size() > 0){
				// for newStart, the start variables need to be set back, 
				// since checking the end condition brings them to the next binding
				if (onNewStart)
					registerStartVars=true;
				Window iter = produceWindow(windowsToProcess.remove(0));
				//if we have more than one startVar we have to set it.
				this.registerStartVars = true;
				return iter;
			}else{
				//Check the end condition for open windows
				checkOpenWindows(currentPosition, false, closeWindowWhenMatch);
				if(windowsToProcess.size() > 0){
					usePrevPositionToProcess = false;
					return produceWindow(windowsToProcess.remove(0));
				}
				increaseCurrentPosition();
			}
			if(!endOfStream){
				if((startNewWhenActive || index.size() == 0) && checkStartExpr(currentPosition)){
					if(onNewStart){
						usePrevPositionToProcess = true;
						checkOpenWindows(currentPosition-1, true, true);
					}
					registerValueAtIndex(currentPosition);
				}			
			}else{
				if(!forceEnd){
					usePrevPositionToProcess = true;
					checkOpenWindows(currentPosition-1, true, true);
				}
			}
		}
		return null;
	}
	
	/**
	 * All open windows are checked.
	 * @param endPos
	 * @param forceToCreate
	 * @param closeWindows
	 * @throws MXQueryException
	 */
	private void checkOpenWindows(int endPos, boolean forceToCreate, boolean closeWindows) throws MXQueryException {
		callid++;
		//If there are no open Windows we can not close any
		if(index.size() == 0){
			return;
		}
		if(forceToCreate){
			if(closeWindows) {
				windowsToProcess.addAll(index.getAndRemoveAll());
			}else{
				windowsToProcess.addAll(index.getAll());
			}
		}
		
		//Depending on the end status we create all windows or not
		switch(statusEnd){
		case STATUS_END_ALWAYS_TRUE:
			if(closeWindows) {
				windowsToProcess.addAll(index.getAndRemoveAll());
			}else{
				windowsToProcess.addAll(index.getAll());
			}
			return;
		case STATUS_END_NEVER_TRUE:
			return;
		}
		
		endTerms.reset(LogicalUnit.DEPENDENCY_NO);
		if(registerEndVars){
			assignVars(endVars, endVarsHolder, endPos);
			registerEndVars = false;
		}
		//If the endExpr only depends on the end variable than all open windows can be closed
		int result = endTerms.evaluate(LogicalUnit.DEPENDENCY_END);
		if (result == LogicalUnit.RESULT_TRUE){
			statusEnd = STATUS_END_FOR_CURRENT_POS_TRUE;
			if(closeWindows) {
				windowsToProcess.addAll(index.getAndRemoveAll());
			}else{
				windowsToProcess.addAll(index.getAll());
			}
			return;
		// if the end expression evaluates to FALSE and not unknown, we can do nothing anymore
		}else if(result == LogicalUnit.RESULT_FALSE){
			return;
		}
		
		//If we have end conditions which depend on both start and end, we try first all available indexes
		IntegerList startPos=null;
		for(int i = 0; i < indexes.length;i++){
			if(indexes[i].isSimpleValueIndex()){
				if(closeWindows){
					startPos = index.retreiveAndRemove(indexes[i], indexes[i].getEndTokens());
				}else{
					startPos = index.retreive(indexes[i], indexes[i].getEndTokens());
				}
			}else{ 
				if(closeWindows){
					startPos = index.retreiveAndRemove(indexes[i], indexes[i].getEndValues());
				}else{
					startPos = index.retreive(indexes[i], indexes[i].getEndValues());
				}
			}
			indexes[i].resetEndPart();
			if(startPos != null){
				registerStartVars = true;
				windowsToProcess.addAll(startPos);
				return;
			}
		}
		
		//If the index weren't successfull we still need to check everything else
		if(startEndDependenciesWithoutIndex){
			for(int i=0; i < index.size(); i++){
				int start = index.get(i);
				assignVars(startVars,startVarsHolder, start );
				registerStartVars=false;
				if(endTerms.evaluate(LogicalUnit.DEPENDENCY_STARTEND) == LogicalUnit.RESULT_TRUE){
					if(closeWindows){
						windowsToProcess.add(index.getAndRemove(i));
					}else{
						windowsToProcess.add(index.get(i));
					}
					endTerms.reset(LogicalUnit.DEPENDENCY_NO);
					return;
				}
				endTerms.reset(LogicalUnit.DEPENDENCY_START);
			}
		}
	}
	
	protected void increaseCurrentPosition() throws MXQueryException {
		super.increaseCurrentPosition();
		endTerms.reset(LogicalUnit.DEPENDENCY_NO);
		if(statusEnd == STATUS_END_FOR_CURRENT_POS_TRUE){
			statusEnd = STATUS_END_UNKNOWN;
		}
		registerEndVars = true;
		registerStartVars = true;
	}
	
	/**
	 * Produces the window for a specific start position up to the current end position.
	 * @param startPos
	 * @return
	 * @throws MXQueryException
	 */
	private Window produceWindow(int startPos) throws MXQueryException {
		
		if(registerStartVars){
			assignVars(startVars, startVarsHolder, startPos);
			registerStartVars=false;
		}
		if(registerEndVars){
			assignVars(endVars, endVarsHolder, startPos);
			registerEndVars = false;
		}
		
		//System.out.println("Produce window : ["+startPos+","+currentPosition+"]");
		Window iter;
		if(usePrevPositionToProcess){
			iter =  seq.getNewWindow(startPos, currentPosition-1);
		}else{
			iter = seq.getNewWindow(startPos, currentPosition);
		}
		
		
		int lowId = index.get(); 
		if (lowId > 0){
			setSeqPositionForGarbageCollection(min(startPos, lowId));
		}else{
			setSeqPositionForGarbageCollection(startPos-1);
			
		}
		return iter;
	}
	
	 
	protected void init() throws MXQueryException {
		super.init();
		int result = startTerms.evaluate(DTerm.DEPENDENCY_NO);
		switch(result){
		case DTerm.RESULT_TRUE:
			statusStart = STATUS_START_ALWAYS_TRUE;
			break;
		case DTerm.RESULT_FALSE:
			statusStart = STATUS_START_NEVER_TRUE;
			break;
		}
		
		result= endTerms.evaluate(DTerm.DEPENDENCY_NO);
		switch(result){
		case DTerm.RESULT_TRUE:
			statusEnd = STATUS_END_ALWAYS_TRUE;
			break;
		case DTerm.RESULT_FALSE:
			statusEnd = STATUS_END_NEVER_TRUE;
			break;
		}
		
		//In this cases no windows are produced
		//the type is checked in next from ForseqWindowIterator
		//thats the reason why it workes like this
		if (statusStart == STATUS_START_NEVER_TRUE){
			currentToken = Token.END_SEQUENCE_TOKEN;
		}
		if(statusEnd == STATUS_END_NEVER_TRUE && forceEnd){
			currentToken = Token.END_SEQUENCE_TOKEN;
		}
		debugPrintTerms("AfterInit");
	}
	
	protected void resetImpl() throws MXQueryException {
		super.resetImpl();
		freeResources(true);
	}
	
	
	
	/*
	 * ######################################################################
	 * ###########  Helper Functions for Iterator Tree Traversal ############
	 * ######################################################################
	 */
	
	protected void freeResources(boolean restartable) throws MXQueryException {
		super.freeResources(restartable);
		startTerms.reset(DTerm.RESET);
		endTerms.reset(DTerm.RESET);
	}

	private void debugPrintTerms(String tagName){
		if(debug){
			try{
				KXmlSerializer serializer = KXmlSerializer.getOutputKXmlSerializer();
				serializer.startTag(null , tagName);
				debugPrint(startTerms, "startTerms", serializer);
				debugPrint(endTerms, "endTerms", serializer);
				serializer.endTag(null , tagName);
				serializer.flush();
			}catch(Exception err){
				throw new RuntimeException(err);
			}
		}
	}
	
	private void debugPrintIndexes(String tagName){
		if(debug){
			try{
				KXmlSerializer serializer = KXmlSerializer.getOutputKXmlSerializer();
				serializer.startTag(null , tagName);
				for(int i = 0; i < indexes.length; i++){
					debugPrint(indexes[i], "Index", serializer);
				}
				serializer.endTag(null , tagName);
				serializer.flush();
			}catch(Exception err){
				throw new RuntimeException(err);
			}
		}
	}
	
	
	private void debugPrint(Traversable unit, String tagName, KXmlSerializer serializer){
		if(debug){
			try{
				serializer.startTag(null , tagName);
				unit.traverse(serializer);
				serializer.endTag(null , tagName);
				serializer.flush();
			}catch(Exception err){
				throw new RuntimeException(err);
			}
		}
	}
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
//		Iterator newEndExpr = null;
//		Iterator newStartExpr = startExpr.copy(context, null, true);
//		if (endExpr != null)
//			newEndExpr = endExpr.copy(newStartExpr.getContext(), null, true);
		// TODO: Implement copying of DTerm, Index, IndexSchema
//		ForseqWindowIndexIterator ret = new ForseqWindowIndexIterator(
//				context, 
//				windowType, 
//				var.copy(), 
//				varType.copy(), 
//				//seq.copy(context, false), 
//				subIters[0],
//				copyWindowVariables(startVars), 
//				newStartExpr, 
//				copyWindowVariables(endVars), 
//				newEndExpr, 
//				forceEnd, 
//				onNewStart,  
//				ForseqIterator.ORDER_MODE_NONE, // TODO: order mode not used yet
//				loc, (DTerm)startTerms.clone(), (DTerm)endTerms.clone(), index, indexes, startEndDependenciesWithoutIndex);
		
		throw new MXQueryException(ErrorCodes.A0002_EC_NOT_SUPPORTED,"Copying of ForseqIndexIterators not yet supported",loc);
		//return ret;
		}
}
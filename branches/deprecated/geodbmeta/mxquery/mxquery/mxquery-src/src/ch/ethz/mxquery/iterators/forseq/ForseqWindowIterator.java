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

import java.util.Enumeration;
import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.contextConfig.XQDynamicContext;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.iterators.TokenIterator;
import ch.ethz.mxquery.model.VariableHolder;
import ch.ethz.mxquery.model.WindowVariable;
import ch.ethz.mxquery.model.XDMIterator;

import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.util.KXmlSerializer;

/**
 * Basic helper class for all windows. As every window implementation has some stuff in common,
 * this class was introduced
 * @author Tim Kraska (<a href="mailto:mail@tim-kraska.de">mail@tim-kraska.de</a>)
 *
 */
public abstract class ForseqWindowIterator extends ForseqIterator {
//	TODO: Set psotion right! Attentiion is not currentPosition!
	
 
	protected WindowVariable[] startVars;
	protected VariableHolder[] startVarsHolder;
	protected XDMIterator startExpr;
	protected WindowVariable[] endVars;
	protected VariableHolder[] endVarsHolder;
	protected XDMIterator endExpr;
	protected boolean forceEnd=false;
	protected boolean onNewStart=false;
	
	private int garbageCollectionMode = GARBAGAE_COLLECTION_NONE;
	
	private static final int GARBAGAE_COLLECTION_NONE=0;
	private static final int GARBAGAE_COLLECTION_PREV=1;
	private static final int GARBAGAE_COLLECTION_CURRENT=2;	
	
	/**
	 * Creates a new Window Iterator
	 * @param windowType Type of the window
	 * @param var Variable QName
	 * @param type Type of the variable (not supported at the moment)
	 * @param seq The source iterator
	 * @param startVars List of all start WindowVariables
	 * @param startExpr The start expression iterator
	 * @param endVars List of all end  WindowVariables
	 * @param endExpr The end expression iterator
	 * @param forceEnd If a force of the end should be applied
	 * @param onNewStart If the end expressions is newstart
	 * @param orderMode Order mode of the iterator
	 */
	public ForseqWindowIterator(Context ctx, int windowType, QName var, TypeInfo type, XDMIterator seq, WindowVariable[] startVars, XDMIterator startExpr,  WindowVariable[] endVars, XDMIterator endExpr, boolean forceEnd, boolean onNewStart, int orderMode, QueryLocation location) throws MXQueryException {
		super(ctx, windowType, var, type, seq, orderMode, location);
		if (startExpr == null || (endExpr == null && !onNewStart)) {
			throw new IllegalArgumentException();
		}
		this.startVars = startVars ;
		this.startExpr = startExpr;
				
		//Verbesserungspotential!
		this.endVars = endVars; 
		if (endExpr != null)
			this.endExpr = endExpr;
		
		this.forceEnd = forceEnd;
		this.onNewStart = onNewStart;
		garbageCollectionMode = min(checkGarbageCollectionMode(startVars), checkGarbageCollectionMode(endVars));
	}

	/**
	 * Based on the window type the values for startNewWhenActive and closeWindowWhenMatch for assignWindow(..) is called
	 */
	protected Window assignWindow() throws MXQueryException {
		switch(windowType){
		case TUMBLING_WINDOW:
			return  assignWindow(false, true);
		case SLIDING_WINDOW:
			return assignWindow(true, true);
		case LANDMARK_WINDOW:
			return assignWindow(true, false);
			default:
		throw new RuntimeException("That should never happen");
		}
	}
	
	/**
	 * This method returns a new window representing the next binding
	 * @param startNewWhenActive Defines if a new window should be open if already one is open
	 * @param closeWindowWhenMatch Defines if a window should be closed if the end expr matches or if it shoul kept alive
	 * @return Iterator representing the next window
	 * @throws MXQueryException
	 */
	protected abstract Window assignWindow(boolean startNewWhenActive, boolean closeWindowWhenMatch) throws MXQueryException;
	

	/**
	 * Assigns window vars to the context
	 * @param vars
	 * @param position
	 * @throws MXQueryException
	 */
	protected void assignVars(WindowVariable[] vars, VariableHolder[] varHolders, int position)throws MXQueryException {
		if(vars != null){
			for(int i =0; i < vars.length; i++){
				if(vars[i].getType() != WindowVariable.WINDOW_VAR_POSITION){
					Window window = (Window)varHolders[i].getIter();
					if(window != null)
						window.destroyWindow();
				}
				switch(vars[i].getType()){
				case WindowVariable.WINDOW_VAR_CUR_ITEM:
					varHolders[i].setIter(seq.getItem(position));
					break;
				case WindowVariable.WINDOW_VAR_NEXT_ITEM:
					varHolders[i].setIter(seq.getItem(position+1));
					break;
				case WindowVariable.WINDOW_VAR_PREV_ITEM:
					varHolders[i].setIter(seq.getItem(position-1));
					break;
				case WindowVariable.WINDOW_VAR_POSITION:
					varHolders[i].setIter(new TokenIterator(context, position, Type.INTEGER, loc));
					break;
				}
			}
		}
	}
	
	
	

	
	protected void resetImpl() throws MXQueryException {
		super.resetImpl();
		freeResources(true);
	}

	protected void freeResources(boolean restartable) throws MXQueryException {
		for(int i = 0; i < startVarsHolder.length;i++){
			if(startVars[i].getType() != WindowVariable.WINDOW_VAR_POSITION){
				Window window = (Window)startVarsHolder[i].getIter();
				if(window != null){
					window.destroyWindow();
				}
			}
			startVarsHolder[i].setIter(null);
		}
		for(int i = 0; i < endVarsHolder.length;i++){
			if(endVars[i].getType() != WindowVariable.WINDOW_VAR_POSITION){
				Window window = (Window)endVarsHolder[i].getIter();
				if(window != null){
					window.destroyWindow();
				}
			}
			endVarsHolder[i].setIter(null);
		}
	}

		
	public void setContext(Context context, boolean recursive) throws MXQueryException {
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

	public void setResettable(boolean r) throws MXQueryException {
		super.setResettable(r);
		if(startExpr != null){
			startExpr.setResettable(true);
		}
		
		if(endExpr != null){ 
			endExpr.setResettable(true);
		}
	}
	
	/**
	 * Increases the currentPosition and sets endOfStream
	 * This method is called once in init!
	 *
	 */
	protected void increaseCurrentPosition() throws MXQueryException {
		currentPosition++;
		if (!seq.hasItem(currentPosition)){
			endOfStream=true;
		} 
	}
	
	/**
	 * This method is called for the garbage collection
	 *
	 */
	protected void setSeqPositionForGarbageCollection(int position){
		switch(garbageCollectionMode){
		case GARBAGAE_COLLECTION_CURRENT:
			seq.setPosition(position);
			break;
		case GARBAGAE_COLLECTION_PREV:
			seq.setPosition(position-1);
			break;
		case GARBAGAE_COLLECTION_NONE:
			break;
		}
	}
	
	/**
	 * Checks how greedy the garbage collectin can be. 
	 * @param vars
	 * @return
	 */
	private int checkGarbageCollectionMode(WindowVariable[] vars) {
		int garbageMode = GARBAGAE_COLLECTION_CURRENT;
		if(vars !=null){
			for (int i=0;i<vars.length;i++)
			{
				WindowVariable var = vars[i];
				switch(var.getType()){
				case WindowVariable.WINDOW_VAR_POSITION:
				case WindowVariable.WINDOW_VAR_CUR_ITEM:
				break;
				case WindowVariable.WINDOW_VAR_NEXT_ITEM:
					break;
				case WindowVariable.WINDOW_VAR_PREV_ITEM:
					garbageMode = min(garbageMode, ForseqWindowIterator.GARBAGAE_COLLECTION_PREV);
					break;
				}
			}
		}
		return garbageMode;
	}
	

	
	protected int min(int a, int b){
		
		if(a < b ){
			return a;
		}else{
			return b;
		}
	}
	
	/*
	 * ######################################################################
	 * ###########  Helper Functions for Iterator Tree Traversal ############
	 * ######################################################################
	 */

	protected KXmlSerializer createIteratorStartTag(KXmlSerializer serializer) throws Exception {
		super.createIteratorStartTag(serializer);
		switch(windowType){
		case GENERAL_WINDOW:
			serializer.attribute(null, "type", "GENERAL_WINDOW");
			break;
		case TUMBLING_WINDOW:
			serializer.attribute(null, "type", "TUMBLING_WINDOW");
			break;
		case SLIDING_WINDOW:
			serializer.attribute(null, "type", "SLIDING_WINDOW");
			break;
		case LANDMARK_WINDOW:
			serializer.attribute(null, "type", "LANDMARK_WINDOW");
			break;
		}
		serializer.attribute(null, "varname", "$" + this.var.toString());
		serializer.attribute(null, "onNewStart", "" + this.onNewStart);
		serializer.attribute(null, "forceEnd", "" + this.forceEnd);
		return serializer;
	}

	public XDMIterator[] getAllSubIters() {
		Vector vector = new Vector();
		for(int i = 0; i < subIters.length;i++){
			vector.addElement(subIters[i]);
		}
		if(this.startExpr != null){
			vector.addElement(startExpr);
		}
		if(this.endExpr != null){
			vector.addElement(endExpr);
		}
		XDMIterator[] arr = new XDMIterator[vector.size()];
		Enumeration iter =  vector.elements();
		int i = 0;
		while(iter.hasMoreElements()){
			arr[i] = (XDMIterator)iter.nextElement();
			i++;
		}
		return arr;
	}

	public KXmlSerializer traverseIteratorTree(KXmlSerializer serializer) throws Exception {
		createIteratorStartTag(serializer);
		serializer.startTag(null, "source");
		subIters[0].traverse(serializer);
		serializer.endTag(null, "source");
		traverseVars(startVars, "startvar", serializer);
		traverseVars(endVars, "endvar",serializer);
		if(startExpr != null){
			serializer.startTag(null, "startExpr");
			startExpr.traverseIteratorTree(serializer);
			serializer.endTag(null, "startExpr");
		}
		if(endExpr != null){
			serializer.startTag(null, "endExpr");
			endExpr.traverseIteratorTree(serializer);
			serializer.endTag(null, "endExpr");
		}
		createIteratorEndTag(serializer);
		return serializer;
	}
	
	private void traverseVars(WindowVariable[] vars, String type, KXmlSerializer serializer)throws Exception {
		if(vars !=null){
			for(int i = 0; i < vars.length;i++){
				serializer.startTag(null, type);
				switch(vars[i].getType()){
				case WindowVariable.WINDOW_VAR_CUR_ITEM:
					serializer.attribute(null, "type", "curItem");
					break;
				case WindowVariable.WINDOW_VAR_NEXT_ITEM:
					serializer.attribute(null, "type", "nextItem");
					break;
				case WindowVariable.WINDOW_VAR_PREV_ITEM:
					serializer.attribute(null, "type", "prevItem");
					break;
				case WindowVariable.WINDOW_VAR_POSITION:
					serializer.attribute(null, "type", "position");
					break;
				}
				serializer.attribute(null, "name", "$" + vars[i].getQName().getLocalPart());
				serializer.endTag(null, type);
			}
		}
	}

	protected void init() throws MXQueryException {
		if(firstInit){
			XQDynamicContext innermostContext = getInnermostContext();
			updateVarHolders(innermostContext);
			firstInit = false;
		}
		super.init();
	}

	public Context getInnermostContext() {
		Context innermostContext = startExpr.getContext();
		if (endExpr != null)
			innermostContext = endExpr.getContext();
		return innermostContext;
	}
	/**
	 * Set the variable holder using this context
	 * Access is public, since for parallel execution each new binding goes into new varHolders
	 * @param ctx Context from which to resolve the variables into varHolders 
	 */
	public void updateVarHolders(XQDynamicContext ctx) throws MXQueryException{
		varHolder = ctx.getVariable(var);
		startVarsHolder = new VariableHolder[startVars.length];
		for(int i = 0; i < startVarsHolder.length; i++){
			startVarsHolder[i] = ctx.getVariable(startVars[i].getQName());
		}
		endVarsHolder = new VariableHolder[endVars.length];
		for(int i = 0; i < endVars.length; i++){
			endVarsHolder[i] = ctx.getVariable(endVars[i].getQName());
		}
	}
	
	/**
	 * Get the name of all variables (possibly) bound by this Forseq window iterator instance
	 * @return a sequence of variable names
	 */
	
	public QName[] getForseqVars() {
		QName [] ret = new QName[startVars.length + endVars.length +1];
		for (int i=0;i<startVars.length;i++)
			ret[i] = startVars[i].getQName();
		for (int i=0;i<endVars.length;i++)
			ret[startVars.length+i] = endVars[i].getQName();
		ret[ret.length-1] = var;
		return ret;
	}
	
	protected static WindowVariable [] copyWindowVariables(WindowVariable[] vars) throws MXQueryException {
		WindowVariable [] result = new WindowVariable [vars.length];
		for (int i=0; i<vars.length; i++) {
			result[i] = vars[i].copy();
		}
		return result;
	}	
	

}

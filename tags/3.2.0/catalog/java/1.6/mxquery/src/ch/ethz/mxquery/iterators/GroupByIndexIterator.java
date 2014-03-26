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
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.iterators.forseq.ForseqIterator;
import ch.ethz.mxquery.iterators.forseq.ForseqWindowIndexIterator;
import ch.ethz.mxquery.model.Constants;
import ch.ethz.mxquery.model.Iterator;
import ch.ethz.mxquery.model.VariableHolder;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.opt.index.Index;
import ch.ethz.mxquery.opt.index.IndexImpl;
import ch.ethz.mxquery.opt.index.SimpleIndexSchema;
import ch.ethz.mxquery.util.IntegerList;


/**
 * 
 * @author Tim Kraska
 * 
 */
public final class GroupByIndexIterator extends Iterator {

	private XDMIterator current = null;
	
	private Token currentToken;	
	
	private boolean firstInit = true;
	
	private VariableHolder targetValueHolder;//, sourcValueHolder ;
	
	private VariableHolder[] byTargetsValueHolders;

	private QName sourceQName, targetQName;

	private QName[] byTargets;

	private XDMIterator where;
	
	private VariableIterator sourceIter;
	
	private XDMIterator[] bySources, lets, flworSubs;
	
	private Window currentTarget;

	private FFLWORIterator flwor;

	private Window var;

	private int nestedLevel = 0;

	private int fors = 0;

	// currently processed group id
	private int currGroup = 0;
	

	
	private Window blockGarbageCollection;

	private SimpleIndexSchema schema = null;
	private Index index = null;
	
	public GroupByIndexIterator(Context ctx, QName sourceQName,
			QName targetQName, XDMIterator[] bySources, QName[] byTargets,
			XDMIterator[] lets, XDMIterator where, QueryLocation location) throws MXQueryException {
		this(ctx, null, sourceQName, targetQName, bySources, byTargets, lets, where, location);
	}

	/**
	 * Creates a new group by iterator which applies an index for the values to group for 
	 * @param flwor
	 * @param sourceQName
	 * @param targetQName
	 * @param bySources
	 * @param byTargets
	 * @param lets
	 * @param where
	 * @throws MXQueryException
	 */
	public GroupByIndexIterator(Context ctx, FFLWORIterator flwor, QName sourceQName,
			QName targetQName, XDMIterator[] bySources, QName[] byTargets,
			XDMIterator[] lets, XDMIterator where, QueryLocation location) throws MXQueryException {
		super(ctx, new XDMIterator[] { flwor },location);
		if (sourceQName == null || targetQName == null
				|| bySources == null || bySources.length < 1
				|| byTargets == null || byTargets.length < 1) {
			throw new IllegalArgumentException();
		}
		this.flwor = flwor;
		flworSubs = flwor.getSubIters();
		this.sourceQName = sourceQName;
		this.sourceIter = new VariableIterator(ctx, sourceQName, false,location);
		
		this.targetQName = targetQName;
		this.bySources = new XDMIterator[bySources.length];
		for(int i=0;i < bySources.length;i++){
			this.bySources[i] = bySources[i];
		}
		this.byTargets = byTargets;
		this.lets = lets;
		this.where = where;
		if (flworSubs[flworSubs.length - 1] instanceof ForIterator) {
			fors++;
		} else if (flworSubs[flworSubs.length - 1] instanceof LetIterator) {
		} else if (flworSubs[flworSubs.length - 1] instanceof ForseqIterator) {
			fors++;
		} else if (flworSubs[flworSubs.length - 1] instanceof ForseqWindowIndexIterator) {
			fors++;
		} else {
			System.err.println("ERROR IN GROUPBYITERATOR (sorry)");
		}
	}
	
	public void setFLWOR(FFLWORIterator flwor){
		super.subIters =  new XDMIterator[] { flwor };
	}
	
	public void setContext(Context context, boolean recursive) throws MXQueryException {
		flwor.setContext(context, true);

		// set groupBy context to the most inner for/let context
		if (flworSubs[flworSubs.length - 1] instanceof ForIterator) {
			fors++;
			this.context = ((ForIterator) flworSubs[flworSubs.length - 1])
					.getContext();
		} else if (flworSubs[flworSubs.length - 1] instanceof LetIterator) {
			this.context = ((LetIterator) flworSubs[flworSubs.length - 1])
					.getContext();
		} else if (flworSubs[flworSubs.length - 1] instanceof ForseqIterator) {
			fors++;
			this.context = ((ForseqIterator) flworSubs[flworSubs.length - 1])
					.getContext();
		} else if (flworSubs[flworSubs.length - 1] instanceof ForseqWindowIndexIterator) {
			fors++;
			this.context = ((ForseqWindowIndexIterator) flworSubs[flworSubs.length - 1])
					.getContext();
		} else {
			System.err.println("ERROR IN GROUPBYITERATOR (sorry)");
		}

		sourceIter.setContext(this.context, true);
		sourceIter.setResettable(true);
		for (int i = 0; i < bySources.length; i++) {
			bySources[i].setContext(this.context, true);
			if (fors > 0) {
				bySources[i].setResettable(true);
			}
		}

		for (int i = 0; i < lets.length; i++) {
			lets[i].setContext(this.context, true);
		}

		if (where != null) {
			where.setContext(this.context, true);
			if (fors > 0) {
				where.setResettable(true);
			}
		}
	}

	
	
	/* (non-Javadoc)
	 * @see ch.ethz.mxquery.model.iterators.Iterator#setResettable(boolean)
	 */
	public void setResettable(boolean r) throws MXQueryException {
		super.setResettable(r);
		sourceIter.setResettable(true);
		for (int i = 0; i < bySources.length; i++) {
			if (fors > 0) {
				bySources[i].setResettable(true);
			}
		}

		if (where != null) {
			where.setContext(this.context, true);
			if (fors > 0) {
				where.setResettable(true);
			}
		}
	}

	/**
	 * The complete grouping according to values is done during the init. 
	 * @throws MXQueryException
	 */
	public void init() throws MXQueryException {
		//Can be optimized!!!
		if(firstInit){
			firstInit = false;
			//sourcValueHolder = context.getVariable(sourceQName);
			targetValueHolder = context.getVariable(targetQName);
			byTargetsValueHolders = new VariableHolder[byTargets.length];
			for(int i = 0; i < byTargets.length;i++){
				byTargetsValueHolders[i] = context.getVariable(byTargets[i]);
			}
		}
		if(flwor == null){
			throw new IllegalArgumentException("Null not for flwor allowed");
		}
		// create index schema
		int IndexNo = 1;
		schema = new SimpleIndexSchema(IndexNo);
		schema.setGroupByIndex(true);

		// initialize grouping parameter variables
		for (int i = 0; i < byTargets.length; i++) {
			TokenIterator atom = new TokenIterator(context, 0, Type.INTEGER,loc);
			atom.setResettable(true);
			byTargetsValueHolders[i].setIter(atom);
			schema.registerValue(Constants.COMP_EQ,
					Constants.COMP_VALUE, i + "");
		}

		// create index
		index = new IndexImpl();
		index.registerIndex(schema);
		index.compileIndex();

		if (flworSubs.length != 1) {
			throw new MXQueryException(ErrorCodes.A0002_EC_NOT_SUPPORTED, "More than one for/let/forseq currently not supported with Group By", loc);
		} else {
			if (doNextBinding()) {
				//This is necassary to block the garbage collection to this point
				var = sourceIter.getUnderlyingIterator();
				blockGarbageCollection = var.getNewWindow(1,1);
				
				// create groups
				while (true) {
					// bind for variable
					if (testWhereClause()) {
						// get variable content
						
						// get current value of grouping parameter
						Token tokens[] = new Token[bySources.length];
						Token t = null;
	
						for (int gparam = 0; gparam < bySources.length; gparam++) {
							
							t = bySources[gparam].next();
							bySources[gparam].reset();
	//						System.out.println(t.getName() + ":" + t.getInt());
							tokens[gparam] = t;
						}
						
						
						int nodId = var.getStartNode();
						index.index(schema, tokens, nodId);
						sourceIter.reset();
						if (!doNextBinding()) {
							break;
						}
						var = sourceIter.getUnderlyingIterator();
					}
				} // while
			}
		}
		// set current to the flwor's return expression
		current = flwor.getReturnExpr();
		called++;
	}

	/**
	 * Binds the next group to a variable
	 * @return
	 * @throws MXQueryException
	 */
	private boolean bindNextGroup()  throws MXQueryException{
		//get all groups
		Vector groups = index.getGroups(schema);
		Vector values = index.getValues(schema);
		int groupSize = groups.size();
		current.reset();
		// currGroup < groups.size()
		while (currGroup < groupSize) {
			
			IntegerList iList = (IntegerList) groups.elementAt(currGroup);
			IntegerList valList = (IntegerList) values.elementAt(currGroup);
			currGroup++;
			if(currentTarget != null){
				currentTarget.destroyWindow();
			}
			currentTarget=var.getNewItemWindow(iList);
			// bind grouping variable
			targetValueHolder.setIter(currentTarget);
			// bind variables of grouping parameters
			for (int i = 0; i < byTargets.length; i++) {
				TokenIterator iter = (TokenIterator) byTargetsValueHolders[i].getIter();
				byTargetsValueHolders[i].setIter(new TokenIterator(context, valList.get(i), Type.INTEGER,loc));
				iter.reset();
				//iter.getToken().setInt(valList.get(i));
			}
			if (testGroupByWhereClause()) {
				return true;
			}
		}
		if(currentTarget != null){
			currentTarget.destroyWindow();
		}
		targetValueHolder.setIter(null);

		if(currentTarget != null){
			currentTarget.destroyWindow();
			currentTarget = null;
		}
		current.reset();
		current = null;
		return false;
	}

	public Token next() throws MXQueryException {

		if (called==0 || current == null) {
			init();
			bindNextGroup();
		}
		do{
			if(current == null){
				return Token.END_SEQUENCE_TOKEN;
			}
			currentToken = current.next();
			if(currentToken.getEventType() != Type.END_SEQUENCE){
				return currentToken;
			}else{
				bindNextGroup();
			}
		}while(currentToken.getEventType() == Type.END_SEQUENCE);
		throw new RuntimeException("That should never happen!!!!");
	}

	private boolean testWhereClause() throws MXQueryException {
		XDMIterator whereExpr = flwor.getWhereExpr();

		if (whereExpr == null) {
			return true;
		}

		boolean value = false;
		Token tok = whereExpr.next();
		if (tok.getEventType() == Type.BOOLEAN) {
			value = tok.getBoolean();
		}
		whereExpr.reset();
		return value;
	}

	private boolean testGroupByWhereClause() throws MXQueryException {

		if (where == null) {
			return true;
		}

		boolean value = false;
		Token tok = where.next();
		if (tok.getEventType() == Type.BOOLEAN) {
			value = tok.getBoolean();
		}
		where.reset();
		return value;
	}

	private boolean doNextBinding() throws MXQueryException {
		while (true) {
			Token tok = flworSubs[nestedLevel].next();
			if (tok.getEventType() == Type.END_SEQUENCE) {
				if (nestedLevel == 0) {
					return false;
				} else {
					// This is only importand if the binding is over
					if (flworSubs[nestedLevel].isResettable()) {
						flworSubs[nestedLevel].reset();
					} else {
						nestedLevel = 0;
						return false;
					}
					nestedLevel--;
					// return doNextBinding();
				}
			} else {
				if (nestedLevel == flworSubs.length - 1) {
					if (testWhereClause()) {
						return true;
					}
				} else {
					nestedLevel++;
				}
			}
		}
	}

	protected void resetImpl() throws MXQueryException{
		super.resetImpl();
		freeResources(true);
		nestedLevel = 0;
		currGroup=0;
	}
	
	protected void freeResources(boolean restartable) throws MXQueryException {
		super.freeResources(restartable);
		 if (blockGarbageCollection != null){
			 blockGarbageCollection.destroyWindow();
			 blockGarbageCollection = null;
		 }
		for (int i = 0; i < bySources.length; i++) {
			bySources[i].reset();
		}

		for (int i = 0; i < lets.length; i++) {
			lets[i].reset();
		}
		if(currentTarget != null){
			currentTarget.destroyWindow();
			targetValueHolder.setIter(null);
			currentTarget=null;
		}
		
	}
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		// TODO: special copy implementation?
		return new GroupByIndexIterator(
				context, 
				(FFLWORIterator) flwor.copy(context, null, false, nestedPredCtxStack), 
				sourceQName.copy(), 
				targetQName.copy(), 
				Iterator.copyIterators(context, bySources, nestedPredCtxStack), 
				Iterator.copyQNames(byTargets), 
				Iterator.copyIterators(context, lets, nestedPredCtxStack), 
				where.copy(context, null, false, nestedPredCtxStack),loc);
	}
}



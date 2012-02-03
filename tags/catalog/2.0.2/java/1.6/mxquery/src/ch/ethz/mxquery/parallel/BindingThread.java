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

package ch.ethz.mxquery.parallel;

import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.iterators.PFFLWORIterator;
import ch.ethz.mxquery.iterators.forseq.ForseqWindowEarlyBindingParallel;
import ch.ethz.mxquery.iterators.forseq.ForseqWindowIterator;
import ch.ethz.mxquery.iterators.forseq.ForseqWindowNaiveIterator;
import ch.ethz.mxquery.model.XDMIterator;

public class BindingThread extends Thread {
	private ThreadPool pool;
	private ParallelOutput output;
	
	private PFFLWORIterator pfflwor;
	
	private int bindingCount = 0;
	private boolean done = false;
	
	public BindingThread(ThreadPool pool, ParallelOutput output, PFFLWORIterator pfflwor) {
		this.pool = pool;
		this.output = output;
		this.pfflwor = pfflwor;
		
		output.setBindingThread(this);
		if (ThreadPool.isDebug()) ThreadPool.log(this, "BindingThread initialized");
	}
	
	public void run() {
		try {
			XDMIterator[] subIters = pfflwor.getSubIters();
			
			// Currently only works for a single FORSEQ iterator, extend later
			//for (int i=0; i<subIters.length; i++) {
			if (!(subIters[0] instanceof ForseqWindowNaiveIterator || subIters[0] instanceof ForseqWindowEarlyBindingParallel)) {
				throw new RuntimeException("Parallel Execution Only works with Forseq");
			}
			// insert a new context between PFFLWOR and FORSEQ: "mid"

			if (ThreadPool.isDebug()) ThreadPool.log(this, "Binding Thread started");
			
			ForseqWindowIterator fw = ((ForseqWindowIterator)subIters[0]);
			
			Context midContext = new Context(pfflwor.getContext());
			subIters[0].getContext().setParent(midContext);
			// flatten Variables from FORSEQ into new context
			
			QName [] fsVars = fw.getForseqVars();
			
			midContext.flattenVariablesFrom(fsVars,fw.getInnermostContext());
			
			// make a copy of that context so that we always have a clean starting point
			Context workingContext = midContext.copy();
			subIters[0].getContext().setParent(workingContext);
			fw.updateVarHolders(workingContext);
			
			XDMIterator iter;
			Worker w;
			
			// for each binding
			while (pfflwor.doNextBinding()) {
				if (ThreadPool.isDebug()) ThreadPool.log(this, "Binding " + bindingCount + " done");
				bindingCount++;
				  // copy the return (later also where and nested FFLO operators) with the current "mid" context
				iter = pfflwor.getReturnExpr().copy(workingContext, null, true, new Vector());
				
				  // make a new copy of the "mid" context, put it in
				workingContext = midContext.copy();
				subIters[0].getContext().setParent(workingContext);
				 //update varHolder -> more efficient than doing it inside Forseq with possibly a lot of context nesting
				fw.updateVarHolders(workingContext);
				
				  // start thread with the copied operator
				w = new Worker(iter, output);
				pool.execute(w);
			}
			if (ThreadPool.isDebug()) ThreadPool.log(this, "No more bindings");
		} catch (MXQueryException ex) {
			ex.printStackTrace();
		}
		
		done = true;
	}
	
	public synchronized void waitForNextBinding() {
		int currentCount = bindingCount;
		if (ThreadPool.isDebug()) ThreadPool.log(this, "Waiting for next binding (" + currentCount + ")");
		while(!done && currentCount == bindingCount) {
			Thread.yield();
		}
		if (ThreadPool.isDebug()) ThreadPool.log(this, "Waiting for next binding done!");
	}
}

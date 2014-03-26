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
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.iterators.forseq.ForseqWindowEarlyBinding;
import ch.ethz.mxquery.iterators.forseq.ForseqWindowEarlyBindingParallel;
import ch.ethz.mxquery.iterators.forseq.ForseqWindowIterator;
import ch.ethz.mxquery.iterators.forseq.ForseqWindowNaiveIterator;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.parallel.BindingThread;
import ch.ethz.mxquery.parallel.ParallelOutput;
import ch.ethz.mxquery.parallel.ThreadPool;
import ch.ethz.mxquery.parallel.Worker;

public class PFFLWORIterator extends FFLWORIterator {
	private ParallelOutput output = new ParallelOutput();
	private ThreadPool pool = null;

	public PFFLWORIterator(Context ctx, XDMIterator[] subIters, XDMIterator whereExpr,
			OrderByIterator orderByExpr, XDMIterator returnExpr, QueryLocation location)
			throws MXQueryException {
		super(ctx, subIters, whereExpr, orderByExpr, returnExpr,location);
	}
	
	private void bindingInMainThread() throws MXQueryException {
		pool = new ThreadPool();

		// Currently only works for a single FORSEQ iterator, extend later
		//for (int i=0; i<subIters.length; i++) {
		if (!(subIters[0] instanceof ForseqWindowNaiveIterator || subIters[0] instanceof ForseqWindowEarlyBinding || subIters[0] instanceof ForseqWindowEarlyBindingParallel)) {
			throw new RuntimeException("Parallel Execution Only works with Forseq");
		}
		// insert a new context between PFFLWOR and FORSEQ: "mid"

		ForseqWindowIterator fw = ((ForseqWindowIterator)subIters[0]);

		Context midContext = new Context(context);
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

		int bindingCount = 0;
		// for each binding
		while (doNextBinding()) {
			if (ThreadPool.isDebug()) ThreadPool.log(this, "Binding " + bindingCount + " done");
			bindingCount++;

			// copy the return (later also where and nested FFLO operators) with the current "mid" context
			iter = returnExpr.copy(workingContext, null, true, new Vector());

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
	}
	
	public void init() throws MXQueryException {
		if (ThreadPool.useBindingThread()) {
			pool = new ThreadPool();
			new BindingThread(pool, output, this).start();
		} else {
			bindingInMainThread();
		}
	}
	
	public Token next() throws MXQueryException {		
		if (called == 0) {
			if (ThreadPool.isDebug()) ThreadPool.log(this, "Going to init() method");
			init();
			if (ThreadPool.isDebug()) ThreadPool.log(this, "Returned to next() method");
			called++;
		}
		
		Token t = output.next();
		
		if (t.getEventType() == Type.END_SEQUENCE) {
			if (pool != null) {
				pool.shutdown();
				pool = null;
			}
		}
		
		return t;
	}
}

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

import java.util.LinkedList;
import java.util.Vector;

import ch.ethz.mxquery.datamodel.xdm.Token;
//import ch.ethz.mxquery.iterators.forseq.BindingThread;

public class ParallelOutput {
	private Vector output; // <LinkedList<Token>>
	private Vector done; // <Boolean>
	private int workerIndex;
	private BindingThread bindingThread;
	
	private Object lock_register = new Object();
	
	private boolean inRegister = false;
	private boolean inNext = false;
	
	public ParallelOutput() {
		output = new Vector(); // <LinkedList<Token>>
		done = new Vector(); // <Boolean>
		workerIndex = 0;
		if (ThreadPool.isDebug()) ThreadPool.log(this, "ParallelOutput initialized.");
	}
	
	public int registerWorker() {
		if (ThreadPool.isDebug()) ThreadPool.log(this, "Trying to register worker " + (output.size()) + "!");
		while (inNext);
		
		synchronized (lock_register) {
			inRegister = true;
			output.add(new LinkedList()); // <Token>
			done.add(new Boolean(false));
			
			if (ThreadPool.isDebug()) ThreadPool.log(this, "Worker " + (output.size()-1) + " registered.");
			
			inRegister = false;
			return output.size()-1;
		}
	}
	
	public synchronized void add(int id, Token t) {
		if (id < output.size()) {
			((LinkedList) output.get(id)).addLast(t);
		}
		notifyAll();
	}
	
	public synchronized void done(int id) {
		if (id < done.size()) {
			if (ThreadPool.isDebug()) ThreadPool.log(this, "Worker " + id + " finished it's work.");
			done.set(id, new Boolean(true));
		}
		notifyAll();
	}
	
	public synchronized Token next() {
		while (output.size() == 0) {
			// wait until first output is generated!
			try {
				wait();	
			} catch (InterruptedException ex) {}
		}
		
		// wait while any thread registers
		while (inRegister) {
			try {
				wait();	
			} catch (InterruptedException ex) {}
		}
		
		inNext = true;
		
		if (workerIndex < output.size()) {
			if (!((LinkedList) output.get(workerIndex)).isEmpty()) {
				// if output is not empty, return next token
				inNext = false;
				return (Token) ((LinkedList) output.get(workerIndex)).removeFirst();
			} else {
				// if output is empty
				if (((Boolean) done.get(workerIndex)).booleanValue()) {
					// if worker is done, go to next worker
					workerIndex++;
					inNext = false;
					return next();
				} else {
					// else, wait until output contains elements or worker is done
					while (((LinkedList) output.get(workerIndex)).isEmpty() && !((Boolean) done.get(workerIndex)).booleanValue()) { 
						try {
							wait();	
						} catch (InterruptedException ex) {}
					}
					inNext = false;
					return next();
				}
			}
		}
		
		inNext = false;
		
		if (bindingThread != null && bindingThread.isAlive()) { 
			bindingThread.waitForNextBinding();

			return next();
		}
		
		if (ThreadPool.isDebug()) ThreadPool.log(this, "next() returns END_SEQUENCE!");
		return Token.END_SEQUENCE_TOKEN;
	}

	public synchronized void setBindingThread(BindingThread bindingThread) {
		this.bindingThread = bindingThread;
	}
}

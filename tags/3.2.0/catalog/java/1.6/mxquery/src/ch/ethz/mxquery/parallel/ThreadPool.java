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

import java.util.concurrent.ExecutorService;
import java.util.concurrent.Executors;


public class ThreadPool {	
	public final static int DEFAULT_THREADSIZE = 8;
	
	private static boolean debug = false;
	private static boolean dynamic = false;
	private static boolean bindingThread = true;
	private static int numthreads = DEFAULT_THREADSIZE;
	
	public static boolean isDebug() {
		return debug;
	}
	
	public static boolean isDynamic() {
		return ThreadPool.dynamic;
	}
	
	public static boolean useBindingThread() {
		return bindingThread;
	}
	
	public static void setUseBindingThread(boolean use) {
		bindingThread = use;
	}
	
	public static void log(Object o, String msg) {
		if (o instanceof Worker) {
			System.out.print("# W #\t");
		} else if (o instanceof ParallelOutput) {
			System.out.print("# P #\t");
		} else if (o instanceof BindingThread) {
			System.out.print("# B #\t");
		} else {
			System.out.print("# ? #\t");
		}
		System.out.println(msg);
	}
	
	public static void setDebug(boolean debug) {
		ThreadPool.debug = debug;
	}

	public static void setDynamic(boolean dynamic) {
		ThreadPool.dynamic = dynamic;
	}

	public static void setThreadCount(int count) {
		numthreads = count;
	}
	
	private ExecutorService exec;
	
	public ThreadPool() {
		init();
	}

	public void execute(Runnable thread) {
		exec.execute(thread);
	}

	private void init() {
		if (dynamic) {
			exec = Executors.newCachedThreadPool();
		} else {
			exec = Executors.newFixedThreadPool(numthreads);
		}	
	}
	
	public void shutdown() {
		exec.shutdown();
	}

	public static int getThreadCount() {
		return numthreads;
	}
}
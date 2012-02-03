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

package examples;

import java.io.PrintStream;
import java.io.StringReader;

import ch.ethz.mxquery.query.XQCompiler;
import ch.ethz.mxquery.query.PreparedStatement;
import ch.ethz.mxquery.query.impl.CompilerImpl;
import ch.ethz.mxquery.sms.StoreFactory;
import ch.ethz.mxquery.sms.MMimpl.StreamStoreInput;
import ch.ethz.mxquery.sms.interfaces.ActiveStore;
import ch.ethz.mxquery.sms.interfaces.StreamStore;
import ch.ethz.mxquery.contextConfig.CompilerOptions;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.xdmio.XDMInputFactory;
import ch.ethz.mxquery.xdmio.XDMSerializer;
import ch.ethz.mxquery.xdmio.XMLSource;

/**
 * This is a simple example of using MXQuery in a streaming mode,
 * consisting of input queue and a single continuous query reading from it
 * The next release of MXQuery will contain a stream server that will 
 * provide a more flexible and general setup
 * @author Peter Fischer
 *
 */
public class StreamExample {
	
	
	public static void main(String [] args) throws Exception {
		String query = "declare variable $input external; $input";
		System.out.println("Push Stream");
		runPushStream(query);
		System.out.println("\nPush Individual Items");
		runPushItems(query);
		System.out.println("\nPull Stream by Queue (active/eager)");
		runActivePullStream(query);
		System.out.println("\nPull Stream by Queries (passive/lazy)");
		runPassivePullStream(query);
		System.out.println("\nPull Stream by Queue, multi queries (active/eager)");
		runActivePullStreamMultiQuery(query);

	}
	/**
	 * A example of a push stream where the caller needs to push the stream data into the input queue
	 * This is useful if data arrives in push manner (callback functions)
	 * or the gatherer should be under the full control of the application (e.g. timing pattern) 
	 * @param query the query to run
	 * @throws MXQueryException
	 */
	private static void runPushStream(String query) throws MXQueryException {
		// Compile and run the query
		Context ctx = new Context();
		CompilerOptions co = new CompilerOptions();
		XQCompiler compiler = new CompilerImpl();			

		PreparedStatement statement =  compiler.compile(ctx, query,co);
		XDMIterator result;
		result = statement.evaluate();
		// Pseudo stream 
		String xml = "<push/>";
		XMLSource xmlIt = XDMInputFactory.createXMLInput(result.getContext(), new StringReader(xml),true,Context.NO_VALIDATION,QueryLocation.OUTSIDE_QUERY_LOC);
		
		StreamStore sr = ctx.getStores().createStreamStore(StoreFactory.SHARED_RANDOM_FIFO, "example-stream");
		XDMIterator wnd = sr.getIterator(ctx);
		statement.addExternalResource(new QName("input"),wnd);
		StreamStoreInput si = new StreamStoreInput(sr);
		while (true) {
			Token token = xmlIt.next();
			boolean eos = si.bufferNext(token);
			if (eos)
				break;
		}
		XDMSerializer ip = new XDMSerializer();
		PrintStream pr = new PrintStream(System.out);
		// The serializer can also handle inifinite XDM sequences
		ip.eventsToXML(pr,result);
	}

	/**
	 * A example of a push stream where the caller pushes individual items into the input queue
	 * This can be useful if the incoming stream consists of individual items
	 * @param query the query to run
	 * @throws MXQueryException
	 */
	private static void runPushItems(String query) throws MXQueryException {
		// Compile and run the query
		Context ctx = new Context();
		CompilerOptions co = new CompilerOptions();
		XQCompiler compiler = new CompilerImpl();			

		PreparedStatement statement =  compiler.compile(ctx, query,co);
		XDMIterator result;
		result = statement.evaluate();
		// Pseudo stream 
		String item1 = "<item1/>";
		String item2 = "<item2/>";
		String item3 = "<item3/>";

		XMLSource xmlIt1 = XDMInputFactory.createXMLInput(result.getContext(), new StringReader(item1),true,Context.NO_VALIDATION,QueryLocation.OUTSIDE_QUERY_LOC);
		XMLSource xmlIt2 = XDMInputFactory.createXMLInput(result.getContext(), new StringReader(item2),true,Context.NO_VALIDATION,QueryLocation.OUTSIDE_QUERY_LOC);
		XMLSource xmlIt3 = XDMInputFactory.createXMLInput(result.getContext(), new StringReader(item3),true,Context.NO_VALIDATION,QueryLocation.OUTSIDE_QUERY_LOC);
		
		StreamStore sr = ctx.getStores().createStreamStore(StoreFactory.RANDOM_FIFO, "example-stream");
		XDMIterator wnd = sr.getIterator(ctx);
		statement.addExternalResource(new QName("input"),wnd);
		StreamStoreInput si = new StreamStoreInput(sr,true);
		XDMIterator curItemIt = xmlIt1;
		Token tok;
		while ((tok = curItemIt.next()) != Token.END_SEQUENCE_TOKEN) {
			si.bufferNext(tok);
		}
		curItemIt = xmlIt2;
		while ((tok = curItemIt.next()) != Token.END_SEQUENCE_TOKEN) {
			si.bufferNext(tok);
		}
		curItemIt = xmlIt3;
		while ((tok = curItemIt.next()) != Token.END_SEQUENCE_TOKEN) {
			si.bufferNext(tok);
		}

		si.endStream();

		XDMSerializer ip = new XDMSerializer();
		PrintStream pr = new PrintStream(System.out);
		ip.eventsToXML(pr,result);
	}
	
	
	
	/**
	 * A example of a pull stream where the input queue gathers the data when it is available
	 * This is useful if data shall be gathered as fast as possible
	 * @param query The query to run
	 * @throws MXQueryException
	 */
	private static void runActivePullStream(String query) throws MXQueryException {
		// Compile and run the query
		Context ctx = new Context();
		CompilerOptions co = new CompilerOptions();
		XQCompiler compiler = new CompilerImpl();			

		PreparedStatement statement =  compiler.compile(ctx, query,co);
		XDMIterator result;
		result = statement.evaluate();
		// Pseudo stream 
		String xml = "<pullEager/>";
		XMLSource xmlIt = XDMInputFactory.createXMLInput(result.getContext(), new StringReader(xml),true,Context.NO_VALIDATION,QueryLocation.OUTSIDE_QUERY_LOC);
		
		StreamStore sr = ctx.getStores().createStreamStore(StoreFactory.SHARED_RANDOM_FIFO, "example-stream");
		sr.setIterator(xmlIt);
		ActiveStore ac = (ActiveStore)sr;
		ac.start();
		XDMIterator wnd = sr.getIterator(ctx);
		statement.addExternalResource(new QName("input"),wnd);
		XDMSerializer ip = new XDMSerializer();
		PrintStream pr = new PrintStream(System.out);
		ip.eventsToXML(pr,result);
	}

	/**
	 * A example of a pull stream where the query triggers gathering the data
	 * This is useful if the state in the input queue should be minimized
	 * @param query the query to run
	 * @throws MXQueryException
	 */
	private static void runPassivePullStream(String query) throws MXQueryException {
		// Compile and run the query
		Context ctx = new Context();
		CompilerOptions co = new CompilerOptions();
		XQCompiler compiler = new CompilerImpl();			

		PreparedStatement statement =  compiler.compile(ctx, query,co);
		XDMIterator result;
		result = statement.evaluate();
		// Pseudo stream 
		String xml = "<pullLazy/>";
		XMLSource xmlIt = XDMInputFactory.createXMLInput(result.getContext(), new StringReader(xml),true,Context.NO_VALIDATION,QueryLocation.OUTSIDE_QUERY_LOC);
		
		StreamStore sr = ctx.getStores().createStreamStore(StoreFactory.LAZY_RANDOM_FIFO, "example-stream");
		sr.setIterator(xmlIt);

		XDMIterator wnd = sr.getIterator(ctx);
		statement.addExternalResource(new QName("input"),wnd);
		XDMSerializer ip = new XDMSerializer();
		PrintStream pr = new PrintStream(System.out);
		ip.eventsToXML(pr,result);
	}

	/**
	 * A example of a multiple continuos queries 
	 * This is useful if data shall be gathered as fast as possible
	 * @param query The query to run
	 * @throws MXQueryException
	 */
	private static void runActivePullStreamMultiQuery(String query) throws MXQueryException {
		// Compile and run the query
		Context ctx = new Context();
		Context ctx2 = new Context();
		CompilerOptions co = new CompilerOptions();
		XQCompiler compiler = new CompilerImpl();			

		PreparedStatement statement =  compiler.compile(ctx, query,co);
		PreparedStatement statement2 =  compiler.compile(ctx2, query,co);
		
		XDMIterator result;
		result = statement.evaluate();
		XDMIterator result2 = statement2.evaluate();
		// Pseudo stream 
		String xml = "<pullEager/>";
		XMLSource xmlIt = XDMInputFactory.createXMLInput(result.getContext(), new StringReader(xml),true,Context.NO_VALIDATION,QueryLocation.OUTSIDE_QUERY_LOC);
		
		StreamStore sr = ctx.getStores().createStreamStore(StoreFactory.SHARED_RANDOM_FIFO, "example-stream");
		sr.setIterator(xmlIt);
		ActiveStore ac = (ActiveStore)sr;
		ac.start();
		// Get two read access to the input queue
		XDMIterator wnd = sr.getIterator(ctx);
		XDMIterator wnd2 = sr.getIterator(ctx2);
		statement.addExternalResource(new QName("input"),wnd);
		statement2.addExternalResource(new QName("input"),wnd2);

		XDMSerializer ip = new XDMSerializer();
		PrintStream pr = new PrintStream(System.out);
		// Run both streaming queries
		// This could also be done in separate threads 
		// Also with output queues 
		ip.eventsToXML(pr,result);
		ip.eventsToXML(pr,result2);
	}
	
}



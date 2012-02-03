package examples;

import java.io.FileInputStream;
import java.io.IOException;
import java.io.InputStreamReader;

import ch.ethz.mxquery.contextConfig.CompilerOptions;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.Source;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.model.updatePrimitives.UpdateableStore;
import ch.ethz.mxquery.query.PreparedStatement;
import ch.ethz.mxquery.query.XQCompiler;
import ch.ethz.mxquery.query.impl.CompilerImpl;
import ch.ethz.mxquery.sms.ftstore.FullTextStore;
import ch.ethz.mxquery.util.StringReader;
import ch.ethz.mxquery.xdmio.XDMInputFactory;
import ch.ethz.mxquery.xdmio.XDMSerializer;
/**
 * Overview on the Store API of MXQuery, 
 * the different types of stores to create,
 * and how to share stores between queries 
 * @author Peter Fischer
 *
 */
public class StoreExample {

	public static void main(String [] args) throws Exception {
		System.out.println("Default stores");
		runDefaultStore();
		System.out.println("Updateable stores: empty output, since update and regular output cannot be mixed");
		runUpdateStore();
		System.out.println("Updateable stores - reusing/sharing store set between queries\n first query updates store, second performs the output");
		runUpdateStoreReuse();
		System.out.println("Fulltext store");
		runFullTextStore();
		// For stream stores, please have a look StreamExample.java
	}
	
	private static void runDefaultStore() throws MXQueryException {
		final String query = "declare variable $data external; $data";		
		Context ctx = new Context();
		// Uncomment one, if you want to switch to one of these as default store
//		ctx.getStores().setUseUpdateStores(true);
//		ctx.getStores().setUseFulltextStores(true);
		CompilerOptions co = new CompilerOptions();
		XQCompiler comp = new CompilerImpl();
		PreparedStatement statement;
		XDMSerializer ser = new XDMSerializer();
		try {
			statement = comp.compile(ctx, query, co);
		} catch (MXQueryException err) {
			MXQueryException.printErrorPosition(query, err.getLocation());
			System.err.println("Error:");
			throw err;		
		}
		
		// Default store
		XDMIterator it;
		String xml = "<data attr='123'><child/>characters</data>";
		StringReader sr = new StringReader(xml);
		Source store = ctx.getStores().createStore(XDMInputFactory.createXMLInput(ctx, sr, false, Context.NO_VALIDATION, QueryLocation.OUTSIDE_QUERY_LOC));
		statement.addExternalResource(new QName("data"), store.getIterator(ctx));
		it = statement.evaluate();
		System.out.println(ser.eventsToXML(it));
		statement.close();
		ctx.getStores().freeRessources();		
	}
	
	private static void runUpdateStore() throws MXQueryException, IOException{
		final String query = "declare variable $data external; insert node <hello/> into $data/data";		
		Context ctx = new Context();
		ctx.getStores().setUseUpdateStores(true);
		ctx.getStores().setSerializeStores(true);
		CompilerOptions co = new CompilerOptions();
		co.setUpdate(true);
		XQCompiler comp = new CompilerImpl();
		PreparedStatement statement;
		XDMSerializer ser = new XDMSerializer();
		try {
			statement = comp.compile(ctx, query, co);
		} catch (MXQueryException err) {
			MXQueryException.printErrorPosition(query, err.getLocation());
			System.err.println("Error:");
			throw err;		
		}
		
		// Update store
		XDMIterator it;
		String xml = "<data attr='123'><child/>characters</data>";
		StringReader sr = new StringReader(xml);
		UpdateableStore store = ctx.getStores().createUpdateableStore("datafile",XDMInputFactory.createXMLInput(ctx, sr, false, Context.NO_VALIDATION, QueryLocation.OUTSIDE_QUERY_LOC),true,true);
		ctx.getStores().addStoreToSerialize(store, "datafile.xml");
		statement.addExternalResource(new QName("data"), store.getIterator(ctx));
		it = statement.evaluate();
		System.out.println(ser.eventsToXML(it));
		statement.applyPUL();
		statement.serializeStores(false);
		statement.close();
		ctx.getStores().freeRessources();
	}
	
	private static void runUpdateStoreReuse() throws MXQueryException {
		final String query = "declare variable $data external; insert node <hello/> into $data/data";		
		Context ctx = new Context();
		CompilerOptions co = new CompilerOptions();
		co.setUpdate(true);
		ctx.getStores().setUseUpdateStores(true);
		XQCompiler comp = new CompilerImpl();
		PreparedStatement statement;
		XDMSerializer ser = new XDMSerializer();
		try {
			statement = comp.compile(ctx, query, co);
		} catch (MXQueryException err) {
			MXQueryException.printErrorPosition(query, err.getLocation());
			System.err.println("Error:");
			throw err;		
		}
		
		// Update store
		XDMIterator it;
		String xml = "<data attr='123'><child/>characters</data>";
		StringReader sr = new StringReader(xml);
		UpdateableStore store = ctx.getStores().createUpdateableStore("datafile",XDMInputFactory.createXMLInput(ctx, sr, false, Context.NO_VALIDATION, QueryLocation.OUTSIDE_QUERY_LOC),true,true);
		ctx.getStores().addStoreToSerialize(store, "datafile.xml");
		statement.addExternalResource(new QName("data"), store.getIterator(ctx));
		it = statement.evaluate();
		System.out.println(ser.eventsToXML(it));
		statement.applyPUL();
		statement.close();
		Context ctx2 = new Context(null,ctx.getStores());
		final String query2 = "declare variable $data external; $data";
		PreparedStatement statement2;
		try {
			statement2 = comp.compile(ctx2, query2, co);
		} catch (MXQueryException err) {
			MXQueryException.printErrorPosition(query, err.getLocation());
			System.err.println("Error:");
			throw err;		
		}
		statement2.addExternalResource(new QName("data"), ctx2.getStores().getStore("datafile").getIterator(ctx2));
		it = statement2.evaluate();
		System.out.println(ser.eventsToXML(it));
		statement2.applyPUL();
		statement2.close();

		ctx.getStores().freeRessources();

	}
	
	private static void runFullTextStore() throws MXQueryException, IOException{
		final String query = "declare variable $data external; $data/books/book/title[. ftcontains 'wave' case insensitive]";		
		Context ctx = new Context();
		CompilerOptions co = new CompilerOptions();
		co.setFulltext(true);
		XQCompiler comp = new CompilerImpl();
		PreparedStatement statement;
		XDMSerializer ser = new XDMSerializer();
		try {
			statement = comp.compile(ctx, query, co);
		} catch (MXQueryException err) {
			MXQueryException.printErrorPosition(query, err.getLocation());
			System.err.println("Error:");
			throw err;		
		}

		// Fulltext store 
		XDMIterator it;
		FileInputStream fi = new FileInputStream("src/examples/books.xml");
		InputStreamReader sr = new InputStreamReader(fi);
		FullTextStore store = ctx.getStores().createFulltextStore("books.xml", XDMInputFactory.createXMLInput(ctx, sr, false, Context.NO_VALIDATION, QueryLocation.OUTSIDE_QUERY_LOC));
		statement.addExternalResource(new QName("data"), store.getIterator(ctx));
		it = statement.evaluate();
		System.out.println(ser.eventsToXML(it));
		statement.applyPUL();
		statement.close();
		ctx.getStores().freeRessources();		
	}
	
}

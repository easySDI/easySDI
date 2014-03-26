package examples;

import ch.ethz.mxquery.contextConfig.CompilerOptions;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.contextConfig.XQDynamicContext;
import ch.ethz.mxquery.datamodel.MXQueryDateTime;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.query.PreparedStatement;
import ch.ethz.mxquery.query.XQCompiler;
import ch.ethz.mxquery.query.impl.CompilerImpl;
import ch.ethz.mxquery.util.StringReader;
import ch.ethz.mxquery.xdmio.XDMAtomicItemFactory;
import ch.ethz.mxquery.xdmio.XDMInputFactory;
import ch.ethz.mxquery.xdmio.XDMSerializer;


// set boundary space policy
//set external variables
//set context item
// set current time 
public class ContextExample {

	public static void main(String[] arg) throws Exception{
		String query = "declare variable $ext external;"+
		"\n<result>\n\t<external>\n\t\t{$ext}\n\t</external>"+
		"\n\t<context>\n\t\t{.}\n\t</context>\n\t<time>\n\t\t{fn:current-dateTime()}\n\t</time>\n</result>";
		Context ctx = new Context();
		CompilerOptions co = new CompilerOptions();
		XQCompiler comp = new CompilerImpl();
		PreparedStatement statement;
		XDMSerializer ser = new XDMSerializer();
		// Static context setting
		// Keep boundary space (default is no)
		ctx.setBoundarySpaceHandling(true);
		
		try {
			statement = comp.compile(ctx, query, co);

			// Default store
			XDMIterator it;
			String xml = "<data attr='123'><child/>characters</data>";
			String context = "context123";
			XDMIterator inp = XDMInputFactory.createXMLInput(ctx, new StringReader(xml), false, Context.NO_VALIDATION, QueryLocation.OUTSIDE_QUERY_LOC);
			XDMIterator cItem = XDMAtomicItemFactory.createString(context);
			statement.addExternalResource(new QName("ext"), inp);
			statement.setContextItem(cItem);
			XQDynamicContext dynCtx = statement.getContext();
			it = statement.evaluate();
			dynCtx.setCurrentTime(new MXQueryDateTime("2000-01-01T00:00:00-00:00"));
			System.out.println(ser.eventsToXML(it));
			statement.close();
			ctx.getStores().freeRessources();		
		} catch (MXQueryException err) {
			MXQueryException.printErrorPosition(query, err.getLocation());
			System.err.println("Error:");
			throw err;		
		}

	}
	
}

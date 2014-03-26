package examples;

import java.io.ByteArrayInputStream;
import java.io.IOException;
import java.io.UnsupportedEncodingException;
import java.util.Vector;

import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.ParserConfigurationException;
import javax.xml.stream.FactoryConfigurationError;
import javax.xml.stream.XMLInputFactory;
import javax.xml.stream.XMLStreamException;
import javax.xml.stream.XMLStreamReader;

import org.w3c.dom.Node;
import org.xml.sax.SAXException;
import ch.ethz.mxquery.contextConfig.CompilerOptions;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.model.Iterator;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.query.PreparedStatement;
import ch.ethz.mxquery.query.XQCompiler;
import ch.ethz.mxquery.query.impl.CompilerImpl;
import ch.ethz.mxquery.util.StringReader;
import ch.ethz.mxquery.xdmio.WrapperIterator;
import ch.ethz.mxquery.xdmio.XDMAtomicItemFactory;
import ch.ethz.mxquery.xdmio.XDMInputFactory;
import ch.ethz.mxquery.xdmio.XDMSerializer;
import ch.ethz.mxquery.xdmio.XDMSerializerSettings;

/**
 * This class gives examples about input and output using the MXQuery JAVA API
 * It covers
 * 	- Setting external variables from String, Sax, StaX, DOM, TidyString
 *	- Importing relational data (e.g. CSV, SQL Data, etc)
 *	- Controlling XDM Serialization settings
 *
 * @author Peter M. Fischer
 *
 */

public class InputOutputExample {
	public class RelWrapper extends WrapperIterator {
		
		// Sample relational data: Firstname, Lastname, Mail, Birthday
		String [] [] relationalData = {{"Peter","Fischer","peter.fischer@inf.ethz.ch","1977-12-31"},{"Jane","Doe","jane.doe@example.com","1961-02-03"}};
		//
		int pos = 0;
		
		
		public RelWrapper(Context ctx, QueryLocation loc) throws MXQueryException{
			super(ctx,loc);
			enclosingName = new QName("PersonData");
			valueNames = new QName[4];
			types = new int [4];
			
			valueNames[0] = new QName("firstname");
			valueNames[1] = new QName("lastname");
			valueNames[2] = new QName("mail");
			valueNames[3] = new QName("birthday");
			
			for (int i=0;i<types.length;i++)
				types[i] = Type.STRING;

		}
		protected boolean getData() {
			if (pos < relationalData.length) {
				inputValues = relationalData[pos];
				pos++;
				currentElement = 0;
				return true;
			}
			else return false;
		}

		protected XDMIterator copy(Context context, XDMIterator[] subIters,
				Vector nestedPredCtxStack) throws MXQueryException {
			Iterator ret = new RelWrapper(context,loc);
			ret.setSubIters(subIters);
			return ret;
		}
		
	}
	
	static final String query = "declare variable $data external; $data";
	
	/**
	 * @param args
	 */
	public static void main(String[] args) throws Exception{
		InputOutputExample io = new InputOutputExample();
		io.runIOTests();
		
	}

	private void runIOTests() throws MXQueryException,
			ParserConfigurationException, SAXException, IOException,
			UnsupportedEncodingException, FactoryConfigurationError,
			XMLStreamException {
		Context ctx = new Context();
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
		
		// AtomicValue
		
		System.out.println("Atomic Value");
		statement.addExternalResource(new QName("data"), XDMAtomicItemFactory.createInt(12345));
		XDMIterator it = statement.evaluate();
		System.out.println(ser.eventsToXML(it));
		statement.close();
		ctx.getStores().freeRessources();
		
		System.out.println("XML from a character sequence");
		String xml = "<data attr='123'><child/>characters</data>";
		StringReader sr = new StringReader(xml);
		statement.addExternalResource(new QName("data"), XDMInputFactory.createXMLInput(ctx, sr, false, Context.NO_VALIDATION, QueryLocation.OUTSIDE_QUERY_LOC));
		it = statement.evaluate();
		System.out.println(ser.eventsToXML(it));
		statement.close();
		ctx.getStores().freeRessources();

		System.out.println("XML from a DOM node");
		xml = "<data attr='123'><child/>DOM</data>";
		Node nd = null;
		DocumentBuilder parser;
		DocumentBuilderFactory fac = DocumentBuilderFactory.newInstance();
		fac.setNamespaceAware(true);
		parser = fac.newDocumentBuilder();
		nd = parser.parse(new ByteArrayInputStream(xml.getBytes("UTF-8")));
		statement.addExternalResource(new QName("data"), XDMInputFactory.createDOMInput(ctx, nd, QueryLocation.OUTSIDE_QUERY_LOC));
		it = statement.evaluate();
		System.out.println(ser.eventsToXML(it));
		statement.close();
		ctx.getStores().freeRessources();

//		System.out.println("XML from SAX");
//		xml = "<data attr='123'><child/>SAX</data>";
//		XMLReader rd = XMLReaderFactory.createXMLReader();
//		rd.
//		statement.addExternalResource("data", XDMInputFactory.createSAXInput(ctx, rd, QueryLocation.OUTSIDE_QUERY_LOC));
//		it = statement.evaluate();
//		System.out.println(ser.eventsToXML(it));
//		statement.close();
//		ctx.getStores().freeRessources();

		
		
		
		System.out.println("XML from StaX/XMLStreamReader");
		xml = "<data attr='123'><child/>StAX</data>";
		XMLInputFactory factory = XMLInputFactory.newInstance();
		XMLStreamReader staxReader = factory.createXMLStreamReader(new StringReader(xml));
		statement.addExternalResource(new QName("data"), XDMInputFactory.createStaxInput(ctx, staxReader, QueryLocation.OUTSIDE_QUERY_LOC));
		it = statement.evaluate();
		System.out.println(ser.eventsToXML(it));
		statement.close();
		ctx.getStores().freeRessources();

		System.out.println("XML from relational wrapping");
		xml = "<data attr='123'><child/>StAX</data>";
		statement.addExternalResource(new QName("data"), new RelWrapper(ctx,QueryLocation.OUTSIDE_QUERY_LOC));
		it = statement.evaluate();
		System.out.println(ser.eventsToXML(it));
		statement.close();
		ctx.getStores().freeRessources();
		
		System.out.println("Serializer settings");
		xml = "<data attr='123'><child/>characters<cdata><![CDATA[<enctag/>]]></cdata></data>";
		sr = new StringReader(xml);
		statement.addExternalResource(new QName("data"), XDMInputFactory.createXMLInput(ctx, sr, false, Context.NO_VALIDATION, QueryLocation.OUTSIDE_QUERY_LOC));
		it = statement.evaluate();
		XDMSerializerSettings set = new XDMSerializerSettings();
		set.setOutputMethod(XDMSerializerSettings.OUTPUT_METHOD_XML);
		set.setOmitXMLDeclaration(true);
		// not yet implemented
//		set.setCdataSectionElements(new Set().add(new QName("cdata"));
		XDMSerializer ser1 = new XDMSerializer(set);
		System.out.println(ser1.eventsToXML(it));
		statement.close();
		ctx.getStores().freeRessources();
		
		
		// To be added: output as StAX
		
		// To be implemented: output as SAX, DOM
		
	}

}

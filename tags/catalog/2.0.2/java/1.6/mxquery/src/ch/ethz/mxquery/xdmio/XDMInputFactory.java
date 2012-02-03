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

package ch.ethz.mxquery.xdmio;

import java.io.IOException;
import java.io.Reader;

import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.ParserConfigurationException;
import javax.xml.parsers.SAXParser;
import javax.xml.parsers.SAXParserFactory;
import javax.xml.stream.XMLInputFactory;
import javax.xml.stream.XMLStreamException;
import javax.xml.stream.XMLStreamReader;

import org.w3c.dom.Node;
import org.xml.sax.InputSource;
import org.xml.sax.SAXException;
import org.xml.sax.XMLReader;
import org.xmlpull.v1.XmlPullParser;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.contextConfig.XQStaticContext;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.xdmio.xmlAdapters.NonSchemaValidatingSaxImportAdapter;
import ch.ethz.mxquery.xdmio.xmlAdapters.NonValidatingDOMAdapter;
import ch.ethz.mxquery.xdmio.xmlAdapters.NonValidatingStaxAdapter;
import ch.ethz.mxquery.xdmio.xmlAdapters.ValidatedSaxImportAdapter;
import ch.ethz.mxquery.xdmio.xmlAdapters.XPPImportAdapter;

public class XDMInputFactory {
		
	
	/**
	 * Create an XML input from a character stream provided by a Reader. The exact type of parser used is determine by the engine, this setting can be changed using Context.globalContext.setParserType()
	 * @param ctx The context in which the input should run 
	 * @param xml A Reader producing a character stream
	 * @param doc Shall the result be treated as document or as element (the latter is not supported by all parsers)
	 * @param valMode The type of validation that should be performed on the input, can be Context.NO_VALIDATION, Context.DTD_VALIDATION, Context.NO_VALIDATION, Context.SCHEMA_VALIDATION_LAX, Context.SCHEMA_VALIDATION_STRICT  
	 * @param location The location of input in the query, set to QueryLocation.OUTSIDE_QUERY_LOC if not required
	 * @return A MXQuery Iterator representing the input
	 * @throws MXQueryException
	 */
	public static XMLSource createXMLInput(Context ctx, Reader xml, boolean doc, int valMode, QueryLocation location) throws MXQueryException {
		if (valMode == Context.NO_VALIDATION || valMode == Context.DTD_VALIDATION) {
			switch (ctx.getParserType()) {
				case Context.NONVALIDATED_INPUT_MODE_XPP:
					return new XPPImportAdapter(ctx,xml,doc,location);
				case Context.NONVALIDATED_INPUT_MODE_SAX:
					return new NonSchemaValidatingSaxImportAdapter(ctx,location,new InputSource(xml), valMode == Context.DTD_VALIDATION, false);
				case Context.NONVALIDATED_INPUT_MODE_SAX_TIDY:
					return new NonSchemaValidatingSaxImportAdapter(ctx,location,new InputSource(xml), false, true);
				case Context.NONVALIDATED_INPUT_MODE_STAX:
					XMLInputFactory factory = XMLInputFactory.newInstance();
					if (valMode == Context.NO_VALIDATION)
						factory.setProperty("javax.xml.stream.supportDTD", "false");
					else 
						factory.setProperty("javax.xml.stream.supportDTD", "true");
					try {
						XMLStreamReader reader = factory.createXMLStreamReader(xml);
						return new NonValidatingStaxAdapter(ctx,location,reader);
					} catch (XMLStreamException e) {
						throw new MXQueryException(ErrorCodes.A0007_EC_IO,"I/O Error while parsing",location);
					}
				case Context.NONVALIDATED_INPUT_MODE_DOM:
					DocumentBuilder parser;
					try {
						DocumentBuilderFactory fac = DocumentBuilderFactory.newInstance();
						fac.setNamespaceAware(true);
						parser = fac.newDocumentBuilder();
						Node node = parser.parse(new InputSource(xml));
						return new NonValidatingDOMAdapter(ctx,location,node);
					} catch (ParserConfigurationException e) {
						throw new MXQueryException(ErrorCodes.A0007_EC_IO,"I/O Error while parsing",location);
					} catch (IOException e) {
						throw new MXQueryException(ErrorCodes.A0007_EC_IO,"I/O Error while parsing",location);
					} catch (SAXException e) {
						throw new MXQueryException(ErrorCodes.A0007_EC_IO,"I/O Error while parsing",location);
					}
				default:
					throw new MXQueryException(ErrorCodes.A0002_EC_NOT_SUPPORTED,"Input mode for non-validated input currently not supported",location);
			}
		}
		else {
			System.setProperty("javax.xml.parsers.SAXParserFactory", "org.apache.xerces.jaxp.SAXParserFactoryImpl");
			SAXParserFactory spf = SAXParserFactory.newInstance();
			spf.setNamespaceAware(true);
			try {
				spf.setFeature("http://xml.org/sax/features/validation", true);
				//spf.setFeature("http://apache.org/xml/features/validation/dynamic", true);
				spf.setFeature("http://apache.org/xml/features/validation/schema", true);
				spf.setFeature("http://apache.org/xml/features/validation/schema/augment-psvi", true);
				spf.setFeature("http://xml.org/sax/features/namespace-prefixes", true);
				spf.setFeature("http://apache.org/xml/features/honour-all-schemaLocations",true);
				// XML input always uses strict mode
				if (valMode == Context.SCHEMA_VALIDATION_STRICT) {
					spf.setFeature("http://apache.org/xml/features/validation/warn-on-undeclared-elemdef", true);
					spf.setFeature("http://apache.org/xml/features/validation/schema/ignore-xsi-type-until-elemdecl", true);
				}
				SAXParser parser = spf.newSAXParser();
				XMLReader reader = parser.getXMLReader();
				return new ValidatedSaxImportAdapter(valMode,ctx,location,reader,xml,doc);
			} catch (SAXException e) {
				throw new MXQueryException(ErrorCodes.A0002_EC_NOT_SUPPORTED,"Error creating validating input: "+e.toString(),location);
			} catch (ParserConfigurationException e) {
				throw new MXQueryException(ErrorCodes.A0002_EC_NOT_SUPPORTED,"Error creating validating input - parser configuration error",location);
			}
		}
	}
	/**
	 * Create an XML input using an XML Pull Parser instance
	 * @param ctx The context in which the input should run 
	 * @param pullParser A XML Pull Parser instance
	 * @param loc The location of input in the query, set to QueryLocation.OUTSIDE_QUERY_LOC if not required 
	 * @return A MXQuery Iterator representing the input
	 * @throws MXQueryException
	 */
	public static XMLSource createXPPInput(Context ctx, XmlPullParser pullParser, QueryLocation loc) throws MXQueryException{
		return new XPPImportAdapter(ctx,pullParser,true,loc);
	}
	
	/**
	 * Create an XML input using a StAX/XMLStreamReader instance
	 * @param ctx The context in which the input should run 
	 * @param staxInput A StAX/XMLStream Reader 
	 * @param location The location of input in the query, set to QueryLocation.OUTSIDE_QUERY_LOC if not required
	 * @return A MXQuery Iterator representing the input
	 * @throws MXQueryException
	 */
	public static XMLSource createStaxInput(Context ctx, XMLStreamReader staxInput, QueryLocation location) throws MXQueryException {
		return new NonValidatingStaxAdapter(ctx, location, staxInput);
	}

	/**
	 * Create an XML input using a SAX/XMLReader instance
	 * @param ctx The context in which the input should run 
	 * @param saxInput A SAX/XML Reader  
	 * @param location The location of input in the query, set to QueryLocation.OUTSIDE_QUERY_LOC if not required
	 * @return A MXQuery Iterator representing the input
	 * @throws MXQueryException
	 */
	public static XMLSource createSAXInput(Context ctx, XMLReader saxInput, QueryLocation location) throws MXQueryException{
		return new NonSchemaValidatingSaxImportAdapter(ctx,location,saxInput);
	}
	/**
	 * Create an XML input using a DOM node instance. The DOM is read into MXQuery, but never changed
	 * @param ctx The context in which the input should run 
	 * @param node A DOM node 
	 * @param location The location of input in the query, set to QueryLocation.OUTSIDE_QUERY_LOC if not required
	 * @return A MXQuery Iterator representing the input
	 * @throws MXQueryException
	 */
	public static XMLSource createDOMInput(Context ctx, Node node, QueryLocation location) throws MXQueryException{
		return new NonValidatingDOMAdapter(ctx,location,node);
	}
	
	/**
	 * Create an updateable XML input by binding against a DOM node instance. Changes to the XDM are propagated to the DOM
	 * @param ctx The context in which the input should run 
	 * @param node A DOM node 
	 * @param location The location of input in the query, set to QueryLocation.OUTSIDE_QUERY_LOC if not required
	 * @return A MXQuery Iterator representing the input
	 * @throws MXQueryException
	 */
	public static XMLSource createUpdateableDOMInput(XQStaticContext ctx, Node node, QueryLocation location) throws MXQueryException{
		throw new MXQueryException(ErrorCodes.A0002_EC_NOT_SUPPORTED, "Binding to DOM nodes not supported yet", location);
	}
	/**
	 * Create an XHTML input from a character stream provided by a Reader. The character stream does not need to be well-formed, it will be automatically corrected 
	 * @param ctx The context in which the input should run 
	 * @param xml A Reader producing a character stream
	 * @param location The location of input in the query, set to QueryLocation.OUTSIDE_QUERY_LOC if not required
	 * @return A MXQuery Iterator representing the input
	 * @throws MXQueryException
	 */	
	public static XMLSource createTidyInput(Context ctx, Reader xml, QueryLocation location) throws MXQueryException  {
		return new NonSchemaValidatingSaxImportAdapter(ctx,location,new InputSource(xml), false, true);		
	}
	
}

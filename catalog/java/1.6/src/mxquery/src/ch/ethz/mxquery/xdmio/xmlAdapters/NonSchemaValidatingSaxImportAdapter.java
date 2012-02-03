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

package ch.ethz.mxquery.xdmio.xmlAdapters;

import java.io.IOException;
import java.util.Stack;
import java.util.Vector;

import javax.xml.parsers.ParserConfigurationException;
import javax.xml.parsers.SAXParser;
import javax.xml.parsers.SAXParserFactory;

import org.xml.sax.Attributes;
import org.xml.sax.InputSource;
import org.xml.sax.SAXException;
import org.xml.sax.SAXParseException;
import org.xml.sax.XMLReader;
import org.xml.sax.ext.DeclHandler;
import org.xml.sax.ext.LexicalHandler;
import org.xml.sax.helpers.DefaultHandler;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.xdm.CommentToken;
import ch.ethz.mxquery.datamodel.xdm.NamedToken;
import ch.ethz.mxquery.datamodel.xdm.ProcessingInstrToken;
import ch.ethz.mxquery.datamodel.xdm.TextToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.model.XDMIterator;

public class NonSchemaValidatingSaxImportAdapter extends XDMImportAdapter {


	SaxAdapter adapter;
	
	protected InputSource tobeParsed;
	
	Vector tokens;
	
	boolean validateDTD;
	boolean tidyInput;
	
	
	class SaxAdapter extends DefaultHandler implements LexicalHandler, DeclHandler{

		private int tokensCount;

		private boolean startCData;

		private Stack tokensIndexStack;
		
		private XMLReader reader;

		public SaxAdapter(XMLReader source) {
			this.tokensIndexStack = new Stack();
			reader = source;
		}

		public void parse() throws SAXException, IOException,MXQueryException {
			try {
				if (reader == null) {
					if (tidyInput) {
						System.setProperty("javax.xml.parsers.SAXParserFactory", "org.ccil.cowan.tagsoup.jaxp.SAXFactoryImpl");
					} else
						System.setProperty("javax.xml.parsers.SAXParserFactory", "org.apache.xerces.jaxp.SAXParserFactoryImpl");

					SAXParserFactory spf = SAXParserFactory.newInstance();
					if (!tidyInput) {
						spf.setNamespaceAware(true);
						spf.setFeature("http://xml.org/sax/features/namespace-prefixes", true);
					} else {
						spf.setFeature("http://xml.org/sax/features/namespaces",false);
					}
					if (validateDTD) {
						spf.setFeature("http://xml.org/sax/features/validation", true);
						spf.setFeature("http://apache.org/xml/features/validation/dynamic", true);
					}
					
					SAXParser parser = spf.newSAXParser();
					reader = parser.getXMLReader();
				}
				reader.setErrorHandler(this);
				reader.setProperty("http://xml.org/sax/properties/lexical-handler", this);
				if (validateDTD) {
					reader.setProperty("http://xml.org/sax/properties/declaration-handler", this);
				}
				//	reader.setProperty("http://apache.org/xml/properties/schema/external-schemaLocation", context.initDictionary().getSchemaLocations());
				reader.setContentHandler(this);
				if (tobeParsed != null) 
					reader.parse(tobeParsed);
				else 
					reader.parse((String)null);
		} catch (SAXException e) {
			throw new DynamicException(ErrorCodes.A0007_EC_IO,"Error creating validating input: "+e.toString(),loc);
		} catch (ParserConfigurationException e) {
			throw new MXQueryException(ErrorCodes.A0002_EC_NOT_SUPPORTED,"Error creating validating input - parser configuration error",loc);
		}			
		}
		public void processingInstruction(String target, String data) throws SAXException {
			try {
				tokens.add(new ProcessingInstrToken(createNextTokenId(Type.PROCESSING_INSTRUCTION,null), data, target,curNsScope));
				tokensCount++;
			} catch (MXQueryException e) {
				e.printStackTrace();
			}
		}

		public void startDocument() throws SAXException {
			tokens.add(new Token(Type.START_DOCUMENT, createNextTokenId(Type.START_DOCUMENT,null),curNsScope));
			if (getURI() != null) 
				curNsScope.setBaseURI(getURI());
			tokensCount++;
			//level++;
		}

		public void endDocument() throws SAXException {
			tokens.add(new Token(Type.END_DOCUMENT, createNextTokenId(Type.END_DOCUMENT,null),curNsScope));
			tokens.add(Token.END_SEQUENCE_TOKEN);
			level--;
		}

		/** Start element. */
		public void startElement(String uri, String localName, String qname, Attributes attributes) throws SAXException {
			level++;
		
			boolean foundId = false;
			boolean foundIdREF = false;
			boolean foundIdREFS = false;
			
			String xmlId = null; 
			String xmlIdREF = null;
			String xmlIdREFS = null;

			String prefix = null;
			String localPart;
			boolean createdNSScope = false;
            int splitPos = qname.indexOf(':');
			if (splitPos > 0) {
				prefix = qname.substring(0,splitPos);
				localPart = qname.substring(splitPos+1);
			} else
				localPart = qname;
			QName qName = new QName(uri, prefix, localPart);
			NamedToken token = new NamedToken(Type.START_TAG, createNextTokenId(Type.START_TAG,qName.toString()), qName, curNsScope);
			tokens.add(token);
			tokensIndexStack.push(new Integer(tokensCount));
			this.tokensCount++;
			
			for (int i = 0; i < attributes.getLength(); i++) {
				int type;
				type = Type.UNTYPED_ATOMIC; //TODO: DTD types annotation, e.g. ID?
				
				try {
					QName attQname = new QName(attributes.getQName(i));
					String attVal = attributes.getValue(i);
					
					if (!foundId && (type == Type.ID || isXMLId(attQname,qName))) {
						foundId = true;
						xmlId = attVal;
					}
					if (!foundIdREF && (type == Type.IDREF || isIDREF(attQname,qName))) {
						foundIdREF = true;
						xmlIdREF = attVal;
					}
					
					if (!foundIdREFS && (type == Type.IDREFS || isIDREFS(attQname,qName))) {
						foundIdREFS = true;
						xmlIdREFS = attVal;
					}

					
					boolean newOpened = checkOpenNsScopeAddNs(createdNSScope, attQname, attVal);
					if (newOpened && !createdNSScope) 
						tokens.setElementAt(new NamedToken(Type.START_TAG, token.getId(), qName, curNsScope), ((Integer)tokensIndexStack.peek()).intValue());
									
					if (attQname.getNamespacePrefix() == null || attQname.getNamespacePrefix().equals("")) {
						if (attQname.getLocalPart().equals("xmlns")) 
							continue;
					} else if (attQname.getNamespacePrefix().equals("xmlns")) 
						continue;
						
				attQname.setNamespaceURI(attributes.getURI(i));
				NamedToken attToken = createAttributeToken(type, attributes.getValue(i), attQname, curNsScope);
				if (foundIdREF){ 
					attToken.setIDREF(xmlIdREF);
				}
				tokens.add(attToken);
				} catch (MXQueryException e) {
					// TODO Auto-generated catch block
					e.printStackTrace();
				}
				this.tokensCount++;
			}
			if (foundId) {
				int tokenIndex = ((Integer)tokensIndexStack.peek()).intValue();
				NamedToken nmToken = (NamedToken) tokens.elementAt(tokenIndex);
				nmToken.setID(xmlId);
			}
			if (foundIdREFS) {
				int tokenIndex = ((Integer)tokensIndexStack.peek()).intValue();
				NamedToken nmToken = (NamedToken) tokens.elementAt(tokenIndex);
				nmToken.setIDREFS(xmlIdREFS);
			}
		} // startElement(String,String,String,Attributes)


		/** End element. */
		public void endElement(String uri, String localName, String qname) throws SAXException {
			String prefix = null;
			String localPart;
			//String[] qNameParts = qname.split(":");
            int splitPos = qname.indexOf(':');
			if (splitPos > 0) {
				prefix = qname.substring(0,splitPos);
				localPart = qname.substring(splitPos+1);
			} else
				localPart = qname;

			QName qName = new QName(uri, prefix, localPart);
			NamedToken token = new NamedToken(Type.END_TAG, createNextTokenId(Type.END_TAG,qName.toString()), qName, curNsScope);
			tokens.add(token);
			tokensCount++;
			checkCloseNsScope();
			level--;
		} // endElement(String,String,String)

		public void characters(char[] ch, int start, int length) throws SAXException {
			StringBuffer text = new StringBuffer();
            text.append(ch,start,length);
//			for (int i = start; i < start + length; i++) {
//				text.append(ch[i]);
//			}
			int type = Type.TEXT_NODE_UNTYPED_ATOMIC;
			if (startCData)
				try {
				tokens.add(new TextToken(type,createNextTokenId(type,null), text.toString(), curNsScope));
				}catch (MXQueryException e) {
					throw new SAXException(e.toString());
				}
			else { 
				try {
					// Merge text nodes if needed
					// TODO: Maybe replace with method that does not look back, but collects the text and then produces a token
					Token tk = (Token)tokens.elementAt(tokensCount-1);
					if (Type.isTextNode(tk.getEventType())) {
						TextToken tokAppended = new TextToken(tk.getEventType(), tk.getId(),tk.getText()+text.toString(),curNsScope);
						tokens.setElementAt(tokAppended, tokensCount-1);
						tokensCount--;
					}
					else 
						tokens.add(new TextToken(type,createNextTokenId(type,null), text.toString(), curNsScope));
				} catch (MXQueryException e) {
					throw new SAXException(e.toString());
				}
			}
			tokensCount++;
		}

		public void error(SAXParseException e) throws SAXException {
			throw e;
		}

		public void fatalError(SAXParseException e) throws SAXException {
			throw e;
		}

		public void warning(SAXParseException e) throws SAXException {
			System.out.println("Warning: " + e.getMessage());
		}

		public void comment(char[] ch, int start, int end) throws SAXException {
			String text = new String(ch, start, end);
			try {
				tokens.add(new CommentToken(createNextTokenId(Type.COMMENT,null), text,curNsScope));
				tokensCount++;
			} catch (DynamicException e) {
				throw new SAXException(e);
			}
		}

		public void endCDATA() throws SAXException {
			startCData = false;
		}

		public void endDTD() throws SAXException {
		}

		public void endEntity(String arg0) throws SAXException {
			//System.out.println("Entity ended");
		}

		public void startCDATA() throws SAXException {
			startCData = true;
		}

		public void startDTD(String name, String publicID, String systemID) throws SAXException {
			systemid = systemID;
			publicid = publicID;
			dtdRootElem = name;
		}

		public void startEntity(String arg0) throws SAXException {
			//System.out.println("Entitiy started "+arg0);
		}

		public void attributeDecl(String name, String name2, String type,String mode, String value) throws SAXException {
			if (type.equals("ID"))idsVector.add(name+"#"+name2);
			if (type.equals("IDREF")) idRefVector.add(name+"#"+name2);
			if (type.equals("IDREFS")) idRefsVector.add(name+"#"+name2);
		}

	public void elementDecl(String name, String model) throws SAXException {
			//System.out.println("element decl"+model);
		}
	
     public void externalEntityDecl(String name, String publicId,
				String systemId) throws SAXException {
//			System.out.println("external decl");
			
		}

		public void internalEntityDecl(String name, String value)
				throws SAXException {
		//	System.out.println("internal decl");
			
		}
	}
	
	public NonSchemaValidatingSaxImportAdapter(Context ctx, QueryLocation loc,InputSource xml, boolean validateDTD, boolean tidyInput) {
		super(ctx,loc);
		adapter= new SaxAdapter(null);
		this.tokens = new Vector();
		tobeParsed = xml;
		this.tidyInput = tidyInput;
		this.validateDTD = validateDTD;
	}
	
	public NonSchemaValidatingSaxImportAdapter(Context ctx, QueryLocation loc,XMLReader source) {
		super(ctx,loc);
		adapter= new SaxAdapter(source);
		this.tokens = new Vector();
	}
	
	protected void init() throws MXQueryException {
		try { 
				adapter.parse();
			}
		 catch (SAXException e) {
			 String message = e.getMessage();
			throw new DynamicException(ErrorCodes.E0027_DYNAMIC_VALIDATE_UNEXPECTED_VALIDITY, message, loc);
			
		 } catch (IOException e) {
			throw new MXQueryException(ErrorCodes.A0007_EC_IO,"I/O Error while parsing",loc);
		}
	}

	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		return new NonSchemaValidatingSaxImportAdapter(this.context, loc,tobeParsed, true, false);
	}
	public Token next() throws MXQueryException {
		if (called == 0) {
			init();
		}
		if (called < tokens.size()) {
			called++; 
			return (Token)tokens.elementAt(called-1);
		}
		return Token.END_SEQUENCE_TOKEN;
	}
}

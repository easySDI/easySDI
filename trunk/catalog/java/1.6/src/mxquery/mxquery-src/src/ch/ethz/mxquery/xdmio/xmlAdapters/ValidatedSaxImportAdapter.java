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
import java.io.Reader;
import java.util.Stack;
import java.util.Vector;



import org.apache.xerces.xs.AttributePSVI;
import org.apache.xerces.xs.ElementPSVI;
import org.apache.xerces.xs.ItemPSVI;
import org.apache.xerces.xs.PSVIProvider;
import org.apache.xerces.xs.ShortList;
import org.apache.xerces.xs.XSComplexTypeDefinition;
import org.apache.xerces.xs.XSSimpleTypeDefinition;
import org.apache.xerces.xs.XSTypeDefinition;
import org.xml.sax.Attributes;
import org.xml.sax.InputSource;
import org.xml.sax.SAXException;
import org.xml.sax.SAXParseException;
import org.xml.sax.XMLReader;
import org.xml.sax.ext.DeclHandler;
import org.xml.sax.ext.LexicalHandler;
import org.xml.sax.helpers.DefaultHandler;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.contextConfig.XQStaticContext;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeDictionary;
import ch.ethz.mxquery.datamodel.xdm.CommentToken;
import ch.ethz.mxquery.datamodel.xdm.NamedToken;
import ch.ethz.mxquery.datamodel.xdm.ProcessingInstrToken;
import ch.ethz.mxquery.datamodel.xdm.TextToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.exceptions.StaticException;
import ch.ethz.mxquery.model.XDMIterator;

public class ValidatedSaxImportAdapter extends XDMImportAdapter {

	int mode;

	SaxValidatorAdapter val;
	
	XMLReader reader;
	
	InputSource tobeValidated;
	
	Vector tokens;

	Reader xml;

	boolean doc;


	class SaxValidatorAdapter extends DefaultHandler implements LexicalHandler, DeclHandler{
		
		private PSVIProvider psviProvider;

		private int tokensCount;

		private boolean startCData;

		private Stack tokensIndexStack;

		private ShortList listValueTypes = null;

		private boolean skipWhiteSpace;

		public SaxValidatorAdapter() {
			this.tokensIndexStack = new Stack();
		}

		public void validate() throws SAXException, IOException {
		//	SchemaFactory factory = SchemaFactory.newInstance(XMLConstants.W3C_XML_SCHEMA_NS_URI);

			// Setup SAX parser for schema validation.
			// spf.setFeature("http://xml.org/sax/features/xmlns-uris",true);
			reader.setErrorHandler(this);
			psviProvider = (PSVIProvider) reader;

			reader.setProperty("http://xml.org/sax/properties/lexical-handler", this);
			reader.setProperty("http://xml.org/sax/properties/declaration-handler", this);
			reader.setProperty("http://apache.org/xml/properties/schema/external-schemaLocation", Context.initDictionary().getSchemaLocations());
			reader.setContentHandler(this);
			reader.parse(tobeValidated);
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
			if (doc) {
				tokens.add(new Token(Type.START_DOCUMENT, createNextTokenId(Type.START_DOCUMENT,null), curNsScope));
				if (getURI() != null) 
					curNsScope.setBaseURI(getURI());
				tokensCount++;
				level++;
			}
		}

		public void endDocument() throws SAXException {
			if (doc) {
				tokens.add(new Token(Type.END_DOCUMENT, createNextTokenId(Type.END_DOCUMENT,null),curNsScope));
				level--;
			}
			tokens.add(Token.END_SEQUENCE_TOKEN);			
		}

		/** Start element. */
		public void startElement(String uri, String localName, String qname, Attributes attributes) throws SAXException {
		/*	if (text.length() > 0) {
				if (startCData) {
					if (!skipWhiteSpace)
					tokens.add(new CDataToken(createNextTokenId(Type.createTextNodeType(Type.UNTYPED),null), text.toString()));
				}
				else
				{   if (!skipWhiteSpace)
					tokens.add(new TextToken(createNextTokenId(Type.createTextNodeType(Type.UNTYPED),null), text.toString()));
				}
				if (!skipWhiteSpace)tokensCount++;
				}
				else text = new StringBuffer();
				skipWhiteSpace = false;	
		*/	
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
			tokensIndexStack.push(String.valueOf(tokensCount));
			this.tokensCount++;
			
			for (int i = 0; i < attributes.getLength(); i++) {
				int type;
				AttributePSVI attrPSVI = psviProvider.getAttributePSVI(i);
				if (attrPSVI != null) {
				short validationAttempted = attrPSVI.getValidationAttempted();
				short validity = attrPSVI.getValidity();
				
				XSTypeDefinition typeDefinition = attrPSVI.getTypeDefinition();

				type = typeAnnotation(validity, validationAttempted, typeDefinition, attrPSVI, true);
				
				 
				} else {
					type = Type.UNTYPED_ATOMIC; //TODO: DTD types annotation, e.g. ID?
				}
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
						tokens.setElementAt(new NamedToken(Type.START_TAG, token.getId(), qName, curNsScope), Integer.parseInt((String)tokensIndexStack.peek()));
									
					if (attQname.getNamespacePrefix() == null || attQname.getNamespacePrefix().equals("")) {
						if (attQname.getLocalPart().equals("xmlns")) 
							continue;
					} else if (attQname.getNamespacePrefix().equals("xmlns")) 
						continue;
						
				QName qn = new QName(attributes.getQName(i));	
				qn.setNamespaceURI(attributes.getURI(i));
				if (attrPSVI != null) {
					String attValue;
					if (foundId)
						attValue = attributes.getValue(i);
					else 
						attValue = psviProvider.getAttributePSVI(i).getSchemaNormalizedValue();
					NamedToken tok = createAttributeToken(type, attValue, qn, curNsScope);
					if (listValueTypes != null) {
						tok.setListValueTypes(listValueTypes);
						listValueTypes = null;		
					}
					tokens.add(tok);
				//	tokens.add(createAttributeToken(type, psviProvider.getAttributePSVI(i).getSchemaNormalizedValue(), qn, curNsScope));
				}
				else 
					tokens.add(createAttributeToken(type, attributes.getValue(i), qn, curNsScope));
				} catch (MXQueryException e) {
					// TODO Auto-generated catch block
					e.printStackTrace();
				}
				
				this.tokensCount++;
			}
			if (foundId) {
				int tokenIndex = Integer.parseInt((String) tokensIndexStack.peek());
				NamedToken nmToken = (NamedToken) tokens.elementAt(tokenIndex);
				nmToken.setID(xmlId);
			}
			if (foundIdREF){ 
			int tokenIndex = Integer.parseInt((String) tokensIndexStack.peek());
			NamedToken nmToken = (NamedToken) tokens.elementAt(tokenIndex);
			nmToken.setIDREF(xmlIdREF);
			}
			if (foundIdREFS) {
				int tokenIndex = Integer.parseInt((String) tokensIndexStack.peek());
				NamedToken nmToken = (NamedToken) tokens.elementAt(tokenIndex);
				nmToken.setIDREFS(xmlIdREFS);
			}
		} // startElement(String,String,String,Attributes)


		/** End element. */
		public void endElement(String uri, String localName, String qname) throws SAXException {
			/*if (text.length() > 0) {
			if (startCData) {
				if (!skipWhiteSpace)
				tokens.add(new CDataToken(createNextTokenId(Type.createTextNodeType(Type.UNTYPED),null), text.toString()));
			}
			else
			{   if (!skipWhiteSpace)
				tokens.add(new TextToken(createNextTokenId(Type.createTextNodeType(Type.UNTYPED),null), text.toString()));
			}
			if (!skipWhiteSpace)tokensCount++;
			}
			else text = new StringBuffer();
			skipWhiteSpace = false;*/
			
			String prefix = null;
			String localPart;
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

			ElementPSVI elemPSVI = psviProvider.getElementPSVI();
			if (elemPSVI != null) {
				short validationAttempted = elemPSVI.getValidationAttempted();
				short validity = elemPSVI.getValidity();
				int type = Type.UNTYPED;
				boolean isNillable = elemPSVI.getNil();
				XSTypeDefinition typeDefinition = elemPSVI.getTypeDefinition();

				type = typeAnnotation(validity, validationAttempted, typeDefinition, elemPSVI, false);
				
				int tokenIndex = Integer.parseInt((String) tokensIndexStack.pop());
				Token tok = (Token) tokens.elementAt(tokenIndex);

				if (schemaNormalizedValueNeeded(type))
					tok.setSchemaNormalizedValue(elemPSVI.getSchemaNormalizedValue());

				// annotate token
				if (isNillable)
					type = Type.setIsNilled(type); // set nillable
				int tokenType = tok.getEventType();
				tok.setEventType(tokenType | type);
			
				if (listValueTypes != null) {
					((NamedToken)tok).setListValueTypes(listValueTypes);
					listValueTypes = null;	
				}
				if (type == Type.IDREF) {
					((NamedToken)tok).setIDREF(elemPSVI.getSchemaNormalizedValue());
				}

			} else {
				// DTD, possible TODO (e.g. ID annotation)
			}
			checkCloseNsScope();
			level--;
		} // endElement(String,String,String)

		/**
		 * Checks whether storing the [schema normalized value] is necessary for a given token type
		 * @param type 
		 * @return true if the [schema normalized value] should be stored
		 */
		private boolean schemaNormalizedValueNeeded(int type) {
			int contentType = TypeDictionary.findContentType(type, Context.getDictionary());
			if (contentType == TypeDictionary.COMPLEX_SIMPLE || contentType == TypeDictionary.SIMPLE ||
					type == Type.ANY_SIMPLE_TYPE || type == Type.ANY_ATOMIC_TYPE)
			return true;
			else return false;
		}

		/**
		 * Specifies the exact type with which a token should be annotated after accessing the PSVI
		 * @param validity
		 * @param validationAttempted
		 * @param typeDefinition
		 * @param psvi
		 * @param isAttribute
		 * @return the type 
		 */
		private int typeAnnotation(short validity, short validationAttempted, XSTypeDefinition typeDefinition, ItemPSVI psvi, boolean isAttribute) {
			int type;
			if (isAttribute)
				type = Type.UNTYPED_ATOMIC;
			else
				type = Type.UNTYPED;
			if ((validity == ItemPSVI.VALIDITY_VALID) && (validationAttempted == ItemPSVI.VALIDATION_FULL)) {
				// XSTypeDefinition typeDefinition = elemPSVI.getTypeDefinition();
				if (typeDefinition.getTypeCategory() == XSTypeDefinition.SIMPLE_TYPE) { // check
					XSSimpleTypeDefinition simpleType = (XSSimpleTypeDefinition) typeDefinition;
					if (simpleType.getVariety() == XSSimpleTypeDefinition.VARIETY_UNION)
						typeDefinition = psvi.getMemberTypeDefinition();
					if (simpleType.getVariety() == XSSimpleTypeDefinition.VARIETY_LIST){
						listValueTypes  = psvi.getItemValueTypes();
					}
				}
				if (typeDefinition.getName() != null) { // check for anonymous type
					String namespace = typeDefinition.getNamespace();
					String name = typeDefinition.getName();
					QName typeQName = rewriteUDTQNameWithResolvedPrefix(name, namespace);
					try {
						type = Type.getTypeFootprint(typeQName, Context.getDictionary());
					} catch (MXQueryException e) {
						e.printStackTrace();
					}
				} else { // Xerces bug for accessing automatically generated name for anonymous types
					//boolean simple;
					String[] result;
	      			String[] tokens = typeDefinition.toString().split("'");
					if (tokens.length > 1)  
						result = tokens[1].split(",");
					else 
						result = tokens[0].split(","); //simple = true;
					
					String namespace = result[0];
					String name = result[1];
					try {
						if (Context.getDictionary().lookUpByName("{" + namespace + "}"+name)==null) 
							Context.getDictionary().addEntry("{" + namespace + "}" + name, typeDefinition);
						QName typeQName = rewriteUDTQNameWithResolvedPrefix(name, namespace);
						type = Type.getTypeFootprint(typeQName, Context.getDictionary());
					} catch (StaticException e) {
						e.printStackTrace();
					} catch (MXQueryException e) {
						e.printStackTrace();
					}
					
				 }
			} else if (validity == ItemPSVI.VALIDITY_INVALID || validationAttempted == ItemPSVI.VALIDATION_PARTIAL)
				if (isAttribute)
					type = Type.ANY_SIMPLE_TYPE;
				else
					type = Type.ANY_TYPE;
			else if (validity == ItemPSVI.VALIDITY_NOTKNOWN || validationAttempted == ItemPSVI.VALIDATION_NONE)
				if (isAttribute)
					type = Type.UNTYPED_ATOMIC;
				else
					type = Type.UNTYPED;

			return type;
		}

		private QName rewriteUDTQNameWithResolvedPrefix(String name, String namespace) {
			QName qName;
			if (namespace.equals(XQStaticContext.URI_XS))
				qName = new QName(Type.NAMESPACE_XS, name);
			else
				qName = new QName(namespace, name);
			return qName;
		}

		public void characters(char[] ch, int start, int length) throws SAXException {
			//handle white space correctly - in the case of XDM construction from PSVI ignore unless content is "mixed" 
			skipWhiteSpace = false;
			ElementPSVI elemPSVI = psviProvider.getElementPSVI();
			if (elemPSVI != null){
				skipWhiteSpace = true;
				XSTypeDefinition typeDef = elemPSVI.getTypeDefinition(); 
				if (typeDef.getTypeCategory() == XSTypeDefinition.COMPLEX_TYPE &&
					(((XSComplexTypeDefinition)typeDef).getContentType() == XSComplexTypeDefinition.CONTENTTYPE_MIXED))
					skipWhiteSpace = false;
			}
			StringBuffer text = new StringBuffer();
				
			for (int i = start; i < start + length; i++) {
				if (ch[i] != '\n' && ch[i] !=' ' && ch[i]!='\t') skipWhiteSpace = false;  
				text.append(ch[i]);
			}
			
			int type = Type.TEXT_NODE_UNTYPED_ATOMIC;
			
			if (startCData) {
				if (!skipWhiteSpace) try {
				tokens.add(new TextToken(type,createNextTokenId(type,null), text.toString(), curNsScope));
				}catch (MXQueryException e) {
					throw new SAXException(e.toString());
				}
			}
			else {
				if (!skipWhiteSpace) {
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
			}
			if (!skipWhiteSpace)tokensCount++;

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
			/*if (text.length() > 0) {
				if (startCData) {
					if (!skipWhiteSpace)
					tokens.add(new CDataToken(createNextTokenId(Type.createTextNodeType(Type.UNTYPED),null), text.toString()));
				}
				else
				{   if (!skipWhiteSpace)
					tokens.add(new TextToken(createNextTokenId(Type.createTextNodeType(Type.UNTYPED),null), text.toString()));
				}
				if (!skipWhiteSpace)tokensCount++;
				}
				else text = new StringBuffer();
				skipWhiteSpace = false;*/
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

		public void attributeDecl(String name, String name2, String type,
				String mode, String value) throws SAXException {
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
	
	public ValidatedSaxImportAdapter(int mode, Context ctx, QueryLocation loc, XMLReader saxSource, InputSource xml) {
		super(ctx,loc);
		this.mode = mode;
		reader = saxSource;
		val = new SaxValidatorAdapter();
		this.tokens = new Vector();
		tobeValidated = xml;
	}

	public ValidatedSaxImportAdapter(int mode, Context ctx, QueryLocation loc, XMLReader saxSource, Reader xml,boolean doc) {
		super(ctx,loc);
		this.mode = mode;
		reader = saxSource;
		val = new SaxValidatorAdapter();
		this.tokens = new Vector();
		tobeValidated = new InputSource(xml);
		this.xml = xml;
		this.doc = doc;
	//	this.text = new StringBuffer();
	}
	protected void init() throws MXQueryException {
		
		try { 
			val.validate();
		} catch (SAXException e) {
			String message = e.getMessage();
			if ((message.startsWith("cvc-elt.1:")) && mode != Context.SCHEMA_VALIDATION_LAX)
				throw new DynamicException(ErrorCodes.E0084_DYNAMIC_VALIDATE_STRICT_MISSING_TOP_LEVEL_DECL, message, loc);
			else if (message.startsWith("Content") || message.startsWith("Premature") || message.startsWith("The markup"))
				throw new StaticException(ErrorCodes.E0030_TYPE_VALIDATE_MORE_THAN_ONE_ELEMENT, message, loc);
			else if (message.startsWith("The markup"))
				throw new DynamicException(ErrorCodes.E0061_DYNAMIC_VALIDATE_MORE_THAN_ONE_ELEMENT, message, loc);
			else
				throw new DynamicException(ErrorCodes.E0027_DYNAMIC_VALIDATE_UNEXPECTED_VALIDITY, message, loc);
		} catch (IOException e) {
			throw new MXQueryException(ErrorCodes.A0007_EC_IO,"I/O Error while parsing",loc);
		}
	}

	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		return new ValidatedSaxImportAdapter(this.mode, this.context, loc, reader,xml,doc);
	}

	public Token next() throws MXQueryException {
		if (called == 0) {
			init();
		}
		if (called < tokens.size()) {
			called++;

//			Token tok = (Token)tokens.elementAt(called-1);
			return (Token)tokens.elementAt(called-1);
		}
		return Token.END_SEQUENCE_TOKEN;
	}

//	public String getDocDecl() {
//		if (it != null)
//			return it.getDocDecl();
//		else
//			return super.getDocDecl();
//	}

}

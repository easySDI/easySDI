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
import java.io.InputStream;
import java.io.Reader;
import java.util.Vector;

import org.kxml2.io.KXmlParser;
import org.xmlpull.v1.XmlPullParser;
import org.xmlpull.v1.XmlPullParserException;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.xdm.CommentToken;
import ch.ethz.mxquery.datamodel.xdm.NamedToken;
import ch.ethz.mxquery.datamodel.xdm.ProcessingInstrToken;
import ch.ethz.mxquery.datamodel.xdm.TextToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.util.KXmlSerializer;

/**
 * 
 * @author Matthias Braun
 * 
 */

public class XPPImportAdapter extends XDMImportAdapter  {

	private boolean endOfStream = false;

	private boolean peekedEvent = false;
	
	boolean inclDoc = false;


	private class XPPAdapter implements XmlPullParser {
		
		/** XmlPullParser Event Types: */

		private XmlPullParser p;

		protected Reader xml;

		private boolean startRead;


		private int flag_endtag = -1;

		int type = Type.START_SEQUENCE;

		private int attributeIndex = -1;

		private boolean endOfStream = false;
		
		QueryLocation loc;

		public XPPAdapter(Reader xml, XmlPullParser parser,QueryLocation location) throws MXQueryException {
			/**
			 * Constructor
			 * 
			 * @param xml
			 *            Reader that represents XML input (alternative 1)
			 * @param parser
			 * 			  Parser instance that is used for the XML input (alternative 2)           
			 * @param doc
			 *            declares, if the input should be treated as document
			 * @throws MXQueryException
			 */
			this.startRead = false;
			this.xml = xml;
			loc = location;
			if (parser == null) {

				p = new KXmlParser();

				try {
					p.setInput(xml);
					//p.setFeature(arg0, arg1)
				} catch (XmlPullParserException e) {
					throw new MXQueryException(ErrorCodes.A0007_EC_IO, e, "Error Processing XML input file", loc);
				}			
			} else
				p = parser;
		}

		// >> KXmlParser Methods start
		public void setFeature(String s, boolean flag) throws XmlPullParserException {
				p.setFeature(s, flag);
		}

		public boolean getFeature(String s) {
			return p.getFeature(s);
		}

		public void setProperty(String s, Object obj) throws XmlPullParserException {
			p.setProperty(s, obj);
		}

		public Object getProperty(String s) {
			return p.getProperty(s);
		}

		public void setInput(Reader reader) throws XmlPullParserException {
			p.setInput(reader);
		}

		public void setInput(InputStream inputstream, String s) throws XmlPullParserException {
			p.setInput(inputstream, s);
		}

		public String getInputEncoding() {
			return p.getInputEncoding();
		}

		public void defineEntityReplacementText(String s, String s1) throws XmlPullParserException {
				p.defineEntityReplacementText(s, s1);
		}

		public int getNamespaceCount(int i) throws XmlPullParserException {
			return p.getNamespaceCount(i);
		}

		public String getNamespacePrefix(int i) throws XmlPullParserException {
			return p.getNamespacePrefix(i);
		}

		public String getNamespaceUri(int i) throws XmlPullParserException {
			return p.getNamespaceUri(i);
		}

		public String getNamespace(String s) {
			return p.getNamespace(s);
		}

		public int getDepth() {
			if (this.attributeIndex < 0) {
				return p.getDepth();
			} else {
				return p.getDepth() + 1;
			}
		}

		public String getPositionDescription() {
			return p.getPositionDescription();
		}

		public int getLineNumber() {
			return p.getLineNumber();
		}

		public int getColumnNumber() {
			return p.getColumnNumber();
		}

		public boolean isWhitespace() throws XmlPullParserException {
			return p.isWhitespace();
		}

		public String getText() {
			if (this.attributeIndex < 0) {
				return p.getText();
			} else {
				return normalize(p.getAttributeValue(this.attributeIndex));
			}
		}

		public char[] getTextCharacters(int ai[]) {
			if (this.attributeIndex < 0) {
				return p.getTextCharacters(ai);
			} else {
				return null;
			}
		}

		public String getNamespace() {
			// TODO: StaticException would be nice!
			throw new RuntimeException("Getting namespaces explicitly not supported");
		}


		public String getName() {
			if (this.attributeIndex < 0) {
				return p.getName();
			} else {
				return p.getAttributeName(this.attributeIndex);
			}
		}

		public String getLocal() {
			if (this.attributeIndex < 0) {
				return QName.parseQName(p.getName())[1];
			} else {
				return QName.parseQName(p.getAttributeName(this.attributeIndex))[1];
			}
		}

		public String getPrefix() {
			if (this.attributeIndex < 0) {
				return p.getPrefix();
			} else {
				return p.getAttributePrefix(this.attributeIndex);
			}
		}

		public boolean isEmptyElementTag() throws XmlPullParserException {
			if (this.attributeIndex < 0) {
				return p.isEmptyElementTag();
			} else {
				return false;
			}
		}

		public String nextText() throws XmlPullParserException, IOException {
			return p.nextText();
		}

		// << KXmlParser Methods end

		private void testForNewNamespaces() throws XmlPullParserException, IOException {
			try {
				if (this.p.getEventType() == START_TAG) {
					boolean createdNSScope = false;
					for (int i = 0; i < p.getAttributeCount(); i++) {
						QName qname = new QName(p.getAttributeName(i));
						String attVal = p.getAttributeValue(i);
						createdNSScope = checkOpenNsScopeAddNs(createdNSScope,
								qname, attVal);
					}
				}
			} catch (MXQueryException e) {
				throw new XmlPullParserException(e.toString());
			}
		}

		private void testForEndNamespaces() throws XmlPullParserException, IOException {
			if (this.p.getEventType() == END_TAG) {
				flag_endtag = p.getDepth();
			}
		}

		public int next() throws XmlPullParserException, IOException {
			if (flag_endtag >= 0) {
				checkCloseNsScope();
				flag_endtag = -1;
			}
			if (endOfStream) {
				type = Type.END_SEQUENCE;
				return Type.END_SEQUENCE;
			}
			
			if (!startRead) {
				startRead = true;
				if (inclDoc) {
					type = Type.START_DOCUMENT;
					return Type.START_DOCUMENT;
				}
			}

				if (p.getAttributeCount() <= (this.attributeIndex + 1)) {
					this.attributeIndex = -1;
					// -------
					
					type = p.nextToken();
					if (type == DOCDECL) {
						systemid = p.getText(); // proper parsing needed
						type = p.nextToken();
					}
					
					// -------
					this.testForNewNamespaces();
					this.testForEndNamespaces();
					// --------
					type = getPreciseType();

				} else {
					this.attributeIndex++;
					// --------
					try {
						this.type = Type.createAttributeType(Type.UNTYPED_ATOMIC);
					} catch (TypeException e) {
						throw new XmlPullParserException(e.toString());
					}
					// this.type = this.parseAttribute();
					// --------
				}


			if (type == Type.END_DOCUMENT) {
				if (!inclDoc) {
					this.type = Type.END_SEQUENCE;
				}
				endOfStream = true;
			}
			return type;
		}

		public int getEventType() {
			if (!this.startRead) {
				return Type.START_SEQUENCE;
			} else {
				return this.type;
			}
		}

		public int getAttributeIndex() {
			return attributeIndex;
		}
		
		protected int getPreciseType() throws XmlPullParserException, IOException {
			if (endOfStream) {
				return Type.END_SEQUENCE;
			}

			int e;
				e = p.getEventType();

				// convert XmlPullParser type to MXQuery type:
				switch (e) {
				case START_TAG:
					e = Type.START_TAG;
					break;
				case END_TAG:
					e = Type.END_TAG;
					break;
				case TEXT:
				case CDSECT:
				case IGNORABLE_WHITESPACE:
					e = Type.UNTYPED;
					break;
				case START_DOCUMENT:
					e = Type.START_DOCUMENT;
					break;
				case END_DOCUMENT:
					e = Type.END_DOCUMENT;
					break;
				case PROCESSING_INSTRUCTION:
					e = Type.PROCESSING_INSTRUCTION;
					break;
				case COMMENT:
					e = Type.COMMENT;
					break;
				case ENTITY_REF:
					e = Type.ENTITY_REF;
					break;
				default:
					// DOCDECL:
					throw new RuntimeException("MXQuery type system doesn't support XmlPullParser type " + this.type);
				}

			if (e == Type.UNTYPED) {
				e = Type.TEXT_NODE_UNTYPED_ATOMIC;

				String s = getText();
				if (s.equals("")) {
					e = next();
				}
			}
			return e;
		}

		private String normalize(String s) {
			if (s.length() == 0) {
				return s;
			}
			while (s.charAt(0) == ' ' || s.charAt(0) == '\n' || s.charAt(0) == '\r' || s.charAt(0) == '\t') {
				if (s.length() == 1) {
					s = "";
					return s;
				}
				s = s.substring(1);
			}
			while (s.charAt(s.length() - 1) == ' ' || s.charAt(s.length() - 1) == '\n'
					|| s.charAt(s.length() - 1) == '\r' || s.charAt(s.length() - 1) == '\t') {
				if (s.length() == 1) {
					s = "";
					return s;
				}
				s = s.substring(0, s.length() - 1);
			}
			return s;
		}

		public void resetImpl() {
			try {
				p.setInput(xml);
			} catch (XmlPullParserException e) {
				// TODO: what to do here?
				// should reset / resetImpl generally throw an Exception?
				// see FunctionCallIterator
				e.printStackTrace();
			}

			endOfStream = false;
			startRead = false;
			type = Type.START_SEQUENCE;
			this.attributeIndex = -1;
		}

		public int getInt() {
			if (this.attributeIndex < 0) {
				return Integer.parseInt(this.normalize(p.getText()));
			} else {
				return Integer.parseInt(this.normalize(p.getAttributeValue(this.attributeIndex)));
			}
		}

		public void require(int i, String s, String s1) throws XmlPullParserException {
			throw new RuntimeException("Not supported anymore!");
		}

		public int nextTag() throws XmlPullParserException {
			throw new RuntimeException("Not supported anymore!");
		}

		/**
		 * Moves the pointer in the context item to the next token.
		 * 
		 * This method returns the result of next().
		 * 
		 * @result The event type of the next token
		 */
		public int nextToken() throws XmlPullParserException, IOException {
			return next();
		}

		public int getAttributeCount() {
			throw new RuntimeException("Not supported anymore!");
		}

		public String getAttributeNamespace(int i) {
			throw new RuntimeException("Not supported anymore!");
		}

		public String getAttributeName(int i) {
			return p.getAttributeName(i);
		}

		public String getAttributePrefix(int i) {
			throw new RuntimeException("Not supported anymore!");
		}

		public String getAttributeType(int i) {
			throw new RuntimeException("Not supported anymore!");
		}

		public boolean isAttributeDefault(int i) {
			throw new RuntimeException("Not supported anymore!");
		}

		public String getAttributeValue(int i) {
			throw new RuntimeException("Not supported anymore!");

		}

		public String getAttributeValue(String s, String s1) {
			throw new RuntimeException("Not supported anymore!");
		}
	}

	XPPAdapter xppParser;
	
	

	public XPPImportAdapter(Context ctx, Reader xml, boolean doc, QueryLocation location) throws MXQueryException {
		super(ctx,location);
		scopeDepth.push(0);
		inclDoc = doc;
		xppParser = new XPPAdapter(xml, null, loc);
	}

	public XPPImportAdapter(Context ctx, XmlPullParser pullParser, boolean doc, QueryLocation location) throws MXQueryException {
		super(ctx,location);
		scopeDepth.push(0);
		inclDoc = doc;
		xppParser = new XPPAdapter(null, pullParser, loc);
	}	
	
	public String getNS() throws MXQueryException {
		String name = null;
		String namespace = null;
		if (xppParser.getAttributeIndex() >= 0) {
			name = xppParser.getAttributeName(xppParser.getAttributeIndex());
		} else {
			int type = xppParser.getEventType();
			if (type == Type.START_TAG || type == Type.END_TAG || Type.isAttribute(type)) {
				name = xppParser.getName();
			} else {
				return null;
			}
		}

		String str[] = QName.parseQName(name);
		if (str[0] == null || str[0].equals("")) {
			if (xppParser.getAttributeIndex() < 0) {
				namespace = curNsScope.getNsURI("");
			}
		} else {
			namespace = curNsScope.getNsURI(str[0]);
		}
		if (namespace == null) {
			// throw generateStaticError(StaticException.SYNTAX_ERROR,
			// "err:XPST0081", "Error while parsing: 'StringLiteral'
			// expected!");
		}

		return namespace;
	}	
	
	public Token next() throws MXQueryException {
		called++;
		if (endOfStream) {
			return Token.END_SEQUENCE_TOKEN;
		}

		// If the previous peek has already produced the next event, just return
		// this
		if (peekedEvent) {
			peekedEvent = false;
			Token tok = createTokenForCurrentEvent(xppParser);
			if (tok != null)
				return tok;
			else
				return next();
		}
		
		try {
			int event = xppParser.next();
			if (event == Type.END_SEQUENCE)
				endOfStream = true;
			// Fix for spurious linebreaks before/after the root node
			if ((level == 0 || inclDoc && level == 1) && 
					Type.getTextNodeValueType(event) == Type.UNTYPED_ATOMIC && 
					xppParser.getText().equals("\n"))
				event = xppParser.next();
			
			// Combine adjacent text nodes and entity references
			if (Type.getTextNodeValueType(event) == Type.UNTYPED || event == Type.ENTITY_REF) {
				peekedEvent = true;
				String returnText = "";
				do {
					if (Type.getTextNodeValueType(event) == Type.UNTYPED)
						returnText = returnText + xppParser.getText();
					else
						returnText = returnText + changeEntityRef(xppParser.getName());
					event = xppParser.next();
				} while (Type.getTextNodeValueType(event) == Type.UNTYPED || event == Type.ENTITY_REF);
				int evType = Type.TEXT_NODE_UNTYPED_ATOMIC;
				Token token;
				// also remove spurious text after the root node
				if ((level == 0 || inclDoc && level == 1) && event == Type.END_DOCUMENT) {
					token = new Token(event,null, curNsScope);
					peekedEvent = false;
				}else 
					token = new TextToken(evType, createNextTokenId(evType, null), returnText, curNsScope);				
				return token;
				

			}
		} catch (XmlPullParserException e) {
			throw new MXQueryException(ErrorCodes.A0007_EC_IO,"General Error Parsing XML input",loc);
		}
		catch (IOException e) {
			// TODO Auto-generated catch block
			throw new MXQueryException(ErrorCodes.A0007_EC_IO,"I/O Error Parsing XML input",loc);
		}
		peekedEvent = false;
		Token tok = createTokenForCurrentEvent(xppParser);
		if (tok != null)
			return tok;
		else
			return next();
	}

	public KXmlSerializer traverseIteratorTree(KXmlSerializer serializer) throws Exception {
		this.createIteratorStartTag(serializer);
		// TODO: Should work again
		// serializer.text(xml);
		this.createIteratorEndTag(serializer);
		return serializer;
	}



	protected Token createTokenForCurrentEvent(XPPAdapter seq) throws MXQueryException {
		int eventType = seq.getEventType();
		int typeToCheck = eventType;
		//String seqName;

		boolean isAttribute = false;
		if (Type.isAttribute(eventType)) {
			isAttribute = true;
			eventType = Type.getAttributeValueType(eventType);
			typeToCheck = eventType;
		} else if (Type.isTextNode(eventType)) {
			typeToCheck = Type.getTextNodeValueType(eventType);
		}
		Token token = null;
		switch (typeToCheck) {
		case Type.END_SEQUENCE:
			token = Token.END_SEQUENCE_TOKEN;
			break;
		case Type.START_SEQUENCE:
			token = Token.START_SEQUENCE_TOKEN;
			break;
		case Type.START_DOCUMENT:
			token = new Token(eventType, createNextTokenId(eventType, null),curNsScope);
			if (getURI()!= null)
				curNsScope.setBaseURI(getURI());
			level = 1;
			break;
		case Type.END_DOCUMENT:	
			token = new Token(eventType,null,curNsScope);
			break;
		case Type.START_TAG:
			//seqName = seq.getName();
			QName startName = new QName(seq.getName());
			startName.setNamespaceURI(getNS());
			token = new NamedToken(eventType,createNextTokenId(eventType, seq.getName()),startName,curNsScope);
			level++;
			break;
			
		case Type.END_TAG:
			QName endName = new QName(seq.getName());
			endName.setNamespaceURI(getNS());
			token = new NamedToken(eventType,null,endName, curNsScope);
			//seqName = seq.getName();
			level--;
			break;
		
		case Type.UNTYPED_ATOMIC:
			if (isAttribute) {
				/*
				 * Removing Namespace declaration from the attribute comparision!
				 */
				String str[] = QName.parseQName(seq.getName());
				if(str[0] == null && str[1].equals("xmlns") || (str[0] != null && str[0].equals("xmlns"))){
					// skip NS attributes
					return null;
				}
				QName qn = new QName(seq.getName());
				qn.setNamespaceURI(getNS());
				token = createAttributeToken(Type.UNTYPED_ATOMIC, seq.getText(), qn, curNsScope);
			} else {
				if (Type.isTextNode(eventType)) {
					token = new TextToken(Type.TEXT_NODE_UNTYPED_ATOMIC, createNextTokenId(eventType, null),
							seq.getText(),curNsScope);					
				}
				else
				throw new RuntimeException("Untyped atomic should not occur 'standalone'");
			}
			break;
		case Type.UNTYPED: 
				
			token = new TextToken(Type.TEXT_NODE_UNTYPED_ATOMIC, createNextTokenId(eventType, null),
					seq.getText(),curNsScope);
			break;
		case Type.COMMENT:
			token = new CommentToken(createNextTokenId(eventType, null), seq.getText(),curNsScope);
			break;
		case Type.PROCESSING_INSTRUCTION:
			String val = seq.getText();
			// split into "name" and "content"
			int delim = val.indexOf(' ');
			String name;
			String content = "";
			if (delim < 0 )
				name = val;
			else {
				name = val.substring(0, delim);
				content = val.substring(delim+1);
			}
			token = new ProcessingInstrToken(createNextTokenId(eventType, null),content, name,curNsScope);
			break;
		case Type.ENTITY_REF:			
			token = new TextToken(Type.TEXT_NODE_UNTYPED_ATOMIC,createNextTokenId(eventType, null),
					changeEntityRef(seq.getName()),curNsScope);
			
			break;

		default:
			throw new IllegalArgumentException("Got an eventType " + eventType + " which is unknown");
		}
		return token;
	}

	public void resetImpl() {
		xppParser.resetImpl();
		endOfStream = false;
	}
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		return new XPPImportAdapter(context, xppParser.xml, inclDoc,loc);
	}	
	
}

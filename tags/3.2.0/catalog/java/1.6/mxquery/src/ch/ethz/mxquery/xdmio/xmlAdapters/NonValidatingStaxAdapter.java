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

import java.util.LinkedList;
import java.util.Vector;

import javax.xml.stream.XMLStreamConstants;
import javax.xml.stream.XMLStreamException;
import javax.xml.stream.XMLStreamReader;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.xdm.CommentToken;
import ch.ethz.mxquery.datamodel.xdm.NamedToken;
import ch.ethz.mxquery.datamodel.xdm.ProcessingInstrToken;
import ch.ethz.mxquery.datamodel.xdm.TextToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.model.XDMIterator;

public class NonValidatingStaxAdapter extends XDMImportAdapter {
	private XMLStreamReader reader;

	private LinkedList tokensList;

	public NonValidatingStaxAdapter(Context ctx, QueryLocation loc, XMLStreamReader reader) {
		super(ctx, loc);
		this.reader = reader;
		this.tokensList = new LinkedList();
	}

	private Token getNext() {
		Token tok = Token.END_SEQUENCE_TOKEN;
		if (endOfSeq)
			return tok;
		try {
			int event = reader.getEventType();
				switch (event) {
				case XMLStreamConstants.ATTRIBUTE:
					break;
				case XMLStreamConstants.DTD:
					break;
				case XMLStreamConstants.CDATA:
				case XMLStreamConstants.CHARACTERS:
				case XMLStreamConstants.SPACE:
				{
					StringBuffer mergedText = new StringBuffer(); 
					while (event == XMLStreamConstants.CDATA || event == XMLStreamConstants.SPACE || event == XMLStreamConstants.CHARACTERS) {
						mergedText.append(reader.getText());
						reader.next();
						event = reader.getEventType();
					}
					return new TextToken(Type.TEXT_NODE_UNTYPED_ATOMIC, createNextTokenId(Type.TEXT_NODE_UNTYPED_ATOMIC, null), mergedText.toString(),curNsScope);
				}
				case XMLStreamConstants.COMMENT:
					tok = new CommentToken(createNextTokenId(Type.COMMENT, null), reader.getText(),curNsScope);
					break;
				case XMLStreamConstants.END_DOCUMENT:
					tok = new Token(Type.END_DOCUMENT,createNextTokenId(Type.END_DOCUMENT, null),curNsScope);
					break;
				case XMLStreamConstants.END_ELEMENT:
					String name = reader.getLocalName();
					String ns_uri = reader.getNamespaceURI();
					String prefix = reader.getPrefix();
					if (prefix != null && prefix.length() == 0)
						prefix = null;
					QName tName = new QName(ns_uri, prefix, name);
					tok = new NamedToken(Type.END_TAG, createNextTokenId(Type.END_TAG, tName.toString()), tName, curNsScope);
					checkCloseNsScope();
					level--;
					break;
				case XMLStreamConstants.ENTITY_DECLARATION: // not used
					break;
				case XMLStreamConstants.ENTITY_REFERENCE:
					break;
				case XMLStreamConstants.NAMESPACE:
					break;
				case XMLStreamConstants.NOTATION_DECLARATION:
					tok = new TextToken(createNextTokenId(Type.NOTATION, null), reader.getText());
					break;
				case XMLStreamConstants.PROCESSING_INSTRUCTION:
					tok = new ProcessingInstrToken(createNextTokenId(Type.PROCESSING_INSTRUCTION, null), reader.getPIData(), reader.getPITarget(),curNsScope);
					break;
				case XMLStreamConstants.START_DOCUMENT:
					tok = new Token(Type.START_DOCUMENT,createNextTokenId(Type.START_DOCUMENT, null),curNsScope);
					break;
				case XMLStreamConstants.START_ELEMENT:
					level++;
					
					boolean foundId = false;
					boolean foundIdREF = false;
					boolean foundIdREFS = false;
					
					String xmlId = null; 
					String xmlIdREF = null;
					String xmlIdREFS = null;
					
					boolean createdNSScope = false;
					name = reader.getLocalName();
					ns_uri = reader.getNamespaceURI();
					prefix = reader.getPrefix();

					if (prefix !=  null &&  prefix.length() == 0)
						prefix = null;
					tName = new QName(ns_uri, prefix, name);
					tok = new NamedToken(Type.START_TAG, createNextTokenId(Type.START_TAG, tName.toString()), tName, curNsScope);

					for (int i = 0; i < reader.getNamespaceCount(); i++) {
						String namespacePrefix = reader.getNamespacePrefix(i);
						String namespaceUri = reader.getNamespaceURI(i);
						QName q;
						if (namespacePrefix == null)
							q = new QName(null, "xmlns");
						else
							q = new QName("xmlns", namespacePrefix);
						boolean newOpened = checkOpenNsScopeAddNs(createdNSScope, q, namespaceUri);
						if (newOpened && !createdNSScope)
							tok = new NamedToken(Type.START_TAG, tok.getId(), tName, curNsScope);
					}

					for (int i = 0, n = reader.getAttributeCount(); i < n; ++i) {
						javax.xml.namespace.QName qName = reader.getAttributeName(i);
						name = qName.getLocalPart();
						ns_uri = qName.getNamespaceURI();
						prefix = qName.getPrefix();

						
						
						if (prefix.length() == 0)
							prefix = null;
						QName q = new QName(ns_uri, prefix, name);
						String value = reader.getAttributeValue(i);
						
						String attType = reader.getAttributeType(i);
						
						if (!foundId && (attType.equals("ID") || isXMLId(tName,q))) {
							foundId = true;
							xmlId = value;
						}
						if (!foundIdREF && (attType.equals("IDREF") || isIDREF(tName,q))) {
							foundIdREF = true;
							xmlIdREF = value;
						}
						
						if (!foundIdREFS && (attType.equals("IDREFS") || isIDREFS(tName,q))) {
							foundIdREFS = true;
							xmlIdREFS = value;
						}

						
						boolean newOpened = checkOpenNsScopeAddNs(createdNSScope, q, value);
						if (newOpened && !createdNSScope)
							tok = new NamedToken(Type.START_TAG, tok.getId(), tName, curNsScope);

						if (q.getNamespacePrefix() == null || q.getNamespacePrefix().equals("")) {
							if (q.getLocalPart().equals("xmlns"))
								continue;
						} else if (q.getNamespacePrefix().equals("xmlns"))
							continue;
						NamedToken attToken = createAttributeToken(Type.UNTYPED_ATOMIC, value, q, curNsScope);
						if (foundIdREF){ 
							attToken.setIDREF(xmlIdREF);
						}
						tokensList.add(attToken);
						
						if (foundId) {
							NamedToken nmToken = (NamedToken) tok ;
							nmToken.setID(xmlId);
						}
						if (foundIdREFS) {
							NamedToken nmToken = (NamedToken) tok;
							nmToken.setIDREFS(xmlIdREFS);
						}

						
					}
					break;
				default:
				}
				if (reader.hasNext())
					reader.next();
				else {
					endOfSeq = true;
				}
				return tok;
		} catch (XMLStreamException e) {
			e.printStackTrace();
		} catch (MXQueryException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
			try {
				reader.close();
			} catch (XMLStreamException se) {
				// TODO Auto-generated catch block
				se.printStackTrace();
			}
		}
		return Token.END_SEQUENCE_TOKEN;
	}

	public Token next() throws MXQueryException {
		if (!tokensList.isEmpty()) {
			Token tok = (Token) tokensList.removeFirst();
			return tok;//(Token) tokensList.remove();
		}
		else {
			Token tok = getNext();
			return tok;
		}
	}

	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		return new NonValidatingStaxAdapter(context, loc, reader);
	}
}

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

import java.util.Vector;

import ch.ethz.mxquery.datamodel.QName;

import org.w3c.dom.Attr;
import org.w3c.dom.DocumentType;
import org.w3c.dom.Element;
import org.w3c.dom.NamedNodeMap;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;
import org.w3c.dom.ProcessingInstruction;


import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.contextConfig.XQStaticContext;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.xdm.CommentToken;
import ch.ethz.mxquery.datamodel.xdm.NamedToken;
import ch.ethz.mxquery.datamodel.xdm.ProcessingInstrToken;
import ch.ethz.mxquery.datamodel.xdm.TextToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.model.XDMIterator;

public class NonValidatingDOMAdapter extends XDMImportAdapter {

	Vector tokens;
	
	Attr prevAttr;
	
	int parentStartTokenIndex;
	boolean createdNsScope = false;
	
	Node input;
	
	public NonValidatingDOMAdapter(Context ctx, QueryLocation loc, Node nd) {
		super(ctx, loc);
		this.input = nd;
		tokens = new Vector();
	}

	protected XDMIterator copy(Context context, XDMIterator[] subIters,
			Vector nestedPredCtxStack) throws MXQueryException {
		// TODO Auto-generated method stub
		return null;
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
	
	public void processNode(Node nd) {
		try {
			String value = nd.getNodeValue();
			int type = nd.getNodeType();
			switch (type) {
			case Node.DOCUMENT_NODE:
				tokens.add(new Token(Type.START_DOCUMENT, createNextTokenId(Type.START_DOCUMENT,null),curNsScope));
				parentStartTokenIndex = tokens.size()-1;
				processChildren(nd);
				tokens.add(new Token(Type.END_DOCUMENT, createNextTokenId(Type.END_DOCUMENT,null),curNsScope));
				break;
			case Node.ELEMENT_NODE:
				String localname = nd.getLocalName();
				QName name;
				if (localname != null) {
					name =  new QName(nd.getNamespaceURI(), nd.getPrefix(), localname);
				} else {
					name = new QName(nd.getNodeName());
				}
				// add startelementtoken
				tokens.add(new NamedToken(Type.START_TAG, createNextTokenId(Type.START_TAG, name.toString()), name, curNsScope));
				parentStartTokenIndex = tokens.size()-1;
				createdNsScope = false;
				// add attributes
				Element elementnd = (Element)nd;
				NamedNodeMap attrMap = elementnd.getAttributes();
				for (int i = 0; i < attrMap.getLength(); i++) {
					processNode(attrMap.item(i));
				}
				// add children
				processChildren(nd);
				// add endelementtoken
				tokens.add(new NamedToken(Type.END_TAG, createNextTokenId(Type.END_TAG, name.toString()), name, curNsScope));
				if (createdNsScope) {
					checkCloseNsScope();
					createdNsScope = false;
				}
				break;
			case Node.CDATA_SECTION_NODE:
			case Node.TEXT_NODE:
				if (nd.getPreviousSibling() != null && (nd.getPreviousSibling().getNodeType() == Node.TEXT_NODE || nd.getPreviousSibling().getNodeType() == Node.CDATA_SECTION_NODE)) {
					//merge
					TextToken previousSiblingToken = (TextToken)tokens.lastElement();
					TextToken mergedToken = new TextToken(previousSiblingToken.getEventType(), previousSiblingToken.getId(), previousSiblingToken.getText() + value, curNsScope);
					tokens.setElementAt(mergedToken, tokens.size()-1);
				} else {
					tokens.add(new TextToken(Type.TEXT_NODE_UNTYPED_ATOMIC, createNextTokenId(Type.TEXT_NODE_UNTYPED_ATOMIC, null), value, curNsScope));
				}
				break;
			case Node.ATTRIBUTE_NODE:
				Attr attrnd = (Attr)nd;
				String attrlocalname = attrnd.getLocalName();
				QName attqname;
				if (attrlocalname != null) {
					attqname =  new QName(attrnd.getNamespaceURI(), attrnd.getPrefix(), attrlocalname);
				} else {
					attqname = new QName(attrnd.getName());
				}
				// is Namespace?
				createdNsScope = checkOpenNsScopeAddNs(createdNsScope, attqname, value);
				boolean isSpecAttribute = false;
				boolean isNSAttribute = false;
				if (attqname.getNamespacePrefix() == null || attqname.getNamespacePrefix().equals("")) {
					if (attqname.getLocalPart().equals(XQStaticContext.NS_XMLNS)) {
						isSpecAttribute = true;
						isNSAttribute = true;
					}
				} else {
					if (attqname.getNamespacePrefix().equals(XQStaticContext.NS_XMLNS)) {
						isSpecAttribute = true;
						isNSAttribute = true;
					}
					if (attqname.getLocalPart().equals("base")) {
						isSpecAttribute = true;
					}
					if (attqname.getLocalPart().equals("lang")) {
						isSpecAttribute = true;;
					}
				}
				if (isSpecAttribute) {
					NamedToken parentStartToken = (NamedToken)tokens.get(parentStartTokenIndex);
					tokens.setElementAt(new NamedToken(Type.START_TAG, parentStartToken.getId(), new QName(parentStartToken.getNS(), parentStartToken.getPrefix(), parentStartToken.getLocal()), curNsScope), parentStartTokenIndex);
					if (isNSAttribute)
						break;
				}
				String atttype = attrnd.getSchemaTypeInfo().getTypeName();
				if (atttype == null)
					atttype = "";
				Element parentnd = attrnd.getOwnerElement();
				QName parentQname = new QName(parentnd.getNamespaceURI(), parentnd.getPrefix(), parentnd.getLocalName());
				tokens.add(createAttributeToken(Type.UNTYPED_ATOMIC, value, attqname, curNsScope));
				// is ID, IDREF, IDREFS?
				if (atttype.equals("ID") || isXMLId(attqname, parentQname)) {
					((NamedToken)tokens.get(parentStartTokenIndex)).setID(value); //set id to parents start token
				} else if (atttype.equals("IDREF") || isIDREF(attqname, parentQname)) {
					((NamedToken)tokens.get(tokens.size()-1)).setIDREF(value); //set idref to attributes token
				} else if (atttype.equals("IDREFS") || isIDREFS(attqname, parentQname)) {
					((NamedToken)tokens.get(tokens.size()-1)).setIDREFS(value); //set idrefs to parents start token
				}
				break;
			case Node.PROCESSING_INSTRUCTION_NODE:
				ProcessingInstruction pind = (ProcessingInstruction)nd;
				tokens.add(new ProcessingInstrToken(createNextTokenId(Type.PROCESSING_INSTRUCTION,null), pind.getData(), pind.getTarget(), curNsScope));
				break;
			case Node.COMMENT_NODE:
				tokens.add(new CommentToken(createNextTokenId(Type.COMMENT,null), value, curNsScope));
				break;
			case Node.DOCUMENT_FRAGMENT_NODE:
				throw new MXQueryException(ErrorCodes.A0002_EC_NOT_SUPPORTED, "DOCUMENT_FRAGMENT_NODE not supported yet.", null);
			case Node.DOCUMENT_TYPE_NODE:	
				DocumentType docType = (DocumentType)nd;
				systemid = docType.getSystemId();
				publicid = docType.getPublicId();
				dtdRootElem = docType.getName();
				break;
			case Node.ENTITY_NODE:
			case Node.ENTITY_REFERENCE_NODE:
				throw new MXQueryException(ErrorCodes.A0002_EC_NOT_SUPPORTED, "DTD not supported yet.", null);
			default: return;
			}
		} catch (MXQueryException e) {
			e.printStackTrace();
		}
	}
	
	public void processChildren(Node nd) {
		if (nd.hasChildNodes()) {
			NodeList children = nd.getChildNodes();
			for (int i = 0; i < children.getLength(); i++) {
				processNode(children.item(i));
			}
		}
	}

	private void init() throws MXQueryException {
		processNode(input);
	}

}

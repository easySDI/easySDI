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

package ch.ethz.mxquery.iterators;


import java.util.Vector;

import ch.ethz.mxquery.bindings.WindowFactory;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.contextConfig.XQStaticContext;
import ch.ethz.mxquery.datamodel.Identifier;
import ch.ethz.mxquery.datamodel.IdentifierFactory;
import ch.ethz.mxquery.datamodel.Namespace;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.Source;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.xdm.NamedToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.datamodel.xdm.UntypedAtomicAttrToken;
import ch.ethz.mxquery.datamodel.xdm.XDMScope;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.exceptions.StaticException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.util.KXmlSerializer;
import ch.ethz.mxquery.util.Utils;

/**
 * Event of an Attribute in XML ('attributename="attributevalue"')
 * 
 * @author David Alexander Graf
 * 
 */
public class XMLAttrIterator extends TokenBasedIterator implements Source {

	private NamedToken token = null;
	XDMScope curNsScope;
	private boolean generateNodeIds = true;
	Identifier identifier = null;
	private static int nodeIdCount = 0;
	
	private final static String uri = "http://www.mxquery.org/nodeconstruction";
	static int docs = 0;
	private String docId;
	
	public XMLAttrIterator(Context ctx, XDMIterator nameExpr, XDMIterator [] expr, QueryLocation location, XDMScope nsScope) throws MXQueryException {
		super(ctx, location);
		curNsScope = nsScope;
		this.subIters = new XDMIterator[expr.length+1];
		subIters[0] = nameExpr;
		docId = uri+docs++;
		for (int i = 1;i<expr.length+1;i++)
			subIters[i] = expr[i-1];

	}

	public XMLAttrIterator(Context ctx, NamedToken token, QueryLocation location, XDMScope nsScope) {
		super(ctx, location);
		this.token = token;
	}

	protected void init() throws MXQueryException {

		if (subIters != null) {
			// compute name if necessary
			if (token == null) {
				token = computeCompAttrConstructor(subIters[0]);
			}
			
			boolean isIdAttr = false;

			QName attrQName = new QName(token.getName());
			
			if ((attrQName.getLocalPart().equals("id")||attrQName.getLocalPart().equals("idref"))&&
					(XQStaticContext.URI_XML).equals(token.getNS()))
					isIdAttr = true;			
			
			StringBuffer completeAttrValue = new StringBuffer();
			for (int j=1;j<subIters.length;j++) {
				String value = "";
				currentToken = subIters[j].next();
				// FIXME: do a real copy here if this is already an attribute
				Vector tokens = new Vector();
				while (currentToken.getEventType() != Type.END_SEQUENCE) {
					tokens.addElement(currentToken);
					currentToken = subIters[j].next();
				}

				
				if (tokens.size() == 1) {
					Token t = (Token) tokens.elementAt(0);
					if (isIdAttr)
						value += Utils.normalizeString(t.getValueAsString());
					else
						value += Utils.normalizeStringContent(t.getValueAsString(), isIdAttr);
				} else if (tokens.size() > 1){
					for (int i = 0; i < tokens.size()-1; i++) {
						Token t = (Token) tokens.elementAt(i);
						value += Utils.normalizeStringContent(t.getValueAsString(), isIdAttr);
						value += " ";
					}
					value += Utils.normalizeStringContent(((Token) tokens.elementAt(tokens.size()-1)).getValueAsString(), isIdAttr);
					value = value.trim();
					if (isIdAttr)
						value = Utils.normalizeString(value);
				}
				completeAttrValue.append(value);
			}
			currentToken = new UntypedAtomicAttrToken(createNextTokenId(), completeAttrValue.toString(), new QName(token.getName()),curNsScope);
			currentToken.setNS(token.getNS());


		} else {
			//Namespace declarations are always static
			currentToken = token;
		}
	}

	protected KXmlSerializer createIteratorStartTag(KXmlSerializer serializer) throws Exception {
		super.createIteratorStartTag(serializer);
		if (token != null)
			serializer.attribute("", "name", this.token.getName());
		return serializer;
	}
	
	private NamedToken computeCompAttrConstructor(XDMIterator it) throws TypeException, MXQueryException, DynamicException, StaticException {
		Token next = it.next();
		if (it.next().getEventType() != Type.END_SEQUENCE)
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,"Need a single item to construct the attribute name", loc);
		NamedToken tok = null;
		QName eleTargetQn;
		if ( !(Type.isTypeOrSubTypeOf(next.getEventType(),Type.STRING,Context.getDictionary()) || next.getEventType() == Type.UNTYPED_ATOMIC || 
				next.getEventType() == Type.QNAME || next.getEventType() == Type.UNTYPED))
				throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,"Incorrect type of the parameter in Computed Attribute Constructor: " + Type.getTypeQName(next.getEventType(), Context.getDictionary()), loc );

		
		if (next.getEventType() == Type.QNAME) {
			eleTargetQn = next.getQNameTokenValue();
			if ((eleTargetQn.getNamespacePrefix() != null || context.getPrefix("") != null) && eleTargetQn.getNamespaceURI() == null)
				eleTargetQn = eleTargetQn.resolveQNameNamespace(context);
			if (eleTargetQn.getNamespaceURI() != null && eleTargetQn.getNamespacePrefix() == null) {
				String prefix = curNsScope.getPrefix(eleTargetQn.getNamespaceURI());
				if (prefix != null)
					eleTargetQn = new QName (eleTargetQn.getNamespaceURI(), prefix, eleTargetQn.getLocalPart());
				else  {
					eleTargetQn = new QName (eleTargetQn.getNamespaceURI(), "pref", eleTargetQn.getLocalPart());
					curNsScope.addNamespace("pref", eleTargetQn.getNamespaceURI());
				}
			}
			// Add namespace binding to Scope
			if ((eleTargetQn.getNamespacePrefix() != null || context.getPrefix("") != null) && eleTargetQn.getNamespaceURI() != null) {
				// Do we need to check for previous, possibly conflicting bindings?
				curNsScope.addNamespace(eleTargetQn.getNamespacePrefix(), eleTargetQn.getNamespaceURI());
			}
		} else {
			try {
				eleTargetQn = new QName(next.getText());
			} catch (DynamicException de) {
				if (de.getErrorCode().equals(ErrorCodes.F0005_INVALID_LEXICAL_VALUE))
					throw new DynamicException(ErrorCodes.E0074_DYNAMIC_NAME_EXPRESSION_CANNOT_BE_CONVERTED_TO_QNAME, "Invalid QName "+next.getText(), loc); 
				else 
					throw de;
			} 

		if(eleTargetQn.getNamespacePrefix() != null || next.getNS() != null){
			Namespace ns = null;
			String nsString = null;
			if((nsString = next.getNS()) != null) {
				eleTargetQn.setNamespaceURI(nsString);
			} else if ((ns = context.getNamespace(eleTargetQn.getNamespacePrefix())) != null) {
				eleTargetQn.setNamespaceURI(ns.getURI());
				curNsScope.addNamespace(eleTargetQn.getNamespacePrefix(), ns.getURI());
			} else 
				throw new StaticException(ErrorCodes.E0074_DYNAMIC_NAME_EXPRESSION_CANNOT_BE_CONVERTED_TO_QNAME,"Namespace prefix "+eleTargetQn.getNamespacePrefix()+" not bound", loc);
		} 
		}
		if ((eleTargetQn.getNamespacePrefix() != null && eleTargetQn.getNamespacePrefix().equals("xmlns")) ||
				(eleTargetQn.getNamespaceURI() != null && eleTargetQn.getNamespaceURI().equals(XQStaticContext.URI_XMLNS))
				|| eleTargetQn.getLocalPart().equals("xmlns"))
			throw new DynamicException(ErrorCodes.E0044_DYNAMIC_NODE_NAME_OF_ATTRIBUTE_IS_XMLNS,"Computed attributes must not use xmlns names",loc);
		
		if((subIters[0].next().getEventType() != Type.END_SEQUENCE) || eleTargetQn == null){
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "The name expression in comment expression cannot be cast to xs:QName", loc);
		}
		
		tok= new NamedToken(-1, null, eleTargetQn, curNsScope);
		return tok;
	}
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		if (token != null) {
			return new XMLAttrIterator(context, (NamedToken) token.copy(),loc, curNsScope);
		} else {
			XDMIterator[] iter = new XDMIterator[subIters.length-1];
			for (int i=1; i<subIters.length; i++) {
				iter[i-1] = subIters[i];
			}
			
			return new XMLAttrIterator(context, subIters[0], iter,loc, curNsScope);
		}
		
	}
	
	public int compare(Source store) {
		if (store.getURI() != null) {
			return uri.compareTo(store.getURI());
		} else {
			return -2;
		}
	}

	public String getURI() {
		return docId;
	}
	
	public Source copySource(Context ctx, Vector nestedPredCtxStack) throws MXQueryException {
		return (Source) copy(ctx, null, false, nestedPredCtxStack);
	}
	
	private Identifier createNextTokenId() {
		if (generateNodeIds) {
			// TODO: Do not assign end_element etc. a separate node id
			identifier = IdentifierFactory.createIdentifier(nodeIdCount++, this,identifier,(short) depth);
			return identifier;
		} else
			return null;
	}

	protected void resetImpl() throws MXQueryException {
		super.resetImpl();
		if (subIters != null && subIters.length > 1)
			token = null;
	}
	public Window getIterator(Context ctx) throws MXQueryException {
		return WindowFactory.getNewWindow(context, this);
	}		
}

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

import java.util.Hashtable;
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
import ch.ethz.mxquery.datamodel.types.TypeLexicalConstraints;
import ch.ethz.mxquery.datamodel.xdm.NamedToken;
import ch.ethz.mxquery.datamodel.xdm.QNameToken;
import ch.ethz.mxquery.datamodel.xdm.TextToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.datamodel.xdm.XDMScope;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.exceptions.StaticException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.util.KXmlSerializer;
import ch.ethz.mxquery.util.Set;
import ch.ethz.mxquery.util.Stack;
import ch.ethz.mxquery.util.URIUtils;

/**
 * 
 * @author Matthias Braun
 * 
 */
public class XMLContent extends CurrentBasedIterator implements Source {
    int index = 0;
    private boolean generateNodeIds = true;
    private boolean document = false;
    private static int nodeIdCount = 0;
    private StringBuffer collectedTextValues = new StringBuffer();
    private boolean computedAttributesAllowed = true;
    private Token matTok = null; // Text node merging and attribute buffering
				    // needs a lookahead, if not matched keep
				    // here
    private Vector collectedAttributes = new Vector();
    private boolean[] enclosedExpr;
    XDMScope curNsScope;
    XDMScope oldParentNs;
    private boolean preserveNS = true;
    private boolean inheritNS = true;
    private boolean createdNSScope = false;
    private Set requiredNS = new Set();

    Stack scopeDepth = new Stack();

    int level = 0;

    XDMIterator constElemIterator = null;

    Identifier last_identifier;

    private final static String uri = "http://www.mxquery.org/nodeconstruction";
    static int docs = 0;
    private String docId;

    boolean started = false;
    private boolean computedConstructor;

    public XMLContent(Context ctx, XDMIterator[] subIters, boolean[] enclExpr,
	    int depth, QueryLocation location, XDMScope nsScope,
	    boolean compElemConst) throws MXQueryException {
	super(ctx, location);
	this.subIters = subIters;
	this.level = depth;
	this.enclosedExpr = enclExpr;
	computedConstructor = compElemConst;
	if (subIters != null && subIters.length > 0) {
	    current = subIters[0];
	}
	curNsScope = nsScope;
	docId = uri + docs++;
	preserveNS = ctx.getRootContext().getCopyNamespacesPreserveMode();
	inheritNS = ctx.getRootContext().getCopyNamespacesInheritMode();
	if (ctx.getBaseURI() != null) {
	    curNsScope.setBaseURI(ctx.getBaseURI());
	}
    }

    public Token next() throws MXQueryException {
	Token next;
	if (!this.started) {
	    this.started = true;
	    this.current = this.subIters[0];
	    if (computedConstructor) {
		setNamedToken(computeElemQName(context, subIters[0], loc));
	    }
	}

	called++;
	if (subIters != null && subIters.length > index) {
	    if (collectedAttributes.size() > 0) {
		Token topAttr = (Token) collectedAttributes.elementAt(0);
		collectedAttributes.removeElementAt(0);
		if (depth > 1)
		    next = topAttr;
		else
		    return topAttr;
	    } else if (matTok != null) {
		next = matTok;
		matTok = null;
	    } else
		next = getNext();

	    // also support Computed Element Constructors, compute result of
	    // first iterator into NamedToken
	    if (called == 1 && index == 0) {
		if (next.getEventType() == Type.START_DOCUMENT)
		    document = true;
		if (!document)
		    computedAttributesAllowed = true;

		// collect attributes to check
		int savedDepth = depth;
		collectAttributes();
		return genResult(next, savedDepth);
	    }

	    next = filterDocNodes(next);
	    int savedDepth = depth;
	    next = collectRequiredNamespaces(next);
	    next = collectTextValues(next);

	    if (next.getEventType() == Type.END_SEQUENCE) {
		index++;
		if (index < subIters.length) {
		    current = subIters[index];
		    next = getNext();
		    next = filterDocNodes(next);
		    savedDepth = depth;
		    next = collectRequiredNamespaces(next);
		    next = collectTextValues(next);
		    if (next.getEventType() == Type.END_SEQUENCE
			    && index < subIters.length) {
			return next();
		    }
		    return genResult(next, savedDepth);
		}
	    } else {
		if (next.getEventType() == Type.END_DOCUMENT && !document
			&& index < subIters.length - 1) {
		    // count depth down again (ignore the impact on depth)
		    depth++;
		    return this.next();
		} else {
		    return genResult(next, savedDepth);
		}
	    }
	}
	return Token.END_SEQUENCE_TOKEN;
    }

    private Token collectRequiredNamespaces(Token next) throws MXQueryException {
	if (depth >= 1 && next.getEventType() == Type.START_TAG) {
	    Token tok = getNext();
	    tok = switchIterIfEnd(tok);
	    while (Type.isAttribute(tok.getEventType())) {
		NamedToken nm = (NamedToken) tok;
		requiredNS.add(new Namespace(nm.getPrefix(), nm.getNS()));
		collectedAttributes.addElement(nm);
		tok = getNext();
		tok = switchIterIfEnd(tok);
	    }
	    matTok = tok;
	}
	return next;
    }

    private Token filterDocNodes(Token next) throws MXQueryException {
	if (next.getEventType() == Type.START_DOCUMENT && !document
		&& index > 0) {// filter out START_DOCUMENT, as embedding a
				// document into generated XML content removes
				// them
	    // count up down again, since this node is being ignored
	    depth--;
	    next = getNext();
	}
	return next;
    }

    private Token genResult(Token next, int tDepth) throws MXQueryException {
	Token res;
	if (collectedTextValues.toString().length() > 0) {
	    matTok = next;
	    Identifier idt = null;
	    if (generateNodeIds) {
		idt = createNextTokenId();
	    }
	    res = new TextToken(Type.TEXT_NODE_UNTYPED_ATOMIC, idt,
		    collectedTextValues.toString(), curNsScope);
	    collectedTextValues = new StringBuffer();
	    return res;
	}
	if (generateNodeIds) {
	    if ((next instanceof NamedToken) && !current.isConstModePreserve()) {
		next = ((NamedToken) next).copyStrip();
	    } else
		next = next.copy();

	    int ev = next.getEventType();
	    if (Type.isAttribute(ev) || ev == Type.START_TAG
		    || ev == Type.PROCESSING_INSTRUCTION || ev == Type.COMMENT) {
		XDMScope ns = next.getDynamicScope();
		if (ns != curNsScope) {
		    if (tDepth == 1) // bring new element + attributes into
					// same scope
			next.setDynamicScope(curNsScope);
		    if (tDepth == 2) {// top event(s) of copied item(s)
			Hashtable inScope = ns.getAllNamespaces();
			if (inScope.size() == 1 && ns.getBaseURI() == null) {// only
										// XML
										// namespace,
										// keep
										// new
										// "parent"
										// Scope
			    next.setDynamicScope(curNsScope);
			} else {
			    if (ns != oldParentNs) {
				mergeNSScope(next, true);
			    } else {
				next.setDynamicScope(curNsScope);
			    }
			}
			oldParentNs = ns;
		    }
		    if (tDepth > 2) { // nested events of copied item
			if (ns == oldParentNs) {
			    next.setDynamicScope(curNsScope);
			} else {
			    if (ns.getParent() == oldParentNs
				    || ns.getParent() == curNsScope) {
				// parent scopes should have been cleaned up
				// already
				oldParentNs = next.getDynamicScope()
					.getParent();
				mergeNSScope(next, false);
			    } // else
			    // throw new RuntimeException("Namespace scope
			    // nesting wrong");
			}
		    }
		}
		if (ev == Type.END_TAG) {
		    if (next instanceof NamedToken
			    && depth == scopeDepth.peek()) {
			curNsScope = curNsScope.getParent();
			scopeDepth.pop();
		    }
		}
	    }
	    next.setId(createNextTokenId());
	}
	return next;
    }

    private void mergeNSScope(Token nt, boolean global) throws MXQueryException {
	XDMScope ns = nt.getDynamicScope();

	XDMScope toSet;

	toSet = XDMScope.combineSopes(global, ns, inheritNS, preserveNS,
		requiredNS, curNsScope, context);

	if (requiredNS.size() > 0)
	    requiredNS = new Set();

	if (toSet != curNsScope) {
	    curNsScope = toSet;
	    scopeDepth.push(depth);
	}
	nt.setDynamicScope(toSet);
    }

    private Token collectTextValues(Token next) throws MXQueryException {
	int wloop;
	int prevEvent;
	wloop = 0;
	prevEvent = Type.START_SEQUENCE;
	int ev = next.getEventType();

	while (Type.isTextNode(ev)
		|| (Type.isAtomicType(ev, Context.getDictionary()))) {
	    if (Type.isTextNode(ev) || Type.isTextNode(prevEvent)
		    || collectedTextValues.length() == 0
		    || (index > 0 && enclosedExpr[index - 1] && wloop == 0)
		    || (index > 1 && enclosedExpr[index - 2]))
		collectedTextValues.append(next.getValueAsString());
	    else
		collectedTextValues.append(" ").append(next.getValueAsString());
	    next = getNext();
	    next = switchIterIfEnd(next);
	    next = filterDocNodes(next);
	    prevEvent = ev;
	    next = switchIterIfEnd(next);
	    ev = next.getEventType();
	    wloop++;
	}
	if (context.getConstructionMode().equals(XQStaticContext.STRIP)
		&& next instanceof NamedToken)
	    return ((NamedToken) next).copyStrip();
	else
	    return next;
    }

    /**
     * Switch to next subIter if next is END_SEQUENCE and another subIter is
     * available
     * 
     * @param next
     * @return
     * @throws MXQueryException
     */
    private Token switchIterIfEnd(Token next) throws MXQueryException {
	if (next.getEventType() == Type.END_SEQUENCE && index < subIters.length) {
	    index++;
	    current = subIters[index];
	    next = getNext();
	}
	return next;
    }

    private void collectAttributes() throws MXQueryException {
	Set seenAttributes = new Set();
	Token tok = getNext();
	int renameCounter = 0; // count renamed namespaces
	tok = switchIterIfEnd(tok);
	while (Type.isAttribute(tok.getEventType())) {
	    String attrName = tok.getName();
	    QName qn = new QName(attrName);
	    qn.setNamespaceURI(tok.getNS());
	    if (seenAttributes.contains(qn))
		throw new DynamicException(
			ErrorCodes.E0025_DYNAMIC_DUPLICATE_ATTRIBUTE_NAMES,
			"Duplicate attribute name " + attrName, loc);
	    seenAttributes.add(qn);
	    NamedToken nm = (NamedToken) tok;
	    if (nm.getPrefix() != null) {
		Namespace attNs = curNsScope.getNamespace(nm.getPrefix());
		if (attNs == null) {
		    String nsURI = nm.getNS();
		    if (nsURI == null) {
			Namespace scopeNS = context
				.getNamespace(nm.getPrefix());
			if (scopeNS == null)
			    throw new StaticException(
				    ErrorCodes.E0081_STATIC_QUERY_UNKNOWN_NAMESPACE,
				    "Namespace prefix " + nm.getPrefix()
					    + " not bound", loc);
			else
			    nsURI = scopeNS.getURI();
		    }
		    curNsScope.addNamespace(nm.getPrefix(), nsURI);
		} else {
		    if (!attNs.getURI().equals(nm.getNS())) {
			// rename namespace prefix, use XXX (without 0) for test
			// suite expected results
			String newPrefix = "XXX";
			if (renameCounter > 0)
			    newPrefix = newPrefix + renameCounter++;
			curNsScope.addNamespace(newPrefix, nm.getNS());
			nm = nm.copy(new QName(nm.getNS(), newPrefix, nm
				.getLocal()));
		    }
		}
		if (nm.getNS().equals(XQStaticContext.URI_XML)
			&& nm.getLocal().equals("base")) {
		    String uri = tok.getValueAsString();
		    if (TypeLexicalConstraints.isRelativeURI(uri)) {
			String currentBase = curNsScope.getBaseURI();
			uri = URIUtils.resolveURI(currentBase, uri, loc);

		    }
		    curNsScope.setBaseURI(uri);
		}
		if (nm.getNS().equals(XQStaticContext.URI_XML)
			&& nm.getLocal().equals("lang")) {
		    curNsScope.setLanguage(tok.getValueAsString());
		}
	    }
	    if (tok == nm)
		nm = (NamedToken) nm.copy();

	    nm.setDynamicScope(curNsScope);
	    if (generateNodeIds)
		nm.setId(createNextTokenId());

	    collectedAttributes.addElement(nm);

	    tok = getNext();
	    tok = switchIterIfEnd(tok);
	}

	matTok = tok;
    }

    public static QName computeElemQName(XQStaticContext context,
	    XDMIterator it, QueryLocation loc) throws MXQueryException {
	Token next = it.next();
	int ev = next.getEventType();
	if (Type.isTextNode(ev))
	    ev = Type.getTextNodeValueType(ev);

	if (!(Type.isTypeOrSubTypeOf(ev, Type.STRING, Context.getDictionary())
		|| ev == Type.UNTYPED_ATOMIC || ev == Type.QNAME))
	    throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,
		    "Incorrect type of the parameter in Computed Element Constructor: "
			    + Type.getTypeQName(next.getEventType(), Context
				    .getDictionary()), loc);

	if (it.next().getEventType() != Type.END_SEQUENCE) {
	    throw new TypeException(
		    ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,
		    "The name expression in comment expression cannot be cast to xs:QName",
		    loc);
	}

	String eleTarget;

	QName eleTargetQName;
	Namespace ns;

	if (ev != Type.QNAME) {
	    eleTarget = next.getText();
	    try {
		eleTargetQName = new QName(eleTarget);
	    } catch (DynamicException de) {
		if (de.getErrorCode().equals(
			ErrorCodes.F0005_INVALID_LEXICAL_VALUE))
		    throw new DynamicException(
			    ErrorCodes.E0074_DYNAMIC_NAME_EXPRESSION_CANNOT_BE_CONVERTED_TO_QNAME,
			    "Invalid QName " + eleTarget, loc);
		else
		    throw de;
	    }
	    ns = context.getNamespace(eleTargetQName.getNamespacePrefix());
	    if (ns != null) {
		eleTargetQName.setNamespaceURI(ns.getURI());
	    }
	} else {
	    QNameToken qnt = ((QNameToken) next);
	    eleTargetQName = qnt.getQName();
	    if (qnt.getNS() != null) {
		ns = new Namespace(eleTargetQName.getNamespacePrefix(), qnt
			.getNS());
		eleTargetQName.setNamespaceURI(ns.getURI());
	    } else
		ns = null;
	}

	if (ns == null && eleTargetQName.getNamespacePrefix() != null)
	    throw new DynamicException(
		    ErrorCodes.E0074_DYNAMIC_NAME_EXPRESSION_CANNOT_BE_CONVERTED_TO_QNAME,
		    "Namespace prefix " + eleTargetQName.getNamespacePrefix()
			    + " not in scope for element construction", loc);
	return eleTargetQName;
    }

    private void setNamedToken(QName eleTargetQName) throws MXQueryException {
	Token tok;
	if (eleTargetQName.getNamespaceURI() != null) {
	    if (curNsScope.getNsURI(eleTargetQName.getNamespacePrefix()) == null) {
		if (!createdNSScope) {
		    curNsScope = new XDMScope(curNsScope);
		}
		curNsScope.addNamespace(eleTargetQName.getNamespacePrefix(),
			eleTargetQName.getNamespaceURI());
	    }
	}
	if (context.getConstructionMode().equals(XQStaticContext.PRESERVE))
	    tok = new NamedToken(Type.START_TAG | Type.ANY_TYPE, null,
		    eleTargetQName, curNsScope);
	else
	    tok = new NamedToken(Type.START_TAG, null, eleTargetQName,
		    curNsScope);
	Token tok2 = new NamedToken(Type.END_TAG, null, eleTargetQName,
		curNsScope);

	constElemIterator = subIters[0];
	subIters[0] = new TokenIterator(context, tok, null, loc);

	subIters[subIters.length - 1] = new TokenIterator(context, tok2, null,
		loc);
	current = subIters[0];
    }

    protected void resetImpl() throws MXQueryException {
	super.resetImpl();

	index = 0;
	this.started = false;

	collectedTextValues = new StringBuffer();
	matTok = null;

	if (subIters != null && subIters.length > 0) {
	    if (constElemIterator != null) {
		constElemIterator.reset();
		subIters[0] = constElemIterator;
		constElemIterator = null;
	    }
	    current = subIters[0];
	}
	collectedAttributes = new Vector();
    }

    public void setResettable(boolean r) throws MXQueryException {
	super.setResettable(r);
	if (constElemIterator != null)
	    constElemIterator.setResettable(true);
    }

    protected KXmlSerializer createIteratorStartTag(KXmlSerializer serializer)
	    throws Exception {
	super.createIteratorStartTag(serializer);
	serializer.attribute(null, "depth", "" + level);
	return serializer;
    }

    public int compare(Source store) {
	if (store.getURI() != null) {
	    return getURI().compareTo(store.getURI());
	} else {
	    return -2;
	}
    }

    public String getURI() {
	return docId;
    }

    private Identifier createNextTokenId() {
	if (generateNodeIds) {
	    // TODO: Do not assign end_element etc. a separate node id

	    last_identifier = IdentifierFactory.createIdentifier(nodeIdCount++,
		    this, last_identifier, (short) depth);
	    return last_identifier;
	} else
	    return null;
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.model.iterators.CurrentBasedIterator#getNext()
     */
    protected Token getNext() throws MXQueryException {
	Token current = super.getNext();
	if (depth < 2) {
	    if (!(Type.isAttribute(current.getEventType()) || current
		    .getEventType() == Type.END_SEQUENCE))
		computedAttributesAllowed = false;

	    if (Type.isAttribute(current.getEventType())
		    && !computedAttributesAllowed)
		if (document)
		    throw new TypeException(
			    ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,
			    "No attributes allowed in document constructor",
			    loc);
		else
		    throw new DynamicException(
			    ErrorCodes.E0024_TYPE_ATTRIBUTE_NODE_FOLLOWS_NONE_ATTRIBUTE_NODE,
			    "No attributes followings non-attributes elements",
			    loc);
	}
	return current;

    }

    public XDMIterator copy(Context parentIterContext,
	    XQStaticContext prevParentIterContext, boolean copyContext,
	    Vector nestedPredCtxStack) throws MXQueryException {
	// initialize new context and subIterators
	Context newContext = parentIterContext;
	XDMIterator[] newSubIters = null;
	if (subIters != null) {
	    newSubIters = new XDMIterator[subIters.length];
	}

	if (context == null) {
	    throw new RuntimeException("DEBUG: Clone problem - context is null");
	}

	if (copyContext) {
	    newContext = context.copy();
	    newContext.setParent(parentIterContext);

	}

	// copy the subIterators
	if (subIters != null) {
	    for (int i = 0; i < subIters.length; i++) {
		Context subCtx = subIters[i].getContext();
		boolean cctx = false;
		if (subCtx != null) {
		    if (i > 0
			    && subCtx.isAnecstorContext(subIters[i - 1]
				    .getContext())) {
			cctx = subIters[i - 1].getContext().equals(subCtx) ? false
				: true;
			newSubIters[i] = subIters[i].copy(newSubIters[i - 1]
				.getContext(), subIters[i - 1].getContext(),
				cctx, nestedPredCtxStack);
		    } else {
			cctx = subIters[i].getContext().equals(context) ? false
				: true;
			newSubIters[i] = subIters[i].copy(newContext,
				this.context, cctx, nestedPredCtxStack);
		    }
		} else {
		    throw new RuntimeException(
			    "DEBUG: Clone problem - context is null");
		}

	    }
	}
	XMLContent cp = (XMLContent) copy(newContext, newSubIters,
		nestedPredCtxStack);
	cp.exprCategory = this.exprCategory;
	return cp;
    }

    protected XDMIterator copy(Context context, XDMIterator[] subIters,
	    Vector nestedPredCtxStack) throws MXQueryException {
	return new XMLContent(context, subIters, enclosedExpr, depth, loc,
		curNsScope.copy(), computedConstructor);
    }

    public Source copySource(Context ctx, Vector nestedPredCtxStack)
	    throws MXQueryException {
	return (Source) copy(ctx, null, false, nestedPredCtxStack);
    }

    public Window getIterator(Context ctx) throws MXQueryException {
	return WindowFactory.getNewWindow(context, this);
    }
}

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

package ch.ethz.mxquery.datamodel;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.contextConfig.XQStaticContext;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeLexicalConstraints;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.StaticException;

/**
 * 
 * @author Matthias Braun
 * 
 * Representation of a QName with a namespace prefix and a local part as well as
 * a resolved QName
 * 
 */
public class QName {
    private String nsUri;
    private String prefix;
    protected String local;

    public QName(String nsURI, String prefix, String local) {
	if (nsURI == null || nsURI.equals(""))
	    this.nsUri = null;
	else
	    this.nsUri = nsURI;
	this.prefix = prefix;
	this.local = local;
    }

    /**
     * Creates a QName
     * 
     * @param prefix
     *                The namespace prefix of the QName
     * @param local
     *                The local part of the QName
     */
    public QName(String prefix, String local) {
	this.prefix = prefix;
	this.local = local;
    }

    /**
     * Creates a QName
     * 
     * @param qname
     *                The Qname in String representation
     */
    public QName(String qname) throws MXQueryException {
	if (qname != null) {
	    int i = qname.indexOf(":");
	    if (i > -1) {
		prefix = qname.substring(0, i);
		if (prefix.equals("")
			|| !TypeLexicalConstraints.validate_NCNAME(Type.NCNAME,
				prefix))
		    throw new DynamicException(
			    ErrorCodes.F0005_INVALID_LEXICAL_VALUE,
			    "Empty string not legal prefix in a QName", null);
		local = qname.substring(i + 1, qname.length());
		if (local.equals("")
			|| !TypeLexicalConstraints.validate_NCNAME(Type.NCNAME,
				local))
		    throw new DynamicException(
			    ErrorCodes.F0005_INVALID_LEXICAL_VALUE,
			    "Invalid value for local part of QName", null);
	    } else {
		local = qname;
		if (local.equals("")
			|| !TypeLexicalConstraints.validate_NCNAME(Type.NCNAME,
				local))
		    throw new DynamicException(
			    ErrorCodes.F0005_INVALID_LEXICAL_VALUE,
			    "Invalid value for local part of QName", null);
	    }
	}
    }

    /**
     * Return the namespace prefix of this QName
     * 
     * @return the Prefix, if defined, otherwise null
     */
    public String getNamespacePrefix() {
	return prefix;
    }

    public static String[] parseQName(String qname) {
	String pre[] = new String[2];
	if (qname != null) {
	    int i = qname.indexOf(":");
	    if (i > -1) {
		pre[0] = qname.substring(0, i);
		pre[1] = qname.substring(i + 1, qname.length());
	    } else {
		pre[1] = qname;
		pre[0] = null;
	    }
	}
	return pre;
    }

    /**
     * CompareTo only needed for compatibility
     * 
     * @param obj
     * @return lexicalcographics comprison of either URI and localname (if URI
     *         present) or prefix and localname
     */
    public int compareTo(Object obj) {
	if (!(obj instanceof QName))
	    return -1;
	QName compareName = (QName) obj;
	String compareURI = compareName.getNamespaceURI();
	String comparePrefix = compareName.getNamespacePrefix();
	String compareLocal = compareName.getLocalPart();
	int compLocal = compareLocal.compareTo(local);
	if (compLocal != 0) {
	    return compLocal;
	}

	if (nsUri != null || compareURI != null) {
	    if (compareURI != null && nsUri != null) {
		return compareURI.compareTo(nsUri);
	    } else
		return -1;
	} else if (comparePrefix != null && prefix != null)
	    return comparePrefix.compareTo(prefix);
	else if (comparePrefix == null && prefix == null)
	    return 0;
	else
	    return -1;
    }

    public boolean equals(Object obj) {
	return (compareTo(obj) == 0);
    }

    public int hashCode() {

	int hc = local.hashCode();

	if (nsUri != null)
	    hc = hc + nsUri.hashCode();
	else if (prefix != null)
	    hc = hc + prefix.hashCode();

	return hc;
    }

    /**
     * @return The local part
     */
    public String getLocalPart() {
	return local;
    }

    public String getNamespaceURI() {
	return nsUri;
    }

    /**
     * @return a string representation of the qname
     */
    public String toString() {
	return (prefix == null) ? local : (prefix + ":" + local);
    }

    public QName copy() {
	return new QName(nsUri, prefix, local);
    }

    /**
     * Resolves a two-valued QName to a namespace URI within the given context
     * 
     * @param ctx
     *                Context to use for resolution
     * @throws MXQueryException
     */
    public QName resolveQNameNamespace(XQStaticContext ctx)
	    throws MXQueryException {
	if (getNamespacePrefix() != null && !equals(Context.CONTEXT_ITEM)
		&& !getNamespacePrefix().equals(Context.ANONYM_VARIABLE_PREFIX)
		&& !getNamespacePrefix().equals(".let")
		&& !getNamespacePrefix().equals(".ft")) {
	    Namespace ns = ctx.getNamespace(getNamespacePrefix());
	    if (ns == null)
		throw new StaticException(
			ErrorCodes.E0081_STATIC_QUERY_UNKNOWN_NAMESPACE,
			"Unkown namespace", null);
	    return new QName(ns.getURI(), prefix, local);
	} else
	    return this;
    }

    public void setNamespaceURI(String nsUri) {
	if (nsUri == null || nsUri.equals(""))
	    this.nsUri = null;
	else
	    this.nsUri = nsUri;
    }

    public boolean isNamespaceDeclAttr() {
	if (prefix == null && local.equals(XQStaticContext.NS_XMLNS)
		|| prefix != null && prefix.equals(XQStaticContext.NS_XMLNS))
	    return true;
	return false;
    }
}

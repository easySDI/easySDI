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
/**
 * Represents dynamic scopes for data
 * - In-Scope namespaces
 * - Base-URI (xml:base)
 * - Language (xml:lang)
 * - root element pointer (to be decided)
 */

package ch.ethz.mxquery.datamodel.xdm;

import java.util.Enumeration;
import java.util.Hashtable;

import ch.ethz.mxquery.contextConfig.XQStaticContext;
import ch.ethz.mxquery.datamodel.Namespace;
import ch.ethz.mxquery.datamodel.types.TypeLexicalConstraints;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.util.LinkedList;
import ch.ethz.mxquery.util.Set;
import ch.ethz.mxquery.util.URIUtils;

public class XDMScope {
	private XDMScope parent;
	private Hashtable prefixNamespaceMap;
	private boolean inherit = true;
	private String baseUri;
	private String lang;
	
	public XDMScope() {
		parent = null;
		prefixNamespaceMap = new Hashtable();
		prefixNamespaceMap.put("xml",new Namespace("xml","http://www.w3.org/XML/1998/namespace"));
	}
	
	public XDMScope(XDMScope par) {
		this(par,true);
	}
	
	public XDMScope(XDMScope par, boolean inherit) {
		parent = par;
		prefixNamespaceMap = new Hashtable();
		this.inherit = inherit;
		if (!inherit)
			prefixNamespaceMap.put("xml",new Namespace("xml","http://www.w3.org/XML/1998/namespace"));
	}
	/**
	 * Add a prefix/namespace mapping.
	 * @param ns a namespace object
	 * @throws MXQueryException
	 */
	public void addNamespace(Namespace ns) throws MXQueryException {
		if (ns.getNamespacePrefix() == null || ns.getURI() == null) {
			throw new MXQueryException(ErrorCodes.A0009_EC_EVALUATION_NOT_POSSIBLE, "Prefix or URI missing for namespace definition", null);
		}
		if (!((ns.getURI() == null || ns.getURI().equals(""))&&getNsURI(ns.getNamespacePrefix())==null))
			prefixNamespaceMap.put(ns.getNamespacePrefix(), ns);
	}
	/**
	 * Add a prefix/namespace mapping.
	 * 
	 * @param prefix
	 *            The prefix.
	 * @param uri
	 *            The URI of the namespace.
	 */
	public void addNamespace(String prefix, String uri) throws MXQueryException {
		addNamespace(new Namespace(prefix, uri));
	}
	
	
	public XDMScope getParent() {
		return parent;
	}

	public void setParent(XDMScope par) {
		parent = par;
	}
	
	
	/**
	 * @return All the visible namespace prefix-URI mappings.
	 */
	public Hashtable getAllNamespaces() {
		if (!inherit)
			return getLocalNamespaces();
		final LinkedList maps = new LinkedList();
		for (XDMScope c = this; c != null; c = c.parent)
			maps.add(c.prefixNamespaceMap);
		return flattenHierarchy(maps);
	}

	public Hashtable getLocalNamespaces() {
		return prefixNamespaceMap;
	}
	
	/**
	 * This method takes a LinkedList of Hashtable, representing the scope hierarchy and produces 
	 * a flatted Hashtable out of it.
	 * @param li
	 * @return
	 */
	private Hashtable flattenHierarchy(LinkedList maps) {
		final Hashtable result = new Hashtable();
		for (Enumeration li = maps.traverseList(); li.hasMoreElements();) {
			Hashtable ht = (Hashtable) li.nextElement();
			Enumeration keys = ht.keys();
			Enumeration values = ht.elements();
			while (keys.hasMoreElements()) {
				result.put(keys.nextElement(), values.nextElement());
			}
		}
		return result;
	}
	
	
	/**
	 * Gets the namespace URI associated with the given prefix
	 * 
	 * @param prefix
	 *            The given prefix
	 * @return The associated namespace URI
	 */
	public String getNsURI(String prefix) {
		if (prefix == null) {
			prefix = "";
		}
		Namespace uri = null;
		if (inherit)
			for (XDMScope c = this; uri == null && c != null; c = c.parent)
				uri = (Namespace) c.prefixNamespaceMap.get(prefix);
		else 
			uri = (Namespace) prefixNamespaceMap.get(prefix);
		if (uri!= null)
			return uri.getURI();
		else return null;
	}	

	public Namespace getNamespace(String prefix) {
		if (prefix == null) {
			prefix = "";
		}
		Namespace uri = null;
		if (inherit)
			for (XDMScope c = this; uri == null && c != null; c = c.parent)
				uri = (Namespace) c.prefixNamespaceMap.get(prefix);
		else
			uri = (Namespace) prefixNamespaceMap.get(prefix);
		
		// "undeclared namespaces"
		if (uri != null && uri.getURI()==null)
			uri = null;
		
		return uri;
	}	
	
	/**
	 * get the (namespace-)prefix for a given URI. This acutally iterates
	 * through the context entries. This iteration is acceptable, because this
	 * method is very rarely used.
	 * 
	 * @param uri
	 *            The URI to look for.
	 * @return TODO
	 */
	public String getPrefix(String uri) {
		Hashtable ht = getAllNamespaces();
		Enumeration keys = ht.keys();
		Enumeration values = ht.elements();
		while (keys.hasMoreElements()) {
			String currentPrefix = (String) keys.nextElement();
			Namespace currentNamespace = (Namespace) values.nextElement();
			if (currentNamespace.getURI().equals(uri)) {
				return currentPrefix;
			}
		}
		return null;
	}	
	
	public XDMScope copy () {
		XDMScope cp = new XDMScope(this.parent,inherit);
		Enumeration keys = prefixNamespaceMap.keys();
		while (keys.hasMoreElements()) {
			Object o = keys.nextElement();
			cp.prefixNamespaceMap.put(o, ((Namespace) prefixNamespaceMap.get(o)).copy());
		}
		return cp;
	}
	
	public static XDMScope combineSopes(boolean global, XDMScope ns, boolean inherit, boolean preserve, Set requiredNS, XDMScope curScope, XQStaticContext ctx)
	throws MXQueryException {
		XDMScope toSet;
		Hashtable inScope;
		if (global)
			inScope = ns.getAllNamespaces();
		else
			inScope = ns.getLocalNamespaces(); 

		XDMScope nsNew = new XDMScope(curScope,inherit);
		Enumeration namesSpaces = inScope.elements(); 
//		for now, preserve + inherit mode:
//		add namespaces that are not already present in the new parent
		int newNamespaces = 0;
		while (namesSpaces.hasMoreElements()) {
			Namespace nm = (Namespace)namesSpaces.nextElement();
			// Skip all namespaces that are not required, if preserve == false
			if (!preserve && !requiredNS.contains(nm))
				continue;
			Namespace existing = curScope.getNamespace(nm.getNamespacePrefix());
			if (!inherit || existing == null || !existing.getURI().equals(nm.getURI())) {
				nsNew.addNamespace(nm);
				newNamespaces++;
			}
		}
//		Undeclare default NS if construction around uses it, but inner content does not
		if ((ns.getNamespace("") == null || ns.getNamespace("").getURI() == null) && curScope.getNamespace("")!=null) {
			nsNew.addNamespace("", "");
			newNamespaces++;
		}
		
		// combine base URI settings
		
		String childBase = ns.baseUri;
		String parentBase = curScope.baseUri;
		if (childBase !=null && !childBase.equals(ctx.getBaseURI())) {
			newNamespaces++;
			if (parentBase == null)
				nsNew.baseUri = childBase;
			else {

				if (TypeLexicalConstraints.isRelativeURI(childBase)) {
					nsNew.baseUri = URIUtils.resolveURI(parentBase, childBase, QueryLocation.OUTSIDE_QUERY_LOC);
				} else {
					String contextBaseURI = ctx.getBaseURI();
					if (childBase.startsWith(contextBaseURI)) {
						childBase = childBase.substring(contextBaseURI.length());
						nsNew.baseUri = URIUtils.resolveURI(parentBase, childBase, QueryLocation.OUTSIDE_QUERY_LOC);
					} else 
						nsNew.baseUri = childBase;
				}
			}
		}
		
		String childLang = ns.lang;
		String parentLang = curScope.lang;
		
		if (childLang != null && !childLang.equals(parentLang)) {
			nsNew.lang = childLang;
			newNamespaces++;
		}
		
		if (newNamespaces != 0 || (!inherit && curScope.getLocalNamespaces().size()!=0)) {
			toSet = nsNew;
		} else
			toSet = curScope;
		return toSet;
	}

		/** get the language (xml:lang) of this scope/node */
		public String getLanguage() {
			if (lang != null)
				return lang;
			else if (parent != null)
				return parent.getLanguage();
			return null;	
		}

		public void setLanguage(String lang) {
			this.lang = lang;
		}
		/** get the base-uri (xml:base) for this scope/node */
		public String getBaseURI() {
			if (baseUri != null)
				return baseUri;
			else if (parent != null)
				return parent.getBaseURI();
			return null;
		}
		
		public void setBaseURI(String baseURI) {
			this.baseUri = baseURI;
		}
}

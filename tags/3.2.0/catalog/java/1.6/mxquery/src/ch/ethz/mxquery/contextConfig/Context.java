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
package ch.ethz.mxquery.contextConfig;

import java.util.Calendar;
import java.util.Enumeration;
import java.util.Hashtable;
import java.util.TimeZone;
import java.util.Vector;

import ch.ethz.mxquery.bindings.WindowFactory;
import ch.ethz.mxquery.datamodel.MXQueryDateTime;
import ch.ethz.mxquery.datamodel.MXQueryDayTimeDuration;
import ch.ethz.mxquery.datamodel.Namespace;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.Source;
import ch.ethz.mxquery.datamodel.types.TypeDictionary;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.exceptions.StaticException;
import ch.ethz.mxquery.functions.Function;
import ch.ethz.mxquery.functions.FunctionGallery;
import ch.ethz.mxquery.model.VariableHolder;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.model.ft.FTCaseMatchOption;
import ch.ethz.mxquery.model.ft.FTStopWordsMatchOption;
import ch.ethz.mxquery.model.ft.FTThesaurusMatchOption;
import ch.ethz.mxquery.update.store.llImpl.LLStoreSet;
import ch.ethz.mxquery.util.IOLib;
import ch.ethz.mxquery.util.LinkedList;
import ch.ethz.mxquery.util.Location;
import ch.ethz.mxquery.util.Set;
import ch.ethz.mxquery.xdmio.StoreSet;

/**
 * This class represents a unified context implementation, covering XQuery
 * static and dynamic contexts as well as MXQuery-specific additions Context
 * nesting/scoping works as follows: 1) A singleton "global" context for all
 * instances of MXQuery within an JVM 2) A "root" context for each compiled
 * query/exposed module 3) nested contexts for lexical scopes
 * 
 * The "global" context carries the - built-in functions - default settings for
 * static context values - the well-known and default namespaces - the
 * dictionary of all user-defined types
 * 
 * A root context carries the - user-defined functions - the store set - the
 * collections - the prolog settings of the static context
 */

public final class Context implements XQStaticContext, XQDynamicContext {

    /**
     * The root context of a query/prepared expression, shortcut for all the
     * descendant contexts
     */
    private Context rootContext;

    /** Global context for all predefined settings */
    private static final Context globalContext;

    /*
     * Different kinds of named values that live in the Context.
     */
    private final static String BASE_URI = "BU";

    // private final static String COLLATION = "CO";

    private final static String CONSTRUCTION_MODE = "CM";

    private final static String DEFAULT_ELEMENT_NAMESPACE_PREFIX = "";

    private final static String DEFAULT_FUNCTION_NAMESPACE = "FN";

    private final static String ORDER_EMPTY_SEQUENCES = "OE";

    private final static String ORDERING_MODE = "OM";

    private final static String COPY_NAMESPACE_INHERIT = "CNI";

    private final static String COPY_NAMESPACE_PRESERVE = "CNP";

    private final static String TIMESTAMP = "TS";

    private final static String TIMEZONE = "TZ";

    private final static String WHITESPACE_HANDLING = "SH";

    private final static String MODULE = "MO";

    private final static String MODULE_URI = "MU";

    private final static String MODULE_CONTEXTS = "MC";

    private final static String WEB_SERVICE_PORT = "WP";

    private final static String WEB_SERVICE_NAMESPACE = "WN";

    private final static String WEB_SERVICE_NAME = "WSN";

    private final static String WEB_SERVICE_ENDPOINT_NAME = "WSEN";

    private final static String VALIDATION_AVAILABLE = "VA";

    private final static String REVALIDATION_MODE = "REVAL";

    public final static String PARSER_TYPE = "PT";

    private final static String FT_CASE = "FTC";

    private final static String FT_DIACRITIC = "FTDI";

    private final static String FT_STEM = "FTST";

    private final static String FT_THESAURUS = "FTTH";

    private final static String FT_STOPWORD = "FTSW";

    private final static String FT_LANG = "FTL";

    private final static String FT_WILDCARD = "FTWC";

    private final static String NS_FN = "fn";

    private final static String NS_XS = "xs";

    private final static String NS_XSI = "xsi";

    private final static String NS_XDT = "xdt";

    private final static String NS_LOCAL = "local";

    private final static String NS_MXQ = "mxq";

    private final static String URI_MXQ = "http://www.mxquery.org/namespace";

    public final static QName CONTEXT_ITEM = new QName(".item", ".item");

    public final static String ANONYM_VARIABLE_URI = "http://www.mxquery.org/anonym_variable";

    public final static String ANONYM_VARIABLE_PREFIX = ".anonym";

    public final static String DEFAULT_COLLECTION_URI = "http://mxquery.org/default-collection";

    private static int anonymNamespaceCounter = 0;

    private static int anonymVariableCounter = 0;

    private Context parent;

    private Hashtable prefixNamespaceMap;

    private Hashtable importedSchemaLocationsMap;

    private Hashtable namedValueMap;

    private Hashtable variableMap;

    /**
     * Choice of input parser
     */
    public static final int NONVALIDATED_INPUT_MODE_XPP = 0;
    public static final int NONVALIDATED_INPUT_MODE_STAX = 1;
    public static final int NONVALIDATED_INPUT_MODE_SAX = 2;
    public static final int NONVALIDATED_INPUT_MODE_DOM = 3;
    public static final int NONVALIDATED_INPUT_MODE_SAX_TIDY = 4;

    public static final int NO_VALIDATION = 0;
    public static final int DTD_VALIDATION = 1;
    public static final int SCHEMA_VALIDATION_LAX = 2;
    public static final int SCHEMA_VALIDATION_STRICT = 3;

    // protected EntityResolver entityResolver;

    private FunctionGallery functionGallery = null;

    private StoreSet storeSet;

    private Hashtable modLocation = new Hashtable();

    // private boolean useUpdateableVariables = false;

    private boolean isWebService = false;

    // Module locations - maybe FIXME?
    public Location location;

    private int position = -1;

    private Vector targetNamespaces;

    private TypeDictionary dictionary;

    // private boolean validationModeOn = false;

    /**
     * Initializes the dictionary to be used in this context
     * 
     * @return the Dictionary of Types to be used in this context
     */
    public static synchronized TypeDictionary initDictionary() {
	if (getDictionary() == null)
	    setDictionary(new TypeDictionary());
	return getDictionary();
    }

    /**
     * 
     * @return the Dictionary of Types to be used in this context
     */
    public static TypeDictionary getDictionary() {
	return globalContext.dictionary;
    }

    /**
     * Set the type dictionary
     * 
     * @param dict
     */
    public static void setDictionary(TypeDictionary dict) {
	globalContext.dictionary = dict;
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQDynamicContext#getPosition()
     */
    public int getPosition() {
	return position;
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQDynamicContext#setPosition(int)
     */
    public void setPosition(int position) {
	this.position = position;
    }

    /**
     * Return the parser type that is the default for XML parsing
     * 
     * @return the Parser type as in NONVALIDATED_INPUT_MODE_SAX,
     *         NONVALIDATED_INPUT_MODE_STAX, NONVALIDATED_INPUT_MODE_DOM etc
     */
    public int getParserType() {
	return ((Integer) getNamedValue(PARSER_TYPE)).intValue();
    }

    public void setParserType(int pType) {
	Integer parserType = new Integer(pType);
	setNamedValue(PARSER_TYPE, parserType);
    }

    /**
     * Creates a new root context with a separate store set
     */
    public Context() {
	this(null);
    }

    /**
     * Creates a new context with the given parent
     * 
     * @param parent
     *                the parent context
     */
    public Context(Context parent) {
	this(parent, null);
    }

    /**
     * Create a new context with the given parent and the given store set
     * 
     * @param parent
     *                if not null, this will be the parent context of the newly
     *                created context, otherwise this context will be a root
     *                context
     * @param stores
     *                If the newly created context is a root context, it will
     *                use this store set
     */
    public Context(Context parent, StoreSet stores) {

	prefixNamespaceMap = new Hashtable();
	namedValueMap = new Hashtable();
	variableMap = new Hashtable();
	if (parent == null) { // root context of a query/prepared expression
	    this.parent = globalContext;
	    rootContext = this;
	    // for functions in scope of the query/module
	    rootContext.functionGallery = new FunctionGallery(rootContext);
	    if (stores != null)
		rootContext.storeSet = stores;
	    else
		rootContext.storeSet = new LLStoreSet();
	    rootContext.setBaseURI(IOLib.getSystemBaseUri());

	} else {
	    this.parent = parent;
	    rootContext = parent.getRootContext();
	}

	if ((this.parent != null && this.parent == globalContext)) {
	    try {
		registerNewContextItem();
	    } catch (MXQueryException me) {
		throw new RuntimeException(
			"Creating context item in initial dynamic context failed - this should not happen");
	    }
	}

	// Entity resolver needs to be implemented
	// entityResolver = parent != null && parent.getEntityResolver() != null
	// ? parent.getEntityResolver() : null; // new
    }

    private Context(Context parent, FunctionGallery fg,
	    Hashtable prefixNamespaceMap, Hashtable namedValueMap,
	    Hashtable variableMap, Hashtable modLocation,
	    Hashtable invertedList, Location location, StoreSet storeSet)
	    throws MXQueryException {
	this(parent);
	this.functionGallery = fg;
	this.prefixNamespaceMap = prefixNamespaceMap;
	this.namedValueMap = namedValueMap;
	this.variableMap = variableMap;
	this.modLocation = modLocation;
	// this.invertedList = invertedList;
	this.location = location;
	this.storeSet = storeSet;
    }

    /**
     * Adds a targetNamespace to the current context
     * 
     * @param targetNamespace
     * @return true false if targetNamespace is already defined
     */
    public boolean addTargetNamespace(String targetNamespace) {
	if (this.targetNamespaces == null)
	    this.targetNamespaces = new Vector();

	if (this.targetNamespaces.contains(targetNamespace))
	    return false;
	else {
	    this.targetNamespaces.addElement(targetNamespace);
	    return true;
	}
    }

    public boolean containsTargetNamespace(String targetNamespace) {
	// if (this.targetNamespaces == null)
	// this.targetNamespaces = new Vector();
	boolean contains = false;
	for (Context c = this; contains == false && c != null; c = c.parent) {
	    if (c.targetNamespaces != null)
		contains = c.targetNamespaces.contains(targetNamespace);
	    if (contains == true)
		break;
	}
	return contains;
    }

    public void registerVariable(QName qname, boolean isFFLWOR)
	    throws MXQueryException {
	registerVariable(qname, false, isFFLWOR, null, false);
    }

    public void registerVariable(QName qname, boolean isFFLWOR,
	    XDMIterator seqTypeIter, boolean assignable)
	    throws MXQueryException {
	registerVariable(qname, false, isFFLWOR, seqTypeIter, assignable);
    }

    public void registerNewContextItem() throws MXQueryException {
	registerVariable(CONTEXT_ITEM, false, null, false);
    }

    public void registerVariable(QName qname, boolean external,
	    boolean isFFLWOR, XDMIterator seqTypeIter, boolean assignable)
	    throws MXQueryException {
	registerVariable(qname, external, isFFLWOR, seqTypeIter, true,
		assignable);
    }

    public void registerVariable(QName qname, boolean external,
	    boolean isFFLWOR, XDMIterator seqTypeIter, boolean resolve,
	    boolean assignable) throws MXQueryException {
	QName resolvedQName = qname;
	if (resolve)
	    resolvedQName = qname.resolveQNameNamespace(this);

	VariableHolder holder = (VariableHolder) variableMap.get(resolvedQName);
	if (holder != null) {
	    if (holder.isDeclared()) {
		if (isFFLWOR) {
		    throw new StaticException(
			    ErrorCodes.E0089_STATIC_FLWOR_VARIABE_AND_POSITIONAL_VARIABLE_HAVE_SAME_NAME,
			    "Variable already exists", null);
		} else {
		    throw new StaticException(
			    ErrorCodes.E0049_STATIC_MODULE_DUPLICATE_VARIABLE_NAMES,
			    "Variable already exists", null);
		}
	    } else {
		holder.setDeclared();
	    }
	} else {
	    holder = new VariableHolder(this, external);
	    holder.setDeclared();
	    variableMap.put(resolvedQName, holder);
	}
	holder.setSeqTypeIt(seqTypeIter);
	holder.setAssignable(assignable);
    }

    public QName registerAnonymousVariable() {
	QName qname = new QName(ANONYM_VARIABLE_PREFIX, ANONYM_VARIABLE_PREFIX
		+ ++anonymVariableCounter);
	variableMap.put(qname, new VariableHolder(this, false));
	return qname;
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQDynamicContext#setVariableValue(ch.ethz.mxquery.datamodel.QName,
     *      ch.ethz.mxquery.model.Iterator, boolean, boolean)
     */
    public void setVariableValue(QName qname, XDMIterator iter, boolean check,
	    boolean resolve) throws MXQueryException {
	VariableHolder holder = getVariable(qname, resolve);
	if (holder == null) {
	    if (check)
		throw new MXQueryException(
			ErrorCodes.A0009_EC_EVALUATION_NOT_POSSIBLE,
			"The variable "
				+ qname
				+ " is not registered and therefore it is not allowed to set a value",
			null);
	    else {
		registerVariable(qname, false);
		holder = getVariable(qname, resolve);
	    }
	}
	QName resolvedQName = qname;
	if (resolve)
	    resolvedQName = qname.resolveQNameNamespace(this);
	Source st = rootContext.storeSet.createStore(resolvedQName.toString(),
		iter, false);
	holder.setIter(st.getIterator(this));
	// holder.setIter(iter);
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQDynamicContext#setVariableValue(ch.ethz.mxquery.datamodel.QName,
     *      ch.ethz.mxquery.model.Iterator)
     */
    public void setVariableValue(QName qname, XDMIterator iter)
	    throws MXQueryException {
	setVariableValue(qname, iter, true, true);
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQDynamicContext#bindVariableValue(ch.ethz.mxquery.datamodel.QName,
     *      ch.ethz.mxquery.model.Iterator)
     */

    public void bindVariableValue(QName qname, XDMIterator iter)
	    throws MXQueryException {
	VariableHolder holder = getVariable(qname, true);
	if (holder == null) {
	    throw new MXQueryException(
		    ErrorCodes.A0009_EC_EVALUATION_NOT_POSSIBLE,
		    "The variable "
			    + qname
			    + " is not registered and therefore it is not allowed to set a value",
		    null);
	}
	holder.setIter(iter);

    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQDynamicContext#getVariable(ch.ethz.mxquery.datamodel.QName)
     */
    public VariableHolder getVariable(QName qname) throws MXQueryException {
	return getVariable(qname, true);
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQDynamicContext#getVariable(ch.ethz.mxquery.datamodel.QName,
     *      boolean)
     */
    public VariableHolder getVariable(QName qname, boolean resolve)
	    throws MXQueryException {
	QName resolvedQName = qname;
	if (resolve)
	    resolvedQName = qname.resolveQNameNamespace(this);
	Object value = null;
	for (Context c = this; value == null && c != null; c = c.parent)
	    value = c.variableMap.get(resolvedQName);
	return (VariableHolder) value;
    }

    public boolean checkVariable(QName qname) throws MXQueryException {
	VariableHolder holder = getVariable(qname);
	if (holder == null) {
	    return false;
	} else {
	    return true;
	}
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQDynamicContext#incVariableUse(ch.ethz.mxquery.datamodel.QName)
     */
    public void incVariableUse(QName qname) throws MXQueryException {
	VariableHolder var = getVariable(qname);
	if (var != null) {
	    var.incUseCounter();
	} else {
	    throw new RuntimeException(
		    "It is not allowed to increase the usage of an unknown function");
	}
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQDynamicContext#getStores()
     */
    public StoreSet getStores() {
	return rootContext.storeSet;
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#setOrderingMode(java.lang.String)
     */
    public void setOrderingMode(String value) throws MXQueryException {
	if (ORDERED.equals(value)) {
	    setNamedValue(ORDERING_MODE, ORDERED);
	} else if (UNORDERED.equals(value)) {
	    setNamedValue(ORDERING_MODE, UNORDERED);
	} else {
	    throw new MXQueryException(
		    ErrorCodes.A0009_EC_EVALUATION_NOT_POSSIBLE,
		    "Invalid Ordering mode", null);
	}
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#getOrderingMode()
     */
    public String getOrderingMode() {
	return (String) getNamedValue(ORDERING_MODE);
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#setDefaultOrderEmptySequence(java.lang.String)
     */
    public void setDefaultOrderEmptySequence(String value)
	    throws MXQueryException {
	if (ORDER_GREATEST.equals(value)) {
	    setNamedValue(ORDER_EMPTY_SEQUENCES, ORDER_GREATEST);
	} else if (ORDER_LEAST.equals(value)) {
	    setNamedValue(ORDER_EMPTY_SEQUENCES, ORDER_LEAST);
	} else {
	    throw new MXQueryException(
		    ErrorCodes.A0009_EC_EVALUATION_NOT_POSSIBLE,
		    "Invalid Default Order Empty mode", null);
	}
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#getDefaultOrderEmptySequence()
     */
    public String getDefaultOrderEmptySequence() {
	return (String) getNamedValue(ORDER_EMPTY_SEQUENCES);
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#setCopyNamespacesMode(boolean,
     *      boolean)
     */
    public void setCopyNamespacesMode(boolean preserve, boolean inherit) {
	if (inherit)
	    setNamedValue(COPY_NAMESPACE_INHERIT, COPY_MODE_INHERIT);
	else
	    setNamedValue(COPY_NAMESPACE_INHERIT, COPY_MODE_NO_INHERIT);
	if (preserve)
	    setNamedValue(COPY_NAMESPACE_PRESERVE, COPY_MODE_PRESERVE);
	else
	    setNamedValue(COPY_NAMESPACE_PRESERVE, COPY_MODE_NO_PRESERVE);

    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#getCopyNamespacesInheritMode()
     */
    public boolean getCopyNamespacesInheritMode() {
	if (((String) getNamedValue(COPY_NAMESPACE_INHERIT))
		.equals(COPY_MODE_NO_INHERIT))
	    return false;
	return true;
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#getCopyNamespacesPreserveMode()
     */
    public boolean getCopyNamespacesPreserveMode() {
	if (((String) getNamedValue(COPY_NAMESPACE_PRESERVE))
		.equals(COPY_MODE_NO_PRESERVE))
	    return false;
	return true;
    }

    /**
     * Prepares the context for as a webservice
     * 
     * @param port
     * @param namespace
     */
    public void setWebService(int port, Namespace namespace) {
	this.setNamedValue(WEB_SERVICE_PORT, new Integer(port));
	this.setNamedValue(WEB_SERVICE_NAMESPACE, namespace);
    }

    public void setWebService(boolean isWebService) {
	this.isWebService = isWebService;
    }

    public void setWebServiceNamespace(Namespace namespace) {
	this.setNamedValue(WEB_SERVICE_NAMESPACE, namespace);
    }

    public void setWebServiceName(String serviceName) {
	this.setNamedValue(WEB_SERVICE_NAME, serviceName);
    }

    public void setWebServiceEndpointName(String endpointName) {
	this.setNamedValue(WEB_SERVICE_ENDPOINT_NAME, endpointName);
    }

    /**
     * Returns if the context is declared as a web service.
     * 
     * @return true if this context is used by a module that is exported as a
     *         web service
     */
    public boolean isWebService() {
	Object value = getNamedValue(WEB_SERVICE_PORT);
	if (value != null) {
	    this.isWebService = true;
	}
	// value = getNamedValue(WEB_SERVICE_NAME);
	// if(value != null){
	// this.isWebService = true;
	// }
	value = getNamedValue(WEB_SERVICE_NAME);
	if (value != null) {
	    this.isWebService = true;
	}
	value = getNamedValue(WEB_SERVICE_ENDPOINT_NAME);
	if (value != null) {
	    this.isWebService = true;
	}
	return this.isWebService;
    }

    /**
     * Returns the web service port
     * 
     * @return an integer describing the web service port
     * @throws MXQueryException
     */
    public int getWebServicePort() throws MXQueryException {
	Object value = getNamedValue(WEB_SERVICE_PORT);
	if (value == null) {
	    throw new MXQueryException(
		    ErrorCodes.A0009_EC_EVALUATION_NOT_POSSIBLE,
		    "WebService port not defined", null);
	}
	return ((Integer) value).intValue();
    }

    /**
     * Returns the namespace of the to exposing web service functions
     * 
     * @return the namespace of this web service
     * @throws MXQueryException
     */
    public Namespace getWebServiceNamespace() throws MXQueryException {
	Object value = getNamedValue(WEB_SERVICE_NAMESPACE);
	if (value == null) {
	    throw new MXQueryException(
		    ErrorCodes.A0009_EC_EVALUATION_NOT_POSSIBLE,
		    "WebService name not defined", null);
	}
	return (Namespace) value;
    }

    public String getWebServiceName() {
	Object value = getNamedValue(WEB_SERVICE_NAME);
	return (String) value;
    }

    public String getWebServiceEndpointName() {
	Object value = getNamedValue(WEB_SERVICE_ENDPOINT_NAME);
	return (String) value;
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#getAllNamespaces()
     */
    public Hashtable getAllNamespaces() {
	final LinkedList maps = new LinkedList();
	for (Context c = this; c != null; c = c.parent)
	    maps.add(c.prefixNamespaceMap);
	return flattenHierarchy(maps);
    }

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

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#getAllVariables()
     */
    public Hashtable getAllVariables() {
	final LinkedList maps = new LinkedList();
	for (Context c = this; c != null; c = c.parent)
	    maps.add(c.variableMap);
	return flattenHierarchy(maps);
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#getBaseURI()
     */
    public String getBaseURI() {
	return (String) getNamedValue(BASE_URI);
    }

    /**
     * Sets if the context belongs to a module
     * 
     * @param isModule
     */
    public void setModuleContext(boolean isModule) {
	this.setNamedValue(MODULE, new Boolean(isModule));
    }

    public void exposeModule() {
	this.setWebService(true);
	this.setWebServiceNamespace(new Namespace(null, this.getModuleURI()));
    }

    /**
     * Checks if the context belongs to a module The property it is a module is
     * not inherited with the context.
     * 
     * @return true, if this the root Context of a module
     */
    public boolean isModuleContext() {
	Object value = namedValueMap.get(MODULE);
	if (value == null) {
	    return false;
	} else {
	    return ((Boolean) value).booleanValue();
	}
    }

    /**
     * Adds an context to the list of module contexts
     * 
     * @param modCtx
     */
    public void addModuleContext(XQStaticContext modCtx) {
	Vector mods = (Vector) getNamedValue(MODULE_CONTEXTS);
	if (mods == null) {
	    mods = new Vector();
	    rootContext.setNamedValue(MODULE_CONTEXTS, mods);
	}
	mods.addElement(modCtx);
    }

    public void clearModuleContexts() {
	Vector mods = (Vector) getNamedValue(MODULE_CONTEXTS);
	if (mods != null) {
	    rootContext.setNamedValue(MODULE_CONTEXTS, new Vector());
	}
    }

    public Vector getModuleContexts() {
	return (Vector) getNamedValue(MODULE_CONTEXTS);
    }

    /**
     * Sets the module URI
     */
    public void setModuleURI(String uri) {
	this.setNamedValue(MODULE_URI, uri);
    }

    /**
     * Gets the module URI if available or null. Module URI's are not inherited
     * with the context.
     * 
     * @return the URI for this module
     */
    public String getModuleURI() {
	Object value = namedValueMap.get(MODULE_URI);
	return value == null ? null : (String) value;
    }

    /**
     * Gets a named value either from this <code>Context</code> or, if not
     * found, from parent <code>Context</code>'s.
     * 
     * @param key
     *                The key of the value.
     * @return If found, returns the value; <code>null</code> otherwise.
     */
    private Object getNamedValue(String key) {
	Object value = null;
	for (Context c = this; value == null && c != null; c = c.parent)
	    value = c.namedValueMap.get(key);
	return value;
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#getConstructionMode()
     */
    public String getConstructionMode() {
	return (String) getNamedValue(CONSTRUCTION_MODE);
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQDynamicContext#getCurrentTime()
     */
    public MXQueryDateTime getCurrentTime() throws MXQueryException {
	final MXQueryDateTime cal = (MXQueryDateTime) getNamedValue(TIMESTAMP);
	if (cal == null)
	    // TODO EXCEP actually this is not part of the static context, so
	    // TODO EXCEP this should be a different exception
	    throw new MXQueryException(
		    ErrorCodes.A0009_EC_EVALUATION_NOT_POSSIBLE,
		    "Could not determine current time", null);
	return cal;
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQDynamicContext#getCurrentTimeZone()
     */
    public MXQueryDayTimeDuration getCurrentTimeZone() throws MXQueryException {
	final MXQueryDayTimeDuration tz = (MXQueryDayTimeDuration) getNamedValue(TIMEZONE);
	if (tz == null)
	    // TODO EXCEP actually this is not part of the static context, so
	    // TODO EXCEP this should be a different exception
	    throw new MXQueryException(
		    ErrorCodes.A0009_EC_EVALUATION_NOT_POSSIBLE,
		    "Could not determine current time zone", null);
	return tz;
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#getDefaultElementNamespace()
     */
    public String getDefaultElementNamespace() {
	Namespace ns = getNamespace(DEFAULT_ELEMENT_NAMESPACE_PREFIX);
	if (ns != null)
	    return ns.getURI();
	else
	    return null;
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#getDefaultElementPrefix()
     */
    public String getDefaultElementPrefix() {
	return this.getPrefix(getDefaultElementNamespace());
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#getDefaultFunctionNamespace()
     */
    public String getDefaultFunctionNamespace() {
	return (String) getNamedValue(DEFAULT_FUNCTION_NAMESPACE);
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#getDefaultFunctionPrefix()
     */
    public String getDefaultFunctionPrefix() {
	return this.getPrefix(getDefaultFunctionNamespace());
    }

    // /**
    // * Returns this <code>Context</code>'s {@link EntityResolver}.
    // *
    // * @return Returns said {@link EntityResolver}.
    // */
    // public EntityResolver getEntityResolver() {
    // return entityResolver != null ? entityResolver :
    // parent.getEntityResolver();
    // }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#getNamespace(java.lang.String)
     */
    public Namespace getNamespace(String prefix) {
	if (prefix == null) {
	    prefix = "";
	}
	Namespace uri = null;
	for (Context c = this; uri == null && c != null; c = c.parent)
	    uri = (Namespace) c.prefixNamespaceMap.get(prefix);
	// undeclaring
	if (uri != null && (uri.getURI() == null || uri.getURI().equals("")))
	    uri = null;
	return uri;
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#getPrefix(java.lang.String)
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

    /**
     * Gets the parent <code>Context</code> of this <code>Context</code>.
     * 
     * @return Returns said <code>Context</code> or <code>null</code> if
     *         none.
     */
    public Context getParent() {
	return parent;
    }

    /**
     * Set/replace the parent with the given context
     * 
     * @param ctx
     *                new parent context
     */
    public void setParent(Context ctx) {
	parent = ctx;
    }

    /**
     * Checks if the given Context is an ancestor of this context
     * 
     * @param ctx
     *                that is a possible ancestor
     * @return true if ctx is ancestor, false if not
     */
    public boolean isAnecstorContext(XQStaticContext ctx) {
	for (Context c = this.getParent(); c != null; c = c.parent) {
	    if (c == ctx)
		return true;
	}
	return false;
    }

    /**
     * Access the root context explicitly
     * 
     * @return the root context of the query/module
     */
    public Context getRootContext() {
	return rootContext;
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#addNamespace(ch.ethz.mxquery.datamodel.Namespace)
     */
    public void addNamespace(Namespace ns) throws MXQueryException {
	if (ns.getNamespacePrefix() == null || ns.getURI() == null) {
	    throw new MXQueryException(
		    ErrorCodes.A0009_EC_EVALUATION_NOT_POSSIBLE,
		    "Prefix or URI missing for namespace definition", null);
	}
	prefixNamespaceMap.put(ns.getNamespacePrefix(), ns);
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#addNamespace(java.lang.String,
     *      java.lang.String)
     */
    public void addNamespace(String prefix, String uri) throws MXQueryException {
	addNamespace(new Namespace(prefix, uri));
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#getAllNsURI()
     */
    public Vector getAllNsURI() {
	Vector resVec = new Vector();
	Enumeration en = prefixNamespaceMap.elements();
	while (en.hasMoreElements()) {
	    Namespace tmp = (Namespace) en.nextElement();
	    resVec.addElement(tmp.getURI());
	}
	return resVec;
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#removeNamespace(java.lang.String)
     */
    public void removeNamespace(String prefix) {
	this.prefixNamespaceMap.remove(prefix);
    }

    /**
     * This methods adds a new namespace to the context and returns the prefix
     * (if the namespace already exists) or creates an anonymous namespace just
     * for internal use
     * 
     * @param uri
     * @return the prefix of the anonymous namespace
     */
    public String addAnonymousNamespace(String uri) throws MXQueryException {
	String prefix = this.getPrefix(uri);
	if (prefix == null) {
	    prefix = ".NS" + ++anonymNamespaceCounter;
	    addNamespace(prefix, uri);
	}
	return prefix;
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQDynamicContext#setBaseURI(java.lang.String)
     */
    public void setBaseURI(String uri) {
	namedValueMap.put(BASE_URI, uri);
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#setConstructionMode(java.lang.String)
     */
    public void setConstructionMode(String value) {

	if (!PRESERVE.equals(value) && !STRIP.equals(value))
	    throw new IllegalArgumentException(value);

	namedValueMap.put(CONSTRUCTION_MODE, value);
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQDynamicContext#setCurrentTime()
     */
    public void setCurrentTime(MXQueryDateTime dateTime) {
	if (dateTime == null) {
	    Calendar cal = MXQueryDateTime.getNewCalendar();
	    TimeZone tz = cal.getTimeZone();
	    int timezoneOffset = tz.getRawOffset() / (1000 * 60);

	    try {
		String tzDurString;
		if (timezoneOffset < 0)
		    tzDurString = "-PT" + (-timezoneOffset) + "M";
		else
		    tzDurString = "PT" + timezoneOffset + "M";
		namedValueMap.put(TIMEZONE, new MXQueryDayTimeDuration(
			tzDurString));
	    } catch (MXQueryException e) {
		throw new RuntimeException(
			"setCurrentTime():Time zone could not be inicialized");
	    }

	    namedValueMap.put(TIMESTAMP, new MXQueryDateTime(cal,
		    timezoneOffset, MXQueryDateTime.VALUE_TYPE_DATE_TIME));
	} else {
	    MXQueryDayTimeDuration tz = dateTime.getTimezoneAsDuration();
	    if (tz == null) {
		try {
		    tz = new MXQueryDayTimeDuration("PT0M");
		} catch (MXQueryException e) {
		    // TODO Auto-generated catch block
		    e.printStackTrace();
		}
	    }
	    namedValueMap.put(TIMEZONE, tz);
	    namedValueMap.put(TIMESTAMP, dateTime);
	}
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQDynamicContext#setCurrentTime(java.util.TimeZone)
     */
    public void setCurrentTimeZone(TimeZone tz) {
	Calendar cal = MXQueryDateTime.getNewCalendar();
	int timezoneOffset = tz.getRawOffset() / (1000 * 60);

	try {
	    String tzDurString;
	    if (timezoneOffset < 0)
		tzDurString = "-PT" + (-timezoneOffset) + "M";
	    else
		tzDurString = "PT" + timezoneOffset + "M";
	    namedValueMap
		    .put(TIMEZONE, new MXQueryDayTimeDuration(tzDurString));
	} catch (MXQueryException e) {
	    throw new RuntimeException(
		    "setCurrentTime():Time zone could not be inicialized");
	}

	namedValueMap.put(TIMESTAMP, new MXQueryDateTime(cal, timezoneOffset,
		MXQueryDateTime.VALUE_TYPE_DATE_TIME));
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#setDefaultElementNamespace(java.lang.String)
     */
    public void setDefaultElementNamespace(String URI) throws MXQueryException {
	this.addNamespace(DEFAULT_ELEMENT_NAMESPACE_PREFIX, URI);
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#setDefaultFunctionNamespace(java.lang.String)
     */
    public void setDefaultFunctionNamespace(String URI) throws MXQueryException {
	addAnonymousNamespace(URI);
	namedValueMap.put(DEFAULT_FUNCTION_NAMESPACE, URI);
    }

    /**
     * Sets a named value for this <code>Context</code> that can be accessed
     * by this and all descendant contexts.
     * 
     * @param name
     *                the name of the value
     * @param value
     *                the value
     */
    private void setNamedValue(String name, Object value) {
	namedValueMap.put(name, value);
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#setBoundarySpaceHandling(boolean)
     */
    public void setBoundarySpaceHandling(boolean value) {
	namedValueMap.put(WHITESPACE_HANDLING, new Boolean(value));
    }

    public void addFunction(Function function, boolean checkExistence,
	    boolean external) throws MXQueryException {
	if (functionGallery == null) {
	    functionGallery = rootContext.functionGallery;
	}
	functionGallery.add(function, checkExistence, external);
    }

    public void addFunction(Function function) throws MXQueryException {
	addFunction(function, true, false);
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#getFunctions(java.lang.String)
     */
    public Hashtable getFunctions(String namespace) { // this 'namespace'
							// parameter is not
							// used!!!
	if (functionGallery != null)
	    return this.functionGallery.get(namespace);
	else
	    return new Hashtable();
    }

    public Hashtable getWSFunctions(String namespace) {
	if (functionGallery != null)
	    return this.functionGallery.getFunctionOfNS(namespace);
	else
	    return new Hashtable();
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#getFunction(ch.ethz.mxquery.datamodel.QName,
     *      int)
     */
    public Function getFunction(QName qname, int arity) throws MXQueryException {
	Function iter = null;
	for (Context c = this; iter == null && c != null; c = c.parent)
	    if (c.functionGallery != null) {
		iter = c.functionGallery.get(qname, arity);
	    }
	return iter;
    }

    /**
     * Add the locations URI where the contents of a module identified by the
     * namespace moduleNS_URI can be found
     * 
     * @param moduleNS_URI
     *                Namespace of module
     * @param location
     *                Location where to retrieve the module contents
     */
    public void addModuleLocation(String moduleNS_URI, String location) {
	Vector locat = (Vector) this.modLocation.get(moduleNS_URI);
	if (locat == null) {
	    locat = new Vector();
	}
	locat.insertElementAt(location, locat.size());
	this.modLocation.put(moduleNS_URI, locat);
    }

    public Vector getModuleLocation(String moduleNS_URI) {
	if (modLocation.size() == 0 && parent != null)
	    return parent.getModuleLocation(moduleNS_URI);
	else
	    return (Vector) this.modLocation.get(moduleNS_URI);
    }

    public void getModuleLocations(Context ctx) {
	modLocation = ctx.modLocation;
    }

    public void clearModuleLocation() {
	modLocation.clear();
    }

    public void addSchemaLocation(String schemaURI, String schemaLocation) {
	if (importedSchemaLocationsMap == null) {
	    importedSchemaLocationsMap = new Hashtable();
	}
	importedSchemaLocationsMap.put(schemaURI, schemaLocation);
    }

    public String getLocationOfSchema(String schemaURI) {
	if (importedSchemaLocationsMap == null)
	    return null;
	return (String) importedSchemaLocationsMap.get(schemaURI);
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#getWhitespaceHandling()
     */
    public boolean getsetBoundarySpaceHandling() {
	Object value = getNamedValue(WHITESPACE_HANDLING);
	return value == null ? false : ((Boolean) value).booleanValue();
    }

    /**
     * Shall input be validated?
     * 
     * @return the validation mode
     */

    public int getInputValidationMode() {
	Object value = getNamedValue(VALIDATION_AVAILABLE);
	return value == null ? Context.NO_VALIDATION : ((Integer) value)
		.intValue();
	// return true;
    }

    /**
     * Set the Type of input validation
     * 
     * @param valMode
     *                the validation mode
     */
    public void setInputValidationMode(int valMode) {
	setNamedValue(VALIDATION_AVAILABLE, new Integer(valMode));
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#getCollations()
     */

    public Set getCollations() {
	Set colls = new Set();
	colls.add(CODEPOINT_COLLATION_URI);
	return colls;
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#getDefaultCollation()
     */
    public String getDefaultCollation() {
	return CODEPOINT_COLLATION_URI;
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#setDefaultCollation(java.lang.String)
     */
    public void setDefaultCollation(String coll) throws MXQueryException {
	if (!coll.equals(CODEPOINT_COLLATION_URI))
	    throw new StaticException(
		    ErrorCodes.E0038_STATIC_MULTIPLE_DEFAULT_COLLATION_DECL_OR_STATICALLY_UNKNOWN,
		    "This collation is not supported by MXQuery", null);
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQDynamicContext#getContextItem()
     */
    public VariableHolder getContextItem() throws MXQueryException {
	return getVariable(CONTEXT_ITEM);
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQDynamicContext#setContextItem(ch.ethz.mxquery.model.Iterator)
     */
    public void setContextItem(XDMIterator iter) throws MXQueryException {
	Window wi = WindowFactory.getNewWindow(this, iter);
	getVariable(CONTEXT_ITEM).setIter(wi);
    }

    /**
     * Gets the {@link XQueryCollator} for the given URI.
     * 
     * @param uri
     *                The URI that identifies the collation.
     * @return Returns said {@link XQueryCollator}.
     * @throws XQueryException
     *                 Thrown only if there is no collator associated with the
     *                 given URI.
     */
    // public XQueryCollator getCollator( final String uri )
    /**
     * Add a collator.
     * 
     * @param uri
     *                The URI that identifies the collation.
     */
    // public void addCollator( String uri, String collator )
    static {

	try {
	    globalContext = new Context();
	    globalContext.setConstructionMode(PRESERVE);
	    globalContext.setCopyNamespacesMode(true, true);
	    globalContext.setBoundarySpaceHandling(false);
	    globalContext.addNamespace(NS_FN, URI_FN);
	    globalContext.addNamespace(NS_XML, URI_XML);
	    globalContext.addNamespace(NS_XS, URI_XS);
	    globalContext.addNamespace(NS_MXQ, URI_MXQ);
	    globalContext.addNamespace(NS_XSI, URI_XSI);
	    globalContext.addNamespace(NS_XDT, URI_XDT);
	    globalContext.addNamespace(NS_LOCAL, URI_LOCAL);
	    globalContext.addNamespace(NS_ERR, URI_ERR);
	    // globalContext.addNamespace(NS_XMLNS, URI_XMLNS);
	    globalContext.addNamespace(ANONYM_VARIABLE_PREFIX,
		    ANONYM_VARIABLE_URI);
	    globalContext.setDefaultFunctionNamespace(URI_FN);
	    globalContext.functionGallery = FunctionGallery
		    .createStdFunctionGallery(globalContext);
	    globalContext.setParserType(NONVALIDATED_INPUT_MODE_SAX);

	    globalContext.parent = null;
	} catch (MXQueryException err) {
	    throw new RuntimeException(err.toString());
	}
    }

    public Context copy() throws MXQueryException {
	Hashtable prefixNamespaceMapCopy = new Hashtable();
	Hashtable namedValueMapCopy = new Hashtable();
	Hashtable variableMapCopy = new Hashtable();
	Hashtable modLocationCopy = new Hashtable();
	Enumeration keys;
	Object o;

	keys = prefixNamespaceMap.keys();
	while (keys.hasMoreElements()) {
	    o = keys.nextElement();
	    prefixNamespaceMapCopy.put(o, ((Namespace) prefixNamespaceMap
		    .get(o)).copy());
	}

	keys = namedValueMap.keys();
	while (keys.hasMoreElements()) {
	    o = keys.nextElement();
	    namedValueMapCopy.put(o, (namedValueMap.get(o)));
	}

	keys = modLocation.keys();
	while (keys.hasMoreElements()) {
	    o = keys.nextElement();
	    Vector orig = ((Vector) modLocation.get(o));
	    Vector cV = new Vector();
	    for (int i = 0; i < orig.size(); i++) {
		cV.addElement(orig.elementAt(i));
	    }

	    modLocationCopy.put(o, cV);
	}

	Context copy = new Context(parent,
		null, // functiongallery
		prefixNamespaceMapCopy, namedValueMapCopy,
		variableMapCopy, // variableMapCopy
		modLocationCopy,
		null, // invertedListCopy
		location == null ? null : location.copy(),
		storeSet == null ? null : storeSet.copy());

	// copy function gallery
	// copy.functionGallery =
	// (functionGallery==null)?null:functionGallery.copy(copy);
	copy.functionGallery = functionGallery;
	// copy variable map
	keys = variableMap.keys();
	while (keys.hasMoreElements()) {
	    o = keys.nextElement();
	    variableMapCopy.put(o, ((VariableHolder) variableMap.get(o)).copy(
		    copy, new Vector()));
	}
	if (rootContext == this)
	    copy.rootContext = copy;
	else
	    copy.rootContext = parent.getRootContext();
	copy.setPosition(position);

	return copy;
    }

    public void flattenVariablesFrom(QName[] fsVars, Context innermostContext)
	    throws MXQueryException {
	for (Context c = innermostContext; c != this; c = c.parent)
	    for (int n = 0; n < fsVars.length; n++) {
		VariableHolder var = (VariableHolder) c.variableMap
			.get(fsVars[n]);
		c.variableMap.remove(fsVars[n]);
		if (var != null) {
		    this.registerVariable(fsVars[n], false);
		    VariableHolder varNew = (VariableHolder) this.variableMap
			    .get(fsVars[n]);
		    varNew.setResetable(var.isResetable());
		    if (var.isDeclared())
			varNew.setDeclared();
		    varNew.setUseCounter(var.getUsage());
		    // maybe extend to external?
		}
	    }

    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#getRevalidationMode()
     */
    public int getRevalidationMode() {
	Object value = getNamedValue(REVALIDATION_MODE);
	return value == null ? XQStaticContext.REVALIDATION_SKIP
		: ((Integer) value).intValue();

    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#setRevalidationMode(int)
     */
    public void setRevalidationMode(int valMode) throws MXQueryException {
	if (valMode == REVALIDATION_LAX || valMode == REVALIDATION_SKIP
		|| valMode == REVALIDATION_STRICT)
	    rootContext.setNamedValue(REVALIDATION_MODE, new Integer(valMode));
	else
	    throw new StaticException(
		    ErrorCodes.A0009_EC_EVALUATION_NOT_POSSIBLE,
		    "Invalid Revalidation mode specified",
		    QueryLocation.OUTSIDE_QUERY_LOC);
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#setFTLanguage(java.lang.String)
     */
    public void setFTLanguage(String lan) {
	setNamedValue(FT_LANG, lan);
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#getFTLanguage()
     */
    public String getFTLanguage() {
	Object value = getNamedValue(FT_LANG);
	return value == null ? "en" : (String) value;
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#setFTCase(int)
     */
    public void setFTCase(int caseOpt) {
	setNamedValue(FT_CASE, new Integer(caseOpt));
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#getFTCase()
     */
    public int getFTCase() {
	Object value = getNamedValue(FT_LANG);
	return value == null ? FTCaseMatchOption.CASE_INSENSITIVE
		: ((Integer) value).intValue();
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#setFTDiacriticsSensitive(boolean)
     */
    public void setFTDiacriticsSensitive(boolean sensitive) {
	setNamedValue(FT_DIACRITIC, new Boolean(sensitive));
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#isFTDiacriticsSensitive()
     */
    public boolean isFTDiacriticsSensitive() {
	Object value = getNamedValue(FT_DIACRITIC);
	return value == null ? false : ((Boolean) value).booleanValue();
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#setFTStemming(boolean)
     */
    public void setFTStemming(boolean stemming) {
	setNamedValue(FT_STEM, new Boolean(stemming));
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#isFTStemming()
     */
    public boolean isFTStemming() {
	Object value = getNamedValue(FT_STEM);
	return value == null ? false : ((Boolean) value).booleanValue();
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#setFTWildcard(boolean)
     */
    public void setFTWildcard(boolean wildcards) {
	setNamedValue(FT_WILDCARD, new Boolean(wildcards));
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#isFTWildcard()
     */
    public boolean isFTWildcard() {
	Object value = getNamedValue(FT_STEM);
	return value == null ? false : ((Boolean) value).booleanValue();
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#setFTStopwords(ch.ethz.mxquery.model.ft.FTStopWordsMatchOption)
     */
    public void setFTStopwords(FTStopWordsMatchOption sw) {
	setNamedValue(FT_STOPWORD, sw);
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#getFTStopWords()
     */
    public FTStopWordsMatchOption getFTStopWords() {
	Object value = getNamedValue(FT_STOPWORD);
	return value == null ? new FTStopWordsMatchOption()
		: (FTStopWordsMatchOption) value;
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#setFTThesaurus(ch.ethz.mxquery.model.ft.FTThesaurusMatchOption)
     */
    public void setFTThesaurus(FTThesaurusMatchOption thes) {
	setNamedValue(FT_THESAURUS, thes);
    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#getFTThesaurus()
     */
    public FTThesaurusMatchOption getFTThesaurus() {
	Object value = getNamedValue(FT_THESAURUS);
	return value == null ? new FTThesaurusMatchOption()
		: (FTThesaurusMatchOption) value;

    }

    /*
     * (non-Javadoc)
     * 
     * @see ch.ethz.mxquery.contextConfig.XQStaticContext#isXPath10Compat()
     */
    public boolean isXPath10Compat() {
	return false;
    }

    /**
     * Access the global context explicitly
     * 
     * @return the global context singleton
     */
    public static Context getGlobalContext() {
	return globalContext;
    }

}

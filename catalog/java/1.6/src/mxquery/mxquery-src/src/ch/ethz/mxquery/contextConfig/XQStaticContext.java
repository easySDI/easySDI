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

import java.util.Hashtable;
import java.util.Vector;

import ch.ethz.mxquery.datamodel.Namespace;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.functions.Function;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.model.ft.FTCaseMatchOption;
import ch.ethz.mxquery.model.ft.FTStopWordsMatchOption;
import ch.ethz.mxquery.model.ft.FTThesaurusMatchOption;
import ch.ethz.mxquery.util.Set;

public interface XQStaticContext {

	public final static String PRESERVE = "preserve";
	public final static String STRIP = "strip";
	public final static String ORDERED = "ordered";
	public final static String UNORDERED = "unordered";
	public final static String ORDER_GREATEST = "greatest";
	public final static String ORDER_LEAST = "least";
	public final static String COPY_MODE_PRESERVE = "preserve";
	public final static String COPY_MODE_NO_PRESERVE = "no-preserve";
	public final static String COPY_MODE_INHERIT = "inherit";
	public final static String COPY_MODE_NO_INHERIT = "no-inherit";
	public final static int REVALIDATION_LAX = 0;
	public final static int REVALIDATION_STRICT = 1;
	public final static int REVALIDATION_SKIP = 2;
	public static final String CODEPOINT_COLLATION_URI = "http://www.w3.org/2005/xpath-functions/collation/codepoint";
	public final static String URI_FN = "http://www.w3.org/2005/xpath-functions";
	public final static String NS_XML = "xml";
	public final static String URI_XML = "http://www.w3.org/XML/1998/namespace";
	public final static String URI_XS = "http://www.w3.org/2001/XMLSchema";
	public final static String URI_XSI = "http://www.w3.org/2001/XMLSchema-instance";
	public final static String URI_XDT = "http://www.w3.org/2005/xpath-datatypes";
	public final static String URI_LOCAL = "http://www.w3.org/2005/xquery-local-functions";
	public final static String NS_XMLNS = "xmlns";
	public final static String URI_XMLNS = "http://www.w3.org/2000/xmlns/";
	public final static String NS_ERR = "err";
	public final static String URI_ERR = "http://www.w3.org/2005/xqt-errors";

	/**
	 * Sets the Ordering mode, which has the value ordered or unordered,
	 * affects the ordering of the result sequence returned by certain path
	 * expressions, union, intersect, and except expressions, and FLWOR
	 * expressions that have no order by clause.] Details are provided in the
	 * descriptions of these expressions.
	 */
	public abstract void setOrderingMode(String value) throws MXQueryException;

	public abstract String getOrderingMode();

	/**
	 * Sets the default order for empty sequences. This component controls the
	 * processing of empty sequences and NaN values as ordering keys in an order
	 * by clause in a FLWOR expression, as described in 3.8.3 Order By and
	 * Return Clauses. Its value may be greatest or least.
	 * 
	 * @param value
	 * @throws MXQueryException
	 */
	public abstract void setDefaultOrderEmptySequence(String value)
			throws MXQueryException;

	/**
	 * Returns the default order for empty sequences 
	 * @return ORDER_GREATEST or ORDER_LEAST
	 */
	public abstract String getDefaultOrderEmptySequence();

	/**
	 * Set the copy namespaces modes for element construction, see 3.7.1.3 and 4.9 of the spec
	 * @param preserve keep the namespaces of the original element 
	 * @param inherit inherit namespaces of the enclosing constructed element
	 */
	public abstract void setCopyNamespacesMode(boolean preserve, boolean inherit);

	/**
	 * Returns which Inherit setting of the copy namespace mode is set
	 * @return false if NO INHERIT set, otherwise true
	 */
	public abstract boolean getCopyNamespacesInheritMode();

	/**
	 * Returns which Preserve setting of the copy namespace mode is set
	 * @return false if NO PRESERVER set, otherwise true
	 */
	public abstract boolean getCopyNamespacesPreserveMode();

	/**
	 * 
	 * @return All the visible namespace prefix-URI mappings.
	 */
	public abstract Hashtable getAllNamespaces();

	/**
	 * @return All the visible name-variable mappings.
	 */
	public abstract Hashtable getAllVariables();

	/**
	 * Get the base URI.
	 * 
	 * @return Returns said URI.
	 */
	public abstract String getBaseURI();


	/**
	 * Sets the base URI.
	 * 
	 * @param uri
	 *            The URI to set.
	 */
	public abstract void setBaseURI(String uri);

	
	/**
	 * Gets the construction mode of the Context
	 * 
	 * @return The construction mode value
	 */
	public abstract String getConstructionMode();

	/**
	 * Gets the defaultElementNamespace attribute of the Context object
	 * 
	 * @return The defaultElementNamespace value
	 */
	public abstract String getDefaultElementNamespace();

	/**
	 * Returns the default function prefix, which can be an anonymous prefix
	 * @return a string expressing the default element prefix
	 */
	public abstract String getDefaultElementPrefix();

	/**
	 * Gets the defaultFunctionNamespace attribute of the Context object
	 * 
	 * @return The defaultFunctionNamespace value
	 */
	public abstract String getDefaultFunctionNamespace();

	/**
	 * Returns the default function prefix, which can be an anonymous prefix
	 * @return a String representing the default function namespace prefix
	 */
	public abstract String getDefaultFunctionPrefix();

	/**
	 * Gets the namespace URI associated with the given prefix
	 * 
	 * @param prefix
	 *            The given prefix
	 * @return The associated namespace URI
	 */
	public abstract Namespace getNamespace(String prefix);

	/**
	 * get the (namespace-)prefix for a given URI. This acutally iterates
	 * through the context entries. This iteration is acceptable, because this
	 * method is very rarely used.
	 * 
	 * @param uri
	 *            The URI to look for.
	 * @return the Namespace object for this prefix
	 */
	public abstract String getPrefix(String uri);

    /**
     * Add a namespace mapping.
     * @param ns a namespace object containing prefix and uri
     * @throws MXQueryException
     */
	public abstract void addNamespace(Namespace ns) throws MXQueryException;
	/**
	 * Add a prefix/namespace mapping.
	 * 
	 * @param prefix
	 *            The prefix.
	 * @param uri
	 *            The URI of the namespace.
	 */	
	public abstract void addNamespace(String prefix, String uri)
			throws MXQueryException;

	public abstract Vector getAllNsURI();

	/**
	 * Remove a prefix/namespace from mapping.
	 * 
	 * @param prefix
	 * 			The prefix of the namespace to be removed
	 */
	public abstract void removeNamespace(String prefix);

	/**
	 * Sets the construction mode of the Context
	 * 
	 * @param value
	 *            The construction mode value
	 */
	public abstract void setConstructionMode(String value);

	/**
	 * Sets the defaultCollation attribute of the Context object
	 * 
	 * @param uri
	 *            The new defaultCollation value
	 */
	// public void setDefaultCollation( String uri );
	/**
	 * Sets the defaultElementNamespace attribute of the Context object
	 * 
	 * @param URI
	 *            The new defaultElementNamespace value
	 */
	public abstract void setDefaultElementNamespace(String URI)
			throws MXQueryException;

	/**
	 * Sets the defaultFunctionNamespace attribute of the Context object
	 * 
	 * @param URI
	 *            The new defaultFunctionNamespace value
	 */
	public abstract void setDefaultFunctionNamespace(String URI)
			throws MXQueryException;

	/**
	 * Sets the boundary Space Handling attribute of the Context object
	 * 
	 * @param value true - preserve false not preserve
	 *            
	 */
	public abstract void setBoundarySpaceHandling(boolean value);

	/**
	 * Adds a function to the context
	 * @param function
	 * @throws MXQueryException
	 */
	public abstract void addFunction(Function function, boolean checkExistence, boolean external) throws MXQueryException;
	/**
	 * 
	 * @param namespace
	 * @return
	 */
	
	/**
	 * Adds a function to the context
	 * @param function
	 * @throws MXQueryException
	 */
	public abstract void addFunction(Function function) throws MXQueryException;

	/**
	 * Get all functions with in particular namespace
	 * @param namespace the namespace in which the functions must be
	 * @return a Hashtable containing (FunctionSignate->Function) entries
	 */
	public abstract Hashtable getFunctions(String namespace);
	/**
	 * Get a function with a name and number of parameters
	 * @param qname the name of the function
	 * @param arity the number of parameters
	 * @return A function Object containing both the exact signature and means to get the implementation
	 * @throws MXQueryException
	 */
	public abstract Function getFunction(QName qname, int arity)
			throws MXQueryException;

	/**
	 * Gets the whitespaceHandling attribute of the Context object
	 * 
	 * @return The whitespaceHandling value
	 */
	public abstract boolean getsetBoundarySpaceHandling();

	/**
	 * Get the statically known collations
	 * @return The set of Collations known to the engine
	 */
	public abstract Set getCollations();
	/**
	 * Get the default collation
	 * @return A String/URI describing the default collation 
	 */
	public abstract String getDefaultCollation();

	public abstract void setDefaultCollation(String coll)
			throws MXQueryException;

	/**
	 * Return the revalidation mode for update expressions
	 * @return one of REVALIDATION_LAX, REVALIDATION_STRICT, REVALIDATION_SKIP
	 */
	public abstract int getRevalidationMode();

	/**
	 * Set the revalidation mode for update expressions
	 * @param valMode one of REVALIDATION_LAX, REVALIDATION_STRICT, REVALIDATION_SKIP
	 * @throws MXQueryException If an invalid or unsupported revalidation mode was specified
	 */
	public abstract void setRevalidationMode(int valMode)
			throws MXQueryException;

	/**
	 * Set the Full Text Language Option
	 * @param lan
	 */
	public abstract void setFTLanguage(String lan);

	/**
	 * Get the Full Text Language Option
	 * @return the language identifier (e.g. "en") for the language option
	 */
	public abstract String getFTLanguage();

	/**
	 * Set the Full Text Case Option
	 * @param caseOpt one of {@link FTCaseMatchOption}.CASE_INSENSITIVE, CASE_SENSITIVE, CASE_LOWERCASE, CASE_UPPERCASE 
	 */
	public abstract void setFTCase(int caseOpt);

	/**
	 * Set the Full Text Case Option
	 * @return one of {@link FTCaseMatchOption}.CASE_INSENSITIVE, CASE_SENSITIVE, CASE_LOWERCASE, CASE_UPPERCASE 
	 */
	public abstract int getFTCase();

	/**
	 * Set the full text diacritics sensitive option (i.e. if diacritics should be considered or not)
	 * @param sensitive
	 */
	public abstract void setFTDiacriticsSensitive(boolean sensitive);

	/**
	 * Get the  full text diacritics sensitive option 
	 * @return true diacritics sensitive, false not sensitive
	 */
	public abstract boolean isFTDiacriticsSensitive();

	/**
	 * Set the full text stemming option, i.e. if stemming should be used in the matching
	 * @param stemming true stemming should be used, no stemming should not be used
	 */
	public abstract void setFTStemming(boolean stemming);

	/**
	 * Get the full text stemming option, i.e. if stemming should be used in the matching
	 * @return true stemming should be used, no stemming should not be used
	 * 
	 */
	public abstract boolean isFTStemming();

	/**
	 * Set the full text wildcard option, i.e. if wildcards should be used in the matching
	 * @param wildcards true wildcards should be used, no wildscards should not be used
	 */
	public abstract void setFTWildcard(boolean wildcards);

	/**
	 * Get the full text wildcard option, i.e. if wildcards should be used in the matching
	 * @return true wildcards should be used, no wildscards should not be used
	 * 
	 */
	public abstract boolean isFTWildcard();

	/**
	 * Set the full text stopwords option, i.e. which stopwords should be used in the matching
	 * @param sw A FTStopWordsMatchOption object describing the stopwords
	 */
	public abstract void setFTStopwords(FTStopWordsMatchOption sw);

	/**
	 * Get the full text stopwords option, i.e. which stopwords should be used in the matching
	 * @return A FTStopWordsMatchOption object describing the stopwords
	 */
	public abstract FTStopWordsMatchOption getFTStopWords();
	/**
	 * Set the full text thesaurus option, i.e. which thesauri should be used in the matching
	 * @param thes A FTThesaurusMatchOption object describing the thesauri
	 */
	public abstract void setFTThesaurus(FTThesaurusMatchOption thes);
	/**
	 * Set the full text thesaurus option, i.e. which thesauri should be used in the matching
	 * @return A FTThesaurusMatchOption object describing the thesauri
	 */

	public abstract FTThesaurusMatchOption getFTThesaurus();

	/**
	 * Get the XPath 1.0 Compatibility setting
	 * @return the XPath 1.0 compatibility setting - always false 
	 */
	public abstract boolean isXPath10Compat();

	/**
	 * Registers a new variable. If the variable already exists, an error is thrown.
	 * @param qname
	 * @param isFFLWOR
	 */
	public void registerVariable(QName qname, boolean isFFLWOR)
			throws MXQueryException;
	/**
	 * Registers a new variable. If the variable already exists, an error is thrown.
	 * @param qname
	 * @param isFFLWOR
	 * @param seqTypeIter
	 * @param assignable
	 * @throws MXQueryException
	 */
	public void registerVariable(QName qname, boolean isFFLWOR, XDMIterator seqTypeIter,
			boolean assignable) throws MXQueryException;
	/**
	 * Registers a new context item in this scope
	 * @throws MXQueryException
	 */
	public void registerNewContextItem() throws MXQueryException;
	/**
	 * Registers a new variable.  If the variable already exists, an error is thrown.
	 * @param qname
	 * @param external
	 * @param isFFLWOR
	 * @param seqTypeIter
	 * @param assignable
	 * @throws MXQueryException
	 */
	public void registerVariable(QName qname, boolean external, boolean isFFLWOR,
			XDMIterator seqTypeIter, boolean assignable) throws MXQueryException;

	/**
	 * Registers a new variable. If the variable already exists, an error is thrown.
	 * @param qname
	 * @param external
	 * @param isFFLWOR
	 * @param seqTypeIter
	 * @param resolve
	 * @param assignable
	 * @throws MXQueryException
	 */
	public void registerVariable(QName qname, boolean external, boolean isFFLWOR,
			XDMIterator seqTypeIter, boolean resolve, boolean assignable) throws MXQueryException;
	/**
	 * Registers a new "anonymous" (=internal) variable
	 * @return the QName of the registered variable
	 */
	public QName registerAnonymousVariable();
	/**
	 * Check if the variable is defined
	 * @param qname the name of the variable
	 * @return true if the variable exists, false otherwise
	 * @throws MXQueryException
	 */
	public boolean checkVariable(QName qname) throws MXQueryException;

	/**
	 * This methods adds a new namespace to the context and returns the
	 * prefix (if the namespace already exists) or creates an anonymous namespace
	 * just for internal use
	 * @param uri
	 * @return the prefix of the anonymous namespace
	 */
	public String addAnonymousNamespace(String uri) throws MXQueryException;

	public Hashtable getWSFunctions(String namespace);

	/** 
	 * Add the locations URI where the contents of a module identified by the namespace moduleNS_URI can be found
	 * @param moduleNS_URI Namespace of module
	 * @param location Location where to retrieve the module contents
	 */
	public void addModuleLocation(String moduleNS_URI, String location);

	/**
	 * Retrieve the list of locations where the contents of the module are stored
	 * @param moduleNS_URI Namespace of the module
	 * @return the locations/URIs associated with this module
	 */
	public Vector getModuleLocation(String moduleNS_URI);
	/**
	 * Clear this list of locations mapping between module identifiers and locations
	 */
	public void clearModuleLocation();
	/**
	 * 
	 * @param schemaURI
	 * @param schemaLocation
	 */
	public void addSchemaLocation(String schemaURI, String schemaLocation);

	public String getLocationOfSchema(String schemaURI);

}
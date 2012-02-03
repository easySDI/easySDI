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

package ch.ethz.mxquery.query.parser;

import java.util.Vector;

import org.apache.xerces.dom.DOMInputImpl;
import org.apache.xerces.impl.xs.XSAttributeDecl;
import org.apache.xerces.impl.xs.XSElementDecl;
import org.apache.xerces.xs.XSComplexTypeDefinition;
import org.apache.xerces.xs.XSConstants;
import org.apache.xerces.xs.XSImplementation;
import org.apache.xerces.xs.XSLoader;
import org.apache.xerces.xs.XSModel;
import org.apache.xerces.xs.XSNamedMap;
import org.apache.xerces.xs.XSNamespaceItemList;
import org.apache.xerces.xs.XSSimpleTypeDefinition;
import org.apache.xerces.xs.XSTypeDefinition;
import org.w3c.dom.DOMConfiguration;
import org.w3c.dom.DOMError;
import org.w3c.dom.DOMErrorHandler;
import org.w3c.dom.bootstrap.DOMImplementationRegistry;
import org.w3c.dom.ls.LSInput;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeDictionary;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.exceptions.StaticException;
import ch.ethz.mxquery.functions.Function;
import ch.ethz.mxquery.functions.FunctionSignature;
import ch.ethz.mxquery.functions.xs.XSBinary;
import ch.ethz.mxquery.functions.xs.XSBoolean;
import ch.ethz.mxquery.functions.xs.XSConstructorIterator;
import ch.ethz.mxquery.functions.xs.XSDate;
import ch.ethz.mxquery.functions.xs.XSDateTime;
import ch.ethz.mxquery.functions.xs.XSDayTimeDuration;
import ch.ethz.mxquery.functions.xs.XSDecimal;
import ch.ethz.mxquery.functions.xs.XSDouble;
import ch.ethz.mxquery.functions.xs.XSDuration;
import ch.ethz.mxquery.functions.xs.XSFloat;
import ch.ethz.mxquery.functions.xs.XSGregorian;
import ch.ethz.mxquery.functions.xs.XSInteger;
import ch.ethz.mxquery.functions.xs.XSQName;
import ch.ethz.mxquery.functions.xs.XSString;
import ch.ethz.mxquery.functions.xs.XSTime;
import ch.ethz.mxquery.functions.xs.XSYearMonthDuration;
import ch.ethz.mxquery.model.XDMIterator;

public class SchemaParser implements DOMErrorHandler {
    /**
     * Parses a given schema file and and updates the ISSDs : type Dictionary
     * accordingly
     * 
     * @param uri
     * @param targetNamespace
     * @param dict
     * @throws MXQueryException
     */
    public static void parseSchema(Context ctx, String uri,
	    String targetNamespace, TypeDictionary dict, QueryLocation loc)
	    throws MXQueryException {
	boolean preloadedSchema = false;
	if (targetNamespace == null
		|| !dict.containsDefinitions4Namespace(targetNamespace))
	    try {
		if ((targetNamespace == null) || loc == null)
		    preloadedSchema = true;

		XSModel model = getXSModel(uri);
		if (model != null) {
		    updateDictionary(uri, targetNamespace, model, dict, loc,
			    ctx, preloadedSchema);
		}
	    } catch (StaticException ex) {
		throw ex;
	    } catch (RuntimeException re) {
		throw new StaticException(
			ErrorCodes.E0012_STATIC_SCHEMA_IMPORTS_NOT_VALID,
			"Imported schema definitions not valid : "
				+ re.getMessage(), loc);
	    } catch (Exception e) {
		throw new StaticException(
			ErrorCodes.E0012_STATIC_SCHEMA_IMPORTS_NOT_VALID,
			"Imported schema definitions not valid : "
				+ e.getMessage(), loc);
	    }
    }

    public static void parseSchema(String uri, String targetNamespace,
	    TypeDictionary dict, QueryLocation loc) throws MXQueryException {
	parseSchema(null, uri, targetNamespace, dict, loc);
    }

    /**
     * Parses a string representing a schema and and updates the ISSDs : type
     * Dictionary accordingly
     * 
     */
    public static void parseSchema(Context ctx, LSInput schema,
	    String targetNamespace, TypeDictionary dict, QueryLocation loc)
	    throws ClassCastException, ClassNotFoundException,
	    InstantiationException, IllegalAccessException, MXQueryException {
	XSModel model = getXSModel(schema);
	if (model != null)
	    try {
		updateDictionary(null, targetNamespace, model, dict, loc, ctx,
			false);
	    } catch (RuntimeException re) {
		throw new StaticException(
			ErrorCodes.E0012_STATIC_SCHEMA_IMPORTS_NOT_VALID,
			"Imported schema definitions not valid : "
				+ re.getMessage(), null);
	    }

    }

    public static void preLoadSchema(String uri) throws MXQueryException {
	parseSchema(uri, null, Context.initDictionary(), null);
    }

    private static void updateDictionary(String uri, String targetNamespace,
	    XSModel model, TypeDictionary dict, QueryLocation loc, Context ctx,
	    boolean preloadedSchema) throws MXQueryException {
	String itemNamespace;
	Vector UDTFunctionsList = null;
	XSNamespaceItemList nmlist = model.getNamespaceItems();
	String schemaTargetNamespace = nmlist.item(0).getSchemaNamespace();
	if (targetNamespace == null)
	    targetNamespace = schemaTargetNamespace; // null used for schema
							// preloading

	if (!(targetNamespace.equals(schemaTargetNamespace) || (schemaTargetNamespace == null && targetNamespace
		.length() == 0)))
	    throw new StaticException(
		    ErrorCodes.E0059_STATIC_UNABLE_TO_PROCESS_SCHEMA_OR_MODULE_IMPORT,
		    "Imported schema target namespace {"
			    + schemaTargetNamespace + "} does not match {"
			    + targetNamespace + "}", loc);

	// type declarations
	XSNamedMap map = model.getComponents(XSConstants.TYPE_DEFINITION);
	if (map.getLength() != 0) {
	    for (int i = 0; i < map.getLength(); i++) {
		boolean atomic = false;
		XSTypeDefinition item = (XSTypeDefinition) map.item(i);

		if (item instanceof XSSimpleTypeDefinition) {
		    if (((XSSimpleTypeDefinition) item).getPrimitiveType() != null)
			atomic = true;
		}
		// only schema components bound to the targetNamespace should be
		// added to the dictionary
		// -- separate imports needed for schemas imported within schema
		// however all types declared in schema should be available for
		// validating documents

		itemNamespace = item.getNamespace();

		dict.addEntry("{" + itemNamespace + "}" + item.getName(), item);
		if (itemNamespace.equals(targetNamespace)) {
		    // dict.addEntry("{" + itemNamespace + "}" + item.getName(),
		    // item);
		    if (ctx != null && atomic)
			addFunction(ctx, item, dict);
		    else if (ctx == null && atomic && preloadedSchema) {
			UDTFunctionsList = updateUDTFunctionNamesList(
				UDTFunctionsList, item);
		    }
		    if (UDTFunctionsList != null)
			saveUDTFunctionNamesList(UDTFunctionsList,
				targetNamespace, dict);
		} else
		    dict.add2XSImportList("{" + itemNamespace + "}"
			    + item.getName());
	    }
	    if (preloadedSchema && UDTFunctionsList != null)
		saveUDTFunctionNamesList(UDTFunctionsList, targetNamespace,
			dict);
	    dict.addToNamespacesSet(targetNamespace);
	    if (uri != null)
		dict.updateSchemaLocation(targetNamespace + " " + uri);
	}

	// global Element Declarations
	map = model.getComponents(XSConstants.ELEMENT_DECLARATION);
	if (map.getLength() != 0) {
	    for (int i = 0; i < map.getLength(); i++) {

		XSElementDecl item = (XSElementDecl) map.item(i);

		// only schema components bound to the
		// targetNamespace should be added to the dictionary
		// -- separate imports needed for schemas imported
		// within schema
		itemNamespace = item.getNamespace();
		if (itemNamespace != null
			&& itemNamespace.equals(targetNamespace))
		    dict.addEntry("(" + itemNamespace + ")" + item.getName(),
			    item);
	    }
	}

	// global Attribute Declarations
	map = model.getComponents(XSConstants.ATTRIBUTE_DECLARATION);
	if (map.getLength() != 0) {
	    for (int i = 0; i < map.getLength(); i++) {

		XSAttributeDecl item = (XSAttributeDecl) map.item(i);

		// only schema components bound to the
		// targetNamespace should be added to the dictionary
		// -- separate imports needed for schemas imported
		// within schema
		itemNamespace = item.getNamespace();
		if (itemNamespace.equals(targetNamespace))
		    dict.addEntry("[" + itemNamespace + "]" + item.getName(),
			    item);
	    }
	}

    }

    private static XSLoader createSchemaLoader() throws ClassCastException,
	    ClassNotFoundException, InstantiationException,
	    IllegalAccessException {
	System.setProperty(DOMImplementationRegistry.PROPERTY,
		"org.apache.xerces.dom.DOMXSImplementationSourceImpl");
	DOMImplementationRegistry registry = DOMImplementationRegistry
		.newInstance();

	XSImplementation impl = (XSImplementation) registry
		.getDOMImplementation("XS-Loader");

	XSLoader schemaLoader = impl.createXSLoader(null);

	DOMConfiguration config = schemaLoader.getConfig();

	// create Error Handler
	DOMErrorHandler errorHandler = new SchemaParser();

	// set error handler
	config.setParameter("error-handler", errorHandler);

	// set validation feature
	config.setParameter("validate", Boolean.TRUE);
	return schemaLoader;
    }

    private static void saveUDTFunctionNamesList(Vector functionsList,
	    String targetNamespace, TypeDictionary dict) {
	dict.addUDTFunctionsListEntry(targetNamespace, functionsList);
    }

    private static Vector updateUDTFunctionNamesList(Vector list,
	    XSTypeDefinition name) {
	if (list == null)
	    list = new Vector();
	list.add(name);
	return list;
    }

    /**
     * Adds a UDT constructor function to the gallery of functions of the
     * current context
     * 
     * @param ctx
     * @param typeDef
     * @throws MXQueryException
     */
    public static void addFunction(Context ctx, XSTypeDefinition typeDef,
	    TypeDictionary dict) throws MXQueryException {
	short category = typeDef.getTypeCategory();
	// UDT constructor functions only for Simple types or Complex Types with
	// simple content
	if (category == XSTypeDefinition.SIMPLE_TYPE
		|| (typeDef.getTypeCategory() == XSTypeDefinition.COMPLEX_TYPE && ((XSComplexTypeDefinition) typeDef)
			.getContentType() == XSComplexTypeDefinition.CONTENTTYPE_SIMPLE)) {
	    String fName = typeDef.getName();

	    String namespace = typeDef.getNamespace();
	    String prefix = ctx.getPrefix(namespace);
	    QName qn = new QName(prefix, fName);

	    qn = qn.resolveQNameNamespace(ctx);

	    TypeInfo[] paramTypes = new TypeInfo[1];
	    paramTypes[0] = new TypeInfo(Type.ITEM,
		    Type.OCCURRENCE_IND_ZERO_OR_MORE, null, null);
	    FunctionSignature signature = new FunctionSignature(qn, paramTypes,
		    FunctionSignature.SYSTEM_FUNCTION,
		    XDMIterator.EXPR_CATEGORY_SIMPLE, false);
	    XDMIterator it = getBaseTypeConstructor(ctx, Type.getTypeFootprint(
		    new QName(namespace, namespace, fName), dict), dict);
	    if (it != null) {
		((XSConstructorIterator) it)
			.setFacetsList(((XSSimpleTypeDefinition) typeDef)
				.getFacets());
		((XSConstructorIterator) it)
			.setMfacetsList(((XSSimpleTypeDefinition) typeDef)
				.getMultiValueFacets());

		// System.out.println(typeDef.getName()+":"+((XSConstructorIterator)it).getMfacetsList());
	    }
	    Function function = new Function(null, signature, it);
	    ctx.addFunction(function);
	}
    }

    public static XDMIterator getBaseTypeConstructor(Context ctx,
	    int typeFootprint, TypeDictionary dict) throws MXQueryException {
	XSConstructorIterator it;
	if (Type.isTypeOrSubTypeOf(typeFootprint, Type.YEAR_MONTH_DURATION,
		dict)) {
	    it = new XSYearMonthDuration();
	} else if (Type.isTypeOrSubTypeOf(typeFootprint,
		Type.DAY_TIME_DURATION, dict)) {
	    it = new XSDayTimeDuration();
	} else if (Type.isTypeOrSubTypeOf(typeFootprint, Type.INTEGER, dict)) {
	    it = new XSInteger();
	} else if (Type.isTypeOrSubTypeOf(typeFootprint, Type.FLOAT, dict)) {
	    it = new XSFloat();
	} else if (Type.isTypeOrSubTypeOf(typeFootprint, Type.DOUBLE, dict)) {
	    it = new XSDouble();
	} else if (Type.isTypeOrSubTypeOf(typeFootprint, Type.DECIMAL, dict)) {
	    it = new XSDecimal();
	} else if (Type.isTypeOrSubTypeOf(typeFootprint, Type.DURATION, dict)) {
	    it = new XSDuration();
	} else if (Type.isTypeOrSubTypeOf(typeFootprint, Type.G_DAY, dict)
		|| Type.isTypeOrSubTypeOf(typeFootprint, Type.G_MONTH, dict)
		|| Type
			.isTypeOrSubTypeOf(typeFootprint, Type.G_MONTH_DAY,
				dict)
		|| Type.isTypeOrSubTypeOf(typeFootprint, Type.G_YEAR, dict)
		|| Type.isTypeOrSubTypeOf(typeFootprint, Type.G_YEAR_MONTH,
			dict)) {
	    it = new XSGregorian();
	} else if (Type.isTypeOrSubTypeOf(typeFootprint, Type.QNAME, dict)
		|| Type.isTypeOrSubTypeOf(typeFootprint, Type.NOTATION, dict)) {
	    it = new XSQName();
	} else if (Type.isTypeOrSubTypeOf(typeFootprint, Type.TIME, dict)) {
	    it = new XSTime();
	} else if (Type.isTypeOrSubTypeOf(typeFootprint, Type.BASE64_BINARY,
		dict)) {
	    it = new XSBinary();
	} else if (Type.isTypeOrSubTypeOf(typeFootprint, Type.BOOLEAN, dict)) {
	    it = new XSBoolean();
	    return it;
	} else if (Type.isTypeOrSubTypeOf(typeFootprint, Type.DATE, dict)) {
	    it = new XSDate();
	} else if (Type.isTypeOrSubTypeOf(typeFootprint, Type.DATE_TIME, dict)) {
	    it = new XSDateTime();
	} else if (Type.isTypeOrSubTypeOf(typeFootprint, Type.DATE, dict)) {
	    it = new XSDate();
	} else {
	    it = new XSString();
	}
	it.setTargetType(typeFootprint);
	it.setContext(ctx, false);
	return it;
    }

    /**
     * Return the XSModel for a given schema document uri
     * 
     * @param uri
     * @return
     * @throws ClassCastException
     * @throws ClassNotFoundException
     * @throws InstantiationException
     * @throws IllegalAccessException
     */
    private static XSModel getXSModel(String uri) throws ClassCastException,
	    ClassNotFoundException, InstantiationException,
	    IllegalAccessException {
	XSLoader schemaLoader = createSchemaLoader();
	// parse document
	XSModel model = schemaLoader.loadURI(uri);
	return model;
    }

    /**
     * Return the XSModel for given LSInput
     * 
     * @param lsInput
     * @return
     * @throws ClassCastException
     * @throws ClassNotFoundException
     * @throws InstantiationException
     * @throws IllegalAccessException
     */
    private static XSModel getXSModel(LSInput lsInput)
	    throws ClassCastException, ClassNotFoundException,
	    InstantiationException, IllegalAccessException {
	XSLoader schemaLoader = createSchemaLoader();
	XSModel model = schemaLoader.load(lsInput);
	return model;
    }

    /**
     * Convenience method that wraps a string in a LSINput interface
     * implementation - to be used with the XSLoader
     */
    public static LSInput createLSInput(String schema) {
	DOMInputImpl lsInput = new DOMInputImpl();
	lsInput.setStringData(schema);
	return lsInput;
    }

    public boolean handleError(DOMError error) {
	if (!(error.getSeverity() == DOMError.SEVERITY_WARNING))
	    throw new RuntimeException(error.getMessage());
	else
	    return false;
    }
}
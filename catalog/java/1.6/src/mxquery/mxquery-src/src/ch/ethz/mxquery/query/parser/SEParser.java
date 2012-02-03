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

import java.io.File;
import java.io.IOException;
import java.net.MalformedURLException;
import java.net.URL;
import java.net.URLConnection; // import java.util.ArrayList;
// import java.util.List;
import java.util.Vector;

import org.apache.xerces.xs.XSSimpleTypeDefinition;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.contextConfig.XQStaticContext;
import ch.ethz.mxquery.datamodel.Namespace;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeDictionary;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.StaticException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.iterators.ValidateIterator;
import ch.ethz.mxquery.iterators.forseq.ForseqIterator; // import
							// ch.ethz.mxquery.iterators.forseq.ForseqWindowIndexIterator;
import ch.ethz.mxquery.iterators.forseq.ForseqWindowNaiveIterator;
import ch.ethz.mxquery.model.Iterator;
import ch.ethz.mxquery.model.WindowVariable; // import
						// ch.ethz.mxquery.opt.expr.CTerm;
// import ch.ethz.mxquery.opt.expr.CompareLiteralIndexSchema;
// import ch.ethz.mxquery.opt.expr.DTerm;
// import ch.ethz.mxquery.opt.index.Index;
// import ch.ethz.mxquery.opt.index.IndexImpl;
// import ch.ethz.mxquery.query.optimizer.ExpressionSplitter;
// import ch.ethz.mxquery.query.optimizer.VarSearchTerms;
import ch.ethz.mxquery.model.XDMIterator;

/**
 * Extended version of the parser that enables features and optimizations that
 * are only available on J2SE 1.4 and higher
 * 
 * @author Peter M. Fischer
 * 
 */

public class SEParser extends Parser {

    protected TypeInfo SchemaElementTest() throws MXQueryException {
	int oldIndex = index;
	TypeInfo stepData;
	if (parseKeyword("schema-element")) {
	    if (!co.isSchemaAwareness())
		generateStaticError(
			ErrorCodes.A0002_EC_NOT_SUPPORTED,
			"Schema support disabled: schema-element() not available. Either enable it, or switch to a version of MXQuery that supports it");
	    stepData = new TypeInfo();
	    if (!parseString("(", true, false)) {
		index = oldIndex;
		return null;
	    }

	    QName q = QName();
	    if (q == null) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: 'QName' expected!");
	    }
	    if (!parseString(")", true, false)) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: ')' expected!");
	    }
	    if (!co.isSchemaAwareness())
		generateStaticError(ErrorCodes.A0002_EC_NOT_SUPPORTED,
			"SchemaElementTest not supported yet!");
	    try {
		q = rewriteUDTQNameWithResolvedPrefix(q);
	    } catch (MXQueryException me) {
		if (me
			.getErrorCode()
			.equals(
				ErrorCodes.E0051_STATIC_QNAME_AS_ATOMICTYPE_NOT_DEFINED_AS_ATOMIC))
		    throw new TypeException(
			    ErrorCodes.E0008_STATIC_NAME_OR_PREFIX_NOT_DEFINED,
			    "Type not available for element check",
			    getCurrentLoc());
		else
		    throw me;
	    }
	    String namespaceUri = q.getNamespaceURI();
	    String localPart = q.getLocalPart();

	    if (Context.getDictionary().lookUpByName(
		    "(" + namespaceUri + ")" + localPart) == null)
		generateStaticError(
			ErrorCodes.E0008_STATIC_NAME_OR_PREFIX_NOT_DEFINED,
			"Global Element " + q.toString()
				+ " has not been declared");
	    stepData.setName(localPart);
	    stepData.setNameSpaceURI(namespaceUri);
	    stepData.setType(Type.TYPE_NK_SCHEMA_ELEM_TEST);
	    return stepData;
	    //			
	}
	index = oldIndex;
	return null;
    }

    protected TypeInfo SchemaAttrTest() throws MXQueryException {
	int oldIndex = index;
	TypeInfo stepData;
	if (parseKeyword("schema-attribute")) {
	    if (!co.isSchemaAwareness())
		generateStaticError(
			ErrorCodes.A0002_EC_NOT_SUPPORTED,
			"Schema support disabled: schema-attribute() not available. Either enable it, or switch to a version of MXQuery that supports it");
	    stepData = new TypeInfo();
	    if (!parseString("(", true, false)) {
		index = oldIndex;
		return null;
	    }

	    QName q = QName();
	    if (q == null) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: 'QName' expected!");
	    }

	    if (!parseString(")", true, false)) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: ')' expected!");
	    }
	    if (!co.isSchemaAwareness())
		generateStaticError(ErrorCodes.A0002_EC_NOT_SUPPORTED,
			"SchemaAttributeTest not supported yet!");
	    try {
		q = rewriteUDTQNameWithResolvedPrefix(q);
	    } catch (MXQueryException me) {
		if (me
			.getErrorCode()
			.equals(
				ErrorCodes.E0051_STATIC_QNAME_AS_ATOMICTYPE_NOT_DEFINED_AS_ATOMIC))
		    throw new TypeException(
			    ErrorCodes.E0008_STATIC_NAME_OR_PREFIX_NOT_DEFINED,
			    "Type not available for element check",
			    getCurrentLoc());
		else
		    throw me;
	    }
	    String namespaceUri = q.getNamespaceURI();
	    String localPart = q.getLocalPart();

	    if (Context.getDictionary().lookUpByName(
		    "[" + namespaceUri + "]" + localPart) == null)
		generateStaticError(
			ErrorCodes.E0008_STATIC_NAME_OR_PREFIX_NOT_DEFINED,
			"Global Attribute " + q.toString()
				+ " has not been declared");
	    stepData.setName(localPart);
	    stepData.setNameSpaceURI(namespaceUri);
	    stepData.setType(Type.TYPE_NK_SCHEMA_ATTR_TEST);

	    return stepData;
	    // generateStaticError(ErrorCodes.A0002_EC_NOT_SUPPORTED,
	    // "SchemaAttributeTest not supported yet!");
	}
	index = oldIndex;
	return null;
    }

    protected TypeInfo ElementTest() throws MXQueryException {
	int oldIndex = index;
	TypeInfo stepData;
	int type = 0;

	if (parseString("element", true, false)
		&& parseString("(", true, false)) {

	    QName qname;
	    stepData = new TypeInfo();

	    if (parseString("*", true, false)) {
		stepData.setName("*");
		if (parseString(",", true, false)) {
		    QName q = QName();
		    if (q == null) {
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"Error while parsing: 'QName' expected!");
		    } else {
			try {
			    String namespacePrefix = q.getNamespacePrefix();
			    if ((namespacePrefix == null)
				    || (!q.getNamespacePrefix().equals(
					    Type.NAMESPACE_XS)))
				q = rewriteUDTQNameWithResolvedPrefix(q);
			    type = Type.getTypeFootprint(q, Context
				    .getDictionary());
			    // stepData.setTypeAnnotation(type);
			    // System.out.println(Type.getTypeQName(stepData.getTypeAnnotation(),Context.getDictionary()
			    // ));
			} catch (MXQueryException me) {
			    throw new TypeException(
				    ErrorCodes.E0008_STATIC_NAME_OR_PREFIX_NOT_DEFINED,
				    "Schema Type not available for element check",
				    getCurrentLoc());
			}
			// generateStaticError(ErrorCodes.A0002_EC_NOT_SUPPORTED,
			// "ElementTest with 'type' not supported yet!");
		    }
		    if (parseString("?", true, false)) {
			type = Type.setIsNilled(type);
			// stepData.setTypeAnnotation(type);
			// generateStaticError(ErrorCodes.A0002_EC_NOT_SUPPORTED,
			// "ElementTest with 'type ?' not supported yet!");
		    }
		}
	    } else if ((qname = QName()) != null) {
		String namespacePrefix = qname.getNamespacePrefix();
		if (namespacePrefix != null
			|| getCurrentContext().getDefaultElementNamespace() != null) {
		    Namespace ns = getCurrentContext().getNamespace(
			    namespacePrefix);
		    if (ns != null)
			stepData.setNameSpaceURI(ns.getURI());
		    else
			generateStaticError(
				ErrorCodes.E0081_STATIC_QUERY_UNKNOWN_NAMESPACE,
				"Prefix " + qname.getNamespacePrefix()
					+ " not bound");
		}
		stepData.setName(qname.toString());

		if (parseString(",", true, false)) {
		    QName q = QName();
		    if (q == null) {
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"Error while parsing: 'QName' expected!");
		    } else {
			try {
			    namespacePrefix = q.getNamespacePrefix();
			    if ((namespacePrefix == null)
				    || (!q.getNamespacePrefix().equals(
					    Type.NAMESPACE_XS)))
				q = rewriteUDTQNameWithResolvedPrefix(q);
			    type = Type.getTypeFootprint(q, Context
				    .getDictionary());
			} catch (MXQueryException me) {
			    if (me.getErrorCode() == ErrorCodes.E0051_STATIC_QNAME_AS_ATOMICTYPE_NOT_DEFINED_AS_ATOMIC)
				throw new TypeException(
					ErrorCodes.E0008_STATIC_NAME_OR_PREFIX_NOT_DEFINED,
					"Type not available for element check",
					getCurrentLoc());
			    else
				throw me;
			}
		    }
		}
	    }

	    if (!parseString(")", true, false)) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: ')' expected!");
	    }
	    // System.out.println(Type.getTypeQName(type,
	    // Context.getDictionary()));
	    // stepData.setType(Type.TYPE_NK_ELEM_TEST);
	    stepData.setType(Type.START_TAG | type);

	    return stepData;
	}

	index = oldIndex;
	return null;
    }

    protected TypeInfo AttributeTest() throws MXQueryException {
	int oldIndex = index;
	TypeInfo stepData;
	int type = 0;

	if (parseString("attribute", true, false)
		&& parseString("(", true, false)) {

	    QName qname;
	    stepData = new TypeInfo();

	    if (parseString("*", true, false)) {
		stepData.setName("*");
		if (parseString(",", true, false)) {
		    QName q = QName();
		    if (q == null) {
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"Error while parsing: 'QName' expected!");
		    } else {
			try {
			    String namespacePrefix = q.getNamespacePrefix();
			    if ((namespacePrefix == null)
				    || (!q.getNamespacePrefix().equals(
					    Type.NAMESPACE_XS)))
				q = rewriteUDTQNameWithResolvedPrefix(q);
			    type = Type.getTypeFootprint(q, Context
				    .getDictionary());
			} catch (MXQueryException me) {
			    if (me.getErrorCode() == ErrorCodes.E0051_STATIC_QNAME_AS_ATOMICTYPE_NOT_DEFINED_AS_ATOMIC)
				throw new TypeException(
					ErrorCodes.E0008_STATIC_NAME_OR_PREFIX_NOT_DEFINED,
					"Type not available for element check",
					getCurrentLoc());
			    else
				throw me;
			}

		    }
		}
	    } else if ((qname = QName()) != null) {
		stepData.setName(qname.toString());
		String namespacePrefix = qname.getNamespacePrefix();
		if (namespacePrefix != null && !namespacePrefix.equals("")) {
		    Namespace ns = getCurrentContext().getNamespace(
			    namespacePrefix);
		    if (ns != null)
			stepData.setNameSpaceURI(ns.getURI());
		}
		// stepData.setName( qname.toString() );
		if (parseString(",", true, false)) {
		    QName q = QName();
		    if (q == null) {
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"Error while parsing: 'QName' expected!");
		    } else {
			namespacePrefix = q.getNamespacePrefix();
			try {
			    if ((namespacePrefix == null)
				    || (!q.getNamespacePrefix().equals(
					    Type.NAMESPACE_XS)))
				q = rewriteUDTQNameWithResolvedPrefix(q);

			    type = Type.getTypeFootprint(q, Context
				    .getDictionary());
			} catch (MXQueryException me) {
			    if (me.getErrorCode() == ErrorCodes.E0051_STATIC_QNAME_AS_ATOMICTYPE_NOT_DEFINED_AS_ATOMIC)
				throw new TypeException(
					ErrorCodes.E0008_STATIC_NAME_OR_PREFIX_NOT_DEFINED,
					"Type not available for element check",
					getCurrentLoc());
			    else
				throw me;
			}
		    }
		}
	    }

	    if (!parseString(")", true, false)) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: ')' expected!");
	    }
	    stepData.setType(Type.TYPE_NK_ATTR_TEST | type);
	    // stepData.setType(Type.createAttributeType(type));
	    return stepData;
	}

	index = oldIndex;
	return null;
    }

    protected void processSchemas(Vector schema_import, String ns_uri,
	    String prefix, boolean defaultElementNamespace)
	    throws MXQueryException {
	if (!co.isSchemaAwareness())
	    generateStaticError(
		    ErrorCodes.E0009_STATIC_SCHEMA_IMPORTS_NOT_SUPPORTED,
		    "Schema Imports are disabled. Either enable it, or switch to a version of MXQuery that supports it");
	Context ctx = getCurrentContext();
	Namespace ns = ctx.getNamespace(prefix);
	TypeDictionary dict = Context.initDictionary();
	boolean preloadedSchema = false;

	if (dict.containsDefinitions4Namespace(ns_uri))
	    preloadedSchema = true;
	if (ns != null && (prefix != null)
		&& !(ns.getURI().equals(XQStaticContext.URI_XS))
		|| ((prefix != null) && prefix.equals(Type.NAMESPACE_XS))) {
	    generateStaticError(
		    ErrorCodes.E0033_STATIC_MODULE_MULTIPLE_BINDINGS_FOR_SAME_PREFIX,
		    "Multiple declarations of Namespace " + prefix);
	}

	if (ctx.addTargetNamespace(ns_uri) && (prefix != null)) {
	    ctx.addNamespace(new Namespace(prefix, ns_uri)); // bind prefix
								// with
								// namespace of
								// imported
								// schema
	} else if (prefix != null)
	    generateStaticError(
		    ErrorCodes.E0058_STATIC_SCHEMA_IMPORTS_SPECIFY_SAME_TARGET_NAMESPACE,
		    "Multiple declaration of TargetNameSpace {" + ns_uri + "}");

	if ((((schema_import.size() == 0) && !defaultElementNamespace && !dict
		.containsDefinitions4Namespace(ns_uri)) || !exists(
		schema_import, ns_uri))
		&& !dict.containsDefinitions4Namespace(ns_uri)
		&& !ns_uri.equals(XQStaticContext.URI_XS))
	    throw new StaticException(
		    ErrorCodes.E0059_STATIC_UNABLE_TO_PROCESS_SCHEMA_OR_MODULE_IMPORT,
		    "Unable to locate schema for namespace: " + ns_uri,
		    getCurrentLoc());
	else if (!ns_uri.equals(XQStaticContext.URI_XS)) {
	    Object[] schemas = new Object[schema_import.size()];
	    schema_import.copyInto(schemas);
	    String[] uriList = new String[schemas.length];

	    for (int j = 0; j < schemas.length; j++) {
		uriList[j] = (String) schema_import.elementAt(j);
		try {
		    SchemaParser.parseSchema(ctx, uriList[j], ns_uri, dict,
			    getCurrentLoc());
		} catch (StaticException e) {
		    if (j == schemas.length - 1) {
			throw e;
		    }
		}
	    }
	    // if (preloadedSchema && schema_import.size()==0)
	    if (preloadedSchema)
		addUDTFunctions(ns_uri, prefix);
	}
    }

    private void addUDTFunctions(String ns_uri, String prefix)
	    throws MXQueryException {
	TypeDictionary dict = Context.getDictionary();
	Object list = Context.getDictionary().getUDTFunctionsList(ns_uri);
	if (list != null) {
	    for (java.util.Iterator iter = ((Vector) list).iterator(); iter
		    .hasNext();) {
		XSSimpleTypeDefinition typeDef = (XSSimpleTypeDefinition) iter
			.next();
		SchemaParser.addFunction(getCurrentContext(), typeDef, dict);
	    }
	}

    }

    private boolean exists(Vector schema_import, String ns_uri) {
	Object[] schemas = new Object[schema_import.size()];
	schema_import.copyInto(schemas);
	String[] uriList = new String[schemas.length];
	for (int i = 0; i < uriList.length; i++) {
	    uriList[i] = (String) schema_import.elementAt(i);
	    File f = new File(uriList[i]);
	    if (f.exists())
		return true;
	    try {
		URL url = new URL(uriList[i]);
		URLConnection con = url.openConnection();
		con.getInputStream();
		return true;
	    } catch (MalformedURLException e) {
	    } catch (IOException e) {
	    }
	}
	if (getCurrentContext().getLocationOfSchema(ns_uri) != null) {
	    schema_import.addElement(getCurrentContext().getLocationOfSchema(
		    ns_uri));
	    return true;
	}
	return false;
    }

    protected ForseqIterator generateForseqIterator(QName varQName,
	    TypeInfo qType, XDMIterator seq, int windowType,
	    Context outerContext, WindowVariable[] startVars,
	    XDMIterator startExpr, boolean forceEnd, WindowVariable[] endVars,
	    boolean onNewStart, XDMIterator endExpr) throws MXQueryException {
	ForseqIterator res;

	// if (!parallelExecution && windowType ==
	// ForseqIterator.SLIDING_WINDOW) {
	// // determine indexes +
	// //First we split the expression to find possibilities to apply an
	// index
	// ExpressionSplitter splitter = new
	// ExpressionSplitter(VarSearchTerms.createVarSearchTerms(startVars),
	// VarSearchTerms.createVarSearchTerms(endVars));
	// DTerm startTerms = splitter.splitExpression(startExpr);
	// DTerm endTerms = splitter.splitExpression(endExpr);
	//
	// Index index = new IndexImpl();
	//			
	// CompareLiteralIndexSchema[] indexes;
	//			
	// boolean startEndDependenciesWithoutIndex = false;
	//			
	// List indexSchemas = new ArrayList();
	// for(int i = 0 ; i < endTerms.size(); i++){
	// CTerm cTerm = endTerms.getCTerm(i);
	// CompareLiteralIndexSchema schema = cTerm.getIndexSchema(i);
	// //If it is indexable try to apply an index
	// if(schema != null){
	// //If we have a tumbling window - we know there is only one window
	// open per time
	// //if the index is not accepted, you have still to check the
	// predicates
	// if(windowType != ForseqIterator.TUMBLING_WINDOW &&
	// index.registerIndex(schema)){
	// indexSchemas.add(schema);
	// cTerm.setIndexed(true);
	// }else{
	// startEndDependenciesWithoutIndex = true;
	// }
	// }else{
	// if(cTerm.getDependency() == CTerm.DEPENDENCY_STARTEND){
	// startEndDependenciesWithoutIndex = true;
	// }
	// }
	// }
	// index.compileIndex();
	//			
	// indexes = new CompareLiteralIndexSchema[indexSchemas.size()];
	// for(int i = 0 ; i < indexSchemas.size();i++){
	// indexes[i] = (CompareLiteralIndexSchema)indexSchemas.get(i);
	// }
	// return new ForseqWindowIndexIterator(outerContext, windowType,
	// varQName, qType, seq, startVars, startExpr, endVars, endExpr,
	// forceEnd,
	// onNewStart, ForseqIterator.ORDER_MODE_END, getCurrentLoc(),
	// startTerms, endTerms, index, indexes,
	// startEndDependenciesWithoutIndex);
	// } else {
	res = new ForseqWindowNaiveIterator(outerContext, windowType, varQName,
		qType, seq, startVars, startExpr, endVars, endExpr, forceEnd,
		onNewStart, ForseqIterator.ORDER_MODE_END, getCurrentLoc());
	// }

	return res;
    }

    protected Iterator validate(Iterator exprIterator, int mode)
	    throws MXQueryException, StaticException {
	if (!co.isSchemaAwareness())
	    generateStaticError(
		    ErrorCodes.E0075_STATIC_VALIDATION_NOT_SUPPORTED,
		    "ValidateExpr is disabled. Either enable it, or switch to a version of MXQuery that supports it");
	return new ValidateIterator(mode, getCurrentContext(),
		new Iterator[] { exprIterator }, getCurrentLoc());

    }
}

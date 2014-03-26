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

import java.io.IOException;
import java.util.Enumeration;
import java.util.Hashtable;
import java.util.Stack;
import java.util.Vector;

import ch.ethz.mxquery.contextConfig.CompilerOptions;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.contextConfig.XQStaticContext;
import ch.ethz.mxquery.datamodel.MXQueryBigDecimal;
import ch.ethz.mxquery.datamodel.MXQueryDouble;
import ch.ethz.mxquery.datamodel.Namespace;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.Source;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.types.TypeLexicalConstraints;
import ch.ethz.mxquery.datamodel.xdm.CommentToken;
import ch.ethz.mxquery.datamodel.xdm.NamedToken;
import ch.ethz.mxquery.datamodel.xdm.ProcessingInstrToken;
import ch.ethz.mxquery.datamodel.xdm.TextToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.datamodel.xdm.UntypedAtomicAttrToken;
import ch.ethz.mxquery.datamodel.xdm.UntypedAtomicToken;
import ch.ethz.mxquery.datamodel.xdm.XDMScope;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.exceptions.StaticException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.extensionsModules.ExtensionModulesConfig;
import ch.ethz.mxquery.functions.Function;
import ch.ethz.mxquery.functions.FunctionSignature;
import ch.ethz.mxquery.functions.fn.BooleanIterator;
import ch.ethz.mxquery.functions.fn.CountIterator;
import ch.ethz.mxquery.functions.fn.DataValuesIterator;
import ch.ethz.mxquery.functions.fn.Doc;
import ch.ethz.mxquery.functions.fn.Last;
import ch.ethz.mxquery.functions.fn.Position;
import ch.ethz.mxquery.functions.ft.Score;
import ch.ethz.mxquery.iterators.AdditiveIterator;
import ch.ethz.mxquery.iterators.AttributeIterator;
import ch.ethz.mxquery.iterators.CastAsIterator;
import ch.ethz.mxquery.iterators.ChildIterator;
import ch.ethz.mxquery.iterators.CompareIterator;
import ch.ethz.mxquery.iterators.ComputedCommentConstrIterator;
import ch.ethz.mxquery.iterators.ComputedPIConstrIterator;
import ch.ethz.mxquery.iterators.ComputedTextConstrIterator;
import ch.ethz.mxquery.iterators.DescendantOrSelfIterator;
import ch.ethz.mxquery.iterators.DocOrderIterator;
import ch.ethz.mxquery.iterators.ExceptIterator;
import ch.ethz.mxquery.iterators.FFLWORIterator;
import ch.ethz.mxquery.iterators.ForIterator;
import ch.ethz.mxquery.iterators.IfThenElseIterator;
import ch.ethz.mxquery.iterators.InstanceOfIterator;
import ch.ethz.mxquery.iterators.IntersectIterator;
import ch.ethz.mxquery.iterators.LetIterator;
import ch.ethz.mxquery.iterators.LogicalIterator;
import ch.ethz.mxquery.iterators.MultiplicativeIterator;
import ch.ethz.mxquery.iterators.OrderByIterator;
import ch.ethz.mxquery.iterators.ParentIterator;
import ch.ethz.mxquery.iterators.PredicateIterator;
import ch.ethz.mxquery.iterators.QuantifiedIterator;
import ch.ethz.mxquery.iterators.RangeIterator;
import ch.ethz.mxquery.iterators.SelfAxisIterator;
import ch.ethz.mxquery.iterators.SequenceIterator;
import ch.ethz.mxquery.iterators.SequenceTypeIterator;
import ch.ethz.mxquery.iterators.TokenIterator;
import ch.ethz.mxquery.iterators.TreatAsIterator;
import ch.ethz.mxquery.iterators.TypeSwitchIterator;
import ch.ethz.mxquery.iterators.UnionIterator;
import ch.ethz.mxquery.iterators.UserdefFuncCall;
import ch.ethz.mxquery.iterators.UserdefFuncCallLateBinding;
import ch.ethz.mxquery.iterators.VariableIterator;
import ch.ethz.mxquery.iterators.XMLAttrIterator;
import ch.ethz.mxquery.iterators.XMLContent;
import ch.ethz.mxquery.iterators.forseq.ForseqGeneralIterator;
import ch.ethz.mxquery.iterators.forseq.ForseqIterator;
import ch.ethz.mxquery.iterators.forseq.ForseqWindowNaiveIterator;
import ch.ethz.mxquery.iterators.ft.FTAndIterator;
import ch.ethz.mxquery.iterators.ft.FTBaseIterator;
import ch.ethz.mxquery.iterators.ft.FTContainsIterator;
import ch.ethz.mxquery.iterators.ft.FTIteratorInterface;
import ch.ethz.mxquery.iterators.ft.FTMildNotIterator;
import ch.ethz.mxquery.iterators.ft.FTOrIterator;
import ch.ethz.mxquery.iterators.ft.FTSelectionIterator;
import ch.ethz.mxquery.iterators.ft.FTUnaryNotIterator;
import ch.ethz.mxquery.iterators.ft.MatchIterator;
import ch.ethz.mxquery.iterators.ft.Words;
import ch.ethz.mxquery.iterators.scripting.AssignIterator;
import ch.ethz.mxquery.iterators.scripting.ApplyExprIterator;
import ch.ethz.mxquery.iterators.scripting.BlockDeclIterator;
import ch.ethz.mxquery.iterators.scripting.BlockIterator;
import ch.ethz.mxquery.iterators.scripting.CatchIterator;
import ch.ethz.mxquery.iterators.scripting.EarlyReturnIterator;
import ch.ethz.mxquery.iterators.scripting.TryIterator;
import ch.ethz.mxquery.iterators.scripting.WhileIterator;
import ch.ethz.mxquery.iterators.update.DeleteIterator;
import ch.ethz.mxquery.iterators.update.InsertIterator;
import ch.ethz.mxquery.iterators.update.RenameIterator;
import ch.ethz.mxquery.iterators.update.ReplaceIterator;
import ch.ethz.mxquery.iterators.update.TransformIterator;
import ch.ethz.mxquery.model.Constants;
import ch.ethz.mxquery.model.EmptySequenceIterator;
import ch.ethz.mxquery.model.Iterator;
import ch.ethz.mxquery.model.VariableHolder;
import ch.ethz.mxquery.model.Wildcard;
import ch.ethz.mxquery.model.Window;
import ch.ethz.mxquery.model.WindowVariable;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.model.ft.AnyAllOption;
import ch.ethz.mxquery.model.ft.FTCaseMatchOption;
import ch.ethz.mxquery.model.ft.FTContent;
import ch.ethz.mxquery.model.ft.FTDistance;
import ch.ethz.mxquery.model.ft.FTExtensionMatchOption;
import ch.ethz.mxquery.model.ft.FTLanguageMatchOption;
import ch.ethz.mxquery.model.ft.FTOrder;
import ch.ethz.mxquery.model.ft.FTPositional;
import ch.ethz.mxquery.model.ft.FTScope;
import ch.ethz.mxquery.model.ft.FTStopWordsMatchOption;
import ch.ethz.mxquery.model.ft.FTThesaurusMatchOption;
import ch.ethz.mxquery.model.ft.FTWindow;
import ch.ethz.mxquery.model.ft.MatchOption;
import ch.ethz.mxquery.model.ft.Range;
import ch.ethz.mxquery.model.updatePrimitives.UpdatePrimitive;
import ch.ethz.mxquery.query.XQCompiler;
import ch.ethz.mxquery.query.impl.CompilerImpl;
import ch.ethz.mxquery.query.webservice.WSDLHandler;
import ch.ethz.mxquery.util.FileReader;
import ch.ethz.mxquery.util.Location;
import ch.ethz.mxquery.util.ObjectObjectPair;
import ch.ethz.mxquery.util.OrderOptions;
import ch.ethz.mxquery.util.PlatformDependentUtils;
import ch.ethz.mxquery.util.Set;
import ch.ethz.mxquery.util.URIUtils;
import ch.ethz.mxquery.util.Utils;

/**
 * 
 * @author Matthias Braun
 * 
 */

public class Parser extends CharacterCompatibility {

    static class FunctionSigHolder {
	QName funcName;
	int arity;
    }

    private ExtensionModulesConfig extensions = new ExtensionModulesConfig();

    private static Hashtable hRestrictedFunctionNames;
    private static Hashtable hRestrictedTypeNames;

    CompilerOptions co;

    protected int index = -1;

    private String query = null;

    private boolean skippingAllowed = true;

    private boolean defElemSet = false;

    private boolean defCollSet = false;

    private boolean defFunSet = false;

    private boolean baseURISet = false;

    private boolean boundarySet = false;

    private boolean defOrderSet = false;

    private boolean constructionSet = false;

    private boolean orderingSet = false;

    private boolean copySet = false;

    private boolean isWebServiceDecl = false;

    private boolean isModuleDecl = false;

    private int cDepth = 0;

    // private String currentStep = ".";

    private boolean noBodyAllowed = false;

    private boolean errFlag = true;

    private boolean descOrSelf = false;

    boolean inPredicate = false;

    /**
     * Shall node IDs be generated?
     */
    // private boolean generateNodeIds = true;
    // private int nodeIdCount = 0;
    private Stack contextStack = new Stack();

    // private boolean needContextItem = false;

    private boolean inProlog = false;

    private boolean inConstructor = false;

    private boolean useFTScoring = false;

    XDMScope curNsScope;

    Vector lateResolvedFuncs;
    Vector addedFunctions;
    Vector variablesWithDependencies;

    Set declaredNamespaces;

    // for parallel execution, replicating a variable over more contexts might
    // be needed
    // if this is done, the resolution varName -> varHolder needs to be done at
    // each reset
    protected boolean resolveVarsOnReset = false;
    protected boolean parallelExecution = false;

    // private short level = 0;
    // private Identifier last_id = null;

    private class VariableIteratorMapping {
	public VariableIteratorMapping(QName qname, XDMIterator expr) {
	    qn = qname;
	    iter = expr;
	}

	QName qn;
	XDMIterator iter;
    }

    /**
     * Used for static checking of continue and break<br/> They can only appear
     * in loop => during parsing of a break/coninue must be known if they are in
     * a loop or not. This is done over this variable.
     */
    private int openLoops = 0;

    /**
     * Used for static checking of an early return (early returns must be in a
     * function) This variable declares if the parser is in a function.
     */
    // private boolean openFunction = false;
    private char[] separators = new char[] { ' ', '\n', '\r', '\t', '(', ')',
	    '[', ']', '{', '}', '=', ';', ',', '/', '$' };

    private boolean revalidateSet;

    protected int fflworIndex = 0;

    protected Context getCurrentContext() {
	return (Context) contextStack.peek();
    }

    private XQStaticContext createNewContextScope() {
	XQStaticContext ctx = new Context((Context) contextStack.peek());
	contextStack.push(ctx);
	return ctx;
    }

    private XQStaticContext extendCurrentContextScope() {
	Context ctx = (Context) contextStack.pop();
	ctx = new Context(ctx);
	contextStack.push(ctx);
	return ctx;
    }

    private XQStaticContext removeContextScope() {
	return (XQStaticContext) contextStack.pop();
    }

    public Parser(boolean noBodyAllowed) {
	this.noBodyAllowed = noBodyAllowed;
    }

    public Parser() {
	query = null;
    }

    public Iterator parse(XQStaticContext context, String query,
	    CompilerOptions co) throws MXQueryException {
	this.query = query;
	this.co = co;
	if (query == null || query.equals("")) {
	    throw new StaticException(
		    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
		    "Error while parsing: query is null", getCurrentLoc());
	} else {
	    contextStack.push(context);
	    // this.declContext = new XQueryExpression(this.context);
	    index = 0;
	}

	Iterator result = Module();
	if (result != null) {
	    if (index < query.length()) {
		throw new StaticException(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: could not parse query completely",
			getCurrentLoc());
	    }
	    return result;
	} else {
	    throw new StaticException(
		    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
		    "Error while parsing: query body is empty", getCurrentLoc());
	}
    }

    protected QueryLocation getCurrentLoc() {
	return new QueryLocation(index, index);
    }

    static {
	hRestrictedFunctionNames = new Hashtable();

	hRestrictedFunctionNames.put("attribute", "");
	hRestrictedFunctionNames.put("comment", "");
	hRestrictedFunctionNames.put("document-node", "");
	hRestrictedFunctionNames.put("element", "");
	hRestrictedFunctionNames.put("empty-sequence", "");
	hRestrictedFunctionNames.put("if", "");
	hRestrictedFunctionNames.put("item", "");
	hRestrictedFunctionNames.put("node", "");
	hRestrictedFunctionNames.put("processing-instruction", "");
	hRestrictedFunctionNames.put("schema-attribute", "");
	hRestrictedFunctionNames.put("schema-element", "");
	hRestrictedFunctionNames.put("text", "");
	hRestrictedFunctionNames.put("typeswitch", "");
	// XQueryP
	hRestrictedFunctionNames.put("catch", "");

	// add other names if there are
	hRestrictedTypeNames = new Hashtable();
	hRestrictedTypeNames.put("in", "");

    }

    protected void generateStaticError(String code, String message)
	    throws StaticException {
	if (errFlag) {
	    throw new StaticException(code, message, getCurrentLoc());
	}
	return;
    }

    private Iterator Module() throws MXQueryException {
	VersionDecl();
	Iterator module = LibraryModule();
	if (module == null && !noBodyAllowed) {
	    module = MainModule();
	}
	return module;
    }

    private Iterator LibraryModule() throws MXQueryException {
	if (!(this.isModuleDecl = ModuleDecl())
		&& !(this.isWebServiceDecl = ServiceDecl())) {
	    return null;
	}
	;
	Prolog();
	return new EmptySequenceIterator(getCurrentContext(), getCurrentLoc());
    }

    private Iterator MainModule() throws MXQueryException {
	Prolog();
	return QueryBody();
    }

    private boolean ModuleDecl() throws MXQueryException {
	if (parseKeyword("module")) {
	    String str = null;
	    if (parseKeyword("namespace")) {
		if ((str = NCName()) != null) {
		    if (parseString("=", true, false)) {
			XDMIterator it = null;
			if ((it = StringLiteral()) != null) {
			    Token tok = it.next();
			    Context ctx = getCurrentContext();
			    ctx.setModuleContext(true);
			    String moduleNS = tok.getText();
			    if (moduleNS.equals(""))
				throw new StaticException(
					ErrorCodes.E0088_STATIC_MODULE_EMPTY_NAMESPACE_LITERAL,
					"Empty Namespace not allowed for module definitions",
					getCurrentLoc());
			    ctx.setModuleURI(moduleNS);
			    ctx.addNamespace(new Namespace(str, tok.getText()));
			    if (parseString(";", true, false)) {
				return true;
			    } else {
				generateStaticError(
					ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
					"Error while parsing: ';' expected!");
			    }
			} else {
			    generateStaticError(
				    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				    "Error while parsing: 'URILiteral' expected!");
			}
		    } else {
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"Error while parsing: '=' expected!");
		    }
		} else {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'NCName' expected!");
		}
	    } else {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: 'namespace' expected!");
	    }
	}
	return false;
    }

    private boolean ServiceDecl() throws MXQueryException {
	/*
	 * Web Service Support If the user declares this, the engine starts
	 * automatically a Web Service
	 */
	if (parseKeyword("service")) {
	    int port = 80;
	    if (!parseKeyword("namespace")) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"A service declaration must contain a namespace selection!");
	    }
	    String prefix = NCName();
	    if (prefix == null) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"prefix in namespace declaration of serice import not declared!");
	    }
	    if (!parseString("=", true, false)) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"'=' missing in namespace declaration of a service import");
	    }
	    String uri = StringLiteralAsString();
	    if (uri == null) {
		generateStaticError(ErrorCodes.E0046_STATIC_EMPTY_URI,
			"url in namespace declaration of serice import not declared!");
	    }
	    Namespace namespace = new Namespace(prefix, uri);

	    if (parseString("port:", true, false)) {
		String strPort = Digits();
		if (strPort != null) {
		    try {
			port = Integer.parseInt(strPort);
		    } catch (NumberFormatException e) {
			// do nothing, the following Exception generation will
			// be reached
		    }
		}
	    }
	    if (!parseString(";", true, false)) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Semicolon at the end of a service module declaration is missing");
	    }
	    Context ctx = getCurrentContext();
	    ctx.setWebService(port, namespace);
	    ctx.addNamespace(namespace);
	    return true;
	}
	return false;
    }

    private void Prolog() throws MXQueryException {
	inProlog = true;

	skipComments();

	VersionDecl();

	skipComments();

	declaredNamespaces = new Set();

	while (NS_Setter_Import()) {
	    skipComments();
	}

	curNsScope = new XDMScope();
	if (getCurrentContext().getDefaultElementNamespace() != null) {
	    curNsScope.addNamespace("", getCurrentContext()
		    .getDefaultElementNamespace());
	}

	lateResolvedFuncs = new Vector();
	addedFunctions = new Vector();
	variablesWithDependencies = new Vector();
	while (Var_Function_Option()) {
	    skipComments();
	}
	inProlog = false;

	// Do all the check that can only be done when the entire prolog is
	// done, e.g.
	// resolving for and backward references between variables and functions

	// Check if forward definitions of functions are correct
	if (lateResolvedFuncs.size() > 0) {
	    // check if all functions that could not be resolved before have
	    // been defined
	    // FIXME: perform check for circular dependencies
	    for (int i = 0; i < lateResolvedFuncs.size(); i++) {
		FunctionSigHolder funcToCheck = (FunctionSigHolder) lateResolvedFuncs
			.elementAt(i);
		if (getCurrentContext().getFunction(funcToCheck.funcName,
			funcToCheck.arity) == null)
		    throw new StaticException(
			    ErrorCodes.E0017_STATIC_DOESNT_MATCH_FUNCTION_SIGNATURE,
			    "Function named " + funcToCheck.funcName.toString()
				    + " not available", getCurrentLoc());
	    }
	}
	// Check if the expression type is consistent
	// This is only possible once all "forward/late" functions are available
	for (int i = 0; i < addedFunctions.size(); i++) {
	    XDMIterator funcToCheck = (XDMIterator) addedFunctions.elementAt(i);
	    funcToCheck.getExpressionCategoryType(co.isScripting());
	}

	// Check variable dependencies
	for (int i = 0; i < variablesWithDependencies.size(); i++) {
	    VariableIteratorMapping vi = (VariableIteratorMapping) variablesWithDependencies
		    .elementAt(i);
	    checkVariableDependencies(vi.qn, vi.iter, new Set());
	}

    }

    private void checkVariableDependencies(QName qn, XDMIterator iter,
	    Set seenFunctions) throws MXQueryException {
	if (iter instanceof VariableIterator) {
	    VariableIterator var = (VariableIterator) iter;
	    if (var.getVarQName().equals(qn))
		throw new StaticException(
			ErrorCodes.E0054_STATIC_VARIABLE_DEPENDS_ON_ITSELF,
			"Variable " + qn.toString() + " depends on itself",
			getCurrentLoc());
	}

	if (iter instanceof UserdefFuncCall) {
	    UserdefFuncCall func = (UserdefFuncCall) iter;
	    QName fName = func.getFunctionName();
	    seenFunctions.add(fName);
	}

	if (iter instanceof UserdefFuncCallLateBinding) {
	    UserdefFuncCallLateBinding func = (UserdefFuncCallLateBinding) iter;
	    QName fName = func.getFunctionName();
	    if (seenFunctions.contains(fName))
		return;
	    else {
		func.lookup();
		// seenFunctions.add(fName);
		checkVariableDependencies(qn, func.getResolvedFunc(),
			seenFunctions);
	    }
	} else {
	    XDMIterator[] subs = iter.getAllSubIters();
	    for (int i = 0; i < subs.length; i++) {
		XDMIterator it = subs[i];
		// Check variable iterators for equality with qn
		// Step into UDFfunctions recursively

		XDMIterator recCheck = it;

		if (it instanceof UserdefFuncCall) {
		    UserdefFuncCall func = (UserdefFuncCall) it;
		    QName fName = func.getFunctionName();
		    seenFunctions.add(fName);
		}

		if (it instanceof UserdefFuncCallLateBinding) {
		    UserdefFuncCallLateBinding func = (UserdefFuncCallLateBinding) it;
		    QName fName = func.getFunctionName();
		    if (seenFunctions.contains(fName))
			continue;
		    else {
			func.lookup();
			// seenFunctions.add(fName);
			recCheck = func.getResolvedFunc();
		    }
		}

		checkVariableDependencies(qn, recCheck, seenFunctions);

	    }
	}
    }

    private boolean NS_Setter_Import() throws MXQueryException {
	int tempIndex = index;
	Vector module_import = new Vector();

	Vector schema_import = new Vector();
	if (parseKeyword("declare")) {
	    if (parseKeyword("execution")) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Execution modes are no longer supported, use instead the scripting facility");
		if (!parseString(";", true, false)) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: ';' expected!");
		}
		return true;
	    }

	    if (parseKeyword("ft-option")) {
		FTOptionDecl();
		return true;
	    }

	    if (parseKeyword("default")) {
		int mode = 0;
		if (parseKeyword("element")) {
		    if (defElemSet) {
			generateStaticError(
				ErrorCodes.E0066_STATIC_PROLOG_MULTIPLE_DEFAULT_NAMESPACE_DECL,
				"Only one default element namespace declaration allowed!");
		    }
		    defElemSet = true;
		    mode = 1;
		} else if (parseKeyword("function")) {
		    if (defFunSet) {
			generateStaticError(
				ErrorCodes.E0066_STATIC_PROLOG_MULTIPLE_DEFAULT_NAMESPACE_DECL,
				"Only one default function namespace declaration allowed!");
		    }
		    defFunSet = true;
		    mode = 2;
		} else if (parseKeyword("collation")) { // Setter element
		    XDMIterator it = StringLiteral();
		    Token tok = it.next();
		    String uri = tok.getText();
		    if (uri == null) {
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"Error while parsing: 'StringLiteral' expected!");
		    }
		    if (!parseString(";", true, false)) {
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"Error while parsing: ';' expected!");
		    }

		    if (defCollSet)
			generateStaticError(
				ErrorCodes.E0038_STATIC_MULTIPLE_DEFAULT_COLLATION_DECL_OR_STATICALLY_UNKNOWN,
				"Only one default collation declaration allowed!");
		    defCollSet = true;
		    String baseUri = getCurrentContext().getBaseURI();
		    if (baseUri != null
			    && !TypeLexicalConstraints.isAbsoluteURI(uri))
			getCurrentContext().setDefaultCollation(baseUri + uri);
		    else
			getCurrentContext().setDefaultCollation(uri);
		    return true;
		} else if (parseKeyword("order")) { // Setter element
		    if (defOrderSet) {
			generateStaticError(
				ErrorCodes.E0069_STATIC_PROLOG_MULTIPLE_EMPTY_ORDER_DECL,
				"Only one default element namespace declaration allowed!");
		    }
		    defOrderSet = true;

		    if (parseKeyword("empty")) {
			if (parseKeyword("least")) {
			    mode = 1;
			} else if (parseKeyword("greatest")) {
			    mode = 2;
			} else {
			    generateStaticError(
				    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				    "Error while parsing: 'greatest' or 'least' expected!");
			}

			if (!parseString(";", true, false)) {
			    generateStaticError(
				    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				    "Error while parsing: ';' expected!");
			}

			if (mode == 1) {
			    getCurrentContext().setDefaultOrderEmptySequence(
				    XQStaticContext.ORDER_LEAST);
			} else if (mode == 2) {
			    getCurrentContext().setDefaultOrderEmptySequence(
				    XQStaticContext.ORDER_GREATEST);
			}

			return true;
		    } else {
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"Error while parsing: 'empty' expected!");
		    }
		} else {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'element' or 'function' expected!");
		}

		if (!parseKeyword("namespace")) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'namespace' expected!");
		}
		XDMIterator it = StringLiteral();
		if (it == null) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'StringLiteral' expected!");
		}

		Token tok = it.next();
		String uri = tok.getText();

		if (uri == null) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'StringLiteral' expected!");
		}

		if (!parseString(";", true, false)) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: ';' expected!");
		}

		if (mode == 1) {
		    if (!uri.equals("")) {
			getCurrentContext().setDefaultElementNamespace(uri);
		    }
		} else if (mode == 2) {
		    if (!uri.equals(""))
			getCurrentContext().setDefaultFunctionNamespace(uri);
		    else
			generateStaticError(
				ErrorCodes.E0060_STATIC_FUNCTION_NAME_IS_NOT_A_NAMESPACE,
				"Empty default function namespace URI not allowed");
		}

		return true;
	    } else if (parseKeyword("namespace")) {
		String name = NCName();
		if (name == null) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'NCName' expected!");
		}

		if (!parseString("=", true, false)) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: '=' expected!");
		}

		String uri = null;

		XDMIterator it = StringLiteral();
		if (it != null) {
		    Token tok = it.next();
		    uri = tok.getText();
		}

		if (uri == null) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'StringLiteral' expected!");
		}

		if (!parseString(";", true, false)) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: ';' expected!");
		}
		if (name.equals("xml") || name.equals("xmlns")
			|| uri.equals(XQStaticContext.URI_XML)) {
		    generateStaticError(
			    ErrorCodes.E0070_STATIC_INVALID_NAMESPACE_DECL_WITH_XML_OR_XMLNS,
			    "Prefix xml cannot be redeclared");
		}

		XQStaticContext ctx = getCurrentContext();
		Namespace ns = ctx.getNamespace(name);
		if (ns != null
			&& !(ns.getURI().equals(XQStaticContext.URI_LOCAL)
				|| ns.getURI().equals(XQStaticContext.URI_FN)
				|| ns.getURI().equals(XQStaticContext.URI_XS)
				|| ns.getURI().equals(XQStaticContext.URI_XSI) || ns
				.getURI().equals(XQStaticContext.URI_XDT))) {
		    generateStaticError(
			    ErrorCodes.E0033_STATIC_MODULE_MULTIPLE_BINDINGS_FOR_SAME_PREFIX,
			    "Multiple declarations of Namespace " + name);
		}

		if (declaredNamespaces.contains(name))
		    generateStaticError(
			    ErrorCodes.E0033_STATIC_MODULE_MULTIPLE_BINDINGS_FOR_SAME_PREFIX,
			    "Multiple declarations of Namespace " + name);
		ctx.addNamespace(new Namespace(name, uri));
		declaredNamespaces.add(name);
		return true;

	    } else if (parseKeyword("boundary-space")) {
		if (boundarySet) {
		    generateStaticError(
			    ErrorCodes.E0068_STATIC_PROLOG_MULTIPLE_BOUNDARY_SPACE_DECL,
			    "Only one boundary space declaration allowed!");
		}
		boundarySet = true;

		if (parseKeyword("preserve")) {
		    getCurrentContext().setBoundarySpaceHandling(true);
		} else if (parseKeyword("strip")) {
		    getCurrentContext().setBoundarySpaceHandling(false);
		} else {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'preserve' or 'strip' expected!");
		}

		if (!parseString(";", true, false)) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: ';' expected!");
		}
		return true;
	    } else if (parseKeyword("base-uri")) {
		if (baseURISet) {
		    generateStaticError(
			    ErrorCodes.E0032_STATIC_MORE_THAN_ONE_BASE_URI_DECL,
			    "Only one base URI declaration allowed!");
		}
		baseURISet = true;
		String uri = StringLiteral().next().getText();

		if (uri == null) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'StringLiteral' expected!");
		}

		if (!parseString(";", true, false)) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: ';' expected!");
		}

		getCurrentContext().setBaseURI(Utils.normalizeString(uri));

		return true;
	    } else if (parseKeyword("construction")) {
		if (constructionSet) {
		    generateStaticError(
			    ErrorCodes.E0067_STATIC_PROLOG_MULTIPLE_CONSTRUCTION_DECL,
			    "Only one construction mode declaration allowed!");
		}
		constructionSet = true;

		if (parseKeyword("preserve")) {
		    if (co.isSchemaAwareness())
			getCurrentContext().setConstructionMode(
				XQStaticContext.PRESERVE);
		    else
			generateStaticError(ErrorCodes.A0002_EC_NOT_SUPPORTED,
				"Construction mode 'preserve' currently not supported");
		} else if (parseKeyword("strip")) {
		    getCurrentContext().setConstructionMode(
			    XQStaticContext.STRIP);
		} else {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'preserve' or 'strip' expected!");
		}

		if (!parseString(";", true, false)) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: ';' expected!");
		}

		return true;
	    } else if (parseKeyword("ordering")) {
		if (orderingSet) {
		    generateStaticError(
			    ErrorCodes.E0065_STATIC_PROLOG_CONTAINS_MULTIPLE_ORDERING_MODE_DECL,
			    "Only one ordering mode declaration allowed!");
		}
		orderingSet = true;

		if (parseKeyword("ordered")) {
		    getCurrentContext()
			    .setOrderingMode(XQStaticContext.ORDERED);
		} else if (parseKeyword("unordered")) {
		    getCurrentContext().setOrderingMode(
			    XQStaticContext.UNORDERED);
		} else {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'ordered' or 'unordered' expected!");
		}

		if (!parseString(";", true, false)) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: ';' expected!");
		}

		return true;
	    } else if (parseKeyword("revalidation")) {
		if (revalidateSet) {
		    generateStaticError(
			    ErrorCodes.U0003_UPDATE_STATIC_PROLOG_MULTIPLE_REVALIDATE_DECL,
			    "Only one 'revalidate' declaration allowed!");
		}
		revalidateSet = true;

		if (parseKeyword("strict")) {
		    getCurrentContext().setRevalidationMode(
			    XQStaticContext.REVALIDATION_STRICT);
		    generateStaticError(
			    ErrorCodes.U0026_UPDATE_STATIC_UNSUPPORTED_REVALIDATION_MODE,
			    "'strict' revalidation mode not supported");
		} else if (parseKeyword("lax")) {
		    getCurrentContext().setRevalidationMode(
			    XQStaticContext.REVALIDATION_LAX);
		    generateStaticError(
			    ErrorCodes.U0026_UPDATE_STATIC_UNSUPPORTED_REVALIDATION_MODE,
			    "'lax' revalidation mode not supported");
		} else if (parseKeyword("skip")) {
		    getCurrentContext().setRevalidationMode(
			    XQStaticContext.REVALIDATION_SKIP);
		    generateStaticError(
			    ErrorCodes.U0026_UPDATE_STATIC_UNSUPPORTED_REVALIDATION_MODE,
			    "'skip' revalidation mode not supported");
		} else
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'strict','lax' or 'skip' expected!");
		if (!parseString(";", true, false)) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: ',' expected!");
		}
		return true;

	    } else if (parseKeyword("copy-namespaces")) {
		if (copySet) {
		    generateStaticError(
			    ErrorCodes.E0055_STATIC_PROLOG_MULTIPLE_COPY_NAMESPACES_DECL,
			    "Only one copy namespace declaration allowed!");
		}
		copySet = true;
		int modeA = 0;
		int modeB = 0;
		if (parseKeyword("preserve")) {
		    modeA = 1;
		} else if (parseKeyword("no-preserve")) {
		    modeA = 2;
		} else {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'preserve' or 'no-preserve' expected!");
		}

		if (!parseString(",", true, false)) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: ',' expected!");
		}

		if (parseKeyword("inherit")) {
		    modeB = 1;
		} else if (parseKeyword("no-inherit")) {
		    modeB = 2;
		} else {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'preserve' or 'no-preserve' expected!");
		}

		if (!parseString(";", true, false)) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: ';' expected!");
		}
		getCurrentContext().getRootContext().setCopyNamespacesMode(
			modeA == 1, modeB == 1);
		return true;
	    }
	} else if (parseKeyword("import")) {
	    Vector optionNames = new Vector();
	    Vector optionValues = new Vector();
	    String ns_uri = null;
	    String temp = null;
	    int i = 0;
	    if (parseKeyword("module")) {
		if (parseKeyword("namespace")) {

		    if ((temp = NCName()) != null) {

			if (temp.equals("xml") || temp.equals("xmlns")) {
			    generateStaticError(
				    ErrorCodes.E0070_STATIC_INVALID_NAMESPACE_DECL_WITH_XML_OR_XMLNS,
				    "Error while parsing:  The namespace prefix specified in a module import must not be xml or xmlns");
			}

			if (parseString("=", true, false)) {

			} else {
			    generateStaticError(
				    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				    "Error while parsing: '=' expected!");
			}
		    } else {
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"Error while parsing: 'NCName' expected!");
		    }
		}
		if ((ns_uri = StringLiteralAsString()) != null) {
		    if (ns_uri.equals("")) {
			generateStaticError(
				ErrorCodes.E0088_STATIC_MODULE_EMPTY_NAMESPACE_LITERAL,
				"Error while parsing: The first URILiteral in a module import must be of nonzero length");
		    }
		    Vector nsUris = getCurrentContext().getAllNsURI();
		    for (int j = 0; j < nsUris.size(); j++) {
			if (nsUris.elementAt(j).equals(ns_uri))
			    throw new StaticException(
				    ErrorCodes.E0047_STATIC_MODULES_SPECIFY_SAME_NAMESPACE,
				    "Module with namespace " + ns_uri
					    + " already imported",
				    getCurrentLoc());
		    }
		    getCurrentContext().addNamespace(temp, ns_uri);
		    String str = null;
		    if (parseKeyword("at")) {
			if ((str = StringLiteralAsString()) != null) {
			    module_import.insertElementAt(str, i++);
			    while (parseString(",", true, false)) {
				if ((str = StringLiteralAsString()) == null) {
				    generateStaticError(
					    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
					    "Error while parsing: 'URILiteral' expected!");
				} else
				    module_import.insertElementAt(str, i++);
			    }
			} else {
			    generateStaticError(
				    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				    "Error while parsing: 'URILiteral' expected!");
			}
		    } else {
			module_import = getCurrentContext().getRootContext()
				.getModuleLocation(ns_uri);
			if (module_import != null)
			    i = module_import.size();
			else {
			    if (extensions.getModuleFunctions(ns_uri) != null) {
				module_import = new Vector();
				module_import
					.addElement("--builtin-extension--");
				i = module_import.size();
			    }

			}
		    }
		} else {
		    generateStaticError(
			    ErrorCodes.E0088_STATIC_MODULE_EMPTY_NAMESPACE_LITERAL,
			    "The first URILiteral in a module import must be of nonzero length!");
		}
		if (parseString("options", true, false)) {
		    skipComments();
		    do {
			QName optionName = QName();
			if (optionName == null) {
			    generateStaticError(
				    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				    "Error while parsing the options: 'QName' expected!");
			}
			XDMIterator it = StringLiteral();
			Token tok = it.next();
			String optionValue = tok.getText();
			if (optionValue == null) {
			    generateStaticError(
				    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				    "Error while parsing: 'StringLiteral' expected!");
			}
			optionNames.addElement(optionName.toString());
			optionValues.addElement(optionValue);
		    } while (parseString(",", true, false));
		    // int vectorSize = optionNames.size();
		}

		if (parseString(";", true, false)) {
		    String fileContent = "";
		    if (i > 0
			    && module_import.elementAt(0).equals(
			    "--builtin-extension--")) {
			Vector funcs = extensions.getModuleFunctions(ns_uri);
			for (int k = 0; k < funcs.size(); k++) {
			    Function fun = (Function) funcs.elementAt(k);
			    getCurrentContext()
			    .addFunction(
				    fun.getAsExternalFunction(temp),
				    true, true);
			}
			return true;
		    }
		    for (int j = 0; j < i; j++) {
			if (getCurrentContext().getRootContext().location == null)
			    getCurrentContext().getRootContext().location = new Location();
			    if (!getCurrentContext().getRootContext().location
				    .addString((String) module_import
					    .elementAt(j))) {
				getCurrentContext().getRootContext().location = null;
				generateStaticError(
					ErrorCodes.E0073_STATIC_MODULE_IMPORTS_CYCLE,
				"Error while parsing: Import modules creating cycle!");
			    }

			try {
			    String uri = URIUtils
			    .resolveURI(
				    getCurrentContext()
				    .getBaseURI(),
				    getCurrentContext()
				    .getRootContext().location
				    .getString(),
				    getCurrentLoc());
			    fileContent = FileReader.getFileContent(uri);
			} catch (IOException e) {
			    throw new StaticException(
				    ErrorCodes.E0059_STATIC_UNABLE_TO_PROCESS_SCHEMA_OR_MODULE_IMPORT,
				    e.getMessage(), getCurrentLoc());
			} catch (DynamicException de) {
			    throw new StaticException(
				    ErrorCodes.E0059_STATIC_UNABLE_TO_PROCESS_SCHEMA_OR_MODULE_IMPORT,
				    de.getMessage(), getCurrentLoc());
			}

			if (fileContent
				.indexOf("http://schemas.xmlsoap.org/wsdl") != -1) {
			    // Check if the fileContent is a valid WSDL or not
			    // At the moment checking for existence of 'wsdl
			    // url'
			    // TODO: validate it against wsdl.xsd!
			    WSDLHandler wh = new WSDLHandler(fileContent);
			    Namespace namespace = new Namespace(temp, ns_uri);
			    String serviceName = "";
			    int optionsIndex = optionNames
			    .indexOf("fn:servicename");
			    if (optionsIndex != -1) {
				serviceName = (String) optionValues
				.elementAt(optionsIndex);
			    }
			    String endpointName = "";
			    optionsIndex = optionNames.indexOf("fn:endpoint");
			    if (optionsIndex != -1) {
				endpointName = (String) optionValues
				.elementAt(optionsIndex);
			    }
			    wh.run(getCurrentContext(), namespace, serviceName,
				    endpointName, getCurrentLoc(), co);

			}
			else {
			    XQCompiler inComp = new CompilerImpl();
			    Context modCtx = new Context();
			    modCtx.getModuleLocations(getCurrentContext()
				    .getRootContext());
			    modCtx.location = getCurrentContext()
			    .getRootContext().location;
			    getCurrentContext().addModuleContext(modCtx);

			    inComp.compile(modCtx, fileContent, co);
			    if (modCtx.getModuleURI() == null) {
				getCurrentContext().getRootContext().location = null;
				generateStaticError(
					ErrorCodes.E0059_STATIC_UNABLE_TO_PROCESS_SCHEMA_OR_MODULE_IMPORT,
				"No Module decclaration at the specified file!");
			    }
			    if (!modCtx.getModuleURI().equals(ns_uri)) {
				getCurrentContext().getRootContext().location = null;
				generateStaticError(
					ErrorCodes.E0059_STATIC_UNABLE_TO_PROCESS_SCHEMA_OR_MODULE_IMPORT,
				"Import Module namespace URI doesnot match module declaration URI!");
			    }
			    Hashtable ht = modCtx.getFunctions(modCtx
				    .getModuleURI());
			    Enumeration e = ht.keys();
			    while (e.hasMoreElements()) {
				FunctionSignature fs = (FunctionSignature) e
				.nextElement();

				String tempURI = fs.getName()
				.getNamespaceURI();
				if (!tempURI.equals(ns_uri)) {
				    throw new StaticException(
					    ErrorCodes.E0048_STATIC_FUNCTION_NOT_IN_LIBRARY_NAMESPACE,
					    "All variables and functions in a module need to be in the target namespace",
					    getCurrentLoc());
				}

				Function func = (Function) ht.get(fs);

				getCurrentContext().addFunction(
					func.getAsExternalFunction(temp),
					true, true);
			    }
			    ht = modCtx.getAllVariables();
			    e = ht.keys();
			    while (e.hasMoreElements()) {
				QName qn = (QName) e.nextElement();
				// skip context item - maybe move to
				// getAllVariables
				if (qn.toString().equals(".item:.item"))
				    continue;

				VariableHolder vh = (VariableHolder) ht
				.get(qn);
				if (!vh.isExternal()) {
				    // all variables need to be in the
				    // target namespace of the module
				    if (!qn.getNamespaceURI()
					    .equals(ns_uri)) {
					throw new StaticException(
						ErrorCodes.E0048_STATIC_FUNCTION_NOT_IN_LIBRARY_NAMESPACE,
						"All variables and functions in a module need to be in the target namespace",
						getCurrentLoc());
				    }
				    // getCurrentContext().registerExternalVariable(qn,
				    // vh.getIter(), false);
				    getCurrentContext().registerVariable(
					    qn, true, false, null, false,
					    false);
				    getCurrentContext().setVariableValue(
					    qn, vh.getIter(), true, false);
				}
			    }
			    getCurrentContext().getRootContext().location
			    .popLast();
			    if (getCurrentContext().getRootContext().location
				    .getNestingLevel() == 0)
				getCurrentContext().getRootContext().location = null;
			}
		    }
		    return true;
		} else {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
		    "Error while parsing: ';' expected!");
		}
	    } else if (parseKeyword("schema")) {
		String prefix = null;
		// getCurrentContext().setValidationMode(true);
		boolean defaultElementNamespace = false;
		if (parseKeyword("namespace")) {
		    String str;
		    if ((str = NCName()) != null) {
			if (!parseString("=", true, false)) {
			    generateStaticError(
				    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				    "Error while parsing: '=' expected!");
			}
		    } else {
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"Error while parsing: 'NCName' expected!");
		    }
		    if (str.equals("xml") || str.equals("xmlns")) {
			generateStaticError(
				ErrorCodes.E0070_STATIC_INVALID_NAMESPACE_DECL_WITH_XML_OR_XMLNS,
				"Error while parsing:  The namespace prefix specified in a schema import must not be xml or xmlns");
		    } else
			prefix = str;
		} else if (parseKeyword("default")) {
		    if (parseKeyword("element")) {
			if (!parseKeyword("namespace")) {
			    generateStaticError(
				    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				    "Error while parsing: 'namespace' expected!");
			}
		    } else {
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"Error while parsing: 'element' expected!");
		    }
		    defaultElementNamespace = true;
		}
		if ((ns_uri = StringLiteralAsString()) != null) {
		    if (ns_uri.equals("")) {
			generateStaticError(
				ErrorCodes.E0057_STATIC_SCHEMA_IMPORT_NS_PREFIX_WITHOUT_TARGET_NAMESPACE,
				"Error while parsing: The first URILiteral in a schema import must be of nonzero length");
		    }
		    String str = null;
		    if (parseKeyword("at")) {
			if ((str = StringLiteralAsString()) != null) {
			    schema_import.insertElementAt(str, i++);
			    while (parseString(",", true, false)) {
				if ((str = StringLiteralAsString()) == null) {
				    generateStaticError(
					    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
					    "Error while parsing: 'URILiteral' expected!");
				} else
				    schema_import.insertElementAt(str, i++);
			    }
			} else {
			    generateStaticError(
				    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				    "Error while parsing: 'URILiteral' expected!");
			}
		    }
		} else {
		    generateStaticError(
			    ErrorCodes.E0088_STATIC_MODULE_EMPTY_NAMESPACE_LITERAL,
			    "The first URILiteral in a module import must be of nonzero length!");
		}
		if (parseString(";", true, false)) {
		    if (!co.isSchemaAwareness())
			generateStaticError(
				ErrorCodes.E0009_STATIC_SCHEMA_IMPORTS_NOT_SUPPORTED,
				"Schema Imports are not supported in this version");
		    if (defaultElementNamespace)
			getCurrentContext().setDefaultElementNamespace(ns_uri);
		    processSchemas(schema_import, ns_uri, prefix,
			    defaultElementNamespace);

		    return true;
		} else {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: ';' expected!");
		}
	    } else {
		index = tempIndex;
		return false;
	    }
	}

	index = tempIndex;
	return false;
    }

    protected void processSchemas(Vector schema_import, String ns_uri,
	    String prefix, boolean defaultElementNamespace)
	    throws StaticException, MXQueryException {
	generateStaticError(
		ErrorCodes.E0009_STATIC_SCHEMA_IMPORTS_NOT_SUPPORTED,
		"Schema Imports are not supported in this version");
    }

    private boolean Var_Function_Option() throws MXQueryException {
	Iterator seqTypeIt = null;

	int tempIndex = index;
	// boolean isCopy = false;
	if (parseKeyword("declare")) {
	    int declareIndex = index;
	    String varType;
	    if ((varType = parseStringGetResult("variable", true)) != null
		    || (varType = parseStringGetResult("constant", true)) != null) {
		if (!parseString("$", true, false)) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: '$' expected!");
		}

		skipComments();
		// skippingAllowed = false;
		QName qname = QName();
		// skippingAllowed = true;

		if (qname == null) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'QName' expected!");
		}

		TypeInfo typeInfo = new TypeInfo();
		if (parseKeyword("as")) {
		    typeInfo = SequenceType();
		    seqTypeIt = new SequenceTypeIterator(typeInfo, true, false,
			    getCurrentContext(), getCurrentLoc());
		}

		boolean external = false;
		Iterator expr = null;
		if (parseKeyword("external")) {
		    external = true;
		} else if (parseString(":=", true, false)) {
		    createNewContextScope();
		    expr = ExprSingle();
		    try {
			if (expr.getExpressionCategoryType(co.isScripting()) == XDMIterator.EXPR_CATEGORY_UPDATING
				|| expr.getExpressionCategoryType(co
					.isScripting()) == XDMIterator.EXPR_CATEGORY_SEQUENTIAL)
			    generateStaticError(
				    ErrorCodes.U0001_UPDATE_STATIC_UPDATING_EXPRESSION_NOT_ALLOWED_HERE,
				    "Variable declarations in prolog must not contain an updating or sequential expression");
		    } catch (NullPointerException e) {
			// TODO: Better solution for forward references
		    }
		    // check if variable depends on other variables or functions
		    // if yes, put into check list, check at end of
		    // variable/function prolog
		    if (expr instanceof VariableIterator
			    || expr instanceof UserdefFuncCall
			    || expr instanceof UserdefFuncCallLateBinding)
			variablesWithDependencies
				.addElement(new VariableIteratorMapping(qname,
					expr));
		    else {
			Vector subs = expr.getAllSubItersRecursive();
			for (int i = 0; i < subs.size(); i++) {
			    XDMIterator cur = (XDMIterator) subs.elementAt(i);
			    if (cur instanceof VariableIterator
				    || cur instanceof UserdefFuncCall
				    || cur instanceof UserdefFuncCallLateBinding) {
				variablesWithDependencies
					.addElement(new VariableIteratorMapping(
						qname, expr));
				break;
			    }
			}
		    }
		    removeContextScope();
		} else
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Variable declared in a prolog need to either have an initializing expression or marked external");

		if (!parseString(";", true, false)) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: ';' expected!");
		}
		boolean assignable = true;
		if (varType.equals("constant"))
		    assignable = false;
		// this is a hack - an optimizer should do it better (ask Tim)
		if (external) {
		    getCurrentContext().registerVariable(qname, external,
			    false, seqTypeIt, assignable);
		} else {
		    getCurrentContext().registerVariable(qname, external,
			    false, null, assignable);
		}

		// In the web service case: exposing the global variables
		// through a getter function
		if (this.isWebServiceDecl || this.isModuleDecl) {
		    FunctionSignature getterSignature = null;
		    QName getterFunctionName = new QName(getCurrentContext()
			    .getNamespace(qname.getNamespacePrefix()).getURI(),
			    qname.getNamespacePrefix(), "get"
				    + qname.getLocalPart());
		    TypeInfo getterTypeInfo[] = new TypeInfo[0];
		    getterSignature = new FunctionSignature(getterFunctionName,
			    getterTypeInfo, FunctionSignature.SYSTEM_FUNCTION,
			    XDMIterator.EXPR_CATEGORY_SIMPLE, true);
		    VariableIterator vIter = new VariableIterator(
			    getCurrentContext(), qname, false, getCurrentLoc());
		    vIter.setReturnType(typeInfo.getType());
		    XDMIterator funcCallIter = new UserdefFuncCall(
			    getCurrentContext(), qname, null, null, vIter,
			    typeInfo, seqTypeIt,
			    XDMIterator.EXPR_CATEGORY_SIMPLE, getCurrentLoc());
		    Function fn = new Function(null, getterSignature,
			    funcCallIter);
		    getCurrentContext().addFunction(fn);
		    // TODO: do we need the following settings here?
		    // funcCallIter.getExpressionCategoryType();
		    // addedFunctions.addElement(funcCallIter);
		}

		// Variable var = new Variable(qname, null, getCurrentLoc());

		Window iter = null;
		if (!external) {
		    if (expr != null) {
			if (seqTypeIt != null) {
			    seqTypeIt.setSubIters(expr);
			    expr = seqTypeIt;
			}
			Source src = getCurrentContext().getStores()
				.createStore(qname.toString(), expr, false);
			iter = src.getIterator(getCurrentContext());
		    }

		    getCurrentContext().setVariableValue(qname, iter);
		}

		return true;
	    } else {
		index = declareIndex;
	    }
	    int expressionCategory = XDMIterator.EXPR_CATEGORY_SIMPLE;
	    if (parseKeyword("updating"))
		expressionCategory = XDMIterator.EXPR_CATEGORY_UPDATING;
	    else if (parseKeyword("sequential"))
		expressionCategory = XDMIterator.EXPR_CATEGORY_SEQUENTIAL;
	    else if (parseKeyword("simple")) {
	    }
	    ;
	    if (parseKeyword("function")) {

		boolean external = false;
		// skippingAllowed = false;
		QName qname = QName();
		qname = qname.resolveQNameNamespace(getCurrentContext());
		if (qname.getNamespacePrefix() == null)
		    qname.setNamespaceURI(getCurrentContext()
			    .getDefaultFunctionNamespace());
		// skippingAllowed = true;

		if (qname == null) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'QName' expected!");
		}
		if (!parseString("(", true, false)) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Function declaration without '('!");
		}

		Vector paramNames = new Vector();
		Vector paramTypes = new Vector();
		QName paramName;
		if (!parseString(")", true, false)) {
		    do {
			if (parseString("$", true, false)
				&& (paramName = QName()) != null) {
			    if (paramNames.contains(paramName)) {
				generateStaticError(
					ErrorCodes.E0039_STATIC_FUNCTION_DECL_SAME_PARAMETER_NAMES,
					"Duplicate parameter name'");
			    }
			    paramNames.addElement(paramName);
			} else {
			    generateStaticError(
				    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				    "ExprSingle expected '");
			}

			seqTypeIt = null;
			TypeInfo paramType = TypeDeclaration();
			paramTypes.addElement(paramType);

		    } while (parseString(",", true, false));
		    if (!parseString(")", true, false)) {
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"Function declaration without ')'!");
		    }
		}

		TypeInfo[] arrParamTypes = new TypeInfo[paramTypes.size()];
		for (int i = 0; i < paramTypes.size(); i++) {
		    arrParamTypes[i] = (TypeInfo) paramTypes.elementAt(i);
		}

		QName[] paramArr = new QName[paramNames.size()];
		paramNames.copyInto(paramArr);

		TypeInfo returnType = TypeDeclaration();

		if (!returnType.isUndefined()
			&& !co.isScripting()
			&& expressionCategory == XDMIterator.EXPR_CATEGORY_UPDATING)
		    throw new StaticException(
			    ErrorCodes.U0028_UPDATE_STATIC_FUNCTION_UPDATING_RETURNTYPE,
			    "'updating' function must not specify a return type",
			    getCurrentLoc());

		XDMIterator funcCallIter = null;
		if (parseKeyword("external")) {
		    external = true;
		    // TODO: Insert runtime PUL checking for external functions
		} else {
		    // openFunction = true;
		    createNewContextScope();

		    TypeInfo paramType = null;
		    for (int i = 0; i < paramNames.size(); i++) {
			seqTypeIt = null;
			if (!(arrParamTypes[i]).isUndefined()) {
			    paramType = arrParamTypes[i];
			    seqTypeIt = new SequenceTypeIterator(paramType,
				    true, true, getCurrentContext(),
				    getCurrentLoc()); // streaming
			}
			getCurrentContext().registerVariable(
				(QName) paramNames.elementAt(i), false,
				seqTypeIt, false);
		    }

		    Iterator body;
		    if (expressionCategory == XDMIterator.EXPR_CATEGORY_SEQUENTIAL) {
			body = Block();
		    } else {
			body = EnclosedExpr(false);
		    }

		    if (!returnType.isUndefined()) {
			seqTypeIt = new SequenceTypeIterator(returnType, true,
				true, getCurrentContext(), getCurrentLoc()); // streaming
		    } else {
			seqTypeIt = null;
		    }

		    funcCallIter = new UserdefFuncCall(getCurrentContext(),
			    qname, paramArr, arrParamTypes, body, returnType,
			    seqTypeIt, expressionCategory, getCurrentLoc());

		    removeContextScope();
		    // openFunction = false;
		}
		if (!parseString(";", true, false)) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: ';' expected!");
		}
		if (!external) {
		    FunctionSignature fs = new FunctionSignature(qname,
			    arrParamTypes,
			    FunctionSignature.USER_DEFINED_FUNCTION,
			    expressionCategory, false);

		    Function fn = new Function(null, fs, funcCallIter);
		    getCurrentContext().addFunction(fn);
		    // Check if the category can be resolved now
		    funcCallIter.getExpressionCategoryType(co.isScripting());
		    addedFunctions.addElement(funcCallIter);
		} else {
		    FunctionSignature fs = new FunctionSignature(qname,
			    arrParamTypes, FunctionSignature.EXTERNAL_FUNCTION,
			    expressionCategory, false);
		    Function fn = new Function(null, fs, funcCallIter);
		    getCurrentContext().addFunction(fn, true, true);
		}

		return true;
	    } else {
		index = declareIndex;
	    }

	    if (parseKeyword("option")) {
		QName qname = QName();

		if (qname == null) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: QName expected!");
		}

		if (qname.getNamespacePrefix() == null) {
		    generateStaticError(
			    ErrorCodes.E0081_STATIC_QUERY_UNKNOWN_NAMESPACE,
			    "Option Namespace not known");
		} else {
		    Namespace ns = getCurrentContext().getNamespace(
			    qname.getNamespacePrefix());
		    if (ns == null)
			generateStaticError(
				ErrorCodes.E0081_STATIC_QUERY_UNKNOWN_NAMESPACE,
				"Option Namespace not known");
		}

		XDMIterator iter = StringLiteral();

		if (iter == null) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: StringLiteral expected!");
		}

		if (!parseString(";", true, false)) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: ';' expected!");
		}
		if (qname.toString().toLowerCase().equals("fn:webservice")
			&& iter.next().getText().toLowerCase().equals("true")) {
		    Context ctx = getCurrentContext();
		    if (!ctx.isWebService()) {
			ctx.setWebService(true);
			ctx.setWebServiceNamespace(new Namespace(null, ctx
				.getModuleURI()));
			this.isWebServiceDecl = true;
		    }
		}
		if (qname.toString().toLowerCase().equals("fn:servicename")) {
		    Context ctx = getCurrentContext();
		    if (!ctx.isWebService()) {
			ctx.setWebService(true);
			ctx.setWebServiceNamespace(new Namespace(null, ctx
				.getModuleURI()));
			this.isWebServiceDecl = true;
		    }
		    ctx.setWebServiceName(iter.next().getText());
		}
		if (qname.toString().toLowerCase().equals("fn:endpoint")) {
		    Context ctx = getCurrentContext();
		    if (!ctx.isWebService()) {
			ctx.setWebService(true);
			ctx.setWebServiceNamespace(new Namespace(null, ctx
				.getModuleURI()));
			this.isWebServiceDecl = true;
		    }
		    ctx.setWebServiceEndpointName(iter.next().getText());
		}
		// TODO: handling the "location" option!

		return true;
		// generateStaticError(ErrorCodes.A0002_EC_NOT_SUPPORTED,
		// "Option declarations are not supported yet!" );
	    }
	}

	index = tempIndex;
	return false;
    }

    private void VersionDecl() throws MXQueryException {
	if (parseKeyword("xquery") && parseKeyword("version")) {
	    XDMIterator version = StringLiteral();
	    if (version != null) {
		if (!version.next().getText().equals("1.0"))
		    throw new StaticException(
			    ErrorCodes.E0031_STATIC_VERSION_NOT_SUPPORTED,
			    "Only XQuery version 1.0 supported",
			    getCurrentLoc());
		if (parseKeyword("encoding")) {
		    XDMIterator encoding = StringLiteral();
		    if (encoding == null) {
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"Error while parsing: 'StringLiteral' expected");
		    } else {
			String enc = encoding.next().getText();
			if (!isEncName(enc)) {
			    throw new StaticException(
				    ErrorCodes.E0087_STATIC_WRONG_ENCODING,
				    "incorrect encoding specified",
				    getCurrentLoc());
			}
		    }
		}
	    } else {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: 'StringLiteral' expected");
	    }

	    if (!parseString(";", true, false)) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: ';' expected");
	    }
	}
    }

    private Iterator EnclosedExpr(boolean treatWS) throws MXQueryException {
	Iterator expr;
	boolean preserveWS = getCurrentContext().getsetBoundarySpaceHandling();
	if (parseString("{", !(treatWS && preserveWS), false)) {
	    boolean comments = commentProhibited;
	    commentProhibited = false;
	    if ((expr = Expr()) != null) {
		skipComments();
		if (parseString("}", true, false)) {
		    return expr;
		} else {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: '}' expected");
		}
	    } else {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: Expr expected");
	    }
	    commentProhibited = comments;
	}
	return null;
    }

    private Iterator QueryBody() throws MXQueryException {
	return Expr();
    }

    private Iterator Expr() throws MXQueryException {
	// FIXME: Factor in ApplyExpr by parsing ConcatExpr explicitly, then
	// checking for ;
	// Depending on the result, go Apply or Concat
	// Avoids parsing twice
	Iterator expr = ApplyExpr();
	if (expr != null)
	    return expr;
	expr = ConcatExpr();
	if (expr != null)
	    return expr;
	generateStaticError(
		ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
		"Could not parse an expression");
	return null;
    }

    private Iterator ConcatExpr() throws MXQueryException {
	Vector its = new Vector();

	Iterator single = ExprSingle();
	if (single == null) {
	    return null;
	}

	while (parseString(",", true, false)) {
	    XDMIterator temp = null;

	    if ((temp = ExprSingle()) == null) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: ExprSingle expected!");
	    } else {
		if (its.size() == 0) {
		    its.addElement(single);
		}
		its.addElement(temp);
	    }
	}

	if (its.size() > 0) {
	    Iterator[] i = new Iterator[its.size()];
	    its.copyInto(i);
	    return new SequenceIterator(getCurrentContext(), i, getCurrentLoc());
	} else {
	    return single;
	}
    }

    private boolean inComment = false;
    private boolean commentProhibited = false;
    private boolean pragma = false;
    private int innerComments = 0;
    private boolean inValidateExpr;

    private void skipComments() throws MXQueryException {
	inComment = true;
	while (parseString("(:", true, false)) {
	    if (commentProhibited)
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Invalid comment - comments not allowed here");
	    while (true) {
		if (parseString("(:", true, false)) {
		    innerComments++;
		} else if (parseString(":)", true, false)) {
		    if (innerComments == 0) {
			break;
		    }

		    innerComments--;
		} else {
		    if (index >= query.length() - 1) {
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"Invalid comment - maybe you did not close a comment?");
		    }
		    index++;
		}
	    }
	}
	inComment = false;
    }

    private Iterator ExprSingle() throws MXQueryException {
	// skip comments:
	skipComments();

	Iterator temp = null;
	if ((temp = FFLWORExpr()) != null) {
	    return temp;
	} else if ((temp = QuantifiedExpr()) != null) {
	    return temp;
	} else if ((temp = TypeswitchExpr()) != null) {
	    return temp;
	} else if ((temp = IfExpr()) != null) {
	    return temp;
	} else if ((temp = CatchOldUpdateExpr()) != null) {
	    return temp;
	} else if ((temp = InsertExpr()) != null) {
	    return temp;
	} else if ((temp = DeleteExpr()) != null) {
	    return temp;
	} else if ((temp = ReplaceExpr()) != null) {
	    return temp;
	} else if ((temp = RenameExpr()) != null) {
	    return temp;
	} else if ((temp = TransformExpr()) != null) {
	    return temp;
	} else if ((temp = AssignExpr()) != null) {
	    return temp;
	} else if ((temp = BlockExpr()) != null) {
	    return temp;
	} else if ((temp = ExitExpr()) != null) {
	    return temp;
	} else if ((temp = WhileExpr()) != null) {
	    return temp;
	} else if ((temp = WhileExpr()) != null) {
	    return temp;
	    // } else if ((temp = ContinueExpr()) != null) {
	    // return temp;
	    // } else if ((temp = BreakExpr()) != null) {
	    // return temp;
	} else if ((temp = TryCatchExpr()) != null) {
	    return temp;
	} else if ((temp = OrExpr()) != null) {
	    return temp;
	}
	return null;
    }

    /**
     * Helper method to give better error messages to people using the old
     * update syntax
     * 
     * @return
     */
    private Iterator CatchOldUpdateExpr() throws MXQueryException {
	int oldIndex = index;
	if (parseKeyword("do")) {
	    if (parseKeyword("insert") || parseKeyword("delete")
		    || parseKeyword("replace") || parseKeyword("rename")
		    || parseKeyword("transform"))
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Updating expressions with 'do' have been deprecated");
	}
	index = oldIndex;
	return null;
    }

    /**
     * This expression replace FLWOR
     * 
     * @return
     * @throws MXQueryException
     */
    private Iterator FFLWORExpr() throws MXQueryException {
	Vector iterators = new Vector();
	XDMIterator where = null;
	OrderByIterator orderBy = null;
	XDMIterator ret = null;
	Vector v = null;

	XDMIterator temp = null;
	boolean goOn = true;

	createNewContextScope();

	Context outerFFLWORScope = getCurrentContext();

	boolean containsForseq = false;

	while (goOn) {
	    if ((temp = forWindowClause()) != null) {
		iterators.addElement(temp);
		containsForseq = true;
	    }
	    if ((temp = forseqClause()) != null) {
		iterators.addElement(temp);
		containsForseq = true;
	    }
	    if ((v = forClause()) != null) {
		for (int i = 0; i < v.size(); i++)
		    iterators.addElement(v.elementAt(i));
	    } else if ((v = letClause()) != null) {
		for (int i = 0; i < v.size(); i++)
		    iterators.addElement(v.elementAt(i));
	    } else {
		goOn = false;
	    }
	}

	if (iterators.size() == 0) {
	    removeContextScope();
	    return null;
	}

	fflworIndex++;

	where = whereClause();

	GroupHelper gh = GroupByClause();

	orderBy = OrderByClause();

	if (parseKeyword("return")) {
	    ret = ExprSingle();

	    if (ret == null) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: 'ExprSingle' expected!");
	    }

	    XDMIterator[] iters = new Iterator[iterators.size()];
	    // iterators.toArray(iters);
	    for (int i = 0; i < iterators.size(); i++) {
		iters[i] = (Iterator) iterators.elementAt(i);
	    }

	    fflworIndex--;
	    if (where != null) {
		where = BooleanIterator.createEBVIterator(where,
			iters[iters.length - 1].getContext());
	    }
	    Iterator flwor = generateFLWOR(where, orderBy, ret,
		    outerFFLWORScope, containsForseq, gh, iters);
	    removeContextScope();
	    return flwor;
	} else {
	    generateStaticError(
		    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
		    "Error while parsing FFLWOR Expr: 'return' expected!");
	}
	removeContextScope();
	return null;
    }

    protected Iterator generateFLWOR(XDMIterator where,
	    OrderByIterator orderBy, XDMIterator ret, Context outerFFLWORScope,
	    boolean containsForseq, GroupHelper gh, XDMIterator[] iters)
	    throws MXQueryException {
	Iterator flwor;
	flwor = new FFLWORIterator(outerFFLWORScope, iters, where, orderBy,
		ret, getCurrentLoc());
	if (gh != null) {
	    throw new StaticException(ErrorCodes.A0002_EC_NOT_SUPPORTED,
		    "Group By not supported on this version", getCurrentLoc());
	}
	return flwor;
    }

    /*
     * XQuery 1.1 Window Clause for (slidingWindowClause|TumblingWindowClause)
     * tumbling ::= "tumbling" "window" $ Varname TypeDecl? "in" ExprSingle
     * WindowStartCondition WindowEndCondition WindowEnd ::= "only"? end
     * WindowVars when ExprSingle only end == force
     */

    private XDMIterator forWindowClause() throws MXQueryException {
	int origIndex = index;
	if (co.isXquery11() && parseKeyword("for")) {
	    int windowType = 0;
	    if (parseKeyword("tumbling")) {
		windowType = ForseqIterator.TUMBLING_WINDOW;
	    } else if (parseKeyword("sliding")) {
		windowType = ForseqIterator.SLIDING_WINDOW;
	    } else if (parseKeyword("landmark")) {
		windowType = ForseqIterator.LANDMARK_WINDOW;
	    }
	    if (windowType != 0) {
		if (!parseKeyword("window"))
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "window expected");
		extendCurrentContextScope(); // put variables bound by window
		// operator into own scope
		if (!parseString("$", true, false)) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: '$' expected!");
		}

		QName varQName = QName();
		if (varQName == null) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'QName' expected!");
		}
		Iterator seqTypeIt = null;
		TypeInfo qType = TypeDeclaration();
		if (!qType.isUndefined()) {
		    seqTypeIt = new SequenceTypeIterator(qType, true, true,
			    getCurrentContext(), getCurrentLoc());
		}

		getCurrentContext().registerVariable(varQName, true, seqTypeIt,
			false);

		if (!parseKeyword("in")) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'in' expected!");
		}

		Iterator seq;
		if ((seq = ExprSingle()) == null) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'ExprSingle' expected!");
		}
		Context outerContext = getCurrentContext();
		// StartExpression
		if (!parseKeyword("start")) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'start' expected!");
		}
		WindowVariable[] startVars = forWindowVars();
		if (!parseKeyword("when")) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'when' expected!");
		}

		// for parallel execution, do late resolving of varHolders,
		// since the context might have been swapped

		if (parallelExecution) {
		    resolveVarsOnReset = true;
		}

		XDMIterator startExpr = null;
		if ((startExpr = ExprSingle()) == null) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'ExprSingle' expected!");
		}

		startExpr = BooleanIterator.createEBVIterator(startExpr,
			getCurrentContext());

		boolean forceEnd = false;

		if (parseKeyword("only")) {
		    forceEnd = true;
		}
		boolean onNewStart = false;
		XDMIterator endExpr = null;
		WindowVariable[] endVars = null;
		// EndExpression
		if (!parseKeyword("end")) {
		    if (windowType != ForseqIterator.TUMBLING_WINDOW)
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"Error while parsing: 'end' expected!");
		    else {
			onNewStart = true;
			endVars = new WindowVariable[0];
		    }
		} else {
		    endVars = forWindowVars();
		    if (!parseKeyword("when")) {
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"Error while parsing: 'when' expected!");
		    }
		    if ((endExpr = ExprSingle()) == null) {
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"Error while parsing: 'ExprSingle' expected!");
		    }
		    endExpr = BooleanIterator.createEBVIterator(endExpr,
			    getCurrentContext());
		}

		resolveVarsOnReset = false;

		return generateForseqIterator(varQName, qType, seq, windowType,
			outerContext, startVars, startExpr, forceEnd, endVars,
			onNewStart, endExpr);
	    }
	}
	index = origIndex;
	return null;
    }

    private XDMIterator forseqClause() throws MXQueryException {
	if (co.isXquery11() && parseKeyword("forseq")) {
	    extendCurrentContextScope();
	    if (!parseString("$", true, false)) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: '$' expected!");
	    }

	    QName varQName = QName();
	    if (varQName == null) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: 'QName' expected!");
	    }

	    // rememberVariable(varQName); // gc

	    Iterator seqTypeIt = null;
	    TypeInfo qType = TypeDeclaration();
	    if (!qType.isUndefined()) {
		seqTypeIt = new SequenceTypeIterator(qType, true, true,
			getCurrentContext(), getCurrentLoc());
	    }

	    getCurrentContext().registerVariable(varQName, true, seqTypeIt,
		    false);

	    if (!parseKeyword("in")) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: 'in' expected!");
	    }

	    Iterator seq;
	    if ((seq = ExprSingle()) == null) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: 'ExprSingle' expected!");
	    }

	    // TODO: Define static window types in the iterator
	    int windowType = 0;
	    if (parseKeyword("tumbling window")) {
		windowType = ForseqIterator.TUMBLING_WINDOW;
	    } else if (parseKeyword("sliding window")) {
		windowType = ForseqIterator.SLIDING_WINDOW;
	    } else if (parseKeyword("landmark window")) {
		windowType = ForseqIterator.LANDMARK_WINDOW;
	    } else {
		windowType = ForseqIterator.GENERAL_WINDOW;
	    }
	    if (windowType == 0) {
		return new ForseqGeneralIterator(getCurrentContext(),
			windowType, varQName, qType, seq,
			ForseqIterator.ORDER_MODE_END, getCurrentLoc());
	    } else {
		Context outerContext = getCurrentContext();
		// StartExpression
		if (!parseKeyword("start")) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'start' expected!");
		}
		WindowVariable[] startVars = forseqWindowVars();
		if (!parseKeyword("when")) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'when' expected!");
		}

		// for parallel execution, do late resolving of varHolders,
		// since the context might have been swapped

		if (parallelExecution) {
		    resolveVarsOnReset = true;
		}

		XDMIterator startExpr = null;
		if ((startExpr = ExprSingle()) == null) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'ExprSingle' expected!");
		}

		startExpr = BooleanIterator.createEBVIterator(startExpr,
			getCurrentContext());

		boolean forceEnd = false;

		if (parseKeyword("force")) {
		    forceEnd = true;
		}

		// EndExpression
		if (!parseKeyword("end")) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'end' expected!");
		}

		WindowVariable[] endVars = forseqWindowVars();
		if (!parseKeyword("when")) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'when' expected!");
		}
		boolean onNewStart = false;
		XDMIterator endExpr = null;
		;
		if (parseKeyword("newstart")) {
		    onNewStart = true;
		} else if ((endExpr = ExprSingle()) == null) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'ExprSingle' expected!");
		}

		if (endExpr != null)
		    endExpr = BooleanIterator.createEBVIterator(endExpr,
			    getCurrentContext());

		resolveVarsOnReset = false;

		return generateForseqIterator(varQName, qType, seq, windowType,
			outerContext, startVars, startExpr, forceEnd, endVars,
			onNewStart, endExpr);

	    }
	}
	return null;
    }

    protected ForseqIterator generateForseqIterator(QName varQName,
	    TypeInfo qType, XDMIterator seq, int windowType,
	    Context outerContext, WindowVariable[] startVars,
	    XDMIterator startExpr, boolean forceEnd, WindowVariable[] endVars,
	    boolean onNewStart, XDMIterator endExpr) throws MXQueryException {
	return new ForseqWindowNaiveIterator(outerContext, windowType,
		varQName, qType, seq, startVars, startExpr, endVars, endExpr,
		forceEnd, onNewStart, ForseqIterator.ORDER_MODE_END,
		getCurrentLoc());
    }

    private WindowVariable[] forWindowVars() throws MXQueryException {
	Vector windowVars = new Vector();
	boolean newContext = false;

	newContext = parseWindowVar(windowVars, newContext,
		WindowVariable.WINDOW_VAR_CUR_ITEM, false);
	if (parseString("at", true, true))
	    newContext = parseWindowVar(windowVars, newContext,
		    WindowVariable.WINDOW_VAR_POSITION, true);
	if (parseString("previous", true, true))
	    newContext = parseWindowVar(windowVars, newContext,
		    WindowVariable.WINDOW_VAR_PREV_ITEM, true);
	if (parseString("next", true, true))
	    newContext = parseWindowVar(windowVars, newContext,
		    WindowVariable.WINDOW_VAR_NEXT_ITEM, true);
	WindowVariable[] ret = new WindowVariable[windowVars.size()];
	for (int i = 0; i < ret.length; i++) {
	    ret[i] = (WindowVariable) windowVars.elementAt(i);
	}
	return ret;
    }

    private boolean parseWindowVar(Vector windowVars, boolean newContext,
	    int varType, boolean required) throws MXQueryException,
	    StaticException {
	if (parseString("$", true, false)) {
	    QName qname = QName();
	    if (qname == null) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: 'QName' expected!");
	    }
	    if (!newContext) {
		extendCurrentContextScope();
		newContext = false;
	    }
	    windowVars.addElement(new WindowVariable(qname, varType,
		    getCurrentLoc()));
	    getCurrentContext().registerVariable(qname, false);
	} else {
	    if (required)
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: '$' expected!");
	}
	return newContext;
    }

    private WindowVariable[] forseqWindowVars() throws MXQueryException {
	Vector windowVars = new Vector();
	boolean newContext = false;
	do {
	    int varType = -1;
	    if (parseKeyword("position"))
		varType = WindowVariable.WINDOW_VAR_POSITION;
	    if (parseKeyword("curItem"))
		varType = WindowVariable.WINDOW_VAR_CUR_ITEM;
	    if (parseKeyword("nextItem"))
		varType = WindowVariable.WINDOW_VAR_NEXT_ITEM;
	    if (parseKeyword("prevItem"))
		varType = WindowVariable.WINDOW_VAR_PREV_ITEM;
	    if (varType > -1) {
		if (!newContext) {
		    extendCurrentContextScope();
		    newContext = false;
		}
		if (parseString("$", true, false)) {
		    QName qname = QName();
		    if (qname == null) {
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"Error while parsing: 'QName' expected!");
		    }
		    windowVars.addElement(new WindowVariable(qname, varType,
			    getCurrentLoc()));
		    getCurrentContext().registerVariable(qname, false);
		}
	    }
	} while (parseString(",", true, false)); // Here we are more friendly
	// than the specification,
	// so "start ,,," when would
	// be valid :-)
	WindowVariable[] ret = new WindowVariable[windowVars.size()];
	for (int i = 0; i < ret.length; i++) {
	    ret[i] = (WindowVariable) windowVars.elementAt(i);
	}
	return ret;
    }

    private Vector forClause() throws MXQueryException {

	Vector its = new Vector();
	int i = 0;
	if (parseKeyword("for")) {
	    if (parseString("$", true, false)) {
		QName q = QName();
		if (q == null) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'QName' expected!");
		}

		TypeInfo qType = TypeDeclaration();
		Iterator seqTypeIt = null;
		if (!qType.isUndefined()) {
		    seqTypeIt = new SequenceTypeIterator(qType, true, true,
			    getCurrentContext(), getCurrentLoc());
		}

		QName p = PositionalVar(); // may be null

		// full text extension
		QName s = null;
		if (co.isFulltext())
		    s = FTScoreVar(); // may be null

		if (parseKeyword("in")) {
		    Iterator inIt;
		    if ((inIt = ExprSingle()) != null) {
			// wait with the new context until in is parsed
			extendCurrentContextScope().registerVariable(q, true,
				seqTypeIt, false);

			if (p != null) {
			    getCurrentContext().registerVariable(p, true);
			}

			if (s != null) {
			    generateStaticError(
				    ErrorCodes.A0002_EC_NOT_SUPPORTED,
				    "Scoring in FOR not yet supported");
			    // getCurrentContext().registerVariable(s, true);

			}

			its.insertElementAt(new ForIterator(
				getCurrentContext(), new Iterator[] { inIt },
				q, qType, p, getCurrentLoc()), i++);

			while (parseString(",", true, false)) {
			    if (!parseString("$", true, false)) {
				generateStaticError(
					ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
					"Error while parsing: '$' expected!");
			    }

			    QName qname = QName();
			    if (qname == null) {
				generateStaticError(
					ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
					"Error while parsing: 'QName' expected!");
			    }

			    TypeInfo t1 = TypeDeclaration();
			    seqTypeIt = null;
			    if (!t1.isUndefined()) {
				seqTypeIt = new SequenceTypeIterator(t1, true,
					true, getCurrentContext(),
					getCurrentLoc());
			    }

			    QName p1 = PositionalVar(); // may be null
			    QName s1 = FTScoreVar(); // may be null

			    if (parseKeyword("in")) {
				Iterator inIt1;
				if ((inIt1 = ExprSingle()) != null) {

				    // wait with the new context until in is
				    // parsed
				    extendCurrentContextScope()
					    .registerVariable(qname, false,
						    seqTypeIt, false);
				    if (p1 != null) {
					getCurrentContext().registerVariable(
						p1, true);
				    }

				    if (s1 != null) {
					generateStaticError(
						ErrorCodes.A0002_EC_NOT_SUPPORTED,
						"score variable exists, but scoring not supported");
					// getCurrentContext().registerVariable(s1,
					// true);

				    }
				    its.insertElementAt(new ForIterator(
					    getCurrentContext(),
					    new Iterator[] { inIt1 }, qname,
					    t1, p1, getCurrentLoc()), i++);

				    // generateStaticError(ErrorCodes.A0002_EC_NOT_SUPPORTED,"For
				    // Iterator for score variables not
				    // supported yet");
				    // generateStaticError(ErrorCodes.A0002_EC_NOT_SUPPORTED,
				    // "Multiple Arguments in for clause not
				    // supported yet!" );
				} else {
				    generateStaticError(
					    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
					    "Error while parsing: 'ExprSingle' expected!");
				}
			    } else {
				generateStaticError(
					ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
					"Error while parsing: 'in' expected!");
			    }

			}
			return its;
		    } else {
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"Error while parsing: 'ExprSingle' expected!");
		    }
		} else {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'in' expected!");
		}

	    } else {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: '$' expected!");
	    }
	}

	return null;
    }

    private Vector letClause() throws MXQueryException {

	Vector its = new Vector();
	int i = 0;
	boolean score = false;
	QName q = null;
	// TypeInfo type = null;
	Iterator seqTypeIt = null;

	int oldIndex = index;

	if (parseKeyword("let")) {
	    if (co.isFulltext() && parseString("score", true, false)) {
		score = true;
	    }
	    if (parseString("$", true, false)) {

		q = QName();
		if (q == null) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'QName' expected!");
		}

		TypeInfo t = TypeDeclaration();
		seqTypeIt = null;
		if (!t.isUndefined()) {
		    if (score == false) {
			seqTypeIt = new SequenceTypeIterator(t, true, false,
				getCurrentContext(), getCurrentLoc());
		    } else {
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"Error while parsing: no TypeDeclaration() if score is defined");
		    }
		}

		if (parseString(":=", true, false)) {
		    Iterator inIt;
		    if (score)
			useFTScoring = true;
		    if ((inIt = ExprSingle()) != null) {
			if (score) {
			    useFTScoring = false;
			    getCurrentContext().registerVariable(
				    new QName(".ft", ".score"), false);
			    extendCurrentContextScope().registerVariable(q,
				    false, seqTypeIt, false);
			    Score s = new Score(inIt, getCurrentContext(),
				    getCurrentLoc());
			    its.insertElementAt(new LetIterator(
				    getCurrentContext(), new Iterator[] { s },
				    q, t, getCurrentLoc()), i++);

			} else {
			    extendCurrentContextScope().registerVariable(q,
				    false, seqTypeIt, false);
			    its.insertElementAt(new LetIterator(
				    getCurrentContext(),
				    new Iterator[] { inIt }, q, t,
				    getCurrentLoc()), i++);
			}
			while (parseString(",", true, false)) {

			    QName qname = null;
			    seqTypeIt = null;
			    TypeInfo t1 = null;

			    if (parseString("$", true, false)) {

				qname = QName();
				if (qname == null) {
				    generateStaticError(
					    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
					    "Error while parsing: 'QName' expected!");
				}

				t1 = TypeDeclaration();
				// seqTypeIt = null;
				if (!t1.isUndefined()) {
				    seqTypeIt = new SequenceTypeIterator(t1,
					    true, true, getCurrentContext(),
					    getCurrentLoc());
				}

			    } else {
				t1 = new TypeInfo();
				qname = FTScoreVar();
				if (qname == null) {
				    generateStaticError(
					    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
					    "Error while parsing: 'FTScoreVar' expected!");
				}

			    }

			    if (parseString(":=", true, false)) {
				Iterator inIt1;
				if ((inIt1 = ExprSingle()) != null) {
				    extendCurrentContextScope()
					    .registerVariable(qname, false,
						    seqTypeIt, false);
				    its.insertElementAt(new LetIterator(
					    getCurrentContext(),
					    new Iterator[] { inIt1 }, qname,
					    t1, getCurrentLoc()), i++);
				} else {
				    generateStaticError(
					    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
					    "Error while parsing: 'ExprSingle' expected!");
				}
			    } else {
				generateStaticError(
					ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
					"Error while parsing: ':=' expected!");
			    }
			}

			return its;
		    } else {
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"Error while parsing: 'ExprSingle' expected!");
		    }
		} else {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: ':=' expected!");
		}
	    }

	    else {
		index = oldIndex;
		return null;
	    }
	}
	return null;
    }

    private Iterator whereClause() throws MXQueryException {
	if (parseKeyword("where")) {
	    Iterator temp;
	    if ((temp = ExprSingle()) != null) {
		return temp;
	    } else {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: 'else' expected!");
	    }
	}
	return null;
    }

    private TypeInfo TypeDeclaration() throws MXQueryException {
	if (parseKeyword("as")) {
	    return SequenceType();
	}

	return new TypeInfo(); // null;
    }

    private TypeInfo SequenceType() throws MXQueryException {

	QName typeQName = null;
	TypeInfo typeInfo = null;

	if (parseKeyword("empty-sequence") && parseString("(", true, false)
		&& parseString(")", true, false)) {
	    typeInfo = new TypeInfo();
	    typeInfo.setType(Type.TYPE_NK_EMPTY_SEQ_TEST);
	} else if ((typeInfo = KindTest()) != null) {
	    // do nothing
	} else if (parseKeyword("item") && parseString("(", true, false)
		&& parseString(")", true, false)) {
	    typeInfo = new TypeInfo();
	    typeInfo.setType(Type.ITEM);

	} else if ((typeQName = QName()) == null) {

	    generateStaticError(
		    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
		    "Error while parsing: SequenceType clause is incorrect!");
	} else {

	    if (parseStringAndStay("(", true, false)
		    || hRestrictedTypeNames.get(typeQName.toString()) != null)
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: SequenceType clause is incorrect!");

	    String namespacePrefix = typeQName.getNamespacePrefix();
	    if ((namespacePrefix == null)
		    || (!typeQName.getNamespacePrefix().equals(
			    Type.NAMESPACE_XS)))
		typeQName = rewriteUDTQNameWithResolvedPrefix(typeQName);

	    int typeToCheck = Type.getTypeFootprint(typeQName, Context
		    .getDictionary());
	    typeInfo = new TypeInfo();
	    typeInfo.setType(typeToCheck);
	}

	int occ = OccurrenceIndicator();
	if (occ == -1)
	    occ = Type.OCCURRENCE_IND_EXACTLY_ONE;

	if (typeInfo.getType() == Type.TYPE_NK_EMPTY_SEQ_TEST
		&& (occ == Type.OCCURRENCE_IND_ONE_OR_MORE || occ == Type.OCCURRENCE_IND_ZERO_OR_ONE))
	    generateStaticError(
		    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
		    "empty-sequence() with occurrence indicator '+' or '?' is not allowed");

	typeInfo.setOccurID(occ);

	return typeInfo;
    }

    protected QName rewriteUDTQNameWithResolvedPrefix(QName typeQName)
	    throws StaticException {
	Namespace ns = getCurrentContext().getNamespace(
		typeQName.getNamespacePrefix());
	if (ns == null
		|| !getCurrentContext().containsTargetNamespace(ns.getURI())) {
	    if (typeQName.getNamespacePrefix() == null)
		throw new StaticException(
			ErrorCodes.E0051_STATIC_QNAME_AS_ATOMICTYPE_NOT_DEFINED_AS_ATOMIC,
			"Type '" + typeQName + "' has not been defined",
			getCurrentLoc());
	    else
		throw new StaticException(
			ErrorCodes.E0081_STATIC_QUERY_UNKNOWN_NAMESPACE,
			"Prefix " + typeQName.getNamespacePrefix()
				+ " has not been declared", getCurrentLoc());
	} else {
	    QName qName;
	    if (ns.getURI().equals(XQStaticContext.URI_XS))
		qName = new QName(Type.NAMESPACE_XS, typeQName.getLocalPart());
	    else
		qName = new QName(ns.getURI(), ns.getURI(), typeQName
			.getLocalPart());
	    return qName;
	}
    }

    private int OccurrenceIndicator() throws MXQueryException {
	if (co.isContinuousXQ() && parseString("**", true, false))
	    return Type.OCCURRENCE_IND_INFINITIVE;
	if (parseString("?", true, false))
	    return Type.OCCURRENCE_IND_ZERO_OR_ONE;
	if (parseString("*", true, false))
	    return Type.OCCURRENCE_IND_ZERO_OR_MORE;
	if (parseString("+", true, false))
	    return Type.OCCURRENCE_IND_ONE_OR_MORE;
	return -1;
    }

    private QName PositionalVar() throws MXQueryException {
	if (parseKeyword("at")) {
	    if (parseString("$", true, false)) {
		QName temp;
		if ((temp = QName()) != null) {
		    return temp;
		} else {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'QName' expected!");
		}
	    } else {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: '$' expected!");
	    }
	}
	return null;
    }

    private OrderByIterator OrderByClause() throws MXQueryException {
	int curIndex = index;
	if (parseKeyword("stable") && parseKeyword("order")
		&& parseKeyword("by")) {
	    return OrderSpecList(true);
	}
	index = curIndex;
	if (parseKeyword("order") && parseKeyword("by")) {
	    return OrderSpecList(true);
	}
	index = curIndex;
	return null;
    }

    private OrderByIterator OrderSpecList(boolean stable)
	    throws MXQueryException {
	Vector orderExprs = new Vector();
	Vector orderOptions = new Vector();
	do {
	    XDMIterator orderExpr = ExprSingle();

	    boolean ascending = true;
	    boolean emptiesGreatest = false;

	    String defaultOrder = getCurrentContext()
		    .getDefaultOrderEmptySequence();

	    if (defaultOrder != null
		    && defaultOrder.equals(XQStaticContext.ORDER_GREATEST))
		emptiesGreatest = true;

	    // parsing Order Modifiers
	    if (parseKeyword("ascending")) {
		ascending = true;
	    } else if (parseKeyword("descending")) {
		ascending = false;
	    }
	    if (parseKeyword("empty")) {
		if (parseKeyword("greatest")) {
		    emptiesGreatest = true;
		} else if (parseKeyword("least")) {
		    emptiesGreatest = false;
		} else {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "An empty handling declaration in "
				    + "an order by must be followed by"
				    + "'greatest' or 'least'!");
		}
	    }
	    collationSpecification();
	    OrderOptions eo = new OrderOptions(ascending, emptiesGreatest);
	    orderExprs.addElement(orderExpr);
	    orderOptions.addElement(eo);
	} while (parseString(",", true, false));
	XDMIterator[] arrExprs = new Iterator[orderExprs.size()];
	OrderOptions[] arrOptions = new OrderOptions[orderOptions.size()];
	orderExprs.copyInto(arrExprs);
	for (int i = 0; i < arrExprs.length; i++) {
	    arrExprs[i] = DataValuesIterator.getDataIterator(arrExprs[i],
		    getCurrentContext());
	}
	orderOptions.copyInto(arrOptions);
	return new OrderByIterator(getCurrentContext(), arrExprs, arrOptions,
		stable, getCurrentLoc());
    }

    private void collationSpecification() throws MXQueryException,
	    StaticException {
	if (parseKeyword("collation")) {
	    XDMIterator it = StringLiteral();
	    Token tok = it.next();
	    String uri = tok.getText();
	    if (uri == null) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: 'StringLiteral' expected!");
	    } else {
		if (!TypeLexicalConstraints.isValidURI(uri))
		    generateStaticError(ErrorCodes.E0046_STATIC_EMPTY_URI,
			    "Invalid URI for ordering collation");
		if (TypeLexicalConstraints.isRelativeURI(uri)) {
		    uri = getCurrentContext().getBaseURI() + uri;
		}

		if (!uri.equals(XQStaticContext.CODEPOINT_COLLATION_URI))
		    generateStaticError(
			    ErrorCodes.E0076_STATIC_FLWOR_ORDER_BY_UNKNOWN_COLLATION,
			    "Unsupported collation for ordering");
	    }
	}
    }

    static class GroupHelper {
	GroupHelper(QName sourceQName, QName targetQName,
		XDMIterator[] bySources, QName[] byTargets, XDMIterator[] lets,
		XDMIterator where) throws MXQueryException {

	    this.sourceQName = sourceQName;

	    this.targetQName = targetQName;
	    this.bySources = bySources;
	    this.byTargets = byTargets;
	    this.lets = lets;
	    this.where = where;
	}

	QName sourceQName;
	QName targetQName;
	XDMIterator[] bySources;
	QName[] byTargets;
	XDMIterator[] lets;
	XDMIterator where;
    }

    /*
     * XQuery 1.1 groupby groupbyclause ::= "group" "by" GroupingSpecList
     * GroupingSpecList ::= GroupingSpec ("," GroupingSpec)* GroupingSpec ::= $
     * Varname ("collation URILiteral)?
     */

    private GroupHelper GroupByClause() throws MXQueryException {
	int origPos = index;
	if (co.isXquery11() && parseKeyword("group") && parseKeyword("by")) {
	    Vector groupVars = new Vector();
	    do {
		XDMIterator orderExpr = VarRef();
		groupVars.addElement(orderExpr);
		collationSpecification();
	    } while (parseString(",", true, false));
	    XDMIterator[] arrExprs = new XDMIterator[groupVars.size()];
	    groupVars.copyInto(arrExprs);
	    // TODO Adapt GroupByHelper, implement new Group By Iterator
	    generateStaticError(ErrorCodes.A0002_EC_NOT_SUPPORTED,
		    "XQuery 1.1 Group By currently not yet supported");
	}
	index = origPos;
	return null;
    }

    private Iterator QuantifiedExpr() throws MXQueryException {
	String op;
	if ((op = parseStringGetResult("some", true)) != null
		|| (op = parseStringGetResult("every", true)) != null) {

	    Vector qNames = new Vector();
	    // Vector typeInfos = new Vector();
	    Vector inSeq = new Vector();
	    if (parseString("$", true, false)) {
		QName q = QName();
		if (q == null) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'QName' expected!");
		}
		qNames.addElement(q);
		TypeInfo t = TypeDeclaration();
		Iterator seqTypeIt = null;
		if (!t.isUndefined()) {
		    seqTypeIt = new SequenceTypeIterator(t, true, true,
			    getCurrentContext(), getCurrentLoc());
		}

		if (parseKeyword("in")) {
		    Iterator inIt;
		    if ((inIt = ExprSingle()) != null) {
			createNewContextScope();
			inIt.setContext(getCurrentContext(), true);
			getCurrentContext().registerVariable(q, false,
				seqTypeIt, false);
			inIt = new ForIterator(getCurrentContext(),
				new Iterator[] { inIt }, q, t, null,
				getCurrentLoc());
			inSeq.addElement(inIt);
			while (parseString(",", true, false)) {
			    if (parseString("$", true, false)) {

				QName qname = QName();
				if (qname == null) {
				    generateStaticError(
					    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
					    "Error while parsing: 'QName' expected!");
				}
				qNames.addElement(qname);
				TypeInfo t1 = TypeDeclaration();
				seqTypeIt = null;
				if (!t1.isUndefined()) {
				    seqTypeIt = new SequenceTypeIterator(t1,
					    true, true, getCurrentContext(),
					    getCurrentLoc());
				}

				if (parseKeyword("in")) {
				    Iterator inIt1;
				    if ((inIt1 = ExprSingle()) != null) {
					extendCurrentContextScope()
						.registerVariable(qname, false,
							seqTypeIt, false);
					inIt1 = new ForIterator(
						getCurrentContext(),
						new Iterator[] { inIt1 },
						qname, t1, null,
						getCurrentLoc());
					inSeq.addElement(inIt1);
				    } else {
					generateStaticError(
						ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
						"Error while parsing: 'ExprSingle' expected!");
				    }
				} else {
				    generateStaticError(
					    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
					    "Error while parsing: 'in' expected!");
				}
			    }
			}

			if (parseKeyword("satisfies")) {
			    XDMIterator satIt;
			    if ((satIt = ExprSingle()) != null) {
				QName[] quantVars = new QName[qNames.size()];
				for (int i = 0; i < qNames.size(); i++)
				    quantVars[i] = (QName) qNames.elementAt(i);
				XDMIterator[] its = new Iterator[inSeq.size() + 1];
				for (int i = 0; i < inSeq.size(); i++)
				    its[i] = (Iterator) inSeq.elementAt(i);
				its[inSeq.size()] = BooleanIterator
					.createEBVIterator(satIt,
						getCurrentContext());
				// TypeInfo [] inTypes = new TypeInfo
				// [qNames.size()];
				boolean every = false;
				if (op.equals("every"))
				    every = true;
				removeContextScope();
				Iterator iter = new QuantifiedIterator(
					getCurrentContext(), its, every,
					getCurrentLoc());
				return iter;
			    } else {
				generateStaticError(
					ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
					"Error while parsing: 'ExprSingle' expected!");
			    }
			} else {
			    generateStaticError(
				    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				    "Error while parsing: 'satisifies' expected!");
			}
		    } else {
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"Error while parsing: 'ExprSingle' expected!");
		    }
		} else {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'in' expected!");
		}

	    } else {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: '$' expected!");
	    }
	}
	// ("," "$" VarName TypeDeclaration? "in" ExprSingle)* "satisfies"
	// ExprSingle
	return null;
    }

    // "typeswitch" "(" Expr ")" CaseClause+ "default" ("$" VarName)? "return"
    // ExprSingle
    private Iterator TypeswitchExpr() throws MXQueryException {

	if (parseString("typeswitch", true, false)) {
	    Iterator operandIt = null;
	    Vector vCaseSwitch = null;
	    Iterator[] vDefault;

	    if (parseString("(", true, false)) {
		extendCurrentContextScope();
		if ((operandIt = Expr()) != null) {
		    if (!parseString(")", true, false)) {
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"Error while parsing: ')' expected!");
		    }
		} else {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'Expr' expected!");
		}
	    } else {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: '(' expected!");
	    }

	    QName anymVar = this.getCurrentContext()
		    .registerAnonymousVariable();
	    LetIterator mainBinding = new LetIterator(getCurrentContext(),
		    new Iterator[] { operandIt }, anymVar, null,
		    getCurrentLoc());

	    vCaseSwitch = new Vector();
	    XDMIterator[] vCaseData;
	    if ((vCaseData = CaseClause(anymVar)) != null) {
		vCaseSwitch.addElement(vCaseData);
		while ((vCaseData = CaseClause(anymVar)) != null) {
		    vCaseSwitch.addElement(vCaseData);
		}

		Iterator varBinding = null;

		if (parseKeyword("default")) {
		    QName defVarName = null;
		    createNewContextScope();
		    if (parseString("$", true, false)) {
			defVarName = QName();
			if (defVarName == null) {
			    generateStaticError(
				    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				    "Error while parsing: 'QName' expected!");
			}

			getCurrentContext().registerVariable(defVarName, false);
			varBinding = new LetIterator(getCurrentContext(),
				new Iterator[] { new VariableIterator(
					getCurrentContext(), anymVar, false,
					getCurrentLoc()) }, defVarName, null,
				getCurrentLoc());
		    }

		    if (parseKeyword("return")) {
			Iterator retIt;
			if ((retIt = ExprSingle()) != null) {
			    removeContextScope();
			    if (varBinding != null) {
				vDefault = new Iterator[2];
				vDefault[1] = varBinding;

			    } else {
				vDefault = new Iterator[1];
			    }

			    vDefault[0] = retIt;

			    return new TypeSwitchIterator(getCurrentContext(),
				    mainBinding, new VariableIterator(
					    getCurrentContext(), anymVar,
					    false, getCurrentLoc()),
				    vCaseSwitch, vDefault, getCurrentLoc());

			} else {
			    generateStaticError(
				    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				    "Error while parsing: 'ExprSingle' expected!");
			}
		    } else {
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"Error while parsing: 'return' expected!");
		    }
		} else {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'default' expected!");
		}
	    } else {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: 'CaseClause' expected!");
	    }
	    removeContextScope();
	}
	return null;

    }

    // "case" ("$" VarName "as")? SequenceType "return" ExprSingle
    private XDMIterator[] CaseClause(QName anymVar) throws MXQueryException {
	XDMIterator[] caseData;
	XDMIterator retIt;
	LetIterator varBinding = null;

	if (parseKeyword("case")) {
	    createNewContextScope();

	    if (parseString("$", true, false)) {

		QName varQName = QName();
		if (varQName == null) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'QName' expected!");
		}

		getCurrentContext().registerVariable(varQName, false);
		varBinding = new LetIterator(getCurrentContext(),
			new Iterator[] { new VariableIterator(
				getCurrentContext(), anymVar, false,
				getCurrentLoc()) }, varQName, null,
			getCurrentLoc());

		if (!parseKeyword("as")) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'as' expected!");
		}
	    }

	    TypeInfo typeInfo = SequenceType();
	    XDMIterator seqTypeIt = new SequenceTypeIterator(typeInfo, false,
		    false, getCurrentContext(), getCurrentLoc()); // non
	    // streaming

	    if (seqTypeIt == null)
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: type QName expected!");

	    if (parseKeyword("return")) {
		if ((retIt = ExprSingle()) != null) {
		    removeContextScope();
		    if (varBinding != null) {
			caseData = new XDMIterator[3];
			caseData[2] = varBinding;
		    } else {
			caseData = new XDMIterator[2];
		    }

		    caseData[0] = retIt;
		    caseData[1] = seqTypeIt;

		    return caseData;
		} else {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'ExprSingle' expected!");
		}
	    } else {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: 'return' expected!");
	    }
	}
	return null;
    }

    private Iterator IfExpr() throws MXQueryException {
	XDMIterator[] ifIters = new Iterator[3];

	if (parseKeyword("if")) {
	    if (parseString("(", true, false)) {
		ifIters[0] = BooleanIterator.createEBVIterator(Expr(),
			getCurrentContext());
		if (parseString(")", true, false)) {
		    if (parseKeyword("then")) {
			ifIters[1] = ExprSingle();
			if (parseKeyword("else")) {
			    ifIters[2] = ExprSingle();
			    return new IfThenElseIterator(getCurrentContext(),
				    ifIters, getCurrentLoc());
			} else {
			    generateStaticError(
				    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				    "Error while parsing: 'else' expected!");
			}
		    } else {
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"Error while parsing: 'then' expected!");
		    }
		} else {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: ')' expected!");
		}
	    } else {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: '(' expected!");
	    }
	}
	return null;
    }

    private Iterator OrExpr() throws MXQueryException {
	boolean logical = false;
	Iterator iter = null;
	Vector its = new Vector();

	if ((iter = AndExpr()) == null) {
	    return null;
	}

	while (parseKeyword("or")) {
	    logical = true;
	    Iterator temp = null;

	    if ((temp = AndExpr()) == null) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: AndExpr expected!");
	    } else {
		if (its.size() == 0) {
		    its.addElement(BooleanIterator.createEBVIterator(iter,
			    getCurrentContext()));
		}
		its.addElement(BooleanIterator.createEBVIterator(temp,
			getCurrentContext()));
	    }
	}

	if (!logical) {
	    return iter;
	} else {
	    Iterator[] i = new Iterator[its.size()];
	    its.copyInto(i);
	    return new LogicalIterator(getCurrentContext(), LogicalIterator.OR,
		    i, getCurrentLoc());
	}
    }

    private Iterator AndExpr() throws MXQueryException {
	boolean logical = false;
	Iterator iter = null;
	Vector its = new Vector();

	if ((iter = ComparisonExpr()) == null) {
	    return null;
	}

	while (parseKeyword("and")) {
	    logical = true;
	    Iterator temp = null;

	    if ((temp = AndExpr()) == null) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: AndExpr expected!");
	    } else {
		if (its.size() == 0) {
		    its.addElement(BooleanIterator.createEBVIterator(iter,
			    getCurrentContext()));
		}
		its.addElement(BooleanIterator.createEBVIterator(temp,
			getCurrentContext()));
	    }
	}

	if (!logical) {
	    return iter;
	} else {
	    Iterator[] i = new Iterator[its.size()];
	    its.copyInto(i);
	    return new LogicalIterator(getCurrentContext(),
		    LogicalIterator.AND, i, getCurrentLoc());
	}
    }

    private Iterator ComparisonExpr() throws MXQueryException {
	XDMIterator iter[] = new Iterator[2];
	if (co.isFulltext() == true) {
	    iter[0] = FTContainsExpr();
	} else {
	    iter[0] = RangeExpr();
	}
	if (iter[0] == null) {
	    return null;
	}

	int comparator = -1;
	int compareType = -1;
	boolean comp = false;
	if ((comparator = ValueComp()) != -1) {
	    compareType = Constants.COMP_VALUE;
	    comp = true;
	} else if ((comparator = NodeComp()) != -1) {
	    compareType = Constants.COMP_NODE;
	    comp = true;
	} else if ((comparator = GeneralComp()) != -1) {
	    compareType = Constants.COMP_GENERAL;
	    comp = true;
	}

	if (comp) {
	    skipComments();
	    if (co.isFulltext() == true) {
		iter[1] = FTContainsExpr();
	    } else {
		iter[1] = RangeExpr();
	    }
	    if (iter[1] == null) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: RangeExpr expected!");
	    } else {

		// for (int i = 0; i < iter.length; i++) {
		// if ((iter[i] instanceof AttributeIterator
		// || iter[i] instanceof ChildIterator || iter[i] instanceof
		// DescendantOrSelfIterator)) {
		// // assign context iterator to the Iterator at the
		// // end of the path
		// if (!iter[i].areSubItersDefined()) {
		// iter[i].setSubIters(new VariableIterator(
		// getCurrentContext(),
		// Context.CONTEXT_ITEM));
		// } else {
		// Iterator tmpIt = iter[i];
		// while (tmpIt.areSubItersDefined()
		// && (tmpIt instanceof AttributeIterator
		// || tmpIt instanceof ChildIterator || tmpIt instanceof
		// DescendantOrSelfIterator)) {
		// tmpIt = tmpIt.getSubIters()[0]; // only one
		// // subiterator
		// // allowed
		// // for these
		// // iterators
		// if (!tmpIt.areSubItersDefined()
		// && (tmpIt instanceof AttributeIterator
		// || tmpIt instanceof ChildIterator || tmpIt instanceof
		// DescendantOrSelfIterator)) {
		// tmpIt.setSubIters(new VariableIterator(
		// getCurrentContext(),
		// Context.CONTEXT_ITEM));
		// break;
		// }
		// }
		// }
		// }// if
		//
		// } // for

		if (compareType != Constants.COMP_NODE) {
		    for (int i = 0; i < iter.length; i++) {
			iter[i] = DataValuesIterator.getDataIterator(iter[i],
				getCurrentContext());
		    }
		}
		return new CompareIterator(getCurrentContext(), compareType,
			comparator, iter, getCurrentLoc());
	    }
	}

	return (Iterator) iter[0];

    }

    private int ValueComp() throws MXQueryException {
	if (parseKeyword("eq")) {
	    return Constants.COMP_EQ;
	} else if (parseKeyword("ne")) {
	    return Constants.COMP_NE;
	} else if (parseKeyword("lt")) {
	    return Constants.COMP_LT;
	} else if (parseKeyword("le")) {
	    return Constants.COMP_LE;
	} else if (parseKeyword("gt")) {
	    return Constants.COMP_GT;
	} else if (parseKeyword("ge")) {
	    return Constants.COMP_GE;
	} else {
	    return -1;
	}
    }

    private int GeneralComp() throws MXQueryException {

	if (parseString("=", true, false)) {
	    return Constants.COMP_EQ;
	} else if (parseString("!=", true, false)) {
	    return Constants.COMP_NE;
	} else if (parseString("<=", true, false)) {
	    return Constants.COMP_LE;
	} else if (parseString("<", true, false)) {
	    return Constants.COMP_LT;
	} else if (parseString(">=", true, false)) {
	    return Constants.COMP_GE;
	} else if (parseString(">", true, false)) {
	    return Constants.COMP_GT;
	} else {
	    return -1;
	}
    }

    private int NodeComp() throws MXQueryException {
	if (parseKeyword("is")) {
	    return Constants.COMP_EQ;
	} else if (parseString("<<", true, false)) {
	    return Constants.COMP_LT;
	} else if (parseString(">>", true, false)) {
	    return Constants.COMP_GT;
	} else {
	    return -1;
	}
    }

    private Iterator RangeExpr() throws MXQueryException {
	XDMIterator expr;
	if ((expr = AdditiveExpr()) != null) {
	    XDMIterator toExpr;
	    if (parseKeyword("to")) {
		if ((toExpr = AdditiveExpr()) != null) {
		    return new RangeIterator(getCurrentContext(),
			    new XDMIterator[] {
				    DataValuesIterator.getDataIterator(expr,
					    getCurrentContext()),
				    DataValuesIterator.getDataIterator(toExpr,
					    getCurrentContext()) },
			    getCurrentLoc());
		} else {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Additive Iterator expected after 'to'!");
		}
	    } else {
		return (Iterator) expr;
	    }
	}

	return null;
    }

    private Iterator AdditiveExpr() throws MXQueryException {
	Iterator iter;
	if ((iter = MultiplicativeExpr()) != null) {
	    boolean isAdditive = false;
	    Vector its = new Vector();
	    Vector conn = new Vector();

	    XDMIterator temp;
	    String connector;
	    while ((connector = parseStringGetResult("+", true)) != null
		    || (connector = parseStringGetResult("-", true)) != null) {
		isAdditive = true;
		if ((temp = MultiplicativeExpr()) == null) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "MultiplicativeExpr expected!");
		}
		if (its.size() == 0) {
		    its.addElement(iter);
		}
		conn.addElement(connector);
		its.addElement(temp);
	    }

	    if (isAdditive) {
		String[] c = new String[conn.size()];
		XDMIterator[] subs = new Iterator[its.size()];

		conn.copyInto(c);
		its.copyInto(subs);
		for (int i = 0; i < subs.length; i++) {
		    subs[i] = DataValuesIterator.getDataIterator(subs[i],
			    getCurrentContext());
		}
		return new AdditiveIterator(getCurrentContext(), c, subs,
			getCurrentLoc());
	    } else {
		return iter;
	    }
	}
	return null;
    }

    private Iterator MultiplicativeExpr() throws MXQueryException {
	Iterator iter;
	if ((iter = UnionExpr()) != null) {
	    boolean isMultiplicative = false;
	    Vector its = new Vector();
	    Vector conn = new Vector();

	    XDMIterator temp;
	    String connector;

	    while ((connector = parseStringGetResult("*", true)) != null
		    || (connector = parseStringGetResult("div", true)) != null
		    || (connector = parseStringGetResult("idiv", true)) != null
		    // it is necessary to check if the actual string don't start
		    // with "modi",
		    // else the keyword "modify" of the Transform Expression is
		    // never reached
		    || (!parseStringAndStay("modify", true, true) && (connector = parseStringGetResult(
			    "mod", true)) != null)) {

		// whitespace required before div, idiv, mod
		if ((connector.equals("idiv") || connector.equals("div") || connector
			.equals("mod"))
			&& !isSeparatorNext(index - connector.length() - 1)) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Whitespace before div, idiv and mod required!");
		}

		if (connector.equals("div") || connector.equals("idiv")
			|| connector.equals("mod")) {
		    if (!(isSeparatorNext(index))) {
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"Whitespace after div, idiv and mod required!");
		    }
		}

		isMultiplicative = true;
		if ((temp = UnionExpr()) == null) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Union expected!");
		}
		if (its.size() == 0) {
		    its.addElement(iter);
		}
		conn.addElement(connector.trim());
		its.addElement(temp);
	    }

	    if (isMultiplicative) {
		String[] c = new String[conn.size()];
		XDMIterator[] subs = new Iterator[its.size()];

		conn.copyInto(c);
		its.copyInto(subs);
		for (int i = 0; i < subs.length; i++) {
		    subs[i] = DataValuesIterator.getDataIterator(subs[i],
			    getCurrentContext());
		}
		return new MultiplicativeIterator(getCurrentContext(), c, subs,
			getCurrentLoc());
	    } else {
		return iter;
	    }
	}
	return null;
    }

    private Iterator UnionExpr() throws MXQueryException {

	Iterator iter = IntersectExceptExpr();
	if (iter != null) {
	    while (parseKeyword("union") || parseString("|", true, false)) {
		iter = new UnionIterator(getCurrentContext(), iter,
			IntersectExceptExpr(), getCurrentLoc());
	    }
	}
	return iter;
    }

    private Iterator IntersectExceptExpr() throws MXQueryException {
	Iterator iter = InstanceofExpr();
	boolean f1 = parseKeyword("intersect");
	boolean f2 = parseKeyword("except");
	while (f1 || f2) {
	    if (f1)
		iter = new IntersectIterator(getCurrentContext(), iter,
			InstanceofExpr(), getCurrentLoc());
	    else
		iter = new ExceptIterator(getCurrentContext(), iter,
			InstanceofExpr(), getCurrentLoc());
	    f1 = parseKeyword("intersect");
	    f2 = parseKeyword("except");
	}
	return iter;
    }

    private Iterator InstanceofExpr() throws MXQueryException {

	Iterator iter = TreatExpr();

	if (parseKeyword("instance")) {
	    if (parseKeyword("of")) {

		TypeInfo typeInfo = SequenceType();
		Iterator seqTypeIt = new SequenceTypeIterator(typeInfo, false,
			false, getCurrentContext(), getCurrentLoc()); // non
		// streaming
		seqTypeIt.setContext(getCurrentContext(), true);
		seqTypeIt.setSubIters(iter);

		return new InstanceOfIterator(getCurrentContext(), seqTypeIt,
			getCurrentLoc());

	    } else {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: 'of' expected!");
	    }
	}
	return iter;
    }

    private Iterator TreatExpr() throws MXQueryException {
	Iterator iter = CastableExpr();
	if (parseKeyword("treat")) {
	    if (parseKeyword("as")) {
		TypeInfo typeInfo = SequenceType();
		return new TreatAsIterator(getCurrentContext(), iter, typeInfo,
			getCurrentLoc());
	    } else {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: 'as' expected!");
	    }
	}
	return iter;
    }

    private TypeInfo SingleType() throws MXQueryException {
	QName typeName = QName();
	String namespacePrefix = typeName.getNamespacePrefix();
	if ((namespacePrefix == null)
		|| (!typeName.getNamespacePrefix().equals(Type.NAMESPACE_XS)))
	    typeName = rewriteUDTQNameWithResolvedPrefix(typeName);

	int type = Type.getTypeFootprint(typeName, Context.getDictionary());

	if (!Type.isAtomicType(type, Context.getDictionary()))
	    throw new StaticException(
		    ErrorCodes.E0051_STATIC_QNAME_AS_ATOMICTYPE_NOT_DEFINED_AS_ATOMIC,
		    "Type " + typeName + " is not a valid ayomic type!",
		    getCurrentLoc());

	int oi = Type.OCCURRENCE_IND_EXACTLY_ONE;

	if (parseString("?", true, false)) {
	    oi = Type.OCCURRENCE_IND_ZERO_OR_ONE;
	}

	TypeInfo t = new TypeInfo(type, oi, null, null);
	return t;
    }

    private Iterator CastableExpr() throws MXQueryException {

	XDMIterator iter = CastExpr();

	if (parseKeyword("castable")) {
	    if (parseKeyword("as")) {
		TypeInfo typeInfo = SingleType();
		iter = DataValuesIterator.getDataIterator(iter,
			getCurrentContext());
		return new CastAsIterator(getCurrentContext(), iter, typeInfo,
			true, false, getCurrentLoc());
	    } else {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: 'as' expected!");
	    }
	}
	return (Iterator) iter;
    }

    private Iterator CastExpr() throws MXQueryException {

	XDMIterator iter = UnaryExpr();

	if (parseKeyword("cast")) {
	    if (parseKeyword("as")) {
		TypeInfo typeInfo = SingleType();
		iter = DataValuesIterator.getDataIterator(iter,
			getCurrentContext());
		return new CastAsIterator(getCurrentContext(), iter, typeInfo,
			false, false, getCurrentLoc());
	    } else {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: 'as' expected!");
	    }
	}
	return (Iterator) iter;
    }

    private Iterator UnaryExpr() throws MXQueryException {
	boolean negative = false;
	String conn;
	while ((conn = parseStringGetResult("+", true)) != null
		|| (conn = parseStringGetResult("-", true)) != null) {
	    if (conn.equals("-")) {
		negative = !negative;
	    }
	}

	Iterator iter = ValueExpr();

	if (negative) {
	    String[] c = new String[1];
	    XDMIterator[] i = new Iterator[2];

	    c[0] = "*";
	    i[0] = new TokenIterator(getCurrentContext(), -1, Type.INTEGER,
		    getCurrentLoc());
	    i[1] = DataValuesIterator
		    .getDataIterator(iter, getCurrentContext());

	    return new MultiplicativeIterator(getCurrentContext(), c, i,
		    getCurrentLoc());
	} else {
	    return iter;
	}
    }

    private Iterator ValueExpr() throws MXQueryException {
	Iterator expr = null;
	if ((expr = ValidateExpr()) != null) {
	    return expr;
	} else if ((expr = PathExpr()) != null) {
	    return expr;
	} else if ((expr = ExtensionExpr()) != null) {
	    return expr;
	}
	return null;
    }

    private Iterator ValidateExpr() throws MXQueryException {
	int oldIndex = index;
	if (parseKeyword("validate")) {
	    int mode = Context.SCHEMA_VALIDATION_STRICT;
	    if (parseKeyword("lax")) {
		mode = Context.SCHEMA_VALIDATION_LAX;
	    } else if (parseKeyword("strict")) {
		mode = Context.SCHEMA_VALIDATION_STRICT;
	    }

	    if (!parseString("{", true, false)) {
		index = oldIndex;
		return null;
	    }
	    inValidateExpr = true;
	    Iterator exIt = Expr();
	    inValidateExpr = false;
	    if (exIt != null) {
		if (!parseString("}", true, false)) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: '}' expected!");
		}
		return validate(exIt, mode);
	    } else {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: 'Expr' expected!");
	    }

	}
	return null;
    }

    protected Iterator validate(Iterator exprIterator, int mode)
	    throws MXQueryException, StaticException {
	generateStaticError(ErrorCodes.E0075_STATIC_VALIDATION_NOT_SUPPORTED,
		"ValidateExpr not supported in this version!");
	return null;
    }

    private Iterator PathExpr() throws MXQueryException {
	Iterator expr;
	if ((parseStringGetResult("//", true)) != null) {
	    if ((expr = RelativePathExpr()) != null) {
		return expr;
	    } else {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: RelativePathExpr expected!");
	    }
	} else if ((parseStringGetResult("/", true)) != null) {

	    if ((expr = RelativePathExpr()) != null) {
		return expr;
	    } else {
		// TODO: get context item
		return null;
	    }
	} else if ((expr = RelativePathExpr()) != null) {
	    return expr;
	}

	return null;
    }

    private Iterator RelativePathExpr() throws MXQueryException {
	Iterator iter;
	createNewContextScope();
	if ((iter = StepExpr()) != null) {
	    boolean isRelative = false;

	    Vector its = new Vector();
	    its.addElement(iter);
	    int numContexts = 0;
	    String slashDSlash;
	    while ((slashDSlash = parseStringGetResult("//", true)) != null
		    || (slashDSlash = parseStringGetResult("/", true)) != null) {
		if (slashDSlash.equals("//"))
		    descOrSelf = true;
		else
		    descOrSelf = false;
		isRelative = true;
		createNewContextScope();
		numContexts++;
		if ((iter = StepExpr()) == null) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "StepExpr expected!");
		}

		its.addElement(iter);
		descOrSelf = false;

	    }
	    for (int i = 0; i < numContexts; i++)
		removeContextScope();
	    if (isRelative) {
		removeContextScope();
		return getRelativePath(its);
	    } else {
		Iterator it = iter;
		if (iter instanceof RewriteExpression) {

		    RewriteExpression exp = (RewriteExpression) iter;
		    if (exp.getExpressionType() == RewriteExpression.FUNCTION)
			it = (Iterator) exp.getSubIters()[0];
		}

		if (iter.hasPredicates()) {
		    Iterator[] preds = (iter.getPredicates());

		    for (int j = 0; j < preds.length; j++) {
			it = generatePredicateIterator(it, preds[j]);
		    }
		    iter.removePredicates();
		}
		removeContextScope();
		return it;
	    }
	}
	removeContextScope();
	return null;
    }

    private Iterator getRelativePath(Vector its) throws MXQueryException {

	Iterator resIt = null;
	// states for duplicate elimination
	final int STATE_INIT = 0; // initial state
	final int STATE_CHILD = 1; // only child steps
	final int STATE_ONEDUP = 2; // one more not-child step and there are
	// duplicates
	final int STATE_DUP = 3; // duplicates may exist. Apply docorder
	/*
	 * TODO this state could be improved using a counter. the counts for
	 * child and parent steps need to match. however, if any other step
	 * occurs in between, the state should change to dup
	 */
	final int STATE_ONEDUP_NOSORT = 4; // only a parent step can get us
	// back to onedup. In this state,
	// sorting needs to be applied
	int state = STATE_INIT;

	for (int i = 0; i < its.size(); i++) {
	    Iterator currIt = (Iterator) its.elementAt(i);

	    // decide if doc order / duplicate elimination needs to be aplies
	    switch (state) {
	    case STATE_INIT:
		if (currIt instanceof ChildIterator) {
		    state = STATE_CHILD;
		} else if (currIt instanceof ParentIterator) {
		    state = STATE_INIT;
		} else if (currIt instanceof DescendantOrSelfIterator
			|| (currIt instanceof AttributeIterator && ((AttributeIterator) currIt)
				.isDescendantOrSelf())) {
		    state = STATE_ONEDUP;
		}
		break;
	    case STATE_CHILD:
		if (currIt instanceof ChildIterator) {
		    state = STATE_CHILD;
		} else if (currIt instanceof ParentIterator) {
		    state = STATE_DUP;
		} else if (currIt instanceof DescendantOrSelfIterator
			|| (currIt instanceof AttributeIterator && ((AttributeIterator) currIt)
				.isDescendantOrSelf())) {
		    state = STATE_ONEDUP;
		}
		break;
	    case STATE_ONEDUP:
		if (currIt instanceof ChildIterator) {
		    state = STATE_ONEDUP_NOSORT;
		} else if (currIt instanceof DescendantOrSelfIterator
			|| currIt instanceof ParentIterator
			|| (currIt instanceof AttributeIterator && ((AttributeIterator) currIt)
				.isDescendantOrSelf())) {
		    state = STATE_DUP;
		}
		break;
	    case STATE_ONEDUP_NOSORT:
		if (currIt instanceof ParentIterator) {
		    state = STATE_ONEDUP;
		} else {
		    state = STATE_DUP;
		}
		break;
	    case STATE_DUP:
		// fall through
	    default:
		// should actually not happen
		break;
	    }

	    // prepare for predicate rewrite

	    Iterator[] preds = null;
	    if (currIt.hasPredicates()) {
		preds = currIt.getPredicates();
		currIt.removePredicates();
	    }

	    if (currIt instanceof VariableIterator) {
		if (((VariableIterator) currIt).getVarQName().equals(
			Context.CONTEXT_ITEM)
			&& i != 0) {

		    resIt = new DocOrderIterator(resIt.getContext(), resIt,
			    resIt.getLoc());

		    continue;
		}
	    }

	    if (!(currIt instanceof ChildIterator
		    || currIt instanceof DescendantOrSelfIterator
		    || currIt instanceof ParentIterator
		    || currIt instanceof SelfAxisIterator || currIt instanceof AttributeIterator)
		    && i != 0) {
		// rewrite into FFLWOR/For to call the function for all the
		// bindings
		if (i == 0)
		    resIt = currIt;
		else {
		    Context fflworCtx = new Context(resIt.getContext());
		    Context forScope = new Context(currIt.getContext());
		    forScope.registerNewContextItem();

		    Vector orphans = getUnresolvedPathSteps(currIt);

		    if (orphans.size() == 1) {
			XDMIterator orph = (XDMIterator) orphans.elementAt(0);
			Iterator orphIt = new VariableIterator(orph
				.getContext(), Context.CONTEXT_ITEM, false,
				orph.getLoc());
			forScope.incVariableUse(Context.CONTEXT_ITEM);
			setOrphanedSubs(orph, orphIt);
		    }
		    if (orphans.size() > 1) {
			for (int j = 0; j < orphans.size(); j++) {
			    XDMIterator orph = (XDMIterator) orphans
				    .elementAt(j);

			    Iterator orphIt = new VariableIterator(orph
				    .getContext(), Context.CONTEXT_ITEM, false,
				    orph.getLoc());
			    forScope.incVariableUse(Context.CONTEXT_ITEM);
			    setOrphanedSubs(orph, orphIt);
			}
			// System.out.println("Multi-orphan");
		    }

		    // currIt.getContext().setParent(forScope);
		    currIt.setContext(forScope, true);
		    Iterator[] iters = null;

		    ForIterator forIt = new ForIterator(forScope,
			    new Iterator[] { resIt }, Context.CONTEXT_ITEM,
			    null, null, true, currIt.getLoc());
		    iters = new Iterator[] { forIt };
		    resIt = new FFLWORIterator(fflworCtx, iters, null, null,
			    currIt, currIt.getLoc());
		}
	    } else {
		//
		Vector orphans = getUnresolvedPathSteps(currIt);
		if (orphans.size() == 1) {
		    XDMIterator orph = (XDMIterator) orphans.elementAt(0);
		    if (i == 0 && resIt == null)
			resIt = new VariableIterator(getCurrentContext(),
				Context.CONTEXT_ITEM, false, orph.getLoc());
		    setOrphanedSubs(orph, resIt);
		    resIt = currIt;
		} else if (orphans.size() > 1) {
		    // Iterator commonBufferIt = null;
		    Iterator orphIt;
		    if (resIt instanceof VariableIterator) {
			for (int j = 0; j < orphans.size(); j++) {
			    XDMIterator orph = (XDMIterator) orphans
				    .elementAt(j);

			    QName varname = ((VariableIterator) resIt)
				    .getVarQName();
			    orphIt = new VariableIterator(orph.getContext(),
				    varname, false, orph.getLoc());
			    getCurrentContext().incVariableUse(varname);
			    setOrphanedSubs(orph, orphIt);
			}
			resIt = currIt;
		    } else {
			// rewrite in FFLOWR/Let for shared input
			Context fflworContext = new Context(resIt.getContext());
			Context letContext = new Context(fflworContext);
			QName anon = letContext.registerAnonymousVariable();
			LetIterator letIt = new LetIterator(letContext,
				new Iterator[] { resIt }, anon, null, resIt
					.getLoc());
			currIt.setContext(letContext, true);
			resIt = new FFLWORIterator(fflworContext,
				new Iterator[] { letIt }, null, null, currIt,
				resIt.getLoc());
			VariableHolder vh = letContext.getVariable(anon);
			for (int j = 0; j < orphans.size(); j++) {
			    XDMIterator orph = (XDMIterator) orphans
				    .elementAt(j);
			    setOrphanedSubs(orph, new VariableIterator(orph
				    .getContext(), anon, false, orph.getLoc()));
			    vh.incUseCounter();
			}
		    }

		} else {
		    resIt = currIt;
		}
	    }

	    // Rewrite predicates
	    if (preds != null) {
		for (int j = 0; j < preds.length; j++) {
		    resIt = generatePredicateIterator(resIt, preds[j]);
		}
	    }

	    // insert doc order iterator where necessary
	    if (!Type.isAtomicType(resIt.getStaticType().getType(), Context
		    .getDictionary())
		    && (state == STATE_DUP || state == STATE_ONEDUP_NOSORT)) {
		state = STATE_INIT;
		resIt = new DocOrderIterator(resIt.getContext(), resIt, resIt
			.getLoc());
	    }
	}

	return resIt;
    }

    private void setOrphanedSubs(XDMIterator orph, Iterator orphIt) {
	XDMIterator[] subs = orph.getAllSubIters();
	if (subs.length == 0)
	    orph.setSubIters(orphIt);
	else {
	    for (int k = 0; k < subs.length; k++)
		if (subs[k] instanceof VariableIterator
			&& ((VariableIterator) subs[k]).getVarQName().equals(
				Context.CONTEXT_ITEM)) {
		    subs[k] = orphIt;
		}
	}
    }

    private Iterator generatePredicateIterator(Iterator contextItem,
	    Iterator predicate) throws MXQueryException {

	// All positional predicates not based on a variable iterator should be
	// mapped to FFLOWR with fn:position
	// positional predicates on variable iterator get a (index-based)
	// predicate iterator

	if (predicate instanceof RangeIterator) // could be improved by looking
	    // if the return value is a
	    // sequence with more than one
	    // item
	    throw new TypeException(
		    ErrorCodes.F0028_INVALID_ARGUMENT_TYPE,
		    "Sequence with more than one entry not allowed in predicate",
		    getCurrentLoc());

	// Prepare special rewrite for fn:last()

	Context fflworCtx = new Context(contextItem.getContext());
	Context predCtx = new Context(fflworCtx);

	Vector orphs = getUnresolvedIters(predicate);
	Vector lasts = new Vector();
	for (int i = 0; i < orphs.size(); i++) {
	    Object o = orphs.elementAt(i);
	    if (o instanceof Last) {
		lasts.addElement(o);
	    } else {
		// check for Context-based path expression, add explicit
		// reference to context variable iterator
		// Iterator it = (Iterator)o;
		// Iterator [] subs = it.getAllSubIters();
		// if (subs == null || subs.length == 0)
		// it.setSubIters(new VariableIterator(predCtx,
		// Context.CONTEXT_ITEM));

	    }
	    // end

	}

	LetIterator let1 = null;
	LetIterator lastLet = null;
	QName var1 = null;
	if (!(contextItem instanceof VariableIterator && (Type
		.isTypeOrSubTypeOf(predicate.getStaticType().getType(),
			Type.NUMBER, Context.getDictionary())
		|| predicate instanceof VariableIterator
		|| predicate instanceof AdditiveIterator || predicate instanceof MultiplicativeIterator))) {
	    for (int i = 0; i < lasts.size(); i++) {
		Object o = lasts.elementAt(i);
		QName letLast = new QName(".let", ".last");
		if (let1 == null) {
		    var1 = predCtx.registerAnonymousVariable();
		    let1 = new LetIterator(predCtx,
			    new Iterator[] { contextItem }, var1, null,
			    predicate.getLoc());
		    CountIterator lastCount = new CountIterator();
		    lastCount.setSubIters(new VariableIterator(predCtx, var1,
			    false, predicate.getLoc()));
		    lastCount.setContext(predCtx, true);
		    predCtx = new Context(predCtx);
		    predCtx.registerVariable(letLast, true);
		    lastLet = new LetIterator(predCtx,
			    new Iterator[] { lastCount }, letLast, null,
			    predicate.getLoc());

		}
		((XDMIterator) o).setSubIters(new VariableIterator(predCtx,
			letLast, false, predicate.getLoc()));
		predCtx.incVariableUse(letLast);
	    }
	}
	// FIXME:For variables, additive and multiplicative iterators, a numeric
	// return type is assumed
	// Static information should be exploited better, and there is also the
	// need to do some dynamic plan rewriting here
	if (Type.isTypeOrSubTypeOf(predicate.getStaticType().getType(),
		Type.NUMBER, null)
		|| predicate instanceof VariableIterator
		|| predicate instanceof AdditiveIterator
		|| predicate instanceof MultiplicativeIterator) {
	    if (contextItem instanceof VariableIterator) {
		if (lasts.size() > 0) {
		    let1 = null;
		    for (int i = 0; i < lasts.size(); i++) {
			Object o = lasts.elementAt(i);
			QName letLast = new QName(".let", ".last");
			if (let1 == null) {
			    predCtx.registerVariable(letLast, true);

			    CountIterator lastCount = new CountIterator();
			    lastCount.setSubIters(new VariableIterator(predCtx,
				    ((VariableIterator) contextItem)
					    .getVarQName(), false, predicate
					    .getLoc()));
			    lastCount.setContext(predCtx, true);
			    lastLet = new LetIterator(predCtx,
				    new Iterator[] { lastCount }, letLast,
				    null, predicate.getLoc());

			}
			((XDMIterator) o).setSubIters(new VariableIterator(
				predCtx, letLast, false, predicate.getLoc()));
			predCtx.incVariableUse(letLast);
		    }

		    Iterator[] its = { lastLet };

		    Iterator pred1 = new PredicateIterator(predCtx,
			    new Iterator[] { contextItem, predicate },
			    getCurrentLoc());

		    return new FFLWORIterator(fflworCtx, its, null, null,
			    pred1, predicate.getLoc());
		}
		// getCurrentContext().registerVariable(new
		// QName(".let",".last"), false, contextItem);
		return new PredicateIterator(getCurrentContext(),
			new Iterator[] { contextItem, predicate },
			getCurrentLoc());
	    } else {

		Context forScope = new Context(predCtx);
		forScope.registerNewContextItem();
		Iterator pos = new Position();
		pos.setContext(forScope, true);
		predicate.setContext(forScope, true);

		for (int i = 0; i < orphs.size(); i++) {
		    Object o = orphs.elementAt(i);
		    XDMIterator it = (XDMIterator) o;
		    setOrphanedSubs(it, new VariableIterator(forScope,
			    Context.CONTEXT_ITEM, false, predicate.getLoc()));
		}

		Iterator[] its = null;

		Iterator pred1 = new BooleanIterator(forScope,
			new Iterator[] { new CompareIterator(forScope,
				Constants.COMP_VALUE, Constants.COMP_EQ,
				new Iterator[] { pos, predicate }, predicate
					.getLoc()) }, predicate.getLoc());

		if (let1 != null) {
		    ForIterator forIt = new ForIterator(forScope,
			    new Iterator[] { new VariableIterator(predCtx,
				    var1, false, predicate.getLoc()) },
			    Context.CONTEXT_ITEM, null, null, true, predicate
				    .getLoc());
		    predCtx.incVariableUse(var1);
		    its = new Iterator[] { let1, lastLet, forIt };
		} else {
		    ForIterator forIt = new ForIterator(forScope,
			    new Iterator[] { contextItem },
			    Context.CONTEXT_ITEM, null, null, true, predicate
				    .getLoc());
		    its = new Iterator[] { forIt };
		}

		VariableIterator varIt = new VariableIterator(forScope,
			Context.CONTEXT_ITEM, false, predicate.getLoc());

		return new FFLWORIterator(fflworCtx, its, pred1, null, varIt,
			predicate.getLoc());

	    }
	} else {
	    Context forScope = new Context(predCtx);
	    forScope.registerNewContextItem();
	    // if (!(predicate instanceof FFLWORIterator))
	    predicate.setContext(forScope, true);

	    for (int i = 0; i < orphs.size(); i++) {
		Object o = orphs.elementAt(i);
		XDMIterator it = (XDMIterator) o;
		setOrphanedSubs(it, new VariableIterator(forScope,
			Context.CONTEXT_ITEM, false, predicate.getLoc()));
	    }

	    Iterator[] its = null;
	    if (let1 != null) {
		ForIterator forIt = new ForIterator(forScope,
			new Iterator[] { new VariableIterator(predCtx, var1,
				false, predicate.getLoc()) },
			Context.CONTEXT_ITEM, null, null, true, predicate
				.getLoc());
		predCtx.incVariableUse(var1);
		its = new Iterator[] { let1, lastLet, forIt };
	    } else {
		ForIterator forIt = new ForIterator(forScope,
			new Iterator[] { contextItem }, Context.CONTEXT_ITEM,
			null, null, true, predicate.getLoc());
		its = new Iterator[] { forIt };
	    }
	    VariableIterator varIt = new VariableIterator(forScope,
		    Context.CONTEXT_ITEM, false, predicate.getLoc());
	    predicate = (Iterator) BooleanIterator.createEBVIterator(predicate,
		    its[its.length - 1].getContext());
	    Iterator iter = new FFLWORIterator(fflworCtx, its, predicate, null,
		    varIt, predicate.getLoc());
	    return iter;
	}
    }

    private Iterator StepExpr() throws MXQueryException {
	Iterator expr;
	if ((expr = FilterExpr()) != null) {
	    return expr;
	} else if ((expr = AxisStep()) != null) {
	    return expr;
	}

	return null;
    }

    /**
     * 
     * 
     */
    private Vector getUnresolvedPathSteps(XDMIterator it) {
	Vector res = new Vector();
	XDMIterator[] subs = it.getAllSubIters();
	if (it instanceof ChildIterator
		|| it instanceof DescendantOrSelfIterator
		|| it instanceof AttributeIterator
		|| it instanceof ParentIterator
		|| it instanceof SelfAxisIterator) {
	    if (subs == null || subs.length == 0) {
		res.addElement(it);
		return res;
	    }
	}
	for (int j = 0; j < subs.length; j++) {
	    Vector cands = getUnresolvedPathSteps(subs[j]);
	    for (int k = 0; k < cands.size(); k++)
		res.addElement(cands.elementAt(k));
	}
	return res;
    }

    private Vector getUnresolvedIters(XDMIterator it) {
	Vector res = new Vector();
	XDMIterator[] subs = it.getAllSubIters();
	if (subs == null || subs.length == 0
		&& !(it instanceof VariableIterator)
		&& !(it instanceof TokenIterator) && !(it instanceof Position)) {
	    res.addElement(it);
	    return res;
	}
	for (int j = 0; j < subs.length; j++) {
	    if ((subs[j] instanceof VariableIterator)
		    && ((VariableIterator) subs[j]).getVarQName().equals(
			    Context.CONTEXT_ITEM)
		    && !(it instanceof FFLWORIterator))
		res.addElement(it);
	    if (!(it instanceof FFLWORIterator) || j < subs.length - 2) { // on
		// an
		// FFLOWR,
		// only
		// look
		// at
		// the
		// FFLO
		// part,
		// not
		// where,
		// return
		Vector cands = getUnresolvedIters(subs[j]);
		for (int k = 0; k < cands.size(); k++) {
		    XDMIterator itk = (XDMIterator) cands.elementAt(k);
		    if (!(itk instanceof VariableIterator)
			    || ((VariableIterator) itk).getVarQName().equals(
				    Context.CONTEXT_ITEM))
			res.addElement(itk);
		}
	    }
	}
	return res;
    }

    private Iterator FilterExpr() throws MXQueryException {
	Iterator iter;
	int oldIndex = index;

	if ((iter = PrimaryExpr()) != null) {
	    Vector ps = new Vector();
	    while (parseString("[", true, false)) {
		XDMIterator temp = null;
		inPredicate = true;
		if ((temp = Expr()) == null) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: Expr expected!");
		} else {
		    if (!parseString("]", true, false)) {
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"']' expected!");
		    }
		    inPredicate = false;
		    // check for Context-based path expression, add explicit
		    // reference to context variable iterator
		    Vector orphans = getUnresolvedPathSteps(temp);
		    for (int i = 0; i < orphans.size(); i++) {
			XDMIterator it = (XDMIterator) orphans.elementAt(i);
			XDMIterator[] subs = it.getAllSubIters();
			if (subs == null || subs.length == 0)
			    it.setSubIters(new VariableIterator(
				    getCurrentContext(), Context.CONTEXT_ITEM,
				    false, getCurrentLoc()));
		    }
		    // end

		    ps.addElement(temp);
		}
	    }

	    if (ps.size() > 0) {
		Iterator[] i = new Iterator[ps.size()];
		ps.copyInto(i);
		iter.addPredicates(i);
	    }

	    return iter;
	}

	index = oldIndex;
	return null;
    }

    private Iterator AxisStep() throws MXQueryException {
	Iterator iter;

	if ((iter = ReverseStep()) != null || (iter = ForwardStep()) != null) {
	    Vector ps = new Vector();

	    while (parseString("[", true, false)) {
		XDMIterator temp = null;
		inPredicate = true;
		if ((temp = Expr()) == null) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: Expr expected!");
		} else {
		    if (!parseString("]", true, false)) {
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"']' expected!");
		    }
		    inPredicate = false;
		    // // check for Context-based path expression, add explicit
		    // reference to context variable iterator
		    // Vector orphans = getUnresolvedPathSteps(temp);
		    // for (int i= 0; i< orphans.size();i++) {
		    // Iterator it = (Iterator)orphans.elementAt(i);
		    // Iterator [] subs = it.getAllSubIters();
		    // if (subs == null || subs.length == 0)
		    // it.setSubIters(new VariableIterator(getCurrentContext(),
		    // Context.CONTEXT_ITEM));
		    // }
		    // // end

		    ps.addElement(temp);
		}
	    }

	    if (ps.size() > 0) {
		Iterator[] i = new Iterator[ps.size()];
		ps.copyInto(i);
		iter.addPredicates(i);
	    }
	    return iter;
	}

	return null;
    }

    private Iterator ReverseStep() throws MXQueryException {
	String step;
	int oldIndex = index;

	if (parseString("..", true, false)) {
	    if (isSeparatorNext(index)) {
		TypeInfo stepData = new TypeInfo(Type.TYPE_NK_ANY_NODE_TEST,
			Type.OCCURRENCE_IND_EXACTLY_ONE, null, null);
		return genIteratorFromPathStep(stepData, true, false);
	    } else {
		throw new StaticException(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			".. is not a valid grammar element at this point!",
			getCurrentLoc());
	    }

	} else {

	    if ((step = parseStringGetResult("parent::", false)) != null) {
		TypeInfo stepData = NodeTest();
		if (stepData != null)
		    return genIteratorFromPathStep(stepData, true, false);
		else
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "NodeTest expected");
	    } else if ((step = parseStringGetResult("ancestor::", false)) != null) {
		generateStaticError(ErrorCodes.A0002_EC_NOT_SUPPORTED, "'"
			+ step + "' not supported yet!");
	    } else if ((step = parseStringGetResult("preceding-sibling::",
		    false)) != null) {
		generateStaticError(ErrorCodes.A0002_EC_NOT_SUPPORTED, "'"
			+ step + "' not supported yet!");
	    } else if ((step = parseStringGetResult("preceding::", false)) != null) {
		generateStaticError(ErrorCodes.A0002_EC_NOT_SUPPORTED, "'"
			+ step + "' not supported yet!");
	    } else if ((step = parseStringGetResult("ancestor-or-self::", false)) != null) {
		generateStaticError(ErrorCodes.A0002_EC_NOT_SUPPORTED, "'"
			+ step + "' not supported yet!");
	    }
	}

	index = oldIndex;
	return null;
    }

    private Iterator ForwardStep() throws MXQueryException {
	String step = null;

	TypeInfo stepData;

	boolean descendantOrSelf = false;
	if ((index > 2 && query.substring(index - 2).startsWith("//"))
		|| (descOrSelf == true && !inPredicate)) {
	    descendantOrSelf = true;
	}

	if (parseString("@", true, false)) {

	    // -- attribute QName-Wildcard
	    if ((stepData = NodeTest()) != null) {
		return new AttributeIterator(getCurrentContext(), stepData,
			descendantOrSelf, getCurrentLoc());
	    }

	} else if ((stepData = NodeTest()) != null) {

	    return genIteratorFromPathStep(stepData, false, descendantOrSelf);

	} else {
	    if ((step = parseStringGetResult("child::", false)) != null) {
		if (descendantOrSelf) {
		    // child::... => //...
		    stepData = NodeTest();
		    return genIteratorFromPathStep(stepData, false,
			    descendantOrSelf);
		} else
		    // should this also change?

		    return StepExpr();

	    } else if ((step = parseStringGetResult("descendant::", false)) != null) {

		if ((stepData = NodeTest()) != null) {
		    return new DescendantOrSelfIterator(getCurrentContext(),
			    stepData, getCurrentLoc(),
			    DescendantOrSelfIterator.DESC_AXIS_DESCENDANT);
		}
		// //TODO: Make the right implementation: // and descendant are
		// not the same
		// //
		// descOrSelf = true;
		// Iterator iter = RelativePathExpr();
		// descOrSelf = false;
		// return iter;
		// generateStaticError(StaticException.NOT_SUPPORTED_ERROR, "#",
		// "'" + step + "' not supported yet!");
	    } else if ((step = parseStringGetResult("attribute::", false)) != null) {
		if ((stepData = NodeTest()) != null) {
		    return new AttributeIterator(getCurrentContext(), stepData,
			    descendantOrSelf, getCurrentLoc());
		}
	    } else if ((step = parseStringGetResult("self::", false)) != null) {
		if ((stepData = NodeTest()) != null) {
		    if (descendantOrSelf) {
			return new DescendantOrSelfIterator(
				getCurrentContext(),
				stepData,
				getCurrentLoc(),
				DescendantOrSelfIterator.DESC_AXIS_DESCENDANT_OR_SELF);
		    } else
			return new SelfAxisIterator(getCurrentContext(),
				stepData, getCurrentLoc());
		}
	    } else if ((step = parseStringGetResult("descendant-or-self::",
		    false)) != null) {

		if ((step = parseStringGetResult("node()/", false)) != null) {
		    descOrSelf = true;
		    Iterator iter = RelativePathExpr();
		    descOrSelf = false;
		    return iter;
		} else {
		    if ((stepData = NodeTest()) != null) {
			return new DescendantOrSelfIterator(
				getCurrentContext(),
				stepData,
				getCurrentLoc(),
				DescendantOrSelfIterator.DESC_AXIS_DESCENDANT_OR_SELF);
		    }
		}

	    } else if ((step = parseStringGetResult("following-sibling::",
		    false)) != null) {
		generateStaticError(ErrorCodes.A0002_EC_NOT_SUPPORTED, "'"
			+ step + "' not supported yet!");
	    } else if ((step = parseStringGetResult("following::", false)) != null) {
		generateStaticError(ErrorCodes.A0002_EC_NOT_SUPPORTED, "'"
			+ step + "' not supported yet!");
	    } else if (parseStringAndStay("(#", true, false)) {
		return null;
	    } else if ((step = parseStringGetResult("(", false)) != null) {
		generateStaticError(ErrorCodes.A0002_EC_NOT_SUPPORTED, "'"
			+ step + "' in ForwardStep not supported yet!");
	    }

	}
	return null;
    }

    private Iterator genIteratorFromPathStep(TypeInfo stepData,
	    boolean backwards, boolean descendantOrSelf)
	    throws MXQueryException {
	// -- attribute kind test
	// if ( Type.isAttribute(stepData.getType()))

	if (Type.isAttribute(stepData.getType())) {
	    return new AttributeIterator(getCurrentContext(), stepData,
		    descendantOrSelf, getCurrentLoc());
	}

	if (stepData.isUndefined()) {
	    // -- element QName-Wildcard
	    // stepData.setType(Type.TYPE_NK_ELEM_TEST);
	    stepData.setType(Type.START_TAG);
	}

	if (backwards) {
	    return new ParentIterator(getCurrentContext(), stepData,
		    getCurrentLoc());
	}

	// -- Element / PI / comment / text node / any node kind tests
	if (descendantOrSelf)
	    return new DescendantOrSelfIterator(getCurrentContext(), stepData,
		    getCurrentLoc(),
		    DescendantOrSelfIterator.DESC_AXIS_SLASHSLASH);
	else
	    return new ChildIterator(getCurrentContext(), stepData,
		    getCurrentLoc());

    }

    private TypeInfo NodeTest() throws MXQueryException {
	TypeInfo stepData;
	QName qname;
	Wildcard wc;
	if ((stepData = KindTest()) != null) {
	    return stepData;
	} else if ((wc = Wildcard()) != null) {
	    stepData = new TypeInfo();
	    stepData.setName(wc.toString());
	    return stepData;
	} else if ((qname = QName()) != null) {
	    stepData = new TypeInfo();
	    stepData.setName(qname.toString());
	    String defaultNS = getCurrentContext().getDefaultElementNamespace();
	    if (defaultNS != null)
		stepData.setNameSpaceURI(defaultNS);
	    return stepData;
	}

	return null;
    }

    private TypeInfo KindTest() throws MXQueryException {
	TypeInfo temp;
	if ((temp = DocTest()) != null) {
	    return temp;
	} else if ((temp = ElementTest()) != null) {
	    return temp;
	} else if ((temp = AttributeTest()) != null) {
	    return temp;
	} else if ((temp = SchemaElementTest()) != null) {
	    return temp;
	} else if ((temp = SchemaAttrTest()) != null) {
	    return temp;
	} else if ((temp = PITest()) != null) {
	    return temp;
	} else if ((temp = CommentTest()) != null) {
	    return temp;
	} else if ((temp = TextTest()) != null) {
	    return temp;
	} else if ((temp = AnyKindTest()) != null) {
	    return temp;
	}
	return null;
    }

    private TypeInfo DocTest() throws MXQueryException {
	int oldIndex = index;
	TypeInfo stepData;

	if (parseKeyword("document-node")) {
	    if (!parseString("(", true, false)) {
		index = oldIndex;
		return null;
	    }

	    // FIXME: Currently, the element/schema-element test is parsed, but
	    // cannot be evaluated

	    TypeInfo innerStepData = ElementTest();
	    if (innerStepData == null) {
		innerStepData = SchemaElementTest();
	    }

	    if (!parseString(")", true, false)) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: ')' expected!");
	    }
	    if (innerStepData == null) {
		stepData = new TypeInfo();
		stepData.setType(Type.TYPE_NK_DOC_TEST);
		return stepData;
	    } else {
		innerStepData.setType(innerStepData.getType()
			| Type.TYPE_NK_DOC_TEST);
		return innerStepData;
	    }
	}
	index = oldIndex;
	return null;
    }

    protected TypeInfo ElementTest() throws MXQueryException {
	int oldIndex = index;
	TypeInfo stepData;

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
			    stepData.setType(Type.getTypeFootprint(q, null));
			} catch (MXQueryException me) {
			    throw new StaticException(
				    ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,
				    "Type not available for element check",
				    getCurrentLoc());
			}
			// generateStaticError(ErrorCodes.A0002_EC_NOT_SUPPORTED,
			// "ElementTest with 'type' not supported yet!");
		    }
		    if (parseString("?", true, false)) {
			generateStaticError(ErrorCodes.A0002_EC_NOT_SUPPORTED,
				"ElementTest with 'type ?' not supported yet!");
		    }
		}
	    } else if ((qname = QName()) != null) {
		String namespacePrefix = qname.getNamespacePrefix();

		Namespace ns = getCurrentContext()
			.getNamespace(namespacePrefix);
		if (ns != null)
		    stepData.setNameSpaceURI(ns.getURI());
		else
		    generateStaticError(
			    ErrorCodes.E0081_STATIC_QUERY_UNKNOWN_NAMESPACE,
			    "Prefix " + qname.getNamespacePrefix()
				    + " not bound");
		stepData.setName(qname.toString());

		if (parseString(",", true, false)) {
		    QName q = QName();
		    if (q == null) {
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"Error while parsing: 'QName' expected!");
		    } else {
			try {
			    stepData.setType(Type.getTypeFootprint(q, null));
			} catch (MXQueryException me) {
			    throw new TypeException(
				    ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,
				    "Type not available for element check",
				    getCurrentLoc());
			}
		    }
		}
	    }

	    if (!parseString(")", true, false)) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: ')' expected!");
	    }
	    stepData.setType(Type.START_TAG);
	    return stepData;
	}

	index = oldIndex;
	return null;
    }

    protected TypeInfo AttributeTest() throws MXQueryException {
	int oldIndex = index;
	TypeInfo stepData;

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
			generateStaticError(ErrorCodes.A0002_EC_NOT_SUPPORTED,
				"AttributeTest with type not supported yet!");
		    }
		}
	    } else if ((qname = QName()) != null) {
		stepData.setName(qname.toString());
		if (parseString(",", true, false)) {
		    QName q = QName();
		    if (q == null) {
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"Error while parsing: 'QName' expected!");
		    } else {
			generateStaticError(ErrorCodes.A0002_EC_NOT_SUPPORTED,
				"AttributeTest with type not supported yet!");
		    }
		}
	    }

	    if (!parseString(")", true, false)) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: ')' expected!");
	    }
	    stepData.setType(Type.TYPE_NK_ATTR_TEST);
	    // stepData.setType(Type.createAttributeType(0));
	    return stepData;
	}

	index = oldIndex;
	return null;
    }

    protected TypeInfo SchemaElementTest() throws MXQueryException {
	int oldIndex = index;
	if (parseKeyword("schema-element")) {
	    if (!parseString("(", true, false)) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: '(' expected!");
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
	    generateStaticError(ErrorCodes.A0002_EC_NOT_SUPPORTED,
		    "SchemaElementTest not supported yet!");
	}
	index = oldIndex;
	return null;
    }

    protected TypeInfo SchemaAttrTest() throws MXQueryException {
	int oldIndex = index;
	if (parseKeyword("schema-attribute")) {
	    if (!parseString("(", true, false)) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: '(' expected!");
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
	    generateStaticError(ErrorCodes.A0002_EC_NOT_SUPPORTED,
		    "SchemaAttributeTest not supported yet!");
	}
	index = oldIndex;
	return null;
    }

    private TypeInfo PITest() throws MXQueryException {

	int oldIndex = index;
	TypeInfo stepData;

	if (parseString("processing-instruction", true, false)
		&& parseString("(", true, false)) {

	    stepData = new TypeInfo();
	    stepData.setType(Type.PROCESSING_INSTRUCTION);

	    String name;
	    if ((name = StringLiteralAsString()) != null) {
		stepData.setName(name);
	    } else if ((name = NCName()) != null)
		stepData.setName(name);

	    if (!parseString(")", true, false))
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"')' expected!");

	    return stepData;
	}

	index = oldIndex;
	return null;
    }

    private TypeInfo CommentTest() throws MXQueryException {

	int oldIndex = index;
	TypeInfo stepData;

	if (parseString("comment", true, false)
		&& parseString("(", true, false)) {

	    stepData = new TypeInfo();
	    stepData.setType(Type.COMMENT);

	    if (!parseString(")", true, false))
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"')' expected!");

	    return stepData;
	}

	index = oldIndex;
	return null;
    }

    private TypeInfo TextTest() throws MXQueryException {

	int oldIndex = index;
	TypeInfo stepData;

	if (parseString("text", true, false) && parseString("(", true, false)) {

	    stepData = new TypeInfo();
	    stepData.setType(Type.TYPE_NK_TEXT_TEST);

	    if (!parseString(")", true, false)) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: ')' expected!");
	    }

	    return stepData;
	}

	index = oldIndex;
	return null;
    }

    private TypeInfo AnyKindTest() throws MXQueryException {

	int oldIndex = index;
	TypeInfo stepData;

	if (parseString("node", true, false) && parseString("(", true, false)) {

	    stepData = new TypeInfo();
	    stepData.setType(Type.TYPE_NK_ANY_NODE_TEST);

	    if (!parseString(")", true, false)) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: ')' expected!");
	    }

	    return stepData;
	}

	index = oldIndex;
	return null;
    }

    private Iterator ExtensionExpr() throws MXQueryException {
	boolean found = false;

	skipComments();
	while (parseString("(#", true, false)) {
	    found = true;
	    pragma = true;
	    // parse (#x20 | #x9 | #xD | #xA)*

	    QName q = QName();

	    if (q == null) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: 'QName' expected!");
	    }

	    XQStaticContext ctx = getCurrentContext();
	    Namespace ns = ctx.getNamespace(q.getNamespacePrefix());
	    if (ns == null) {
		generateStaticError(
			ErrorCodes.E0081_STATIC_QUERY_UNKNOWN_NAMESPACE,
			"Pragma namespace unknown!");
	    }

	    // parse (#x20 | #x9 | #xD | #xA)*

	    boolean startContent = true;
	    int oldIndex = index;
	    if (parseString("#)", true, false)) {
		// doNothing
	    } else {
		index = oldIndex;
		while (!parseString("#)", false, false)) {
		    if (startContent) {
			if (!isWhitespace()) {
			    generateStaticError(
				    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				    "Error while parsing: Whitespace required before pragma content!");
			}
			startContent = false;
		    }
		    if (index >= query.length() - 2) {
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"Error while parsing: '#)' expected!");
		    }
		    index++;
		}
	    }
	}

	if (found) {
	    if (parseString("{}", true, true)) {
		generateStaticError(
			ErrorCodes.E0079_STATIC_EXTENSION_EXPRESSION_DOESNT_CONTAIN_PRAGMA_OR_CURLY_CONTENT,
			"Pragma Expression expected!");
	    }

	    skipComments();
	    Iterator expr = EnclosedExpr(false);

	    pragma = false;
	    return expr;
	}

	pragma = false;
	return null;
    }

    private Iterator PrimaryExpr() throws MXQueryException {
	Iterator expr = null;
	if ((expr = Literal()) != null) {
	    return expr;
	} else if ((expr = VarRef()) != null) {
	    return expr;
	} else if ((expr = ParenthesizedExpr()) != null) {
	    return expr;
	} else if ((expr = ContextItemExpr()) != null) {
	    return expr;
	} else if ((expr = Constructor()) != null) {
	    return expr;
	} else if ((expr = OrderedExpr()) != null) {
	    return expr;
	} else if ((expr = UnorderedExpr()) != null) {
	    return expr;
	} else if ((expr = FunctionCall()) != null) {
	    return expr;
	} else if ((expr = BlockExpr()) != null) {
	    return expr;
	} else {
	    return null;
	}
    }

    private Iterator Literal() throws MXQueryException {
	Iterator expr = null;
	if ((expr = NumericLiteral()) != null) {
	    return expr;
	} else if ((expr = StringLiteral()) != null) {
	    return expr;
	} else {
	    return null;
	}
    }

    private Iterator NumericLiteral() throws MXQueryException {
	int oldIndex = index;

	String number;
	String fraction;
	String e = "";
	String sign = null;

	int t = Type.DECIMAL;
	if ((number = Digits()) != null) {
	    if (parseString(".", false, false)) {
		// double
		fraction = Digits();
		if (fraction == null)
		    fraction = "";
		if (parseString("e", false, false)
			|| parseString("E", false, false)) {
		    if ((sign = parseStringGetResult("-", false)) == null) {
			sign = parseStringGetResult("+", false);
			if (sign == null) {
			    sign = "";
			}
		    }

		    if (isSeparatorNext(index)) {
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"No whitespace within Double value allowed!");
		    }

		    String exp = Digits();

		    if (exp == null) {
			return null;
		    }
		    e = "E" + sign + exp;
		    t = Type.DOUBLE;
		}

		switch (t) {
		case Type.DOUBLE:
		case Type.FLOAT:
		    return new TokenIterator(getCurrentContext(),
			    new MXQueryDouble(number + "." + fraction + e), t,
			    getCurrentLoc());
		case Type.DECIMAL:
		    return new TokenIterator(getCurrentContext(),
			    new MXQueryBigDecimal(number + "." + fraction + e),
			    t, getCurrentLoc());
		}

	    } else {
		// integer
		if (parseString("e", false, false)
			|| parseString("E", false, false)) {
		    if ((sign = parseStringGetResult("-", false)) == null) {
			sign = parseStringGetResult("+", false);
			if (sign == null) {
			    sign = "";
			}
		    }

		    if (isSeparatorNext(index)) {
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"No whitespace within Double value allowed!");
		    }

		    String exp = Digits();

		    if (exp == null) {
			return null;
		    }
		    e = "E" + sign + exp;
		    t = Type.DOUBLE;

		    switch (t) {
		    case Type.DOUBLE:
		    case Type.FLOAT:
			return new TokenIterator(getCurrentContext(),
				new MXQueryDouble(number + e), t,
				getCurrentLoc());
		    case Type.DECIMAL:
			return new TokenIterator(getCurrentContext(),
				new MXQueryBigDecimal(number + e), t,
				getCurrentLoc());
		    }

		}
		try {

		    long longResult = Long.parseLong(number);
		    Iterator res = new TokenIterator(getCurrentContext(),
			    longResult, Type.INTEGER, getCurrentLoc());
		    res.setContext(getCurrentContext(), true);
		    return res;

		} catch (NumberFormatException numInt) {
		    generateStaticError(
			    ErrorCodes.F0003_OVERFLOW_UNDERFLOW_NUMERIC,
			    "Long values exceeding the java long space are currently not supported!");
		}

	    }
	} else if (parseString(".", false, false)) {
	    if ((fraction = Digits()) != null) {
		// double eg.: .5
		if (parseString("e", false, false)
			|| parseString("E", false, false)) {
		    if ((sign = parseStringGetResult("-", false)) == null) {
			sign = parseStringGetResult("+", false);
			if (sign == null) {
			    sign = "";
			}
		    }

		    if (isSeparatorNext(index)) {
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"No whitespace within Double value allowed!");
		    }

		    String exp = Digits();

		    if (exp == null) {
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"Digit Exponent expected!");
		    }
		    e = "E" + sign + exp;
		    t = Type.DOUBLE;
		}

		switch (t) {
		case Type.DOUBLE:
		case Type.FLOAT:
		    return new TokenIterator(getCurrentContext(),
			    new MXQueryDouble("." + fraction + e), t,
			    getCurrentLoc());
		case Type.DECIMAL:
		    return new TokenIterator(getCurrentContext(),
			    new MXQueryBigDecimal("." + fraction + e), t,
			    getCurrentLoc());
		}

	    } else {
		index = oldIndex;
	    }
	}
	return null;
    }

    private String StringLiteralAsString() throws MXQueryException {
	StringBuffer item = new StringBuffer();
	String temp = ""; // empty String initialization only for while loop.
	// Is set to null later.

	if (parseStringGetResult("\"", true) != null) {
	    while (temp != null) {
		temp = null;
		if ((temp = CharRef()) != null) {
		    item.append(resolveCharRef(temp, false));
		} else if ((temp = PredefinedEntityRef(false)) != null) {
		    item.append(temp);
		} else if ((temp = parseStringGetResult("\"\"", false)) != null) {
		    item.append("\"");
		} else if ((temp = quotString()) != null) {
		    item.append(temp);
		}
	    }
	    if (parseStringGetResult("\"", false) != null) {
		return item.toString();
	    } else {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"'\"' expected!");
	    }
	} else if (parseStringGetResult("'", true) != null) {
	    while (temp != null) {
		temp = null;
		if ((temp = CharRef()) != null) {
		    item.append(resolveCharRef(temp, false));
		} else if ((temp = PredefinedEntityRef(false)) != null) {
		    item.append(temp);
		} else if ((temp = parseStringGetResult("''", false)) != null) {
		    item.append("'");
		} else if ((temp = aposString()) != null) {
		    item.append(temp);
		}
	    }
	    if (parseStringGetResult("'", false) != null) {
		return item.toString();
	    } else {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"\"'\" expected!");
	    }
	} else {
	    return null;
	}
	return null;
    }

    // private Iterator StringLiteral() throws MXQueryException {
    // String sl = StringLiteralAsString();
    // if (sl == null) {
    // return null;
    // } else {
    // TokenIterator v = TokenIterator.createIterator(sl.toString());
    // v.setEnclosed(true);
    // return v;
    // }
    // }

    private Iterator StringLiteral() throws MXQueryException {
	StringBuffer item = new StringBuffer();
	String temp = ""; // empty String initialization only for while loop.
	// Is set to null later.

	if (parseStringGetResult("\"", true) != null) {
	    while (temp != null) {
		temp = null;
		if ((temp = CharRef()) != null) {
		    item.append(resolveCharRef(temp, false));
		} else if ((temp = PredefinedEntityRef(false)) != null) {
		    item.append(temp);
		} else if ((temp = parseStringGetResult("\"\"", false)) != null) {
		    item.append("\"");
		} else if ((temp = quotString()) != null) {
		    item.append(temp);
		}
	    }
	    if (parseStringGetResult("\"", false) != null) {
		TokenIterator v = new TokenIterator(getCurrentContext(), item
			.toString(), getCurrentLoc());
		// v.setEnclosed(true);
		return v;
	    } else {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"'\"' expected!");
	    }
	} else if (parseStringGetResult("'", true) != null) {
	    while (temp != null) {
		temp = null;
		if ((temp = CharRef()) != null) {
		    item.append(resolveCharRef(temp, false));
		} else if ((temp = PredefinedEntityRef(false)) != null) {
		    item.append(temp);
		} else if ((temp = parseStringGetResult("''", false)) != null) {
		    item.append("'");
		} else if ((temp = aposString()) != null) {
		    item.append(temp);
		}
	    }
	    if (parseStringGetResult("'", false) != null) {
		TokenIterator v = new TokenIterator(getCurrentContext(), item
			.toString(), getCurrentLoc());
		// v.setEnclosed(true);
		return v;
	    } else {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"\"'\" expected!");
	    }
	} else {
	    return null;
	}
	return null;
    }

    private Iterator VarRef() throws MXQueryException {
	QName var = null;
	if (parseString("$", true, false) && (var = QName()) != null) {
	    if (!getCurrentContext().checkVariable(var)) {
		if (var.getNamespacePrefix() != null
			&& getCurrentContext().getNamespace(
				var.getNamespacePrefix()) == null) {
		    throw new StaticException(
			    ErrorCodes.E0081_STATIC_QUERY_UNKNOWN_NAMESPACE,
			    "Unknown Namespace for Variabe '" + var.toString()
				    + "'", getCurrentLoc());
		}
		throw new StaticException(
			ErrorCodes.E0008_STATIC_NAME_OR_PREFIX_NOT_DEFINED,
			"Variable '" + var.toString() + "' not declared",
			getCurrentLoc());
	    }
	    getCurrentContext().incVariableUse(var);
	    Iterator it = new VariableIterator(getCurrentContext(), var,
		    resolveVarsOnReset, getCurrentLoc());
	    if (inConstructor
		    && getCurrentContext().getConstructionMode().equals(
			    XQStaticContext.STRIP))
		it.setConstModePreserve(false);
	    return it;

	    // return new VariableIterator(getCurrentContext(), var,
	    // resolveVarsOnReset, getCurrentLoc());
	}
	return null;
    }

    private Iterator ParenthesizedExpr() throws MXQueryException {
	int oldindex = index;
	if (parseString("()", true, false)) {
	    return new EmptySequenceIterator(getCurrentContext(),
		    getCurrentLoc());
	} else if (parseString("(", true, false)) {
	    if (parseString("#", false, false)) {
		// pragma
		// generateStaticError(ErrorCodes.A0002_EC_NOT_SUPPORTED,
		// "pragma information is not supported yet");
		index = oldindex;
		return null;
	    }

	    if (parseString(")", true, false)) {
		return new EmptySequenceIterator(getCurrentContext(),
			getCurrentLoc());
	    }

	    Iterator iter = null;
	    // createNewContextScope();
	    if ((iter = Expr()) == null) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Parenthesized Iterator expected!");
	    }
	    // removeContextScope();
	    if (parseString(")", true, false)) {
		if (iter.getClass().getName().equals(
			"ch.ethz.mxquery.model.iterators.SequenceIterator")
			&& parseStringAndStay("[", false, false)) {
		    // sequence + predicates workaround
		    // TODO: integrate completely in parser structure

		    Vector ps = new Vector();
		    while (parseString("[", true, false)) {
			XDMIterator temp = null;
			inPredicate = true;
			if ((temp = Expr()) == null) {
			    generateStaticError(
				    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				    "Error while parsing: Expr expected!");
			} else {
			    if (!parseString("]", true, false)) {
				generateStaticError(
					ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
					"']' expected!");
			    }
			    inPredicate = false;
			    // check for Context-based path expression, add
			    // explicit reference to context variable iterator
			    Vector orphans = getUnresolvedPathSteps(temp);
			    for (int i = 0; i < orphans.size(); i++) {
				XDMIterator it = (XDMIterator) orphans
					.elementAt(i);
				XDMIterator[] subs = it.getAllSubIters();
				if (subs == null || subs.length == 0)
				    it.setSubIters(new VariableIterator(
					    getCurrentContext(),
					    Context.CONTEXT_ITEM, false,
					    getCurrentLoc()));
			    }
			    // end

			    ps.addElement(temp);
			}
		    }

		    if (ps.size() > 0) {
			Iterator[] i = new Iterator[ps.size()];
			ps.copyInto(i);
			iter.addPredicates(i);

			Iterator[] preds = iter.getPredicates();

			for (int j = 0; j < preds.length; j++) {
			    iter = generatePredicateIterator(iter, preds[j]);
			}
			iter.removePredicates();
		    }
		}
		if (!(iter instanceof SequenceIterator
			|| iter instanceof EmptySequenceIterator || iter instanceof FFLWORIterator))
		    return new SequenceIterator(getCurrentContext(),
			    new Iterator[] { iter }, getCurrentLoc());
		else
		    return iter;
	    }
	}
	return null;
    }

    private Iterator ContextItemExpr() throws MXQueryException {
	if (parseStringAndStay(".", true, false)
		&& !parseStringAndStay("..", true, false)) {
	    // needContextItem=true;
	    index++;
	    return new VariableIterator(getCurrentContext(),
		    Context.CONTEXT_ITEM, false, getCurrentLoc());
	}
	return null;
    }

    private Iterator FunctionCall() throws MXQueryException {
	int oldIndex = index;

	QName qname = null;

	if ((qname = QName()) == null) {
	    index = oldIndex;
	    return null;
	}

	// -- check whether function name is in the restricted names list
	if (qname.getNamespacePrefix() == null
		&& hRestrictedFunctionNames.get(qname.getLocalPart()) != null) {
	    index = oldIndex;
	    return null;
	}
	// --

	if (!parseString("(", true, false)) {
	    index = oldIndex;
	    return null;
	    // generateStaticError(StaticError.SYNTAX_ERROR, "err:XPST0003",
	    // "FunctionCall without '('!");
	}

	if (parseString("#", false, false)) {
	    // pragma
	    index = oldIndex;
	    generateStaticError(ErrorCodes.A0002_EC_NOT_SUPPORTED,
		    "pragma information is not supported yet");
	}

	Vector params = new Vector();
	XDMIterator iter;

	if (!parseString(")", true, false)) {
	    do {
		if ((iter = ExprSingle()) != null) {
		    params.addElement(iter);
		} else {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "ExprSingle expected '");
		}

	    } while (parseString(",", true, false));

	    if (!parseString(")", true, false)) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"FunctionCall without ')'!");
	    }
	}

	if (qname.getNamespacePrefix() == null
		|| qname.getNamespacePrefix().equals("")) {
	    qname.setNamespaceURI(getCurrentContext()
		    .getDefaultFunctionNamespace());
	} else {
	    qname = qname.resolveQNameNamespace(getCurrentContext());
	}

	XDMIterator it = null;

	// Directly generate the function iterator if called from the main query
	// body
	// (no more function declaration can come) or if the call is to a
	// builtin (fn/xs/mxq) function
	Function func = getCurrentContext().getFunction(qname, params.size());

	if (func == null) {
	    if (inProlog) {
		// Insert Iterator that resolves actual function code only at
		// runtime
		it = new UserdefFuncCallLateBinding(getCurrentContext(), qname,
			params.size());
		FunctionSigHolder fs = new FunctionSigHolder();
		fs.arity = params.size();
		fs.funcName = qname;
		lateResolvedFuncs.addElement(fs);
	    } else {
		generateStaticError(
			ErrorCodes.E0017_STATIC_DOESNT_MATCH_FUNCTION_SIGNATURE,
			"Function named " + qname.toString() + " with arity "
				+ params.size() + " not available");
	    }
	} else {
	    it = func.getFunctionImplementation(getCurrentContext());
	}

	it.setLoc(new QueryLocation(oldIndex, index));

	if (inValidateExpr && (it instanceof Doc))
	    ((Doc) it).setInValidateExpression(true);
	// it.setLoc(getCurrentLoc());

	XDMIterator[] iters = new Iterator[params.size()];
	// params.toArray(iters);
	if (func != null) {
	    FunctionSignature sig = func.getFunctionSignature();

	    TypeInfo[] pType = sig.getParameterTypes();

	    for (int i = 0; i < params.size(); i++) {
		XDMIterator toAdapt = (Iterator) params.elementAt(i);
		if (sig.getName().getLocalPart().equals("concat")
			|| (pType[i] != null
				&& pType[i].getType() != TypeInfo.UNDEFINED && Type
				.isAtomicType(pType[i].getType(), null)))
		    toAdapt = DataValuesIterator.getDataIterator(toAdapt,
			    toAdapt.getContext());
		iters[i] = toAdapt;
	    }
	} else {
	    for (int i = 0; i < params.size(); i++) {
		iters[i] = (Iterator) params.elementAt(i);
	    }
	}
	it.setSubIters(iters);
	if (getCurrentContext().getParent() != getCurrentContext()
		.getRootContext()
		&& inPredicate)
	    it.setContext(getCurrentContext().getParent(), true);

	if (inConstructor
		&& getCurrentContext().getConstructionMode().equals(
			XQStaticContext.STRIP))
	    it.setConstModePreserve(false);

	return (Iterator) it;
    }

    private Iterator OrderedExpr() throws MXQueryException {
	int oldindex = index;
	if (parseKeyword("ordered")) {
	    if (!parseString("{", true, false)) {
		index = oldindex;
		return null;
	    }
	    createNewContextScope();
	    getCurrentContext().setOrderingMode(XQStaticContext.ORDERED);
	    Iterator exIt = Expr();
	    removeContextScope();
	    if (exIt == null) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: 'Expr' expected!");
	    }

	    if (!parseString("}", true, false)) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: '}' expected!");
	    }
	    return exIt;
	}
	return null;
    }

    private Iterator UnorderedExpr() throws MXQueryException {
	int oldindex = index;
	if (parseKeyword("unordered")) {
	    if (!parseString("{", true, false)) {
		index = oldindex;
		return null;
	    }
	    createNewContextScope();
	    getCurrentContext().setOrderingMode(XQStaticContext.UNORDERED);
	    Iterator exIt = Expr();
	    removeContextScope();
	    if (exIt == null) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: 'Expr' expected!");
	    }

	    if (!parseString("}", true, false)) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: '}' expected!");
	    }
	    return exIt;
	}
	return null;
    }

    private Iterator Constructor() throws MXQueryException {
	Iterator expr;
	if ((expr = DirectConstructor()) != null) {
	    return expr;
	} else if ((expr = ComputedConstructor()) != null) {
	    return expr;
	}

	return null;
    }

    private Iterator DirectConstructor() throws MXQueryException {
	Iterator expr;
	if ((expr = DirPIConstructor()) != null) {
	    return expr;
	} else if ((expr = DirCommentConstructor()) != null) {
	    return expr;
	} else if ((expr = DirElemConstructor()) != null) {
	    return expr;
	}

	return null;
    }

    private boolean checkForNsDef(QName attrName, String value)
	    throws MXQueryException {
	if (attrName.getNamespacePrefix() == null
		&& attrName.getLocalPart().equals(XQStaticContext.NS_XMLNS)) {
	    if (value.equals(XQStaticContext.URI_XML)
		    || value.equals(XQStaticContext.URI_XMLNS))
		throw new StaticException(
			ErrorCodes.E0070_STATIC_INVALID_NAMESPACE_DECL_WITH_XML_OR_XMLNS,
			"xml or xmlns not allowed in Namespace declarations",
			getCurrentLoc());
	    return true;
	} else if (attrName.getNamespacePrefix() != null) {
	    if (attrName.getNamespacePrefix().equals(XQStaticContext.NS_XMLNS)) {
		if ((attrName.getLocalPart().equals("xml") && !value
			.equals(XQStaticContext.URI_XML))
			|| (attrName.getLocalPart().equals("xmlns") && !value
				.equals(XQStaticContext.URI_XMLNS))
			|| (value.equals(XQStaticContext.URI_XML) && !attrName
				.getLocalPart().equals("xml"))
			|| (value.equals(XQStaticContext.URI_XMLNS) && !attrName
				.getLocalPart().equals("xmlns")))
		    throw new StaticException(
			    ErrorCodes.E0070_STATIC_INVALID_NAMESPACE_DECL_WITH_XML_OR_XMLNS,
			    "xml or xmlns not allowed in Namespace declarations",
			    getCurrentLoc());
		// createNewContextScope().addNamespace(attrName.getLocalPart(),
		// value);
		return true;
	    }
	    if (attrName.getNamespacePrefix().equals(XQStaticContext.NS_XML)) {
		if (attrName.getLocalPart().equals("base")
			|| attrName.getLocalPart().equals("lang"))
		    return true;
	    }
	}
	return false;
    }

    private Iterator DirElemConstructor() throws MXQueryException {
	// String name = null;
	Vector attr = new Vector();
	Vector attrValue = new Vector();
	boolean createdNSScope = false;
	// int nrOfNsDefs = 0;
	// Namespace xmlns = null;

	int oldIndex = index;

	if (!parseStringAndStay("</", !getCurrentContext()
		.getsetBoundarySpaceHandling(), false)
		&& !parseStringAndStay("<!", !getCurrentContext()
			.getsetBoundarySpaceHandling(), false)
		&& parseString("<", !getCurrentContext()
			.getsetBoundarySpaceHandling(), false)) {
	    inConstructor = true;
	    // level++;
	    commentProhibited = true;
	    if (isWhitespace()) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Whitespace is not allowed after '<' in Element Constructor!");
	    }
	    cDepth++;

	    QName qname;
	    QName qname_2;

	    if ((qname = QName()) == null) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"QName expected!");
	    }

	    if (!(isWhitespace() || parseStringAndStay(">", false, false) || parseStringAndStay(
		    "/", false, false)))
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Whitespace or end of element expected");

	    // START ATTRIBUTES
	    QName attrName;
	    boolean declaredDefaultNs = false;
	    while ((attrName = QName()) != null) {
		if (parseString("=", true, false)) {
		    String attDelim;
		    if ((attDelim = parseStringGetResult("\"", true)) != null
			    || (attDelim = parseStringGetResult("'", true)) != null) {
			XDMIterator expr = null;
			String value = "";
			String c = "";
			Vector values = new Vector();

			while (c != null) {

			    c = null;
			    if ((c = parseStringGetResult(attDelim + attDelim,
				    false)) != null) {
				value += attDelim;
			    } else {
				if (parseStringAndStay(attDelim, false, false)) {
				    break;
				}
				if (!(parseStringAndStay("<", false, false)
					|| parseStringAndStay("&", false, false)
					|| parseStringAndStay("{", false, false) || parseStringAndStay(
					"}", false, false))) {
				    c = nextChar();
				    value += c;
				} else if ((expr = CommonContent()) != null) {
				    if (!value.equals("")) {
					values.addElement(new TokenIterator(
						getCurrentContext(), value,
						getCurrentLoc()));
					value = "";
				    }

				    // for now, remove EnclosedExpr && CHAR_Red
				    // annotations
				    if (expr instanceof RewriteExpression
					    && (((RewriteExpression) (expr))
						    .getExpressionType() == RewriteExpression.ENCLOSED_EXPRESSION || ((RewriteExpression) (expr))
						    .getExpressionType() == RewriteExpression.CHAR_REF))
					expr = expr.getSubIters()[0];
				    values.addElement(expr);
				    expr = null;
				    c = "";
				    // break;
				}
			    }
			}

			if (parseString(attDelim, true, false)) {
			    if (values.size() > 0) {
				if (attrName.isNamespaceDeclAttr())
				    throw new StaticException(
					    ErrorCodes.E0022_STATIC_NOT_A_VALID_URI,
					    "Literal expected as namespace URI",
					    getCurrentLoc());

				if (!value.equals("")) {
				    values.addElement(new TokenIterator(
					    getCurrentContext(), value,
					    getCurrentLoc()));
				    value = "";
				}
				XDMIterator[] arr = new XDMIterator[values
					.size()];
				values.copyInto(arr);
				attr.addElement(attrName);
				attrValue.addElement(arr);
			    } else {
				if (checkForNsDef(attrName, value)) {
				    if (!createdNSScope) {
					curNsScope = new XDMScope(
						curNsScope,
						getCurrentContext()
							.getCopyNamespacesInheritMode());
					createdNSScope = true;
				    }
				    Hashtable localNamespaces = curNsScope
					    .getLocalNamespaces();
				    if (attrName.getLocalPart().equals("xmlns")) {
					if (declaredDefaultNs) {
					    // do local checking, since a
					    // declaration of empty default ns
					    // might not be added to the scope
					    // if there is no non-empty default
					    // ns
					    generateStaticError(
						    ErrorCodes.E0071_STATIC_DUPLICATE_NAMESPACE_ATTRIBUTES,
						    "No identical local parts in namespace attributes allowed!");
					} else {
					    declaredDefaultNs = true;
					}
					curNsScope.addNamespace("", value);
				    } else if (attrName.getNamespacePrefix() != null
					    && attrName
						    .getNamespacePrefix()
						    .equals(
							    XQStaticContext.NS_XMLNS))
					if (localNamespaces
						.containsKey(attrName
							.getLocalPart()))
					    generateStaticError(
						    ErrorCodes.E0071_STATIC_DUPLICATE_NAMESPACE_ATTRIBUTES,
						    "No identical local parts in namespace attributes allowed!");
					else
					    curNsScope.addNamespace(attrName
						    .getLocalPart(), value);
				    else {
					attr.addElement(attrName);
					attrValue.addElement(value);
				    }
				} else {
				    attr.addElement(attrName);
				    attrValue.addElement(value);
				}
			    }
			} else
			    generateStaticError(
				    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				    attDelim + " expected!");
		    } else
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"Attribute value expected");
		} else {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "'=' expected!");
		}
		if (!(isWhitespace() || parseStringAndStay(">", false, false) || parseStringAndStay(
			"/", false, false)))
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Whitespace or end of element expected");
	    }
	    commentProhibited = false;
	    // If a namespace scope has been opened, also put it into statically
	    // known namespaces
	    if (createdNSScope) {
		XQStaticContext newCtx = createNewContextScope();
		Enumeration newNamespaces = curNsScope.getLocalNamespaces()
			.elements();
		while (newNamespaces.hasMoreElements()) {
		    Namespace nm = (Namespace) newNamespaces.nextElement();
		    newCtx.addNamespace(nm);
		}

	    }

	    // END ATTRIBUTES

	    if (parseString("/>", true, false)) {
		inConstructor = false;
		// level--;
		return dirElemConstGen2(attr, attrValue, createdNSScope, qname,
			new Vector());
	    } else if (parseString(">", true, false)) {
		Vector its = parseDirectElemConstructorContent();
		if (parseString("</", true, false)) {
		    commentProhibited = true;
		    if (isWhitespace()) {
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"Whitespace is not allowed after '</' in Element Constructor!");
		    }
		    if ((qname_2 = QName()) != null) {
			if (!qname.toString().equals(qname_2.toString())) {
			    generateStaticError(
				    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				    "Closing tag '" + qname_2
					    + "' doesn't match start tag '"
					    + qname + "'!");
			}

			if (parseString(">", true, false)) {
			    return dirElemConstGen2(attr, attrValue,
				    createdNSScope, qname, its);

			} else {
			    generateStaticError(
				    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				    "'>' expected!");
			}
		    }
		} else {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "'/>' expected!");
		}
	    } else {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"'/>' or '>' expected!");
	    }
	}
	index = oldIndex;
	return null;
    }

    private Iterator dirElemConstGen2(Vector attr, Vector attrValue,
	    boolean createdNSScope, QName qname, Vector its)
	    throws MXQueryException, StaticException {
	cDepth--;
	// new attribute handling (attributes are also events)
	int attrSize = attr.size();
	XDMIterator[] c = new Iterator[its.size() + attrSize + 2];

	boolean[] isEnclosed = new boolean[attr.size() + its.size()];

	Namespace nsStart = getCurrentContext().getNamespace(
		qname.getNamespacePrefix());

	if (nsStart != null) {
	    if (curNsScope.getNsURI(qname.getNamespacePrefix()) == null) {
		if (!createdNSScope) {
		    curNsScope = new XDMScope(curNsScope);
		    createdNSScope = true;
		    createNewContextScope();
		}
		curNsScope.addNamespace(nsStart);
	    }
	}
	NamedToken tStart;
	if (getCurrentContext().getConstructionMode().equals(
		XQStaticContext.PRESERVE))
	    tStart = new NamedToken(Type.START_TAG | Type.ANY_TYPE, null,
		    qname, curNsScope);
	else
	    tStart = new NamedToken(Type.START_TAG, null, qname, curNsScope);
	// NamedToken tStart = new NamedToken(Type.START_TAG, null,
	// qname,curNsScope);
	c[0] = new TokenIterator(getCurrentContext(), tStart, null,
		getCurrentLoc());

	for (int j = 0; j < attrSize; j++) {
	    isEnclosed[j] = false;
	    QName attrQName = (QName) attr.elementAt(j);
	    Namespace attrNS;
	    if (attrQName.getNamespacePrefix() != null) {
		attrNS = curNsScope
			.getNamespace(attrQName.getNamespacePrefix());
		if (attrNS == null)
		    attrNS = getCurrentContext().getNamespace(
			    attrQName.getNamespacePrefix());
		if (attrNS == null)
		    generateStaticError(
			    ErrorCodes.E0081_STATIC_QUERY_UNKNOWN_NAMESPACE,
			    "Prefix " + attrQName.getNamespacePrefix()
				    + " not bound");
		else
		    attrQName.setNamespaceURI(attrNS.getURI());

	    }

	    boolean isIdAttr = false;

	    if ((attrQName.getLocalPart().equals("id") || attrQName
		    .getLocalPart().equals("idref")
		    && (attrQName.getNamespaceURI()
			    .equals(XQStaticContext.NS_XML))))
		isIdAttr = true;

	    if (attrValue.elementAt(j) instanceof String) {
		String attValNorm;
		if (isIdAttr)
		    attValNorm = Utils.normalizeString((String) attrValue
			    .elementAt(j));
		else
		    attValNorm = Utils.normalizeStringContent(
			    (String) attrValue.elementAt(j), isIdAttr);
		NamedToken untypedToken = new UntypedAtomicAttrToken(null,
			attValNorm, attrQName, curNsScope);

		c[j + 1] = new XMLAttrIterator(getCurrentContext(),
			untypedToken, getCurrentLoc(), curNsScope);
	    } else {
		XDMIterator[] contents = (XDMIterator[]) attrValue.elementAt(j);
		for (int k = 0; k < contents.length; k++) {
		    contents[k] = DataValuesIterator.getDataIterator(
			    contents[k], contents[k].getContext());
		}
		NamedToken nt = new NamedToken(-1, null, attrQName, curNsScope);
		c[j + 1] = new XMLAttrIterator(getCurrentContext(),
			DataValuesIterator.getDataIterator(
				new TokenIterator(getCurrentContext(), nt,
					null, getCurrentLoc()),
				getCurrentContext()), contents,
			getCurrentLoc(), curNsScope);
	    }
	}

	Set seenAttributes = new Set();

	for (int i = 0; i < attr.size(); i++) {
	    QName qn = (QName) attr.elementAt(i);
	    if (seenAttributes.contains(qn)) {
		generateStaticError(
			ErrorCodes.E0040_STATIC_DUPLICATE_ATTRIBUTE_NAMES,
			"No variables with identical name allowed!");
	    } else {
		seenAttributes.add(qn);
	    }
	}

	int aSize = attr.size();
	for (int j = 0; j < its.size(); j++) {
	    XDMIterator expr = (XDMIterator) its.elementAt(j);

	    if (expr instanceof RewriteExpression
		    && (((RewriteExpression) (expr)).getExpressionType() == RewriteExpression.ENCLOSED_EXPRESSION
			    || ((RewriteExpression) (expr)).getExpressionType() == RewriteExpression.CHAR_REF || ((RewriteExpression) (expr))
			    .getExpressionType() == RewriteExpression.CDATA)) {

		if (((RewriteExpression) (expr)).getExpressionType() == RewriteExpression.CHAR_REF) {
		    TokenIterator ti = (TokenIterator) expr.getSubIters()[0];
		    ti.setToken(new TextToken(null, Utils.expandCharRefs(ti
			    .getToken().getText())));
		}
		expr = expr.getSubIters()[0];
		isEnclosed[j + aSize] = true;
	    }

	    c[j + attrSize + 1] = expr;
	}
	NamedToken tEnd = new NamedToken(Type.END_TAG, null, qname, curNsScope);
	c[c.length - 1] = new TokenIterator(getCurrentContext(), tEnd, null,
		getCurrentLoc());

	// TODO: What about default namespace?
	if (nsStart != null) {
	    tStart.setNS(nsStart.getURI());
	    tEnd.setNS(nsStart.getURI());
	} else if (tStart.getPrefix() != null) {
	    throw new StaticException(
		    ErrorCodes.E0081_STATIC_QUERY_UNKNOWN_NAMESPACE,
		    "Namespace binding for prefix " + tStart.getPrefix()
			    + " not known", getCurrentLoc());
	}

	XDMScope inScope = curNsScope;

	if (createdNSScope) {
	    removeContextScope();
	    curNsScope = curNsScope.getParent();
	}
	commentProhibited = false;
	return new XMLContent(getCurrentContext(), c, isEnclosed, cDepth,
		getCurrentLoc(), inScope, false);
    }

    private Vector parseDirectElemConstructorContent() throws MXQueryException {
	XDMIterator content;
	Vector its = new Vector();

	if (!parseStringAndStay("</", false, false)) {
	    while ((content = DirElemContent()) != null) {
		its.addElement(content);
	    }
	}
	// Hack (?) to remove spurious whitespace at the end
	if (its.size() > 0) {
	    XDMIterator it = (XDMIterator) its.elementAt(its.size() - 1);
	    if (it instanceof TokenIterator) {
		TokenIterator ti = (TokenIterator) it;
		Token tok = ti.next();
		String text = tok.getText();
		if (text != null
			&& text.trim().length() == 0
			&& tok instanceof TextToken
			&& !getCurrentContext().getsetBoundarySpaceHandling()
			&& !(its.size() > 1
				&& its.elementAt(its.size() - 2) instanceof RewriteExpression && ((RewriteExpression) its
				.elementAt(its.size() - 2)).getExpressionType() == RewriteExpression.CDATA))
		    its.removeElementAt(its.size() - 1);
		ti.reset();
	    }
	}
	return its;
    }

    private XDMIterator DirElemContent() throws MXQueryException {
	Iterator expr;
	boolean prevInComment = inComment;
	inComment = true; // prevent skipping over comments inside element
	// content
	if ((expr = CDataSection()) != null) {
	    inComment = prevInComment;
	    return new RewriteExpression(RewriteExpression.CDATA,
		    getCurrentContext(), new Iterator[] { expr },
		    getCurrentLoc());
	} else if ((expr = DirectConstructor()) != null) {
	    inComment = prevInComment;
	    return expr;
	} else if ((expr = CommonContent()) != null) {
	    inComment = prevInComment;
	    return expr;
	} else if ((expr = ElementContentChars()) != null) {
	    inComment = prevInComment;
	    return expr;
	}
	inComment = prevInComment;
	return null;
    }

    private Iterator CDataSection() throws MXQueryException {
	StringBuffer content = new StringBuffer();
	if (parseString("<![CDATA[", false, false)) {
	    while (!parseStringAndStay("]]>", false, false)) {
		content.append(nextChar());
	    }
	    if (parseString("]]>", false, false)) {
		if (content.length() > 0) {
		    Token t = new UntypedAtomicToken(null, content.toString());
		    return new TokenIterator(getCurrentContext(), t, null,
			    getCurrentLoc());
		} else
		    return null;
	    } else {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"']]>' expected!");
	    }
	}
	return null;
    }

    private Iterator CommonContent() throws MXQueryException {
	String content;
	Iterator expr;
	if ((content = CharRef()) != null) {
	    Token t = new TextToken(null, resolveCharRef(content, true));
	    return new RewriteExpression(RewriteExpression.CHAR_REF, null,
		    new Iterator[] { new TokenIterator(getCurrentContext(), t,
			    null, getCurrentLoc()) }, getCurrentLoc());
	} else if ((content = PredefinedEntityRef(true)) != null) {
	    Token t = new TextToken(null, content);
	    return new TokenIterator(getCurrentContext(), t, null,
		    getCurrentLoc());
	} else if (parseStringGetResult("{{", false) != null) {
	    Token t = new TextToken(null, "{");
	    return new TokenIterator(getCurrentContext(), t, null,
		    getCurrentLoc());
	} else if ((parseStringGetResult("}}", false)) != null) {
	    Token t = new TextToken(null, "}");
	    return new TokenIterator(getCurrentContext(), t, null,
		    getCurrentLoc());
	} else if ((expr = EnclosedExpr(true)) != null) {
	    return new RewriteExpression(RewriteExpression.ENCLOSED_EXPRESSION,
		    null, new Iterator[] { expr }, getCurrentLoc());
	}
	return null;
    }

    private Iterator ElementContentChars() throws MXQueryException {

	StringBuffer content = new StringBuffer();
	boolean changed = false;
	while (!(parseStringAndStay("<", false, false)
		|| parseStringAndStay("&", false, false)
		|| parseStringAndStay("{", false, false) || parseStringAndStay(
		"}", false, false))) {
	    String curChar = nextChar();

	    if (curChar.length() > 0) {
		changed = true;
		content.append(curChar);
	    }
	}

	if (changed) {
	    String con = Utils.replaceAll(content.toString(), "\r", "");
	    Token t;
	    t = new TextToken(Type.TEXT_NODE_UNTYPED_ATOMIC, null, con,
		    curNsScope);
	    return new TokenIterator(getCurrentContext(), t, null,
		    getCurrentLoc());
	}
	return null;
    }

    private Iterator DirCommentConstructor() throws MXQueryException {
	StringBuffer content = new StringBuffer();
	if (parseString("<!--", !getCurrentContext()
		.getsetBoundarySpaceHandling(), false)) {
	    while (!parseStringAndStay("--", false, false)) {
		content.append(nextChar());
	    }

	    if (parseString("-->", false, false)) {

		Token t = new CommentToken(null, content.toString(), curNsScope);
		return new TokenIterator(getCurrentContext(), t, null,
			getCurrentLoc());
	    } else {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"'-->' expected!");
	    }
	}
	return null;
    }

    private Iterator DirPIConstructor() throws MXQueryException {
	StringBuffer content = new StringBuffer();
	if (parseString("<?", !getCurrentContext()
		.getsetBoundarySpaceHandling(), false)) {
	    if (query.length() <= index || isWhitespace(query.charAt(index)))
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"No whitespace allowed between PI start and target");
	    String target = null;
	    QName qn = QName();
	    if (qn != null
		    && TypeLexicalConstraints.satisfyStringConstraints(
			    Type.NCNAME, qn.toString())
		    && !qn.toString().toLowerCase().equals("xml")) {
		// skip whitespaces in front
		target = qn.toString();

		if (query.length() <= index
			|| !(isWhitespace(query.charAt(index)) || query
				.charAt(index) == '?'))
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Invalid PI declaration");
		// skipWhiteSpaces();

		while (!parseStringAndStay("?>", false, false)) {
		    content.append(nextChar());
		}

	    } else {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"'PITarget' must not be a valid NCName and not 'xml'!");
	    }
	    if (parseString("?>", true, false)) {

		String sCont = content.toString();
		String piContent = null;

		int pos = containsWhiteSpaces(sCont);
		if (pos > -1) {
		    while (pos + 1 < sCont.length()
			    && isWhitespace(sCont.charAt(pos + 1)))
			pos++;
		    piContent = sCont.substring(pos + 1);
		    if (piContent.length() == 0)
			piContent = sCont;
		}

		Token t = new ProcessingInstrToken(null, piContent, target,
			curNsScope);

		return new TokenIterator(getCurrentContext(), t, null,
			getCurrentLoc());
	    } else {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"'?>' expected!");
	    }
	}
	return null;
    }

    private Iterator ComputedConstructor() throws MXQueryException {
	Iterator expr;

	if ((expr = CompDocConstructor()) != null) {

	} else if ((expr = CompElemConstructor()) != null) {

	} else if ((expr = CompAttrConstructor()) != null) {

	} else if ((expr = CompTextConstructor()) != null) {

	} else if ((expr = CompCommentConstructor()) != null) {

	} else if ((expr = CompPIConstructor()) != null) {

	}

	return expr;
    }

    private Iterator CompDocConstructor() throws MXQueryException {
	int oldIndex = index;
	if (parseKeyword("document") && parseString("{", true, false)) {
	    Iterator exIt = Expr();

	    if (exIt == null) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: 'Expr' expected!");
	    }

	    if (!parseString("}", true, false)) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: '}' expected!");
	    }

	    Iterator startIt = new TokenIterator(getCurrentContext(),
		    new Token(Type.START_DOCUMENT, null, curNsScope), null,
		    getCurrentLoc());
	    Iterator endIt = new TokenIterator(getCurrentContext(), new Token(
		    Type.END_DOCUMENT, null, curNsScope), null, getCurrentLoc());

	    return new XMLContent(getCurrentContext(), new Iterator[] {
		    startIt, exIt, endIt }, new boolean[] { false }, 0,
		    getCurrentLoc(), curNsScope, false);
	}
	index = oldIndex;
	return null;
    }

    private Iterator CompElemConstructor() throws MXQueryException {

	int oldIndex = index;
	QName q = null;
	XDMIterator itTarget = null;
	XDMIterator itContent = null;
	boolean targetPresent = false;

	if (parseKeyword("element")) {

	    if (parseString("{", true, false)) {

		// Element target part
		itTarget = Expr();
		if (itTarget == null)
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Element constructer target undefined");

		if (!parseString("}", true, false))
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "'}' expected!");
		itTarget = DataValuesIterator.getDataIterator(itTarget,
			getCurrentContext());

		targetPresent = true;

	    } else if ((q = QName()) != null) {
		if (q.getNamespacePrefix() != null
			|| getCurrentContext().getDefaultElementNamespace() != null) {
		    if (getCurrentContext()
			    .getNamespace(q.getNamespacePrefix()) == null)
			throw new StaticException(
				ErrorCodes.E0081_STATIC_QUERY_UNKNOWN_NAMESPACE,
				"Namespace prefix " + q.getNamespacePrefix()
					+ " not bound", getCurrentLoc());
		    else {
			q.setNamespaceURI(getCurrentContext().getNamespace(
				q.getNamespacePrefix()).getURI());
		    }
		}
		itTarget = new TokenIterator(getCurrentContext(), q,
			getCurrentLoc());
		targetPresent = true;
	    }

	    if (targetPresent) {

		// Element Content
		if (parseString("{", true, false)) {

		    if (parseStringAndStay("}", true, false))
			itContent = null;
		    else
			itContent = Expr();

		    if (!parseString("}", true, false))
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"'}' expected!");
		} else {
		    index = oldIndex;
		    return null;
		}

		if (itContent != null) {
		    return new XMLContent(
			    getCurrentContext(),
			    new XDMIterator[] { itTarget, itContent, itTarget },
			    new boolean[] { false }, 0, getCurrentLoc(),
			    new XDMScope(curNsScope), true);
		} else {
		    return new XMLContent(getCurrentContext(),
			    new XDMIterator[] { itTarget, itTarget },
			    new boolean[] {}, 0, getCurrentLoc(), new XDMScope(
				    curNsScope), true);
		}
	    }
	}

	index = oldIndex;
	return null;
    }

    private Iterator CompAttrConstructor() throws MXQueryException {
	int oldIndex = index;
	boolean exp = false;
	if (parseKeyword("attribute")) {
	    QName q = QName();
	    XDMIterator exIt;
	    if (q == null) {
		if (!parseString("{", true, false)) {
		    index = oldIndex;
		    return null;
		}
		exIt = Expr();
		if (!parseString("}", true, false)) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: '}' expected!");
		}
		exp = true;
	    } else {
		if (q.getNamespacePrefix() != null) {
		    Namespace ns = getCurrentContext().getNamespace(
			    q.getNamespacePrefix());
		    if (ns == null)
			throw new StaticException(
				ErrorCodes.E0081_STATIC_QUERY_UNKNOWN_NAMESPACE,
				"Namespace prefix " + q.getNamespacePrefix()
					+ " not bound", getCurrentLoc());
		    else
			q.setNamespaceURI(ns.getURI());
		}

		NamedToken namTok = new NamedToken(-1, null, q, curNsScope);

		exIt = new TokenIterator(getCurrentContext(), namTok, null,
			getCurrentLoc());
	    }

	    exIt = DataValuesIterator.getDataIterator(exIt, exIt.getContext());
	    if (!parseString("{", true, false)) {
		if (exp)
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: '{' expected!");
		else {
		    index = oldIndex;
		    return null;
		}
	    }

	    XDMIterator itContent;
	    if (parseStringAndStay("}", true, false))
		itContent = new EmptySequenceIterator(getCurrentContext(),
			getCurrentLoc());
	    else
		itContent = Expr();
	    itContent = DataValuesIterator.getDataIterator(itContent, itContent
		    .getContext());
	    if (!parseString("}", true, false)) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"'}' expected!");
	    }
	    return new XMLAttrIterator(getCurrentContext(), exIt,
		    new XDMIterator[] { itContent }, getCurrentLoc(),
		    new XDMScope(curNsScope));
	}
	index = oldIndex;
	return null;
    }

    private Iterator CompTextConstructor() throws MXQueryException {
	int oldIndex = index;
	if (parseKeyword("text") && parseString("{", true, false)) {
	    XDMIterator exIt = Expr();
	    if (exIt == null) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"'Expr' expected!");
	    }
	    exIt = DataValuesIterator
		    .getDataIterator(exIt, getCurrentContext());
	    if (!parseString("}", true, false)) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"'}' expected!");
	    }
	    return new ComputedTextConstrIterator(getCurrentContext(),
		    new XDMIterator[] { exIt }, getCurrentLoc(), curNsScope);
	}
	index = oldIndex;
	return null;
    }

    private Iterator CompCommentConstructor() throws MXQueryException {
	int oldIndex = index;

	if (parseKeyword("comment") && parseString("{", true, false)) {

	    XDMIterator contentIt = Expr();
	    if (contentIt == null)
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"comment content undefined");

	    if (!parseString("}", true, false))
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"'}' expected!");

	    contentIt = DataValuesIterator.getDataIterator(contentIt,
		    getCurrentContext());

	    return new ComputedCommentConstrIterator(getCurrentContext(),
		    new XDMIterator[] { contentIt }, getCurrentLoc(),
		    curNsScope);
	}

	index = oldIndex;
	return null;
    }

    private Iterator CompPIConstructor() throws MXQueryException {

	int oldIndex = index;

	String target = null;
	boolean targetPresent = false;
	XDMIterator itTarget = null;
	XDMIterator itContent = null;

	if (parseKeyword("processing-instruction")) {

	    if (parseString("{", true, false)) {

		// -- PI target part
		itTarget = Expr();
		if (itTarget == null)
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "processing-instruction target undefined");

		if (!parseString("}", true, false))
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "'}' expected!");
		itTarget = DataValuesIterator.getDataIterator(itTarget,
			getCurrentContext());
		targetPresent = true;
		// --

	    } else if ((target = NCName()) != null) {
		itTarget = new TokenIterator(getCurrentContext(), target,
			getCurrentLoc());
		targetPresent = true;
	    }

	    if (targetPresent) {
		// -- PI content part (optional)
		if (parseString("{", true, false)) {

		    if (parseStringAndStay("}", true, false))
			itContent = null;
		    else
			itContent = DataValuesIterator.getDataIterator(Expr(),
				getCurrentContext());

		    if (!parseString("}", true, false))
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"'}' expected!");
		} else {
		    index = oldIndex;
		    return null;
		}
		// --

		if (itContent != null) {
		    return new ComputedPIConstrIterator(getCurrentContext(),
			    new XDMIterator[] { itTarget, itContent },
			    getCurrentLoc(), curNsScope);
		} else {
		    return new ComputedPIConstrIterator(getCurrentContext(),
			    new XDMIterator[] { itTarget }, getCurrentLoc(),
			    curNsScope);
		}
	    }
	}

	index = oldIndex;
	return null;
    }

    /***************************************************************************
     * XQuery Update Parsing *
     **************************************************************************/

    private Iterator InsertExpr() throws MXQueryException {
	if (!co.isUpdate())
	    return null;
	int type = 0;
	int curIndex = index;
	if (parseKeyword("insert")) {
	    if ((parseKeyword("node")) || (parseKeyword("nodes"))) {
		Iterator sourceExpr = SourceExpr();
		if (parseKeyword("as")) {
		    if (parseKeyword("first")) {
			type = UpdatePrimitive.INSERT_INTO_AS_FIRST;
		    } else if (parseKeyword("last")) {
			type = UpdatePrimitive.INSERT_INTO_AS_LAST;
		    } else {
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"Error while parsing InsertExpr: first | last expected!");
		    }
		}
		if (type > 0) {
		    if (!parseKeyword("into")) {
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"Error while parsing InsertExpr: into expected!");
		    }
		} else {
		    if (parseKeyword("into")) {
			type = UpdatePrimitive.INSERT_INTO;
		    } else if (parseKeyword("after")) {
			type = UpdatePrimitive.INSERT_AFTER;
		    } else if (parseKeyword("before")) {
			type = UpdatePrimitive.INSERT_BEFORE;
		    } else {
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"Error while parsing InsertExpr: into | after | before expected!");
		    }
		}
		Iterator targetExpr = TargetExpr();
		return new InsertIterator(getCurrentContext(), type,
			targetExpr, sourceExpr, getCurrentLoc());
	    }
	}
	index = curIndex;
	return null;
    }

    private Iterator DeleteExpr() throws MXQueryException {
	if (!co.isUpdate())
	    return null;
	int curIndex = index;
	if (parseKeyword("delete")) {
	    if ((parseKeyword("node")) || (parseKeyword("nodes"))) {
		Iterator targetExpr = TargetExpr();
		return new DeleteIterator(getCurrentContext(), targetExpr,
			getCurrentLoc());
	    }
	}
	index = curIndex;
	return null;
    }

    private Iterator ReplaceExpr() throws MXQueryException {
	if (!co.isUpdate())
	    return null;
	int type;
	int curIndex = index;
	if (parseKeyword("replace") && !parseString("(", true, false)) {
	    if (parseKeyword("value")) {
		if (!parseKeyword("of")) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing ReplaceExpr: of expected!");
		}
		type = UpdatePrimitive.REPLACE_VALUE;
	    } else {
		type = UpdatePrimitive.REPLACE_NODE;
	    }
	    if (!parseKeyword("node")) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing ReplaceExpr: node expected!");
	    }
	    Iterator targetExpr = TargetExpr();
	    if (!parseKeyword("with")) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing ReplaceExpr: with expected!");
	    }
	    XDMIterator singleExpr = ExprSingle();
	    if (type == UpdatePrimitive.REPLACE_VALUE) {
		singleExpr = DataValuesIterator.getDataIterator(singleExpr,
			singleExpr.getContext());
	    }
	    return new ReplaceIterator(getCurrentContext(), type, targetExpr,
		    singleExpr, getCurrentLoc());
	}
	index = curIndex;
	return null;
    }

    private Iterator RenameExpr() throws MXQueryException {
	if (!co.isUpdate())
	    return null;
	int curIndex = index;

	if (parseKeyword("rename")) {
	    if (parseKeyword("node")) {
		XDMIterator targetExpr = TargetExpr();
		if (!parseKeyword("as")) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing RenameExpr: as expected!");
		}
		XDMIterator newNameExpr = DataValuesIterator.getDataIterator(
			NewNameExpr(), getCurrentContext());
		return new RenameIterator(getCurrentContext(), targetExpr,
			newNameExpr, getCurrentLoc());
	    }
	}
	index = curIndex;
	return null;
    }

    private Iterator SourceExpr() throws MXQueryException {
	Iterator expr = ExprSingle();
	if (expr == null) {
	    generateStaticError(
		    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
		    "Impossible to parse a Source Expression!");
	}
	return expr;
    }

    private Iterator TargetExpr() throws MXQueryException {
	Iterator expr = ExprSingle();
	if (expr == null) {
	    generateStaticError(
		    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
		    "Impossible to parse a Target Expression!");
	}
	return expr;
    }

    private Iterator NewNameExpr() throws MXQueryException {
	Iterator expr = ExprSingle();
	if (expr == null) {
	    generateStaticError(
		    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
		    "Impossible to parse a NewName Expression!");
	}
	return expr;
    }

    private Iterator TransformExpr() throws MXQueryException {
	if (!co.isUpdate())
	    return null;
	Vector copyVars = new Vector();
	Vector copyExprs = new Vector();

	if (parseKeyword("copy")) {
	    createNewContextScope();
	    do {
		if (!parseString("$", true, false)) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing TransformExpr: '$' expected!");
		}
		QName copyVar = QName();
		if (copyVar == null) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing TransformExpr: Qname expected!");
		}
		if (!parseString(":=", true, false)) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing TransformExpr: ':=' expected!");
		}
		XDMIterator copyExpr = ExprSingle();
		getCurrentContext().registerVariable(copyVar, false);
		copyVars.addElement(copyVar);
		copyExprs.addElement(copyExpr);
	    } while (parseString(",", true, false));
	    if (!parseKeyword("modify")) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing TransformExpr: modify expected!");
	    }
	    Iterator updateExpr = ExprSingle();
	    if (!parseKeyword("return")) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing TransformExpr: return expected!");
	    }
	    Iterator returnExpr = ExprSingle();

	    Iterator[] arrCopyExprs = new Iterator[copyExprs.size()];
	    copyExprs.copyInto(arrCopyExprs);
	    QName[] arrCopyVars = new QName[copyVars.size()];
	    copyVars.copyInto(arrCopyVars);
	    TransformIterator transformIterator = new TransformIterator(
		    getCurrentContext(), arrCopyExprs, arrCopyVars, updateExpr,
		    returnExpr, getCurrentLoc());
	    removeContextScope();
	    return transformIterator;
	}
	return null;
    }

    /***************************************************************************
     * XQuery Scripting *
     **************************************************************************/

    private Iterator ApplyExpr() throws MXQueryException {
	if (!co.isScripting())
	    return null;
	int oldIndex = index;
	Vector items = new Vector();
	int lastLoopIndex;
	do {
	    lastLoopIndex = index;
	    items.addElement(ConcatExpr());
	} while (parseString(";", true, false));

	if (items.size() > 1) {
	    if (index != query.length()) {
		index = lastLoopIndex; // "un-parse" the last expression, since
		// it is not followed by a ;
	    }
	    items.removeElementAt(items.size() - 1);
	    Iterator[] iters = new Iterator[items.size()];
	    items.copyInto(iters);
	    return new ApplyExprIterator(getCurrentContext(), iters,
		    getCurrentLoc());
	}
	index = oldIndex;
	return null;

    }

    private Iterator AssignExpr() throws MXQueryException {
	if (!co.isScripting())
	    return null;
	if (parseKeyword("set")) {
	    if (!parseString("$", true, false)) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing Assign Expr: '$' expected!");
	    }
	    QName varName = QName();
	    if (varName == null) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing Assign Expr: QName expected!");
	    }
	    if (!parseString(":=", true, false)) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing Assign Expr: ':=' expected!");
	    }
	    Iterator sourceExpr = ExprSingle();
	    return new AssignIterator(getCurrentContext(), varName, sourceExpr,
		    getCurrentLoc());
	}
	return null;
    }

    private XDMIterator BlockDecl() throws MXQueryException {
	if (!co.isScripting())
	    return null;
	Vector varNames = new Vector();
	Vector sourceExprs = new Vector();

	if (parseKeyword("declare")) {
	    do {
		if (!parseString("$", true, false)) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing Block Declaration: '$' expected!");
		}
		QName varName = QName();
		if (varName == null) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing Block Declaration: QName expected!");
		}
		SequenceTypeIterator seqTypeIt = null;
		if (parseKeyword("as")) {
		    TypeInfo typeInfo = SequenceType();
		    seqTypeIt = new SequenceTypeIterator(typeInfo, true, true,
			    getCurrentContext(), getCurrentLoc());
		}
		getCurrentContext().registerVariable(varName, false, seqTypeIt,
			true);
		XDMIterator sourceExpr = null;
		if (parseString(":=", true, false)) {
		    sourceExpr = ExprSingle();
		}
		varNames.addElement(varName);
		sourceExprs.addElement(sourceExpr);
	    } while (parseString(",", true, false));
	    Iterator[] iterExprs = new Iterator[sourceExprs.size()];
	    sourceExprs.copyInto(iterExprs);
	    QName[] arrNames = new QName[varNames.size()];
	    varNames.copyInto(arrNames);
	    return new BlockDeclIterator(getCurrentContext(), iterExprs,
		    arrNames, getCurrentLoc());
	}
	return null;
    }

    private Iterator BlockExpr() throws MXQueryException {
	if (!co.isScripting())
	    return null;
	int oldIndex = index;
	if (parseString("block", true, false)) {
	    Iterator block = Block();
	    if (block != null)
		return block;
	}
	index = oldIndex;
	return null;
    }

    private Iterator Block() throws MXQueryException {
	if (!co.isScripting())
	    return null;
	// boolean isAtomic;
	if (parseString("{", true, false)) {
	    createNewContextScope();
	    // parses block delcarations
	    Vector bdis = new Vector();
	    XDMIterator bdi = BlockDecl();
	    while (bdi != null) {
		bdis.addElement(bdi);
		if (!parseString(";", true, false)) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing Block: ';' expected after Block Decl!");
		}
		bdi = BlockDecl();
	    }
	    Iterator[] declarations = new BlockDeclIterator[bdis.size()];
	    bdis.copyInto(declarations);

	    // parses block expressions
	    Iterator blockBody = Expr();
	    if (blockBody == null) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Could not parse block body");
	    }

	    if (!parseString("}", true, false)) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing Block: Every Block must end with '}'!");
	    }
	    Iterator iter = new BlockIterator(getCurrentContext(),
		    declarations, blockBody, getCurrentLoc());
	    removeContextScope();
	    return iter;
	}
	return null;
    }

    private Iterator ExitExpr() throws MXQueryException {
	if (!co.isScripting())
	    return null;
	int curIndex = index;
	if (parseKeyword("exit") && parseKeyword("with")) {
	    Iterator exitExpr = ExprSingle();
	    if (exitExpr == null) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"An early return must be followed by a returning expression!");
	    } else if (exitExpr.getExpressionCategoryType(co.isScripting()) == XDMIterator.EXPR_CATEGORY_UPDATING)
		generateStaticError(
			ErrorCodes.U0001_UPDATE_STATIC_UPDATING_EXPRESSION_NOT_ALLOWED_HERE,
			"No updating expression allowed in 'exit with'");
	    return new EarlyReturnIterator(getCurrentContext(), exitExpr,
		    getCurrentLoc());
	}
	index = curIndex;
	return null;
    }

    private Iterator WhileExpr() throws MXQueryException {
	if (!co.isScripting())
	    return null;
	if (parseKeyword("while")) {
	    if (!parseString("(", true, false)) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parse While Expr: The condition must start with '('");
	    }
	    XDMIterator condition = BooleanIterator.createEBVIterator(
		    ExprSingle(), getCurrentContext());
	    if (!parseString(")", true, false)) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing While Expr: The condition must end with '('");
	    }
	    openLoops++;
	    Iterator body = Block();
	    openLoops--;
	    return new WhileIterator(getCurrentContext(), condition, body,
		    getCurrentLoc());
	}
	return null;
    }

    // private Iterator BreakExpr() throws MXQueryException{
    // int curIndex = index;
    // if (parseKeyword("break") && parseKeyword("loop")) {
    // return new BreakContinueIterator(getCurrentContext(),
    // CFException.CF_BREAK, getCurrentLoc());
    // }
    // index = curIndex;
    // return null;
    // }
    // private Iterator ContinueExpr() throws MXQueryException{
    // int curIndex = index;
    // if (parseKeyword("continue") && parseKeyword("loop")) {
    // return new BreakContinueIterator(getCurrentContext(),
    // CFException.CF_CONTINUE, getCurrentLoc());
    // }
    // index = curIndex;
    // return null;
    // }

    private CatchIterator CatchExpr() throws MXQueryException {
	if (parseKeyword("catch")) {
	    if (!parseString("(", true, false)) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"A catch must be followed by '( ... )'!");
	    }
	    Object nametest = null;
	    if ((nametest = Wildcard()) == null && (nametest = QName()) == null)
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"A catch clause must contain a NameTest!");

	    QName errorCode = null;
	    QName errorDescr = null;
	    QName errorVal = null;
	    boolean scopeOpened = false;
	    if (parseString(",", true, false)) {
		if (parseString("$", true, false)) {
		    errorCode = QName();
		    if (errorCode == null) {
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"Variable declaration for catch not found!");
		    }
		    createNewContextScope();
		    scopeOpened = true;
		    getCurrentContext().registerVariable(errorCode, false);
		} else
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "$ expected");
	    }
	    if (parseString(",", true, false)) {
		if (parseString("$", true, false)) {
		    errorDescr = QName();
		    if (errorDescr == null) {
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"Variable declaration for catch not found!");
		    }
		    getCurrentContext().registerVariable(errorDescr, false);
		} else
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "$ expected");
	    }
	    if (parseString(",", true, false)) {
		if (parseString("$", true, false)) {
		    errorVal = QName();
		    if (errorVal == null) {
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"Variable declaration for catch not found!");
		    }
		    getCurrentContext().registerVariable(errorVal, false);
		} else
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "$ expected");
	    }
	    if (!parseString(")", true, false)) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"A catch must be followed by '( ... )'!");
	    }
	    if (!parseKeyword("{")) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"The returned expression of a CATCH must be included into {}");
	    }
	    Iterator catchExpr = ExprSingle();
	    if (!parseKeyword("}")) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"The returned expression of a CATCH must be included into {}");
	    }
	    CatchIterator catchIter = new CatchIterator(getCurrentContext(),
		    catchExpr, nametest, errorCode, errorDescr, errorVal,
		    getCurrentLoc());
	    if (scopeOpened)
		removeContextScope();
	    return catchIter;
	}
	return null;
    }

    private Iterator TryCatchExpr() throws MXQueryException {
	int oldIndex = index;
	if (co.isXquery11() && parseKeyword("try")) {
	    if (!parseString("{", true, false)) {
		index = oldIndex;
		return null;
	    }
	    Iterator tryBlock = ExprSingle();
	    if (!parseString("}", true, false)) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"A try body must end with '}'!");
	    }

	    CatchIterator catchBlock;
	    Vector catchBlocks = new Vector();
	    while ((catchBlock = CatchExpr()) != null) {
		catchBlocks.addElement(catchBlock);
	    }
	    if (catchBlocks.size() == 0) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"A try must be followed by at least one catch or a default clause!");
	    }
	    CatchIterator[] cbArr = new CatchIterator[catchBlocks.size()];
	    catchBlocks.copyInto(cbArr);
	    return new TryIterator(getCurrentContext(), tryBlock, cbArr,
		    getCurrentLoc());
	}
	return null;
    }

    /***************************************************************************
     * literals *
     **************************************************************************/
    /* not necessary predifined entity reference, maybe any string e.g. "&kuku" */
    private String PredefinedEntityRef(boolean insideElem)
	    throws MXQueryException {
	String item = "";
	String val = "";
	boolean entityRef = false;

	String c = null;
	if ((c = parseStringGetResult("&", false)) != null) {
	    item += c;
	    if (parseString(";", true, false)) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"invalid predefined entity reference!");
	    }
	    if ((c = parseStringGetResult("lt", false)) != null) {
		item += c;
		val = "<";
		entityRef = true;
	    } else if ((c = parseStringGetResult("gt", false)) != null) {
		item += c;
		val = ">";
		entityRef = true;
	    } else if ((c = parseStringGetResult("amp", false)) != null) {
		item += c;
		val = "&";
		entityRef = true;
	    } else if ((c = parseStringGetResult("quot", false)) != null) {
		item += c;
		val = "\"";
		entityRef = true;
	    } else if ((c = parseStringGetResult("apos", false)) != null) {
		item += c;
		val = "'";
		entityRef = true;
	    } else {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Invalid predefined entity reference!");
	    }

	    if (entityRef) {
		if ((c = parseStringGetResult(";", false)) != null) {
		    return val;
		} else {
		    // if (insideElem)
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "';' expected!");
		    // else return item;
		}
	    } else if (item.equals("&") && insideElem)
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"'&' not allowed in the node value");
	    else
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"'&' must be escaped!");
	    // return item;
	}
	return null;
    }

    private String CharRef() throws MXQueryException {
	String c;
	String temp;
	// int codePoint = -1;
	if ((c = parseStringGetResult("&#", false)) != null) {
	    if (parseString(";", true, false)) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"invalid character reference!");
	    }
	    if ((temp = Digits()) != null) {
		c += temp;
		if ((temp = parseStringGetResult(";", false)) != null) {
		    c += temp;
		} else {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "';' expected!");
		}
	    } else if ((temp = parseStringGetResult("x", false)) != null) {
		c += temp;
		int n = 0;
		while (!parseString(";", false, false)) {
		    // if (n > 5) {
		    // generateStaticError(ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
		    // "character reference hex value too long!");
		    // }

		    if ((temp = Digit()) != null) {
			c += temp;
		    } else if ((temp = nextChar()) != null) {
			if (temp.charAt(0) >= 97 && temp.charAt(0) <= 102) {
			    c += temp.toUpperCase();
			} else if (temp.charAt(0) >= 65 && temp.charAt(0) <= 70) {
			    c += temp;
			} else {
			    generateStaticError(
				    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				    "character reference must only contain hex values!");
			}
		    }
		    n++;
		}

		c += ";";
	    } else

		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Invalid content of Character Reference!");
	    return c;
	}
	return null;
    }

    private String resolveCharRef(String c, boolean elemAttribute)
	    throws MXQueryException {
	int codePoint;
	if (c.indexOf('x') >= 0) {
	    // Hex digits
	    String charRef = c.substring(3, c.length() - 1);
	    if (charRef.length() == 0)
		throw new StaticException(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Invalid character reference", getCurrentLoc());
	    try {
		codePoint = Integer.parseInt(charRef, 16);
	    } catch (NumberFormatException e) {
		throw new StaticException(
			ErrorCodes.E0090_STATIC_CHARREF_INVALID_CHARACTER,
			"Invalid character reference", getCurrentLoc());
	    }
	} else { // Decimal digits
	    String charRef = c.substring(2, c.length() - 1);
	    if (charRef.length() == 0)
		throw new StaticException(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Invalid character reference", getCurrentLoc());
	    try {
		codePoint = Integer.parseInt(charRef, 10);
	    } catch (NumberFormatException nf) {
		throw new StaticException(
			ErrorCodes.E0090_STATIC_CHARREF_INVALID_CHARACTER,
			"Invalid character reference", getCurrentLoc());
	    }

	}
	if (!(codePoint == 0x9 || codePoint == 0xA || codePoint == 0xD
		|| (codePoint >= 0x20 && codePoint <= 0xd7ff)
		|| (codePoint >= 0xE000 && codePoint <= 0xFFFD) || (codePoint >= 0x10000 && codePoint <= 0x10FFFF)))
	    throw new StaticException(
		    ErrorCodes.E0090_STATIC_CHARREF_INVALID_CHARACTER,
		    "Invalid character reference", getCurrentLoc());

	if (elemAttribute
		&& (codePoint == 0x9 || codePoint == 0xA || codePoint == 0xD)) {
	    return c;
	}

	// c = new String(new char[] {(char)codePoint});
	c = new String(PlatformDependentUtils.codepointToChars(codePoint));
	// add pending whitespace
	StringBuffer wsb = new StringBuffer();
	char ws;
	while (isWhitespace(ws = query.charAt(index++)))
	    wsb.append(ws);
	index--;
	c += wsb;
	return c;
    }

    private String quotString() throws MXQueryException {
	if (parseStringAndStay("\"", false, false)
		|| parseStringAndStay("&", false, false)) {
	    return null;
	} else {
	    return nextChar();
	}
    }

    private String aposString() throws MXQueryException {
	if (parseStringAndStay("'", false, false)
		|| parseStringAndStay("&", false, false)) {
	    return null;
	} else {
	    return nextChar();
	}
    }

    private Wildcard Wildcard() throws MXQueryException {
	int oldindex = index;
	String part = null;
	boolean hasWildcardForPrefix = false;
	if (parseString("*", true, false)) {
	    hasWildcardForPrefix = true;
	} else if ((part = NCName()) == null) {
	    return null;
	}

	if (parseString("::", false, false)) {
	    index = oldindex;
	    return null;
	}

	if (!parseString(":", false, false)) {
	    if (hasWildcardForPrefix)
		return new Wildcard("*", false);
	    else {
		index = oldindex;
		return null;
	    }
	}

	if (hasWildcardForPrefix) {
	    if ((part = NCName()) == null) {
		index = oldindex;
		return null;
	    }
	} else {
	    if (!parseString("*", false, false)) {
		index = oldindex;
		return null;
	    }
	}

	return new Wildcard(part, hasWildcardForPrefix);
    }

    protected QName QName() throws MXQueryException {
	int tempIndex = index;

	String prefix = null;
	String local = null;

	String temp = null;
	if ((temp = NCName()) != null) {
	    int currIndex = index;
	    if (parseString(":=", false, false)) {
		index = currIndex;
		return new QName(null, temp);
	    } else if (parseString("::", false, false)) {
		index = tempIndex;
		return null;
	    } else if (parseString(":", false, false)) {
		if (isWhitespace()) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "No whitespace after ':' in QName allowed!");
		}
		if ((local = NCName()) != null) {
		    prefix = temp;
		    return new QName(prefix, local);
		} else {
		    // could still be a wildcard!!!
		    if (parseString("*", false, false)) {
			index = tempIndex;
			return null;
		    }
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "No QName without local part!");
		}
	    } else {
		return new QName(null, temp);
	    }
	}
	return null;
    }

    private String NCName() throws MXQueryException {
	skipWhiteSpaces();

	String name = "";

	String temp = null;
	if ((temp = Letter()) != null) {
	    name += temp;
	} else if ((temp = parseStringGetResult("_", false)) != null) {
	    name += temp;
	} else {
	    return null;
	}

	temp = null;
	while ((temp = NCNameChar()) != null) {
	    name += temp;
	}

	return name;
    }

    private String Letter() {
	String c = null;

	if (index >= query.length()) {
	    return null;
	}
	char ch = query.charAt(index);
	if ((((((1 << 1) | (1 << 2) | (1 << 3) | (1 << 4) | (1 << 5)) >> (A[Y[(X[ch >> 6] << 5)
		| ((ch >> 1) & 0x1F)]
		| (ch & 0x1)] & 0x1F)) & 1) != 0)) {
	    c = String.valueOf(query.charAt(index));
	    index++;
	    return c;
	}
	return null;
    }

    private String Digit() {
	String c = null;

	if (index >= query.length()) {
	    return null;
	}
	if (Character.isDigit(query.charAt(index))) {
	    c = String.valueOf(query.charAt(index));
	    index++;
	    return c;
	}
	return null;
    }

    private String Digits() {
	String integer = null;
	String temp;

	if ((integer = Digit()) != null) {
	    while ((temp = Digit()) != null) {
		integer += temp;
	    }
	    return integer;
	}

	return null;
    }

    private String NCNameChar() throws MXQueryException {
	// TODO: extend with additional chars
	String c = null;

	if ((c = Letter()) != null) {
	    return c;
	} else if ((c = Digit()) != null) {
	    return c;
	} else if ((c = parseStringGetResult(".", false)) != null) {
	    return c;
	} else if ((c = parseStringGetResult("-", false)) != null) {
	    return c;
	} else if ((c = parseStringGetResult("_", false)) != null) {
	    return c;
	} else {
	    return null;
	}
    }

    private boolean isEncName(String val) {
	if (val.length() == 0)
	    return false;
	if (!Utils.isLetter(val.charAt(0)))
	    return false;
	int pos = 1;
	while (pos < val.length()) {
	    if (!(Utils.isLetter(val.charAt(pos))
		    || Character.isDigit(val.charAt(pos))
		    || val.charAt(pos) == '-' || val.charAt(pos) == '_' || val
		    .charAt(pos) == '.'))
		return false;
	    pos++;
	}
	return true;
    }

    private String nextChar() throws MXQueryException {
	if (index >= query.length()) {
	    generateStaticError(
		    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
		    "Error while parsing: end of document reached!");
	}
	String result = String.valueOf(query.charAt(index));
	index++;
	return result;
    }

    /**
     * parse the next string
     * 
     * TODO: replace parsing of separators, equations, etc. and mark this method
     * as deprecated!
     * 
     * @param string
     *                The string to parse
     * @param skip
     *                Skip white spaces
     * @param requireSeparator
     *                require a separator at the end of the parsed String
     * @return Returns true if the parsing was successful, else false
     */
    protected boolean parseString(String string, boolean skip,
	    boolean requireSeparator) throws MXQueryException {
	int oldIndex = index;
	if (skip && !string.startsWith(" ")) {
	    skipWhiteSpaces();
	}
	if (index >= query.length()) {
	    return false;
	}
	// String s = query.substring(index);
	if (query.substring(index).startsWith(string)) {
	    if (requireSeparator) {
		if (isSeparatorNext(index + string.length())) {
		    index = index + string.length();
		    return true;
		}
	    } else {
		index = index + string.length();
		return true;
	    }
	}

	index = oldIndex;
	return false;
    }

    /**
     * parses a string but the position stays the same
     * 
     * @param string
     *                The string to parse
     * @param skip
     *                Skip white spaces
     * @return Returns if the parsing was succesful
     */
    private boolean parseStringAndStay(String string, boolean skip,
	    boolean requireSeparator) throws MXQueryException {
	if (skip && !string.equals(" ") && !string.startsWith(" ")) {
	    skipWhiteSpaces();
	}
	if (query.substring(index).startsWith(string)) {
	    if (requireSeparator) {
		if (isSeparatorNext(index + string.length())) {
		    return true;
		}
	    } else {
		return true;
	    }
	}

	// index = oldIndex;
	return false;
    }

    /**
     * Parses a string, the position moves but if a match is successful the
     * parsed string is returned This method doesn't need to check a separator
     * after the matching string.
     * 
     * @param string
     *                The string to parse
     * @param skip
     *                Skip white spaces
     * @return Null if the parsing was not successful otherwise it returns the
     *         string parameter
     */
    private String parseStringGetResult(String string, boolean skip)
	    throws MXQueryException {
	// int oldIndex = index;
	if (skip && !string.equals(" ") && !string.startsWith(" ")) {
	    skipWhiteSpaces();
	}
	if (query.substring(index).startsWith(string)) {
	    index = index + string.length();
	    return string;
	}
	// index = oldIndex;
	return null;
    }

    /**
     * replaces the parseString(x, true, true) method for better overview
     * 
     * @param keyword
     *                The keyword to parse
     * @return true, if the keyword is parsed, else false
     */
    protected boolean parseKeyword(String keyword) throws MXQueryException {
	return parseString(keyword, true, true);
    }

    /**
     * Returns true, if the next item to parse is a seperator, else false
     * 
     * @return true or false
     */
    private boolean isSeparatorNext(int index) {
	if (query.length() == index)
	    return true;

	for (int i = 0; i < separators.length; i++) {
	    if (query.charAt(index) == separators[i]) {
		return true;
	    }
	}

	return false;
    }

    private void skipWhiteSpaces() throws MXQueryException {
	if (skippingAllowed) {
	    if (!inComment && !pragma) {
		skipComments();
	    }
	    while (parseString(" ", false, false)
		    || parseString("\f", false, false)
		    || parseString("\r", false, false)
		    || parseString("\n", false, false)
		    || parseString("\t", false, false)) {
	    }
	}
    }

    /***************************************************************************
     * 
     * @param source
     *                to search for the white spaces
     * @return position of the first white space, otherwise -1
     */
    private int containsWhiteSpaces(String source) {
	int pos = -1;

	if (source.indexOf(" ") > -1)
	    pos = source.indexOf(" ");

	if (source.indexOf("\r") > -1 && source.indexOf("\r") < pos)
	    pos = source.indexOf("\r");

	if (source.indexOf("\n") > -1 && source.indexOf("\n") < pos)
	    pos = source.indexOf("\n");

	if (source.indexOf("\t") > -1 && source.indexOf("\t") < pos)
	    pos = source.indexOf("\t");

	return pos;
    }

    private boolean isWhitespace() throws MXQueryException {
	if (parseString(" ", false, false) || parseString("\r", false, false)
		|| parseString("\n", false, false)
		|| parseString("\t", false, false)) {
	    return true;
	}
	return false;
    }

    // private boolean equalWhitespaces(int i, int oindex) {
    // if ((query.charAt(i)=='\n' || query.charAt(i)=='\r')
    // && (originalQuery.charAt(oindex)=='\n' ||
    // originalQuery.charAt(oindex)=='\r')) {
    // return true;
    // }
    // return false;
    // }

    private boolean isWhitespace(char ch) {
	if (ch == ' ')
	    return true;
	if (ch == '\n')
	    return true;
	if (ch == '\t')
	    return true;
	if (ch == '\r')
	    return true;
	return false;
    }

    /** Full text extension */

    /**
     * the "score" value of the current item must be determined and binded to
     * the score variable.
     */
    private QName FTScoreVar() throws MXQueryException {
	if (parseKeyword("score")) {
	    if (parseString("$", true, false)) {
		QName qname;
		if ((qname = QName()) != null) {
		    return qname;
		} else {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'QName' expected!");
		}
	    } else {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: '$' expected!");
	    }
	}
	return null;
    }

    private void FTOptionDecl() throws MXQueryException {

	Vector match;

	if ((match = FTMatchOptions()) != null) {
	    if (!parseKeyword(";"))
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: ';' expected!");
	    int i = 0;
	    while (i < match.size()) {
		MatchOption option = (MatchOption) match.elementAt(i);
		switch (option.getMatchOptionType()) {
		case MatchOption.MATCH_OPTION_TYPE_LANGUAGE:
		    FTLanguageMatchOption langOption = (FTLanguageMatchOption) option;
		    getCurrentContext().getRootContext().setFTLanguage(
			    langOption.getLanguage());
		    break;
		case MatchOption.MATCH_OPTION_TYPE_CASE:
		    FTCaseMatchOption caseOption = (FTCaseMatchOption) option;
		    getCurrentContext().getRootContext().setFTCase(
			    caseOption.getCaseType());
		    break;
		case MatchOption.MATCH_OPTION_TYPE_DIACRITICS:
		    getCurrentContext().getRootContext()
			    .setFTDiacriticsSensitive(option.isOptionValue());
		    break;
		case MatchOption.MATCH_OPTION_TYPE_STEMMING:
		    getCurrentContext().getRootContext().setFTStemming(
			    option.isOptionValue());
		    break;
		case MatchOption.MATCH_OPTION_TYPE_WILDCARD:
		    getCurrentContext().getRootContext().setFTWildcard(
			    option.isOptionValue());
		    break;
		case MatchOption.MATCH_OPTION_TYPE_STOPWORD:
		    getCurrentContext().getRootContext().setFTStopwords(
			    (FTStopWordsMatchOption) option);
		    break;
		case MatchOption.MATCH_OPTION_TYPE_THESAURUS:
		    getCurrentContext().getRootContext().setFTThesaurus(
			    (FTThesaurusMatchOption) option);
		    break;
		}
		i++;
	    }
	} else {
	    generateStaticError(
		    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
		    "Error while parsing: 'FTMatchOptions' expected!");
	}
    }

    private Iterator FTContainsExpr() throws MXQueryException {
	Iterator range = null;
	FTBaseIterator selection = null;
	Iterator ignore = null;
	if ((range = RangeExpr()) != null) {
	    if (parseKeyword("ftcontains")) {
		Iterator res = null;
		createNewContextScope();
		getCurrentContext().registerNewContextItem();
		range = new ForIterator(getCurrentContext(),
			new Iterator[] { range }, Context.CONTEXT_ITEM, null,
			null, getCurrentLoc());
		if ((selection = FTSelection()) != null) {
		    if (selection instanceof FTUnaryNotIterator) {
			generateStaticError(
				ErrorCodes.FTST002_FTUnaryNotOperator_RESTRICTION_NOT_OBEYED,
				"not cannot negate any kind of FTSelection");
		    }

		    if ((ignore = FTIgnoreOption()) != null) {
			Vector orphs = getUnresolvedIters(ignore);
			for (int i = 0; i < orphs.size(); i++) {
			    Iterator it = (Iterator) orphs.elementAt(i);
			    it.setSubIters(new VariableIterator(
				    getCurrentContext(), Context.CONTEXT_ITEM,
				    false, it.getLoc()));
			}
			res = new FTContainsIterator(getCurrentContext(),
				new Iterator[] { range, ignore }, selection,
				getCurrentLoc(), useFTScoring);
		    } else {
			res = new FTContainsIterator(getCurrentContext(),
				new Iterator[] { range }, selection,
				getCurrentLoc(), useFTScoring);
		    }

		} else {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'FTSelection' expected!");
		}
		removeContextScope();
		return res;

	    }
	    // removeContextScope();
	    return range;
	} else {
	    generateStaticError(
		    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
		    "Error while parsing: 'RangeExpr' expected!");
	}
	// removeContextScope();
	return null;
    }

    private FTBaseIterator FTSelection() throws MXQueryException {

	FTBaseIterator temp;
	FTPositional pos;
	XDMIterator weight = null;
	Vector its = new Vector();
	int posCounter = 0;
	if ((temp = FTOr()) != null) {
	    while ((pos = FTPosFilter()) != null) {
		its.addElement(pos);
		posCounter++;
	    }
	    if (parseKeyword("weight")) {
		if ((weight = RangeExpr()) == null) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "RangeExpr expected!");
		}
	    }

	    if (posCounter > 1) {
		generateStaticError(
			ErrorCodes.FTST0011_FTWindow_OR_FTDistance_RESTRICITION_NOT_OBEYED,
			"Nested Proximity not supported");

	    }

	    if (its.size() > 0 || weight != null)
		return new FTSelectionIterator(getCurrentContext(), temp, its,
			weight);
	    else
		return temp;

	}
	return null;
    }

    private FTBaseIterator FTOr() throws MXQueryException {
	FTBaseIterator temp = FTAnd();
	FTBaseIterator temp2;
	Vector its = new Vector();
	its.addElement(temp);
	while (parseKeyword("ftor")) {
	    if ((temp2 = FTAnd()) != null) {
		its.addElement(temp2);
	    } else
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"FTAnd expected after ftor");
	}
	if (its.size() > 1)
	    return new FTOrIterator(getCurrentContext(), its);
	else
	    return temp;
    }

    private FTBaseIterator FTAnd() throws MXQueryException {
	FTBaseIterator temp;
	FTBaseIterator temp2;
	Vector its = new Vector();
	boolean ftnotFlag = false;
	if ((temp = FTMildNot()) != null) {
	    its.addElement(temp);
	    while (parseKeyword("ftand")) {
		if ((temp2 = FTMildNot()) != null) {
		    if (temp2 instanceof FTUnaryNotIterator) {
			ftnotFlag = true;
		    }
		    its.addElement(temp2);
		} else {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "FTMildNot expected");
		}
	    }
	    if (its.size() > 1)
		return new FTAndIterator(getCurrentContext(), its, ftnotFlag);
	    else
		return (FTBaseIterator) its.elementAt(0);

	}
	return null;
    }

    private FTBaseIterator FTMildNot() throws MXQueryException {
	FTBaseIterator temp;
	FTBaseIterator temp2;
	Vector its = new Vector();
	if ((temp = FTUnaryNot()) != null) {
	    its.addElement(temp);
	    while (parseKeyword("not")) {
		if (parseKeyword("in")) {
		    if ((temp2 = FTUnaryNot()) != null) {
			its.addElement(temp2);
		    }
		} else {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'in' expected");
		}

	    }
	    if (its.size() > 1) {
		if (temp instanceof FTUnaryNotIterator) {
		    return new FTMildNotIterator(getCurrentContext(),
			    new FTIteratorInterface[] { temp }, true);
		} else {
		    // return new
		    // FTMildNotIterator(getCurrentContext(),its,false);
		    generateStaticError(
			    ErrorCodes.FTST001_FTMildNotOperator_NOT_SUPPORTED,
			    "FTMildNot is not supported yet");
		}
	    } else
		return temp;

	}

	return null;
    }

    private FTBaseIterator FTUnaryNot() throws MXQueryException {
	FTBaseIterator temp;
	if (parseKeyword("ftnot")) {
	    if ((temp = FTPrimaryWithOptions()) != null) {
		return new FTUnaryNotIterator(getCurrentContext(),
			new FTIteratorInterface[] { temp });

	    } else {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing FTPrimaryWithOptions");
	    }
	} else if ((temp = FTPrimaryWithOptions()) != null) {
	    return temp;
	}
	return null;
    }

    private FTBaseIterator FTPrimaryWithOptions() throws MXQueryException {
	FTBaseIterator temp;
	Vector options;

	if ((temp = FTPrimary()) != null) {
	    if ((options = FTMatchOptions()) != null) {
		if (temp instanceof Words) {
		    Words words = (Words) temp;
		    return new MatchIterator(words.getContext(), words
			    .getSubIterators(), options, words
			    .getAnyAllOption(), useFTScoring, 0);
		} else {
		    return temp;
		}
	    }
	    if (temp instanceof Words) {
		Words words = (Words) temp;
		return new MatchIterator(words.getContext(), words
			.getSubIterators(), null, words.getAnyAllOption(),
			useFTScoring, 0);
	    } else {
		return temp;
	    }

	}
	return null;

    }

    private FTBaseIterator FTPrimary() throws MXQueryException {
	Words temp;
	Range times;
	FTBaseIterator selection;
	// Iterator extensionSelection;
	// Vector v = new Vector();
	if ((temp = FTWords()) != null) {
	    if ((times = FTTimes()) != null) {
		// TODO: include times
		Vector subIter = new Vector();
		subIter.addElement(temp.getVarIter());
		subIter.addElement(temp.getTokenIter());
		subIter.addElement(times);
		return new Words(getCurrentContext(), subIter);

	    }

	    return temp;

	} else {
	    int origPos = index;
	    if (parseString("(", false, false)
		    || (parseString("(", true, false))) {
		if ((selection = FTSelection()) != null) {
		    if (parseString(")", false, false)
			    || parseString(")", true, false)) {
			return selection;
		    } else {
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"Error while parsing: ')' expected");
		    }
		}
		index = origPos;
	    }

	    if ((selection = FTExtensionSelection()) != null) {
		return selection;
	    }
	}
	return null;
    }

    /** */

    private Words FTWords() throws MXQueryException {
	Words temp;
	AnyAllOption option;
	if ((temp = FTWordsValue()) != null) {
	    if ((option = FTAnyallOption()) != null) {
		temp.setAnyAllOption(option);
		return temp;
	    }
	    return temp;
	}
	return null;
    }

    /** FTWordsValue specifies the tokens and phrases */
    private Words FTWordsValue() throws MXQueryException {

	XDMIterator temp = null;
	XDMIterator keyword = null;
	if (((keyword = Literal()) != null)) {
	    Vector iter = new Vector();
	    VariableIterator source = new VariableIterator(getCurrentContext(),
		    Context.CONTEXT_ITEM, false, getCurrentLoc());
	    iter.addElement(source);
	    iter.addElement(keyword);

	    Words words = new Words(getCurrentContext(), iter);
	    return words;

	} else if (parseString("{", false, false)) {

	    if ((temp = Expr()) != null) {

		if (parseString("}", false, false)
			|| parseString("}", false, true)) {
		    Vector iter = new Vector();
		    VariableIterator source = new VariableIterator(
			    getCurrentContext(), Context.CONTEXT_ITEM, false,
			    getCurrentLoc());
		    source.setResettable(true);
		    iter.addElement(source);
		    iter.addElement(temp);
		    return new Words(getCurrentContext(), iter);
		} else {

		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: '}' expected");
		}

	    } else {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: 'Expr' expected");
	    }
	}
	return null;

    }

    private FTBaseIterator FTExtensionSelection() throws MXQueryException {

	FTBaseIterator selection;

	while (parseString("(#", true, false)) {

	    QName q = QName();
	    if (q == null) {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: 'QName' expected!");
	    }

	    Namespace ns = getCurrentContext().getNamespace(
		    q.getNamespacePrefix());
	    if (ns == null) {
		generateStaticError(
			ErrorCodes.E0081_STATIC_QUERY_UNKNOWN_NAMESPACE,
			"Pragma namespace unknown!");
	    }

	    while (!parseString("#)", true, false)) {
		if (index >= query.length() - 2) {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: '#)' expected!");
		}
		index++;
	    }

	    if (parseString("{", true, false)) {
		if ((selection = FTSelection()) != null) {
		    if (parseString("}", true, false)) {
			return selection;
		    } else {
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"Error while parsing: '}' expected");
		    }
		} else if (parseString("}", true, false)) {
		    generateStaticError(
			    ErrorCodes.E0079_STATIC_EXTENSION_EXPRESSION_DOESNT_CONTAIN_PRAGMA_OR_CURLY_CONTENT,
			    "Error while parsing: 'FTSelecion' expected");
		}
	    }

	    // return new EmptySequenceIterator(getCurrentContext(),
	    // getCurrentLoc());
	}
	return null;
    }

    private AnyAllOption FTAnyallOption() throws MXQueryException {

	if (parseKeyword("any")) {
	    if (parseKeyword("word") || parseString("word", false, false)) {
		return new AnyAllOption(AnyAllOption.ANY_ALL_OPT_ANYWORD);
	    }
	    return new AnyAllOption(AnyAllOption.ANY_ALL_OPT_ANY);
	}

	else if (parseKeyword("all")) {
	    if (parseKeyword("words") || parseString("words", false, false)) {
		return new AnyAllOption(AnyAllOption.ANY_ALL_OPT_ALLWORDS);
	    }
	    return new AnyAllOption(AnyAllOption.ANY_ALL_OPT_ALL);
	}

	else if (parseKeyword("phrase") || parseString("phrase", false, false)
		|| parseString("phrase", true, false)) {
	    return new AnyAllOption(AnyAllOption.ANY_ALL_OPT_PHRASE);
	}

	return null;
    }

    private Range FTTimes() throws MXQueryException {
	Range temp = null;
	if (parseKeyword("occurs")) {
	    if (((temp = FTRange()) != null)) {
		if (parseKeyword("times") || parseString("times", false, false)) {
		    generateStaticError(
			    ErrorCodes.FTST005_FTTimesOperator_NOT_SUPPORTED,
			    "FTTimes not supported");
		} else {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'times' expected");
		}
	    } else {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: 'FTRange' expected");
	    }

	}

	return temp;
    }

    /**
     * FTRange specifies a range of integer values, providing a minimum and
     * maximum value
     */
    private Range FTRange() throws MXQueryException {
	XDMIterator temp, from, to;
	XDMIterator[] iters = null;
	String range = null;

	if (parseKeyword("exactly") || parseString("exactly", true, false)
		|| parseString("exactly", false, false)
		|| parseString("exactly", false, true)) {
	    range = "exactly";

	    if (((temp = AdditiveExpr()) != null)) {
		iters = new XDMIterator[1];
		iters[0] = temp;
		return new Range(getCurrentContext(), iters, range);
	    } else {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: 'AdditiveExpr' expected");
	    }

	}

	if (parseKeyword("at") || parseString("at", false, false)
		|| parseString("at", true, false)
		|| parseString("at", false, true)) {
	    if (parseKeyword("least")) {
		range = "least";

		if (((temp = AdditiveExpr()) != null)) {
		    iters = new XDMIterator[1];
		    iters[0] = temp;
		    return new Range(getCurrentContext(), iters, range);
		} else {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'AdditiveExpr' expected");

		}

	    }
	    if (parseKeyword("most") || parseString("most", false, false)
		    || parseString("most", true, false)
		    || parseString("most", false, true)) {
		range = "most";

		if (((temp = AdditiveExpr()) != null)) {
		    iters = new XDMIterator[1];
		    iters[0] = temp;
		    return new Range(getCurrentContext(), iters, range);
		} else {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'AdditiveExpr' expected");
		}

	    }
	}

	if (parseKeyword("from")) {
	    if (((from = AdditiveExpr()) != null)) {
		if (parseKeyword("to")) {
		    range = "from";

		    if (((to = AdditiveExpr()) != null)) {
			iters = new XDMIterator[2];
			iters[0] = from;
			iters[1] = to;
			return new Range(getCurrentContext(), iters, range);
		    } else {
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"Error while parsing: 'AdditiveExpr' expected");
		    }
		} else {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'to' expected");
		}

	    } else {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: 'AdditiveExpr' expected");
	    }
	}

	return null;
    }

    private FTPositional FTPosFilter() throws MXQueryException {
	FTPositional temp;
	int oldIndex = index;
	if ((temp = FTOrder()) != null) {
	    return temp;
	} else if ((temp = FTWindow()) != null) {
	    return temp;
	} else if ((temp = FTDistance()) != null) {
	    return temp;
	} else if ((temp = FTScope()) != null) {
	    return temp;
	} else if ((temp = FTContent()) != null) {
	    return temp;
	}
	index = oldIndex;
	return null;

    }

    private FTPositional FTOrder() throws MXQueryException {

	if (parseKeyword("ordered") || parseString("ordered", false, false)) {
	    return new FTOrder("ordered");
	}

	return null;
    }

    /** */
    private FTPositional FTWindow() throws MXQueryException {

	String unit;
	XDMIterator additiveExpr;
	if (parseKeyword("window") || parseString("window", false, false)) {
	    if ((additiveExpr = AdditiveExpr()) != null) {
		if ((unit = FTUnit()) != null) {
		    return new FTWindow(additiveExpr, unit);
		} else {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'FTUnit' expected");
		}
	    } else {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: 'AdditiveExpr' expected");
	    }
	}

	return null;

    }

    private FTPositional FTDistance() throws MXQueryException {
	Range temp;
	String ftUnit;

	if (parseKeyword("distance") || parseString("distance", false, false)
		|| parseString("distance", true, false)) {
	    if ((temp = FTRange()) != null) {

		if ((ftUnit = FTUnit()) != null) {
		    return new FTDistance(temp, ftUnit);
		} else {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'FTUnit' expected");
		}
	    } else {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: 'FTRange' expected");
	    }
	}

	return null;
    }

    private String FTUnit() throws MXQueryException {
	if (parseKeyword("words")) {
	    return "words";
	} else if (parseKeyword("sentences")) {
	    return "sentences";
	} else if (parseKeyword("paragraphs")) {
	    return "paragraphs";
	}
	return null;
    }

    private FTPositional FTScope() throws MXQueryException {
	String unit = null;
	if (parseKeyword("same")) {
	    if ((unit = FTBigUnit()) != null) {
		return new FTScope(FTPositional.POS_SCOPE_SAME, unit,
			getCurrentLoc());
	    } else {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"'FTBigUnit' expected");
	    }
	} else if (parseKeyword("different")) {
	    if ((unit = FTBigUnit()) != null) {
		return new FTScope(FTPositional.POS_SCOPE_DIFFERENT, unit,
			getCurrentLoc());
	    } else {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"'FTBigUnit' expected");
	    }
	}
	return null;

    }

    private String FTBigUnit() throws MXQueryException {

	if (parseKeyword("sentence") || parseString("sentence", true, false)) {

	    return "sentence";
	} else if (parseKeyword("paragraph")
		|| parseString("paragraph", true, false)) {

	    return "paragraph";
	}
	return null;
    }

    private FTPositional FTContent() throws MXQueryException {

	int oldIndex = index;
	if (parseKeyword("at")) {
	    if (parseString("start", true, false)) {
		return new FTContent(FTPositional.POS_CONTENT_START,
			getCurrentLoc());
	    } else if (parseString("end", true, false)) {
		return new FTContent(FTPositional.POS_CONTENT_END,
			getCurrentLoc());
	    } else {
		generateStaticError(
			ErrorCodes.FTST0012_FTContentOperator_NOT_SUPPORTED,
			"'start' or 'end' expected");
	    }

	}

	else if (parseKeyword("entire")) {
	    if (parseString("content", true, false)) {
		return new FTContent(FTPositional.POS_CONTENT_ALL,
			getCurrentLoc());
	    } else {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"'content' expected");
	    }
	}
	index = oldIndex;
	return null;
    }

    private Vector FTMatchOptions() throws MXQueryException {
	MatchOption temp = null;
	Vector matchOptionList = new Vector();

	while ((temp = FTMatchOption()) != null) {
	    for (int i = 0; i < matchOptionList.size(); i++) {
		MatchOption mo = (MatchOption) matchOptionList.elementAt(i);
		if (mo.getMatchOptionType() == temp.getMatchOptionType()
			&& !(mo.equals(temp) || mo.getMatchOptionType() == MatchOption.MATCH_OPTION_TYPE_EXTENSION))
		    throw new StaticException(
			    ErrorCodes.FTST0019_CONFLICTING_MATCH_OPTIONS,
			    "Incompatible Match Option Settings",
			    getCurrentLoc());
	    }
	    matchOptionList.addElement(temp);
	}
	if (matchOptionList.size() == 0) {
	    return null;
	}

	return matchOptionList;
    }

    private MatchOption FTMatchOption() throws MXQueryException {
	int oldIndex = index;
	MatchOption temp = null;
	if ((temp = FTLanguageOption()) != null) {
	    return temp;
	} else if ((temp = FTWildCardOption()) != null) {
	    return temp;
	} else if ((temp = FTThesaurusOption()) != null) {
	    return temp;
	} else if ((temp = FTStemOption()) != null) {
	    return temp;
	} else if ((temp = FTDiacriticsOption()) != null) {
	    return temp;
	} else if ((temp = FTStopWordOption()) != null) {
	    return temp;
	} else if ((temp = FTExtensionOption()) != null) {
	    return temp;
	} else if ((temp = FTCaseOption()) != null) {
	    return temp;
	}
	index = oldIndex;
	return null;

    }

    private MatchOption FTStopWordOption() throws MXQueryException {
	Vector stopWords;
	Vector incl;
	Vector stopWordsInclExcl = new Vector();
	int oldIndex = index;
	if (parseKeyword("with")) {
	    if (parseKeyword("stop")) {
		if (parseKeyword("words")) {

		    if ((stopWords = FTStopWords()) != null) {
			while ((incl = FTStopWordsInclExcl()) != null) {
			    stopWordsInclExcl.addElement(incl);
			    // TODO: support of multiple inclExcl
			}
			return new FTStopWordsMatchOption(stopWords,
				stopWordsInclExcl);
		    }
		}
	    } else if (parseKeyword("default")) {
		if (parseKeyword("stop")) {
		    if (parseKeyword("words")) {
			while ((incl = FTStopWordsInclExcl()) != null) {
			    stopWordsInclExcl.addElement(incl);
			}
			// TODO: support of multiple inclExcl
			return new FTStopWordsMatchOption(null, null);
		    }
		}
	    }
	} else if (parseKeyword("without")) {
	    if (parseKeyword("stop")) {
		if (parseKeyword("words") || parseString("words", true, false)) {
		    return new FTStopWordsMatchOption();
		} else {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'words' expected");
		}
	    }
	}
	index = oldIndex;
	return null;
    }

    private Vector FTStopWords() throws MXQueryException {

	XDMIterator stringLiteral;
	Vector stringLiterals = new Vector();
	if (parseKeyword("at")) {
	    if ((stringLiteral = StringLiteral()) != null) {
		stringLiterals.addElement(stringLiteral);
		return stringLiterals;
	    } else {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: URILiteral expected");
	    }
	} else if (parseKeyword("(") || parseString("(", true, false)) {
	    if ((stringLiteral = StringLiteral()) != null) {

		while (parseKeyword(",") || parseString(",", false, false)) {
		    if ((stringLiteral = StringLiteral()) != null) {
			stringLiterals.addElement(stringLiteral);
		    }
		    // TODO: support of multiple string literals
		}

		if (parseKeyword(")") || parseString(")", false, false)) {
		    return stringLiterals;
		} else {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: ) expected");
		}
	    }
	}

	return null;
    }

    private MatchOption FTDiacriticsOption() throws MXQueryException {
	int oldIndex = index;
	if (parseKeyword("diacritics")
		|| parseString("diacritics", true, false)) {
	    if (parseKeyword("insensitive")
		    || parseString("insensitive", true, false)) {
		return new MatchOption(
			MatchOption.MATCH_OPTION_TYPE_DIACRITICS, false);
	    } else if (parseKeyword("sensitive")
		    || parseString("sensitive", true, false)) {
		return new MatchOption(
			MatchOption.MATCH_OPTION_TYPE_DIACRITICS, true);
	    } else {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: sensitive or insensitive expected");
	    }
	}
	index = oldIndex;
	return null;
    }

    private MatchOption FTCaseOption() throws MXQueryException {

	int oldIndex = index;
	if (parseKeyword("case") || parseString("case", true, false)) {
	    if (parseKeyword("insensitive")
		    || parseString("insensitive", false, false)
		    || parseString("insensitive", true, false)) {
		return new FTCaseMatchOption(false,
			FTCaseMatchOption.CASE_INSENSITIVE);
	    } else if (parseKeyword("sensitive")
		    || parseString("sensitive", false, false)
		    || parseString("sensitive", true, false)) {
		return new FTCaseMatchOption(true,
			FTCaseMatchOption.CASE_SENSITIVE);
	    } else {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"'case insensitive or sensitive' expected");
	    }
	} else if (parseKeyword("lowercase")
		|| parseString("lowercase", true, false)) {
	    return new FTCaseMatchOption(true, FTCaseMatchOption.CASE_LOWERCASE);
	} else if (parseKeyword("uppercase")
		|| parseString("uppercase", true, false)) {
	    return new FTCaseMatchOption(true, FTCaseMatchOption.CASE_UPPERCASE);
	}
	index = oldIndex;
	return null;

    }

    private MatchOption FTStemOption() throws MXQueryException {
	int oldIndex = index;
	if (parseKeyword("with")) {
	    if (parseKeyword("stemming")
		    || parseString("stemming", true, false)) {
		return new MatchOption(MatchOption.MATCH_OPTION_TYPE_STEMMING,
			true);
	    }
	}

	else if (parseKeyword("without") || parseString("without", true, false)) {
	    if (parseKeyword("stemming")
		    || parseString("stemming", true, false)) {
		return new MatchOption(MatchOption.MATCH_OPTION_TYPE_STEMMING,
			false);
	    }
	}
	index = oldIndex;
	return null;
    }

    private MatchOption FTThesaurusOption() throws MXQueryException {

	ObjectObjectPair temp;
	Vector ids = new Vector();
	int oldIndex = index;
	if (parseKeyword("with")) {
	    if (parseString("thesaurus", true, false)) {

		if (parseKeyword("default")) {
		    return new FTThesaurusMatchOption(new ObjectObjectPair(
			    "default", null));
		} else if ((temp = FTThesaurusID()) != null) {
		    return new FTThesaurusMatchOption(temp);
		}

		if (parseString("(", true, false)) {

		    boolean expr2 = parseKeyword("default");

		    boolean expr1 = ((temp = FTThesaurusID()) != null);
		    if (expr1 == true) {
			ids.addElement(temp);
		    }

		    if (expr1 || expr2) {
			while (parseString(",", true, false)) {
			    if ((temp = FTThesaurusID()) != null) {
				ids.addElement(temp);
			    } else {
				generateStaticError(
					ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
					"'FTThesaurusID' expected");
			    }

			}
			if (parseString(")", true, false)) {
			    return new FTThesaurusMatchOption(
				    new ObjectObjectPair(ids, null));
			} else {
			    generateStaticError(
				    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				    "')' expected");
			}

		    } else {
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"'default or ThesaurusID' expected");
		    }

		} else {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "'(' expected");
		}

	    }
	} else if (parseKeyword("without")) {
	    if (parseKeyword("thesaurus")) {
		return new MatchOption(MatchOption.MATCH_OPTION_TYPE_THESAURUS,
			false);
	    }
	}
	index = oldIndex;
	return null;
    }

    private ObjectObjectPair FTThesaurusID() throws MXQueryException {
	XDMIterator temp;
	String uriLiteral;
	Range ftRange;
	Vector v = new Vector();
	if (parseKeyword("at") || parseString("at", true, false)
		|| parseString("at", false, false)) {
	    if ((uriLiteral = StringLiteralAsString()) != null) {

		if (parseKeyword("relationship")) {
		    if ((temp = StringLiteral()) != null) {
			TextToken rel = (TextToken) temp.next();
			if (rel.getText().equals("synonyms")) {
			    v.addElement(temp);
			} else {
			    generateStaticError(
				    ErrorCodes.A0002_EC_NOT_SUPPORTED,
				    "relationship " + "\"" + rel.getText()
					    + "\"" + " not supported");
			}

		    } else {
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"Error while parsing: 'StringLiteral' expected");
			return null;
		    }
		}
		if ((ftRange = FTRange()) != null) {
		    if (parseString("levels", true, false)) {
			v.addElement(ftRange);
			generateStaticError(ErrorCodes.A0002_EC_NOT_SUPPORTED,
				"level not supported");
		    } else {
			generateStaticError(
				ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
				"Error while parsing: 'levels' expected");
			return null;
		    }
		}
		return new ObjectObjectPair(uriLiteral, v);

	    } else {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: 'URILiteral' expected");
	    }

	}
	return null;

    }

    private Vector FTStopWordsInclExcl() throws MXQueryException {

	Vector ftStopWords = null;

	if (parseKeyword("union")) {
	    if ((ftStopWords = FTStopWords()) != null) {
		return ftStopWords;
	    } else {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: 'FTStopWords' expected");
	    }
	}

	else if (parseKeyword("except")) {

	    if ((ftStopWords = FTStopWords()) != null) {
		return ftStopWords;
	    } else {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: 'FTStopWords' expected");
	    }
	}

	return null;
    }

    /**
     * A language option modifies token matching by specifying the language of
     * search tokens and phrases
     */
    private MatchOption FTLanguageOption() throws MXQueryException {
	XDMIterator temp;
	if (parseKeyword("language")) {
	    if ((temp = StringLiteral()) != null) {
		TextToken token = (TextToken) temp.next();
		if (!token.getText().equals("en")) {
		    generateStaticError(
			    ErrorCodes.FTST009_LANGUAGE_NOT_SUPPORTED,
			    "Only English supported");
		}

		return new FTLanguageMatchOption("language ", token.getText());
	    } else {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: 'StringLiteral' expected");
	    }
	}
	return null;

    }

    /**
     * A wildcard option modifies token and phrase matching by specifying
     * whether wildcards are used or not.
     */
    private MatchOption FTWildCardOption() throws MXQueryException {
	int oldIndex = index;
	if (parseKeyword("with") || parseString("with", false, false)) {
	    if (parseKeyword("wildcards")
		    || parseString("wildcards", true, false)) {
		return new MatchOption(MatchOption.MATCH_OPTION_TYPE_WILDCARD,
			true);
	    }

	}

	else if (parseKeyword("without") || parseString("without", true, false)) {
	    if (parseString("wildcards", true, false)
		    || parseKeyword("wildcards")) {
		return new MatchOption(MatchOption.MATCH_OPTION_TYPE_WILDCARD,
			false);
	    }

	}
	index = oldIndex;
	return null;
    }

    /** An extension option is a match option */
    private MatchOption FTExtensionOption() throws MXQueryException {
	XDMIterator temp;
	QName qname;
	if (parseKeyword("option")) {
	    if ((qname = QName()) != null) {

		if (qname.getNamespacePrefix() == null) {
		    generateStaticError(
			    ErrorCodes.E0081_STATIC_QUERY_UNKNOWN_NAMESPACE,
			    "Option Namespace not known");
		} else {
		    Namespace ns = getCurrentContext().getNamespace(
			    qname.getNamespacePrefix());
		    if (ns == null)
			generateStaticError(
				ErrorCodes.E0081_STATIC_QUERY_UNKNOWN_NAMESPACE,
				"Option Namespace not known");
		}

		if ((temp = StringLiteral()) != null) {
		    return new FTExtensionMatchOption(qname, temp);
		} else {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'StringLiteral' expected");
		}
	    } else {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: 'QName' expected");
	    }
	}

	return null;
    }

    /** The ignore option specifies a set of nodes whose content are ignored */
    private Iterator FTIgnoreOption() throws MXQueryException {
	Iterator temp;
	int oldIndex = index;
	if (parseKeyword("without")) {
	    if (parseKeyword("content")) {
		if (((temp = UnionExpr()) != null)) {
		    return temp;
		} else {
		    generateStaticError(
			    ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			    "Error while parsing: 'UnionExpr' expected");
		}

	    } else {
		generateStaticError(
			ErrorCodes.E0003_STATIC_NOT_A_VALID_GRAMMAR_ELEMENT,
			"Error while parsing: keyword 'content' expected");
	    }

	}
	index = oldIndex;
	return null;
    }

    public void setParallelExecution(boolean para) {
	parallelExecution = para;
    }
}

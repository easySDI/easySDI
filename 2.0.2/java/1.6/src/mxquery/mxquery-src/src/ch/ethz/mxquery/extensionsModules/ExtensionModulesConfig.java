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

package ch.ethz.mxquery.extensionsModules;

import java.util.Enumeration;
import java.util.Hashtable;
import java.util.Vector;

import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.functions.Function;
import ch.ethz.mxquery.functions.FunctionSignature;
import ch.ethz.mxquery.model.XDMIterator;

public class ExtensionModulesConfig {
	private static final String restNS = "http://www.zorba-xquery.com/zorba/rest-functions";
	private static final String restPackage = "ch.ethz.mxquery.extensionsModules.zorbaRest.";
	private static final String utilNS = "http://www.zorba-xquery.com/zorba/util-functions";
	private static final String utilPackage = "ch.ethz.mxquery.extensionsModules.util.";
	private static final String mathNS = "http://www.zorba-xquery.com/zorba/math-functions";
	private static final String mathPackage = "ch.ethz.mxquery.extensionsModules.math.";
	private static final String ioNS = "http://www.zorba-xquery.com/zorba/internal-functions";
	private static final String ioPackage = "ch.ethz.mxquery.extensionsModules.io.";

	Hashtable modules = new Hashtable();
	public ExtensionModulesConfig() {
		Vector restFunctions = new Vector();
		TypeInfo [] getParams = new TypeInfo[3];
		getParams[0] = new TypeInfo(Type.STRING,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
		getParams[1] = new TypeInfo(Type.START_TAG,Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
		getParams[2] = new TypeInfo(Type.START_TAG,Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
		FunctionSignature sig = new FunctionSignature(new QName(restNS,"zorba-rest","get"),getParams,FunctionSignature.EXTERNAL_FUNCTION,XDMIterator.EXPR_CATEGORY_SEQUENTIAL,true);
		restFunctions.addElement(new Function(restPackage+"HttpGet",sig,null));

		getParams = new TypeInfo[2];
		getParams[0] = new TypeInfo(Type.STRING,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
		getParams[1] = new TypeInfo(Type.START_TAG,Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
		sig = new FunctionSignature(new QName(restNS,"zorba-rest","get"),getParams,FunctionSignature.EXTERNAL_FUNCTION,XDMIterator.EXPR_CATEGORY_SEQUENTIAL,true);
		restFunctions.addElement(new Function(restPackage+"HttpGet",sig,null));

		getParams = new TypeInfo[1];
		getParams[0] = new TypeInfo(Type.STRING,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
		sig = new FunctionSignature(new QName(restNS,"zorba-rest","get"),getParams,FunctionSignature.EXTERNAL_FUNCTION,XDMIterator.EXPR_CATEGORY_SEQUENTIAL,true);
		restFunctions.addElement(new Function(restPackage+"HttpGet",sig,null));
		
		getParams = new TypeInfo[4];
		getParams[0] = new TypeInfo(Type.STRING,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
		getParams[1] = new TypeInfo(Type.START_TAG,Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
		getParams[2] = new TypeInfo(Type.START_TAG,Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
		getParams[3] = new TypeInfo(Type.STRING,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
		sig = new FunctionSignature(new QName(restNS,"zorba-rest","getTidy"),getParams,FunctionSignature.EXTERNAL_FUNCTION,XDMIterator.EXPR_CATEGORY_SEQUENTIAL,true);
		restFunctions.addElement(new Function(restPackage+"HttpGet",sig,null));

		getParams = new TypeInfo[3];
		getParams[0] = new TypeInfo(Type.STRING,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
		getParams[1] = new TypeInfo(Type.START_TAG,Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
		getParams[2] = new TypeInfo(Type.STRING,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
		sig = new FunctionSignature(new QName(restNS,"zorba-rest","getTidy"),getParams,FunctionSignature.EXTERNAL_FUNCTION,XDMIterator.EXPR_CATEGORY_SEQUENTIAL,true);
		restFunctions.addElement(new Function(restPackage+"HttpGet",sig,null));

		getParams = new TypeInfo[2];
		getParams[0] = new TypeInfo(Type.STRING,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
		getParams[1] = new TypeInfo(Type.STRING,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
		sig = new FunctionSignature(new QName(restNS,"zorba-rest","getTidy"),getParams,FunctionSignature.EXTERNAL_FUNCTION,XDMIterator.EXPR_CATEGORY_SEQUENTIAL,true);
		restFunctions.addElement(new Function(restPackage+"HttpGet",sig,null));

		
		
		getParams = new TypeInfo[3];
		getParams[0] = new TypeInfo(Type.STRING,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
		getParams[1] = new TypeInfo(Type.START_TAG,Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
		getParams[2] = new TypeInfo(Type.START_TAG,Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
		sig = new FunctionSignature(new QName(restNS,"zorba-rest","post"),getParams,FunctionSignature.EXTERNAL_FUNCTION,XDMIterator.EXPR_CATEGORY_SEQUENTIAL,true);
		restFunctions.addElement(new Function(restPackage+"HttpPost",sig,null));

		getParams = new TypeInfo[2];
		getParams[0] = new TypeInfo(Type.STRING,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
		getParams[1] = new TypeInfo(Type.START_TAG,Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
		sig = new FunctionSignature(new QName(restNS,"zorba-rest","post"),getParams,FunctionSignature.EXTERNAL_FUNCTION,XDMIterator.EXPR_CATEGORY_SEQUENTIAL,true);
		restFunctions.addElement(new Function(restPackage+"HttpPost",sig,null));

		getParams = new TypeInfo[1];
		getParams[0] = new TypeInfo(Type.STRING,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
		sig = new FunctionSignature(new QName(restNS,"zorba-rest","post"),getParams,FunctionSignature.EXTERNAL_FUNCTION,XDMIterator.EXPR_CATEGORY_SEQUENTIAL,true);
		restFunctions.addElement(new Function(restPackage+"HttpPost",sig,null));
		
		getParams = new TypeInfo[3];
		getParams[0] = new TypeInfo(Type.STRING,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
		getParams[1] = new TypeInfo(Type.START_TAG,Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
		getParams[2] = new TypeInfo(Type.START_TAG,Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
		sig = new FunctionSignature(new QName(restNS,"zorba-rest","put"),getParams,FunctionSignature.EXTERNAL_FUNCTION,XDMIterator.EXPR_CATEGORY_SEQUENTIAL,true);
		restFunctions.addElement(new Function(restPackage+"HttpPut",sig,null));

		getParams = new TypeInfo[2];
		getParams[0] = new TypeInfo(Type.STRING,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
		getParams[1] = new TypeInfo(Type.START_TAG,Type.OCCURRENCE_IND_ZERO_OR_ONE,null,null);
		sig = new FunctionSignature(new QName(restNS,"zorba-rest","put"),getParams,FunctionSignature.EXTERNAL_FUNCTION,XDMIterator.EXPR_CATEGORY_SEQUENTIAL,true);
		restFunctions.addElement(new Function(restPackage+"HttpPut",sig,null));

		getParams = new TypeInfo[1];
		getParams[0] = new TypeInfo(Type.STRING,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
		sig = new FunctionSignature(new QName(restNS,"zorba-rest","put"),getParams,FunctionSignature.EXTERNAL_FUNCTION,XDMIterator.EXPR_CATEGORY_SEQUENTIAL,true);
		restFunctions.addElement(new Function(restPackage+"HttpPut",sig,null));

		
		modules.put(restNS,restFunctions);

		Vector utilFunctions = new Vector();
		getParams = new TypeInfo[1];
		getParams[0] = new TypeInfo(Type.STRING,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
		sig = new FunctionSignature(new QName(utilNS,"zorba-util","tdoc"),getParams,FunctionSignature.EXTERNAL_FUNCTION,XDMIterator.EXPR_CATEGORY_SIMPLE,false);
		utilFunctions.addElement(new Function(utilPackage+"Doc-Tidy",sig,null));

		getParams = new TypeInfo[1];
		getParams[0] = new TypeInfo(Type.STRING,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
		sig = new FunctionSignature(new QName(utilNS,"zorba-util","tidy"),getParams,FunctionSignature.EXTERNAL_FUNCTION,XDMIterator.EXPR_CATEGORY_SIMPLE,false);
		utilFunctions.addElement(new Function(utilPackage+"Tidy",sig,null));

		getParams = new TypeInfo[1];
		getParams[0] = new TypeInfo(Type.STRING,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
		sig = new FunctionSignature(new QName(utilNS,"zorba-util","parseStringToXML"),getParams,FunctionSignature.EXTERNAL_FUNCTION,XDMIterator.EXPR_CATEGORY_SIMPLE,false);
		utilFunctions.addElement(new Function(utilPackage+"ParseStringToXML",sig,null));

		getParams = new TypeInfo[1];
		getParams[0] = new TypeInfo(Type.NODE,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
		sig = new FunctionSignature(new QName(utilNS,"zorba-util","serializeXMLToString"),getParams,FunctionSignature.EXTERNAL_FUNCTION,XDMIterator.EXPR_CATEGORY_SIMPLE,false);
		utilFunctions.addElement(new Function(utilPackage+"SerializeXMLToString",sig,null));

		getParams = new TypeInfo[0];
		sig = new FunctionSignature(new QName(utilNS,"zorba-util","random"),getParams,FunctionSignature.EXTERNAL_FUNCTION,XDMIterator.EXPR_CATEGORY_SIMPLE,true);
		utilFunctions.addElement(new Function(utilPackage+"Random",sig,null));
		
		getParams = new TypeInfo[1];
		getParams[0] = new TypeInfo(Type.INTEGER,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
		sig = new FunctionSignature(new QName(utilNS,"zorba-util","random"),getParams,FunctionSignature.EXTERNAL_FUNCTION,XDMIterator.EXPR_CATEGORY_SIMPLE,true);
		utilFunctions.addElement(new Function(utilPackage+"Random",sig,null));

		getParams = new TypeInfo[0];
		sig = new FunctionSignature(new QName(utilNS,"zorba-util","uuid"),getParams,FunctionSignature.EXTERNAL_FUNCTION,XDMIterator.EXPR_CATEGORY_SIMPLE,true);
		utilFunctions.addElement(new Function(utilPackage+"Uuid",sig,null));

		
		modules.put(utilNS,utilFunctions);
		
		Vector mathFunctions = new Vector();
		getParams = new TypeInfo[1];
		getParams[0] = new TypeInfo(Type.NUMBER,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
		sig = new FunctionSignature(new QName(mathNS,"zorba-math","sqrt"),getParams,FunctionSignature.EXTERNAL_FUNCTION,XDMIterator.EXPR_CATEGORY_SIMPLE,false);
		mathFunctions.addElement(new Function(mathPackage+"Sqrt",sig,null));

		getParams = new TypeInfo[1];
		getParams[0] = new TypeInfo(Type.DOUBLE,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
		sig = new FunctionSignature(new QName(mathNS,"zorba-math","exp"),getParams,FunctionSignature.EXTERNAL_FUNCTION,XDMIterator.EXPR_CATEGORY_SIMPLE,false);
		mathFunctions.addElement(new Function(mathPackage+"TransMathExp",sig,null));

		getParams = new TypeInfo[1];
		getParams[0] = new TypeInfo(Type.DOUBLE,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
		sig = new FunctionSignature(new QName(mathNS,"zorba-math","log"),getParams,FunctionSignature.EXTERNAL_FUNCTION,XDMIterator.EXPR_CATEGORY_SIMPLE,false);
		mathFunctions.addElement(new Function(mathPackage+"TransMathLog",sig,null));

		getParams = new TypeInfo[1];
		getParams[0] = new TypeInfo(Type.DOUBLE,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
		sig = new FunctionSignature(new QName(mathNS,"zorba-math","sin"),getParams,FunctionSignature.EXTERNAL_FUNCTION,XDMIterator.EXPR_CATEGORY_SIMPLE,false);
		mathFunctions.addElement(new Function(mathPackage+"TransMathSin",sig,null));

		getParams = new TypeInfo[1];
		getParams[0] = new TypeInfo(Type.DOUBLE,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
		sig = new FunctionSignature(new QName(mathNS,"zorba-math","cos"),getParams,FunctionSignature.EXTERNAL_FUNCTION,XDMIterator.EXPR_CATEGORY_SIMPLE,false);
		mathFunctions.addElement(new Function(mathPackage+"TransMathCos",sig,null));
		
		getParams = new TypeInfo[1];
		getParams[0] = new TypeInfo(Type.DOUBLE,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
		sig = new FunctionSignature(new QName(mathNS,"zorba-math","tan"),getParams,FunctionSignature.EXTERNAL_FUNCTION,XDMIterator.EXPR_CATEGORY_SIMPLE,false);
		mathFunctions.addElement(new Function(mathPackage+"TransMathTan",sig,null));

		getParams = new TypeInfo[1];
		getParams[0] = new TypeInfo(Type.DOUBLE,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
		sig = new FunctionSignature(new QName(mathNS,"zorba-math","asin"),getParams,FunctionSignature.EXTERNAL_FUNCTION,XDMIterator.EXPR_CATEGORY_SIMPLE,false);
		mathFunctions.addElement(new Function(mathPackage+"TransMathAsin",sig,null));

		getParams = new TypeInfo[1];
		getParams[0] = new TypeInfo(Type.DOUBLE,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
		sig = new FunctionSignature(new QName(mathNS,"zorba-math","acos"),getParams,FunctionSignature.EXTERNAL_FUNCTION,XDMIterator.EXPR_CATEGORY_SIMPLE,false);
		mathFunctions.addElement(new Function(mathPackage+"TransMathAcos",sig,null));
		
		getParams = new TypeInfo[1];
		getParams[0] = new TypeInfo(Type.DOUBLE,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
		sig = new FunctionSignature(new QName(mathNS,"zorba-math","atan"),getParams,FunctionSignature.EXTERNAL_FUNCTION,XDMIterator.EXPR_CATEGORY_SIMPLE,false);
		mathFunctions.addElement(new Function(mathPackage+"TransMathAtan",sig,null));

		getParams = new TypeInfo[2];
		getParams[0] = new TypeInfo(Type.NUMBER,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
		getParams[1] = new TypeInfo(Type.NUMBER,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
		sig = new FunctionSignature(new QName(mathNS,"zorba-math","pow"),getParams,FunctionSignature.EXTERNAL_FUNCTION,XDMIterator.EXPR_CATEGORY_SIMPLE,false);
		mathFunctions.addElement(new Function(mathPackage+"Pow",sig,null));
		
		modules.put(mathNS,mathFunctions);
		
		Vector ioFunctions = new Vector();
		
		getParams = new TypeInfo[1];
		getParams[0] = new TypeInfo(Type.ITEM,Type.OCCURRENCE_IND_ZERO_OR_MORE,null,null);
		sig = new FunctionSignature(new QName(ioNS,"zorba","print"),getParams,FunctionSignature.EXTERNAL_FUNCTION,XDMIterator.EXPR_CATEGORY_SIMPLE,false);
		ioFunctions.addElement(new Function(ioPackage+"Print",sig,null));

		sig = new FunctionSignature(new QName(ioNS,"zorba","readline"),new TypeInfo[]{},FunctionSignature.EXTERNAL_FUNCTION,XDMIterator.EXPR_CATEGORY_SIMPLE,true);
		ioFunctions.addElement(new Function(ioPackage+"ReadlineIterator",sig,null));
		
		modules.put(ioNS,ioFunctions);

	}
	public Vector getModuleFunctions(String moduleNamespace) {
		return (Vector)modules.get(moduleNamespace);
	}
	public Enumeration getAllModuleNamespaces() {
		return modules.keys();
	}
}

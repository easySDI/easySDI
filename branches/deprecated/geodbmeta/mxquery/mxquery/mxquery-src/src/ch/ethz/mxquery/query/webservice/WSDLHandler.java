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

package ch.ethz.mxquery.query.webservice;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileWriter;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.PrintWriter;
import java.util.Hashtable;
//import java.util.Scanner;
import java.util.Vector;

import org.kxml2.io.KXmlParser;
import org.xmlpull.v1.XmlPullParserException;

import ch.ethz.mxquery.contextConfig.CompilerOptions;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.Namespace;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeDictionary;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.functions.Function;
import ch.ethz.mxquery.functions.FunctionSignature;
import ch.ethz.mxquery.iterators.TokenIterator;
import ch.ethz.mxquery.iterators.scripting.WSFunction;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.query.XQCompiler;
import ch.ethz.mxquery.query.PreparedStatement;
import ch.ethz.mxquery.query.impl.CompilerImpl;
import ch.ethz.mxquery.query.parser.SchemaParser;
import ch.ethz.mxquery.util.LineReader;
import ch.ethz.mxquery.util.StringReader;
import ch.ethz.mxquery.xdmio.XDMInputFactory;
import ch.ethz.mxquery.xdmio.XDMSerializer;
import ch.ethz.mxquery.xdmio.XDMSerializerSettings;

/**
 * Class to parse a WSDL document and to add the resulting WebService methods
 * into the Function Gallery.
 * 
 * @author David Graf
 * 
 */
public final class WSDLHandler {
	private String wsdl;
	private String wsdlParserCode;
	private String wsdlSchema;

	/**
	 * Constructor.
	 * 
	 * @param wsdlParserUrl
	 *            URL of the WSDL file <br />
	 *            TODO: make it also possible with a URI!
	 */
	public WSDLHandler(String wsdlParserUrl) {
		this.wsdl = wsdlParserUrl;
	}

	/**
	 * Parses the WSDL file (with XQUERYP!).
	 * 
	 * @param wsdl
	 *            wsdl file
	 * @param runtime
	 *            static content
	 * @param namespace
	 * @throws MXQueryException
	 * @throws FileNotFoundException 
	 */
	private void parseWSDL(String wsdl, Context context,
			Namespace namespace, String serviceName, String endpointName, QueryLocation loc, CompilerOptions co) throws MXQueryException, IOException {
		
		String functionNamePrefix = null;
		if (namespace != null) {
			functionNamePrefix = namespace.getNamespacePrefix();
		}
		
		Context wsdlContext = new Context();
		XQCompiler wsdlCompiler = new CompilerImpl();
		PreparedStatement statement;
		XDMIterator result;
		
		statement = wsdlCompiler.compile(wsdlContext, this.wsdlParserCode,co);
		wsdlContext.setVariableValue(new QName("wsdl"), XDMInputFactory.createXMLInput(wsdlContext, new StringReader(wsdl), true,context.getInputValidationMode(),QueryLocation.OUTSIDE_QUERY_LOC), true, true);
		wsdlContext.setVariableValue(new QName("servicename"), new TokenIterator(wsdlContext,serviceName,loc),true, true);
		wsdlContext.setVariableValue(new QName("endpoint"), new TokenIterator(wsdlContext,endpointName, loc),true, true);
		result = statement.evaluate();
		XDMSerializerSettings ser = new XDMSerializerSettings();
		ser.setOmitXMLDeclaration(true);
		XDMSerializer ip = new XDMSerializer(ser);
		String filteredWSDL = ip.eventsToXML(result);
		

		//if there are more than one port in the this filtered result, raise an error. In such cases user needs to
		//explicitly specify the name of the endpoint he intends to use (as an option!)

		if (filteredWSDL.indexOf("Error1") != -1){
			throw new MXQueryException(ErrorCodes.A0014_Unspecified_Service_Name,"Error while parsing the WSDL: there are more than one available service name Specify one of them as an 'servicename' option", loc);
		}
		if (filteredWSDL.indexOf("Error2") != -1){
			throw new MXQueryException(ErrorCodes.A0015_Unspecified_Endpoint,"Error while parsing the WSDL: there are more than one available port name ! Specify one of them as an 'endpoint' option", loc);
		}
		
		//Extracting the targetNamespace's prefix [needed later for checking the types]
//		String stringContainingNSDeclaration = wsdl;
//		int tnsIndex = -1;
//		int tempIndex = stringContainingNSDeclaration.indexOf("targetNamespace");
//		if (tempIndex != -1){
//			if (stringContainingNSDeclaration.substring(0,tempIndex).contains(namespace.getURI())){
//				stringContainingNSDeclaration = stringContainingNSDeclaration.substring(0,tempIndex);
//			} else{
//				stringContainingNSDeclaration = stringContainingNSDeclaration.substring(stringContainingNSDeclaration.indexOf(namespace.getURI())+namespace.getURI().length());
//			}
//		}
//		tnsIndex = stringContainingNSDeclaration.indexOf(namespace.getURI());
//		int xmlns4tnsIndex = stringContainingNSDeclaration.substring(0,tnsIndex).lastIndexOf("xmlns");
//		String tnsPrefix = "";
//		for (int i= xmlns4tnsIndex+6; i<tnsIndex-1; i++){
//			if (stringContainingNSDeclaration.charAt(i) !='=' && stringContainingNSDeclaration.charAt(i) !=' '){
//				tnsPrefix = tnsPrefix +stringContainingNSDeclaration.charAt(i);
//			}
//		}
		
		
		//create a hash table of prefixes and namespaces, they might be used by WSFunction class
		Hashtable declaredNSs = extractNSs(wsdl);
		
		
		//Extracting the schema part from the wsdl file
		int endOfSchemaIndex = filteredWSDL.indexOf("<services>");
		if (endOfSchemaIndex == -1) 
			throw new MXQueryException(ErrorCodes.A0016_Endpoint_Does_Not_Exist,"The specified endpoint doesn't exist. Please set the 'servicename' and 'endpoint' options correctly",loc);
		this.wsdlSchema = filteredWSDL.substring(0, endOfSchemaIndex);
		
		//Some namespaces needs to be imported (like 'soapenc'): 		
		//1) finding the right prefix of the schema [special case: no-schema!]
		int xsIndex = wsdlSchema.indexOf("http://www.w3.org/2001/XMLSchema");
		if (xsIndex!= -1)
		{
			int xmlnsIndex = wsdlSchema.substring(0,xsIndex).lastIndexOf("xmlns");
			String schemaPrefix = "";
			for (int i= xmlnsIndex+6; i<xsIndex-1; i++){
				if (wsdlSchema.charAt(i) !='=' && wsdlSchema.charAt(i) !=' '){
					schemaPrefix = schemaPrefix +wsdlSchema.charAt(i);
				}
			}
			//2) injecting the <prefix:import> for those namepaces 
			int firstClosingTag = wsdlSchema.indexOf('>');
			wsdlSchema = wsdlSchema.substring(0,firstClosingTag+1)+"<"+schemaPrefix+
			":import namespace=\"http://schemas.xmlsoap.org/soap/encoding/\" schemaLocation=\"http://schemas.xmlsoap.org/soap/encoding/\"/>"+

			// a sample subschema, for testing the unionType mapping!
//			"<xsd:simpleType name=\"baseType1\">"+
//				"<xsd:restriction base=\"xsd:integer\">"+
//					"<xsd:maxExclusive value =\"100\" />"+
//				"</xsd:restriction>"+
//			"</xsd:simpleType>"+
//			"<xsd:simpleType name=\"simp\">"+
//				"<xsd:union memberTypes=\"xsd:double xsd:positiveInteger typens:baseType1\"/>"+
//			"</xsd:simpleType>"+
//			"<xsd:element name=\"simpleElement\" type=\"typens:simp\"/>"+
//
//			"<xsd:element name=\"simpleElement2\">"+
//				"<xsd:simpleType>"+
//					"<xsd:union memberTypes=\"xsd:integer xsd:string xsd:token\"/>"+
//				"</xsd:simpleType>"+
//			"</xsd:element>"+
//			
//			"<xsd:element name=\"simpleElement3\">"+
//				"<xsd:simpleType>"+
//					"<xsd:union>"+
//						"<xsd:simpleType>"+
//						  "<xsd:list itemType=\"xsd:integer\"/>"+
//						"</xsd:simpleType>"+
//						"<xsd:simpleType>"+
//				          "<xsd:restriction base=\"xsd:positiveInteger\">"+
//				          	"<xsd:maxExclusive value=\"100\"/>"+
//				          "</xsd:restriction>"+
//						"</xsd:simpleType>"+
//					"</xsd:union>"+
//				"</xsd:simpleType>"+
//			"</xsd:element>"+
			
			wsdlSchema.substring(firstClosingTag+1);
			
			//parsing the schema and adding the UDTs to the dictionary and context!
			//TODO: using the new API and avoiding the materialization 
			LineReader scan = new LineReader(new StringReader(wsdlSchema));
			File matterSchema = new File("temp.xsd");
			PrintWriter print = null;
			try {
				print = new PrintWriter(new FileWriter(matterSchema));
				String ln = scan.readLine();
				while (ln != null){
					print.print(ln+"\n");
					ln = scan.readLine();
				}
			} finally {
				if (print != null)
					print.close();		
			}
			//SchemaParser schemaParser = new SchemaParser();
			TypeDictionary dict = new TypeDictionary();
			SchemaParser.parseSchema("temp.xsd",namespace.toString(), dict, loc);

			//due to incompatibility between WSDL and XQuery types, there must be some pre-processing on the schema
			//pre-process1: Union Types -> closest common ancestor! 
			mapUnionTypes(schemaPrefix, dict);
			//pre-process2: List Types -> AtomicTypes* !
			//TODO: mapListTypes()
			
			//Parsing the new schema
			scan = new LineReader(new StringReader(wsdlSchema));
			matterSchema = new File("temp.xsd");
			try {
				print = new PrintWriter(new FileWriter(matterSchema));
				String ln = scan.readLine();
				while (ln != null ){
					print.print(ln+"\n");
					ln = scan.readLine();
				}
			}finally {
				if (print != null)
					print.close();		
			}
			//schemaParser = new SchemaParser();
			dict = new TypeDictionary();
			SchemaParser.parseSchema("temp.xsd",namespace.toString(), dict, loc);
			//FIXME: This sets the global dictionary, not a local one
			Context.setDictionary(dict);
			matterSchema.deleteOnExit();
		}
		
		filteredWSDL = filteredWSDL.substring(endOfSchemaIndex);
		
		KXmlParser kxp = new KXmlParser();
		try {
			String host = null;
			QName functionName = null;
			String style = null;
			String soapAction = null;
			String inputNamespace = null;
			String inputEncoding = null;
			String returnName = null;
			String returnType = null;
			Vector paramNames = new Vector();
			Vector paramTypes = new Vector();
			kxp.setInput(new StringReader(filteredWSDL));
			int type;
			while ((type = kxp.next()) != KXmlParser.END_DOCUMENT) {
				if (type == KXmlParser.START_TAG) {
					if (kxp.getName().equals("service")) {
						host = kxp.getAttributeValue(0);
					} else if (kxp.getName().equals("function")) {
						functionName = new QName(namespace.getURI(),functionNamePrefix, kxp.getAttributeValue(null,
								"name"));
						style = kxp.getAttributeValue(null, "style");
						soapAction = kxp.getAttributeValue(null, "soapaction");
						inputNamespace = kxp.getAttributeValue(null,
								"inputnamespace");
						inputEncoding = kxp.getAttributeValue(null,
								"inputencoding");
						returnName = kxp.getAttributeValue(null,
								"returnName");
						returnType = kxp.getAttributeValue(null,
						"returnType");
						paramNames.removeAllElements();
						paramTypes.removeAllElements();
					} else if (kxp.getName().equals("param")) {
						paramNames.addElement(kxp.getAttributeValue(0));
						paramTypes.addElement(kxp.getAttributeValue(1));
					}
				} else if (type == KXmlParser.END_TAG) {
					if (kxp.getName().equals("function")) {
						String[] arrPNames = new String[paramNames.size()];
						paramNames.copyInto(arrPNames);
						String[] arrPTypes = new String[paramTypes.size()];
						paramTypes.copyInto(arrPTypes);
						WSFunction wf = new WSFunction(functionName, style,
								soapAction, inputNamespace, declaredNSs, inputEncoding,
								host, arrPNames, arrPTypes, returnName, returnType);

						TypeInfo [] params = new TypeInfo[arrPTypes.length];
						for (int i=0;i<params.length;i++) {
							//the TypeInfo is not really used, storage and retrieval of the functions is done using 
							// only function name and arity
							params[i] = new TypeInfo(Type.ITEM,Type.OCCURRENCE_IND_ZERO_OR_MORE,null,null);
						}
						FunctionSignature signature = new FunctionSignature(functionName,params,FunctionSignature.EXTERNAL_FUNCTION,XDMIterator.EXPR_CATEGORY_SEQUENTIAL,true);
						Function function = new Function(null,signature,wf);
						context.addFunction(function, true, true);
					}
				}
			}
		} catch (IOException e) {
			throw new MXQueryException(ErrorCodes.A0007_EC_IO, e,"I/O Error when handling a WSDL file", null);
		} catch (XmlPullParserException e) {
			throw new MXQueryException(ErrorCodes.A0003_EC_WSDL_IS_ERROR_MSG, e,"Parse Error when handling a WSDL file", null);
		}
	}
	
	private Hashtable extractNSs(String wsdl){
		Hashtable ns = new Hashtable();
		String rootElement = wsdl.substring(wsdl.indexOf("xmlns"));
		rootElement = rootElement.substring(0,rootElement.indexOf(">"));
		while(rootElement.indexOf("xmlns") != -1){
			rootElement = rootElement.substring(rootElement.indexOf("xmlns")+5);
			String nextNSDecl = null;
			int tempIndex = rootElement.indexOf("xmlns");
			if (tempIndex == -1){
				nextNSDecl = rootElement.trim();
			}else {
				nextNSDecl = rootElement.substring(0,tempIndex);
			}
			String prefix = nextNSDecl.substring(0,nextNSDecl.indexOf("="));
			String uri = nextNSDecl.substring(nextNSDecl.indexOf("=")+1);
			if (prefix.indexOf(":") != -1){//excluding the default namespace declaration
				prefix = prefix.substring(prefix.indexOf(":")+1).trim();
				//TODO: check for single quote's as well
				uri = uri.substring(uri.indexOf("\"")+1);
				uri = uri.substring(0,uri.indexOf("\"")).trim();
				ns.put(prefix, uri);	
			}
		}
		return ns;
	}
	
	private void mapUnionTypes(String schemaPrefix, TypeDictionary dict) throws MXQueryException{
		//finding the prefix for schema's targetnamespace!
		int indexOfTargetNSDec = this.wsdlSchema.indexOf("targetNamespace");
		String schemaTargetNs = this.wsdlSchema.substring(indexOfTargetNSDec + 15);
		int indexForSingleQuoteCheck = schemaTargetNs.indexOf("\"");
		if (schemaTargetNs.indexOf("\'") != -1 && schemaTargetNs.indexOf("\'") < indexForSingleQuoteCheck ){
			indexForSingleQuoteCheck = schemaTargetNs.indexOf("\'");
		}
		schemaTargetNs = schemaTargetNs.substring(indexForSingleQuoteCheck + 1);
		indexForSingleQuoteCheck = schemaTargetNs.indexOf("\"");
		if (schemaTargetNs.indexOf("\'") != -1 && schemaTargetNs.indexOf("\'") < indexForSingleQuoteCheck ){
			indexForSingleQuoteCheck = schemaTargetNs.indexOf("\'");
		}
		schemaTargetNs = schemaTargetNs.substring(0,indexForSingleQuoteCheck).trim();
		String schemaTargetNSPrefix = "";
		String[] schemaNSDels = this.wsdlSchema.split("xmlns:");
		for (int i = 0; i < schemaNSDels.length ; i++){
			if (schemaNSDels[i].indexOf(schemaTargetNs)!= -1){
				schemaTargetNSPrefix = schemaNSDels[i].substring(0,schemaNSDels[i].indexOf(schemaTargetNs));
				schemaTargetNSPrefix = schemaTargetNSPrefix.substring(0,schemaTargetNSPrefix.indexOf("=")).trim();
			}
		}

		String unionTag = "<"+schemaPrefix+":union";
		while (this.wsdlSchema.indexOf(unionTag) != -1){
			int CCA = Type.ANY_SIMPLE_TYPE;
			boolean hasNestedTypeDef = false;
			boolean isAnonymous = false;
			int indexOfStartingSimpleType;
			int indexOfEndingSimpleType;

			String firstHalf = this.wsdlSchema.substring(0,this.wsdlSchema.indexOf(unionTag));
			String secondHalf = this.wsdlSchema.substring(this.wsdlSchema.indexOf(unionTag)+1,this.wsdlSchema.length());
			if ((secondHalf.indexOf("memberTypes") == -1) ||(secondHalf.indexOf("memberTypes") > secondHalf.indexOf(">"))){
				hasNestedTypeDef = true;
			}
			indexOfStartingSimpleType = firstHalf.lastIndexOf("<"+schemaPrefix+":simpleType");
			if (!hasNestedTypeDef){
				indexOfEndingSimpleType = secondHalf.indexOf("</"+schemaPrefix+":simpleType") + firstHalf.length();
			}else{
				//TODO: nested unions
				//if (secondHalf.indexOf("</"+schemaPrefix+":union") > secondHalf.indexOf("<"+schemaPrefix+":union")){
				//} else{
					String tempString = secondHalf.substring(secondHalf.indexOf("</"+schemaPrefix+":union"));
					indexOfEndingSimpleType = tempString.indexOf("</"+schemaPrefix+":simpleType") + firstHalf.length()+secondHalf.length() - tempString.length();
				//}
			}
			String unionTypDecExcerpt = this.wsdlSchema.substring(indexOfStartingSimpleType,indexOfEndingSimpleType+1)+"</"+schemaPrefix+":simpleType>";
			String beforeUnionType = this.wsdlSchema.substring(0,indexOfStartingSimpleType);
			String afterUnionType = this.wsdlSchema.substring(indexOfEndingSimpleType);
			afterUnionType = afterUnionType.substring(afterUnionType.indexOf("</"+schemaPrefix+":simpleType")+13+schemaPrefix.length()).substring(afterUnionType.indexOf(">")+1);
			if (unionTypDecExcerpt.indexOf("name") == -1 && unionTypDecExcerpt.indexOf("name") < unionTypDecExcerpt.indexOf(">")){
				isAnonymous = true;
			}
			if (!hasNestedTypeDef){
				String memberTypesSubstring = unionTypDecExcerpt.substring(unionTypDecExcerpt.indexOf("memberTypes")+11,unionTypDecExcerpt.indexOf("/>"));
				int indexOfFirstQuotation = 0;
				indexOfFirstQuotation = memberTypesSubstring.indexOf("\"");
				if (memberTypesSubstring.indexOf("'") != -1){
					indexOfFirstQuotation = memberTypesSubstring.indexOf("'"); 
				}
				memberTypesSubstring = memberTypesSubstring.substring(indexOfFirstQuotation+1);
				int indexOfSecondQuotation = 0;
				indexOfSecondQuotation = memberTypesSubstring.indexOf("\"");
				if (memberTypesSubstring.indexOf("'") != -1){
					indexOfSecondQuotation = memberTypesSubstring.indexOf("'"); 
				}
				memberTypesSubstring = memberTypesSubstring.substring(0,indexOfSecondQuotation).trim();
				Vector memberTypes = new Vector();
				while (memberTypesSubstring.indexOf(" ") != -1){
					String tempType = memberTypesSubstring.substring(0,memberTypesSubstring.indexOf(" "));
					memberTypes.add(tempType);
					memberTypesSubstring = memberTypesSubstring.substring(memberTypesSubstring.indexOf(" ")+1).trim();
				} ;
				memberTypes.add(memberTypesSubstring);
				//TODO: in case of more than two memberTypes, it's not trivial in which order they should be processed ]
				QName firsType = new QName((String)memberTypes.elementAt(0));
				String nameSpaceForPrefix = this.wsdlSchema.substring(this.wsdlSchema.indexOf("xmlns:"+firsType.getNamespacePrefix()));
				//TODO: check for single quote as well [in addition to double quote ]
				nameSpaceForPrefix = nameSpaceForPrefix.substring(nameSpaceForPrefix.indexOf("\"")+1);
				nameSpaceForPrefix = nameSpaceForPrefix.substring(0,nameSpaceForPrefix.indexOf("\"")).trim();
				//due to some weird behaviors of getTypeFootprint in the type class!!!!
				if (nameSpaceForPrefix.indexOf("http://www.w3.org/2001/XMLSchema") >= 0){
					nameSpaceForPrefix="xs";
				}
				CCA = Type.getTypeFootprint(new QName(nameSpaceForPrefix,firsType.getLocalPart()),dict);
				for (int i = 1; i < memberTypes.size(); i++){
					QName nextType = new QName((String)memberTypes.get(i));
					nameSpaceForPrefix = this.wsdlSchema.substring(this.wsdlSchema.indexOf("xmlns:"+nextType.getNamespacePrefix()));
					//checking for single quote as well [in addition to double quote ]
					int tempIndex = nameSpaceForPrefix.indexOf("\"");
					if (tempIndex < nameSpaceForPrefix.indexOf("\'")){
						tempIndex = nameSpaceForPrefix.indexOf("\'");
					}
					nameSpaceForPrefix = nameSpaceForPrefix.substring(tempIndex+1);
					tempIndex = nameSpaceForPrefix.indexOf("\"");
					if (tempIndex < nameSpaceForPrefix.indexOf("\'")){
						tempIndex = nameSpaceForPrefix.indexOf("\'");
					}
					nameSpaceForPrefix = nameSpaceForPrefix.substring(0,tempIndex).trim();
					if (nameSpaceForPrefix.indexOf("http://www.w3.org/2001/XMLSchema") >= 0 ){
						nameSpaceForPrefix="xs";
					}
					int nextTypeFootPrint = Type.getTypeFootprint(new QName(nameSpaceForPrefix,nextType.getLocalPart()),dict);
					CCA = CCAFinder(CCA,nextTypeFootPrint,dict);
				}
			} else {
				//TODO: handling union components with nested type declarations [without memberTypes]
				//currently: nothing! just returning the initial value which is anySimpleType
			}
			// applying the changes to the original WSDL
			QName CCAQName = Type.getTypeQName(CCA, dict);
			if (CCAQName.getNamespacePrefix().equals("xs")){
				CCAQName = new QName(schemaPrefix,CCAQName.getLocalPart());
			}
			
			if (isAnonymous){
				int indexOfLastClosingTag = beforeUnionType.lastIndexOf(">");
				beforeUnionType =beforeUnionType.substring(0,indexOfLastClosingTag) + " type=\""+CCAQName.toString()+ "\"/>";
				afterUnionType = afterUnionType.substring(afterUnionType.indexOf("</element")+9);
				afterUnionType = afterUnionType.substring(afterUnionType.indexOf(">")+1);
			} else{
				String unionTypeName =  unionTypDecExcerpt.substring(unionTypDecExcerpt.indexOf("name")+4,unionTypDecExcerpt.indexOf(">"));
				unionTypeName = unionTypeName.substring(unionTypeName.indexOf("\"")+1);
				unionTypeName = unionTypeName.substring(0,unionTypeName.indexOf("\"")).trim();
			
				
				int tempIndex = -1;
				while ( (tempIndex = beforeUnionType.indexOf(schemaTargetNSPrefix+":"+unionTypeName)) != -1){
					beforeUnionType = beforeUnionType.substring(0,tempIndex)+CCAQName.toString()+beforeUnionType.substring(tempIndex+11);
				}
				tempIndex = -1;
				while ( (tempIndex = afterUnionType.indexOf(schemaTargetNSPrefix+":"+unionTypeName)) != -1){
					afterUnionType = afterUnionType.substring(0,tempIndex)+CCAQName.toString()+afterUnionType.substring(tempIndex+11);
				}
			}
			this.wsdlSchema = beforeUnionType + afterUnionType;
		}
		
		
	}
	
	private int CCAFinder(int type1, int type2, TypeDictionary dict){
		int resType = Type.ANY_SIMPLE_TYPE;
		if (Type.isTypeOrSubTypeOf(type1, type2, dict)){
			resType = type2;
		} else if (Type.isTypeOrSubTypeOf(type2, type1, dict)){
			resType = type1;
		} else if (Type.isTypeOrSubTypeOf(type1, Type.getEventTypeSubstituted(type2,dict), dict)){
			resType = Type.getEventTypeSubstituted(type2,dict);
		} else if (Type.isTypeOrSubTypeOf(type2, Type.getEventTypeSubstituted(type1,dict), dict)){
			resType = Type.getEventTypeSubstituted(type1,dict); 
		} else if (Type.isNumericPrimitiveType(Type.getEventTypeSubstituted(type1,dict)) && Type.isNumericPrimitiveType(Type.getEventTypeSubstituted(type2,dict))){
			resType = Type.getNumericalOpResultType(Type.getEventTypeSubstituted(type1,dict), Type.getEventTypeSubstituted(type2,dict));
		}
		//TODO: checking for other possibilities of type promotion [i.e. string & integer -> string ??]
		return resType;
	}
	
	/**
	 * Starts the generating
	 * 
	 * @param runtime
	 *            Runtime where the WebService functions are added.
	 * @param namespace
	 *            namespace of the imported service (might be null)
	 * @throws MXQueryException
	 */
	public void run(Context runtime, Namespace namespace, String serviceName, String endpointName, QueryLocation loc, CompilerOptions co)
			throws MXQueryException {
		try {
			InputStream is = this.getClass().getResourceAsStream(
					"wsdlParser.xq");
			StringBuffer str = new StringBuffer();
			try {
				String thisLine;
				LineReader br = new LineReader(new InputStreamReader(is));
				while ((thisLine = br.readLine()) != null) {

					str.append(thisLine + "\n");
				}
				is.close();
			} catch (IOException e) {
				throw new MXQueryException(ErrorCodes.A0004_EC_WS_IS_ERROR_MSG, e,"I/O Error when handling a WSDL file", loc);
			}
			this.wsdlParserCode = str.toString();
			this.parseWSDL(this.wsdl,  runtime,namespace, serviceName, endpointName,loc,co);
		} catch (IOException e) {
			throw new MXQueryException(ErrorCodes.A0003_EC_WSDL_IS_ERROR_MSG, e,
					"Not possible to download or parse a WSDL definition!", loc);
		}
	}
}

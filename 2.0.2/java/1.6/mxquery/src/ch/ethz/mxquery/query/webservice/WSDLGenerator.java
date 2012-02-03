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

import java.util.Enumeration;
import java.util.Hashtable;
import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.contextConfig.XQStaticContext;
import ch.ethz.mxquery.datamodel.Namespace;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.functions.Function;
import ch.ethz.mxquery.functions.FunctionSignature;

/**
 * Generates a WSDL (String) for a Hashtable of Functions
 * 
 * @author David Graf
 * 
 */
public class WSDLGenerator {
	private String location;
	private String serviceName = "MXQueryWebService";
	private String endpointName = "MXQueryEndpoint";
	private StringBuffer types;
	private int numberOfNewTypes;
	private Vector importedSchemaPrefixes = new Vector();
	private Vector importedSchemaNSs = new Vector();
	private XQStaticContext currentContext;
	private StringBuffer messages;
	private StringBuffer portOperations;
	private StringBuffer bindings;
	private StringBuffer wsdl;

	/**
	 * Constructor.
	 * 
	 * @param location
	 *            URLString with the address of the provided webservice.
	 */
	public WSDLGenerator(String location) {
		this.location = location;
	}
	
	public WSDLGenerator(String location, String serviceName, String endpointName) {
		this.location = location;
		if (serviceName != null){	
			this.serviceName = serviceName;
		}
		if (endpointName != null){
			this.endpointName = endpointName;
		}
	}
	
//	private void generateTypes(){
//		//TODO: inserting the query's imported schemas
//		
//	}
	private StringBuffer mapIndSymbols(TypeInfo typeInfo) throws MXQueryException{
		int occurID = typeInfo.getOccurID(); 
		int typeFootprint = typeInfo.getType();
		StringBuffer typesWithIndSymbol = new StringBuffer();
		String paramType = Type.getTypeQName(typeFootprint, Context.getDictionary()).toString();
		//TODO: using the more precise mappings...
		if (paramType.indexOf("node") !=-1 || paramType.indexOf("item")!=-1){
			paramType = "xs:anyType";
		}
//		typesWithIndSymbol.append("<xs:element name=\"newElement").append(this.numberOfNewTypes).append("\"> <xs:complexType><xs:sequence ");
		if (Type.isAtomicType(typeFootprint, Context.getDictionary())){
			typesWithIndSymbol.append("<xs:simpleType name=\"newType").append(this.numberOfNewTypes).append("\">");
			typesWithIndSymbol.append("<xs:list itemType=\"").append(paramType).append("\" />");
			typesWithIndSymbol.append("</xs:simpleType>");
		}else{
			typesWithIndSymbol.append("<xs:complexType name=\"newType").append(this.numberOfNewTypes).append("\"><xs:sequence ");
			switch (occurID){
			case Type.OCCURRENCE_IND_ZERO_OR_MORE: 
				typesWithIndSymbol.append(" minOccurs=\"0\" maxOccurs=\"unbounded\" >");
				break;
			case Type.OCCURRENCE_IND_ONE_OR_MORE: 
				typesWithIndSymbol.append(" minOccurs=\"1\" maxOccurs=\"unbounded\" >");
				break;
			case Type.OCCURRENCE_IND_ZERO_OR_ONE: 
				typesWithIndSymbol.append(" minOccurs=\"0\" maxOccurs=\"1\" >");
				break;
			}
			if (paramType.indexOf("element") == -1 ){
				//generated complexType will use both of name and type attributes of the inner element
				typesWithIndSymbol.append("<xs:element name=\"baseType\" type=\"").append(paramType).append("\" />");	
			}else{
				//generated complexType will use only the name attribute of the inner element
				String elementNameAsString =typeInfo.getName();
				if (elementNameAsString == null){// declared type is: element()
					typesWithIndSymbol.append("<xs:element name=\"baseType\" type=\"xs:anyType\" />");
				}else if (elementNameAsString.equals("*")){ // declared type is: element(*,)
					//TODO
				}else{// declared type is: element(QName,)
					QName elementNameAsQName = new QName(elementNameAsString);
					String elementNamePrefix = elementNameAsQName.getNamespacePrefix();
					Namespace elementNameNS =  this.currentContext.getNamespace(elementNamePrefix);
					if (elementNameNS!=null){
						if(!importedSchemaPrefixes.contains(elementNamePrefix)){//Make sure that this schema hasn't been added before!
							this.importedSchemaPrefixes.addElement(elementNamePrefix);
							this.importedSchemaNSs.addElement(elementNameNS);
						}
					}else {
						throw  new MXQueryException(ErrorCodes.E0081_STATIC_QUERY_UNKNOWN_NAMESPACE, "Prefix "+elementNamePrefix+" not bound", null);
					}
					typesWithIndSymbol.append("    <xs:element name=\"").append(elementNameAsString).append("\"/>\n");
				}
			}
			typesWithIndSymbol.append(" </xs:sequence></xs:complexType>");		
		}
		
	//	typesWithIndSymbol.append(" </xs:sequence></xs:complexType></xs:element>");
		return typesWithIndSymbol;
	}
	
	/**
	 * Generates the message part of the WSDL for a function.
	 * 
	 * @param externalFunction
	 * @throws MXQueryException 
	 */
	private void generateMessage(QName functionName, TypeInfo[] paramTypes, TypeInfo retType) throws MXQueryException {
		int arity = paramTypes.length;
		String name = functionName.getLocalPart();
		StringBuffer message = new StringBuffer();
//		paramTypes[0] = new TypeInfo(Type.INTEGER,Type.OCCURRENCE_IND_ZERO_OR_MORE,null,null);
		message.append("  <message name=\"").append(name).append("\">\n");
		for (int i = 0; i < arity; i++) {
			if (paramTypes[i].getOccurID()!=Type.OCCURRENCE_IND_EXACTLY_ONE && paramTypes[i].getOccurID()!=-1)
			{
				this.types.append(mapIndSymbols(paramTypes[i]));
				message.append("    <part name=\"p").append(i).append("\" type=\"tns:newType").append(this.numberOfNewTypes).append("\"/>\n");
				this.numberOfNewTypes++;
			} else{
				String paramType = Type.getTypeQName(paramTypes[i].getType(), Context.getDictionary()).toString();
				if (paramType.indexOf("element") == -1){
					//generated part will use the 'type' attribute
					if (paramType.indexOf("node")!=-1 || paramType.indexOf("item") !=-1){
						paramType = "xs:anyType";
					}
					//TODO: using the more precise mappings...
					message.append("    <part name=\"p").append(i).append("\" type=\"").append(paramType).append("\"/>\n");	
				}else{
					//generated part will use the 'element' attribute
					String elementNameAsString =paramTypes[i].getName();
					if (elementNameAsString == null){// declared type is: element() or unTyped
						message.append("    <part name=\"p").append(i).append("\" type=\"xs:anyType\"/>\n");
					}else if (elementNameAsString.equals("*")){ // declared type is: element(*,)
						QName paramTypeQName = Type.getTypeQName(paramTypes[i].getTypeAnnotation(), Context.getDictionary());
						if (paramTypeQName.getNamespacePrefix().indexOf("{") != -1 && paramTypeQName.getNamespacePrefix().indexOf("}") != -1){
							String paramTypeNS = paramTypeQName.getNamespacePrefix();
							paramTypeNS = paramTypeNS.substring(paramTypeNS.indexOf("{")+1,paramTypeNS.indexOf("}"));
							String paramTypePrefix = currentContext.getPrefix(paramTypeNS); 
							paramType = paramTypePrefix + ":"+paramTypeQName.getLocalPart();
							if(!importedSchemaPrefixes.contains(paramTypePrefix)){//Make sure that this schema hasn't been added before!
								this.importedSchemaPrefixes.addElement(paramTypePrefix);
								this.importedSchemaNSs.addElement(paramTypeNS);
							}
						}else{
							paramType =paramTypeQName.toString();
						}
						message.append("    <part name=\"p").append(i).append("\" type=\"").append(paramType).append("\"/>\n");
					}else{// declared type is: element(QName,)
						QName elementNameAsQName = new QName(elementNameAsString);
						String elementNamePrefix = elementNameAsQName.getNamespacePrefix();
						Namespace elementNameNS =  this.currentContext.getNamespace(elementNamePrefix);
						if (elementNameNS!=null){
							if(!importedSchemaPrefixes.contains(elementNamePrefix)){//Make sure that this schema hasn't been added before!
								this.importedSchemaPrefixes.addElement(elementNamePrefix);
								this.importedSchemaNSs.addElement(elementNameNS);
							}
						}else {
							throw  new MXQueryException(ErrorCodes.E0081_STATIC_QUERY_UNKNOWN_NAMESPACE, "Prefix "+elementNamePrefix+" not bound", null);
						}
						message.append("    <part name=\"p").append(i).append("\" element=\"").append(elementNameAsString).append("\"/>\n");
					}
				}
			}
		}
		message.append("  </message>\n");
		
		message.append("  <message name=\"").append(name).append(
				"Response\">\n");
		if (retType.getOccurID()!=Type.OCCURRENCE_IND_EXACTLY_ONE && retType.getOccurID()!=-1)
		{
			//TODO: do the schema import for these types as well
			this.types.append(mapIndSymbols(retType));
			message.append("    <part name=\"return").append("\" type=\"tns:newType").append(this.numberOfNewTypes).append("\"/>\n");
			this.numberOfNewTypes++;
		} else{
			String returnType = Type.getTypeQName(retType.getType(), Context.getDictionary()).toString();
			if (returnType.indexOf("element") == -1){
					//generated part will use the 'type' attribute
					if (returnType.indexOf("item") !=-1 || returnType.indexOf("node") != -1){
						returnType = "xs:anyType";
						//TODO: using the more precise mappings...
					}
					message.append("    <part name=\"return\" type=\"").append(returnType).append("\"/>\n");				
			}else {
					//generated part will use the 'element' attribute
					String elementNameAsString =retType.getName();
					if (elementNameAsString == null){// declared type is: element() or unTyped
						message.append("    <part name=\"return\" type=\"xs:anyType\"/>\n");
					}else if (elementNameAsString.equals("*")){ // declared type is: element(*,)
						QName returnTypeQName = Type.getTypeQName(retType.getTypeAnnotation(), Context.getDictionary());
						if (returnTypeQName.getNamespacePrefix().indexOf("{") != -1 && returnTypeQName.getNamespacePrefix().indexOf("{") != -1){
							String returnTypeNS = returnTypeQName.getNamespacePrefix();
							returnTypeNS = returnTypeNS.substring(returnTypeNS.indexOf("{")+1,returnTypeNS.indexOf("}"));
							String returnTypePrefix = currentContext.getPrefix(returnTypeNS); 
							returnType = returnTypePrefix + ":"+returnTypeQName.getLocalPart();
							if(!importedSchemaPrefixes.contains(returnTypePrefix)){//Make sure that this schema hasn't been added before!
								this.importedSchemaPrefixes.addElement(returnTypePrefix);
								this.importedSchemaNSs.addElement(returnTypeNS);
							}
						}else{
							returnType =returnTypeQName.toString();
						}
						message.append("    <part name=\"return\" type=\"").append(returnType).append("\"/>\n");
					}else{// declared type is: element(QName,)
						QName elementNameAsQName = new QName(elementNameAsString);
						String elementNamePrefix = elementNameAsQName.getNamespacePrefix();
						Namespace elementNameNS =  this.currentContext.getNamespace(elementNamePrefix);
						if (elementNameNS!=null){
							if(!importedSchemaPrefixes.contains(elementNamePrefix)){//Make sure that this schema hasn't been added before!
								this.importedSchemaPrefixes.addElement(elementNamePrefix);
								this.importedSchemaNSs.addElement(elementNameNS);
							}
						}else {
							throw  new MXQueryException(ErrorCodes.E0081_STATIC_QUERY_UNKNOWN_NAMESPACE, "Prefix "+elementNamePrefix+" not bound", null);
						}
						message.append("    <part name=\"return\" element=\"").append(elementNameAsString).append("\"/>\n");
					}
			}
		}
		message.append("  </message>\n");

		this.messages.append(message);
	}

	/**
	 * Generates the port part of the WSDL for a function.
	 * 
	 * @param externalFunction
	 */
	private void generatePort(QName functionName, int counter) {
		String name = functionName.getLocalPart();
		StringBuffer port = new StringBuffer();
		port.append("    <operation name=\"").append(name).append("\">\n");
		port.append("      <input name=\"input"+counter+"\" message=\"tns:").append(name)
				.append("\"/>\n");
		port.append("      <output name=\"output"+counter+"\" message=\"tns:").append(name).append(
				"Response\"/>\n");
		port.append("    </operation>\n");

		this.portOperations.append(port);
	}

	private void generateBinding(QName functionName, int counter) {
		String name = functionName.getLocalPart();
		this.bindings.append("    <operation name=\"").append(name).append(
				"\">\n");
		this.bindings.append("      <soap:operation soapAction=\"\"/>\n");
		this.bindings.append("      <input name=\"input").append(counter).append(
				"\">\n");
		this.bindings
				.append("        <soap:body use=\"literal\" namespace=\"\"/>\n");
		this.bindings.append("      </input>\n");
		this.bindings.append("      <output name=\"output").append(counter).append(
				"\">\n");
		this.bindings
				.append("        <soap:body use=\"literal\" namespace=\"\"/>\n");
		this.bindings.append("      </output>\n");
		this.bindings.append("    </operation>\n");
	}

	/**
	 * Generates the WSLD for the passed (external) functions.
	 * 
	 * @param externalFunctions
	 * @param namespace
	 *            Only function that are in this namespace are exported!
	 * @return the generated WSDL as string
	 * @throws MXQueryException
	 */
	public String generate(Hashtable externalFunctions, String namespace, Context context)
			throws MXQueryException {
		this.numberOfNewTypes = 0;
		this.types = new StringBuffer();
		this.messages = new StringBuffer();
		this.portOperations = new StringBuffer();
		this.bindings = new StringBuffer();
		this.wsdl = new StringBuffer();
		this.currentContext = context;

		Enumeration funcSigs = externalFunctions.keys();

		int fcount = 1;
		this.types.append("<types>");
		this.types.append("<xs:schema  targetNamespace=\""+namespace+"\">");
		while (funcSigs.hasMoreElements()) {
			FunctionSignature lightFS = (FunctionSignature) funcSigs.nextElement();
			FunctionSignature fs = ((Function)externalFunctions.get(lightFS)).getFunctionSignature();
			TypeInfo retType = ((Function) externalFunctions.get(lightFS)).getFunctionImplementation(context).getStaticType();
			String fsNs = context.getNamespace(fs.getName().getNamespacePrefix()).getURI();
			if (!namespace.equals(fsNs)) {
				continue;
			}
			this.generateMessage(fs.getName(), fs.getParameterTypes(), retType);
			this.generatePort(fs.getName(),fcount);
			this.generateBinding(fs.getName(),fcount);
			fcount++;
		}
		this.types.append("</xs:schema>");
		this.types.append("</types>");

		this.wsdl.append("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
		this.wsdl.append("<definitions name=\"MXQueryWebService\" " +
				" targetNamespace=\""+namespace+"\" " +
				" xmlns:wsdl=\"http://schemas.xmlsoap.org/wsdl/\" " +
				" xmlns:xs=\"http://www.w3.org/2001/XMLSchema\" " +
				" xmlns:tns=\""+namespace+"\" " +
				" xmlns:soap=\"http://schemas.xmlsoap.org/wsdl/soap/\" " +
				" xmlns=\"http://schemas.xmlsoap.org/wsdl/\" ");

		for (int i = 0; i < importedSchemaNSs.size(); i ++){
			this.wsdl.append(" xmlns:"+importedSchemaPrefixes.elementAt(i)+"=\""+importedSchemaNSs.elementAt(i)+"\" ");
		}
		this.wsdl.append(">");
		
		//Check whether we need to add the types information or not
		for (int i = 0; i < importedSchemaNSs.size(); i ++){
			String tempTypes = this.types.toString();
			tempTypes = tempTypes.substring(tempTypes.indexOf("<types>")+7);
			int schemaEndTagIndex = tempTypes.indexOf(">");
			tempTypes = tempTypes.substring(0,schemaEndTagIndex+1)+"<xs:import namespace=\""+importedSchemaNSs.elementAt(i)+"\" schemaLocation=\""+currentContext.getLocationOfSchema(importedSchemaNSs.elementAt(i).toString())+"\"/>"+tempTypes.substring(schemaEndTagIndex+1);
			schemaEndTagIndex = tempTypes.indexOf(">");
			tempTypes = tempTypes.substring(0, schemaEndTagIndex)+" xmlns:"+importedSchemaPrefixes.elementAt(i)+"=\""+importedSchemaNSs.elementAt(i)+"\""+tempTypes.substring(schemaEndTagIndex);
			this.types = new StringBuffer();
			this.types.append("<types>").append(tempTypes);
		}
		if (this.types.length() != namespace.length()+58){
			this.wsdl.append(this.types);
		}
		this.wsdl.append(this.messages);

		this.wsdl.append("  <portType name=\"port\">\n");
		this.wsdl.append(this.portOperations);
		this.wsdl.append("  </portType>\n");

		this.wsdl.append("  <binding name=\"binding\" type=\"tns:port\">\n");
		this.wsdl
				.append("    <soap:binding style=\"rpc\" transport=\"http://schemas.xmlsoap.org/soap/http\"/>\n");
		this.wsdl.append(this.bindings);
		this.wsdl.append("  </binding>\n");

		this.wsdl.append("  <service name=\"").append(this.serviceName).append("\">\n");
		this.wsdl.append("    <port name=\"").append(this.endpointName).append("\" binding=\"tns:binding\">\n");
		this.wsdl.append("      <soap:address location=\"").append(
				this.location).append("\"/>\n");
		this.wsdl.append("    </port>\n");
		this.wsdl.append("  </service>\n");

		this.wsdl.append("</definitions>");

		return this.wsdl.toString();
	}
}

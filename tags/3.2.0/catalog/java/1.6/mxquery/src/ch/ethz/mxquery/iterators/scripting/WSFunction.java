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

package ch.ethz.mxquery.iterators.scripting;

import java.io.IOException;
import java.util.Enumeration;
import java.util.Hashtable;
import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeDictionary;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.iterators.ChildIterator;
import ch.ethz.mxquery.iterators.DescendantOrSelfIterator;
import ch.ethz.mxquery.iterators.NodeIterator;
import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.Iterator;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.model.ws.SoapInvoker;
import ch.ethz.mxquery.util.KXmlSerializer;
import ch.ethz.mxquery.util.StringReader;
import ch.ethz.mxquery.xdmio.XDMInputFactory;
import ch.ethz.mxquery.xdmio.XDMSerializer;
import ch.ethz.mxquery.xdmio.XDMSerializerSettings;
import ch.ethz.mxquery.xdmio.XMLSource;

/**
 * A function that is invokes a Web Service
 * 
 * @author David Graf
 * 
 */
public class WSFunction extends CurrentBasedIterator {
	private QName name;
	private String style;
	private String soapAction;
	private String location;
	private String namespace;
	private Hashtable declaredNSs;
	private String encoding;
	private String[] paramNames;
	private String[] paramTypes;
	private String returnName;
	private String returnType;

	public WSFunction(QName name, String style,String soapAction, String namespace, Hashtable declaredNSs,
			String encoding, String location, String[] paramNames,
			String[] paramTypes, String returnName, String returnType) {
		exprCategory = XDMIterator.EXPR_CATEGORY_SIMPLE; // to be discussed
		this.name = name;
		this.style = style;
		this.soapAction = soapAction;
		this.namespace = namespace;
		this.declaredNSs = declaredNSs;
		this.encoding = encoding;
		this.location = location;
		this.paramNames = paramNames;
		this.paramTypes = paramTypes;
		this.returnName = returnName;
		this.returnType = returnType;
	}

	private String generateBody(String[] params) {
		StringBuffer body = new StringBuffer();
		String elementName= null;
		boolean isDocumentStyle = this.style.equals("document");
		boolean nsSet = false;
		if (this.namespace != null && !this.namespace.equals("")) {
			nsSet = true;
		}

		if (!isDocumentStyle){//in RPC style, name of the function is included in the soap request
			if (nsSet) {
				elementName = this.name.getLocalPart();
				body.append("<tns:").append(elementName).append(" xmlns:tns=\"")
						.append(this.namespace).append("\"");			
			} else {
				elementName = this.name.getLocalPart();
				body.append("<").append(elementName);			
			}
			if (this.encoding != null && !this.encoding.equals("")) {
				body.append(" SOAP-ENV:encodingStyle=\"").append(this.encoding)
						.append("\"");
			}
			body.append(">");
		}
		for (int i = 0; i < params.length; i++) {
			if (this.paramNames[i] != null) {
				boolean isElementPart = this.paramTypes[i].equals("noType");
				if (isElementPart){
					body.append(params[i]);
				}else{
					body.append("<").append(this.paramNames[i]);
					if (isDocumentStyle){
						body.append(" xmlns=\"").append(this.namespace).append("\"");
					}
					if (this.paramTypes[i] != null) {
						body.append(" xsi:type=\"").append(this.paramTypes[i]).append(
								"\"");
					}
					body.append(">");
					body.append(params[i]);
					body.append("</").append(this.paramNames[i]).append(">");
				}
			}
		}
		if (!isDocumentStyle){//end element for function name in RPC style
			body.append("</tns:").append(elementName).append(">");
		}
		
		return body.toString();
	}

	public Token next() throws MXQueryException {
		if (called == 0) {
			init();
			called++;
		}
		return current.next();
	}
	
	private void init() throws MXQueryException {
		//adding all declared namespaces in the WSDL file
		Enumeration keys = this.declaredNSs.keys();
		while (keys.hasMoreElements()){
			String prefix = (String)keys.nextElement();
			String uri = (String)this.declaredNSs.get(prefix);
			context.addNamespace(prefix,uri);
		}
		TypeDictionary dict = Context.getDictionary();
		String[] strParams;
		if (subIters == null) {
			strParams = new String[0];
		} else {
			strParams = new String[subIters.length];
		}
		XDMSerializerSettings ser = new XDMSerializerSettings();
		ser.setOmitXMLDeclaration(true);
		for (int i = 0; i < strParams.length; i++) {
			
			XDMSerializer ip = new XDMSerializer(ser);
			if (this.paramTypes[i] != null && !this.paramTypes[i].equals("") && !this.paramTypes[i].equals("noType")){
				QName tempQName = new QName(this.paramTypes[i]);
				QName toCheckQName = new QName((String)this.declaredNSs.get(tempQName.getNamespacePrefix()),tempQName.getLocalPart());
				if (!toCheckQName.getNamespacePrefix().equals("http://www.w3.org/2001/XMLSchema") && !Type.isSubTypeOf(Type.getTypeFootprint(toCheckQName,dict),Type.ANY_SIMPLE_TYPE,dict)){
					//doing the mapping element(*,ComplexType) -> type = ComplexType
					subIters[i] = new NodeIterator(context, subIters[i],loc);
				}
			}
			strParams[i] = ip.eventsToXML(subIters[i]);
		}
		String soapBody = this.generateBody(strParams);
		String soapEnv = createSoapEnv(soapBody);

		SoapInvoker si = new SoapInvoker(this.location,null, this.soapAction, soapEnv);
		try {
			String soapResult = si.query(loc, true);
			
			
			// an artificial sample for testing the multi-part output message web services			
//			this.resultElement = "multiple-result";
//			soapResult ="<ns1:doSpellingSuggestionResponse xmlns:ns1=\"urn:GoogleSearch\" SOAP-ENV:encodingStyle=\"http://schemas.xmlsoap.org/soap/encoding/\" xmlns:SOAP-ENV=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:xsi=\"http://www.w3.org/1999/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/1999/XMLSchema\">"+
//							"<return xsi:type=\"xsd:string\">relation</return>"+
//							"<return1 xsi:type=\"xsd:string\">ship</return1>"+
//						"</ns1:doSpellingSuggestionResponse>";

			//surrounding the result with the 'multiple-result' element
			if (this.returnName.equals("multiple-result"))
			{
				int startTagIndex = soapResult.indexOf('>');
				//int endTagIndex = soapResult.lastIndexOf("</");
				//lastIndexOf is not supported in CLDC
				String tempString = soapResult;
				int endTagIndex = tempString.indexOf("</");
				tempString = tempString.substring(endTagIndex+1);
				while (tempString.indexOf("</") != -1){
					endTagIndex = endTagIndex+tempString.indexOf("</")+1;
					tempString = tempString.substring(tempString.indexOf("</")+1);
				}
				soapResult = soapResult.substring(0, startTagIndex+1)+"<multiple-result><multiple-result>"+
							soapResult.substring(startTagIndex+1,endTagIndex )+"</multiple-result></multiple-result>"+
							soapResult.substring(endTagIndex);
			}
		
			//child elements need to be unqualified! an example follows...
//			soapResult ="<ns1:addResponse soapenv:encodingStyle=\"http://schemas.xmlsoap.org/soap/encoding/\" xmlns:ns1=\"http://localhost:8080/axis/First.jws\" xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\">"+
//			"<ns1:getTermReturn xsi:type=\"xsd:int\">20</ns1:getTermReturn>"+
//			"</ns1:addResponse>";
			
			//soapResult = soapResult.trim();
			String prefix = null;
			//see if the root element of soapResult has a prefix. The case to check: when only its child element has the prefix ....
			if (soapResult.indexOf(":") < soapResult.indexOf(">")){
				prefix = soapResult.substring(soapResult.indexOf("<")+1, soapResult.indexOf(":"));
			}
			if (prefix != null){
				String startTag = soapResult.substring(0,soapResult.indexOf(">")+1);
				//CDLD doesn't support the lastIndexOf...
				//String endTag = soapResult.substring(soapResult.lastIndexOf("</"),soapResult.length());
				int endOfTagName = soapResult.indexOf('>');
				if (soapResult.indexOf(' ') < endOfTagName){
					endOfTagName = soapResult.indexOf(' ') ;
				}
				String startTagName = soapResult.substring(soapResult.indexOf('<')+1, endOfTagName);
				String endTag = soapResult.substring(soapResult.indexOf("</"+startTagName+">"),soapResult.length());
				String childElement = soapResult.substring(soapResult.indexOf(">")+1, soapResult.indexOf("</"+startTagName+">"));

				//CLDC doesn't support replaceAll...
				//childElement = childElement.replaceAll(prefix+":","");
				String prefixAndCollon = prefix +":";
				while (childElement.indexOf(prefixAndCollon)!= -1)
				{
					childElement = childElement.substring(0,childElement.indexOf(prefixAndCollon))+ childElement.substring(childElement.indexOf(prefixAndCollon)+prefix.length()+1);
				}
				soapResult = startTag+childElement+endTag;
			}
			XMLSource xmlIt = XDMInputFactory.createXMLInput(context, new StringReader(soapResult), false, context.getInputValidationMode(), loc);
			xmlIt.setURI(location);

			if (this.returnType == null || this.returnType.equals("")){//the return 'part' is an element
				TypeInfo stepInfo = new TypeInfo(Type.START_TAG,Type.OCCURRENCE_IND_EXACTLY_ONE,this.returnName,null);
				current = new DescendantOrSelfIterator(context, stepInfo, DescendantOrSelfIterator.DESC_AXIS_SLASHSLASH, new XDMIterator[] {xmlIt},loc);
//				current = new NodeIterator(context, xmlIt,loc);
			}else if (this.returnType.equals("multiple-result")){//the return message has more than one 'part'
			//TODO: possible merging?	
				current = new NodeIterator(context, xmlIt,loc);
			}else {//the 'part' in the  return message is of a type
				QName tempQName = new QName(this.returnType);
				QName toCheckQName = new QName((String)this.declaredNSs.get(tempQName.getNamespacePrefix()),tempQName.getLocalPart());
				if ( !toCheckQName.getNamespacePrefix().equals("http://www.w3.org/2001/XMLSchema")&& !Type.isSubTypeOf(Type.getTypeFootprint(toCheckQName, dict), Type.ANY_SIMPLE_TYPE, dict)){
					//doing the mapping: type = ComplexTypeelement -> (*,ComplexType) 
					current = new NodeIterator(context, new ChildIterator(context, "*", new XDMIterator[] {xmlIt },loc),loc);
				} else{// type of the parameter is simple
						current = new NodeIterator(context, new ChildIterator(context, this.returnName,
								new XDMIterator[] {new ChildIterator(context, "*",new XDMIterator[] {xmlIt },loc)},loc),loc);
				}
			}
			
			
//			if (this.returnName == null || this.returnName.equals("")) { //The 'param' was an element
//				current = new NodeIterator(context, xmlIt,loc);
//			} else {//The 'param' was of a type
//				if (this.returnType.indexOf(this.tnsPrefix+":") != -1 && !Type.isSubTypeOf(Type.getTypeFootprint(new QName(this.namespace,this.returnType.substring(this.returnType.indexOf(":")+1)), dict), Type.ANY_SIMPLE_TYPE, dict)){
//					//TODO: check all possible prefixes [need to be available as a hash table]
//					//doing the mapping: type = ComplexTypeelement -> (*,ComplexType) 
//					current = new NodeIterator(context, new ChildIterator(context, "*", new Iterator[] {xmlIt },loc),loc);
//				} else{// type of the parameter is simple
//						current = new NodeIterator(context, new ChildIterator(context, this.returnName,
//								new Iterator[] {new ChildIterator(context, "*",new Iterator[] {xmlIt },loc)},loc),loc);
//				}
//			}
		} catch (IOException e) {
			throw new MXQueryException(ErrorCodes.A0004_EC_WS_IS_ERROR_MSG, e, "Error during Web Service Invocation!", loc);
		}
	}
	
	private String createSoapEnv(String soapBody){
		StringBuffer msg = new StringBuffer();
		msg.append("<SOAP-ENV:Envelope").append(" ");
		msg.append(
				"xmlns:SOAP-ENV=\"http://schemas.xmlsoap.org/soap/envelope/\"")
				.append(" ");
		msg.append("xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"")
				.append(" ");
		msg.append("xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\">").append(
				"\n");
		msg.append("<SOAP-ENV:Body>").append("\n");
		msg.append(soapBody).append("\n");
		msg.append("</SOAP-ENV:Body>").append("\n");
		msg.append("</SOAP-ENV:Envelope>").append("\n");
		return msg.toString();		
	}

	public QName getQName() {
		if (name.getNamespacePrefix() == null
				|| name.getNamespacePrefix().equals("")) {
			return new QName("fn", name.getLocalPart());
		} else {
			return this.name;
		}
	}

	public int getArity() {
		return this.paramNames.length;
	}
		
	public KXmlSerializer traverseIteratorTree(KXmlSerializer serializer) throws Exception {
		serializer.startTag(null, "WSFunction");
		serializer.attribute(null, "name", this.name.toString());
		for (int i = 0; i < this.paramNames.length; i++) {
			String paramName = (this.paramNames[i] != null ? this.paramNames[i] : "");
			serializer.attribute(null, "param" + i, paramName);
		}
		serializer.endTag(null, "WSFunction");
		return serializer;
	}
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new WSFunction(name.copy(), style, soapAction, namespace, declaredNSs, encoding, location, paramNames, Iterator.copyStrings(paramTypes), returnName, returnType);
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;
	}
}

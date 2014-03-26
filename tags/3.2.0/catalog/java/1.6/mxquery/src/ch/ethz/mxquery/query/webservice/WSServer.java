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

import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.io.PrintStream;
import java.io.UnsupportedEncodingException;
import java.util.Enumeration;
import java.util.Vector;

import org.kxml2.io.KXmlParser;
import org.xmlpull.v1.XmlPullParserException;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.Namespace;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.functions.Function;
import ch.ethz.mxquery.functions.FunctionSignature;
import ch.ethz.mxquery.functions.fn.DataValuesIterator;
import ch.ethz.mxquery.iterators.ChildIterator;
import ch.ethz.mxquery.iterators.NodeIterator;
import ch.ethz.mxquery.iterators.SequenceIterator;
import ch.ethz.mxquery.iterators.TokenIterator;
//import ch.ethz.mxquery.iterators.UserdefFuncCall;
//import ch.ethz.mxquery.iterators.scripting.WSFunction;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.util.StringReader;
import ch.ethz.mxquery.xdmio.XDMInputFactory;
import ch.ethz.mxquery.xdmio.XDMSerializer;

/**
 * Very Simple WebService Client.
 * 
 * @author David Graf
 * 
 */
public class WSServer extends Thread {

	private Context context;

	private boolean isFault;
	
	private String wsdl;

	private Namespace namespace;

	public WSServer(String serverURL, String serviceName, String endpointName, Namespace namespace, Context context)	throws MXQueryException {
		this.context = context;
		this.context.addNamespace("tn", "http://www.mxquery.org/soap/targetNamespace");
		this.namespace = namespace;
		WSDLGenerator wsdlGenerator = new WSDLGenerator(serverURL, serviceName, endpointName);
		this.wsdl = wsdlGenerator.generate(context.getWSFunctions(namespace.getURI()),
				namespace.getURI(), this.context);
	}
	
	private WSServer(Context ctx, Namespace nmsp, String wsdl) {
		this.context = ctx;
		this.namespace = nmsp;
		this.wsdl = wsdl;
	}
	
	
	public String getWSDL(){
		return this.wsdl;
	}


	private String generateFunctionInformation(String query, Vector params)
			throws MXQueryException {
		String functionName = "";
		try {
			KXmlParser p = new KXmlParser();
			p.setInput(new StringReader(query));
			int type;
			while ((type = p.next()) != KXmlParser.END_DOCUMENT) {
				if (type == KXmlParser.START_TAG) {
					if (p.getDepth() == 1) {
						functionName = p.getName();
					} else if (p.getDepth() == 2) {
						params.addElement(new QName(p.getName()));
					}
				}
			}
		} catch (XmlPullParserException e) {
			throw new MXQueryException(ErrorCodes.A0013_Invalid_SOAP_request, e,"Error while parsing the the SOAP request", null);
		} catch (IOException e) {
			throw new MXQueryException(ErrorCodes.A0004_EC_WS_IS_ERROR_MSG, e,"I/O Error when handling a WS request", null);
		}
		return deletePrefix(functionName);
	}

	public static String deletePrefix(String name) {
		int index = name.indexOf(":");
		if (index > 0) {
			name = name.substring(index + 1);
		}
		return name;
	}

	
	private XDMIterator[] generateParams(FunctionSignature fs, Vector params, String query)
			throws MXQueryException {
		XDMIterator[] iter = new XDMIterator[params.size()];
		Enumeration en = params.elements();
		int i = 0;
		while (en.hasMoreElements()) {
			QName paramName = (QName) en.nextElement();
			int oi = fs.getParameterTypes()[i].getOccurID();
			if (oi == Type.OCCURRENCE_IND_ONE_OR_MORE || oi == Type.OCCURRENCE_IND_ZERO_OR_MORE || oi == Type.OCCURRENCE_IND_ZERO_OR_ONE) {
				//Detection and construction of the sequence parameters
				iter[i]= DataValuesIterator.getDataIterator(new SequenceIterator(this.context,getSeqAsTokens(paramName, query),null),this.context);
			} else{
				iter[i] = new NodeIterator(this.context, new ChildIterator(this.context, paramName
						.getLocalPart(), new XDMIterator[] { new ChildIterator(this.context, "*", new XDMIterator[] {XDMInputFactory.createXMLInput(context,
						new StringReader(query), false,context.getInputValidationMode(),null)},null) },null),null);
			}
			iter[i].setContext(this.context, true);			
			i++;
		}
		
		return iter;
	}
	
//	private boolean isSequenceParam(QName param, String query){
//		int startIndex = query.indexOf("<"+param.toString());
//		int endIndex = query.indexOf("</"+param.toString()+">");
//		String subQuery = query.substring(startIndex,endIndex);
//		return subQuery.contains("baseType");
//	}
	
	private TokenIterator[] getSeqAsTokens(QName param, String query) throws MXQueryException {
		int startIndex = query.indexOf("<"+param.toString());
		int endIndex = query.indexOf("</"+param.toString()+">");
		String subQuery = query.substring(startIndex,endIndex);
		int seqLengh = 0;
		String[] values = new String[20];
		if (subQuery.indexOf("baseType") >= 0){ //sequence of non-atomic types
			//TODO: number is constant!
			while (subQuery.indexOf("<baseType>")!= -1){
				subQuery = subQuery.substring(subQuery.indexOf("<baseType>")+10);
				values[seqLengh] = subQuery.substring(0,subQuery.indexOf('<'));
				subQuery = subQuery.substring(subQuery.indexOf("</baseType>")+11);
				seqLengh++;
			}
		}else{//sequence of atomic types: xs:list!
			//TODO: do we need to deal with values with quotes?
			subQuery = subQuery.substring(subQuery.indexOf(">")+1).trim();
			int listDelimiterIndex = subQuery.indexOf(" ");
			while (listDelimiterIndex != -1){
				values[seqLengh] = subQuery.substring(0, listDelimiterIndex);
				subQuery = subQuery.substring(listDelimiterIndex).trim();
				seqLengh++;
				listDelimiterIndex = subQuery.indexOf(" ");
			}
			if (!subQuery.equals("")){
				values[seqLengh] = subQuery;
				seqLengh++;
			}
		}
		

		TokenIterator[] tokItArr = new TokenIterator[seqLengh];
		for (int i = 0; i < seqLengh; i++){
				//why UnTyped? -> The underneath TokenIterator doesn't work for some types [i.e. Integer]
			tokItArr[i] = new TokenIterator(this.context,values[i],Type.UNTYPED,null);
		}
		return tokItArr;
	}

	private String generateReply(XDMIterator functionImpl, XDMIterator[] params) throws MXQueryException{
		
		functionImpl.setSubIters(params);
		XDMSerializer ip = new XDMSerializer();
		
		ByteArrayOutputStream bout = new ByteArrayOutputStream(); 
		PrintStream stream;
		try {
			stream = new PrintStream(bout,false,"UTF-8");
			ip.eventsToSOAPMsg(stream, functionImpl);
			return bout.toString("UTF-8");
		} catch (UnsupportedEncodingException ue) {
			throw new MXQueryException(ErrorCodes.A0009_EC_EVALUATION_NOT_POSSIBLE,"Cannot serialize",null);
		}
	}

	private StringBuffer soapEnvelope(StringBuffer content, String functionName) {
		StringBuffer envelope = new StringBuffer();

		envelope.append("<SOAP-ENV:Envelope").append("\n");
		envelope.append(
				"xmlns:SOAP-ENV=\"http://schemas.xmlsoap.org/soap/envelope/\"")
				.append("\n");
		envelope.append(
				"xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"")
				.append("\n");
		envelope.append("xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\">")
				.append("\n");
		envelope.append("<SOAP-ENV:Body>").append("\n");
		if (!isFault){
			envelope
			.append("<tns:")
			.append(functionName)
			.append("Response")
			.append(
					" xmlns:tns=\"http://www.mxquery.org/soap/targetNamespace\"")
			.append(">\n");
			envelope.append("<return>\n");
		}
		envelope.append(content).append("\n");
		if (!isFault){
			envelope.append("</return>\n");
			envelope.append("</tns:").append(functionName).append("Response")
					.append(">\n");
		}
	
		envelope.append("</SOAP-ENV:Body>").append("\n");
		envelope.append("</SOAP-ENV:Envelope>").append("\n");
		return envelope;
	}


	public StringBuffer handleSOAP(String inputSoap){
		String query = inputSoap;
		Vector paramNames = new Vector();
		StringBuffer reply = new StringBuffer();
		String functionName = null;
		isFault = false;
		try{
			functionName = this.generateFunctionInformation(query,
					paramNames);
			
			QName qname = new QName(namespace.getURI(),namespace.getNamespacePrefix(), functionName);
			//FIXME: problem with retrieving non-parametric functions
			if (this.context.getFunction(qname,paramNames.size()) == null){
				throw new MXQueryException(ErrorCodes.E0017_STATIC_DOESNT_MATCH_FUNCTION_SIGNATURE,"The function \"" + functionName+ "\" with arity "+paramNames.size()+ " doesn't exist!",null);
			}
			Function function = this.context.getFunction(qname, paramNames.size());
			XDMIterator[] paramIters = this.generateParams(function.getFunctionSignature(), paramNames, query);
			reply.append(this.generateReply(function.getFunctionImplementation(this.context),
					paramIters));
		} catch (MXQueryException e) {
			isFault = true;
			//TODO: resolve the right faultCode, see: http://www.w3schools.com/SOAP/soap_fault.asp
			String faultCode = "Client";
			String faultString = e.getMessage();
			reply = new StringBuffer();
			reply.append("<SOAP-ENV:Fault><faultcode>SOAP-ENV:").append(faultCode).append("</faultcode><faultstring>").append(faultString).append("</faultstring></SOAP-ENV:Fault>");
			}
		StringBuffer envelope = this.soapEnvelope(reply, functionName);
		return envelope;
	}
	public WSServer copy(Context ctx) {
		WSServer ret = new WSServer(ctx,namespace,wsdl);
		return ret;
	}
}

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

import java.io.IOException;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.util.Enumeration;
import java.util.Vector;

import javax.microedition.io.Connector;
import javax.microedition.io.ServerSocketConnection;
import javax.microedition.io.SocketConnection;

import org.kxml2.io.KXmlParser;
import org.xmlpull.v1.XmlPullParserException;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.datamodel.Namespace;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.iterators.ChildIterator;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.model.ws.*;
import ch.ethz.mxquery.xdmio.XDMInputFactory;
import ch.ethz.mxquery.xdmio.XDMSerializer;
import ch.ethz.mxquery.util.StringReader;

/**
 * Very Simple WebService Client.
 * 
 * @author David Graf
 * 
 */
public class WSServer extends Thread {
	private int port;
	private Context context;
	private ServerSocketConnection server;
	private String wsdl;
	private Namespace namespace;

	public WSServer(int port, Namespace namespace, Context context)
			throws MXQueryException {
		throw new RuntimeException("Implement this - David");
//		context.getStaticContext().addNamespace(new Namespace("tn", "http://www.mxquery.org/soap/targetNamespace"));
//		this.port = port;
//		this.namespace = namespace;
//		
//		// TODO
//		String hostAddress = Utils.getCurrentIP();
//		
//		WSDLGenerator wsdlGenerator = new WSDLGenerator("http://" + hostAddress + ":" + this.port);
//		this.wsdl = wsdlGenerator.generate(context.getExternalFunctions(), namespace);
//		this.context = context;
//		try {
//			this.server = (ServerSocketConnection)Connector.open("socket://:" + this.port);
//		} catch (IOException e) {
//			throw new MXQueryException(e, 
//					"Not possible to open the port " + this.port
//					+ " for a web service.");
//		}
	}

	public void run(){
		try {
			while (true) {
				SocketConnection conn = null;
				conn = (SocketConnection)this.server.acceptAndOpen();
				this.handleRequest(conn);
				conn.close();
			}
		} catch (Exception e) {
			e.printStackTrace();
		}
	}
	
	private String generateFunctionInformation(String query, Vector params) throws MXQueryException {
		String functionName = "";
		try {
			KXmlParser p = new KXmlParser();
			p.setInput(new StringReader(query));
			int type;
			while((type = p.next()) != KXmlParser.END_DOCUMENT) {
				if (type == KXmlParser.START_TAG) {
					if (p.getDepth() == 1) {
						functionName = p.getName();
					} else if (p.getDepth() == 2) {
						params.addElement(new QName(p.getName()));
					}
				}
			}
		} catch (XmlPullParserException e) {
			throw new MXQueryException(ErrorCodes.A0004_EC_WS_IS_ERROR_MSG, e,"Parsing Error when handling a WS request", null);
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
	
	private XDMIterator[] generateParams(Vector params, String query) throws MXQueryException {
		XDMIterator[] iter = new XDMIterator[params.size()];
		Enumeration en = params.elements();
		int i = 0;
		while (en.hasMoreElements()) {
			QName paramName = (QName)en.nextElement();
			iter[i] = new ChildIterator(context, paramName.getLocalPart(), 
					new XDMIterator[]{
				XDMInputFactory.createXMLInput(context, new StringReader(query),
						false, context.getInputValidationMode(),null)},null); 
			//iter[i].setContext(this.context);
			i++;
		}
		return iter;
	}
	
	private StringBuffer generateReply(String functionName, XDMIterator[] params) throws MXQueryException {
		QName qname = new QName(namespace.getNamespacePrefix(), functionName);
		throw new RuntimeException("FIXME - David");
		//Iterator func = this.context.getFunctionGallery().getFunction(qname,
		//		params.length);
		// if (func instanceof UserFunction) {
		// ((UserFunction)func).setContext(this.context);
		// }
		//return XDMSerializer.eventsToXML(func, false);
	}
	
	private StringBuffer soapEnvelope(StringBuffer content, String functionName) {
		StringBuffer envelope = new StringBuffer();
		
		envelope.append("<SOAP-ENV:Envelope").append("\n");
		envelope.append(
				"xmlns:SOAP-ENV=\"http://schemas.xmlsoap.org/soap/envelope/\"")
				.append("\n");
		envelope.append("xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"")
				.append("\n");
		envelope.append("xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\">").append(
				"\n");
		envelope.append("<SOAP-ENV:Body>").append("\n");
		
		envelope.append("<").append(functionName).append("Response").append(">\n");
		envelope.append("<return>\n");
		envelope.append(content).append("\n");
		envelope.append("</return>\n");
		envelope.append("</").append(functionName).append("Response").append(">\n");
		
		envelope.append("</SOAP-ENV:Body>").append("\n");
		envelope.append("</SOAP-ENV:Envelope>").append("\n");
		return envelope;
	}
	
	private String generateHttpReply(StringBuffer replyContent) throws IOException {
		HttpResponse response = new HttpResponse("1.0", 200, "OK");
		response.setHttpContent(replyContent.toString());
		response.addAttribute("Content-Type", "text/xml; charset=utf-8");
		return response.getHttpMsg();
	}

	/**
	 * TODO Each Request in one thread. Maybe not possible because the query
	 * doesn't tolerate concurrent access.
	 * 
	 * @param client
	 * @throws IOException
	 */
	private void handleRequest(SocketConnection conn) throws IOException,MXQueryException {
		InputStreamReader isr = new InputStreamReader(conn.openInputStream());
		OutputStreamWriter osw = new OutputStreamWriter(conn.openOutputStream());
		
		HttpRequest httpRequest = new HttpRequest(isr);

		String retMsg;
		try {
			if (httpRequest.getPath().endsWith("wsdl") || httpRequest.getPath().endsWith("WSDL")) {
				retMsg = this.generateHttpReply(new StringBuffer(this.wsdl));
			} else {
				String query = httpRequest.getSoapBody();
				Vector paramNames = new Vector();
				String functionName = this.generateFunctionInformation(query, paramNames);
				XDMIterator[] paramIters = this.generateParams(paramNames, query);
				StringBuffer reply = this.generateReply(functionName, paramIters);
				StringBuffer envelope = this.soapEnvelope(reply, functionName);
				retMsg = this.generateHttpReply(envelope);
			}
		} catch (Exception e) {
			e.printStackTrace();
			HttpResponse errorResponse = new HttpResponse("1.0", 400, "Bad Request");
			retMsg = errorResponse.getHttpMsg();
		}
		osw.write(retMsg);
		osw.flush();
		osw.close();
		isr.close();
	}
}

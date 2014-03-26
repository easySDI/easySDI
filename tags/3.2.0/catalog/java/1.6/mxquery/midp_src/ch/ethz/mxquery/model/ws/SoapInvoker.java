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

package ch.ethz.mxquery.model.ws;

import java.io.IOException;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;

import javax.microedition.io.Connector;
import javax.microedition.io.HttpConnection;

import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.util.LineReader;

/**
 * Class to invoke a SOAP method (a webservice)
 * 
 * @author David Graf
 * 
 */
public class SoapInvoker {
	private String strUrl;
	private String soapAction;
	private String soapEnv;
	private String method;

	/**
	 * Constructor
	 * 
	 * @param strUrl
	 * @param soapAction
	 * @param soapEnv
	 */
	public SoapInvoker(String strUrl,String method, String soapAction,
			String soapEnv) {
		this.strUrl = strUrl;
		this.method = method;
		this.soapAction = soapAction;
		this.soapEnv = soapEnv;
	}

	/**
	 * Makes a Web Service Invocation with the
	 * 
	 * @return
	 */
	public String query(QueryLocation loc, boolean getOnlyTheBody) throws IOException, MXQueryException {
		String msg = this.soapEnv;

		HttpConnection conn = null;
		LineReader br = null;
		OutputStreamWriter osw = null;
		StringBuffer result = new StringBuffer();
		try {
            conn = 
                   (HttpConnection)Connector.open(this.strUrl);
            if (this.method != null && this.method.equals(" ")){//ToDo: a weird behavior, should be ""  
            	conn.setRequestMethod(this.method);
            }else{
            	conn.setRequestMethod("POST");            	
            }
            if (this.soapAction != null) {
            	conn.setRequestProperty("SOAPAction", this.soapAction);
            }
            conn.setRequestProperty("MxqLength", String.valueOf(msg.length()));
            conn.setRequestProperty("Content-Type", "text/xml; charset=utf-8");
            osw = new OutputStreamWriter(conn.openOutputStream());
			osw.write(msg);
			osw.flush();
            if (conn.getResponseCode() != HttpConnection.HTTP_OK) {
            	throw new DynamicException(ErrorCodes.A0004_EC_WS_IS_ERROR_MSG,
    					"WebService reponse is an Error Msg: "
            			+ conn.getResponseCode() + " "
						+ conn.getResponseMessage(), loc);
            }
            else {
            	  br = new LineReader(new InputStreamReader(conn.openInputStream()));
            	  String str;
            	  while ((str = br.readLine()) != null) {
            		  result.append(str).append("\n");
            	  }
            }

            

		} catch (IOException e) {
            throw e;
        } catch (MXQueryException e) {
            throw e;
        } finally {
        	try {
        		if (conn != null) {
        			conn.close();
        		}
        		if (osw != null) {
        			osw.close();
        		}
        		if (br != null) {
        			br.close();
        		}
			} catch (IOException e) {
			}
        }
        if (!getOnlyTheBody){
        	return result.toString();
        }
        return HttpMessage.getSoapBody(result.toString());
	}

	/**
	 * Creates Content of the HTTP request.
	 * 
	 * @return
	 */
	private String createMsg() {
		StringBuffer msg = new StringBuffer();
		msg.append("<SOAP-ENV:Envelope").append("\n");
		msg.append(
				"xmlns:SOAP-ENV=\"http://schemas.xmlsoap.org/soap/envelope/\"")
				.append("\n");
		msg.append("xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"")
				.append("\n");
		msg.append("xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\">").append(
				"\n");
		msg.append("<SOAP-ENV:Body>").append("\n");
		msg.append(this.soapEnv).append("\n");
		msg.append("</SOAP-ENV:Body>").append("\n");
		msg.append("</SOAP-ENV:Envelope>").append("\n");
		return msg.toString();
	}

//	/**
//	 * Handels the OutputStream, Socket, InputStream stuff
//	 * 
//	 * @param query
//	 * @return
//	 */
//	private HttpResponse soapAction(String query) throws IOException {
//		Socket connection = null;
//		OutputStreamWriter osw = null;
//		InputStreamReader isr = null;
//		
//		HttpResponse httpResponse = null;
//		
//		try {
//			connection = new Socket(this.url.getHost(), this.url.getPort());
//			osw = new OutputStreamWriter(connection.getOutputStream());
//			osw.write(query);
//			osw.flush();
//			isr = new InputStreamReader(connection.getInputStream());
//			httpResponse = new HttpResponse(isr);
//			
//		} catch (IOException e) {
//			throw e;
//		} finally {
//			if (osw != null) {
//				osw.close();
//			}
//			if (isr != null) {
//				isr.close();
//			}
//			if (connection != null) {
//				connection.close();
//			}
//		}
//		return httpResponse;
//	}
}

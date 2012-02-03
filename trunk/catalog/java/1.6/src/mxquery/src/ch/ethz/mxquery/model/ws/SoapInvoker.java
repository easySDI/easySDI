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
import java.net.HttpURLConnection;
import java.net.URL;

import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.model.FnErrorException;
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
	 * Performs  a Web Service Invocation
	 * 
	 * @return the soap body as string
	 */
	public String query(QueryLocation loc, boolean getOnlyTheBody) throws IOException, MXQueryException {
		String msg = this.soapEnv;

		StringBuffer result = performHTTPOperation(loc, msg);
        if (!getOnlyTheBody){
        	return result.toString();
        }
        String soapBody = HttpMessage.getSoapBody(result.toString());
        //Mapping the SOAP fault messages to a meaningful invocation of fn:error() in XQuery
        if (soapBody.indexOf("faultcode") >= 0){
        	String errorCode ="err:XQDY0101";
        	int faultStringStartElementIndex = soapBody.indexOf("faultstring");
        	int faultStringEndElementIndex = soapBody.substring(faultStringStartElementIndex+11).indexOf("faultstring");
        	String errorDesc = soapBody.substring(faultStringStartElementIndex+12, faultStringStartElementIndex+faultStringEndElementIndex+9);   
        	throw new FnErrorException(new QName("http://www.w3.org/2005/xqt-errors",errorCode), "soap response is a fault message: " + errorDesc, loc);
        }
        return soapBody;
	}

	private StringBuffer performHTTPOperation(QueryLocation loc, String msg)
			throws IOException, MXQueryException {
		HttpURLConnection conn = null;
		LineReader br = null;
		OutputStreamWriter osw = null;
		StringBuffer result = new StringBuffer();
		try {
			URL url = new URL(this.strUrl);
            conn = 
                   (HttpURLConnection)url.openConnection();
            if (this.method != null && this.method.equals(" ")){//ToDo: a weird behavior, should be ""  
            	conn.setRequestMethod(this.method);
            }else{
            	conn.setRequestMethod("POST");            	
            }
            
            if (this.soapAction != null) {
            	conn.addRequestProperty("SOAPAction", this.soapAction);
            }
            conn.addRequestProperty("MxqLength", String.valueOf(msg.length()));
            conn.addRequestProperty("Content-Type", "text/xml; charset=utf-8");
            conn.setUseCaches(false);
            conn.setDoOutput(true);
            osw = new OutputStreamWriter(conn.getOutputStream());
			osw.write(msg);
			osw.flush();
            if ((conn.getResponseCode() != HttpURLConnection.HTTP_OK) && (conn.getResponseCode() != HttpURLConnection.HTTP_INTERNAL_ERROR)) {
            	throw new DynamicException(ErrorCodes.A0004_EC_WS_IS_ERROR_MSG,
    					"WebService reponse is an Error Msg: "
            			+ conn.getResponseCode() + " "
						+ conn.getResponseMessage(), loc);
            }
            else {
            		if (conn.getResponseCode() == HttpURLConnection.HTTP_INTERNAL_ERROR){
            			br = new LineReader(new InputStreamReader(conn.getErrorStream()));
            		}
            		else {
            			br = new LineReader(new InputStreamReader(conn.getInputStream()));	
            		}
            	  
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
        			conn.disconnect();
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
		return result;
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

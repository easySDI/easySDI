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
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.PrintWriter;
import java.util.HashMap;
import java.util.Map;

import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import ch.ethz.mxquery.query.XQCompiler;
import ch.ethz.mxquery.query.PreparedStatement;
import ch.ethz.mxquery.query.impl.CompilerImpl;
import ch.ethz.mxquery.contextConfig.CompilerOptions;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.contextConfig.XQStaticContext;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.exceptions.StaticException;
import ch.ethz.mxquery.util.LineReader;


public class SingleXQueryServer extends HttpServlet{
	/*
	 * Queries are compiled when a WSDL for a particular module is requested
	 * A new request for the same WSDL triggers checking and recompilation,
	 * until then the compiled query is cached and can be invoked even it is
	 * removed from the file system
	 * For a now, only XQuery 1.0/1.1/Fulltext are enabled, since Updates/Scripting
	 * are not fully synchronized/threadsafe yet, especially wrt to file system operations
	 * Alternatively, all query executions would need to be synchronized
	 */
	private Map statements = new HashMap();
	
	private static final long serialVersionUID = 4945416383991939905L;
	public void doGet(HttpServletRequest request, HttpServletResponse response) throws IOException{
		//Fetching the query (module code)
		
		File dir = new File(getServletContext().getRealPath("/"));
		String serviceName = getServiceName(request);
		StringBuffer  wsdl = new StringBuffer(); 		

		synchronized (this) {
			//File xqueryFile = new File(filePath);
			PreparedStatement statement= null;
			try {
				statement = generateStatement(request, dir, serviceName);
			} catch (FileNotFoundException e) {
					wsdl.append("There is no module named: "+serviceName+" in "+dir);

			} catch (MXQueryException err) {
				wsdl.append(err.getMessage());
			}
			response.setContentType("text/xml; charset=utf-8");
			PrintWriter print = response.getWriter();
			if (statement != null && statement.isWebService()) {
				try {
					//Generating the WSDL
					wsdl = new StringBuffer();
					wsdl.append(statement.generateWSDL(request.getRequestURL().toString()));
				} catch (MXQueryException e) {
					wsdl.append(e.getMessage());
				}
			}
			print.println(wsdl);
		};
	}

	private PreparedStatement generateStatement(HttpServletRequest request,
			File dir, String serviceName)
	throws IOException, MXQueryException {
		StringBuffer query = new StringBuffer();
		String filePath = dir+"/"+serviceName+".xquery";
		LineReader scan = new LineReader(new java.io.FileReader(filePath));
		String ln = scan.readLine();
		while (ln != null ){
			query = query.append(ln);
			ln = scan.readLine();
		}
		scan.close();
		Context ctx = new Context();
		CompilerOptions co = new CompilerOptions();
		//Enabling schema support and feeding the context by available schemas
		co.setSchemaAwareness(true);
		// XQuery 1.1 and Fulltext are "safe", so we can enable them
		co.setFulltext(true);
		co.setXquery11(true);
		addSchemaLocations(ctx, dir, request.getRequestURL().toString());

		XQCompiler compiler;
		compiler = new CompilerImpl();
		PreparedStatement statement =  compiler.compile(ctx, query.toString(),co);
		if (statement != null) {
			if (!statement.isWebService()) {
				if(statement.isModuleDecl()){
					statement.exposeModule();
					statements.put(serviceName, statement);
				} else {
					throw new StaticException(ErrorCodes.A0004_EC_WS_IS_ERROR_MSG,"There was no XQuery module to expose. This is the result of the query we found: "+ query, QueryLocation.OUTSIDE_QUERY_LOC);
				}
			}//TODO: If we can obtain the URL of the server efficiently, here, we can generate WSDL [just once!] 
		}
		return statement;
	}

	private String getServiceName(HttpServletRequest request) {
		String serviceName = request.getRequestURL().toString();
		serviceName = serviceName.substring(serviceName.lastIndexOf("/")+1);
		return serviceName;
	}
	
	public void doPost(HttpServletRequest request, HttpServletResponse response) throws IOException{
		File dir = new File(getServletContext().getRealPath("/"));

		InputStreamReader isr =  new InputStreamReader(request.getInputStream());
		StringBuffer inputSoap = new StringBuffer();
		LineReader lr = new LineReader(isr);
		String tempString = null;
		while ((tempString = lr.readLine()) != null){
			inputSoap.append(tempString);
		}
		//Extract the invoked function's information from the SOAP message,
		//execute it 
		//wrap the result in a SOAP message
		
		String serviceName = getServiceName(request);
		
		try {
			PreparedStatement statement= ((PreparedStatement)statements.get(serviceName));
			if (statement == null) {
				statement = generateStatement(request, dir, serviceName);
			}
			statement.evaluate();
			StringBuffer outputSoap = statement.handleSOAP(getSoapBody(inputSoap.toString()));
			response.setContentType("text/xml; charset=utf-8");
			PrintWriter print = response.getWriter();		
			print.println(outputSoap);

		} catch (MXQueryException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}

	}
	
	public String getSoapBody(String soap) throws IOException {
		// Gets index
		int bodyIndex = soap.indexOf("Body>");
		if (bodyIndex < 0) {
			throw new IOException(
					"Not possible to parse SOAP body in the HttpMessage!");
		}
		int cur = bodyIndex;
		while (soap.charAt(cur - 1) != '<') {
			cur--;
		}

		// Gets bull name of body element. Needed to find the End of the body,
		// because an element inside the body can also have the name Body.
		String bodyName = soap.substring(cur, bodyIndex + 4);
		String withoutBodyStart = soap.substring(bodyIndex + 5);
		int bodyEnd = withoutBodyStart.indexOf(bodyName);
		if (bodyEnd < 0) {
			throw new IOException(
					"Not possible to parse SOAP pody in the HttpMessage!");
		}
		String bodyContent = withoutBodyStart.substring(0, bodyEnd - 2);

		// extracting the list of namespaces in the <envlope> tag
		int envStartFirstIndex = soap.indexOf("Envelope");
		String tempString = soap.substring(envStartFirstIndex+9);
		int envStartLastIndex = tempString.indexOf('>');
		String nsList = tempString.substring(0,envStartLastIndex);
		
		// injecting the namespace list to the net body of the soap response message
		int closingTagIndex = bodyContent.indexOf('>');
		//checking for none-parameter functions: '/>'
		if (bodyContent.charAt(closingTagIndex - 1) == '/'){
			closingTagIndex--;
		}
		bodyContent = bodyContent.substring(0,closingTagIndex)+" "+nsList+bodyContent.substring(closingTagIndex);
		return bodyContent;
	}
	private void addSchemaLocations(XQStaticContext context, File directory, String url) throws IOException{
		String[] children = directory.list();
		for (int i =0;  i < children.length; i++){
    		if (children[i].toLowerCase().endsWith(".xsd")){
    			String filePath = directory+"/"+children[i];
    			File schemaFile = new File(filePath);
    			LineReader scan = new LineReader(new java.io.FileReader(schemaFile));
    			StringBuffer schemaContent = new StringBuffer();
    			String ln = scan.readLine();
    			while (ln != null){
    				schemaContent = schemaContent.append(ln);
    				ln = scan.readLine();
    			}
    			int tnsIndex = schemaContent.indexOf("targetNamespace");
    			String tempString = schemaContent.substring(tnsIndex+15);
    			tempString = tempString.substring(tempString.indexOf("\"")+1);
    			//TODO: Check for single quote as well
    			String schemaURI = tempString.substring(0,tempString.indexOf("\"")).trim();
    			String schemaLocation = url.substring(0,url.indexOf("/services"))+"/schemas/"+children[i].substring(0,children[i].indexOf(".xsd"));
    			context.addSchemaLocation(schemaURI, schemaLocation);
    		}
    	}
	}
}

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
import java.io.IOException;
import java.io.PrintWriter;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

public class MultipleXQueryServer extends HttpServlet{
	/**
	 * 
	 */
	private static final long serialVersionUID = -1325624903589088635L;
	public void doGet(HttpServletRequest request, HttpServletResponse response) throws IOException{
		String[] children;
		response.setContentType("text/html; charset=utf-8");
		PrintWriter print = response.getWriter();
		print.println("<html><head><title>MXQuery Server</title></head>");
		print.println("<body>");

		File dir = new File(getServletContext().getRealPath("/"));
	    children = dir.list();
    	int modNum = 0;
		for (int i =0;  i < children.length; i++){
    		if (children[i].toLowerCase().endsWith(".xquery")){
    			modNum++;
    		}
    	}
		print.println("<b>There are "+ modNum +" XQuery modules available to expose, click the URL to see the coresponding WSDL:</b>");
		print.println("<ul>");
		for (int i =0;  i < children.length; i++){
	    		if (children[i].toLowerCase().endsWith(".xquery")){
	    			print.println("<li><a href=\""+request.getRequestURI()+"/"+children[i].substring(0,children[i].indexOf(".xquery"))+"\">"+children[i].substring(0,children[i].indexOf(".xquery"))+"</a></li>");
	    		}
	    	}
		print.println("</ul><br/></body></html>");
		print.close();
	}
	
	public void doPost(HttpServletRequest request, HttpServletResponse response) throws IOException{
		doGet(request,response);
	}
}

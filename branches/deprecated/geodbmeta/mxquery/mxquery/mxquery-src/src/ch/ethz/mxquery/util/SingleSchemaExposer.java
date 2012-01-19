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

package ch.ethz.mxquery.util;

import java.io.File;
import java.io.IOException;
import java.io.PrintWriter;

import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;


public class SingleSchemaExposer extends HttpServlet{
	/**
	 * 
	 */
	private static final long serialVersionUID = 6876417661315648861L;

	/**
	 * 
	 */
	public void doGet(HttpServletRequest request, HttpServletResponse response) throws IOException{
		StringBuffer schemaContent = new StringBuffer();
		File dir = new File(getServletContext().getRealPath("/"));
		String fileName = request.getRequestURL().toString();
		fileName = fileName.substring(fileName.lastIndexOf("/")+1);
		String filePath = dir+"/"+fileName+".xsd";
		File schemaFile = new File(filePath);
		LineReader scan = new LineReader(new java.io.FileReader(schemaFile));
		String ln = scan.readLine();
		while (ln != null){
			schemaContent = schemaContent.append(ln);
			ln = scan.readLine();
		}
		response.setContentType("text/xml; charset=utf-8");
		PrintWriter print = response.getWriter();
		print.println(schemaContent);
	}
	
	public void doPost(HttpServletRequest request, HttpServletResponse response) throws IOException{
		doGet(request, response);
	}
}

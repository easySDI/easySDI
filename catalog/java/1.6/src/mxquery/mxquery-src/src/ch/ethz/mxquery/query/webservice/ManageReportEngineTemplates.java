package ch.ethz.mxquery.query.webservice;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileReader;
import java.io.FileWriter;
import java.io.IOException;
import java.io.PrintWriter;
import java.io.StringWriter;
import java.util.Map;
import java.util.UUID;

import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import java.io.*;
import javax.servlet.*;
import javax.servlet.http.*;
import java.util.*;



public class ManageReportEngineTemplates  extends HttpServlet{

	/**
	 * 
	 */
	private static final long serialVersionUID = -362714994690704286L;
	/**
	 * 
	 */

	

	public void doPost(HttpServletRequest request, HttpServletResponse response) throws IOException{
		
		String operationStatus ="" ;
		String message ="";
		String result ="";
		
		response.setContentType("text/plain");
		PrintWriter out = response.getWriter();

	
		try{
		int operationType = Integer.parseInt(request.getParameter("operationtype").toString()) ;
		String xQueryCode = request.getParameter("xquerycode").toString();
		String xQueryReportFileId = request.getParameter("fileid").toString();		
	
		
		response.setHeader( "Cache-Control", "no-cache" );	
			
			operationStatus ="success";
	
			if(operationType == 1){
				result= addNewQuery(xQueryCode);
			}else if (operationType ==  0){
				result=removeQuery(xQueryReportFileId);
			}else{
				
				result ="operation unknown";
			}			

			
		}
		catch( XQueryEngineException xe){
			
			result = xe.getError();
			
		}
		catch (Exception e) {			
			 StringWriter sw = new StringWriter();
			 PrintWriter pw = new PrintWriter(sw);
			 e.printStackTrace(pw);		
			 result = "An unknown error occured"+sw.toString();
			 
		}
	
	
		out.println(result);
		
	
	    out.close();
		

	}

	private String  addNewQuery(String xQueryCode) throws XQueryEngineException{
		
	
		try
		{
			File file = new File(getServletContext().getRealPath("/")+ XQueryEngineConstants.TEMPLATE_FILE_NAME);
		
			BufferedReader reader = new BufferedReader(new FileReader(file));
			String line = "", templateText = "";
			while((line = reader.readLine()) != null)
			{
				templateText += line + "\r\n";
			}
			reader.close();
	
			String newtext = templateText+ "\n"+xQueryCode;
			String newName = UUID.randomUUID().toString() ;
			String newPath =  getServletContext().getRealPath("/")+newName +".xquery";
			FileWriter writer = new FileWriter(newPath);
			writer.write(newtext);
			writer.close();
			
			return  newName;
		}
		catch (IOException ioe)
		{
			 StringWriter sw = new StringWriter();
			 PrintWriter pw = new PrintWriter(sw);
			 ioe.printStackTrace(pw);		

			throw new XQueryEngineException("Could not create xquery file"+sw.toString());

		}
	

	}

	private String removeQuery(String xQueryReportFileId) throws XQueryEngineException{
		
			try {
				
			
				File file = new File(getServletContext().getRealPath("/")+ xQueryReportFileId+".xquery");
				if(file.exists())
					file.delete();
				else
					throw new XQueryEngineException("File does not exist"+file.getAbsolutePath());
			} catch (Exception e) {
				
				 StringWriter sw = new StringWriter();
				 PrintWriter pw = new PrintWriter(sw);
				 e.printStackTrace(pw);		

				throw new XQueryEngineException("Could not delete xquery file"+sw.toString());

			}
			return  xQueryReportFileId; //meaning it went well.
		
		

	}
}

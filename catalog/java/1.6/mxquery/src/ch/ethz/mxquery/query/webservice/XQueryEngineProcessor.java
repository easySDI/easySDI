package ch.ethz.mxquery.query.webservice;

import java.io.BufferedInputStream;
import java.io.BufferedReader;
import java.io.DataInputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileReader;
import java.io.FileWriter;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.io.PrintWriter;
import java.io.Reader;
import java.io.StringWriter;
import java.io.Writer;
import java.net.MalformedURLException;
import java.net.URI;
import java.net.URL;
import java.util.UUID;
import java.util.regex.Pattern;

import javax.servlet.http.Cookie;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.xml.namespace.QName;
import javax.xml.stream.XMLStreamReader;
import javax.xml.transform.sax.SAXSource;
import javax.xml.xquery.XQConnection;
import javax.xml.xquery.XQDataSource;
import javax.xml.xquery.XQExpression;
import javax.xml.xquery.XQPreparedExpression;
import javax.xml.xquery.XQResultSequence;
import javax.xml.xquery.XQSequence;
import javax.xml.xquery.XQStaticContext;

import org.xml.sax.InputSource;
import org.xml.sax.XMLReader;

import java.net.URLConnection;


import ch.ethz.mxquery.xqj.MXQueryXQConnection;
import ch.ethz.mxquery.xqj.MXQueryXQDataSource;
import ch.ethz.mxquery.xqj.MXQueryXQExpression;
import ch.ethz.mxquery.xqj.MXQueryXQSequence;

public class XQueryEngineProcessor extends HttpServlet{

	/**
	 * 
	 */
	private static final long serialVersionUID = -4307133488421931688L;
	
	public void doPost(HttpServletRequest request, HttpServletResponse response) throws IOException{
		
	
		String result ="";
		String url ="";
		String fileId ="";
		String xqcode ="";
		String tmpXml ="";
		String cntxPath  ="";
		response.setContentType("text/plain;charset=utf-8");
		response.setHeader( "Cache-Control", "no-cache" );	
		PrintWriter out = response.getWriter();
		Cookie[] cookies ;
		String cookieList = "";
		try{
			cookies = request.getCookies();
		
		    int lastindex = cookies.length-1;
		    if (cookies != null) {
		      for (int i = 0; i < cookies.length; i++) {
		    	  cookieList += cookies[i].getName() +"="+	cookies[i].getValue() ;				
					if ( i !=lastindex)
						cookieList+= ";";	
		        }
		     
		     }
		
		    	


	
			url = request.getParameter("url");
			fileId = request.getParameter("fileid");
			String[] namespaces = request.getParameter("namespaces").split(";");
			int maxRecords = Integer.parseInt(request.getParameter("maxrecords"));			
			int paginationStep =Integer.parseInt(request.getParameter("paginationstep"));
			
			tmpXml = downloadXml(url, cookieList, maxRecords, paginationStep);
			//	String xQueryReportFileId = request.getParameter("fileid").toString();				
		
			
			MXQueryXQDataSource ds = new MXQueryXQDataSource();
			XQConnection conn = ds.getConnection();  

			xqcode = getXQueryCode(fileId,tmpXml );

			// get a static context object with the default values
			XQStaticContext cntxt = conn.getStaticContext();
			String tmp[];
			for(int i=0; i< namespaces.length;i++){
				tmp = namespaces[i].split("=");
				cntxt.declareNamespace(tmp[0], tmp[1]);	
			
			}

			cntxPath = new File(getServletContext().getRealPath("/")).toURI().toString();
			cntxPath =cntxPath.replaceAll("file:/","file:///");
			cntxt.setBaseURI(cntxPath );

			XQPreparedExpression xqe = conn.prepareExpression(xqcode, cntxt);
			XQSequence sequence = xqe.executeQuery();

			sequence.writeSequence( (Writer)out, null); 

			conn.close();
			sequence.close();
			// stage2.close();
			File tmpFile = new File(getServletContext().getRealPath("/"+tmpXml));
			tmpFile.delete();

			
		}
		catch (Exception e) {			
			 StringWriter sw = new StringWriter();
			 PrintWriter pw = new PrintWriter(sw);
			 e.printStackTrace(pw);		
			 result = "An unknown error occured"+sw.toString();
			
		}
	
		//out.println("<?xml version=\"1.0\"?>");	
		out.println(result);			
	    out.close();
		

	}

	private String filterResponseToRemoveResultsEnvelope(String response){
		
		String output ="";
		
		int indexofcswresults = response.indexOf("<csw:SearchResults");
		int indexofClosingQuote = response.indexOf(">", indexofcswresults);
		int indexofClosingResultsQuote = response.indexOf("</csw:SearchResults");
		
		output= response.substring(indexofClosingQuote+1, indexofClosingResultsQuote);
		
		return output;
		
	}

	
	private String downloadXml(String url, String cookieList, int maxRecords, int paginationStep) throws IOException{

		URL u;
	
		String s;
		StringBuffer buffer = new StringBuffer();
		BufferedReader in = null;
		URLConnection urlConn;
		String newName = UUID.randomUUID().toString()  +".xml";
		String newPath =  getServletContext().getRealPath("/")+newName ;
		int remainder = maxRecords % paginationStep;
		int numberofLoops = (int)((int) maxRecords/(int)paginationStep);
		if(remainder!=0){			
			numberofLoops++; //increase by 1 for last call to get remainder;
		}
		
		int startPosition =0;
		String thisCallUrl ="";
		FileWriter writer;
		writer = new FileWriter(newPath);
		
		for (int i = 0; i< numberofLoops; i++){
			   
				try {
					 startPosition = (i*paginationStep);
					 thisCallUrl = url + "&paginationstep="+paginationStep+"&startposition="+startPosition;
					 u = new URL(thisCallUrl);			
					 urlConn =  u.openConnection();
					 urlConn.setRequestProperty("Cookie", cookieList);
		
					 urlConn.connect();
		
			
					in = new BufferedReader(
							new InputStreamReader( urlConn.getInputStream()));
		
		
					while ((s = in.readLine()) != null) 
						buffer.append(s);
					
					in.close();				
					 

		
		
				} catch (MalformedURLException mue) {
					mue.printStackTrace();
		
		
				} catch (IOException ioe) {	        
					ioe.printStackTrace();	     
		
				} 
				
				
				if(i==0)
					writer.append("<?xml version='1.0' encoding='UTF-8'?><results>");				
				
				writer.append(filterResponseToRemoveResultsEnvelope(buffer.toString()));				
					
		}
		
		writer.append("</results>");				
	
		writer.close();
				
		return  newName;
		



	}
	
	
	private String getXQueryCode(String fileid, String XmlFilePath) throws XQueryEngineException{

		
		try
		{
			File file = new File(getServletContext().getRealPath("/"+fileid+".xquery") );
		
			BufferedReader reader = new BufferedReader(new FileReader(file));
			String line = "", templateText = "";
			while((line = reader.readLine()) != null)
			{
				templateText += line + "\r\n";
			}
			reader.close();
	
			String newtext = templateText.replaceAll(XQueryEngineConstants.XMLFILE_TO_REPLACE,XmlFilePath);
		
			return  newtext;
		}
		catch (IOException ioe)
		{
			 StringWriter sw = new StringWriter();
			 PrintWriter pw = new PrintWriter(sw);
			 ioe.printStackTrace(pw);		

			throw new XQueryEngineException("Could not read xquery file"+sw.toString());

		}
	
		
	
		
		
	}

}

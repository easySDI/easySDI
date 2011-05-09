package ch.ethz.mxquery.query.webservice;

import java.io.FileInputStream;
import java.io.IOException;
import java.io.PrintWriter;
import java.io.StringWriter;
import java.io.Writer;
import java.net.URL;

import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.xml.xquery.XQConnection;
import javax.xml.xquery.XQExpression;
import javax.xml.xquery.XQPreparedExpression;
import javax.xml.xquery.XQResultSequence;
import javax.xml.xquery.XQSequence;
import javax.xml.xquery.XQStaticContext;

import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.xqj.MXQueryXQDataSource;

public class testxQuery  extends HttpServlet {

	/**
	 * 
	 */
	private static final long serialVersionUID = -6744658127864292110L;
	
	public void doPost(HttpServletRequest request, HttpServletResponse response) throws IOException{
		
		String operationStatus ="" ;
		String message ="";
		String result ="";
		String url ="";
		String fileId ="";
		String xqcode ="";
		String tmpXml ="";
		
		response.setContentType("text/plain;charset=utf-8");
		response.setHeader( "Cache-Control", "no-cache" );	
		PrintWriter out = response.getWriter();
		String path= "";
		URL u = null;
		try{
			
			url = request.getParameter("url");
			fileId = request.getParameter("fileid");
			// u = new URL(url);
		
		
			//	String xQueryReportFileId = request.getParameter("fileid").toString();				
			operationStatus ="success";

			
			
			
			MXQueryXQDataSource ds = new MXQueryXQDataSource();
			XQConnection conn = ds.getConnection();  
			XQStaticContext cntxt = conn.getStaticContext();
			cntxt.declareNamespace("gmd", "http://www.isotc211.org/2005/gmd");
			cntxt.declareNamespace("gco", "http://www.isotc211.org/2005/gco");
			
			String ex2 =
				 "xquery version '1.0'; "+

				//" import schema namespace  gmd='http://www.isotc211.org/2005/gmd' at 'http://www.isotc211.org/2005/gmd/gmd.xsd' ;"+
			//	" import schema namespace  xlink='http://www.w3.org/1999/xlink' ;"+
				//	"  import schema namespace  gts='http://www.isotc211.org/2005/gts' ;"+
				//		" import schema namespace  srv='http://www.isotc211.org/2005/srv' ;"+
				//			" import schema namespace  bee='http://www.be.ch/bve/agi/2010/bee' ;"+
					//			" import schema namespace  che='http://www.geocat.ch/2008/che' ;"+
						//			" import schema namespace  gco='http://www.isotc211.org/2005/gco' at 'http://www.isotc211.org/2005/gco/gco.xsd' ;"+
						//				" import schema namespace  gml='http://www.opengis.net/gml' ;"+
						//					" import schema namespace  geonet='http://www.fao.org/geonetwork' ;"+
//												" for $x in doc('books.xml')/bookstore/book"+
//												" where $x/price>30" +	
//												" return $x/title";
	

	" for $x in doc('431570e4-c095-410d-9811-ac0df3e09343.xml')/gmd:MD_Metadata"+
				" return $x/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:abstract/gco:CharacterString" ;

			 XQPreparedExpression xqe = conn.prepareExpression(ex2, cntxt);
			 XQSequence sequence = xqe.executeQuery();
			 //			XQPreparedExpression stage2;
//			stage2 = conn.prepareExpression(new FileInputStream("books.xquery"));
//
//		
//			// Execute the query (stage2)
//			XQSequence sequence = stage2.executeQuery();
			
			sequence.writeSequence( (Writer)out, null); 

		

	        conn.close();
	        sequence.close();
	       // stage2.close();
	   

			
		}
		catch (Exception e) {			
			 StringWriter sw = new StringWriter();
			 PrintWriter pw = new PrintWriter(sw);
			 e.printStackTrace(pw);		
			 message = "An unknown error occured"+sw.toString();
			 operationStatus ="failure";
		}
	
		out.println("<?xml version=\"1.0\"?>");
		out.println("<result>");
		out.println(result);
		out.println("</result>");
		out.println("<operationStatus>");
		out.println(operationStatus);
		out.println("</operationStatus>");
		out.println("<message>");
		out.println(message);
		out.println("</message>");
		out.println("<path>");
		out.println(path);
		out.println("</path>");

		
		out.println("<tmpXml>");
		out.println(tmpXml);
		out.println("</tmpXml>");
	
	    out.close();
		

	}
	
	public void doGet(HttpServletRequest request, HttpServletResponse response) throws IOException{
		doPost(request,response );
	}
	
	
}



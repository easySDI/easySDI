package org.easysdi.exportpdf;

import java.io.IOException;
import java.io.PrintWriter;
import java.util.logging.Logger;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import javax.servlet.ServletConfig;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.apache.fop.apps.FOPException;
import org.easysdi.exportpdf.wrapper.FOPWrapper;


public class PdfServlet  extends HttpServlet {

	private String tmpFld;
	private String cfgFld;
	private String IMAGE_PATTERN = "([^\\s]+(\\.(?i)(xml|fo|pdf))$)";
	private Pattern pattern;
	private Matcher matcher;
    private static Log logger = LogFactory.getLog(PdfServlet.class);


	public void init(ServletConfig config) throws ServletException {
		this.tmpFld = config.getInitParameter("tmpfolder");
		this.cfgFld = config.getInitParameter("cfgfolder");
		pattern = Pattern.compile(IMAGE_PATTERN);
		logger.info("ExportPdf servlet initialized.");
	}

	@Override
	protected void doGet(HttpServletRequest req, HttpServletResponse resp)
	throws ServletException {
		// TODO Auto-generated method stub
		//String cfg, String fo, String pdf
		PrintWriter writer = null;
		try {
			writer = resp.getWriter();
			String cfg = req.getParameter("cfg");
			String fo = req.getParameter("fo");
			String pdf = req.getParameter("pdf");

			if(cfg == null){
				writer.println("Error: missing parameter cfg");	
			}
			else if(fo == null){
				writer.println("Error: missing parameter fo");
			}
			else if(pdf == null){
				writer.println("Error: missing parameter pdf");
			} 
			else if (!this.validate(cfg)){
				writer.println("Error: bad name for file: "+cfg);
			}
			else if (!this.validate(fo)){
				writer.println("Error: bad name for file: "+fo);
			}
			else if(!this.validate(pdf)){
				writer.println("Error: bad name for file: "+pdf);
			}
			else{
					String res = "";
					FOPWrapper fw = new FOPWrapper();
					if(cfg != "")
						res = fw.convert(this.cfgFld+cfg, this.tmpFld+fo, this.tmpFld+pdf);
					else
						res = fw.convert("", this.tmpFld+fo, this.tmpFld+pdf);
					if(res != "success")
						writer.println("Error: "+res);
					else
						writer.println("Success");
			}
		} catch (IOException e) {
			writer.println("Error generating the pdf file.");
			logger.error("IO Error: "+e.getMessage()+FOPWrapper.stack2string(e));
		} catch (FOPException e) {
			writer.println("Error generating the pdf file.");
			logger.error("FOP Error: "+e.getMessage()+FOPWrapper.stack2string(e));
		}finally{
			if(writer != null)
				writer.close();
		}
	}

	@Override
	protected void doPost(HttpServletRequest req, HttpServletResponse resp)
	throws ServletException, IOException {
		doGet(req, resp);
	}

	public boolean validate(final String image){

		matcher = pattern.matcher(image);
		return matcher.matches();

	}

}

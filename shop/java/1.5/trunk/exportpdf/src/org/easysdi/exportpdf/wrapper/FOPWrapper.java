package org.easysdi.exportpdf.wrapper;

/*
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements.  See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License.  You may obtain a copy of the License at
 * 
 *      http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/* $Id: ExampleFO2PDF.java 426576 2006-07-28 15:44:37Z jeremias $ */
 


// Java
import java.io.BufferedOutputStream;
import java.io.BufferedWriter;
import java.io.File;
import java.io.FileOutputStream;
import java.io.FileWriter;
import java.io.IOException;
import java.io.OutputStream;
import java.io.PrintWriter;
import java.io.StringWriter;
import java.io.Writer;
import java.util.logging.Logger;

//JAXP
import javax.xml.transform.Transformer;
import javax.xml.transform.TransformerFactory;
import javax.xml.transform.Source;
import javax.xml.transform.Result;
import javax.xml.transform.stream.StreamSource;
import javax.xml.transform.sax.SAXResult;


// FOP
import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.apache.fop.apps.FOUserAgent;
import org.apache.fop.apps.Fop;
import org.apache.fop.apps.FOPException;
import org.apache.fop.apps.FopFactory;
import org.apache.fop.apps.FormattingResults;
import org.apache.fop.apps.MimeConstants;
import org.apache.fop.apps.PageSequenceResults;

/**
 * This class demonstrates the conversion of an FO file to PDF using FOP.
 */
public class FOPWrapper {

    // configure fopFactory as desired
    private FopFactory fopFactory = FopFactory.newInstance();
    private static Log logger = LogFactory.getLog(FOPWrapper.class);

    /**
     * Converts an FO file to a PDF file using FOP
     * @param fo the FO file
     * @param pdf the target PDF file
     * @throws IOException In case of an I/O problem
     * @throws FOPException In case of a FOP problem
     */
    public String convert(String cfg, String fo, String pdf) throws IOException, FOPException {
    	logger.info("converting");
        
    	OutputStream out = null;
        String resp = "";
        //BufferedWriter bw = null;
        try {   
            FOUserAgent foUserAgent = fopFactory.newFOUserAgent();
            // configure foUserAgent as desired
    
            // Setup output stream.  Note: Using BufferedOutputStream
            // for performance reasons (helpful with FileOutputStreams).
            out = new FileOutputStream(pdf);
            out = new BufferedOutputStream(out);
            
            // Construct fop with desired output format
            Fop fop = fopFactory.newFop(MimeConstants.MIME_PDF, foUserAgent, out);
            
            if(cfg != "")
            	fopFactory.setUserConfig(new File(cfg));
            
            // Setup JAXP using identity transformer
            TransformerFactory factory = TransformerFactory.newInstance();
            Transformer transformer;
            transformer = factory.newTransformer(); // identity transformer
            
            
            // Setup input stream
            Source src = new StreamSource(fo);

            // Resulting SAX events (the generated FO) must be piped through to FOP
            Result res = new SAXResult(fop.getDefaultHandler());
            //bw.write("calling transformer");
            // Start XSLT transformation and FOP processing
            transformer.transform(src, res);
            //bw.write("called transformer");
            
            // Result processing
            FormattingResults foResults = fop.getResults();
            java.util.List pageSequences = foResults.getPageSequences();
            for (java.util.Iterator it = pageSequences.iterator(); it.hasNext();) {
                PageSequenceResults pageSequenceResults = (PageSequenceResults)it.next();
                /*
                bw.write("PageSequence " 
                        + (String.valueOf(pageSequenceResults.getID()).length() > 0 
                                ? pageSequenceResults.getID() : "<no id>") 
                        + " generated " + pageSequenceResults.getPageCount() + " pages.");
                        */
                /*
                System.out.println("PageSequence " 
                        + (String.valueOf(pageSequenceResults.getID()).length() > 0 
                                ? pageSequenceResults.getID() : "<no id>") 
                        + " generated " + pageSequenceResults.getPageCount() + " pages.");
                        */
            }
            //System.out.println("Generated " + foResults.getPageCount() + " pages in total.");
            //bw.write("ending!");
            resp = "success";
            return resp;
        } catch (Exception e) {
        	/*
        	bw.write(e.getMessage());
        	Writer writer = new StringWriter();
        	 PrintWriter printWriter = new PrintWriter(writer);
        	 e.printStackTrace(printWriter);
        	 bw.write(writer.toString());
        	 */
        	logger.error(stack2string(e));
            return e.getMessage();
        } finally {
        	/*
        	if(bw != null)
        		bw.close();
        		*/
        	if(out != null)
        		out.close();
        	//clear fo file
        	try
        	{
        		File foTmp = new File(fo);
        		foTmp.delete();
        	} catch (Exception e) {
        		logger.error(stack2string(e));
            }
        }
    }

    public static String stack2string(Exception e) {
    	  try {
    	    StringWriter sw = new StringWriter();
    	    PrintWriter pw = new PrintWriter(sw);
    	    e.printStackTrace(pw);
    	    return sw.toString();
    	  }
    	  catch(Exception e2) {
    	    return "bad stack2string";
    	  }
    }

    /**
     * Main method.
     * @param args command-line arguments
     */
    public static void main(String[] args) {
        try {
            System.out.println("FOP ExampleFO2PDF\n");
            System.out.println("Preparing...");
            
            //Setup input and output files            
            String fofile = "C:\\TEMP\\fo\\509.fo";
            //File fofile = new File(baseDir, "../fo/pagination/franklin_2pageseqs.fo");
            String pdffile = "C:\\TEMP\\fo\\509.pdf";
            String cfgfile = "C:\\TEMP\\fo\\fop.xml";
            
            System.out.println("Input: XSL-FO (" + fofile + ")");
            System.out.println("Output: PDF (" + pdffile + ")");
            System.out.println("User config: (" + cfgfile + ")");
            System.out.println();
            System.out.println("Transforming...");
            
            FOPWrapper app = new FOPWrapper();
            app.convert(cfgfile, fofile, pdffile);
            
            System.out.println("Success!");
        } catch (Exception e) {
            e.printStackTrace(System.err);
            //System.exit(-1);
        }
    }
}

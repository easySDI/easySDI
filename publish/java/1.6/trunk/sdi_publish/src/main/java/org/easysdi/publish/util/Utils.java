package org.easysdi.publish.util;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.PrintWriter;
import java.io.StringWriter;
import java.io.Writer;
import java.net.MalformedURLException;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.LinkedList;
import java.util.List;
import java.util.Map;
import java.util.logging.Logger;
import java.util.zip.ZipEntry;
import java.util.zip.ZipInputStream;

import org.apache.commons.codec.binary.Base64;
import org.deegree.services.wps.ProcessletException;
import org.deegree.services.wps.input.ComplexInput;
import org.deegree.services.wps.input.LiteralInput;
import org.deegree.services.wps.input.ProcessletInput;
import org.easysdi.publish.exception.DataInputException;
import org.easysdi.publish.exception.PublishConfigurationException;
import org.easysdi.publish.exception.TransformationException;
import org.easysdi.publish.util.BinaryIn;
import org.easysdi.publish.util.BinaryOut;

import com.eaio.uuid.UUID;


public class Utils {
	static Logger logger = Logger.getLogger("org.easysdi.publish.Utils");
	public static DateFormat dateFormat = new SimpleDateFormat("yyyy-MM-dd");
	
	public static String exceptiontoXML(String errorCode, String errorMessage){
		return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>"+
		"<ows:ExceptionReport xmlns:ows=\"http://www.opengis.net/ows/1.1\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.opengis.net/ows/1.1../../../ows/1.1.0/owsExceptionReport.xsd\" version=\"1.0.0\" xml:lang=\"en-CA\">"+
		"<ows:Exception exceptionCode=\""+errorCode+"\">"+
		"<ows:ExceptionText>"+errorMessage+"</ows:ExceptionText>"+
		"</ows:Exception>"+
		"</ows:ExceptionReport>";
	}
		
	public static List<String> getParameter(String parameter, Map<String, List<String>> paramMap) throws DataInputException{
		//Fill with map content
		if(paramMap.containsKey(parameter))
			return paramMap.get(parameter);
		else
			throw new DataInputException("The required parameter:"+parameter+" was missing");
	}

	//header of the WPS execute response
	public static String WPSHeader(String methodName)
	{
		return "<?xml version=\"1.0\" encoding=\"UTF-8\"?><wps:Execute service=\"WPS\" version=\"1.0.0\" store=\"false\" status=\"false\" xmlns:wps=\"http://www.opengeospatial.net/wps\" xmlns:ows=\"http://www.opengis.net/ows\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.opengeospatial.net/wps..\\wpsExecute.xsd\">"+
		"<ows:Identifier>"+methodName+"</ows:Identifier>" +
		"<wps:ResponseForm>";
	}

	//Variables of the WPS execute response
	public static String WPSResponseFiller(String dataType, String dataItself)
	{
		return "<wps:RawDataOutput>"+
		"<ows:Identifier>"+dataType+"</ows:Identifier>"+
		"<ows:Data>"+ dataItself +"</ows:Data>"+
		"</wps:RawDataOutput>";
	}

	//footer of the WPS execute response
	public static String WPSFooter()
	{
		return "</wps:ResponseForm>"+
		"</wps:Execute>";    	
	}

	//helper to build the body of the WPS execute response
	public static String WPSResponseSectionDataInput( Map<String, List<String>>inpMap )
	{
		StringBuilder ret = new StringBuilder();
		ret.append("<wps:DataInputs>");

		java.util.Set<String> keySet = inpMap.keySet();
		for( String key : keySet)
		{
			ret.append("<wps:Input>");
			ret.append("<ows:Identifier>");
			ret.append(key);	
			ret.append("</ows:Identifier>");
			ret.append("<ows:Data>");
			//TODO: Handle the multimap case
			ret.append(inpMap.get(key));	
			ret.append("</ows:Data>");
			ret.append("<wps:Reference xlink:href=\"http://foo.bar/some_WFS_request.xml\"/>");
			ret.append("</wps:Input>");    		
		}
		ret.append("</wps:DataInputs>");

		return ret.toString();
	}
	
	public static String getStackTrace(Throwable aThrowable) {
	    final Writer result = new StringWriter();
	    final PrintWriter printWriter = new PrintWriter(result);
	    aThrowable.printStackTrace(printWriter);
	    return result.toString();
	}
	
	public static List<String> writeHttpGetToFileSystem(String location, List<String> URLs, ComplexInput ZipInput)
	throws MalformedURLException, IOException, TransformationException, PublishConfigurationException {
				
		//Read the supplied files. 
		//We either have:
		//- 1 to many file(s) in URLFile param, that we'll
		//  have to GET into a temp folder.
		//- 1 single zip file in URLFile param, that we'll have to
		//  unzip into a temp folder.
		//- 1 and inline base64 encoded zip file, that we'll also have to
		//  unzip into a temp folder.
		//
		
		//We're gonna build a list of pathes "outFileArray" that we return.  
		List<String> outFileArray = new ArrayList<String>();
		
		//create a local accessible folder
		String tempFileDir = System.getProperty("java.io.tmpdir")+"/"+location+"/";
		logger.info("Temp file dir is: " + tempFileDir);
		boolean success = (new File(tempFileDir)).mkdir();
		if (success) {
			logger.info("Directory: " + tempFileDir + " created");
		}
		else{
			logger.info("Directory: " + tempFileDir + " not created");
		}

		
		InputStream ZipInputStream = null;
		//do the url list
		if(URLs.size() != 0){
			System.out.println("There are files to treat");
			//
			// We have a single zip file
			//
			String zipFileStringUrl = URLs.get(0);
			if(URLs.size() == 1 && (zipFileStringUrl.substring(zipFileStringUrl.length()-3, zipFileStringUrl.length()).equalsIgnoreCase("zip"))){
				System.out.println("Single Zip file to treat");
				
				//move file locally
				BinaryIn  in  = new BinaryIn(zipFileStringUrl.replace(" ", "%20"));
				String[] temp = zipFileStringUrl.split("/");
				String outFile = tempFileDir+temp[temp.length-1];
				BinaryOut out = new BinaryOut(outFile);
				// read one 8-bit char at a time
				while (!in.isEmpty()){
					Byte b = in.readByte();
					out.write(b);
				}
				out.flush();
				out.close();
				in.close();
				
				//unzip files
				outFileArray = unzipFile(tempFileDir, outFile);
	
			}
			//
			//Treat all other files
			//
			else{
				System.out.println("Many files");
				//treat the files given by url		
				for (String strUrl : URLs) {
					BinaryIn  in  = new BinaryIn(strUrl.replace(" ", "%20"));
					String[] temp = strUrl.split("/");
					String outFile = tempFileDir+temp[temp.length-1];
					outFileArray.add(outFile);
					BinaryOut out = new BinaryOut(outFile);
					// read one 8-bit char at a time
					while (!in.isEmpty()){
						Byte b = in.readByte();
						out.write(b);
					}
					out.flush();
					out.close();
					in.close();
				}
			}
		}
		//Base64 inline Zip input.
		else if(ZipInput != null){
			System.out.println("ZipInput to treat");
			InputStream tmpInputStream = ZipInput.getValueAsBinaryStream();
			//decode base64 zip content
			BinaryIn  intmp  = new BinaryIn(tmpInputStream);
			UUID uuid = new UUID();
		    String tempFile = tempFileDir+uuid.toString()+".zip";	
			BinaryOut outtmp = new BinaryOut(tempFile);
			
			while (!intmp.isEmpty()){
				Byte b = intmp.readByte();
				outtmp.write(b);
			}
			outtmp.flush();
			outtmp.close();
			intmp.close();
			
			System.out.println("inline zip content:"+tempFile);
			
			//File tmpFile = new File(tempFile);
			//String zipDecoded = Utils.decodeBase64(tempFileDir, tmpFile);
			//unzip files
			outFileArray = unzipFile(tempFileDir, tempFile);
			
		}else
		{
			throw new TransformationException("You must provide either an URLFile or a ZipInput as input data.");
		}

		System.out.println("Files  written in Java temp dir:"+tempFileDir);
		for(String name : outFileArray)
			System.out.println("returned file:"+name);
		return outFileArray;
	}
	
	public static String getShellPrefix(){
		if(isWindows()){
			return "cmd /c "; 
		}else if(isMac()){
			return "bash -c ";
		}else if(isUnix()){
			return "";
		}else{
			return "bash -c ";
		}
	}
	
	public static boolean isWindows(){
		 
		String os = System.getProperty("os.name").toLowerCase();
		//windows
	    return (os.indexOf( "win" ) >= 0); 
 
	}
 
	public static boolean isMac(){
 
		String os = System.getProperty("os.name").toLowerCase();
		//Mac
	    return (os.indexOf( "mac" ) >= 0); 
 
	}
 
	public static boolean isUnix(){
 
		String os = System.getProperty("os.name").toLowerCase();
		//linux or unix
	    return (os.indexOf( "nix") >=0 || os.indexOf( "nux") >=0);
 
	}

	// Returns the contents of the file in a byte array.
	public static byte[] getBytesFromFile(File file) throws IOException {
	    InputStream is = new FileInputStream(file);

	    // Get the size of the file
	    long length = file.length();

	    // You cannot create an array using a long type.
	    // It needs to be an int type.
	    // Before converting to an int type, check
	    // to ensure that file is not larger than Integer.MAX_VALUE.
	    if (length > Integer.MAX_VALUE) {
	        // File is too large
	    }

	    // Create the byte array to hold the data
	    byte[] bytes = new byte[(int)length];

	    // Read in the bytes
	    int offset = 0;
	    int numRead = 0;
	    while (offset < bytes.length
	           && (numRead=is.read(bytes, offset, bytes.length-offset)) >= 0) {
	        offset += numRead;
	    }

	    // Ensure all the bytes have been read in
	    if (offset < bytes.length) {
	        throw new IOException("Could not completely read file "+file.getName());
	    }

	    // Close the input stream and return bytes
	    is.close();
	    return bytes;
	}
	
	public static String decodeBase64(String tempFileDir, File inputFile) throws IOException, PublishConfigurationException{
		    UUID uuid = new UUID();
		    String fileName = uuid.toString();
		    String tempFile = tempFileDir+fileName+".zip";	
		    byte [] decoded = Base64.decodeBase64(getBytesFromFile(inputFile));
			BinaryOut out = new BinaryOut(tempFile);
			
			for(byte b : decoded)
				out.write(b);

			out.flush();
			out.close();
			return tempFile;
	}
	
	public static File encodeBase64(File inputfile) throws IOException, PublishConfigurationException{
		byte[] encoded = Base64.encodeBase64(getBytesFromFile(inputfile));
		UUID uuid = new UUID();
	    String fileName = uuid.toString();
	    String tempFile = System.getProperty("java.io.tmpdir")+"/"+fileName+"/";	
		BinaryOut out = new BinaryOut(tempFile);
		
		for(byte b : encoded)
			out.write(b);

		out.flush();
		out.close();
		File f = new File(tempFile);
		return f;	
	}
	
	private static List<String> unzipFile(String tempFileDir, String zipFile) throws IOException{
		List<String> retFiles = new ArrayList<String>(); 
		byte[] buf = new byte[1024];			
		ZipEntry zipentry;
		ZipInputStream zipinputstream = new ZipInputStream(new FileInputStream(zipFile));
        zipentry = zipinputstream.getNextEntry();
        while (zipentry != null) 
        { 
            //for each entry to be extracted
            String entryName = zipentry.getName();
            logger.info("zip entryname: "+entryName);
            int n;
            FileOutputStream fileoutputstream;
            File newFile = new File(entryName);
            String directory = newFile.getParent();
            
            if(directory == null)
            {
                if(newFile.isDirectory())
                    break;
            }
            
            fileoutputstream = new FileOutputStream(
            		tempFileDir+entryName);             

            logger.info("unzip: "+tempFileDir+entryName);
            
            retFiles.add(tempFileDir+entryName);
            
            while ((n = zipinputstream.read(buf, 0, 1024)) > -1)
                fileoutputstream.write(buf, 0, n);

            fileoutputstream.close(); 
            zipinputstream.closeEntry();
            zipentry = zipinputstream.getNextEntry();

            
        }//while
        zipinputstream.close();
        
        return retFiles;
	}
}

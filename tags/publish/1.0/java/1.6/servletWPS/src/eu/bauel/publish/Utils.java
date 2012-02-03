package eu.bauel.publish;

import java.io.BufferedReader;
import java.io.File;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.PrintWriter;
import java.io.StringWriter;
import java.io.Writer;
import java.net.MalformedURLException;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.HashMap;
import java.util.LinkedList;
import java.util.List;
import java.util.Map;
import java.util.logging.Logger;

import eu.bauel.publish.exception.DataInputException;
import eu.bauel.publish.exception.PublishConfigurationException;
import eu.bauel.publish.exception.TransformationException;
import eu.bauel.publish.transformation.BinaryIn;
import eu.bauel.publish.transformation.BinaryOut;

import net.opengis.wps._1_0.InputType;

public class Utils {
	static Logger logger = Logger.getLogger("eu.bauel.publish.Utils");
	public static DateFormat dateFormat = new SimpleDateFormat("yyyy-MM-dd");
	
	public static String exceptiontoXML(String errorCode, String errorMessage){
		return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>"+
		"<ows:ExceptionReport xmlns:ows=\"http://www.opengis.net/ows/1.1\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.opengis.net/ows/1.1../../../ows/1.1.0/owsExceptionReport.xsd\" version=\"1.0.0\" xml:lang=\"en-CA\">"+
		"<ows:Exception exceptionCode=\""+errorCode+"\">"+
		"<ows:ExceptionText>"+errorMessage+"</ows:ExceptionText>"+
		"</ows:Exception>"+
		"</ows:ExceptionReport>";
	}
	
	/**
	 * Parse input parameter and store them in a map where the value is a list of string
	 * => Trick to create a multimap
	 * @param execute
	 * @return
	 */
	public static Map<String, List<String>> makeParamMap(  net.opengis.wps._1_0.Execute execute )
	{
		List<InputType> lInputs = execute.getDataInputs().getInput();
		Map<String,List<String>>  paramMap = new HashMap<String, List<String>>();
		for( InputType ipT : lInputs )
		{
			//If the map does not contain this identifier crate a new entry
			//If is does add the value in the list corresponding to this entry
			boolean matchingKey = false;

			for( String key : paramMap.keySet() )
			{
				String lval = key.trim();
				String rval = ipT.getIdentifier().getValue().trim() ;
				if(  lval.equals(rval) )
				{
					List<String> listForMap = paramMap.get( rval );
					listForMap.add(ipT.getData().getLiteralData().getValue());

					matchingKey = true;
					break;
				}
			}	    	

			if( false == matchingKey)
			{
				List<String> listForMap = new LinkedList<String>();
				listForMap.add(ipT.getData().getLiteralData().getValue().trim() );
				paramMap.put(ipT.getIdentifier().getValue().trim(), listForMap );
			}

		}

		return paramMap;
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
	
	public static String writeHttpGetToFileSystem(String location, List<String> URLs)
	throws MalformedURLException, IOException, TransformationException, PublishConfigurationException {
		String tempFileDir = System.getProperty("java.io.tmpdir")+"/"+location+"/";
		logger.info("Temp file dir is: " + tempFileDir);
		boolean success = (new File(tempFileDir)).mkdir();
		if (success) {
			logger.info("Directory: " + tempFileDir + " created");
		}
		else{
			logger.info("Directory: " + tempFileDir + " not created");
		}

		
		for (String strUrl : URLs) {
			BinaryIn  in  = new BinaryIn(strUrl.replace(" ", "%20"));
			String[] temp = strUrl.split("/");
			BinaryOut out = new BinaryOut(tempFileDir+temp[temp.length-1]);
			// read one 8-bit char at a time
			while (!in.isEmpty()){
				Byte b = in.readByte();
				out.write(b);
			}
			out.flush();
			out.close();
			in.close();
		}
		 
		/*
		for (String strUrl : URLs) {

			String[] temp = strUrl.split("/");
			URL url = new URL(strUrl);
			URLConnection site = url.openConnection();
			InputStream is = site.getInputStream();
			InputStreamReader isr = new InputStreamReader(is);
			//String defaultEncoding = isr.getEncoding();
			//System.out.println("reader default encoding:"+defaultEncoding);

			//output
			FileOutputStream fos = new FileOutputStream(tempFileDir+temp[temp.length-1]);
			Writer out = new OutputStreamWriter(fos);


			Reader in = new BufferedReader(isr);
			int ch;
			PrintStream buffer;
			while ((ch = in.read()) > -1) {
				out.write((char)ch);
			}
			in.close();
			out.close();
		}
		*/

		System.out.println("tempdir:"+tempFileDir);
		return tempFileDir;
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

	
}

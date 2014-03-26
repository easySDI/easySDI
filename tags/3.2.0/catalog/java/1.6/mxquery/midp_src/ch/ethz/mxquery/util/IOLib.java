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

//import java.io.BufferedInputStream;
//import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.io.PrintStream;
import java.io.Reader;
import java.io.UnsupportedEncodingException;
//import java.net.URL;
//import java.net.URLConnection;
import javax.microedition.io.Connector;


import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;

public class IOLib {

	public static void copyFile(String source, String destination) throws MXQueryException{
		//TODO: replace with code from JSR75
//		try {
//		File src = new File(source);
//		File dst = new File(destination);
//		if (src.exists()) {
//			// create a copy of the file, possibly overwriting previous copies
//			FileInputStream in = new FileInputStream(src);
//			FileOutputStream out = new FileOutputStream(dst, false);
//		      byte[] buf = new byte[4096];
//		      int len;
//		      while ((len = in.read(buf)) > 0){
//		        out.write(buf, 0, len);
//		      }
//		      in.close();
//		      out.close();
//		}
//
//		} catch (IOException io) {
//			throw new DynamicException(ErrorCodes.A0007_EC_IO,"I/O Error copying file "+io.toString(),QueryLocation.OUTSIDE_QUERY_LOC);
//		}
	}
	
	/**
	 * Check the file encoding using a 8bit representation
	 * @param ins Input stream to check
	 * @param xquery snoop an xquery file, not an XML file
	 * @return Encoding String if detected, otherwise null
	 * @throws UnsupportedEncodingException
	 * @throws IOException
	 */

	public static final String snoopEncoding8bit(InputStream ins, boolean xquery) throws UnsupportedEncodingException, IOException {
		String encoding = null;
		String typeToSearch = "?xml";
		if (xquery)
			typeToSearch = "xquery";

		// Check all the encoding that are 8bit-oriented for the normal characters
		InputStreamReader rs = new InputStreamReader(ins, "us-ascii");
		char [] cbuf = new char[1025];
		int bytes = rs.read(cbuf, 0, 1024);
		if (bytes > 0 ) {
			String startLine = new String(cbuf);
			if (startLine != null && startLine.indexOf(typeToSearch) >= 0) {
				int encodingPos = startLine.indexOf("encoding=\"");
				if (encodingPos > 0) {
					String tempEncoding = startLine.substring(encodingPos + 10);
					int endEncoding = tempEncoding.indexOf('"');
					encoding = tempEncoding.substring(0, endEncoding);
				}
			}
		}
		return encoding;
	}

	/**
	 * Check the file encoding using a 16bit representation
	 * @param ins Input stream to check
	 * @param xquery TODO
	 * @return Encoding String if detected, otherwise null
	 * @throws UnsupportedEncodingException
	 * @throws IOException
	 */

	public static final String snoopEncoding16bit(InputStream ins, boolean xquery) throws UnsupportedEncodingException, IOException {

		String encoding = null;
		String typeToSearch = "?xml";
		if (xquery)
			typeToSearch = "xquery";

		InputStreamReader rs = new InputStreamReader(ins, "utf-16");
		char [] cbuf = new char[1025];
		int bytes = rs.read(cbuf, 0, 1024);
		if (bytes > 0 ) {
			String startLine = new String(cbuf);
			if (startLine != null && startLine.indexOf(typeToSearch) >= 0) {
				encoding = "utf-16";
			}
		}
		return encoding;
	}
	
	public static Reader getInput (String uri, QueryLocation loc) throws MXQueryException {
		String encoding = "utf-8";
		InputStream ins = null;
		

		  
		try {
		    ins = Connector.openInputStream(uri);
			encoding = "iso-8859-1";
			String tempEncoding = IOLib.snoopEncoding8bit(ins, false);
			if (tempEncoding != null) {
				encoding = tempEncoding;
			} else {
				ins.close();
				ins = Connector.openInputStream(uri);
				tempEncoding = IOLib.snoopEncoding16bit(ins, false);
				if (tempEncoding != null) {
					encoding = tempEncoding;
				}
			}
			ins.close();
			ins = Connector.openInputStream(uri);

		    return new InputStreamReader(ins, encoding);
		}
		catch( IOException ioe ){
		    throw new DynamicException(ErrorCodes.F0014_ERROR_RETRIEVING_RESOURCE, "File '" + uri.toString() + "' could not be openend " + ioe.toString(), loc);
		}

		
//			if (xml.exists()) {
//				try {
//					ins = new FileInputStream(xml);
//					// snoop encoding 
//					encoding = "iso-8859-1";
//					String tempEncoding = IOLib.snoopEncoding8bit(ins, false);
//					if (tempEncoding != null) {
//						encoding = tempEncoding;
//					} else {
//						ins.close();
//						ins = new FileInputStream(xml);
//						tempEncoding = IOLib.snoopEncoding16bit(ins, false);
//						if (tempEncoding != null) {
//							encoding = tempEncoding;
//						}
//					}
//					ins.close();
//					ins = new FileInputStream(xml);
//					return new BufferedReader(new UnicodeReader(ins, encoding));
//				} catch (UnsupportedEncodingException ue) {
//					try {
//						ins.close();
//					} catch (IOException ie) {
//						//
//					}
//					throw new DynamicException(ErrorCodes.A0007_EC_IO, "Unsupported encoding '" + encoding + "' in File '" + uri.toString(), loc);				
//				}catch (Exception e) {
//					try {
//						ins.close();
//					} catch (IOException ie) {
//						//
//					}
//					throw new DynamicException(ErrorCodes.F0014_ERROR_RETRIEVING_RESOURCE, "File '" + uri.toString() + "' could not be openend " + e.toString(), loc);
//				}
//
//			} else {
//			throw new DynamicException(ErrorCodes.F0014_ERROR_RETRIEVING_RESOURCE, "File '" + uri.toString() + "' does not exist!", loc);
//			}
//		}
	}
	
	public static String getSystemBaseUri() {
		String curDir = System.getProperty("user.dir");
		return curDir;
	}
	
	public static PrintStream getOutput(String url, boolean append, String enconding) throws IOException {
//		OutputStream outw = new FileOutputStream(new File(url), false);
//		return new PrintStream(outw,false);
		return null;
	}
	
}

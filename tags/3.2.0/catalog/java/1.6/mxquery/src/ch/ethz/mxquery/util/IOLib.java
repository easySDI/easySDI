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

import java.io.BufferedInputStream;
import java.io.BufferedReader;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.io.PrintStream;
import java.io.Reader;
import java.io.UnsupportedEncodingException;
import java.net.URI;
import java.net.URISyntaxException;
import java.net.URL;
import java.net.URLConnection;

import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;

public class IOLib {

	public static void copyFile(String source, String destination) throws MXQueryException{
		FileInputStream in = null;
		FileOutputStream out = null;
		try {
		File src = new File(new URI(source));
		File dst = new File(new URI(destination));
		if (src.exists()) {
			// create a copy of the file, possibly overwriting previous copies
			in = new FileInputStream(src);
			out = new FileOutputStream(dst, false);
		      byte[] buf = new byte[4096];
		      int len;
		      while ((len = in.read(buf)) > 0){
		        out.write(buf, 0, len);
		      }
		}
		
		} catch (IOException io) {
			throw new DynamicException(ErrorCodes.A0007_EC_IO,"I/O Error copying file "+io.toString(),QueryLocation.OUTSIDE_QUERY_LOC);
		} catch (URISyntaxException e) {
			throw new DynamicException(ErrorCodes.A0007_EC_IO,"I/O Error copying file "+e.toString(),QueryLocation.OUTSIDE_QUERY_LOC);
		} 
		finally {
			if (in != null)
				try {
					in.close();
				} catch (IOException e) {
					// TODO Auto-generated catch block
					e.printStackTrace();
				}
			if (out != null)
				try {
					out.close();
				} catch (IOException e) {
					// TODO Auto-generated catch block
					e.printStackTrace();
				}			
		}
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
			if (startLine.indexOf(typeToSearch) >= 0) {
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
			if (startLine.indexOf(typeToSearch) >= 0) {
				encoding = "utf-16";
			}
		}
		return encoding;
	}
	
	public static Reader getInput (String uri, QueryLocation loc) throws MXQueryException {
		String encoding = "utf-8";
		InputStream ins = null;
		if (uri.startsWith("http://") || uri.startsWith("https://")) {
			try {
				URL url = new URL(uri);
				URLConnection conn = url.openConnection();
				ins = conn.getInputStream();
				// try to use content-type specific
				String contentType = conn.getContentType();
				int encPos = contentType.indexOf("charset=");
				if (encPos > 0)
					encoding = contentType.substring(encPos+8); //lenght of "charset="
				//encoding = "iso-8859-1";
				BufferedInputStream bufs = new BufferedInputStream(ins);
				bufs.mark(16384);
				String tempEncoding = IOLib.snoopEncoding8bit(bufs, false);
				if (tempEncoding != null) {
					encoding = tempEncoding;
				} else {
					bufs.reset();
					bufs.mark(16384);
					tempEncoding = IOLib.snoopEncoding16bit(bufs, false);
					if (tempEncoding != null) {
						encoding = tempEncoding;
					}
				}
				bufs.reset();
				return new BufferedReader(new UnicodeReader(bufs, encoding));
			} catch (IOException e) {
				throw new DynamicException(ErrorCodes.F0014_ERROR_RETRIEVING_RESOURCE, "I/O Error - Remote Data cannot be accessed: " + e, loc);
			}
		} else {
			File xml;
			try {
				xml = new File(new URI(uri));
			} catch (URISyntaxException e) {
				throw new DynamicException(ErrorCodes.F0017_INVALID_ARGUMENT_TO_FN_DOC, "Invalid URI given to fn:doc", loc);
			}
			
		//	xml = new File(uri);
			if (xml.exists()) {
				try {
					ins = new FileInputStream(xml);
					// snoop encoding 
					encoding = "iso-8859-1";
					String tempEncoding = IOLib.snoopEncoding8bit(ins, false);
					if (tempEncoding != null) {
						encoding = tempEncoding;
					} else {
						ins.close();
						ins = new FileInputStream(xml);
						tempEncoding = IOLib.snoopEncoding16bit(ins, false);
						if (tempEncoding != null) {
							encoding = tempEncoding;
						}
					}
					ins.close();
					ins = new FileInputStream(xml);
					return new BufferedReader(new UnicodeReader(ins, encoding));
				} catch (UnsupportedEncodingException ue) {
					try {
						ins.close();
					} catch (IOException ie) {
						//
					}
					throw new DynamicException(ErrorCodes.A0007_EC_IO, "Unsupported encoding '" + encoding + "' in File '" + uri, loc);				
				}catch (Exception e) {
					try {
						ins.close();
					} catch (IOException ie) {
						//
					}
					throw new DynamicException(ErrorCodes.F0014_ERROR_RETRIEVING_RESOURCE, "File '" + uri + "' could not be openend " + e.toString(), loc);
				}

			} else {
			throw new DynamicException(ErrorCodes.F0014_ERROR_RETRIEVING_RESOURCE, "File '" + uri + "' does not exist!", loc);
			}
		}
	}
	
	public static PrintStream getOutput(String url, boolean append, String encoding) throws IOException, MXQueryException {
		URI uri = null;
		try {
			uri = new URI(url);
		} catch (URISyntaxException e) {
			throw new DynamicException(ErrorCodes.A0007_EC_IO,"Error Creating output file - invalid file name/URI: "+url,QueryLocation.OUTSIDE_QUERY_LOC);
		}
		OutputStream outw = new FileOutputStream(new File(uri), append);
		return new PrintStream(outw,false,encoding);
	}
	
	public static String getSystemBaseUri() {
		String curDir = System.getProperty("user.dir");
		File fl = new File(curDir);
		return fl.toURI().toString();
	}
}

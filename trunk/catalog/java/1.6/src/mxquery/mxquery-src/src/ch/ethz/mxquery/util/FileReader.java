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

import java.io.IOException;
import java.io.Reader;

import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;

/**
 * TODO Replace for CLDC!!
 * @author anands
 *
 */
public class FileReader {
//	public static String uriToString(String uri) throws IOException {
//		return fileToString(new File(uri));
//	}
//	protected static String fileToString(File file) throws IOException {
//		FileInputStream fileInput = new FileInputStream(file);
//		byte[] b = new byte[fileInput.available()];
//		fileInput.read(b);
//		fileInput.close();
//		return new String(b);
//	}
	public static String getFileContent(String uri) throws IOException, MXQueryException{		
		Reader ir = IOLib.getInput(uri, QueryLocation.OUTSIDE_QUERY_LOC);
		LineReader lr = new LineReader(ir);
		StringBuffer buf = new StringBuffer();
		String str;
		while ((str = lr.readLine()) != null) {
		buf.append(str).append("\n");
		}
		lr.close();
		return buf.toString();
	}
}

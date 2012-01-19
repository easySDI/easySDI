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

/**
 * Line Reader for a Reader. <br />
 * Needed because CLDC doesn't support BufferedReader
 * 
 * @author David Graf
 * 
 */
public class LineReader {
	private Reader reader;

	public LineReader(Reader reader) {
		this.reader = reader;
	}
	
	public String readLine() throws IOException {
		StringBuffer sb = new StringBuffer();
		int i = 0;
		if ((i = this.reader.read()) < 0) {
			return null;
		}
		do {
			if ((char)i == '\n') {
				break;
			} else if ((char)i == '\r') {
				// a '\r' must be followed by a '\n'
				this.reader.read();
				break;
			}
			sb.append((char)i);
		} while ((i = this.reader.read()) >= 0);
		return sb.toString();
	}
	
	public int read() throws IOException {
		return this.reader.read();
	}
	
	public void close() throws IOException {
		this.reader.close();
	}
}

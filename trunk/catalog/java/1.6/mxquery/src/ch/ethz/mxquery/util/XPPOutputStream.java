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
import java.io.OutputStream;

public class XPPOutputStream extends OutputStream {

	OutputStream fStream;
	
	public XPPOutputStream(OutputStream out) {
		fStream = out;
	}

	private boolean started = true;
	private int depth = 0;

	private int buf0 = -1;
	private int buf1 = -1;
	
	public void write(int b) throws IOException {
		if (buf1 == '<') {
			if (!started && b != '\n') {
				fStream.write('\n');
			} else {
				started = false;
			}
			if (b == '/'){
				depth--;
			}
			for (int j = 0; j < depth; j++) {
				fStream.write(new byte[] {' ', ' '});
			}
			if (b != '/'){
				depth++;
			}
		}
		if (buf1 > 0)
			fStream.write(buf1);
		if (buf1 == '>') {
			if (b != '<' && b!= '\n') {
				fStream.write('\n');
				for (int j = 0; j < depth; j++) {
					fStream.write(new byte[] {' ', ' '});
				}
			} else  if (buf0 == '/') {
				depth--;
			}
		}
		buf0 = buf1;
		buf1 = b;
	}
	
	public void close() throws IOException {
		fStream.write(buf1);
		super.close();
	}

}

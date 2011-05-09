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


public class StringReader extends Reader {

    private String str;
    private int length;
    private int next = 0;
    
    
    public StringReader(String s) {
	this.str = s;
	this.length = s.length();
    }


    public int read() throws IOException {
	    if (next >= length)
		return -1;
	    return str.charAt(next++);
    }

    public int read(char cbuf[], int off, int len) throws IOException {
            if ((off < 0) || (off > cbuf.length) || (len < 0) ||
                ((off + len) > cbuf.length) || ((off + len) < 0)) {
                throw new IndexOutOfBoundsException();
            } else if (len == 0) {
                return 0;
            }
	    if (next >= length)
		return -1;
	    int n = Math.min(length - next, len);
	    str.getChars(next, next + n, cbuf, off);
	    next += n;
	    return n;
    }

    public void close() {
    }

}

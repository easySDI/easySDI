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

package ch.ethz.mxquery.model.ws;

import java.io.IOException;
import java.io.InputStreamReader;

public class HttpResponse extends HttpMessage {
    private String httpVersion;
    private int httpStatusCode;
    private String httpInfo;

    /**
     * Constructor
     * 
     * @param httpVersion
     *                1.0 or 1.1
     * @param httpCode
     * @param httpInfo
     * @throws IOException
     */
    public HttpResponse(String httpVersion, int httpCode, String httpInfo)
	    throws IOException {
	if (!"1.0".equals(httpVersion) && !"1.1".equals(httpVersion)) {
	    throw new IOException("The HTTP version must be '1.0' or '1.1'!");
	}
	this.httpVersion = httpVersion;
	this.httpStatusCode = httpCode;
	this.httpInfo = httpInfo;
    }

    public HttpResponse(InputStreamReader inputStreamReader) throws IOException {
	super(inputStreamReader);
    }

    /**
     * Constructor. Parses a (String-)HTTP response.
     * 
     * @param response
     *                HTTP response as a String
     * @throws IOException
     */
    public HttpResponse(String response) throws IOException {
	this.parse(response);
    }

    protected void parse(String response) throws IOException {
	String httpInfo = this.nextWord(response);
	if (httpInfo.startsWith("HTTP/")) {
	    this.httpVersion = httpInfo.substring("HTTP/".length());
	    if (!this.httpVersion.equals("1.0")
		    && !this.httpVersion.equals("1.1")) {
		throw new IOException("Non existion HTTP version delcared!");
	    }
	} else {
	    throw new IOException("HTTP version not delcared!");
	}

	try {
	    this.httpStatusCode = Integer.parseInt(this.nextWord(response));
	} catch (NumberFormatException e) {
	    throw new IOException("HTTP response code is missing!");
	}
	this.httpInfo = this.remainingLine(response);

	super.parse(response);
    }

    public String getHttpVersion() {
	return this.httpVersion;
    }

    public int getHttpStatusCode() {
	return this.httpStatusCode;
    }

    public String getHttpInfo() {
	return this.httpInfo;
    }

    public String getHttpMsg() {
	StringBuffer msg = new StringBuffer();
	msg.append("HTTP/").append(this.httpVersion).append(" ").append(
		this.httpStatusCode).append(" ").append(this.httpInfo).append(
		"\n");
	msg.append(super.getHttpMsg());
	return msg.toString();
    }

    /**
     * Returns true if the message is declared as an error message (http status
     * code).
     * 
     * @return true if the result is not OK
     */
    public boolean isErrorMsg() {
	return this.httpStatusCode >= 300;
    }
}

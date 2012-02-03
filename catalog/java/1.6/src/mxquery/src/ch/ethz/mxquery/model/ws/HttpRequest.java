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


/**
 * Represents the content of a HttpRequest
 * 
 * @author David Graf
 * 
 */
public class HttpRequest extends HttpMessage {
	private String method;
	private String path;
	private String httpVersion;

	/**
	 * Constructor
	 * 
	 * @param method
	 *            GET or POST
	 * @param path 
	 * @param httpVersion
	 *            1.0 or 1.1
	 * @throws IOException
	 */
	public HttpRequest(String method, String path, String httpVersion) throws IOException {
		if (!"POST".equals(method) && !"GET".equals(method)) {
			throw new IOException("The HTTP method must be 'GET' or 'POST'!");
		}
		this.method = method;
		this.path = path;

		if (!"1.0".equals(httpVersion) && !"1.1".equals(httpVersion)) {
			throw new IOException("The HTTP version must be '1.0' or '1.1'!");
		}
		this.httpVersion = httpVersion;
	}
	
	public HttpRequest(InputStreamReader inputStreamReader) throws IOException {
		super(inputStreamReader);
	}

	/**
	 * Constructor. Parses a (String-)HttpRequest.
	 * 
	 * @param request
	 *            HttpRequest
	 * @throws IOException
	 */
	public HttpRequest(String request) throws IOException {
		this.parse(request);
	}
	
	protected void parse(String request) throws IOException {
		this.method = this.nextWord(request);
		if (!this.method.equals("GET") && !this.method.equals("POST")) {
			throw new IOException("A HTTP request must start with GET or POST!");
		}
		String nextWord = this.nextWord(request);
		if (!nextWord.startsWith("HTTP/")) {
			this.path = nextWord;
			nextWord = this.nextWord(request);
		} else {
			this.path = "";
		}

		if (nextWord.startsWith("HTTP/")) {
			this.httpVersion = nextWord.substring("HTTP/".length());
			if (!this.httpVersion.equals("1.0")
					&& !this.httpVersion.equals("1.1")) {
				throw new IOException("Non existion HTTP version delcared!");
			}
		} else {
			throw new IOException("HTTP version not delcared!");
		}

		super.parse(request);
	}

	public String getHttpVersion() {
		return this.httpVersion;
	}

	public String getMessage() {
		return this.httpContent;
	}

	public String getMethod() {
		return this.method;
	}

	public String getPath() {
		return this.path;
	}

	public String getHttpMsg() {
		StringBuffer msg = new StringBuffer();
		msg.append(this.method).append(" ").append(this.path).append(" ")
				.append("HTTP/").append(this.httpVersion).append("\n");
		msg.append(super.getHttpMsg());
		return msg.toString();
	}
}

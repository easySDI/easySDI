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
import java.util.Enumeration;
import java.util.Hashtable;

import ch.ethz.mxquery.util.LineReader;

/**
 * Represents a HTTP message without request or response starting.
 * 
 * @author David Graf
 * 
 */
public class HttpMessage {
    /**
     * HTTP Attributes
     */
    protected Hashtable attributes = new Hashtable();

    /**
     * HTTP Content
     */
    protected String httpContent = "";

    /**
     * index to parse a HTTP Message
     */
    protected int index = 0;

    protected HttpMessage() {
    }

    protected HttpMessage(InputStreamReader inputStreamReader)
	    throws IOException {
	LineReader lineReader = new LineReader(inputStreamReader);
	StringBuffer sb = new StringBuffer();
	String str;
	while ((str = lineReader.readLine()) != null && !str.equals("")) {
	    sb.append(str).append("\n");
	}

	this.parse(sb.toString());

	int contentLength = this.getLengthAttributeValue();
	if (this.attributes.containsKey("MxqLength")) {
	    contentLength = Integer.parseInt((String) this.attributes
		    .get("MxqLength"));
	}
	sb = new StringBuffer();
	if (contentLength >= 0) {
	    while (sb.length() < contentLength) {
		sb.append((char) lineReader.read());
	    }
	    /*
	     * A Http (non Error) Response must have a content! => Readline till
	     * nothing is returned when the length is not sent
	     * 
	     */
	} else if (this instanceof HttpResponse
		&& !((HttpResponse) this).isErrorMsg()) {
	    String line;
	    while ((line = lineReader.readLine()) != null) {
		sb.append(line);
	    }
	}
	this.setHttpContent(sb.toString());
    }

    /**
     * Increments the parse index till it doesn't point on a space
     * 
     * @param httpMsg
     */
    protected void skipSpaces(String httpMsg) {
	if (this.index >= httpMsg.length()) {
	    return;
	}
	while (httpMsg.charAt(this.index) == ' ') {
	    this.index++;
	}
    }

    /**
     * Inkrements the parser index if it points on a new line character
     * 
     * @param httpMsg
     * @return true, if it skipped a line
     */
    protected boolean skipNewLine(String httpMsg) {
	this.skipSpaces(httpMsg);
	boolean first = false;
	if (this.index >= httpMsg.length()) {
	    return false;
	}
	if ((first = (httpMsg.charAt(this.index) == '\n'))
		|| httpMsg.charAt(this.index) == '\r') {
	    this.index++;
	    if (this.index < httpMsg.length()) {
		if (first) {
		    if (httpMsg.charAt(this.index) == '\r') {
			this.index++;
		    }
		} else {
		    if (httpMsg.charAt(this.index) == '\n') {
			this.index++;
		    }
		}
	    }
	    return true;
	} else {
	    return false;
	}
    }

    /**
     * Returns from the current index position the complete line (and increments
     * the index till the next character)
     * 
     * @param httpMsg
     * @return
     */
    protected String remainingLine(String httpMsg) {
	this.skipSpaces(httpMsg);
	int oldIndex = this.index;
	int msgLength = httpMsg.length();
	while (this.index < msgLength && httpMsg.charAt(this.index) != '\n'
		&& httpMsg.charAt(this.index) != '\r') {
	    this.index++;
	}
	return httpMsg.substring(oldIndex, this.index);
    }

    /**
     * Returns the next word from the current index position (and increments the
     * index till the next character)
     * 
     * @param httpMsg
     * @return
     */
    protected String nextWord(String httpMsg) {
	this.skipSpaces(httpMsg);
	int oldIndex = this.index;
	int msgLength = httpMsg.length();
	while (this.index < msgLength && httpMsg.charAt(this.index) != ' '
		&& httpMsg.charAt(this.index) != '\n'
		&& httpMsg.charAt(this.index) != '\r') {
	    this.index++;
	}
	return httpMsg.substring(oldIndex, this.index);
    }

    /**
     * Parses the HTTP Attributes and the content
     * 
     * @param httpMsg
     * @throws IOException
     */
    protected void parse(String httpMsg) throws IOException {
	while (this.skipNewLine(httpMsg)) {
	    if (this.skipNewLine(httpMsg)) {
		break;
	    }
	    String attribute = this.nextWord(httpMsg);
	    if (attribute.equals("")) {
		break;
	    }
	    if (!attribute.endsWith(":")) {
		throw new IOException("An attribute name must end with a ':'!");
	    }
	    attribute = attribute.substring(0, attribute.length() - 1);
	    String value = this.remainingLine(httpMsg);
	    this.attributes.put(attribute, value);
	}

	this.httpContent = httpMsg.substring(this.index);
    }

    /**
     * Gets the value of an HTTP attribute
     * 
     * @param name
     *                name of a HTTP attribute
     * @return null, if the attribute doesn't exist
     */
    public String getAttribute(String name) {
	if (this.attributes.containsKey(name)) {
	    return (String) this.attributes.get(name);
	} else {
	    return null;
	}
    }

    /**
     * Returns the declared content length (Http Header). If the length is not
     * declared, it returns -1.
     * 
     * @return -1 if no length is declared, the length in bytes otherwise
     */
    public int getLengthAttributeValue() {
	String strLength = this.getAttribute("Content-Length");
	int length = -1;
	if (strLength != null) {
	    try {
		length = Integer.parseInt(strLength);
	    } catch (NumberFormatException e) {
	    }
	}
	return length;
    }

    /**
     * Adds an attribute to this HTTP message.
     * 
     * @param name
     * @param value
     */
    public void addAttribute(String name, String value) {
	this.attributes.put(name, value);
    }

    /**
     * Returns the content of this message.
     * 
     * @return the http payload as string
     */
    public String getHttpContent() {
	return this.httpContent;
    }

    public void setHttpContent(String httpContent) {
	this.httpContent = httpContent;
    }

    /**
     * Gets the SOAP body of the content of this message.
     * 
     * @return the SOAP body as string
     * @throws IOException,
     *                 if the content doesn't contain SOAP
     */
    public String getSoapBody() throws IOException {
	return HttpMessage.getSoapBody(this.httpContent);
    }

    public static String getSoapBody(String soap) throws IOException {
	// Gets index
	int bodyIndex = soap.indexOf("Body>");
	if (bodyIndex < 0) {
	    throw new IOException(
		    "Not possible to parse SOAP body in the HttpMessage!");
	}
	int cur = bodyIndex;
	while (soap.charAt(cur - 1) != '<') {
	    cur--;
	}

	// Gets bull name of body element. Needed to find the End of the body,
	// because an element inside the body can also have the name Body.
	String bodyName = soap.substring(cur, bodyIndex + 4);
	String withoutBodyStart = soap.substring(bodyIndex + 5);
	int bodyEnd = withoutBodyStart.indexOf(bodyName);
	if (bodyEnd < 0) {
	    throw new IOException(
		    "Not possible to parse SOAP pody in the HttpMessage!");
	}
	String bodyContent = withoutBodyStart.substring(0, bodyEnd - 2);

	// extracting the list of namespaces in the <envlope> tag
	int envStartFirstIndex = soap.indexOf("Envelope");
	String tempString = soap.substring(envStartFirstIndex + 9);
	int envStartLastIndex = tempString.indexOf('>');
	String nsList = tempString.substring(0, envStartLastIndex);

	// injecting the namespace list to the net body of the soap response
	// message
	int closingTagIndex = bodyContent.indexOf('>');
	// checking for none-parameter functions: '/>'
	if (bodyContent.charAt(closingTagIndex - 1) == '/') {
	    closingTagIndex--;
	}
	bodyContent = bodyContent.substring(0, closingTagIndex) + " " + nsList
		+ bodyContent.substring(closingTagIndex);
	return bodyContent;
    }

    /**
     * Gets this HTTP message as a String
     * 
     * @return the full http message as string
     */
    public String getHttpMsg() {
	StringBuffer msg = new StringBuffer();
	Enumeration keyEnum = this.attributes.keys();
	while (keyEnum.hasMoreElements()) {
	    String key = (String) keyEnum.nextElement();
	    String value = (String) this.attributes.get(key);
	    msg.append(key).append(": ").append(value).append("\n");
	}
	if (!this.attributes.containsKey("Content-Length")) {
	    msg.append("Content-Length").append(": ").append(
		    this.httpContent.length()).append("\n");
	}
	msg.append("\n");
	msg.append(this.httpContent);
	return msg.toString();
    }
}

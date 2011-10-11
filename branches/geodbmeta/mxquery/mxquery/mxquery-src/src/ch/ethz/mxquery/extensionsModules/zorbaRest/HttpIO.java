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

package ch.ethz.mxquery.extensionsModules.zorbaRest;

import java.io.BufferedInputStream;
import java.io.DataOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.MXQueryBinary;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.xdm.BinaryToken;
import ch.ethz.mxquery.datamodel.xdm.NamedToken;
import ch.ethz.mxquery.datamodel.xdm.TextToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.datamodel.xdm.UntypedAtomicAttrToken;
import ch.ethz.mxquery.datamodel.xdm.XDMScope;
import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.functions.RequestTypeMulti;
import ch.ethz.mxquery.functions.fn.EncodeForURI;
import ch.ethz.mxquery.iterators.TokenIterator;
import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.TokenSequenceIterator;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.model.ws.MultiPartFormOutputStream;
import ch.ethz.mxquery.util.LineReader;
import ch.ethz.mxquery.util.StringReader;
import ch.ethz.mxquery.xdmio.XDMInputFactory;
import ch.ethz.mxquery.xdmio.XDMSerializer;

public class HttpIO extends CurrentBasedIterator implements RequestTypeMulti {
    private static final String restNS = "http://www.zorba-xquery.com/zorba/rest-functions";
    private static final QName resultName = new QName(restNS, "zorba-rest",
	    "result");
    private static final QName statusName = new QName(restNS, "zorba-rest",
	    "status_code");
    private static final QName headersName = new QName(restNS, "zorba-rest",
	    "headers");
    private static final QName headerName = new QName(restNS, "zorba-rest",
	    "header");
    private static final QName headernameName = new QName(null, null, "name");
    private static final QName payloadName = new QName(restNS, "zorba-rest",
	    "payload");

    private XDMScope curNsScope = new XDMScope();
    private int index = -1;
    private boolean inSubIterator = false;

    public static final int REQUEST_TYPE_GET = 1;
    public static final int REQUEST_TYPE_POST = 2;
    public static final int REQUEST_TYPE_PUT = 3;
    public static final int REQUEST_TYPE_HEAD = 4;
    public static final int REQUEST_TYPE_DELETE = 4;

    private int request_type = REQUEST_TYPE_POST;
    private boolean tidy = false;

    protected XDMIterator copy(Context context, XDMIterator[] subIters,
	    Vector nestedPredCtxStack) throws MXQueryException {
	HttpIO copy = new HttpIO();
	copy.setContext(context, true);
	copy.setSubIters(subIters);
	if (request_type == REQUEST_TYPE_GET)
	    copy.setRequestType("get");
	if (request_type == REQUEST_TYPE_POST)
	    copy.setRequestType("post");
	if (request_type == REQUEST_TYPE_PUT)
	    copy.setRequestType("put");
	copy.tidy = tidy;
	return copy;
    }

    private Vector getCurrentItem() throws MXQueryException {
	int prevDepth = this.depth - 1;
	Vector res = new Vector();
	Token t = getNext();
	res.add(t);

	while (this.depth - prevDepth > 0) {
	    t = getNext();
	    res.add(t);
	}

	return res;
    }

    public void setRequestType(String type) {
	if (type.equals("get"))
	    request_type = REQUEST_TYPE_GET;
	if (type.equals("getTidy")) {
	    request_type = REQUEST_TYPE_GET;
	    tidy = true;
	}
	if (type.equals("post"))
	    request_type = REQUEST_TYPE_POST;
	if (type.equals("put"))
	    request_type = REQUEST_TYPE_PUT;
    }

    XDMIterator[] resIts;

    private void init() throws MXQueryException {
	Token urlToken = subIters[0].next();
	String url;
	switch (urlToken.getEventType()) {
	case Type.END_SEQUENCE:
	    throw new DynamicException(
		    ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,
		    "Emtpy sequence not allowed for URL", loc);
	case Type.UNTYPED_ATOMIC:
	    url = urlToken.getText();
	    break;
	default:
	    if (Type.isTypeOrSubTypeOf(urlToken.getEventType(), Type.STRING,
		    Context.getDictionary()))
		url = urlToken.getText();
	    else
		throw new DynamicException(
			ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,
			"Expected String type for URL", loc);
	    break;
	}
	if (request_type != REQUEST_TYPE_GET && url.indexOf('?') >= 0) {
	    throw new DynamicException(
		    ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,
		    "URL must not contain ? and parameters, these are given by the payload",
		    loc);
	}
	String contentType = "";
	Vector sendPayloadNames = new Vector();
	Vector sendPayloadTypes = new Vector();
	Vector sendPayloadValues = new Vector();
	if (subIters.length > 1) { // payload/parameters
	    Token curPlToken = subIters[1].next();
	    current = subIters[1];
	    if (curPlToken != Token.END_SEQUENCE_TOKEN) {
		if (curPlToken.getEventType() == Type.START_TAG
			&& ((NamedToken) curPlToken).getLocal().equals(
				"payload")) {
		    curPlToken = subIters[1].next();
		    if (request_type == REQUEST_TYPE_GET) {
			while (curPlToken.getEventType() == Type.START_TAG
				&& ((NamedToken) curPlToken).getLocal().equals(
					"part")) {
			    curPlToken = subIters[1].next();
			    if (Type.isAttribute(curPlToken.getEventType())
				    && ((NamedToken) curPlToken).getLocal()
					    .equals("name")) {
				sendPayloadNames.addElement(curPlToken
					.getText());
			    } else
				throw new DynamicException(
					ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,
					"header must contain 'name' attribute",
					loc);
			    curPlToken = subIters[1].next();
			    if (Type.isTextNode(curPlToken.getEventType())) {
				sendPayloadValues.addElement(curPlToken
					.getText());
			    }
			    curPlToken = subIters[1].next();// closing
			    curPlToken = subIters[1].next(); // next
			}
		    }
		    if (request_type == REQUEST_TYPE_POST) {
			String fName = null;
			if (Type.isAttribute(curPlToken.getEventType())) {
			    if (((NamedToken) curPlToken).getLocal().equals(
				    "filename"))
				fName = curPlToken.getText();
			    if (((NamedToken) curPlToken).getLocal().equals(
				    "content-type"))
				contentType = curPlToken.getText();
			    curPlToken = subIters[1].next();
			}
			// single file upload
			if (fName != null) {
			    contentType = "application/octet-stream";
			    if (Type.isAttribute(curPlToken.getEventType())) {
				if (((NamedToken) curPlToken).getLocal()
					.equals("content-type"))
				    contentType = curPlToken.getText();

			    }
			    sendPayloadValues.add(fName);
			    sendPayloadTypes.add("FILE");
			} else {
			    // multipart/form-data + url-encoded form data
			    if (contentType.equals("multipart/form-data")
				    || contentType
					    .equals("application/x-www-form-urlencoded")) {
				while (curPlToken.getEventType() == Type.START_TAG
					&& ((NamedToken) curPlToken).getLocal()
						.equals("part")) {
				    curPlToken = subIters[1].next();
				    if (Type.isAttribute(curPlToken
					    .getEventType())
					    && ((NamedToken) curPlToken)
						    .getLocal().equals("name")) {
					sendPayloadNames.addElement(curPlToken
						.getText());
				    } else
					throw new DynamicException(
						ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,
						"header must contain 'name' attribute",
						loc);
				    curPlToken = subIters[1].next();
				    if (Type.isAttribute(curPlToken
					    .getEventType())
					    && ((NamedToken) curPlToken)
						    .getLocal().equals(
							    "filename")) {
					if (contentType
						.equals("application/x-www-form-urlencoded"))
					    throw new DynamicException(
						    ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,
						    "file uploads not possible with application/x-www-form-urlencoded",
						    loc);
					sendPayloadValues.addElement(curPlToken
						.getText());
					curPlToken = subIters[1].next();
					if (Type.isAttribute(curPlToken
						.getEventType())
						&& ((NamedToken) curPlToken)
							.getLocal().equals(
								"content-Type")) {
					    sendPayloadTypes
						    .addElement(curPlToken
							    .getText());

					} else {
					    sendPayloadTypes
						    .addElement("application/octet-stream");
					}
				    } else {
					sendPayloadTypes.addElement("unknown");
					if (Type.isTextNode(curPlToken
						.getEventType())) {
					    sendPayloadValues
						    .addElement(curPlToken
							    .getText());
					}
					if (curPlToken.getEventType() == Type.START_TAG) {
					    Vector item = getCurrentItem();
					    item.insertElementAt(curPlToken, 0);
					    XDMSerializer ser = new XDMSerializer();
					    sendPayloadValues
						    .addElement(ser
							    .eventsToXML(new TokenSequenceIterator(
								    item)));
					}
				    }
				    curPlToken = subIters[1].next();// closing
				    curPlToken = subIters[1].next(); // next
				}
			    } else {
				// "plain data" POST
				if (Type.isTextNode(curPlToken.getEventType())) {
				    sendPayloadValues.addElement(curPlToken
					    .getText());
				    sendPayloadTypes.addElement("text/plain");
				    if (contentType.equals(""))
					contentType = "text/plain";
				}
				if (curPlToken.getEventType() == Type.START_TAG) {
				    Vector item = getCurrentItem();
				    item.insertElementAt(curPlToken, 0);
				    XDMSerializer ser = new XDMSerializer();
				    sendPayloadValues
					    .addElement(ser
						    .eventsToXML(new TokenSequenceIterator(
							    item)));
				    sendPayloadTypes.addElement("text/xml");
				    if (contentType.equals(""))
					contentType = "text/xml";

				    // throw new
				    // MXQueryException(ErrorCodes.A0002_EC_NOT_SUPPORTED,"XML
				    // Content in post not yet supported",loc);
				}
			    }
			}
		    }

		} else
		    throw new DynamicException(
			    ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,
			    "payload paramter must be an element named 'payload'",
			    loc);
	    }
	    if ((request_type == REQUEST_TYPE_GET || contentType
		    .equals("application/x-www-form-urlencoded"))
		    && sendPayloadNames.size() > 0) {
		StringBuffer params = new StringBuffer();
		for (int j = 0; j < sendPayloadNames.size(); j++) {
		    if (j > 0)
			params.append("&");
		    params.append(sendPayloadNames.elementAt(j)).append("=");
		    params.append(EncodeForURI
			    .encode((String) sendPayloadValues.elementAt(j)));
		}
		if (request_type == REQUEST_TYPE_GET)
		    if (url.indexOf('?') >= 0)
			url = url + "&" + params.toString();
		    else
			url = url + "?" + params.toString();
		else {
		    sendPayloadValues.clear();
		    sendPayloadTypes.clear();
		    sendPayloadNames.clear();
		    sendPayloadValues.addElement(params.toString());
		    sendPayloadTypes.addElement("form");
		}

	    }
	}
	Vector sendHeaderNames = new Vector();
	Vector sendHeaderValues = new Vector();
	if (subIters.length > 2) { // additional headers
	    Token curPlToken = subIters[2].next();
	    if (curPlToken != Token.END_SEQUENCE_TOKEN) {
		if (curPlToken.getEventType() == Type.START_TAG
			&& ((NamedToken) curPlToken).getLocal().equals(
				"headers")) {
		    curPlToken = subIters[2].next();
		    while (curPlToken.getEventType() == Type.START_TAG
			    && ((NamedToken) curPlToken).getLocal().equals(
				    "header")) {
			curPlToken = subIters[2].next();
			if (Type.isAttribute(curPlToken.getEventType())
				&& ((NamedToken) curPlToken).getLocal().equals(
					"name")) {
			    sendHeaderNames.addElement(curPlToken.getText());
			} else
			    throw new DynamicException(
				    ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,
				    "header must contain 'name' attribute", loc);
			curPlToken = subIters[2].next();
			if (Type.isTextNode(curPlToken.getEventType())) {
			    sendHeaderValues.addElement(curPlToken.getText());
			}
			curPlToken = subIters[2].next();// closing
			curPlToken = subIters[2].next(); // next
		    }

		} else
		    throw new DynamicException(
			    ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,
			    "payload paramter must be an element named 'payload'",
			    loc);
	    }
	}

	HttpURLConnection conn = null;
	LineReader br = null;
	StringBuffer result = new StringBuffer();
	byte[] data = null;
	int respCode;
	Vector headerNames = new Vector();
	Vector headerValues = new Vector();

	try {
	    URL javaURL = new URL(url);
	    conn = (HttpURLConnection) javaURL.openConnection();
	    if (sendHeaderNames.size() > 0) {
		for (int j = 0; j < sendHeaderNames.size(); j++) {
		    conn.setRequestProperty((String) sendHeaderNames
			    .elementAt(j), (String) sendHeaderValues
			    .elementAt(j));
		}
	    }
	    conn.setDoInput(true);
	    conn.setUseCaches(false);
	    conn.setDefaultUseCaches(false);
	    if (request_type == REQUEST_TYPE_GET) {
		conn.connect();
	    }
	    if (request_type == REQUEST_TYPE_POST) {
		//
		//
		//
		conn.setDoOutput(true);
		conn.setRequestProperty("Connection", "Keep-Alive");
		conn.setRequestProperty("Cache-Control", "no-cache");
		if (contentType.equals("multipart/form-data")) {
		    // create a boundary string
		    String boundary = MultiPartFormOutputStream
			    .createBoundary();
		    conn.setRequestProperty("Accept", "*/*");
		    conn.setRequestProperty("Content-Type",
			    MultiPartFormOutputStream.getContentType(boundary));
		    // set some other request headers...
		    // no need to connect cuz getOutputStream() does it
		    MultiPartFormOutputStream out = new MultiPartFormOutputStream(
			    conn.getOutputStream(), boundary);
		    // write a text field element
		    for (int i = 0; i < sendPayloadNames.size(); i++) {

			if (sendPayloadTypes.elementAt(i).equals("unknown")) {
			    out.writeField((String) sendPayloadNames
				    .elementAt(i), (String) sendPayloadValues
				    .elementAt(i));
			} else {
			    // upload a file
			    out.writeFile((String) sendPayloadNames
				    .elementAt(i), (String) sendPayloadTypes
				    .elementAt(i), new File(
				    (String) sendPayloadValues.elementAt(i)));
			}
		    }
		    out.close();
		} else {
		    sendNonFormPayload(contentType, sendPayloadTypes,
			    sendPayloadValues, conn);

		}
	    }
	    if (request_type == REQUEST_TYPE_PUT) {
		conn.setRequestMethod("PUT");
		conn.connect();
		sendNonFormPayload(contentType, sendPayloadTypes,
			sendPayloadValues, conn);
	    }

	    respCode = conn.getResponseCode();
	    // headers is a set, but header names can show up up multiple times,
	    // count + copy manually
	    // also skip statuscode
	    for (int j = 1;; j++) {
		String headerVal = conn.getHeaderField(j);
		if (headerVal == null)
		    break;

		String hName = conn.getHeaderFieldKey(j);
		if (hName.equalsIgnoreCase("Content-Type")) {
		    contentType = headerVal.split(";")[0].trim();
		}
		if (headerName != null) {
		    headerNames.add(hName);
		    headerValues.add(headerVal);
		}
	    }
	    String str;
	    if (contentType.startsWith("text")
		    || contentType.indexOf("+xml") >= 0
		    || contentType.equals("application/xml")) {
		if (respCode == HttpURLConnection.HTTP_INTERNAL_ERROR) {
		    br = new LineReader(new InputStreamReader(conn
			    .getErrorStream()));
		} else {
		    br = new LineReader(new InputStreamReader(conn
			    .getInputStream()));
		}

		while ((str = br.readLine()) != null) {
		    result.append(str).append("\n");
		}
	    } else { // binary data
		int contentLength = conn.getContentLength();
		BufferedInputStream in = new BufferedInputStream(conn
			.getInputStream());
		data = new byte[contentLength];
		int bytesRead = 0;
		int offset = 0;
		while (offset < contentLength) {
		    bytesRead = in.read(data, offset, data.length - offset);
		    if (bytesRead == -1)
			break;
		    offset += bytesRead;
		}
		in.close();

		if (offset != contentLength) {
		    throw new IOException("Only read " + offset
			    + " bytes; Expected " + contentLength + " bytes");
		}
	    }
	} catch (MalformedURLException e) {
	    throw new DynamicException(
		    ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE,
		    "Invalid value for HTTP URL " + url, loc);
	} catch (IOException e) {
	    throw new DynamicException(ErrorCodes.A0007_EC_IO,
		    "Could not establish HTTP Connection " + e.toString(), loc);
	} finally {
	    try {
		if (conn != null) {
		    conn.disconnect();
		}
		// if (osw != null) {
		// osw.close();
		// }
		if (br != null) {
		    br.close();
		}
	    } catch (IOException e) {
	    }
	}
	// retIterator count 2 (zorba:result), 3 status code, 2 header env, 4 x
	// header entries, 2 payload env, 1 actual payload
	resIts = new XDMIterator[12 + 4 * headerNames.size()];

	resIts[0] = new TokenIterator(context, new Token(Type.START_DOCUMENT,
		null, curNsScope), null, loc);
	resIts[1] = new TokenIterator(context, new NamedToken(Type.START_TAG,
		null, resultName, curNsScope), null, loc);
	resIts[2] = new TokenIterator(context, new NamedToken(Type.START_TAG,
		null, statusName, curNsScope), null, loc);
	resIts[3] = new TokenIterator(context, new TextToken(
		Type.TEXT_NODE_UNTYPED_ATOMIC, null,
		Integer.toString(respCode), curNsScope), null, loc);
	resIts[4] = new TokenIterator(context, new NamedToken(Type.END_TAG,
		null, statusName, curNsScope), null, loc);
	resIts[5] = new TokenIterator(context, new NamedToken(Type.START_TAG,
		null, headersName, curNsScope), null, loc);
	for (int h = 0; h < headerNames.size(); h++) {
	    resIts[6 + h * 4] = new TokenIterator(context, new NamedToken(
		    Type.START_TAG, null, headerName, curNsScope), null, loc);
	    resIts[6 + h * 4 + 1] = new TokenIterator(context,
		    new UntypedAtomicAttrToken(null, (String) headerNames
			    .elementAt(h), headernameName, curNsScope), null,
		    loc);
	    resIts[6 + h * 4 + 2] = new TokenIterator(context, new TextToken(
		    Type.TEXT_NODE_UNTYPED_ATOMIC, null, (String) headerValues
			    .elementAt(h), curNsScope), null, loc);
	    resIts[6 + h * 4 + 3] = new TokenIterator(context, new NamedToken(
		    Type.END_TAG, null, headerName, curNsScope), null, loc);
	}
	resIts[resIts.length - 6] = new TokenIterator(context, new NamedToken(
		Type.END_TAG, null, headersName, curNsScope), null, loc);
	resIts[resIts.length - 5] = new TokenIterator(context, new NamedToken(
		Type.START_TAG, null, payloadName, curNsScope), null, loc);
	if (contentType.equalsIgnoreCase("text/xml")
		|| contentType.equalsIgnoreCase("application/xml")
		|| contentType.indexOf("+xml") >= 0)
	    if (tidy)
		resIts[resIts.length - 4] = XDMInputFactory.createTidyInput(
			context, new StringReader(result.toString()), loc);
	    else
		resIts[resIts.length - 4] = XDMInputFactory.createXMLInput(
			context, new StringReader(result.toString()), false,
			context.getInputValidationMode(), loc);
	else if (contentType.startsWith("text/")) {
	    resIts[resIts.length - 4] = new TokenIterator(context,
		    new TextToken(Type.TEXT_NODE_UNTYPED_ATOMIC, null, result
			    .toString(), curNsScope), null, loc);
	} else { // not identified => binary
	    resIts[resIts.length - 4] = new TokenIterator(context,
		    new BinaryToken(
			    Type.createTextNodeType(Type.BASE64_BINARY), null,
			    new MXQueryBinary(data, Type.BASE64_BINARY),
			    curNsScope), null, loc);
	}
	resIts[resIts.length - 3] = new TokenIterator(context, new NamedToken(
		Type.END_TAG, null, payloadName, curNsScope), null, loc);
	resIts[resIts.length - 2] = new TokenIterator(context, new NamedToken(
		Type.END_TAG, null, resultName, curNsScope), null, loc);
	resIts[resIts.length - 1] = new TokenIterator(context, new Token(
		Type.END_DOCUMENT, null, curNsScope), null, loc);

    }

    private void sendNonFormPayload(String contentType,
	    Vector sendPayloadTypes, Vector sendPayloadValues,
	    HttpURLConnection conn) throws IOException, FileNotFoundException {
	if (sendPayloadTypes.size() > 0) {
	    conn.setRequestProperty("Content-Type", contentType);
	    if (sendPayloadTypes.elementAt(0).equals("FILE")) {
		DataOutputStream out = null;
		InputStream is = null;
		try {
		    out = new DataOutputStream(conn.getOutputStream());
		    is = new FileInputStream(new File(
			    (String) sendPayloadValues.elementAt(0)));
		    byte[] fileData = new byte[1024];
		    int r = 0;
		    while ((r = is.read(fileData, 0, fileData.length)) != -1) {
			out.write(fileData, 0, r);
		    }
		} finally {
		    if (is != null)
			is.close();
		    if (out != null)
			out.close();
		}
	    } else {
		OutputStreamWriter out = null;
		try {
		    out = new OutputStreamWriter(conn.getOutputStream());
		    out.write((String) sendPayloadValues.elementAt(0));
		} finally {
		    if (out != null)
			out.close();
		}
	    }
	}
    }

    public Token next() throws MXQueryException {
	if (called == 0) {
	    init();
	    called++;
	}
	return seqNext(resIts);
    }

    protected Token seqNext(XDMIterator[] seq) throws MXQueryException {
	// TODO: Use stores for the generates input
	if (called == 0) {
	    current = subIters[0];
	    called++;
	}
	if (seq.length > index) {
	    Token next;

	    if (inSubIterator) {
		next = current.next(); // handleSubIterator
	    } else {
		// handle new sub iterator
		index++;
		if (index < seq.length) {
		    current = seq[index];
		    inSubIterator = true;
		    next = current.next(); // handleSubIterator
		} else {
		    // job done
		    return Token.END_SEQUENCE_TOKEN;
		}
	    }

	    if (next.getEventType() == Type.END_SEQUENCE) {
		inSubIterator = false;
		return next();

	    } else {
		return next;
	    }
	}
	return Token.END_SEQUENCE_TOKEN;
    }

    protected void resetImpl() throws MXQueryException {
	super.resetImpl();
	index = -1;
	inSubIterator = false;
    }

}

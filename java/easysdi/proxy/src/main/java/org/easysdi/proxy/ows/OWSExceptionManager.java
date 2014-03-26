package org.easysdi.proxy.ows;

import java.io.ByteArrayOutputStream;
import java.util.HashMap;
import java.util.Hashtable;

import org.easysdi.proxy.core.ProxyServlet;

public interface OWSExceptionManager {

	public boolean filterResponseAndExceptionFiles(Hashtable<String,String> serverResponses, Hashtable<String, String> serverExceptions) throws Exception;
	public ByteArrayOutputStream buildResponseForRemoteOgcException (Hashtable<String, String> ogcExceptionFilePathTable);
	public HashMap<String, String> getRemoteServerExceptionResponse (ProxyServlet servlet,HashMap<String, String> remoteServerResponseFile);
}
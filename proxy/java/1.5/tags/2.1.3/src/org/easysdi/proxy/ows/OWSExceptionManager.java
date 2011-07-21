package org.easysdi.proxy.ows;

import java.io.ByteArrayOutputStream;
import java.util.Hashtable;

public interface OWSExceptionManager {

	public boolean filterResponseAndExceptionFiles(Hashtable<String,String> serverResponses, Hashtable<String, String> serverExceptions) throws Exception;
	public ByteArrayOutputStream buildResponseForRemoteOgcException (Hashtable<String, String> ogcExceptionFilePathTable);
}
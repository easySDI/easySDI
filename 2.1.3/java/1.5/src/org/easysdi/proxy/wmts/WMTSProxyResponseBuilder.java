package org.easysdi.proxy.wmts;

import java.util.Hashtable;

import org.easysdi.proxy.core.ProxyServlet;
import org.jdom.Namespace;

import com.google.common.collect.Multimap;

public abstract class WMTSProxyResponseBuilder {

	protected ProxyServlet servlet; 
	protected Exception lastException;
	protected Namespace nsOWS ;
	protected Namespace nsWMTS;
	protected Namespace nsXLINK;
	
	public WMTSProxyResponseBuilder(ProxyServlet proxyServlet) {
		super();
		servlet = proxyServlet;
		nsOWS = Namespace.getNamespace("http://www.opengis.net/ows/1.1");
		nsXLINK = Namespace.getNamespace("http://www.w3.org/1999/xlink");
		
	}
	
	public void setLastException(Exception lastException) {
		this.lastException = lastException;
	}

	public Exception getLastException() {
		return lastException;
	}
	
	public abstract Boolean CapabilitiesOperationsFiltering (String filePath, String href );
	public abstract Boolean CapabilitiesContentsFiltering (Hashtable<String, String> filePathList);
	public abstract Boolean CapabilitiesMerging(Hashtable<String, String> filePathList);
	public abstract Boolean CapabilitiesServiceIdentificationWriting(String filePath, String href);
}

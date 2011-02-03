package org.easysdi.proxy.wmts;

import org.easysdi.proxy.core.ProxyServlet;
import org.jdom.Namespace;

import com.google.common.collect.Multimap;

public abstract class WMTSProxyResponseBuilder {

	protected ProxyServlet servlet; 
	protected Exception lastException;
	protected Namespace nsOWS ;
	protected Namespace nsWMTS;
	protected Namespace nsXLINK = Namespace.getNamespace("http://www.w3.org/1999/xlink");
	
	public WMTSProxyResponseBuilder(ProxyServlet proxyServlet) {
		super();
		servlet = proxyServlet;
	}
	
	public void setLastException(Exception lastException) {
		this.lastException = lastException;
	}

	public Exception getLastException() {
		return lastException;
	}
	
	public abstract Boolean CapabilitiesOperationFiltering (Multimap<Integer, String> filePathList, String href );
	public abstract Boolean CapabilitiesLayerFiltering (Multimap<Integer, String> filePathList);
	public abstract Boolean CapabilitiesMerging(Multimap<Integer, String> filePathList);
	public abstract Boolean CapabilitiesServiceMetadataWriting(Multimap<Integer, String> filePathList);
}

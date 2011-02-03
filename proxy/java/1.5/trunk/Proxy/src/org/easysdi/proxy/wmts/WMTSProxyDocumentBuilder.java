package org.easysdi.proxy.wmts;

import org.easysdi.proxy.core.ProxyServlet;

import com.google.common.collect.Multimap;

public abstract class WMTSProxyDocumentBuilder {

	protected ProxyServlet servlet; 
	private Exception lastException;
	
	public WMTSProxyDocumentBuilder(ProxyServlet proxyServlet) {
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
	public abstract Boolean CapabilitiesMetadataWriting(Multimap<Integer, String> filePathList);
}

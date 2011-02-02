package org.easysdi.proxy.wmts;

import java.util.Arrays;

public class WMTS100ProxyServlet extends WMTSProxyServlet{

	/**
	 * 
	 */
	private static final long serialVersionUID = 9165610435888466203L;
	
	public WMTS100ProxyServlet() {
		super();
		ServiceOperations =  Arrays.asList( "GetCapabilities", "GetTile", "GetFeatureInfo" );
		ServiceSupportedOperations = Arrays.asList("GetCapabilities", "GetTile");
		docBuilder = new WMTS100ProxyDocumentBuilder(this);
		
	}
}

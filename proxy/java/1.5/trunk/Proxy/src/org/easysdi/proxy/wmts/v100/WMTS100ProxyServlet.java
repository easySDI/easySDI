package org.easysdi.proxy.wmts.v100;

import java.util.Arrays;

import org.easysdi.proxy.ows.v200.*;
import org.easysdi.proxy.wmts.*;

public class WMTS100ProxyServlet extends WMTSProxyServlet{

	/**
	 * 
	 */
	private static final long serialVersionUID = 9165610435888466203L;
	
	public WMTS100ProxyServlet() {
		super();
		ServiceOperations =  Arrays.asList( "GetCapabilities", "GetTile", "GetFeatureInfo" );
		ServiceSupportedOperations = Arrays.asList("GetCapabilities", "GetTile");
		docBuilder = new WMTS100ProxyResponseBuilder(this);
		owsExceptionManager = new OWS200ExceptionManager();
		owsExceptionReport = new OWS200ExceptionReport();
	}
}

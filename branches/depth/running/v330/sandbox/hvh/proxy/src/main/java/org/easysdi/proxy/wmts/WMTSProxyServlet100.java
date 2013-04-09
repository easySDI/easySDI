package org.easysdi.proxy.wmts;

import java.util.Arrays;

import org.easysdi.proxy.core.ProxyServletRequest;
import org.easysdi.proxy.domain.SdiPolicy;
import org.easysdi.proxy.domain.SdiVirtualservice;
import org.easysdi.proxy.ows.v200.*;
import org.easysdi.proxy.wmts.v100.*;

public class WMTSProxyServlet100 extends WMTSProxyServlet{

	private static final long serialVersionUID = 9165610435888466203L;
	
	 public WMTSProxyServlet100(ProxyServletRequest proxyRequest, SdiVirtualservice virtualService, SdiPolicy policy) {
		super(proxyRequest, virtualService, policy);
	    ServiceOperations =  Arrays.asList( "GetCapabilities", "GetTile", "GetFeatureInfo" );
		ServiceSupportedOperations = Arrays.asList("GetCapabilities", "GetTile", "GetFeatureInfo");
		docBuilder = new WMTSProxyResponseBuilder100(this);
		owsExceptionManager = new OWS200ExceptionManager();
		owsExceptionReport = new WMTSExceptionReport100();
	}
	
	
}

package org.easysdi.proxy.wmts;

import java.util.Arrays;
import org.easysdi.proxy.ows.v200.*;
import org.easysdi.proxy.wmts.v100.*;

@SuppressWarnings("serial")
public class WMTSProxyServlet100 extends WMTSProxyServlet{
	public WMTSProxyServlet100() {
		super();
		ServiceOperations =  Arrays.asList( "GetCapabilities", "GetTile", "GetFeatureInfo" );
		ServiceSupportedOperations = Arrays.asList("GetCapabilities", "GetTile", "GetFeatureInfo");
		docBuilder = new WMTSProxyResponseBuilder100(this);
		owsExceptionManager = new OWS200ExceptionManager();
		owsExceptionReport = new WMTSExceptionReport100();
	}
	
	
}

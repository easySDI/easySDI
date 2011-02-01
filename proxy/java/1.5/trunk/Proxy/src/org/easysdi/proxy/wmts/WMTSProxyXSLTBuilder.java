package org.easysdi.proxy.wmts;

import java.io.ByteArrayOutputStream;
import java.io.File;
import java.util.List;

import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import org.easysdi.proxy.core.ProxyServlet;

public abstract class WMTSProxyXSLTBuilder {
	
	protected ProxyServlet servlet; 
	
	public WMTSProxyXSLTBuilder(ProxyServlet proxyServlet) {
		super();
		servlet = proxyServlet;
	}

	public abstract StringBuffer getCapabilitiesXSLT (HttpServletRequest req, HttpServletResponse resp, int remoteServerIndex);
	public abstract ByteArrayOutputStream mergeCapabilities(List<File> tempFileCapa, HttpServletResponse resp);
	
}

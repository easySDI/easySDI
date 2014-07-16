package org.easysdi.proxy.wmts;

import org.easysdi.proxy.core.ProxyResponseBuilder;
import org.easysdi.proxy.core.ProxyServlet;
import org.jdom.Namespace;

public abstract class WMTSProxyResponseBuilder extends ProxyResponseBuilder{

	protected Namespace nsWMTS;
	
	/**
	 * @param proxyServlet
	 */
	public WMTSProxyResponseBuilder(ProxyServlet proxyServlet) {
		super(proxyServlet);
		nsOWS = Namespace.getNamespace("http://www.opengis.net/ows/1.1");
		nsXLINK = Namespace.getNamespace("http://www.w3.org/1999/xlink");
	}
}

package org.easysdi.proxy.reporting;

import java.net.URLConnection;

import org.apache.fop.apps.FOURIResolver;
import org.springframework.security.core.Authentication;
import org.springframework.security.crypto.codec.Base64;

public class EasySDIFOURIResolver extends FOURIResolver {

	private Authentication token;

	public EasySDIFOURIResolver(Authentication token) {
		this.token = token;
	}

	@Override
	protected void updateURLConnection(URLConnection connection, String href) {
		connection.setRequestProperty("Authorization", null);
		String authHeader = "Basic " + new String(Base64.encode((token.getPrincipal().toString() + ":" + token.getCredentials().toString()).getBytes()));
		if (token != null) {
			connection.setRequestProperty("Authorization", authHeader);
		}
	}
}
package org.easysdi.proxy.reporting;

import java.net.URLConnection;

import org.apache.fop.apps.FOURIResolver;
import org.springframework.security.authentication.UsernamePasswordAuthenticationToken;
import org.springframework.security.core.codec.Base64;

public class EasySDIFOURIResolver extends FOURIResolver {

	private UsernamePasswordAuthenticationToken token;

	public EasySDIFOURIResolver(UsernamePasswordAuthenticationToken token) {
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
package org.easysdi.proxy.csw;

import java.net.URLDecoder;

public class GetRequestHandler 
{
	private String _request ;
	private String _server ;
	private String _fragment;
	private String _parameters;
	
	/**
	 * @return the _fragment
	 */
	public String getParameters() {
		return _parameters;
	}
	
	/**
	 * @return the _fragment
	 */
	public String getFragment() {
		return _fragment;
	}

	/**
	 * @return the _request
	 */
	public String getRequest() {
		return _request;
	}
	
	/**
	 * @return the _server
	 */
	public String getServer() {
		return _server;
	}

	public GetRequestHandler (String request)
	{
		_request = request;
		_server = _request.substring(0, _request.indexOf("?"));
		_parameters = _request.substring(_request.indexOf("?")+1);
		_fragment = URLDecoder.decode(_request.substring(_request.indexOf("fragment")+ 9));
	}



}

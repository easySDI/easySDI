/**
 * 
 */
package org.easysdi.proxy.wms;

import java.io.UnsupportedEncodingException;
import java.lang.reflect.InvocationTargetException;
import java.lang.reflect.Method;
import java.net.URLEncoder;
import java.util.Enumeration;
import javax.servlet.http.HttpServletRequest;
import org.easysdi.proxy.core.ProxyServletRequest;
import org.easysdi.proxy.exception.ProxyServletException;
import org.easysdi.proxy.exception.VersionNotSupportedException;


/**
 * @author DEPTH SA
 *
 */
public class WMSProxyServletRequest extends ProxyServletRequest {

	/**
	 * 
	 */
	private String bbox;
	
	/**
	 * 
	 */
	private String srsName;
	
	/**
	 * 
	 */
	private String layer;
	
	/**
	 * 
	 */
	private String queryLayers;
	
	/**
	 * 
	 */
	private String layers;
	
	/**
	 * 
	 */
	private String styles;
	
	/**
	 * 
	 */
	private String width;
	
	/**
	 * 
	 */
	private String height;
	
	/**
	 * 
	 */
	private String format;
	
	/**
	 * @return the bbox
	 */
	public String getBbox() {
		return bbox;
	}


	/**
	 * @return the srsName
	 */
	public String getSrsName() {
		return srsName;
	}


	/**
	 * @return the layer
	 */
	public String getLayer() {
		return layer;
	}


	/**
	 * @return the queryLayers
	 */
	public String getQueryLayers() {
		return queryLayers;
	}


	/**
	 * @return the layers
	 */
	public String getLayers() {
		return layers;
	}


	/**
	 * @return the styles
	 */
	public String getStyles() {
		return styles;
	}


	/**
	 * @return the width
	 */
	public String getWidth() {
		return width;
	}


	/**
	 * @return the height
	 */
	public String getHeight() {
		return height;
	}


	/**
	 * @return the format
	 */
	public String getFormat() {
		return format;
	}


	/**
	 * @param request
	 */
	public WMSProxyServletRequest(HttpServletRequest req) {
		super(req);
	}

	
//	public void parseRequestPOST () {
//		
//	}
	
	public void parseRequestGET () throws ProxyServletException{
		Enumeration<String> parameterNames = request.getParameterNames();
		
		while (parameterNames.hasMoreElements()) {
			String key = (String) parameterNames.nextElement();
			
			String value = "";
			if (	   key.equalsIgnoreCase("LAYER") 
					|| key.equalsIgnoreCase("QUERY_LAYERS") 
					|| key.equalsIgnoreCase("LAYERS") 
					|| key.equalsIgnoreCase("STYLES")
					|| key.equalsIgnoreCase("BBOX") 
					|| key.equalsIgnoreCase("SRS") 
					|| key.equalsIgnoreCase("CRS")) {
				value = request.getParameter(key);
			} else {
				try {
					value = URLEncoder.encode(request.getParameter(key),"UTF-8");
				} catch (UnsupportedEncodingException e) {
					throw new ProxyServletException(e);
				}
			}

			if (!key.equalsIgnoreCase("QUERY_LAYERS") && !key.equalsIgnoreCase("LAYERS") && !key.equalsIgnoreCase("STYLES")) {
				urlParameters = urlParameters + key + "=" + value + "&";
			}

			if (key.equalsIgnoreCase("requestuest")) {
				// Gets the requested Operation
				if (value.equalsIgnoreCase("capabilities")) {
					operation = "GetCapabilities";
				} else {
					operation = value;
				}
			} else if (key.equalsIgnoreCase("version")) {
				// Gets the requested version
				requestedVersion = value;
				version = value;
				if (version.equalsIgnoreCase("1.0.0")) {
					throw new VersionNotSupportedException(value);
				}
			} else if (key.equalsIgnoreCase("wmtver")) {
				// Gets the requested wmtver
				version = value;
				service = "WMS";
			} else if (key.equalsIgnoreCase("service")) {
				// Gets the requested service
				service = value;
			} else if (key.equalsIgnoreCase("BBOX")) {
				// Gets the requested bbox
				bbox = value;
			} else if (key.equalsIgnoreCase("SRS")) {
				// Gets the requested srs
				srsName = value;
			} else if (key.equalsIgnoreCase("CRS")) // Version 1.3.0
			{
				// Gets the requested srs
				srsName = value;
			} else if (key.equalsIgnoreCase("LAYER")) {
				// Gets the requested layer -> GetLegendGraphic only
				layer = value;
			}
			// Debug tb 18.01.2010
			else if (key.equalsIgnoreCase("QUERY_LAYERS")) {
				// Gets the requested querylayers -> GetFeatureInfo
				queryLayers = value;
			}
			// Fin de Debug
			else if (key.equalsIgnoreCase("LAYERS")) {
				// Gets the requested layers -> GetMap
				layers = value;
			} else if (key.equalsIgnoreCase("STYLES")) {
				styles = value;
			} else if (key.equalsIgnoreCase("WIDTH")) {
				width = value;
			} else if (key.equalsIgnoreCase("HEIGHT")) {
				height = value;
			} else if (key.equalsIgnoreCase("FORMAT")) {
				format = value;
			}
		}			
	}
}

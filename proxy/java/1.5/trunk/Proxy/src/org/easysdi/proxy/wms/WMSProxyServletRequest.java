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
import org.geotools.referencing.CRS;
import org.opengis.referencing.FactoryException;
import org.opengis.referencing.NoSuchAuthorityCodeException;
import org.opengis.referencing.crs.CoordinateReferenceSystem;


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
	private Double x1;
	
	/**
	 * 
	 */
	private Double x2;
	
	/**
	 * 
	 */
	private Double y1;
	
	/**
	 * 
	 */
	private Double y2;
	
	/**
	 * 
	 */
	private String srsName;
	
	/**
	 * 
	 */
	private CoordinateReferenceSystem crs; 
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
	 * 
	 */
	private String info_format;
	
	/**
	 * @return the bbox
	 */
	public String getBbox() {
		return bbox;
	}

	/**
	 * @return x1 of bbox
	 */
	public double getX1(){
		if(x1 == null ){
			String[] s = bbox.split(",");
			x1 = Double.parseDouble(s[0]);
		}
		return x1;
	}
	/**
	 * @return x2 of bbox
	 */
	public double getX2(){
		if(x2 == null ){
			String[] s = bbox.split(",");
			x2 = Double.parseDouble(s[2]);
		}
		return x2;
	}
	
	/**
	 * @return y1 of bbox
	 */
	public double getY1(){
		if(y1 == null ){
			String[] s = bbox.split(",");
			y1 = Double.parseDouble(s[1]);
		}
		return y1;
	}
	
	/**
	 * @return y2 of bbox
	 */
	public double getY2(){
		if(y2 == null ){
			String[] s = bbox.split(",");
			y2 = Double.parseDouble(s[3]);
		}
		return y2;
	}
	
	/**
	 * @return the srsName
	 */
	public String getSrsName() {
		return srsName;
	}
	
	/**
	 * @return the coordinateReferenceSystem get from the srsName given in the request
	 */
	public CoordinateReferenceSystem getCoordinateReferenceSystem () throws FactoryException{
		if(crs == null)
			crs = CRS.decode(getSrsName());
		return crs;
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
	 * @param info_format the info_format to set
	 */
	public void setInfo_format(String info_format) {
		this.info_format = info_format;
	}

	/**
	 * @return the info_format
	 */
	public String getInfo_format() {
		return info_format;
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
	
	/**
	 * @throws ProxyServletException
	 */
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

			if (key.equalsIgnoreCase("request")) {
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
			} else if (key.equalsIgnoreCase("QUERY_LAYERS")) {
				// Gets the requested querylayers -> GetFeatureInfo
				queryLayers = value;
			} else if (key.equalsIgnoreCase("LAYERS")) {
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
			}else if (key.equalsIgnoreCase("INFO_FORMAT")) {
				info_format = value;
			}
		}			
	}
}

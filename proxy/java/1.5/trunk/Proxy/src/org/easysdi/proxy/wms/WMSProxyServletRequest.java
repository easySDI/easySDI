/**
 * 
 */
package org.easysdi.proxy.wms;

import java.io.BufferedReader;
import java.io.ByteArrayInputStream;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.UnsupportedEncodingException;
import java.lang.reflect.InvocationTargetException;
import java.lang.reflect.Method;
import java.net.URLEncoder;
import java.util.Enumeration;
import javax.servlet.http.HttpServletRequest;
import org.easysdi.proxy.core.ProxyServletRequest;
import org.easysdi.proxy.exception.ProxyServletException;
import org.easysdi.proxy.exception.VersionNotSupportedException;
import org.easysdi.xml.handler.CswRequestHandler;
import org.easysdi.xml.handler.WMSRequestHandler;
import org.geotools.referencing.CRS;
import org.opengis.referencing.FactoryException;
import org.opengis.referencing.NoSuchAuthorityCodeException;
import org.opengis.referencing.crs.CoordinateReferenceSystem;
import org.xml.sax.InputSource;
import org.xml.sax.SAXException;
import org.xml.sax.XMLReader;
import org.xml.sax.helpers.XMLReaderFactory;


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
	 * In WMS version 1.0.0, the name of the parameter VERSION was WMTVER.
	 * This name is now deprecated but for backwards compatibility and version 
	 * negociation a post 1.0.0 server shall accept either form without issuing an exception.
	 * This backward compatibility is not defined in WMS 1.3.0, so WMTVER parameter is handle 
	 * only for WMS 1.1.0 and 1.1.1 
	 */
	private String wmtver;
	
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
	 * @param format the format to set
	 */
	public void setFormat(String format) {
		this.format = format;
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
	 * @param wmtver the wmtver to set
	 */
	public void setWmtver(String wmtver) {
		this.wmtver = wmtver;
	}

	/**
	 * @return the wmtver
	 */
	public String getWmtver() {
		return wmtver;
	}

	/**
	 * @param request
	 * @throws Throwable 
	 */
	public WMSProxyServletRequest(HttpServletRequest req) throws Throwable {
		super(req);
	}

	
	public void parseRequestPOST () {
		XMLReader xr;
		try {
			xr = XMLReaderFactory.createXMLReader();
			WMSRequestHandler rh = new WMSRequestHandler();
			xr.setContentHandler(rh);

			StringBuffer param = new StringBuffer();
			String input;
			BufferedReader in = new BufferedReader(new InputStreamReader(request.getInputStream()));
			while ((input = in.readLine()) != null) {
				param.append(input);
			}

			xr.parse(new InputSource(new InputStreamReader(new ByteArrayInputStream(param.toString().getBytes()))));
			
			version = rh.getVersion();
			if (version.equalsIgnoreCase("1.0.0")) {
				throw new VersionNotSupportedException(version);
			}
			requestedVersion = version;
			width = rh.getWidth();
			height = rh.getHeight();
			format = rh.getFormat();
			srsName = rh.getCRS();
			bbox = rh.getLowerCorner().replace(" ", ",")+rh.getUpperCorner().replace(" ", ",");
			String sb = new String();
			for (String s : rh.getLayers())
			{
			    sb+=s+",";
			}
			layers = sb.substring(0, sb.length()-1);
		} catch (SAXException e) {
			throw new ProxyServletException(e.getMessage());
		} catch (IOException e) {
			throw new ProxyServletException(e.getMessage());
		}
		
	}
	
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
					throw new ProxyServletException(e.toString());
				}
			}

			if (!key.equalsIgnoreCase("FORMAT") && !key.equalsIgnoreCase("QUERY_LAYERS") && !key.equalsIgnoreCase("LAYERS") && !key.equalsIgnoreCase("STYLES")) {
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
				wmtver = value;
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
		
		//If VERSION and WMTVER are both given, VERSION takes precedence
		//If only WMTVER is given, it will be used as VERSION
		if(version == null && wmtver != null){
			version = wmtver;
		}
		
		//If FORMAT is provided for a GetCapabilities (1.3.0 only)
		//Overwrite it to have text/xml, the only format supported to rewrite Getcapabilities response
		if(operation.equalsIgnoreCase("GetCapabilities") && format != null && !format.equalsIgnoreCase("text/xml")){
			format = "text/xml";
		}
		urlParameters = urlParameters + "FORMAT=" + format + "&";
	}
}

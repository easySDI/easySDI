/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) EasySDI Community
 * For more information : www.easysdi.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or 
 * any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/gpl.html. 
 */
package org.easysdi.proxy.wmts;

import java.io.UnsupportedEncodingException;
import java.net.URLEncoder;
import java.util.Enumeration;

import javax.servlet.http.HttpServletRequest;

import org.easysdi.proxy.core.ProxyLayer;
import org.easysdi.proxy.core.ProxyServletRequest;
import org.easysdi.proxy.exception.ProxyServletException;
import org.easysdi.proxy.exception.VersionNotSupportedException;

/**
 * @author DEPTH SA
 *
 */
public class WMTSProxyServletRequest extends ProxyServletRequest {

    /**
     * 
     */
    private String acceptVersions;
    
    /**
     * 
     */
    private String sections;
    
    /**
     * 
     */
    private String updateSequence;
    
    /**
     * 
     */
    private String acceptFormats;
    
    /**
     * 
     */
    private String layer;
    
    /**
     * 
     */
    private String style;
    
    /**
     * 
     */
    private String format;
    
    /**
     * 
     */
    private String infoFormat;
    
    /**
     * 
     */
    private String tileMatrixSet;
    
    /**
     * 
     */
    private String tileMatrix;
    
    /**
     * 
     */
    private String tileRow;
    
    /**
     * 
     */
    private String tileCol;
    
    /**
     * 
     */
    private String i;
    
    /**
     * 
     */
    private String j;
	
	/**
	 * @param tileCol the tileCol to set
	 */
	public void setTileCol(String tileCol) {
		this.tileCol = tileCol;
	}

	/**
	 * @return the tileCol
	 */
	public String getTileCol() {
		return tileCol;
	}

	/**
	 * @param tileRow the tileRow to set
	 */
	public void setTileRow(String tileRow) {
		this.tileRow = tileRow;
	}

	/**
	 * @return the tileRow
	 */
	public String getTileRow() {
		return tileRow;
	}

	/**
	 * @param tileMatrix the tileMatrix to set
	 */
	public void setTileMatrix(String tileMatrix) {
		this.tileMatrix = tileMatrix;
	}

	/**
	 * @return the tileMatrix
	 */
	public String getTileMatrix() {
		return tileMatrix;
	}

	/**
	 * @param tileMatrixSet the tileMatrixSet to set
	 */
	public void setTileMatrixSet(String tileMatrixSet) {
		this.tileMatrixSet = tileMatrixSet;
	}

	/**
	 * @return the tileMatrixSet
	 */
	public String getTileMatrixSet() {
		return tileMatrixSet;
	}

	/**
	 * @param format the format to set
	 */
	public void setFormat(String format) {
		this.format = format;
	}

	/**
	 * @return the format
	 */
	public String getFormat() {
		return format;
	}

	/**
	 * @param style the style to set
	 */
	public void setStyle(String style) {
		this.style = style;
	}

	/**
	 * @return the style
	 */
	public String getStyle() {
		return style;
	}

	/**
	 * @param layer the layer to set
	 */
	public void setLayer(String layer) {
		this.layer = layer;
	}

	/**
	 * @return the layer
	 */
	public String getLayer() {
		return layer;
	}

	/**
	 * @param acceptFormats the acceptFormats to set
	 */
	public void setAcceptFormats(String acceptFormats) {
		this.acceptFormats = acceptFormats;
	}

	/**
	 * @return the acceptFormats
	 */
	public String getAcceptFormats() {
		return acceptFormats;
	}

	/**
	 * @param updateSequence the updateSequence to set
	 */
	public void setUpdateSequence(String updateSequence) {
		this.updateSequence = updateSequence;
	}

	/**
	 * @return the updateSequence
	 */
	public String getUpdateSequence() {
		return updateSequence;
	}

	/**
	 * @param sections the sections to set
	 */
	public void setSections(String sections) {
		this.sections = sections;
	}

	/**
	 * @return the sections
	 */
	public String getSections() {
		return sections;
	}

	/**
	 * @param acceptVersions the acceptVersions to set
	 */
	public void setAcceptVersions(String acceptVersions) {
		this.acceptVersions = acceptVersions;
	}

	/**
	 * @return the acceptVersions
	 */
	public String getAcceptVersions() {
		return acceptVersions;
	}

	/**
	 * @param infoFormat the infoFormat to set
	 */
	public void setInfoFormat(String infoFormat) {
	    this.infoFormat = infoFormat;
	}

	/**
	 * @return the infoFormat
	 */
	public String getInfoFormat() {
	    return infoFormat;
	}

	/**
	 * @param i the i to set
	 */
	public void setI(String i) {
	    this.i = i;
	}

	/**
	 * @return the i
	 */
	public String getI() {
	    return i;
	}

	/**
	 * @param j the j to set
	 */
	public void setJ(String j) {
	    this.j = j;
	}

	/**
	 * @return the j
	 */
	public String getJ() {
	    return j;
	}

	/**
	 * @param req
	 * @throws Throwable
	 */
	public WMTSProxyServletRequest(HttpServletRequest req) throws Throwable {
		super(req);
	}

	public void parseRequestPOST () {
	
	}
	
	@SuppressWarnings("unchecked")
	public void parseRequestGET () throws ProxyServletException{
	    Enumeration<String> parameterNames = request.getParameterNames();
	    
	    while (parameterNames.hasMoreElements()) {
		String key = (String) parameterNames.nextElement();
		String value = null;
			
		if (	   key.equalsIgnoreCase("LAYER") 
			|| key.equalsIgnoreCase("STYLE")
			|| key.equalsIgnoreCase("TILEMATRIX") 
			|| key.equalsIgnoreCase("TILEMATRIXSET") 
			|| key.equalsIgnoreCase("TILEROW")
			|| key.equalsIgnoreCase("TILECOL")) {
		    value = request.getParameter(key);
		} else {
		    try {
        		value = URLEncoder.encode(request.getParameter(key),"UTF-8");
		    } catch (UnsupportedEncodingException e) {
        		throw new ProxyServletException(e.toString());
		    }
		}
			
			if (key.equalsIgnoreCase("acceptVersions")){
				value = "1.0.0";
				urlParameters = urlParameters + key + "=" + value + "&";
			}
			else if (key.equalsIgnoreCase("Layer")){
				urlParameters = urlParameters + key + "=" +  new ProxyLayer(value).getPrefixedName() + "&" ;
			}
			else{
				urlParameters = urlParameters + key + "=" + value + "&";
			}
			
			if (key.equalsIgnoreCase("service") )
			{
				service = request.getParameter(key);
//				if(!service.equalsIgnoreCase("WMTS"))
//				{
//					logger.info( "Service requested is not WMTS.");
//					StringBuffer out = owsExceptionReport.generateExceptionReport(OWSExceptionReport.TEXT_INVALID_SERVICE_NAME,OWSExceptionReport.CODE_INVALID_PARAMETER_VALUE,"service");
//					sendHttpServletResponse(req, resp,out,"text/xml; charset=utf-8", HttpServletResponse.SC_BAD_REQUEST);
//					return;
//				}
			}
			else if (key.equalsIgnoreCase("request"))
			{
				operation = request.getParameter(key);
			}
			else if (key.equalsIgnoreCase("acceptVersions"))
			{
				setAcceptVersions(request.getParameter(key));
				if(getAcceptVersions().contains("1.0.0"))
				{
					setAcceptVersions("1.0.0");
					requestedVersion = "1.0.0";
				}
				else
				{
					throw new VersionNotSupportedException(getAcceptVersions());
				}
			}
			else if (key.equalsIgnoreCase("version"))
			{
				version = request.getParameter(key);
				if(!version.equalsIgnoreCase("1.0.0"))
				{
					throw new VersionNotSupportedException(version);
				}
			}
			else if (key.equalsIgnoreCase("sections"))
			{
				sections = request.getParameter(key);
			}
			else if (key.equalsIgnoreCase("updateSequence"))
			{
				updateSequence = request.getParameter(key);
			}
			else if (key.equalsIgnoreCase("acceptFormats"))
			{
				acceptFormats = request.getParameter(key);
			}
			else if (key.equalsIgnoreCase("Layer"))
			{
				layer = request.getParameter(key);
			}
			else if (key.equalsIgnoreCase("Style"))
			{
				style = request.getParameter(key);
			}
			else if (key.equalsIgnoreCase("Format"))
			{
				format = request.getParameter(key);
			}
			else if (key.equalsIgnoreCase("TileMatrixSet"))
			{
				tileMatrixSet = request.getParameter(key);
			}
			else if (key.equalsIgnoreCase("TileMatrix"))
			{
				tileMatrix = request.getParameter(key);
			}
			else if (key.equalsIgnoreCase("TileRow"))
			{
				tileRow = request.getParameter(key);
			}
			else if (key.equalsIgnoreCase("TileCol"))
			{
				tileCol = request.getParameter(key);
			}
			else if (key.equalsIgnoreCase("I"))
			{
				i = request.getParameter(key);
			}
			else if (key.equalsIgnoreCase("J"))
			{
				j = request.getParameter(key);
			}
			else if (key.equalsIgnoreCase("INFOFORMAT"))
			{
				infoFormat = request.getParameter(key);
			}
		}
		
	}
}

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
package org.easysdi.proxy.wms;

import java.io.IOException;

import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import org.easysdi.proxy.core.ProxyServletRequest;
import org.easysdi.proxy.domain.SdiPolicy;
import org.easysdi.proxy.domain.SdiVirtualservice;
import org.easysdi.proxy.ows.OWSExceptionReport;
import org.easysdi.proxy.wms.v130.WMSExceptionReport130;
import org.easysdi.proxy.wms.v130.WMSProxyResponseBuilder130;
import org.springframework.context.ApplicationContext;

/**
 * @author DEPTH SA
 *
 */
public class WMSProxyServlet130 extends WMSProxyServlet {

    /**
     * 
     */
    private static final long serialVersionUID = -675490104090297877L;

    /**
     * 
     */
    public WMSProxyServlet130(ProxyServletRequest proxyRequest, SdiVirtualservice virtualService, SdiPolicy policy, ApplicationContext context) {
		super(proxyRequest, virtualService, policy, context);
		docBuilder = new WMSProxyResponseBuilder130(this);
		owsExceptionReport = new WMSExceptionReport130 ();
    }

    /* (non-Javadoc)
     * @see org.easysdi.proxy.wms.WMSProxyServlet#transformGetCapabilities(javax.servlet.http.HttpServletRequest, javax.servlet.http.HttpServletResponse)
     */
    @Override
    public void transformGetCapabilities(HttpServletRequest req,HttpServletResponse resp) {
	super.transformGetCapabilities(req, resp);
    }

    /* (non-Javadoc)
     * @see org.easysdi.proxy.wms.WMSProxyServlet#requestPreTreatmentGetCapabilities(javax.servlet.http.HttpServletRequest, javax.servlet.http.HttpServletResponse)
     */
    @Override
    public void requestPreTreatmentGetCapabilities(HttpServletRequest req, HttpServletResponse resp) {
	super.requestPreTreatmentGetCapabilities(req, resp);
    }

    /* (non-Javadoc)
     * @see org.easysdi.proxy.wms.WMSProxyServlet#requestPreTreatmentGetMap(javax.servlet.http.HttpServletRequest, javax.servlet.http.HttpServletResponse)
     */
    @Override
    public void requestPreTreatmentGetMap(HttpServletRequest req,
	    HttpServletResponse resp) {
	super.requestPreTreatmentGetMap(req, resp);
    }

    /* (non-Javadoc)
     * @see org.easysdi.proxy.wms.WMSProxyServlet#requestPreTreatmentGetLegendGraphic(javax.servlet.http.HttpServletRequest, javax.servlet.http.HttpServletResponse)
     */
    @Override
    public void requestPreTreatmentGetLegendGraphic(HttpServletRequest req,
	    HttpServletResponse resp) {
	super.requestPreTreatmentGetLegendGraphic(req, resp);
    }

    /* (non-Javadoc)
     * @see org.easysdi.proxy.wms.WMSProxyServlet#requestPreTreatmentGetFeatureInfo(javax.servlet.http.HttpServletRequest, javax.servlet.http.HttpServletResponse)
     */
    @Override
    public void requestPreTreatmentGetFeatureInfo(HttpServletRequest req, HttpServletResponse resp) {
		try {
		    if(((WMSProxyServletRequest)getProxyRequest()).getInfo_format() == null || ((WMSProxyServletRequest)getProxyRequest()).getInfo_format().equalsIgnoreCase(""))
		    {
				logger.info( "QUERY_LAYERS "+OWSExceptionReport.TEXT_MISSING_PARAMETER_VALUE);
				owsExceptionReport.sendExceptionReport(request, response,"INFO_FORMAT "+OWSExceptionReport.TEXT_MISSING_PARAMETER_VALUE,OWSExceptionReport.CODE_MISSING_PARAMETER_VALUE, "INFO_FORMAT", HttpServletResponse.SC_BAD_REQUEST);
				return;
		    }
		} catch (IOException e) {
		    resp.setHeader("easysdi-proxy-error-occured", "true");
		    e.printStackTrace();
		    logger.error( e.getMessage());
		    try {
		    	owsExceptionReport.sendExceptionReport(request, response,OWSExceptionReport.TEXT_ERROR_IN_EASYSDI_PROXY,OWSExceptionReport.CODE_NO_APPLICABLE_CODE, "", HttpServletResponse.SC_OK);
			} catch (IOException e1) {
				logger.error( e1.getMessage());
				e1.printStackTrace();
		    }
		    return;
		}
		super.requestPreTreatmentGetFeatureInfo(req, resp);
    }

}

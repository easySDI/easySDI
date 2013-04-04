/**
 * 
 */
package org.easysdi.proxy.wfs;

import java.io.ByteArrayOutputStream;
import java.io.IOException;

import javax.servlet.http.HttpServletResponse;

import org.easysdi.proxy.ows.OWSExceptionReport;

/**
 * @author Helene
 *
 */
public class WFSExceptionReport extends OWSExceptionReport {

	/* (non-Javadoc)
	 * @see org.easysdi.proxy.ows.OWSExceptionReport#generateExceptionReport(java.lang.String, java.lang.String, java.lang.String)
	 */
	public StringBuffer generateExceptionReport(String errorMessage,String code, String locator) throws IOException {
		return generateExceptionReport (errorMessage, code, locator, "1.0.0");
	}

	/* (non-Javadoc)
	 * @see org.easysdi.proxy.ows.OWSExceptionReport#generateExceptionReport(java.lang.String, java.lang.String, java.lang.String, java.lang.String)
	 */
	public StringBuffer generateExceptionReport(String errorMessage,String code, String locator, String version) throws IOException {
		StringBuffer sb = new StringBuffer("<?xml version='1.0' encoding='utf-8'?>\n");
		sb.append("<ServiceExceptionReport xmlns=\"http://www.opengis.net/ogc\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.opengis.net/ogc ../wfs/1.0.0/OGC-exception.xsd\" version=\"1.2.0\">\n");
		sb.append("\t<ServiceException ");
		if(code != null && code != "")
		{
			sb.append(" code=\"");
			sb.append(code);
			sb.append("\"");
		}
		if(locator != null && locator != "")
		{
			sb.append(" locator=\"");
			sb.append(locator);
			sb.append("\"");
		}
		sb.append(">");
		sb.append(errorMessage);
		sb.append("</ServiceException>\n");
		sb.append("</ServiceExceptionReport>");
		
		return sb;
	}

	@Override
	public void sendExceptionReport(HttpServletResponse response,
			String errorMessage, String code, String locator)
			throws IOException {
		// TODO Auto-generated method stub
		
	}

}

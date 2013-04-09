/**
 * 
 */
package org.easysdi.proxy.wfs;

import java.io.IOException;
import java.io.OutputStream;

import javax.servlet.http.HttpServletResponse;

import org.easysdi.proxy.ows.OWSExceptionReport;

/**
 * @author DEPTH SA
 *
 */
public class WFSExceptionReport extends OWSExceptionReport {

	public static void sendExceptionReport(HttpServletResponse response, String errorMessage, String code, String locator) throws IOException {
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
		
		response.setContentType("text/xml; charset=utf-8");
		response.setContentLength(sb.length());
		OutputStream os;
		os = response.getOutputStream();
		os.write(sb.toString().getBytes());
		os.flush();
		os.close();
		
	}

}

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
package org.easysdi.proxy.ows;

import java.io.BufferedOutputStream;
import java.io.IOException;
import java.lang.reflect.Field;
import java.text.SimpleDateFormat;
import java.util.Date;

import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import org.apache.log4j.Logger;

/**
 * @author DEPTH SA
 *
 */
public abstract class OWSExceptionReport implements OWSIExceptionReport {

   public abstract void sendExceptionReport(HttpServletRequest request, HttpServletResponse response, String errorMessage, String code, String locator, int responseCode) throws IOException ;
   
   public void sendHttpServletResponse (HttpServletRequest req, HttpServletResponse resp, StringBuffer tempOut, String responseContentType, Integer responseCode)
   {
	   Logger logger = Logger.getLogger("ProxyLogger");
		try
		{
			BufferedOutputStream os = new BufferedOutputStream(resp.getOutputStream());
		    resp.setContentType(responseContentType);
		    resp.setStatus(responseCode);
		    if (tempOut != null)
		    	resp.setContentLength(tempOut.length());
		    else
		    	resp.setContentLength(0);
	
		    try {
			    logger.trace("transform begin response writting");
				if (req!= null && "1".equals(req.getParameter("download"))) {
				    String format = req.getParameter("format");
				    if (format == null)
				    	format = req.getParameter("FORMAT");
				    if (format != null) {
				    	String parts[] = format.split("/");
						String ext = "";
						if (parts.length > 1)
						    ext = parts[1];
						resp.setHeader("Content-Disposition", "attachment; filename=download." + ext);
				    }
				}
				if (tempOut != null)
				    os.write(tempOut.toString().getBytes());
				logger.trace("transform end response writting");
			} 
		    finally {
				os.flush();
				os.close();
				Date d = new Date();
				logger.info("ClientResponseDateTime="+ new SimpleDateFormat("dd/MM/yyyy HH:mm:ss").format(d));
				if (tempOut != null)
				    logger.info("ClientResponseLength="+ tempOut.length());
		    }
		} 
		catch (Exception e) 
		{
		    resp.setHeader("easysdi-proxy-error-occured", "true");
		    logger.error(e.getMessage());
		}
   }

		
    /* (non-Javadoc)
     * @see org.easysdi.proxy.ows.OWSIExceptionReport#getHttpCodeDescription(java.lang.String)
     */
    @SuppressWarnings("rawtypes")
    public String getHttpCodeDescription(String code) 
    {
		try {
		    String text = "HTTP_CODE_"+code;
		    Class clas = this.getClass();
		    Field f = clas.getField(text);
		    if(f == null)
			return null;
		    Object o = f.get(this);
		    if(o !=  null){
			return o.toString();
		    }
		    return null;
		} catch (SecurityException e) {
		    e.printStackTrace();
		} catch (NoSuchFieldException e) {
		    e.printStackTrace();
		} catch (IllegalArgumentException e) {
		    e.printStackTrace();
		} catch (IllegalAccessException e) {
		    e.printStackTrace();
		}
		return null;
    }



	
}

/**
 * 
 */
package org.easysdi.proxy.ows.v200;

import java.io.ByteArrayOutputStream;
import java.io.IOException;

import org.easysdi.proxy.ows.OWSExceptionReport;


/**
 * @author DEPTH SA
 *
 */
public class OWS200ExceptionReport implements OWSExceptionReport {

	/* 
	 * @see org.easysdi.proxy.ows.OWSExceptionReport#generateExceptionReport(java.lang.String, java.lang.String, java.lang.String)
	 */
	public ByteArrayOutputStream generateExceptionReport(String errorMessage,
			String code, String locator) throws IOException {
		ByteArrayOutputStream out = new ByteArrayOutputStream();
		String s = new String ();
		s = "<?xml version='1.0' encoding='utf-8'?>";
		s+= "\n<ExceptionReport xmlns=\"http://www.opengis.net/ows/1.1\" ";
		s+= "xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" ";
		s+= "xsi:schemaLocation=\"http://www.opengis.net/ows/1.1\" version=\"1.1.0\">";
		s+= "\n\t<Exception exceptionCode=\"";
		s+= code;
		s+= "\"";
		if(locator != null && locator != "" )
		{
			s+= " locator=\"";
			s+= locator;
			s+= "\"";
		}
		s+= ">";
		if( errorMessage != null && errorMessage.length()!= 0)
		{
			s+= "\n\t\t<ExceptionText>";
			s+= errorMessage;
			s+= "</ExceptionText>";
		}
		s+= "\n\t</Exception>";
		s+= "\n</ExceptionReport>";
		
		byte buf[] = s.getBytes(); 
		out.write(buf);
		return out;
	}

}

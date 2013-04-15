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
package org.easysdi.proxy.xml.resolver;

import java.io.IOException;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.URL;
import java.util.zip.GZIPInputStream;

import org.xml.sax.InputSource;
import org.xml.sax.SAXException;

public class ResourceResolver  implements org.xml.sax.EntityResolver
{
    String userName = null;
    String password = null;


    /**
     * @param userName
     * @param password
     */
    public ResourceResolver(String userName, String password) {
	super();
	this.userName = userName;
	this.password = password;
    }



    /* (non-Javadoc)
     * @see org.xml.sax.EntityResolver#resolveEntity(java.lang.String, java.lang.String)
     */
    public InputSource resolveEntity(String publicId, String systemId)
    throws SAXException, IOException {



	
	// TODO Auto-generated method stub
	return getSecuredResource(systemId);
    }


    protected InputSource getSecuredResource( String urlstr) {
	try {	    
	    String cookie = null;


	    boolean isAuthenticated=false;
	    String encoding =null;
	    if (userName != null && password != null) {
		String userPassword = userName + ":" + password;
		encoding = new sun.misc.BASE64Encoder()
		.encode(userPassword.getBytes());
		isAuthenticated=true;		
	    }


	    URL url = new URL(urlstr);
	    HttpURLConnection hpcon = null;

	    hpcon = (HttpURLConnection) url.openConnection();
	    hpcon.setRequestMethod("GET");
	    if (cookie != null) {
		hpcon.addRequestProperty("Cookie", cookie);
	    }
	    if (isAuthenticated){
		hpcon.setRequestProperty("Authorization", "Basic " + encoding);
	    }
	    hpcon.setUseCaches(false);
	    hpcon.setDoInput(true);


	    hpcon.setDoOutput(false);


	    // getting the response is required to force the request, otherwise
	    // it might not even be sent at all
	    InputStream in= null;

	    if (hpcon.getContentEncoding() != null && hpcon.getContentEncoding().indexOf("gzip") != -1) { //$NON-NLS-1$
		in = new GZIPInputStream(hpcon.getInputStream());
	    }else{
		in= hpcon.getInputStream();
	    }
	 		
	return new org.xml.sax.InputSource(in); 	    

	} catch (Exception e) {
	    e.printStackTrace();
	    return null;

	    // throw new IOException(e.getMessage());
	}
    }

} // ResourceResolver
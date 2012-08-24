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
package org.easysdi.xml.handler.mapper;

import com.sun.xml.bind.marshaller.NamespacePrefixMapper;

public class NamespacePrefixMapperImpl extends NamespacePrefixMapper {
	 public String getPreferredPrefix(String namespaceUri, String suggestion, boolean requirePrefix) {
	     
		 
	 
	        if( "http://www.isotc211.org/2005/gmd".equals(namespaceUri) )
	            return "gmd";
	         
	     
	        if( "http://www.opengis.net/gml".equals(namespaceUri) )
	            return "gml";

	     
	        if( "http://www.isotc211.org/2005/gts".equals(namespaceUri) )
	            return "gts";
	            
	        if( "http://www.isotc211.org/2005/gco".equals(namespaceUri) )
	            return "gco";
	        
	        if( "http://www.isotc211.org/2005/gts".equals(namespaceUri) )
	            return "gts";
	        
	        if( "http://www.fao.org/geonetwork".equals(namespaceUri) )
	            return "geonet";
	        
	        if( "http://www.w3.org/1999/xlink".equals(namespaceUri) )
	            return "xlink";
	        
	        if( "http://www.depth.ch/2008/ext".equals(namespaceUri) )
	            return "ext";
	        
	        if( "http://www.opengis.net/ogc".equals(namespaceUri))
	        	return "ogc";
	        
	        if( "http://www.opengis.net/ows".equals(namespaceUri))
	        	return "ows";
	        
	        if( "http://www.geocat.ch/2003/05/gateway/GM03Small".equals(namespaceUri))
	        	return "chmin";
	        if("http://schemas.xmlsoap.org/soap/envelope/".equals(namespaceUri)){
	        	return "soapenv";	        	
	        }
	        
	        if ("http://www.geocat.ch/2003/05/gateway/header".equals(namespaceUri)){
	        	return "gch";
	        }
	        return suggestion;
	    }
	
}

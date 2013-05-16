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
package org.easysdi.xml.handler;


import java.util.HashMap;
import java.util.List;
import java.util.Vector;

import org.xml.sax.Attributes;
import org.xml.sax.SAXException;
import org.xml.sax.helpers.DefaultHandler;

public class GeonetworkSearchResultHandler extends DefaultHandler {

    private boolean isInMetadata = false;
    private boolean isInInfo=false;
    private String data ="";
    private String currentId ="";    
    private String currentUUId ="";
    private HashMap<String,List<String>> map = new HashMap();
    
    public List<String> getGeonetworkInternalId(String uuid){
	
	return (List<String>)map.get(uuid);
    }
    
    
    public void startElement(String nameSpace, String localName,  String qName, Attributes attr) throws SAXException  {  			  

	
	//<metadata xmlns:xalan="http://xml.apache.org/xalan" xmlns:gml="http://www.opengis.net/gml" xmlns:gmx="http://www.isotc211.org/2005/gmx" xmlns:gco="http://www.isotc211.org/2005/gco" xmlns:gmd="http://www.isotc211.org/2005/gmd"><geonet:info><id>904</id><uuid>8888</uuid><schema>iso19139</schema><createDate>2008-02-29T18:05:34</createDate><changeDate>2008-02-29T18:05:34</changeDate><source>f890f5ac-91fb-4cab-9951-b6af5f4002b0</source><score>0.4101154</score></geonet:info></metadata>
	
	
	if (localName.equals("info")){
	    isInInfo=true;
	}	    
	if (localName.equals("metadata")){
	    isInMetadata = true;
	    }

    }

    public void endElement(String nameSpace, String localName, 
	    String qName) throws SAXException {
	
	if (isInInfo && isInMetadata && localName.equals("id")){
	    currentId = data;
	}
	if (isInInfo && isInMetadata && localName.equals("uuid")){
	    currentUUId = data;
	}
	
	if (currentId.length() > 0 && currentUUId.length() > 0){
	    
	    Vector<String> v= (Vector<String>)map.get(currentUUId);
	    if (v == null){
		v = new Vector<String>();
	    }
	    v.add(currentId);
	    map.put(currentUUId, v);
	    
	    currentId="";
	    currentUUId="";
	}
	if (localName.equals("metadata")){
	    isInMetadata = false;
	    }
	if (localName.equals("info")){
	    isInInfo=false;
	}	
	data = "";
    }

    /**
     * Actions à réaliser au début du document.
     */
    public void startDocument() {

    }

    /**
     * Actions à réaliser lors de la fin du document XML.
     */
    public void endDocument() {

    }

    /**
     * Actions à réaliser sur les données
     */
    public void characters(char[] caracteres, int debut, 
	    int longueur) throws SAXException {
	String donnees = new String(caracteres, debut, longueur);
	if (data == null)
	    data = donnees.trim();
	else
	    data = data + donnees.trim();

    }
}




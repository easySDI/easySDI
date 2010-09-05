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


import java.util.List;
import java.util.Vector;

import org.xml.sax.Attributes;
import org.xml.sax.SAXException;
import org.xml.sax.helpers.DefaultHandler;

public class CswRequestHandler extends DefaultHandler {

    private String operation = "";
    private  String service = "";
    private String version ="";
    private boolean isFirst= true;
    private String data ="";
    private List typeName= new Vector();
    private boolean hasFilter= false;
    private boolean isInInsert = false;
    private boolean isInDelete = false;
    private boolean hasInsert = false;
    private boolean hasDelete = false;
    private boolean isInFileIdentifier=false;
    private boolean isInMetadata=false;
    private boolean isInCharacterString=false;
    private boolean isPropertyNameFileIdentifier =false; 
    private List<String> MdToInsertList =new Vector();
    private List<String> MdToDeleteList =new Vector();
    private boolean isInPropertyIsEqualTo=false; 
    private String recordId ="";

    public List<String> getUUidListToDelete(){
	return MdToDeleteList;
    }
    
    public List<String> getUUIdListToInsert(){
	return MdToInsertList;
    }
    public boolean isTransactionInsert(){
	return hasInsert;
    }
    public boolean isTransactionDelete(){
	return hasDelete;
    }
    
    public boolean hasRequestFilter(){

	return hasFilter;
    }

    public String getVersion() {
	return version;
    }

    public void setVersion(String version) {
	this.version = version;
    }

    public String getOperation() {
	return operation;
    }

    public void setOperation(String operation) {
	this.operation = operation;
    }

    public String getService() {
	return service;
    }

    public void setService(String service) {
	this.service = service;
    }
    
	/**
	 * @param getRecordId the getRecordId to set
	 */
	public void setRecordId(String recordId) {
		this.recordId = recordId;
	}

	/**
	 * @return the getRecordId
	 */
	public String getRecordId() {
		return recordId;
	}


    public void startElement(String nameSpace, String localName,  String qName, Attributes attr) throws SAXException  {  			  

	if (isFirst){
	    operation=localName;
	    service=attr.getValue("service");
	    version=attr.getValue("version");
	    isFirst= false;
	}
	
	
	
	//Requested by the GetFeature operation	    
	if (localName.equals("Query")){

	    if ("GetRecords".equalsIgnoreCase(operation) && "CSW".equalsIgnoreCase(service)){
		String typeNameTemp =attr.getValue("typeNames");
		String a[]=typeNameTemp.split(" ");
		for (int i = 0 ;i<a.length;i++){		   
		    typeName.add(a[i]);
		}
	    }			
	}

	if (localName.equals("Insert")){
	    if ("Transaction".equalsIgnoreCase(operation) && "CSW".equalsIgnoreCase(service)){
		hasInsert=true;
		isInInsert = true;
	    }
	}

	if (isInFileIdentifier && isInMetadata && localName.equals("CharacterString")){
	    isInCharacterString =true;
	}
	if (isInMetadata&&localName.equals("fileIdentifier")){
	    isInFileIdentifier = true;
	}
	
	if (isInInsert && localName.equals("MD_Metadata")){
	    isInMetadata=true;

	}
	  
	if (localName.equals("Delete")){
	    if ("Transaction".equalsIgnoreCase(operation) && "CSW".equalsIgnoreCase(service)){
		hasDelete=true;
		isInDelete = true;
	    }
	}
	
	
	
	/***
	 * Supports only the Filter PropertyIsEqualTo on the File Identifier.
	 */	
	if (isInDelete && localName.equals("PropertyIsEqualTo")){
	   isInPropertyIsEqualTo = true; 
	}	           

    }



    public void endElement(String nameSpace, String localName, 
	    String qName) throws SAXException {
	
	//Handle getRecordByID
	if (localName.equals("Id")){
	    if ("GetRecordById".equalsIgnoreCase(operation) && "CSW".equalsIgnoreCase(service)){
	    	recordId = data;
	    }
	}
	
	if (isInDelete && localName.equals("PropertyIsEqualTo")){
		   isInPropertyIsEqualTo = false; 
		}
	
	
	//Requested by the DescribeFeatureType
	if (localName.equals("TypeName")){		
	    typeName.add(data.substring(data.indexOf(":") + 1));
	}
	if (localName.equals("Filter")){		
	    hasFilter=true;
	}

	if (localName.equals("Delete")){
	    if ("Transaction".equalsIgnoreCase(operation) && "CSW".equalsIgnoreCase(service)){
		isInDelete = false;
	    }
	}

	if (localName.equals("Insert")){
	    if ("Transaction".equalsIgnoreCase(operation) && "CSW".equalsIgnoreCase(service)){
		isInInsert = false;
	    }	    	    
	}

	if (isInFileIdentifier && isInMetadata && localName.equals("CharacterString")){
	     MdToInsertList.add(data);
	     isInCharacterString = false;
	}
	if (localName.equals("fileIdentifier")){
	    isInFileIdentifier = false;
	}
	
	if (localName.equals("MD_Metadata")){
	    isInMetadata=true;
	}

	
	
	if (isInPropertyIsEqualTo && localName.equals("PropertyName")){
	
	    if (data.contains("fileIdentifier")){
		isPropertyNameFileIdentifier=true;
	    }
		 
	}
	
	if (isPropertyNameFileIdentifier&&localName.equals("Literal")){
        	    MdToDeleteList.add(data);
        	    isPropertyNameFileIdentifier=false;
	}
	 
	      
	      

	data = "";
    }

    /**
     * Actions � r�aliser au d�but du document.
     */
    public void startDocument() {

    }

    /**
     * Actions � r�aliser lors de la fin du document XML.
     */
    public void endDocument() {

    }

    /**
     * Actions � r�aliser sur les donn�es
     */
    public void characters(char[] caracteres, int debut, 
	    int longueur) throws SAXException {
	String donnees = new String(caracteres, debut, longueur);
	if (data == null)
	    data = donnees.trim();
	else
	    data = data + donnees.trim();

    }

    public List getTypeName() {	    
	return typeName;
    }

    public void setTypeName(List typeName) {
	this.typeName = typeName;
    }

}




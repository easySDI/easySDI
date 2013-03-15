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

@Deprecated
public class PolicyHandler extends DefaultHandler {

    private String remoteServer;
    private String user;


    private boolean isInPolicySet = false;
    private boolean isInPolicy = false;
    private boolean isInRule = false;
    private boolean isInTarget = false;
    private boolean isInSubjects = false;
    private boolean isInSubject = false;
    private boolean isInSubjectMatch = false;
    private boolean isInAnySubject = false;
    private boolean isInResources = false;
    private boolean isInResource = false;
    private boolean isInResourceMatch = false;
    private boolean isInAttributeValue = false;
    private boolean isInResourceAttributeDesignator = false;

    private boolean isInSubjectAttributeDesignator = false;	

    private String resourceMatchFunction = "";
    private String  subjectMatchFunction = "";
    private String attributeValueDataType = "";
    private String attributeId ="";

    private Vector<String> operationsExplicitlyPermitVector = new Vector<String>();
    private Vector<String> operationsExplicitlyDenyVector = new Vector<String>();

    private String globalUser="";
    private String ruleUser="";
    private String globalServer="";
    private String ruleServer="";	
    private String ruleEffect="";

    private String matchString1 ="";
    private String matchString2 ="";

    private static String RULE = "Rule";
    private static String  TARGET = "Target";
    private static String SUBJECTS  = "Subjects";
    private static String SUBJECT = "Subject";
    private static String SUBJECTMATCH = "SubjectMatch";
    private static String  ANYSUBJECT = "AnySubject";	
    private static String RESOURCES = "Resources";
    private static String RESOURCE = "Resource";
    private static String RESOURCEMATCH ="ResourceMatch";
    private static String ATTRIBUTEVALUE = "AttributeValue";
    private static String RESOURCEATTRIBUTEDESIGNATOR = "ResourceAttributeDesignator";
    private static String POLICY = "Policy";
    private static String POLICYSET = "PolicySet";
    private static String SUBJECTATTRIBUTEDESIGNATOR = "SubjectAttributeDesignator";  



    /***
     * 
     * @return Returns the lists of explicitly permited Operations
     */
    public List<String> getPermitedOperations(){

	return operationsExplicitlyPermitVector;
    }
    /***
     * 
     * @return Returns the lists of explicitly denied Operations
     */
    public List<String> getDeniedOperations(){

	return operationsExplicitlyDenyVector;
    }

    /***
     * Returns if a resource is granted or not
     * If the operation is not found in the policy file
     * then the operation is rejected
     * 
     * @param operation the operation on test against the policy file
     * @return true if resource is allowed false if not 
     */	
    public boolean isOperationAllowed(String operation){

	java.util.Iterator<String> it = operationsExplicitlyDenyVector.iterator();
	while(it.hasNext()){
	    String op = (String)it.next();
	    if (op.equalsIgnoreCase(operation)){
		return false;
	    }
	}
	it = operationsExplicitlyPermitVector.iterator();
	while(it.hasNext()){
	    String op = (String)it.next();
	    if (op.equalsIgnoreCase(operation)){
		return true;
	    }
	}		

	return false;
    } 

    /***
     * Sets the remote server to test
     * @param server is the remote server
     */
    public void setRemoteServer(String server){
	this.remoteServer = server;

    }

    /***
     * Sets the user calling the resource
     * '*' means any user
     * @param user is the user
     */

    public void setCurrentUser (String user){

	this.user= user;
    }

    public void startElement(String nameSpace, String localName,  String qName, Attributes attr) throws SAXException  {  			  

	if (qName.equals(POLICY)){	  			
	    isInPolicy = true;	
	}
	if (qName.equals(POLICYSET)){	  			
	    isInPolicySet = true;	
	}
	if (qName.equals(SUBJECTATTRIBUTEDESIGNATOR)){	  			
	    isInSubjectAttributeDesignator = true;	
	    attributeId = attr.getValue("AttributeId");;
	    if (attributeId.equals("urn:ogc:def:geoxacml:1.0:resource:Service-Id")){
		matchString2 = remoteServer;	
	    }		  		
	    if (attributeId.equals("urn:oasis:names:tc:xacml:1.0:subject:subject-id")){
		matchString2 = user;	
	    }

	}


	if (qName.equals(RULE)){	  			
	    isInRule = true;	
	    ruleEffect = attr.getValue("Effect");;
	}
	if (qName.equals(TARGET)){	  			
	    isInTarget = true;	
	}
	if (qName.equals(SUBJECTS)){	  			
	    isInSubjects = true;	
	}	  		
	if (qName.equals(SUBJECT)){	  			
	    isInSubject = true;	
	}
	if (qName.equals(SUBJECTMATCH)){
	    subjectMatchFunction = attr.getValue("MatchId");
	    isInSubjectMatch = true;	
	}
	if (qName.equals(ANYSUBJECT)){	  			
	    isInAnySubject = true;	
	}
	if (qName.equals(RESOURCES)){	  			
	    isInResources = true;	
	}
	if (qName.equals(RESOURCE)){	  			
	    isInResource = true;	
	}
	if (qName.equals(RESOURCEMATCH)){	  			
	    isInResourceMatch = true;	
	    resourceMatchFunction = attr.getValue("MatchId");
	}
	if (qName.equals(ATTRIBUTEVALUE)){	  			
	    isInAttributeValue = true;	
	    attributeValueDataType = attr.getValue("DataType");;
	}
	if (qName.equals(RESOURCEATTRIBUTEDESIGNATOR)){	  			
	    isInResourceAttributeDesignator = true;	
	    attributeId = attr.getValue("AttributeId");;

	    if (attributeId.equals("urn:ogc:def:geoxacml:1.0:resource:Service-Id")){
		matchString2 = remoteServer;	
	    }		  		
	    if (attributeId.equals("urn:oasis:names:tc:xacml:1.0:subject:subject-id")){
		matchString2 = user;	
	    }

	}


    }

    public void endElement(String nameSpace, String localName, 
	    String qName) throws SAXException {
	if (qName.equals(POLICYSET)){	  			
	    isInPolicySet = false;	
	}
	if (qName.equals(SUBJECTATTRIBUTEDESIGNATOR)){	  			
	    isInSubjectAttributeDesignator = false;	
	}
	if (qName.equals(POLICY)){	  			
	    isInPolicy = false;	
	}
	if (qName.equals(RULE)){	  			
	    isInRule = false;	
	    ruleUser ="";
	    ruleServer = "";
	    ruleEffect = "";
	}

	if (qName.equals(TARGET)){	  			
	    isInTarget = false;	
	}
	if (qName.equals(SUBJECTS)){	  			
	    isInSubjects = false;			
	}	  		
	if (qName.equals(SUBJECT)){	  			
	    isInSubject = false;	
	}
	if (qName.equals(SUBJECTMATCH)){
	    boolean isUserOk = false;
	    if(matchString1.equalsIgnoreCase("") || matchString2.equalsIgnoreCase("")){
		System.err.println("missing resource in ResourceMatch. Cannot process the tag");	
	    }else{
		if (subjectMatchFunction.equals("urn:oasis:names:tc:xacml:1.0:function:string-equal")){
		    if (matchString1.equalsIgnoreCase(matchString2)){
			isUserOk = true;
		    }
		}		
	    }

	    if (isUserOk){
		if (isInPolicy && isInTarget && isInSubjects ){
		    if (!isInRule){
			globalUser=user;
		    }else{						
			ruleUser=user;	
		    }					
		}

	    }

	    resourceMatchFunction = "";
	    matchString1 = "";
	    matchString2 = "";

	    isInSubjectMatch = false;	
	}
	if (qName.equals(ANYSUBJECT)){
	    //Means every one
	    if (isInPolicy && isInTarget && isInSubjects && isInAnySubject){
		if (!isInRule){
		    globalUser="*";}else{
			ruleUser="*";	
		    }

	    }			

	    isInAnySubject = false;	
	}
	if (qName.equals(RESOURCES)){	  			
	    isInResources = false;	
	}
	if (qName.equals(RESOURCE)){	  			
	    isInResource = false;	
	}




	if (qName.equals(RESOURCEMATCH)){	  			


	    /*
	     * global server resource reference or rule server reference
	     */
	    if (isInPolicy && isInTarget && isInResources && isInResource && isInResourceMatch && attributeId.equals("urn:ogc:def:geoxacml:1.0:resource:Service-Id")){
		if (resourceMatchFunction.equals("urn:oasis:names:tc:xacml:1.0:function:anyURI-equal")){
		    if (matchString1.equalsIgnoreCase(matchString2)){
			if (isInRule){	
			    ruleServer=matchString2;
			}else {
			    globalServer=matchString2;	
			}
		    }					
		}
	    }
	    /*
	     * global operation resource reference or rule operation reference 
	     */				
	    if (isInPolicy && isInTarget && isInResources && isInResource && isInResourceMatch && attributeId.equals("urn:ogc:def:geoxacml:1.0:resource:Operation-Id")){					
		if (resourceMatchFunction.equals("urn:oasis:names:tc:xacml:1.0:function:string-equal")){
		    if (isInRule){				
			boolean isServerOk=false;
			if ((globalUser.equals("*") || globalUser.equalsIgnoreCase(user)) && (globalServer.equalsIgnoreCase(remoteServer))){
			    isServerOk = true;	
			    if (ruleUser.equals("")){
				isServerOk = true;	
			    }  else{
				if ((ruleUser.equals("*") || ruleUser.equalsIgnoreCase(user)) && (ruleServer.equalsIgnoreCase(remoteServer))){
				    isServerOk = true;
				}else{
				    if (ruleServer.equalsIgnoreCase("") && isServerOk == true){
					isServerOk = true;
				    }else{
					isServerOk = false;
				    }
				}
			    }							
			}
			else{
			    if (globalUser.equals("") && globalServer.equals("") ){
				/**
				 * No remote server tested against a user in the global area.
				 * A user and a server should be tested in the rule area
				 */ 								
				if (ruleUser.equals("") || ruleServer.equals("")){
				    isServerOk = false;	

				}else{
				    if ((ruleUser.equals("*") || ruleUser.equalsIgnoreCase(user)) && (ruleServer.equalsIgnoreCase(remoteServer))){
					isServerOk=true;
				    }else{
					isServerOk=false;
				    }
				}																
			    }
			    else{
				isServerOk = false;
			    }
			}

			boolean isUserOk = false;

			if (isServerOk && (ruleUser.equals("*") || ruleUser.equalsIgnoreCase(user))){
			    isUserOk = true;
			}	


			if (isServerOk && isUserOk){
			    if(ruleEffect.equals("Deny")){
				operationsExplicitlyDenyVector.add(matchString1);
			    }else{
				operationsExplicitlyPermitVector.add(matchString1);
			    }
			}
		    }
		}
	    }

	    isInResourceMatch = false;
	    attributeId ="";
	    resourceMatchFunction = "";
	    matchString1 = "";
	    matchString2 = "";
	}
	if (qName.equals(ATTRIBUTEVALUE)){	  			
	    isInAttributeValue = false;	
	    attributeValueDataType = "";
	}
	if (qName.equals(RESOURCEATTRIBUTEDESIGNATOR)){	  			
	    isInResourceAttributeDesignator = false;				
	}			  		
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

	if (isInPolicy && isInTarget && isInResources && isInResource && isInResourceMatch && isInAttributeValue && attributeValueDataType.equals("http://www.w3.org/2001/XMLSchema#anyURI")){
	    matchString1 = donnees.trim();		    	
	} 	

	if (isInPolicy && isInTarget && isInResources && isInResource && isInResourceMatch && isInAttributeValue && attributeValueDataType.equals("http://www.w3.org/2001/XMLSchema#string")){
	    matchString1 = donnees.trim();		    	
	} 	

	if (isInPolicy && isInTarget && isInSubjects && isInSubject && isInSubjectMatch && isInAttributeValue && attributeValueDataType.equals("http://www.w3.org/2001/XMLSchema#string")){
	    matchString1 = donnees.trim();		    	
	} 			    		    

    }
}




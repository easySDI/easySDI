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

import java.util.Vector;

import org.easysdi.xml.documents.Operation;
import org.xml.sax.Attributes;
import org.xml.sax.SAXException;
import org.xml.sax.helpers.DefaultHandler;



public class CSWCapabilities200Handler extends DefaultHandler {
	public static String OPERATIONTAG = "Operation";
	public static String HTTPTAG = "HTTP";
	public static String DCPTAG = "DCP";
	public static String GETTAG = "Get";
	public static String POSTTAG = "Post";
	
	  private String tagCourant = "";
	  private boolean isInOperation = false;
	  private boolean isInDCP = false;
	  private boolean isInHTTP = false;
	  private boolean isInGet = false;
	  private boolean isInPost = false;
	  private String currentOperationName ;
	  private String currentGetUrl ;
	  private String currentPostUrl;
	  private Vector operations = new Vector();
	  private Operation op;
	  
	  public boolean isOperationInCapabilities(String operation){
		if (operations == null) return false;
		java.util.Iterator it = operations.iterator();
		while(it.hasNext()){
			Operation op = (Operation) it.next();
			if (op.getName().equals(operation)){return true;}
		}
		return false;
	  }
	  
	  /**
	   * Actions a réaliser lors de la detection d'un nouvel element.
	   */
	  public void startElement(String nameSpace, String localName,  String qName, Attributes attr) throws SAXException  {
		  		  
		 if (qName.equals(OPERATIONTAG)){
			 
			 isInOperation = true;
			 currentOperationName = attr.getValue("name");
			 op = new Operation();
			 op.setName(currentOperationName);
		 } 
		 if (qName.equals(DCPTAG)){
			 isInDCP = true;
		 } 
		 if (qName.equals(HTTPTAG)){
			 isInHTTP = true;
		 }  
		 if (qName.equals(GETTAG)){
			 isInGet = true;
			 currentGetUrl = attr.getValue("xlink:href");
			 op.setGetUrl(currentGetUrl);
		 }  
		 if (qName.equals(POSTTAG)){
			 isInPost = true;
			 currentPostUrl = attr.getValue("xlink:href");
			 op.setGetUrl(currentPostUrl);
		 }  
		 
	    tagCourant = localName;	    
	  }

	  /**
	   * Actions to be realized when the end of an element is detected
	   */
	  public void endElement(String nameSpace, String localName, 
	    String qName) throws SAXException {
		  
		    		 
			 if (qName.equals(OPERATIONTAG)){
				 isInOperation = false;
				 currentOperationName=null;
				 operations.add(op);
			 } 
			 
			 if (qName.equals(DCPTAG)){
				 isInDCP = false;
			 }
			 
			 if (qName.equals(HTTPTAG)){
				 isInHTTP = false;
			 }
			 
			 if (qName.equals(GETTAG)){				 
				 isInGet = false;
				 currentGetUrl=null;				
			 }  
			 
			 if (qName.equals(POSTTAG)){
				 isInPost = false;
				 currentPostUrl=null;
			 }  
			 			 
	    tagCourant = "";
	    System.out.println("Fin tag " + qName);
	  }

	
	  public void startDocument() {
	    System.out.println("Debut du document");
	  }

	  /**
	   * Actions à réaliser lors de la fin du document XML.
	   */
	  public void endDocument() {
	    System.out.println("Fin du document");
	  }

	  /**
	   * Actions à réaliser sur les données
	   */
	  public void characters(char[] caracteres, int debut, 
	    int longueur) throws SAXException {
	    String donnees = new String(caracteres, debut, longueur);

	    if (!tagCourant.equals("")) {	
	      if(!Character.isISOControl(caracteres[debut])) {
	        System.out.println("   Element " + tagCourant +", valeur = *" + donnees + "*");
	      }
	    }
	  }
	  	  
}

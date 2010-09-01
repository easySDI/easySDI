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

import org.easysdi.proxy.cgp.CGPRequestId;
import org.easysdi.proxy.cgp.Expression;
import org.xml.sax.Attributes;
import org.xml.sax.SAXException;
import org.xml.sax.helpers.DefaultHandler;


public class CGPHandler extends DefaultHandler {

	private boolean isSoap = false;
	private boolean isInRequestId= false;	
	private boolean isInHeader= false;
	private boolean isInBody=false;
	private boolean isInCatalogGatewayRequest = false;
	private boolean  isInQueryRequest= false;
	private boolean   isInCriteria= false;
	private boolean isInExpression= false;
	private boolean isInFormat= false;
	private boolean isInConcatenatedExpression= false;
	
	private String data;
	private String profile;
	
	private CGPRequestId reqId;
	private List exps;
	
	public void startElement(String nameSpace, String localName,  String qName, Attributes attr) throws SAXException  {  			  
		
		if (qName.equalsIgnoreCase("Envelope") && nameSpace.equalsIgnoreCase("http://schemas.xmlsoap.org/soap/envelope/")){
			isSoap=true;		
		}
		if (isSoap && qName.equalsIgnoreCase("Header") && nameSpace.equalsIgnoreCase("http://schemas.xmlsoap.org/soap/envelope/")){			
			isInHeader= true;			
		}
		if (isInHeader&& qName.equalsIgnoreCase("requestID") && nameSpace.equalsIgnoreCase("http://www.geocat.ch/2003/05/gateway/header")){			
			isInRequestId= true;
			reqId = new  CGPRequestId();
		}			
		if (isSoap && qName.equalsIgnoreCase("Body") && nameSpace.equalsIgnoreCase("http://schemas.xmlsoap.org/soap/envelope/")){			
			isInBody= true;			
		}
		if (isInBody && qName.equalsIgnoreCase("catalogGatewayRequest") && nameSpace.equalsIgnoreCase("http://www.geocat.ch/2003/05/gateway/query")){			
			isInCatalogGatewayRequest= true;			
		}
		if (isInCatalogGatewayRequest && qName.equalsIgnoreCase("queryRequest") && nameSpace.equalsIgnoreCase("http://www.geocat.ch/2003/05/gateway/query")){			
			isInQueryRequest= true;			
		}
		if (isInQueryRequest && qName.equalsIgnoreCase("criteria") && nameSpace.equalsIgnoreCase("http://www.geocat.ch/2003/05/gateway/query")){			
			isInCriteria= true;			
		}
		if (isInCriteria && qName.equalsIgnoreCase("expression") && nameSpace.equalsIgnoreCase("http://www.geocat.ch/2003/05/gateway/query")){			
			isInExpression= true;
			if (exps == null){
				exps = new Vector();
			}
			exps.add(new Expression());
			
		}
		if (isInQueryRequest && qName.equalsIgnoreCase("format") && nameSpace.equalsIgnoreCase("http://www.geocat.ch/2003/05/gateway/query")){			
			isInFormat= true;			
		}
		if (isInCriteria && qName.equalsIgnoreCase("concatenatedExpression") && nameSpace.equalsIgnoreCase("http://www.geocat.ch/2003/05/gateway/query")){			
			isInConcatenatedExpression= true;		
		}
		
	}

	public void endElement(String nameSpace, String localName, 
			String qName) throws SAXException {
		
		if (isInRequestId && qName.equalsIgnoreCase("version") && nameSpace.equalsIgnoreCase("http://www.geocat.ch/2003/05/gateway/header")){			
		reqId.setVersion(data);	
		}
		if (isInRequestId && qName.equalsIgnoreCase("sendingNode") && nameSpace.equalsIgnoreCase("http://www.geocat.ch/2003/05/gateway/header")){			
			reqId.setSendingNode(data);
		}
		if (isInRequestId && qName.equalsIgnoreCase("referenceId") && nameSpace.equalsIgnoreCase("http://www.geocat.ch/2003/05/gateway/header")){			
			reqId.setReferenceId(data);
		}
		if (isInRequestId && qName.equalsIgnoreCase("messageId") && nameSpace.equalsIgnoreCase("http://www.geocat.ch/2003/05/gateway/header")){			
			reqId.setMessageId(data);
		}
		if (isInRequestId && qName.equalsIgnoreCase("responseTo") && nameSpace.equalsIgnoreCase("http://www.geocat.ch/2003/05/gateway/header")){			
			reqId.setResponseTo(data);
		}
		if (isInRequestId && qName.equalsIgnoreCase("dateAndTime") && nameSpace.equalsIgnoreCase("http://www.geocat.ch/2003/05/gateway/header")){			
			reqId.setDateAndTime(data);
		}
		if (isInHeader && qName.equalsIgnoreCase("requestID") && nameSpace.equalsIgnoreCase("http://www.geocat.ch/2003/05/gateway/header")){
			isInRequestId= false;
		}
		if (isSoap && qName.equalsIgnoreCase("Header") && nameSpace.equalsIgnoreCase("http://schemas.xmlsoap.org/soap/envelope/")){
			isInHeader= false;
		}
		if (isSoap && qName.equalsIgnoreCase("Body") && nameSpace.equalsIgnoreCase("http://schemas.xmlsoap.org/soap/envelope/")){			
			isInBody= false;			
		}
		if (isInBody && qName.equalsIgnoreCase("catalogGatewayRequest") && nameSpace.equalsIgnoreCase("http://www.geocat.ch/2003/05/gateway/query")){			
			isInCatalogGatewayRequest= false;			
		}
		if (isInCatalogGatewayRequest && qName.equalsIgnoreCase("queryRequest") && nameSpace.equalsIgnoreCase("http://www.geocat.ch/2003/05/gateway/query")){			
			isInQueryRequest= false;			
		}
		if (isInQueryRequest && qName.equalsIgnoreCase("criteria") && nameSpace.equalsIgnoreCase("http://www.geocat.ch/2003/05/gateway/query")){			
			isInCriteria= false;			
		}
		if (isInCriteria && qName.equalsIgnoreCase("expression") && nameSpace.equalsIgnoreCase("http://www.geocat.ch/2003/05/gateway/query")){			
			isInExpression= false;			
		}	
		if (isInExpression && qName.equalsIgnoreCase("attribute") && nameSpace.equalsIgnoreCase("http://www.geocat.ch/2003/05/gateway/query")){			
			if (exps.size()> 0){
				((Expression)exps.get(exps.size()-1)).setAttribute(data); 				
			}
		}
		if (isInExpression && qName.equalsIgnoreCase("operator") && nameSpace.equalsIgnoreCase("http://www.geocat.ch/2003/05/gateway/query")){
			if (exps.size()> 0){
				((Expression)exps.get(exps.size()-1)).setOperator(data); 				
			}			
		}
		if (isInExpression && qName.equalsIgnoreCase("value") && nameSpace.equalsIgnoreCase("http://www.geocat.ch/2003/05/gateway/query")){
			if (exps.size()> 0){
				((Expression)exps.get(exps.size()-1)).setValue(data); 				
			}			
		}
		if (isInQueryRequest && qName.equalsIgnoreCase("format") && nameSpace.equalsIgnoreCase("http://www.geocat.ch/2003/05/gateway/query")){			
			isInFormat= false;					
		}
		if (isInFormat && qName.equalsIgnoreCase("profile") && nameSpace.equalsIgnoreCase("http://www.geocat.ch/2003/05/gateway/query")){			
			profile = data;
		}
		if (isInCriteria && qName.equalsIgnoreCase("concatenatedExpression") && nameSpace.equalsIgnoreCase("http://www.geocat.ch/2003/05/gateway/query")){			
			isInConcatenatedExpression= false;		
		}
		if (isInConcatenatedExpression && qName.equalsIgnoreCase("concatenationOperator") && nameSpace.equalsIgnoreCase("http://www.geocat.ch/2003/05/gateway/query")){			
			if (exps.size()> 0){
				((Expression)exps.get(exps.size()-1)).setConcatenationOperator((data)); 				
			}								
		}		
	}
	
	public void startDocument() {

	}

	
	public void endDocument() {

	}

	
	public void characters(char[] caracteres, int debut, 
			int longueur) throws SAXException {
		
		String donnees = new String(caracteres, debut, longueur);
		data = donnees.trim();		
}
}




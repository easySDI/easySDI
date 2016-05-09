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
package org.easysdi.proxy.xml.handler;

import java.util.List;
import java.util.Vector;

import org.xml.sax.Attributes;
import org.xml.sax.SAXException;
import org.xml.sax.helpers.DefaultHandler;

public class RequestHandler extends DefaultHandler {

	private String operation = "";
	private String service = "";
	private String version = "";
	private boolean isFirst = true;
	private String data = "";
	private List typeName = new Vector();
	// Debug tb 11.09.2009
	private Boolean isInFilterElement = false; // Pour �tre sur que le
												// propertyName lu n'est pas
												// celui de l'�l�ment <Filter>,
												// mais bien un attribut �
												// renvoyer
	private String elementQName = "";
	private Boolean isElementQName = false; // To be sure to avoid "\t\t"
											// characters
	private List propertyName = new Vector();
	private String elementLocalName= "";
	// Fin de debug
	private boolean hasFilter = false;

	public boolean hasRequestFilter() {

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

	public void startElement(String nameSpace, String localName, String qName, Attributes attr) throws SAXException {
		// Debug tb 04.06.2009
		elementQName = qName; // N�cessaire pour utilsation dans la m�thode
								// "characters"
		elementLocalName = localName;
		isElementQName = false;
		// Fin de debug

		if (isFirst) {
			operation = localName;
			service = attr.getValue("service");
			version = attr.getValue("version");
			isFirst = false;
		}

		// Requested by the GetFeature operation
		if (localName.equals("Query")) {
			if ("GetFeature".equalsIgnoreCase(operation) && "WFS".equalsIgnoreCase(service)) {
				String typeNameTemp = attr.getValue("typeName");
				typeName.add(typeNameTemp);
				// typeName.add(
				// typeNameTemp.substring(typeNameTemp.indexOf(":") + 1));
			} else {
				if ("GetRecords".equalsIgnoreCase(operation) && "CSW".equalsIgnoreCase(service)) {
					String typeNameTemp = attr.getValue("typeNames");
					String a[] = typeNameTemp.split(" ");
					for (int i = 0; i < a.length; i++) {
						typeName.add(a[i]);
					}
				}
			}
		}

		// Debug tb 11.09.2009
		if (localName.equalsIgnoreCase("Filter")) {
			isInFilterElement = true;
		}
		// Fin de debug
	}

	public void endElement(String nameSpace, String localName, String qName) throws SAXException {
		// Requested by the DescribeFeatureType
		if (localName.equals("TypeName")) {
			typeName.add(data);
			// typeName.add(data.substring(data.indexOf(":") + 1));
		}
		if (localName.equalsIgnoreCase("Filter")) {
			hasFilter = true;
			// Debug tb 11.09.2009
			isInFilterElement = false;
			// Fin de debug
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
		// Debug tb 04.06.2009
		if (propertyName.size() == 0) {
			propertyName.add("");
		}
		// Fin de debug
	}

	/**
	 * Actions � r�aliser sur les donn�es
	 */
	public void characters(char[] caracteres, int debut, int longueur) throws SAXException {
		String donnees = new String(caracteres, debut, longueur);
		// Debug tb 11.09.2009
		// String [] s = elementQName.split(":");
		// String tmpFT = s[s.length-1];
		String tmpFT = elementLocalName;
		if (tmpFT.equals("PropertyName") && !isElementQName && !isInFilterElement) {
			propertyName.add(donnees);
			isElementQName = true;
		}
		// Fin de debug
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

	// Debug tb 04.06.2009
	public List getPropertyName() {
		return propertyName;
	}
	// Fin de debug
}

package org.easysdi.xml.handler;

import java.util.ArrayList;
import java.util.List;

import org.xml.sax.Attributes;
import org.xml.sax.SAXException;
import org.xml.sax.helpers.DefaultHandler;

public class WMSRequestHandler extends DefaultHandler {

	private boolean isFirst = true;
	private boolean isStyledLayerDescriptor;
	private boolean isBoundingBox;
	private boolean isCoord;
	private boolean isX;
	private boolean isY;
	private boolean isOutPut;
	private boolean isNamedLayer;
	private List<String> layers = new ArrayList<String>();
	private String data = "";
	private String operation = "";
	private String version = "";
	private String service = "";
	private String CRS = ""; 
	private String srsName = "";
	private String lowerCorner;
	private String upperCorner;
	private String minX = "";
	private String minY = "";
	private String maxX = "";
	private String maxY = "";
	private String width;
	private String height;
	private String format;
	private String transparent;  
	private String exceptions;
	
	/* (non-Javadoc)
	 * @see org.xml.sax.helpers.DefaultHandler#startDocument()
	 */
	@Override
	public void startDocument() throws SAXException {
	}

	/* (non-Javadoc)
	 * @see org.xml.sax.helpers.DefaultHandler#endDocument()
	 */
	@Override
	public void endDocument() throws SAXException {
		if(CRS == "" && srsName != ""){
			CRS = srsName;
		}
		if(lowerCorner == null && minX != ""){
			lowerCorner = minX + " " + minY;
			upperCorner = maxX + " " + maxY;
		}
	}

	/* (non-Javadoc)
	 * @see org.xml.sax.helpers.DefaultHandler#startElement(java.lang.String, java.lang.String, java.lang.String, org.xml.sax.Attributes)
	 */
	@Override
	public void startElement(String uri, String localName, String qName,Attributes attributes) throws SAXException {
		if (isFirst) {
			operation = localName;
			version = attributes.getValue("version");
			service = attributes.getValue("service");
			isFirst = false;
		}
		if(!isFirst && localName.equals("StyledLayerDescriptor")){
			isStyledLayerDescriptor = true;
		}
		if(!isFirst && isStyledLayerDescriptor && localName.equals("NamedLayer")){
			isNamedLayer = true;
		}
		if(!isFirst && localName.equals("BoundingBox")){
			srsName = attributes.getValue("srsName");
			isBoundingBox = true;
		}
		if(!isFirst && isBoundingBox && localName.equals("coord")){
			isCoord = true;
		}
		if(!isFirst && localName.equals("Output")){
			isOutPut = true;
		}
		
	}

	/* (non-Javadoc)
	 * @see org.xml.sax.helpers.DefaultHandler#endElement(java.lang.String, java.lang.String, java.lang.String)
	 */
	@Override
	public void endElement(String uri, String localName, String qName) throws SAXException {
		if(!isFirst && isStyledLayerDescriptor && isNamedLayer && localName.equals("Name")){
			layers.add(data);
			isNamedLayer = false;
		}
		if(!isFirst && localName.equals("StyledLayerDescriptor")){
			isStyledLayerDescriptor = false;
		}
		if(!isFirst && localName.equals("CRS")){
			CRS = data;
		}
		if(!isFirst && localName.equals("Exceptions")){
			exceptions = data;
		}
		if(!isFirst && localName.equals("BoundingBox")){
			isBoundingBox = false;
		}
		if(!isFirst && localName.equals("coord")){
			isCoord = false;
		}
		if(!isFirst && isBoundingBox && localName.equals("LowerCorner")){
			lowerCorner = data;
		}
		if(!isFirst && isBoundingBox && localName.equals("UpperCorner")){
			upperCorner = data;
		}
		if(!isFirst && isBoundingBox && isCoord && localName.equals("X")){
			if(minX == "")
				minX = data;
			else
				maxX = data;
		}
		if(!isFirst && isBoundingBox && isCoord && localName.equals("Y")){
			if(minY == "")
				minY = data;
			else
				maxY = data;
		}
		if(!isFirst && localName.equals("Output")){
			isOutPut = false;
		}
		if(!isFirst && isOutPut && localName.equals("Width")){
			width = data;
		}
		if(!isFirst && isOutPut && localName.equals("Height")){
			height = data;
		}
		if(!isFirst && isOutPut && localName.equals("Format")){
			format = data;
		}
		if(!isFirst && isOutPut && localName.equals("Tranparent")){
			transparent = data;
		}
		
		data = "";
	}
	
	public void characters(char[] caracteres, int debut, int longueur) throws SAXException {
		String donnees = new String(caracteres, debut, longueur);
		if (data == null)
			data = donnees.trim();
		else
			data = data + donnees.trim();
	}

		/**
	 * @return the operation
	 */
	public String getOperation() {
		return operation;
	}

	/**
	 * @return the version
	 */
	public String getVersion() {
		return version;
	}

	/**
	 * @return the service
	 */
	public String getService() {
	    return service;
	}

	/**
	 * @return the layers
	 */
	public List<String> getLayers() {
		return layers;
	}

	/**
	 * @return the cRS
	 */
	public String getCRS() {
		return CRS;
	}

	/**
	 * @return the lowerCorner
	 */
	public String getLowerCorner() {
		return lowerCorner;
	}

	/**
	 * @return the upperCorner
	 */
	public String getUpperCorner() {
		return upperCorner;
	}

	/**
	 * @return the width
	 */
	public String getWidth() {
		return width;
	}

	/**
	 * @return the height
	 */
	public String getHeight() {
		return height;
	}

	/**
	 * @return the format
	 */
	public String getFormat() {
		return format;
	}

	/**
	 * @return the transparent
	 */
	public String getTransparent() {
		return transparent;
	}

	/**
	 * @return the exceptions
	 */
	public String getExceptions() {
		return exceptions;
	}

}

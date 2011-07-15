//
// This file was generated by the JavaTM Architecture for XML Binding(JAXB) Reference Implementation, vhudson-jaxb-ri-2.1-520 
// See <a href="http://java.sun.com/xml/jaxb">http://java.sun.com/xml/jaxb</a> 
// Any modifications to this file will be lost upon recompilation of the source schema. 
// Generated on: 2008.06.16 at 03:41:04 PM CEST 
//

package org.easysdi.proxy.policy;

import java.io.Serializable;

import javax.xml.bind.annotation.XmlAccessType;
import javax.xml.bind.annotation.XmlAccessorType;
import javax.xml.bind.annotation.XmlAttribute;
import javax.xml.bind.annotation.XmlElement;
import javax.xml.bind.annotation.XmlRootElement;
import javax.xml.bind.annotation.XmlType;

/**
 * <p>
 * Java class for anonymous complex type.
 * 
 * <p>
 * The following schema fragment specifies the expected content contained within
 * this class.
 * 
 * <pre>
 * &lt;complexType>
 *   &lt;complexContent>
 *     &lt;restriction base="{http://www.w3.org/2001/XMLSchema}anyType">
 *       &lt;sequence>
 *         &lt;element ref="{}Name"/>
 *         &lt;element ref="{}ScaleMin" minOccurs="0"/>
 *         &lt;element ref="{}ScaleMax" minOccurs="0"/>
 *         &lt;element ref="{}Filter" minOccurs="0"/>
 *         &lt;element ref="{}LatLonBoundingBox" minOccurs="0"/>
 *       &lt;/sequence>
 *     &lt;/restriction>
 *   &lt;/complexContent>
 * &lt;/complexType>
 * </pre>
 * 
 * 
 */
@XmlAccessorType(XmlAccessType.FIELD)
@XmlType(name = "", propOrder = { "name", "scaleMin", "scaleMax", "filter","LatLonBoundingBox","BoundingBox" })
@XmlRootElement(name = "Layer")
public class Layer implements Serializable {

    	@XmlAttribute(name = "All")
	protected Boolean all = false;
	@XmlElement(name = "Name", required = true)
	protected String name;
	@XmlElement(name = "ScaleMin")
	protected Double scaleMin;
	@XmlElement(name = "ScaleMax")
	protected Double scaleMax;
	@XmlElement(name = "Filter")
	protected Filter filter;
	@XmlElement(name = "LatLonBoundingBox")
	protected String LatLonBoundingBox;
	@XmlElement(name = "BoundingBox")
	protected BoundingBox BoundingBox;

	@Override
	public int hashCode() {
		int hashCode = 0;
		if (name != null)
			hashCode += name.hashCode();
		if (scaleMin != null)
			hashCode += scaleMin.hashCode();
		if (scaleMax != null)
			hashCode += scaleMax.hashCode();
		if (filter != null)
			hashCode += filter.hashCode();
		if (LatLonBoundingBox != null)
			hashCode += LatLonBoundingBox.hashCode();
		if (BoundingBox != null)
			hashCode += BoundingBox.hashCode();
		return hashCode;
	}

	/**
	 * Gets the value of the name property.
	 * 
	 * @return possible object is {@link String }
	 * 
	 */
	public String getName() {
		return name;
	}

	/**
	 * Sets the value of the name property.
	 * 
	 * @param value
	 *            allowed object is {@link String }
	 * 
	 */
	public void setName(String value) {
		this.name = value;
	}

	/**
	 * Gets the value of the scaleMin property.
	 * 
	 * @return possible object is {@link Double }
	 * 
	 */
	public Double getScaleMin() {
		return scaleMin;
	}

	/**
	 * Sets the value of the scaleMin property.
	 * 
	 * @param value
	 *            allowed object is {@link Double }
	 * 
	 */
	public void setScaleMin(Double value) {
		this.scaleMin = value;
	}

	/**
	 * Gets the value of the scaleMax property.
	 * 
	 * @return possible object is {@link Double }
	 * 
	 */
	public Double getScaleMax() {
		return scaleMax;
	}

	/**
	 * Sets the value of the scaleMax property.
	 * 
	 * @param value
	 *            allowed object is {@link Double }
	 * 
	 */
	public void setScaleMax(Double value) {
		this.scaleMax = value;
	}

	/**
	 * Gets the value of the filter property.
	 * 
	 * @return possible object is {@link Filter }
	 * 
	 */
	public Filter getFilter() {
		return filter;
	}

	/**
	 * Sets the value of the filter property.
	 * 
	 * @param value
	 *            allowed object is {@link Filter }
	 * 
	 */
	public void setFilter(Filter value) {
		this.filter = value;
	}
	
	/**
	 * Gets the value of the LatLonBoundingBox property.
	 * 
	 * @return possible object is {@link String }
	 * 
	 */
	public String getLatLonBoundingBox() {
		return LatLonBoundingBox;
	}

	/**
	 * Sets the value of the LatLonBoundingBox property.
	 * 
	 * @param value
	 *            allowed object is {@link String }
	 * 
	 */
	public void setLatLonBoundingBox(String value) {
		this.LatLonBoundingBox = value;
	}
	
	/**
	 * Gets the value of the name property.
	 * 
	 * @return possible object is {@link BoundingBox }
	 * 
	 */
	public BoundingBox getBoundingBox() {
		return this.BoundingBox;
	}

	/**
	 * Sets the value of the name property.
	 * 
	 * @param value
	 *            allowed object is {@link BoundingBox }
	 * 
	 */
	public void setBoundingBox(BoundingBox value) {
		this.BoundingBox = value;
	}

}

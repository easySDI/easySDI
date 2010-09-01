//
// This file was generated by the JavaTM Architecture for XML Binding(JAXB) Reference Implementation, vhudson-jaxb-ri-2.1-520 
// See <a href="http://java.sun.com/xml/jaxb">http://java.sun.com/xml/jaxb</a> 
// Any modifications to this file will be lost upon recompilation of the source schema. 
// Generated on: 2008.06.16 at 03:41:04 PM CEST 
//

package org.easysdi.proxy.policy;

import java.io.Serializable;
import java.util.ArrayList;
import java.util.List;
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
 *         &lt;element ref="{}Layer" maxOccurs="unbounded"/>
 *       &lt;/sequence>
 *       &lt;attribute name="All" type="{http://www.w3.org/2001/XMLSchema}boolean" />
 *     &lt;/restriction>
 *   &lt;/complexContent>
 * &lt;/complexType>
 * </pre>
 * 
 * 
 */
@XmlAccessorType(XmlAccessType.FIELD)
@XmlType(name = "", propOrder = { "layer" })
@XmlRootElement(name = "Layers")
public class Layers implements Serializable {

	@XmlElement(name = "Layer", required = true)
	protected List<Layer> layer;
	@XmlAttribute(name = "All")
	protected Boolean all;

	@Override
	public int hashCode() {
		int hashCode = 0;
		hashCode += ((all) ? 9781 : 4551);
		if (layer != null)
			hashCode += layer.hashCode();
		return hashCode;
	}

	/**
	 * Gets the value of the layer property.
	 * 
	 * <p>
	 * This accessor method returns a reference to the live list, not a
	 * snapshot. Therefore any modification you make to the returned list will
	 * be present inside the JAXB object. This is why there is not a
	 * <CODE>set</CODE> method for the layer property.
	 * 
	 * <p>
	 * For example, to add a new item, do as follows:
	 * 
	 * <pre>
	 * getLayer().add(newItem);
	 * </pre>
	 * 
	 * 
	 * <p>
	 * Objects of the following type(s) are allowed in the list {@link Layer }
	 * 
	 * 
	 */
	public List<Layer> getLayer() {
		if (layer == null) {
			layer = new ArrayList<Layer>();
		}
		return this.layer;
	}

	/**
	 * Gets the value of the all property.
	 * 
	 * @return possible object is {@link Boolean }
	 * 
	 */
	public Boolean isAll() {
		if (all == null)
			all = false;
		return all;
	}

	/**
	 * Sets the value of the all property.
	 * 
	 * @param value
	 *            allowed object is {@link Boolean }
	 * 
	 */
	public void setAll(Boolean value) {
		this.all = value;
	}

}

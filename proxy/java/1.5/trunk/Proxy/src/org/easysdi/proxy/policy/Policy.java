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
 *       &lt;choice>
 *         &lt;element ref="{}AvailabilityPeriod"/>
 *         &lt;element ref="{}ImageSize"/>
 *         &lt;element ref="{}Operations"/>
 *         &lt;element ref="{}Servers"/>
 *         &lt;element ref="{}Subjects"/>
 *       &lt;/choice>
 *       &lt;attribute name="ConfigId" type="{http://www.w3.org/2001/XMLSchema}string" />
 *       &lt;attribute name="Inherit" type="{http://www.w3.org/2001/XMLSchema}string" />
 *       &lt;attribute name="Id" use="required" type="{http://www.w3.org/2001/XMLSchema}string" />
 *     &lt;/restriction>
 *   &lt;/complexContent>
 * &lt;/complexType>
 * </pre>
 * 
 * 
 */
@XmlAccessorType(XmlAccessType.FIELD)
@XmlType(name = "", propOrder = { "availabilityPeriod", "imageSize", "operations", "servers", "subjects" })
@XmlRootElement(name = "Policy")
public class Policy implements Serializable {

	@XmlElement(name = "AvailabilityPeriod")
	protected AvailabilityPeriod availabilityPeriod;
	@XmlElement(name = "ImageSize")
	protected ImageSize imageSize;
	@XmlElement(name = "Operations")
	protected Operations operations;
	@XmlElement(name = "Servers")
	protected Servers servers;
	@XmlElement(name = "Subjects")
	protected Subjects subjects;
	@XmlAttribute(name = "ConfigId")
	protected String configId;
	@XmlAttribute(name = "Inherit")
	protected String inherit;
	@XmlAttribute(name = "Id", required = true)
	protected String id;

	@Override
	public int hashCode() {
		int hashCode = 0;
		if (availabilityPeriod != null)
			hashCode += availabilityPeriod.hashCode();
		if (imageSize != null)
			hashCode += imageSize.hashCode();
		if (operations != null)
			hashCode += operations.hashCode();
		if (servers != null)
			hashCode += servers.hashCode();
		if (subjects != null)
			hashCode += subjects.hashCode();
		if (configId != null)
			hashCode += configId.hashCode();
		if (inherit != null)
			hashCode += inherit.hashCode();
		if (id != null)
			hashCode += id.hashCode();
		return hashCode;
	}

	/**
	 * Gets the value of the availabilityPeriod property.
	 * 
	 * @return possible object is {@link AvailabilityPeriod }
	 * 
	 */
	public AvailabilityPeriod getAvailabilityPeriod() {
		return availabilityPeriod;
	}

	/**
	 * Sets the value of the availabilityPeriod property.
	 * 
	 * @param value
	 *            allowed object is {@link AvailabilityPeriod }
	 * 
	 */
	public void setAvailabilityPeriod(AvailabilityPeriod value) {
		this.availabilityPeriod = value;
	}

	/**
	 * Gets the value of the imageSize property.
	 * 
	 * @return possible object is {@link ImageSize }
	 * 
	 */
	public ImageSize getImageSize() {
		return imageSize;
	}

	/**
	 * Sets the value of the imageSize property.
	 * 
	 * @param value
	 *            allowed object is {@link ImageSize }
	 * 
	 */
	public void setImageSize(ImageSize value) {
		this.imageSize = value;
	}

	/**
	 * Gets the value of the operations property.
	 * 
	 * @return possible object is {@link Operations }
	 * 
	 */
	public Operations getOperations() {
		return operations;
	}

	/**
	 * Sets the value of the operations property.
	 * 
	 * @param value
	 *            allowed object is {@link Operations }
	 * 
	 */
	public void setOperations(Operations value) {
		this.operations = value;
	}

	/**
	 * Gets the value of the servers property.
	 * 
	 * @return possible object is {@link Servers }
	 * 
	 */
	public Servers getServers() {
		return servers;
	}

	/**
	 * Sets the value of the servers property.
	 * 
	 * @param value
	 *            allowed object is {@link Servers }
	 * 
	 */
	public void setServers(Servers value) {
		this.servers = value;
	}

	/**
	 * Gets the value of the subjects property.
	 * 
	 * @return possible object is {@link Subjects }
	 * 
	 */
	public Subjects getSubjects() {
		return subjects;
	}

	/**
	 * Sets the value of the subjects property.
	 * 
	 * @param value
	 *            allowed object is {@link Subjects }
	 * 
	 */
	public void setSubjects(Subjects value) {
		this.subjects = value;
	}

	/**
	 * Gets the value of the configId property.
	 * 
	 * @return possible object is {@link String }
	 * 
	 */
	public String getConfigId() {
		return configId;
	}

	/**
	 * Sets the value of the configId property.
	 * 
	 * @param value
	 *            allowed object is {@link String }
	 * 
	 */
	public void setConfigId(String value) {
		this.configId = value;
	}

	/**
	 * Gets the value of the inherit property.
	 * 
	 * @return possible object is {@link String }
	 * 
	 */
	public String getInherit() {
		return inherit;
	}

	/**
	 * Sets the value of the inherit property.
	 * 
	 * @param value
	 *            allowed object is {@link String }
	 * 
	 */
	public void setInherit(String value) {
		this.inherit = value;
	}

	/**
	 * Gets the value of the id property.
	 * 
	 * @return possible object is {@link String }
	 * 
	 */
	public String getId() {
		return id;
	}

	/**
	 * Sets the value of the id property.
	 * 
	 * @param value
	 *            allowed object is {@link String }
	 * 
	 */
	public void setId(String value) {
		this.id = value;
	}
}

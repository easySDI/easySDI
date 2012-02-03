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
 *         &lt;element ref="{}Status" maxOccurs="unbounded"/>
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
@XmlType(name = "", propOrder = { "status" })
@XmlRootElement(name = "ObjectStatus")
public class ObjectStatus implements Serializable {
	
	@XmlElement(name = "Status", required = false)
	protected List<Status> status;
	@XmlAttribute(name = "All")
	protected Boolean all;
	
	@Override
	public int hashCode() {
		int hashCode = 0;
		hashCode += ((all) ? 5909 : 4919);
		if (status != null)
			hashCode += status.hashCode();
		return hashCode;
	}

	/**
	 * Gets the value of the Status property.
	 * 
	 * <p>
	 * This accessor method returns a reference to the live list, not a
	 * snapshot. Therefore any modification you make to the returned list will
	 * be present inside the JAXB object. This is why there is not a
	 * <CODE>set</CODE> method for the visibilities property.
	 * 
	 * <p>
	 * For example, to add a new item, do as follows:
	 * 
	 * <pre>
	 * getStatus().add(newItem);
	 * </pre>
	 * 
	 * 
	 * <p>
	 * Objects of the following type(s) are allowed in the list
	 * {@link String }
	 * 
	 * 
	 */
	public List<Status> getStatus() {
		if (status == null) {
			status = new ArrayList<Status>();
		}
		return this.status;
	}

	/**
	 * Gets the value of the all property.
	 * 
	 * @return possible object is {@link Boolean }
	 * 
	 */
	public Boolean isAll() {
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

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
 *         &lt;element ref="{}Status" maxOccurs="1"/>
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
@XmlType(name = "", propOrder = { "mode" })
@XmlRootElement(name = "ObjectVersion")
public class ObjectVersion implements Serializable {

	@XmlElement(name = "mode", required = true)
	protected List<String> mode;
	
	
	@Override
	public int hashCode() {
		int hashCode = 0;
		if (mode != null)
			hashCode += mode.hashCode();
		return hashCode;
	}

	/**
	 * Gets the value of the versionModes property.
	 * 
	 * <p>
	 * This accessor method returns a reference to the live list, not a
	 * snapshot. Therefore any modification you make to the returned list will
	 * be present inside the JAXB object. This is why there is not a
	 * <CODE>set</CODE> method for the versionModes property.
	 * 
	 * <p>
	 * For example, to add a new item, do as follows:
	 * 
	 * <pre>
	 * getVersionModes().add(newItem);
	 * </pre>
	 * 
	 * 
	 * <p>
	 * Objects of the following type(s) are allowed in the list
	 * {@link VersionMode }
	 * 
	 * 
	 */
	public List<String> getVersionModes() {
		if (mode == null) {
			mode = new ArrayList<String>();
		}
		return this.mode;
	}	
}

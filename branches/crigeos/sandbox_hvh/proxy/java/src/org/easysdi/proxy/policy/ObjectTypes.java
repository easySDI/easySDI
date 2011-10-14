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


@XmlAccessorType(XmlAccessType.FIELD)
@XmlType(name = "", propOrder = { "ObjectType" })
@XmlRootElement(name = "ObjectTypes")
public class ObjectTypes implements Serializable {

	/**
	 * 
	 */
	private static final long serialVersionUID = 1L;
	@XmlElement(name = "ObjectType", required = false)
	protected List<String> ObjectType;
	@XmlAttribute(name = "All")
	protected Boolean all;
	
	@Override
	public int hashCode() {
		int hashCode = 0;
		hashCode += ((all) ? 5909 : 4919);
		if (ObjectType != null)
			hashCode += ObjectType.hashCode();
		return hashCode;
	}

	/**
	 * Gets the value of the ObjectTypes property.
	 * 
	 * <p>
	 * This accessor method returns a reference to the live list, not a
	 * snapshot. Therefore any modification you make to the returned list will
	 * be present inside the JAXB object. This is why there is not a
	 * <CODE>set</CODE> method for the ObjectTypes property.
	 * 
	 * <p>
	 * For example, to add a new item, do as follows:
	 * 
	 * <pre>
	 * getObjectTypes().add(newItem);
	 * </pre>
	 * 
	 * 
	 * <p>
	 * Objects of the following type(s) are allowed in the list
	 * {@link String }
	 * 
	 * 
	 */
	public List<String> getObjectTypes() {
		if (ObjectType == null) {
			ObjectType = new ArrayList<String>();
		}
		return this.ObjectType;
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

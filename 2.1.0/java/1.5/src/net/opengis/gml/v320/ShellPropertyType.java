//
// This file was generated by the JavaTM Architecture for XML Binding(JAXB) Reference Implementation, vhudson-jaxb-ri-2.1-520 
// See <a href="http://java.sun.com/xml/jaxb">http://java.sun.com/xml/jaxb</a> 
// Any modifications to this file will be lost upon recompilation of the source schema. 
// Generated on: 2008.03.07 at 04:51:42 PM CET 
//


package net.opengis.gml.v320;

import javax.xml.bind.annotation.XmlAccessType;
import javax.xml.bind.annotation.XmlAccessorType;
import javax.xml.bind.annotation.XmlElement;
import javax.xml.bind.annotation.XmlType;


/**
 * A property with the content model of gml:ShellPropertyType encapsulates a shell to represent a component of a solid boundary.
 * 
 * <p>Java class for ShellPropertyType complex type.
 * 
 * <p>The following schema fragment specifies the expected content contained within this class.
 * 
 * <pre>
 * &lt;complexType name="ShellPropertyType">
 *   &lt;complexContent>
 *     &lt;restriction base="{http://www.w3.org/2001/XMLSchema}anyType">
 *       &lt;sequence>
 *         &lt;element ref="{http://www.opengis.net/gml}Shell"/>
 *       &lt;/sequence>
 *     &lt;/restriction>
 *   &lt;/complexContent>
 * &lt;/complexType>
 * </pre>
 * 
 * 
 */
@XmlAccessorType(XmlAccessType.FIELD)
@XmlType(name = "ShellPropertyType", propOrder = {
    "shell"
})
public class ShellPropertyType {

    @XmlElement(name = "Shell", required = true)
    protected ShellType shell;

    /**
     * Gets the value of the shell property.
     * 
     * @return
     *     possible object is
     *     {@link ShellType }
     *     
     */
    public ShellType getShell() {
        return shell;
    }

    /**
     * Sets the value of the shell property.
     * 
     * @param value
     *     allowed object is
     *     {@link ShellType }
     *     
     */
    public void setShell(ShellType value) {
        this.shell = value;
    }

}

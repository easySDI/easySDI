//
// This file was generated by the JavaTM Architecture for XML Binding(JAXB) Reference Implementation, vhudson-jaxb-ri-2.1-520 
// See <a href="http://java.sun.com/xml/jaxb">http://java.sun.com/xml/jaxb</a> 
// Any modifications to this file will be lost upon recompilation of the source schema. 
// Generated on: 2008.03.13 at 04:39:39 PM CET 
//


package ch.interlis.interlis2.GM03V18;

import javax.xml.bind.annotation.XmlAccessType;
import javax.xml.bind.annotation.XmlAccessorType;
import javax.xml.bind.annotation.XmlElement;
import javax.xml.bind.annotation.XmlSeeAlso;
import javax.xml.bind.annotation.XmlType;


/**
 * <p>Java class for GM03Comprehensive.Comprehensive.resourceFormatMD_Identification complex type.
 * 
 * <p>The following schema fragment specifies the expected content contained within this class.
 * 
 * <pre>
 * &lt;complexType name="GM03Comprehensive.Comprehensive.resourceFormatMD_Identification">
 *   &lt;complexContent>
 *     &lt;restriction base="{http://www.w3.org/2001/XMLSchema}anyType">
 *       &lt;sequence>
 *         &lt;element name="resourceFormat" type="{http://www.interlis.ch/INTERLIS2.2}RoleType"/>
 *         &lt;element name="MD_Identification" type="{http://www.interlis.ch/INTERLIS2.2}RoleType"/>
 *       &lt;/sequence>
 *     &lt;/restriction>
 *   &lt;/complexContent>
 * &lt;/complexType>
 * </pre>
 * 
 * 
 */
@XmlAccessorType(XmlAccessType.FIELD)
@XmlType(name = "GM03Comprehensive.Comprehensive.resourceFormatMD_Identification", propOrder = {
    "resourceFormat",
    "mdIdentification"
})
@XmlSeeAlso({
    ch.interlis.interlis2.GM03V18.GM03ComprehensiveComprehensive.GM03ComprehensiveComprehensiveResourceFormatMDIdentification.class
})
public class GM03ComprehensiveComprehensiveResourceFormatMDIdentification {

    @XmlElement(required = true)
    protected RoleType resourceFormat;
    @XmlElement(name = "MD_Identification", required = true)
    protected RoleType mdIdentification;

    /**
     * Gets the value of the resourceFormat property.
     * 
     * @return
     *     possible object is
     *     {@link RoleType }
     *     
     */
    public RoleType getResourceFormat() {
        return resourceFormat;
    }

    /**
     * Sets the value of the resourceFormat property.
     * 
     * @param value
     *     allowed object is
     *     {@link RoleType }
     *     
     */
    public void setResourceFormat(RoleType value) {
        this.resourceFormat = value;
    }

    /**
     * Gets the value of the mdIdentification property.
     * 
     * @return
     *     possible object is
     *     {@link RoleType }
     *     
     */
    public RoleType getMDIdentification() {
        return mdIdentification;
    }

    /**
     * Sets the value of the mdIdentification property.
     * 
     * @param value
     *     allowed object is
     *     {@link RoleType }
     *     
     */
    public void setMDIdentification(RoleType value) {
        this.mdIdentification = value;
    }

}

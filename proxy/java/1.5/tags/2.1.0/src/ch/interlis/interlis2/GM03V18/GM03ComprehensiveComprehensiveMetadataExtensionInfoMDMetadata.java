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
 * <p>Java class for GM03Comprehensive.Comprehensive.metadataExtensionInfoMD_Metadata complex type.
 * 
 * <p>The following schema fragment specifies the expected content contained within this class.
 * 
 * <pre>
 * &lt;complexType name="GM03Comprehensive.Comprehensive.metadataExtensionInfoMD_Metadata">
 *   &lt;complexContent>
 *     &lt;restriction base="{http://www.w3.org/2001/XMLSchema}anyType">
 *       &lt;sequence>
 *         &lt;element name="metadataExtensionInfo" type="{http://www.interlis.ch/INTERLIS2.2}RoleType"/>
 *         &lt;element name="MD_Metadata" type="{http://www.interlis.ch/INTERLIS2.2}RoleType"/>
 *       &lt;/sequence>
 *     &lt;/restriction>
 *   &lt;/complexContent>
 * &lt;/complexType>
 * </pre>
 * 
 * 
 */
@XmlAccessorType(XmlAccessType.FIELD)
@XmlType(name = "GM03Comprehensive.Comprehensive.metadataExtensionInfoMD_Metadata", propOrder = {
    "metadataExtensionInfo",
    "mdMetadata"
})
@XmlSeeAlso({
    ch.interlis.interlis2.GM03V18.GM03ComprehensiveComprehensive.GM03ComprehensiveComprehensiveMetadataExtensionInfoMDMetadata.class
})
public class GM03ComprehensiveComprehensiveMetadataExtensionInfoMDMetadata {

    @XmlElement(required = true)
    protected RoleType metadataExtensionInfo;
    @XmlElement(name = "MD_Metadata", required = true)
    protected RoleType mdMetadata;

    /**
     * Gets the value of the metadataExtensionInfo property.
     * 
     * @return
     *     possible object is
     *     {@link RoleType }
     *     
     */
    public RoleType getMetadataExtensionInfo() {
        return metadataExtensionInfo;
    }

    /**
     * Sets the value of the metadataExtensionInfo property.
     * 
     * @param value
     *     allowed object is
     *     {@link RoleType }
     *     
     */
    public void setMetadataExtensionInfo(RoleType value) {
        this.metadataExtensionInfo = value;
    }

    /**
     * Gets the value of the mdMetadata property.
     * 
     * @return
     *     possible object is
     *     {@link RoleType }
     *     
     */
    public RoleType getMDMetadata() {
        return mdMetadata;
    }

    /**
     * Sets the value of the mdMetadata property.
     * 
     * @param value
     *     allowed object is
     *     {@link RoleType }
     *     
     */
    public void setMDMetadata(RoleType value) {
        this.mdMetadata = value;
    }

}

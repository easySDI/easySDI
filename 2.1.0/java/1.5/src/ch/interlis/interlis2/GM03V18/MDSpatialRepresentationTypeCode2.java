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
import javax.xml.bind.annotation.XmlType;


/**
 * <p>Java class for GM03Core.Core.MD_SpatialRepresentationTypeCode_ complex type.
 * 
 * <p>The following schema fragment specifies the expected content contained within this class.
 * 
 * <pre>
 * &lt;complexType name="GM03Core.Core.MD_SpatialRepresentationTypeCode_">
 *   &lt;complexContent>
 *     &lt;restriction base="{http://www.w3.org/2001/XMLSchema}anyType">
 *       &lt;sequence>
 *         &lt;element name="value" type="{http://www.interlis.ch/INTERLIS2.2}GM03Core.Core.MD_SpatialRepresentationTypeCode"/>
 *       &lt;/sequence>
 *     &lt;/restriction>
 *   &lt;/complexContent>
 * &lt;/complexType>
 * </pre>
 * 
 * 
 */
@XmlAccessorType(XmlAccessType.FIELD)
@XmlType(name = "GM03Core.Core.MD_SpatialRepresentationTypeCode_", propOrder = {
    "value"
})
public class MDSpatialRepresentationTypeCode2 {

    @XmlElement(required = true)
    protected GM03CoreCoreMDSpatialRepresentationTypeCode value;

    /**
     * Gets the value of the value property.
     * 
     * @return
     *     possible object is
     *     {@link GM03CoreCoreMDSpatialRepresentationTypeCode }
     *     
     */
    public GM03CoreCoreMDSpatialRepresentationTypeCode getValue() {
        return value;
    }

    /**
     * Sets the value of the value property.
     * 
     * @param value
     *     allowed object is
     *     {@link GM03CoreCoreMDSpatialRepresentationTypeCode }
     *     
     */
    public void setValue(GM03CoreCoreMDSpatialRepresentationTypeCode value) {
        this.value = value;
    }

}

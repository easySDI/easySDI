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
 * <p>Java class for GM03Core.Core.EX_ExtentverticalElement complex type.
 * 
 * <p>The following schema fragment specifies the expected content contained within this class.
 * 
 * <pre>
 * &lt;complexType name="GM03Core.Core.EX_ExtentverticalElement">
 *   &lt;complexContent>
 *     &lt;restriction base="{http://www.w3.org/2001/XMLSchema}anyType">
 *       &lt;sequence>
 *         &lt;element name="EX_Extent" type="{http://www.interlis.ch/INTERLIS2.2}RoleType"/>
 *         &lt;element name="verticalElement" type="{http://www.interlis.ch/INTERLIS2.2}RoleType"/>
 *       &lt;/sequence>
 *     &lt;/restriction>
 *   &lt;/complexContent>
 * &lt;/complexType>
 * </pre>
 * 
 * 
 */
@XmlAccessorType(XmlAccessType.FIELD)
@XmlType(name = "GM03Core.Core.EX_ExtentverticalElement", propOrder = {
    "exExtent",
    "verticalElement"
})
@XmlSeeAlso({
    ch.interlis.interlis2.GM03V18.GM03ComprehensiveComprehensive.GM03CoreCoreEXExtentverticalElement.class,
    ch.interlis.interlis2.GM03V18.GM03CoreCore.GM03CoreCoreEXExtentverticalElement.class
})
public class GM03CoreCoreEXExtentverticalElement {

    @XmlElement(name = "EX_Extent", required = true)
    protected RoleType exExtent;
    @XmlElement(required = true)
    protected RoleType verticalElement;

    /**
     * Gets the value of the exExtent property.
     * 
     * @return
     *     possible object is
     *     {@link RoleType }
     *     
     */
    public RoleType getEXExtent() {
        return exExtent;
    }

    /**
     * Sets the value of the exExtent property.
     * 
     * @param value
     *     allowed object is
     *     {@link RoleType }
     *     
     */
    public void setEXExtent(RoleType value) {
        this.exExtent = value;
    }

    /**
     * Gets the value of the verticalElement property.
     * 
     * @return
     *     possible object is
     *     {@link RoleType }
     *     
     */
    public RoleType getVerticalElement() {
        return verticalElement;
    }

    /**
     * Sets the value of the verticalElement property.
     * 
     * @param value
     *     allowed object is
     *     {@link RoleType }
     *     
     */
    public void setVerticalElement(RoleType value) {
        this.verticalElement = value;
    }

}

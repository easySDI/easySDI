//
// This file was generated by the JavaTM Architecture for XML Binding(JAXB) Reference Implementation, vhudson-jaxb-ri-2.1-520 
// See <a href="http://java.sun.com/xml/jaxb">http://java.sun.com/xml/jaxb</a> 
// Any modifications to this file will be lost upon recompilation of the source schema. 
// Generated on: 2008.03.07 at 04:51:42 PM CET 
//


package org.isotc211._2005.gmd;

import javax.xml.bind.annotation.XmlAccessType;
import javax.xml.bind.annotation.XmlAccessorType;
import javax.xml.bind.annotation.XmlElement;
import javax.xml.bind.annotation.XmlType;
import org.isotc211._2005.gco.AbstractObjectType;
import org.isotc211._2005.gco.DatePropertyType;


/**
 * <p>Java class for CI_Date_Type complex type.
 * 
 * <p>The following schema fragment specifies the expected content contained within this class.
 * 
 * <pre>
 * &lt;complexType name="CI_Date_Type">
 *   &lt;complexContent>
 *     &lt;extension base="{http://www.isotc211.org/2005/gco}AbstractObject_Type">
 *       &lt;sequence>
 *         &lt;element name="date" type="{http://www.isotc211.org/2005/gco}Date_PropertyType"/>
 *         &lt;element name="dateType" type="{http://www.isotc211.org/2005/gmd}CI_DateTypeCode_PropertyType"/>
 *       &lt;/sequence>
 *     &lt;/extension>
 *   &lt;/complexContent>
 * &lt;/complexType>
 * </pre>
 * 
 * 
 */
@XmlAccessorType(XmlAccessType.FIELD)
@XmlType(name = "CI_Date_Type", propOrder = {
    "date",
    "dateType"
})
public class CIDateType
    extends AbstractObjectType
{

    @XmlElement(required = true)
    protected DatePropertyType date;
    @XmlElement(required = true)
    protected CIDateTypeCodePropertyType dateType;

    /**
     * Gets the value of the date property.
     * 
     * @return
     *     possible object is
     *     {@link DatePropertyType }
     *     
     */
    public DatePropertyType getDate() {
        return date;
    }

    /**
     * Sets the value of the date property.
     * 
     * @param value
     *     allowed object is
     *     {@link DatePropertyType }
     *     
     */
    public void setDate(DatePropertyType value) {
        this.date = value;
    }

    /**
     * Gets the value of the dateType property.
     * 
     * @return
     *     possible object is
     *     {@link CIDateTypeCodePropertyType }
     *     
     */
    public CIDateTypeCodePropertyType getDateType() {
        return dateType;
    }

    /**
     * Sets the value of the dateType property.
     * 
     * @param value
     *     allowed object is
     *     {@link CIDateTypeCodePropertyType }
     *     
     */
    public void setDateType(CIDateTypeCodePropertyType value) {
        this.dateType = value;
    }

}

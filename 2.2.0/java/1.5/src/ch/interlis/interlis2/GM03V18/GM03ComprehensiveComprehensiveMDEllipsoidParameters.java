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
 * <p>Java class for GM03Comprehensive.Comprehensive.MD_EllipsoidParameters complex type.
 * 
 * <p>The following schema fragment specifies the expected content contained within this class.
 * 
 * <pre>
 * &lt;complexType name="GM03Comprehensive.Comprehensive.MD_EllipsoidParameters">
 *   &lt;complexContent>
 *     &lt;restriction base="{http://www.w3.org/2001/XMLSchema}anyType">
 *       &lt;sequence>
 *         &lt;element name="semiMajorAxis" type="{http://www.interlis.ch/INTERLIS2.2}GM03Core.Core.Real"/>
 *         &lt;element name="axisUnits" type="{http://www.interlis.ch/INTERLIS2.2}GM03Core.Core.UomLength"/>
 *         &lt;element name="denominatorOfFlatteningRatio" type="{http://www.interlis.ch/INTERLIS2.2}GM03Core.Core.Real" minOccurs="0"/>
 *       &lt;/sequence>
 *     &lt;/restriction>
 *   &lt;/complexContent>
 * &lt;/complexType>
 * </pre>
 * 
 * 
 */
@XmlAccessorType(XmlAccessType.FIELD)
@XmlType(name = "GM03Comprehensive.Comprehensive.MD_EllipsoidParameters", propOrder = {
    "semiMajorAxis",
    "axisUnits",
    "denominatorOfFlatteningRatio"
})
@XmlSeeAlso({
    ch.interlis.interlis2.GM03V18.GM03ComprehensiveComprehensive.GM03ComprehensiveComprehensiveMDEllipsoidParameters.class
})
public class GM03ComprehensiveComprehensiveMDEllipsoidParameters {

    protected double semiMajorAxis;
    @XmlElement(required = true)
    protected String axisUnits;
    protected Double denominatorOfFlatteningRatio;

    /**
     * Gets the value of the semiMajorAxis property.
     * 
     */
    public double getSemiMajorAxis() {
        return semiMajorAxis;
    }

    /**
     * Sets the value of the semiMajorAxis property.
     * 
     */
    public void setSemiMajorAxis(double value) {
        this.semiMajorAxis = value;
    }

    /**
     * Gets the value of the axisUnits property.
     * 
     * @return
     *     possible object is
     *     {@link String }
     *     
     */
    public String getAxisUnits() {
        return axisUnits;
    }

    /**
     * Sets the value of the axisUnits property.
     * 
     * @param value
     *     allowed object is
     *     {@link String }
     *     
     */
    public void setAxisUnits(String value) {
        this.axisUnits = value;
    }

    /**
     * Gets the value of the denominatorOfFlatteningRatio property.
     * 
     * @return
     *     possible object is
     *     {@link Double }
     *     
     */
    public Double getDenominatorOfFlatteningRatio() {
        return denominatorOfFlatteningRatio;
    }

    /**
     * Sets the value of the denominatorOfFlatteningRatio property.
     * 
     * @param value
     *     allowed object is
     *     {@link Double }
     *     
     */
    public void setDenominatorOfFlatteningRatio(Double value) {
        this.denominatorOfFlatteningRatio = value;
    }

}

//
// This file was generated by the JavaTM Architecture for XML Binding(JAXB) Reference Implementation, vhudson-jaxb-ri-2.1-520 
// See <a href="http://java.sun.com/xml/jaxb">http://java.sun.com/xml/jaxb</a> 
// Any modifications to this file will be lost upon recompilation of the source schema. 
// Generated on: 2008.03.07 at 04:51:42 PM CET 
//


package org.isotc211._2005.gmd;

import javax.xml.bind.annotation.XmlAccessType;
import javax.xml.bind.annotation.XmlAccessorType;
import javax.xml.bind.annotation.XmlSeeAlso;
import javax.xml.bind.annotation.XmlType;
import org.isotc211._2005.gco.AbstractObjectType;
import org.isotc211._2005.gco.CharacterStringPropertyType;
import org.isotc211._2005.gco.MemberNamePropertyType;


/**
 * Set of adjacent wavelengths in the electro-magnetic spectrum with a common characteristic, such as the visible band
 * 
 * <p>Java class for MD_RangeDimension_Type complex type.
 * 
 * <p>The following schema fragment specifies the expected content contained within this class.
 * 
 * <pre>
 * &lt;complexType name="MD_RangeDimension_Type">
 *   &lt;complexContent>
 *     &lt;extension base="{http://www.isotc211.org/2005/gco}AbstractObject_Type">
 *       &lt;sequence>
 *         &lt;element name="sequenceIdentifier" type="{http://www.isotc211.org/2005/gco}MemberName_PropertyType" minOccurs="0"/>
 *         &lt;element name="descriptor" type="{http://www.isotc211.org/2005/gco}CharacterString_PropertyType" minOccurs="0"/>
 *       &lt;/sequence>
 *     &lt;/extension>
 *   &lt;/complexContent>
 * &lt;/complexType>
 * </pre>
 * 
 * 
 */
@XmlAccessorType(XmlAccessType.FIELD)
@XmlType(name = "MD_RangeDimension_Type", propOrder = {
    "sequenceIdentifier",
    "descriptor"
})
@XmlSeeAlso({
    MDBandType.class
})
public class MDRangeDimensionType
    extends AbstractObjectType
{

    protected MemberNamePropertyType sequenceIdentifier;
    protected CharacterStringPropertyType descriptor;

    /**
     * Gets the value of the sequenceIdentifier property.
     * 
     * @return
     *     possible object is
     *     {@link MemberNamePropertyType }
     *     
     */
    public MemberNamePropertyType getSequenceIdentifier() {
        return sequenceIdentifier;
    }

    /**
     * Sets the value of the sequenceIdentifier property.
     * 
     * @param value
     *     allowed object is
     *     {@link MemberNamePropertyType }
     *     
     */
    public void setSequenceIdentifier(MemberNamePropertyType value) {
        this.sequenceIdentifier = value;
    }

    /**
     * Gets the value of the descriptor property.
     * 
     * @return
     *     possible object is
     *     {@link CharacterStringPropertyType }
     *     
     */
    public CharacterStringPropertyType getDescriptor() {
        return descriptor;
    }

    /**
     * Sets the value of the descriptor property.
     * 
     * @param value
     *     allowed object is
     *     {@link CharacterStringPropertyType }
     *     
     */
    public void setDescriptor(CharacterStringPropertyType value) {
        this.descriptor = value;
    }

}

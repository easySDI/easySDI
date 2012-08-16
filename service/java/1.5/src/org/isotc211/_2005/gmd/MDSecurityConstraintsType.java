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
import org.isotc211._2005.gco.CharacterStringPropertyType;


/**
 * Handling restrictions imposed on the dataset because of national security, privacy, or other concerns
 * 
 * <p>Java class for MD_SecurityConstraints_Type complex type.
 * 
 * <p>The following schema fragment specifies the expected content contained within this class.
 * 
 * <pre>
 * &lt;complexType name="MD_SecurityConstraints_Type">
 *   &lt;complexContent>
 *     &lt;extension base="{http://www.isotc211.org/2005/gmd}MD_Constraints_Type">
 *       &lt;sequence>
 *         &lt;element name="classification" type="{http://www.isotc211.org/2005/gmd}MD_ClassificationCode_PropertyType"/>
 *         &lt;element name="userNote" type="{http://www.isotc211.org/2005/gco}CharacterString_PropertyType" minOccurs="0"/>
 *         &lt;element name="classificationSystem" type="{http://www.isotc211.org/2005/gco}CharacterString_PropertyType" minOccurs="0"/>
 *         &lt;element name="handlingDescription" type="{http://www.isotc211.org/2005/gco}CharacterString_PropertyType" minOccurs="0"/>
 *       &lt;/sequence>
 *     &lt;/extension>
 *   &lt;/complexContent>
 * &lt;/complexType>
 * </pre>
 * 
 * 
 */
@XmlAccessorType(XmlAccessType.FIELD)
@XmlType(name = "MD_SecurityConstraints_Type", propOrder = {
    "classification",
    "userNote",
    "classificationSystem",
    "handlingDescription"
})
public class MDSecurityConstraintsType
    extends MDConstraintsType
{

    @XmlElement(required = true)
    protected MDClassificationCodePropertyType classification;
    protected CharacterStringPropertyType userNote;
    protected CharacterStringPropertyType classificationSystem;
    protected CharacterStringPropertyType handlingDescription;

    /**
     * Gets the value of the classification property.
     * 
     * @return
     *     possible object is
     *     {@link MDClassificationCodePropertyType }
     *     
     */
    public MDClassificationCodePropertyType getClassification() {
        return classification;
    }

    /**
     * Sets the value of the classification property.
     * 
     * @param value
     *     allowed object is
     *     {@link MDClassificationCodePropertyType }
     *     
     */
    public void setClassification(MDClassificationCodePropertyType value) {
        this.classification = value;
    }

    /**
     * Gets the value of the userNote property.
     * 
     * @return
     *     possible object is
     *     {@link CharacterStringPropertyType }
     *     
     */
    public CharacterStringPropertyType getUserNote() {
        return userNote;
    }

    /**
     * Sets the value of the userNote property.
     * 
     * @param value
     *     allowed object is
     *     {@link CharacterStringPropertyType }
     *     
     */
    public void setUserNote(CharacterStringPropertyType value) {
        this.userNote = value;
    }

    /**
     * Gets the value of the classificationSystem property.
     * 
     * @return
     *     possible object is
     *     {@link CharacterStringPropertyType }
     *     
     */
    public CharacterStringPropertyType getClassificationSystem() {
        return classificationSystem;
    }

    /**
     * Sets the value of the classificationSystem property.
     * 
     * @param value
     *     allowed object is
     *     {@link CharacterStringPropertyType }
     *     
     */
    public void setClassificationSystem(CharacterStringPropertyType value) {
        this.classificationSystem = value;
    }

    /**
     * Gets the value of the handlingDescription property.
     * 
     * @return
     *     possible object is
     *     {@link CharacterStringPropertyType }
     *     
     */
    public CharacterStringPropertyType getHandlingDescription() {
        return handlingDescription;
    }

    /**
     * Sets the value of the handlingDescription property.
     * 
     * @param value
     *     allowed object is
     *     {@link CharacterStringPropertyType }
     *     
     */
    public void setHandlingDescription(CharacterStringPropertyType value) {
        this.handlingDescription = value;
    }

}

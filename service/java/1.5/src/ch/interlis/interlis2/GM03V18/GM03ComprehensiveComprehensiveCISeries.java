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
 * <p>Java class for GM03Comprehensive.Comprehensive.CI_Series complex type.
 * 
 * <p>The following schema fragment specifies the expected content contained within this class.
 * 
 * <pre>
 * &lt;complexType name="GM03Comprehensive.Comprehensive.CI_Series">
 *   &lt;complexContent>
 *     &lt;restriction base="{http://www.w3.org/2001/XMLSchema}anyType">
 *       &lt;sequence>
 *         &lt;element name="page" type="{http://www.interlis.ch/INTERLIS2.2}GM03Core.Core.CharacterString" minOccurs="0"/>
 *         &lt;element name="issueIdentification" minOccurs="0">
 *           &lt;complexType>
 *             &lt;complexContent>
 *               &lt;restriction base="{http://www.w3.org/2001/XMLSchema}anyType">
 *                 &lt;sequence>
 *                   &lt;element name="GM03Core.Core.PT_FreeText" type="{http://www.interlis.ch/INTERLIS2.2}GM03Core.Core.PT_FreeText"/>
 *                 &lt;/sequence>
 *               &lt;/restriction>
 *             &lt;/complexContent>
 *           &lt;/complexType>
 *         &lt;/element>
 *         &lt;element name="name" minOccurs="0">
 *           &lt;complexType>
 *             &lt;complexContent>
 *               &lt;restriction base="{http://www.w3.org/2001/XMLSchema}anyType">
 *                 &lt;sequence>
 *                   &lt;element name="GM03Core.Core.PT_FreeText" type="{http://www.interlis.ch/INTERLIS2.2}GM03Core.Core.PT_FreeText"/>
 *                 &lt;/sequence>
 *               &lt;/restriction>
 *             &lt;/complexContent>
 *           &lt;/complexType>
 *         &lt;/element>
 *       &lt;/sequence>
 *     &lt;/restriction>
 *   &lt;/complexContent>
 * &lt;/complexType>
 * </pre>
 * 
 * 
 */
@XmlAccessorType(XmlAccessType.FIELD)
@XmlType(name = "GM03Comprehensive.Comprehensive.CI_Series", propOrder = {
    "page",
    "issueIdentification",
    "name"
})
@XmlSeeAlso({
    ch.interlis.interlis2.GM03V18.GM03ComprehensiveComprehensive.GM03ComprehensiveComprehensiveCISeries.class
})
public class GM03ComprehensiveComprehensiveCISeries {

    protected String page;
    protected GM03ComprehensiveComprehensiveCISeries.IssueIdentification issueIdentification;
    protected GM03ComprehensiveComprehensiveCISeries.Name name;

    /**
     * Gets the value of the page property.
     * 
     * @return
     *     possible object is
     *     {@link String }
     *     
     */
    public String getPage() {
        return page;
    }

    /**
     * Sets the value of the page property.
     * 
     * @param value
     *     allowed object is
     *     {@link String }
     *     
     */
    public void setPage(String value) {
        this.page = value;
    }

    /**
     * Gets the value of the issueIdentification property.
     * 
     * @return
     *     possible object is
     *     {@link GM03ComprehensiveComprehensiveCISeries.IssueIdentification }
     *     
     */
    public GM03ComprehensiveComprehensiveCISeries.IssueIdentification getIssueIdentification() {
        return issueIdentification;
    }

    /**
     * Sets the value of the issueIdentification property.
     * 
     * @param value
     *     allowed object is
     *     {@link GM03ComprehensiveComprehensiveCISeries.IssueIdentification }
     *     
     */
    public void setIssueIdentification(GM03ComprehensiveComprehensiveCISeries.IssueIdentification value) {
        this.issueIdentification = value;
    }

    /**
     * Gets the value of the name property.
     * 
     * @return
     *     possible object is
     *     {@link GM03ComprehensiveComprehensiveCISeries.Name }
     *     
     */
    public GM03ComprehensiveComprehensiveCISeries.Name getName() {
        return name;
    }

    /**
     * Sets the value of the name property.
     * 
     * @param value
     *     allowed object is
     *     {@link GM03ComprehensiveComprehensiveCISeries.Name }
     *     
     */
    public void setName(GM03ComprehensiveComprehensiveCISeries.Name value) {
        this.name = value;
    }


    /**
     * <p>Java class for anonymous complex type.
     * 
     * <p>The following schema fragment specifies the expected content contained within this class.
     * 
     * <pre>
     * &lt;complexType>
     *   &lt;complexContent>
     *     &lt;restriction base="{http://www.w3.org/2001/XMLSchema}anyType">
     *       &lt;sequence>
     *         &lt;element name="GM03Core.Core.PT_FreeText" type="{http://www.interlis.ch/INTERLIS2.2}GM03Core.Core.PT_FreeText"/>
     *       &lt;/sequence>
     *     &lt;/restriction>
     *   &lt;/complexContent>
     * &lt;/complexType>
     * </pre>
     * 
     * 
     */
    @XmlAccessorType(XmlAccessType.FIELD)
    @XmlType(name = "", propOrder = {
        "gm03CoreCorePTFreeText"
    })
    public static class IssueIdentification {

        @XmlElement(name = "GM03Core.Core.PT_FreeText", required = true)
        protected GM03CoreCorePTFreeText gm03CoreCorePTFreeText;

        /**
         * Gets the value of the gm03CoreCorePTFreeText property.
         * 
         * @return
         *     possible object is
         *     {@link GM03CoreCorePTFreeText }
         *     
         */
        public GM03CoreCorePTFreeText getGM03CoreCorePTFreeText() {
            return gm03CoreCorePTFreeText;
        }

        /**
         * Sets the value of the gm03CoreCorePTFreeText property.
         * 
         * @param value
         *     allowed object is
         *     {@link GM03CoreCorePTFreeText }
         *     
         */
        public void setGM03CoreCorePTFreeText(GM03CoreCorePTFreeText value) {
            this.gm03CoreCorePTFreeText = value;
        }

    }


    /**
     * <p>Java class for anonymous complex type.
     * 
     * <p>The following schema fragment specifies the expected content contained within this class.
     * 
     * <pre>
     * &lt;complexType>
     *   &lt;complexContent>
     *     &lt;restriction base="{http://www.w3.org/2001/XMLSchema}anyType">
     *       &lt;sequence>
     *         &lt;element name="GM03Core.Core.PT_FreeText" type="{http://www.interlis.ch/INTERLIS2.2}GM03Core.Core.PT_FreeText"/>
     *       &lt;/sequence>
     *     &lt;/restriction>
     *   &lt;/complexContent>
     * &lt;/complexType>
     * </pre>
     * 
     * 
     */
    @XmlAccessorType(XmlAccessType.FIELD)
    @XmlType(name = "", propOrder = {
        "gm03CoreCorePTFreeText"
    })
    public static class Name {

        @XmlElement(name = "GM03Core.Core.PT_FreeText", required = true)
        protected GM03CoreCorePTFreeText gm03CoreCorePTFreeText;

        /**
         * Gets the value of the gm03CoreCorePTFreeText property.
         * 
         * @return
         *     possible object is
         *     {@link GM03CoreCorePTFreeText }
         *     
         */
        public GM03CoreCorePTFreeText getGM03CoreCorePTFreeText() {
            return gm03CoreCorePTFreeText;
        }

        /**
         * Sets the value of the gm03CoreCorePTFreeText property.
         * 
         * @param value
         *     allowed object is
         *     {@link GM03CoreCorePTFreeText }
         *     
         */
        public void setGM03CoreCorePTFreeText(GM03CoreCorePTFreeText value) {
            this.gm03CoreCorePTFreeText = value;
        }

    }

}

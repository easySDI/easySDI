//
// This file was generated by the JavaTM Architecture for XML Binding(JAXB) Reference Implementation, vhudson-jaxb-ri-2.1-520 
// See <a href="http://java.sun.com/xml/jaxb">http://java.sun.com/xml/jaxb</a> 
// Any modifications to this file will be lost upon recompilation of the source schema. 
// Generated on: 2008.03.13 at 04:39:39 PM CET 
//


package ch.interlis.interlis2.GM03V18;

import java.util.ArrayList;
import java.util.List;
import javax.xml.bind.annotation.XmlAccessType;
import javax.xml.bind.annotation.XmlAccessorType;
import javax.xml.bind.annotation.XmlAttribute;
import javax.xml.bind.annotation.XmlElement;
import javax.xml.bind.annotation.XmlSeeAlso;
import javax.xml.bind.annotation.XmlType;


/**
 * <p>Java class for GM03Comprehensive.Comprehensive.DQ_ConceptualConsistency complex type.
 * 
 * <p>The following schema fragment specifies the expected content contained within this class.
 * 
 * <pre>
 * &lt;complexType name="GM03Comprehensive.Comprehensive.DQ_ConceptualConsistency">
 *   &lt;complexContent>
 *     &lt;restriction base="{http://www.w3.org/2001/XMLSchema}anyType">
 *       &lt;sequence>
 *         &lt;element name="nameOfMeasure" minOccurs="0">
 *           &lt;complexType>
 *             &lt;complexContent>
 *               &lt;restriction base="{http://www.w3.org/2001/XMLSchema}anyType">
 *                 &lt;sequence>
 *                   &lt;element name="GM03Core.Core.CharacterString_" type="{http://www.interlis.ch/INTERLIS2.2}GM03Core.Core.CharacterString_" maxOccurs="unbounded"/>
 *                 &lt;/sequence>
 *               &lt;/restriction>
 *             &lt;/complexContent>
 *           &lt;/complexType>
 *         &lt;/element>
 *         &lt;element name="measureDescription" type="{http://www.interlis.ch/INTERLIS2.2}GM03Core.Core.CharacterString" minOccurs="0"/>
 *         &lt;element name="evaluationMethodType" type="{http://www.interlis.ch/INTERLIS2.2}GM03Comprehensive.Comprehensive.DQ_EvaluationMethodTypeCode" minOccurs="0"/>
 *         &lt;element name="evaluationMethodDescription" type="{http://www.interlis.ch/INTERLIS2.2}GM03Core.Core.CharacterString" minOccurs="0"/>
 *         &lt;element name="dateTime" minOccurs="0">
 *           &lt;complexType>
 *             &lt;complexContent>
 *               &lt;restriction base="{http://www.w3.org/2001/XMLSchema}anyType">
 *                 &lt;sequence>
 *                   &lt;element name="GM03Core.Core.DateTime_" type="{http://www.interlis.ch/INTERLIS2.2}GM03Core.Core.DateTime_" maxOccurs="unbounded"/>
 *                 &lt;/sequence>
 *               &lt;/restriction>
 *             &lt;/complexContent>
 *           &lt;/complexType>
 *         &lt;/element>
 *         &lt;element name="measureIdentification" minOccurs="0">
 *           &lt;complexType>
 *             &lt;complexContent>
 *               &lt;restriction base="{http://www.w3.org/2001/XMLSchema}anyType">
 *                 &lt;attribute name="REF" type="{http://www.interlis.ch/INTERLIS2.2}IliID" />
 *                 &lt;attribute name="EXTREF" type="{http://www.interlis.ch/INTERLIS2.2}IliID" />
 *                 &lt;attribute name="BID" type="{http://www.interlis.ch/INTERLIS2.2}IliID" />
 *                 &lt;attribute name="NEXT_TID" type="{http://www.interlis.ch/INTERLIS2.2}IliID" />
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
@XmlType(name = "GM03Comprehensive.Comprehensive.DQ_ConceptualConsistency", propOrder = {
    "nameOfMeasure",
    "measureDescription",
    "evaluationMethodType",
    "evaluationMethodDescription",
    "dateTime",
    "measureIdentification"
})
@XmlSeeAlso({
    ch.interlis.interlis2.GM03V18.GM03ComprehensiveComprehensive.GM03ComprehensiveComprehensiveDQConceptualConsistency.class
})
public class GM03ComprehensiveComprehensiveDQConceptualConsistency {

    protected GM03ComprehensiveComprehensiveDQConceptualConsistency.NameOfMeasure nameOfMeasure;
    protected String measureDescription;
    protected GM03ComprehensiveComprehensiveDQEvaluationMethodTypeCode evaluationMethodType;
    protected String evaluationMethodDescription;
    protected GM03ComprehensiveComprehensiveDQConceptualConsistency.DateTime dateTime;
    protected GM03ComprehensiveComprehensiveDQConceptualConsistency.MeasureIdentification measureIdentification;

    /**
     * Gets the value of the nameOfMeasure property.
     * 
     * @return
     *     possible object is
     *     {@link GM03ComprehensiveComprehensiveDQConceptualConsistency.NameOfMeasure }
     *     
     */
    public GM03ComprehensiveComprehensiveDQConceptualConsistency.NameOfMeasure getNameOfMeasure() {
        return nameOfMeasure;
    }

    /**
     * Sets the value of the nameOfMeasure property.
     * 
     * @param value
     *     allowed object is
     *     {@link GM03ComprehensiveComprehensiveDQConceptualConsistency.NameOfMeasure }
     *     
     */
    public void setNameOfMeasure(GM03ComprehensiveComprehensiveDQConceptualConsistency.NameOfMeasure value) {
        this.nameOfMeasure = value;
    }

    /**
     * Gets the value of the measureDescription property.
     * 
     * @return
     *     possible object is
     *     {@link String }
     *     
     */
    public String getMeasureDescription() {
        return measureDescription;
    }

    /**
     * Sets the value of the measureDescription property.
     * 
     * @param value
     *     allowed object is
     *     {@link String }
     *     
     */
    public void setMeasureDescription(String value) {
        this.measureDescription = value;
    }

    /**
     * Gets the value of the evaluationMethodType property.
     * 
     * @return
     *     possible object is
     *     {@link GM03ComprehensiveComprehensiveDQEvaluationMethodTypeCode }
     *     
     */
    public GM03ComprehensiveComprehensiveDQEvaluationMethodTypeCode getEvaluationMethodType() {
        return evaluationMethodType;
    }

    /**
     * Sets the value of the evaluationMethodType property.
     * 
     * @param value
     *     allowed object is
     *     {@link GM03ComprehensiveComprehensiveDQEvaluationMethodTypeCode }
     *     
     */
    public void setEvaluationMethodType(GM03ComprehensiveComprehensiveDQEvaluationMethodTypeCode value) {
        this.evaluationMethodType = value;
    }

    /**
     * Gets the value of the evaluationMethodDescription property.
     * 
     * @return
     *     possible object is
     *     {@link String }
     *     
     */
    public String getEvaluationMethodDescription() {
        return evaluationMethodDescription;
    }

    /**
     * Sets the value of the evaluationMethodDescription property.
     * 
     * @param value
     *     allowed object is
     *     {@link String }
     *     
     */
    public void setEvaluationMethodDescription(String value) {
        this.evaluationMethodDescription = value;
    }

    /**
     * Gets the value of the dateTime property.
     * 
     * @return
     *     possible object is
     *     {@link GM03ComprehensiveComprehensiveDQConceptualConsistency.DateTime }
     *     
     */
    public GM03ComprehensiveComprehensiveDQConceptualConsistency.DateTime getDateTime() {
        return dateTime;
    }

    /**
     * Sets the value of the dateTime property.
     * 
     * @param value
     *     allowed object is
     *     {@link GM03ComprehensiveComprehensiveDQConceptualConsistency.DateTime }
     *     
     */
    public void setDateTime(GM03ComprehensiveComprehensiveDQConceptualConsistency.DateTime value) {
        this.dateTime = value;
    }

    /**
     * Gets the value of the measureIdentification property.
     * 
     * @return
     *     possible object is
     *     {@link GM03ComprehensiveComprehensiveDQConceptualConsistency.MeasureIdentification }
     *     
     */
    public GM03ComprehensiveComprehensiveDQConceptualConsistency.MeasureIdentification getMeasureIdentification() {
        return measureIdentification;
    }

    /**
     * Sets the value of the measureIdentification property.
     * 
     * @param value
     *     allowed object is
     *     {@link GM03ComprehensiveComprehensiveDQConceptualConsistency.MeasureIdentification }
     *     
     */
    public void setMeasureIdentification(GM03ComprehensiveComprehensiveDQConceptualConsistency.MeasureIdentification value) {
        this.measureIdentification = value;
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
     *         &lt;element name="GM03Core.Core.DateTime_" type="{http://www.interlis.ch/INTERLIS2.2}GM03Core.Core.DateTime_" maxOccurs="unbounded"/>
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
        "gm03CoreCoreDateTime"
    })
    public static class DateTime {

        @XmlElement(name = "GM03Core.Core.DateTime_", required = true)
        protected List<GM03CoreCoreDateTime2> gm03CoreCoreDateTime;

        /**
         * Gets the value of the gm03CoreCoreDateTime property.
         * 
         * <p>
         * This accessor method returns a reference to the live list,
         * not a snapshot. Therefore any modification you make to the
         * returned list will be present inside the JAXB object.
         * This is why there is not a <CODE>set</CODE> method for the gm03CoreCoreDateTime property.
         * 
         * <p>
         * For example, to add a new item, do as follows:
         * <pre>
         *    getGM03CoreCoreDateTime().add(newItem);
         * </pre>
         * 
         * 
         * <p>
         * Objects of the following type(s) are allowed in the list
         * {@link GM03CoreCoreDateTime2 }
         * 
         * 
         */
        public List<GM03CoreCoreDateTime2> getGM03CoreCoreDateTime() {
            if (gm03CoreCoreDateTime == null) {
                gm03CoreCoreDateTime = new ArrayList<GM03CoreCoreDateTime2>();
            }
            return this.gm03CoreCoreDateTime;
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
     *       &lt;attribute name="REF" type="{http://www.interlis.ch/INTERLIS2.2}IliID" />
     *       &lt;attribute name="EXTREF" type="{http://www.interlis.ch/INTERLIS2.2}IliID" />
     *       &lt;attribute name="BID" type="{http://www.interlis.ch/INTERLIS2.2}IliID" />
     *       &lt;attribute name="NEXT_TID" type="{http://www.interlis.ch/INTERLIS2.2}IliID" />
     *     &lt;/restriction>
     *   &lt;/complexContent>
     * &lt;/complexType>
     * </pre>
     * 
     * 
     */
    @XmlAccessorType(XmlAccessType.FIELD)
    @XmlType(name = "")
    public static class MeasureIdentification {

        @XmlAttribute(name = "REF")
        protected String ref;
        @XmlAttribute(name = "EXTREF")
        protected String extref;
        @XmlAttribute(name = "BID")
        protected String bid;
        @XmlAttribute(name = "NEXT_TID")
        protected String nexttid;

        /**
         * Gets the value of the ref property.
         * 
         * @return
         *     possible object is
         *     {@link String }
         *     
         */
        public String getREF() {
            return ref;
        }

        /**
         * Sets the value of the ref property.
         * 
         * @param value
         *     allowed object is
         *     {@link String }
         *     
         */
        public void setREF(String value) {
            this.ref = value;
        }

        /**
         * Gets the value of the extref property.
         * 
         * @return
         *     possible object is
         *     {@link String }
         *     
         */
        public String getEXTREF() {
            return extref;
        }

        /**
         * Sets the value of the extref property.
         * 
         * @param value
         *     allowed object is
         *     {@link String }
         *     
         */
        public void setEXTREF(String value) {
            this.extref = value;
        }

        /**
         * Gets the value of the bid property.
         * 
         * @return
         *     possible object is
         *     {@link String }
         *     
         */
        public String getBID() {
            return bid;
        }

        /**
         * Sets the value of the bid property.
         * 
         * @param value
         *     allowed object is
         *     {@link String }
         *     
         */
        public void setBID(String value) {
            this.bid = value;
        }

        /**
         * Gets the value of the nexttid property.
         * 
         * @return
         *     possible object is
         *     {@link String }
         *     
         */
        public String getNEXTTID() {
            return nexttid;
        }

        /**
         * Sets the value of the nexttid property.
         * 
         * @param value
         *     allowed object is
         *     {@link String }
         *     
         */
        public void setNEXTTID(String value) {
            this.nexttid = value;
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
     *         &lt;element name="GM03Core.Core.CharacterString_" type="{http://www.interlis.ch/INTERLIS2.2}GM03Core.Core.CharacterString_" maxOccurs="unbounded"/>
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
        "gm03CoreCoreCharacterString"
    })
    public static class NameOfMeasure {

        @XmlElement(name = "GM03Core.Core.CharacterString_", required = true)
        protected List<GM03CoreCoreCharacterString> gm03CoreCoreCharacterString;

        /**
         * Gets the value of the gm03CoreCoreCharacterString property.
         * 
         * <p>
         * This accessor method returns a reference to the live list,
         * not a snapshot. Therefore any modification you make to the
         * returned list will be present inside the JAXB object.
         * This is why there is not a <CODE>set</CODE> method for the gm03CoreCoreCharacterString property.
         * 
         * <p>
         * For example, to add a new item, do as follows:
         * <pre>
         *    getGM03CoreCoreCharacterString().add(newItem);
         * </pre>
         * 
         * 
         * <p>
         * Objects of the following type(s) are allowed in the list
         * {@link GM03CoreCoreCharacterString }
         * 
         * 
         */
        public List<GM03CoreCoreCharacterString> getGM03CoreCoreCharacterString() {
            if (gm03CoreCoreCharacterString == null) {
                gm03CoreCoreCharacterString = new ArrayList<GM03CoreCoreCharacterString>();
            }
            return this.gm03CoreCoreCharacterString;
        }

    }

}

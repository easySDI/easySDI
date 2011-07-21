//
// This file was generated by the JavaTM Architecture for XML Binding(JAXB) Reference Implementation, vhudson-jaxb-ri-2.1-520 
// See <a href="http://java.sun.com/xml/jaxb">http://java.sun.com/xml/jaxb</a> 
// Any modifications to this file will be lost upon recompilation of the source schema. 
// Generated on: 2008.03.07 at 04:52:04 PM CET 
//


package net.opengis.cat.csw.v201;

import javax.xml.bind.annotation.XmlAccessType;
import javax.xml.bind.annotation.XmlAccessorType;
import javax.xml.bind.annotation.XmlElement;
import javax.xml.bind.annotation.XmlType;


/**
 * <p>Java class for HarvestResponseType complex type.
 * 
 * <p>The following schema fragment specifies the expected content contained within this class.
 * 
 * <pre>
 * &lt;complexType name="HarvestResponseType">
 *   &lt;complexContent>
 *     &lt;restriction base="{http://www.w3.org/2001/XMLSchema}anyType">
 *       &lt;choice>
 *         &lt;element ref="{http://www.opengis.net/cat/csw}Acknowledgement"/>
 *         &lt;element ref="{http://www.opengis.net/cat/csw}TransactionResponse"/>
 *       &lt;/choice>
 *     &lt;/restriction>
 *   &lt;/complexContent>
 * &lt;/complexType>
 * </pre>
 * 
 * 
 */
@XmlAccessorType(XmlAccessType.FIELD)
@XmlType(name = "HarvestResponseType", propOrder = {
    "acknowledgement",
    "transactionResponse"
})
public class HarvestResponseType {

    @XmlElement(name = "Acknowledgement")
    protected AcknowledgementType acknowledgement;
    @XmlElement(name = "TransactionResponse")
    protected TransactionResponseType transactionResponse;

    /**
     * Gets the value of the acknowledgement property.
     * 
     * @return
     *     possible object is
     *     {@link AcknowledgementType }
     *     
     */
    public AcknowledgementType getAcknowledgement() {
        return acknowledgement;
    }

    /**
     * Sets the value of the acknowledgement property.
     * 
     * @param value
     *     allowed object is
     *     {@link AcknowledgementType }
     *     
     */
    public void setAcknowledgement(AcknowledgementType value) {
        this.acknowledgement = value;
    }

    /**
     * Gets the value of the transactionResponse property.
     * 
     * @return
     *     possible object is
     *     {@link TransactionResponseType }
     *     
     */
    public TransactionResponseType getTransactionResponse() {
        return transactionResponse;
    }

    /**
     * Sets the value of the transactionResponse property.
     * 
     * @param value
     *     allowed object is
     *     {@link TransactionResponseType }
     *     
     */
    public void setTransactionResponse(TransactionResponseType value) {
        this.transactionResponse = value;
    }

}

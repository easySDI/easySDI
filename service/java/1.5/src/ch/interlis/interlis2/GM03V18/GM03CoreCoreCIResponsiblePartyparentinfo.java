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
 * <p>Java class for GM03Core.Core.CI_ResponsiblePartyparentinfo complex type.
 * 
 * <p>The following schema fragment specifies the expected content contained within this class.
 * 
 * <pre>
 * &lt;complexType name="GM03Core.Core.CI_ResponsiblePartyparentinfo">
 *   &lt;complexContent>
 *     &lt;restriction base="{http://www.w3.org/2001/XMLSchema}anyType">
 *       &lt;sequence>
 *         &lt;element name="parentResponsibleParty" type="{http://www.interlis.ch/INTERLIS2.2}RoleType"/>
 *         &lt;element name="CI_ResponsibleParty" type="{http://www.interlis.ch/INTERLIS2.2}RoleType"/>
 *       &lt;/sequence>
 *     &lt;/restriction>
 *   &lt;/complexContent>
 * &lt;/complexType>
 * </pre>
 * 
 * 
 */
@XmlAccessorType(XmlAccessType.FIELD)
@XmlType(name = "GM03Core.Core.CI_ResponsiblePartyparentinfo", propOrder = {
    "parentResponsibleParty",
    "ciResponsibleParty"
})
@XmlSeeAlso({
    ch.interlis.interlis2.GM03V18.GM03ComprehensiveComprehensive.GM03CoreCoreCIResponsiblePartyparentinfo.class,
    ch.interlis.interlis2.GM03V18.GM03CoreCore.GM03CoreCoreCIResponsiblePartyparentinfo.class
})
public class GM03CoreCoreCIResponsiblePartyparentinfo {

    @XmlElement(required = true)
    protected RoleType parentResponsibleParty;
    @XmlElement(name = "CI_ResponsibleParty", required = true)
    protected RoleType ciResponsibleParty;

    /**
     * Gets the value of the parentResponsibleParty property.
     * 
     * @return
     *     possible object is
     *     {@link RoleType }
     *     
     */
    public RoleType getParentResponsibleParty() {
        return parentResponsibleParty;
    }

    /**
     * Sets the value of the parentResponsibleParty property.
     * 
     * @param value
     *     allowed object is
     *     {@link RoleType }
     *     
     */
    public void setParentResponsibleParty(RoleType value) {
        this.parentResponsibleParty = value;
    }

    /**
     * Gets the value of the ciResponsibleParty property.
     * 
     * @return
     *     possible object is
     *     {@link RoleType }
     *     
     */
    public RoleType getCIResponsibleParty() {
        return ciResponsibleParty;
    }

    /**
     * Sets the value of the ciResponsibleParty property.
     * 
     * @param value
     *     allowed object is
     *     {@link RoleType }
     *     
     */
    public void setCIResponsibleParty(RoleType value) {
        this.ciResponsibleParty = value;
    }

}

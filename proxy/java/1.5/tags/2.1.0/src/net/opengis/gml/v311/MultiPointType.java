//
// This file was generated by the JavaTM Architecture for XML Binding(JAXB) Reference Implementation, vhudson-jaxb-ri-2.1-520 
// See <a href="http://java.sun.com/xml/jaxb">http://java.sun.com/xml/jaxb</a> 
// Any modifications to this file will be lost upon recompilation of the source schema. 
// Generated on: 2008.03.13 at 09:48:29 AM CET 
//


package net.opengis.gml.v311;

import java.util.ArrayList;
import java.util.List;
import javax.xml.bind.annotation.XmlAccessType;
import javax.xml.bind.annotation.XmlAccessorType;
import javax.xml.bind.annotation.XmlType;


/**
 * A MultiPoint is defined by one or more Points, referenced through pointMember elements.
 * 
 * <p>Java class for MultiPointType complex type.
 * 
 * <p>The following schema fragment specifies the expected content contained within this class.
 * 
 * <pre>
 * &lt;complexType name="MultiPointType">
 *   &lt;complexContent>
 *     &lt;extension base="{http://www.opengis.net/gml}AbstractGeometricAggregateType">
 *       &lt;sequence>
 *         &lt;element ref="{http://www.opengis.net/gml}pointMember" maxOccurs="unbounded" minOccurs="0"/>
 *         &lt;element ref="{http://www.opengis.net/gml}pointMembers" minOccurs="0"/>
 *       &lt;/sequence>
 *     &lt;/extension>
 *   &lt;/complexContent>
 * &lt;/complexType>
 * </pre>
 * 
 * 
 */
@XmlAccessorType(XmlAccessType.FIELD)
@XmlType(name = "MultiPointType", propOrder = {
    "pointMember",
    "pointMembers"
})
public class MultiPointType
    extends AbstractGeometricAggregateType
{

    protected List<PointPropertyType> pointMember;
    protected PointArrayPropertyType pointMembers;

    /**
     * Gets the value of the pointMember property.
     * 
     * <p>
     * This accessor method returns a reference to the live list,
     * not a snapshot. Therefore any modification you make to the
     * returned list will be present inside the JAXB object.
     * This is why there is not a <CODE>set</CODE> method for the pointMember property.
     * 
     * <p>
     * For example, to add a new item, do as follows:
     * <pre>
     *    getPointMember().add(newItem);
     * </pre>
     * 
     * 
     * <p>
     * Objects of the following type(s) are allowed in the list
     * {@link PointPropertyType }
     * 
     * 
     */
    public List<PointPropertyType> getPointMember() {
        if (pointMember == null) {
            pointMember = new ArrayList<PointPropertyType>();
        }
        return this.pointMember;
    }

    /**
     * Gets the value of the pointMembers property.
     * 
     * @return
     *     possible object is
     *     {@link PointArrayPropertyType }
     *     
     */
    public PointArrayPropertyType getPointMembers() {
        return pointMembers;
    }

    /**
     * Sets the value of the pointMembers property.
     * 
     * @param value
     *     allowed object is
     *     {@link PointArrayPropertyType }
     *     
     */
    public void setPointMembers(PointArrayPropertyType value) {
        this.pointMembers = value;
    }

}

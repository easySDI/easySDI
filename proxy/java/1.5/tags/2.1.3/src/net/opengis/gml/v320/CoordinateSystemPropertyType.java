//
// This file was generated by the JavaTM Architecture for XML Binding(JAXB) Reference Implementation, vhudson-jaxb-ri-2.1-520 
// See <a href="http://java.sun.com/xml/jaxb">http://java.sun.com/xml/jaxb</a> 
// Any modifications to this file will be lost upon recompilation of the source schema. 
// Generated on: 2008.03.07 at 04:51:42 PM CET 
//


package net.opengis.gml.v320;

import java.util.ArrayList;
import java.util.List;
import javax.xml.bind.JAXBElement;
import javax.xml.bind.annotation.XmlAccessType;
import javax.xml.bind.annotation.XmlAccessorType;
import javax.xml.bind.annotation.XmlAttribute;
import javax.xml.bind.annotation.XmlElementRef;
import javax.xml.bind.annotation.XmlSchemaType;
import javax.xml.bind.annotation.XmlType;
import org.isotc211._2005.gmx.MLAffineCSType;
import org.isotc211._2005.gmx.MLCartesianCSType;
import org.isotc211._2005.gmx.MLCylindricalCSType;
import org.isotc211._2005.gmx.MLEllipsoidalCSType;
import org.isotc211._2005.gmx.MLLinearCSType;
import org.isotc211._2005.gmx.MLPolarCSType;
import org.isotc211._2005.gmx.MLSphericalCSType;
import org.isotc211._2005.gmx.MLTimeCSType;
import org.isotc211._2005.gmx.MLUserDefinedCSType;
import org.isotc211._2005.gmx.MLVerticalCSType;


/**
 * gml:CoordinateSystemPropertyType is a property type for association roles to a coordinate system, either referencing or containing the definition of that coordinate system.
 * 
 * <p>Java class for CoordinateSystemPropertyType complex type.
 * 
 * <p>The following schema fragment specifies the expected content contained within this class.
 * 
 * <pre>
 * &lt;complexType name="CoordinateSystemPropertyType">
 *   &lt;complexContent>
 *     &lt;restriction base="{http://www.w3.org/2001/XMLSchema}anyType">
 *       &lt;sequence minOccurs="0">
 *         &lt;element ref="{http://www.opengis.net/gml}AbstractCoordinateSystem"/>
 *       &lt;/sequence>
 *       &lt;attGroup ref="{http://www.opengis.net/gml}AssociationAttributeGroup"/>
 *     &lt;/restriction>
 *   &lt;/complexContent>
 * &lt;/complexType>
 * </pre>
 * 
 * 
 */
@XmlAccessorType(XmlAccessType.FIELD)
@XmlType(name = "CoordinateSystemPropertyType", propOrder = {
    "abstractCoordinateSystem"
})
public class CoordinateSystemPropertyType {

    @XmlElementRef(name = "AbstractCoordinateSystem", namespace = "http://www.opengis.net/gml", type = JAXBElement.class)
    protected JAXBElement<? extends AbstractCoordinateSystemType> abstractCoordinateSystem;
    @XmlAttribute
    protected List<String> nilReason;
    @XmlAttribute(namespace = "http://www.opengis.net/gml")
    @XmlSchemaType(name = "anyURI")
    protected String remoteSchema;
    @XmlAttribute(namespace = "http://www.w3.org/1999/xlink")
    protected String type;
    @XmlAttribute(namespace = "http://www.w3.org/1999/xlink")
    @XmlSchemaType(name = "anyURI")
    protected String href;
    @XmlAttribute(namespace = "http://www.w3.org/1999/xlink")
    @XmlSchemaType(name = "anyURI")
    protected String role;
    @XmlAttribute(namespace = "http://www.w3.org/1999/xlink")
    @XmlSchemaType(name = "anyURI")
    protected String arcrole;
    @XmlAttribute(namespace = "http://www.w3.org/1999/xlink")
    protected String title;
    @XmlAttribute(namespace = "http://www.w3.org/1999/xlink")
    protected String show;
    @XmlAttribute(namespace = "http://www.w3.org/1999/xlink")
    protected String actuate;

    /**
     * Gets the value of the abstractCoordinateSystem property.
     * 
     * @return
     *     possible object is
     *     {@link JAXBElement }{@code <}{@link MLSphericalCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link TemporalCSType }{@code >}
     *     {@link EllipsoidalCS2 }
     *     {@link JAXBElement }{@code <}{@link AbstractCoordinateSystemType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLLinearCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link ObliqueCartesianCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLUserDefinedCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link CylindricalCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link UserDefinedCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLAffineCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLTimeCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLCylindricalCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLEllipsoidalCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link LinearCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link AffineCSType }{@code >}
     *     {@link CartesianCS2 }
     *     {@link SphericalCS2 }
     *     {@link JAXBElement }{@code <}{@link VerticalCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link TimeCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLCartesianCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link PolarCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLPolarCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLVerticalCSType }{@code >}
     *     
     */
    public JAXBElement<? extends AbstractCoordinateSystemType> getAbstractCoordinateSystem() {
        return abstractCoordinateSystem;
    }

    /**
     * Sets the value of the abstractCoordinateSystem property.
     * 
     * @param value
     *     allowed object is
     *     {@link JAXBElement }{@code <}{@link MLSphericalCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link TemporalCSType }{@code >}
     *     {@link EllipsoidalCS2 }
     *     {@link JAXBElement }{@code <}{@link AbstractCoordinateSystemType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLLinearCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link ObliqueCartesianCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLUserDefinedCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link CylindricalCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link UserDefinedCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLAffineCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLTimeCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLCylindricalCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLEllipsoidalCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link LinearCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link AffineCSType }{@code >}
     *     {@link CartesianCS2 }
     *     {@link SphericalCS2 }
     *     {@link JAXBElement }{@code <}{@link VerticalCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link TimeCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLCartesianCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link PolarCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLPolarCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLVerticalCSType }{@code >}
     *     
     */
    public void setAbstractCoordinateSystem(JAXBElement<? extends AbstractCoordinateSystemType> value) {
        this.abstractCoordinateSystem = ((JAXBElement<? extends AbstractCoordinateSystemType> ) value);
    }

    /**
     * Gets the value of the nilReason property.
     * 
     * <p>
     * This accessor method returns a reference to the live list,
     * not a snapshot. Therefore any modification you make to the
     * returned list will be present inside the JAXB object.
     * This is why there is not a <CODE>set</CODE> method for the nilReason property.
     * 
     * <p>
     * For example, to add a new item, do as follows:
     * <pre>
     *    getNilReason().add(newItem);
     * </pre>
     * 
     * 
     * <p>
     * Objects of the following type(s) are allowed in the list
     * {@link String }
     * 
     * 
     */
    public List<String> getNilReason() {
        if (nilReason == null) {
            nilReason = new ArrayList<String>();
        }
        return this.nilReason;
    }

    /**
     * Gets the value of the remoteSchema property.
     * 
     * @return
     *     possible object is
     *     {@link String }
     *     
     */
    public String getRemoteSchema() {
        return remoteSchema;
    }

    /**
     * Sets the value of the remoteSchema property.
     * 
     * @param value
     *     allowed object is
     *     {@link String }
     *     
     */
    public void setRemoteSchema(String value) {
        this.remoteSchema = value;
    }

    /**
     * Gets the value of the type property.
     * 
     * @return
     *     possible object is
     *     {@link String }
     *     
     */
    public String getType() {
        if (type == null) {
            return "simple";
        } else {
            return type;
        }
    }

    /**
     * Sets the value of the type property.
     * 
     * @param value
     *     allowed object is
     *     {@link String }
     *     
     */
    public void setType(String value) {
        this.type = value;
    }

    /**
     * Gets the value of the href property.
     * 
     * @return
     *     possible object is
     *     {@link String }
     *     
     */
    public String getHref() {
        return href;
    }

    /**
     * Sets the value of the href property.
     * 
     * @param value
     *     allowed object is
     *     {@link String }
     *     
     */
    public void setHref(String value) {
        this.href = value;
    }

    /**
     * Gets the value of the role property.
     * 
     * @return
     *     possible object is
     *     {@link String }
     *     
     */
    public String getRole() {
        return role;
    }

    /**
     * Sets the value of the role property.
     * 
     * @param value
     *     allowed object is
     *     {@link String }
     *     
     */
    public void setRole(String value) {
        this.role = value;
    }

    /**
     * Gets the value of the arcrole property.
     * 
     * @return
     *     possible object is
     *     {@link String }
     *     
     */
    public String getArcrole() {
        return arcrole;
    }

    /**
     * Sets the value of the arcrole property.
     * 
     * @param value
     *     allowed object is
     *     {@link String }
     *     
     */
    public void setArcrole(String value) {
        this.arcrole = value;
    }

    /**
     * Gets the value of the title property.
     * 
     * @return
     *     possible object is
     *     {@link String }
     *     
     */
    public String getTitle() {
        return title;
    }

    /**
     * Sets the value of the title property.
     * 
     * @param value
     *     allowed object is
     *     {@link String }
     *     
     */
    public void setTitle(String value) {
        this.title = value;
    }

    /**
     * Gets the value of the show property.
     * 
     * @return
     *     possible object is
     *     {@link String }
     *     
     */
    public String getShow() {
        return show;
    }

    /**
     * Sets the value of the show property.
     * 
     * @param value
     *     allowed object is
     *     {@link String }
     *     
     */
    public void setShow(String value) {
        this.show = value;
    }

    /**
     * Gets the value of the actuate property.
     * 
     * @return
     *     possible object is
     *     {@link String }
     *     
     */
    public String getActuate() {
        return actuate;
    }

    /**
     * Sets the value of the actuate property.
     * 
     * @param value
     *     allowed object is
     *     {@link String }
     *     
     */
    public void setActuate(String value) {
        this.actuate = value;
    }

}

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
import org.isotc211._2005.gmx.CodeDefinitionType;
import org.isotc211._2005.gmx.CodeListDictionaryType;
import org.isotc211._2005.gmx.MLAffineCSType;
import org.isotc211._2005.gmx.MLBaseUnitType;
import org.isotc211._2005.gmx.MLCartesianCSType;
import org.isotc211._2005.gmx.MLCodeDefinitionType;
import org.isotc211._2005.gmx.MLCodeListDictionaryType;
import org.isotc211._2005.gmx.MLCompoundCRSType;
import org.isotc211._2005.gmx.MLConcatenatedOperationType;
import org.isotc211._2005.gmx.MLConventionalUnitType;
import org.isotc211._2005.gmx.MLConversionType;
import org.isotc211._2005.gmx.MLCoordinateSystemAxisType;
import org.isotc211._2005.gmx.MLCylindricalCSType;
import org.isotc211._2005.gmx.MLDerivedCRSType;
import org.isotc211._2005.gmx.MLDerivedUnitType;
import org.isotc211._2005.gmx.MLEllipsoidType;
import org.isotc211._2005.gmx.MLEllipsoidalCSType;
import org.isotc211._2005.gmx.MLEngineeringCRSType;
import org.isotc211._2005.gmx.MLEngineeringDatumType;
import org.isotc211._2005.gmx.MLGeodeticCRSType;
import org.isotc211._2005.gmx.MLGeodeticDatumType;
import org.isotc211._2005.gmx.MLImageCRSType;
import org.isotc211._2005.gmx.MLImageDatumType;
import org.isotc211._2005.gmx.MLLinearCSType;
import org.isotc211._2005.gmx.MLOperationMethodType;
import org.isotc211._2005.gmx.MLOperationParameterGroupType;
import org.isotc211._2005.gmx.MLOperationParameterType;
import org.isotc211._2005.gmx.MLPassThroughOperationType;
import org.isotc211._2005.gmx.MLPolarCSType;
import org.isotc211._2005.gmx.MLPrimeMeridianType;
import org.isotc211._2005.gmx.MLProjectedCRSType;
import org.isotc211._2005.gmx.MLSphericalCSType;
import org.isotc211._2005.gmx.MLTemporalCRSType;
import org.isotc211._2005.gmx.MLTemporalDatumType;
import org.isotc211._2005.gmx.MLTimeCSType;
import org.isotc211._2005.gmx.MLTransformationType;
import org.isotc211._2005.gmx.MLUnitDefinitionType;
import org.isotc211._2005.gmx.MLUserDefinedCSType;
import org.isotc211._2005.gmx.MLVerticalCRSType;
import org.isotc211._2005.gmx.MLVerticalCSType;
import org.isotc211._2005.gmx.MLVerticalDatumType;


/**
 * <p>Java class for DictionaryEntryType complex type.
 * 
 * <p>The following schema fragment specifies the expected content contained within this class.
 * 
 * <pre>
 * &lt;complexType name="DictionaryEntryType">
 *   &lt;complexContent>
 *     &lt;extension base="{http://www.opengis.net/gml}AbstractMemberType">
 *       &lt;sequence minOccurs="0">
 *         &lt;element ref="{http://www.opengis.net/gml}Definition"/>
 *       &lt;/sequence>
 *       &lt;attGroup ref="{http://www.opengis.net/gml}AssociationAttributeGroup"/>
 *     &lt;/extension>
 *   &lt;/complexContent>
 * &lt;/complexType>
 * </pre>
 * 
 * 
 */
@XmlAccessorType(XmlAccessType.FIELD)
@XmlType(name = "DictionaryEntryType", propOrder = {
    "definition"
})
public class DictionaryEntryType
    extends AbstractMemberType
{

    @XmlElementRef(name = "Definition", namespace = "http://www.opengis.net/gml", type = JAXBElement.class)
    protected JAXBElement<? extends DefinitionType> definition;
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
     * Gets the value of the definition property.
     * 
     * @return
     *     possible object is
     *     {@link TemporlDatum2 }
     *     {@link JAXBElement }{@code <}{@link TemporalCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link AbstractGeneralTransformationType }{@code >}
     *     {@link JAXBElement }{@code <}{@link TimeCoordinateSystemType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLUserDefinedCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link ProjectedCRSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLPrimeMeridianType }{@code >}
     *     {@link JAXBElement }{@code <}{@link DefinitionProxyType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLImageDatumType }{@code >}
     *     {@link JAXBElement }{@code <}{@link DerivedUnitType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLCartesianCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLCoordinateSystemAxisType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLVerticalCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLEllipsoidType }{@code >}
     *     {@link JAXBElement }{@code <}{@link TimeReferenceSystemType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLGeodeticDatumType }{@code >}
     *     {@link JAXBElement }{@code <}{@link BaseUnitType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLDerivedUnitType }{@code >}
     *     {@link JAXBElement }{@code <}{@link PassThroughOperationType }{@code >}
     *     {@link JAXBElement }{@code <}{@link GeographicCRSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link CompoundCRSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link UserDefinedCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLProjectedCRSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link AffineCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link LinearCSType }{@code >}
     *     {@link SphericalCS2 }
     *     {@link JAXBElement }{@code <}{@link PolarCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLVerticalDatumType }{@code >}
     *     {@link JAXBElement }{@code <}{@link AbstractGeneralOperationParameterType }{@code >}
     *     {@link JAXBElement }{@code <}{@link CoordinateSystemAxisType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLTemporalCRSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLPassThroughOperationType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLOperationParameterGroupType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLAffineCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link OperationMethodType }{@code >}
     *     {@link JAXBElement }{@code <}{@link ConcatenatedOperationType }{@code >}
     *     {@link JAXBElement }{@code <}{@link AbstractCoordinateOperationType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLOperationParameterType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLUnitDefinitionType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLSphericalCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link TimeCalendarType }{@code >}
     *     {@link JAXBElement }{@code <}{@link DerivedCRSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link AbstractCoordinateOperationType }{@code >}
     *     {@link JAXBElement }{@code <}{@link DefinitionType }{@code >}
     *     {@link JAXBElement }{@code <}{@link GeodeticCRSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link TimeCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link AbstractGeneralDerivedCRSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLEngineeringDatumType }{@code >}
     *     {@link JAXBElement }{@code <}{@link AbstractDatumType }{@code >}
     *     {@link JAXBElement }{@code <}{@link EngineeringCRSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link ConventionalUnitType }{@code >}
     *     {@link JAXBElement }{@code <}{@link OperationParameterGroupType }{@code >}
     *     {@link PrimeMeridian2 }
     *     {@link JAXBElement }{@code <}{@link MLConventionalUnitType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLConcatenatedOperationType }{@code >}
     *     {@link ImageDatum2 }
     *     {@link JAXBElement }{@code <}{@link MLBaseUnitType }{@code >}
     *     {@link JAXBElement }{@code <}{@link VerticalCSType }{@code >}
     *     {@link EllipsoidalCS2 }
     *     {@link JAXBElement }{@code <}{@link MLTemporalDatumType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLVerticalCRSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link ObliqueCartesianCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLCompoundCRSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLEngineeringCRSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link CodeListDictionaryType }{@code >}
     *     {@link JAXBElement }{@code <}{@link VerticalCRSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link TimeClockType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLPolarCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLCodeListDictionaryType }{@code >}
     *     {@link JAXBElement }{@code <}{@link AbstractCRSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link OperationParameterType }{@code >}
     *     {@link JAXBElement }{@code <}{@link TemporalCRSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link AbstractCoordinateSystemType }{@code >}
     *     {@link JAXBElement }{@code <}{@link CodeDefinitionType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLConversionType }{@code >}
     *     {@link VerticalDatum2 }
     *     {@link JAXBElement }{@code <}{@link MLCylindricalCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link ImageCRSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLEllipsoidalCSType }{@code >}
     *     {@link Conversion2 }
     *     {@link JAXBElement }{@code <}{@link AbstractGeneralConversionType }{@code >}
     *     {@link CartesianCS2 }
     *     {@link Ellipsoid2 }
     *     {@link JAXBElement }{@code <}{@link EngineeringDatumType }{@code >}
     *     {@link JAXBElement }{@code <}{@link DictionaryType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLImageCRSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLGeodeticCRSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLLinearCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link AbstractCoordinateOperationType }{@code >}
     *     {@link JAXBElement }{@code <}{@link TimeOrdinalReferenceSystemType }{@code >}
     *     {@link JAXBElement }{@code <}{@link TransformationType }{@code >}
     *     {@link JAXBElement }{@code <}{@link CylindricalCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link AbstractCRSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLOperationMethodType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLTimeCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link UnitDefinitionType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLDerivedCRSType }{@code >}
     *     {@link GeodeticDatum2 }
     *     {@link JAXBElement }{@code <}{@link MLCodeDefinitionType }{@code >}
     *     {@link JAXBElement }{@code <}{@link GeocentricCRSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLTransformationType }{@code >}
     *     {@link JAXBElement }{@code <}{@link DictionaryType }{@code >}
     *     
     */
    public JAXBElement<? extends DefinitionType> getDefinition() {
        return definition;
    }

    /**
     * Sets the value of the definition property.
     * 
     * @param value
     *     allowed object is
     *     {@link TemporlDatum2 }
     *     {@link JAXBElement }{@code <}{@link TemporalCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link AbstractGeneralTransformationType }{@code >}
     *     {@link JAXBElement }{@code <}{@link TimeCoordinateSystemType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLUserDefinedCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link ProjectedCRSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLPrimeMeridianType }{@code >}
     *     {@link JAXBElement }{@code <}{@link DefinitionProxyType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLImageDatumType }{@code >}
     *     {@link JAXBElement }{@code <}{@link DerivedUnitType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLCartesianCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLCoordinateSystemAxisType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLVerticalCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLEllipsoidType }{@code >}
     *     {@link JAXBElement }{@code <}{@link TimeReferenceSystemType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLGeodeticDatumType }{@code >}
     *     {@link JAXBElement }{@code <}{@link BaseUnitType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLDerivedUnitType }{@code >}
     *     {@link JAXBElement }{@code <}{@link PassThroughOperationType }{@code >}
     *     {@link JAXBElement }{@code <}{@link GeographicCRSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link CompoundCRSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link UserDefinedCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLProjectedCRSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link AffineCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link LinearCSType }{@code >}
     *     {@link SphericalCS2 }
     *     {@link JAXBElement }{@code <}{@link PolarCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLVerticalDatumType }{@code >}
     *     {@link JAXBElement }{@code <}{@link AbstractGeneralOperationParameterType }{@code >}
     *     {@link JAXBElement }{@code <}{@link CoordinateSystemAxisType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLTemporalCRSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLPassThroughOperationType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLOperationParameterGroupType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLAffineCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link OperationMethodType }{@code >}
     *     {@link JAXBElement }{@code <}{@link ConcatenatedOperationType }{@code >}
     *     {@link JAXBElement }{@code <}{@link AbstractCoordinateOperationType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLOperationParameterType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLUnitDefinitionType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLSphericalCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link TimeCalendarType }{@code >}
     *     {@link JAXBElement }{@code <}{@link DerivedCRSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link AbstractCoordinateOperationType }{@code >}
     *     {@link JAXBElement }{@code <}{@link DefinitionType }{@code >}
     *     {@link JAXBElement }{@code <}{@link GeodeticCRSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link TimeCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link AbstractGeneralDerivedCRSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLEngineeringDatumType }{@code >}
     *     {@link JAXBElement }{@code <}{@link AbstractDatumType }{@code >}
     *     {@link JAXBElement }{@code <}{@link EngineeringCRSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link ConventionalUnitType }{@code >}
     *     {@link JAXBElement }{@code <}{@link OperationParameterGroupType }{@code >}
     *     {@link PrimeMeridian2 }
     *     {@link JAXBElement }{@code <}{@link MLConventionalUnitType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLConcatenatedOperationType }{@code >}
     *     {@link ImageDatum2 }
     *     {@link JAXBElement }{@code <}{@link MLBaseUnitType }{@code >}
     *     {@link JAXBElement }{@code <}{@link VerticalCSType }{@code >}
     *     {@link EllipsoidalCS2 }
     *     {@link JAXBElement }{@code <}{@link MLTemporalDatumType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLVerticalCRSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link ObliqueCartesianCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLCompoundCRSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLEngineeringCRSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link CodeListDictionaryType }{@code >}
     *     {@link JAXBElement }{@code <}{@link VerticalCRSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link TimeClockType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLPolarCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLCodeListDictionaryType }{@code >}
     *     {@link JAXBElement }{@code <}{@link AbstractCRSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link OperationParameterType }{@code >}
     *     {@link JAXBElement }{@code <}{@link TemporalCRSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link AbstractCoordinateSystemType }{@code >}
     *     {@link JAXBElement }{@code <}{@link CodeDefinitionType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLConversionType }{@code >}
     *     {@link VerticalDatum2 }
     *     {@link JAXBElement }{@code <}{@link MLCylindricalCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link ImageCRSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLEllipsoidalCSType }{@code >}
     *     {@link Conversion2 }
     *     {@link JAXBElement }{@code <}{@link AbstractGeneralConversionType }{@code >}
     *     {@link CartesianCS2 }
     *     {@link Ellipsoid2 }
     *     {@link JAXBElement }{@code <}{@link EngineeringDatumType }{@code >}
     *     {@link JAXBElement }{@code <}{@link DictionaryType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLImageCRSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLGeodeticCRSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLLinearCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link AbstractCoordinateOperationType }{@code >}
     *     {@link JAXBElement }{@code <}{@link TimeOrdinalReferenceSystemType }{@code >}
     *     {@link JAXBElement }{@code <}{@link TransformationType }{@code >}
     *     {@link JAXBElement }{@code <}{@link CylindricalCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link AbstractCRSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLOperationMethodType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLTimeCSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link UnitDefinitionType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLDerivedCRSType }{@code >}
     *     {@link GeodeticDatum2 }
     *     {@link JAXBElement }{@code <}{@link MLCodeDefinitionType }{@code >}
     *     {@link JAXBElement }{@code <}{@link GeocentricCRSType }{@code >}
     *     {@link JAXBElement }{@code <}{@link MLTransformationType }{@code >}
     *     {@link JAXBElement }{@code <}{@link DictionaryType }{@code >}
     *     
     */
    public void setDefinition(JAXBElement<? extends DefinitionType> value) {
        this.definition = ((JAXBElement<? extends DefinitionType> ) value);
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

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
 * <p>Java class for ArrayAssociationType complex type.
 * 
 * <p>The following schema fragment specifies the expected content contained within this class.
 * 
 * <pre>
 * &lt;complexType name="ArrayAssociationType">
 *   &lt;complexContent>
 *     &lt;restriction base="{http://www.w3.org/2001/XMLSchema}anyType">
 *       &lt;sequence>
 *         &lt;element ref="{http://www.opengis.net/gml}AbstractObject" maxOccurs="unbounded" minOccurs="0"/>
 *       &lt;/sequence>
 *       &lt;attGroup ref="{http://www.opengis.net/gml}OwnershipAttributeGroup"/>
 *     &lt;/restriction>
 *   &lt;/complexContent>
 * &lt;/complexType>
 * </pre>
 * 
 * 
 */
@XmlAccessorType(XmlAccessType.FIELD)
@XmlType(name = "ArrayAssociationType", propOrder = {
    "abstractObject"
})
public class ArrayAssociationType {

    @XmlElementRef(name = "AbstractObject", namespace = "http://www.opengis.net/gml", type = JAXBElement.class)
    protected List<JAXBElement<?>> abstractObject;
    @XmlAttribute
    protected java.lang.Boolean owns;

    /**
     * Gets the value of the abstractObject property.
     * 
     * <p>
     * This accessor method returns a reference to the live list,
     * not a snapshot. Therefore any modification you make to the
     * returned list will be present inside the JAXB object.
     * This is why there is not a <CODE>set</CODE> method for the abstractObject property.
     * 
     * <p>
     * For example, to add a new item, do as follows:
     * <pre>
     *    getAbstractObject().add(newItem);
     * </pre>
     * 
     * 
     * <p>
     * Objects of the following type(s) are allowed in the list
     * {@link TemporlDatum2 }
     * {@link JAXBElement }{@code <}{@link TemporalCSType }{@code >}
     * {@link JAXBElement }{@code <}{@link AbstractGeneralTransformationType }{@code >}
     * {@link JAXBElement }{@code <}{@link TimeCoordinateSystemType }{@code >}
     * {@link JAXBElement }{@code <}{@link AbstractCurveType }{@code >}
     * {@link JAXBElement }{@code <}{@link MLPrimeMeridianType }{@code >}
     * {@link JAXBElement }{@code <}{@link BagType }{@code >}
     * {@link JAXBElement }{@code <}{@link MLImageDatumType }{@code >}
     * {@link JAXBElement }{@code <}{@link DerivedUnitType }{@code >}
     * {@link JAXBElement }{@code <}{@link MLCartesianCSType }{@code >}
     * {@link JAXBElement }{@code <}{@link Object }{@code >}
     * {@link JAXBElement }{@code <}{@link CompositeCurveType }{@code >}
     * {@link JAXBElement }{@code <}{@link FaceType }{@code >}
     * {@link JAXBElement }{@code <}{@link MLVerticalCSType }{@code >}
     * {@link JAXBElement }{@code <}{@link EnvelopeType }{@code >}
     * {@link JAXBElement }{@code <}{@link MLGeodeticDatumType }{@code >}
     * {@link JAXBElement }{@code <}{@link MLDerivedUnitType }{@code >}
     * {@link JAXBElement }{@code <}{@link AbstractGeometricPrimitiveType }{@code >}
     * {@link JAXBElement }{@code <}{@link TopoComplexType }{@code >}
     * {@link JAXBElement }{@code <}{@link GeographicCRSType }{@code >}
     * {@link JAXBElement }{@code <}{@link AbstractTopoPrimitiveType }{@code >}
     * {@link JAXBElement }{@code <}{@link net.opengis.gml.v320.Boolean }{@code >}
     * {@link JAXBElement }{@code <}{@link AffineCSType }{@code >}
     * {@link JAXBElement }{@code <}{@link ArrayType }{@code >}
     * {@link JAXBElement }{@code <}{@link SolidType }{@code >}
     * {@link JAXBElement }{@code <}{@link ArcByBulgeType }{@code >}
     * {@link JAXBElement }{@code <}{@link List }{@code <}{@link String }{@code >}{@code >}
     * {@link JAXBElement }{@code <}{@link MLVerticalDatumType }{@code >}
     * {@link JAXBElement }{@code <}{@link BSplineType }{@code >}
     * {@link JAXBElement }{@code <}{@link GeodesicStringType }{@code >}
     * {@link JAXBElement }{@code <}{@link AbstractGeneralOperationParameterType }{@code >}
     * {@link JAXBElement }{@code <}{@link MLTemporalCRSType }{@code >}
     * {@link JAXBElement }{@code <}{@link MovingObjectStatusType }{@code >}
     * {@link JAXBElement }{@code <}{@link MLOperationParameterGroupType }{@code >}
     * {@link JAXBElement }{@code <}{@link RectifiedGridCoverageType }{@code >}
     * {@link JAXBElement }{@code <}{@link OperationMethodType }{@code >}
     * {@link JAXBElement }{@code <}{@link AbstractCoordinateOperationType }{@code >}
     * {@link JAXBElement }{@code <}{@link ValueArrayType }{@code >}
     * {@link JAXBElement }{@code <}{@link FeatureCollectionType }{@code >}
     * {@link JAXBElement }{@code <}{@link NodeType }{@code >}
     * {@link JAXBElement }{@code <}{@link MLUnitDefinitionType }{@code >}
     * {@link JAXBElement }{@code <}{@link TimeCalendarType }{@code >}
     * {@link JAXBElement }{@code <}{@link DerivedCRSType }{@code >}
     * {@link JAXBElement }{@code <}{@link CompositeSolidType }{@code >}
     * {@link JAXBElement }{@code <}{@link CircleType }{@code >}
     * {@link JAXBElement }{@code <}{@link CurveType }{@code >}
     * {@link JAXBElement }{@code <}{@link DefinitionType }{@code >}
     * {@link JAXBElement }{@code <}{@link GeodeticCRSType }{@code >}
     * {@link JAXBElement }{@code <}{@link AbstractGeneralDerivedCRSType }{@code >}
     * {@link JAXBElement }{@code <}{@link SurfaceType }{@code >}
     * {@link JAXBElement }{@code <}{@link MLEngineeringDatumType }{@code >}
     * {@link JAXBElement }{@code <}{@link AbstractDatumType }{@code >}
     * {@link JAXBElement }{@code <}{@link OperationParameterGroupType }{@code >}
     * {@link PrimeMeridian2 }
     * {@link JAXBElement }{@code <}{@link LinearRingType }{@code >}
     * {@link ImageDatum2 }
     * {@link JAXBElement }{@code <}{@link Object }{@code >}
     * {@link JAXBElement }{@code <}{@link TimePeriodType }{@code >}
     * {@link JAXBElement }{@code <}{@link MultiSurfaceType }{@code >}
     * {@link JAXBElement }{@code <}{@link ParameterValueType }{@code >}
     * {@link JAXBElement }{@code <}{@link MLVerticalCRSType }{@code >}
     * {@link JAXBElement }{@code <}{@link ObliqueCartesianCSType }{@code >}
     * {@link JAXBElement }{@code <}{@link PointType }{@code >}
     * {@link JAXBElement }{@code <}{@link CodeOrNilReasonListType }{@code >}
     * {@link JAXBElement }{@code <}{@link AbstractTopologyType }{@code >}
     * {@link JAXBElement }{@code <}{@link GridType }{@code >}
     * {@link JAXBElement }{@code <}{@link LineStringSegmentType }{@code >}
     * {@link JAXBElement }{@code <}{@link CodeListDictionaryType }{@code >}
     * {@link JAXBElement }{@code <}{@link ArcStringType }{@code >}
     * {@link JAXBElement }{@code <}{@link MappingRuleType }{@code >}
     * {@link JAXBElement }{@code <}{@link MLPolarCSType }{@code >}
     * {@link JAXBElement }{@code <}{@link ClothoidType }{@code >}
     * {@link JAXBElement }{@code <}{@link List }{@code <}{@link String }{@code >}{@code >}
     * {@link JAXBElement }{@code <}{@link AbstractCoordinateSystemType }{@code >}
     * {@link JAXBElement }{@code <}{@link PolyhedralSurfaceType }{@code >}
     * {@link JAXBElement }{@code <}{@link TimeInstantType }{@code >}
     * {@link JAXBElement }{@code <}{@link MLConversionType }{@code >}
     * {@link JAXBElement }{@code <}{@link OrientableSurfaceType }{@code >}
     * {@link JAXBElement }{@code <}{@link CategoryExtentType }{@code >}
     * {@link JAXBElement }{@code <}{@link Category }{@code >}
     * {@link CartesianCS2 }
     * {@link JAXBElement }{@code <}{@link AbstractGMLType }{@code >}
     * {@link JAXBElement }{@code <}{@link EngineeringDatumType }{@code >}
     * {@link JAXBElement }{@code <}{@link Count }{@code >}
     * {@link JAXBElement }{@code <}{@link DictionaryType }{@code >}
     * {@link JAXBElement }{@code <}{@link AbstractMetaDataType }{@code >}
     * {@link JAXBElement }{@code <}{@link ArcByCenterPointType }{@code >}
     * {@link JAXBElement }{@code <}{@link TimeOrdinalReferenceSystemType }{@code >}
     * {@link JAXBElement }{@code <}{@link CylindricalCSType }{@code >}
     * {@link JAXBElement }{@code <}{@link AbstractCurveSegmentType }{@code >}
     * {@link JAXBElement }{@code <}{@link AbstractCRSType }{@code >}
     * {@link JAXBElement }{@code <}{@link MLOperationMethodType }{@code >}
     * {@link JAXBElement }{@code <}{@link AbstractTimeObjectType }{@code >}
     * {@link JAXBElement }{@code <}{@link Object }{@code >}
     * {@link JAXBElement }{@code <}{@link MLDerivedCRSType }{@code >}
     * {@link GeodeticDatum2 }
     * {@link JAXBElement }{@code <}{@link MultiPointCoverageType }{@code >}
     * {@link JAXBElement }{@code <}{@link MLTransformationType }{@code >}
     * {@link JAXBElement }{@code <}{@link DictionaryType }{@code >}
     * {@link JAXBElement }{@code <}{@link AbstractDiscreteCoverageType }{@code >}
     * {@link JAXBElement }{@code <}{@link AbstractTimeTopologyPrimitiveType }{@code >}
     * {@link JAXBElement }{@code <}{@link GridCoverageType }{@code >}
     * {@link JAXBElement }{@code <}{@link MLUserDefinedCSType }{@code >}
     * {@link JAXBElement }{@code <}{@link AbstractSurfaceType }{@code >}
     * {@link JAXBElement }{@code <}{@link ProjectedCRSType }{@code >}
     * {@link JAXBElement }{@code <}{@link AbstractFeatureType }{@code >}
     * {@link JAXBElement }{@code <}{@link ShellType }{@code >}
     * {@link JAXBElement }{@code <}{@link DefinitionProxyType }{@code >}
     * {@link JAXBElement }{@code <}{@link BezierType }{@code >}
     * {@link JAXBElement }{@code <}{@link OrientableCurveType }{@code >}
     * {@link JAXBElement }{@code <}{@link MLCoordinateSystemAxisType }{@code >}
     * {@link JAXBElement }{@code <}{@link GeometricComplexType }{@code >}
     * {@link JAXBElement }{@code <}{@link GenericMetaDataType }{@code >}
     * {@link JAXBElement }{@code <}{@link MLEllipsoidType }{@code >}
     * {@link JAXBElement }{@code <}{@link TimeReferenceSystemType }{@code >}
     * {@link JAXBElement }{@code <}{@link EdgeType }{@code >}
     * {@link JAXBElement }{@code <}{@link GeodesicType }{@code >}
     * {@link JAXBElement }{@code <}{@link BaseUnitType }{@code >}
     * {@link JAXBElement }{@code <}{@link AbstractFeatureCollectionType }{@code >}
     * {@link JAXBElement }{@code <}{@link PassThroughOperationType }{@code >}
     * {@link JAXBElement }{@code <}{@link MultiPointType }{@code >}
     * {@link JAXBElement }{@code <}{@link AbstractGeneralParameterValueType }{@code >}
     * {@link JAXBElement }{@code <}{@link CompoundCRSType }{@code >}
     * {@link JAXBElement }{@code <}{@link AffinePlacementType }{@code >}
     * {@link JAXBElement }{@code <}{@link UserDefinedCSType }{@code >}
     * {@link JAXBElement }{@code <}{@link MLProjectedCRSType }{@code >}
     * {@link JAXBElement }{@code <}{@link CompositeValueType }{@code >}
     * {@link JAXBElement }{@code <}{@link LinearCSType }{@code >}
     * {@link JAXBElement }{@code <}{@link MultiSolidCoverageType }{@code >}
     * {@link SphericalCS2 }
     * {@link JAXBElement }{@code <}{@link PolarCSType }{@code >}
     * {@link JAXBElement }{@code <}{@link TimeEdgeType }{@code >}
     * {@link JAXBElement }{@code <}{@link RingType }{@code >}
     * {@link JAXBElement }{@code <}{@link MultiGeometryType }{@code >}
     * {@link JAXBElement }{@code <}{@link CoordinateSystemAxisType }{@code >}
     * {@link JAXBElement }{@code <}{@link MLPassThroughOperationType }{@code >}
     * {@link JAXBElement }{@code <}{@link QuantityExtentType }{@code >}
     * {@link JAXBElement }{@code <}{@link MLAffineCSType }{@code >}
     * {@link JAXBElement }{@code <}{@link GridFunctionType }{@code >}
     * {@link JAXBElement }{@code <}{@link AbstractSolidType }{@code >}
     * {@link JAXBElement }{@code <}{@link ConcatenatedOperationType }{@code >}
     * {@link JAXBElement }{@code <}{@link PolygonType }{@code >}
     * {@link JAXBElement }{@code <}{@link MLOperationParameterType }{@code >}
     * {@link JAXBElement }{@code <}{@link TinType }{@code >}
     * {@link JAXBElement }{@code <}{@link Object }{@code >}
     * {@link JAXBElement }{@code <}{@link AbstractGeometryType }{@code >}
     * {@link JAXBElement }{@code <}{@link MultiSolidType }{@code >}
     * {@link JAXBElement }{@code <}{@link MLSphericalCSType }{@code >}
     * {@link JAXBElement }{@code <}{@link AbstractTimeGeometricPrimitiveType }{@code >}
     * {@link JAXBElement }{@code <}{@link AbstractTimeSliceType }{@code >}
     * {@link JAXBElement }{@code <}{@link AbstractTimePrimitiveType }{@code >}
     * {@link JAXBElement }{@code <}{@link TimeTopologyComplexType }{@code >}
     * {@link JAXBElement }{@code <}{@link AbstractCoordinateOperationType }{@code >}
     * {@link JAXBElement }{@code <}{@link MultiCurveCoverageType }{@code >}
     * {@link JAXBElement }{@code <}{@link TimeCSType }{@code >}
     * {@link JAXBElement }{@code <}{@link DynamicFeatureCollectionType }{@code >}
     * {@link JAXBElement }{@code <}{@link ObservationType }{@code >}
     * {@link JAXBElement }{@code <}{@link AbstractGeometryType }{@code >}
     * {@link JAXBElement }{@code <}{@link EngineeringCRSType }{@code >}
     * {@link JAXBElement }{@code <}{@link ConventionalUnitType }{@code >}
     * {@link JAXBElement }{@code <}{@link MLConventionalUnitType }{@code >}
     * {@link JAXBElement }{@code <}{@link MLConcatenatedOperationType }{@code >}
     * {@link JAXBElement }{@code <}{@link AbstractCoverageType }{@code >}
     * {@link JAXBElement }{@code <}{@link DataBlockType }{@code >}
     * {@link JAXBElement }{@code <}{@link List }{@code <}{@link String }{@code >}{@code >}
     * {@link JAXBElement }{@code <}{@link MLBaseUnitType }{@code >}
     * {@link JAXBElement }{@code <}{@link VerticalCSType }{@code >}
     * {@link JAXBElement }{@code <}{@link ArcType }{@code >}
     * {@link JAXBElement }{@code <}{@link TimeNodeType }{@code >}
     * {@link JAXBElement }{@code <}{@link OffsetCurveType }{@code >}
     * {@link EllipsoidalCS2 }
     * {@link JAXBElement }{@code <}{@link CompositeSurfaceType }{@code >}
     * {@link JAXBElement }{@code <}{@link MLTemporalDatumType }{@code >}
     * {@link JAXBElement }{@code <}{@link MultiSurfaceCoverageType }{@code >}
     * {@link JAXBElement }{@code <}{@link MultiCurveType }{@code >}
     * {@link JAXBElement }{@code <}{@link MLCompoundCRSType }{@code >}
     * {@link JAXBElement }{@code <}{@link MLEngineeringCRSType }{@code >}
     * {@link JAXBElement }{@code <}{@link ParameterValueGroupType }{@code >}
     * {@link JAXBElement }{@code <}{@link VerticalCRSType }{@code >}
     * {@link JAXBElement }{@code <}{@link TimeClockType }{@code >}
     * {@link JAXBElement }{@code <}{@link RectifiedGridType }{@code >}
     * {@link JAXBElement }{@code <}{@link EnvelopeWithTimePeriodType }{@code >}
     * {@link JAXBElement }{@code <}{@link MeasureOrNilReasonListType }{@code >}
     * {@link JAXBElement }{@code <}{@link AbstractTimeComplexType }{@code >}
     * {@link JAXBElement }{@code <}{@link MLCodeListDictionaryType }{@code >}
     * {@link JAXBElement }{@code <}{@link AbstractCRSType }{@code >}
     * {@link JAXBElement }{@code <}{@link OperationParameterType }{@code >}
     * {@link JAXBElement }{@code <}{@link LineStringType }{@code >}
     * {@link JAXBElement }{@code <}{@link TemporalCRSType }{@code >}
     * {@link JAXBElement }{@code <}{@link TriangulatedSurfaceType }{@code >}
     * {@link JAXBElement }{@code <}{@link AbstractGeometricAggregateType }{@code >}
     * {@link JAXBElement }{@code <}{@link CodeDefinitionType }{@code >}
     * {@link JAXBElement }{@code <}{@link CubicSplineType }{@code >}
     * {@link VerticalDatum2 }
     * {@link JAXBElement }{@code <}{@link MLCylindricalCSType }{@code >}
     * {@link JAXBElement }{@code <}{@link ImageCRSType }{@code >}
     * {@link JAXBElement }{@code <}{@link MLEllipsoidalCSType }{@code >}
     * {@link Conversion2 }
     * {@link JAXBElement }{@code <}{@link DirectedObservationType }{@code >}
     * {@link JAXBElement }{@code <}{@link AbstractRingType }{@code >}
     * {@link JAXBElement }{@code <}{@link AbstractGeneralConversionType }{@code >}
     * {@link Ellipsoid2 }
     * {@link JAXBElement }{@code <}{@link FileType }{@code >}
     * {@link JAXBElement }{@code <}{@link CircleByCenterPointType }{@code >}
     * {@link JAXBElement }{@code <}{@link DynamicFeatureType }{@code >}
     * {@link JAXBElement }{@code <}{@link AbstractContinuousCoverageType }{@code >}
     * {@link JAXBElement }{@code <}{@link DirectedObservationAtDistanceType }{@code >}
     * {@link JAXBElement }{@code <}{@link MLImageCRSType }{@code >}
     * {@link JAXBElement }{@code <}{@link MLGeodeticCRSType }{@code >}
     * {@link JAXBElement }{@code <}{@link MLLinearCSType }{@code >}
     * {@link JAXBElement }{@code <}{@link AbstractCoordinateOperationType }{@code >}
     * {@link JAXBElement }{@code <}{@link TopoSolidType }{@code >}
     * {@link JAXBElement }{@code <}{@link TransformationType }{@code >}
     * {@link JAXBElement }{@code <}{@link MLTimeCSType }{@code >}
     * {@link JAXBElement }{@code <}{@link UnitDefinitionType }{@code >}
     * {@link JAXBElement }{@code <}{@link ArcStringByBulgeType }{@code >}
     * {@link JAXBElement }{@code <}{@link MLCodeDefinitionType }{@code >}
     * {@link JAXBElement }{@code <}{@link GeocentricCRSType }{@code >}
     * {@link JAXBElement }{@code <}{@link Quantity }{@code >}
     * {@link JAXBElement }{@code <}{@link CoverageFunctionType }{@code >}
     * 
     * 
     */
    public List<JAXBElement<?>> getAbstractObject() {
        if (abstractObject == null) {
            abstractObject = new ArrayList<JAXBElement<?>>();
        }
        return this.abstractObject;
    }

    /**
     * Gets the value of the owns property.
     * 
     * @return
     *     possible object is
     *     {@link java.lang.Boolean }
     *     
     */
    public boolean isOwns() {
        if (owns == null) {
            return false;
        } else {
            return owns;
        }
    }

    /**
     * Sets the value of the owns property.
     * 
     * @param value
     *     allowed object is
     *     {@link java.lang.Boolean }
     *     
     */
    public void setOwns(java.lang.Boolean value) {
        this.owns = value;
    }

}

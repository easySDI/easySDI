/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) EasySDI Community
 * For more information : www.easysdi.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or 
 * any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/gpl.html. 
 */

package org.easysdi.proxy.cgp;

import java.io.BufferedReader;
import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.DataOutputStream;
import java.io.FileInputStream;
import java.io.FileReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.Serializable;
import java.math.BigDecimal;
import java.math.BigInteger;
import java.net.HttpURLConnection;
import java.net.URL;
import java.util.GregorianCalendar;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.UUID;
import java.util.Vector;

import javax.servlet.ServletException;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.xml.bind.JAXBContext;
import javax.xml.bind.JAXBElement;
import javax.xml.bind.Marshaller;
import javax.xml.bind.Unmarshaller;
import javax.xml.datatype.DatatypeFactory;
import javax.xml.datatype.XMLGregorianCalendar;
import javax.xml.transform.stream.StreamSource;

import net.opengis.cat.csw.v201.ElementSetNameType;
import net.opengis.cat.csw.v201.ElementSetType;
import net.opengis.cat.csw.v201.GetRecordsResponseType;
import net.opengis.cat.csw.v201.GetRecordsType;
import net.opengis.cat.csw.v201.QueryConstraintType;
import net.opengis.cat.csw.v201.QueryType;
import net.opengis.cat.csw.v201.ResultType;
import net.opengis.cat.csw.v201.SearchResultsType;
import net.opengis.gml.v311.CoordType;
import net.opengis.gml.v311.EnvelopeType;
import net.opengis.gml.v311.LinearRingType;
import net.opengis.ogc.BBOXType;
import net.opengis.ogc.BinaryComparisonOpType;
import net.opengis.ogc.BinarySpatialOpType;
import net.opengis.ogc.FilterType;
import net.opengis.ogc.LiteralType;
import net.opengis.ogc.PropertyIsLikeType;
import net.opengis.ogc.PropertyNameType;
import net.opengis.ogc.UnaryLogicOpType;

import org.easysdi._2008.ext.EXExtendedMetadataPropertyType;
import org.easysdi.proxy.core.ProxyServlet;
import org.easysdi.xml.handler.mapper.NamespacePrefixMapperImpl;
import org.isotc211._2005.gco.CharacterStringPropertyType;
import org.isotc211._2005.gmd.AbstractMDIdentificationType;
import org.isotc211._2005.gmd.CICitationPropertyType;
import org.isotc211._2005.gmd.CICitationType;
import org.isotc211._2005.gmd.CIDatePropertyType;
import org.isotc211._2005.gmd.CIDateType;
import org.isotc211._2005.gmd.CIResponsiblePartyPropertyType;
import org.isotc211._2005.gmd.CIResponsiblePartyType;
import org.isotc211._2005.gmd.EXExtentPropertyType;
import org.isotc211._2005.gmd.EXGeographicExtentPropertyType;
import org.isotc211._2005.gmd.MDDataIdentificationType;
import org.isotc211._2005.gmd.MDDistributorPropertyType;
import org.isotc211._2005.gmd.MDIdentificationPropertyType;
import org.isotc211._2005.gmd.MDSpatialRepresentationPropertyType;
import org.isotc211._2005.gmd.MDTopicCategoryCodePropertyType;
import org.isotc211._2005.gmd.MDTopicCategoryCodeType;
import org.w3c.dom.Node;
import org.xmlsoap.schemas.soap.envelope.Body;
import org.xmlsoap.schemas.soap.envelope.Envelope;
import org.xmlsoap.schemas.soap.envelope.Header;

import com.vividsolutions.jts.geom.Coordinate;
import com.vividsolutions.jts.geom.Geometry;
import com.vividsolutions.jts.geom.LinearRing;
import com.vividsolutions.jts.geom.Polygon;

import ch.geocat._2003._05.gateway.gm03small.MDMetadataType;
import ch.geocat._2003._05.gateway.gm03small.OptionalString;
import ch.geocat._2003._05.gateway.gm03small.OptionalTopicCategory;
import ch.geocat._2003._05.gateway.gm03small.PTFreeText;
import ch.geocat._2003._05.gateway.gm03small.PTGroup;
import ch.geocat._2003._05.gateway.gm03small.MDMetadataType.IdentificationInfo;
import ch.geocat._2003._05.gateway.gm03small.MDMetadataType.IdentificationInfo.Citation;
import ch.geocat._2003._05.gateway.gm03small.MDMetadataType.IdentificationInfo.Extent;
import ch.geocat._2003._05.gateway.gm03small.MDMetadataType.IdentificationInfo.PointOfContact;
import ch.geocat._2003._05.gateway.gm03small.MDMetadataType.IdentificationInfo.Citation.Date;
import ch.geocat._2003._05.gateway.gm03small.MDMetadataType.IdentificationInfo.Extent.GeographicElement;
import ch.geocat._2003._05.gateway.gml.DirectPositionType;
import ch.geocat._2003._05.gateway.header.RequestIDType;
import ch.geocat._2003._05.gateway.query.CatalogGatewayRequest;
import ch.geocat._2003._05.gateway.query.FormatType;
import ch.geocat._2003._05.gateway.query.OperatorType;
import ch.geocat._2003._05.gateway.query.ProfileType;
import ch.geocat._2003._05.gateway.query.QueryResultType;
import ch.interlis.interlis2.GM03V18.DataSection;
import ch.interlis.interlis2.GM03V18.GM03CoreCore;
import ch.interlis.interlis2.GM03V18.GM03CoreCoreCICitation;
import ch.interlis.interlis2.GM03V18.GM03CoreCoreCICitationdate;
import ch.interlis.interlis2.GM03V18.GM03CoreCoreCIResponsibleParty;
import ch.interlis.interlis2.GM03V18.GM03CoreCoreDate;
import ch.interlis.interlis2.GM03V18.GM03CoreCoreLanguageCodeISO2;
import ch.interlis.interlis2.GM03V18.GM03CoreCoreMDDataIdentification;
import ch.interlis.interlis2.GM03V18.GM03CoreCoreMDMetadata;
import ch.interlis.interlis2.GM03V18.GM03CoreCoreMDTopicCategoryCode2;
import ch.interlis.interlis2.GM03V18.GM03CoreCorePTFreeText;
import ch.interlis.interlis2.GM03V18.GM03CoreCorePTGroup;
import ch.interlis.interlis2.GM03V18.GM03CoreCoreURL2;
import ch.interlis.interlis2.GM03V18.Transfer;
import ch.interlis.interlis2.GM03V18.GM03CoreCoreCIResponsibleParty.Address;
import ch.interlis.interlis2.GM03V18.GM03CoreCoreCIResponsibleParty.OrganisationName;
import ch.interlis.interlis2.GM03V18.GM03CoreCoreCIResponsibleParty.PositionName;
import ch.interlis.interlis2.GM03V18.GM03CoreCoreMDDataIdentification.Abstract;
import ch.interlis.interlis2.GM03V18.GM03CoreCoreMDDataIdentification.Language;
import ch.interlis.interlis2.GM03V18.GM03CoreCoreMDDataIdentification.TopicCategory;
import ch.interlis.interlis2.GM03V18.GM03CoreCorePTFreeText.TextGroup;

/**
 * Receive a Catalog Gateway request
 * Transform the CGP request to a CSW request
 * Send the CSW request to the CSW server
 * Filter the results if needed.
 * Transform the CSW Response to GM03Core or GM03Small as needed. 
 * 
 * @author rmi
 */
public class CGPProxyServlet extends ProxyServlet {

    /**
     * 
     */
    private static final long serialVersionUID = -4958015390377000868L;
    private static final String CHARACTER_ENCODING = "UTF-8";
    private RequestIDType clientHeader;
    private String order;
    private String gm03Prolile;
    private ch.geocat._2003._05.gateway.gm03small.ObjectFactory ofGm03Small = new ch.geocat._2003._05.gateway.gm03small.ObjectFactory();
    private ch.interlis.interlis2.GM03V18.ObjectFactory ofGm03Core = new ch.interlis.interlis2.GM03V18.ObjectFactory();
    private net.opengis.ogc.ObjectFactory ofOgc = new net.opengis.ogc.ObjectFactory();
    private net.opengis.gml.v311.ObjectFactory ofGml = new net.opengis.gml.v311.ObjectFactory();
    private ch.geocat._2003._05.gateway.query.ObjectFactory ofQueryGm03 = new ch.geocat._2003._05.gateway.query.ObjectFactory();
    private String spatialOperator = null;
    private Polygon polygonJts;


    public void doGet(HttpServletRequest req, HttpServletResponse resp)
    throws ServletException, IOException {

	doPost(req, resp);
    }

    /**
     * Builds the OGC filter from the CG Request 
     * @param is the InputStream with the CG request
     * @return the OGC FilterType 
     */
    private FilterType BuildFromCGRequest(InputStream is) {
	try {	    
	    JAXBContext jc = JAXBContext
	    .newInstance(org.xmlsoap.schemas.soap.envelope.Envelope.class);
	    Unmarshaller u = jc.createUnmarshaller();

	    JAXBElement elem = (JAXBElement) u.unmarshal(is);
	    FilterType filterTypeOgc = ofOgc.createFilterType();

	    Envelope env = (Envelope) elem.getValue();
	    {
		List l = env.getHeader().getAny();

		Iterator it = l.iterator();
		while (it.hasNext()) {
		    clientHeader = getHeader((Node) it.next());
		}
	    }
	    {
		List l = env.getBody().getAny();

		Iterator it = l.iterator();
		while (it.hasNext()) {
		    CatalogGatewayRequest cgr = getBody((Node) it.next());
		    FormatType ft = cgr.getQueryRequest().getFormat();
		    order = ft.getOrder();
		    gm03Prolile = ft.getProfile().value();
		    // Gets the first criteria of the request
		    List<Serializable> contentList = cgr.getQueryRequest().getCriteria().getExpression().getValue().getContent();
		    Iterator<Serializable> contentIterator = contentList.iterator();
		    while (contentIterator.hasNext()) {
			Object o = contentIterator.next();

			if (o instanceof String) {
			    String operatorGm03 = cgr.getQueryRequest().getCriteria().getExpression().getOperator().value();
			    createLogicalFilter(operatorGm03,cgr.getQueryRequest().getCriteria().getExpression().getAttribute(),(String) o,filterTypeOgc);			    			   				
			} else if (o instanceof JAXBElement) {
			    spatialOperator  = cgr.getQueryRequest().getCriteria().getExpression().getOperator().value();
			    createSpatialFilter(spatialOperator,(JAXBElement) o,filterTypeOgc);

			}
		    }
		}
	    }
	    return filterTypeOgc;
	} catch (Exception e) {
	    e.printStackTrace();
	    logger.error(e.getMessage());
	}
	return null;
    }

    /**
     * @param operatorGm03
     * @param attribute
     * @param o
     * @param filterTypeOgc
     */
    private void createLogicalFilter(String operatorGm03, String attribute, String o,
	    FilterType filterTypeOgc) {
	// Not Like comparison
	if (operatorGm03
		.equalsIgnoreCase(OperatorType.NOT_LIKE
			.toString())) {

	    UnaryLogicOpType unaryLogicOpType = ofOgc
	    .createUnaryLogicOpType();

	    PropertyIsLikeType propertyIsLikeTypeOgc = ofOgc
	    .createPropertyIsLikeType();
	    PropertyNameType pntOgc = ofOgc
	    .createPropertyNameType();
	    pntOgc.getContent()
	    .add(getPropertyNameSearchCriteria(attribute));
	    // pntOgc.getContent().add("dc:title");
	    propertyIsLikeTypeOgc.setPropertyName(pntOgc);
	    LiteralType literalTypeOgc = ofOgc
	    .createLiteralType();
	    literalTypeOgc.getContent().add((String) o);
	    propertyIsLikeTypeOgc
	    .setLiteral(literalTypeOgc);
	    unaryLogicOpType
	    .setComparisonOps(ofOgc
		    .createPropertyIsLike(propertyIsLikeTypeOgc));
	    filterTypeOgc.setLogicOps(ofOgc
		    .createNot(unaryLogicOpType));
	} else
	    // Like comparison
	    if (operatorGm03.equalsIgnoreCase(OperatorType.LIKE
		    .toString())) {
		PropertyIsLikeType propertyIsLikeTypeOgc = ofOgc
		.createPropertyIsLikeType();
		PropertyNameType pntOgc = ofOgc
		.createPropertyNameType();				    

		pntOgc.getContent().add(
			getPropertyNameSearchCriteria(attribute));
		propertyIsLikeTypeOgc.setPropertyName(pntOgc);
		LiteralType literalTypeOgc = ofOgc
		.createLiteralType();
		literalTypeOgc.getContent().add((String) o);
		// literalTypeOgc.getContent().add("%");
		propertyIsLikeTypeOgc
		.setLiteral(literalTypeOgc);
		propertyIsLikeTypeOgc.setWildCard("%");
		filterTypeOgc
		.setComparisonOps(ofOgc
			.createPropertyIsLike(propertyIsLikeTypeOgc));
	    } else if (operatorGm03
		    .equalsIgnoreCase(OperatorType.EQ
			    .toString())
			    || operatorGm03
			    .equalsIgnoreCase(OperatorType.GE
				    .toString())
				    || operatorGm03
				    .equalsIgnoreCase(OperatorType.GT
					    .toString())
					    || operatorGm03
					    .equalsIgnoreCase(OperatorType.LE
						    .toString())
						    || operatorGm03
						    .equalsIgnoreCase(OperatorType.LT
							    .toString())
							    || operatorGm03
							    .equalsIgnoreCase(OperatorType.NE
								    .toString())) {
		BinaryComparisonOpType binary = ofOgc
		.createBinaryComparisonOpType();
		PropertyNameType pntOgc = ofOgc.createPropertyNameType();
		pntOgc.getContent().add(getPropertyNameSearchCriteria(attribute));
		JAXBElement<PropertyNameType> pnOgc = ofOgc
		.createPropertyName(pntOgc);
		binary.getExpression().add(pnOgc);
		LiteralType literalTypeOgc = ofOgc.createLiteralType();
		literalTypeOgc.getContent().add((String) o);
		binary.getExpression().add(ofOgc.createLiteral(literalTypeOgc));

		if (operatorGm03
			.equalsIgnoreCase(OperatorType.EQ
				.toString()))
		    filterTypeOgc.setComparisonOps(ofOgc
			    .createPropertyIsEqualTo(binary));
		else if (operatorGm03
			.equalsIgnoreCase(OperatorType.GE
				.toString()))
		    filterTypeOgc
		    .setComparisonOps(ofOgc
			    .createPropertyIsGreaterThanOrEqualTo(binary));
		else if (operatorGm03
			.equalsIgnoreCase(OperatorType.GT
				.toString()))
		    filterTypeOgc
		    .setComparisonOps(ofOgc
			    .createPropertyIsGreaterThan(binary));
		else if (operatorGm03
			.equalsIgnoreCase(OperatorType.LE
				.toString()))
		    filterTypeOgc
		    .setComparisonOps(ofOgc
			    .createPropertyIsLessThanOrEqualTo(binary));
		else if (operatorGm03
			.equalsIgnoreCase(OperatorType.LT
				.toString()))
		    filterTypeOgc.setComparisonOps(ofOgc
			    .createPropertyIsLessThan(binary));
		else if (operatorGm03
			.equalsIgnoreCase(OperatorType.NE
				.toString()))
		    filterTypeOgc
		    .setComparisonOps(ofOgc
			    .createPropertyIsNotEqualTo(binary));

	    }

    }

    /**
     * Creates the spatial filter
     * @param operator the stapial operator
     * @param jbElem
     */
    private void createSpatialFilter(String operator, JAXBElement jbElem,FilterType filterTypeOgc) {

	//The operator should be one of these spatial filter
	if (operator.equalsIgnoreCase("WITHIN")
		||operator.equalsIgnoreCase("CONTAINS")
		||operator.equalsIgnoreCase("EQUALS")
		||operator.equalsIgnoreCase("OVERLAPS")
		|| operator.equalsIgnoreCase("DISJOINT")) {

	    BBOXType bboxTypeOgc = ofOgc.createBBOXType();

	    if (operator.equalsIgnoreCase("WITHIN")) {				    
		filterTypeOgc.setSpatialOps(ofOgc.createBBOX(bboxTypeOgc));
	    } else
		if (operator.equalsIgnoreCase("CONTAINS")) {				    
		    filterTypeOgc.setSpatialOps(ofOgc.createBBOX(bboxTypeOgc));
		} else if (operator.equalsIgnoreCase("EQUALS")) {				    
		    filterTypeOgc.setSpatialOps(ofOgc.createBBOX(bboxTypeOgc));		    
		} else if (operator.equalsIgnoreCase("OVERLAPS")) {				    
		    filterTypeOgc.setSpatialOps(ofOgc.createBBOX(bboxTypeOgc));
		} else if (operator.equalsIgnoreCase("DISJOINT")) {
		    //Not BBOX
		    UnaryLogicOpType unaryTypeOgc = ofOgc.createUnaryLogicOpType();		
		    JAXBElement<UnaryLogicOpType>  notFilterOgc = ofOgc.createNot(unaryTypeOgc);				
		    unaryTypeOgc.setSpatialOps(ofOgc.createBBOX(bboxTypeOgc));
		    filterTypeOgc.setLogicOps(notFilterOgc);
		} else if (operator.equalsIgnoreCase("BBOX")) {				    
		    filterTypeOgc.setSpatialOps(ofOgc
			    .createBBOX(bboxTypeOgc));
		}
	    if ("{http://www.geocat.ch/2003/05/gateway/GML}Polygon"
		    .equals(jbElem.getName().toString())) {				    				   				    

		com.vividsolutions.jts.geom.GeometryFactory geomFactoryJts = new com.vividsolutions.jts.geom.GeometryFactory();


		String srs = ((ch.geocat._2003._05.gateway.gml.PolygonType) jbElem
			.getValue()).getSrsName();
		List<DirectPositionType> exteriorRingList = ((ch.geocat._2003._05.gateway.gml.PolygonType) jbElem
			.getValue()).getExteriorRing()
			.getPos();
		Iterator<DirectPositionType> exteriorRingIterator = exteriorRingList.iterator();		    
		List<Coordinate> coordinateJtsList = new Vector<Coordinate>();

		// List each point of the exterior ring
		while (exteriorRingIterator.hasNext()) {
		    DirectPositionType dptGM03 = exteriorRingIterator.next();
		    List<Double> posListGM03 = dptGM03.getValue();
		    Iterator<Double> posIteratorGM03 = posListGM03.iterator();

		    // List each coordinate of the point
		    while (posIteratorGM03.hasNext()) {
			Double x = posIteratorGM03.next();					    
			Double y= posIteratorGM03.next();
			//Creates a jts coordinate
			com.vividsolutions.jts.geom.Coordinate coordinateJts = new com.vividsolutions.jts.geom.Coordinate(x,y);			    
			coordinateJtsList.add(coordinateJts);
		    }
		}
		if (coordinateJtsList.size()>0){
		    //builds the envelope containing the polygon from the JTS coordinates 
		    Object[] objectArray = coordinateJtsList.toArray();
		    Coordinate[] array = (Coordinate[])coordinateJtsList.toArray(new Coordinate[coordinateJtsList.size()]);			    
		    LinearRing linearRingJts = geomFactoryJts.createLinearRing(array );			
		    polygonJts = geomFactoryJts.createPolygon(linearRingJts, null );
		    com.vividsolutions.jts.geom.Envelope envelopeJts = polygonJts.getEnvelopeInternal();

		    EnvelopeType envelopeTypeV311 = ofGml.createEnvelopeType();
		    envelopeTypeV311.setSrsName(srs);
		    net.opengis.gml.v311.DirectPositionType lowerCorner = ofGml.createDirectPositionType();
		    net.opengis.gml.v311.DirectPositionType upperCorner = ofGml.createDirectPositionType();
		    if (operator.equalsIgnoreCase("OVERLAPS")){
			lowerCorner.getValue().add(-180.0);
			lowerCorner.getValue().add(-90.0);		    		   
			upperCorner.getValue().add(180.0);
			upperCorner.getValue().add(90.0);
		    }else{
			lowerCorner.getValue().add(envelopeJts.getMinX());
			lowerCorner.getValue().add(envelopeJts.getMinY());		    		   
			upperCorner.getValue().add(envelopeJts.getMaxX());
			upperCorner.getValue().add(envelopeJts.getMaxY());
		    }
		    envelopeTypeV311.setLowerCorner(lowerCorner);
		    envelopeTypeV311.setUpperCorner(upperCorner);

		    bboxTypeOgc.setEnvelope(envelopeTypeV311);
		    PropertyNameType pntOgc = ofOgc.createPropertyNameType();
		    JAXBElement<PropertyNameType> pnt = ofOgc.createPropertyName(pntOgc);

		    pntOgc.getContent().add("ows:BoundingBox");
		    bboxTypeOgc.setPropertyName(pnt.getValue());
		}
	    }}
	else {

	    /*	    BBOXType bBoxTypeOgc = ofOgc.createBBOXType();
	    filterTypeOgc.setSpatialOps(ofOgc
		    .createBBOX(bBoxTypeOgc));

	    dump("WARNING", "The operator : \""+ operator + "\"is not managed");*/
	}


    }

    /**
     * @param Search Property
     * @return a string corresponding to the csw search criteria
     */
    private String getPropertyNameSearchCriteria(String searchProperty) {

	if (searchProperty == null)
	    return null;

	if (searchProperty.equals("/MD_Metadata")) {
	    return "any";
	} else if (searchProperty.equals("/MD_Metadata/fileIdentifier")) {
	    return "FileIdentifier";
	} else if (searchProperty.equals("/MD_Metadata/dateStamp")) {
	    return "CreationDate";
	} else if (searchProperty
		.equals("/MD_Metadata/identificationInfo/pointOfContact/role")) {
	    return "any";
	} else if (searchProperty
		.equals("/MD_Metadata/identificationInfo/pointOfContact/individualName")) {
	    return "any";
	} else if (searchProperty
		.equals("/MD_Metadata/identificationInfo/pointOfContact/positionName/textGroup/plainText")) {
	    return "any";
	} else if (searchProperty
		.equals("/MD_Metadata/identificationInfo/pointOfContact/organisationName/textGroup/plainText")) {
	    return "OrganisationName";
	} else if (searchProperty
		.equals("/MD_Metadata/identificationInfo/language")) {
	    return "Language";
	} else if (searchProperty
		.equals("/MD_Metadata/identificationInfo/purpose/textGroup/plainText")) {
	    return "dc:subject";
	} else if (searchProperty
		.equals("/MD_Metadata/identificationInfo/topicCategory")) {
	    return "TopicCategory";
	} else if (searchProperty
		.equals("/MD_Metadata/identificationInfo/abstract/textGroup/plainText")) {
	    return "dct:abstract";
	} else if (searchProperty
		.equals("/MD_Metadata/identificationInfo/citation/title/textGroup/plainText")) {
	    return "dc:title";
	} else if (searchProperty
		.equals("/MD_Metadata/identificationInfo/citation/date/date")) {
	    return "any";
	} else if (searchProperty
		.equals("/MD_Metadata/identificationInfo/citation/date/dateType")) {
	    return "any";
	} else if (searchProperty
		.equals("/MD_Metadata/identificationInfo/descriptiveKeywords/keyword/textGroup/plainText")) {
	    return "KeywordType";
	} else if (searchProperty
		.equals("/MD_Metadata/identificationInfo/extent/description/textGroup/plainText")) {
	    return "GeographicDescriptionCode";
	} else if (searchProperty
		.equals("/MD_Metadata/identificationInfo/extent/geographicElement/polygon")) {
	    return "ows:BoundingBox";
	} else if (searchProperty
		.equals("/MD_Metadata/identificationInfo/extent/geographicElement/westBoundLongitude")) {
	    return "any";
	} else if (searchProperty
		.equals("/MD_Metadata/identificationInfo/extent/geographicElement/eastBoundLongitude")) {
	    return "any";
	} else if (searchProperty
		.equals("/MD_Metadata/identificationInfo/extent/geographicElement/southBoundLatitude")) {
	    return "any";
	} else if (searchProperty
		.equals("/MD_Metadata/identificationInfo/extent/geographicElement/northBoundLatitude")) {
	    return "any";
	} else if (searchProperty
		.equals("/MD_Metadata/identificationInfo/extent/geographicElement/geographicIdentifier/code/textGroup/plainText")) {
	    return "any";
	} else
	    return "any";
    }


    private org.isotc211._2005.gmd.MDMetadataType getMD_Metadata(Node node)
    throws Exception {
	try {
	    JAXBContext jc2 = JAXBContext
	    .newInstance("org.isotc211._2005.gmd:net.opengis.gml.v320");
	    Unmarshaller u2 = jc2.createUnmarshaller();
	    JAXBElement requestIdType = (JAXBElement) u2.unmarshal(node,
		    org.isotc211._2005.gmd.MDMetadataType.class);

	    return (org.isotc211._2005.gmd.MDMetadataType) requestIdType
	    .getValue();
	} catch (Exception e) {
	    e.printStackTrace();
	    logger.error(e.getMessage());
	    throw e;
	}

    }

    private RequestIDType getHeader(Node node) throws Exception {
	try {
	    JAXBContext jc2 = JAXBContext
	    .newInstance(ch.geocat._2003._05.gateway.header.RequestIDType.class);
	    Unmarshaller u2 = jc2.createUnmarshaller();
	    JAXBElement requestIdType = (JAXBElement) u2.unmarshal(node,
		    ch.geocat._2003._05.gateway.header.RequestIDType.class);
	    RequestIDType req = (RequestIDType) requestIdType.getValue();
	    return req;
	} catch (Exception e) {
	    e.printStackTrace();
	    logger.error(e.getMessage());
	    throw e;
	}

    }

    private CatalogGatewayRequest getBody(Node node) throws Exception {
	try {
	    JAXBContext jc2 = JAXBContext
	    .newInstance("ch.geocat._2003._05.gateway.query:ch.geocat._2003._05.gateway.gml");

	    Unmarshaller u2 = jc2.createUnmarshaller();
	    JAXBElement request = (JAXBElement) u2
	    .unmarshal(
		    node,
		    ch.geocat._2003._05.gateway.query.CatalogGatewayRequest.class);
	    CatalogGatewayRequest req = (CatalogGatewayRequest) request
	    .getValue();
	    return req;
	} catch (Exception e) {
	    e.printStackTrace();
	    logger.error(e.getMessage());
	    throw e;
	}

    }

    private List<Object> transformCswResponseToCgpResponse(
	    String filePath) throws Exception {
	try {
	    JAXBContext jc = JAXBContext
	    .newInstance("net.opengis.cat.csw.v201:net.opengis.ogc:net.opengis.gml.v311:org.purl.dc.terms");

	    Unmarshaller u = jc.createUnmarshaller();
	    InputStream bis = new FileInputStream(filePath);

	    JAXBElement elem = (JAXBElement) u.unmarshal(new StreamSource(bis),
		    net.opengis.cat.csw.v201.GetRecordsResponseType.class);

	    GetRecordsResponseType getRecordsResponseType = (GetRecordsResponseType) (elem
		    .getValue());
	    SearchResultsType searchResultsType = getRecordsResponseType
	    .getSearchResults();
	    List<Object> metadataList = new Vector<Object>();
	    if (searchResultsType != null) {
		List l = searchResultsType.getAny();
		Iterator iterator = l.iterator();

		while (iterator.hasNext()) {
		    org.isotc211._2005.gmd.MDMetadataType mDMetadataType = getMD_Metadata((Node) iterator.next());

		    if (mDMetadataType != null) {
			if (gm03Prolile.equals(ProfileType.GM_03_SMALL.value())) {			    
			    MDMetadataType cgr = ToCatalogGatewaySmallRequest(mDMetadataType);
			    if (cgr!=null) metadataList.add(cgr);
			} else if (gm03Prolile.equals(ProfileType.GM_03_CORE.value())) {
			    GM03CoreCore cgr = ToCatalogGatewayCoreRequest(mDMetadataType);			    
			    if (cgr!=null) metadataList.add(cgr);
			}

		    }
		}

		return metadataList;
	    }
	    return null;	
	} catch (Exception e) {
	    e.printStackTrace();
	    logger.error(e.getMessage());
	    throw e;
	}

    }

    /**
     * @param characterString
     * @return
     */
    private String toString(JAXBElement<?> characterString) {
	// TODO Auto-generated method stub
	return null;
    }

    private OptionalString toOptionalString(String s) {

	OptionalString oS = ofGm03Small.createOptionalString();
	oS.setValue(s);
	return oS;
    }

    private OptionalString toOptionalString(JAXBElement<?> s) {
	if (s == null)
	    return toOptionalString("");

	return toOptionalString(s.getValue().toString());
    }

    /***
     * Transforms an Iso 19139 Metadata into a GM03 profil small metadata
     * returns null if the spatial filter is not ok  
     * @param mdMetadataIso
     * @return an instance of MDMetadataType
     */
    private MDMetadataType ToCatalogGatewaySmallRequest(
	    org.isotc211._2005.gmd.MDMetadataType mdMetadataIso) {

	//Create the Object factory.	

	MDMetadataType mdMetadataTypeGM03 = ofGm03Small.createMDMetadataType();

	// Fileidentifier
	if (mdMetadataIso.getFileIdentifier() != null)
	    mdMetadataTypeGM03.setFileIdentifier(toOptionalString(mdMetadataIso
		    .getFileIdentifier().getCharacterString()));

	// DateStamp
	if (mdMetadataIso.getDateStamp() != null)
	    mdMetadataTypeGM03.setDateStamp(mdMetadataIso.getDateStamp()
		    .getDateTime());

	// Dataset URI
	if (mdMetadataIso.getDataSetURI() != null)
	    mdMetadataTypeGM03.setDataSetURI(toOptionalString(mdMetadataIso
		    .getDataSetURI().getCharacterString()));

	//Create the GM03 IndentificationInfo Tag
	List<IdentificationInfo> lIdentificationInfoGm03Small = mdMetadataTypeGM03
	.getIdentificationInfo();

	//Gets the Iso 19139 IndentificationInfo tag
	List<MDIdentificationPropertyType> lMDIdentificationPropertyTypeIso = mdMetadataIso
	.getIdentificationInfo();

	Iterator<MDIdentificationPropertyType> itIso = lMDIdentificationPropertyTypeIso
	.iterator();
	//Each Iso IdentificationInfo
	while (itIso.hasNext()) {
	    MDIdentificationPropertyType mDIdentificationPropertyTypeIso = itIso
	    .next();

	    JAXBElement<? extends AbstractMDIdentificationType> abstractMDIdentificationIso = mDIdentificationPropertyTypeIso
	    .getAbstractMDIdentification();

	    //It is an instance of MD_DataIdentification
	    if (abstractMDIdentificationIso.getName().toString().equals(
	    "{http://www.isotc211.org/2005/gmd}MD_DataIdentification")) {

		IdentificationInfo identificationInfoGm03 = ofGm03Small
		.createMDMetadataTypeIdentificationInfo();
		lIdentificationInfoGm03Small.add(identificationInfoGm03);

		MDDataIdentificationType mDDataIdentificationTypeIso = ((org.isotc211._2005.gmd.MDDataIdentificationType) abstractMDIdentificationIso.getValue());


		//If the operator is a spatial operator
		if (spatialOperator!=null){
		    List<EXExtentPropertyType> lEXExtentPropertyTypeIso = mDDataIdentificationTypeIso.getExtent();
		    //If the metadataextent is not empty
		    if (!lEXExtentPropertyTypeIso.isEmpty()){
			EXExtentPropertyType extentPT = lEXExtentPropertyTypeIso.get(0);
			if (!extentPT.getEXExtent().getGeographicElement().isEmpty()){
			    EXGeographicExtentPropertyType gePt = extentPT.getEXExtent().getGeographicElement().get(0);			     
			    if (gePt.getAbstractEXGeographicExtent().getName().toString().equals("{http://www.isotc211.org/2005/gmd}EX_GeographicBoundingBox")) {
				org.isotc211._2005.gmd.EXGeographicBoundingBoxType extent = (org.isotc211._2005.gmd.EXGeographicBoundingBoxType) gePt.getAbstractEXGeographicExtent().getValue();
				double y1 = (extent.getNorthBoundLatitude().getDecimal().floatValue());
				double y2 = (extent.getSouthBoundLatitude().getDecimal().floatValue());
				double x1 = (extent.getWestBoundLongitude().getDecimal().floatValue());
				double x2 = (extent.getEastBoundLongitude().getDecimal().floatValue());
				com.vividsolutions.jts.geom.Envelope env = new com.vividsolutions.jts.geom.Envelope(x1,x2,y1,y2);				

				if (spatialOperator.equals("OVERLAPS")) if (!polygonJts.getFactory().toGeometry(env).overlaps(polygonJts)) return null;
				if (spatialOperator.equals("DISJOINT")) if (!polygonJts.getFactory().toGeometry(env).disjoint(polygonJts)) return null;
				if (spatialOperator.equals("CONTAINS")) if (!polygonJts.getFactory().toGeometry(env).contains(polygonJts)) return null;
				if (spatialOperator.equals("WITHIN")) if (!polygonJts.getFactory().toGeometry(env).within(polygonJts)) return null;
				if (spatialOperator.equals("EQUALS")) if (!polygonJts.equals(polygonJts.getFactory().toGeometry(env))) return null;
			    }		    
			}
		    }

		}



		// Topic Category		
		buildOptionalTopicCategoryGm03Small(identificationInfoGm03.getTopicCategory(), mDDataIdentificationTypeIso
			.getTopicCategory());		
		// Language
		buildLanguageGm03Small(identificationInfoGm03.getLanguage(),mDDataIdentificationTypeIso
			.getLanguage());

		// Abstract		
		if (mDDataIdentificationTypeIso.getAbstract() != null) {
		    if (mDDataIdentificationTypeIso.getAbstract()
			    .getCharacterString() != null) {
			identificationInfoGm03.setAbstract(
				toPTFreeTextGm03Small( "CH","FR",mDDataIdentificationTypeIso.getAbstract().getCharacterString()));
		    }
		}

		//CICitation
		CICitationPropertyType cICitationPropertyTypeIso = mDDataIdentificationTypeIso
		.getCitation();
		if (cICitationPropertyTypeIso != null) {
		    CICitationType cICitationTypeIso = cICitationPropertyTypeIso
		    .getCICitation();
		    if (cICitationTypeIso != null) {
			Citation citationGm03 = new MDMetadataType.IdentificationInfo.Citation();
			// Title
			CharacterStringPropertyType titleIso = cICitationTypeIso
			.getTitle();
			if (titleIso != null) {
			    if (titleIso.getCharacterString() != null) {								
				citationGm03.setTitle(toPTFreeTextGm03Small("CH", "FR", titleIso.getCharacterString()));
			    }
			}
			// Date
			transformCIDatePropertyTypeGM03Small(citationGm03.getDate(),cICitationTypeIso.getDate());						
			identificationInfoGm03.setCitation(citationGm03);
		    }

		    // Point of Contact
		    transformToPointOfContactGm03(identificationInfoGm03
			    .getPointOfContact(),mDDataIdentificationTypeIso
			    .getPointOfContact());		    		    

		    // Distributor
		    transformToDistributorGm03(identificationInfoGm03
			    .getPointOfContact(),mdMetadataIso.getDistributionInfo().getMDDistribution().getDistributor());

		    // Custodian
		    transformToCustodian (identificationInfoGm03.getPointOfContact(),mdMetadataIso.getContact());

		    // Extent
		    transformToExtentGm03Small(identificationInfoGm03.getExtent(),mDDataIdentificationTypeIso.getExtent());
		}
	    } else {
	    	logger.info("|" + abstractMDIdentificationIso.getName() + "|");
	    }
	}

	return mdMetadataTypeGM03;
    }

    /**
     * @param extent
     * @param extent2
     */
    private void transformToExtentGm03Small(List<Extent> lExtentGm03,
	    List<EXExtentPropertyType> lEXExtentPropertyTypeIso) {


	Iterator<EXExtentPropertyType> itEXExtentPropertyTypeIso = lEXExtentPropertyTypeIso.iterator();

	while (itEXExtentPropertyTypeIso.hasNext()) {
	    EXExtentPropertyType eXExtentPropertyType = itEXExtentPropertyTypeIso.next();
	    List<EXGeographicExtentPropertyType> lEXGeographicExtentPropertyTypeIso = eXExtentPropertyType.getEXExtent().getGeographicElement();
	    Iterator<EXGeographicExtentPropertyType> itEXGeographicExtentPropertyTypeIso = lEXGeographicExtentPropertyTypeIso.iterator();

	    while (itEXGeographicExtentPropertyTypeIso.hasNext()) {
		EXGeographicExtentPropertyType eXGeographicExtentPropertyTypeIso = itEXGeographicExtentPropertyTypeIso
		.next();

		if (eXGeographicExtentPropertyTypeIso.getAbstractEXGeographicExtent().getName().toString().equals(
		"{http://www.isotc211.org/2005/gmd}EX_GeographicBoundingBox")) {
		    org.isotc211._2005.gmd.EXGeographicBoundingBoxType extent = (org.isotc211._2005.gmd.EXGeographicBoundingBoxType) eXGeographicExtentPropertyTypeIso.getAbstractEXGeographicExtent().getValue();
		    GeographicElement geographicElementGm03 = ofGm03Small.createMDMetadataTypeIdentificationInfoExtentGeographicElement();
		    Extent extentGm03 = ofGm03Small.createMDMetadataTypeIdentificationInfoExtent();
		    List<GeographicElement> lGeographicElementGm03 = extentGm03.getGeographicElement();
		    lGeographicElementGm03.add(geographicElementGm03);
		    geographicElementGm03.setEastBoundLongitude(extent.getEastBoundLongitude().getDecimal().doubleValue());
		    geographicElementGm03.setWestBoundLongitude(extent.getWestBoundLongitude().getDecimal().doubleValue());
		    geographicElementGm03.setNorthBoundLatitude(extent.getNorthBoundLatitude().getDecimal().doubleValue());
		    geographicElementGm03.setSouthBoundLatitude(extent.getSouthBoundLatitude().getDecimal().doubleValue());

		    lExtentGm03.add(extentGm03);
		}
	    }
	}


    }

    /**
     * @param pointOfContact
     * @param contact
     */
    private void transformToCustodian(List<PointOfContact> lPointOfContactGm03,
	    List<CIResponsiblePartyPropertyType> lContactIso) {


	Iterator<CIResponsiblePartyPropertyType> itCIResponsiblePartyPropertyTypeIso = lContactIso
	.iterator();
	while (itCIResponsiblePartyPropertyTypeIso.hasNext()) {
	    CIResponsiblePartyPropertyType cIResponsiblePartyPropertyTypeIso = itCIResponsiblePartyPropertyTypeIso
	    .next();
	    lPointOfContactGm03.add(toPOC(
		    cIResponsiblePartyPropertyTypeIso
		    .getCIResponsibleParty(), ofGm03Small));
	}

    }

    /**
     * @param pointOfContact
     * @param distributor
     */
    private void transformToDistributorGm03(
	    List<PointOfContact> lPointOfContactGm03,
	    List<MDDistributorPropertyType> lMDDistributorPropertyTypeIso) {


	Iterator<MDDistributorPropertyType> itMDDistributorPropertyTypeIso = lMDDistributorPropertyTypeIso
	.iterator();
	while (itMDDistributorPropertyTypeIso.hasNext()) {
	    MDDistributorPropertyType mDDistributorPropertyTypeIso = itMDDistributorPropertyTypeIso
	    .next();
	    CIResponsiblePartyPropertyType cIResponsiblePartyPropertyTypeIso = mDDistributorPropertyTypeIso
	    .getMDDistributor().getDistributorContact();
	    if (cIResponsiblePartyPropertyTypeIso != null) {
		lPointOfContactGm03.add(toPOC(
			cIResponsiblePartyPropertyTypeIso
			.getCIResponsibleParty(), ofGm03Small));
	    }
	}


    }

    /**
     * @param pointOfContact
     * @param pointOfContact2
     */
    private void transformToPointOfContactGm03(
	    List<PointOfContact> lPointOfContactGm03,
	    List<CIResponsiblePartyPropertyType> lPocIso) {


	Iterator<CIResponsiblePartyPropertyType> itPocIso = lPocIso
	.iterator();
	while (itPocIso.hasNext()) {
	    CIResponsiblePartyPropertyType cIResponsiblePartyPropertyTypeIso = itPocIso
	    .next();
	    CIResponsiblePartyType cIResponsiblePartyTypeIso = cIResponsiblePartyPropertyTypeIso
	    .getCIResponsibleParty();
	    lPointOfContactGm03.add(toPOC(
		    cIResponsiblePartyTypeIso, ofGm03Small));
	}
    }

    /**
     * @param date
     * @param date2
     */
    private void transformCIDatePropertyTypeGM03Small(List<Date> ldateGm03,
	    List<CIDatePropertyType> lCIDatePropertyTypeIso) {

	Iterator<CIDatePropertyType> itCIDatePropertyTypeIso = lCIDatePropertyTypeIso
	.iterator();
	while (itCIDatePropertyTypeIso.hasNext()) {
	    CIDatePropertyType cIDatePropertyTypeIso = itCIDatePropertyTypeIso.next();
	    CIDateType cIDateTypeIso = cIDatePropertyTypeIso.getCIDate();
	    if (cIDateTypeIso != null) {
		if (cIDateTypeIso.getDate() != null) {
		    Date dateGm03 = new Date();
		    dateGm03.setDate(cIDateTypeIso.getDate().getDate());
		    dateGm03.setDateType(cIDateTypeIso.getDateType().getCIDateTypeCode().getCodeListValue());
		    ldateGm03.add(dateGm03);
		}
	    }
	}	
    }

    /**
     * @param string
     * @param string2
     * @param characterString
     */
    private PTFreeText toPTFreeTextGm03Small(String country, String language,
	    JAXBElement<?> characterString) {


	PTGroup ptGroup = ofGm03Small.createPTGroup();
	Object o = characterString.getValue();


	if (o instanceof String) ptGroup.setPlainText((String)characterString.getValue());			
	else if (o instanceof org.isotc211._2005.gmd.LocalisedCharacterStringType) {
	    ptGroup.setPlainText(((org.isotc211._2005.gmd.LocalisedCharacterStringType)characterString.getValue()).getValue());
	    String locale = ((org.isotc211._2005.gmd.LocalisedCharacterStringType)characterString.getValue()).getLocale();
	    language = locale.substring(0, locale.indexOf("-"));
	    country = locale.substring(locale.indexOf("-")+1 );

	}



	ptGroup.setCountry(country);
	ptGroup.setLanguage(language);

	JAXBElement<PTGroup> ptFreeTextGroup = ofGm03Small
	.createPTFreeTextTextGroup(ptGroup);
	PTFreeText pTFreeText = new PTFreeText();
	pTFreeText.getContent().add(ptFreeTextGroup);

	return pTFreeText;

    }

    /**
     * Transforms the iso language to the gm03 small language
     * @param language
     * @param language2
     */
    private void buildLanguageGm03Small(List<String> lLangueGm03,
	    List<CharacterStringPropertyType> lLanguageIso) {

	Iterator<CharacterStringPropertyType> itLanguageIso = lLanguageIso
	.iterator();

	while (itLanguageIso.hasNext()) {
	    CharacterStringPropertyType languageIso = itLanguageIso
	    .next();
	    lLangueGm03.add((String) languageIso.getCharacterString()
		    .getValue());
	}

    }

    /**
     * builds the Gm03 Topic Category from the Iso Topic Category
     * @param topicCategory
     * @param topicCategory2
     */
    private void buildOptionalTopicCategoryGm03Small(
	    List<OptionalTopicCategory> lOptionalTopicCategoryGM03,
	    List<MDTopicCategoryCodePropertyType> mDTopicCategoryCodePropertyTypeListIso) {


	Iterator<MDTopicCategoryCodePropertyType> itMDTopicCategoryCodePropertyTypeListIso = mDTopicCategoryCodePropertyTypeListIso
	.iterator();
	while (itMDTopicCategoryCodePropertyTypeListIso.hasNext()) {
	    MDTopicCategoryCodePropertyType mDTopicCategoryCodePropertyTypeIso = itMDTopicCategoryCodePropertyTypeListIso
	    .next();
	    MDTopicCategoryCodeType mDTopicCategoryCodeTypeIso = mDTopicCategoryCodePropertyTypeIso
	    .getMDTopicCategoryCode();	    

	    OptionalTopicCategory optionalTopicCategoryGm03 = ofGm03Small
	    .createOptionalTopicCategory();
	    optionalTopicCategoryGm03
	    .setValue(mDTopicCategoryCodeTypeIso.value());
	    lOptionalTopicCategoryGM03.add(optionalTopicCategoryGm03);
	}


    }

    /**
     * Transform the iso 19139 metadata to GM03 core
     * @param mdMetadataIso
     * @return an instance of GM03CoreCoreMDMetadata
     */
    private GM03CoreCore  ToCatalogGatewayCoreRequest(
	    org.isotc211._2005.gmd.MDMetadataType mdMetadataIso) {	


	GM03CoreCore coreCoreGm03 = ofGm03Core.createGM03CoreCore();
	List<Object> lCore = coreCoreGm03.getGM03CoreCoreDQDataQualityOrGM03CoreCoreMDAuthorityOrGM03CoreCoreMDDigitalTransferOptions();

	GM03CoreCoreMDMetadata gM03CoreCoreMDMetadata = ofGm03Core.createGM03CoreCoreMDMetadata();
	gM03CoreCoreMDMetadata.setMetadataStandardName("GM03Core");
	lCore.add(gM03CoreCoreMDMetadata);
	// Fileidentifier
	if (mdMetadataIso.getFileIdentifier() != null)
	    gM03CoreCoreMDMetadata.setFileIdentifier(toString(mdMetadataIso
		    .getFileIdentifier().getCharacterString()));

	// DateStamp
	if (mdMetadataIso.getDateStamp() != null)
	    gM03CoreCoreMDMetadata.setDateStamp(toGm03CoreDate(mdMetadataIso
		    .getDateStamp().getDateTime()));
	// Dataset URI
	if (mdMetadataIso.getDataSetURI() != null)
	    gM03CoreCoreMDMetadata.setDataSetURI(toCoreUrl(mdMetadataIso
		    .getDataSetURI().getCharacterString()));




	//Gets the Iso 19139 IndentificationInfo tag
	List<MDIdentificationPropertyType> lMDIdentificationPropertyTypeIso = mdMetadataIso
	.getIdentificationInfo();

	Iterator<MDIdentificationPropertyType> itIso = lMDIdentificationPropertyTypeIso
	.iterator();
	//Each Iso IdentificationInfo
	while (itIso.hasNext()) {
	    MDIdentificationPropertyType mDIdentificationPropertyTypeIso = itIso
	    .next();

	    JAXBElement<? extends AbstractMDIdentificationType> abstractMDIdentificationIso = mDIdentificationPropertyTypeIso
	    .getAbstractMDIdentification();

	    //It is an instance of MD_DataIdentification
	    if (abstractMDIdentificationIso.getName().toString().equals(
	    "{http://www.isotc211.org/2005/gmd}MD_DataIdentification")) {

		//Create the GM03Core IndentificationInfo Tag
		GM03CoreCoreMDDataIdentification identificationInfoGm03Core = ofGm03Core.createGM03CoreCoreMDDataIdentification();
		lCore.add(identificationInfoGm03Core);		
		MDDataIdentificationType mDDataIdentificationTypeIso = ((org.isotc211._2005.gmd.MDDataIdentificationType) abstractMDIdentificationIso
			.getValue());

		// Topic Category		
		identificationInfoGm03Core.setTopicCategory(toTopicCategoryGm03Core(mDDataIdentificationTypeIso.getTopicCategory()));						

		// Language
		identificationInfoGm03Core.setLanguage(toLanguageGm03Core(mDDataIdentificationTypeIso
			.getLanguage()));


		// Abstract		
		if (mDDataIdentificationTypeIso.getAbstract() != null) {
		    if (mDDataIdentificationTypeIso.getAbstract()
			    .getCharacterString() != null) {			
			Abstract abstractGm03Core = new GM03CoreCoreMDDataIdentification.Abstract();
			abstractGm03Core.setGM03CoreCorePTFreeText(toGM03CoreCorePTFreeText("CH","FR",mDDataIdentificationTypeIso.getAbstract().getCharacterString()));
			identificationInfoGm03Core.setAbstract(abstractGm03Core );			
		    }
		}

		//CICitation
		CICitationPropertyType cICitationPropertyTypeIso = mDDataIdentificationTypeIso.getCitation();
		if (cICitationPropertyTypeIso != null) {
		    CICitationType cICitationTypeIso = cICitationPropertyTypeIso.getCICitation();
		    if (cICitationTypeIso != null) {

			GM03CoreCoreCICitation citationGm03Core = ofGm03Core.createGM03CoreCoreCICitation();
			// Title
			CharacterStringPropertyType titleIso = cICitationTypeIso.getTitle();
			if (titleIso != null) {
			    if (titleIso.getCharacterString() != null) {					
				GM03CoreCoreCICitation.Title titleGm03Core = new GM03CoreCoreCICitation.Title();
				titleGm03Core.setGM03CoreCorePTFreeText(toGM03CoreCorePTFreeText("CH", "FR", titleIso.getCharacterString()));
				citationGm03Core.setTitle(titleGm03Core);
			    }
			}
			lCore.add(citationGm03Core);

			/*
			// Date

			transformCIDatePropertyTypeGM03Small(citationGm03.getDate(),cICitationTypeIso.getDate());						



			identificationInfoGm03Core.setCitation(alue);*/
		    }

		    // Point of Contact

		    GM03CoreCoreCIResponsibleParty respPartyGm03Core = toResponsiblePartyGm03Core(mDDataIdentificationTypeIso
			    .getPointOfContact());		    
		    lCore.add(respPartyGm03Core);		    		    

		    /*		    // Distributor
		    transformToDistributorGm03(identificationInfoGm03
			    .getPointOfContact(),mdMetadataIso.getDistributionInfo().getMDDistribution().getDistributor());
		     */
		    // Custodian
		    GM03CoreCoreCIResponsibleParty custodianGm03Core = toResponsiblePartyGm03Core(mdMetadataIso.getContact());		    
		    lCore.add(custodianGm03Core);		    


		    // Extent
		    //transformToExtentGm03Small(identificationInfoGm03.getExtent(),mDDataIdentificationTypeIso.getExtent());	
		}
	    } else {
	    	logger.info("|" + abstractMDIdentificationIso.getName() + "|");
	    }
	}







	return coreCoreGm03;
    }

    /**
     * @param pointOfContact
     * @return
     */
    private GM03CoreCoreCIResponsibleParty toResponsiblePartyGm03Core(
	    List<CIResponsiblePartyPropertyType> lPocIso) {

	GM03CoreCoreCIResponsibleParty respPartyGm03Core = ofGm03Core.createGM03CoreCoreCIResponsibleParty();	

	Iterator<CIResponsiblePartyPropertyType> itPocIso = lPocIso
	.iterator();
	while (itPocIso.hasNext()) {
	    CIResponsiblePartyPropertyType cIResponsiblePartyPropertyTypeIso = itPocIso
	    .next();
	    CIResponsiblePartyType cIResponsiblePartyTypeIso = cIResponsiblePartyPropertyTypeIso
	    .getCIResponsibleParty();

	    respPartyGm03Core.setIndividualLastName((String)cIResponsiblePartyTypeIso.getIndividualName().getCharacterString().getValue());
	    OrganisationName on = new GM03CoreCoreCIResponsibleParty.OrganisationName () ;
	    on.setGM03CoreCorePTFreeText(toGM03CoreCorePTFreeText("CH", "FR", cIResponsiblePartyTypeIso.getOrganisationName().getCharacterString()));
	    respPartyGm03Core.setOrganisationName(on  );

	    PositionName pn = new GM03CoreCoreCIResponsibleParty.PositionName();
	    pn.setGM03CoreCorePTFreeText(toGM03CoreCorePTFreeText("CH", "FR", cIResponsiblePartyTypeIso.getOrganisationName().getCharacterString()));
	    respPartyGm03Core.setPositionName(pn);

	}





	return  respPartyGm03Core;
    }

    /**
     * @param language
     * @return
     */
    private Language toLanguageGm03Core(
	    List<CharacterStringPropertyType> lLanguageIso) {

	Language languageGm03Core =  new GM03CoreCoreMDDataIdentification.Language();

	GM03CoreCoreLanguageCodeISO2 o = ofGm03Core.createGM03CoreCoreLanguageCodeISO2();	
	List<GM03CoreCoreLanguageCodeISO2> lLangueGm03 = languageGm03Core.getCodeISOLanguageCodeISO();	

	Iterator<CharacterStringPropertyType> itLanguageIso = lLanguageIso
	.iterator();

	while (itLanguageIso.hasNext()) {
	    CharacterStringPropertyType languageIso = itLanguageIso
	    .next();
	    o.setValue(ch.interlis.interlis2.GM03V18.CodeISOLanguageCodeISO.fromValue((String) languageIso.getCharacterString().getValue()));
	    lLangueGm03.add(o);
	}

	return languageGm03Core;		
    }

    /**
     * @param topicCategory
     * @return
     */
    private TopicCategory toTopicCategoryGm03Core(
	    List<MDTopicCategoryCodePropertyType> mDTopicCategoryCodePropertyTypeListIso) {


	GM03CoreCoreMDDataIdentification.TopicCategory tcGm03Core = new GM03CoreCoreMDDataIdentification.TopicCategory();
	List<GM03CoreCoreMDTopicCategoryCode2> topicCategoryGm03CoreList = tcGm03Core.getGM03CoreCoreMDTopicCategoryCode();

	Iterator<MDTopicCategoryCodePropertyType> itMDTopicCategoryCodePropertyTypeListIso = mDTopicCategoryCodePropertyTypeListIso
	.iterator();
	while (itMDTopicCategoryCodePropertyTypeListIso.hasNext()) {
	    MDTopicCategoryCodePropertyType mDTopicCategoryCodePropertyTypeIso = itMDTopicCategoryCodePropertyTypeListIso
	    .next();
	    MDTopicCategoryCodeType mDTopicCategoryCodeTypeIso = mDTopicCategoryCodePropertyTypeIso
	    .getMDTopicCategoryCode();	    

	    GM03CoreCoreMDTopicCategoryCode2 o = ofGm03Core.createGM03CoreCoreMDTopicCategoryCode2();
	    o.setValue(ch.interlis.interlis2.GM03V18.GM03CoreCoreMDTopicCategoryCode.fromValue(mDTopicCategoryCodeTypeIso.value()));
	    topicCategoryGm03CoreList.add(o);	    
	}

	return tcGm03Core;

    }

    /**
     * Transforms a CharacterString into a GM03CoreCorePTFreeText 
     * @param CharacterString
     * @return an instance of GM03CoreCorePTFreeText
     */
    private GM03CoreCorePTFreeText toGM03CoreCorePTFreeText(String country,String language,JAXBElement<?> characterString) {


	String value = "";
	if (characterString.getValue() instanceof String) value = (String)characterString.getValue();			
	else if (characterString.getValue() instanceof org.isotc211._2005.gmd.LocalisedCharacterStringType) {
	    value = ((String)((org.isotc211._2005.gmd.LocalisedCharacterStringType)characterString.getValue()).getValue());	    
	    String locale = ((org.isotc211._2005.gmd.LocalisedCharacterStringType)characterString.getValue()).getLocale();
	    language = locale.substring(0, locale.indexOf("-"));
	    country = locale.substring(locale.indexOf("-")+1 );	    
	}	    


	GM03CoreCorePTFreeText gM03CoreCorePTFreeText = ofGm03Core
	.createGM03CoreCorePTFreeText();

	TextGroup gM03TextGroup = new GM03CoreCorePTFreeText.TextGroup();

	List<GM03CoreCorePTGroup> gM03CoreCorePTGroupList = gM03TextGroup
	.getGM03CoreCorePTGroup();
	GM03CoreCorePTGroup gM03CoreCorePTGroup = ofGm03Core
	.createGM03CoreCorePTGroup();
	gM03CoreCorePTGroup.setPlainText(value);
	gM03CoreCorePTGroup.setCountry(ch.interlis.interlis2.GM03V18.CodeISOCountryCodeISO.fromValue(country.toUpperCase()));
	gM03CoreCorePTGroup.setLanguage(ch.interlis.interlis2.GM03V18.CodeISOLanguageCodeISO.fromValue(language.toUpperCase()));
	gM03CoreCorePTGroupList.add(gM03CoreCorePTGroup);
	gM03CoreCorePTFreeText.setTextGroup(gM03TextGroup);

	return gM03CoreCorePTFreeText;

    }

    /**
     * Transforms a String into a GM03CoreCorePTFreeText 
     * @param string
     * @return an instance of GM03CoreCorePTFreeText
     */
    private GM03CoreCorePTFreeText toGM03CoreCorePTFreeText(String value) {

	GM03CoreCorePTFreeText gM03CoreCorePTFreeText = ofGm03Core
	.createGM03CoreCorePTFreeText();

	TextGroup gM03TextGroup = new GM03CoreCorePTFreeText.TextGroup();

	List<GM03CoreCorePTGroup> gM03CoreCorePTGroupList = gM03TextGroup
	.getGM03CoreCorePTGroup();
	GM03CoreCorePTGroup gM03CoreCorePTGroup = ofGm03Core
	.createGM03CoreCorePTGroup();
	gM03CoreCorePTGroup.setPlainText(value);
	gM03CoreCorePTGroupList.add(gM03CoreCorePTGroup);
	gM03CoreCorePTFreeText.setTextGroup(gM03TextGroup);
	return gM03CoreCorePTFreeText;
    }

    /**
     * @param characterString
     * @return
     */
    private GM03CoreCoreURL2 toCoreUrl(JAXBElement<?> characterString) {
	GM03CoreCoreURL2 url = new GM03CoreCoreURL2();
	url.setValue(toString(characterString));

	return url;
    }

    /**
     * @param dateTime
     * @return
     */
    private GM03CoreCoreDate toGm03CoreDate(XMLGregorianCalendar dateTime) {
	GM03CoreCoreDate date = new GM03CoreCoreDate();
	date.setValue(dateTime.toXMLFormat());

	return date;
    }

    /**
     * Transforms the Iso responsiblePartyType to gm03 type
     * 
     * @param cIResponsiblePartyTypeIso
     *            the responsible party to transform
     * @param ofGm03
     *            the objectfactory
     * @return The gmo3 PointOfContact object
     */
    private PointOfContact toPOC(
	    CIResponsiblePartyType cIResponsiblePartyTypeIso,
	    ch.geocat._2003._05.gateway.gm03small.ObjectFactory ofGm03) {
	PointOfContact pocGm03 = ofGm03
	.createMDMetadataTypeIdentificationInfoPointOfContact();

	// Individual Name
	pocGm03.setIndividualName((String) cIResponsiblePartyTypeIso
		.getIndividualName().getCharacterString().getValue());
	// Position Name
	PTFreeText pTFreeTextPositionNameGm03 = ofGm03.createPTFreeText();
	PTGroup pTGroupPositionNameGm03 = ofGm03.createPTGroup();
	if (cIResponsiblePartyTypeIso.getPositionName() != null) {
	    if (cIResponsiblePartyTypeIso.getPositionName()
		    .getCharacterString() != null) {
		pTGroupPositionNameGm03
		.setPlainText((String) cIResponsiblePartyTypeIso
			.getPositionName().getCharacterString()
			.getValue());

		List<Serializable> lContentGm03 = pTFreeTextPositionNameGm03
		.getContent();
		JAXBElement<PTGroup> ptGroup = ofGm03
		.createPTFreeTextTextGroup(pTGroupPositionNameGm03);
		lContentGm03.add(ptGroup);
		pocGm03.setPositionName(pTFreeTextPositionNameGm03);

	    }
	}

	JAXBElement<PTGroup> ptGroupPositionName = ofGm03
	.createPTFreeTextTextGroup(pTGroupPositionNameGm03);
	pTFreeTextPositionNameGm03.getContent().add(ptGroupPositionName);
	pocGm03.setPositionName(pTFreeTextPositionNameGm03);
	// Organisation Name

	PTFreeText pTFreeTextOrganisationNameGm03 = toPTFreeTextGm03Small("CH", "FR", cIResponsiblePartyTypeIso.getOrganisationName().getCharacterString());	
	pocGm03.setOrganisationName(pTFreeTextOrganisationNameGm03);

	// role
	List<String> lRoleGm03 = pocGm03.getRole();
	lRoleGm03.add((String) cIResponsiblePartyTypeIso.getRole()
		.getCIRoleCode().getCodeListValue());

	return pocGm03;
    }

    /**
     * Builds the csw getRecords Request that will be sent to the remote csw
     * server.
     * 
     * @return the csw response
     * @throws Exception
     */
    private byte[] buildGetRecordsRequest(InputStream is) throws Exception {
	try {

	    JAXBContext jc2 = JAXBContext
	    .newInstance("net.opengis.cat.csw.v201:net.opengis.ogc:net.opengis.gml.v311:org.purl.dc.terms");

	    Marshaller m = jc2.createMarshaller();

	    m.setProperty(Marshaller.JAXB_FORMATTED_OUTPUT, Boolean.TRUE);
	    m.setProperty("com.sun.xml.bind.namespacePrefixMapper",
		    new NamespacePrefixMapperImpl());
	    m.setProperty(Marshaller.JAXB_ENCODING, CHARACTER_ENCODING);
	    m.setProperty(Marshaller.JAXB_FRAGMENT, Boolean.FALSE);

	    net.opengis.cat.csw.v201.ObjectFactory of = new net.opengis.cat.csw.v201.ObjectFactory();
	    QueryType qt = of.createQueryType();
	    ElementSetNameType elementSetNameType = of.createElementSetNameType();
	    elementSetNameType.setValue(ElementSetType.FULL);
	    qt.setElementSetName(elementSetNameType);

	    QueryConstraintType queryConstraintType = of
	    .createQueryConstraintType();
	    FilterType ft = BuildFromCGRequest(is);
	    if ((ft.getComparisonOps() != null || ft.getLogicOps() != null || ft.getSpatialOps() != null)) queryConstraintType.setFilter(ft);
	    queryConstraintType.setVersion("1.1.0");

	    qt.setConstraint(queryConstraintType);

	    JAXBElement<QueryType> q = of.createQuery(qt);

	    GetRecordsType getRecordsType = of.createGetRecordsType();
	    getRecordsType.setAbstractQuery(q);
	    getRecordsType.setService("CSW");
	    getRecordsType.setVersion("2.0.1");
	    getRecordsType.setOutputSchema("csw:IsoRecord");
	    if (getRemoteServerInfo(0).getMaxRecords()!=null){
	    getRecordsType.setMaxRecords(new BigInteger(getRemoteServerInfo(0).getMaxRecords()));
	    }
	    getRecordsType.setResultType(ResultType.RESULTS);
	    ByteArrayOutputStream baos = new ByteArrayOutputStream();

	    m.marshal(of.createGetRecords(getRecordsType), baos);
	    byte[] b = baos.toByteArray();

	    return b;
	} catch (Exception e) {
	    e.printStackTrace();
	    logger.error(e.getMessage());
	    throw (new Exception(e));
	}
    }



    /* (non-Javadoc)
     * @see org.easysdi.proxy.core.ProxyServlet#requestPreTreatmentGET(javax.servlet.http.HttpServletRequest, javax.servlet.http.HttpServletResponse)
     */
    @Override
    protected void requestPreTreatmentGET(HttpServletRequest req,
	    HttpServletResponse resp) {
	// TODO Auto-generated method stub
	
    }
    /* (non-Javadoc)
     * @see org.easysdi.proxy.core.ProxyServlet#requestPreTreatmentPOST(javax.servlet.http.HttpServletRequest, javax.servlet.http.HttpServletResponse)
     */
    @Override
    protected void requestPreTreatmentPOST(HttpServletRequest req,
	    HttpServletResponse resp) {
	try {	

	    byte[] b = buildGetRecordsRequest(req.getInputStream());
	    ByteArrayInputStream bis = new ByteArrayInputStream(b);

	    BufferedReader in = new BufferedReader(new InputStreamReader(bis));
	    String input;
	    String request = "";
	    while ((input = in.readLine()) != null) {
		request = request + input;
	    }
	    logger.info("Request="+request);	

	    String filePath = sendData("POST", getRemoteServerUrl(0), request);	    


	    CatalogGatewayRequest catalogGatewayRequest = ofQueryGm03.createCatalogGatewayRequest();
	    QueryResultType rType = ofQueryGm03.createQueryResultType();
	    catalogGatewayRequest.setQueryResult(rType);



	    List<Object> metadataList = transformCswResponseToCgpResponse(filePath);
	    if (metadataList != null) {
		Iterator<Object> it = metadataList
		.iterator();
		resp.getOutputStream().write(
			"<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>"
			.getBytes());

		org.xmlsoap.schemas.soap.envelope.ObjectFactory ofSoapEnv = new org.xmlsoap.schemas.soap.envelope.ObjectFactory();
		ch.geocat._2003._05.gateway.header.ObjectFactory ofHeaderGm03 = new ch.geocat._2003._05.gateway.header.ObjectFactory();
		RequestIDType requestIDTypeGm03 = ofHeaderGm03
		.createRequestIDType();
		if (clientHeader != null) {
		    requestIDTypeGm03
		    .setMessageId(UUID.randomUUID().toString());
		    requestIDTypeGm03.setReferenceId(clientHeader
			    .getReferenceId());
		    requestIDTypeGm03
		    .setResponseTo(clientHeader.getMessageId());
		    requestIDTypeGm03.setSendingNodeId(req.getRemoteAddr());
		    requestIDTypeGm03.setVersion(clientHeader.getVersion());
		} else {
		    requestIDTypeGm03
		    .setMessageId(UUID.randomUUID().toString());
		    requestIDTypeGm03.setReferenceId("");
		    requestIDTypeGm03.setResponseTo("");
		    requestIDTypeGm03.setSendingNodeId(req.getRemoteAddr());
		    requestIDTypeGm03.setVersion("0.99");
		}
		GregorianCalendar gCalendar = new GregorianCalendar();
		gCalendar.setTimeInMillis(System.currentTimeMillis());
		XMLGregorianCalendar dateTime = DatatypeFactory.newInstance()
		.newXMLGregorianCalendar(gCalendar);

		requestIDTypeGm03.setDateAndTime(dateTime);
		Envelope envelopeSoap = ofSoapEnv.createEnvelope();
		Header headerSoap = ofSoapEnv.createHeader();
		headerSoap.getAny().add(
			ofHeaderGm03.createRequestID(requestIDTypeGm03));
		// headerSoap.getAny().add(requestIDTypeGm03);
		Body bodySoap = ofSoapEnv.createBody();
		envelopeSoap.setHeader(headerSoap);
		envelopeSoap.setBody(bodySoap);
		int i = 0;

		Transfer transfert = ofGm03Core.createTransfer();
		DataSection ds = ofGm03Core.createDataSection();    
		transfert.setDATASECTION(ds);
		String classString="none";
		while (it.hasNext()) {
		    try {
		    	logger.info("COUNT="+ String.valueOf(i));
			Object o = it.next();
			if (o instanceof GM03CoreCore){
			    classString= "GM03CoreCore";
			    ds.getGM03CoreCoreOrGM03ComprehensiveComprehensive().add(o);			            		    
			}else if (o instanceof MDMetadataType){
			    classString= "MDMetadataType";
			    rType.getMDMetadata().add((MDMetadataType)o);
			}
			i++;
		    } catch (Exception e) {
			e.printStackTrace();
			logger.error(e.getMessage());
		    }
		}
		if (classString.equals("GM03CoreCore")){
		    rType.getGM03Core().add(transfert);
		}
		bodySoap.getAny().add(catalogGatewayRequest);

		JAXBContext jc2 = JAXBContext
		.newInstance("org.xmlsoap.schemas.soap.envelope:ch.geocat._2003._05.gateway.query:ch.geocat._2003._05.gateway.gml:ch.geocat._2003._05.gateway.header:ch.interlis.interlis2.GM03V18");

		Marshaller marshaller = jc2.createMarshaller();

		marshaller.setProperty(Marshaller.JAXB_FORMATTED_OUTPUT,
			Boolean.TRUE);
		marshaller.setProperty(
			"com.sun.xml.bind.namespacePrefixMapper",
			new NamespacePrefixMapperImpl());
		marshaller.setProperty(Marshaller.JAXB_ENCODING,
			CHARACTER_ENCODING);
		marshaller.setProperty(Marshaller.JAXB_FRAGMENT, Boolean.TRUE);

		marshaller.marshal(envelopeSoap, resp.getOutputStream());

	    } else {
	    	logger.error("Error on the remore host");
	    }

	} catch (Exception e) {
	    e.printStackTrace();
	    logger.error(e.getMessage());
	}
	
    }

    /**
     * Tests the class, sending a cgp request from a file to the remote server.
     * @param args
     */
    public static void main(String args[]) {

	CGPProxyServlet cgp = new CGPProxyServlet();

	try {	  
	    URL url = new URL("http://localhost:8081/Proxy/CGP");
	    HttpURLConnection hpcon = null;

	    FileReader fr = new FileReader(
	    "D:\\DEPTH\\Projets\\projets\\eclipse\\workspace\\Proxy\\vb6\\Outils d'administration\\geocat\\tests\\renaud1.xml");
	    BufferedReader br = new BufferedReader(fr);
	    String input;
	    String parameters = "";
	    while ((input = br.readLine()) != null) {
		parameters = parameters + input;
	    }

	    hpcon = (HttpURLConnection) url.openConnection();
	    hpcon.setRequestMethod("POST");

	    hpcon.setUseCaches(false);
	    hpcon.setDoInput(true);

	    hpcon.setRequestProperty("Content-Length", ""
		    + Integer.toString(parameters.getBytes().length));
	    hpcon.setRequestProperty("Content-Type", "text/xml");
	    hpcon.setDoOutput(true);
	    DataOutputStream printout = new DataOutputStream(hpcon
		    .getOutputStream());
	    printout.writeBytes(parameters);
	    printout.flush();
	    printout.close();

	    // getting the response is required to force the request, otherwise it might not even be sent at all
	    BufferedReader in = new BufferedReader(new InputStreamReader(hpcon
		    .getInputStream()));
	    while ((input = in.readLine()) != null) {
		System.out.println(input);
	    }
	    in.close();

	} catch (Exception e) {
	    e.printStackTrace();	    
	}

    }

	@Override
	protected StringBuffer generateOgcException(String errorMessage, String code, String locator,String version) {
		// TODO Auto-generated method stub
		return null;
	}
}

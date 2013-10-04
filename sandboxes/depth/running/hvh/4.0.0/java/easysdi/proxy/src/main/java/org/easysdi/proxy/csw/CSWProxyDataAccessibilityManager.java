/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin d�Arche 40b, CH-1870 Monthey,
 * easysdi@depth.ch
 *
 * This program is free software: you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or any later version. This
 * program is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with
 * this program. If not, see http://www.gnu.org/licenses/gpl.html.
 */
package org.easysdi.proxy.csw;

import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.Reader;
import java.io.StringBufferInputStream;
import java.io.StringReader;
import java.io.UnsupportedEncodingException;
import java.net.URLDecoder;
import java.net.URLEncoder;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.HashSet;
import java.util.Iterator;
import java.util.List;
import org.easysdi.proxy.domain.SdiAccessscope;
import org.easysdi.proxy.domain.SdiAccessscopeHome;
import org.easysdi.proxy.domain.SdiMetadata;
import org.easysdi.proxy.domain.SdiMetadataHome;
import org.easysdi.proxy.domain.SdiOrganism;

import org.easysdi.proxy.domain.SdiPolicy;
import org.easysdi.proxy.domain.SdiPolicyMetadatastate;
import org.easysdi.proxy.domain.SdiPolicyVisibility;
import org.easysdi.proxy.domain.SdiResource;
import org.easysdi.proxy.domain.SdiResourcetype;
import org.easysdi.proxy.domain.SdiUser;
import org.easysdi.proxy.jdom.filter.ElementFilter;
import org.jdom.Document;
import org.jdom.Element;
import org.jdom.JDOMException;
import org.jdom.Namespace;
import org.jdom.input.SAXBuilder;
import org.jdom.output.Format;
import org.jdom.output.XMLOutputter;

/**
 * Access the database to retreive accessible metadatas and rewrite request to
 * send to the CSW remote server
 *
 * @author DEPTH SA
 *
 */
public class CSWProxyDataAccessibilityManager {

    private SdiPolicy policy;
    private String dataIdVersionAccessible;
    Namespace nsCSW = Namespace.getNamespace("http://www.opengis.net/cat/csw/2.0.2");
    Namespace nsOGC = Namespace.getNamespace("http://www.opengis.net/ogc");
    final String CQL_TEXT = "CQL_TEXT";
    final String FILTER = "FILTER";

    /**
     *
     * @return
     */
    public Integer getCountOfEasySDIMetadatas() {
//		String query = "SELECT count(guid) as c FROM "+ prefix +"sdi_metadata ";
////		Map<String, Object> result= joomlaProvider.sjt.queryForMap(query);
//		Map<String, Object> result = null;
//		return  ((Long)result.get("c")).intValue();
        return 0;
    }

    /**
     * @param dataIdVersionAccessible the dataIdVersionAccessible to set
     */
    public void setDataIdVersionAccessible(String dataIdVersionAccessible) {
        this.dataIdVersionAccessible = dataIdVersionAccessible;
    }

    /**
     * Check if filters are defined in the loaded policy. Included the
     * geographic filter only usefull in a GetRecords operation
     *
     * @return
     */
    public boolean isAllDataAccessibleForGetRecords() {
        if (policy.getSdiCswSpatialpolicy() == null
                && policy.isCsw_anyresourcetype()
                && policy.isCsw_anyvisibility()
                && policy.isCsw_anystate()) {
            return true;
        }
        return false;
    }

    /**
     * Check if filters on the EASYSDI MD are defined in the loaded policy.
     *
     * @return
     */
    public boolean isAllEasySDIDataAccessible() {
        if (policy.isCsw_anyresourcetype()
                && policy.isCsw_anyvisibility()
                && policy.isCsw_anystate()) {
            return true;
        }
        return false;
    }

    /**
     * @return the dataIdVersionAccessible
     */
    public String getMetadataVersionAccessible() {
        return dataIdVersionAccessible;
    }

    /**
     *
     * @param p_policy
     * @param p_joomlaProvider
     */
    public CSWProxyDataAccessibilityManager(SdiPolicy p_policy) {
        policy = p_policy;
    }

    /**
     *
     * @param dataId
     * @return
     */
    public boolean isObjectAccessible(String dataId) {
//		//Is the requested Metadata managed by EasySDI
//		//OR is the requested metadata a harvested one?
//		try{
//			String query = "SELECT m.guid FROM "+ prefix +"sdi_metadata m WHERE m.guid = '"+dataId+"'";
////			Map<String, Object> result= joomlaProvider.sjt.queryForMap(query);
//			Map<String, Object> result = null;
//		}catch (IncorrectResultSizeDataAccessException ex)
//		{
//			//The metadata is harvested
//			//If the harvested are allowed, return true.
//			if(policy.getIncludeHarvested())
//				return true;
//			else
//				return false;
//		}
//		
//		if((policy.getObjectVisibilities() == null || policy.getObjectVisibilities().isAll())
//				&& (policy.getObjectTypes()== null || policy.getObjectTypes().isAll())
//				&& (policy.getObjectStatus()== null || policy.getObjectStatus().isAll()))
//		{
//			return true;
//		}
//		
//		String listVisibility = "";
//		if(policy.getObjectVisibilities() != null && !policy.getObjectVisibilities().isAll())
//		{
//			List<String>  allowedVisibility = policy.getObjectVisibilities().getVisibilities();
//			for (int i = 0 ; i<allowedVisibility.size() ; i++)
//			{
//				listVisibility += "'"+ allowedVisibility.get(i)+"'";
//				if(i != allowedVisibility.size()-1)
//				{
//					listVisibility += ",";
//				}
//			}
//		}
//		String listObjectType="";
//		if(policy.getObjectTypes()!= null && !policy.getObjectTypes().isAll())
//		{
//			List<String>  allowedObjectTypes = policy.getObjectTypes().getObjectTypes();
//			for (int i = 0 ; i<allowedObjectTypes.size() ; i++)
//			{
//				listObjectType += "'"+ allowedObjectTypes.get(i)+"'";
//				if(i != allowedObjectTypes.size()-1)
//				{
//					listObjectType += ",";
//				}
//			}
//		}
//		try
//		{
//			String query =     " SELECT m.guid FROM "+ prefix +"sdi_metadata m "+
//			" INNER JOIN "+ prefix +"sdi_objectversion ov  ON m.id = ov.metadata_id "+
//			" INNER JOIN "+ prefix +"sdi_object o ON o.id = ov.object_id "+
//			" INNER JOIN "+ prefix +"sdi_objecttype ot ON o.objecttype_id = ot.id "+
//			" INNER JOIN "+ prefix +"sdi_list_visibility v ON o.visibility_id = v.id ";
//			if(listObjectType != "" || listVisibility != "")
//			{
//				query += " WHERE ";
//				
//			}
//			if(listObjectType != "" )
//			{
//				query += " ot.code IN ("+ listObjectType +") ";
//				if(listVisibility != "")
//				{
//					query += " AND v.code IN ("+ listVisibility +") ";
//				}
//				
//			}
//			else
//			{
//				query += " v.code IN ("+ listVisibility +") ";
//			}
//			query += " AND m.guid = '"+dataId+"'" ;
//	
////			Map<String, Object> resultfinal= joomlaProvider.sjt.queryForMap(query);
//			Map<String, Object> resultfinal = null;
//		}
//		catch (IncorrectResultSizeDataAccessException ex)
//		{
//			return false;
//		}
//		return true;
        return true;
    }

    protected StringBuffer generateEmptyResponse(String version) {
        StringBuffer sb = new StringBuffer("<?xml version='1.0' encoding='utf-8' ?>");
        sb.append("<csw:GetRecordByIdResponse xmlns:csw=\"http://www.opengis.net/cat/csw/");
        sb.append(version);
        sb.append("\">");
        sb.append("</csw:GetRecordByIdResponse>");
        return sb;
    }

    protected StringBuffer generateEmptyResponseForGetRecords(String version) {
        Date now = new Date();
        SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ss");
        String dateToSend = sdf.format(now);
        StringBuffer sb = new StringBuffer("<?xml version='1.0' encoding='utf-8' ?>");
        sb.append("<csw:GetRecordsResponse xmlns:csw=\"http://www.opengis.net/cat/csw/");
        sb.append(version);
        sb.append("\">");
        sb.append("<csw:SearchStatus timestamp=\"" + dateToSend + "\"/>");
        sb.append("<csw:SearchResults numberOfRecordsMatched=\"0\" numberOfRecordsReturned=\"0\" nextRecord=\"0\"/>");
        sb.append("</csw:GetRecordsResponse>");
        return sb;
    }

    /**
     * Complete an existing XML constraint with a BBOX filter or build a
     * complete XML constraint with a BBOX filter The returned constraint is
     * used in a KVP GetRecords request
     *
     * @param constraint
     * @return
     * @throws IOException
     * @throws JDOMException
     */
    @SuppressWarnings("unchecked")
    public String addXMLBBOXFilter(String constraint) throws JDOMException, IOException {
        if (!policy.getSdiCswSpatialpolicy().isValid()) {
            return constraint;
        }

        if (constraint != null && constraint.length() > 0) {
            //Add Geographical filter in the existing XML filter
            SAXBuilder sxb = new SAXBuilder();
            Document docParent = sxb.build(new StringReader(URLDecoder.decode(constraint, "UTF-8")));
            Element racine = docParent.getRootElement();
            List<Element> filters = racine.removeContent();
            Element and = new Element("And", nsOGC);
            and.addContent(filters);

            Reader in = new StringReader(buildXMLBBOXFilter());
            Document filterDoc = sxb.build(in);
            and.addContent(filterDoc.getRootElement().detach());
            racine.addContent(and);

            XMLOutputter sortie = new XMLOutputter(Format.getPrettyFormat());
            ByteArrayOutputStream result = new ByteArrayOutputStream();
            sortie.output(docParent, result);

            constraint = result.toString();

        } else {
            //Build the XML filter
            constraint = buildXMLConstraint();
        }

        return URLEncoder.encode(constraint, "UTF-8");
    }

    /**
     * Build a complete XML constraint to use in a KVP GetRecords request
     *
     * @return
     * @throws UnsupportedEncodingException
     */
    public String buildXMLConstraint() throws UnsupportedEncodingException {
        if (!policy.getSdiCswSpatialpolicy().isValid()) {
            return new String();
        }

        String constraint = new String();
        constraint = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><Filter xmlns='http://www.opengis.net/ogc' xmlns:gml='http://www.opengis.net/gml'>";
        constraint += buildXMLBBOXFilter();
        constraint += "</Filter>";
        return constraint;
    }

    /**
     * Build the XML BBOX filter corresponding to the policy restriction
     *
     * <ogc:BBOX xmlns:ogc="http://www.opengis.net/ogc"
     * xmlns:gml="http://www.opengis.net/gml">
     * <ogc:PropertyName>BoundingBox</ogc:PropertyName>
     * <gml:Envelope srsName="urn:x-ogc:def:crs:EPSG:4326">
     * <gml:lowerCorner>-180 -90</gml:lowerCorner>
     * <gml:upperCorner>180 90</gml:upperCorner>
     * </gml:Envelope>
     * </ogc:BBOX>
     *
     * @return
     */
    protected String buildXMLBBOXFilter() {
        String filter = "<ogc:BBOX xmlns:ogc='http://www.opengis.net/ogc' xmlns:gml='http://www.opengis.net/gml'>";
        filter += "<ogc:PropertyName>BoundingBox</ogc:PropertyName>";
        filter += "<gml:Envelope srsName='";
        filter += "urn:x-ogc:def:crs:EPSG:4326'>";
        filter += "<gml:lowerCorner>";
        filter += policy.getSdiCswSpatialpolicy().getWestboundlongitude() + " ";
        filter += policy.getSdiCswSpatialpolicy().getSouthboundlatitude() + " ";
        filter += "</gml:lowerCorner>";
        filter += "<gml:upperCorner>";
        filter += policy.getSdiCswSpatialpolicy().getEastboundlongitude() + " ";
        filter += policy.getSdiCswSpatialpolicy().getNorthboundlatitude() + " ";
        filter += "</gml:upperCorner>";
        filter += "</gml:Envelope>";
        filter += "</ogc:BBOX>";
        return filter;
    }

    /**
     * Add to the KVP CQL filter the BBOX filter corresponding to the policy
     * restriction
     *
     * @param filter
     * @return
     */
    public String addCQLBBOXFilter(String constraint) throws UnsupportedEncodingException {
        if (policy.getSdiCswSpatialpolicy() == null || !policy.getSdiCswSpatialpolicy().isValid()) {
            return constraint;
        }
        //Add a geographic filter if one defined in the loaded policy

        if (constraint.length() > 0) {
            constraint += URLEncoder.encode(" AND BBOX(BoundingBox," + policy.getSdiCswSpatialpolicy().getWestboundlongitude() + "," + policy.getSdiCswSpatialpolicy().getSouthboundlatitude() + "," + policy.getSdiCswSpatialpolicy().getEastboundlongitude() + "," + policy.getSdiCswSpatialpolicy().getNorthboundlatitude() + ",'urn:x-ogc:def:crs:EPSG:4326') ", "UTF-8");
        } else {
            constraint = URLEncoder.encode(" BBOX(BoundingBox," + policy.getSdiCswSpatialpolicy().getWestboundlongitude() + "," + policy.getSdiCswSpatialpolicy().getSouthboundlatitude() + "," + policy.getSdiCswSpatialpolicy().getEastboundlongitude() + "," + policy.getSdiCswSpatialpolicy().getNorthboundlatitude() + ",'urn:x-ogc:def:crs:EPSG:4326') ", "UTF-8");
        }
        return constraint;
    }

    /**
     *
     * @return
     */
    public boolean isMetadataAccessible(String id) {
        if (isAllMetadataAccessible()) {
            return true;
        }

        SdiMetadataHome metadataHome = new SdiMetadataHome();
        SdiMetadata metadata = metadataHome.findByguid(id);
        if (metadata == null) {
            if (policy.isCsw_includeharvested()) {
                //This unknow metadata guid can be an harvested metadata
                //The request is allowed through the proxy and the CSW server will handle 
                //if the guid exists or not in the catalog
                return true;
            }
            return false;
        }

        SdiResource resource = metadata.getSdiVersion().getSdiResource();

        if (!policy.isCsw_anyresourcetype()) {
            HashSet<SdiResourcetype> resourceTypes = (HashSet<SdiResourcetype>) policy.getSdiResourcetypes();
            if (!resourceTypes.contains(resource.getSdiResourcetype())) {
                //Resource type is not allowed
                return false;
            }
        }
        if (!policy.isCsw_anyvisibility()) {
            if (!policy.getCswSdiSysAccessscope().getValue().equals(resource.getSdiSysAccessscope().getValue())) {
                return false;
            }

            SdiAccessscopeHome accessscopeHome = new SdiAccessscopeHome();
            List resourceAccessScope = accessscopeHome.findByGuid(resource.getGuid());

            if (policy.getCswSdiSysAccessscope().getValue().equals("user")) {
                for (SdiPolicyVisibility policyVisibility : policy.getSdiPolicyVisibilities()) {
                    SdiUser sdiuser = policyVisibility.getSdiUser();
                    boolean found = false;
                    for (Object accessscope : resourceAccessScope) {
                        if (((SdiAccessscope) accessscope).getSdiUser().getGuid().equals(sdiuser.getGuid())) {
                            found = true;
                            break;
                        }
                    }
                    if (!found) {
                        return false;
                    }
                }
            }
            if (policy.getCswSdiSysAccessscope().getValue().equals("organism")) {
                for (SdiPolicyVisibility policyVisibility : policy.getSdiPolicyVisibilities()) {
                    SdiOrganism sdiorganism = policyVisibility.getSdiOrganism();
                    boolean found = false;
                    for (Object accessscope : resourceAccessScope) {
                        if (((SdiAccessscope) accessscope).getSdiOrganism().getGuid().equals(sdiorganism.getGuid())) {
                            found = true;
                            break;
                        }
                    }
                    if (!found) {
                        return false;
                    }
                }
            }
        }

        if (!policy.isCsw_anystate()) {
            boolean found = false;
            for (SdiPolicyMetadatastate policyMetadatastate : policy.getSdiPolicyMetadatastates()) {
                if (metadata.getSdiSysMetadatastate().getId().equals(policyMetadatastate.getSdiSysMetadatastate().getId())) {
                    if (metadata.getSdiSysMetadatastate().getValue().equals("published")) {
                        //Check if the last/all restriction is ok
                        //Set the setMetadataVersionAccessible if we replace an old version guid with the last one
                    }
                    found = true;
                    break;
                }
            }
            if (!found) {
                return false;
            }
        }

        return true;
    }

    /**
     *
     * @return boolean
     */
    public boolean isAllMetadataAccessible() {
        if (policy.isCsw_includeharvested()
                && policy.isCsw_anyresourcetype()
                && policy.isCsw_anyvisibility()
                && policy.isCsw_anystate()) {
            return true;
        }
        return false;
    }

    public StringBuffer addPolicyRestrictionFilters(String ogcSearchFilter, StringBuffer param) throws JDOMException, IOException {
        SAXBuilder sxb = new SAXBuilder();

        InputStream xml = new StringBufferInputStream(param.toString());
        Document docParent = sxb.build(xml);
        Element racine = docParent.getRootElement();
        Element elementQuery = racine.getChild("Query", nsCSW);
        Element elementConstraint = null;
        Element elementFilter = null;
        Element elementAnd = null;

        ElementFilter filtre = new ElementFilter("csw:Constraint");
        Iterator it = docParent.getDescendants(filtre);


        while (it.hasNext()) {
            elementConstraint = (Element) it.next();
        }

        if (elementConstraint == null) {
            //No constraint defined
            elementConstraint = new Element("Constraint", nsCSW);
            elementQuery.addContent(elementConstraint);
            elementConstraint.setAttribute("version", "1.1.0");
            elementFilter = new Element("Filter", nsOGC);
            elementConstraint.addContent(elementFilter);
        } else {
            //Constraint already exists
            elementFilter = elementConstraint.getChild("Filter", nsOGC);
            if (elementFilter == null) {
                elementFilter = elementConstraint.getChild("Filter");
            }
            if (elementFilter == null) {
                elementFilter = new Element("Filter", nsOGC);
                elementConstraint.addContent(elementFilter);
            }
        }

        List<Element> filterChildren = elementFilter.getChildren();
        for (int i = 0; i < filterChildren.size(); i++) {
            if ("And".equalsIgnoreCase(filterChildren.get(i).getName())) {
                elementAnd = filterChildren.get(i);
                break;
            }
        }

        //Create the and node if not already exists
        if (elementAnd == null) {
            elementAnd = new Element("And", nsOGC);
            for (int i = filterChildren.size() - 1; i >= 0; i--) {
                elementAnd.addContent(filterChildren.get(i).detach());
            }
            elementFilter.addContent(elementAnd);
        }

        //Add the policy restriction on visibility, metadatastate, resourcetype and harvested status
        String restrictionfilter = buildCSWXMLFilter();
        if (restrictionfilter != null) {
            SAXBuilder builder = new SAXBuilder();
            Reader in = new StringReader(restrictionfilter);
            Document filterDoc = builder.build(in);
            elementAnd.addContent(filterDoc.getRootElement().detach());
        }

        //Add the spatial policy restriction
        if (policy.getSdiCswSpatialpolicy().isValid()) {
            SAXBuilder builder = new SAXBuilder();
            Reader in = new StringReader(buildXMLBBOXFilter());
            Document filterDoc = builder.build(in);
            elementAnd.addContent(filterDoc.getRootElement().detach());
        }

        //Return
        XMLOutputter sortie = new XMLOutputter(Format.getPrettyFormat());
        ByteArrayOutputStream result = new ByteArrayOutputStream();
        sortie.output(docParent, result);

        return new StringBuffer(result.toString());

    }

    /**
     *
     * @return String
     */
    public String buildCSWXMLFilter() {
        String filter = "<ogc:And>";
        if (!policy.isCsw_includeharvested()) {
            filter += getIsHarvestedFilter(this.FILTER);
        }
        if (!policy.isCsw_anyresourcetype()) {
            filter += getResourceTypeFilter(this.FILTER);
        }
        if (!policy.isCsw_anyvisibility()) {
            filter += getVisibilityFilter(this.FILTER);
        }
        if (!policy.isCsw_anystate()) {
            filter += getMetadatastatefilter(this.FILTER);
        }
        filter += "</ogc:And>";
        return filter;
    }
    
    public String buildCSWCQLFilter(){
        String filter = "";
        if (!policy.isCsw_includeharvested()) {
            filter += "(" + getIsHarvestedFilter(this.CQL_TEXT) + ")";
        }
        if (!policy.isCsw_anyresourcetype()) {
            if(filter.length() > 0){
                filter += " AND ";
            }
            filter += "(" +getResourceTypeFilter(this.CQL_TEXT)+ ")";
        }
        if (!policy.isCsw_anyvisibility()) {
            if(filter.length() > 0){
                filter += " AND ";
            }
            filter += "(" +getVisibilityFilter(this.CQL_TEXT)+ ")";
        }
        if (!policy.isCsw_anystate()) {
            if(filter.length() > 0){
                filter += " AND ";
            }
            filter += "(" +getMetadatastatefilter(this.CQL_TEXT)+ ")";
        }
        
        return filter;
    }

    /**
     *
     * <ogc:PropertyIsEqualTo>
     * <ogc:PropertyName>harvested</ogc:PropertyName>
     * <ogc:Literal>false</ogc:Literal>
     * </ogc:PropertyIsEqualTo>
     *
     * @return
     */
    public String getIsHarvestedFilter(String constraintlanguage) {
        if (policy.isCsw_includeharvested()) {
            return null;
        }

        String filter = null;
        if (constraintlanguage.equals(this.FILTER)) {
            filter = "<ogc:PropertyIsEqualTo>";
            filter += "<ogc:PropertyName>harvested</ogc:PropertyName>";
            filter += "<ogc:Literal>false</ogc:Literal>";
            filter += "</ogc:PropertyIsEqualTo>";
        } else if (constraintlanguage.equals(this.CQL_TEXT)) {
            filter = " harvested = false ";
        }
        return filter;
    }

    /**
     * <Or>
     * <ogc:PropertyIsEqualTo>
     * <ogc:PropertyName>resourcetype</ogc:PropertyName>
     * <ogc:Literal>geoproduct</ogc:Literal>
     * </ogc:PropertyIsEqualTo>
     * <ogc:PropertyIsEqualTo>
     * <ogc:PropertyName>resourcetype</ogc:PropertyName>
     * <ogc:Literal>layer</ogc:Literal>
     * </ogc:PropertyIsEqualTo>
     * </Or>
     *
     * @return
     */
    public String getResourceTypeFilter(String constraintlanguage) {
        if (policy.isCsw_anyresourcetype()) {
            return null;
        }

        String filter = null;
        if (constraintlanguage.equals(this.FILTER)) {
            filter = "<ogc:Or>";
            for (SdiResourcetype resourcetype : policy.getSdiResourcetypes()) {
                filter += "<ogc:PropertyIsEqualTo>";
                filter += "<ogc:PropertyName>resourcetype</ogc:PropertyName>";
                filter += "<ogc:Literal>" + resourcetype.getAlias() + "</ogc:Literal>";
                filter += "</ogc:PropertyIsEqualTo>";
            }
            filter += "</ogc:Or>";
        } else if (constraintlanguage.equals(this.CQL_TEXT)) {
            for (SdiResourcetype resourcetype : policy.getSdiResourcetypes()) {
                if (filter != null) {
                    filter += " AND ";
                } else {
                    filter = "";
                }
                filter += " resourcetype = " + resourcetype.getAlias();
            }

        }

        return filter;
    }

    /**
     * <And>
     * <ogc:PropertyIsEqualTo>
     * <ogc:PropertyName>scope</ogc:PropertyName>
     * <ogc:Literal>user</ogc:Literal>
     * </ogc:PropertyIsEqualTo>
     * <Or>
     * <ogc:PropertyIsEqualTo>
     * <ogc:PropertyName>sdiuser</ogc:PropertyName>
     * <ogc:Literal>ebca0378-04d1-7b64-65c5-832f21107302</ogc:Literal>
     * </ogc:PropertyIsEqualTo>
     * <ogc:PropertyIsEqualTo>
     * <ogc:PropertyName>sdiuser</ogc:PropertyName>
     * <ogc:Literal>8274ea8c-1d27-b154-51ff-e759977bab20</ogc:Literal>
     * </ogc:PropertyIsEqualTo>
     * </Or>
     * </And>
     *
     * @return
     */
    public String getVisibilityFilter(String constraintlanguage) {
        if (policy.isCsw_anyvisibility()) {
            return null;
        }

        if (policy.getCswSdiSysAccessscope().getValue().equals("public")) {
            return null;
        }

        String filter = null;
        if (constraintlanguage.equals(this.FILTER)) {
            filter = "<ogc:And>";
            filter += "<ogc:PropertyIsEqualTo>";
            filter += "<ogc:PropertyName>scope</ogc:PropertyName>";
            filter += "<ogc:Literal>" + policy.getCswSdiSysAccessscope().getValue() + "</ogc:Literal>";
            filter += "</ogc:PropertyIsEqualTo>";
            filter += "<ogc:Or>";
            if (policy.getCswSdiSysAccessscope().getValue().equals("user")) {
                for (SdiPolicyVisibility policyVisibility : policy.getSdiPolicyVisibilities()) {
                    if (policyVisibility.getSdiUser() != null) {
                        filter += "<ogc:PropertyIsEqualTo>";
                        filter += "<ogc:PropertyName>sdiuser</ogc:PropertyName>";
                        filter += "<ogc:Literal>" + policyVisibility.getSdiUser().getGuid() + "</ogc:Literal>";
                        filter += "</ogc:PropertyIsEqualTo>";
                    }
                }
            } else {
                for (SdiPolicyVisibility policyVisibility : policy.getSdiPolicyVisibilities()) {
                    if (policyVisibility.getSdiOrganism() != null) {
                        filter += "<ogc:PropertyIsEqualTo>";
                        filter += "<ogc:PropertyName>sdiorganism</ogc:PropertyName>";
                        filter += "<ogc:Literal>" + policyVisibility.getSdiOrganism().getGuid() + "</ogc:Literal>";
                        filter += "</ogc:PropertyIsEqualTo>";
                    }
                }
            }
            filter += "</ogc:Or>";
            filter += "</ogc:And>";
        } else if (constraintlanguage.equals(this.CQL_TEXT)) {
            filter = "scope = " + policy.getCswSdiSysAccessscope().getValue();
            filter += " AND ";
            if (policy.getCswSdiSysAccessscope().getValue().equals("user")) {
                boolean first = true;
                filter += "( ";
                for (SdiPolicyVisibility policyVisibility : policy.getSdiPolicyVisibilities()) {
                    if (policyVisibility.getSdiUser() != null) {
                        if (first) {
                            first = false;
                        } else {
                            filter += " OR ";
                        }
                        filter += " sdiuser = " + policyVisibility.getSdiUser().getGuid();
                    }
                }

            } else {
                boolean first = true;
                filter += "( ";
                for (SdiPolicyVisibility policyVisibility : policy.getSdiPolicyVisibilities()) {
                    if (policyVisibility.getSdiOrganism() != null) {
                        if (first) {
                            first = false;
                        } else {
                            filter += " OR ";
                        }
                        filter += " sdiorganism = " + policyVisibility.getSdiOrganism().getGuid();
                    }
                }
                filter += ") ";
            }
        }
        return filter;
    }

    /**
     * <Or>
     * <And>
     * <ogc:PropertyIsEqualTo>
     * <ogc:PropertyName>metadatastate</ogc:PropertyName>
     * <ogc:Literal>published</ogc:Literal>
     * </ogc:PropertyIsEqualTo>
     * <ogc:PropertyIsGreaterThanOrEqualTo>
     * <ogc:PropertyName>published</ogc:PropertyName>
     * <ogc:Literal>2010-12-21 10:12:45</ogc:Literal>
     * </ogc:PropertyIsGreaterThanOrEqualTo>
     * <ogc:PropertyIsEqualTo>
     * <ogc:PropertyName>lastversion</ogc:PropertyName>
     * <ogc:Literal>true</ogc:Literal>
     * </ogc:PropertyIsEqualTo>
     * </And>
     * <ogc:PropertyIsEqualTo>
     * <ogc:PropertyName>metadatastate</ogc:PropertyName>
     * <ogc:Literal>validated</ogc:Literal>
     * </ogc:PropertyIsEqualTo>
     * </Or>
     *
     * @return
     */
    public String getMetadatastatefilter(String constraintlanguage) {
        if (policy.isCsw_anystate()) {
            return null;
        }

        String filter = null;
        if (constraintlanguage.equals(this.FILTER)) {
            filter = "<ogc:Or>";
            for (SdiPolicyMetadatastate policyMetadataState : policy.getSdiPolicyMetadatastates()) {
                if (policyMetadataState.getSdiSysMetadatastate().getValue().equals("published")) {
                    filter += "<ogc:And>";
                    filter += "<ogc:PropertyIsEqualTo>";
                    filter += "<ogc:PropertyName>metadatastate</ogc:PropertyName>";
                    filter += "<ogc:Literal>" + policyMetadataState.getSdiSysMetadatastate().getValue() + "</ogc:Literal>";
                    filter += "</ogc:PropertyIsEqualTo>";
                    filter += "<ogc:PropertyIsGreaterThanOrEqualTo>";
                    filter += "<ogc:PropertyName>published</ogc:PropertyName>";
                    DateFormat dateFormat = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
                    filter += "<ogc:Literal>" + dateFormat.format(new Date()) + "</ogc:Literal>";
                    filter += "</ogc:PropertyIsGreaterThanOrEqualTo>";
                    if (policyMetadataState.getSdiSysMetadataversion().getValue().equals("last")) {
                        filter += "<ogc:PropertyIsEqualTo>";
                        filter += "<ogc:PropertyName>lastversion</ogc:PropertyName>";
                        filter += "<ogc:Literal>true</ogc:Literal>";
                        filter += "</ogc:PropertyIsEqualTo>";
                    }
                    filter += "</ogc:And>";
                } else {
                    filter += "<ogc:PropertyIsEqualTo>";
                    filter += "<ogc:PropertyName>metadatastate</ogc:PropertyName>";
                    filter += "<ogc:Literal>" + policyMetadataState.getSdiSysMetadatastate().getValue() + "</ogc:Literal>";
                    filter += "</ogc:PropertyIsEqualTo>";
                }
            }
            filter += "</ogc:Or>";
        } else if (constraintlanguage.equals(this.CQL_TEXT)) {
            filter = "";
            boolean first = true;
            for (SdiPolicyMetadatastate policyMetadataState : policy.getSdiPolicyMetadatastates()) {
                if(first){
                        first = false;
                    }else{
                        filter += " OR ";
                    }
                if (policyMetadataState.getSdiSysMetadatastate().getValue().equals("published")) {                    
                    filter += "(";
                    filter += " metadatastate = "+ policyMetadataState.getSdiSysMetadatastate().getValue();
                    DateFormat dateFormat = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
                    filter += " AND published >= "+ dateFormat.format(new Date()) ;
                    if (policyMetadataState.getSdiSysMetadataversion().getValue().equals("last")) {
                        filter += "AND lastversion = TRUE";                        
                    }
                    filter += ")";
                    
                } else {
                    filter += "(";
                    filter += " metadatastate = "+ policyMetadataState.getSdiSysMetadatastate().getValue();
                    filter += ")";                    
                }
            }
        }

        return null;
    }
}

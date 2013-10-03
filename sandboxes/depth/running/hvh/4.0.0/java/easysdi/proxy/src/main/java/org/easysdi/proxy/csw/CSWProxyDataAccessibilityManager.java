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
import java.util.Iterator;
import java.util.List;
import java.util.Map;

import org.easysdi.proxy.domain.SdiPolicy;
import org.easysdi.proxy.domain.SdiPolicyMetadatastate;
import org.easysdi.proxy.domain.SdiPolicyVisibility;
import org.easysdi.proxy.domain.SdiResourcetype;
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

//		if( (policy.getObjectVisibilities() == null || policy.getObjectVisibilities().isAll()) 
//			&& (policy.getObjectTypes()== null || policy.getObjectTypes().isAll())
//			&& (policy.getObjectStatus()== null || policy.getObjectStatus().isAll())
//			&& (policy.getBboxFilter() == null || policy.getBboxFilter().isValide() ))
//		{
//			return true;
//		}
//		return false;
        return true;
    }

    /**
     * Check if filters on the EASYSDI MD are defined in the loaded policy.
     *
     * @return
     */
    public boolean isAllEasySDIDataAccessible() {
//		if( (policy.getObjectVisibilities() == null || policy.getObjectVisibilities().isAll()) 
//			&& (policy.getObjectTypes()== null || policy.getObjectTypes().isAll())
//			&& (policy.getObjectStatus()== null || policy.getObjectStatus().isAll())
//			)
//		{
//			return true;
//		}
//		return false;
        return true;
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

    /**
     *
     * @param dataId
     * @return
     */
    public boolean isMetadataAccessible(String dataId) {
//		if (policy.getObjectStatus()!= null && !policy.getObjectStatus().isAll())
//		{
//			//Get metadata status
//			String query= "SELECT  m.guid as guid, m.published  as published, m.archived as archived, ms.code as state" ;
//			query +=	" FROM "+ prefix +"sdi_metadata m ";
//			query +=	" INNER JOIN "+ prefix +"sdi_list_metadatastate ms ON m.metadatastate_id=ms.id ";
//			query +=	" AND m.guid = '"+dataId+"' ";
//			query +=	" LIMIT 0,1 ";
//			
////			Map<String, Object> metadataObject= joomlaProvider.sjt.queryForMap(query);
//			Map<String, Object> metadataObject = null;
//			String status = (String)metadataObject.get("state");
//			
//			List<Status>  allowedStatus = policy.getObjectStatus().getStatus();
//
//			for (int i = 0 ; i <allowedStatus.size() ; i++)
//			{
//				if (allowedStatus.get(i).getStatus().equalsIgnoreCase("validated") && ("validated").equalsIgnoreCase(status))
//				{
//					return true;
//				}
//				if (allowedStatus.get(i).getStatus().equalsIgnoreCase("unpublished") && ("unpublished").equalsIgnoreCase(status))
//				{
//					return true;
//				}
//				if (allowedStatus.get(i).getStatus().equalsIgnoreCase("archived") 
//						&& ("archived").equalsIgnoreCase(status) 
//						&& (((Date)metadataObject.get("archived")).compareTo(new Date()) < 0 ) )
//				{
//					return true;
//				}
//				if(allowedStatus.get(i).getStatus().equalsIgnoreCase("published") 
//						&& allowedStatus.get(i).getVersion().equalsIgnoreCase("all")
//						&& ((("published").equalsIgnoreCase(status) 
//								&& (((Date)metadataObject.get("published")).compareTo(new Date()) < 0 ))
//								||(("archived").equalsIgnoreCase(status) 
//										&& (((Date)metadataObject.get("archived")).compareTo(new Date()) > 0 ) 
//										&& (((Date)metadataObject.get("published")).compareTo(new Date()) < 0 ))))
//				{
//					return true;
//				}
//				if(allowedStatus.get(i).getStatus().equalsIgnoreCase("published") 
//						&& allowedStatus.get(i).getVersion().equalsIgnoreCase("last")
//						&& ((("published").equalsIgnoreCase(status) 
//								&& (((Date)metadataObject.get("published")).compareTo(new Date()) < 0 ))
//								||(("archived").equalsIgnoreCase(status) 
//										&& (((Date)metadataObject.get("archived")).compareTo(new Date()) > 0 ) 
//										&& (((Date)metadataObject.get("published")).compareTo(new Date()) < 0 ))))
//				{
//					//request with condition on last published metadata
//					String queryVersion= "SELECT  m.guid as guid, m.published  as title ";
//					queryVersion +=	"FROM "+ prefix +"sdi_metadata m ";
//					queryVersion +=	" INNER JOIN "+ prefix +"sdi_objectversion ov ON ov.metadata_id = m.id ";
//					queryVersion +=	" INNER JOIN "+ prefix +"sdi_object o ON o.id = ov.object_id ";
//					queryVersion +=	" INNER JOIN "+ prefix +"sdi_list_metadatastate ms ON m.metadatastate_id=ms.id ";
//					queryVersion +=	" WHERE (ms.code='published' ";
//					queryVersion +=	" AND o.id = (SELECT object_id FROM "+ prefix +"sdi_objectversion WHERE metadata_id = (SELECT id FROM "+ prefix +"sdi_metadata WHERE guid = '"+dataId+"')) ";
//					queryVersion +=	" AND m.published <=CURDATE() )";
//					queryVersion +=	" OR ( ms.code='archived' ";
//					queryVersion +=	" AND o.id = (SELECT object_id FROM "+ prefix +"sdi_objectversion WHERE metadata_id = (SELECT id FROM "+ prefix +"sdi_metadata WHERE guid = '"+dataId+"')) ";
//					queryVersion +=	" AND m.published <=CURDATE() AND m.archived > CURDATE())";
//					queryVersion +=	" ORDER BY m.published DESC ";
//					queryVersion +=	" LIMIT 0,1 ";
//					try
//					{
////						Map<String, Object> results= joomlaProvider.sjt.queryForMap(queryVersion);
//						Map<String, Object> results = null;
//						if(results.containsValue(dataId))
//						{
//							return true;
//						}
//						else
//						{
//							try
//							{
//								setDataIdVersionAccessible((String) results.get("guid"));
//								return false;
//							}
//							catch (Exception ex)
//							{
//								return false;
//							}
//						}
//					}
//					catch (Exception ex)
//					{
//						return false;
//					}
//				}
//			}
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
     *
     * @return null = all metadatas allowed empty list = none of the metadatas
     * allowed
     */
    public List<Map<String, Object>> getAccessibleDataIds() {
//		List<Map<String,Object>> object_ids = null;
//		List<Map<String,Object>> metadata_ids = null;
        List<Map<String, Object>> final_metadata_ids = null;
//		String query;
//		
//		//If ObjectVisibilities, ObjectStatus or ObjectTypes don't have any value selected in the policy and the attribute all is set to 'false' :
//		//none of the metadatas will be allowed to be delivered
//		if(    (policy.getObjectVisibilities() != null && 
//				policy.getObjectVisibilities().getVisibilities()!= null && 
//				policy.getObjectVisibilities().getVisibilities().size() == 0  && 
//				!policy.getObjectVisibilities().isAll()) 
//				|| 
//			   (policy.getObjectTypes()!= null && 
//			    policy.getObjectTypes().getObjectTypes() != null && 
//			    policy.getObjectTypes().getObjectTypes().size() == 0 &&  
//			    !policy.getObjectTypes().isAll()) 
//			    ||
//			    (policy.getObjectStatus() != null && 
//			    policy.getObjectStatus().getStatus() != null &&
//			    policy.getObjectStatus().getStatus().size() == 0 &&
//			    !policy.getObjectStatus().isAll())
//				)
//		{
//			return new ArrayList<Map<String,Object>>();
//		}
//		
//		//Accessible objects
//		if(    (policy.getObjectVisibilities() != null && 
//				policy.getObjectVisibilities().getVisibilities()!= null && 
//				policy.getObjectVisibilities().getVisibilities().size() != 0  && 
//				!policy.getObjectVisibilities().isAll()) 
//				|| 
//			   (policy.getObjectTypes()!= null && 
//			    policy.getObjectTypes().getObjectTypes() != null && 
//			    policy.getObjectTypes().getObjectTypes().size() != 0 &&  
//			    !policy.getObjectTypes().isAll()))
//			   
//		{
//			String listVisibility = "";
//			if(policy.getObjectVisibilities() != null && !policy.getObjectVisibilities().isAll())
//			{
//				List<String>  allowedVisibility = policy.getObjectVisibilities().getVisibilities();
//				for (int i = 0 ; i<allowedVisibility.size() ; i++)
//				{
//					listVisibility += "'"+ allowedVisibility.get(i)+"'";
//					if(i != allowedVisibility.size()-1)
//					{
//						listVisibility += ",";
//					}
//				}
//			}
//			String listObjectType="";
//			if(policy.getObjectTypes()!= null && !policy.getObjectTypes().isAll())
//			{
//				List<String>  allowedObjectTypes = policy.getObjectTypes().getObjectTypes();
//				for (int i = 0 ; i<allowedObjectTypes.size() ; i++)
//				{
//					listObjectType += "'"+ allowedObjectTypes.get(i)+"'";
//					if(i != allowedObjectTypes.size()-1)
//					{
//						listObjectType += ",";
//					}
//				}
//			}
//			
//			query =     " SELECT m.guid FROM "+ prefix +"sdi_metadata m "+
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
//			
//			if(metadata_ids == null)
////				metadata_ids = joomlaProvider.sjt.queryForList(query);
//				metadata_ids  = null;
//			else
////				metadata_ids.addAll(joomlaProvider.sjt.queryForList(query));
//				metadata_ids = null;
//		}
//		
//		//Status & version
//		if(policy.getObjectStatus() != null && !policy.getObjectStatus().isAll())
//		{
//			//metatadata id allowed by the filter on object
//			String tempMetadataIdString ="";
//			if(metadata_ids != null)
//			{
//				for (int k = 0 ; k<metadata_ids.size() ; k++)
//				{
//					tempMetadataIdString += "'" + metadata_ids.get(k).get("guid") + "'";
//					if(k != metadata_ids.size()-1)
//					{
//						tempMetadataIdString += ",";
//					}
//				}
//			}
//			
//			query = "SELECT m.guid FROM "+ prefix +"sdi_metadata m" +
//			" INNER JOIN "+ prefix +"sdi_objectversion ov  ON m.id = ov.metadata_id "+
//			" INNER JOIN "+ prefix +"sdi_object o ON o.id = ov.object_id "+
//			" INNER JOIN "+ prefix +"sdi_objecttype ot ON o.objecttype_id = ot.id "+
//			" INNER JOIN "+ prefix +"sdi_list_metadatastate ms ON m.metadatastate_id=ms.id "+
//			" WHERE ";
//			
//			if(tempMetadataIdString != "")
//			{
//				query += " m.guid IN  ("+tempMetadataIdString+") AND ";
//			}
//				
//			boolean needOr = false;
//			boolean needExec = false;
//			List<Status>  allowedStatus = policy.getObjectStatus().getStatus();
//			for (int i = 0 ; i <allowedStatus.size() ; i++)
//			{
//				if (allowedStatus.get(i).getStatus().equalsIgnoreCase("validated") )
//				{
//					if(needOr)
//						query += " OR ";
//					query += "( ms.code = 'validated' )";
//					needOr = true;
//					needExec = true;
//				}
//				if (allowedStatus.get(i).getStatus().equalsIgnoreCase("unpublished") )
//				{
//					if(needOr)
//						query += " OR ";
//					query += " ( ms.code = 'unpublished' )";
//					needOr = true;
//					needExec = true;
//				}
//				if (allowedStatus.get(i).getStatus().equalsIgnoreCase("archived"))
//				{
//					if(needOr)
//						query += " OR ";
//					query += " (ms.code = 'archived' AND m.archived <= CURDATE() )";
//					needOr = true;
//					needExec = true;
//				}
//				if(allowedStatus.get(i).getStatus().equalsIgnoreCase("published") 
//						&& allowedStatus.get(i).getVersion().equalsIgnoreCase("all")
//						)
//				{
//					if(needOr)
//						query += " OR ";
//					query += "(( ms.code = 'published' AND m.published <= CURDATE()) OR (ms.code = 'archived' AND m.archived >= CURDATE() AND m.published <= CURDATE())) ";
//					needOr = true;
//					needExec = true;
//				}
//				
//				//Particular case only last published
//				if(allowedStatus.get(i).getStatus().equalsIgnoreCase("published") 
//						&& allowedStatus.get(i).getVersion().equalsIgnoreCase("last"))
//				{
//					String queryObjects="";
//					if (tempMetadataIdString != "")
//					{
//						queryObjects= "SELECT ov.object_id FROM "+ prefix +"sdi_objectversion ov " +
//							" INNER JOIN "+ prefix +"sdi_metadata m ON ov.metadata_id=m.id "+
//							" WHERE m.guid IN ("+tempMetadataIdString+") GROUP BY ov.object_id ";
//					}
//					else
//					{
//						queryObjects= "SELECT object_id FROM "+ prefix +"sdi_objectversion " +
//						" GROUP BY object_id ";
//					}
////					object_ids = joomlaProvider.sjt.queryForList(queryObjects);
//					object_ids = null;
//					
//					//Get last  metadata id
//					for(int j = 0 ; j <object_ids.size() ; j++)
//					{
//						String localQuery = "  SELECT m.guid  "+
//		                " FROM "+ prefix +"sdi_objectversion ov "+ 
//		                " INNER JOIN "+ prefix +"sdi_metadata m ON ov.metadata_id=m.id "+
//		                " INNER JOIN "+ prefix +"sdi_list_metadatastate ms ON m.metadatastate_id=ms.id "+
//		                " INNER JOIN "+ prefix +"sdi_object o ON ov.object_id=o.id " + 
//		                " WHERE o.id="+object_ids.get(j).get("object_id")+" " +
//		                        " AND ((ms.code='published' AND m.published <=CURDATE() )" +
//		                        " OR (ms.code='archived' AND m.archived >= CURDATE() AND m.published <= CURDATE())) "+ 
//		                " ORDER BY m.published DESC " +
//		                " LIMIT 0,1 ";
//						
//						if(final_metadata_ids == null)
////							final_metadata_ids = joomlaProvider.sjt.queryForList(localQuery);
//							final_metadata_ids = null;
//						else
////							final_metadata_ids.addAll(joomlaProvider.sjt.queryForList(localQuery));
//							final_metadata_ids = null;
//						
//					}
//					
//				}
//			}
//			if(needExec)
//			{	
//				if(final_metadata_ids == null)
////					final_metadata_ids = joomlaProvider.sjt.queryForList(query);
//					final_metadata_ids = null;
//				else
////					final_metadata_ids.addAll(joomlaProvider.sjt.queryForList(query));
//					final_metadata_ids = null;
//			}
//		}
//		else
//		{
//			final_metadata_ids = metadata_ids;
//		}

        return final_metadata_ids;
    }

    /**
     * Add a <and> block in a GetRecords <Filter> block containing the
     * accessible metadata's Id to restrain the result send back by the csw
     * service to those allowed by the current policy
     *
     * @param ogcSearchFilter
     * @param param
     * @param ids
     * @return
     *
     * <ogc:Filter xmlns:ogc="http://www.opengis.net/ogc"
     * xmlns:gml="http://www.opengis.net/gml">
     * <ogc:And>
     * <ogc:Or>
     * <ogc:PropertyIsLike wildCard="%" singleChar="_" escapeChar="\">
     * <ogc:PropertyName>mainsearch_FR</ogc:PropertyName>
     * <ogc:Literal>%free%</ogc:Literal>
     * </ogc:PropertyIsLike>
     * <ogc:PropertyIsLike wildCard="%" singleChar="_" escapeChar="\">
     * <ogc:PropertyName>mainsearch_FR</ogc:PropertyName>
     * <ogc:Literal>%free%</ogc:Literal>
     * </ogc:PropertyIsLike>
     * <ogc:PropertyIsLike wildCard="%" singleChar="_" escapeChar="\">
     * <ogc:PropertyName>mainsearch_FR</ogc:PropertyName>
     * <ogc:Literal>%free%</ogc:Literal>
     * </ogc:PropertyIsLike>
     * </ogc:Or>
     * <ogc:PropertyIsLike wildCard="%" singleChar="_" escapeChar="\">
     * <ogc:PropertyName>titleFR</ogc:PropertyName>
     * <ogc:Literal>%title%</ogc:Literal>
     * </ogc:PropertyIsLike>
     * <ogc:Or>
     * <ogc:And>
     * <ogc:Or>
     * <ogc:PropertyIsEqualTo>
     * <ogc:PropertyName>fileId</ogc:PropertyName>
     * <ogc:Literal>a876b544-5be3-4250-93f8-d5852fe05256</ogc:Literal>
     * </ogc:PropertyIsEqualTo>
     * <ogc:PropertyIsEqualTo>
     * <ogc:PropertyName>fileId</ogc:PropertyName>
     * <ogc:Literal>a876b544-5be3-4250-93f8-d5852fe05256</ogc:Literal>
     * </ogc:PropertyIsEqualTo>
     * </ogc:Or>
     * <ogc:PropertyIsEqualTo>
     * <ogc:PropertyName>harvested</ogc:PropertyName>
     * <ogc:Literal>false</ogc:Literal>
     * </ogc:PropertyIsEqualTo>
     * </ogc:And>
     * <ogc:PropertyIsEqualTo>
     * <ogc:PropertyName>harvested</ogc:PropertyName>
     * <ogc:Literal>true</ogc:Literal>
     * </ogc:PropertyIsEqualTo>
     * </ogc:Or>
     * </ogc:And>
     * </ogc:Filter>
     */
    //TODO : le type de contrainte peut être soit XML soit CQL
    //-> le support du CQl en POST n'est pas présent
    @SuppressWarnings({"unchecked", "rawtypes", "deprecation"})
    public StringBuffer addFilterOnDataAccessible(String ogcSearchFilter, StringBuffer param) {
        SAXBuilder sxb = new SAXBuilder();
        try {
            InputStream xml = new StringBufferInputStream(param.toString());
            Document docParent = sxb.build(xml);
            Element racine = docParent.getRootElement();
            Element elementQuery = racine.getChild("Query", nsCSW);
            Element elementConstraint = null;
            Element elementFilter = null;
            Element elementAnd = null;
            Element elementOr = null;

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
            //Get the list of authorized Ids
            List<Map<String, Object>> ids = getAccessibleDataIds();

            Element secondElementAnd = new Element("And", nsOGC);

            //No Metadata accessible : set explicitly a fake metadata id in the request
            if (!isAllEasySDIDataAccessible() && (ids == null || ids.size() == 0)) {
                //<Or>
                //Add the "Or" node
                elementOr = new Element("Or", nsOGC);
                elementAnd.addContent(elementOr);

                //<And>
                //Add a "and" node
                elementOr.addContent(secondElementAnd);

                //<Or>
                //Add a "Or" node
                Element secondElementOr = new Element("Or", nsOGC);
                secondElementAnd.addContent(secondElementOr);
                Element elementProperty = new Element("PropertyIsEqualTo", nsOGC);
                secondElementOr.addContent(elementProperty);
                Element elementName = new Element("PropertyName", nsOGC);
                elementProperty.addContent(elementName);
                elementName.setText(ogcSearchFilter);
                Element elementLiteral = new Element("Literal", nsOGC);
                elementProperty.addContent(elementLiteral);
                elementLiteral.setText("-1");

                Element elementHarvestedValueFalse = new Element("PropertyIsEqualTo", nsOGC);
                secondElementAnd.addContent(elementHarvestedValueFalse);
                Element elementName1 = new Element("PropertyName", nsOGC);
                elementHarvestedValueFalse.addContent(elementName1);
                elementName1.setText("harvested");
                Element elementLiteral1 = new Element("Literal", nsOGC);
                elementHarvestedValueFalse.addContent(elementLiteral1);
                elementLiteral1.setText("false");

                if (policy.isCsw_includeharvested()) {
                    Element elementHarvestedValueTrue = new Element("PropertyIsEqualTo", nsOGC);
                    elementOr.addContent(elementHarvestedValueTrue);
                    Element elementName2 = new Element("PropertyName", nsOGC);
                    elementHarvestedValueTrue.addContent(elementName2);
                    elementName2.setText("harvested");
                    Element elementLiteral2 = new Element("Literal", nsOGC);
                    elementHarvestedValueTrue.addContent(elementLiteral2);
                    elementLiteral2.setText("true");
                }
            } else if (isAllEasySDIDataAccessible() && (ids == null || ids.size() == 0)) {
                //There is only a geographical restriction defined in the policy

                //If harvested MD have to be excluded
                if (!policy.isCsw_includeharvested()) {
                    Element elementHarvestedValueFalse = new Element("PropertyIsEqualTo", nsOGC);
                    elementAnd.addContent(elementHarvestedValueFalse);
                    Element elementName = new Element("PropertyName", nsOGC);
                    elementHarvestedValueFalse.addContent(elementName);
                    elementName.setText("harvested");
                    Element elementLiteral = new Element("Literal", nsOGC);
                    elementHarvestedValueFalse.addContent(elementLiteral);
                    elementLiteral.setText("false");
                }
            } else {
                //<Or>
                //Add the "Or" node
                elementOr = new Element("Or", nsOGC);
                elementAnd.addContent(elementOr);

                //<And>
                //Add a "and" node
                elementOr.addContent(secondElementAnd);

                //<Or>
                //Add a "Or" node
                Element secondElementOr = new Element("Or", nsOGC);
                secondElementAnd.addContent(secondElementOr);
                //Add the list of authorized Ids
                for (int m = 0; m < ids.size(); m++) {
                    Element elementProperty = new Element("PropertyIsEqualTo", nsOGC);
                    secondElementOr.addContent(elementProperty);
                    Element elementName = new Element("PropertyName", nsOGC);
                    elementProperty.addContent(elementName);
                    elementName.setText(ogcSearchFilter);
                    Element elementLiteral = new Element("Literal", nsOGC);
                    elementProperty.addContent(elementLiteral);
                    elementLiteral.setText(ids.get(m).get("guid").toString());
                }

                Element elementHarvestedValueFalse = new Element("PropertyIsEqualTo", nsOGC);
                secondElementAnd.addContent(elementHarvestedValueFalse);
                Element elementName = new Element("PropertyName", nsOGC);
                elementHarvestedValueFalse.addContent(elementName);
                elementName.setText("harvested");
                Element elementLiteral = new Element("Literal", nsOGC);
                elementHarvestedValueFalse.addContent(elementLiteral);
                elementLiteral.setText("false");

                if (policy.isCsw_includeharvested()) {
                    Element elementHarvestedValueTrue = new Element("PropertyIsEqualTo", nsOGC);
                    elementOr.addContent(elementHarvestedValueTrue);
                    Element elementName2 = new Element("PropertyName", nsOGC);
                    elementHarvestedValueTrue.addContent(elementName2);
                    elementName2.setText("harvested");
                    Element elementLiteral2 = new Element("Literal", nsOGC);
                    elementHarvestedValueTrue.addContent(elementLiteral2);
                    elementLiteral2.setText("true");
                }
            }

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
        } catch (Exception e) {
            e.printStackTrace();
            return null;
        }
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
    public boolean isMetadataAccessible(){
        return true;
    }
    
    /**
     * 
     * @return boolean
     */
    public boolean isAllMetadataAccessible(){
        if(policy.isCsw_includeharvested() &&
           policy.isCsw_anyresourcetype() &&
           policy.isCsw_anyvisibility()   &&
           policy.isCsw_anystate()     )
            return true;
        return false;
    }
    
    public StringBuffer addPolicyRestrictionFilters(String ogcSearchFilter, StringBuffer param) {
        SAXBuilder sxb = new SAXBuilder();
        try {
            InputStream xml = new StringBufferInputStream(param.toString());
            Document docParent = sxb.build(xml);
            Element racine = docParent.getRootElement();
            Element elementQuery = racine.getChild("Query", nsCSW);
            Element elementConstraint = null;
            Element elementFilter = null;
            Element elementAnd = null;
            Element elementOr = null;

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
            if(restrictionfilter != null){
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
        } catch (Exception e) {
            e.printStackTrace();
            return null;
        }
    }

    /**
     * 
     * @return String
     */
    public String buildCSWXMLFilter() {
        String filter = "<ogc:And>";
        if (!policy.isCsw_includeharvested()) {
            filter += getIsHarvestedFilter();
        }
        if (!policy.isCsw_anyresourcetype()) {
            filter += getResourceTypeFilter();
        }
        if (!policy.isCsw_anyvisibility()) {
            filter += getVisibilityFilter();
        }
        if (!policy.isCsw_anystate()) {
            filter += getMetadatastatefilter();
        }
        filter += "</ogc:And>";
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
    public String getIsHarvestedFilter() {
        if (policy.isCsw_includeharvested()) {
            return null;
        }

        String filter = "<ogc:PropertyIsEqualTo>";
        filter += "<ogc:PropertyName>harvested</ogc:PropertyName>";
        filter += "<ogc:Literal>false</ogc:Literal>";
        filter += "</ogc:PropertyIsEqualTo>";
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
    public String getResourceTypeFilter() {
        if (policy.isCsw_anyresourcetype()) {
            return null;
        }
        String filter = "<ogc:Or>";
        for (SdiResourcetype resourcetype : policy.getSdiResourcetypes()) {
            filter += "<ogc:PropertyIsEqualTo>";
            filter += "<ogc:PropertyName>resourcetype</ogc:PropertyName>";
            filter += "<ogc:Literal>" + resourcetype.getAlias() + "</ogc:Literal>";
            filter += "</ogc:PropertyIsEqualTo>";
        }
        filter += "</ogc:Or>";

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
    public String getVisibilityFilter() {
        if (policy.isCsw_anyvisibility()) {
            return null;
        }

        if (policy.getCswSdiSysAccessscope().getValue().equals("public")) {
            return null;
        }

        String filter = "<ogc:And>";
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
    public String getMetadatastatefilter() {
        if (policy.isCsw_anystate()) {
            return null;
        }
        String filter = "<ogc:Or>";
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
        return null;
    }
}

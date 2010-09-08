package org.easysdi.proxy.csw;

import java.io.File;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;
import java.util.Map;

import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;

import org.easysdi.proxy.policy.Policy;
import org.easysdi.security.JoomlaProvider;
import org.w3c.dom.*;
import org.springframework.dao.IncorrectResultSizeDataAccessException;


public class CSWProxyDataAccessibilityManager {
	
	private Policy policy;
	private JoomlaProvider joomlaProvider;
	private String dataIdVersionAccessible;
	
	/**
	 * @param dataIdVersionAccessible the dataIdVersionAccessible to set
	 */
	public void setDataIdVersionAccessible(String dataIdVersionAccessible) {
		this.dataIdVersionAccessible = dataIdVersionAccessible;
	}

	/**
	 * @return the dataIdVersionAccessible
	 */
	public String getDataIdVersionAccessible() {
		return dataIdVersionAccessible;
	}
	
	public CSWProxyDataAccessibilityManager(Policy p_policy, JoomlaProvider p_joomlaProvider)
	{
		policy = p_policy;
		joomlaProvider = p_joomlaProvider;
	}
	
	public boolean isDataVersionAccessible (String dataId)
	{
		if(!policy.getObjectVersion().getVersionModes().contains("all"))
		{
			String queryVersion= "SELECT m.guid as guid, MAX(v.title) as title FROM "+ joomlaProvider.getPrefix() +"sdi_metadata m ";
			queryVersion += " INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_objectversion v ON v.metadata_id = m.id ";
			queryVersion += " WHERE v.object_id IN (SELECT object_id FROM "+ joomlaProvider.getPrefix() +"sdi_objectversion WHERE metadata_id IN (SELECT id  FROM "+ joomlaProvider.getPrefix() +"sdi_metadata WHERE guid='"+dataId+"')) ";
			queryVersion +=" ";
			
			
			try
			{
				Map<String, Object> results= joomlaProvider.sjt.queryForMap(queryVersion);
				if(results.containsValue(dataId))
				{
					return true;
				}
				else
				{
					try
					{
						setDataIdVersionAccessible((String) results.get("guid"));
						return false;
					}
					catch (Exception ex)
					{
						return false;
					}
				}
			}
			catch (Exception ex)
			{
				return false;
			}
		}
		return true;
	}
	
	public boolean isAllDataAccessible ()
	{
		if(policy.getObjectStatus().isAll() && policy.getObjectVisibilities().isAll() && policy.getObjectVersion().getVersionModes().contains("all"))
		{
			return true;
		}
		return false;
	}
	
	public boolean isDataAccessible(String dataId)
	{
		if(policy.getObjectStatus().isAll() && policy.getObjectVisibilities().isAll() && policy.getObjectVersion().getVersionModes().contains("all"))
		{
			return true;
		}
		
		String query;
		String subQueryStatus="";
		String subQueryVisibility="";
		
		if (!policy.getObjectStatus().isAll())
		{
			subQueryStatus = getStatusSQLQuery(policy.getObjectStatus().getStatus());
		}
		if(!policy.getObjectVisibilities().isAll())
		{
			subQueryVisibility = getVisibilitySQLQuery(policy.getObjectVisibilities().getVisibilities());
		}
		
		query = "SELECT m.guid, m.id FROM "+ joomlaProvider.getPrefix() +"sdi_metadata m ";
		query += subQueryVisibility;
		if(subQueryVisibility != "")
			query += " AND ";
		else
			query += " WHERE ";
		query += "  m.guid='"+dataId+"'";
		query += subQueryStatus;
		
		try
		{
			Map<String, Object> results= joomlaProvider.sjt.queryForMap(query);
			return true;
		}
		catch (IncorrectResultSizeDataAccessException ex)
		{
			return false;
		}
				
		
	}
	
	private String getStatusSQLQuery (List <String> statusList)
	{
		String query = " AND m.metadatastate_id IN (SELECT ls.id FROM "+ joomlaProvider.getPrefix() +"sdi_list_metadatastate ls WHERE ls.code IN (";
		 for (int i=0; i< statusList.size(); i++)
		  {
			  query += "'"+ statusList.get(i)+"'";
			  if (i != statusList.size()-1)
				  query +=",";
		  }
		 query += ") )";
		
		return query;
	}
	
	private String getVisibilitySQLQuery (List <String> visibilityList)
	{
		String query = " INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_objectversion ov ON ov.metadata_id = m.id";
		query += " INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_object o ON ov.object_id = o.id";
		query += " WHERE o.visibility_id IN (SELECT lv.id FROM "+ joomlaProvider.getPrefix() +"sdi_list_visibility lv WHERE lv.code IN (";
		 for (int i=0; i< visibilityList.size(); i++)
		  {
			  query += "'"+visibilityList.get(i)+"'";
			  if (i != visibilityList.size()-1)
				  query +=",";
		  }
		 query += ") )";
		
		return query;
	}
	
	protected StringBuffer generateEmptyResponse(String version) {
		StringBuffer sb = new StringBuffer("<?xml version='1.0' encoding='utf-8' ?>");
		sb.append("<csw:GetRecordByIdResponse xmlns:csw=\"http://www.opengis.net/cat/csw/");
		sb.append(version);
		sb.append("\"/>");
		return sb;
	}

	public List <String> extractRecordIDFromGetRecordsResponse (File response, String outputSchema)
	{
		List <String> recordIds = new ArrayList<String>();
		try
		{
			DocumentBuilderFactory dbf = DocumentBuilderFactory.newInstance();
			DocumentBuilder db = dbf.newDocumentBuilder();
			Document doc = db.parse(response);
			doc.getDocumentElement().normalize();
			
			if(outputSchema == null || outputSchema =="" || CSWProxyServlet2.cswOutputSchemas.contains(outputSchema))
			{
				NodeList nodeLst = doc.getElementsByTagName("dc:identifier");
							
				for (int s = 0; s < nodeLst.getLength(); s++) {
					Node fstNode = nodeLst.item(s);
					String id = fstNode.getFirstChild().getNodeValue();
					recordIds.add(id);
				}
				return recordIds;
			}
			else if (CSWProxyServlet2.gmdOutputSchemas.contains(outputSchema))
			{
				NodeList nodeLst = doc.getElementsByTagName("gmd:fileIdentifier");
				
				for (int s = 0; s < nodeLst.getLength(); s++) {
					Node fstNode = nodeLst.item(s);
					NodeList chldNodes = fstNode.getChildNodes();
					Node idNode = null;
					for(int c = 0 ;c<chldNodes.getLength();c++)
					{
						Node pcNode = chldNodes.item(c);
						if(pcNode.getNodeName() != "gco:CharacterString")
						{
							continue;
						}
						else
						{
							idNode = pcNode;
							break;
						}
					}
					if(idNode != null)
					{
						String id =idNode.getFirstChild().getNodeValue();
						recordIds.add(id);
					}
				}
				return recordIds;
			}
			return null;
		}
		catch (Exception ex)
		{
			return null;
		}
	}
	
	public List<Map<String,Object>> getAccessibleDataIds ()
	{
		//TODO : change prefix table 
		List<String> listDataIDs = Arrays.asList();
		List<Map<String,Object>> object_ids = null;
		List<Map<String,Object>> metadata_ids = null;
		List<Map<String,Object>> metadata_guids = null;
		String query;
		
		if(!policy.getObjectVersion().getVersionModes().contains("all"))
		{
			//Not all the versions are allowed, just the last one
			
			//Get object's id 
			query= "SELECT object_id FROM jos_sdi_objectversion  GROUP BY object_id ";
			object_ids = joomlaProvider.sjt.queryForList(query);	

			for(int i = 0 ; i <object_ids.size() ; i++)
			{
				query= "SELECT metadata_id  FROM jos_sdi_objectversion " +
						"WHERE object_id="+object_ids.get(i).get("object_id")+" " +
								"AND title=(SELECT MAX(title) FROM jos_sdi_objectversion WHERE object_id="+object_ids.get(i).get("object_id")+")";
				if(metadata_ids == null)
					metadata_ids = joomlaProvider.sjt.queryForList(query);
				else
					metadata_ids.addAll(joomlaProvider.sjt.queryForList(query));
			}
					
			//Get metadata's id for last version
			String idString ="";
			for (int i = 0 ; i<metadata_ids.size() ; i++)
			{
				idString += metadata_ids.get(i).get("metadata_id");
				if(i != metadata_ids.size()-1)
				{
					idString += ",";
				}
			}
			query= "SELECT m.guid FROM jos_sdi_metadata m " +
					"WHERE m.id IN ("+idString+")";
			metadata_guids = joomlaProvider.sjt.queryForList(query);	
		} 		
		
		if(!policy.getObjectVisibilities().isAll())
		{
			List<String> allowedVisibility = policy.getObjectVisibilities().getVisibilities();
			String visibilityString ="";
			for (int i = 0 ; i<allowedVisibility.size() ; i++)
			{
				visibilityString += "'"+ allowedVisibility.get(i) +"'";
				if(i != allowedVisibility.size()-1)
				{
					visibilityString += ",";
				}
			}
			if(metadata_guids != null)
			{
				String idString ="";
				for (int i = 0 ; i<metadata_guids.size() ; i++)
				{
					idString += "'"+ metadata_guids.get(i).get("guid") +"'";
					if(i != metadata_guids.size()-1)
					{
						idString += ",";
					}
				}
				
				query = " SELECT m.guid FROM jos_sdi_metadata m " +
						" INNER JOIN jos_sdi_objectversion ov ON ov.metadata_id = m.id" +
						" INNER JOIN jos_sdi_object o ON o.id = ov.object_id  " +
						" WHERE o.visibility_id IN " +
						"(SELECT id FROM jos_sdi_list_visibility WHERE code IN ("+visibilityString+"))" +
								"AND m.guid IN ("+idString+")";
			}
			else
			{
				query = " SELECT m.guid FROM jos_sdi_metadata m " +
						" INNER JOIN jos_sdi_objectversion ov ON ov.metadata_id = m.id" +
						" INNER JOIN jos_sdi_object o ON o.id = ov.object_id  " +
						" WHERE o.visibility_id IN " +
						"(SELECT id FROM jos_sdi_list_visibility WHERE code IN ("+visibilityString+"))" ;
				
			}
			metadata_guids = joomlaProvider.sjt.queryForList(query);
		}
		
		if (!policy.getObjectStatus().isAll())
		{
			List<String> allowedStatus = policy.getObjectStatus().getStatus();
			String statusString ="";
			for (int i = 0 ; i<allowedStatus.size() ; i++)
			{
				statusString += "'"+ allowedStatus.get(i)+"'";
				if(i != allowedStatus.size()-1)
				{
					statusString += ",";
				}
			}
			if(metadata_guids != null)
			{
				String idString ="";
				for (int i = 0 ; i<metadata_guids.size() ; i++)
				{
					idString += "'"+ metadata_guids.get(i).get("guid") +"'";
					if(i != metadata_guids.size()-1)
					{
						idString += ",";
					}
				}
				query = " SELECT m.guid FROM jos_sdi_metadata m " +
						" WHERE m.metadatastate_id IN " +
						"(SELECT id FROM jos_sdi_list_metadatastate WHERE code IN ("+statusString+"))" +
								"AND m.guid IN ("+idString+")";
			}
			else
			{
				query = " SELECT m.guid FROM jos_sdi_metadata m " +
						" WHERE m.metadatastate_id IN " +
						"(SELECT id FROM jos_sdi_list_metadatastate WHERE code IN ("+statusString+"))" ;
				
			}
			metadata_guids = joomlaProvider.sjt.queryForList(query);
		}
		
		
		
		return metadata_guids;
	}
	
	

}

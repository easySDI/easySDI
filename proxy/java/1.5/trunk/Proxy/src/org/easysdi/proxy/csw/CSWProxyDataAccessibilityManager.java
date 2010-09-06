package org.easysdi.proxy.csw;

import java.io.File;
import java.util.ArrayList;
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

	public List <String> extractRecordIDFromGetRecordsResponse (File response)
	{
		List <String> recordIds = new ArrayList<String>();
		
		try
		{
			DocumentBuilderFactory dbf = DocumentBuilderFactory.newInstance();
			DocumentBuilder db = dbf.newDocumentBuilder();
			Document doc = db.parse(response);
			doc.getDocumentElement().normalize();
			NodeList nodeLst = doc.getElementsByTagName("dc:identifier");
						
			for (int s = 0; s < nodeLst.getLength(); s++) {
				Node fstNode = nodeLst.item(s);
				String id = fstNode.getFirstChild().getNodeValue();
				recordIds.add(id);
			}
			return recordIds;
		}
		catch (Exception ex)
		{
			return null;
		}
	}
	
	

}

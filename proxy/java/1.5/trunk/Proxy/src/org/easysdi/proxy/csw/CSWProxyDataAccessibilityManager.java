package org.easysdi.proxy.csw;

import java.io.ByteArrayOutputStream;
import java.io.InputStream;
import java.io.StringBufferInputStream;
import java.util.Arrays;
import java.util.Date;
import java.util.Iterator;
import java.util.List;
import java.util.Map;
import org.easysdi.proxy.policy.Policy;
import org.easysdi.proxy.policy.Status;
import org.easysdi.security.JoomlaProvider;
import org.jdom.*;
import org.jdom.filter.Filter;
import org.jdom.input.SAXBuilder;
import org.jdom.output.Format;
import org.jdom.output.XMLOutputter;
import org.springframework.dao.IncorrectResultSizeDataAccessException;

import ch.interlis.interlis2.GM03V18.GM03ComprehensiveComprehensiveDQAbsoluteExternalPositionalAccuracy.DateTime;

/**
 * Access the database to retreive accessible metadatas
 * and rewrite request to send to the CSW service
 * @author DEPTH SA
 *
 */
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
	 * 
	 * @return
	 */
	public boolean isAllDataAccessible ()
	{
		if(    (policy.getObjectStatus() == null || policy.getObjectStatus().isAll()) 
			&& (policy.getObjectVisibilities() == null || policy.getObjectVisibilities().isAll()) 
			&& (policy.getObjectContexts()== null || policy.getObjectContexts().isAll())
			&& ( policy.getObjectVersion() == null || policy.getObjectVersion().getVersionModes().contains("all")))
		{
			return true;
		}
		return false;
	}
	
	/**
	 * @return the dataIdVersionAccessible
	 */
	public String getDataIdVersionAccessible() {
		return dataIdVersionAccessible;
	}
	
	/**
	 * 
	 * @param p_policy
	 * @param p_joomlaProvider
	 */
	public CSWProxyDataAccessibilityManager(Policy p_policy, JoomlaProvider p_joomlaProvider)
	{
		policy = p_policy;
		joomlaProvider = p_joomlaProvider;
	}
	
	/**
	 * 
	 * @param dataId
	 * @return
	 */
	public boolean isObjectAccessible (String dataId) 
	{
		if((policy.getObjectVisibilities() == null || policy.getObjectVisibilities().isAll())
				&& (policy.getObjectTypes()== null || policy.getObjectTypes().isAll()))
		{
			return true;
		}
		
		String listVisibility = "";
		if(policy.getObjectVisibilities() != null && !policy.getObjectVisibilities().isAll())
		{
			List<String>  allowedVisibility = policy.getObjectVisibilities().getVisibilities();
			for (int i = 0 ; i<allowedVisibility.size() ; i++)
			{
				listVisibility += "'"+ allowedVisibility.get(i)+"'";
				if(i != allowedVisibility.size()-1)
				{
					listVisibility += ",";
				}
			}
		}
		String listObjectType="";
		if(policy.getObjectTypes()!= null && !policy.getObjectTypes().isAll())
		{
			List<String>  allowedObjectTypes = policy.getObjectTypes().getObjectTypes();
			for (int i = 0 ; i<allowedObjectTypes.size() ; i++)
			{
				listObjectType += "'"+ allowedObjectTypes.get(i)+"'";
				if(i != allowedObjectTypes.size()-1)
				{
					listObjectType += ",";
				}
			}
		}
		try
		{
			String query =     " SELECT m.guid FROM "+ joomlaProvider.getPrefix() +"sdi_metadata m "+
			" INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_objectversion ov  ON m.id = ov.metadata_id "+
			" INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_object o ON o.id = ov.object_id "+
			" INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_objecttype ot ON o.objecttype_id = ot.id "+
			" INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_list_visibility v ON o.visibility_id = v.id ";
			if(listObjectType != "" || listVisibility != "")
			{
				query += " WHERE ";
				
			}
			if(listObjectType != "" )
			{
				query += " ot.code IN ("+ listObjectType +") ";
				if(listVisibility != "")
				{
					query += " AND v.code IN ("+ listVisibility +") ";
				}
				
			}
			else
			{
				query += " v.code IN ("+ listVisibility +") ";
			}
			query += " AND m.guid = '"+dataId+"'" ;
	
			Map<String, Object> resultfinal= joomlaProvider.sjt.queryForMap(query);
		}
		catch (IncorrectResultSizeDataAccessException ex)
		{
			return false;
		}
		return true;
	}
	
	/**
	 * 
	 * @param dataId
	 * @return
	 */
	public boolean isStatusAndVersionAccessible (String dataId)
	{
		if (policy.getObjectStatus()!= null && !policy.getObjectStatus().isAll())
		{
			//Get metadata status
			String query= "SELECT  m.guid as guid, m.published  as published, m.archived as archived, ms.code as state" ;
			query +=	" FROM "+ joomlaProvider.getPrefix() +"sdi_metadata m ";
			query +=	" INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_list_metadatastate ms ON m.metadatastate_id=ms.id ";
			query +=	" AND m.guid = '"+dataId+"' ";
			query +=	" LIMIT 0,1 ";
			
			Map<String, Object> metadataObject= joomlaProvider.sjt.queryForMap(query);
			String status = (String)metadataObject.get("state");
			
			
			String listObjectStatus="";	
			List<Status>  allowedStatus = policy.getObjectStatus().getStatus();

			for (int i = 0 ; i <allowedStatus.size() ; i++)
			{
				if (allowedStatus.get(i).getStatus().equalsIgnoreCase("validated") && ("validated").equalsIgnoreCase(status))
				{
					return true;
				}
				if (allowedStatus.get(i).getStatus().equalsIgnoreCase("unpublished") && ("unpublished").equalsIgnoreCase(status))
				{
					return true;
				}
				if (allowedStatus.get(i).getStatus().equalsIgnoreCase("archived") 
						&& ("archived").equalsIgnoreCase(status) 
						&& (((Date)metadataObject.get("archived")).compareTo(new Date()) < 0 ) )
				{
					return true;
				}
				if(allowedStatus.get(i).getStatus().equalsIgnoreCase("published") 
						&& allowedStatus.get(i).getVersion().equalsIgnoreCase("all")
						&& ((("published").equalsIgnoreCase(status) 
								&& (((Date)metadataObject.get("published")).compareTo(new Date()) < 0 ))
								||(("archived").equalsIgnoreCase(status) 
										&& (((Date)metadataObject.get("archived")).compareTo(new Date()) > 0 ) 
										&& (((Date)metadataObject.get("published")).compareTo(new Date()) < 0 ))))
				{
					return true;
				}
				if(allowedStatus.get(i).getStatus().equalsIgnoreCase("published") 
						&& allowedStatus.get(i).getVersion().equalsIgnoreCase("last")
						&& ((("published").equalsIgnoreCase(status) 
								&& (((Date)metadataObject.get("published")).compareTo(new Date()) < 0 ))
								||(("archived").equalsIgnoreCase(status) 
										&& (((Date)metadataObject.get("archived")).compareTo(new Date()) > 0 ) 
										&& (((Date)metadataObject.get("published")).compareTo(new Date()) < 0 ))))
				{
					//request with condition on last published metadata
					String queryVersion= "SELECT  m.guid as guid, m.published  as title ";
					queryVersion +=	"FROM "+ joomlaProvider.getPrefix() +"sdi_metadata m ";
					queryVersion +=	" INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_objectversion ov ON ov.metadata_id = m.id ";
					queryVersion +=	" INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_object o ON o.id = ov.object_id ";
					queryVersion +=	" INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_list_metadatastate ms ON m.metadatastate_id=ms.id ";
					queryVersion +=	" WHERE (ms.code='published' ";
					queryVersion +=	" AND o.id = (SELECT object_id FROM "+ joomlaProvider.getPrefix() +"sdi_objectversion WHERE metadata_id = (SELECT id FROM "+ joomlaProvider.getPrefix() +"sdi_metadata WHERE guid = '"+dataId+"')) ";
					queryVersion +=	" AND m.published <=CURDATE() )";
					queryVersion +=	" OR ( ms.code='archived' ";
					queryVersion +=	" AND o.id = (SELECT object_id FROM "+ joomlaProvider.getPrefix() +"sdi_objectversion WHERE metadata_id = (SELECT id FROM "+ joomlaProvider.getPrefix() +"sdi_metadata WHERE guid = '"+dataId+"')) ";
					queryVersion +=	" AND m.published <=CURDATE() AND m.archived > CURDATE())";
					queryVersion +=	" ORDER BY m.published DESC ";
					queryVersion +=	" LIMIT 0,1 ";
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
			}
			return false;
			
			
					
//			for (int i = 0 ; i<allowedStatus.size() ; i++)
//			{
//				if(("last").equalsIgnoreCase(allowedStatus.get(i).getVersion()))
//				{
////					List<Status>  allowedObjectStatus = policy.getObjectStatus().getStatus();
////					for (int j = 0 ; j<allowedObjectStatus.size() ; j++)
////					{
////						if(allowedObjectStatus.get(j).getStatus().equalsIgnoreCase("published"))
////								continue;
////						listObjectStatus += "'"+ allowedObjectStatus.get(j).getStatus()+"'";
////						if(j != allowedObjectStatus.size()-1)
////						{
////							listObjectStatus += ",";
////						}
////					}
//					
//					
//					//request with condition on last published metadata
//					String queryVersion= "SELECT  m.guid as guid, m.published  as title ";
//					queryVersion +=	"FROM "+ joomlaProvider.getPrefix() +"sdi_metadata m ";
//					queryVersion +=	" INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_objectversion ov ON ov.metadata_id = m.id ";
//					queryVersion +=	" INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_object o ON o.id = ov.object_id ";
//					queryVersion +=	" INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_list_metadatastate ms ON m.metadatastate_id=ms.id ";
//					queryVersion +=	" WHERE (ms.code='published' ";
//					queryVersion +=	" AND o.id = (SELECT object_id FROM "+ joomlaProvider.getPrefix() +"sdi_objectversion WHERE metadata_id = (SELECT id FROM "+ joomlaProvider.getPrefix() +"sdi_metadata WHERE guid = '"+dataId+"')) ";
//					queryVersion +=	" AND m.published <=CURDATE() )";
//					queryVersion +=	" OR ( ms.code='archived' ";
//					queryVersion +=	" AND o.id = (SELECT object_id FROM "+ joomlaProvider.getPrefix() +"sdi_objectversion WHERE metadata_id = (SELECT id FROM "+ joomlaProvider.getPrefix() +"sdi_metadata WHERE guid = '"+dataId+"')) ";
//					queryVersion +=	" AND m.published <=CURDATE() AND m.archived > CURDATE())";
//					queryVersion +=	" OR ms.code IN ("+listObjectStatus+") ";
//					queryVersion +=	" ORDER BY m.published DESC ";
//					queryVersion +=	" LIMIT 0,1 ";
//					try
//					{
//						Map<String, Object> results= joomlaProvider.sjt.queryForMap(queryVersion);
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
			//No 'last' condition
//			List<Status>  allowedObjectStatus = policy.getObjectStatus().getStatus();
//			for (int i = 0 ; i<allowedObjectStatus.size() ; i++)
//			{
//				listObjectStatus += "'"+ allowedObjectStatus.get(i).getStatus()+"'";
//				if(i != allowedObjectStatus.size()-1)
//				{
//					listObjectStatus += ",";
//				}
//			}
//			String queryVersion= "SELECT  m.guid as guid, m.published  as title ";
//			queryVersion +=	" FROM "+ joomlaProvider.getPrefix() +"sdi_metadata m ";
//			queryVersion +=	" INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_list_metadatastate ms ON m.metadatastate_id=ms.id ";
//			queryVersion +=	" WHERE ms.code IN ("+listObjectStatus+ ") ";
//			queryVersion +=	" AND m.guid = '"+dataId+"' ";
//			queryVersion +=	" LIMIT 0,1 ";
//			try
//			{
//				Map<String, Object> results= joomlaProvider.sjt.queryForMap(queryVersion);
//				if(results.containsValue(dataId))
//				{
//					return true;
//				}
//				else
//				{
//					return false;
//				}
//			}
//			catch (Exception ex)
//			{
//				return false;
//			}
//		}
		}
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

	
	public List<Map<String,Object>> getAccessibleDataIds ()
	{
		List<String> listDataIDs = Arrays.asList();
		List<Map<String,Object>> object_ids = null;
		List<Map<String,Object>> metadata_ids = null;
		List<Map<String,Object>> metadata_guids = null;
		String query;
		
		//Accessible objects
		if((policy.getObjectVisibilities() != null && !policy.getObjectVisibilities().isAll()) 
				|| (policy.getObjectTypes()!= null && !policy.getObjectTypes().isAll()))
		{
			String listVisibility = "";
			if(policy.getObjectVisibilities() != null && !policy.getObjectVisibilities().isAll())
			{
				List<String>  allowedVisibility = policy.getObjectVisibilities().getVisibilities();
				for (int i = 0 ; i<allowedVisibility.size() ; i++)
				{
					listVisibility += "'"+ allowedVisibility.get(i)+"'";
					if(i != allowedVisibility.size()-1)
					{
						listVisibility += ",";
					}
				}
			}
			String listObjectType="";
			if(policy.getObjectTypes()!= null && !policy.getObjectTypes().isAll())
			{
				List<String>  allowedObjectTypes = policy.getObjectTypes().getObjectTypes();
				for (int i = 0 ; i<allowedObjectTypes.size() ; i++)
				{
					listObjectType += "'"+ allowedObjectTypes.get(i)+"'";
					if(i != allowedObjectTypes.size()-1)
					{
						listObjectType += ",";
					}
				}
			}
			
			query =     " SELECT m.guid FROM "+ joomlaProvider.getPrefix() +"sdi_metadata m "+
			" INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_objectversion ov  ON m.id = ov.metadata_id "+
			" INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_object o ON o.id = ov.object_id "+
			" INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_objecttype ot ON o.objecttype_id = ot.id "+
			" INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_list_visibility v ON o.visibility_id = v.id ";
			if(listObjectType != "" || listVisibility != "")
			{
				query += " WHERE ";
				
			}
			if(listObjectType != "" )
			{
				query += " ot.code IN ("+ listObjectType +") ";
				if(listVisibility != "")
				{
					query += " AND v.code IN ("+ listVisibility +") ";
				}
				
			}
			else
			{
				query += " v.code IN ("+ listVisibility +") ";
			}
			
			if(metadata_ids == null)
				metadata_ids = joomlaProvider.sjt.queryForList(query);
			else
				metadata_ids.addAll(joomlaProvider.sjt.queryForList(query));
		}
		
		//Status & version
		if(policy.getObjectStatus() != null && !policy.getObjectStatus().isAll())
		{
			
		}
		
//		if(policy.getObjectVersion()!= null && !policy.getObjectVersion().getVersionModes().contains("all"))
//		{
//			//Not all the versions are allowed, just the last one
//			
//			//Get object's id 
//			query= "SELECT object_id FROM "+ joomlaProvider.getPrefix() +"sdi_objectversion  GROUP BY object_id ";
//			object_ids = joomlaProvider.sjt.queryForList(query);	
//
//			for(int i = 0 ; i <object_ids.size() ; i++)
//			{
////				query = " SELECT metadata_id  FROM "+ joomlaProvider.getPrefix() +"sdi_objectversion " +
////					    " WHERE object_id="+object_ids.get(i).get("object_id")+" " +
////					    " AND created=(SELECT MAX(created) FROM "+ joomlaProvider.getPrefix() +"sdi_objectversion " +
////					    " WHERE object_id="+object_ids.get(i).get("object_id")+
////					    " AND metadata_id IN (SELECT id FROM "+ joomlaProvider.getPrefix() +"sdi_metadata WHERE " +
////					    " metadatastate_id = (SELECT ls.id FROM "+ joomlaProvider.getPrefix() +"sdi_list_metadatastate ls " +
////					    " WHERE ls.code='published' )))";
////				query = " SELECT metadata_id  FROM "+ joomlaProvider.getPrefix() +"sdi_objectversion " +
////			    " WHERE object_id="+object_ids.get(i).get("object_id")+" " +
////			    " AND created=(SELECT MAX(created) FROM "+ joomlaProvider.getPrefix() +"sdi_objectversion " +
////			    " WHERE object_id="+object_ids.get(i).get("object_id")+")";
//		
//				//HVH - 22.11.2010 : Bug Fix returns the last published version of the metadata 
//				query = "  SELECT ov.metadata_id "+
//                " FROM "+ joomlaProvider.getPrefix() +"sdi_objectversion ov "+ 
//                " INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_metadata m ON ov.metadata_id=m.id "+
//                " INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_list_metadatastate ms ON m.metadatastate_id=ms.id "+
//                " INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_object o ON ov.object_id=o.id " + 
//                " INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_list_visibility v ON o.visibility_id=v.id "+
//                " WHERE o.id="+object_ids.get(i).get("object_id")+" " +
//                        " AND ms.code='published' "+
//                        " AND m.published <=CURDATE() "+ 
//                " ORDER BY m.published DESC " +
//                " LIMIT 0,1 ";
//
//				if(metadata_ids == null)
//					metadata_ids = joomlaProvider.sjt.queryForList(query);
//				else
//					metadata_ids.addAll(joomlaProvider.sjt.queryForList(query));
//			}
//					
//			//Get metadata's id for last version
//			String idString ="";
//			for (int i = 0 ; i<metadata_ids.size() ; i++)
//			{
//				idString += metadata_ids.get(i).get("metadata_id");
//				if(i != metadata_ids.size()-1)
//				{
//					idString += ",";
//				}
//			}
//			query= "SELECT m.guid FROM "+ joomlaProvider.getPrefix() +"sdi_metadata m " +
//					"WHERE m.id IN ("+idString+")";
//			metadata_guids = joomlaProvider.sjt.queryForList(query);
//			
//			if(metadata_guids.size()==0)
//				return metadata_guids;
//		} 		
		
		if(policy.getObjectVisibilities()!= null && !policy.getObjectVisibilities().isAll())
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
			if(metadata_guids != null && metadata_guids.size() > 0)
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
				
				query = " SELECT m.guid FROM "+ joomlaProvider.getPrefix() +"sdi_metadata m " +
						" INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_objectversion ov ON ov.metadata_id = m.id" +
						" INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_object o ON o.id = ov.object_id  " +
						" INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_list_visibility v ON v.id = o.visibility_id " +
						" WHERE v.code IN ("+visibilityString+")" +
						" AND m.guid IN ("+idString+")";
			}
			else
			{
				query = " SELECT m.guid FROM "+ joomlaProvider.getPrefix() +"sdi_metadata m " +
						" INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_objectversion ov ON ov.metadata_id = m.id" +
						" INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_object o ON o.id = ov.object_id  " +
						" INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_list_visibility v ON v.id = o.visibility_id " +
						" WHERE v.code IN ("+visibilityString+")" ;
				
			}
			metadata_guids = joomlaProvider.sjt.queryForList(query);
			
			if(metadata_guids.size()==0)
				return metadata_guids;
		}
		
		if (policy.getObjectStatus()!= null && !policy.getObjectStatus().isAll())
		{
			
			List<Status>  allowedStatus = policy.getObjectStatus().getStatus();
			
			String statusString ="";
			for (int i = 0 ; i<allowedStatus.size() ; i++)
			{
				statusString += "'"+ allowedStatus.get(i).getStatus()+"'";
				if(i != allowedStatus.size()-1)
				{
					statusString += ",";
				}
			}
			if(metadata_guids != null && metadata_guids.size() > 0)
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
				query = " SELECT m.guid FROM "+ joomlaProvider.getPrefix() +"sdi_metadata m " +
						" INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_list_metadatastate ms ON ms.id = m.metadatastate_id " +
						" WHERE ms.code IN ("+statusString+") " +
						" AND m.guid IN ("+idString+")";
			}
			else
			{
				query = " SELECT m.guid FROM "+ joomlaProvider.getPrefix() +"sdi_metadata m " +
						" INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_list_metadatastate ms ON ms.id = m.metadatastate_id " +
						" WHERE ms.code IN ("+statusString+")" ;
				
			}
			metadata_guids = joomlaProvider.sjt.queryForList(query);
			
			if(metadata_guids.size()==0)
				return metadata_guids;
		}
		
		if(policy.getObjectContexts()!= null && !policy.getObjectContexts().isAll())
		{
			List<String> allowedContext = policy.getObjectContexts().getContexts();
			String contextString ="";
			for (int i = 0 ; i<allowedContext.size() ; i++)
			{
				contextString += "'"+ allowedContext.get(i)+"'";
				if(i != allowedContext.size()-1)
				{
					contextString += ",";
				}
			}
			if(metadata_guids != null && metadata_guids.size() > 0)
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
				query =     " SELECT m.guid FROM "+ joomlaProvider.getPrefix() +"sdi_metadata m "+
							" INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_objectversion ov  ON m.id = ov.metadata_id "+
							" INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_object o ON o.id = ov.object_id "+
							" INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_objecttype ot ON o.objecttype_id = ot.id "+
							" INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_context_objecttype co ON co.objecttype_id = ot.id "+
							" INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_context c ON c.id = co.context_id "+
							" WHERE c.code IN ("+contextString+")" +
							" AND m.guid IN ("+idString+")" ;
			}
			else
			{
				query =     " SELECT m.guid FROM jos_sdi_metadata m "+
							" INNER JOIN  jos_sdi_objectversion ov  ON m.id = ov.metadata_id "+
							" INNER JOIN jos_sdi_object o ON o.id = ov.object_id "+
							" INNER JOIN jos_sdi_objecttype ot ON o.objecttype_id = ot.id "+
							" INNER JOIN jos_sdi_context_objecttype co ON co.objecttype_id = ot.id "+
							" INNER JOIN jos_sdi_context c ON c.id = co.context_id "+
							" WHERE c.code IN ("+contextString+")" ;
			}
			metadata_guids = joomlaProvider.sjt.queryForList(query);
			
			if(metadata_guids.size()==0)
				return metadata_guids;
			
		}
		return metadata_guids;
	}
	
	/**
	 * Add a <and> block in a GetRecords <Filter> block containing the accessible metadata's Id
	 * to restrain the result send back by the csw service to those allowed by the current policy
	 * @param ogcSearchFilter
	 * @param param
	 * @param ids
	 * @return
	 */
	public StringBuffer addFilterOnDataAccessible (String ogcSearchFilter, StringBuffer param)
	{
		Namespace nsCSW =  Namespace.getNamespace("http://www.opengis.net/cat/csw/2.0.2");
		Namespace nsOGC =  Namespace.getNamespace("http://www.opengis.net/ogc");
		
		SAXBuilder sxb = new SAXBuilder();
		try 
		{
			InputStream xml = new StringBufferInputStream(param.toString());
	    	Document  docParent = sxb.build(xml);
	    	Element racine = docParent.getRootElement();
	    	Element elementQuery = racine.getChild("Query", nsCSW);
	    	Element elementConstraint = null;
	    	Element elementFilter = null;
	    	Element elementAnd = null;
	    	Element elementOr = null;
	    	
	    	Filter filtre = new CSWProxyMetadataConstraintFilter();
	    	Iterator it= docParent.getDescendants(filtre);
			  
	    	
			while(it.hasNext())
			{
				elementConstraint = (Element)it.next();
			}
			
			if (elementConstraint == null)
			{
				//No constraint defined
				elementConstraint = new Element("Constraint",nsCSW);
					elementQuery.addContent(elementConstraint);
				elementConstraint.setAttribute("version", "1.1.0");
				elementFilter = new Element("Filter",nsOGC);
					elementConstraint.addContent(elementFilter);
			}
			else
			{
				//Constraint already exists
				elementFilter = elementConstraint.getChild("Filter", nsOGC);
				if (elementFilter == null)
				{
					elementFilter = elementConstraint.getChild("Filter");
				}
				if (elementFilter == null)
				{
					elementFilter = new Element("Filter",nsOGC);
						elementConstraint.addContent(elementFilter);
				}
			}
			
			
			List<Element> filterChildren = elementFilter.getChildren();
			for(int i = 0 ; i<filterChildren.size() ; i++)
			{
				if("And".equalsIgnoreCase(filterChildren.get(i).getName()))
				{
					elementAnd = filterChildren.get(i);
					break;
				}
			}
			
			
			//Create the and node if not already exists
			if(elementAnd==null)
			{
				elementAnd = new Element("And",nsOGC);
				for(int i = filterChildren.size()-1  ; i>=0 ; i--)
				{
					elementAnd.addContent(filterChildren.get(i).detach());
				}
				elementFilter.addContent(elementAnd);
			}
			
			
			//<Or>
			//Add the "Or" node
			elementOr = new Element("Or", nsOGC);
			elementAnd.addContent(elementOr);
			List<Map<String,Object>> ids = getAccessibleDataIds();
			for (int m = 0; m<ids.size() ; m++)
			{
				Element elementProperty = new Element("PropertyIsEqualTo", nsOGC);
					elementOr.addContent(elementProperty);
				Element elementName = new Element("PropertyName", nsOGC);
					elementProperty.addContent(elementName);
				elementName.setText(ogcSearchFilter);
				Element elementLiteral = new Element("Literal", nsOGC);
					elementProperty.addContent(elementLiteral);
				elementLiteral.setText(ids.get(m).get("guid").toString());
			}

			
			//Return
			XMLOutputter sortie = new XMLOutputter(Format.getPrettyFormat());
			ByteArrayOutputStream result =new ByteArrayOutputStream ();
	        sortie.output(docParent,result );

			return new StringBuffer(result.toString());
		} 
		catch (Exception e) 
		{
			e.printStackTrace();
			return null;
		}
	}
	
	public String addFilterOnDataAccessible (String ogcSearchFilter, String param,  List<Map<String,Object>> ids)
	{
		
//		try 
//		{
//			//Document builder
//			DocumentBuilderFactory dbf = DocumentBuilderFactory.newInstance();
//			dbf.setNamespaceAware(true);
//			DocumentBuilder db;
//			db = dbf.newDocumentBuilder();
//			InputStream xml = new StringBufferInputStream(param);
//			Document doc = db.parse(xml);
//			doc.getDocumentElement().normalize();
//			
//			
//			String docPrefix = doc.getDocumentElement().getPrefix();
//			String docUri = doc.getDocumentElement().getNamespaceURI();
//			if(docPrefix != null)
//				docPrefix = docPrefix+":";
//			else
//				docPrefix ="";
//			
//			Node filterNode = null;
//				
//			NodeList filterNodes = doc.getElementsByTagNameNS("*", "Filter");
//			if(filterNodes.getLength() == 0)
//			{
//				//Create filter node
//				filterNode = doc.createElementNS("http://www.opengis.net/ogc", "Filter");
//			}
//			else
//			{
//				filterNode = doc.getElementsByTagNameNS("*", "Filter").item(0);
//			}
//			
//			
//			//<Filter>
//			String uri =  filterNode.getNamespaceURI();
//			String prefix = filterNode.getPrefix();
//			
//			if(prefix == null)
//				prefix = "";
//			else
//				prefix = prefix+":";
//			
//			//<And>
//			//Get the "And" Node
//			NodeList filterChildNodes =  filterNode.getChildNodes();
//			Boolean isAndExists = false;
//			Node and = null;
//			for (int i = 0 ; i< filterChildNodes.getLength() ; i++)
//			{
//				if(("And").equalsIgnoreCase(filterChildNodes.item(i).getLocalName()))
//				{
//					isAndExists = true;
//					and = filterChildNodes.item(i);
//					break;
//				}
//			}			
//			//Create the and node if not already exists
//			if(!isAndExists)
//			{
//				and = buildAndNodeTofilterOnDataID(doc,uri,prefix,filterChildNodes, filterNode);
//			}
//			
//			//<Or>
//			//Add the "Or" node
//			and.appendChild(buildOrNodeToFilterOnDataId(ogcSearchFilter, doc, uri,prefix, ids));
//			
//			//Return
//			DOMSource domSource = new DOMSource(doc);
//			StringWriter writer = new StringWriter();
//			StreamResult result = new StreamResult(writer);
//			TransformerFactory tf = TransformerFactory.newInstance();
//			Transformer transformer = tf.newTransformer();
//			transformer.transform(domSource, result);
//			String constraint = writer.toString().substring(38);
//			return constraint;
//		} 
//		catch (Exception e) 
//		{
//			e.printStackTrace();
//		}
		
		return param;
	}
}

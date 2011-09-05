package org.easysdi.proxy.csw;

import java.io.ByteArrayOutputStream;
import java.io.InputStream;
import java.io.StringBufferInputStream;
import java.text.SimpleDateFormat;
import java.util.Arrays;
import java.util.Date;
import java.util.Iterator;
import java.util.List;
import java.util.Map;

import org.easysdi.jdom.filter.ElementConstraintFilter;
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
		if(     (policy.getObjectVisibilities() == null || policy.getObjectVisibilities().isAll()) 
			&& (policy.getObjectTypes()== null || policy.getObjectTypes().isAll())
			&& (policy.getObjectStatus()== null || policy.getObjectStatus().isAll()))
		{
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
				&& (policy.getObjectTypes()== null || policy.getObjectTypes().isAll())
				&& (policy.getObjectStatus()== null || policy.getObjectStatus().isAll()))
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
	public boolean isMetadataAccessible (String dataId)
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
	
	protected StringBuffer generateEmptyResponseForGetRecords(String version) {
		Date now = new Date();
        SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ss");
        String dateToSend =  sdf.format( now );
		StringBuffer sb = new StringBuffer("<?xml version='1.0' encoding='utf-8' ?>");
		sb.append("<csw:GetRecordsResponse xmlns:csw=\"http://www.opengis.net/cat/csw/");
		sb.append(version);
		sb.append("\">");
		sb.append("<csw:SearchStatus timestamp=\""+dateToSend+"\"/>");
		sb.append("<csw:SearchResults numberOfRecordsMatched=\"0\" numberOfRecordsReturned=\"0\" nextRecord=\"0\"/>");
		sb.append("</csw:GetRecordsResponse>");
		return sb;
	}
	
	public List<Map<String,Object>> getAccessibleDataIds ()
	{
		List<Map<String,Object>> object_ids = null;
		List<Map<String,Object>> metadata_ids = null;
		List<Map<String,Object>> final_metadata_ids = null;
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
			//metatadata id allowed by the filter on object
			String tempMetadataIdString ="";
			if(metadata_ids != null)
			{
				for (int k = 0 ; k<metadata_ids.size() ; k++)
				{
					tempMetadataIdString += "'" + metadata_ids.get(k).get("guid") + "'";
					if(k != metadata_ids.size()-1)
					{
						tempMetadataIdString += ",";
					}
				}
			}
			
			query = "SELECT m.guid FROM "+ joomlaProvider.getPrefix() +"sdi_metadata m" +
			" INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_objectversion ov  ON m.id = ov.metadata_id "+
			" INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_object o ON o.id = ov.object_id "+
			" INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_objecttype ot ON o.objecttype_id = ot.id "+
			" INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_list_metadatastate ms ON m.metadatastate_id=ms.id "+
			" WHERE ";
			
			if(tempMetadataIdString != "")
			{
				query += " m.guid IN  ("+tempMetadataIdString+") AND ";
			}
				
			boolean needOr = false;
			boolean needExec = false;
			List<Status>  allowedStatus = policy.getObjectStatus().getStatus();
			for (int i = 0 ; i <allowedStatus.size() ; i++)
			{
				if (allowedStatus.get(i).getStatus().equalsIgnoreCase("validated") )
				{
					if(needOr)
						query += " OR ";
					query += "( ms.code = 'validated' )";
					needOr = true;
					needExec = true;
				}
				if (allowedStatus.get(i).getStatus().equalsIgnoreCase("unpublished") )
				{
					if(needOr)
						query += " OR ";
					query += " ( ms.code = 'unpublished' )";
					needOr = true;
					needExec = true;
				}
				if (allowedStatus.get(i).getStatus().equalsIgnoreCase("archived"))
				{
					if(needOr)
						query += " OR ";
					query += " (ms.code = 'archived' AND m.archived <= CURDATE() )";
					needOr = true;
					needExec = true;
				}
				if(allowedStatus.get(i).getStatus().equalsIgnoreCase("published") 
						&& allowedStatus.get(i).getVersion().equalsIgnoreCase("all")
						)
				{
					if(needOr)
						query += " OR ";
					query += "(( ms.code = 'published' AND m.published <= CURDATE()) OR (ms.code = 'archived' AND m.archived >= CURDATE() AND m.published <= CURDATE())) ";
					needOr = true;
					needExec = true;
				}
				
				//Particular case only last published
				if(allowedStatus.get(i).getStatus().equalsIgnoreCase("published") 
						&& allowedStatus.get(i).getVersion().equalsIgnoreCase("last"))
				{
					String queryObjects="";
					if (tempMetadataIdString != "")
					{
						queryObjects= "SELECT ov.object_id FROM "+ joomlaProvider.getPrefix() +"sdi_objectversion ov " +
							" INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_metadata m ON ov.metadata_id=m.id "+
							" WHERE m.guid IN ("+tempMetadataIdString+") GROUP BY ov.object_id ";
					}
					else
					{
						queryObjects= "SELECT object_id FROM "+ joomlaProvider.getPrefix() +"sdi_objectversion " +
						" GROUP BY object_id ";
					}
					object_ids = joomlaProvider.sjt.queryForList(queryObjects);
					
					//Get last  metadata id
					for(int j = 0 ; j <object_ids.size() ; j++)
					{
						String localQuery = "  SELECT m.guid  "+
		                " FROM "+ joomlaProvider.getPrefix() +"sdi_objectversion ov "+ 
		                " INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_metadata m ON ov.metadata_id=m.id "+
		                " INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_list_metadatastate ms ON m.metadatastate_id=ms.id "+
		                " INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_object o ON ov.object_id=o.id " + 
		                " WHERE o.id="+object_ids.get(j).get("object_id")+" " +
		                        " AND ((ms.code='published' AND m.published <=CURDATE() )" +
		                        " OR (ms.code='archived' AND m.archived >= CURDATE() AND m.published <= CURDATE())) "+ 
		                " ORDER BY m.published DESC " +
		                " LIMIT 0,1 ";
						
						if(final_metadata_ids == null)
							final_metadata_ids = joomlaProvider.sjt.queryForList(localQuery);
						else
							final_metadata_ids.addAll(joomlaProvider.sjt.queryForList(localQuery));
					}
					
				}
			}
			if(needExec)
			{	
				if(final_metadata_ids == null)
					final_metadata_ids = joomlaProvider.sjt.queryForList(query);
				else
					final_metadata_ids.addAll(joomlaProvider.sjt.queryForList(query));
			}
		}
		else
		{
			final_metadata_ids = metadata_ids;
		}
		
		return final_metadata_ids;
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
	    	
	    	Filter filtre = new ElementConstraintFilter();
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
			
			//No Metadata accessible
			if(ids == null)
			{
				return null;
			}
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

}

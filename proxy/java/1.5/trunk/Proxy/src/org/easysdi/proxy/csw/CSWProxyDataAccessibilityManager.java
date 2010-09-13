package org.easysdi.proxy.csw;

import java.io.File;
import java.io.InputStream;
import java.io.StringBufferInputStream;
import java.io.StringWriter;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;
import java.util.Map;

import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.ParserConfigurationException;
import javax.xml.transform.Transformer;
import javax.xml.transform.TransformerFactory;
import javax.xml.transform.dom.DOMSource;
import javax.xml.transform.stream.StreamResult;

import org.easysdi.proxy.policy.Policy;
import org.easysdi.security.JoomlaProvider;
import org.w3c.dom.*;
import org.springframework.dao.IncorrectResultSizeDataAccessException;

/**
 * Access the database to retreive accessible metadatas
 * and rewrite request to send to the CSW service
 * @author HVH
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
			String queryVersion= "SELECT m.guid as guid, v.created as title " ;
			queryVersion +=	" FROM "+ joomlaProvider.getPrefix() +"sdi_metadata m ";
			queryVersion += " INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_objectversion v ON v.metadata_id = m.id ";
			queryVersion += " WHERE v.object_id IN (SELECT object_id FROM "+ joomlaProvider.getPrefix() +"sdi_objectversion WHERE metadata_id IN (SELECT id  FROM "+ joomlaProvider.getPrefix() +"sdi_metadata WHERE guid='"+dataId+"')) ";
			queryVersion +=" AND v.created=(SELECT MAX(created) FROM  "+ joomlaProvider.getPrefix() +"sdi_objectversion WHERE object_id=(SELECT object_id FROM "+ joomlaProvider.getPrefix() +"sdi_objectversion WHERE metadata_id IN (SELECT id  FROM "+ joomlaProvider.getPrefix() +"sdi_metadata WHERE guid='"+dataId+"'))) ";
						
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
		List<String> listDataIDs = Arrays.asList();
		List<Map<String,Object>> object_ids = null;
		List<Map<String,Object>> metadata_ids = null;
		List<Map<String,Object>> metadata_guids = null;
		String query;
		
		if(!policy.getObjectVersion().getVersionModes().contains("all"))
		{
			//Not all the versions are allowed, just the last one
			
			//Get object's id 
			query= "SELECT object_id FROM "+ joomlaProvider.getPrefix() +"sdi_objectversion  GROUP BY object_id ";
			object_ids = joomlaProvider.sjt.queryForList(query);	

			for(int i = 0 ; i <object_ids.size() ; i++)
			{
				query= "SELECT metadata_id  FROM "+ joomlaProvider.getPrefix() +"sdi_objectversion " +
						"WHERE object_id="+object_ids.get(i).get("object_id")+" " +
								"AND created=(SELECT MAX(created) FROM "+ joomlaProvider.getPrefix() +"sdi_objectversion WHERE object_id="+object_ids.get(i).get("object_id")+")";
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
			query= "SELECT m.guid FROM "+ joomlaProvider.getPrefix() +"sdi_metadata m " +
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
				
				query = " SELECT m.guid FROM "+ joomlaProvider.getPrefix() +"sdi_metadata m " +
						" INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_objectversion ov ON ov.metadata_id = m.id" +
						" INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_object o ON o.id = ov.object_id  " +
						" WHERE o.visibility_id IN " +
						"(SELECT id FROM "+ joomlaProvider.getPrefix() +"sdi_list_visibility WHERE code IN ("+visibilityString+"))" +
								"AND m.guid IN ("+idString+")";
			}
			else
			{
				query = " SELECT m.guid FROM "+ joomlaProvider.getPrefix() +"sdi_metadata m " +
						" INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_objectversion ov ON ov.metadata_id = m.id" +
						" INNER JOIN "+ joomlaProvider.getPrefix() +"sdi_object o ON o.id = ov.object_id  " +
						" WHERE o.visibility_id IN " +
						"(SELECT id FROM "+ joomlaProvider.getPrefix() +"sdi_list_visibility WHERE code IN ("+visibilityString+"))" ;
				
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
				query = " SELECT m.guid FROM "+ joomlaProvider.getPrefix() +"sdi_metadata m " +
						" WHERE m.metadatastate_id IN " +
						"(SELECT id FROM "+ joomlaProvider.getPrefix() +"sdi_list_metadatastate WHERE code IN ("+statusString+"))" +
								"AND m.guid IN ("+idString+")";
			}
			else
			{
				query = " SELECT m.guid FROM "+ joomlaProvider.getPrefix() +"sdi_metadata m " +
						" WHERE m.metadatastate_id IN " +
						"(SELECT id FROM "+ joomlaProvider.getPrefix() +"sdi_list_metadatastate WHERE code IN ("+statusString+"))" ;
				
			}
			metadata_guids = joomlaProvider.sjt.queryForList(query);
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
	public StringBuffer addFilterOnDataAccessible (String ogcSearchFilter, StringBuffer param, List<Map<String,Object>> ids)
	{
		try 
		{
			//Document builder
			DocumentBuilderFactory dbf = DocumentBuilderFactory.newInstance();
			dbf.setNamespaceAware(true);
			DocumentBuilder db;
			db = dbf.newDocumentBuilder();
			InputStream xml = new StringBufferInputStream(param.toString());
			Document doc = db.parse(xml);
			doc.getDocumentElement().normalize();
			
			String docPrefix = doc.getDocumentElement().getPrefix();
			String docUri = doc.getDocumentElement().getNamespaceURI();
			if(docPrefix != null)
				docPrefix = docPrefix+":";
			else
				docPrefix ="";
			
			NodeList constraintNodes = doc.getElementsByTagNameNS("*","Constraint");
			Node constraint = null;
			Node filterNode = null;
			
			//<Constraint>
			if (constraintNodes.getLength() == 0)
			{
				//No constraint defined
				Node query = doc.getElementsByTagNameNS("*","Query").item(0);
				Element eConstraint = doc.createElementNS(docUri, docPrefix+"Constraint"); 
				eConstraint.setAttribute("version", "1.1.0");
				constraint = eConstraint;
				query.appendChild(constraint);
				filterNode = doc.createElementNS("http://www.opengis.net/ogc", "Filter");
				constraint.appendChild(filterNode);
			}
			else
			{
				//Constraint already exists
				constraint = constraintNodes.item(0);
				//Get the Filter Node
				NodeList filterNodes = doc.getElementsByTagNameNS("*", "Filter");
				if(filterNodes.getLength() == 0)
				{
					//Create filter node
					filterNode = doc.createElementNS("http://www.opengis.net/ogc", "Filter");
					constraint.appendChild(filterNode);
				}
				else
				{
					filterNode = doc.getElementsByTagNameNS("*", "Filter").item(0);
				}
			}
			
			//<Filter>
			String uri =  filterNode.getNamespaceURI();
			String prefix = filterNode.getPrefix();
			
			if(prefix == null)
				prefix = "";
			else
				prefix = prefix+":";
			
			//<And>
			//Get the "And" Node
			NodeList filterChildNodes =  filterNode.getChildNodes();
			Boolean isAndExists = false;
			Node and = null;
			for (int i = 0 ; i< filterChildNodes.getLength() ; i++)
			{
				if(("And").equalsIgnoreCase(filterChildNodes.item(i).getLocalName()))
				{
					isAndExists = true;
					and = filterChildNodes.item(i);
					break;
				}
			}			
			//Create the and node if not already exists
			if(!isAndExists)
			{
				and = buildAndNodeTofilterOnDataID(doc,uri,prefix,filterChildNodes, filterNode);
			}
			
			//<Or>
			//Add the "Or" node
			and.appendChild(buildOrNodeToFilterOnDataId(ogcSearchFilter, doc, uri,prefix, ids));
			
			//Return
			DOMSource domSource = new DOMSource(doc);
			StringWriter writer = new StringWriter();
			StreamResult result = new StreamResult(writer);
			TransformerFactory tf = TransformerFactory.newInstance();
			Transformer transformer = tf.newTransformer();
			transformer.transform(domSource, result);
			return new StringBuffer(writer.toString());
		} 
		catch (Exception e) 
		{
			e.printStackTrace();
		}
		
		return param;
	}

	/**
	 * Build the <And> node and add to it the existing filter blocks
	 * @param doc
	 * @param namespace
	 * @param prefix
	 * @param filterChildNodes
	 * @param filterNode
	 * @return
	 */
	private Node buildAndNodeTofilterOnDataID (Document doc,String namespace, String prefix,NodeList filterChildNodes,Node filterNode)
	{
		Node and = doc.createElementNS(namespace, prefix+"And");
		for (int i = filterChildNodes.getLength() -1 ; i>=0 ; i--)
		{
			Node child = filterChildNodes.item(i);
			filterNode.removeChild(child);
			and.appendChild(child);
		}
		filterNode.appendChild(and);
		return and;
	}
	
	/**
	 * Build the <Or> node and add to it the accessible metadata's Id
	 * @param ogcSearchFilter
	 * @param doc
	 * @param namespace
	 * @param prefix
	 * @param ids
	 * @return
	 */
	private Node buildOrNodeToFilterOnDataId (String ogcSearchFilter, Document doc, String namespace, String prefix,List<Map<String,Object>> ids)
	{
		Element or = doc.createElementNS(namespace,prefix+"Or");
		for (int m = 0; m<ids.size() ; m++)
		{
			Element property = doc.createElementNS(namespace,prefix+"PropertyIsEqualTo");
			Element name = doc.createElementNS(namespace,prefix+"PropertyName");
			name.setTextContent(ogcSearchFilter);
			property.appendChild(name);
			Element literal = doc.createElementNS(namespace,prefix+"Literal");
			literal.setTextContent(ids.get(m).get("guid").toString());
			property.appendChild(literal);
			or.appendChild(property);
		}
		return or;
	}
	
	public String addFilterOnDataAccessible (String ogcSearchFilter, String param,  List<Map<String,Object>> ids)
	{
		
		try 
		{
			//Document builder
			DocumentBuilderFactory dbf = DocumentBuilderFactory.newInstance();
			dbf.setNamespaceAware(true);
			DocumentBuilder db;
			db = dbf.newDocumentBuilder();
			InputStream xml = new StringBufferInputStream(param);
			Document doc = db.parse(xml);
			doc.getDocumentElement().normalize();
			
			
			String docPrefix = doc.getDocumentElement().getPrefix();
			String docUri = doc.getDocumentElement().getNamespaceURI();
			if(docPrefix != null)
				docPrefix = docPrefix+":";
			else
				docPrefix ="";
			
			Node filterNode = null;
				
			NodeList filterNodes = doc.getElementsByTagNameNS("*", "Filter");
			if(filterNodes.getLength() == 0)
			{
				//Create filter node
				filterNode = doc.createElementNS("http://www.opengis.net/ogc", "Filter");
			}
			else
			{
				filterNode = doc.getElementsByTagNameNS("*", "Filter").item(0);
			}
			
			
			//<Filter>
			String uri =  filterNode.getNamespaceURI();
			String prefix = filterNode.getPrefix();
			
			if(prefix == null)
				prefix = "";
			else
				prefix = prefix+":";
			
			//<And>
			//Get the "And" Node
			NodeList filterChildNodes =  filterNode.getChildNodes();
			Boolean isAndExists = false;
			Node and = null;
			for (int i = 0 ; i< filterChildNodes.getLength() ; i++)
			{
				if(("And").equalsIgnoreCase(filterChildNodes.item(i).getLocalName()))
				{
					isAndExists = true;
					and = filterChildNodes.item(i);
					break;
				}
			}			
			//Create the and node if not already exists
			if(!isAndExists)
			{
				and = buildAndNodeTofilterOnDataID(doc,uri,prefix,filterChildNodes, filterNode);
			}
			
			//<Or>
			//Add the "Or" node
			and.appendChild(buildOrNodeToFilterOnDataId(ogcSearchFilter, doc, uri,prefix, ids));
			
			//Return
			DOMSource domSource = new DOMSource(doc);
			StringWriter writer = new StringWriter();
			StreamResult result = new StreamResult(writer);
			TransformerFactory tf = TransformerFactory.newInstance();
			Transformer transformer = tf.newTransformer();
			transformer.transform(domSource, result);
			String constraint = writer.toString().substring(38);
			return constraint;
		} 
		catch (Exception e) 
		{
			e.printStackTrace();
		}
		
		return param;
	}
}

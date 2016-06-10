package org.easysdi.proxy.wfs;

public class WFSProxyGeomAttributes extends Object
	{
	private int requestServerIndex;
	private String policyServerPrefix;
	private String policyServerNamespace;
	private String featureTypeName;
	private String geomAttributName;

	
	WFSProxyGeomAttributes(int p_serverIndex,String p_requestServerPrefixNS,String p_featureTypeName,String p_geomAttributName)
		{
		requestServerIndex = p_serverIndex;
		featureTypeName = p_featureTypeName;
		geomAttributName = p_geomAttributName;
		}
	
	WFSProxyGeomAttributes(int p_serverIndex, String p_policyServerPrefix, String p_policyServerNamespace)
		{
		requestServerIndex = p_serverIndex;
		policyServerPrefix = p_policyServerPrefix;
		policyServerNamespace = p_policyServerNamespace;
		}
	
	WFSProxyGeomAttributes()
		{
		}
	
	
	public int getRequestServerIndex()
		{
		return requestServerIndex;
		}
	
	public String getpolicyServerPrefix()
		{
		return policyServerPrefix;
		}
	
	public String getpolicyServerNamespace()
		{
		return policyServerNamespace;
		}
		
	public String getFeatureTypeName()
		{
		return featureTypeName;
		}
	
	public String getGeomAttributName()
		{
		return geomAttributName;
		}
	
	
	public void setRequestServerIndex(int p_serverIndex)
		{
		requestServerIndex = p_serverIndex;
		}
	
	public void setpolicyServerPrefix(String p_policyServerPrefix)
		{
		policyServerPrefix = p_policyServerPrefix;
		}

	public void setpolicyServerNamespace(String p_policyServerNamespace)
		{
		policyServerNamespace = p_policyServerNamespace;
		}
	
	public void setFeatureTypeName(String p_featureTypeName)
		{
		featureTypeName = p_featureTypeName;
		}
	
	public void setGeomAttributName(String p_geomAttributName)
		{
		geomAttributName = p_geomAttributName;
		}
	}

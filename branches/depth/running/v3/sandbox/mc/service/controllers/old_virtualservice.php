<?php
/**
 * @version     3.0.0
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Service controller class.
 */
class Easysdi_serviceControllerVirtualService extends JController
{

    function __construct() {
    	//Need to be add here even if it is in administrator/controller.php 
    	require_once JPATH_COMPONENT.DS.'helpers'.DS.'easysdi_service.php';
        parent::__construct();
    }
    
    function cancel() {
    	$this->setRedirect('index.php?option=com_easysdi_service&view=virtualservices' );
    }
    
    function add() {
    	$serviceconnector = JRequest::getVar('serviceconnector',null);
    	if(isset($serviceconnector))
    		$this->setRedirect('index.php?option=com_easysdi_service&view=virtualservice&task=add&layout='.$serviceconnector);
    	else
    		$this->setRedirect('index.php?option=com_easysdi_service&view=virtualservice&task=add&layout=add');
    }
    
    function edit() {
    	$params 			= JComponentHelper::getParams('com_easysdi_service');
    	$xml 				= simplexml_load_file($params->get('proxyconfigurationfile'));
    	$cid 				= JRequest::getVar('cid',array(0));
    	$layout 			= JRequest::getVar('serviceconnector',null);
    	if(!isset($layout)){
	    	foreach ($cid as $id ){
	    		foreach ($xml->config as $config) {
	    			if (strcmp($config['id'],$id)==0){
	    				if($config->{'servlet-class'} == "org.easysdi.proxy.wms.WMSProxyServlet")
	    				{
	    					$layout = "wms";
	    				}
	    				else if($config->{'servlet-class'} == "org.easysdi.proxy.wmts.WMTSProxyServlet")
	    				{
	    					$layout = "wmts";
	    				}
	    				else if($config->{'servlet-class'} == "org.easysdi.proxy.csw.CSWProxyServlet")
	    				{
	    					$layout = "csw";
	    				}
	    				else if($config->{'servlet-class'} == "org.easysdi.proxy.wfs.WFSProxyServlet")
	    				{
	    					$layout = "wfs";
	    				}
	    			}
	    		}
	    	}
	    }

    	$this->setRedirect('index.php?option=com_easysdi_service&view=virtualservice&task=edit&id='.$cid[0].'&layout='.$layout );
    }
    
    function delete() {
    	$params		= JComponentHelper::getParams('com_easysdi_service');
    	$xml 		= simplexml_load_file($params->get('proxyconfigurationfile'));
    	$cid 		= JRequest::getVar('cid',array(0));
    	foreach ($cid as $id ){
	    	foreach ($xml->config as $config) {
	    		if (strcmp($config['id'],$id)==0){
	    				
	    			$child = dom_import_simplexml($config);
	    			$parent = $child->parentNode;
	    			$parent->removeChild($child);
	    	
	    			$xml->asXML($params->get('proxyconfigurationfile'));
	    	
	    			$this->deleteAllPolicy($xml,$id);
	    			break;
	    		}
	    	}
    	}
    	$this->setRedirect('index.php?option=com_easysdi_service&view=virtualservices');
    }
    
    /**
     * Delete all policy of a specific configuration
     * @param object $xml
     * @param String $configId
     */
    function deleteAllPolicy($xml,$configId){
    
    	foreach ($xml->config as $config) {
    		if (strcmp($config['id'],$configId)==0){
    
    			$policyFile = $config->{'authorization'}->{'policy-file'};
    			$servletClass =  $config->{'servlet-class'};
    
    			if (file_exists($policyFile)) {
    				$xmlConfigFile = simplexml_load_file($config->{'authorization'}->{'policy-file'});
    
    				foreach ($xmlConfigFile->Policy as $policy){
    						
    					if (strcmp($policy['ConfigId'],$configId)==0){
    
    						$child = dom_import_simplexml($policy);
    						$parent = $child->parentNode;
    						$parent->removeChild($child);
    
    						$xmlConfigFile->asXML($policyFile);
    						break;
    					}
    				}
    
    			}
    		}
    	}
    
    }
    
    
    function save() {
    	$params 		= JComponentHelper::getParams('com_easysdi_service');
    	$xml 			= simplexml_load_file($params->get('proxyconfigurationfile'));
    	$configId 		= JRequest::getVar("id","New Config");
    	$previoustask 	= JRequest::getVar("previoustask", 'edit');
    	$new			= ($previoustask == 'add')? true : false;
    	
    	if ($new){
    		$found = false;
    		$i=0;
    		foreach ($xml->config as $config) {
    			if (strcmp($config['id'],$configId)==0){
    				$found = true;
    				break;
    			}
    		}
    	
    		while($found){
    			foreach ($xml->config as $config) {
    				$found=false;
    				if (strcmp($config['id'],$configId.$i)==0){
    					$found = true;
    					break;
    				}
    			}
    			if ($found == false){
    				$configId = $configId.$i;
    			}
    			$i++;
    		}
    	
    		$config = $xml->addChild("config");
    		$config->addAttribute("id",$configId);
    	
    		$config->addChild("authorization")->addChild("policy-file");	
    	}
    	
    	foreach ($xml->config as $config) {
    		if (strcmp($config['id'],$configId)==0){
    			//Id
    			$config['id']=$configId;
    	
    			//Servlet class
    			$servletName = JRequest::getVar("serviceconnector");
    			if($servletName == "WMS")
    				$servletClass = "org.easysdi.proxy.wms.WMSProxyServlet";
    			else if($servletName == "WMTS")
    				$servletClass = "org.easysdi.proxy.wmts.WMTSProxyServlet";
    			else if($servletName == "CSW")
    				$servletClass = "org.easysdi.proxy.csw.CSWProxyServlet";
    			else if($servletName== "WFS")
    				$servletClass = "org.easysdi.proxy.wfs.WFSProxyServlet";
    			$config->{'servlet-class'}=$servletClass;
    			
    			//Supported version
    			$supportedVersionByconfig 		= json_decode(JRequest::getVar("supportedVersionsByConfig"));
    			$config->{'supported-versions'}	= "";
    			foreach ($supportedVersionByconfig as $version){
    				$config->{'supported-versions'}->addChild("version",$version);
    			}
    	
    			//XSLT repository
    			$config->{"xslt-path"}->{"url"} = JRequest::getVar("xsltPath");
    	
    			//Log file
    			$config->{'log-config'}->{'logger'}							= "org.apache.log4j.Logger";
    			$config->{'log-config'}->{'log-level'}						= JRequest::getVar("logLevel");
    			$logPath													= JRequest::getVar("logPath");
    			$logSuffix													= JRequest::getVar("logSuffix");
    			$logPrefix													= JRequest::getVar("logPrefix");
    			$logExt														= JRequest::getVar("logExt");
    			$logPeriod													= JRequest::getVar("logPeriod");
    			$config->{'log-config'}->{'file-structure'}->{'path'} 		= $logPath;
    			$config->{'log-config'}->{'file-structure'}->{'suffix'} 	= $logSuffix;
    			$config->{'log-config'}->{'file-structure'}->{'prefix'} 	= $logPrefix;
    			$config->{'log-config'}->{'file-structure'}->{'extension'} 	= $logExt;
    			$config->{'log-config'}->{'file-structure'}->{'period'} 	= $logPeriod;
    			$config->{'log-config'}->{'date-format'} 					= JRequest::getVar("dateFormat","dd/MM/yyyy HH:mm:ss");
    	
    			//Host translator
    			$hostTranslator 				= JRequest::getVar("hostTranslator");
    			$config->{'host-translator'}	= $hostTranslator;
    	
    			//Remote server
    			$config->{'remote-server-list'} = "";
    			$i=0;
    			for($i = 0 ; $i < JRequest::getVar("nbServer",0); $i++)
    			{
	    			$service  		= JRequest::getVar("service_".$i,"");
	    			$db 			= JFactory::getDbo();
	    			$db->setQuery('SELECT resourceurl, resourceusername, resourcepassword, serviceurl, serviceusername, servicepassword FROM #__sdi_physicalservice WHERE alias="'.$service.'"');
	    			$sdi_service 	= $db->loadObject();
	    			
	    			$remoteServer 	= $config->{'remote-server-list'}->addChild("remote-server");
	    			
	    			if($i == 0)
	    				$remoteServer['master'] = "true";
	    			else
	    				$remoteServer['master'] = "false";
	    			
	    			$remoteServer->alias		= $service;
	    			$remoteServer->user			= $sdi_service->resourceusername;
	    			$remoteServer->url			= $sdi_service->resourceurl;
	    			$remoteServer->password		= $sdi_service->resourcepassword;
	    			if (strcmp($servletClass,"org.easysdi.proxy.csw.CSWProxyServlet")==0 )
	    			{
		    			$remoteServer->{'max-records'}		= JRequest::getVar("max-records_".$i,"-1");
		    			$remoteServer->{'login-service'}	= $sdi_service->serviceurl.'?username='.$sdi_service->serviceusername.'&password='.$sdi_service->servicepassword;
		    			$geonetworktransaction  			= $remoteServer->addChild("transaction");
		    			$geonetworktransaction->{'type'}	= 'geonetwork';
	    			}
    			}
    				
    			//Harvesting and Ogc search filter
    			if (strcmp($servletClass,"org.easysdi.proxy.csw.CSWProxyServlet")==0 )
    			{
	    			$harvesting 		= JRequest::getVar("harvestingConfig",0);
	    			if($harvesting == 1)
	    				$config->{"harvesting-config"}="true";
	    			else
	    				$config->{"harvesting-config"}="false";
	    			$ogcSearchFilter 	= JRequest::getVar("ogcSearchFilter","");
	    			$config->{"ogc-search-filter"}=$ogcSearchFilter;
    			}
    	
    			//Exception
    			$exceptionMode 						= JRequest::getVar("exception_mode","permissive");
    			$config->{"exception"}->{"mode"}	= $exceptionMode;
    	
    			//Policy
    			$policyFile 								= JRequest::getVar("policyFile");
    			$config->{"authorization"}->{"policy-file"}	= $policyFile;
    	
    			//Service metadata
    			if (strcmp($servletClass,"org.easysdi.proxy.wmts.WMTSProxyServlet")==0 )
    				$config = $this->serviceMetadataOWS($config);
    			else
    				$config = $this->serviceMetadataWFS($config);
    			$xml->asXML($params->get('proxyconfigurationfile'));
    		}
    		
    	}
    	$this->setRedirect('index.php?option=com_easysdi_service&view=virtualservices');
    }
    
    /**
     * Service metadata OWS 1.1.0 spécification
     * @param object $config
     */
    function serviceMetadataOWS ($config)
    {
    	//ServiceIdentification
    	$config->{"service-metadata"}->{"ServiceIdentification"}->{"Title"}=JRequest::getVar("service_title");
    	$config->{"service-metadata"}->{"ServiceIdentification"}->{"Abstract"}=JRequest::getVar("service_abstract");
    	$config->{"service-metadata"}->{"ServiceIdentification"}->{"KeywordList"}="";
    	$keywordsList = JRequest::getVar("service_keyword" );
    	$pos = strpos($keywordsList, ",");
    	if($pos)
    	{
    		$keywords = $keywordsList;
    		while ($pos)
    		{
    			$config->{"service-metadata"}->{"ServiceIdentification"}->{"KeywordList"}->addChild("Keyword",  trim(substr ($keywords, 0,$pos ))) ;
    			$keywords = substr ($keywords, $pos +1 );
    			$pos = strpos($keywords,",");
    		}
    		$config->{"service-metadata"}->{"ServiceIdentification"}->{"KeywordList"}->addChild("Keyword",  $keywords) ;
    	}
    	else
    	{
    		$config->{"service-metadata"}->{"ServiceIdentification"}->{"KeywordList"}->{"Keyword"}=$keywordsList;
    	}
    	if(JRequest::getVar("service_fees" ))
    		$config->{"service-metadata"}->{"ServiceIdentification"}->{"Fees"}=JRequest::getVar("service_fees" );
    	else
    		$config->{"service-metadata"}->{"ServiceIdentification"}->{"Fees"}="none";
    	if(JRequest::getVar("service_accessconstraints"))
    		$config->{"service-metadata"}->{"ServiceIdentification"}->{"AccessConstraints"}=JRequest::getVar("service_accessconstraints");
    	else
    		$config->{"service-metadata"}->{"ServiceIdentification"}->{"AccessConstraints"}="none";
    		
    	//Service Provider
    	$config->{"service-metadata"}->{"ServiceProvider"}->{"ProviderName"}=JRequest::getVar("service_providername");
    	$config->{"service-metadata"}->{"ServiceProvider"}->{"ProviderSite"}=JRequest::getVar("service_providersite");
    	$config->{"service-metadata"}->{"ServiceProvider"}->{"ResponsibleParty"}->{'IndividualName'}=JRequest::getVar("service_responsiblename");
    	$config->{"service-metadata"}->{"ServiceProvider"}->{"ResponsibleParty"}->{'PositionName'}=JRequest::getVar("service_responsibleposition");
    	$config->{"service-metadata"}->{"ServiceProvider"}->{"ResponsibleParty"}->{'Role'}=JRequest::getVar("service_responsiblerole");
    	$config->{"service-metadata"}->{"ServiceProvider"}->{"ResponsibleParty"}->{'Contact'}->{'HoursOfService'}=JRequest::getVar("service_responsiblecontacthours");
    	$config->{"service-metadata"}->{"ServiceProvider"}->{"ResponsibleParty"}->{'Contact'}->{'Instructions'}=JRequest::getVar("service_responsiblecontactinstructions");
    	$config->{"service-metadata"}->{"ServiceProvider"}->{"ResponsibleParty"}->{'Contact'}->{'OnlineResource'}=JRequest::getVar("service_responsiblecontactonline");
    	$config->{"service-metadata"}->{"ServiceProvider"}->{"ResponsibleParty"}->{'Contact'}->{'Telephone'}->{'VoicePhone'}=JRequest::getVar("service_responsiblecontactphone");
    	$config->{"service-metadata"}->{"ServiceProvider"}->{"ResponsibleParty"}->{'Contact'}->{'Telephone'}->{'Facsimile'}=JRequest::getVar("service_responsiblecontactfax");
    	$config->{"service-metadata"}->{"ServiceProvider"}->{"ResponsibleParty"}->{'Contact'}->{'Address'}->{'AddressType'}=JRequest::getVar("service_responsiblecontactadresstype");
    	$config->{"service-metadata"}->{"ServiceProvider"}->{"ResponsibleParty"}->{'Contact'}->{'Address'}->{'DelivryPoint'}=JRequest::getVar("service_responsiblecontactadress");
    	$config->{"service-metadata"}->{"ServiceProvider"}->{"ResponsibleParty"}->{'Contact'}->{'Address'}->{'PostalCode'}=JRequest::getVar("service_responsiblecontactpostcode");
    	$config->{"service-metadata"}->{"ServiceProvider"}->{"ResponsibleParty"}->{'Contact'}->{'Address'}->{'City'}=JRequest::getVar("service_responsiblecontactcity");
    	$config->{"service-metadata"}->{"ServiceProvider"}->{"ResponsibleParty"}->{'Contact'}->{'Address'}->{'Area'}=JRequest::getVar("service_responsiblecontactarea");
    	$config->{"service-metadata"}->{"ServiceProvider"}->{"ResponsibleParty"}->{'Contact'}->{'Address'}->{'Country'}=JRequest::getVar("service_responsiblecontactcountry");
    	$config->{"service-metadata"}->{"ServiceProvider"}->{"ResponsibleParty"}->{'Contact'}->{'Address'}->{'ElectronicMailAddress'}=JRequest::getVar("service_responsiblecontactmail");
    
    	return $config;
    }
    
    /**
     * Service metadata WFS 1.0.0
     * @param object $config
     * @return object
     */
    function serviceMetadataWFS ($config)
    {
    	$config->{"service-metadata"}->{"Title"}=JRequest::getVar("service_title");
    	$config->{"service-metadata"}->{"Abstract"}=JRequest::getVar("service_abstract");
    	$config->{"service-metadata"}->{"KeywordList"}="";
    	$keywordsList = JRequest::getVar("service_keyword" );
    	$pos = strpos($keywordsList, ",");
    	if($pos)
    	{
    		$keywords = $keywordsList;
    		while ($pos)
    		{
    			$config->{"service-metadata"}->{'KeywordList'}->addChild("Keyword",  trim(substr ($keywords, 0,$pos ))) ;
    			$keywords = substr ($keywords, $pos +1 );
    			$pos = strpos($keywords,",");
    		}
    		$config->{"service-metadata"}->{'KeywordList'}->addChild("Keyword",  $keywords) ;
    	}
    	else
    	{
    		$config->{"service-metadata"}->{"KeywordList"}->{"Keyword"}=$keywordsList;
    	}
    	$config->{"service-metadata"}->{"ContactInformation"}->{"ContactOrganization"}=JRequest::getVar("service_contactorganization");
    	$config->{"service-metadata"}->{"ContactInformation"}->{"ContactName"}=JRequest::getVar("service_contactperson");
    	$config->{"service-metadata"}->{"ContactInformation"}->{"ContactPosition"}=JRequest::getVar("service_contactposition" );
    	$config->{"service-metadata"}->{"ContactInformation"}->{"ContactAddress"}->{"AddressType"}=JRequest::getVar("service_contacttype" );
    	$config->{"service-metadata"}->{"ContactInformation"}->{"ContactAddress"}->{"Address"}=JRequest::getVar("service_contactadress" );
    	$config->{"service-metadata"}->{"ContactInformation"}->{"ContactAddress"}->{"PostalCode"}=JRequest::getVar("service_contactpostcode");
    	$config->{"service-metadata"}->{"ContactInformation"}->{"ContactAddress"}->{"City"}=JRequest::getVar("service_contactcity" );
    	$config->{"service-metadata"}->{"ContactInformation"}->{"ContactAddress"}->{"State"}=JRequest::getVar("service_contactstate" );
    	$config->{"service-metadata"}->{"ContactInformation"}->{"ContactAddress"}->{"Country"}=JRequest::getVar("service_contactcountry" );
    	$config->{"service-metadata"}->{"ContactInformation"}->{"VoicePhone"}=JRequest::getVar("service_contacttel" );
    	$config->{"service-metadata"}->{"ContactInformation"}->{"Facsimile"}=JRequest::getVar("service_contactfax" );
    	$config->{"service-metadata"}->{"ContactInformation"}->{"ElectronicMailAddress"}=JRequest::getVar("service_contactmail");
    	$config->{"service-metadata"}->{"ContactInformation"}->{"Linkage"}=JRequest::getVar("service_contactlinkage");
    	$config->{"service-metadata"}->{"ContactInformation"}->{"HoursofSservice"}=JRequest::getVar("service_contacthours" );
    	$config->{"service-metadata"}->{"ContactInformation"}->{"Instructions"}=JRequest::getVar("service_contactinstructions");
    	if(JRequest::getVar("service_fees" ))
    		$config->{"service-metadata"}->{"Fees"}=JRequest::getVar("service_fees" );
    	else
    		$config->{"service-metadata"}->{"Fees"}="none";
    	if(JRequest::getVar("service_accessconstraints"))
    		$config->{"service-metadata"}->{"AccessConstraints"}=JRequest::getVar("service_accessconstraints");
    	else
    		$config->{"service-metadata"}->{"AccessConstraints"}="none";
    	return $config;
    }
}
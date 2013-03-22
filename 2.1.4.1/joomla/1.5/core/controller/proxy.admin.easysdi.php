<?php
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

defined('_JEXEC') or die('Restricted access');

class ADMIN_proxy
{
	function editConfig($xml,$new = false)
	{
		$option = JRequest::getVar('option');
		$servletClass = JRequest::getVar('servletClass',"");
		
		$task = "editConfig";
		if($new)
			$task = "addConfig";
		
			
		if (!$new){
			$configId = JRequest::getVar("configId");
			if($servletClass == "")
			{
				foreach ($xml->config as $config) {
					if (strcmp($config['id'],$configId)==0){
						$servletClass = $config->{'servlet-class'};
						break;
					}
				}
			}
		}else{
			$found = false;
			$configId = "New Config";
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
			$config->{'remote-server-list'}="";	
			$remoteServer = $config->{'remote-server-list'}->addChild("remote-server");
			$remoteServer->user="";
			$remoteServer->url="";
			$remoteServer->password="";
		}
		
		$availableServlet = array("org.easysdi.proxy.wfs.WFSProxyServlet" => "org.easysdi.proxy.wfs.WFSProxyServlet", 
								  "org.easysdi.proxy.wms.WMSProxyServlet" => "org.easysdi.proxy.wms.WMSProxyServlet", 
								  "org.easysdi.proxy.wmts.v100.WMTS100ProxyServlet" => "org.easysdi.proxy.wmts.v100.WMTS100ProxyServlet", 
								  "org.easysdi.proxy.csw.CSWProxyServlet" => "org.easysdi.proxy.csw.CSWProxyServlet");
		$availableServletList = array();
		 foreach($availableServlet as $key=>$value) :
		 	$availableServletList[] = JHTML::_('select.option', $key, $value);
		 endforeach;
		 
		if($servletClass == "org.easysdi.proxy.wms.WMSProxyServlet" )
		{
			HTML_proxy::editConfigWMS($xml, $new, $configId, $availableServletList, $option,$task);
		}
		else if($servletClass == "org.easysdi.proxy.wmts.v100.WMTSProxyServlet" )
		{
			HTML_proxy::editConfigWMTS($xml, $new, $configId, $availableServletList, $option,$task);
		}
		else if($servletClass == "org.easysdi.proxy.csw.CSWProxyServlet" )
		{
			HTML_proxy::editConfigCSW($xml, $new, $configId, $availableServletList, $option,$task);
		}
		else if($servletClass == "org.easysdi.proxy.wmts.v100.WMTS100ProxyServlet" )
		{
			HTML_proxy::editConfigWMTS100($xml, $new, $configId, $availableServletList, $option,$task);
		}	
		else 
		{
			HTML_proxy::editConfigWFS($xml, $new, $configId, $availableServletList, $option,$task);	
		} 
	}
	
	function editPolicy ($xml, $new=false)
	{
		$database =& JFactory::getDBO(); 
		$language =& JFactory::getLanguage();
		$configId = JRequest::getVar("configId");
		$isCSW = false;
		
		foreach ($xml->config as $config) 
		{
			if (strcmp($config['id'],$configId)==0)
			{
				$servletClass =  $config->{'servlet-class'};
				if (strcmp($servletClass,"org.easysdi.proxy.csw.CSWProxyServlet")==0 )
				{
					$isCSW = true;
				}	
			}
		}
		//Get  profiles
		$database->setQuery( "SELECT ap.code as value, t.label as text FROM #__sdi_language l, #__sdi_list_codelang cl, #__sdi_accountprofile ap LEFT OUTER JOIN #__sdi_translation t ON ap.guid=t.element_guid WHERE t.language_id=l.id AND l.codelang_id=cl.id AND cl.code='".$language->_lang."'" );
		$rowsProfile = $database->loadObjectList();
		echo $database->getErrorMsg();
		
		//Get users
		$database->setQuery( "SELECT #__users.username as value, #__users.name as text FROM #__users INNER JOIN #__sdi_account ON  #__users.id = #__sdi_account.user_id ORDER BY text" );
		$rowsUser = $database->loadObjectList();
		echo $database->getErrorMsg();
		
		if($isCSW)
		{
			//Get visibility
			$database->setQuery( "SELECT v.code as value, 
										 v.label as text 
								  FROM #__sdi_list_visibility v 
								  ");
			$rowsVisibility = $database->loadObjectList();
			//echo $database->getErrorMsg();
			
			//Get metadata status
			$database->setQuery( "SELECT s.code as value, 
										 s.label as text 
								  FROM #__sdi_list_metadatastate s order by ordering
								  ");
			$rowsStatus = $database->loadObjectList();
			//echo $database->getErrorMsg();
			
			//Get object type
			$database->setQuery( "SELECT ot.code as value, 
										 ot.name as text 
								  FROM #__sdi_objecttype ot order by ot.name
								  ");
			$rowsObjectTypes = $database->loadObjectList();
		}
		
		HTML_proxy::editPolicy($xml, $new, $rowsProfile, $rowsUser, $rowsVisibility, $rowsStatus, $rowsObjectTypes);
	}
	
	
function addPolicy($xml){
	$configId = JRequest::getVar("configId");

	foreach ($xml->config as $config) {

		if (strcmp($config['id'],$configId)==0){

			$policyFile = $config->{'authorization'}->{'policy-file'};
			if (file_exists($policyFile)) {

				$xmlConfigFile = simplexml_load_file($config->{'authorization'}->{'policy-file'});

				$theNewPolicy = $xmlConfigFile->addChild('Policy');
				$theNewPolicy[Id]="new Policy";
				$theNewPolicy[ConfigId]=$configId;
				$theNewPolicy->Servers['All']="false";
				$theNewPolicy->Subjects['All']="false";
				$theNewPolicy->Operations['All']="true";
				$theNewPolicy->AvailabilityPeriod->Mask="dd-MM-yyyy";
				$theNewPolicy->AvailabilityPeriod->From->Date="28-01-2008";
				$theNewPolicy->AvailabilityPeriod->To->Date="28-01-2108";

				$xmlConfigFile->asXML($config->{'authorization'}->{'policy-file'});
			}

			break;
		}
	}

	return $xml;
}

function addConfig($xml){
	$found = false;
	$configId = "New Config";
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
	$config->addChild("remote-server-list")->addChild{'remote-server'};
	$config->addChild("authorization")->addChild("policy-file");

	return $xml;
}

function orderupPolicy($xml){
	$configId = JRequest::getVar("configId");
	$policyId = JRequest::getVar("policyId");


	foreach ($xml->config as $config) {
		if (strcmp($config[id],$configId)==0){

			$policyFile = $config->{'authorization'}->{'policy-file'};
			$servletClass =  $config->{'servlet-class'};

			if (file_exists($policyFile)) {
				$xmlConfigFile = simplexml_load_file($config->{'authorization'}->{'policy-file'});

				foreach ($xmlConfigFile->Policy as $policy){

						
					if (strcmp($policy['ConfigId'],$configId)==0){


						if (strcmp($policy['Id'],$policyId)==0){
								
							$goodChild = dom_import_simplexml($policy);
							$clonedGoodChild = $goodChild->cloneNode(true);
							$lastChild = dom_import_simplexml($lastPolicy);
							$clonedLastChild = $lastChild->cloneNode(true);
							$parent = $lastChild->parentNode;
							$result = $parent->replaceChild( $clonedGoodChild,$lastChild);
							$parent->replaceChild( $clonedLastChild,$goodChild );
							$xmlConfigFile->asXML($policyFile);
								
							break;
						}else{
							$lastPolicy = $policy;
						}
					}
				}
			}
		}
	}
}

function orderdownPolicy($xml){
	$configId = JRequest::getVar("configId");
	$policyId = JRequest::getVar("policyId");


	foreach ($xml->config as $config) {
		if (strcmp($config[id],$configId)==0){

			$policyFile = $config->{'authorization'}->{'policy-file'};
			$servletClass =  $config->{'servlet-class'};

			if (file_exists($policyFile)) {
				$xmlConfigFile = simplexml_load_file($config->{'authorization'}->{'policy-file'});
				$found = false;
				foreach ($xmlConfigFile->Policy as $policy){

						
					if (strcmp($policy['ConfigId'],$configId)==0){


						if (strcmp($policy['Id'],$policyId)==0){
							$found=true;
							$lastPolicy = $policy;
								
						}else{
							if($found){

								$goodChild = dom_import_simplexml($policy);
								$clonedGoodChild = $goodChild->cloneNode(true);
								$lastChild = dom_import_simplexml($lastPolicy);
								$clonedLastChild = $lastChild->cloneNode(true);
								$parent = $lastChild->parentNode;
								$result = $parent->replaceChild( $clonedGoodChild,$lastChild);
								$parent->replaceChild( $clonedLastChild,$goodChild );
								$xmlConfigFile->asXML($policyFile);
								break;
							}
								
								
						}
					}
				}
			}
		}
	}
}

function copyPolicy($xml){
	$configId = JRequest::getVar("configId");
	$policyId = JRequest::getVar("policyId");

	foreach ($xml->config as $config) {
		if (strcmp($config[id],$configId)==0){

			$policyFile = $config->{'authorization'}->{'policy-file'};
			$servletClass =  $config->{'servlet-class'};

			if (file_exists($policyFile)) {
				$xmlConfigFile = simplexml_load_file($config->{'authorization'}->{'policy-file'});

				foreach ($xmlConfigFile->Policy as $policy){
					if (strcmp($policy['Id'],$policyId)==0 && strcmp($policy['ConfigId'],$configId) == 0){


						$child = dom_import_simplexml($policy);
						$newPolicy = $child->cloneNode(true);
						$i=0;
						$found=true;
						while($found){
							$found=false;
							foreach ($xmlConfigFile->Policy as $policy2){
									
								if(strcmp($policy2['Id'],$i.'_'.$policyId)==0 && strcmp($policy2['ConfigId'],$configId) == 0){
									$found = true;
									$i++;
								}
							}
						}
						$newPolicy->setAttribute('Id',$i.'_'.$child->getAttribute('Id'));
							
						$parent = $child->parentNode;
						$parent->appendChild($newPolicy);
						$xmlConfigFile->asXML($policyFile);
						break;


					}
						
				}
			}
		}
	}

}

function deletePolicy($xml,$configId,$policyId){
	
	foreach ($xml->config as $config) {
		if (strcmp($config[id],$configId)==0){

			$policyFile = $config->{'authorization'}->{'policy-file'};
			$servletClass =  $config->{'servlet-class'};

			if (file_exists($policyFile)) {
				$xmlConfigFile = simplexml_load_file($config->{'authorization'}->{'policy-file'});

				foreach ($xmlConfigFile->Policy as $policy){
						
					if (strcmp($policy['Id'],$policyId)==0 && strcmp($policy['ConfigId'],$configId)==0){

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

function deleteConfig($xml,$configFilePath,$configId){
	
	foreach ($xml->config as $config) {
		if (strcmp($config['id'],$configId)==0){

			ADMIN_proxy::deleteAllPolicy($xml,$configId);
			 
			$child = dom_import_simplexml($config);
			$parent = $child->parentNode;
			$parent->removeChild($child);

			$xml->asXML($configFilePath);
			
			
			break;
		}
	}
}

function saveComponentConfig($xmlConfig,$componentConfigFilePath){
	$filePath = JRequest::getVar("filePath");



	$xmlConfig->proxy->configFilePath = $filePath;
	$xmlConfig->asXML($componentConfigFilePath);

	global $xml;
	$xml = simplexml_load_file($xmlConfig->proxy->configFilePath);
	if ($xml === false){
		global $mainframe;
		$mainframe->enqueueMessage(JText::_(  'EASYSDI_PLEASE VERIFY THE CONFIGURATION FILE PATH' ),'error');
	}

}

function savePolicy($xml){
	global $mainframe;
	
	$params = JRequest::get();
	$servletClass = JRequest::getVar("servletClass");
	$allUsers = JRequest::getVar("AllUsers","");
	$newPolicyId = JRequest::getVar("newPolicyId","");
	$isNewPolicy =  JRequest::getBool("isNewPolicy",false);
	$dateFrom = JRequest::getVar("dateFrom","");
	$dateTo = JRequest::getVar("dateTo","");
	$configId = JRequest::getVar("configId","");
	$policyId = JRequest::getVar("policyId","");
	$maxWidth= JRequest::getVar("maxWidth","");
	$minWidth= JRequest::getVar("minWidth","");
	$maxHeight= JRequest::getVar("maxHeight","");
	$minHeight= JRequest::getVar("minHeight","");
	
	foreach ($xml->config as $config) 
	{
		if (strcmp($config['id'],$configId)==0)
		{
			$policyFile = $config->{'authorization'}->{'policy-file'};
			$servletClass =  $config->{'servlet-class'};

			if (file_exists($policyFile)) 
			{
				$xmlConfigFile = simplexml_load_file($config->{'authorization'}->{'policy-file'});

				if ($isNewPolicy)
				{
					$found=false;
					foreach ($xmlConfigFile->Policy as $policy)
					{
						if (strcmp($policy['Id'],$newPolicyId)==0 && strcmp($policy['ConfigId'],$configId) == 0)
						{
							$found=true;
							break;
						}
					}
					$i=0;
					while($found)
					{
						$found=false;
						foreach ($xmlConfigFile->Policy as $policy)
						{
							if (strcmp($policy['Id'],$newPolicyId)==0 && strcmp($policy['ConfigId'],$configId) == 0)
							{
								$found=true;
								break;
							}
						}
						if ($found == true)
						{
							$newPolicyId = $newPolicyId.$i;
						}
						$i++;
					}

					$thePolicy = $xmlConfigFile->addChild('Policy');
					$thePolicy['Id']=$newPolicyId;
					$thePolicy[ConfigId]=$configId;
					$policyId=$newPolicyId;
					$thePolicy->Servers['All']="false";
					$thePolicy->Subjects['All']="false";
					$thePolicy->Operations['All']="true";
					$thePolicy->AvailabilityPeriod->Mask="dd-MM-yyyy";
					$thePolicy->AvailabilityPeriod->From->Date="28-01-2008";
					$thePolicy->AvailabilityPeriod->To->Date="28-01-2108";
				}
				else
				{
					foreach ($xmlConfigFile->Policy as $policy)
					{
						if (strcmp($policy['Id'],$policyId)==0 && strcmp($policy['ConfigId'],$configId) == 0)
						{
							$configId = $policy['ConfigId'];
							$thePolicy = $policy;
							$thePolicy['Id']=$newPolicyId;
						}
					}
				}
			}
		}
	}

	//AvailabilityPeriod
	$thePolicy->AvailabilityPeriod->From->Date =$dateFrom;
	$thePolicy->AvailabilityPeriod->To->Date =$dateTo;
	
	if (strcasecmp($servletClass, 'org.easysdi.proxy.wms.WMSProxyServlet') == 0 )
	{
		//Image size
		$thePolicy->ImageSize="";
		if(strlen($minHeight)>0 && strlen($minWidth>0) )
		{
			$thePolicy->ImageSize->Minimum->Width = $minWidth;
			$thePolicy->ImageSize->Minimum->Height = $minHeight;
		}
		if(strlen($maxHeight)>0 && strlen($maxWidth>0) )
		{
			$thePolicy->ImageSize->Maximum->Width = $maxWidth;
			$thePolicy->ImageSize->Maximum->Height = $maxHeight;
		}
	}
	
	//Users and Roles
	if (is_array($allUsers))
	{
		$thePolicy->Subjects="";
		$thePolicy->Subjects['All']="true";
	}
	else
	{
		$thePolicy->Subjects="";
		$thePolicy->Subjects['All']="false";
		$userNameList = JRequest::getVar("userNameList");
		if (sizeof($userNameList )>0)
		{
			foreach ($userNameList as $user)
			{
				$node = $thePolicy->Subjects->addChild(User,$user);
			}
		}
		$roleNameList = JRequest::getVar("roleNameList");
		if (sizeof($roleNameList)>0)
		{
			foreach ($roleNameList as $role)
			{
				$node = $thePolicy->Subjects->addChild(Role,$role);
			}
		}
	}
	
	//Operations
	$AllOperations = JRequest::getVar("AllOperations","");
	if (is_array($AllOperations))
	{
		$thePolicy->Operations="";
		$thePolicy->Operations['All']="true";
	}
	else
	{
		$thePolicy->Operations="";
		$thePolicy->Operations['All']="false";
		$operationsList = JRequest::getVar("operation");
		if (sizeof($operationsList)>0)
		{
			foreach($operationsList as $operation)
			{
				$node = $thePolicy->Operations->addChild(Operation)->addChild(Name,$operation);
			}
		}
	}
	
	if (strcasecmp($servletClass, 'org.easysdi.proxy.csw.CSWProxyServlet') == 0 )
	{
		//Visibility
		$AllVisibilities = JRequest::getVar("AllVisibilities","");
		if (is_array($AllVisibilities))
		{
			$thePolicy->ObjectVisibilities="";
			$thePolicy->ObjectVisibilities['All']="true";
		}
		else
		{
			$thePolicy->ObjectVisibilities="";
			$thePolicy->ObjectVisibilities['All']="false";
			$visibilitiesList = JRequest::getVar("visibility");
			if (sizeof($visibilitiesList)>0)
			{
				foreach($visibilitiesList as $visibility)
				{
					$node = $thePolicy->ObjectVisibilities->addChild(Visibility,$visibility);
				}
			}
		}
		
		//Status
		$AllStatus = JRequest::getVar("AllStatus","");
		if (is_array($AllStatus))
		{
			$thePolicy->ObjectStatus="";
			$thePolicy->ObjectStatus['All']="true";
		}
		else
		{
			$thePolicy->ObjectStatus="";
			$thePolicy->ObjectStatus['All']="false";
			$statusList = JRequest::getVar("status");
			if (sizeof($statusList)>0)
			{
				foreach($statusList as $status)
				{
					$node = $thePolicy->ObjectStatus->addChild(Status,$status);
					if (strcasecmp($status, 'published')==0)
					{
						$versionMode = JRequest::getVar("objectversion_mode","last");
						$node[version] = $versionMode;
					}
				}
			}
		}
		
		//ObjectType
		$AllObjectType = JRequest::getVar("AllObjectType","");
		if (is_array($AllObjectType))
		{
			$thePolicy->ObjectTypes="";
			$thePolicy->ObjectTypes['All']="true";
		}
		else
		{
			$thePolicy->ObjectTypes="";
			$thePolicy->ObjectTypes['All']="false";
			$ObjectTypesList = JRequest::getVar("objectType");
			if (sizeof($ObjectTypesList)>0)
			{
				foreach($ObjectTypesList as $objecttype)
				{
					$node = $thePolicy->ObjectTypes->addChild(ObjectType,$objecttype);
				}
			}
		}
	}
			
	//Servers
	$AllServer = JRequest::getVar("AllServers","");
	if (is_array($AllServer))
	{
		//All servers checked
		$thePolicy->Servers['All']="true";
		$thePolicy->Servers="";
		for($i=0;;$i++)
		{
			$remoteServer = JRequest::getVar("remoteServer$i","");
			$remoteServerPolicy="";
			
			if(strlen($remoteServer)>0)
			{
				$theServer = $thePolicy->Servers->addChild('Server');
				$theServer->url=$remoteServer;
				$serverPrefixe = JRequest::getVar("serverPrefixe$i","");
				$theServer->Prefix =$serverPrefixe;
				$serverNamespace = JRequest::getVar("serverNamespace$i","");
				$theServer->Namespace = $serverNamespace;
				
				if (strcasecmp($servletClass, 'org.easysdi.proxy.csw.CSWProxyServlet') == 0 )
					$theServer->Metadata ="";
				if (strcasecmp($servletClass, 'org.easysdi.proxy.wms.WMSProxyServlet') == 0 || strcasecmp($servletClass, 'org.easysdi.proxy.wmts.v100.WMTS100ProxyServlet') == 0) 
					$theServer->Layers ="";
				if (strcasecmp($servletClass, 'org.easysdi.proxy.wfs.WFSProxyServlet') == 0 )
					$theServer->FeatureTypes="";
				
				while (list($key, $val) = each($params )) 
				{
					if (!(strpos($key,"featuretype@$i")===false))
					{
							$theServer->FeatureTypes['All']='true';
							$theFeatureType = $theServer->FeatureTypes->addChild('FeatureType');
							$theFeatureType->Name=$val;
							$theFeatureType->Attributes['All']="true";
					}
	
					if (!(strpos($key,"layer@$i")===false))
					{
						$theServer->Layers['All']='true';
						$theLayer = $theServer->Layers->addChild('Layer');
						$theLayer->Name =$val;
					}
				}
				reset($params);
			}
			else
			{
				break;
			}
		}
	}
	else
	{
		//All servers not checked
		$thePolicy->Servers['All']="false";
		$thePolicy->Servers="";
		
		for ($i=0;;$i++)
		{
			$remoteServer = JRequest::getVar("remoteServer$i","");
			$remoteServerPolicy="";
	
			if (strlen($remoteServer)>0)
			{
				$theServer = $thePolicy->Servers->addChild('Server');
				$theServer->url=$remoteServer;
				$serverPrefixe = JRequest::getVar("serverPrefixe$i","");
				$theServer->Prefix =$serverPrefixe;
				$serverNamespace = JRequest::getVar("serverNamespace$i","");
				$theServer->Namespace = $serverNamespace;
				
				if (strcasecmp($servletClass, 'org.easysdi.proxy.csw.CSWProxyServlet') == 0 )
					$theServer->Metadata ="";
				if (strcasecmp($servletClass, 'org.easysdi.proxy.wms.WMSProxyServlet') == 0 || strcasecmp($servletClass, 'org.easysdi.proxy.wmts.v100.WMTS100ProxyServlet') == 0)
					$theServer->Layers ="";
				if (strcasecmp($servletClass, 'org.easysdi.proxy.wfs.WFSProxyServlet') == 0 )
					$theServer->FeatureTypes="";
				$foundParamToExclude = false;
				$foundLayer = false;
				$foundFeatureType = false;
				
				while (list($key, $val) = each($params )) 
				{
					if (!(strpos($key,"param_$i")===false))
					{
						//Parameter to exclude of the metadata
						$theServer->Metadata->Attributes['All']='false';
						if (count($theServer->Metadata->Attributes->Exclude)==0)
						{
							$theServer->Metadata->Attributes->Exclude= "";
						}
						if (strlen($val)>0)
						{
							$theServer->Metadata->Attributes->Exclude->addChild('Attribute',$val);
						}
					 	$foundParamToExclude=true;
					}
	
					if (!(strpos($key,"featuretype@$i")===false))
					{
						$AllFeatureTypes = JRequest::getVar("AllFeatureTypes@$i","");
						if(strlen($AllFeatureTypes)>0)
						{
							//All featuretypes checked
							$foundFeatureType = true;
							$theServer->FeatureTypes['All']='true';
							$theFeatureType = $theServer->FeatureTypes->addChild('FeatureType');
							$theFeatureType->Name=$val;
						}
						else
						{
							$foundFeatureType = true;
							$theServer->FeatureTypes['All']='false';
							$theFeatureType = $theServer->FeatureTypes->addChild('FeatureType');
							$theFeatureType->Name=$val;
								$len =strlen("featuretype@$i")+1;
								$lentot = strlen ($key);
								$ftnum = substr($key,($lentot-$len ) *-1);
		
							//Attribute to keep
								$attributeList = JRequest::getVar("AttributeList@$i@$ftnum","");
							if(strlen($attributeList)>0)
							{
								$theFeatureType->Attributes['All']="false";
								$attributeList = str_replace(" ","",$attributeList);
								$attributesArray =  array();
								ADMIN_proxy::getAttributesList($attributeList, $attributesArray);
								foreach($attributesArray as $attribute)
								{
									$theAttribute = $theFeatureType->Attributes->addChild('Attribute',$attribute);
								}
							}
							else
							{
								$theFeatureType->Attributes['All']="true";
							}
							
							//remote filter
								$remoteFilter = JRequest::getVar("RemoteFilter@$i@$ftnum",null,'defaut','none',JREQUEST_ALLOWRAW);
							if(strlen($remoteFilter)>0)
							{
								$theFeatureType->RemoteFilter =$remoteFilter ;
							}
							
							//local filter
								$localFilter = JRequest::getVar("LocalFilter@$i@$ftnum",null,'defaut','none',JREQUEST_ALLOWRAW);
							if(strlen($remoteFilter)>0 && strlen($localFilter)>0)
							{
								 
								$theFeatureType->LocalFilter =$localFilter ;
							}					
							if(strlen($remoteFilter)<= 0 && strlen($localFilter)>0)
							{
								$mainframe->enqueueMessage(JText::sprintf("EASYSDI_GEOGRAPHIC_FILTER_REMOTE_EMPTY", $remoteServer,$val) ,'error');						
							}
						}
					}
	
					if (!(strpos($key,"layer@$i")===false))
					{
						$AllLayers = JRequest::getVar("AllLayers@$i","");
						if(strlen($AllLayers)>0)
						{
							//All layers checked
							$foundLayer = true;
							$theServer->Layers['All']='true';
							$theLayer = $theServer->Layers->addChild('Layer');
							$theLayer->Name =$val;
						}	
						else
						{
							//All layer not checked
							$foundLayer = true;
							$theServer->Layers['All']='false';
							$theLayer = $theServer->Layers->addChild('Layer');
							$len =strlen("layer@$i")+1;
							$lentot = strlen ($key);
							
							$layernum = substr($key,($lentot-$len ) *-1);
							$theLayer->Name =$val;
							
							$scaleMin = JRequest::getVar("scaleMin@$i@$layernum");
							if (strlen($scaleMin)>0)
							{
								$theLayer->ScaleMin = $scaleMin;
							}
								$scaleMax = JRequest::getVar("scaleMax@$i@$layernum");
							if (strlen($scaleMax)>0)
							{
								$theLayer->ScaleMax = $scaleMax;
							}
		
								$localFilter = JRequest::getVar("LocalFilter@$i@$layernum",null,'defaut','none',JREQUEST_ALLOWRAW);
		
							if (strlen($localFilter)>0)
							{
								//BBOX
								$bbox = JRequest::getVar("BBOX@$i@$layernum");
								$v = strpos($bbox,",");
								$theLayer->BoundingBox['SRS'] = substr($bbox,0,$v);
								$bbox = substr($bbox,$v+1);
								$v = strpos($bbox,",");
								$theLayer->BoundingBox['minx'] = substr($bbox,0,$v);
								$bbox = substr($bbox,$v+1);
								$v = strpos($bbox,",");
								$theLayer->BoundingBox['miny'] = substr($bbox,0,$v);
								$bbox = substr($bbox,$v+1);
								$v = strpos($bbox,",");
								$theLayer->BoundingBox['maxx'] = substr($bbox,0,$v);
								$theLayer->BoundingBox['maxy'] = substr($bbox,$v+1);
								$theLayer->Filter = $localFilter;
							}
						}
					}
					
				}
				if($foundFeatureType == false && strcasecmp($servletClass, 'org.easysdi.proxy.wfs.WFSProxyServlet') == 0)
				{
					$theServer->FeatureTypes['All']='false';
				}
				if($foundLayer == false && strcasecmp($servletClass, 'org.easysdi.proxy.wms.WMSProxyServlet') == 0)
				{
					$theServer->Layers['All']='false';
			}
				if ($foundParamToExclude==false && strcasecmp($servletClass, 'org.easysdi.proxy.csw.CSWProxyServlet') == 0 )
				{			  
					$theServer->Metadata['All']='true';
					$theServer->Metadata->Attributes['All']='true';								
				}
				
				reset($params);
			}
			else
			{
				break;
			}
		}
	}
	$xmlConfigFile->asXML($policyFile);
}

function getAttributesList($attributes, &$attributesArray)
{
	if($attributes)
	{
		$index = strpos($attributes,',');
		if($index)
		{
			$attributesArray[] = substr ($attributes,0,$index);
			$em = substr($attributes,$index + 1);
			ADMIN_proxy::getAttributesList($em, $attributesArray);
		}
		else
		{
			$attributesArray[] = $attributes;
		}
	}
	
}

function saveConfig($xml,$configFilePath){
	$configId = JRequest::getVar("configId","New Config");
	$newConfigId = JRequest::getVar("newConfigId",$configId);
	$new = JRequest::getBool("isNewConfig",false);

	if ($new){
		$found = false;

		$i=0;
		foreach ($xml->config as $config) {
			if (strcmp($config['id'],$newConfigId)==0){
				$found = true;
				break;
			}
		}

		while($found){
			foreach ($xml->config as $config) {
				$found=false;
				if (strcmp($config['id'],$newConfigId.$i)==0){
					$found = true;
					break;
				}
			}
			if ($found == false){
				$newConfigId = $newConfigId.$i;
			}
			$i++;
		}

		$config = $xml->addChild("config");
		$config->addAttribute("id",$newConfigId);

		$config->addChild("authorization")->addChild("policy-file");

		$configId=$newConfigId;
	}


	foreach ($xml->config as $config) {
		if (strcmp($config['id'],$configId)==0){
		
			//Id
			$config['id']=$newConfigId;

			//Servlet class
			$servletClass = JRequest::getVar("servletClass");
			$config->{'servlet-class'}=$servletClass;
			
			//XSLT repository
			$config->{"xslt-path"}->{"url"} = JRequest::getVar("xsltPath");

			//Log file
			$config->{'log-config'}->{'logger'}=JRequest::getVar("logger");
			$config->{'log-config'}->{'log-level'}=JRequest::getVar("logLevel");
			$logPath= JRequest::getVar("logPath");
			$logSuffix= JRequest::getVar("logSuffix");
			$logPrefix= JRequest::getVar("logPrefix");
			$logExt= JRequest::getVar("logExt");
			$logPeriod= JRequest::getVar("logPeriod");
			$config->{'log-config'}->{'file-structure'}->{'path'} = $logPath;
			$config->{'log-config'}->{'file-structure'}->{'suffix'} = $logSuffix;
			$config->{'log-config'}->{'file-structure'}->{'prefix'} = $logPrefix;
			$config->{'log-config'}->{'file-structure'}->{'extension'} = $logExt;
			$config->{'log-config'}->{'file-structure'}->{'period'} = $logPeriod;
			$config->{'log-config'}->{'date-format'} = JRequest::getVar("dateFormat","dd/MM/yyyy HH:mm:ss");
			
			//Host translator
			$hostTranslator = JRequest::getVar("hostTranslator");
			$config->{'host-translator'}=$hostTranslator;
			
			//Remote server
			$config->{'remote-server-list'}="";				
			$i=0;
			for($i = 0 ; $i <= JRequest::getVar("nbServer",0); $i++)
			{
				$url = JRequest::getVar("URL_".$i,"");
				
				if (strlen($url)==0) continue;
				$alias = JRequest::getVar("ALIAS_".$i,"");
				$user = JRequest::getVar("USER_".$i,"");
				$pwd = JRequest::getVar("PASSWORD_".$i,"");

				$remoteServer = $config->{'remote-server-list'}->addChild("remote-server");
				if($i == 0)
					$remoteServer['master'] = "true";
				else 
					$remoteServer['master'] = "false";
				$remoteServer->alias=$alias;
				$remoteServer->user=$user;
				$remoteServer->url=$url;
				$remoteServer->password=$pwd;
				if (strcmp($servletClass,"org.easysdi.proxy.csw.CSWProxyServlet")==0 )
				{
					$remoteServer->{'max-records'}=JRequest::getVar("max-records_".$i,"-1");
					$remoteServer->{'login-service'}=JRequest::getVar("login-service_".$i,"");
					$geonetworktransaction  = $remoteServer->addChild("transaction");
					$geonetworktransaction->{'type'}='geonetwork';
				}
			}
						
			//Ogc search filter
			if (strcmp($servletClass,"org.easysdi.proxy.csw.CSWProxyServlet")==0 )
			{
				$ogcSearchFilter = JRequest::getVar("ogcSearchFilter","");
				$config->{"ogc-search-filter"}=$ogcSearchFilter;
			}
			
			//Exception
			$exceptionMode = JRequest::getVar("exception_mode","permissive");
			$config->{"exception"}->{"mode"}=$exceptionMode;

			//Policy
			$policyFile = JRequest::getVar("policyFile");
			$config->{"authorization"}->{"policy-file"}=$policyFile;
			
			//Service metadata
			if (strcmp($servletClass,"org.easysdi.proxy.wmts.v100.WMTS100ProxyServlet")==0 )
			{
				$config = ADMIN_proxy::serviceMetadataOWS($config);
			}
			else 
			{
				$config = ADMIN_proxy::serviceMetadataWFS($config);
			}
			$xml->asXML($configFilePath);
		}
	}
}

/**
 * 
 * Service metadata OWS 1.1.0 spécification
 * @param unknown_type $config
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


function addNewServer($xml){

	$configId = JRequest::getVar("configId");

	foreach ($xml->config as $config) {
		if (strcmp($config['id'],$configId)==0){

			$newRemoteServer = $config->{"remote-server-list"}->addChild("remote-server");
			$newRemoteServer->addChild("url");
			$newRemoteServer->addChild("user");
			$newRemoteServer->addChild("password");
		}
	}


}
	
	
	
}
?>
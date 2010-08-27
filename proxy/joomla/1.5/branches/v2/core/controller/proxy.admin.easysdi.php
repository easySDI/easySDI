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
		
		// Nouvelle configuration ou edition d'une configuration existante
		if (!$new){
			$configId = JRequest::getVar("configId");
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
		
		HTML_proxy::editConfig($xml, $new, $configId, $option);
	}
	
	function editPolicy ($xml, $new=false)
	{
		$database =& JFactory::getDBO(); 
		$language =& JFactory::getLanguage();
		
		//Get  profiles
		//$database->setQuery( "SELECT code as value, translation as text FROM #__sdi_accountprofile ORDER BY text" );
		$database->setQuery( "SELECT ap.code as value, t.label as text FROM #__sdi_language l, #__sdi_list_codelang cl, #__sdi_accountprofile ap LEFT OUTER JOIN #__sdi_translation t ON ap.guid=t.element_guid WHERE t.language_id=l.id AND l.codelang_id=cl.id AND cl.code='".$language->_lang."'" );
		$rowsProfile = $database->loadObjectList();
		echo $database->getErrorMsg();
		
		//Get users
		$database->setQuery( "SELECT #__users.username as value, #__users.name as text FROM #__users INNER JOIN #__sdi_account ON  #__users.id = #__sdi_account.user_id ORDER BY text" );
		$rowsUser = $database->loadObjectList();
		echo $database->getErrorMsg();
		
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
							  FROM #__sdi_list_metadatastate s 
							  ");
		$rowsStatus = $database->loadObjectList();
		//echo $database->getErrorMsg();
		
		
		HTML_proxy::editPolicy($xml, $new, $rowsProfile, $rowsUser, $rowsVisibility, $rowsStatus);
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
				$theNewPolicy->AvailabilityPeriod->Mask="dd-mm-yyyy";
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
					if (strcmp($policy['Id'],$policyId)==0){


						$child = dom_import_simplexml($policy);
						$newPolicy = $child->cloneNode(true);
						$i=0;
						$found=true;
						while($found){
							$found=false;
							foreach ($xmlConfigFile->Policy as $policy2){
									
								if(strcmp($policy2['Id'],$i.'_'.$policyId)==0){
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

	//Check the geographic filters validity
/*	for ($i=0;;$i++)
	{
		$remoteServer = JRequest::getVar("remoteServer$i","");
		
		if (strlen($remoteServer)>0)
		{
			while (list($key, $val) = each($params )) 
			{
				if (!(strpos($key,"featuretype@$i")===false))
				{
					//remote filter
					$remoteFilter = JRequest::getVar("RemoteFilter@$i@$val","");
					//local filter
					$localFilter = JRequest::getVar("LocalFilter@$i@$val","");
					
					//Do not allowed an empty "geographic filter on query" associated with a filled "geographic filter on answer"
					if(strlen($remoteFilter) == 0 && strlen($localFilter)>0)
					{	
						$mainframe->redirect("index.php?option=com_easysdi_proxy&task=editPolicy&configId=$configId&policyId=$policyId&new=$isNewPolicy");
						return;
					}
				}
			}
		}
		else
		{
			break;
		}
	}

	$remoteServer ="";
	$remoteFilter = "";
	$localFilter = "";
	$i= 0;
	$key = "";
	$val ="";
	$params = JRequest::get();*/
	
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
						if (strcmp($policy['Id'],$newPolicyId)==0)
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
							if (strcmp($policy['Id'],$newPolicyId)==0)
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
					$thePolicy->AvailabilityPeriod->Mask="d-mm-yyyy";
					$thePolicy->AvailabilityPeriod->From->Date="28-01-2008";
					$thePolicy->AvailabilityPeriod->To->Date="28-01-2108";
				}
				else
				{
					foreach ($xmlConfigFile->Policy as $policy)
					{
						if (strcmp($policy['Id'],$policyId)==0)
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
	
	//Users and Roles
	if (strlen($allUsers)>0)
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
	
	
	
	if (strcasecmp($servletClass, 'org.easysdi.proxy.csw.CSWProxyServlet') == 0 )
	{
		//Operations
		$AllOperations = JRequest::getVar("AllOperations","");
		if (strlen($AllOperations)>0)
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
		//Visibility
		$AllVisibilities = JRequest::getVar("AllVisibilities","");
		if (strlen($AllVisibilities)>0)
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
		if (strlen($AllStatus)>0)
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
				}
			}
		}
		
		//Version
		$versionMode = JRequest::getVar("objectversion_mode","last");
		$thePolicy->{"ObjectVersion"}->{"mode"}=$versionMode;
	}
			
	//Servers
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
			
			$theServer->Metadata ="";
			$foundParamToExclude=false;
			
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

				if (!(strpos($key,"layer@$i")===false))
				{
					$theServer->Layers['All']='False';
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
						$theLayer->Filter = $localFilter;
					}
				}
			}
			if ($foundParamToExclude==false)
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
			ADMIN_proxy::getAttributesList($em, &$attributesArray);
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
			
			//Log file
			$config->{'servlet-class'}=$servletClass;
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
			while(true){
				$url = JRequest::getVar("URL_".$i,"");

				if (strlen($url)==0) break;
				$user = JRequest::getVar("USER_".$i,"");
				$pwd = JRequest::getVar("PASSWORD_".$i,"");

				$remoteServer = $config->{'remote-server-list'}->addChild("remote-server");
				$remoteServer->user=$user;
				$remoteServer->url=$url;
				$remoteServer->password=$pwd;
				if (strcmp($servletClass,"org.easysdi.proxy.csw.CSWProxyServlet")==0 ){
				
				$remoteServer->{'max-records'}=JRequest::getVar("max-records_".$i,"-1");
				$remoteServer->{'login-service'}=JRequest::getVar("login-service_".$i,"");
				$geonetworktransaction  = $remoteServer->addChild("transaction");
				$geonetworktransaction->{'type'}='geonetwork';
				$geonetworktransaction->{'search-service-url'}=JRequest::getVar("search-service-url_".$i,"");
				$geonetworktransaction->{'delete-service-url'}=JRequest::getVar("delete-service-url_".$i,"");;
				$geonetworktransaction->{'insert-service-url'}=JRequest::getVar("insert-service-url_".$i,"");;

				}
				$i++;
			}
			
			//Exception
			$exceptionMode = JRequest::getVar("exception_mode","permissive");
			$config->{"exception"}->{"mode"}=$exceptionMode;

			//Policy
			$policyFile = JRequest::getVar("policyFile");
			$config->{"authorization"}->{"policy-file"}=$policyFile;
			
			//Service metadata
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
				
			$xml->asXML($configFilePath);
		}
	}



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
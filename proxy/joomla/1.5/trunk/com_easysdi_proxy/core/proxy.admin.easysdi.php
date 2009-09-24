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
	function editPolicy ($xml, $new=false)
	{
		$database =& JFactory::getDBO(); 
		
		//Get  profiles
		$database->setQuery( "SELECT profile_code as value, profile_description as text FROM #__easysdi_community_profile ORDER BY text" );
		$rowsProfile = $database->loadObjectList();
		echo $database->getErrorMsg();
		
		//Get users
		$database->setQuery( "SELECT #__users.username as value, #__users.name as text FROM #__users INNER JOIN #__easysdi_community_partner ON  #__users.id = #__easysdi_community_partner.user_id ORDER BY text" );
		$rowsUser = $database->loadObjectList();
		echo $database->getErrorMsg();
		
		HTML_proxy::editPolicy($xml, $new, $rowsProfile, $rowsUser);
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
				$theNewPolicy->Servers[All]="false";
				$theNewPolicy->Subjects[All]="false";
				$theNewPolicy->Operations[All]="true";
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
		if (strcmp($config[id],$configId)==0){

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
		if (strcmp($config[id],$configId)==0)
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
					$thePolicy[Id]=$newPolicyId;
					$thePolicy[ConfigId]=$configId;
					$policyId=$newPolicyId;
					$thePolicy->Servers[All]="false";
					$thePolicy->Subjects[All]="false";
					$thePolicy->Operations[All]="true";
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
	
	{
		if (strlen($allUsers)>0)
		{
			$thePolicy->Subjects="";
			$thePolicy->Subjects[All]="true";
		}
		else
		{
			$thePolicy->Subjects="";
			$thePolicy->Subjects[All]="false";
		}
		
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

	$thePolicy->Servers[All]="false";
	$thePolicy->Servers="";
	
	for ($i=0;;$i++)
	{
		$remoteServer = JRequest::getVar("remoteServer$i","");
		$remoteServerPolicy="";

		if (strlen($remoteServer)>0)
		{
			$theServer = $thePolicy->Servers->addChild(Server);
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
					$theServer->Metadata->Attributes[All]='false';
					if (count($theServer->Metadata->Attributes->Exclude)==0)
					{
						$theServer->Metadata->Attributes->Exclude= "";
					}
					if (strlen($val)>0)
					{
						$theServer->Metadata->Attributes->Exclude->addChild(Attribute,$val);
					}
				 	$foundParamToExclude=true;
				}

				if (!(strpos($key,"featuretype@$i")===false))
				{
					$theServer->FeatureTypes[All]='false';
					$theFeatureType = $theServer->FeatureTypes->addChild('FeatureType');
					$theFeatureType->Name=$val;

					//Attribute to keep
					$attributeList = JRequest::getVar("AttributeList@$i@$val","");
					if(strlen($attributeList)>0)
					{
						$theFeatureType->Attributes[All]="false";
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
						$theFeatureType->Attributes[All]="true";
					}
					
					//remote filter
					$remoteFilter = JRequest::getVar("RemoteFilter@$i@$val",null,'defaut','none',JREQUEST_ALLOWRAW);
					if(strlen($remoteFilter)>0)
					{
						$theFeatureType->RemoteFilter =$remoteFilter ;
					}
					
					//local filter
					$localFilter = JRequest::getVar("LocalFilter@$i@$val",null,'defaut','none',JREQUEST_ALLOWRAW);
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
					$theServer->Layers[All]='False';
					$theLayer = $theServer->Layers->addChild('Layer');
					$theLayer->Name =$val;
					$scaleMin = JRequest::getVar("scaleMin@$i@$val");
					if (strlen($scaleMin)>0)
					{
						$theLayer->ScaleMin = $scaleMin;
					}
					$scaleMax = JRequest::getVar("scaleMax@$i@$val");
					if (strlen($scaleMax)>0)
					{
						$theLayer->ScaleMax = $scaleMax;
					}

					$localFilter = JRequest::getVar("LocalFilter@$i@$val",null,'defaut','none',JREQUEST_ALLOWRAW);

					if (strlen($localFilter)>0)
					{
						$theLayer->Filter = $localFilter;
					}
				}
			}
			if ($foundParamToExclude==false)
			{			  
				$theServer->Metadata[All]='true';
				$theServer->Metadata->Attributes[All]='true';								
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
			$config['id']=$newConfigId;
			$servletClass = JRequest::getVar("servletClass");
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
			$config->{'log-config'}->{'date-format'} = "dd/MM/yyyy HH:mm:ss";
			$hostTranslator = JRequest::getVar("hostTranslator");
			$config->{'host-translator'}=$hostTranslator;
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
			$policyFile = JRequest::getVar("policyFile");
			$config->{"authorization"}->{"policy-file"}=$policyFile;
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
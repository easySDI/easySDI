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
	/**
	 * 
	 * Edit configuration
	 * @param object $xml
	 * @param boolean $new
	 */
	function editConfig($xml,$new = false)
	{
		$option = JRequest::getVar('option');
		$servletClass = JRequest::getVar('servletClass',"");
		
		$task = "editConfig";
		if($new)
			$task = "addConfig";
		
			
		if (!$new){
			$configId = JRequest::getVar("configId");
			if($servletClass == ""){
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
		
		$availableService =  ogcservice::getOgcService();
		$availableServletList = array();
		 foreach($availableService as $service) :
		 	$availableServletList[] = JHTML::_('select.option', $service->servletclass, $service->servletclass);
		 endforeach;
		 
		 $service = new ogcservice(JFactory::getDBO());
		if($servletClass == "org.easysdi.proxy.wms.WMSProxyServlet" )
		{
			$service->load('WMS');
			$availableVersion = $service->getVersions();
			HTML_proxyWMS::editConfigWMS($xml, $new, $configId, $availableServletList,$availableVersion, $option,$task);
		}
		else if($servletClass == "org.easysdi.proxy.wmts.WMTSProxyServlet" )
		{
			$service->load('WMTS');
			$availableVersion = $service->getVersions();
			HTML_proxyWMTS::editConfigWMTS($xml, $new, $configId, $availableServletList,$availableVersion, $option,$task);
		}
		else if($servletClass == "org.easysdi.proxy.csw.CSWProxyServlet" )
		{
			$service->load('CSW');
			$availableVersion = $service->getVersions();
			HTML_proxyCSW::editConfigCSW($xml, $new, $configId, $availableServletList,$availableVersion, $option,$task);
		}
		else 
		{
			$service->load('WFS');
			$availableVersion = $service->getVersions();
			HTML_proxyWFS::editConfigWFS($xml, $new, $configId, $availableServletList, $availableVersion,$option,$task);	
		} 
	}
	
	/**
	 * 
	 * Edit policy 
	 * @param object $xml
	 * @param boolean $new
	 */
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
	
	/**
	 * 
	 * Add a new policy
	 * @param object $xml
	 * @return object
	 */
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

	/**
	 * Add a new configuration
	 * @param object $xml
	 * @return object
	 */
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
	
	/**
	 * Order policy in configuration
	 * @deprecated
	 * @param object $xml
	 */
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
	
	/**
	 * Order policy in configuration
	 * @deprecated
	 * @param object $xml
	 */
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
	
	/**
	 * Duplicate a policy definition
	 * @param object $xml
	 */
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
	
	/**
	 * Delete policy
	 * @param object $xml
	 * @param String $configId
	 * @param String $policyId
	 */
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
	
	/**
	 * Delete configuration
	 * @param object $xml
	 * @param String $configFilePath
	 * @param String $configId
	 */
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
	
	/**
	 * 
	 * Save
	 * @param object $xmlConfig
	 * @param String $componentConfigFilePath
	 */
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
	
	/**
	 * Save policy
	 * @param object $xml
	 */
	function savePolicy($xml){
		
		$servletClass = JRequest::getVar("servletClass");
		
		$params = ADMIN_proxy::savePolicyCommonParts($xml);
		
		if (strcasecmp($servletClass, 'org.easysdi.proxy.wms.WMSProxyServlet') == 0 )
		{
			ADMIN_proxy::savePolicyWMS($params[2]);	
		}else if (strcasecmp($servletClass, 'org.easysdi.proxy.wmts.WMTSProxyServlet') == 0 ){
			ADMIN_proxy::savePolicyWMTS($params[2]);
		}else if (strcasecmp($servletClass, 'org.easysdi.proxy.wfs.WFSProxyServlet') == 0 ){
			ADMIN_proxy::savePolicyWFS($params[2]);
		}else if (strcasecmp($servletClass, 'org.easysdi.proxy.csw.CSWProxyServlet') == 0 ){
			ADMIN_proxy::savePolicyCSW($params[2]);
		}
	
		//Save to file
		$params[1]->asXML($params[0]);
	}
	
	/**
	 * Save the policy informations common to all OWS connector
	 * @param object $xml
	 */
	function savePolicyCommonParts ($xml){
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
						$thePolicy->AvailabilityPeriod->Mask="dd-MM-yyyy";
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
	
		$result = array(0 => $policyFile,1=> $xmlConfigFile, 2=>$thePolicy);
		return $result;
	}
	
	/**
	 * Save the policy informations specific to a WMS connector
	 * @param object $thePolicy
	 */
	function savePolicyWMS($thePolicy){
		$params = JRequest::get();
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
					
					$theServer->Layers ="";
					
					while (list($key, $val) = each($params )) 
					{
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
					
					$theServer->Layers ="";
					$foundLayer = false;
					
					while (list($key, $val) = each($params )) 
					{
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
								
								//WMS : scale
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
								
								//WMS : write BBOX in the policy file
								if (strlen($localFilter)>0  )
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
					if($foundLayer == false )
					{
						$theServer->Layers['All']='false';
					}
					reset($params);
				}
				else
				{
					break;
				}
			}
		}
	}
	
	/**
	 * Save the policy informations specific to a CSW connector
	 * @param object $thePolicy
	 */
	function savePolicyCSW($thePolicy){
		$params = JRequest::get();
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
					$theServer->Metadata ="";
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
					$theServer->Metadata ="";
					$foundParamToExclude = false;
					
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
					}
					if ($foundParamToExclude==false  )
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
		
		//BBOX filter
		$maxxDestination = JRequest::getVar("maxxDestination","",'defaut','none',JREQUEST_ALLOWRAW);
		if($maxxDestination != ""){
			$minxDestination = JRequest::getVar("minxDestination","",'defaut','none',JREQUEST_ALLOWRAW);
			$maxyDestination = JRequest::getVar("maxyDestination","",'defaut','none',JREQUEST_ALLOWRAW);
			$minyDestination = JRequest::getVar("minyDestination","",'defaut','none',JREQUEST_ALLOWRAW);
			$crsSource = JRequest::getVar("crsSource","",'defaut','none',JREQUEST_ALLOWRAW);
			$maxxSource = JRequest::getVar("maxx","",'defaut','none',JREQUEST_ALLOWRAW);
			$minxSource = JRequest::getVar("minx","",'defaut','none',JREQUEST_ALLOWRAW);
			$maxySource = JRequest::getVar("maxy","",'defaut','none',JREQUEST_ALLOWRAW);
			$minySource = JRequest::getVar("miny","",'defaut','none',JREQUEST_ALLOWRAW);
			
			$thePolicy->BBOXFilter['crsSource']=$crsSource;
			$thePolicy->BBOXFilter['minx']=$minxSource;
			$thePolicy->BBOXFilter['miny']=$minySource;
			$thePolicy->BBOXFilter['maxx']=$maxxSource;
			$thePolicy->BBOXFilter['maxy']=$maxySource;
			$thePolicy->BBOXFilter->CRS="urn:x-ogc:def:crs:EPSG:4326";
			$thePolicy->BBOXFilter->minx=$minxDestination;
			$thePolicy->BBOXFilter->miny=$minyDestination;
			$thePolicy->BBOXFilter->maxx=$maxxDestination;
			$thePolicy->BBOXFilter->maxy=$maxyDestination;
		}else{
			unset($thePolicy->BBOXFilter);
		}
		$IncludeHarvested = JRequest::getVar("IncludeHarvested","true",'defaut','none',JREQUEST_ALLOWRAW);
		$thePolicy->IncludeHarvested=$IncludeHarvested;
	}
	
	/**
	 * Save the policy informations specific to a WFS connector
	 * @param object $thePolicy
	 */
	function savePolicyWFS($thePolicy){
		$params = JRequest::get();
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
					}
					reset($params);
				}
				else
				{
					break;
				}
			}
		}	else
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
					
					$theServer->FeatureTypes="";
					$foundFeatureType = false;
					
					while (list($key, $val) = each($params )) 
					{
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
					}
					if($foundFeatureType == false )
					{
						$theServer->FeatureTypes['All']='false';
					}
					reset($params);
				}
				else
				{
					break;
				}
			}
		}
	}
	
	/**
	 * Save the policy informations specific to a WMTS connector
	 * @param object $thePolicy
	 */
	function savePolicyWMTS($thePolicy){
		$params = JRequest::get();
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
					/*$theServer->Layers ="";
					
					while (list($key, $val) = each($params )) 
					{
						if (!(strpos($key,"layer@$i")===false))
						{
							$theServer->Layers['All']='true';
							$theLayer = $theServer->Layers->addChild('Layer');
							$theLayer->Name =$val;
						}
					}*/
					reset($params);
				}
				else
				{
					break;
				}
			}
		}	else
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
					$theServer->Layers ="";
					$AllLayers = JRequest::getVar("AllLayers@$i","");
					if(strlen($AllLayers)>0)
					{
						//All layers checked
						$theServer->Layers['All']='true';
						//$theLayer = $theServer->Layers->addChild('Layer');
						//$theLayer->Name =$val;
					}	
					else
					{
						//All layer not checked
						while (list($key, $val) = each($params )) 
						{
							if (!(strpos($key,"layer@$i")===false))
							{
								$theServer->Layers['All']='false';
								$theLayer = $theServer->Layers->addChild('Layer');
								$len =strlen("layer@$i")+1;
								$lentot = strlen ($key);
								
								$layernum = substr($key,($lentot-$len ) *-1);
								$theLayer->Name =$val;
								
								//Get geographical restriction
								$bboxminx = JRequest::getVar("bboxminx@$i@$layernum",null,'defaut','FLOAT',JREQUEST_ALLOWRAW);
								$bboxmaxx = JRequest::getVar("bboxmaxx@$i@$layernum",null,'defaut','FLOAT',JREQUEST_ALLOWRAW);
								$bboxminy = JRequest::getVar("bboxminy@$i@$layernum",null,'defaut','FLOAT',JREQUEST_ALLOWRAW);
								$bboxmaxy = JRequest::getVar("bboxmaxy@$i@$layernum",null,'defaut','FLOAT',JREQUEST_ALLOWRAW);
								$spatialoperator = JRequest::getVar("spatial-operator@$i@$layernum",null,'defaut','string',JREQUEST_ALLOWRAW);
								
								//If a geographical restriction is define, put it in the policy file that it can be retreive to fill the edition form of the policy 
								if($bboxminx != null){
									//BBOX
									$theLayer->BoundingBox['SRS'] = "EPSG:4326";
									$theLayer->BoundingBox['minx'] = $bboxminx;
									$theLayer->BoundingBox['miny'] = $bboxminy;
									$theLayer->BoundingBox['maxx'] = $bboxmaxx;
									$theLayer->BoundingBox['maxy'] = $bboxmaxy;
									$theLayer->BoundingBox['spatialoperator'] = $spatialoperator;
									/*$theLayer->bboxminx =$bboxminx;
									$theLayer->bboxmaxx =$bboxmaxx;
									$theLayer->bboxminy =$bboxminy;
									$theLayer->bboxmaxy =$bboxmaxy;
									$theLayer->spatialoperator = $spatialoperator;*/
								}
								
								//Get min scale denominator restriction
								$nbTileMatrixSetPerLayer = JRequest::getVar("countTileMatrixSet@$i@$layernum",null,'defaut','none',JREQUEST_ALLOWRAW);
								$listMinScaleDenominatorTileMatrixSetId = array();
								$listBBOXTileMatrixSetId = array();
								$listTileMatrixSetIndex=array();
								$listTileMatrixSetCRSUnits=array();
								//Loop throw the the TileMatrixSets linked to the current layer
								for($j = 0 ; $j <= $nbTileMatrixSetPerLayer ; $j++ ){
									//TileMatrixSetId
									$tileMatrixSetId = JRequest::getVar("TileMatrixSetId@$i@$layernum@$j",null,'defaut','none',JREQUEST_ALLOWRAW);
									//Selected minScaleDenominator
									$minScaleDenominator = JRequest::getVar("minScaleDenominator@$i@$layernum@$j",null,'defaut','none',JREQUEST_ALLOWRAW);
									//If a specific minScaleDenominator is selected, process the posted value to extract the numeric value of the scale denominator
									//NB : posted value is "TileMatrixId [ scaleDenominator ]"
									if($minScaleDenominator != "service-value")
										$minScaleDenominator = substr($minScaleDenominator, strpos($minScaleDenominator, "[")+2, (strlen($minScaleDenominator)-2) - (strpos($minScaleDenominator, "[")+2));
									
									//Fill array with the selected scaleDenominator
									$listMinScaleDenominatorTileMatrixSetId[$tileMatrixSetId]=$minScaleDenominator;
									//Fill array with the geographical filter in the tileMatrixSet CRS (transformation was previously done by proj4js in the edition form)
									$listBBOXTileMatrixSetId[$tileMatrixSetId]=array('minx'=>JRequest::getVar("bboxminx@$i@$layernum@$j",null,'defaut','none',JREQUEST_ALLOWRAW),
																					 'maxx'=>JRequest::getVar("bboxmaxx@$i@$layernum@$j",null,'defaut','none',JREQUEST_ALLOWRAW),
																					 'miny'=>JRequest::getVar("bboxminy@$i@$layernum@$j",null,'defaut','none',JREQUEST_ALLOWRAW),
																					 'maxy'=>JRequest::getVar("bboxmaxy@$i@$layernum@$j",null,'defaut','none',JREQUEST_ALLOWRAW)	);
									$listTileMatrixSetIndex[$tileMatrixSetId]=$j;
									//Fill array with the CRS unit get from the proj4js CRS definition, will be used in tile accessibility computation
									$listTileMatrixSetCRSUnits[$tileMatrixSetId]=JRequest::getVar("unitCRS@$i@$layernum@$j",null,'defaut','none',JREQUEST_ALLOWRAW);
								}
								
								//Load the saved GetCapabilities file
								$confObject = JFactory::getApplication();
								$tmpPath = $confObject->getCfg('tmp_path');
								$tmpName = JRequest::getVar("capaServer@$i",null,'defaut','string',JREQUEST_ALLOWRAW);
								$tmpFile = $tmpPath."/".$tmpName.".xml";
								$xmlCapa = simplexml_load_file($tmpFile);
								$namespaces = $xmlCapa->getDocNamespaces();
								$dom_capa = dom_import_simplexml ($xmlCapa);
								$contents = $dom_capa->getElementsByTagNameNS($namespaces[''],'Contents')->item(0);
								$tileMatrixSets = $contents->getElementsByTagName('TileMatrixSet');
								
								//Layer has filter
								$hasFilter = false;
								foreach($listMinScaleDenominatorTileMatrixSetId as $key=>$value) :
									foreach ( $tileMatrixSets as $tileMatrixSet){
										//Get the TileMatrix definition
										if($tileMatrixSet->parentNode->nodeName == "Contents" )
										{
											$tileMatrixSetIdentifier = $tileMatrixSet->getElementsByTagNameNS($namespaces['ows'],'Identifier')->item(0)->nodeValue;
											$tileMatrixSetSupportedCRS = $tileMatrixSet->getElementsByTagNameNS($namespaces['ows'],'SupportedCRS')->item(0)->nodeValue;
											
											if($tileMatrixSetIdentifier == $key){
												$theTileMatrixSet = $theLayer->addChild(TileMatrixSet);
												$theTileMatrixSet['id'] =$key;
												$tileMatrices = $tileMatrixSet->getElementsByTagName('TileMatrix');
												
												if($value == "service-value"){
													//All the TileMatrixSet scale are keept
													if($bboxminx == null){
														//No geographic filter, the TileMatrixSet is keept entirely
														$theTileMatrixSet['All'] ='true';
													}else{
														//Geographic filter id defined, tiles outside the bbox extend have to be excluded
														$hasFilter = true;
														$theTileMatrixSet['All'] ='false';
														for($tm = 0; $tm<$tileMatrices->length; $tm++){
															$tileMatrix = $tileMatrices->item($tm); 
															$tileMatrixIdentifier = $tileMatrix->getElementsByTagNameNS($namespaces['ows'],'Identifier')->item(0)->nodeValue;
															$tileMatrixScaleDenominator = $tileMatrix->getElementsByTagName('ScaleDenominator')->item(0)->nodeValue;
															
															ADMIN_proxy::addAuthorizedTiles($theTileMatrixSet,$tileMatrixIdentifier, $tileMatrix, $tileMatrixSetSupportedCRS, $listBBOXTileMatrixSetId, $listTileMatrixSetCRSUnits, $tileMatrixSetIdentifier, $tileMatrixScaleDenominator,$spatialoperator);
														}
													}
												}else{
													//A minimum scale denominator is defined, lower value have to be excluded
													$hasFilter=true;
													$theTileMatrixSet->minScaleDenominator = $value;
													$theTileMatrixSet['All'] ='false';
													if($bboxminx == null){
														//No geographic filter, the TileMatrix are keept entirely
														for($tm = 0; $tm<$tileMatrices->length; $tm++){
															$tileMatrix = $tileMatrices->item($tm); 
															$tileMatrixIdentifier = $tileMatrix->getElementsByTagNameNS($namespaces['ows'],'Identifier')->item(0)->nodeValue;
															$tileMatrixScaleDenominator = $tileMatrix->getElementsByTagName('ScaleDenominator')->item(0)->nodeValue;
															if($tileMatrixScaleDenominator >= $value){
																$theTileMatrix = $theTileMatrixSet->addChild(TileMatrix);
																$theTileMatrix['id']= $tileMatrixIdentifier;
																$theTileMatrix['All']= 'true';
															}
														}
													}else{
														//Geographic filter id defined, tiles outside the bbox extend have to be excluded
														for($tm = 0; $tm<$tileMatrices->length; $tm++){
															$tileMatrix = $tileMatrices->item($tm); 
															$tileMatrixIdentifier = $tileMatrix->getElementsByTagNameNS($namespaces['ows'],'Identifier')->item(0)->nodeValue;
															$tileMatrixScaleDenominator = $tileMatrix->getElementsByTagName('ScaleDenominator')->item(0)->nodeValue;
															
															if($tileMatrixScaleDenominator >= $value){
																ADMIN_proxy::addAuthorizedTiles($theTileMatrixSet,$tileMatrixIdentifier, $tileMatrix, $tileMatrixSetSupportedCRS, $listBBOXTileMatrixSetId, $listTileMatrixSetCRSUnits, $tileMatrixSetIdentifier, $tileMatrixScaleDenominator,$spatialoperator);
															}
														}
													}
												}
												
											}						
										}
									}
	 							endforeach;
	 							if(!$hasFilter){
	 								$theLayer['All'] = 'true';
	 							}else{
	 								$theLayer['All'] = 'false';
	 							}
							}
						}
						unlink($tmpFile);
					}
					reset($params);
				}
				else
				{
					break;
				}
			}
		}
	}
	
	
	/**
	 * Calculate the range of tiles allowed by the BBOX filter
	 * Algorithm given by OGC 07-057r2 document (page 9).
	 * @param unknown_type $theTileMatrixSet : Element of the policy document describing the TileMatrixSet
	 * @param unknown_type $tileMatrixIdentifier : identifier of the TileMatrix
	 * @param unknown_type $tileMatrix : Element of the GetCapabilities document describing the TileMatrix
	 * @param unknown_type $tileMatrixSetSupportedCRS : CRS of the TileMatrixSet
	 * @param unknown_type $listBBOXTileMatrixSetId : list of the BBOX filter defined in the submited form. Accessible by the TileMatrixSetId
	 * @param unknown_type $listTileMatrixSetCRSUnits : list of the CRS units get from the submited form. Accessible by the TileMatrixSetId
	 * @param unknown_type $tileMatrixSetIdentifier : identifier of the TileMatrixSet
	 * @param unknown_type $tileMatrixScaleDenominator : ScaleDenominator of the TileMatrix
	 */
	function addAuthorizedTiles($theTileMatrixSet,$tileMatrixIdentifier, $tileMatrix, $tileMatrixSetSupportedCRS, $listBBOXTileMatrixSetId, $listTileMatrixSetCRSUnits, $tileMatrixSetIdentifier, $tileMatrixScaleDenominator,$spatialoperator){
		
		
		//Get TileMatrix informations
		$tileWidth = $tileMatrix->getElementsByTagName('TileWidth')->item(0)->nodeValue;
		$tileHeight = $tileMatrix->getElementsByTagName('TileHeight')->item(0)->nodeValue;
		$matrixWidth = $tileMatrix->getElementsByTagName('MatrixWidth')->item(0)->nodeValue;
		$matrixHeight = $tileMatrix->getElementsByTagName('MatrixHeight')->item(0)->nodeValue;
	
		//Get BBOX filter define for this layer
		$bboxCRS = $listBBOXTileMatrixSetId[$tileMatrixSetIdentifier];
		//Get the unit of the TileMatrixSet CRS 
		$CRSunits = $listTileMatrixSetCRSUnits[$tileMatrixSetIdentifier];
		
		//Calculate the meterPerUnit parameter
		$meterPerUnit = 111319.49;
		if($CRSunits == "m" || $CRSunits == "metre")
			$meterPerUnit = 1;
		if($CRSunits == "grad")
			$meterPerUnit = 100187.54;
		if($CRSunits == "degree" || $CRSunits == "Degree")
			$meterPerUnit = 111319.49;
		if($CRSunits == "rad" || $CRSunits == "radian" || $CRSunits == "Radian")
			$meterPerUnit = 6378137;
	
		//Get the East and North coordinates of the top left corner of the TileMatrix.
		//
		//EPSG authority CRS definition (see : www.epsg-registry.org):
		//- Geographic CRS give the topLeftCorner as <TopLeftCorner>North East</TopLeftCorner>
		//- Projected CRS give the topLeftCorner as <TopLeftCorner>East North</TopLeftCorner>
		//OGC authority CRS defnition (see : OGC 07-057r7 document)
		//- all CRS give the topLeftCorner as <TopLeftCorner>East North</TopLeftCorner>
		//Others authorities are not supported.
		$topLeftCorner = $tileMatrix->getElementsByTagName('TopLeftCorner')->item(0)->nodeValue;
		if(!strpos($CRSunits,'m') && strpos($tileMatrixSetSupportedCRS,'EPSG')){
			$topLeftCornerY = substr($topLeftCorner, 0, strpos($topLeftCorner," "));
			$topLeftCornerX = substr($topLeftCorner, strpos($topLeftCorner," ")+1);
		}else{
			$topLeftCornerX = substr($topLeftCorner, 0, strpos($topLeftCorner," "));
			$topLeftCornerY = substr($topLeftCorner, strpos($topLeftCorner," ")+1);
		}
		
		//Calculate TileMatrix dimensions
		$pixelSpan = $tileMatrixScaleDenominator *0.00028 / $meterPerUnit;
		$tileSpanX = $tileWidth * $pixelSpan;
		$tileSpanY = $tileHeight * $pixelSpan;
		$tileMatrixMaxX = $topLeftCornerX + $tileSpanX * $matrixWidth;
		$tileMatrixMinY = $topLeftCornerY - $tileSpanY * $matrixHeight;
		$epsilon = 0.000001;
		
		//Calculate the range of tileset indexes included in the BBOX filter
		if($spatialoperator == "touch"){
			$tileMinCol = floor(($bboxCRS[minx] - $topLeftCornerX)/$tileSpanX + $epsilon);
			$tileMaxCol = floor(($bboxCRS[maxx] - $topLeftCornerX)/$tileSpanX - $epsilon);
			$tileMinRow = floor(($topLeftCornerY - $bboxCRS[maxy])/$tileSpanY + $epsilon);
			$tileMaxRow = floor(($topLeftCornerY - $bboxCRS[miny])/$tileSpanY - $epsilon);
		}else{
			$tileMinCol = ceil(($bboxCRS[minx] - $topLeftCornerX)/$tileSpanX + $epsilon);
			$tileMaxCol = floor(($bboxCRS[maxx] - $topLeftCornerX)/$tileSpanX - $epsilon) -1;
			$tileMinRow = ceil(($topLeftCornerY - $bboxCRS[maxy])/$tileSpanY + $epsilon) ;
			$tileMaxRow = floor(($topLeftCornerY - $bboxCRS[miny])/$tileSpanY - $epsilon) -1;
		}
		
		//Error control to avoid requesting empty tiles
		if($tileMinCol < 0){
			$tileMinCol = 0;
		}
		if($tileMaxCol < 0){
			return;
		}
		if($tileMinCol > $tileMaxCol){
			return;
		}
		if($tileMinCol >= $matrixWidth){
			return;
		}
		if($tileMaxCol >= $matrixWidth){
			$tileMaxCol = $matrixWidth -1;
		}
		if($tileMinRow < 0){
			$tileMinRow = 0;
		}
		if($tileMaxRow < 0){
			return;
		}
		if($tileMinRow > $tileMaxRow){
			return;
		}
		if($tileMinRow >= $matrixHeight){
			return;
		}
		if($tileMaxRow >= $matrixHeight){
			$tileMaxRow = $matrixHeight -1;
		}
		
		//Control if the BBOX filter and the TileMatrix extent intersect each other
		if($tileMatrixMaxX < $bboxCRS[minx] 
			|| $tileMatrixMinY > $bboxCRS[maxy]
			|| $topLeftCornerY < $bboxCRS[miny] 
			|| $topLeftCornerX > $bboxCRS[maxx]){
			//No intersection : none of the Tile is allowed
			//$theTileMatrix['none'] = 'true';
		}
		else{
			$theTileMatrix = $theTileMatrixSet->addChild(TileMatrix);
			$theTileMatrix['id']= $tileMatrixIdentifier;
			$theTileMatrix['All']= 'false';
			//Intersection 
			$theTileMatrix->TileMinCol = $tileMinCol;
			$theTileMatrix->TileMaxCol = $tileMaxCol;
			$theTileMatrix->TileMinRow = $tileMinRow;
			$theTileMatrix->TileMaxRow = $tileMaxRow;
		}
	}
	
	/**
	 * Get attributes list
	 * @param unknown_type $attributes
	 * @param unknown_type $attributesArray
	 */
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
	
	/**
	 * Save configuration 
	 * @param object $xml
	 * @param String $configFilePath
	 */
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
				
				//Supported version				
				$supportedVersionByconfig = json_decode(JRequest::getVar("supportedVersionsByConfig")); 
				$config->{'supported-versions'}="";
				foreach ($supportedVersionByconfig as $version){
					$config->{'supported-versions'}->addChild("version",$version);
				}
								
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
				
				$service = new ogcservice(JFactory::getDBO());
				$service->load(JRequest::getVar('serviceType'));
				$availableVersion = $service->getVersions();
								
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
					
					$supportedVersionNode = $remoteServer->addChild("supported-versions");
					foreach ($availableVersion as $version)
					{
						//dot in variable names are replaced by underscore character when form is posted 
						//Service version contain dot, so : str_replace(".","_",$version)
						$supportedVersion = JRequest::getVar(str_replace(".","_",$version)."_".$i."_state","");
						
 						if(strcmp( $supportedVersion ,"supported") == 0 ){
 							$supportedVersionNode->addChild("version",$version);
 						}
					}
				}
							
				//Harvesting and Ogc search filter
				if (strcmp($servletClass,"org.easysdi.proxy.csw.CSWProxyServlet")==0 )
				{
					$harvesting = JRequest::getVar("harvestingConfig",0);
					if($harvesting == 1)
						$config->{"harvesting-config"}="true";
					else 
						$config->{"harvesting-config"}="false";
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
				if (strcmp($servletClass,"org.easysdi.proxy.wmts.WMTSProxyServlet")==0 )
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
	 * Service metadata OWS 1.1.0 sp�cification
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
	
	/**
	 * Add a new Server to configuration
	 * @param object $xml
	 */
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
		
	/**
	 * Execute all the needed requests on the remote server to achieve the negociation version.
	 * Result of those requests are a list of supported versions by the remote server.
	 * @param string $url : url of the remote server
	 * @param string $user : user for authentication, if needed
	 * @param string $password : password for authentication, if needed
	 * @param string $service : service type (WFS, WMS, CSW or WMTS)
	 * @param string $availableVersions : list of the service versions supported by EasySDI proxy. Only those versions will be tested for capabilities.
	 */
	function negociateVersionForServer($url,$user,$password,$service,$availableVersions){
		$supported_versions = array();
		$versions_array = json_decode($availableVersions,true);
		
		$urlWithPassword = $url;
		
		//Authentication
		if($service === "CSW"){
			//Perform a geonetwork login	
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
			curl_setopt ($ch, CURLOPT_COOKIEJAR, "cookie.txt");
			curl_setopt ($ch, CURLOPT_POSTFIELDS, "username=".$user."&password=".$password);
			ob_start();
			curl_exec ($ch);
			ob_end_clean();
			curl_close ($ch);
			unset($ch);
		}else{
			if (strlen($user)!=null && strlen($password)!=null){
				if (strlen($user)>0 && strlen($password)>0){
					if (strpos($url,"http:")===False){
						$urlWithPassword =  "https://".$user.":".$password."@".substr($url,8);
					}else{
						$urlWithPassword =  "http://".$user.":".$password."@".substr($url,7);
					}
				}
			}
		}
		
		$pos1 = stripos($urlWithPassword, "?");
		$separator = "&";
		if ($pos1 === false) {
			//"?" Not found then use ? instead of &
			$separator = "?";
		}
		
		$completeurl = "";
		foreach ($versions_array as $version){
			$completeurl = $urlWithPassword.$separator."REQUEST=GetCapabilities&SERVICE=".$service."&VERSION=".$version;
// 			$xmlCapa = simplexml_load_file($completeurl);
			$ch = curl_init($completeurl);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$xml_raw = curl_exec($ch);
			$xmlCapa = simplexml_load_string($xml_raw);
			
			if ($xmlCapa === false){
				global $mainframe;
				$mainframe->enqueueMessage(JText::_('EASYSDI_UNABLE TO RETRIEVE THE CAPABILITIES OF THE REMOTE SERVER' )." - ".$completeurl,'error');
			}else{
				foreach ($xmlCapa->attributes() as $key => $value){
					if($key == 'version'){
						
						if($value == $version)
							$supported_versions[]=$version;
					}
				}
			}
		}
		
		$encoded = json_encode($supported_versions);
		echo $encoded;
		die();
	}
		
}
?>
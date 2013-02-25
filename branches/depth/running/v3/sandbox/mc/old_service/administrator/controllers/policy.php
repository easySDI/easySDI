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
class Easysdi_serviceControllerPolicy extends JController
{

	function __construct() {
		//Need to be add here even if it is in administrator/controller.php
		require_once JPATH_COMPONENT.DS.'helpers'.DS.'easysdi_service.php';
		parent::__construct();
	}

	function copy (){
		$params				= JComponentHelper::getParams('com_easysdi_service');
		$xml 				= simplexml_load_file($params->get('proxyconfigurationfile'));
		$id 				= JRequest::getVar('cid',array(0));
		$connector 			= JRequest::getVar('connector','');
		$configId		 	= JRequest::getVar('config','');
		
		foreach ($xml->config as $config) {
			if (strcmp($config[id],$configId)==0){
		
				$policyFile = $config->{'authorization'}->{'policy-file'};
				$servletClass =  $config->{'servlet-class'};
		
				if (file_exists($policyFile)) {
					$xmlConfigFile = simplexml_load_file($config->{'authorization'}->{'policy-file'});
		
					foreach ($xmlConfigFile->Policy as $policy){
						if (strcmp($policy['Id'],$id[0])==0){
							$child = dom_import_simplexml($policy);
							$newPolicy = $child->cloneNode(true);
							$i=0;
							$found=true;
							while($found){
								$found=false;
								foreach ($xmlConfigFile->Policy as $policy2){
		
									if(strcmp($policy2['Id'],$i.'_'.$id[0])==0){
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
		$this->setRedirect('index.php?option=com_easysdi_service&view=policies&config='.$configId.'&connector='.$connector );
	}
	
	/**
	 * Order policy in configuration
	 * @deprecated
	 * @param object $xml
	 */
	function orderupPolicy($xml){
		$params				= JComponentHelper::getParams('com_easysdi_service');
		$xml 				= simplexml_load_file($params->get('proxyconfigurationfile'));
		$id 				= JRequest::getVar('cid',array(0));
		$connector 			= JRequest::getVar('connector','');
		$configId		 	= JRequest::getVar('config','');
	
	
		foreach ($xml->config as $config) {
			if (strcmp($config[id],$configId)==0){
	
				$policyFile = $config->{'authorization'}->{'policy-file'};
				$servletClass =  $config->{'servlet-class'};
	
				if (file_exists($policyFile)) {
					$xmlConfigFile = simplexml_load_file($config->{'authorization'}->{'policy-file'});
	
					foreach ($xmlConfigFile->Policy as $policy){
	
							
						if (strcmp($policy['ConfigId'],$configId)==0){
	
	
							if (strcmp($policy['Id'],$id[0])==0){
									
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
		$this->setRedirect('index.php?option=com_easysdi_service&view=policies&config='.$configId.'&connector='.$connector );
	}
	
	/**
	 * Order policy in configuration
	 * @deprecated
	 * @param object $xml
	 */
	function orderdownPolicy($xml){
		$params				= JComponentHelper::getParams('com_easysdi_service');
		$xml 				= simplexml_load_file($params->get('proxyconfigurationfile'));
		$id 				= JRequest::getVar('cid',array(0));
		$connector 			= JRequest::getVar('connector','');
		$configId		 	= JRequest::getVar('config','');
	
	
		foreach ($xml->config as $config) {
			if (strcmp($config[id],$configId)==0){
	
				$policyFile = $config->{'authorization'}->{'policy-file'};
				$servletClass =  $config->{'servlet-class'};
	
				if (file_exists($policyFile)) {
					$xmlConfigFile = simplexml_load_file($config->{'authorization'}->{'policy-file'});
					$found = false;
					foreach ($xmlConfigFile->Policy as $policy){
	
							
						if (strcmp($policy['ConfigId'],$configId)==0){
	
	
							if (strcmp($policy['Id'],$id[0])==0){
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
		$this->setRedirect('index.php?option=com_easysdi_service&view=policies&config='.$configId.'&connector='.$connector );
	}
	
	function cancel() {
		$connector 			= JRequest::getVar('connector','');
		$config		 		= JRequest::getVar('configId','');
			
		$this->setRedirect('index.php?option=com_easysdi_service&view=policies&config='.$config.'&connector='.$connector );
	}
	
	function add() {
		$connector 			= JRequest::getVar('connector','');
		$config		 		= JRequest::getVar('config','');
		
		$this->setRedirect('index.php?option=com_easysdi_service&view=policy&task=add&layout='.$connector.'&config='.$config );
	}

	function edit() {
		$id 				= JRequest::getVar('cid',array(0));
		$connector 			= JRequest::getVar('connector','');
		$config		 		= JRequest::getVar('config','');
		 
		$this->setRedirect('index.php?option=com_easysdi_service&view=policy&task=edit&id='.$id[0].'&layout='.$connector.'&config='.$config );
	}

	function delete() {
		$params		= JComponentHelper::getParams('com_easysdi_service');
		$xml 		= simplexml_load_file($params->get('proxyconfigurationfile'));
		$cid 		= JRequest::getVar('cid',array(0));
		$configId		= JRequest::getVar('config','');
		$connector	= JRequest::getVar('connector','');
		
		foreach ($cid as $id ){
			foreach ($xml->config as $config) {
				if (strcmp($config[id],$configId)==0){
			
					$policyFile = $config->{'authorization'}->{'policy-file'};
					$servletClass =  $config->{'servlet-class'};
			
					if (file_exists($policyFile)) {
						$xmlConfigFile = simplexml_load_file($config->{'authorization'}->{'policy-file'});
			
						foreach ($xmlConfigFile->Policy as $policy){
								
							if (strcmp($policy['Id'],$id)==0 && strcmp($policy['ConfigId'],$configId)==0){
			
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
	   	$this->setRedirect('index.php?option=com_easysdi_service&view=policies&config='.$configId.'&connector='.$connector);
	}

	function save() {
		$params 		= JComponentHelper::getParams('com_easysdi_service');
		$xml 			= simplexml_load_file($params->get('proxyconfigurationfile'));
		$connector 		= JRequest::getVar("connector");
		$configId 		= JRequest::getVar("configId","");
		$params 		= $this->savePolicyCommonParts($xml);
		 
		 
		 
		if (strcasecmp($connector, 'WMS') == 0 ){
			$this->savePolicyWMS($params[2]);
		}else if (strcasecmp($connector, 'WMTS') == 0 ){
			$this->savePolicyWMTS($params[2]);
		}else if (strcasecmp($connector, 'WFS') == 0 ){
			$this->savePolicyWFS($params[2]);
		}else if (strcasecmp($connector, 'CSW') == 0 ){
			$this->savePolicyCSW($params[2]);
		}
		 
		//Save to file
		$params[1]->asXML($params[0]);
		 
		$this->setRedirect('index.php?option=com_easysdi_service&view=policies&config='.$configId.'&connector='.$connector);
		 
	}

	/**
	 * Save the policy informations common to all OWS connector
	 * @param object $xml
	 */
	function savePolicyCommonParts ($xml){
		global $mainframe;
	
		$params 			= JRequest::get();
		$servletClass 		= JRequest::getVar("servletClass");
		$allUsers 			= JRequest::getVar("AllUsers","");
		$newPolicyId 		= JRequest::getVar("newPolicyId","");
		$previoustask		= JRequest::getVar("previoustask","edit");
		$isNewPolicy 		= ($previoustask == 'edit')?false: true;
		$dateFrom 			= JRequest::getVar("dateFrom","");
		$dateTo 			= JRequest::getVar("dateTo","");
		$configId 			= JRequest::getVar("configId","");
		$policyId 			= JRequest::getVar("policyId","");
		 
	
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
	
						$thePolicy 									= $xmlConfigFile->addChild('Policy');
						$thePolicy['Id']							= $newPolicyId;
						$thePolicy[ConfigId]						= $configId;
						$policyId									= $newPolicyId;
						$thePolicy->Servers['All']					= "false";
						$thePolicy->Subjects['All']					= "false";
						$thePolicy->Operations['All']				= "true";
						$thePolicy->AvailabilityPeriod->Mask		= "dd-MM-yyyy";
						$thePolicy->AvailabilityPeriod->From->Date	= "28-01-2008";
						$thePolicy->AvailabilityPeriod->To->Date	= "28-01-2108";
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
		$thePolicy->AvailabilityPeriod->From->Date  = $dateFrom;
		$thePolicy->AvailabilityPeriod->To->Date   	= $dateTo;
	
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
					$node = $thePolicy->Subjects->addChild('User',$user);
				}
			}
			$groupNameList = JRequest::getVar("groupNameList");
			if (sizeof($groupNameList)>0)
			{
				foreach ($groupNameList as $group)
				{
					$node = $thePolicy->Subjects->addChild('Group',$group);
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
			$params 			= JRequest::get();
			$maxWidth			= JRequest::getVar("maxWidth","");
			$minWidth			= JRequest::getVar("minWidth","");
			$maxHeight			= JRequest::getVar("maxHeight","");
			$minHeight			= JRequest::getVar("minHeight","");
			 
			//Image size
			$thePolicy->ImageSize="";
			if(strlen($minHeight)>0 && strlen($minWidth>0) )
			{
				$thePolicy->ImageSize->Minimum->Width 	= $minWidth;
				$thePolicy->ImageSize->Minimum->Height	= $minHeight;
			}
			if(strlen($maxHeight)>0 && strlen($maxWidth>0) )
			{
				$thePolicy->ImageSize->Maximum->Width 	= $maxWidth;
				$thePolicy->ImageSize->Maximum->Height 	= $maxHeight;
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
									$this->getAttributesList($attributeList, $attributesArray);
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

														$this->addAuthorizedTiles($theTileMatrixSet,$tileMatrixIdentifier, $tileMatrix, $tileMatrixSetSupportedCRS, $listBBOXTileMatrixSetId, $listTileMatrixSetCRSUnits, $tileMatrixSetIdentifier, $tileMatrixScaleDenominator,$spatialoperator);
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
															$this->addAuthorizedTiles($theTileMatrixSet,$tileMatrixIdentifier, $tileMatrix, $tileMatrixSetSupportedCRS, $listBBOXTileMatrixSetId, $listTileMatrixSetCRSUnits, $tileMatrixSetIdentifier, $tileMatrixScaleDenominator,$spatialoperator);
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
				$this->getAttributesList($em, &$attributesArray);
			}
			else
			{
				$attributesArray[] = $attributes;
			}
		}
	}
}
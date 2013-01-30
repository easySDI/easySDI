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

/**
 * Easysdi_service helper.
 */
class Easysdi_serviceHelper
{
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($vName = '')
	{
		JSubMenuHelper::addEntry(
			JText::_('COM_EASYSDI_SERVICE_SUBMENU_TITLE_PHYSICALSERVICES'),
			'index.php?option=com_easysdi_service&view=physicalservices',
			$vName == 'physicalservices'
		);
		
		JSubMenuHelper::addEntry(
				JText::_('COM_EASYSDI_SERVICE_SUBMENU_CATEGORIES'),
				'index.php?option=com_categories&extension=com_easysdi_service',
				$vName == 'categories'
		);
		
		if ($vName=='categories') {
			JToolBarHelper::title(
					JText::_('COM_EASYSDI_SERVICE_TITLE_CATEGORIES'),
					'easysdi_service-categories');		}
		
		JSubMenuHelper::addEntry(
				JText::_('COM_EASYSDI_SERVICE_SUBMENU_TITLE_VIRTUALSERVICES'),
				'index.php?option=com_easysdi_service&view=virtualservices',
				$vName == 'virtualservices'
		);
		
// 		JSubMenuHelper::addEntry(
// 				JText::_('COM_EASYSDI_SERVICE_SUBMENU_CATEGORIES'),
// 				'index.php?option=com_categories&extension=com_easysdi_service.virtualservice',
// 				$vName == 'virtualservicecategories'
// 		);
		
// 		if ($vName=='virtualservicecategories') {
// 			JToolBarHelper::title(
// 					JText::_('COM_EASYSDI_SERVICE_TITLE_CATEGORIES'),
// 					'easysdi_virtualservice-categories');
// 		}
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return	JObject
	 * @since	1.6
	 */
	public static function getActions($servicetype = null, $categoryId = 0, $serviceId = 0)
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		if (empty($serviceId) && empty($categoryId)) {
			$assetName = 'com_easysdi_core';
		}
		elseif (empty($serviceId) ) {
// 			$assetName = 'com_easysdi_service.'.$servicetype.'service.category.'.(int) $categoryId;
			$assetName = 'com_easysdi_service.category.'.(int) $categoryId;
		}
		else{
// 			$assetName = 'com_easysdi_service.'.$servicetype.'service.'.(int) $serviceId;
			$assetName = 'com_easysdi_service.physicalservice.'.(int) $serviceId;
		}

		$actions = array(
				'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);
	
		foreach ($actions as $action) {
			$result->set($action,	$user->authorise($action, $assetName));
		}

		return $result;
	}
	
	/**
	 * Execute all the needed requests on the remote server to achieve the negotiation version.
	 * Result of those requests are a list of supported versions by the remote server.
	 * @param string $url : url of the remote server
	 * @param string $user : user for authentication, if needed
	 * @param string $password : password for authentication, if needed
	 * @param string $service : service type (WFS, WMS, CSW or WMTS)
	 */
	public static function negotiation ($params){
		$service 				= $params['service'];
		$url 					= $params['resurl'];
		$user 					= $params['resuser'];
		$password 				= $params['respassword'];
	
		$supported_versions 	= array();
		$urlWithPassword 		= $url;
		 
		if(isset($params['serurl']))
		{
			//Authentication needed
			$s_url 				= $params['serurl'];
			$s_user 			= $params['seruser'];
			$s_password 		= $params['serpassword'];
	
			//Perform a geonetwork login
			$ch = curl_init();
			if(!$ch)
			{
				echo 'Error';
				die();
			}
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_URL, $s_url);
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
			curl_setopt ($ch, CURLOPT_COOKIEJAR, "cookie.txt");
			curl_setopt ($ch, CURLOPT_POSTFIELDS, "username=".$s_user."&password=".$s_password);
			ob_start();
			curl_exec ($ch);
			ob_end_clean();
			curl_close ($ch);
			unset($ch);
		}
		else
		{
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
	
		$pos1 		= stripos($urlWithPassword, "?");
		$separator 	= "&";
		if ($pos1 === false) {
			//"?" Not found then use ? instead of &
			$separator = "?";
		}
	
		//Get the implemented version of the requested ServiceConnector
		@$db =& JFactory::getDBO();
		$query = "SELECT c.id as id, sv.value as value
		FROM #__sdi_sys_serviceconnector sc
		INNER JOIN #__sdi_sys_servicecompliance c ON c.serviceconnector_id = sc.id
		INNER JOIN #__sdi_sys_serviceversion sv ON c.serviceversion_id = sv.id
		WHERE c.implemented = 1
		AND sc.value = '".$service."'
		";
		$db->setQuery($query);
		$implemented_versions= $db->loadObjectList();
	
		$completeurl = "";
		foreach ($implemented_versions as $version){
			$completeurl = $urlWithPassword.$separator."REQUEST=GetCapabilities&SERVICE=".$service."&VERSION=".$version->value;
			
			$xmlCapa = simplexml_load_file($completeurl);
			if ($xmlCapa === false)
			{
				$supported_versions['ERROR']=JText::_('COM_EASYSDI_SERVICE_FORM_DESC_SERVICE_NEGOTIATION_ERROR');
				echo json_encode($supported_versions);
				die();
			}
			else
			{
				foreach ($xmlCapa->attributes() as $key => $value){
					if($key == 'version'){
						if($value == $version->value)
							$supported_versions[$version->id]=$version->value;
					}
				}
			}
		}
	
		$encoded = json_encode($supported_versions);
		echo $encoded;
		die();
	}
	
	/**
	 * Execute a GetCapabilities to return queriable layers.
	 * @param string $service : prefixed id of the service
	 */
	public static function getLayers ($params){
		$service 				= $params['service'];
		$user 					= $params['user'];
		$password 				= $params['password'];
		
		$physical_id = -1;
		
		$db = JFactory::getDbo();
		$pos 					= strstr ($service, 'physical_');
		if($pos){
			$id = substr ($service, strrpos ($service, '_')+1);
			$query = 'SELECT s.resourceurl as url, sc.value as connector FROM #__sdi_physicalservice s 
								INNER JOIN #__sdi_sys_serviceconnector sc  ON sc.id = s.serviceconnector_id
								WHERE s.id='.$id ;
			$db->setQuery($query);
			$resource 			= $db->loadObject();
			$db->setQuery(
								'SELECT sv.value as value, sc.id as id FROM #__sdi_service_servicecompliance ssc ' .
								' INNER JOIN #__sdi_sys_servicecompliance sc ON sc.id = ssc.servicecompliance_id '.
								' INNER JOIN #__sdi_sys_serviceversion sv ON sv.id = sc.serviceversion_id'.
								' WHERE ssc.service_id ='.$id.
								' AND ssc.servicetype = "physical"'.
								' LIMIT 1'
			);
			$compliance = $db->loadObject();
			$physical_id = $id;
		}
		else {
			$id = substr ($service, strrpos ($service, '_')+1);
			$query = 'SELECT ps.id as physicalservice_id, ps.resourceurl as url, sc.value as connector FROM #__sdi_virtualservice vs
								INNER JOIN #__sdi_physicalservice ps ON ps.id = vs.physicalservice_id
								INNER JOIN #__sdi_sys_serviceconnector sc  ON sc.id = ps.serviceconnector_id
								WHERE vs.id='.$id;
			$db->setQuery($query);
			$resource 			= $db->loadObject();
			$query = 'SELECT sv.value as value, sc.id as id FROM #__sdi_physicalservice_servicecompliance pssc
							INNER JOIN #__sdi_sys_servicecompliance sc ON sc.id = pssc.servicecompliance_id
							INNER JOIN #__sdi_sys_serviceversion sv ON sv.id = sc.serviceversion_id
							WHERE pssc.physicalservice_id ='.$resource->physicalservice_id.'
							LIMIT 1';
			
			$db->setQuery($query);
			$compliance = $db->loadObject();
			$physical_id = $resource->physicalservice_id;
		}
		
		$url		= $resource->url;
		$connector	= ($resource->connector == "WMSC")? "WMS" : $resource->connector;
		$pos1 		= stripos($url, "?");
		$separator 	= "&";
		if ($pos1 === false) {
			$separator = "?";
		}
		
		$completeurl = $url.$separator."REQUEST=GetCapabilities&SERVICE=".$connector;
		if($compliance && $compliance->value){
			$completeurl .= "&version=".$compliance->value;
		}
		
		$session 	= curl_init($completeurl);
		$httpHeader = array();
		if (!empty($user)  && !empty($password)) {
			$httpHeader[]='Authorization: Basic '.base64_encode($user.':'.$password);
		}
		if (count($httpHeader)>0)
		{
			curl_setopt($session, CURLOPT_HTTPHEADER, $httpHeader);
		}
		curl_setopt($session, CURLOPT_HEADER, false);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($session);
		$http_status = curl_getinfo($session, CURLINFO_HTTP_CODE);
		curl_close($session);
		
		//HTTP status error
		if($http_status != '200') {
			$result['ERROR']=JText::_('COM_EASYSDI_MAP_GET_CAPABILITIES_HTTP_ERROR').$http_status;
			echo json_encode($result);
			die();
		}
		
		$xmlCapa = simplexml_load_string($response);
		$result = array();
		
		//Response empty
		if ($xmlCapa === false) {
			$result['ERROR']=JText::_('COM_EASYSDI_MAP_GET_CAPABILITIES_ERROR');
			echo json_encode($result);
			die();
		}
		
		//OGC exception returned
		if($xmlCapa->getName() == "ServiceExceptionReport") {
			foreach ($xmlCapa->children() as $exception) {
				$ogccode = $exception['code'];
				break;
			}
			$result['ERROR']=JText::_('COM_EASYSDI_MAP_GET_CAPABILITIES_OGC_ERROR').$ogccode;
			echo json_encode($result);
			die();
		}
		
		//Parse capabilities to get layers
		$namespaces = $xmlCapa->getNamespaces(true);
		foreach ($namespaces as $key => $value) {
			if($key == '') {
				$xmlCapa->registerXPathNamespace ("dflt",$value);
			}
			else {
				$xmlCapa->registerXPathNamespace ($key,$value);
			}
			var_dump($key . ' - ' . $value);
		}
		$version = $xmlCapa->xpath ('@version');
		$version = (string)$version[0];
		
		switch ($resource->connector) {
			case "WMS":
			case "WMSC":
				switch ($version) {
					case "1.3.0":
						$wmsLayerList = $xmlCapa->xpath('//dflt:Layer');
						break;
					default:
						$wmsLayerList = $xmlCapa->xpath('/Capability/Layer/Layer');
						break;
				}
				
				//flushing the wmslayer table
				//@$tab_layer =& JTable::getInstance('wmslayer', 'Easysdi_serviceTable');
				$tab_layer = JTable::getInstance('wmslayer', 'Easysdi_serviceTable');
				$tab_layer->wipeByPhysicalId($physical_id);
				unset($tab_layer);
				
				//inserting each wmslayer
				foreach ($wmsLayerList as $wmsLayer) {
					//@$tab_layer =& JTable::getInstance('wmslayer', 'Easysdi_serviceTable');
					$tab_layer->save(Array(
						'name' => (String) $wmsLayer->Name,
						'description' => (String) $wmsLayer->Title,
						'physicalservice_id' => $physical_id
					));
					unset($tab_layer);
				}
				break;
			case "WMTS": 
				$wmtsLayerList = $xmlCapa->xpath('/dflt:Contents/dflt:Layer');
				var_dump($wmtsLayerList);
				//flushing the wmtslayer table
				@$tab_layer =& JTable::getInstance('wmtslayer', 'Easysdi_serviceTable');
				$tab_layer->wipeByPhysicalId($physical_id);
				unset($tab_layer);
				
				//inserting each wmtslayer
				foreach ($wmtsLayerList as $wmtsLayer) {
					var_dump($wmtsLayer);
					@$tab_layer =& JTable::getInstance('wmtslayer', 'Easysdi_serviceTable');
					$tab_layer->save(Array(
						'name' => (String) $wmtsLayer->Title,
						'description' => (String) $wmtsLayer->Title,
						'physicalservice_id' => $physical_id
					));
					unset($tab_layer);
				}
				//die();
				break;
			case "WFS":
				$featureTypeList = $xmlCapa->xpath('//dflt:FeatureType');
				
				//flushing the featureClass table
				@$tab_layer =& JTable::getInstance('featureclass', 'Easysdi_serviceTable');
				$tab_layer->wipeByPhysicalId($physical_id);
				unset($tab_layer);
				
				//inserting each featureClass
				foreach ($featureTypeList as $featureType) {
					@$tab_layer =& JTable::getInstance('featureclass', 'Easysdi_serviceTable');
					$tab_layer->save(Array(
						'name' => (String) $featureType->Name,
						'description' => (String) $featureType->Title,
						'physicalservice_id' => $physical_id
					));
					unset($tab_layer);
				}
				break;
		}
	}
}

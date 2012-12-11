<?php
/**
 * @version     3.0.0
 * @package     com_easysdi_map
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Easysdi_map helper.
 */
class Easysdi_mapHelper
{
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($vName = '')
	{
		JSubMenuHelper::addEntry(
				JText::_('COM_EASYSDI_MAP_TITLE_CONTEXTS'),
				'index.php?option=com_easysdi_map&view=contexts',
				$vName == 'contexts'
		);
		JSubMenuHelper::addEntry(
				JText::_('COM_EASYSDI_MAP_TITLE_LAYERS'),
				'index.php?option=com_easysdi_map&view=layers',
				$vName == 'layers'
		);
		JSubMenuHelper::addEntry(
				JText::_('COM_EASYSDI_MAP_TITLE_GROUPS'),
				'index.php?option=com_easysdi_map&view=groups',
				$vName == 'groups'
		);
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return	JObject
	 * @since	1.6
	 */
	public static function getActions($dataType = null, $Id = null)
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		if (!empty($dataType) && !empty($Id)) {
			$assetName = 'com_easysdi_map.'.$dataType.'.'.(int) $Id;
		}
		else{
			$assetName = 'com_easysdi_map';
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
	 * Execute a GetCapabilities to return queriable layers.
	 * @param string $service : prefixed id of the service
	 */
	public static function getLayers ($params){
		$service 				= $params['service'];
		$user 					= $params['user'];
		$password 				= $params['password'];

		$db = JFactory::getDbo();
		$pos 					= strstr ($service, 'physical_');
		if($pos){
			$id = substr ($service, strrpos ($service, '_')+1);
			$query = 'SELECT s.resourceurl as url, sc.value as connector FROM #__sdi_physicalservice s 
								INNER JOIN #__sdi_sys_serviceconnector sc  ON sc.id = s.serviceconnector_id
								WHERE s.id='.substr ($service, strrpos ($service, '_')+1) ;
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
		}
		else{
			$id = substr ($service, strrpos ($service, '_')+1);
			$query = 'SELECT s.url as url,  sc.value as connector FROM #__sdi_virtualservice s
								INNER JOIN #__sdi_sys_serviceconnector sc  ON sc.id = s.serviceconnector_id
								WHERE s.id='.substr ($service, strrpos ($service, '_')+1);
			$db->setQuery($query);
			$resource 			= $db->loadObject();
			$db->setQuery(
								'SELECT sv.value as value, sc.id as id FROM #__sdi_service_servicecompliance ssc ' .
								' INNER JOIN #__sdi_sys_servicecompliance sc ON sc.id = ssc.servicecompliance_id '.
								' INNER JOIN #__sdi_sys_serviceversion sv ON sv.id = sc.serviceversion_id'.
								' WHERE ssc.service_id ='.$id.
								' AND ssc.servicetype = "virtual"'.
								' LIMIT 1'
				
			);
			$compliance = $db->loadObject();
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
		if (!empty($user)  && !empty($password))
		{
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
		if($http_status != '200')
		{
			$result['ERROR']=JText::_('COM_EASYSDI_MAP_GET_CAPABILITIES_HTTP_ERROR').$http_status;
			echo json_encode($result);
			die();
		}
		
		$xmlCapa = simplexml_load_string($response);
		$result = array();
		
		//Response empty
		if ($xmlCapa === false)
		{
			$result['ERROR']=JText::_('COM_EASYSDI_MAP_GET_CAPABILITIES_ERROR');
			echo json_encode($result);
			die();
		}
		
		//OGC exception returned
		if($xmlCapa->getName() == "ServiceExceptionReport")
		{
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
		foreach ($namespaces as $key => $value)
		{
			if($key == '')
				$xmlCapa->registerXPathNamespace ("dflt",$value);
			else
				$xmlCapa->registerXPathNamespace ($key,$value);
		}
		$version = $xmlCapa->xpath ('@version');
		$version = (string)$version[0];
		
		switch ($resource->connector)
		{
			case "WMS" :
			case "WMSC" :
				switch	($version)	
				{
					case "1.3.0":
						$layers_array = $xmlCapa->xpath('//dflt:Layer/dflt:Name');
						break;
					default:
						$layers_array = $xmlCapa->xpath('//Layer/Name');
						break;
				}	
				break;
			case "WMTS" : 
				$layers_array = $xmlCapa->xpath('//dflt:Layer/ows:Identifier');
				break;
		}
		
		
		if(!$layers_array)
		{
			$result['ERROR']=JText::_('COM_EASYSDI_MAP_GET_CAPABILITIES_LAYERS_ERROR');
			echo json_encode($result);
			die();
		}
		
		foreach ($layers_array as $layer) {
			$result[] = (string)$layer;
		}
		
		$encoded = json_encode($result);
		echo $encoded;
		die();
	}
}

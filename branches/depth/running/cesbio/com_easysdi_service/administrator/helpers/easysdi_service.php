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
			JText::_('COM_EASYSDI_SERVICE_TITLE_SERVICES'),
			'index.php?option=com_easysdi_service&view=services',
			$vName == 'services'
		);
		
		JSubMenuHelper::addEntry(
				JText::_('COM_EASYSDI_SERVICE_SUBMENU_CATEGORIES'),
				'index.php?option=com_categories&extension=com_easysdi_service',
				$vName == 'categories'
		);
		
		if ($vName=='categories') {
			JToolBarHelper::title(
					JText::sprintf('COM_CATEGORIES_CATEGORIES_TITLE', JText::_('com_easysdi_service')),
					'easysdi_service-categories');
		}
		
		JSubMenuHelper::addEntry(
				JText::_('COM_EASYSDI_SERVICE_TITLE_CONFIGS'),
				'index.php?option=com_easysdi_service&view=configs',
				$vName == 'configs'
		);
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return	JObject
	 * @since	1.6
	 */
	public static function getActions($categoryId = 0, $serviceId = 0)
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		if (empty($serviceId) && empty($categoryId)) {
			$assetName = 'com_easysdi_service';
		}
		elseif (empty($serviceId) ) {
			$assetName = 'com_easysdi_service.category.'.(int) $categoryId;
		}
		else{
			$assetName = 'com_easysdi_service.service.'.(int) $serviceId;
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
		$user 					= $params['resusername'];
		$password 				= $params['respassword'];
	
		$supported_versions 	= array();
		$urlWithPassword 		= $url;
		 
		if(isset($params['serurl']))
		{
			//Authentication needed
			$s_url 				= $params['serurl'];
			$s_user 			= $params['serusername'];
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
		$db =& JFactory::getDBO();
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
}

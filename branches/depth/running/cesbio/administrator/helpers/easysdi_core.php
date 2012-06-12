<?php
/**
 * @version     3.0.0
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Easysdi_core helper.
 */
class Easysdi_coreHelper
{
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($vName = '')
	{

		JSubMenuHelper::addEntry(
			JText::_('COM_EASYSDI_CORE_TITLE_USERS'),
			'index.php?option=com_easysdi_core&view=users',
			$vName == 'users'
		);
		
		JSubMenuHelper::addEntry(
				JText::_('COM_EASYSDI_CORE_SUBMENU_CATEGORIES'),
				'index.php?option=com_categories&extension=com_easysdi_core',
				$vName == 'categories'
		);
		
		if ($vName=='categories') {
			JToolBarHelper::title(
					JText::sprintf('COM_CATEGORIES_CATEGORIES_TITLE', JText::_('com_easysdi_core')),
					'easysdi_core-categories');
		}
		
		JSubMenuHelper::addEntry(
			JText::_('COM_EASYSDI_CORE_TITLE_SERVICES'),
			'index.php?option=com_easysdi_core&view=services',
			$vName == 'services'
		);

	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return	JObject
	 * @since	1.6
	 */
	public static function getActions()
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		$assetName = 'com_easysdi_core';

// 		if (empty($articleId) && empty($categoryId)) {
// 			$assetName = 'com_content';
// 		}
// 		elseif (empty($articleId)) {
// 			$assetName = 'com_content.category.'.(int) $categoryId;
// 		}
// 		else {
// 			$assetName = 'com_content.article.'.(int) $articleId;
// 		}
		
		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action,	$user->authorise($action, $assetName));
		}

		return $result;
	}
	
	public static function uuid() 
	{
	      return sprintf('%04x%04x-%04x-%03x4-%04x-%04x%04x%04x',
	        mt_rand(0, 65535), mt_rand(0, 65535), // 32 bits for "time_low"
	        mt_rand(0, 65535), // 16 bits for "time_mid"
	        mt_rand(0, 4095),  // 12 bits before the 0100 of (version) 4 for "time_hi_and_version"
	        bindec(substr_replace(sprintf('%016b', mt_rand(0, 65535)), '01', 6, 2)),
	            // 8 bits, the last two of which (positions 6 and 7) are 01, for "clk_seq_hi_res"
	            // (hence, the 2nd hex digit after the 3rd hyphen can only be 1, 5, 9 or d)
	            // 8 bits for "clk_seq_low"
	        mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535) // 48 bits for "node"  
	    );
	}

 /**
     * Execute all the needed requests on the remote server to achieve the negociation version.
     * Result of those requests are a list of supported versions by the remote server.
     * @param string $url : url of the remote server
     * @param string $user : user for authentication, if needed
     * @param string $password : password for authentication, if needed
     * @param string $service : service type (WFS, WMS, CSW or WMTS)
     */
    public static function negociation($url,$user,$password,$service){
    	echo 'negociation';
    	die();
    	
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
    		$xmlCapa = simplexml_load_file($completeurl);
    			
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

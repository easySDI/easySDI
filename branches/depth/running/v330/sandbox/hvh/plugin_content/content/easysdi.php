<?php
/**
* @version     3.3.0
* @package     com_easysdi_core
* @copyright   Copyright (C) 2013. All rights reserved.
* @license     GNU General Public License version 3 or later; see LICENSE.txt
* @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
*/

defined('_JEXEC') or die;

/**
 * 
 *
 * @package     
 * @subpackage  
 * @since       3.3.0
 */
class plgContentEasysdi extends JPlugin
{
	/**
	 * 
	 * @param object $subject
	 * @param array $config
	 */
	public function __construct($subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}
	/**
	 * Method is called right after the item is saved
	 *
	 * @param	string		The context of the content passed to the plugin (added in 1.6)
	 * @param	object		A JTableContent object
	 * @param	bool		If the content is just about to be created
	 * @since	1.6
	 */
	public function onContentAfterSave($context, $data, $isNew)
	{
		return $this->onContentAfterAction($context, $data, 'UPDATE');
	}

	/**
	 *  Method is called right after the item was deleted
	 *
	 * @param	string	The context for the content passed to the plugin.
	 * @param	object	The data relating to the content that was deleted.
	 * @return	boolean
	 * @since	1.6
	 */
	public function onContentAfterDelete($context, $data)
	{
		return $this->onContentAfterAction($context, $data, 'DELETE');
	}
	
	/**
	 * 
	 * @param string	The context for the content passed to the plugin.
	 * @param object	The data relating to the item.
	 * @param string	Operation performed on the item : UPDATE or DELETE
	 */
	private function onContentAfterAction ($context, $data, $operation){
		$entity = "";
		if ($context == 'com_easysdi_service.policy') {
			$entity = "SdiPolicy";
		}
		else if ($context == 'com_easysdi_service.virtualservice') {
			$entity = "SdiVirtualservice";
		}
		else if ($context == 'com_easysdi_service.physicalservice') {
			$entity = "SdiPhysicalservice";
		}
		else if ($context == 'com_easysdi_contact.user') {
			$entity = "SdiUser";
		}
		else if ($context == 'com_easysdi_contact.organism') {
			$entity = "SdiOrganism";
		}
		$id = $data->id;
		
		$params = JComponentHelper::getParams('com_easysdi_service');
		if(!isset($params))return true;
		$url = $params->get('proxyurl');
		if(!isset($url)) return true;
		
		$url .= "cache?entityclass=".$entity."&id=".$id."&operation=".$operation."&complete=FALSE";
		$user = JFactory::getUser();
		
		$session 	= curl_init($url);
		$httpHeader[]='Authorization: Basic '.base64_encode($user->username .':'.$user->password);
		curl_setopt($session, CURLOPT_HTTPHEADER, $httpHeader);
		curl_setopt($session, CURLOPT_HEADER, false);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		$rawresponse = curl_exec($session);
		curl_close($session);
		$response = json_decode($rawresponse);
		if($response->{"status"} == "OK"){
			JFactory::getApplication()->enqueueMessage(JText::_('PLG_EASYSDICONTENT_OK_INVALIDATION'), 'notice');
			return true;
		}else{
			JFactory::getApplication()->enqueueMessage(JText::_('PLG_EASYSDICONTENT_ERR_INVALIDATION')." ".$response->{"message"}, 'error');
			return false;
		}
	}
}

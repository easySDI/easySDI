<?php
/**
* @version     4.0.0
* @package     plg_easysdi_user
* @copyright   Copyright (C) 2013. All rights reserved.
* @license     GNU General Public License version 3 or later; see LICENSE.txt
* @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
*/

defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.plugin.plugin');

class plgUserEasysdicontact extends JPlugin {

	public function __construct($subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	 * Utility method to check if an easysdi account is linked to the Joomla account to delete.
	 *
	 * @param	array		$user		Holds the Joomla user data.
	 *
	 * @return	void
	 * @since	EasySDI 3.0.0
	 */
	function onUserBeforeDelete($user)
	{
		// ensure the user id is really an int
		$user_id = (int)$user['id'];

		// Load user_easysdi plugin language (not done automatically).
		$lang = JFactory::getLanguage();
		$lang->load('plg_user_easysdi', JPATH_ADMINISTRATOR);
		
		if (empty($user_id)) {
			JFactory::getApplication()->enqueueMessage(JText::_('PLG_EASYSDIUSER_ERR_INVALID_USER'), 'error');
			return false;
		}

		$dbo = JFactory::getDBO();
		$dbo->setQuery('SELECT id FROM #__sdi_user WHERE user_id = '. $user_id );
		$id = $dbo->loadResult();
		if($id){
			JFactory::getApplication()->enqueueMessage(JText::_('PLG_EASYSDIUSER_ERR_CANT_DELETE'), 'error');
			//throw new Exception (JText::_('PLG_EASYSDIUSER_ERR_CANT_DELETE'));
			return false;
		}
		return true;
	}

	/**
	 * Utility method to create an easysdi user after a joomla user was created.
	 *
	 * @param	array		$user		Holds the new user data.
	 * @param	boolean		$isnew		True if a new user is stored.
	 * @param	boolean		$success	True if user was succesfully stored in the database.
	 * @param	string		$msg		Message.
	 *
	 * @return	void
	 * @since	EasySDI 3.0.0
	 */
	function onUserAfterSave($user, $isnew, $success, $msg)
	{
		if(!$success) {
			return false; // if the user wasn't stored we don't create an easysdi user
		}
	
		// ensure the user id is really an int
		$user_id = (int)$user['id'];
		
		// Load user_easysdi plugin language (not done automatically).
		if (empty($user_id)) {
			JFactory::getApplication()->enqueueMessage(JText::_('PLG_EASYSDIUSER_ERR_INVALID_USER'), 'error');
			return false;
		}
		
		if(!$isnew) {
			//Invalidate Proxy cache
			$entity = "Users";
			
			$params = JComponentHelper::getParams('com_easysdi_service');
			if(!isset($params))return true;
			$url = $params->get('proxyurl');
			if(!isset($url)) return true;
			
			$url .= "cache?entityclass=".$entity."&id=".$user_id."&complete=FALSE";
			$juser = JFactory::getUser();
			
			$session 	= curl_init($url);
			$httpHeader[]='Authorization: Basic '.base64_encode($juser->username .':'.$juser->password);
			curl_setopt($session, CURLOPT_HTTPHEADER, $httpHeader);
			curl_setopt($session, CURLOPT_HEADER, false);
			curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
			$rawresponse = curl_exec($session);
			curl_close($session);
			$response = json_decode($rawresponse);
                        
                        $app =& JFactory::getApplication();
                        
                        //Display invalidation proxy message only on backend
                        if ($app->isAdmin()) {
                            if($response->{"status"} == "OK"){
                                    JFactory::getApplication()->enqueueMessage(JText::_('PLG_EASYSDIUSER_OK_INVALIDATION'), 'notice');
                            }else{
                                    JFactory::getApplication()->enqueueMessage(JText::_('PLG_EASYSDIUSER_ERR_INVALIDATION')." ".$response->{"message"}, 'error');
                            }
                        }
			return false; // if the user isn't new we don't create an easysdi user
		}
	
		

		$dbo = JFactory::getDBO();
		
		//Get default user
		$params = JComponentHelper::getParams('com_easysdi_contact');
		$defaultaccount_id = $params->get( 'defaultaccount', null );
		$dbo->setQuery('SELECT * FROM #__sdi_user WHERE user_id = '. $defaultaccount_id );
		$defaultaccount = $dbo->loadObject();
		
		if (!$defaultaccount) {
			JFactory::getApplication()->enqueueMessage(JText::_('PLG_EASYSDIUSER_ERR_FAILED_LOAD_USER'), 'error');
			return false; 
		}
		//Create new EasySDI User account
		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_easysdi_contact/tables');
		$newaccount = JTable::getInstance('user', 'easysdi_contactTable');	
		
		if (!$newaccount) {
			JFactory::getApplication()->enqueueMessage(JText::_('PLG_EASYSDIUSER_ERR_FAILED_INSTANTIATE'), 'error');
			return false;
		}
		
		$newaccount->user_id 						= $user_id;
		$newaccount->catid 							= $defaultaccount->catid;
		$newaccount->params 						= $defaultaccount->params;
		$newaccount->access 						= $defaultaccount->access;
		$newaccount->notificationrequesttreatment 	= $defaultaccount->notificationrequesttreatment;
		//Assets are automatically generated by the frameweork 
		//Check function sets the ordering
		$newaccount->check();
		$result = $newaccount->store();
		
		if (!(isset($result)) || !$result) {
			JFactory::getApplication()->enqueueMessage(JText::_('PLG_EASYSDIUSER_ERR_FAILED_CREATE'), 'error');
			return false;
		}
		
		//Set the state of the new EasySDI account to unpublish
		$newaccount->publish(null, 0);
		JFactory::getApplication()->enqueueMessage(JText::_('PLG_EASYSDIUSER_SUCCESS_CREATE'));
		return true;
	}
}

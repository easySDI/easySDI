<?php
/**
* @version     4.4.4
* @package     plg_easysdi_user
* @copyright   Copyright (C) 2013-2017. All rights reserved.
* @license     GNU General Public License version 3 or later; see LICENSE.txt
* @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
*/

require_once JPATH_ADMINISTRATOR .'/components/com_easysdi_core/libraries/easysdi/user/sdiuser.php';

defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.plugin.plugin');

class plgUserEasysdicontact extends JPlugin {

	public function __construct($subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	 * Utility method to check if an easysdi account is the only one manager of at least one resource.
	 *
	 * @param	array		$user_id		the easySDI user_id
	 *
	 * @return	void
	 * @since	EasySDI 4.2.0
	 */
	function onEasysdiUserBeforeDeleteRoleAttribution($data)
	{
            $user_id = $data['user_id'];
            $organisms_ids = $data['organisms_ids'];
            
            $dbo = JFactory::getDbo();
            $query = $dbo->getQuery(true)
                    ->select('urr2.resource_id')
                    ->from('#__sdi_user_role_resource urr')
                    ->join('LEFT', '#__sdi_user_role_resource urr2 ON urr2.resource_id=urr.resource_id AND urr2.role_id=urr.role_id')
                    ->join('LEFT', '#__sdi_resource r ON r.id=urr.resource_id')
                    ->where('urr.user_id='.$user_id)
                    ->where('urr.role_id=' . sdiUser::resourcemanager, 'AND');
                    if(sizeof($organisms_ids))
                        $query->where('r.organism_id NOT IN ('.implode(',',$organisms_ids).')');
                    $query->group('urr2.resource_id')
                    ->having('COUNT(urr2.id)=1');
            
             //echo $query->__toString(); die();
                    
            // fix Joomla SQL query constructor for SQLServer database
            //$query = str_replace(",", "",$query);
                    
            $dbo->setQuery($query);
            $dbo->execute();
            
            if($dbo->getNumRows()>0){
                return JText::_('PLG_EASYSDIUSER_ERR_USER_UNIQUE_MANAGER');
            }
            
            return true;
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
                $app = JFactory::getApplication();
                
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
                $query = $dbo->getQuery(true);
                $query->select('id');
                $query->from('#__sdi_user');
                $query->where(' user_id = '. (int)$user_id);
                
		$dbo->setQuery($query);
		$id = $dbo->loadResult();
                
		if(!empty($id)){
			JFactory::getApplication()->enqueueMessage(JText::_('PLG_EASYSDIUSER_ERR_CANT_DELETE'), 'error');
                        $app->redirect(JRoute::_('index.php?option=com_users&view=profile&layout=edit'));
                        jExit();
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
                $query = $dbo->getQuery(true);
                $query->select('*');
                $query->from('#__sdi_user');
                $query->where('user_id = '. (int)$defaultaccount_id);
                
		$dbo->setQuery($query);
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

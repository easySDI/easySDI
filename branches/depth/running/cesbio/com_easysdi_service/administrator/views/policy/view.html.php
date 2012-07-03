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

jimport('joomla.application.component.view');

/**
 * View class for a list of Easysdi_service.
 */
class Easysdi_serviceViewPolicy extends JView
{

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		JRequest::setVar('hidemainmenu', true);
	
		JToolBarHelper::title(JText::_('COM_EASYSDI_SERVICE_TITLE_POLICY')." : ".$this->id, 'service.png');
		JToolBarHelper::save('policy.save', 'JTOOLBAR_SAVE');
		JToolBarHelper::back('JTOOLBAR_BACK','index.php?option=com_easysdi_service&view=configs');
	}
	
	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$params 				= JComponentHelper::getParams('com_easysdi_core');
		$this->xml 				= simplexml_load_file($params->get('proxyconfigurationfile'));
		$this->id 				= JRequest::getVar('id',null);
		$this->config 			= JRequest::getVar('config',null);
		$this->connector 		= JRequest::getVar('connector',null);
		$db			 			= JFactory::getDBO();
		//Get Joomla Groups
		$db->setQuery( "SELECT id as value, title as text FROM #__usergroups l ORDER BY text" );
		$this->rowsGroup = $db->loadObjectList();
		echo $db->getErrorMsg();
		
		//Get users
		$db->setQuery( "SELECT #__users.username as value, #__users.name as text FROM #__users INNER JOIN #__sdi_user ON  #__users.id = #__sdi_user.user_id ORDER BY text" );
		$this->rowsUser = $db->loadObjectList();
		echo $db->getErrorMsg();
		
		?>
		<script>
		function disableList(chkBox,list)
		{
			if (document.getElementById(chkBox).checked==true)
			{
				document.getElementById(list).disabled=true;
				for (i = document.getElementById(list).length - 1; i>=0; i--) 
				{
				    document.getElementById(list).options[i].selected = false;
				}
			}
			else
			{
				document.getElementById(list).disabled=false;
			}
		}
		</script>
		<?php 
		$this->addToolbar();
		parent::display($tpl);
	}
	
	function getWMSLayerLocalFilter($theServer,$layer){
	
		if (count($theServer->Layers->Layer)==0) return "";
	
	
		foreach ($theServer->Layers->Layer as $theLayer )
		{
			if (strcmp($theLayer->{'Name'},$layer)==0)
			{
				return $theLayer->{'Filter'};
			}
		}
		return "";
	}
	
	function getLayerMinScale($theServer,$layer){
		if (count($theServer->Layers->Layer)==0) return "";
		foreach ($theServer->Layers->Layer as $theLayer )
		{
			if (strcmp($theLayer->{'Name'},$layer->{'Name'})==0)
			{
				return $theLayer->ScaleMin;
			}
		}
		return "";
	}
	
	function getLayerMaxScale($theServer,$layer){
		if (count($theServer->Layers->Layer)==0) return "";
		foreach ($theServer->Layers->Layer as $theLayer )
		{
			if (strcmp($theLayer->{'Name'},$layer->{'Name'})==0)
			{
				return $theLayer->ScaleMax;
			}
		}
		return "";
	}
	
	function getWMSLayerMinScale($theServer,$layer){
		if (count($theServer->Layers->Layer)==0) return "";
		foreach ($theServer->Layers->Layer as $theLayer )
		{
			if (strcmp($theLayer->{'Name'},$layer)==0)
			{
				return $theLayer->ScaleMin;
			}
		}
		return "";
	}
	function getWMSLayerMaxScale($theServer,$layer){
		if (count($theServer->Layers->Layer)==0) return "";
		foreach ($theServer->Layers->Layer as $theLayer )
		{
			if (strcmp($theLayer->{'Name'},$layer)==0)
			{
				return $theLayer->ScaleMax;
			}
		}
		return "";
	}
	function isLayerChecked($theServer,$layer){
	
		if (strcasecmp($theServer->{"Layers"}['All'],"true")==0) return true;
	
		if (count($theServer->Layers->Layer)==0) return false;
	
		foreach ($theServer->Layers->Layer as $theLayer )
		{
			if (strcmp($theLayer->{'Name'},$layer->{'Name'})==0)
			{
				return true;
			}
		}
		return false;
	}
	
	function isWMSLayerChecked($theServer,$layer){
	
		if (strcasecmp($theServer->{"Layers"}['All'],"true")==0) return true;
	
		if (count($theServer->Layers->Layer)==0) return false;
	
		foreach ($theServer->Layers->Layer as $theLayer )
		{
			if (strcmp($theLayer->{'Name'},$layer)==0)
			{
				return true;
			}
		}
		return false;
	}
}

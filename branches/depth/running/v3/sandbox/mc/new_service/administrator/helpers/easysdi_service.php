<?php
/**
 * @version     3.0.0
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
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
		JHtmlSidebar::addEntry(
			JText::_('COM_EASYSDI_SERVICE_TITLE_SERVICES'),
			'index.php?option=com_easysdi_service&view=services',
			$vName == 'services'
		);
		JHtmlSidebar::addEntry(
			'Categories (Catid)',
			'index.php?option=com_categories&extension=com_easysdi_service.catid',
			$vName == 'categories.catid'
		);
		
if ($vName=='categories.catid') {			
JToolBarHelper::title('Easysdi Service: Categories (Catid)');		
}		JHtmlSidebar::addEntry(
			JText::_('COM_EASYSDI_SERVICE_TITLE_VIRTUALSERVICES'),
			'index.php?option=com_easysdi_service&view=virtualservices',
			$vName == 'virtualservices'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_EASYSDI_SERVICE_TITLE_PHYSICALSERVICES'),
			'index.php?option=com_easysdi_service&view=physicalservices',
			$vName == 'physicalservices'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_EASYSDI_SERVICE_TITLE_POLICIES'),
			'index.php?option=com_easysdi_service&view=policies',
			$vName == 'policies'
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

		$assetName = 'com_easysdi_service';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}
}

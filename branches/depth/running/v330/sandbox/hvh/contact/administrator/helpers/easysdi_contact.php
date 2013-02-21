<?php
/**
 * @version     3.3.0
 * @package     com_easysdi_contact
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Easysdi_contact helper.
 */
class Easysdi_contactHelper
{
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($vName = '')
	{

		JHtmlSidebar::addEntry(
				JText::_('COM_EASYSDI_CONTACT_TITLE_USERS'),
				'index.php?option=com_easysdi_contact&view=users',
				$vName == 'users'
		);
		
		JHtmlSidebar::addEntry(
				'Categories',
				"index.php?option=com_categories&extension=com_easysdi_contact",
				$vName == 'categories'
		);

		if ($vName=='categories') {
			JToolBarHelper::title(JText::sprintf('COM_CATEGORIES_CATEGORIES_TITLE', JText::_('com_easysdi_contact')));
		}
		
		JHtmlSidebar::addEntry(
				JText::_('COM_EASYSDI_CONTACT_TITLE_ORGANISMS'),
				'index.php?option=com_easysdi_contact&view=organisms',
				$vName == 'organisms'
		);

	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param	int		The category ID.
	 * @param	int		The article ID.
	 *
	 * @return	JObject
	 * @since	1.6
	 * 
	 * @todo : add organism
	 */
	public static function getActions($categoryId = null, $userId = null)
	{
		$user	= JFactory::getUser();
		$result	= new JObject;
	
		
		if (empty($userId) && empty($categoryId)) {
			$assetName = 'com_easysdi_contact';
		}
		elseif ( empty($userId)) {
			$assetName = 'com_easysdi_contact.category.'.(int) $categoryId;
		}
		else{
			$assetName = 'com_easysdi_contact.user.'.(int) $userId;
		}

		$actions = array(
				'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);
	
		foreach ($actions as $action) {
			$result->set($action,	$user->authorise($action, $assetName));
		}
	
		return $result;
	}
	

}

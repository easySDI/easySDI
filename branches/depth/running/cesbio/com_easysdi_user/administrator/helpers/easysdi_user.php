<?php
/**
 * @version     3.0.0
  * @package     com_easysdi_user
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Easysdi_core helper.
 */
class Easysdi_userHelper
{
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($vName = '')
	{

		JSubMenuHelper::addEntry(
			JText::_('COM_EASYSDI_CORE_SUBMENU_TITLE_USERS'),
			'index.php?option=com_easysdi_user&view=users',
			$vName == 'users'
		);
		
		JSubMenuHelper::addEntry(
				JText::_('COM_EASYSDI_CORE_SUBMENU_CATEGORIES'),
				'index.php?option=com_categories&extension=com_easysdi_user',
				$vName == 'categories'
		);
		
		if ($vName=='categories') {
			JToolBarHelper::title(
					JText::sprintf('COM_CATEGORIES_CATEGORIES_TITLE', JText::_('com_easysdi_user')),
					'easysdi_user-categories');
		}
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param	int		The category ID.
	 * @param	int		The article ID.
	 *
	 * @return	JObject
	 * @since	1.6
	 */
	public static function getActions($categoryId = null, $userId = null)
	{
		$user	= JFactory::getUser();
		$result	= new JObject;
	
		
		if (empty($userId) && empty($categoryId)) {
			$assetName = 'com_easysdi_user';
		}
		elseif ( empty($userId)) {
			$assetName = 'com_easysdi_user.category.'.(int) $categoryId;
		}
		else{
			$assetName = 'com_easysdi_user.user.'.(int) $userId;
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

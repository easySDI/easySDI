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
}

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
			JText::_('COM_EASYSDI_MAP_TITLE_LAYERGROUPS'),
			'index.php?option=com_easysdi_map&view=layergroups',
			$vName == 'layergroups'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_EASYSDI_MAP_TITLE_MAPLAYERS'),
			'index.php?option=com_easysdi_map&view=maplayers',
			$vName == 'maplayers'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_EASYSDI_MAP_TITLE_MAPCONTEXTS'),
			'index.php?option=com_easysdi_map&view=mapcontexts',
			$vName == 'mapcontexts'
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

		$assetName = 'com_easysdi_map';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}
}

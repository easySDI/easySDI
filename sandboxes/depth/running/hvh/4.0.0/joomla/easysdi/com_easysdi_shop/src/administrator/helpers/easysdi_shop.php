<?php
/**
 * @version     4.0.0
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Easysdi_shop helper.
 */
class Easysdi_shopHelper
{
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($vName = '')
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_EASYSDI_SHOP_TITLE_DIFFUSIONS'),
			'index.php?option=com_easysdi_shop&view=diffusions',
			$vName == 'diffusions'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_EASYSDI_SHOP_TITLE_PROPERTIES'),
			'index.php?option=com_easysdi_shop&view=properties',
			$vName == 'properties'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_EASYSDI_SHOP_TITLE_PROPERTYVALUES'),
			'index.php?option=com_easysdi_shop&view=propertyvalues',
			$vName == 'propertyvalues'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_EASYSDI_SHOP_TITLE_DIFFUSIONPERIMETERS'),
			'index.php?option=com_easysdi_shop&view=diffusionperimeters',
			$vName == 'diffusionperimeters'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_EASYSDI_SHOP_TITLE_DIFFUSIONNOTIFIEDUSERS'),
			'index.php?option=com_easysdi_shop&view=diffusionnotifiedusers',
			$vName == 'diffusionnotifiedusers'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_EASYSDI_SHOP_TITLE_PERIMETERS'),
			'index.php?option=com_easysdi_shop&view=perimeters',
			$vName == 'perimeters'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_EASYSDI_SHOP_TITLE_GRIDS'),
			'index.php?option=com_easysdi_shop&view=grids',
			$vName == 'grids'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_EASYSDI_SHOP_TITLE_ORDERS'),
			'index.php?option=com_easysdi_shop&view=orders',
			$vName == 'orders'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_EASYSDI_SHOP_TITLE_ORDERDIFFUSIONS'),
			'index.php?option=com_easysdi_shop&view=orderdiffusions',
			$vName == 'orderdiffusions'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_EASYSDI_SHOP_TITLE_ORDERPROPERTYVALUES'),
			'index.php?option=com_easysdi_shop&view=orderpropertyvalues',
			$vName == 'orderpropertyvalues'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_EASYSDI_SHOP_TITLE_ORDERPERIMETERS'),
			'index.php?option=com_easysdi_shop&view=orderperimeters',
			$vName == 'orderperimeters'
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

		$assetName = 'com_easysdi_shop';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}
}

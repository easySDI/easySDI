<?php
/**
 * @version     4.3.2
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013-2015. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
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
                '<i class="icon-home"></i> '.JText::_('COM_EASYSDI_SHOP_TITLE_HOME'),
                'index.php?option=com_easysdi_core&view=easysdi',
                $vName == 'easysdi'
            );
            if($vName == 'propertyvalues'){
                JHtmlSidebar::addEntry(
			JText::_('COM_EASYSDI_SHOP_TITLE_PROPERTIES'),
			'index.php?option=com_easysdi_shop&view=properties',
			$vName == 'properties'
		);

                return;
            };
            JHtmlSidebar::addEntry(
                    JText::_('COM_EASYSDI_SHOP_TITLE_PERIMETERS'),
                    'index.php?option=com_easysdi_shop&view=perimeters',
                    $vName == 'perimeters'
            );

            JHtmlSidebar::addEntry(
                    JText::_('COM_EASYSDI_SHOP_TITLE_PROPERTIES'),
                    'index.php?option=com_easysdi_shop&view=properties',
                    $vName == 'properties'
            );

            JHtmlSidebar::addEntry(
                    JText::_('COM_EASYSDI_SHOP_TITLE_ORDERS'),
                    'index.php?option=com_easysdi_shop&view=orders',
                    $vName == 'orders'
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


    /**
     * Creates a list of range options used in filter select list
     * used in com_users on users view
     *
     * @return  array
     *
     * @since   2.5
     */
    public static function getRangeOptions()
    {
        $options = array(
            JHtml::_('select.option', 'today', JText::_('COM_EASYSDI_SHOP_OPTION_RANGE_TODAY')),
            JHtml::_('select.option', 'past_week', JText::_('COM_EASYSDI_SHOP_OPTION_RANGE_PAST_WEEK')),
            JHtml::_('select.option', 'past_1month', JText::_('COM_EASYSDI_SHOP_OPTION_RANGE_PAST_1MONTH')),
            JHtml::_('select.option', 'past_3month', JText::_('COM_EASYSDI_SHOP_OPTION_RANGE_PAST_3MONTH')),
            JHtml::_('select.option', 'past_6month', JText::_('COM_EASYSDI_SHOP_OPTION_RANGE_PAST_6MONTH')),
            JHtml::_('select.option', 'past_year', JText::_('COM_EASYSDI_SHOP_OPTION_RANGE_PAST_YEAR')),
            JHtml::_('select.option', 'post_year', JText::_('COM_EASYSDI_SHOP_OPTION_RANGE_POST_YEAR')),
        );
        return $options;
    }
}

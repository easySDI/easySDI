<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

/**
 * Easysdi_catalog helper.
 */
class Easysdi_catalogHelper {

    /**
     * Configure the Linkbar.
     */
    public static function addSubmenu($vName = '', $itemName = '') {

        require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/helpers/easysdi_core.php';
        Easysdi_coreHelper::addComponentSubmeu('com_easysdi_core');
        Easysdi_coreHelper::addComponentSubmeu('com_easysdi_user');
        Easysdi_coreHelper::addComponentSubmeu('com_easysdi_catalog');
        JHtmlSidebar::addEntry(
                Easysdi_coreHelper::getMenuSpacer() . JText::_('COM_EASYSDI_CATALOG_TITLE_CATALOGS'), 'index.php?option=com_easysdi_catalog&view=catalogs', $vName == 'catalogs'
        );
        if ($vName == 'searchcriterias') {
            JHtmlSidebar::addEntry(
                    //Easysdi_coreHelper::getMenuSpacer() . Easysdi_coreHelper::getMenuSpacer() . JText::_('COM_EASYSDI_CATALOG_TITLE_CATALOGS'), 'index.php?option=com_easysdi_catalog&view=catalogs', $vName == 'catalogs'
                    Easysdi_coreHelper::getMenuSpacer(3) . $itemName, '#', $vName == 'searchcriterias'
            );
        }
        JHtmlSidebar::addEntry(
                Easysdi_coreHelper::getMenuSpacer() . JText::_('COM_EASYSDI_CATALOG_TITLE_RESOURCESTYPE'), 'index.php?option=com_easysdi_catalog&view=resourcestype', $vName == 'resourcestype'
        );
        JHtmlSidebar::addEntry(
                Easysdi_coreHelper::getMenuSpacer() . JText::_('COM_EASYSDI_CATALOG_TITLE_RESOURCETYPELINKS'), 'index.php?option=com_easysdi_catalog&view=resourcetypelinks', $vName == 'resourcetypelinks'
        );
        JHtmlSidebar::addEntry(
                Easysdi_coreHelper::getMenuSpacer() . JText::_('COM_EASYSDI_CATALOG_TITLE_CLASSES'), 'index.php?option=com_easysdi_catalog&view=classes', $vName == 'classes'
        );
        JHtmlSidebar::addEntry(
                Easysdi_coreHelper::getMenuSpacer() . JText::_('COM_EASYSDI_CATALOG_TITLE_ATTRIBUTES'), 'index.php?option=com_easysdi_catalog&view=attributes', $vName == 'attributes'
        );
        if ($vName == 'attributevalues') {
            JHtmlSidebar::addEntry(
                    #Easysdi_coreHelper::getMenuSpacer() . Easysdi_coreHelper::getMenuSpacer() . JText::_('COM_EASYSDI_CATALOG_TITLE_ATTRIBUTES'), 'index.php?option=com_easysdi_catalog&view=attributevalues', $vName == 'attributevalues'
                    Easysdi_coreHelper::getMenuSpacer(3) . $itemName, '#', $vName == 'attributevalues'
            );
        };
        JHtmlSidebar::addEntry(
                Easysdi_coreHelper::getMenuSpacer() . JText::_('COM_EASYSDI_CATALOG_TITLE_RELATIONS'), 'index.php?option=com_easysdi_catalog&view=relations', $vName == 'relations'
        );
        JHtmlSidebar::addEntry(
                Easysdi_coreHelper::getMenuSpacer() . JText::_('COM_EASYSDI_CATALOG_TITLE_PROFILES'), 'index.php?option=com_easysdi_catalog&view=profiles', $vName == 'profiles'
        );
        JHtmlSidebar::addEntry(
                Easysdi_coreHelper::getMenuSpacer() . JText::_('COM_EASYSDI_CATALOG_TITLE_IMPORTREFS'), 'index.php?option=com_easysdi_catalog&view=importrefs', $vName == 'importrefs'
        );
        JHtmlSidebar::addEntry(
                Easysdi_coreHelper::getMenuSpacer() . JText::_('COM_EASYSDI_CATALOG_TITLE_NAMESPACES'), "index.php?option=com_easysdi_catalog&view=namespaces", $vName == 'namespaces'
        );
        JHtmlSidebar::addEntry(
                Easysdi_coreHelper::getMenuSpacer() . JText::_('COM_EASYSDI_CATALOG_TITLE_BOUNDARIESCATEGORY'), 'index.php?option=com_easysdi_catalog&view=boundariescategory', $vName == 'boundariescategory'
        );
        JHtmlSidebar::addEntry(
                Easysdi_coreHelper::getMenuSpacer() . JText::_('COM_EASYSDI_CATALOG_TITLE_BOUNDARIES'), 'index.php?option=com_easysdi_catalog&view=boundaries', $vName == 'boundaries'
        );
        Easysdi_coreHelper::addComponentSubmeu('com_easysdi_shop');
        Easysdi_coreHelper::addComponentSubmeu('com_easysdi_processing');
        Easysdi_coreHelper::addComponentSubmeu('com_easysdi_service');
        Easysdi_coreHelper::addComponentSubmeu('com_easysdi_map');
        Easysdi_coreHelper::addComponentSubmeu('com_easysdi_monitor');
        Easysdi_coreHelper::addComponentSubmeu('com_easysdi_dashboard');
    }

    /**
     * Gets a list of the actions that can be performed.
     *
     * @return	JObject
     * @since	1.6
     */
    public static function getActions() {
        $user = JFactory::getUser();
        $result = new JObject;

        $assetName = 'com_easysdi_catalog';

        $actions = array(
            'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
        );

        foreach ($actions as $action) {
            $result->set($action, $user->authorise($action, $assetName));
        }

        return $result;
    }

}

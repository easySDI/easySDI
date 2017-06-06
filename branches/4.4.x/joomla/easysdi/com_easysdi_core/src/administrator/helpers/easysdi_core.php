<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

/**
 * Easysdi_core helper.
 */
class Easysdi_coreHelper {

    /**
     * Configure the Linkbar.
     */
    public static function addSubmenu($vName = '') {
        self::addComponentSubmeu('com_easysdi_core', true);
        self::addComponentSubmeu('com_easysdi_user');
        self::addComponentSubmeu('com_easysdi_catalog');
        self::addComponentSubmeu('com_easysdi_shop');
        self::addComponentSubmeu('com_easysdi_processing');
        self::addComponentSubmeu('com_easysdi_service');
        self::addComponentSubmeu('com_easysdi_map');
        self::addComponentSubmeu('com_easysdi_monitor');
        self::addComponentSubmeu('com_easysdi_dashboard');
    }
    
    public static function getMenuSpacer($level = 2) {
        return '<span class="sdi-submenu-backend-spacer" style="display:inline-block;width:'. (10*$level) .'px;"> </span> ';
    }

    public static function addComponentSubmeu($componentName, $active = false) {

        //To check if others easysdi components are installed
        $app = JFactory::getApplication();
        // Get the user object to verify permissions
        $user = JFactory::getUser();
        // Load language for external compnents using this helper 
        $lang = JFactory::getLanguage();
        $lang->load('com_easysdi_core', JPATH_ADMINISTRATOR);

        switch ($componentName) {
            case 'com_easysdi_core':
                JHtmlSidebar::addEntry(
                        '<i class="icon-home"></i> ' . JText::_('COM_EASYSDI_CORE_ICON_SDI_HOME'), 'index.php?option=com_easysdi_core&view=easysdi', $active
                );
                break;
            case 'com_easysdi_user':
                if ($user->authorise('core.manage', 'com_easysdi_contact')) {
                    JHtmlSidebar::addEntry(
                            '<i class="icon-user"></i> ' . JText::_('COM_EASYSDI_CORE_ICON_SDI_CONTACT'), 'index.php?option=com_easysdi_contact', $active
                    );
                }
                break;

            case 'com_easysdi_catalog':
                if ($user->authorise('core.manage', 'com_easysdi_catalog')) {
                    JHtmlSidebar::addEntry(
                            '<i class="icon-grid-view"></i> ' . JText::_('COM_EASYSDI_CORE_ICON_SDI_CATALOG'), 'index.php?option=com_easysdi_catalog'
                    );
                }
                break;
                
            case 'com_easysdi_processing':
                if ($user->authorise('core.manage', 'com_easysdi_processing')) {
                    JHtmlSidebar::addEntry(
                            '<i class="icon-cogs"></i> ' . JText::_('COM_EASYSDI_CORE_ICON_SDI_PROCESSING'), 'index.php?option=com_easysdi_processing'
                    );
                }
                break;

            case 'com_easysdi_shop':
                if ($user->authorise('core.manage', 'com_easysdi_shop')) {
                    JHtmlSidebar::addEntry(
                            '<i class="icon-basket"></i> ' . JText::_('COM_EASYSDI_CORE_ICON_SDI_SHOP'), 'index.php?option=com_easysdi_shop'
                    );
                }
                break;

            case 'com_easysdi_service':
                if ($user->authorise('core.manage', 'com_easysdi_service')) {
                    JHtmlSidebar::addEntry(
                            '<i class="icon-wrench"></i> ' . JText::_('COM_EASYSDI_CORE_ICON_SDI_SERVICE'), 'index.php?option=com_easysdi_service'
                    );
                }
                break;

            case 'com_easysdi_map':
                if ($user->authorise('core.manage', 'com_easysdi_map')) {
                    JHtmlSidebar::addEntry(
                            '<i class="icon-location"></i> ' . JText::_('COM_EASYSDI_CORE_ICON_SDI_MAP'), 'index.php?option=com_easysdi_map'
                    );
                }
                break;

            case 'com_easysdi_monitor':
                if ($user->authorise('core.manage', 'com_easysdi_monitor')) {
                    JHtmlSidebar::addEntry(
                            '<i class="icon-health"></i> ' . JText::_('COM_EASYSDI_CORE_ICON_SDI_MONITOR'), 'index.php?option=com_easysdi_monitor'
                    );
                }
                break;

            case 'com_easysdi_dashboard':
                if ($user->authorise('core.manage', 'com_easysdi_dashboard')) {
                    JHtmlSidebar::addEntry(
                            '<i class="icon-dashboard"></i> ' . JText::_('COM_EASYSDI_CORE_ICON_SDI_DASHBOARD'), 'index.php?option=com_easysdi_dashboard'
                    );
                }
                break;
        }
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

        $assetName = 'com_easysdi_core';

        $actions = array(
            'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
        );

        foreach ($actions as $action) {
            $result->set($action, $user->authorise($action, $assetName));
        }

        return $result;
    }

    public static function uuid() {
        return sprintf('%04x%04x-%04x-%03x4-%04x-%04x%04x%04x', mt_rand(0, 65535), mt_rand(0, 65535), // 32 bits for "time_low"
                mt_rand(0, 65535), // 16 bits for "time_mid"
                mt_rand(0, 4095), // 12 bits before the 0100 of (version) 4 for "time_hi_and_version"
                bindec(substr_replace(sprintf('%016b', mt_rand(0, 65535)), '01', 6, 2)),
                // 8 bits, the last two of which (positions 6 and 7) are 01, for "clk_seq_hi_res"
                // (hence, the 2nd hex digit after the 3rd hyphen can only be 1, 5, 9 or d)
                // 8 bits for "clk_seq_low"
                mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535) // 48 bits for "node"
        );
    }
    
    public static function pwd($nb_char) {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < $nb_char - 1 ; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    /**
     * Convert an array to an url
     */
    public static function array2URL($elements) {

        $params = array();
        foreach ($elements as $key => $value) {
            if ($key == 'root') {
                $root = $value;
            } else {
                if (!empty($value)) {
                    $params[] = $key . '=' . $value;
                }
            }
        }

        if (empty($root)) {
            return false;
        } else {
            return $root . '?' . implode('&', $params);
        }
    }

    /**
     * Check recursively if version has viral versionning child
     * 
     * @param stdClass $version
     * @return array
     * @deprecated since version 4.2.0 - replaced by getChildrenVersion
     */
    public function getViralVersionnedChild($version) {
        return $this->getChildrenVersion($version, true);
    }

    /**
     * getChildrenVersion - retrieves the children metadata's version of the given metadata's version
     * 
     * @param int $version
     * @param bool $viralVersioning - limit or not to the viral versionned child
     * @return array
     */
    public function getChildrenVersion($version, $viralVersioning = false, $unpublished = false) {
        $all_versions = array();
        $all_versions[$version->id] = $version;

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('cv.id, cv.name AS version_name, cr.resourcetype_id, cr.name AS resource_name, cr.id AS resource_id, cm.guid AS fileidentifier, cm.id AS metadata_id, cm.metadatastate_id, rtl.viralversioning');
        $query->from('#__sdi_versionlink vl');
        $query->innerJoin('#__sdi_version pv ON vl.parent_id = pv.id');
        $query->innerJoin('#__sdi_resource pr ON pv.resource_id = pr.id');
        $query->innerJoin('#__sdi_resourcetypelink rtl ON pr.resourcetype_id = rtl.parent_id');
        $query->innerJoin('#__sdi_version cv ON vl.child_id = cv.id');
        $query->innerJoin('#__sdi_metadata cm ON cm.version_id = cv.id');
        $query->innerJoin('#__sdi_resource cr ON cv.resource_id = cr.id AND cr.resourcetype_id = rtl.child_id');
        if ($viralVersioning) {
            $query->where('rtl.viralversioning = 1');
        }
        if ($unpublished) {
            $query->where('cm.metadatastate_id NOT IN (3, 4)'); // @TODO: should be replaced by sdiMetadata::PUBLISHED/ARCHIVED
        }
        $query->where('vl.parent_id = ' . (int) $version->id);

        $db->setQuery($query);
        $childs = $db->loadObjectList('id');

        if (count($childs) > 0) {
            $version->children = $childs;
            //$all_versions[$version->id]->children = $childs;

            foreach ($childs as $key => $child) {
                $this->getChildrenVersion($child, $viralVersioning, $unpublished);
            }
        } else
            $version->children = array();

        return $all_versions;
    }

    public function getOrganisms($onlyOrganismsWithResources = false) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select($db->qn(array('o.id', 'o.name')))
                ->from($db->qn('#__sdi_organism', 'o'))
                ->order($db->qn('o.name'));
        if ($onlyOrganismsWithResources) {
            $query->innerJoin($db->qn('#__sdi_resource', 'r') . ' ON (' . $db->qn('r.organism_id') . ' = ' . $db->qn('o.id') . ')');
        }
        $query->group($db->qn(array('o.id', 'o.name')));
        $db->setQuery($query);
        $organisms = $db->loadObjectList();

        array_unshift($organisms, (object) array('id' => '', 'name' => ''));

        return $organisms;
    }

}

<?php

/**
 * @version     4.4.5
 * @package     mod_easysdi_adminbutton
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Script file of JFUploader plugin
 */
class mod_easysdi_adminbuttonInstallerScript {

    function update($parent) {
        //Do nothing
    }

    function install($parent) {

        $db = JFactory::getDbo();

        // get the module ID
        $query = $db->getQuery(true);
        $query->select('id');
        $query->from('#__modules m');
        $query->where('m.module = ' . $db->quote('mod_easysdi_adminbutton'));
        $db->setQuery($query, 0, 1);
        $moduleId = $db->loadResult();
        $db->setQuery($query);
        $moduleId = $db->loadResult();

        // activate the module
        $query = $db->getQuery(true);
        $query->update('#__modules');
        $query->set('ordering = 1');
        $query->set('position = '.$db->quote('easysdi_adm_home_left'));
        $query->set('published = 1');
        $query->where('id='.$moduleId);
        $db->setQuery($query);
        $db->query();

        // Add module to menu 0 (all)
        $module_menu = new stdClass();
        $module_menu->moduleid = $moduleId;
        $module_menu->menuid = 0;
        
        $db->insertObject('#__modules_menu',$module_menu);
    }

    function postflight($type, $parent) {
        
    }

}

?>
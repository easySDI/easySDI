<?php

/**
 * @version     4.0.0
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class com_easysdi_catalogInstallerScript {
    /*
     * $parent is the class calling this method.
     * $type is the type of change (install, update or discover_install, not uninstall).
     * preflight runs before anything else and while the extracted files are in the uploaded temp folder.
     * If preflight returns false, Joomla will abort the update and undo everything already done.
     */

    function preflight($type, $parent) {
        // Installing component manifest file version
        $this->release = $parent->get("manifest")->version;

        // Show the essential information at the install/update back-end
        echo '<p>EasySDI component Catalog [com_easysdi_catalog]';
        echo '<br />' . JText::_('COM_EASYSDI_CATALOG_INSTALL_SCRIPT_MANIFEST_VERSION') . $this->release;
    }

    /*
     * $parent is the class calling this method.
     * install runs after the database scripts are executed.
     * If the extension is new, the install method is run.
     * If install returns false, Joomla will abort the install and undo everything already done.
     */

    function install($parent) {
        // You can have the backend jump directly to the newly installed component configuration page
        // $parent->getParent()->setRedirectURL('index.php?option=com_democompupdate');
    }

    /*
     * $parent is the class calling this method.
     * update runs after the database scripts are executed.
     * If the extension exists, then the update method is run.
     * If this returns false, Joomla will abort the update and undo everything already done.
     */

    function update($parent) {
        // You can have the backend jump directly to the newly updated component configuration page
        // $parent->getParent()->setRedirectURL('index.php?option=com_democompupdate');
    }

    /*
     * $parent is the class calling this method.
     * $type is the type of change (install, update or discover_install, not uninstall).
     * postflight is run after the extension is registered in the database.
     */

    function postflight($type, $parent) {
        if ($type == 'install') {
            
            JTable::addIncludePath(JPATH_ADMINISTRATOR."/components/com_easysdi_catalog/tables");
            //Create system namespace
            $gmd = JTable::getInstance('namespace', 'easysdi_catalogTable');
            $gmd->alias = 'gmd';
            $gmd->state = 1;
            $gmd->name = 'gmd';
            $gmd->prefix = 'gmd';
            $gmd->uri = 'http://www.isotc211.org/2005/gmd';
            $gmd->system = 1;

            if (!$gmd->store(true)) {
                JError::raiseWarning(null, JText::_('COM_EASYSDI_CATALOG_POSTFLIGHT_SCRIPT_NAMESPACE_ERROR'));
                return false;
            }
            
            $gco = JTable::getInstance('namespace', 'easysdi_catalogTable');
            $gco->alias = 'gco';
            $gco->state = 1;
            $gco->name = 'gco';
            $gco->prefix = 'gco';
            $gco->uri = 'http://www.isotc211.org/2005/gco';
            $gco->system = 1;

            if (!$gco->store(true)) {
                JError::raiseWarning(null, JText::_('COM_EASYSDI_CATALOG_POSTFLIGHT_SCRIPT_NAMESPACE_ERROR'));
                return false;
            }
            
            $gml = JTable::getInstance('namespace', 'easysdi_catalogTable');
            $gml->alias = 'gml';
            $gml->state = 1;
            $gml->name = 'gml';
            $gml->prefix = 'gml';
            $gml->uri = 'http://www.opengis.net/gml';
            $gml->system = 1;

            if (!$gml->store(true)) {
                JError::raiseWarning(null, JText::_('COM_EASYSDI_CATALOG_POSTFLIGHT_SCRIPT_NAMESPACE_ERROR'));
                return false;
            }
            
            $sdi = JTable::getInstance('namespace', 'easysdi_catalogTable');
            $sdi->alias = 'sdi';
            $sdi->state = 1;
            $sdi->name = 'sdi';
            $sdi->prefix = 'sdi';
            $sdi->uri = 'http://www.easysdi.org/2011/sdi';
            $sdi->system = 1;

            if (!$sdi->store(true)) {
                JError::raiseWarning(null, JText::_('COM_EASYSDI_CATALOG_POSTFLIGHT_SCRIPT_NAMESPACE_ERROR'));
                return false;
            }
        }
        
        $db = JFactory::getDbo();
        $db->setQuery("DELETE FROM `#__menu` WHERE title = 'com_easysdi_catalog'");
	$db->query();
    }

    /*
     * $parent is the class calling this method
     * uninstall runs before any other action is taken (file removal or database processing).
     */

    function uninstall($parent) {
        
    }

    /*
     * get a variable from the manifest file (actually, from the manifest cache).
     */

    function getParam($name) {
        $db = JFactory::getDbo();
        $db->setQuery('SELECT manifest_cache FROM #__extensions WHERE name = "com_easysdi_catalog"');
        $manifest = json_decode($db->loadResult(), true);
        return $manifest[$name];
    }

    /*
     * sets parameter values in the component's row of the extension table
     */

    function setParams($param_array) {
        if (count($param_array) > 0) {
            // read the existing component value(s)
            $db = JFactory::getDbo();
            $db->setQuery('SELECT params FROM #__extensions WHERE name = "com_easysdi_catalog"');
            $params = json_decode($db->loadResult(), true);
            // add the new variable(s) to the existing one(s)
            foreach ($param_array as $name => $value) {
                $params[(string) $name] = (string) $value;
            }
            // store the combined new and existing values back as a JSON string
            $paramsString = json_encode($params);
            $db->setQuery('UPDATE #__extensions SET params = ' .
                    $db->quote($paramsString) .
                    ' WHERE name = "com_easysdi_catalog"');
            $db->query();
        }
    }

}

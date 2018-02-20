<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
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
            JTable::addIncludePath(JPATH_ADMINISTRATOR . "/components/com_easysdi_catalog/tables");
            //Create system search criteria
            $sc = JTable::getInstance('searchcriteria', 'easysdi_catalogTable');
            $sc->alias = 'isViewable';
            $sc->state = 1;
            $sc->name = 'isViewable';
            $sc->issystem = 1;
            $sc->criteriatype_id = 1;
            $sc->rendertype_id = 1;
            $sc->access = 1;

            if (!$sc->store(true)) {
                JError::raiseWarning(null, JText::_('COM_EASYSDI_CATALOG_POSTFLIGHT_SCRIPT_SEARCHCRITERIA_ERROR'));
                return false;
            }

            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('id');
            $query->from('#__sdi_catalog');
            $db->setQuery($query);
            $catalogs = $db->loadColumn();
            foreach ($catalogs as $catalog):
                $catalogsearchcriteria = JTable::getInstance('catalogsearchcriteria', 'Easysdi_catalogTable');
                $array = array();
                $array['catalog_id'] = $catalog;
                $array['searchcriteria_id'] = $sc->id;
                $array['searchtab_id'] = 4;
                $array['state'] = 1;
                $array['ordering'] = $sc->id;
                $catalogsearchcriteria->save($array);
            endforeach;
        }

        // set default params (only if unset)
        if (!$this->getConfVal('iframewidth')) {
            $params = array('iframewidth' => 400);
            $this->setParams($params);
        }
        if (!$this->getConfVal('iframeheight')) {
            $params = array('iframeheight' => 600);
            $this->setParams($params);
        }


        // Apply pagination patch
        $jversion = new JVersion();
        if ($jversion->getShortVersion() == '3.3.6') {
            copy(JPATH_ROOT . '/administrator/components/com_easysdi_catalog/assets/patch/libraries/cms/router/site.php', JPATH_ROOT . '/libraries/cms/router/site.php');
        }

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->delete('#__menu');
        $query->where('title =' . $db->quote('com_easysdi_catalog'));

        $db->setQuery($query);
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
        $query = $db->getQuery(true);
        $query->select('manifest_cache');
        $query->from('#__extensions');
        $query->where('name =' . $db->quote('com_easysdi_catalog'));
        $db->setQuery($query);
        $manifest = json_decode($db->loadResult(), true);
        return $manifest[$name];
    }
    
    /*
     * get a parameter from existing component config.
     */

    function getConfVal($name) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('params');
        $query->from('#__extensions');
        $query->where('name =' . $db->quote('com_easysdi_catalog'));
        $db->setQuery($query);
        $params = json_decode($db->loadResult(), true);
        return isset($params[$name])?$params[$name]:null;
    }    

    /*
     * sets parameter values in the component's row of the extension table
     */

    function setParams($param_array) {
        if (count($param_array) > 0) {
            // read the existing component value(s)
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('params');
            $query->from('#__extensions');
            $query->where('name =' . $db->quote('com_easysdi_catalog'));
            $db->setQuery($query);
            $params = json_decode($db->loadResult(), true);
            // add the new variable(s) to the existing one(s)
            foreach ($param_array as $name => $value) {
                $params[(string) $name] = (string) $value;
            }
            // store the combined new and existing values back as a JSON string
            $paramsString = json_encode($params);
            $query = $db->getQuery(true);
            $query->update('#__extensions');
            $query->set('params = ' . $db->quote($paramsString));
            $query->where('name = ' . $db->quote('com_easysdi_catalog'));

            $db->setQuery($query);
            $db->query();
        }
    }

}

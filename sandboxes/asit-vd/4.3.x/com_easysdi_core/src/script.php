<?php

/**
 * @version     4.3.2
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class com_easysdi_coreInstallerScript {
    /*
     * $parent is the class calling this method.
     * $type is the type of change (install, update or discover_install, not uninstall).
     * preflight runs before anything else and while the extracted files are in the uploaded temp folder.
     * If preflight returns false, Joomla will abort the update and undo everything already done.
     */

    function preflight($type, $parent) {
                
        // Create stored procedure drop_foreign_key 
        $db = JFactory::getDbo();
        if(($db->name == 'mysqli') || ($db->name == 'mysql')){
            $sqls = array();
            $sqls[] = "DROP PROCEDURE IF EXISTS drop_foreign_key;";
            $sqls[] = "CREATE PROCEDURE drop_foreign_key(IN tableName VARCHAR(64), IN constraintName VARCHAR(64))
                BEGIN
                    IF EXISTS(
                        SELECT * FROM information_schema.table_constraints
                        WHERE 
                            table_schema    = DATABASE()     AND
                            table_name      = CONCAT('".$db->getPrefix()."',tableName) AND
                            constraint_name = CONCAT('".$db->getPrefix()."',constraintName) AND
                            constraint_type = 'FOREIGN KEY')
                    THEN
                        SET @query = CONCAT('ALTER TABLE ','".$db->getPrefix()."',tableName, ' DROP FOREIGN KEY ','".$db->getPrefix()."',constraintName, ';');
                        PREPARE stmt FROM @query; 
                        EXECUTE stmt; 
                        DEALLOCATE PREPARE stmt; 
                    END IF; 
                END";
            
            foreach ($sqls as $sql) {
                $query = $db->getQuery(true);
                $db->setQuery($sql);
                $db->execute();
            }
            
            $sqls = array();
            $sqls[] = "DROP PROCEDURE IF EXISTS drop_column;";
            $sqls[] = "CREATE PROCEDURE drop_column(IN tableName VARCHAR(64), IN columnName VARCHAR(64))
                BEGIN
                    IF EXISTS(
                        SELECT * FROM information_schema.COLUMNS
                        WHERE 
                            table_schema    = DATABASE()     AND
                            table_name      = CONCAT('".$db->getPrefix()."',tableName) AND
                            column_name = columnName)
                    THEN
                        SET @query = CONCAT('ALTER TABLE ','".$db->getPrefix()."',tableName, ' DROP COLUMN ',columnName, ';');
                        PREPARE stmt FROM @query; 
                        EXECUTE stmt; 
                        DEALLOCATE PREPARE stmt; 
                    END IF; 
                END";
            
            foreach ($sqls as $sql) {
                $query = $db->getQuery(true);
                $db->setQuery($sql);
                $db->execute();
            }
        }
        
        // Show the essential information at the install/update back-end
        //echo '<p>EasySDI component Core [com_easysdi_core]';
        //echo '<br />' . JText::_('COM_EASYSDI_CORE_INSTALL_SCRIPT_MANIFEST_VERSION') . $this->release;
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
            require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/helpers/easysdi_core.php';
            $params['infrastructureID'] = Easysdi_coreHelper::uuid();
            $this->setParams($params);
        }

        if ($type == 'install') {
            // import joomla's filesystem classes
            jimport('joomla.filesystem.folder');

            // Create folders inside media folder
            if (!JFolder::create(JPATH_ROOT . '/media/easysdi', "0755")) {
                echo "Unable to create media EasySDI folder";
            }
            if (!JFolder::create(JPATH_ROOT . '/media/easysdi/catalog',"0755")) {
                echo "Unable to create media Catalog folder";
            }
            if (!JFolder::create(JPATH_ROOT . '/media/easysdi/catalog/xsl', "0755")) {
                echo "Unable to create media XSL folder";
            }
        }
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
        $query->where('name = '.$db->quote('com_easysdi_core'));
        $db->setQuery($query);
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
            $query = $db->getQuery(true);
            $query->select('manifest_cache');
            $query->from('#__extensions');
            $query->where('name = '.$db->quote('com_easysdi_core'));
            $db->setQuery($query);
            $params = json_decode($db->loadResult(), true);
            // add the new variable(s) to the existing one(s)
            foreach ($param_array as $name => $value) {
                $params[(string) $name] = (string) $value;
            }
            // store the combined new and existing values back as a JSON string
            $paramsString = json_encode($params);
            $query = $db->getQuery(true);
            $query->select('params');
            $query->from('#__extensions');
            $query->where('name = '.$db->quote('com_easysdi_core'));
            $db->setQuery($query);
            $db->query();
        }
    }

}

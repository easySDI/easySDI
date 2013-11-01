<?php
/**
 * @version     4.0.0
 * @package     mod_easysdi_adminbutton
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org§> - http://www.easysdi.org
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Script file of JFUploader plugin
 */
class mod_easysdi_adminbuttonInstallerScript
{ 

 function update($parent) { 
     //Do nothing
 }
 
  function install($parent) { 

    $db = JFactory::getDbo();
     
    // get the module ID
    $db->setQuery("SELECT 
                  id 
                      FROM #__modules m
                  WHERE
                      m.module = 'mod_easysdi_adminbutton'
                  LIMIT 1");
     $moduleId = $db->loadResult();
     
     // activate the module
     $db->setQuery("UPDATE 
                        #__modules 
                   SET 
                        ordering = 1 , 
                        position='easysdi_adm_home_left', 
                        published=1
                   WHERE 
                        id=$moduleId");
     $db->query();

     // Add module to menu 0 (all)
     $db->setQuery("INSERT INTO 
                        `#__modules_menu` 
                        (`moduleid`, `menuid`)
                    VALUES 
                        ($moduleId, 0)");
     $db->query();
      
  } 
  function postflight( $type, $parent ) {

	}
}
?>
<?php
/**
 * @version     4.0.0
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class plgContentEasysdiserviceInstallerScript
{

	function preflight( $type, $parent ) {
		if($type == 'install'){
			//Check if com_easysdi_core is installed
			$db = JFactory::getDbo();
			$db->setQuery('SELECT COUNT(*) FROM #__extensions WHERE name = "com_easysdi_core"');
			$install = $db->loadResult();
			if($install == 0){
				JError::raiseWarning(null, JText::_('PLG_CONTENT_EASYSDI_INSTALL_SCRIPT_CORE_ERROR'));
				return false;
			}
			
			// Installing component manifest file version
			$this->release = $parent->get( "manifest" )->version;
			// Show the essential information at the install/update back-end
			echo '<p>EasySDI plugin Content [plg_content_easysdi]';
			echo '<br />'.JText::_('Installing plugin manifest file version = ') . $this->release;
		}
	}
 
	function install( $parent ) {
	}
 
	function update( $parent ) {
	}
 
	function postflight( $type, $parent ) {
		if($type == 'install'){
			//Activate the plugin
			$db = JFactory::getDbo();
			$db->setQuery("UPDATE #__extensions SET enabled=1 WHERE type='plugin' AND element='easysdiservice' AND folder='content'");
			$db->execute();
		}
	}

	function uninstall( $parent ) {
	}
 
}

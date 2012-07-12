<?php
/**
 * @version     3.0.0
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
class plg_easysdiInstallerScript
{

	function preflight( $type, $parent ) {
		//Check if com_easysdi_core is installed
		$db = JFactory::getDbo();
		$db->setQuery('SELECT COUNT(*) FROM #__extensions WHERE name = "com_easysdi_core"');
		$install = $db->loadResult();
		if($install == 0){
			JError::raiseWarning(null, JText::_('COM_EASYSDI_SERVICE_INSTALL_SCRIPT_CORE_ERROR'));
			return false;
		}
		
		// Show the essential information at the install/update back-end
		echo '<p>EasySDI plugin User [plg_easysdi]';
		echo '<br />'.JText::_('COM_EASYSDI_SERVICE_INSTALL_SCRIPT_MANIFEST_VERSION') . $this->release;
	}
 
	function install( $parent ) {
	}
 
	function update( $parent ) {
	}
 
	function postflight( $type, $parent ) {
	}

	function uninstall( $parent ) {
	}
 
}

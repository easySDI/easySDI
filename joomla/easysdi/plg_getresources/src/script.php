<?php
/**
 * @version     4.0.0
 * @package     plg_getresources
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class plgEasysdi_admin_infoGetresourcesInstallerScript
{

	function preflight( $type, $parent ) {
	}
 
	function install( $parent ) {
	}
 
	function update( $parent ) {
	}
 
	function postflight( $type, $parent ) {
		if($type == 'install'){
			//Activate the plugin
			$db = JFactory::getDbo();
			$db->setQuery("UPDATE #__extensions SET enabled=1 WHERE type='plugin' AND element='getresources' AND folder='easysdi_admin_info'");
			$db->execute();
		}
	}

	function uninstall( $parent ) {
	}
 
}

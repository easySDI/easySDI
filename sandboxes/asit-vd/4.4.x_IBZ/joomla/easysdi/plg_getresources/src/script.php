<?php
/**
 * @version     4.4.5
 * @package     plg_easysdi_getresources
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
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
                        $query = $db->getQuery(true);
                        $query->update('#__extensions');
                        $query->set('enabled=1');
                        $query->where('type='.$db->quote('plugin'));
                        $query->where('element='.$db->quote('getresources'));
                        $query->where('folder='.$db->quote('easysdi_admin_info'));
			$db->setQuery($query);
			$db->execute();
		}
	}

	function uninstall( $parent ) {
	}
 
}

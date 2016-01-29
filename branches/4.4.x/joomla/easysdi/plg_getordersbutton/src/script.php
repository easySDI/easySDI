<?php
/**
 * @version		4.4.0
 * @package     plg_easysdi_getordersbutton
 * @copyright	
 * @license		
 * @author		
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class plgEasysdi_admin_buttonGetordersbuttonInstallerScript
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
                        $query->where('element='.$db->quote('getordersbutton'));
                        $query->where('folder='.$db->quote('easysdi_admin_info'));
			$db->setQuery($query);
			$db->execute();
		}
	}

	function uninstall( $parent ) {
	}
 
}

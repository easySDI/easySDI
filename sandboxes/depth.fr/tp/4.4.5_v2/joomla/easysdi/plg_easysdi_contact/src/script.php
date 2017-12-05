<?php
/**
 * @version     4.4.5
 * @package     plg_easysdi_user
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class plgUserEasysdicontactInstallerScript
{

	function preflight( $type, $parent ) {
		if($type == 'install'){
			//Check if com_easysdi_core is installed
			$db = JFactory::getDbo();
                        $query = $db->getQuery(true);
                        $query->select('COUNT(*)');
                        $query->from('#__extensions');
                        $query->where('name ='.$db->quote('com_easysdi_core'));
			$db->setQuery($query);
			$install = $db->loadResult();
			if($install == 0){
				JError::raiseWarning(null, JText::_('PLG_EASYSDIUSER_INSTALL_SCRIPT_CORE_ERROR'));
				return false;
			}
			
			// Installing component manifest file version
			$this->release = $parent->get( "manifest" )->version;
			// Show the essential information at the install/update back-end
			echo '<p>EasySDI plugin User [plg_user_easysdi]';
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
                        $query = $db->getQuery(true);
                        $query->update('#__extensions');
                        $query->set('enabled=1');
                        $query->where('type='.$db->quote('plugin'));
                        $query->where('element='.$db->quote('easysdicontact'));
                        $query->where('folder='.$db->quote('user'));
			$db->setQuery($query);
			$db->execute();
		}
	}

	function uninstall( $parent ) {
	}
 
}

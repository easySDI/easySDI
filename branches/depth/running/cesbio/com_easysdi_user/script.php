<?php
/**
 * @version     3.0.0
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
class com_easysdi_coreInstallerScript
{
	/*
	 * $parent is the class calling this method.
	 * $type is the type of change (install, update or discover_install, not uninstall).
	 * preflight runs before anything else and while the extracted files are in the uploaded temp folder.
	 * If preflight returns false, Joomla will abort the update and undo everything already done.
	 */
	function preflight( $type, $parent ) {
		// Installing component manifest file version
		$this->release = $parent->get( "manifest" )->version;
		
		// Show the essential information at the install/update back-end
		echo '<p>EasySDI Core [com_easysdi_core]';
		echo '<br />'.JText::_('COM_EASYSDI_CORE_INSTALL_SCRIPT_MANIFEST_VERSION') . $this->release;
		
// 		echo '<p>' . JText::_('COM_EASYSDI_CORE_PREFLIGHT_SCRIPT' ) . '</p>';
	}
 
	/*
	 * $parent is the class calling this method.
	 * install runs after the database scripts are executed.
	 * If the extension is new, the install method is run.
	 * If install returns false, Joomla will abort the install and undo everything already done.
	 */
	function install( $parent ) {
// 		echo '<p>' . JText::_('COM_EASYSDI_CORE_INSTALL_SCRIPT') . '</p>';
		// You can have the backend jump directly to the newly installed component configuration page
		// $parent->getParent()->setRedirectURL('index.php?option=com_democompupdate');
	}
 
	/*
	 * $parent is the class calling this method.
	 * update runs after the database scripts are executed.
	 * If the extension exists, then the update method is run.
	 * If this returns false, Joomla will abort the update and undo everything already done.
	 */
	function update( $parent ) {
// 		echo '<p>' . JText::_('COM_EASYSDI_CORE_UPDATE_SCRIPT') . '</p>';
		// You can have the backend jump directly to the newly updated component configuration page
		// $parent->getParent()->setRedirectURL('index.php?option=com_democompupdate');
	}
 
	/*
	 * $parent is the class calling this method.
	 * $type is the type of change (install, update or discover_install, not uninstall).
	 * postflight is run after the extension is registered in the database.
	 */
	function postflight( $type, $parent ) {
		if ( $type == 'install' ) {
			JTable::addIncludePath(JPATH_ADMINISTRATOR.DS."..".DS."libraries".DS."joomla".DS."database".DS."table");
			
			//Create a default EasySDI User Category
			$row 					=& JTable::getInstance('category');
			$row->parent_id 		= 1;
			$row->level				= 1;
			$row->path 				= 'uncategorised';
			$row->extension 		= 'com_easysdi_core';
			$row->title 			= 'Uncategorised';
			$row->alias 			= 'uncategorised';
			$row->published 		= 1;
			$row->access 			= 1;
			$row->params  			= '{"category_layout":"","image":""}';
			$row->metadata 			= '{"author":"","robots":""}';
			if(!$row->store(true))
			{
				JError::raiseWarning(null, JText::_('COM_EASYSDI_CORE_POSTFLIGHT_SCRIPT_CATEGORY_ERROR'));
				return false;
			}
			$row->moveByReference(0, 'last-child', $row->id);
			
			//Create new EasySDI User account
			$user	= JFactory::getUser();
			JTable::addIncludePath(JPATH_ADMINISTRATOR.DS."components".DS."com_easysdi_core".DS."tables");
			$newaccount =& JTable::getInstance('user', 'easysdi_coreTable');
			if (!$newaccount) {
				JError::raiseWarning(null, JText::_('COM_EASYSDI_CORE_POSTFLIGHT_SCRIPT_USER_ERROR_INSTANCIATE'));
				return false;
			}
			$newaccount->user_id 	= $user->id;
			$newaccount->acronym	= $user->username;
			$newaccount->catid 		= $row->id;
			$result 				= $newaccount->store();
			if (!(isset($result)) || !$result) {
				JError::raiseError(42, JText::_('COM_EASYSDI_CORE_POSTFLIGHT_SCRIPT_USER_ERROR_STORE'). $newaccount->getError());
				return false;
			}
			require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'helpers'.DS.'easysdi_core.php';
			$params['infrastructureID'] 	=  Easysdi_coreHelper::uuid();
			$params['defaultaccount'] 		= $user->id;
			$params['guestaccount'] 		= $user->id;
			$params['serviceaccount'] 		= $user->id;
			
			$this->setParams( $params );
		}
		
// 		echo '<p>' . JText::_('COM_EASYSDI_CORE_POSTFLIGHT_SCRIPT ') . '</p>';
	}

	/*
	 * $parent is the class calling this method
	 * uninstall runs before any other action is taken (file removal or database processing).
	 */
	function uninstall( $parent ) {
// 		echo '<p>' . JText::_('COM_EASYSDI_CORE_UNINSTALL_SCRIPT ') . '</p>';
	}
 
	/*
	 * get a variable from the manifest file (actually, from the manifest cache).
	 */
	function getParam( $name ) {
		$db = JFactory::getDbo();
		$db->setQuery('SELECT manifest_cache FROM #__extensions WHERE name = "com_easysdi_core"');
		$manifest = json_decode( $db->loadResult(), true );
		return $manifest[ $name ];
	}
 
	/*
	 * sets parameter values in the component's row of the extension table
	 */
	function setParams($param_array) {
		if ( count($param_array) > 0 ) {
			// read the existing component value(s)
			$db = JFactory::getDbo();
			$db->setQuery('SELECT params FROM #__extensions WHERE name = "com_easysdi_core"');
			$params = json_decode( $db->loadResult(), true );
			// add the new variable(s) to the existing one(s)
			foreach ( $param_array as $name => $value ) {
				$params[ (string) $name ] = (string) $value;
			}
			// store the combined new and existing values back as a JSON string
			$paramsString = json_encode( $params );
			$db->setQuery('UPDATE #__extensions SET params = ' .
				$db->quote( $paramsString ) .
				' WHERE name = "com_easysdi_core"' );
				$db->query();
		}
	}
}

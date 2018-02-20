<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class com_easysdi_serviceInstallerScript
{
	/*
	 * $parent is the class calling this method.
	 * $type is the type of change (install, update or discover_install, not uninstall).
	 * preflight runs before anything else and while the extracted files are in the uploaded temp folder.
	 * If preflight returns false, Joomla will abort the update and undo everything already done.
	 */
	function preflight( $type, $parent ) {
		//Check if com_easysdi_core is installed
		$db = JFactory::getDbo();
                $query = $db->getQuery(true);
                $query->select('COUNT(*)');
                $query->from('#__extensions');
                $query->where('name = '.$db->quote('com_easysdi_core'));
		$db->setQuery($query);
		$install = $db->loadResult();
		
		if($install == 0){
			JError::raiseWarning(null, JText::_('COM_EASYSDI_SERVICE_INSTALL_SCRIPT_CORE_ERROR'));
			return false;
                }
		
		// Installing component manifest file version
		$this->release = $parent->get( "manifest" )->version;
		
		// Show the essential information at the install/update back-end
		echo '<p>EasySDI component Service [com_easysdi_service]';
		echo '<br />'.JText::_('COM_EASYSDI_SERVICE_INSTALL_SCRIPT_MANIFEST_VERSION') . $this->release;
	}
 
	/*
	 * $parent is the class calling this method.
	 * install runs after the database scripts are executed.
	 * If the extension is new, the install method is run.
	 * If install returns false, Joomla will abort the install and undo everything already done.
	 */
	function install( $parent ) {
//		echo '<p>' . JText::_('COM_DEMOCOMPUPDATE_INSTALL to ' . $this->release) . '</p>';
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
//		echo '<p>' . JText::_('COM_DEMOCOMPUPDATE_UPDATE_ to ' . $this->release) . '</p>';
		// You can have the backend jump directly to the newly updated component configuration page
		// $parent->getParent()->setRedirectURL('index.php?option=com_democompupdate');
	}
 
	/*
	 * $parent is the class calling this method.
	 * $type is the type of change (install, update or discover_install, not uninstall).
	 * postflight is run after the extension is registered in the database.
	 */
	function postflight( $type, $parent ) {
		JTable::addIncludePath(JPATH_ADMINISTRATOR."/../libraries/joomla/database/table");
		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_easysdi_service/tables');
		
                $db = JFactory::getDbo();
                
		if($type == 'install'){
                        require_once JPATH_ADMINISTRATOR.'/components/com_easysdi_core/libraries/easysdi/database/sditable.php';
                    
			$query = $db->getQuery(true);
                        $columns = array('parent_id', 'level','path','extension','title','alias','published','access','params','metadata','metadesc','metakey','description','language');
                        $values = array(1,1,$query->quote('uncategorised'),$query->quote('com_easysdi_service'),$query->quote('Uncategorised'),$query->quote('uncategorised'),1,1,$query->quote('{"category_layout":"","image":""}'),$query->quote('{"author":"","robots":""}'),$query->quote('uncategorised'),$query->quote(' '),$query->quote(' '),$query->quote(' '));
                        $query->insert('#__categories');
                        $query->columns($query->quoteName($columns));
                        $query->values(implode(',', $values));
                        
                        $db->setQuery($query);
                        $db->execute();
                        
		
			//Create a Bing service
			$row 						= sdiTable::getInstance('physicalservice','easysdi_serviceTable');
			$row->alias					= 'Bing';
			$row->ordering				= 1;
			$row->state					= 1;
			$row->name					= 'Bing';
			$row->serviceconnector_id	= 12;
			$row->resourceurl			= 'http://dev.virtualearth.net/REST/v1/Imagery/Map/imagerySet';
			$row->catid 				= 1;
			$row->access				= 1;
			$result 					= $row->store();
			if (!(isset($result)) || !$result) {
				JError::raiseError(42, JText::_('COM_EASYSDI_MAP_POSTFLIGHT_SCRIPT_BACKGROUND_ERROR'). $row->getError());
				return false;
			}
			
			//Create Bing layers
			$layer 						= sdiTable::getInstance('layer','easysdi_serviceTable');
			$layer->state				= 1;
			$layer->name				= 'Road';
			$layer->physicalservice_id	= $row->id;
			$result 					= $layer->store();
			if (!(isset($result)) || !$result) {
				JError::raiseError(42, JText::_('COM_EASYSDI_MAP_POSTFLIGHT_SCRIPT_BACKGROUND_ERROR'). $layer->getError());
				return false;
			}
			$layer 						= sdiTable::getInstance('layer','easysdi_serviceTable');
			$layer->state				= 1;
			$layer->name				= 'Aerial';
			$layer->physicalservice_id	= $row->id;
			$result 					= $layer->store();
			if (!(isset($result)) || !$result) {
				JError::raiseError(42, JText::_('COM_EASYSDI_MAP_POSTFLIGHT_SCRIPT_BACKGROUND_ERROR'). $layer->getError());
				return false;
			}
			$layer 						= sdiTable::getInstance('layer','easysdi_serviceTable');
			$layer->state				= 1;
			$layer->name				= 'AerialWithLabels';
			$layer->physicalservice_id	= $row->id;
			$result 					= $layer->store();
			if (!(isset($result)) || !$result) {
				JError::raiseError(42, JText::_('COM_EASYSDI_MAP_POSTFLIGHT_SCRIPT_BACKGROUND_ERROR'). $layer->getError());
				return false;
			}
			
			//Create a Google service
			$row 						= sdiTable::getInstance('physicalservice','easysdi_serviceTable');
			$row->alias					= 'Google';
			$row->state					= 1;
			$row->ordering				= 2;
			$row->name					= 'Google';
			$row->serviceconnector_id	= 13;
			$row->resourceurl			= 'https://maps.google.com/maps';
			$row->catid 				= 1;
			$row->access				= 1;
			$result 					= $row->store();
			if (!(isset($result)) || !$result) {
				JError::raiseError(42, JText::_('COM_EASYSDI_MAP_POSTFLIGHT_SCRIPT_BACKGROUND_ERROR'). $row->getError());
				return false;
			}
			//Create Google layers
			$layer 						= sdiTable::getInstance('layer','easysdi_serviceTable');
			$layer->state				= 1;
			$layer->name				= 'ROADMAP';
			$layer->physicalservice_id	= $row->id;
			$result 					= $layer->store();
			if (!(isset($result)) || !$result) {
				JError::raiseError(42, JText::_('COM_EASYSDI_MAP_POSTFLIGHT_SCRIPT_BACKGROUND_ERROR'). $layer->getError());
				return false;
			}
			$layer 						= sdiTable::getInstance('layer','easysdi_serviceTable');
			$layer->state				= 1;
			$layer->name				= 'SATELLITE';
			$layer->physicalservice_id	= $row->id;
			$result 					= $layer->store();
			if (!(isset($result)) || !$result) {
				JError::raiseError(42, JText::_('COM_EASYSDI_MAP_POSTFLIGHT_SCRIPT_BACKGROUND_ERROR'). $layer->getError());
				return false;
			}
			$layer 						= sdiTable::getInstance('layer','easysdi_serviceTable');
			$layer->state				= 1;
			$layer->name				= 'HYBRID';
			$layer->physicalservice_id	= $row->id;
			$result 					= $layer->store();
			if (!(isset($result)) || !$result) {
				JError::raiseError(42, JText::_('COM_EASYSDI_MAP_POSTFLIGHT_SCRIPT_BACKGROUND_ERROR'). $layer->getError());
				return false;
			}
			$layer 						= sdiTable::getInstance('layer','easysdi_serviceTable');
			$layer->state				= 1;
			$layer->name				= 'TERRAIN';
			$layer->physicalservice_id	= $row->id;
			$result 					= $layer->store();
			if (!(isset($result)) || !$result) {
				JError::raiseError(42, JText::_('COM_EASYSDI_MAP_POSTFLIGHT_SCRIPT_BACKGROUND_ERROR'). $layer->getError());
				return false;
			}
			
			//Create an OSM service
			$row 						= sdiTable::getInstance('physicalservice','easysdi_serviceTable');
			$row->alias					= 'OSM';
			$row->state					= 1;
			$row->ordering				= 3;
			$row->name					= 'OSM';
			$row->serviceconnector_id	= 14;
			$row->resourceurl			= 'http://openstreetmap.org/';
			$row->catid 				= 1;
			$row->access				= 1;
			$result 					= $row->store();
			if (!(isset($result)) || !$result) {
				JError::raiseError(42, JText::_('COM_EASYSDI_MAP_POSTFLIGHT_SCRIPT_BACKGROUND_ERROR'). $row->getError());
				return false;
			}
			//Create OSM layers
			$layer 						= sdiTable::getInstance('layer','easysdi_serviceTable');
			$layer->state				= 1;
			$layer->name				= 'mapnik';
			$layer->physicalservice_id	= $row->id;
			$result 					= $layer->store();
			if (!(isset($result)) || !$result) {
				JError::raiseError(42, JText::_('COM_EASYSDI_MAP_POSTFLIGHT_SCRIPT_BACKGROUND_ERROR'). $layer->getError());
				return false;
			}
			$layer 						= sdiTable::getInstance('layer','easysdi_serviceTable');
			$layer->state				= 1;
			$layer->name				= 'osmarender';
			$layer->physicalservice_id	= $row->id;
			$result 					= $layer->store();
			if (!(isset($result)) || !$result) {
				JError::raiseError(42, JText::_('COM_EASYSDI_MAP_POSTFLIGHT_SCRIPT_BACKGROUND_ERROR'). $layer->getError());
				return false;
			}
		}
		
		
                $query = $db->getQuery(true);
                $query->delete('#__menu');
                $query->where('title = '.$db->quote('com_easysdi_service'));
		$db->setQuery($query);
		$db->query();
	}

	/*
	 * $parent is the class calling this method
	 * uninstall runs before any other action is taken (file removal or database processing).
	 */
	function uninstall( $parent ) {
		
	}
 
	/*
	 * get a variable from the manifest file (actually, from the manifest cache).
	 */
	function getParam( $name ) {
		$db = JFactory::getDbo();
                $query = $db->getQuery(true);
                $query->select('manifest_cache');
                $query->from('#__extensions');
                $query->where('name = '.$db->quote('com_easysdi_service'));
		$db->setQuery($query);
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
                        $query = $db->getQuery(true);
                        $query->select('params');
                        $query->from('#__extensions');
                        $query->where('name = '.$db->quote('com_easysdi_service'));
			$db->setQuery($query);
			$params = json_decode( $db->loadResult(), true );
			// add the new variable(s) to the existing one(s)
			foreach ( $param_array as $name => $value ) {
				$params[ (string) $name ] = (string) $value;
			}
			// store the combined new and existing values back as a JSON string
			$paramsString = json_encode( $params );
                        $query = $db->getQuery(true);
                        $query->update('#__extensions');
                        $query->set('params = ' .$db->quote( $paramsString ));
                        $query->where('name = '.$db->quote('com_easysdi_service'));
			$db->setQuery($query);
			$db->query();
		}
	}
}

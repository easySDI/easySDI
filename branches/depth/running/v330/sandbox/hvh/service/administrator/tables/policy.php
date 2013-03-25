<?php
/**
 * @version     3.3.0
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access
defined('_JEXEC') or die;
require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'libraries'.DS.'easysdi'.DS.'database'.DS.'sditable.php';

/**
 * policy Table class
 */
class Easysdi_serviceTablepolicy extends sdiTable {
	/**
	* Constructor
	*
	* @param JDatabase A database connector object
	*/
	public function __construct(&$db) {
		parent::__construct('#__sdi_policy', 'id', $db);
	}
	
	/**
	* Method to compute the default name of the asset.
	* The default name is in the form table_name.id
	* where id is the value of the primary key of the table.
	*
	* @return  string
	*
	* @since   11.1
	*/
	protected function _getAssetName() {
		$k = $this->_tbl_key;
		return 'com_easysdi_service.policy.' . (int) $this->$k;
	}

	/**
	* Method to return the title to use for the asset table.
	*
	* @return  string
	*
	* @since   11.1
	*/
	protected function _getAssetTitle() {
		return $this->alias;
	}  

	/**
	* Method to get the parent asset under which to register this one.
	* By default, all assets are registered to the ROOT node with ID 1.
	* The extended class can define a table and id to lookup.  If the
	* asset does not exist it will be created.
	*
	* @param   JTable   $table  A JTable object for the asset parent.
	* @param   integer  $id     Id to look up
	*
	* @return  integer
	*
	* @since   11.1
	*/
	protected function _getAssetParentId($table = null, $id = null) {
		$db = JFactory::getDbo();
		$db->setQuery('SELECT virtualservice_id FROM #__sdi_policy WHERE id = ' . $id);
		$virtualservice_id = $db->loadResult();
		
		$asset = JTable::getInstance('Asset');
		$asset->loadByName('com_easysdi_service.policy.' . $id);
		return $asset->id;
	}
	
	/**
	 * Overloaded check function
	 */
	public function check() {
		//If there is an ordering column and this is a new row then get the next ordering value
		if (property_exists($this, 'ordering') && $this->id == 0)
		{
			$this->ordering = self::getNextOrder('catid = '.$this->virtualservice_id);
		}
	
		return true;
	}
}

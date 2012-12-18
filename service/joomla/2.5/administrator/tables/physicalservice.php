<?php
/**
 * @version     3.0.0
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access
defined('_JEXEC') or die;

require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'libraries'.DS.'easysdi'.DS.'database'.DS.'sditable.php';

/**
 * service Table class
 */
class Easysdi_serviceTablephysicalservice extends sdiTable
{
	/**
	 * Constructor
	 *
	 * @param JDatabase A database connector object
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__sdi_physicalservice', 'id', $db);
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
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		return 'com_easysdi_service.physicalservice.' . (int) $this->$k;
	}
	
	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	protected function _getAssetTitle()
	{
		return $this->alias;
	}
	
	/**
	 * Method to return the list of services ids used by the specified context id
	 *
	 * @param   integer    	$context_id   			A context identifier
	 *
	 * @return  boolean  True if successful. False if row not found or on error (internal error state set in that case).
	 *
	 * @link    http://docs.joomla.org/JTable/load
	 * @since   EasySDI 3.0.0
	 */
	public function GetIdsByContextId($context_id = null, $reset = true)
	{
		if ($reset)
		{
			$this->reset();
		}
	
		try
		{
			// Initialise the query.
			$query = $this->_db->getQuery(true);
			$query->select('ps.id');
			$query->from($this->_tbl.'  AS ps ');
			$query->join('LEFT', '#__sdi_map_context_physicalservice AS cps ON cps.physicalservice_id=ps.id');
			$query->where('cps.context_id = ' . (int) $context_id);
			$query->where('ps.state = 1' );
			$this->_db->setQuery($query);
	
		
			$rows = $this->_db->loadResultArray();
		}
		catch (JDatabaseException $e)
		{
			$je = new JException($e->getMessage());
			$this->setError($je);
			return false;
		}
	
		// Legacy error handling switch based on the JError::$legacy switch.
		// @deprecated  12.1
		if (JError::$legacy && $this->_db->getErrorNum())
		{
			$e = new JException($this->_db->getErrorMsg());
			$this->setError($e);
			return false;
		}
	
		// Check that we have a result.
		if (empty($rows))
		{
			return false;
		}
	
		return $rows;
	}
	
	/**
	 * Overwrite method Load of JTable to get access and state values from physicalservice categories
	 * (with inheritance) if relevant.
	 *
	 */
	public function loadWithAccessInheritance($keys = null, $reset = true)
	{
		if(! parent::load ($keys, $reset))
			return false;
		
		//Get the access inherited from category
		if(!empty($this->catid))
		{
			return $this->getAccessFromInheritance ();
		}
		
		return true;
	}

	protected function getAccessFromInheritance ()
	{
		$access = $this->access;
		$state = $this->state;
		 
		
		$query = $this->_db->getQuery(true);
		$query->select('v.*');
		$query->from('#__viewlevels AS v');
		$query->where('v.id = '. (int) $this->access);
		$this->_db->setQuery($query);
		$access_object = $this->_db->loadObject();
		$access_ordering = $access_object->ordering;
		
		//If the access of the category is prevalent on the object one
		//set the category access to the object
		$category_parent = $this->getCategoryParent($this->catid);
		if($category_parent->access_ordering > $access_object->ordering)
		{
			$access = $category_parent->access;
			$access_ordering = $category_parent->access_ordering;
		}
	
		//Category is not published : physicalservice is not as well
		if($category_parent->published == 0)
			$state = $category_parent->published;
	
		while ($category_parent->parent_id != 0)
		{
			$category_parent = $this->getCategoryParent($category_parent->parent_id);
			if($category_parent->access_ordering > $access_ordering)
			{
				$access = $category_parent->access;
				$access_ordering = $category_parent->access_ordering;
			}
	
			//Category is not published : physicalservice is not as well
			if($category_parent->published == 0)
				$state = $category_parent->published;
		}
	
		$this->access = $access;
		$this->state = $state;
		return true;
	}
	
	private function getCategoryParent ($catid)
	{
		$query = $this->_db->getQuery(true);
		$query->select('c.*, v.ordering as access_ordering');
		$query->from('#__categories AS c');
		$query->join('INNER', '#__viewlevels AS v on v.id = c.access');
		$query->where('c.id = '. (int) $catid);
		$this->_db->setQuery($query);
		$data = $this->_db->loadObject();
	
		return $data;
	}
}

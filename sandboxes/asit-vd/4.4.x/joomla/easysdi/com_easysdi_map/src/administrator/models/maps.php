<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_map
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Easysdi_map records.
 */
class Easysdi_mapModelmaps extends JModelList
{

	/**
	 * Constructor.
	 *
	 * @param    array    An optional associative array of configuration settings.
	 * @see        JController
	 * @since    1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
					'id', 'a.id',
					'guid', 'a.guid',
					'alias', 'a.alias',
					'created', 'a.created',
					'created_by', 'a.created_by',
					'modified_by', 'a.modified_by',
					'modified', 'a.modified',
					'ordering', 'a.ordering',
					'state', 'a.state',
					'name', 'a.name',
					'srs', 'a.srs',
					'unit_id', 'a.unit_id',
					'centercoordinates', 'a.centercoordinates',
					'maxresolution', 'a.maxresolution',
					'maxextent', 'a.maxextent',
					'abstract', 'a.abstract',
					'access', 'a.access',
					'asset_id', 'a.asset_id',

			);
		}

		parent::__construct($config);
	}


	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$access = $this->getUserStateFromRequest($this->context.'.filter.access', 'filter_access', 0, 'int');
		$this->setState('filter.access', $access);

		$published = $app->getUserStateFromRequest($this->context.'.filter.state', 'filter_published', '', 'string');
		$this->setState('filter.state', $published);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_easysdi_map');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.name', 'asc');
	}

	/**
	 * Method to get an array of data items. Fields are restricted to id and name.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   EasySDI 3.3.0
	 */
	public function getItemsRestricted ()
	{
		// Load the list items.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
	
		// Select the required fields from the table.
		$query->select('m.id as id, m.name as name');
		$query->from('#__sdi_map AS m');
		$query->where('m.state = 1');
		$query->order('m.ordering');
	
		try
		{
			$items = $this->_getList($query);
		}
		catch (RuntimeException $e)
		{
			$this->setError($e->getMessage());
			return false;
		}
		return $items;
	}
	
	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 * @return	string		A store id.
	 * @since	1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id.= ':' . $this->getState('filter.search');
		$id.= ':' . $this->getState('filter.state');
		$id.= ':' . $this->getState('filter.access');
		$id.= ':'. $this->getState('filter.published');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$user	= JFactory::getUser();

		// Select the required fields from the table.
		$query->select(
				$this->getState(
						'list.select',
						'a.id, a.alias, a.state, a.checked_out, a.checked_out_time, a.ordering, a.name, a.access, a.created_by'
				)
		);
		$query->from('#__sdi_map AS a');

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		// Join over the created by field 'created_by'
		/*$query->select('created_by.name AS created_by');
		$query->join('LEFT', '#__users AS created_by ON created_by.id = a.created_by');*/

		// Join over the access level.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

		// Implement View Level Access
		if (!$user->authorise('core.admin'))
		{
			$groups	= implode(',', $user->getAuthorisedViewLevels());
			$query->where('a.access IN ('.$groups.')');
		}

		// Filter by access level.
		if ($access = $this->getState('filter.access')) {
			$query->where('a.access = ' . (int) $access);
		}
		
		// Filter by published state
		$published = $this->getState('filter.state');
		if (is_numeric($published)) {
			$query->where('a.state = '.(int) $published);
		} else if ($published === '') {
			$query->where('(a.state IN (0, 1))');
		}

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('( a.name LIKE '.$search.' )');
			}
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
		if ($orderCol && $orderDirn) {
			$query->order($db->escape($orderCol.' '.$orderDirn));
		}

		return $query;
	}
}

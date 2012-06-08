<?php
/**
 * @version     3.0.0
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Easysdi_core records.
 */
class Easysdi_coreModelservices extends JModelList
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
                'created_by', 'a.created_by',
                'created', 'a.created',
                'modified_by', 'a.modified_by',
                'modified', 'a.modified',
                'ordering', 'a.ordering',
                'state', 'a.state',
                'name', 'a.name',
                'serviceconnector_id', 'a.serviceconnector_id',
                'published', 'a.published',
                'resourceauthentication_id', 'a.resourceauthentication_id',
                'resourceurl', 'a.resourceurl',
                'resourceusername', 'a.resourceusername',
                'resourcepassword', 'a.resourcepassword',
                'serviceauthentication_id', 'a.serviceauthentication_id',
                'serviceurl', 'a.serviceurl',
                'serviceusername', 'a.serviceusername',
                'servicepassword', 'a.servicepassword',
                'catid', 'a.catid',
                'params', 'a.params',

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

		$published = $app->getUserStateFromRequest($this->context.'.filter.state', 'filter_published', '', 'string');
		$this->setState('filter.state', $published);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_easysdi_core');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.guid', 'asc');
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

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.*'
			)
		);
		$query->from('`#__sdi_service` AS a');


        // Join over the users for the checked out user.
        $query->select('uc.name AS editor');
        $query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');
        
        // Join over the foreign key 'serviceconnector_id'
        $query->select('#__sdi_sys_serviceconnector.value AS serviceconnector_value');
        $query->join('LEFT', '#__sdi_sys_serviceconnector ON #__sdi_sys_serviceconnector.id=a.serviceconnector_id');
        
        // Join over the foreign key 'resourceauthentication_id'
        $query->select('ssac1.value AS resourceauth_value');
        $query->join('LEFT', '#__sdi_sys_authenticationconnector as ssac1 ON ssac1.id=a.resourceauthentication_id');
        
        // Join over the foreign key 'serviceauthentication_id'
        $query->select('ssac2.value AS serviceauth_value');
        $query->join('LEFT', '#__sdi_sys_authenticationconnector as ssac2 ON ssac2.id=a.serviceauthentication_id');
        


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
				$search = $db->Quote('%'.$db->getEscaped($search, true).'%');
                $query->where('( a.guid LIKE '.$search.'  OR  a.alias LIKE '.$search.'  OR  a.name LIKE '.$search.'  OR  a.resourceurl LIKE '.$search.'  OR  a.resourceusername LIKE '.$search.'  OR  a.resourcepassword LIKE '.$search.'  OR  a.serviceurl LIKE '.$search.'  OR  a.serviceusername LIKE '.$search.'  OR  a.servicepassword LIKE '.$search.'  OR  a.params LIKE '.$search.' )');
			}
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
        if ($orderCol && $orderDirn) {
		    $query->order($db->getEscaped($orderCol.' '.$orderDirn));
        }

		return $query;
	}
}

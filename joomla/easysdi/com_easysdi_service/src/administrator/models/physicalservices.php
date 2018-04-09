<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Easysdi_service records.
 */
class Easysdi_serviceModelphysicalservices extends JModelList {

    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see        JController
     * @since    1.6
     */
    public function __construct($config = array()) {
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
                'resourceauthentication_id', 'a.resourceauthentication_id',
                'resourceurl', 'a.resourceurl',
                'resourceusername', 'a.resourceusername',
                'resourcepassword', 'a.resourcepassword',
                'serviceauthentication_id', 'a.serviceauthentication_id',
                'serviceurl', 'a.serviceurl',
                'serviceusername', 'a.serviceusername',
                'servicepassword', 'a.servicepassword',
                'catid', 'a.catid', 'category_title',
                'params', 'a.params',
                'asset_id', 'a.asset_id',
                'access', 'a.access', 'access_level',
                'serviceconnector',
            );
        }

        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     */
    protected function populateState($ordering = null, $direction = null) {
        // Initialise variables.
        $app = JFactory::getApplication('administrator');

        // Load the filter state.
        $search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $connector = $app->getUserStateFromRequest($this->context . '.filter.connector', 'filter_connector', '');
        $this->setState('filter.connector', $connector);

        $access = $this->getUserStateFromRequest($this->context . '.filter.access', 'filter_access', 0, 'int');
        $this->setState('filter.access', $access);

        $state = $app->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
        $this->setState('filter.state', $state);

        $categoryId = $this->getUserStateFromRequest($this->context . '.filter.category_id', 'filter_category_id');
        $this->setState('filter.category_id', $categoryId);

        $published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
        $this->setState('filter.published', $published);

        // Load the parameters.
        $params = JComponentHelper::getParams('com_easysdi_service');
        $this->setState('params', $params);

        // List state information.
        parent::populateState('a.alias', 'asc');
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
    protected function getStoreId($id = '') {
        // Compile the store id.
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.connector');
        $id .= ':' . $this->getState('filter.state');
        $id .= ':' . $this->getState('filter.published');
        $id .= ':' . $this->getState('filter.category_id');
        $id .= ':' . $this->getState('filter.access');

        return parent::getStoreId($id);
    }

    /**
     *
     */
    public function getConnector() {
        // Create a new query object.
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('id');
        $query->select('value');
        $query->from('#__sdi_sys_serviceconnector');
        $query->where('state=1');
        $query->order('value');
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return	JDatabaseQuery
     * @since	1.6
     */
    protected function getListQuery() {
        // Create a new query object.
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $user = JFactory::getUser();

        // Select the required fields from the table.
        $query->select(
                $this->getState(
                        'list.select', 'a.*'
                )
        );
        $query->from('#__sdi_physicalservice AS a');

        // Join over the users for the checked out user.
        $query->select('uc.name AS editor');
        $query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

        // Join over the asset groups.
        $query->select('ag.title AS access_level');
        $query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

        // Join over the categories.
        $query->select('c.title AS category_title');
        $query->join('LEFT', '#__categories AS c ON c.id = a.catid');

        // Join over the foreign key 'serviceconnector_id'
        $query->select('#__sdi_sys_serviceconnector.value AS serviceconnector');
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
            $query->where('a.state = ' . (int) $published);
        } else if ($published === '') {
            $query->where('(a.state IN (0, 1))');
        }

        // Filter by connector.
        if ($connector = $this->getState('filter.connector')) {
            $query->where('#__sdi_sys_serviceconnector.id = ' . (int) $connector);
        }

        // Filter by access level.
        if ($access = $this->getState('filter.access')) {
            $query->where('a.access = ' . (int) $access);
        }

        // Implement View Level Access
        if (!$user->authorise('core.admin')) {
            $groups = implode(',', $user->getAuthorisedViewLevels());
            $query->where('a.access IN (' . $groups . ')');
        }

        // Filter by published state
        $published = $this->getState('filter.published');
        if (is_numeric($published)) {
            $query->where('a.state = ' . (int) $published);
        } elseif ($published === '') {
            $query->where('(a.state = 0 OR a.state = 1)');
        }

        // Filter by a single or group of categories.
        $categoryId = $this->getState('filter.category_id');
        if (is_numeric($categoryId)) {
            $query->where('a.catid = ' . (int) $categoryId);
        } elseif (is_array($categoryId)) {
            JArrayHelper::toInteger($categoryId);
            $categoryId = implode(',', $categoryId);
            $query->where('a.catid IN (' . $categoryId . ')');
        }

        // Filter by search in title
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int) substr($search, 3));
            } else {
                $search = $db->Quote('%' . $db->escape($search, true) . '%');
                $query->where('( a.alias LIKE ' . $search . '  OR  a.name LIKE ' . $search . ' )');
            }
        }

        // Add the list ordering clause.
        $orderCol = $this->state->get('list.ordering');
        $orderDirn = $this->state->get('list.direction');
        if ($orderCol && $orderDirn) {
             switch ($orderCol):
                case 'serviceconnector':
                    $orderCol = '#__sdi_sys_serviceconnector.value';
                    break;                
            endswitch;
            $query->order($db->escape($orderCol . ' ' . $orderDirn));
        }

        return $query;
    }

}

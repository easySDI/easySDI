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
class Easysdi_serviceModelpolicies extends JModelList {

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
                'ordering', 'a.ordering',
                'state', 'a.state',
                'params', 'a.params',
                'asset_id', 'a.asset_id',
                'access', 'a.access', 'access_level',
                'created_by', 'a.created_by',
                'anonymousaccess', 'a.anonymousaccess',
                'anygroup', 'a.anygroup',
                'anyoperation', 'a.anyoperation',
                'anyservice', 'a.anyservice',
                'allowfrom', 'a.allowfrom',
                'allowto', 'a.allowto',
                'priority', 'a.priority',
                'guid', 'a.guid',
                'modified_by', 'a.modified_by',
                'modified', 'a.modified',
                'virtualservice_id', 'a.virtualservice_id',
                'virtualservice_name',
                'connector',
                'csw_version', 'a.csw_version',
                'csw_anystate', 'a.csw_anystate',
                'csw_anycontext', 'a.csw_anycontext',
                'csw_anyvisibility', 'a.csw_anyvisibility',
                'csw_geographicfilter', 'a.csw_geographicfilter',
                'wms_minimumwidth', 'a.wms_minimumwidth',
                'wms_minimumheight', 'a.wms_minimumheight',
                'wms_maximumwidth', 'a.wms_maximumwidth',
                'wms_maximumheight', 'a.wms_maximumheight',
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

        $published = $app->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
        $this->setState('filter.state', $published);

        $virtualservice = $app->getUserStateFromRequest($this->context . '.filter.virtualservice', 'filter_virtualservice', '', 'string');
        $this->setState('filter.virtualservice', $virtualservice);

        $connector = $app->getUserStateFromRequest($this->context . '.filter.connector', 'filter_connector', '', 'string');
        $this->setState('filter.connector', $connector);

        // Load the parameters.
        $params = JComponentHelper::getParams('com_easysdi_service');
        $this->setState('params', $params);

        // List state information.
        parent::populateState('a.id', 'asc');
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
        $id.= ':' . $this->getState('filter.search');
        $id.= ':' . $this->getState('filter.state');
        $id.= ':' . $this->getState('filter.virtualservice');
        $id.= ':' . $this->getState('filter.connector');

        return parent::getStoreId($id);
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

        // Select the required fields from the table.
        $query->select(
                $this->getState(
                        'list.select', 'a.id, a.alias, a.virtualservice_id, a.state, a.checked_out, a.checked_out_time, a.ordering, a.name, a.access'
                )
        );
        $query->from('#__sdi_policy AS a');

        // Join over the users for the checked out user.
        $query->select('uc.name AS editor');
        $query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

        // Join over the asset groups.
        $query->select('ag.title AS access_level');
        $query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

        // Join over the created by field 'created_by'
        $query->select('created_by.name AS created_by');
        $query->join('LEFT', '#__users AS created_by ON created_by.id = a.created_by');

        // Join over the foreign key 'virtualservice_id'
        $query->select('vs.name AS virtualservice_name, sc.value as connector');
        $query->join('LEFT', '#__sdi_virtualservice AS vs ON vs.id = a.virtualservice_id');
        $query->join('LEFT', '#__sdi_sys_serviceconnector AS sc ON sc.id = vs.serviceconnector_id');


        // Filter by published state
        $published = $this->getState('filter.state');
        if (is_numeric($published)) {
            $query->where('a.state = ' . (int) $published);
        } else if ($published === '') {
            $query->where('(a.state IN (0, 1))');
        }

        // Filter by connector
        $connector = $this->getState('filter.connector');
        if (is_numeric($connector)) {
            $query->where('sc.id = ' . (int) $connector);
        }

        // Filter by search in title
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int) substr($search, 3));
            } else {
                $search = $db->Quote('%' . $db->escape($search, true) . '%');
                $query->where('( a.name LIKE ' . $search . ' )');
            }
        }

        // Filter by virtualservice 
        $virtualservice = $this->getState('filter.virtualservice');
        if (!empty($virtualservice)) {
            $query->where('a.virtualservice_id = ' . $virtualservice);
        }

        // Add the list ordering clause.
        $orderCol = $this->state->get('list.ordering');
        $orderDirn = $this->state->get('list.direction');
        if ($orderCol && $orderDirn) {
            switch ($orderCol):
                case 'virtualservice_name':
                    $orderCol = 'vs.name';
                    break;
                case 'connector':
                    $orderCol = 'sc.value';
                    break;
            endswitch;
            $query->order($db->escape($orderCol . ' ' . $orderDirn));
        }

        return $query;
    }

}

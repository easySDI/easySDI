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
class Easysdi_serviceModelvirtualservices extends JModelList {

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
                'created_by', 'a.created_by',
                'name', 'a.name',
                'alias', 'a.alias',
                'url', 'a.url',
                'reflectedurl', 'a.reflectedurl',
                'reflectedmetadata', 'a.reflectedmetadata',
                'xsltfilename', 'a.xsltfilename',
                'logautodriven', 'a.logautodriven',
                'logpath', 'a.logpath',
                'logprefixfilename', 'a.logprefixfilename',
                'logsuffixfilename', 'a.logsuffixfilename',
                'logextensionfilename', 'a.logextensionfilename',
                'guid', 'a.guid',
                'modified_by', 'a.modified_by',
                'modified', 'a.modified',
                'harvester', 'a.harvester',
                'maximumrecords', 'a.maximumrecords',
                'identifiersearchattribute', 'a.identifiersearchattribute',
                'proxytype_id', 'a.proxytype_id',
                'exceptionlevel_id', 'a.exceptionlevel_id',
                'loglevel_id', 'a.loglevel_id',
                'logroll_id', 'a.logroll_id',
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

        $published = $app->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
        $this->setState('filter.state', $published);

        $connector = $app->getUserStateFromRequest($this->context . '.filter.connector', 'filter_connector', '', 'string');
        $this->setState('filter.connector', $connector);

        // Load the parameters.
        $params = JComponentHelper::getParams('com_easysdi_service');
        $this->setState('params', $params);

        // List state information.
        parent::populateState('a.id', 'asc');
    }

    /**
     * Method to get an array of data items. Fields are restricted to id and name.
     *
     * @return  mixed  An array of data items on success, false on failure.
     *
     * @since   EasySDI 3.3.0
     */
    public function getItemsRestricted($connector = null, $pk = null) {
        // Get a storage key.
        $store = $this->getStoreId();

        // Try to load the data from internal storage.
        if (isset($this->cache[$store])) {
            return $this->cache[$store];
        }

        // Load the list items.
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select('a.id as id, a.name as name, c.value as connector');
        $query->from('#__sdi_virtualservice AS a');
        $query->join('LEFT', '#__sdi_sys_serviceconnector AS c ON a.serviceconnector_id = c.id');
        if (!empty($connector))
            $query->where('c.id = ' . $connector);
        if (!empty($pk))
            $query->where('a.id = ' . (int) $pk);
        $query->where('a.state IN (1, 0)');
        $query->order('a.ordering');
        try {
            $items = $this->_getList($query, $this->getStart(), $this->getState('list.limit'));
        } catch (RuntimeException $e) {
            $this->setError($e->getMessage());
            return false;
        }

        // Add the items to the internal cache.
        $this->cache[$store] = $items;

        return $this->cache[$store];
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
                        'list.select', 'a.id, a.name, a.alias, a.checked_out, a.checked_out_time, a.state, a.ordering, a.url, a.reflectedurl, a.access'
                )
        );
        $query->from('#__sdi_virtualservice AS a');

        // Join over the users for the checked out user.
        $query->select('uc.name AS editor');
        $query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

        // Join over the asset groups.
        $query->select('ag.title AS access_level');
        $query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

        // Join over the created by field 'created_by'
        $query->select('created_by.name AS created_by');
        $query->join('LEFT', '#__users AS created_by ON created_by.id = a.created_by');

        // Join over the foreign key serviceconnector_id
        $query->select('ssc.value AS serviceconnector');
        $query->join('LEFT', '#__sdi_sys_serviceconnector AS ssc ON ssc.id = a.serviceconnector_id');

        // Join over the foreign key 'proxytype_id'
        $query->select('#__sdi_sys_proxytype_278358.value AS sysproxytypes_proxytype_278358');
        $query->join('LEFT', '#__sdi_sys_proxytype AS #__sdi_sys_proxytype_278358 ON #__sdi_sys_proxytype_278358.id = a.proxytype_id');

        // Join over the foreign key 'exceptionlevel_id'
        $query->select('#__sdi_sys_exceptionlevel_278360.value AS sysexceptionlevels_exceptionlevel_278360');
        $query->join('LEFT', '#__sdi_sys_exceptionlevel AS #__sdi_sys_exceptionlevel_278360 ON #__sdi_sys_exceptionlevel_278360.id = a.exceptionlevel_id');

        // Join over the foreign key 'loglevel_id'
        $query->select('#__sdi_sys_loglevel_278362.value AS sysloglevels_loglevel_278362');
        $query->join('LEFT', '#__sdi_sys_loglevel AS #__sdi_sys_loglevel_278362 ON #__sdi_sys_loglevel_278362.id = a.loglevel_id');

        // Join over the foreign key 'logroll_id'
        $query->select('#__sdi_sys_logroll_278363.value AS __sdi_sys_logroll5937s_logroll_278363');
        $query->join('LEFT', '#__sdi_sys_logroll AS #__sdi_sys_logroll_278363 ON #__sdi_sys_logroll_278363.id = a.logroll_id');

        // Filter by connector state
        $connector = $this->getState('filter.connector');
        if (is_numeric($connector)) {
            $query->where('ssc.id = ' . (int) $connector);
        }

        // Filter by published state
        $published = $this->getState('filter.state');
        if (is_numeric($published)) {
            $query->where('a.state = ' . (int) $published);
        } else if ($published === '') {
            $query->where('(a.state IN (0, 1))');
        }

        // Filter by search in title
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int) substr($search, 3));
            } else {
                $search = $db->Quote('%' . $db->escape($search, true) . '%');
            }
        }

        // Add the list ordering clause.
        $orderCol = $this->state->get('list.ordering');
        $orderDirn = $this->state->get('list.direction');
        if ($orderCol && $orderDirn) {
            switch ($orderCol):
                case 'serviceconnector':
                    $orderCol = 'ssc.value';
                    break;                
            endswitch;
            $query->order($db->escape($orderCol . ' ' . $orderDirn));
        }

        return $query;
    }

}

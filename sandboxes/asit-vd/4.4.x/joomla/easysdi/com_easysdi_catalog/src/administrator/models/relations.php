<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Easysdi_catalog records.
 */
class Easysdi_catalogModelrelations extends JModelList {

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
                'description', 'a.description',
                'parent_id', 'a.parent_id', 'parentname',
                'attributechild_id', 'a.attributechild_id', 'attributechildname',
                'classchild_id', 'a.classchild_id', 'classchildname',
                'lowerbound', 'a.lowerbound',
                'upperbound', 'a.upperbound',
                'relationtype_id', 'a.relationtype_id',
                'rendertype_id', 'a.rendertype_id',
                'namespace_id', 'a.namespace_id',
                'isocode', 'a.isocode',
                'classassociation_id', 'a.classassociation_id',
                'issearchfilter', 'a.issearchfilter',
                'relationscope_id', 'a.relationscope_id',
                'editorrelationscope_id', 'a.editorrelationscope_id',
                'childresourcetype_id', 'a.childresourcetype_id', 'resourcetypechildname',
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



        // Load the parameters.
        $params = JComponentHelper::getParams('com_easysdi_catalog');
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
                        'list.select', 'a.id, a.alias, a.checked_out, a.checked_out_time, a.parent_id, a.classchild_id, a.childresourcetype_id, a.attributechild_id, a.ordering, a.state, a.name'
                )
        );
        $query->from('#__sdi_relation AS a');


        // Join over the users for the checked out user.
        $query->select('uc.name AS editor');
        $query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

        // Join over the user field 'created_by'
        $query->select('created_by.name AS created_by');
        $query->join('LEFT', '#__users AS created_by ON created_by.id = a.created_by');
        // Join over the foreign key 'parent_id'
        $query->select('#__sdi_class_593692.name AS classes_name_593692');
        $query->join('LEFT', '#__sdi_class AS #__sdi_class_593692 ON #__sdi_class_593692.id = a.parent_id');
        // Join over the foreign key 'attributechild_id'
        $query->select('#__sdi_attribute_593693.name AS attributes_name_593693');
        $query->join('LEFT', '#__sdi_attribute AS #__sdi_attribute_593693 ON #__sdi_attribute_593693.id = a.attributechild_id');
        // Join over the foreign key 'classchild_id'
        $query->select('#__sdi_class_593694.name AS classes_name_593694');
        $query->join('LEFT', '#__sdi_class AS #__sdi_class_593694 ON #__sdi_class_593694.id = a.classchild_id');

        // Join over the class for the parent name.
        $query->select('parentclass.name AS parentname');
        $query->join('LEFT', '#__sdi_class AS parentclass ON parentclass.id=a.parent_id');

        // Join over the class for the class child name.
        $query->select('childclass.name AS classchildname');
        $query->join('LEFT', '#__sdi_class AS childclass ON childclass.id=a.classchild_id');

        // Join over the attribute for the attribute child name.
        $query->select('childattribute.name AS attributechildname');
        $query->join('LEFT', '#__sdi_attribute AS childattribute ON childattribute.id=a.attributechild_id');

        // Join over the resourcetype for the resourcetype child name.
        $query->select('resourcetype.name AS resourcetypechildname');
        $query->join('LEFT', '#__sdi_resourcetype AS resourcetype ON resourcetype.id=a.childresourcetype_id');

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
                $query->where('( ( a.name LIKE ' . $search . ' ) OR ( parentclass.name LIKE ' . $search . ' ) OR ( childclass.name LIKE ' . $search . ' ) OR ( childattribute.name LIKE ' . $search . ' ) ) ');
            }
        }

        // Add the list ordering clause.
        $orderCol = $this->state->get('list.ordering');
        $orderDirn = $this->state->get('list.direction');
        if ($orderCol && $orderDirn) {
            switch ($orderCol) {
                case 'resourcetypechildname':
                    $orderCol = 'resourcetype.name';
                    break;
                case 'attributechildname':
                    $orderCol = 'childattribute.name';
                    break;
                case 'classchildname':
                    $orderCol = 'childclass.name';
                    break;
                case 'parentname':
                    $orderCol = 'parentclass.name';
                    break;
            }
            $query->order($db->escape($orderCol . ' ' . $orderDirn));
        }

        return $query;
    }

}

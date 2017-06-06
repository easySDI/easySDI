<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Easysdi_core records.
 */
class Easysdi_catalogModelAssignments extends JModelList {

    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see        JController
     * @since    1.6
     */
    public function __construct($config = array()) {
        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since	1.6
     */
    protected function populateState($ordering = null, $direction = null) {

        // Initialise variables.
        $app = JFactory::getApplication();
        
        // Get the metadata_id
        $metadata_id = $app->input->getInt('metadata', null, 'array');
        $this->setState('metadata_id', $metadata_id);

        // List state information
        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
        $this->setState('list.limit', $limit);

        $limitstart = $app->input->getInt('limitstart', 0);
        $this->setState('list.start', $limitstart);


        if (empty($ordering)) {
            $ordering = 'a.ordering';
        }

        // List state information.
        parent::populateState($ordering, $direction);
        
        $db = $this->getDbo();
        $query = $db->getQuery(true)
                ->select('r.id, r.name')
                ->from('#__sdi_resource r')
                ->innerJoin('#__sdi_version v ON v.resource_id=r.id')
                ->innerJoin('#__sdi_metadata m ON m.version_id=v.id')
                ->where('m.id='.$metadata_id)
                ;
        $db->setQuery($query);
        $this->setState('resource', $db->loadAssoc());
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return	JDatabaseQuery
     * @since	1.6
     */
    protected function getListQuery() {
        
        // Create a new query object.
        $lang = JFactory::getLanguage();
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        
        $query->select(
                $this->getState(
                        'list.select', 'a.id, a.assigned, u1.name as assigned_by, u2.name as assigned_to, a.text'
                        )
                )
                ->from('#__sdi_assignment AS a')
                ->join('LEFT', '#__sdi_user su1 ON su1.id=a.assigned_by')
                ->join('LEFT', '#__users u1 ON u1.id=su1.user_id')
                ->join('LEFT', '#__sdi_user su2 ON su2.id=a.assigned_to')
                ->join('LEFT', '#__users u2 ON u2.id=su2.user_id')
                ->where('a.metadata_id='.$this->getState('metadata_id', 0))
                ->order('a.assigned');
        
        return $query;
    }

}

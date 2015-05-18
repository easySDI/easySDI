<?php

/**
 * @version     4.0.0
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Easysdi_core records.
 */
class Easysdi_coreModelResources extends JModelList {

    /**
     * @var sdiUser 
     */
    private $user;

    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see        JController
     * @since    1.6
     */
    public function __construct($config = array()) {
        parent::__construct($config);

        $this->user = new sdiUser();
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
        
        // Try to get parentId input
        $parentid = $app->input->getInt('parentid', NULL);
        $app->setUserState('com_easysdi_core.parent.resource.version.id', $parentid);
        
        // List state information
        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
        $this->setState('list.limit', $limit);

        $limitstart = $app->input->getInt('limitstart', 0);
        $this->setState('list.start', $limitstart);
        
        // Load the filter state.
        $search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $search = $app->getUserStateFromRequest($this->context . '.filter.status', 'filter_status');
        $this->setState('filter.status', $search);

        $search = $app->getUserStateFromRequest($this->context . '.filter.resourcetype', 'filter_resourcetype');
        $this->setState('filter.resourcetype', $search);
        
        if (empty($ordering)) {
            $ordering = 'a.ordering';
        }

        // List state information.
        parent::populateState($ordering, $direction);
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

        $parentId = JFactory::getApplication()->input->getInt('parentid', null);

        // Select the required fields from the table.
        $query->select('a.id, a.guid, a.alias, a.name');
        $query->from('#__sdi_resource AS a');

        // Join over the foreign key 'resourcetype_id'
        $query->select('trans.text1 AS resourcetype_name, rt.versioning as versioning,'. $query->quoteName('rt.view') .' as supportview, rt.diffusion as supportdiffusion, rt.application as supportapplication');
        $query->join('LEFT', '#__sdi_resourcetype AS rt ON rt.id = a.resourcetype_id');
        $query->join('LEFT', '#__sdi_translation AS trans ON trans.element_guid = rt.guid');
        $query->join('LEFT', '#__sdi_language AS lang ON lang.id = trans.language_id');
        $query->where('lang.code = ' . $query->quote($lang->getTag()));
        $query->where('rt.predefined = 0');

        //join over resourcetypelink to know if some relations are possible
        $query->select('rtl.state as supportrelation');
        $query->join('LEFT', '(SELECT n.parent_id, n.state FROM #__sdi_resourcetypelink n ) rtl ON rtl.parent_id = rt.id');
        $query->select('rtl2.state as canbechild');
        $query->join('LEFT', '(SELECT child_id, state FROM #__sdi_resourcetypelink GROUP BY child_id, state) rtl2 ON rtl2.child_id=rt.id');

        //join over rights table, check if user have any right on resource
        $query->innerJoin('#__sdi_user_role_resource AS urr ON urr.resource_id = a.id');
        $query->where('urr.user_id = ' . $this->user->id);

        $query->group('a.id');
	$query->group('a.guid');
        $query->group('a.alias');
        $query->group('a.name');
        $query->group('trans.text1');
        $query->group('rt.versioning');
        $query->group('rt.diffusion');
        $query->group($query->quoteName('rt.view'));
        $query->group('rt.application');
        $query->group('rtl.state');
        $query->group('rtl2.state');
 	
        $query->innerJoin('#__sdi_version v ON v.resource_id = a.id');
        $query->innerJoin('#__sdi_metadata md ON md.version_id = v.id');
        
        if(!empty($parentId)){
            $query->innerJoin('#__sdi_versionlink vl on v.id = vl.child_id');
            $query->where('vl.parent_id = ' . (int) $parentId);
            $this->setState('parentid',$parentId);
            
            // Filter by resource type
            $resourcetype = $this->getState('filter.resourcetype.children');
            if (is_numeric($resourcetype)) {
                $query->where('a.resourcetype_id = ' . (int) $resourcetype);
            }

            // Filter by search in title
            $search = $this->getState('filter.search.children');
            if (!empty($search)) {
                if (stripos($search, 'id:') === 0) {
                    $query->where('a.id = ' . (int) substr($search, 3));
                } else {
                    $search = $db->Quote('%' . $db->escape($search, true) . '%');
                    $query->where('( a.name LIKE ' . $search . ' )');
                }
            }

            //Controls whether a version of the metadata has a status matching the filter.
            $status = $this->getState('filter.status.children');
            if (!empty($status)) {
                $query->where('md.metadatastate_id = ' . $status);
            }
        }
        else{            
           // Filter by resource type
            $resourcetype = $this->getState('filter.resourcetype');
            if (is_numeric($resourcetype)) {
                $query->where('a.resourcetype_id = ' . (int) $resourcetype);
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

            //Controls whether a version of the metadata has a status matching the filter.
            $status = $this->getState('filter.status');
            if (!empty($status)) {
                $query->where('md.metadatastate_id = ' . $status);
            }
        }
             
        $query->order('a.name');
        
        return $query;
    }

}

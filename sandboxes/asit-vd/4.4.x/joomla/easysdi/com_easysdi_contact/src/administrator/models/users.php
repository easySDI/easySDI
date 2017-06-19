<?php

/**
 * @version     4.4.4
 * @package     com_easysdi_contact
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Easysdi_core records.
 */
class Easysdi_contactModelusers extends JModelList {

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
                'created_by', 'a.created_by',
                'created', 'a.created',
                'modified_by', 'a.modified_by',
                'modified', 'a.modified',
                'ordering', 'a.ordering',
                'state', 'a.state',
                'user_id', 'a.user_id',
                'description', 'a.description',
                'notificationrequesttreatment', 'a.notificationrequesttreatment',
                'catid', 'a.catid',
                'params', 'a.params',
                'member_organism', 'o.name',
                'access', 'a.access', 'access_level',
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

        $access = $this->getUserStateFromRequest($this->context . '.filter.access', 'filter_access', 0, 'int');
        $this->setState('filter.access', $access);

        $published = $app->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
        $this->setState('filter.state', $published);

        $categoryId = $this->getUserStateFromRequest($this->context . '.filter.category_id', 'filter_category_id');
        $this->setState('filter.category_id', $categoryId);

        // Load the parameters.
        $params = JComponentHelper::getParams('com_easysdi_contact');
        $this->setState('params', $params);

        // List state information.
        parent::populateState('u.name', 'asc');


        /**
         * [jvi]:
         * Following code have to be after the parent::populateState to don't be overriden by it !
         * As filter_organism filter is a multiselect fields, if filter_organism isnt sent, we 
         * assume it's empty 
         */
        $organism = !key_exists('filter_organism', $_REQUEST) ? array() : $app->getUserStateFromRequest($this->context . '.filter.organism', 'filter_organism', array(), 'array');
        $this->setState('filter.organism', $organism);
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
        $id .= ':' . $this->getState('filter.category_id');
        $id .= ':' . $this->getState('filter.access');

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
        $user = JFactory::getUser();

        // Select the required fields from the table.
        $query->select(
                $this->getState(
                        //'list.select', 'a.id, a.checked_out, a.checked_out_time, a.created_by, a.ordering,a.state,a.catid,' . $query->concatenate(array($db->qn('o.name'), "' ['", $db->qn('o.guid'), "']'")) . ' AS member_organism, o.id as member_organism_id'
                        'list.select', 'a.id, a.checked_out, a.checked_out_time, a.created_by, a.ordering,a.state,a.catid,CONCAT(o.name,\' [\',o.id,\']\') AS member_organism, o.id as member_organism_id'
                )
        );
        $query->from('#__sdi_user AS a');


        // Join over the users .
        $query->select('u.name, u.username ');
        $query->join('LEFT', '#__users AS u ON u.id=a.user_id');

        // Join over the user's member organism.
        $query->join('LEFT', '#__sdi_user_role_organism uro ON uro.user_id=a.id AND uro.role_id=1');
        $query->join('LEFT', '#__sdi_organism o ON uro.organism_id=o.id');

        // Join over the users for the checked out user.
        $query->select('uc.name AS editor');
        $query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

        // Join over the asset groups.
        $query->select('ag.title AS access_level');
        $query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

        // Join over the categories.
        $query->select('c.title AS category_title');
        $query->join('LEFT', '#__categories AS c ON c.id = a.catid');

        // Filter by published state
        $published = $this->getState('filter.state');
        if (is_numeric($published)) {
            $query->where('a.state = ' . (int) $published);
        } else if ($published === '') {
            $query->where('(a.state IN (0, 1))');
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
        
        // Filter by organism
        $organisms = $this->getState('filter.organism');
        if (count($organisms)) {
            $query->where('uro.organism_id IN (' . implode(',', $organisms) . ')');
        }

        // Filter by search in title
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int) substr($search, 3));
            } else {
                $search = $db->Quote('%' . $db->escape($search, true) . '%');
                $query->where('( u.name LIKE ' . $search . ' OR u.username LIKE ' . $search . ' OR  u.email LIKE ' . $search . '  )');
            }
        }

        // Add the list ordering clause.
        $orderCol = $this->state->get('list.ordering');
        $orderDirn = $this->state->get('list.direction');
        if ($orderCol && $orderDirn) {
            $query->order($db->escape($orderCol . ' ' . $orderDirn));
        }
        //Not necessary
        // group by user.id to have unique rows in result
        //$query->group('a.id');
        
        return $query;
    }

    /**
     * Method to get all user having a role in an organims and their roles.
     * @param   integer $id The organism ID.
     * @return  array an array of objects containing users, organism name and roles.
     */
    public function getUsersAndRolesByOrganismID($id) {
        require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/user/sdiuser.php';
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->qn('u.id', 'sdi_user_id'))
                ->select($db->qn('u.user_id', 'j_user_id'))
                ->select($db->qn('ju.name', 'name'))
                ->select($db->qn('ju.username', 'username'))
                ->select($query->concatenate(array($db->qn('ju.name'), "' ['", $db->qn('ju.username'), "']'")) . ' AS fullusername')
                ->select($db->qn('o.id', 'org_id'))
                ->select($db->qn('o.name', 'org_name'))
                ->select($db->qn('resourcemanager.role_id', 'resourcemanager'))
                ->select($db->qn('metadataresponsible.role_id', 'metadataresponsible'))
                ->select($db->qn('metadataeditor.role_id', 'metadataeditor'))
                ->select($db->qn('diffusionmanager.role_id', 'diffusionmanager'))
                ->select($db->qn('previewmanager.role_id', 'previewmanager'))
                ->select($db->qn('extractionresponsible.role_id', 'extractionresponsible'))
                ->select($db->qn('pricingmanager.role_id', 'pricingmanager'))
                ->select($db->qn('validationmanager.role_id', 'validationmanager'))
                ->select($db->qn('organismmanager.role_id', 'organismmanager'))
                ->from($db->qn('#__sdi_user', 'u'))
                ->innerJoin($db->qn('#__users', 'ju') . '                                    ON (' . $db->qn('u.user_id') . ' = ' . $db->qn('ju.id') . ')')
                ->innerJoin($db->qn('#__sdi_user_role_organism', 'member') . '               ON (' . $db->qn('u.id') . '      = ' . $db->qn('member.user_id') . '                AND ' . $db->qn('member.role_id') . '                = ' . sdiUser::member . ' )')
                ->innerJoin($db->qn('#__sdi_organism', 'o') . '                              ON (' . $db->qn('o.id') . '      = ' . $db->qn('member.organism_id') . ')')
                ->leftJoin($db->qn('#__sdi_user_role_organism', 'resourcemanager') . '       ON (' . $db->qn('u.id') . '      = ' . $db->qn('resourcemanager.user_id') . '       AND ' . $db->qn('resourcemanager.role_id') . '       = ' . sdiUser::resourcemanager . '        AND ' . $db->qn('resourcemanager.organism_id') . '       = ' . (int) $id . ' )')
                ->leftJoin($db->qn('#__sdi_user_role_organism', 'metadataresponsible') . '   ON (' . $db->qn('u.id') . '      = ' . $db->qn('metadataresponsible.user_id') . '   AND ' . $db->qn('metadataresponsible.role_id') . '   = ' . sdiUser::metadataresponsible . '    AND ' . $db->qn('metadataresponsible.organism_id') . '   = ' . (int) $id . ' )')
                ->leftJoin($db->qn('#__sdi_user_role_organism', 'metadataeditor') . '        ON (' . $db->qn('u.id') . '      = ' . $db->qn('metadataeditor.user_id') . '        AND ' . $db->qn('metadataeditor.role_id') . '        = ' . sdiUser::metadataeditor . '         AND ' . $db->qn('metadataeditor.organism_id') . '        = ' . (int) $id . ' )')
                ->leftJoin($db->qn('#__sdi_user_role_organism', 'diffusionmanager') . '      ON (' . $db->qn('u.id') . '      = ' . $db->qn('diffusionmanager.user_id') . '      AND ' . $db->qn('diffusionmanager.role_id') . '      = ' . sdiUser::diffusionmanager . '       AND ' . $db->qn('diffusionmanager.organism_id') . '      = ' . (int) $id . ' )')
                ->leftJoin($db->qn('#__sdi_user_role_organism', 'previewmanager') . '        ON (' . $db->qn('u.id') . '      = ' . $db->qn('previewmanager.user_id') . '        AND ' . $db->qn('previewmanager.role_id') . '        = ' . sdiUser::viewmanager . '            AND ' . $db->qn('previewmanager.organism_id') . '        = ' . (int) $id . ' )')
                ->leftJoin($db->qn('#__sdi_user_role_organism', 'extractionresponsible') . ' ON (' . $db->qn('u.id') . '      = ' . $db->qn('extractionresponsible.user_id') . ' AND ' . $db->qn('extractionresponsible.role_id') . ' = ' . sdiUser::extractionresponsible . '  AND ' . $db->qn('extractionresponsible.organism_id') . ' = ' . (int) $id . ' )')
                ->leftJoin($db->qn('#__sdi_user_role_organism', 'pricingmanager') . '        ON (' . $db->qn('u.id') . '      = ' . $db->qn('pricingmanager.user_id') . '        AND ' . $db->qn('pricingmanager.role_id') . '        = ' . sdiUser::pricingmanager . '         AND ' . $db->qn('pricingmanager.organism_id') . '        = ' . (int) $id . ' )')
                ->leftJoin($db->qn('#__sdi_user_role_organism', 'validationmanager') . '     ON (' . $db->qn('u.id') . '      = ' . $db->qn('validationmanager.user_id') . '     AND ' . $db->qn('validationmanager.role_id') . '     = ' . sdiUser::validationmanager . '      AND ' . $db->qn('validationmanager.organism_id') . '     = ' . (int) $id . ' )')
                ->leftJoin($db->qn('#__sdi_user_role_organism', 'organismmanager') . '       ON (' . $db->qn('u.id') . '      = ' . $db->qn('organismmanager.user_id') . '       AND ' . $db->qn('organismmanager.role_id') . '       = ' . sdiUser::organismmanager . '        AND ' . $db->qn('organismmanager.organism_id') . '       = ' . (int) $id . ' )')
                ->where($db->qn('member.organism_id') . '                = ' . (int) $id, 'OR')
                ->where($db->qn('resourcemanager.organism_id') . '       = ' . (int) $id, 'OR')
                ->where($db->qn('metadataresponsible.organism_id') . '   = ' . (int) $id, 'OR')
                ->where($db->qn('metadataeditor.organism_id') . '        = ' . (int) $id, 'OR')
                ->where($db->qn('diffusionmanager.organism_id') . '      = ' . (int) $id, 'OR')
                ->where($db->qn('previewmanager.organism_id') . '        = ' . (int) $id, 'OR')
                ->where($db->qn('extractionresponsible.organism_id') . ' = ' . (int) $id, 'OR')
                ->where($db->qn('pricingmanager.organism_id') . '        = ' . (int) $id, 'OR')
                ->where($db->qn('validationmanager.organism_id') . '     = ' . (int) $id, 'OR')
                ->where($db->qn('organismmanager.organism_id') . '       = ' . (int) $id, 'OR');
        $db->setQuery($query);
        $userAndRoles = $db->loadObjectList();
        return $userAndRoles;
    }

}

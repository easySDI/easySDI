<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Easysdi_shop records.
 */
class Easysdi_shopModelorders extends JModelList {

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
                'created', 'a.created',
                'name', 'a.name',
                'completed', 'a.completed',
                'user', 'user',
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

        // Load the filters states.
        foreach (
        array(
            'search',
            'state',
            'ordertype',
            'orderstate',
            'orderuser',
            'orderuserorganism',
            'orderprovider',
            'orderdiffusion',
            'ordersent',
            'ordercompleted',
            'orderarchived'
        ) as $key) {
            $state = $app->getUserStateFromRequest($this->context . '.filter.' . $key, 'filter_' . $key, '', 'string');
            $this->setState('filter.' . $key, $state);
        }

        //ordering by default : ID
        $ordering = $app->input->get('filter_order', 'a.id');
        if (!in_array($ordering, $this->filter_fields)) {
            $ordering = 'a.id';
        }

        //direction, by default : DESC
        $direction = $app->input->get('filter_order_Dir', 'DESC');
        if (!in_array(strtoupper($direction), array('ASC', 'DESC', ''))) {
            $direction = 'DESC';
        }

        // Load the parameters.
        $params = JComponentHelper::getParams('com_easysdi_shop');
        $this->setState('params', $params);

        // List state information.
        parent::populateState($ordering, $direction);
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
        $id.= ':' . $this->getState('filter.ordertype');

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
                $this->getState('DISTINCT ' .
                        'list.select', ' a.id,a.guid,a.ordering,a.name,a.alias,a.ordertype_id,a.orderstate_id,a.archived,a.user_id,a.sent,a.completed,a.created_by,a.created'
                )
        );

        $query->from('#__sdi_order AS a');

        // Join over the users for the checked out user.
        $query->select('uc.name AS editor');
        $query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

        // Join over the user field 'user'
        $query->select($db->quoteName('users2.name', 'user'))
                ->select($db->quoteName('users2.username', 'username'))
                ->innerJoin('#__sdi_user AS sdi_user ON sdi_user.id = a.user_id')
                ->innerJoin('#__users    AS users2   ON users2.id   = sdi_user.user_id');

        // Join over the orderstate field 'orderstate'
        $query->select('orderstate.value AS orderstate')
                ->innerJoin('#__sdi_sys_orderstate AS orderstate ON orderstate.id = a.orderstate_id');

        $query->group('orderstate.value');

        // Join over the ordertype field 'ordertype'
        $query->select('ordertype.value AS ordertype')
                ->innerJoin('#__sdi_sys_ordertype AS ordertype ON ordertype.id = a.ordertype_id');

        $query->group('ordertype.value');
        // Filter by ordertype type
        $ordertype = $this->getState('filter.ordertype');
        if (is_numeric($ordertype)) {
            $query->where('a.ordertype_id = ' . (int) $ordertype);
        }

        // Filter by orderstate state
        $orderstate = $this->getState('filter.orderstate');
        if (is_numeric($orderstate)) {
            $query->where('a.orderstate_id = ' . (int) $orderstate);
        }

        // Filter by order archived state
        $orderarchived = $this->getState('filter.orderarchived');
        if (is_numeric($orderarchived)) {
            $query->where('a.archived = ' . (int) $orderarchived);
        }

        // Filter by order user
        $orderuser = $this->getState('filter.orderuser');
        if (is_numeric($orderuser)) {
            $query->where('a.user_id = ' . (int) $orderuser);
        }

        // Filter by order user's organism
        $orderuserorganism = $this->getState('filter.orderuserorganism');
        if (is_numeric($orderuserorganism)) {
            $query->innerJoin('#__sdi_user_role_organism AS uro ON sdi_user.id = uro.user_id AND uro.role_id = 1');
            $query->where('uro.organism_id = ' . (int) $orderuserorganism);
        }

        // Filter by orderprovider state
        $orderprovider = $this->getState('filter.orderprovider');
        if (is_numeric($orderprovider)) {
            $query
                    ->innerJoin('#__sdi_order_diffusion AS order_diffusion2 ON order_diffusion2.order_id = a.id')
                    ->innerJoin('#__sdi_diffusion       AS diffusion2       ON diffusion2.id             = order_diffusion2.diffusion_id')
                    ->innerJoin('#__sdi_version         AS version2         ON version2.id               = diffusion2.version_id')
                    ->innerJoin('#__sdi_resource        AS resource2        ON resource2.id              = version2.resource_id')
                    ->where('resource2.organism_id = ' . (int) $orderprovider);
        }

        // Filter by orderdiffusion state
        $orderdiffusion = $this->getState('filter.orderdiffusion');
        if (is_numeric($orderdiffusion)) {
            $query
                    ->innerJoin('#__sdi_order_diffusion AS order_diffusion3 ON order_diffusion3.order_id =a.id')
                    ->where('order_diffusion3.diffusion_id = ' . (int) $orderdiffusion);
        }

        // Filter by ordersent state
        $ordersent = $this->getState('filter.ordersent');
        if ($ordersent !== '') {
            // Get UTC for now.
            $dNow = new JDate;
            $dStart = clone $dNow;

            switch ($ordersent) {
                case 'past_week':
                    $dStart->modify('-7 day');
                    break;

                case 'past_1month':
                    $dStart->modify('-1 month');
                    break;

                case 'past_3month':
                    $dStart->modify('-3 month');
                    break;

                case 'past_6month':
                    $dStart->modify('-6 month');
                    break;

                case 'post_year':
                case 'past_year':
                    $dStart->modify('-1 year');
                    break;

                case 'today':
                    // Ranges that need to align with local 'days' need special treatment.
                    $app = JFactory::getApplication();
                    $offset = $app->getCfg('offset');

                    // Reset the start time to be the beginning of today, local time.
                    $dStart = new JDate('now', $offset);
                    $dStart->setTime(0, 0, 0);

                    // Now change the timezone back to UTC.
                    $tz = new DateTimeZone('GMT');
                    $dStart->setTimezone($tz);
                    break;
            }

            if ($ordersent == 'post_year') {
                $query->where(
                        'a.sent < ' . $db->quote($dStart->format('Y-m-d H:i:s'))
                );
            } else {
                $query->where(
                        'a.sent >= ' . $db->quote($dStart->format('Y-m-d H:i:s')) .
                        ' AND a.sent <=' . $db->quote($dNow->format('Y-m-d H:i:s'))
                );
            }
        }

        // Filter by ordercompleted state
        $ordercompleted = $this->getState('filter.ordercompleted');
        if ($ordercompleted !== '') {
            // Get UTC for now.
            $dNow = new JDate;
            $dStart = clone $dNow;

            switch ($ordercompleted) {
                case 'past_week':
                    $dStart->modify('-7 day');
                    break;

                case 'past_1month':
                    $dStart->modify('-1 month');
                    break;

                case 'past_3month':
                    $dStart->modify('-3 month');
                    break;

                case 'past_6month':
                    $dStart->modify('-6 month');
                    break;

                case 'post_year':
                case 'past_year':
                    $dStart->modify('-1 year');
                    break;

                case 'today':
                    // Ranges that need to align with local 'days' need special treatment.
                    $app = JFactory::getApplication();
                    $offset = $app->getCfg('offset');

                    // Reset the start time to be the beginning of today, local time.
                    $dStart = new JDate('now', $offset);
                    $dStart->setTime(0, 0, 0);

                    // Now change the timezone back to UTC.
                    $tz = new DateTimeZone('GMT');
                    $dStart->setTimezone($tz);
                    break;
            }

            if ($ordercompleted == 'post_year') {
                $query->where(
                        'a.completed < ' . $db->quote($dStart->format('Y-m-d H:i:s'))
                );
            } else {
                $query->where(
                        'a.completed >= ' . $db->quote($dStart->format('Y-m-d H:i:s')) .
                        ' AND a.completed <=' . $db->quote($dNow->format('Y-m-d H:i:s'))
                );
            }
        }

        // Filter by search in title
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            $searchOnId = '';
            if (is_numeric($search)) {
                $searchOnId = ' OR (a.id = ' . (int) $search . ')';
            }
            $search = $db->Quote('%' . $db->escape($search, true) . '%');
            $query->where('(( a.name LIKE ' . $search . ' ) ' . $searchOnId . ' )');
        }

        // Add the list ordering clause.
        $orderCol = $this->state->get('list.ordering');
        $orderDirn = $this->state->get('list.direction');
        if ($orderCol && $orderDirn) {
            $query->order($db->escape($orderCol . ' ' . $orderDirn));
        }

        //group by order_id
        $query->group('a.id');
        $query->group('a.guid');
        $query->group('a.alias');
        $query->group('a.created_by');
        $query->group('a.created');
        $query->group('a.name');
        $query->group('users2.name');
        $query->group('uc.name');
        $query->group('users2.username');

        return $query;
    }

    public function getItems() {
        return parent::getItems();
    }

    /**
     * get array of order types
     * @return array [array('id'=>id,'value'=>value)]
     */
    public function getOrderTypes() {
        // Load the list items.
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select('o.id as id, o.value as value');
        $query->from('#__sdi_sys_ordertype AS o');
        $query->where('o.state = 1');
        $query->order('o.ordering');
        $query->group('o.ordering');
        $query->group('o.id');
        $query->group('o.value');

        try {
            $items = $this->_getList($query);
        } catch (RuntimeException $e) {
            $this->setError($e->getMessage());
            return false;
        }
        return $items;
    }

    /**
     * get array of order states
     * @return array [array('id'=>id,'value'=>value)]
     */
    public function getOrderStates() {
        // Load the list items.
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select('o.id as id, o.value as value');
        $query->from('#__sdi_sys_orderstate AS o');
        $query->where('o.state = 1');
        $query->order('o.ordering');
        $query->group('o.ordering');
        $query->group('o.id');
        $query->group('o.value');

        try {
            $items = $this->_getList($query);
        } catch (RuntimeException $e) {
            $this->setError($e->getMessage());
            return false;
        }
        return $items;
    }

    /**
     * get array of order archived status
     * @return array [array('id'=>id,'value'=>value)]
     */
    public function getOrderArchived() {
        return array(array('id' => 0, 'value' => 'active'), array('id' => 1, 'value' => 'archived'));
    }

    /**
     * get array of users
     * @return array [array('id'=>id,'name'=>name)]
     */
    public function getOrderUsers() {
        // Load the list items.
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select('o.user_id as id, users.name as name');
        $query->from('#__sdi_order AS o');
        $query->innerJoin('#__sdi_user AS sdi_user ON sdi_user.id = o.user_id');
        $query->innerJoin('#__users    AS users ON users.id = sdi_user.user_id');
        $query->order('users.name');
        $query->group('users.name');
        $query->group('o.user_id');


        try {
            $items = $this->_getList($query);
        } catch (RuntimeException $e) {
            $this->setError($e->getMessage());
            return false;
        }
        return $items;
    }

    /**
     * get array of users
     * @return array [array('id'=>id,'name'=>name)]
     */
    public function getOrderUsersOrganisms() {
        // Load the list items.
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select('org.id as id, org.name as name');
        $query->from('#__sdi_order AS o');
        $query->innerJoin('#__sdi_user AS sdi_user ON sdi_user.id = o.user_id');
        $query->innerJoin('#__sdi_user_role_organism AS uro ON sdi_user.id = uro.user_id AND uro.role_id = 1');
        $query->innerJoin('#__sdi_organism AS org ON org.id = uro.organism_id');
        $query->order('org.name');
        $query->group('org.id');
        $query->group('org.name');

        try {
            $items = $this->_getList($query);
        } catch (RuntimeException $e) {
            $this->setError($e->getMessage());
            return false;
        }
        return $items;
    }

    /**
     * get array of users
     * @return array [array('id'=>id,'name'=>name)]
     */
    public function getOrderProviders() {
        // Load the list items.
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select('distinct organism.id as id, organism.name as name')
                ->from('#__sdi_order AS o')
                ->innerJoin('#__sdi_order_diffusion AS order_diffusion ON order_diffusion.order_id =o.id')
                ->innerJoin('#__sdi_diffusion       AS diffusion       ON diffusion.id=order_diffusion.diffusion_id')
                ->innerJoin('#__sdi_version         AS vers            ON vers.id = diffusion.version_id')
                ->innerJoin('#__sdi_resource        AS resource        ON resource.id=vers.resource_id')
                ->innerJoin('#__sdi_organism        AS organism        ON organism.id=resource.organism_id')
                ->group('organism.id')
                ->group('organism.name')
                ->order('organism.name');

        try {
            $items = $this->_getList($query);
        } catch (RuntimeException $e) {
            var_dump($query);
            $this->setError($e->getMessage());
            return false;
        }

        return $items;
    }

    /**
     * get array of diffusuin names
     * @return array [array('id'=>id,'name'=>name)]
     */
    public function getOrderDiffusion() {
        // Load the list items.
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select('d.id as id, d.name as name');
        $query->from('#__sdi_diffusion AS d');
        $query->innerJoin('#__sdi_order_diffusion AS sdi_order_diffusion ON sdi_order_diffusion.diffusion_id = d.id');
        $query->order('d.name');
        $query->group('d.id');
        $query->group('d.name');

        try {
            $items = $this->_getList($query);
        } catch (RuntimeException $e) {
            $this->setError($e->getMessage());
            return false;
        }

        return $items;
    }

}

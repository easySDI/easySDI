<?php
/**
* @version     4.4.5
* @package     com_easysdi_processing
* @copyright   Copyright (C) 2013-2017. All rights reserved.
* @license     GNU General Public License version 3 or later; see LICENSE.txt
* @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
*/
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_processing/helpers/easysdi_processing_status.php';

/**
 * Methods supporting a list of Easysdi_processing records.
 */
class Easysdi_processingModelorders extends JModelList {

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
                'name', 'a.name',
                'processing_id', 'a.processing_id',
                'parameters', 'a.parameters',
                'input', 'a.input',
                'output', 'a.output',
                'exec_pid', 'a.exec_pid', 
                'exec_info', 'a.exec_info', 
                'status', 'a.status', 
                'info', 'a.info', 
                'created_by', 'a.created_by', 
                'created', 'a.created', 
                'publish_at', 'a.publish_at'
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
        foreach (array(
            'search',
            'ordercompleted',
            'ordersent',
            'orderuser',
            'orderstatus',
            'orderprocessing'
            /*'ordertype',
            'orderstate',
            'orderuser',
            'orderprovider',
            'orderdiffusion',
            'ordersent',
            'ordercompleted'*/
        ) as $key) {
            $state = $app->getUserStateFromRequest($this->context . '.filter.' . $key, 'filter_' . $key, '', 'string');
            $this->setState('filter.' . $key, $state);
        }


        // Load the parameters.
        $params = JComponentHelper::getParams('com_easysdi_processing');
        $this->setState('params', $params);

        // List state information.
        parent::populateState('a.created_by', 'desc');
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
        $id.= ':' . $this->getState('filter.status');
        //$id.= ':' . $this->getState('filter.ordertype');

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
                        'list.select', ' a.*'
                )
        );
        $query->from('#__sdi_processing_order AS a');



        // Join over the user field 'user'
        $query->select($db->quoteName('users2.name', 'user'))
                ->join('LEFT', '#__sdi_user AS sdi_user ON sdi_user.id=a.created_by')
                ->join('LEFT', '#__users AS users2 ON users2.id=sdi_user.created_by');
        
        //Join over processing field 'processing_id'
        $query->select($db->quoteName('p.name', 'processing'))
                ->join('LEFT', '#__sdi_processing AS p ON p.id=a.processing_id');
       

        // Filter by ordertype processing
        $ordertype = $this->getState('filter.orderprocessing');
        if (is_numeric($ordertype)) {
            $query->where('a.processing_id = ' . (int) $ordertype);
        }

        // Filter by orderstate state
        $orderstate = $this->getState('filter.orderstatus');
        if ($orderstate) {
            $query->where("a.status = '" . $orderstate ."'");
        }

        // Filter by orderstate state
        $orderuser = $this->getState('filter.orderuser');
        if (is_numeric($orderuser)) {
            $query->where('a.created_by = ' . (int) $orderuser);
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
                        'a.created < ' . $db->quote($dStart->format('Y-m-d H:i:s'))
                );
            } else {
                $query->where(
                        'a.created >= ' . $db->quote($dStart->format('Y-m-d H:i:s')) .
                        ' AND a.created <=' . $db->quote($dNow->format('Y-m-d H:i:s'))
                );
            }
        } // end ($ordersent!=='')
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
                        'a.sent < ' . $db->quote($dStart->format('Y-m-d H:i:s'))
                );
            } else {
                $query->where(
                        'a.sent >= ' . $db->quote($dStart->format('Y-m-d H:i:s')) .
                        ' AND a.sent <=' . $db->quote($dNow->format('Y-m-d H:i:s'))
                );
            }
        } // end ($ordersent!=='')
        // Filter by search in title
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            $searchOnId = '';
            if (is_numeric($search)) {
                $searchOnId = ' OR (a.id = ' . (int) $search . ')';
            }
            $search = $db->Quote('%' . $db->escape($search, true) . '%');
            $query->where('(( a.name LIKE ' . $search . ' ) '.$searchOnId. ' )');
        }




        // Add the list ordering clause.
        $orderCol = $this->state->get('list.ordering');
        $orderDirn = $this->state->get('list.direction');
        if ($orderCol && $orderDirn) {
            $query->order($db->escape($orderCol . ' ' . $orderDirn));
        }

        return $query;
    }

    /*public function getItems() {
        $items = parent::getItems();

        $products = array();

        foreach ($items as $item) {
            $item->products_array = array();
            $products[$item->id] = $item;
        }

        foreach ($items as $item) {
            $products[$item->id]->products_array[] = $item->product;
        }

        foreach ($products as $product) {
            $product->products = implode('</br>' . PHP_EOL, $product->products_array);
        }

        return $products;
    }*/

    /**
     * get array of order types
     * @return array [array('id'=>id,'value'=>value)]
     */
    public function getProcessings() {
        // Load the list items.
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select('p.id as id, p.name as value');
        $query->from('#__sdi_processing AS p');
        $query->where('p.state = 1');
        $query->order('p.ordering');

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
    public function getOrderStatus() {
        return  Easysdi_processingStatusHelper::getStatus();
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
        $query->select('distinct o.created as id, #__users.name as name');
        $query->from('#__sdi_processing_order AS o');
        $query->join('LEFT', '#__sdi_user AS sdi_user ON sdi_user.id = o.created_by');
        $query->join('LEFT', '#__users AS #__users ON #__users.id = sdi_user.user_id');

        $query->where('sdi_user.state = 1');
        $query->order('#__users.name');


        try {
            $items = $this->_getList($query);
        } catch (RuntimeException $e) {
            $this->setError($e->getMessage());
            return false;
        }
        return $items;
    }
}

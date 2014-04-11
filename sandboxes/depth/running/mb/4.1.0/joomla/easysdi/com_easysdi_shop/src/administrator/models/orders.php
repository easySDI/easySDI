<?php

/**
 * @version     4.0.0
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
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
               /* 'id', 'a.id',
                'guid', 'a.guid',
                'alias', 'a.alias',
                'created_by', 'a.created_by',*/
                'created', 'a.created',/*
                'modified_by', 'a.modified_by',
                'modified', 'a.modified',
                'ordering', 'a.ordering',
                'state', 'a.state',*/
                'name', 'a.name',
               /* 'access', 'a.access',
                'asset_id', 'a.asset_id',
                'ordertype_id', 'a.ordertype_id',
                'orderstate_id', 'a.orderstate_id',
                'user_id', 'a.user_id',
                'thirdparty_id', 'a.thirdparty_id',
                'buffer', 'a.buffer',
                'surface', 'a.surface',
                'remark', 'a.remark',
                'sent', 'a.sent',*/
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
        foreach (array(
            'search',
            'state',
            'ordertype',
            'orderstate',
            'orderuser',
            'orderprovider',
            'orderdiffusion',
            'ordersent',
            'ordercompleted'
        ) as $key) {
            $state = $app->getUserStateFromRequest($this->context . '.filter.'.$key, 'filter_'.$key, '', 'string');
            $this->setState('filter.'.$key, $state);
        }


        // Load the parameters.
        $params = JComponentHelper::getParams('com_easysdi_shop');
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
                $this->getState(
                        'list.select', 'a.*'
                )
        );
        $query->from('`#__sdi_order` AS a');


        // Join over the users for the checked out user.
        $query->select('uc.name AS editor');
        $query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');



        // Join over the user field 'user'
        $query->select('users2.name AS user')
        ->join('LEFT', '#__sdi_user AS sdi_user ON sdi_user.id=a.user_id')
        ->join('LEFT', '#__users AS users2 ON users2.id=sdi_user.user_id');

        // Join over the orderstate field 'orderstate'
        $query->select('orderstate.value AS orderstate')
        ->join('LEFT', '#__sdi_sys_orderstate AS orderstate ON orderstate.id = a.orderstate_id');

        // Join over the ordertype field 'ordertype'
        $query->select('ordertype.value AS ordertype')
        ->join('LEFT', '#__sdi_sys_ordertype AS ordertype ON ordertype.id = a.ordertype_id');

        // Join over the diffusion field 'products'
        $query->select("diffusion.name AS products")
        ->join('LEFT', '#__sdi_order_diffusion AS order_diffusion ON order_diffusion.order_id =a.id')
        ->join('LEFT', '#__sdi_diffusion AS diffusion ON diffusion.id=order_diffusion.diffusion_id')
        ->group('a.id');

        // product with provider
        /*$query->select("GROUP_CONCAT(CONCAT(organism.name,' - ',diffusion.name) SEPARATOR '<br/>".PHP_EOL."') AS products")
        ->join('LEFT', '#__sdi_order_diffusion AS order_diffusion ON order_diffusion.order_id =a.id')
        ->join('LEFT', '#__sdi_diffusion AS diffusion ON diffusion.id=order_diffusion.diffusion_id')
        ->join('LEFT', '#__sdi_resource AS resource ON resource.id=diffusion.version_id')
        ->join('LEFT', '#__sdi_organism AS organism ON organism.id=resource.organism_id')
        ->group('a.id');*/

    // Filter by published state
    $published = $this->getState('filter.state');
    if (is_numeric($published)) {
        $query->where('a.state = '.(int) $published);
    } else if ($published === '') {
        $query->where('(a.state IN (0, 1))');
    }



    // Filter by ordertype state
    $ordertype = $this->getState('filter.ordertype');
     if (is_numeric($ordertype)) {
         $query->where('a.ordertype_id = '.(int) $ordertype);
     }

     // Filter by orderstate state
     $orderstate = $this->getState('filter.orderstate');
      if (is_numeric($orderstate)) {
          $query->where('a.orderstate_id = '.(int) $orderstate);
      }

      // Filter by orderstate state
      $orderuser = $this->getState('filter.orderuser');
       if (is_numeric($orderuser)) {
           $query->where('a.user_id = '.(int) $orderuser);
       }

       // Filter by orderprovider state
       $orderprovider = $this->getState('filter.orderprovider');
        if (is_numeric($orderprovider)) {
         //   $query->where('a.provider_id = '.(int) $orderprovider); !TODO
         $query
         ->join('LEFT', '#__sdi_order_diffusion AS order_diffusion2 ON order_diffusion2.order_id =a.id')
         ->join('LEFT', '#__sdi_diffusion AS diffusion2 ON diffusion2.id=order_diffusion2.diffusion_id')
         ->join('LEFT', '#__sdi_resource AS resource2 ON resource2.id=diffusion2.version_id')
         ->where('resource2.organism_id = '.(int) $orderprovider);
        }

        // Filter by orderdiffusion state
        $orderdiffusion = $this->getState('filter.orderdiffusion');
         if (is_numeric($orderdiffusion)) {
             $query
             ->join('LEFT', '#__sdi_order_diffusion AS order_diffusion2 ON order_diffusion2.order_id =a.id')
             ->where('order_diffusion2.diffusion_id = '.(int) $orderdiffusion);
        }


        // Filter by ordersent state
        $ordersent = $this->getState('filter.ordersent');
        if ($ordersent!=='')
        {
            // Get UTC for now.
            $dNow = new JDate;
            $dStart = clone $dNow;

            switch ($ordersent)
            {
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

            if ($ordersent == 'post_year')
            {
                $query->where(
                    'a.created < ' . $db->quote($dStart->format('Y-m-d H:i:s'))
                );
            }
            else
            {
                $query->where(
                    'a.created >= ' . $db->quote($dStart->format('Y-m-d H:i:s')) .
                        ' AND a.created <=' . $db->quote($dNow->format('Y-m-d H:i:s'))
                );
            }

        } // end ($ordersent!=='')




        // Filter by ordercompleted state
        $ordercompleted = $this->getState('filter.ordercompleted');
        if ($ordercompleted!=='')
        {
            // Get UTC for now.
            $dNow = new JDate;
            $dStart = clone $dNow;

            switch ($ordercompleted)
            {
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

            if ($ordercompleted == 'post_year')
            {
                $query->where(
                    'a.created < ' . $db->quote($dStart->format('Y-m-d H:i:s'))
                );
            }
            else
            {
                $query->where(
                    'a.completed >= ' . $db->quote($dStart->format('Y-m-d H:i:s')) .
                        ' AND a.completed <=' . $db->quote($dNow->format('Y-m-d H:i:s'))
                );
            }

        } // end ($ordersent!=='')


        // Filter by search in title
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int) substr($search, 3));
            } else {
                $search = $db->Quote('%' . $db->escape($search, true) . '%');
                $query->where('( a.name LIKE '.$search.' )');
            }
        }




        // Add the list ordering clause.
        $orderCol = $this->state->get('list.ordering');
        $orderDirn = $this->state->get('list.direction');
        if ($orderCol && $orderDirn) {
            $query->order($db->escape($orderCol . ' ' . $orderDirn));
        }

        return $query;


    }

    public function getItems() {
        $items = parent::getItems();

        return $items;
    }


    /**
     * get array of order types
     * @return array [array('id'=>id,'value'=>value)]
     */
    public function getOrderTypes()
    {
        // Load the list items.
        $db     = $this->getDbo();
        $query  = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select('o.id as id, o.value as value');
        $query->from('`#__sdi_sys_ordertype` AS o');
        $query->where('o.state = 1');
        $query->order('o.ordering');

        try
        {
            $items = $this->_getList($query);
        }
        catch (RuntimeException $e)
        {
            $this->setError($e->getMessage());
            return false;
        }
        return $items;
    }



    /**
     * get array of order states
     * @return array [array('id'=>id,'value'=>value)]
     */
    public function getOrderStates()
    {
        // Load the list items.
        $db     = $this->getDbo();
        $query  = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select('o.id as id, o.value as value');
        $query->from('`#__sdi_sys_orderstate` AS o');
        $query->where('o.state = 1');
        $query->order('o.ordering');

        try
        {
            $items = $this->_getList($query);
        }
        catch (RuntimeException $e)
        {
            $this->setError($e->getMessage());
            return false;
        }
        return $items;
    }


    /**
     * get array of users
     * @return array [array('id'=>id,'name'=>name)]
     */
    public function getOrderUsers()
    {
        // Load the list items.
        $db     = $this->getDbo();
        $query  = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select('distinct o.user_id as id, jos_users.name as name');
        $query->from('`#__sdi_order` AS o');
        $query->join('LEFT', '#__sdi_user AS sdi_user ON sdi_user.id = o.user_id');
        $query->join('LEFT', '#__users AS jos_users ON jos_users.id = sdi_user.user_id');

        $query->where('sdi_user.state = 1');
        $query->order('jos_users.name');


        try
        {
            $items = $this->_getList($query);
        }
        catch (RuntimeException $e)
        {
            $this->setError($e->getMessage());
            return false;
        }
        return $items;
    }


    /**
     * get array of users
     * @return array [array('id'=>id,'name'=>name)]
     */
    public function getOrderProviders()
    {
        // Load the list items.
        $db     = $this->getDbo();
        $query  = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select('distinct organism.id as id, organism.name as name')
        ->from('`#__sdi_order` AS o')
        ->join('LEFT', '#__sdi_order_diffusion AS order_diffusion ON order_diffusion.order_id =o.id')
        ->join('LEFT', '#__sdi_diffusion AS diffusion ON diffusion.id=order_diffusion.diffusion_id')
        ->join('LEFT', '#__sdi_resource AS resource ON resource.id=diffusion.version_id')
        ->join('LEFT', '#__sdi_organism AS organism ON organism.id=resource.organism_id')
        ->group('organism.id');


        try
        {
            $items = $this->_getList($query);
        }
        catch (RuntimeException $e)
        {
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
    public function getOrderDiffusion()
    {
        // Load the list items.
        $db     = $this->getDbo();
        $query  = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select('distinct d.id as id, d.name as name');
        $query->from('`#__sdi_diffusion` AS d');
        $query->innerJoin('#__sdi_order_diffusion AS sdi_order_diffusion ON sdi_order_diffusion.diffusion_id = d.id');
        $query->innerJoin('#__sdi_order AS o ON o.id = sdi_order_diffusion.order_id');
        $query->order('d.name');

        try
        {
            $items = $this->_getList($query);
        }
        catch (RuntimeException $e)
        {
            $this->setError($e->getMessage());
            return false;
        }

        return $items;
    }


}

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
require_once JPATH_SITE . '/components/com_easysdi_shop/helpers/easysdi_shop.php';

/**
 * Methods supporting a list of Easysdi_shop records.
 */
class Easysdi_shopModelRequests extends JModelList {

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

        // Load the filter state.
        $search = $app->getUserStateFromRequest($this->context . '.filter.organism', 'filter_organism');
        $this->setState('filter.organism', $search);

        $search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $search = $app->getUserStateFromRequest($this->context . '.filter.type', 'filter_type');
        $this->setState('filter.type', $search);

        $search = $app->getUserStateFromRequest($this->context . '.filter.status', 'filter_status');
        if (is_null($search)) {
            $this->setState('filter.status', '1');
        } else {
            $this->setState('filter.status', $search);
        }

        //"advanced" filters, for historic requests
        $search = $app->getUserStateFromRequest($this->context . '.filter.clientorganism', 'filter_clientorganism');
        $this->setState('filter.clientorganism', $search);

        $search = $app->getUserStateFromRequest($this->context . '.filter.sentfrom', 'filter_sentfrom');
        $this->setState('filter.sentfrom', $search);

        $search = $app->getUserStateFromRequest($this->context . '.filter.sentto', 'filter_sentto');
        $this->setState('filter.sentto', $search);

        // List state information
        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
        $this->setState('list.limit', $limit);

        $limitstart = JFactory::getApplication()->input->getInt('limitstart', 0);
        $this->setState('list.start', $limitstart);


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
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
                $this->getState(
                        'list.select', 'a.*'
                )
        );



        $query->from('#__sdi_order AS a');

        // Join over the users for the checked out user.
        $query->select('uc.name AS editor');
        $query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

        //Join over the order state value
        $query->select('state.value AS orderstate');
        $query->innerjoin('#__sdi_sys_orderstate AS state ON state.id = a.orderstate_id');

        //Join over the order type value
        $query->select('type.value AS ordertype');
        $query->innerjoin('#__sdi_sys_ordertype AS type ON type.id = a.ordertype_id');

        //get client and client's organism
        $query->select('juclient.name AS clientname');
        $query->select('oclient.name AS organismname');
        $query->innerjoin('#__sdi_user AS uclient ON uclient.id = a.user_id');
        $query->innerjoin('#__users AS juclient ON juclient.id = uclient.user_id');
        $query->innerjoin("#__sdi_user_role_organism uro ON uro.user_id=uclient.id");
        $query->innerjoin("#__sdi_organism oclient ON oclient.id = uro.organism_id");
        $query->where('uro.role_id = ' . Easysdi_shopHelper::ROLE_MEMBER);


        // Filter by type
        $type = $this->getState('filter.type');
        if (is_numeric($type)) {
            $query->where('a.ordertype_id = ' . (int) $type);
        } else {
            $query->where('a.ordertype_id <> 3 ');
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

        //Only order that the current user has something to do with
        $user = sdiFactory::getSdiUser();
        $diffusions = $user->getResponsibleExtraction();
        if (!is_array($diffusions) || count($diffusions) == 0) {
            $diffusions = array(-1);
        }
        $managedOrganisms = $user->getOrganisms(array(sdiUser::organismmanager), true);
        if (count($managedOrganisms) == 0) {
            $managedOrganisms = array(-1);
        }
        $query->select('COUNT(od.id) AS productcount');
        $query->innerjoin('#__sdi_order_diffusion od ON od.order_id = a.id')
                ->innerjoin('#__sdi_diffusion d ON d.id = od.diffusion_id')
                ->innerJoin('#__sdi_version v ON v.id=d.version_id')
                ->innerJoin('#__sdi_resource r ON r.id=v.resource_id')
                ->where('(d.id IN (' . implode(',', $diffusions) . ') OR r.organism_id IN (' . implode(',', $managedOrganisms) . '))');

        // Filter by provder organism
        $organism = $this->getState('filter.organism');
        if ($organism > 0) {
            $query->where('r.organism_id = ' . (int) $organism);
        } else {
            $query->where('a.ordertype_id <> 3 ');
        }

        // Advanced filter for "Done" requests
        $doneRequests = $this->getState('filter.status') == '0' ? true : false;
        if ($doneRequests) {
            // Filter by client's organism
            $clientorg = $this->getState('filter.clientorganism');
            if (is_numeric($clientorg)) {
                $query->where('oclient.id = ' . (int) $clientorg);
            }
            // Filter by dates (order sent date)
            $sentfrom = $this->getState('filter.sentfrom');
            if (strlen($sentfrom) > 1) {
                $query->where('a.sent >= \'' . $sentfrom . ' 00:00:00\'');
            }
            $sentto = $this->getState('filter.sentto');
            if (strlen($sentto) > 1) {
                $query->where('a.sent <= \'' . $sentto . ' 23:59:59\'');
            }
        }

        if ($doneRequests) {
            $query->where('od.productstate_id <> ' . Easysdi_shopHelper::PRODUCTSTATE_SENT);
            $query->where('od.productstate_id <> ' . Easysdi_shopHelper::PRODUCTSTATE_VALIDATION);
            $query->where('od.productstate_id <> ' . Easysdi_shopHelper::PRODUCTSTATE_AWAIT);
        } else {
            $query->where('od.productstate_id IN (' . Easysdi_shopHelper::PRODUCTSTATE_SENT . ',' . Easysdi_shopHelper::PRODUCTSTATE_AWAIT . ')');
        }

        $query->group('a.id');
        $query->group('a.guid');
        $query->group('a.alias');
        $query->group('a.created_by');
        $query->group('a.created');
        $query->group('a.modified_by');
        $query->group('a.modified');
        $query->group('a.ordering');
        $query->group('a.state');
        $query->group('a.checked_out');
        $query->group('a.checked_out_time');
        $query->group('a.name');
        $query->group('a.ordertype_id');
        $query->group('a.orderstate_id');
        $query->group('a.user_id');
        $query->group('a.thirdparty_id');
        $query->group('a.surface');
        $query->group('a.remark');
        $query->group('a.sent');
        $query->group('a.completed');
        $query->group('a.access');
        $query->group('a.asset_id');
        $query->group('a.validated_date');
        $query->group('a.validated_reason');
        $query->group('a.mandate_ref');
        $query->group('a.mandate_contact');
        $query->group('a.mandate_email');
        $query->group('a.level');
        $query->group('a.freeperimetertool');
        $query->group('a.validated');
        $query->group('a.validated_by');
        $query->group('a.usernotified');
        $query->group('a.access_token');
        $query->group('a.validation_token');
        $query->group('uc.name');
        $query->group('state.value');
        $query->group('type.value');
        $query->group('juclient.name');
        $query->group('oclient.name');

        $query->order('a.sent DESC');

        return $query;
    }

    /**
     * Build an SQL query to load the clients organisms list for filters
     *
     * @return	JDatabaseQuery
     */
    function getClientOrganismsListQuery() {
        // Create a new query object.
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $query->from('#__sdi_order AS a');

        //get client and client's organism
        $query->select('oclient.id AS id');
        $query->select('oclient.name AS name');
        $query->innerjoin('#__sdi_user AS uclient ON uclient.id = a.user_id');
        $query->innerjoin('#__users AS juclient ON juclient.id = uclient.user_id');
        $query->innerjoin("#__sdi_user_role_organism uro ON uro.user_id=uclient.id");
        $query->innerjoin("#__sdi_organism oclient ON oclient.id = uro.organism_id");
        $query->where('uro.role_id = ' . Easysdi_shopHelper::ROLE_MEMBER);

        // Filter by type
        $query->where('a.ordertype_id <> 3 ');

        //Only order that the current user has something to do with
        $user = sdiFactory::getSdiUser();
        $diffusions = $user->getResponsibleExtraction();
        if (!is_array($diffusions) || count($diffusions) == 0) {
            $diffusions = array(-1);
        }
        $managedOrganisms = $user->getOrganisms(array(sdiUser::organismmanager), true);
        if (count($managedOrganisms) == 0) {
            $managedOrganisms = array(-1);
        }
        $query->innerjoin('#__sdi_order_diffusion od ON od.order_id = a.id')
                ->innerjoin('#__sdi_diffusion d ON d.id = od.diffusion_id')
                ->innerJoin('#__sdi_version v ON v.id=d.version_id')
                ->innerJoin('#__sdi_resource r ON r.id=v.resource_id')
                ->where('(d.id IN (' . implode(',', $diffusions) . ') OR r.organism_id IN (' . implode(',', $managedOrganisms) . '))');

        $query->group('oclient.id');
        $query->group('oclient.name');
        $query->order('oclient.name');

        return $query;
    }

    function getOrderType() {
        //Load all status value
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select('t.value, t.id ')
                ->from('#__sdi_sys_ordertype t')
                ->where('t.value <> ' . $db->quote('draft'));
        $db->setQuery($query);
        return $db->loadObjectList();
    }

}

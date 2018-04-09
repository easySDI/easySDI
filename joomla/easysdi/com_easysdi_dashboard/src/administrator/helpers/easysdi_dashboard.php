<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_dashboard
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

/**
 * Easysdi_dashboard helper.
 */
class Easysdi_dashboardHelper {

    /**
     * Configure the Linkbar.
     */
    public static function addSubmenu($vName = '') {
        require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/helpers/easysdi_core.php';
        Easysdi_coreHelper::addComponentSubmeu('com_easysdi_core');
        Easysdi_coreHelper::addComponentSubmeu('com_easysdi_user');
        Easysdi_coreHelper::addComponentSubmeu('com_easysdi_catalog');
        Easysdi_coreHelper::addComponentSubmeu('com_easysdi_shop');
        Easysdi_coreHelper::addComponentSubmeu('com_easysdi_processing');
        Easysdi_coreHelper::addComponentSubmeu('com_easysdi_service');
        Easysdi_coreHelper::addComponentSubmeu('com_easysdi_map');
        Easysdi_coreHelper::addComponentSubmeu('com_easysdi_monitor');
        Easysdi_coreHelper::addComponentSubmeu('com_easysdi_dashboard');
        JHtmlSidebar::addEntry(
                Easysdi_coreHelper::getMenuSpacer() . JText::_('COM_EASYSDI_DASHBOARD_TITLE_SHOP'), 'index.php?option=com_easysdi_dashboard&view=shop', $vName == 'shop'
        );
    }

    /**
     * Gets a list of the actions that can be performed.
     *
     * @return	JObject
     */
    public static function getActions() {
        $user = JFactory::getUser();
        $result = new JObject;

        $assetName = 'com_easysdi_dashboard';

        $actions = array(
            'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
        );

        foreach ($actions as $action) {
            $result->set($action, $user->authorise($action, $assetName));
        }

        return $result;
    }

    /**
     * Check filters input sanity
     * @param string $indicator input from query
     * @param string $organism input from query
     * @param string $timestart input from query
     * @param string $timeend input from query
     * @param string $dataformat input from query
     * @param string $limit input from query
     * @return boolean
     */
    public static function checkFiltersInput($indicator, $organism, $timestart, $timeend, $dataformat, $limit) {

        $has_error = false;

        if (is_null($indicator)) {
            $has_error = true;
            JFactory::getApplication()->enqueueMessage('"indicator" parameter not set', 'error');
        } else {
            if (!preg_match('/^[\w]+$/', $indicator)) {
                $has_error = true;
                JFactory::getApplication()->enqueueMessage('"indicator" parameter format error', 'error');
            }
        }

        if (is_null($organism) || !($organism == 'all' || is_numeric($organism))) {
            $has_error = true;
            JFactory::getApplication()->enqueueMessage('"organism" parameter not set', 'error');
        }

        if (is_null($timestart)) {
            $has_error = true;
            JFactory::getApplication()->enqueueMessage('"timestart" parameter not set', 'error');
        }

        if (is_null($timeend)) {
            $has_error = true;
            JFactory::getApplication()->enqueueMessage('"timeend" parameter not set', 'error');
        }

        if (is_null($dataformat)) {
            $has_error = true;
            JFactory::getApplication()->enqueueMessage('"dataformat" parameter not set', 'error');
        }

        if (is_null($limit)) {
            $has_error = true;
            JFactory::getApplication()->enqueueMessage('"limit" parameter not set or not positive integer', 'error');
        }
        if (!JFile::exists(JPATH_ADMINISTRATOR . '/components/com_easysdi_dashboard/indicators/' . $indicator . '.php')) {
            $has_error = true;
            JFactory::getApplication()->enqueueMessage('this indicator does not exists', 'error');
        }

        return !$has_error;
    }

    /**
     * Return HTML pseudo filters that look like joomla originals
     * This is beacause, we cannot change the dynamic behavior of joomla filters
     */
    public static function getBackendFilters() {
        $pseudoFilters = '';

        $pseudoFilters .= self::getAdminOrgFilter();

        //Filter dates
        $pseudoFilters .= '<hr class="hr-condensed">';
        $pseudoFilters .= JHTML::_('select.genericlist', self::getDateFilterList(), 'filter-date', ' onchange="dateFiltersChanged();"');
        //$pseudoFilters .= '<hr class="hr-condensed">';
        //Get custom date filters
        $pseudoFilters .= '<div class="sdi-dashboard-custom-dates" style="display:none">';

        $pseudoFilters .= self::getCustomDateFilterFrom();
        $pseudoFilters .= '<br/>';

        $pseudoFilters .= self::getCustomDateFilterTo();
        $pseudoFilters .= '</div>';

        //Change calendar electric feature to false
        $document = JFactory::getDocument();
        $document->addScriptDeclaration('
        jQuery(document).ready(function(){
            jQuery("#sdi-dashboard-custom-from_img").click(function() {
                calendar.params.electric = false;
            });
            jQuery("#sdi-dashboard-custom-to_img").click(function() {
                calendar.params.electric = false;
            });
        });');


        return(
                '<hr>
                <div class="filter-select hidden-phone">
			<h4 class="page-header">' . JText::_('JSEARCH_FILTER_LABEL') . '</h4>' .
                $pseudoFilters
                . '</div>'
                );
    }

    /**
     * Return HTML filters for fron-end
     */
    public static function getFrontendFilters($sdiUser) {
        $pseudoFilters = '';

        $pseudoFilters .= '<div id="filterorganism">' . self::getFrontOrgFilter($sdiUser) . '</div>';

        //Filter dates
        $pseudoFilters .= '<div id="filtersentfrom" class="">';
        $pseudoFilters .= JHTML::_('select.genericlist', self::getDateFilterList(), 'filter-date', array('list.attr' => ' onchange="dateFiltersChanged();"'));
        $pseudoFilters .= '</div>';
        //Get custom date filters
        $pseudoFilters .= '<div id="filtersentfrom" class="sdi-dashboard-custom-dates" style="display:none">';
        $pseudoFilters .= self::getCustomDateFilterFrom();
        $pseudoFilters .= '</div>';
        $pseudoFilters .= '<div id="filtersentto" class="sdi-dashboard-custom-dates" style="display:none">';
        $pseudoFilters .= self::getCustomDateFilterTo();
        $pseudoFilters .= '</div>';

        //Change calendar electric feature to false
        $document = JFactory::getDocument();
        $document->addScriptDeclaration('
        jQuery(document).ready(function(){
            jQuery("#sdi-dashboard-custom-from_img").click(function() {
                calendar.params.electric = false;
            });
            jQuery("#sdi-dashboard-custom-to_img").click(function() {
                calendar.params.electric = false;
            });
        });');


        return $pseudoFilters;
    }

    /**
     * get organism list for back-end dashboard
     * @return a JHTML select
     */
    private static function getAdminOrgFilter() {
        $db = JFactory::getDbo();

        //Filter to select one or all organisms , need to be admin
        $canDo = Easysdi_dashboardHelper::getActions('core.admin');
        if ($canDo->get('core.admin')) {
            //All organisms with existing diffusion from db
            $query = $db->getQuery(true)
                    ->select('o.id as value, o.name as text')
                    ->from($db->quoteName('#__sdi_diffusion', 'd'))
                    ->join('INNER', $db->quoteName('#__sdi_version', 'v') . ' ON (' . $db->quoteName('d.version_id') . ' = ' . $db->quoteName('v.id') . ')')
                    ->join('INNER', $db->quoteName('#__sdi_resource', 'r') . ' ON (' . $db->quoteName('v.resource_id') . ' = ' . $db->quoteName('r.id') . ')')
                    ->join('INNER', $db->quoteName('#__sdi_organism', 'o') . ' ON (' . $db->quoteName('r.organism_id') . ' = ' . $db->quoteName('o.id') . ')')
                    ->group($db->quoteName('o.id'))
                    ->group($db->quoteName('o.name'))
                    ->order($db->quoteName('o.name'));
            $db->setQuery($query, 0);
            $orgs = $db->loadObjectList();
            //Add "All organisms" to filter
            array_unshift($orgs, (object) array('value' => 'all', 'text' => JText::_('COM_EASYSDI_DASHBOARD_FILTERS_ALL_ORGANISMS')));
            //Create select
            return JHTML::_('select.genericlist', $orgs, 'filter-organism', ' onchange="triggerFilterUpdate();"');
        }
    }

    /**
     * get organism list for front-end dashboard
     * @param sdiUser $sdiUser connected user
     * @return a JHTML select
     */
    private static function getFrontOrgFilter($sdiUser) {
        $db = JFactory::getDbo();

        $tmpOrgList = array();
        //get all orgs with rights for dashboard
        foreach (
        array(
            $sdiUser::resourcemanager,
            $sdiUser::diffusionmanager,
            $sdiUser::extractionresponsible,
            $sdiUser::organismmanager)
        as $roleId) {
            foreach ($sdiUser->role[$roleId] as $org) {
                $tmpOrgList[$org->id] = $org->name;
            }
        }
        //alpha sort
        asort($tmpOrgList);
        //member org on top if existing
        $mid = $sdiUser->role[$sdiUser::member][0]->id;
        if (isset($tmpOrgList[$mid])) {
            $tmpOrgList = array($mid => $tmpOrgList[$mid]) + $tmpOrgList;
        }


        //Create and return select
        $orgs = array();
        foreach ($tmpOrgList as $key => $value) {
            array_push($orgs, array('value' => $key, 'text' => $value));
        }
        return JHTML::_('select.genericlist', $orgs, 'filter-organism', ' onchange="triggerFilterUpdate();"');
    }

    private static function getDateFilterList() {
        $opts = array(
            array("text" => JText::_('COM_EASYSDI_DASHBOARD_TIME_LAST_WEEK'), "value" => (strtotime('monday last week 00:00:00 UTC') . ';' . strtotime('sunday last week 23:59:59 UTC'))),
            array("text" => JText::_('COM_EASYSDI_DASHBOARD_TIME_LAST_MONTH'), "value" => (strtotime('first day of last month 00:00:00 UTC') . ';' . strtotime('last day of last month 23:59:59 UTC'))),
            array("text" => JText::_('COM_EASYSDI_DASHBOARD_TIME_LAST_YEAR'), "value" => (strtotime('first day of January ' . (date('Y') - 1) . ' 00:00:00 UTC') . ';' . strtotime('last day of December ' . (date('Y') - 1) . ' 23:59:59 UTC'))),
            array("text" => JText::_('COM_EASYSDI_DASHBOARD_TIME_CURRENT_YEAR'), "value" => (strtotime('first day of January ' . date('Y') . ' 00:00:00 UTC') . ';' . time())),
            array("text" => JText::_('COM_EASYSDI_DASHBOARD_TIME_CUSTOM'), "value" => 0)
        );

        return $opts;
    }

    private static function getCustomDateFilterFrom() {
        return JHTML::_('calendar', null, 'sdi-dashboard-custom-from', 'sdi-dashboard-custom-from', '%Y-%m-%d', array('onchange' => 'triggerFilterUpdate();', 'placeholder' => JText::_('COM_EASYSDI_DASHBOARD_TIME_CUSTOM_FROM')));
    }

    private static function getCustomDateFilterTo() {
        return JHTML::_('calendar', null, 'sdi-dashboard-custom-to', 'sdi-dashboard-custom-to', '%Y-%m-%d', array('onchange' => 'triggerFilterUpdate();', 'placeholder' => JText::_('COM_EASYSDI_DASHBOARD_TIME_CUSTOM_TO')));
    }

}

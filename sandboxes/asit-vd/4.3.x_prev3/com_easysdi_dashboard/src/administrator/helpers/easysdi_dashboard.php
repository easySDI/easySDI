<?php

/**
 * @version     4.3.2
 * @package     com_easysdi_dashboard
 * @copyright   Copyright (C) 2013-2015. All rights reserved.
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
        JHtmlSidebar::addEntry(
                '<i class="icon-home"></i> ' . JText::_('COM_EASYSDI_DASHBOARD_TITLE_HOME'), 'index.php?option=com_easysdi_core&view=easysdi', $vName == 'easysdi'
        );
        JHtmlSidebar::addEntry(
                JText::_('COM_EASYSDI_DASHBOARD_TITLE_SHOP'), 'index.php?option=com_easysdi_dashboard&view=shop', $vName == 'shop'
        );
        /*JHtmlSidebar::addEntry(
                JText::_('COM_EASYSDI_DASHBOARD_TITLE_CATALOG'), 'index.php?option=com_easysdi_dashboard&view=dashboard', $vName == 'dashboard'
        );*/
    }

    public static function getActions($id = null) {
        $user = JFactory::getUser();
        $result = new JObject;

        if (empty($id)) {
            $assetName = 'com_easysdi_service';
        } else {
            $assetName = 'com_easysdi_service.virtualservice.' . (int) $id;
        }

        $actions = array(
            'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
        );

        foreach ($actions as $action) {
            $result->set($action, $user->authorise($action, $assetName));
        }

        return $result;
    }

    public static function getDateFilterList() {
        $opts = array(
            array("text" => JText::_('COM_EASYSDI_DASHBOARD_TIME_7_D'), "value" => (strtotime(date("Y-m-d", time()) . " - 7 day")) . ';' . time()),
            array("text" => JText::_('COM_EASYSDI_DASHBOARD_TIME_30_D'), "value" => (strtotime(date("Y-m-d", time()) . " - 30 day")) . ';' . time()),
            array("text" => JText::_('COM_EASYSDI_DASHBOARD_TIME_6_M'), "value" => (strtotime(date("Y-m-d", time()) . " - 6 month")) . ';' . time()),
            array("text" => JText::_('COM_EASYSDI_DASHBOARD_TIME_1_Y'), "value" => (strtotime(date("Y-m-d", time()) . " - 1 year")) . ';' . time()),
            array("text" => JText::_('COM_EASYSDI_DASHBOARD_TIME_2_Y'), "value" => (strtotime(date("Y-m-d", time()) . " - 2 year")) . ';' . time())
        );
        return $opts;
    }
    
    public static function getReportFormatList() {
        $opts = array(
            array("text" => "PDF", "value" => "pdf"),
            array("text" => "DOC (Microsoft Word 2003+)", "value" => "doc"),
            array("text" => "XLS (Microsoft Excel 2003+)", "value" => "xls")
        );
        return $opts;
    }
    
    public static function getReportLimitList() {
        $opts = array(
            array("text" => JText::_('COM_EASYSDI_DASHBOARD_REPORTING_LIMIT_ALL'), "value" => 0),
            array("text" => "10", "value" => 10),
            array("text" => "50", "value" => 50),
            array("text" => "100", "value" => 100),
            array("text" => "1000", "value" => 1000)
        );
        return $opts;
    }    

    /**
     * Return HTML pseudo filters that look like joomla originals
     * This is beacause, we cannot change the dynamic behavior of joomla filters
     */
    public static function getPseudoFilters() {
        $pseudoFilters = '';

        $db = JFactory::getDbo();

        //Filter to select one or all organisms , need to be admin
        $canDo = Easysdi_dashboardHelper::getActions('core.admin');
        if ($canDo->get('core.admin')) {
            //All organisms with existing diffusion from db
            $query = $db->getQuery(true)
                    ->select('o.id as value, o.name as text')
                    ->from($db->quoteName('#__sdi_diffusion', 'd'))
                    ->join('INNER', $db->quoteName('#__sdi_version','v') .  ' ON (' . $db->quoteName('d.version_id') . ' = ' . $db->quoteName('v.id') . ')')
                    ->join('INNER', $db->quoteName('#__sdi_resource','r') .  ' ON (' . $db->quoteName('v.resource_id') . ' = ' . $db->quoteName('r.id') . ')')
                    ->join('INNER', $db->quoteName('#__sdi_organism','o') .  ' ON (' . $db->quoteName('r.organism_id') . ' = ' . $db->quoteName('o.id') . ')')
                    ->group($db->quoteName('o.id'))
                    ->group($db->quoteName('o.name'))
                    ->order($db->quoteName('o.name'));
            $db->setQuery($query, 0);
            $orgs = $db->loadObjectList();
            //Add "All organisms" to filter
            array_unshift($orgs, (object) array('value' => 'all', 'text' => JText::_('COM_EASYSDI_DASHBOARD_FILTERS_ALL_ORGANISMS')));
            //Create select
            $pseudoFilters .= JHTML::_('select.genericlist', $orgs, 'filter-organism', 'style="width:225px;" onchange="triggerFilterUpdate();"');
        }
        //Filter dates
        $pseudoFilters .= '<hr class="hr-condensed">';
        $pseudoFilters .= JHTML::_('select.genericlist', Easysdi_dashboardHelper::getDateFilterList(), 'filter-date', 'style="width:225px;" onchange="triggerFilterUpdate();"');

        return(
                '<hr>
                <div class="filter-select hidden-phone">
			<h4 class="page-header">' . JText::_('JSEARCH_FILTER_LABEL') . '</h4>' .
                $pseudoFilters
                . '</div>'
                );
    }

    /**
     * Curl proxy to get birt reports if birt is enabled
     */
    public static function getBirtReportProxy($reportName, $report_format, $jsonData) {
        $params = JComponentHelper::getParams('com_easysdi_dashboard');
        $talendenabled = $params->get('talendenabled');
        $birtenabled = $params->get('birtenabled');
        $birturl = $params->get('birturl');

        if (!function_exists("curl_init")) {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_DASHBOARD_ERROR_BIRT_NEEDS_CURL'), 'error');
            $this->setRedirect('index.php?option=com_easysdi_dashboard');
            return;
        }

        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, $birturl . 'run?');
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($c, CURLOPT_HEADER, false);
        curl_setopt($c, CURLOPT_HTTPHEADER, array('Expect:'));
        curl_setopt($c, CURLOPT_POST, true);
        curl_setopt($c, CURLOPT_POSTFIELDS, 
                '__report=' . $reportName . '&' .
                '__format=' . $report_format . '&' .
                'Json=' . $jsonData);
        $output = curl_exec($c);
        curl_close($c);

        /* Curl got a problem */
        if ($output === false) {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_DASHBOARD_ERROR_CURL_ERROR') . curl_error($c), 'error');
            $this->setRedirect('index.php?option=com_easysdi_dashboard');
            return;
        } else {
            return($output);
        }
    }

}

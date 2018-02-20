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

jimport('joomla.application.component.controller');

require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_dashboard/helpers/easysdi_dashboard.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_dashboard/indicators/sdiIndicator.php';

class Easysdi_dashboardController extends JControllerLegacy {

    /**
     * Get the data from th indicator
     * @return mixed data in requested format (JSON, CSV or PDF)
     */
    public function getData() {
        $indicator = JFactory::getApplication()->input->get('indicator');
        $organism = JFactory::getApplication()->input->get('organism');
        $timestart = JFactory::getApplication()->input->get('timestart');
        $timeend = JFactory::getApplication()->input->get('timeend');
        $dataformat = JFactory::getApplication()->input->get('dataformat');
        $limit = JFactory::getApplication()->input->get('limit', 0);

        //check input sanity
        if (!Easysdi_dashboardHelper::checkFiltersInput($indicator, $organism, $timestart, $timeend, $dataformat, $limit)) {
            $this->setRedirect('index.php?option=com_easysdi_dashboard');
            return;
        }

        //check if user has access to this organism
        if (!(is_numeric($organism) && $this->userCanAccessOrgData($organism))) {
            JFactory::getApplication()->enqueueMessage('Cannot access this organism data', 'error');
            $this->setRedirect('index.php?option=com_easysdi_dashboard');
            return;
        }

        include_once(JPATH_ADMINISTRATOR . '/components/com_easysdi_dashboard/indicators/' . $indicator . '.php');
        $indClassName = 'sdiIndicator' . ucfirst($indicator);
        $ind = new $indClassName();

        $ind->sendResponse($organism, $timestart, $timeend, $dataformat, $limit);
    }

    /**
     * Check the user access based on session array
     * created by Easysdi_dashboardViewShop::hasDashboardAccess($sdiUser)
     * @param type $organismID
     * @return boolean
     * @see Easysdi_dashboardViewShop
     */
    private function userCanAccessOrgData($organismID) {
        $session = JFactory::getSession();
        $dashboardAccessList = $session->get('organismDashboardAccess');
        return isset($dashboardAccessList[$organismID]);
    }

}

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

jimport('joomla.filesystem.file');

require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_dashboard/helpers/easysdi_dashboard.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_dashboard/indicators/sdiIndicator.php';

class Easysdi_dashboardController extends JControllerLegacy {

    /**
     * Method to display a view.
     *
     * @param	boolean			$cachable	If true, the view output will be cached
     * @param	array			$urlparams	An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @return	JController		This object to support chaining.
     * @since	1.5
     */
    public function display($cachable = false, $urlparams = false) {


        $view = JFactory::getApplication()->input->getCmd('view', 'shop');
        JFactory::getApplication()->input->set('view', $view);

        parent::display($cachable, $urlparams);
        return $this;
    }

    public function getData() {
        $indicator = JFactory::getApplication()->input->get('indicator');
        $organism = JFactory::getApplication()->input->get('organism');
        $timestart = JFactory::getApplication()->input->get('timestart');
        $timeend = JFactory::getApplication()->input->get('timeend');
        $dataformat = JFactory::getApplication()->input->get('dataformat');
        $limit = JFactory::getApplication()->input->get('limit', 0);

        if (!Easysdi_dashboardHelper::checkFiltersInput($indicator, $organism, $timestart, $timeend, $dataformat, $limit)) {
            $this->setRedirect('index.php?option=com_easysdi_dashboard');
            return;
        }

        include_once(JPATH_ADMINISTRATOR . '/components/com_easysdi_dashboard/indicators/' . $indicator . '.php');
        $indClassName = 'sdiIndicator' . ucfirst($indicator);
        $ind = new $indClassName();

        $ind->sendResponse($organism, $timestart, $timeend, $dataformat, $limit);
    }

}

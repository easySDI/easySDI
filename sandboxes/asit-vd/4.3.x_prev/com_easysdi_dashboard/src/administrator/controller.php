<?php
/**
 * @version     4.0.0
 * @package     com_easysdi_dashboard
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.filesystem.file');

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

        require_once JPATH_COMPONENT . '/helpers/easysdi_dashboard.php';

        $view = JFactory::getApplication()->input->getCmd('view', 'shop');
        JFactory::getApplication()->input->set('view', $view);

        parent::display($cachable, $urlparams);
        return $this;
    }

    /**
     * Method to redirect to EasySDI home page (driven by easysdi_com_core)
     *
     * @since EasySDI 3.3.0
     */
    public function easySDIHome() {
        $this->setRedirect('index.php?option=com_easysdi_core');
    }

    public function getData() {
        $document = JFactory::getDocument();
        $has_error = false;

        $indicator = JFactory::getApplication()->input->get('indicator');
        if (is_null($indicator)) {
            $has_error = true;
            JFactory::getApplication()->enqueueMessage('"indicator" parameter not set', 'error');
        } else {
            if (!preg_match('/^[\w]+$/', $indicator)) {
                $has_error = true;
                JFactory::getApplication()->enqueueMessage('"indicator" parameter format error', 'error');
            }
        }
        $organism = JFactory::getApplication()->input->get('organism');
        if (is_null($organism)) {
            $has_error = true;
            JFactory::getApplication()->enqueueMessage('"organism" parameter not set', 'error');
        }
        $timestart = JFactory::getApplication()->input->get('timestart');
        if (is_null($timestart)) {
            $has_error = true;
            JFactory::getApplication()->enqueueMessage('"timestart" parameter not set', 'error');
        }
        $timeend = JFactory::getApplication()->input->get('timeend');
        if (is_null($timeend)) {
            $has_error = true;
            JFactory::getApplication()->enqueueMessage('"timeend" parameter not set', 'error');
        }
        $dataformat = JFactory::getApplication()->input->get('dataformat');
        if (is_null($dataformat)) {
            $has_error = true;
            JFactory::getApplication()->enqueueMessage('"dataformat" parameter not set', 'error');
        }
        $limit  = JFactory::getApplication()->input->get('limit', 10);
        if (is_null($limit)) {
            $has_error = true;
            JFactory::getApplication()->enqueueMessage('"limit" parameter not set or not positive integer', 'error');
        }
        if (!JFile::exists(JPATH_COMPONENT . '/indicators/' . $indicator . '.php')) {
            $has_error = true;
            JFactory::getApplication()->enqueueMessage('this indicator does not exists', 'error');
        }
        if ($has_error) {
            $this->setRedirect('index.php?option=com_easysdi_dashboard');
            return;
        }

        include_once(JPATH_COMPONENT . '/indicators/' . $indicator . '.php');
        $ind = new Indicator();

        switch ($dataformat) {
            case 'json':
                $document->setMimeEncoding('application/json');
                echo($ind->getData($organism, $timestart, $timeend, $limit ));
                break;
            case 'pdf':
                $document->setMimeEncoding('application/pdf');
                //TODO Check RFC for content disposition
                JResponse::setHeader('Content-Disposition', 'filename="' . $indicator . '.pdf"');
                echo($ind->getReport($organism, $timestart, $timeend, $limit, $dataformat));
                break;
            case 'xls':
                $document->setMimeEncoding('application/vnd.ms-excel');
                //TODO Check RFC for content disposition
                JResponse::setHeader('Content-Disposition', 'filename="' . $indicator . '.xml.xls"');
                echo($ind->getReport($organism, $timestart, $timeend, $limit, $dataformat));
                break;
            case 'doc':
                $document->setMimeEncoding('application/msword');
                //TODO Check RFC for content disposition
                JResponse::setHeader('Content-Disposition', 'filename="' . $indicator . '.xml.doc"');
                echo($ind->getReport($organism, $timestart, $timeend, $limit, $dataformat));
                break;            

        }
    }

}

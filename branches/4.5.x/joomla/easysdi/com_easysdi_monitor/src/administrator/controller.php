<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_monitor
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

class Easysdi_monitorController extends JControllerLegacy {

    private $requiredRightLevel = 'core.create';
    
    /**
     * Method to display a view.
     *
     * @param	boolean			$cachable	If true, the view output will be cached
     * @param	array			$urlparams	An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @return	JController		This object to support chaining.
     * @since	1.5
     * 
     */
    public function display($cachable = false, $urlparams = false) {
        require_once JPATH_COMPONENT . '/helpers/easysdi_monitor.php';

        $view = JFactory::getApplication()->input->getCmd('view', 'mains');
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

    function create() {
        $database = & JFactory::getDBO();

        $row = json_decode(JRequest::getVar("rows"));
        if (!$this->chechAuth()) {
            echo "{success:false, error:user is not admin}";
            die();
        }

        $query = $database->getQuery(true);
        
        $columns = array('exportName', 'exportType', 'xsltUrl','exportDesc');
        $values = array($query->quote($row->exportName), $query->quote($row->exportType), $query->quote($row->xsltUrl), $query->quote($row->exportDesc));
        $query->insert('#__sdi_monitor_exports');
        $query->columns($query->quoteName($columns));
        $query->values(implode(',', $values));
        
        try {

            $database->setQuery($query);
            $rows = $database->loadObjectList();
            echo "{success:true}";
        } catch (Exception $e) {
            echo "{success:false, error:" . $e->getTraceAsString() . "}";
        }
        die();
    }

    function read() {

        $database = & JFactory::getDBO();

        if (!$this->chechAuth()) {
            echo "{success:false, error:user is not admin}";
            die();
        }

        $limit = JRequest::getVar('limit', 15);
        $limitstart = JRequest::getVar('start', 0);
        //get the total
        $query = $database->getQuery(true);
        $query->select('count(*)');
        $query->from('#__sdi_monitor_exports');
        
        $database->setQuery($query);
        $total = $database->loadResult();
        $pagination = new JPagination($total, $limitstart, $limit);

        $query = $database->getQuery(true);
        $query->select('*');
        $query->from('#__sdi_monitor_exports');
        $query->order('id desc');

        try {

            $database->setQuery($query, $pagination->limitstart, $pagination->limit);
            $rows = $database->loadObjectList();
            echo "{success:true,results:" . $total . ",rows:" . json_encode($rows) . "}";
        } catch (Exception $e) {
            echo "{success:false, error:" . $e->getTraceAsString() . "}";
        }
        die();
    }

    function update() {
        //"id":"1","exportName":"name1","exportType":"csw","exportDesc":"desc111","xsltUrl":"url1"
        $database = & JFactory::getDBO();

        $row = json_decode(JRequest::getVar("rows"));
        if (!$this->chechAuth()) {
            echo "{success:false, error:user is not admin}";
            die();
        }
        
        $database->updateObject('#__sdi_monitor_exports', $row, $row->id);
 
        if ($database->getErrorNum()) {

            echo "{success:false, error:" . $e->getTraceAsString() . "}";
            die();
        }
        echo "{success:true}";
        die();
    }

    function delete() {

        $database = & JFactory::getDBO();
        
        $id = JRequest::getVar("rows");
        if (!$this->chechAuth()) {
            echo "{success:false, error:user is not admin}";
            die();
        }

        $query = $database->getQuery(true);
        $query->delete('#__sdi_monitor');
        $query->where('id =' . (int)$id);
        
        $database->setQuery($query);
        $result = $database->query();
        if ($database->getErrorNum()) {

            echo "{success:false, error:" . $e->getTraceAsString() . "}";
            die();
        }
        echo "{success:true}";
        die();
    }

    /**
     * Check user right on this component
     * 
     * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
     * @return boolean 
     */
    private function chechAuth() {
        $componentName = JRequest::getVar('option');
        
        $User = & JFactory::getUser();
        $isAuth = $User->authorise($this->requiredRightLevel, $componentName);
        
        if($isAuth>0){
            return true;
        }else{
            return false;
        }
    }

}

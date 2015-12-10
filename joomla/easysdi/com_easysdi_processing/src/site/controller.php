<?php
/*------------------------------------------------------------------------
# controller.php - Easysdi_processing Component
# ------------------------------------------------------------------------
# author    Thomas Portier
# copyright Copyright (C) 2015. All Rights Reserved
# license   Depth France
# website   www.depth.fr
-------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controller library
jimport('joomla.application.component.controller');


class Easysdi_processingController extends JControllerLegacy
{


    static private function returnJson($response_array) {
        JResponse::clearHeaders();
        JResponse::setHeader('Content-Type', 'application/json', true);
        JResponse::sendHeaders();

        if (is_array($response_array)) {
            array_walk_recursive($response_array, function(&$item, $key)
            {
                if (is_string($item)) {
                    $item = utf8_encode($item);
                }
            });
        }

        echo json_encode($response_array);
        JFactory::getApplication()->close();
    }

    static public function ltrim0($str) {
        return ltrim($str,'0');
    }







    function display($cachable = false, $urlparams = false) {
    //require_once JPATH_COMPONENT . '/helpers/easysdi_processing.php';

        $view = JFactory::getApplication()->input->getCmd('view', 'orders');
        JFactory::getApplication()->input->set('view', $view);

        parent::display($cachable, $urlparams);

        return $this;

        // set default view if not set
        /*Request::setVar('view', JRequest::getCmd('view', 'Easysdi_processing'));

        // call parent behavior
        parent::display($cachable);

        // set view
        $view = strtolower(JRequest::getVar('view'));*/

    }


    function order_plugin() {
        $jinput = JFactory::getApplication()->input;
        require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_processing/models/order.php';
        $model=new Easysdi_processingModelorder;
        $order=$model->getItem($jinput->get('id'));

        $dispatcher = JDispatcher::getInstance();
        $plugin_results = $dispatcher->trigger( 'onProcessingOrderPluginCall' ,compact('order'));

        foreach ($plugin_results as $plugin_result) {
            if (count($plugin_result)>0) {
                self::returnJson($plugin_result);
            }
        }

        return JError::raiseWarning(404, JText::_('JERROR_LAYOUT_REQUESTED_ORDER_WAS_NOT_FOUND'));
    }






}
?>
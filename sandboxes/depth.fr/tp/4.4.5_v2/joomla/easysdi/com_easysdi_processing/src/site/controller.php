<?php
/**
* @version     4.4.5
* @package     com_easysdi_processing
* @copyright   Copyright (C) 2013-2017. All rights reserved.
* @license     GNU General Public License version 3 or later; see LICENSE.txt
* @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
*/

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

         $user_roles=Easysdi_processingHelper::getCurrentUserRolesOnData($order);

        $private_access=($order->access_key!==null && $order->access_key==$jinput->get('access_key'));

        if (!in_array('creator',$user_roles) && !in_array('contact',$user_roles) && !$private_access ) {
            return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
        }

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
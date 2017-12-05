<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * Orders list controller class.
 */
class Easysdi_shopControllerOrders extends JControllerAdmin {

    /**
     * Proxy for getModel.
     * @since	1.6
     */
    public function getModel($name = 'order', $prefix = 'Easysdi_shopModel') {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));
        return $model;
    }

    /**
     * Method to save the submitted ordering values for records via AJAX.
     *
     * @return  void
     *
     * @since   3.0
     */
    public function saveOrderAjax() {
        // Get the input
        $input = JFactory::getApplication()->input;
        $pks = $input->post->get('cid', array(), 'array');
        $order = $input->post->get('order', array(), 'array');

        // Sanitize the input
        JArrayHelper::toInteger($pks);
        JArrayHelper::toInteger($order);

        // Get the model
        $model = $this->getModel();

        // Save the ordering
        $return = $model->saveorder($pks, $order);

        if ($return) {
            echo "1";
        }

        // Close the application
        JFactory::getApplication()->close();
    }

    /**
     * Function that allows child controller access to model data
     * after the item has been deleted.
     * Used to delete order files on order deletion.
     *
     * @param   JModelLegacy  $model  The data model object.
     * @param   integer       $ids    The array of ids for items being deleted.
     *
     * @return  void
     */
    protected function postDeleteHook($model, $ids = null) {
        if (!isset($ids) || !is_array($ids)) {
            return;
        }
        
        $orderResponseFolderBase = JPATH_ROOT . JComponentHelper::getParams('com_easysdi_shop')->get('orderresponseFolder');
        $orderMDFolderBase = JPATH_ROOT . JComponentHelper::getParams('com_easysdi_shop')->get('orderrequestFolder');
        foreach ($ids as $id) {
            if (!is_int($id)) {
                continue;
            }

            $orderResponseFolder = $orderResponseFolderBase . '/' . $id;
            $orderMDFolder = $orderMDFolderBase . '/' . $id;
            
            if (JFolder::exists($orderResponseFolder)) {
                JFolder::delete($orderResponseFolder);
            }
            if (JFolder::exists($orderMDFolder)) {
                JFolder::delete($orderMDFolder);
            }
        }
    }

}

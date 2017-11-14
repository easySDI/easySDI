<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_contact
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * Users list controller class.
 */
class Easysdi_contactControllerUsers extends JControllerAdmin {

    /**
     * Proxy for getModel.
     * @since	1.6
     */
    public function &getModel($name = 'user', $prefix = 'Easysdi_contactModel') {
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

    public function getUsersAndRolesByOrganismIDAjax() {
        $input = JFactory::getApplication()->input;
        $id = $input->get('organismId', null, 'INT');
        if ($id) {
            $model = $this->getModel('users','Easysdi_contactModel');
            $userAndRoles = $model->getUsersAndRolesByOrganismID($id);
            echo json_encode($userAndRoles);
            die();
        }else{
            echo('error: no id given');
            die();
        }
    }

}

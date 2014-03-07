<?php

/**
 * @version     4.0.0
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org§> - http://www.easysdi.org
 */
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * Searchcriterias list controller class.
 */
class Easysdi_catalogControllerSearchcriterias extends JControllerAdmin {

    /**
     * Proxy for getModel.
     * @since	1.6
     */
    public function getModel($name = 'searchcriteria', $prefix = 'Easysdi_catalogModel') {
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

    public function changeTab() {
        // Get the input
        $input = JFactory::getApplication()->input;
        $id = $input->get('id', null, 'int');
        $tab = $input->get('tab', null, 'int');

        $db = JFactory::getDbo();
        $db->setQuery('UPDATE  #__sdi_catalog_searchcriteria SET searchtab_id = ' .$tab . ' WHERE id=' . $id);
        $db->execute();
        die();
    }

}
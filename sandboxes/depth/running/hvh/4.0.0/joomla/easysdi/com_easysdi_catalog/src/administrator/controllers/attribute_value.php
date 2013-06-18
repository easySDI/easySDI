<?php

/**
 * @version     4.0.0
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org§> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Attribute_value controller class.
 */
class Easysdi_catalogControllerAttribute_value extends JControllerForm {

    function __construct() {
        $this->view_list = 'attribute_values';
        parent::__construct();
    }

    /**
     * Method to cancel an edit.
     *
     * @param   string  $key  The name of the primary key of the URL variable.
     *
     * @return  boolean  True if access level checks pass, false otherwise.
     *
     * @since   12.2
     */
    public function cancel($key = null) {
        if (parent::cancel($key)) {
            $app = JFactory::getApplication('administrator');
            $this->setRedirect(
            JRoute::_(
            'index.php?option=' . $this->option . '&view=' . $this->view_list
            . $this->getRedirectToListAppend(). '&filter_attribute='.$app->getUserState( 'com_easysdi_catalog.attribute_values.filter.attribute')
            , false
            )
            );

            return true;
        }
        return false;
    }

}
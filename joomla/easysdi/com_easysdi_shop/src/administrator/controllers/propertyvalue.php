<?php

/**
 * @version		4.4.0
 * @package     com_easysdi_shop
 * @copyright	
 * @license		
 * @author		
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Propertyvalue controller class.
 */
class Easysdi_shopControllerPropertyvalue extends JControllerForm {

    function __construct() {
        $this->view_list = 'propertyvalues';
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
                            . $this->getRedirectToListAppend() . '&filter_property=' . $app->getUserState('com_easysdi_shop.propertyvalues.filter.property')
                            , false
                    )
            );

            return true;
        }
        return false;
    }

}
<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_contact
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

class Easysdi_contactController extends JControllerLegacy {

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
        require_once JPATH_COMPONENT . '/helpers/easysdi_contact.php';

        $view = JFactory::getApplication()->input->getCmd('view', 'users');
        $layout = JFactory::getApplication()->input->getCmd('layout', 'edit');
        $id = JFactory::getApplication()->input->getInt('id');
        JFactory::getApplication()->input->set('view', $view);

        // Check for edit form.
        if ($view == 'user' && $layout == 'edit' && !$this->checkEditId('com_easysdi_contact.edit.user', $id)) {
            // Somehow the person just went to the form - we don't allow that.
            $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
            $this->setMessage($this->getError(), 'error');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_contact&view=users', false));

            return false;
        }

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

}

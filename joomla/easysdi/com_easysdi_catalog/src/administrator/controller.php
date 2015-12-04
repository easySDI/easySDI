<?php


// No direct access
defined('_JEXEC') or die;

class Easysdi_catalogController extends JControllerLegacy {

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
        require_once JPATH_COMPONENT . '/helpers/easysdi_catalog.php';

        $view = JFactory::getApplication()->input->getCmd('view', 'catalogs');
        JFactory::getApplication()->input->set('view', $view);

        parent::display($cachable, $urlparams);

        return $this;
    }

    public function attributes (){
        $this->setRedirect('index.php?option=com_easysdi_catalog&view=attributes');
    }
    
    public function catalogs (){
        $this->setRedirect('index.php?option=com_easysdi_catalog&view=catalogs');
    }
}

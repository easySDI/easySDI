<?php

/**
 * @version		4.4.0
 * @package     com_easysdi_catalog
 * @copyright	
 * @license		
 * @author		
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View to edit
 */
class Easysdi_catalogViewNamespace extends JViewLegacy {

    protected $state;
    protected $item;
    protected $form;

    /**
     * Display the view
     */
    public function display($tpl = null) {
        $this->state = $this->get('State');
        $this->item = $this->get('Item');
        
        if($this->item->system == 1){
            //Can't edit system namespaces
            JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_CATALOG_NAMESPACES_ERROR_SYSTEM_ITEM'), 'error');
            
            $app = JFactory::getApplication();	
            $link = JRoute::_('index.php?option=com_easysdi_catalog&view=namespaces', false);	
           	 
            JFactory::getApplication()->redirect($link);	 
            
        }
        $this->form = $this->get('Form');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        $this->addToolbar();
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     */
    protected function addToolbar() {
        JFactory::getApplication()->input->set('hidemainmenu', true);

        $user = JFactory::getUser();
        $isNew = ($this->item->id == 0);
        if (isset($this->item->checked_out)) {
            $checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
        } else {
            $checkedOut = false;
        }
        $this->canDo = Easysdi_catalogHelper::getActions();

        JToolBarHelper::title(JText::_('COM_EASYSDI_CATALOG_TITLE_NAMESPACE'), 'namespace.png');

        // If not checked out, can save the item.
        if (!$checkedOut && ($this->canDo->get('core.edit') || ($this->canDo->get('core.create')))) {

            JToolBarHelper::apply('namespace.apply', 'JTOOLBAR_APPLY');
            JToolBarHelper::save('namespace.save', 'JTOOLBAR_SAVE');
        }
        if (!$checkedOut && ($this->canDo->get('core.create'))) {
            JToolBarHelper::custom('namespace.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
        }
        // If an existing item, can save to a copy.
        if (!$isNew && $this->canDo->get('core.create')) {
            JToolBarHelper::custom('namespace.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
        }
        if (empty($this->item->id)) {
            JToolBarHelper::cancel('namespace.cancel', 'JTOOLBAR_CANCEL');
        } else {
            JToolBarHelper::cancel('namespace.cancel', 'JTOOLBAR_CLOSE');
        }
    }

}

<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');
jimport('joomla.html.toolbar');
require_once JPATH_BASE . '/administrator/components/com_easysdi_core/libraries/easysdi/common/SdiToolbar.php';

/**
 * View to edit
 */
class Easysdi_coreViewVersion extends JViewLegacy {

    protected $state;
    protected $item;
    protected $form;
    protected $params;

    /**
     * Display the view
     */
    public function display($tpl = null) {

        $app = JFactory::getApplication();

        $this->state = $this->get('State');
        $this->item = $this->get('Data');
        $this->params = $app->getParams('com_easysdi_core');
        $this->form = $this->get('Form');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        //Check user rights
        $this->user = sdiFactory::getSdiUser();
        if (!$this->user->isEasySDI) {
            JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
            JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
            return;
        }

        if (!$this->user->authorize($this->item->resource_id, sdiUser::resourcemanager) && !$this->user->isOrganismManager($this->item->resource_id, 'resource')) {
            JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
            JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
            return;
        }

        $this->_prepareDocument();

        parent::display($tpl);
    }

    /**
     * Prepares the document
     */
    protected function _prepareDocument() {
        $app = JFactory::getApplication();

        $this->params->def('page_heading', JText::_('COM_EASYSDI_CORE_VERSION_RELATION_PAGE_TITLE'));
        $title = $this->params->get('page_title', '');
        
        if (empty($title)) {
            $title = $app->getCfg('sitename');
        } elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
            $title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
        } elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
            $title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
        }
        $this->document->setTitle($title);

        if ($this->params->get('menu-meta_description')) {
            $this->document->setDescription($this->params->get('menu-meta_description'));
        }

        if ($this->params->get('menu-meta_keywords')) {
            $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
        }

        if ($this->params->get('robots')) {
            $this->document->setMetadata('robots', $this->params->get('robots'));
        }
    }
    
    public function getTopActionBar(){
        $toolbar = new SdiToolbar();
        
        $back_url = array('root' => 'index.php',
            'option' => 'com_easysdi_core',
            'view' => 'resources');
        if(isset($this->state->parentid))
            $back_url['parentid'] = $this->state->parentid;
        
        $toolbar->appendBtnRoute(JText::_('COM_EASYSDI_CORE_BACK'), JRoute::_(Easysdi_coreHelper::array2URL($back_url)), 'btn-small btn-danger btn-back-ressources');
        
        return $toolbar->renderToolbar();
    }

    function getToolbar() {
        $bar = new JToolBar('toolbar');
        //and make whatever calls you require
        if($this->user->authorize($this->item->resource_id, sdiUser::resourcemanager)){
            $bar->appendButton('Standard', 'apply', JText::_('COM_EASYSDI_CORE_APPLY'), 'version.apply', false);
            $bar->appendButton('Separator');
            $bar->appendButton('Standard', 'save', JText::_('COM_EASYSDI_CORE_SAVE'), 'version.save', false);
            $bar->appendButton('Separator');
        }
        $bar->appendButton('Standard', 'cancel', JText::_('JCancel'), 'version.cancel', false);
        //generate the html and return
        return $bar->render();
    }

    function getSearchToolbar() {
        $bar = new JToolBar('toolbar');
        //and make whatever calls you require
        $bar->appendButton('Standard', 'search', JText::_('COM_EASYSDI_CORE_FORM_LBL_VERSION_SEARCH_BTN'), 'version.search', false);
        $bar->appendButton('Separator');
        $bar->appendButton('Standard', 'clear', JText::_('COM_EASYSDI_CORE_FORM_LBL_VERSION_CLEAR_BTN'), 'version.clear', false);
        //generate the html and return
        return $bar->render();
    }

}

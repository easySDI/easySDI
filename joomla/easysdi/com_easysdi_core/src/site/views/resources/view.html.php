<?php

/**
 * @version     4.0.0
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of Easysdi_core.
 */
class Easysdi_coreViewResources extends JViewLegacy {

    protected $items;
    protected $pagination;
    protected $state;
    protected $params;
    protected $user;

    /**
     * Display the view
     */
    public function display($tpl = null) {
        $app = JFactory::getApplication();

        try{
            $this->user = sdiFactory::getSdiUser();
        }catch (Exception $e){
            JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
           return; 
        }
        
        $this->state = $this->get('State');
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->params = $app->getParams('com_easysdi_core');
        
        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        $this->_prepareDocument();
        parent::display($tpl);
    }

    /**
     * Prepares the document
     */
    protected function _prepareDocument() {
        $app = JFactory::getApplication();
        $menus = $app->getMenu();
        $title = null;

        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menu = $menus->getActive();
        if ($menu) {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        } else {
            $this->params->def('page_heading', JText::_('com_easysdi_core_DEFAULT_PAGE_TITLE'));
        }
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

}

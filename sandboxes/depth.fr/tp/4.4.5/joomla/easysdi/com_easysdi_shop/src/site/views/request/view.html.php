<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View to edit
 */
class Easysdi_shopViewRequest extends JViewLegacy {

    protected $state;
    protected $item;
    protected $form;
    protected $params;
    protected $user;

    /**
     * Display the view
     */
    public function display($tpl = null) {
        $app = JFactory::getApplication();
        $this->state = $this->get('State');
        $this->item = $this->get('Data');
        $this->params = $app->getParams('com_easysdi_shop');
        $this->form = $this->get('Form');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }
        
        $this->user = sdiFactory::getSdiUser();
        
        $extractionsIds = array_map(function($d){return $d->id;}, $this->item->basket->extractions);
        $this->authorizeddiffusion = array_intersect($extractionsIds, (array)$this->user->getResponsibleExtraction());
        $this->managedOrganismsDiffusion = array_map(function($o){return $o->id;}, (array)$this->user->getOrganismManagerOrganisms());
        
        if (!$this->user->isEasySDI || (count($this->authorizeddiffusion)==0 && !$this->user->isOrganismManager($extractionsIds, 'diffusion'))) {
            JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
            JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_shop&view=requests', false));
            return false;
        }
        
        //get the contact address of the user
        require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_contact/tables/address.php';
        $tableAddress = JTable::getInstance('Address', 'Easysdi_contactTable', array());
        $tableAddress->loadByUserID($this->item->basket->sdiUser->id, 1);
        $this->item->basket->sdiUser->contactAddress = $tableAddress;        
        
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
            $this->params->def('page_heading', JText::_('com_easysdi_shop_DEFAULT_PAGE_TITLE'));
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
    
    function getToolbar() {
        //load the JToolBar library and create a toolbar
        jimport('joomla.html.toolbar');
        $bar = new JToolBar('toolbar');

        $bar->appendButton('Standard', 'cancel', JText::_('JCancel'), 'request.cancel', false);
        //generate the html and return
        return $bar->render();
    }

}

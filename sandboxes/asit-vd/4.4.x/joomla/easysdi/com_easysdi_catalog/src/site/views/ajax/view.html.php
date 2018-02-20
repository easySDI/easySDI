<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

require_once JPATH_COMPONENT . '/libraries/easysdi/FormHtmlGenerator.php';

/**
 * View to edit
 */
class Easysdi_catalogViewAjax extends JViewLegacy {

    protected $state;
    protected $item;
    protected $form;
    protected $params;
    public $formHtml = '';

    /**
     * Display the view
     */
    public function display($tpl = null) {


        $app = JFactory::getApplication();
        $user = JFactory::getUser();

        $this->state = $this->get('State');
        $this->item = $this->get('Data');
        $this->params = $app->getParams('com_easysdi_catalog');
        $this->form = $this->get('Form');


        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        $this->buildForm();
        $this->_prepareDocument();

        //parent::display($tpl);
        echo $this->formHtml;die();
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
            $this->params->def('page_heading', JText::_('com_easysdi_catalog_DEFAULT_PAGE_TITLE'));
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

    protected function buildForm(){
        $structure = $this->structure = $this->get('Structure');
        $ajaxXpath = $this->ajaxXpath = $this->get('AjaxXpath');
        
        $fhg = new FormHtmlGenerator($this->form, $structure, $ajaxXpath);
        
        $this->formHtml = $fhg->buildForm();
    }

}

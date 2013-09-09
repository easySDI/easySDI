<?php

/**
 * @version     4.0.0
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

require_once JPATH_SITE . '/components/com_easysdi_map/helpers/easysdi_map.php';

/**
 * View to edit
 */
class Easysdi_shopViewBasket extends JViewLegacy {

    protected $state;
    protected $item;
    protected $form;
    protected $params;

    /**
     * Display the view
     */
    public function display($tpl = null) {


        //Load admin language file
        $lang = JFactory::getLanguage();
        $lang->load('com_easysdi_shop', JPATH_ADMINISTRATOR);

        $app = JFactory::getApplication();

        $this->state = $this->get('State');
        $this->item = $this->get('Data');
        $this->params = $app->getParams('com_easysdi_shop');

        $params_array = $this->params->toArray();
        if(isset($params_array['ordermap'])){
            $this->mapscript = Easysdi_mapHelper::getMapScript($params_array['ordermap']);
        }
        else{
            JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_MAP_PREVIEW_NOT_FOUND'), 'error');
            return;
        }
        
        $pathway = $app->getPathway();
        $pathway->addItem(JText::_("COM_EASYSDI_SHOP_BASKET_TITLE"), JRoute::_('index.php?option=com_easysdi_shop&view=basket', false));

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
//        // add required stylesheets from admin template
//        $document = JFactory::getDocument();
//        $document->addStyleSheet('administrator/templates/system/css/system.css');
//        //now we add the necessary stylesheets from the administrator template
//        //in this case i make reference to the bluestork default administrator template in joomla 1.6
//        $document->addCustomTag(
//                '<link href="administrator/templates/isis/css/template.css" rel="stylesheet" type="text/css" />' . "\n\n" .
//                '<!--[if IE 7]>' . "\n" .
//                '<link href="administrator/templates/isis/css/ie7.css" rel="stylesheet" type="text/css" />' . "\n" .
//                '<![endif]-->' . "\n" .
//                '<!--[if gte IE 8]>' . "\n\n" .
//                '<link href="administrator/templates/isis/css/ie8.css" rel="stylesheet" type="text/css" />' . "\n" .
//                '<![endif]-->' . "\n" .
//                '<link rel="stylesheet" href="administrator/templates/isis/css/rounded.css" type="text/css" />' . "\n"
//        );
        //load the JToolBar library and create a toolbar
        jimport('joomla.html.toolbar');
        $bar = new JToolBar('toolbar');
        //and make whatever calls you require
        $bar->appendButton('Standard', 'archive', JText::_('COM_EASYSDI_SHOP_BASKET_BTN_SAVE'), 'basket.save', false);
        $bar->appendButton('Separator');
        $bar->appendButton('Standard', 'edit', JText::_('COM_EASYSDI_SHOP_BASKET_BTN_ESTIMATE'), 'basket.estimate', false);
        $bar->appendButton('Separator');
        $bar->appendButton('Standard', 'publish', JText::_('COM_EASYSDI_SHOP_BASKET_BTN_ORDER'), 'basket.order', false);
        //generate the html and return
        return $bar->render();

//        <div class = "btn-toolbar" id = "toolbar">
//        <div class = "btn-group" id = "toolbar-save">
//        <button href = "#" onclick = "Joomla.submitbutton('basket.estimate')" class = "btn btn-small">
//        <i class = "icon-save ">
//        </i>
//        Devis
//        </button>
//        </div>
//
//        <div class = "btn-group">
//        </div>
//
//        <div class = "btn-group" id = "toolbar-cancel">
//        <button href = "#" onclick = "Joomla.submitbutton('basket.order')" class = "btn btn-small">
//        <i class = "icon-cancel ">
//        </i>
//        Commander
//        </button>
//        </div>
//
//        </div>
    }

}

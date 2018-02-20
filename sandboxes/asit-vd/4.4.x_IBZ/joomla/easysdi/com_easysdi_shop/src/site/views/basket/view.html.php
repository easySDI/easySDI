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

require_once JPATH_SITE . '/components/com_easysdi_map/helpers/easysdi_map.php';
require_once JPATH_COMPONENT . '/helpers/easysdi_shop.php';

/**
 * View to edit
 */
class Easysdi_shopViewBasket extends JViewLegacy {

    protected $state;
    protected $item;
    protected $form;
    protected $params;
    protected $paramsarray;
    protected $importEnabled;

    /**
     * Display the view
     */
    public function display($tpl = null) {
        $app = JFactory::getApplication();

        $this->state = $this->get('State');
        $this->item = $this->get('Data');
        if (is_null($this->item->sdiUser->id)) {
            $this->item->sdiUser = sdiFactory::getSdiUser();
        }
        $this->params = $app->getParams('com_easysdi_shop');
        $this->paramsarray = $this->params->toArray();
        $this->user = sdiFactory::getSdiUser();

        if ($this->_layout != 'confirm' && $this->item && $this->item->extractions) {
            if (isset($this->paramsarray['ordermap'])) {
                $this->mapscript = Easysdi_mapHelper::getMapScript($this->paramsarray['ordermap']);
            } else {
                JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_SHOP_MAP_PREVIEW_NOT_FOUND'), 'error');
                return;
            }
        }

        $this->thirdParties = $this->getAvailableThirdParties();

        $this->basketScriptPlugins = $this->getBasketScriptPlugins();

        //check if free perimeter import is enabled and free perimeter is availlable in this basket
        $this->importEnabled = false;
        foreach ((array)$this->item->perimeters as $perimeter):
            if ($perimeter->id == 1) {
                $this->importEnabled = true;
            }
        endforeach;
        $this->importEnabled &= JComponentHelper::getParams('com_easysdi_shop')->get('perimeterimportactivated', false);

        // rebuild extractions array to allow by supplier grouping
        Easysdi_shopHelper::extractionsBySupplierGrouping($this->item);

        // calculate price for the current basket (only if surface is defined)
        if (isset($this->item->extent) && isset($this->item->extent->surface)){
            Easysdi_shopHelper::basketPriceCalculation($this->item);
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

    /**
     * Return the toolbar for the view
     * @return JToolBar
     */
    function getToolbar() {
        //load the JToolBar library and create a toolbar
        jimport('joomla.html.toolbar');
        $bar = new JToolBar('toolbar');

        $bar->appendButton('Standard', 'archive', JText::_('COM_EASYSDI_SHOP_BASKET_BTN_SAVE'), 'basket.draft', false);
        $bar->appendButton('Separator');
        if (!$this->item->free) {
            $bar->appendButton('Standard', 'edit', JText::_('COM_EASYSDI_SHOP_BASKET_BTN_ESTIMATE'), 'basket.estimate', false);
            $bar->appendButton('Separator');
        }
        $bar->appendButton('Standard', 'publish', JText::_('COM_EASYSDI_SHOP_BASKET_BTN_ORDER'), 'basket.order', false);

        //generate the html and return
        return $bar->render();
    }

    /**
     * Return the list of availlable third parties organisms
     * @return Array list of thirdparties
     */
    private function getAvailableThirdParties() {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id, name')
                ->from('#__sdi_organism')
                ->where('selectable_as_thirdparty = ' . (int) 1)
                ->order('name');
        $db->setQuery($query);
        $thirdparties = $db->loadObjectList();
        return $thirdparties;
    }

    /**
     * Load the plugins and get the javascript
     * Note: plugins must be in 'easysdi_basket_script' folder and 
     * offer the 'getBasketScript' public function
     * @return String javascript from plugins
     */
    private function getBasketScriptPlugins() {
        JPluginHelper::importPlugin('easysdi_basket_script');
        $app = JFactory::getApplication();
        //get scripts
        $scripts = $app->triggerEvent('getBasketScript');

        //Return merged scripts
        return implode("\n", $scripts);
    }

}

<?php

/**
 * @version     4.0.0
 * @package     com_easysdi_map
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

require_once JPATH_SITE . '/components/com_easysdi_map/helpers/easysdi_map.php';

/**
 * View to edit
 */
class Easysdi_shopViewOrderperimeter extends JViewLegacy {

    protected $state;
    protected $item;
    protected $form;
    protected $params;

    /**
     * Display the view
     */
    public function display($tpl = null) {
        $this->params = JFactory::getApplication()->getParams('com_easysdi_shop');
        
        $this->item = $this->get('Data');
       
        $params_array = $this->params->toArray();
        if(isset($params_array['ordermap'])){
            $this->mapscript = Easysdi_mapHelper::getMapScript($params_array['ordermap']);
        }
        else{
            JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_MAP_PREVIEW_NOT_FOUND'), 'error');
            return;
        }
            
        $this->_prepareDocument();

        parent::display($tpl);
        die();
        
    }

    /**
     * Prepares the document
     */
    protected function _prepareDocument() {
        $app = JFactory::getApplication();
        $title = null;

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

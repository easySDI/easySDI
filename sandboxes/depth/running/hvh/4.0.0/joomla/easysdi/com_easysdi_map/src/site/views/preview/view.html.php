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
class Easysdi_mapViewPreview extends JViewLegacy {

    protected $state;
    protected $item;
    protected $form;
    protected $params;

    /**
     * Display the view
     */
    public function display($tpl = null) {
        $app = JFactory::getApplication();
        
        $sdiuser = sdiFactory::getSdiUser();
                
        $this->state = $this->get('State');
        $this->item = $this->get('Data');
        $this->params = $app->getParams('com_easysdi_map');
        $this->form = $this->get('Form');
        
        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        if(!$sdiuser->canView($this->item->id)){
           JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
           return;
        }
        
        if (!$this->item) {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_MAP_PREVIEW_NOT_FOUND'), 'error');
            return;
        }

        $params_array = $this->params->toArray();
        if(isset($params_array['previewmap'])){
            $this->mapscript = Easysdi_mapHelper::getMapScript($params_array['previewmap']);
        }
        else{
            JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_MAP_PREVIEW_NOT_FOUND'), 'error');
            return;
        }
        
        //Get the default group to use to add the layer
        $model = JModelLegacy::getInstance('map', 'Easysdi_mapModel');
        $item = $model->getData($params_array['previewmap']);
        foreach ($item->groups as $group):
            if($group->isdefault){
                $defaultgroup = $group->alias;
                break;
            }
        endforeach;
            $this->addscript .= ' 
                Ext.onReady(function(){
                    sourceConfig = {id :"'.$this->item->service->alias.'",
                                    ptype: "sdi_gxp_wmssource",
                                    url: "'.$this->item->service->url.'"
                                    };

                    layerConfig = { group: "'.$defaultgroup.'",
                                    name: "'.$this->item->layername.'",
                                    attribution: "'.addslashes ($this->item->attribution).'",
                                    opacity: 1,
                                    source: "'.$this->item->service->alias.'",
                                    tiled: true,
                                    title: "'.$this->item->layername.'",
                                    visibility: true};

                    app.addExtraLayer(sourceConfig, layerConfig)
                });';
            
        $this->_prepareDocument();

        parent::display($tpl);
        die();
        
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
            $this->params->def('page_heading', JText::_('COM_EASYSDI_MAP_DEFAULT_PAGE_TITLE'));
        }

//$title = $this->params->get('page_title', '');
        $title = $this->item->name;
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

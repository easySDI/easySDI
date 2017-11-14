<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_map
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
class Easysdi_mapViewVisualization extends JViewLegacy {

    protected $state;
    protected $item;
    protected $form;
    protected $params;

    /**
     * Display the view
     */
    public function display($tpl = null) {
        JForm::addFieldPath(JPATH_ADMINISTRATOR . '/components/com_easysdi_core/models/fields');

        $app = JFactory::getApplication();

        $this->state = $this->get('State');
        $this->item = $this->get('Data');
        $model = $this->getModel();
        $this->authorizedLayers = $model->getAuthorizedLayers($this->item->id);
        $this->params = $app->getParams('com_easysdi_map');
        $this->form = $this->get('Form');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        //Check the user right
        $this->user = sdiFactory::getSdiUser();
        if (!$this->user->isEasySDI) {
            JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
            JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
            return false;
        }

        if (!empty($this->item->id)) {
            if (!$this->user->authorizeOnVersion($this->item->version_id, sdiUser::viewmanager) && !$this->user->isOrganismManager($this->item->version_id, 'version')) {
                JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
                JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
                return false;
            }
        }


        $pathway = $app->getPathway();
        $pathway->addItem(JText::_("COM_EASYSDI_MAP_BREADCRUMBS_RESOURCES"), JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
        $pathway->addItem(JText::_("COM_EASYSDI_MAP_BREADCRUMBS_VISUALIZATION"), '');

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
            $this->params->def('page_heading', JText::_('com_easysdi_map_DEFAULT_PAGE_TITLE'));
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
        //and make whatever calls you require
        if($this->user->authorizeOnVersion($this->item->version_id, sdiUser::viewmanager)){
            /*$bar->appendButton('Standard', 'apply', JText::_('COM_EASYSDI_CORE_APPLY'), 'visualization.apply', false);
            $bar->appendButton('Separator');*/
            $bar->appendButton('Standard', 'save', JText::_('COM_EASYSDI_CORE_SAVE'), 'visualization.save', false);
            $bar->appendButton('Separator');
            /*$bar->appendButton('Standard', 'remove', JText::_('COM_EASYSDI_CORE_DELETE'), 'visualization.remove', false);
            $bar->appendButton('Separator');*/
        }
        $bar->appendButton('Standard', 'cancel', JText::_('JCancel'), 'visualization.cancel', false);
        //generate the html and return
        return $bar->render();
    }

}

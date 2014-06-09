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
    protected $metadatastates;
    protected $pagination;
    protected $state;
    protected $params;
    protected $parent;

    /** @var sdiUser Description * */
    protected $user;

    /**
     * Display the view
     */
    public function display($tpl = null) {
        $app = JFactory::getApplication();

        //Check user rights
        $this->user = sdiFactory::getSdiUser();
        if (!$this->user->isEasySDI || empty($this->user->role)) {
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
        $db = JFactory::getDbo();
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

        if (!empty($this->state->parentid)) {
            $query = $db->getQuery(true);
            $query->select('r.name, v.name as version_name');
            $query->from('#__sdi_resource r');
            $query->innerJoin('#__sdi_version v on v.resource_id = r.id');
            $query->where('v.id = ' . $this->state->parentid);
            $db->setQuery($query);
            $this->parent = $db->loadObject();
        }


        $filter_status = $this->state->get('filter.status');

        // Load metadata for each resources
        foreach ($this->items as $item) {
            $query = $db->getQuery(true);
            $query->select('m.id, v.name, s.value, s.id AS state, v.id as version');
            $query->from('#__sdi_version v');
            $query->innerJoin('#__sdi_metadata m ON m.version_id = v.id');
            $query->innerJoin('#__sdi_sys_metadatastate s ON s.id = m.metadatastate_id');
            $query->leftJoin('#__sdi_versionlink vl ON vl.child_id = v.id');
            $query->where('v.resource_id = ' . (int) $item->id);
            $query->order('v.name DESC');

            // Check if resource has a "unpublish" version
            $db->setQuery($query);
            $item->hasUnpublishVersion = false;
            foreach ($db->loadObjectList() as $metadata) {
                if ($metadata->state < 3) {
                    $item->hasUnpublishVersion = true;
                }
            }

            if (!empty($filter_status)) {
                $query->where('m.metadatastate_id = ' . (int) $filter_status);
            }

            $db->setQuery($query);
            $item->metadata = $db->loadObjectList();
        }

        // load all metadatastate
        $query = $db->getQuery(true);
        $query->select('s.value, s.id');
        $query->from('#__sdi_sys_metadatastate s');
        $db->setQuery($query);
        $this->metadatastates = $db->loadObjectList();
    }

}

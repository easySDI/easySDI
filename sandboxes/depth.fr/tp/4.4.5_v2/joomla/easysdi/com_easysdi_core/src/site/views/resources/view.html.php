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

        $this->mduk = json_decode($app->getUserState('com_easysdi_core.remove.version.mduk'), null);
        $app->setUserState('com_easysdi_core.remove.version.mduk', null);
        $this->vcall = json_decode($app->getUserState('com_easysdi_core.remove.version.call'), null);
        $app->setUserState('com_easysdi_core.remove.version.call', null);

        $this->state = $this->get('State');
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->params = $app->getParams('com_easysdi_core');

        $this->userOrganisms = $this->user->getOrganisms(array(sdiUser::resourcemanager,
            sdiUser::metadataresponsible,
            sdiUser::metadataeditor,
            sdiUser::diffusionmanager,
            sdiUser::viewmanager,
            sdiUser::extractionresponsible,
            sdiUser::organismmanager));

        //Add scripts from plugins, if any
        $this->document->addScriptDeclaration($this->getResourcesScriptPlugins());

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
            $query->select('DISTINCT m.id, v.name, s.value, s.id AS state, v.id as version, m.published, d.state AS diffusion_published, d.hasdownload, d.hasextraction, das.value AS diffusion_accessscope');
            $query->from('#__sdi_version v');
            $query->innerJoin('#__sdi_metadata m ON m.version_id = v.id');
            $query->innerJoin('#__sdi_sys_metadatastate s ON s.id = m.metadatastate_id');
            $query->leftJoin('#__sdi_versionlink vl ON vl.child_id = v.id');
            $query->leftJoin('#__sdi_diffusion d ON d.version_id = v.id');
            $query->leftJoin('#__sdi_sys_accessscope das ON das.id = d.accessscope_id');
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

            // Load roles for each resources
            $query = $db->getQuery(true);

            $query->select('u.id as user_id, u.username, urr.role_id, r.value as role_name')
                    ->from('#__sdi_user_role_resource urr')
                    ->join('left', '#__sdi_user su ON su.id=urr.user_id')
                    ->join('left', '#__users u ON u.id=su.user_id')
                    ->join('left', '#__sdi_sys_role r ON r.id=urr.role_id')
                    ->where('urr.resource_id=' . $item->id);

            $db->setQuery($query);
            $rows = $db->loadAssocList();

            $item->roles = array();
            foreach ($rows as $row) {
                if (!isset($item->roles[$row['role_id']])) {
                    $item->roles[$row['role_id']] = array(
                        'role' => $row['role_name'],
                        'users' => array()
                    );
                }

                $item->roles[$row['role_id']]['users'][$row['user_id']] = $row['username'];
            }

            //if($item->id==29){var_dump($item->roles);exit();}
        }

        // load all metadatastate
        $query = $db->getQuery(true);
        $query->select('s.value, s.id');
        $query->from('#__sdi_sys_metadatastate s');
        $db->setQuery($query);
        $this->metadatastates = $db->loadObjectList();
    }

    /**
     * Load the plugins and get the javascript
     * Note: plugins must be in 'easysdi_basket_script' folder and 
     * offer the 'getResourcesScript' public function
     * @return String javascript from plugins
     */
    private function getResourcesScriptPlugins() {
        JPluginHelper::importPlugin('easysdi_resources_script');
        $app = JFactory::getApplication();
        //get scripts
        $scripts = $app->triggerEvent('getResourcesScript');

        //Return merged scripts
        return implode("\n", $scripts);
    }

}

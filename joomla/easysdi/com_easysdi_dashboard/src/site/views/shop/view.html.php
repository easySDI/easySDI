<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_dashboard
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of Easysdi_dashboard.
 */
class Easysdi_dashboardViewShop extends JViewLegacy {

    protected $items;
    protected $pagination;
    protected $state;
    protected $params;

    /**
     * Display the view
     */
    public function display($tpl = null) {
        //Load admin language file
        $lang = JFactory::getLanguage();
        $lang->load('com_easysdi_dashboard', JPATH_ADMINISTRATOR);

        $app = JFactory::getApplication();

        $this->user = sdiFactory::getSdiUser();
        if (!$this->user->isEasySDI) {
            $app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
            $app->redirect("index.php");
            return false;
        }
        
        if (!$this->hasDashboardAccess($this->user)) {
            $app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
            $app->redirect("index.php");
            return false;
        }

        $this->params = $app->getParams('com_easysdi_dashboard');
        $this->graphcolours = $this->params->get('graphcolours');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        $this->_prepareDocument();
        parent::display($tpl);
    }

    /**
     * Check if user has dashboard access AND SAVES IT in session (for json calls)
     * @param sdiUser $sdiUser
     * @return type
     */
    private function hasDashboardAccess($sdiUser) {
        
        $tmpOrgList = array();
        //get all orgs with rights for dashboard
        foreach (
        array(
            $sdiUser::resourcemanager,
            $sdiUser::diffusionmanager,
            $sdiUser::extractionresponsible,
            $sdiUser::organismmanager)
        as $roleId) {
            foreach ($sdiUser->role[$roleId] as $org) {
                $tmpOrgList[$org->id] = $org->name;
            }
        }

        $session = JFactory::getSession();
        $session->set('organismDashboardAccess', $tmpOrgList);
        
        return count($tmpOrgList) > 0;
    }

    /**
     * Prepares the document
     */
    protected function _prepareDocument() {
        $app = JFactory::getApplication();
        $menus = $app->getMenu();

        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menu = $menus->getActive();
        if ($menu) {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        } else {
            $this->params->def('page_heading', JText::_('COM_EASYSDI_DASHBOARD_DEFAULT_PAGE_TITLE'));
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

        $roles = array(sdiUser::member, sdiUser::organismmanager);
        $this->organisms = $this->user->getOrganisms($roles);

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

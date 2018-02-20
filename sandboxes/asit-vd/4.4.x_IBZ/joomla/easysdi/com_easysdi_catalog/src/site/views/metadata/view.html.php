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
require_once JPATH_BASE . '/administrator/components/com_easysdi_core/libraries/easysdi/common/SdiToolbar.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/helpers/easysdi_core.php';

/**
 * View to edit
 */
class Easysdi_catalogViewMetadata extends JViewLegacy {

    protected $state;
    protected $item;
    protected $form;
    protected $params;
    protected $actionToolbar;
    public $formHtml = '';

    /** @var sdiUser */
    public $user;

    /** @var JDatabaseDriver */
    private $db;

    /** @var DOMDocument */
    public $structure;

    public function __construct($config = array()) {
        $this->db = JFactory::getDbo();

        parent::__construct($config);
    }

    /**
     * Display the view
     */
    public function display($tpl = null) {


        $app = JFactory::getApplication();
        //Check user rights
        $this->user = sdiFactory::getSdiUser();
        if (!$this->user->isEasySDI) {
            JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
            JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
            return;
        }

        $this->state = $this->get('State');
        $this->item = $this->get('Data');
        $this->params = $app->getParams('com_easysdi_catalog');
        $this->form = $this->get('Form');


        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        $this->getCurrentResourceType();
        $this->buildForm();
        $this->buildActionToolbar();
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

    protected function buildForm() {
        $structure = $this->structure = $this->get('Structure');

        $fhg = new FormHtmlGenerator($this->form, $structure);

        $this->formHtml = $fhg->buildForm();
    }

    /**
     * 
     * @return string
     */
    private function buildActionToolbar() {
        $queryImportref = $this->db->getQuery(true);
        $queryImportref->select('ir.id, ir.name');
        $queryImportref->from('#__sdi_importref ir');
        $queryImportref->where('ir.state = 1');
        $queryImportref->order('ir.name DESC');
        $this->db->setQuery($queryImportref);
        $importref = $this->db->loadObjectList();

        $queryMD = $this->db->getQuery(true);
        $queryMD->select('m.id, v.name, s.value, s.id AS state, v.id as version');
        $queryMD->from('#__sdi_version v');
        $queryMD->innerJoin('#__sdi_metadata m ON m.version_id = v.id');
        $queryMD->innerJoin('#__sdi_sys_metadatastate s ON s.id = m.metadatastate_id');
        $queryMD->where('m.id = ' . (int) $this->item->id);
        $queryMD->order('v.name DESC');
        $this->db->setQuery($queryMD);
        $metadata = $this->db->loadObject();

        $toolbar = new SdiToolbar();

        //build save and tools dropdowns
        $saves = array();
        $tools = array();
        switch ($metadata->state) {
            case sdiMetadata::INPROGRESS:
                if ($this->user->authorizeOnMetadata($this->item->id, sdiUser::metadataeditor) || $this->user->authorizeOnMetadata($this->item->id, sdiUser::metadataresponsible)) {

                    //Replicate
                    $tools[JText::_('COM_EASYSDI_CATALOG_TOOLS_DROP_REPLICATE')] = 'metadata.replicate';
                    //Import
                    foreach ($importref as $ir) {
                        $tools[JText::_('COM_EASYSDI_CATALOG_TOOLS_DROP_IMPORT_FROM') . $ir->name] = 'metadata.import.' . $ir->id;
                    }
                    //Preview
                    $tools[JText::_('COM_EASYSDI_CATALOG_TOOLS_DROP_PREVIEW_XML')] = 'metadata.show';
                    $tools[JText::_('COM_EASYSDI_CATALOG_TOOLS_DROP_PREVIEW_XHTML')] = 'metadata.preview';
                    //Reset
                    $tools[JText::_('COM_EASYSDI_CATALOG_TOOLS_DROP_RESET')] = 'metadata.reset';
                    //Save
                    $saves[JText::_('COM_EASYSDI_CATALOG_SAVE_DROP_SAVE_AND_CONTINUE')] = 'metadata.saveAndContinue';
                    $saves[JText::_('COM_EASYSDI_CATALOG_SAVE_DROP_SAVE_AND_VALIDATE')] = 'metadata.valid';
                    $saves[JText::_('COM_EASYSDI_CATALOG_SAVE_DROP_SAVE_AND_CLOSE')] = 'metadata.save';
                }
                break;
            case sdiMetadata::VALIDATED:
                if ($this->user->authorizeOnMetadata($this->item->id, sdiUser::metadataresponsible)) {

                    //Preview
                    $tools[JText::_('COM_EASYSDI_CATALOG_TOOLS_DROP_PREVIEW_XML')] = 'metadata.show';
                    $tools[JText::_('COM_EASYSDI_CATALOG_TOOLS_DROP_PREVIEW_XHTML')] = 'metadata.preview';
                    //Reset
                    $tools[JText::_('COM_EASYSDI_CATALOG_TOOLS_DROP_RESET')] = 'metadata.reset';
                    //Save
                    $saves[JText::_('COM_EASYSDI_CATALOG_SAVE_DROP_SAVE_AND_CONTINUE')] = 'metadata.valid';
                    $saves[JText::_('COM_EASYSDI_CATALOG_SAVE_DROP_SAVE_AND_PUBLISH')] = array('metadata.setPublishDate', json_encode(array('version' => $metadata->version, 'metadata' => $metadata->id)));
                    $saves[JText::_('COM_EASYSDI_CATALOG_SAVE_DROP_SAVE_AND_CLOSE')] = 'metadata.validAndClose';
                    $saves[JText::_('COM_EASYSDI_CATALOG_SAVE_DROP_SAVE_AND_INPROGRESS')] = 'metadata.inprogress';
                }
                break;
            case sdiMetadata::PUBLISHED:
                if ($this->user->authorizeOnMetadata($this->item->id, sdiUser::metadataresponsible)) {

                    //Preview
                    $tools[JText::_('COM_EASYSDI_CATALOG_TOOLS_DROP_PREVIEW_XML')] = 'metadata.show';
                    $tools[JText::_('COM_EASYSDI_CATALOG_TOOLS_DROP_PREVIEW_XHTML')] = 'metadata.preview';
                    //Reset
                    $tools[JText::_('COM_EASYSDI_CATALOG_TOOLS_DROP_RESET')] = 'metadata.reset';
                    //Save
                    $saves[JText::_('COM_EASYSDI_CATALOG_SAVE_DROP_SAVE_AND_CONTINUE')] = 'metadata.publish';
                    $saves[JText::_('COM_EASYSDI_CATALOG_SAVE_DROP_SAVE_AND_INPROGRESS')] = 'metadata.inprogress';
                    $saves[JText::_('COM_EASYSDI_CATALOG_SAVE_DROP_SAVE_AND_CLOSE')] = 'metadata.publishAndClose';
                }
                break;
        }

        //Add save dropbown
        if (count($saves) > 0) {
            $toolbar->append(JText::_('COM_EASYSDI_CATALOG_SAVE_DROP_TITLE'), 'sdi-metadata-save', 'btn-small btn-success', $saves, true, 'icon-apply');
        }
        //Add tools dropbown
        if (count($tools) > 0) {
            $toolbar->append(JText::_('COM_EASYSDI_CATALOG_TOOLS_DROP_TITLE'), 'sdi-metadata-tools', 'btn-small', $tools, true, 'icon-wrench');
        }
        //Add cancel button 
        $back_url = array('root' => 'index.php',
            'option' => 'com_easysdi_core',
            'view' => 'resources',
            'parentid' => $this->state->parentid);
        $toolbar->appendBtnRoute(JText::_('COM_EASYSDI_CATALOG_BACK'), JRoute::_(Easysdi_coreHelper::array2URL($back_url)), 'btn-small btn-back-ressources', '', '', 'icon-cancel');

        $this->actionToolbar = $toolbar->renderToolbar();
    }

    /**
     * get HTML toolbar for top and bottom of MD edit form
     * @return string HTML toolbar
     */
    public function getActionToolbar() {

        return $this->actionToolbar;
    }

    public function getTitle() {
        $query = $this->db->getQuery(true);

        $query->select('m.id, v.name, s.value, s.id AS state, v.id as version, r.name as resource_name');
        $query->from('#__sdi_version v');
        $query->innerJoin('#__sdi_metadata m ON m.version_id = v.id');
        $query->innerJoin('#__sdi_sys_metadatastate s ON s.id = m.metadatastate_id');
        $query->innerJoin('#__sdi_resource r ON r.id = v.resource_id');
        $query->where('m.id = ' . (int) $this->item->id);
        $query->order('v.name DESC');


        $this->db->setQuery($query);
        $metadata = $this->db->loadObjectList();
        $title = $metadata[0];

        //set span label
        $labelClass = '';
        switch ($title->state) {
            case 1:
                $labelClass = 'label-warning';
                break;
            case 2:
            case 5:
                $labelClass = 'label-info';
                break;
            case 3:
                $labelClass = 'label-success';
                break;
            case 4:
                $labelClass = 'label-inverse';
                break;
        }
        $title->state_label = '<span class="label ' . $labelClass . '">' . JText::_(strtoupper($title->value)) . '</span>';

        return $title;
    }

    /**
     * get collapse/expand HTML button
     * @return string HTML button
     */
    public function getToggleCollapseButton() {
        $allOpen = $this->params->get('editmetadatafieldsetstate') == "allopen";
        $btnHTML = '<a id="btn_toggle_all" class="btn btn-mini pull-right">' .
                JText::_($allOpen ? 'COM_EASYSDI_CATALOG_CLOSE_ALL' : 'COM_EASYSDI_CATALOG_OPEN_ALL')
                . '</a>';
        return $btnHTML;
    }

    private function getCurrentResourceType() {
        $query = $this->db->getQuery(true)
                ->select('rt.id')
                ->from('#__sdi_resourcetype rt')
                ->innerJoin('#__sdi_resource r ON r.resourcetype_id=rt.id')
                ->innerJoin('#__sdi_version v ON v.resource_id=r.id')
                ->where('v.id=' . (int) $this->item->version_id);
        $this->db->setQuery($query);
        $this->item->resourcetype_id = (int) $this->db->loadResult();
    }

    /**
     * Return a list of all resource type
     * 
     * @return stdClass[]
     */
    public function getResourceType() {
        $query = $this->db->getQuery(true);

        $query->select('rt.id, rt.name, rt.guid, rt.versioning');
        $query->from('#__sdi_resourcetype rt');
        $query->order('rt.name DESC');

        $this->db->setQuery($query);
        $resourcetype = $this->db->loadObjectList();

        $first = array('id' => '0', 'name' => '', 'guid' => '', 'versioning' => '');
        array_unshift($resourcetype, (object) $first);

        return $resourcetype;
    }

    /**
     * @return stdClass[] List of all status
     */
    public function getStatusList() {
        $query = $this->db->getQuery(true);

        $query->select('ms.id, ms.value');
        $query->from('#__sdi_sys_metadatastate ms');
        $query->order('ms.id ASC');

        $this->db->setQuery($query);
        $metadatastate = $this->db->loadObjectList();

        $first = array('id' => '', 'value' => '');
        array_unshift($metadatastate, (object) $first);

        return $metadatastate;
    }

}

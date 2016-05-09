<?php

/**
 * @version     4.4.0
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013-2016. All rights reserved.
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
    public function getActionToolbar() {
        $query = $this->db->getQuery(true);

        $query->select('m.id, v.name, s.value, s.id AS state, v.id as version_id');
        $query->from('#__sdi_version v');
        $query->innerJoin('#__sdi_metadata m ON m.version_id = v.id');
        $query->innerJoin('#__sdi_sys_metadatastate s ON s.id = m.metadatastate_id');
        $query->where('m.id = ' . (int) $this->item->id);
        $query->order('v.name DESC');

        $this->db->setQuery($query);
        $metadata = $this->db->loadObject();

        $toolbar = new SdiToolbar();

        switch ($metadata->state) {
            case sdiMetadata::INPROGRESS:
                if ($this->user->authorizeOnMetadata($this->item->id, sdiUser::metadataeditor) || $this->user->authorizeOnMetadata($this->item->id, sdiUser::metadataresponsible)) {

                    $toolbar->append(JText::_('COM_EASYSDI_CATALOG_PREVIEW_ITEM'), 'previsulisation', 'btn-small', array(JText::_('COM_EASYSDI_CATALOG_PREVIEW_XML_ITEM') => 'metadata.show', JText::_('COM_EASYSDI_CATALOG_PREVIEW_XHTML_ITEM') => 'metadata.preview'), true);
                    $toolbar->append(JText::_('COM_EASYSDI_CATALOG_SAVE_ITEM'), 'enregistrer', 'btn-small', array(JText::_('COM_EASYSDI_CATALOG_SAVE_AND_CONTINUE_ITEM') => 'metadata.saveAndContinue', JText::_('COM_EASYSDI_CATALOG_SAVE_AND_CLOSE_ITEM') => 'metadata.save'), true);
                    $toolbar->append(JText::_('COM_EASYSDI_CATALOG_VALIDATE_ITEM'), 'Valider', 'btn-small btn-success', array(JText::_('COM_EASYSDI_CATALOG_VALIDATE_ITEM') => 'metadata.control', JText::_('COM_EASYSDI_CATALOG_VALIDATE_AND_SAVE_ITEM') => 'metadata.valid'), true);
                }
                break;
            case sdiMetadata::VALIDATED:
                if ($this->user->authorizeOnMetadata($this->item->id, sdiUser::metadataresponsible)) {
                    
                    $toolbar->append(JText::_('COM_EASYSDI_CATALOG_PREVIEW_ITEM'), 'previsulisation', 'btn-small', array(JText::_('COM_EASYSDI_CATALOG_PREVIEW_XML_ITEM') => 'metadata.show', JText::_('COM_EASYSDI_CATALOG_PREVIEW_XHTML_ITEM') => 'metadata.preview'), true);
                    $toolbar->append(JText::_('COM_EASYSDI_CATALOG_SAVE_ITEM'), 'enregistrer', 'btn-small', array(JText::_('COM_EASYSDI_CATALOG_SAVE_AND_CONTINUE_ITEM') => 'metadata.valid', JText::_('COM_EASYSDI_CATALOG_SAVE_AND_CLOSE_ITEM') => 'metadata.validAndClose'), true);
                    $toolbar->append(JText::_('COM_EASYSDI_CATALOG_INPROGRESS_ITEM'), 'inprogress', 'btn-small', 'metadata.inprogress');
                    $toolbar->append(JText::_('COM_EASYSDI_CATALOG_PUBLISH_ITEM'), 'publier', 'btn-small btn-success', array(JText::_('COM_EASYSDI_CATALOG_VALIDATE_ITEM') => 'metadata.control', JText::_('COM_EASYSDI_CATALOG_PUBLISH_AND_SAVE_ITEM') => array('metadata.setPublishDate', json_encode(array('version' => $metadata->version_id, 'metadata' => $metadata->id)))), true);
                }
                break;
            case sdiMetadata::PUBLISHED:
                if ($this->user->authorizeOnMetadata($this->item->id, sdiUser::metadataresponsible)) {
                    $toolbar->append(JText::_('COM_EASYSDI_CATALOG_PREVIEW_ITEM'), 'previsulisation', 'btn-small', array(JText::_('COM_EASYSDI_CATALOG_PREVIEW_XML_ITEM') => 'metadata.show', JText::_('COM_EASYSDI_CATALOG_PREVIEW_XHTML_ITEM') => 'metadata.preview'), true);
                    $toolbar->append(JText::_('COM_EASYSDI_CATALOG_SAVE_ITEM'), 'enregistrer', 'btn-small', array(JText::_('COM_EASYSDI_CATALOG_SAVE_AND_CONTINUE_ITEM') => 'metadata.publish', JText::_('COM_EASYSDI_CATALOG_SAVE_AND_CLOSE_ITEM') => 'metadata.publishAndClose'), true);
                    $toolbar->append(JText::_('COM_EASYSDI_CATALOG_INPROGRESS_ITEM'), 'inprogress', 'btn-small', 'metadata.inprogress');
                }
                break;
        }
        
        return $toolbar->renderToolbar();
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

        return $metadata[0];
    }

    public function getTopActionBar() {

        $query = $this->db->getQuery(true);

        $query->select('ir.id, ir.name');
        $query->from('#__sdi_importref ir');
        $query->where('ir.state = 1');
        $query->order('ir.name DESC');

        $this->db->setQuery($query);
        $importref = $this->db->loadObjectList();

        $query = $this->db->getQuery(true);

        $query->select('m.id, v.name, s.value, s.id AS state, v.id as version');
        $query->from('#__sdi_version v');
        $query->innerJoin('#__sdi_metadata m ON m.version_id = v.id');
        $query->innerJoin('#__sdi_sys_metadatastate s ON s.id = m.metadatastate_id');
        $query->where('m.id = ' . (int) $this->item->id);
        $query->order('v.name DESC');

        $this->db->setQuery($query);
        $metadata = $this->db->loadObject();

        $importrefactions = array();
        $importrefactions[JText::_('COM_EASYSDI_CATALOG_REPLICATE')] = 'metadata.replicate';
        foreach ($importref as $ir) {
            $importrefactions[$ir->name] = 'metadata.import.' . $ir->id;
        }

        $toolbar = new SdiToolbar();

        if ($this->params->get('editmetadatafieldsetstate') == "allopen") {
            $toolbar->append(JText::_('COM_EASYSDI_CATALOG_CLOSE_ALL'), 'btn_toggle_all', 'btn-small');
        } else {
            $toolbar->append(JText::_('COM_EASYSDI_CATALOG_OPEN_ALL'), 'btn_toggle_all', 'btn-small');
        }
        
        if($this->user->authorizeOnMetadata($metadata->id, sdiUser::metadataeditor) || $this->user->authorizeOnMetadata($metadata->id, sdiUser::metadataresponsible)){
            if ($metadata->state == sdiMetadata::INPROGRESS) {
                $toolbar->append(JText::_('COM_EASYSDI_CATALOG_IMPORT'), 'import', 'btn-small', $importrefactions, true);
            }

            $reset_url = JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.edit&id='.$this->item->id);

            $toolbar->appendBtnRoute(JText::_('COM_EASYSDI_CATALOG_RESET'), $reset_url, 'btn-small', 'btn-reset');
        }
        
        $back_url = array('root' => 'index.php',
            'option' => 'com_easysdi_core',
            'view' => 'resources',
            'parentid' => $this->state->parentid);
        
        $toolbar->appendBtnRoute(JText::_('COM_EASYSDI_CATALOG_BACK'), JRoute::_(Easysdi_coreHelper::array2URL($back_url)), 'btn-small btn-danger btn-back-ressources');

        return $toolbar->renderToolbar();
    }
    
    private function getCurrentResourceType(){
        $query = $this->db->getQuery(true)
                ->select('rt.id')
                ->from('#__sdi_resourcetype rt')
                ->innerJoin('#__sdi_resource r ON r.resourcetype_id=rt.id')
                ->innerJoin('#__sdi_version v ON v.resource_id=r.id')
                ->where('v.id='.(int)$this->item->version_id);
        $this->db->setQuery($query);
        $this->item->resourcetype_id = (int)$this->db->loadResult();
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

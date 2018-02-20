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

JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_easysdi_catalog/tables');

require_once JPATH_COMPONENT . '/controller.php';

require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/FormHtmlGenerator.php';

require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/catalog/sdimetadata.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/catalog/cswmetadata.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/helpers/easysdi_core.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/common/EText.php';

require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/dao/SdiLanguageDao.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/dao/SdiNamespaceDao.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/FormUtils.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/CswMerge.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/FormGenerator.php';
require_once JPATH_BASE . '/administrator/components/com_easysdi_core/libraries/easysdi/user/sdiuser.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/FormStereotype.php';

/**
 * Metadata controller class.
 */
class Easysdi_catalogControllerMetadata extends Easysdi_catalogController {

    /** @var JDatabaseDriver */
    private $db = null;

    /** @var JSession */
    private $session;

    /** @var DOMDocument */
    private $structure;

    /** @var DOMXPath */
    private $domXpathStr;

    /** @var SdiNamespaceDao */
    private $nsdao;

    /** @var SdiLanguageDao */
    private $ldao;
    private $catalog_uri = 'http://www.easysdi.org/2011/sdi/catalog';
    private $catalog_prefix = 'catalog';
    private $cswUri = 'http://www.opengis.net/cat/csw/2.0.2';

    /** @var array array of namespace */
    private $nsArray = array();

    /** @var string[] Submit Form data */
    private $data;

    /** @var Easysdi_coreHelper */
    private $core_helpers;

    function __construct() {
        $this->db = JFactory::getDbo();
        $this->session = JFactory::getSession();
        $this->nsdao = new SdiNamespaceDao();
        $this->ldao = new SdiLanguageDao();
        $this->structure = new DOMDocument('1.0', 'utf-8');
        $this->structure->preserveWhiteSpace = false;
        $_structure = $this->session->get('structure');

        if (!empty($_structure))
            $this->structure->loadXML(unserialize($_structure));
        $this->structure->normalizeDocument();
        $this->domXpathStr = new DOMXPath($this->structure);
        $this->data = JFactory::getApplication()->input->get('jform', array(), 'array');
        foreach ($this->nsdao->getAll() as $ns) {
            $this->nsArray[$ns->prefix] = $ns->uri;
        }

        $this->core_helpers = new Easysdi_coreHelper();

        parent::__construct();
    }

    public function synchronize() {
        $metadata_id = JFactory::getApplication()->input->get('id', null, 'int');

        $query = $this->db->getQuery(true);
        $query->select('v.id, v.name, r.resourcetype_id, r.id AS resource_id, m.id AS metadata_id, m.guid AS fileidentifier');
        $query->from('#__sdi_metadata m');
        $query->innerJoin('#__sdi_version v ON m.version_id = v.id');
        $query->innerJoin('#__sdi_resource r ON v.resource_id = r.id');
        $query->where('m.id = ' . $metadata_id);
        $this->db->setQuery($query);

        $version = $this->db->loadObject();
        //$version->name = date("Y-m-d H:i:s");

        $versions = $this->core_helpers->getViralVersionnedChild($version);

        $this->_syncronize($versions);

        // Redirect to resources list
        JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_CATALOG_METADATA_SYNCHRONIZE_SUCCESS'), 'message');
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
    }

    private function _syncronize($versions) {
        $nsdao = new SdiNamespaceDao();

        foreach ($versions as $version) {
            if (!empty($version->children)) {
                $parent_sdimetadata = new sdiMetadata($version->metadata_id);
                $parentDom = $parent_sdimetadata->load();
                $parentXPath = new DOMXPath($parentDom);

                foreach ($nsdao->getAll() as $ns) {
                    $parentXPath->registerNamespace($ns->prefix, $ns->uri);
                }

                $db = JFactory::getDbo();
                foreach ($version->children as $children) {
                    $query = $db->getQuery(true);
                    $query->select('rtli.xpath');
                    $query->from('#__sdi_resourcetypelinkinheritance rtli');
                    $query->innerJoin('#__sdi_resourcetypelink rtl ON rtli.resourcetypelink_id = rtl.id');
                    $query->where('rtl.parent_id = ' . (int) $version->resourcetype_id);
                    $query->where('rtl.child_id =' . (int) $children->resourcetype_id);

                    $db->setQuery($query);
                    $xpaths = $db->loadObjectList();

                    $child_sdimetadata = new sdiMetadata($children->metadata_id);
                    $childDom = $child_sdimetadata->load();
                    $childXPath = new DOMXpath($childDom);

                    foreach ($nsdao->getAll() as $ns) {
                        $childXPath->registerNamespace($ns->prefix, $ns->uri);
                    }

                    foreach ($xpaths as $xpath) {
                        foreach ($parentXPath->query($xpath->xpath) as $parentNode) {
                            $parentNodeXPath = $parentNode->getNodePath();

                            $childNode = $childXPath->query($parentNodeXPath)->item(0);

                            if ($childNode !== null) {
                                $childParentNode = $childNode->parentNode;
                                $childParentNode->replaceChild($childDom->importNode($parentNode, true), $childNode);
                            } else {
                                // we are trying to synchronize an xpath which doesn't exist in the children element
                                $tmpXPathArr = explode('/', $parentNodeXPath);
                                $depth = 0;
                                do {
                                    $depth++;
                                    $tmpXPath = implode('/', array_slice($tmpXPathArr, 0, count($tmpXPathArr) - $depth));

                                    $childNode = $childXPath->query($tmpXPath)->item(0);
                                } while ($childNode === null && $depth <= count($tmpXPathArr));

                                if ($childNode === null)
                                    continue;

                                do {
                                    $depth--;
                                    $tmpXPath = implode('/', array_slice($tmpXPathArr, 0, count($tmpXPathArr) - $depth));

                                    $childNode->appendChild($childDom->importNode($parentXPath->query($tmpXPath)->item(0), ($depth === 0)));
                                    $childNode = $childXPath->query($tmpXPath)->item(0);
                                } while ($depth > 0);
                            }
                        }
                    }

                    $request = $this->CreateUpdateBody($childXPath->query('/*/*')->item(0), $children->fileidentifier);

                    if (!$child_sdimetadata->update($request->saveXML())) {
                        return false;
                    } else {
                        $query = $db->getQuery(true);
                        $query->update('#__sdi_metadata m');
                        $query->set('lastsynchronization = ' . $query->quote(date('Y-m-d h:i:s')));
                        $query->set('synchronized_by = ' . (int) $version->metadata_id);
                        $query->where('id = ' . (int) $children->metadata_id);

                        $db->setQuery($query);
                        $db->execute();
                    }
                }

                $this->_syncronize($version->children);
            }
        }
    }

    /**
     * Method to check out an item for editing and redirect to the edit form.
     *
     * @since	1.6
     */
    public function edit() {
        $app = JFactory::getApplication();

        // Get the previous edit id (if any) and the current edit id.
        $previousId = (int) $app->getUserState('com_easysdi_catalog.edit.metadata.id');
        $editId = JFactory::getApplication()->input->getInt('id', null, 'array');
        $import = JFactory::getApplication()->input->get('import', array(), 'array');
        if (key_exists('xml_file', $_FILES)) {
            if ($_FILES['xml_file']['type'] == 'text/xml') {
                if ($xml = file_get_contents($_FILES['xml_file']['tmp_name'])) {
                    $import['xml'] = $xml;
                }
            } else {
                JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_CATALOG_METADATA_XML_IMPORT_ERROR'), 'error');
            }
        }

        // Set the user id for the user to edit in the session.
        $app->setUserState('com_easysdi_catalog.edit.metadata.id', $editId);

        $app->setUserState('com_easysdi_catalog.edit.metadata.import', $import);

        // Get the model.
        $model = $this->getModel('Metadata', 'Easysdi_catalogModel');

        // Check out the item
        if ($editId) {
            $model->checkout($editId);
        }

        // Check in the previous user.
        if ($previousId) {
            $model->checkin($previousId);
        }

        // Redirect to the edit screen.
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_catalog&view=metadata&layout=edit', false));
    }

    public function import() {
        $query = $this->db->getQuery(true);

        $query->select('*');
        $query->from('#__sdi_importref');
        $query->where('id = ' . (int) $_GET['id']);

        $this->db->setQuery($query);
        $importRef = $this->db->loadObject();

        $response = array();
        $response['success'] = true;
        $response['result'] = $importRef;
        echo json_encode($response);
        die();
    }

    /**
     * Change metadata status to archived
     */
    public function archive() {
        $this->changeStatusAndUpdate(sdiMetadata::ARCHIVED);
    }

    /**
     * Change metadata status to inprogress
     */
    public function inprogress() {
        if (count($this->data) > 0) {
            $this->changeStatusAndSave(sdiMetadata::INPROGRESS);
        } else {
            $this->changeStatusAndUpdate(sdiMetadata::INPROGRESS);
        }
    }

    /**
     * Change metadata status to validated
     */
    public function valid() {
        $this->changeStatusAndSave(sdiMetadata::VALIDATED);
    }

    /**
     * Change metadata status to validated and retturn to resources page
     */
    public function validAndClose() {
        $this->changeStatusAndSave(sdiMetadata::VALIDATED, FALSE);
    }

    /**
     * Change metadata status to publish
     */
    public function publish($continue = true) {
        if (!$continue || count($this->data) > 0) {
            $this->changeStatusAndSave(sdiMetadata::PUBLISHED, $continue);
        } else {
            $this->changeStatusAndUpdate(sdiMetadata::PUBLISHED);
        }
    }

    /**
     * Change metadata status to publish
     */
    public function publishAndClose() {
        $this->publish(false);
        //$this->changeStatusAndSave(sdiMetadata::PUBLISHED, FALSE);
    }

    /**
     * Change metadata status and save
     * 
     * @param type $statusId
     */
    private function changeStatusAndSave($statusId, $continue = true) {
        $viral = JFactory::getApplication()->input->get('viral', 0, 'integer');

        if (isset($this->data['metadatastate_id']) && $statusId == $this->data['metadatastate_id']) {
            $this->save(null, true, $continue);
        } elseif (isset($viral) && $viral == 1) {
            if ($this->changeStatusViral($this->data['id'], $statusId, $this->data['published'])) {
                JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_CATALOG_METADATA_CHANGE_STATUS_OK'), 'message');
                $this->save(null, true, $continue);
            } else {
                JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_CATALOG_METADATA_CHANGE_STATUS_ERROR'), 'error');
                $this->setRedirect(JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.edit&id=' . $this->data['id']));
            }
        } elseif ($this->changeStatus($this->data['id'], $statusId, $this->data['published']) != FALSE) {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_CATALOG_METADATA_CHANGE_STATUS_OK'), 'message');
            $this->save(null, true, $continue);
        } else {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_CATALOG_METADATA_CHANGE_STATUS_ERROR'), 'error');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.edit&id=' . $this->data['id']));
        }

        if (sdiMetadata::VALIDATED == $statusId)
            $this->publishableNotification();
    }

    private function changeStatusAndUpdate($statusId) {

        $id = JFactory::getApplication()->input->get('id', null, 'int');
        $redirectURL = JFactory::getApplication()->input->get('redirectURL', '');
        if (empty($redirectURL)) {
            $redirectURL = 'index.php?option=com_easysdi_catalog&task=metadata.edit&id=' . $this->data['id'];
        }

        if (isset($this->data['metadatastate_id']) && $statusId == $this->data['metadatastate_id']) {
            $changeStatus = null;
        } else {
            $published = JFactory::getApplication()->input->get('published', null, 'string');
            $viral = JFactory::getApplication()->input->get('viral', 0, 'integer');
            if (isset($published)) {

                if (isset($viral) && $viral == 1) {
                    $changeStatus = $this->changeStatusViral($id, $statusId, $published);
                } else {
                    $changeStatus = $this->changeStatus($id, $statusId, $published);
                }
            } else {
                $changeStatus = $this->changeStatus($id, $statusId);
            }
        }

        if ($changeStatus === false) {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_CATALOG_METADATA_CHANGE_STATUS_ERROR'), 'error');
            $this->setRedirect(JRoute::_(Easysdi_coreHelper::array2URL($redirectURL), false));
        } else {
            $this->update($id);
        }
    }

    /**
     * Update SDI elements
     * 
     * @param int $id metadata id
     */
    private function update($id) {
        $smd = new sdiMetadata($id);
        if ($smd->updateSDIElement()) {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_CATALOG_METADATA_CHANGE_STATUS_OK'), 'message');
        } else {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_CATALOG_METADATA_CHANGE_STATUS_ERROR'), 'error');
        }
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
    }

    /**
     * 
     * @return stdClass[] result list of resource
     */
    public function searchresource() {
        $lang = JFactory::getLanguage();

        $query = $this->db->getQuery(true);

        $query->select('m.id, r.name, v.name as vname, m.guid, t.text1 as rt_name, ms.value as status');
        $query->from('#__sdi_resource r');
        $query->innerJoin('#__sdi_resourcetype rt on r.resourcetype_id = rt.id');
        $query->innerJoin('#__sdi_translation t ON t.element_guid = rt.guid');
        $query->innerJoin('#__sdi_language AS l ON t.language_id = l.id');
        $query->innerJoin('#__sdi_version v on v.resource_id = r.id');
        $query->innerJoin('(SELECT * FROM #__sdi_metadata ORDER BY created DESC)  m on m.version_id = v.id');
        $query->innerJoin('#__sdi_sys_metadatastate ms on ms.id = m.metadatastate_id');
        $query->where('l.code = ' . $query->quote($lang->getTag()));
        if (array_key_exists('version', $_POST)) {
            if ($_POST['version'] == 'last') {
                $query->group(' r.name');
            }
        }

        if ($_POST['status_id'] != '') {
            $query->where('m.metadatastate_id = ' . (int) $_POST['status_id']);
        }
        if (!empty($_POST['resourcetype_id'])) {
            $query->where('r.resourcetype_id = ' . (int) $_POST['resourcetype_id']);
        }
        if ($_POST['resource_name'] != '') {
            $query->where('r.name like ' . $query->quote('%' . $query->escape($_POST['resource_name']) . '%'));
        }
        if (!empty($_POST['organism_id'])) {
            $query->where('r.organism_id = ' . (int) $_POST['organism_id']);
        }

        $user = new sdiUser();
        //user's organism's categories
        $categories = $user->getMemberOrganismsCategoriesIds();
        if (is_null($categories) || count($categories) == 0) {
            $categories = array(0);
        }

        //user's organism
        $organisms = $user->getMemberOrganisms();

        //apply resource's accessscope
        $query->where("("
                . "r.accessscope_id = 1 "
                . "OR (r.accessscope_id = 2 AND (SELECT COUNT(*) FROM #__sdi_accessscope a WHERE a.category_id IN (" . implode(',', $categories) . ") AND a.entity_guid = r.guid ) > 0) "
                . "OR (r.accessscope_id = 3 AND (SELECT COUNT(*) FROM #__sdi_accessscope a WHERE a.organism_id = " . (int) $organisms[0]->id . " AND a.entity_guid = r.guid ) = 1) "
                . "OR (r.accessscope_id = 4 AND (SELECT COUNT(*) FROM #__sdi_accessscope a WHERE a.user_id = " . (int) $user->id . " AND a.entity_guid = r.guid ) = 1)"
                . ")"
        );

        $this->db->setQuery($query);
        $resources = $this->db->loadObjectList();

        $response = array();
        $response['success'] = true;
        $response['total'] = count($resources);
        $response['result'] = $resources;
        echo json_encode($response);
        die();
    }

    /**
     * Show xml preview
     */
    public function show() {
        $this->save($_POST['jform'], false);
        $this->structure->formatOutput = true;
        $xml = $this->structure->saveXML();

        $response = array();
        $response['success'] = true;
        $response['xml'] = '<pre class="brush: xml">' . addslashes(htmlspecialchars($this->structure->saveXML())) . '</pre>';
        echo json_encode($response);
        die();
    }

    /**
     * Show xhtml preview
     */
    public function preview() {
        $this->save($_POST['jform'], false);

        $lang = JFactory::getLanguage();

        $cswm = new cswmetadata();
        $cswm->init($this->structure->firstChild);
        $extend = $cswm->extend('', '', $_POST['preview'], 1, $lang->getTag());

        $response = array();
        $response['success'] = true;
        $response['guid'] = $_POST['jform']['guid'];

        $this->session->set($_POST['jform']['guid'], '<div class="well">' . $cswm->applyXSL(array('preview' => $_POST['preview']), null) . '</div>');

        echo json_encode($response);
        die();
    }

    public function saveAndContinue() {
        $this->save(null, true, true);
    }

    /**
     * Method to save a metadata.
     *
     * @return	void
     * @since	1.6
     */
    public function save($data = null, $commit = true, $continue = false) {
        $this->structure->formatOutput = true;
        $xml = $this->structure->saveXML();

        //print_r($xml);die();

        if (!isset($data)) {
            $data = $this->data;
        }

        foreach ($this->nsdao->getAll() as $ns) {
            $this->domXpathStr->registerNamespace($ns->prefix, $ns->uri);
        }

        // Multiple list decomposer
        $dataWithoutArray = array();
        foreach ($data as $xpath => $values) {

            // if is boundary
            if (strpos($xpath, 'EX_Extent-sla-gmd-dp-description-sla-gco-dp-CharacterString') !== false) {
                $this->addBoundaries($xpath, $values);
                unset($data[$xpath]);
                continue;
            }

            if (is_array($values)) {

                foreach ($values as $key => $value) {
                    $index = $key + 1;
                    $indexedXpath = str_replace('MD_Keywords-sla-gmd-dp-keyword', 'MD_Keywords-sla-gmd-dp-keyword-la-' . $index . '-ra-', $xpath, $nbrReplace);

                    if ($nbrReplace == 0) {
                        $indexedXpath = $this->addIndexToXpath($xpath, 4, $index);
                    }

                    $dataWithoutArray[$indexedXpath] = $value;
                }
            } else {
                $dataWithoutArray[$xpath] = $values;
            }
        }

        foreach ($dataWithoutArray as $xpath => $value) {

            $xpatharray = explode('#', $xpath);

            if (count($xpatharray) > 1) {
                $query = FormUtils::unSerializeXpath($xpatharray[0]) . '[@locale="#' . $xpatharray[1] . '"]';
            } else {
                $query = FormUtils::unSerializeXpath($xpatharray[0]);
            }
            $elements = $this->domXpathStr->query($query);
            if ($elements) {
                $element = $this->domXpathStr->query($query)->item(0);
            } else {

                JFactory::getApplication()->enqueueMessage('Erreur de xpath: ' . $query, 'error');
                $this->setRedirect(JRoute::_('index.php?view=metadata&layout=edit', false));
            }

            if (isset($element)) {
                // List case
                if ($element->hasAttribute('codeList')) {
                    $element->setAttribute('codeListValue', $value);
                    // Resourcetype case
                } elseif ($element->hasAttributeNS('http://www.w3.org/1999/xlink', 'href')) {
                    $element->setAttributeNS('http://www.w3.org/1999/xlink', 'xlink:href', $this->getHref($value));
                    // Text case
                } elseif ($element->parentNode->getAttributeNS($this->catalog_uri, 'stereotypeId') == EnumStereotype::$LOCALECHOICE) {
                    $translations = $this->getI18nValue($value);
                    $element->nodeValue = $translations['default']->text2;
                    foreach ($translations['supported'] as $key => $translation) {
                        $query = $element->parentNode->getNodePath() . '/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale="#' . $key . '"]';
                        $element = $this->domXpathStr->query($query)->item(0);

                        $element->nodeValue = $translation->text2;
                    }
                } else {
                    $element->nodeValue = "";
                    $item = $this->structure->createTextNode($value);
                    $element->appendChild($item);
                }
            }
        }

        $keywords = $this->domXpathStr->query('descendant::*[@catalog:stereotypeId="' . EnumStereotype::$GEMET . '"]');

        if ($keywords->length) {
            $this->cleanEmptyNode($keywords);
        }

        $root = $this->domXpathStr->query('/*')->item(0);

        foreach ($this->getHeader() as $header) {
            $root->insertBefore($header, $root->firstChild);
        }

        $smda = new sdiMetadata($data['id']);

        //$root->insertBefore($smda->getPlatformNode($this->structure), $root->firstChild);
        $root->appendChild($smda->getPlatformNode($this->structure));

        $this->removeNoneExist();
        $this->removeEmptyListNode();
        $this->removeCatalogNS();

        if ($commit) {
            $xml = $this->CreateUpdateBody($root, $data['guid'])->saveXML();

            if (isset($this->data['viral']) && $this->data['viral'] == 1) {
                $virality = $this->changeStatusViral($this->data['id'], $this->data['metadatastate_id'], $this->data['published']);
            }

            $model = $this->getModel('Metadata', 'Easysdi_catalogModel');

            if ($model->save($data, $xml) && (!isset($virality) || $virality === true)) {
                $this->saveTitle($data['guid']);

                JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_CATALOG_METADATA_SAVE_VALIDE'), 'message');
                if ($continue) {
                    $this->setRedirect(JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.edit&id=' . $data['id']));
                } else {

                    $back_url = array('root' => 'index.php',
                        'option' => 'com_easysdi_core',
                        'view' => 'resources',
                        'parentid' => JFactory::getApplication()->getUserState('com_easysdi_core.parent.resource.version.id'));

                    $this->setRedirect(JRoute::_(Easysdi_coreHelper::array2URL($back_url), false));
                }
            } else {
                $this->changeStatus($data['id'], $data['metadatastate_id']);
                JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_CATALOG_METADATA_SAVE_ERROR'), 'error');
                $this->setRedirect(JRoute::_('index.php?view=metadata&layout=edit', false));
            }
        }
    }

    /**
     * Add boundary stereotype into xpath
     * 
     * @param string $xpath
     * @param array $boundaries
     */
    private function addBoundaries($xpath, $boundaries) {
        $formStereotype = new FormStereotype();

        $query = FormUtils::unSerializeXpath($xpath);
        $elements = $this->domXpathStr->query($query);
        $toDeletes = array();
        /* @var $parent DOMElement */
        $parent;
        foreach ($elements as $element) {
            $toDeletes[] = $element->parentNode->parentNode->parentNode;
            $parent = $element->parentNode->parentNode->parentNode->parentNode;
        }

        foreach ($toDeletes as $toDelete) {
            $parent->removeChild($toDelete);
        }

        if (is_array($boundaries)) {
            foreach ($boundaries as $boundary) {
                if (!empty($boundary)) {
                    $parent->appendChild($this->structure->importNode($formStereotype->getMultipleExtentStereotype($boundary), true));
                }
            }
        } else {
            if (!empty($boundaries)) {
                $parent->appendChild($this->structure->importNode($formStereotype->getMultipleExtentStereotype($boundaries), true));
            } else {
                $northBoundLatitude = $this->domXpathStr->query($parent->getNodePath() . '/gmd:extent/gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:northBoundLatitude/gco:Decimal')->item(0);
                $southBoundLatitude = $this->domXpathStr->query($parent->getNodePath() . '/gmd:extent/gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:southBoundLatitude/gco:Decimal')->item(0);
                $eastBoundLongitude = $this->domXpathStr->query($parent->getNodePath() . '/gmd:extent/gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:eastBoundLongitude/gco:Decimal')->item(0);
                $westBoundLongitude = $this->domXpathStr->query($parent->getNodePath() . '/gmd:extent/gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:westBoundLongitude/gco:Decimal')->item(0);

                $parent->appendChild($this->structure->importNode($formStereotype->getExtendStereotype(null, $boundaries, $northBoundLatitude, $southBoundLatitude, $eastBoundLongitude, $westBoundLongitude, '35', true), true));
            }
        }
    }

    /**
     * 
     * @param DOMNodeList $element
     */
    private function cleanEmptyNode(DOMNodeList $keywords) {
        $parent = false;
        $registeredKeywords = array();
        foreach ($keywords as $keyword) {
            if ($parent === false)
                $parent = $keyword->parentNode->parentNode;
            $defaultChild = trim($keyword->childNodes->item(0)->nodeValue);
            if (in_array($defaultChild, $registeredKeywords) || empty($defaultChild))
                $keyword->parentNode->removeChild($keyword);
            else
                array_push($registeredKeywords, $defaultChild);
        }

        if (count($registeredKeywords) == 0)
            $parent->parentNode->removeChild($parent);
    }

    /**
     * Create xml for update request
     * 
     * @param DOMElement $body
     * @param string $guid
     * 
     * @return DOMDocument The body to send to catalog
     */
    private function CreateUpdateBody($body, $guid) {
        $request = new DOMDocument('1.0', 'utf-8');

        $transaction = $request->createElementNS($this->cswUri, 'csw:Transaction');
        $transaction->setAttribute('service', 'CSW');
        $transaction->setAttribute('version', '2.0.2');

        $update = $request->createElementNS($this->cswUri, 'csw:Update');
        $update->appendChild($request->importNode($body, true));

        $update->appendChild($request->importNode($this->getConstraint($guid), true));

        $transaction->appendChild($update);
        $request->appendChild($transaction);

        return $request;
    }

    /**
     * Get and save the value of title in sdi_translation
     * 
     * @param string $guid Guid for the metadata
     */
    public function saveTitle($guid) {
        $user = JFactory::getUser();
        $metadatatitlexpath = JComponentHelper::getParams('com_easysdi_catalog')->get('metadatatitlexpath');
        $defaultLang = $this->ldao->getDefaultLanguage();
        $supportedLangs = $this->ldao->getSupported();

        $titles = array();

        $default = $this->domXpathStr->query($metadatatitlexpath . '/gco:CharacterString')->item(0);

        $titles[$defaultLang->id] = $default->nodeValue;

        foreach ($supportedLangs as $supportedLang) {
            $i18nlang = $this->domXpathStr->query($metadatatitlexpath . '/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale="#' . $supportedLang->{'iso3166-1-alpha2'} . '"]')->item(0);

            if (isset($i18nlang)) {
                $titles[$supportedLang->id] = $i18nlang->nodeValue;
            } else {
                $titles[$supportedLang->id] = $default->nodeValue;
            }
        }

        // Delete old version
        $query = $this->db->getQuery(true);
        $query->delete('#__sdi_translation');

        $query->where('element_guid = ' . $query->quote($guid));

        $this->db->setQuery($query);
        $this->db->execute();

        foreach ($titles as $language_id => $text1) {
            $translationtable = $this->getModel('Metadata', 'Easysdi_catalogModel')->getTable('Translation', 'Easysdi_catalogTable', array());

            $data['guid'] = $this->getGUID();
            $data['created_by'] = $user->id;
            $data['created'] = date($this->db->getDateFormat());
            $data['element_guid'] = $guid;
            $data['language_id'] = $language_id;
            $data['text1'] = $text1;
            $translationtable->save($data);
        }
    }

    /**
     * Change metadata assignment
     */
    public function assign() {
        //Build the array of fields to get from the input
        $fields = array();
        $fields['id'] = '';
        $fields['assign_child'] = '';
        $fields['assigned_by'] = '';
        $fields['assigned_to'] = '';
        $fields['assign_msg'] = '';
        $data = JFactory::getApplication()->input->getArray($fields);

        $cascade = (isset($data['assign_child']) && $data['assign_child'] == 1);

        $metadata_ids = array($data['id']);

        if ($cascade) {
            $query = $this->db->getQuery(true)
                    ->select('m.id')
                    ->from('#__sdi_metadata m')
                    ->innerJoin('#__sdi_versionlink vl ON vl.child_id=m.version_id')
                    ->innerJoin('#__sdi_metadata md ON md.version_id=vl.parent_id')
                    ->where('md.id=' . (int) $data['id']);
            $this->db->setQuery($query);
            $metadata_ids = array_merge($metadata_ids, $this->db->loadColumn());
        }

        $query = $this->db->getQuery(true);
        $query->select('m.guid, m.id')
                ->from('#__sdi_metadata m')
                ->where('m.metadatastate_id=' . sdiMetadata::INPROGRESS)
                ->where('m.id IN (' . implode(',', $metadata_ids) . ')')
        ;
        $this->db->setQuery($query);
        $rows = $this->db->loadAssocList();

        $total = count($rows);
        $success = array();
        $fails = 0;
        foreach ($rows as $row) {
            $assignment = new stdClass();

            $assignment->id = null;
            $assignment->guid = $row['guid'];
            $assignment->assigned = date($this->db->getDateFormat());
            $assignment->assigned_by = $data['assigned_by'];
            $assignment->assigned_to = $data['assigned_to'];
            $assignment->metadata_id = $row['id'];
            $assignment->text = $data['assign_msg'];

            if ($this->db->insertObject('#__sdi_assignment', $assignment, 'id'))
                $success[] = $row['id'];
            else
                $fails++;
        }

        if (count($success) == $total)
            JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_CATALOG_METADATA_ASSIGNED_SUCCESS'), 'success');
        elseif ($fails == $total)
            JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_CATALOG_METADATA_ASSIGNED_FAILS'), 'error');
        else
            JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_CATALOG_METADATA_ASSIGNED_WARNING'), 'warning');

        $notificationsenabled = JComponentHelper::getParams('com_easysdi_catalog')->get('notificationsenabled', 1);
        if (count($success) && $notificationsenabled) {
            $byUser = new sdiUser($data['assigned_by']);
            $toUser = new sdiUser($data['assigned_to']);

            $li = "";
            foreach ($success as $metadata_id) {
                $link = JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.edit&id=' . $metadata_id, true, -1);
                $li .= "<li><a href='{$link}'>{$link}</a></li>";
            }

            $body = JText::sprintf('COM_EASYSDI_CATALOG_METADATA_ASSIGNED_NOTIFICATION_MAIL_BODY', $toUser->juser->name, $byUser->juser->name, count($success), nl2br($data['assign_msg']), $li);

            $toUser->sendMail(
                    JText::_('COM_EASYSDI_CATALOG_METADATA_ASSIGNED_NOTIFICATION'), $body
            );
        }

        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
    }

    public function getRoles() {
        // Load roles for a resource
        $versionId = JFactory::getApplication()->input->get('versionId');

        $query = $this->db->getQuery(true);

        $query->select('su.id as user_id, u.name as username, urr.role_id, r.value as role_name')
                ->from('#__sdi_version v')
                ->join('left', '#__sdi_user_role_resource urr ON urr.resource_id=v.resource_id')
                ->join('left', '#__sdi_user su ON su.id=urr.user_id')
                ->join('left', '#__users u ON u.id=su.user_id')
                ->join('left', '#__sdi_sys_role r ON r.id=urr.role_id')
                ->where('v.id=' . $versionId);

        $this->db->setQuery($query);
        $rows = $this->db->loadAssocList();

        $roles = array();
        foreach ($rows as $row) {
            if (!isset($roles[$row['role_id']])) {
                $roles[$row['role_id']] = array(
                    'role' => $row['role_name'],
                    'users' => array()
                );
            }

            $roles[$row['role_id']]['users'][$row['user_id']] = $row['username'];
        }

        $version = new stdClass();
        $version->id = JFactory::getApplication()->input->get('versionId');

        $versions = $this->core_helpers->getViralVersionnedChild($version);

        if (empty($versions[$version->id]->children)) {
            $roles['hasViralChildren'] = 'false';
        } else {
            $roles['hasViralChildren'] = 'true';
        }

        echo json_encode($roles);
        die();
    }

    public function getSynchronisationInfo() {
        $metadataId = JFactory::getApplication()->input->get('metadata_id');

        $query = $this->db->getQuery(true);

        $query->select('m.id, m.synchronized_by, m.lastsynchronization, r.name AS resource_name, v.name AS version_name, rc.id AS resource_id');
        $query->from('#__sdi_metadata m');
        $query->leftJoin('#__sdi_metadata msb ON m.synchronized_by = msb.id');
        $query->leftJoin('#__sdi_version v ON msb.version_id = v.id');
        $query->leftJoin('#__sdi_resource r ON v.resource_id = r.id');
        $query->innerJoin('#__sdi_version vc ON m.version_id = vc.id');
        $query->innerJoin('#__sdi_resource rc ON vc.resource_id = rc.id');
        $query->where('m.id = ' . (int) $metadataId);

        $this->db->setQuery($query);

        $parent = $this->db->loadObject();

        $response = array();
        if (!empty($parent->synchronized_by)) {
            $response['synchronized'] = true;
            $response['lastsynchronization'] = $parent->lastsynchronization;
            $response['synchronized_by'] = $parent->resource_name . ' : ' . $parent->version_name;
            $response['resource_id'] = $parent->resource_id;
        } else {
            $response['synchronized'] = false;
        }

        echo json_encode($response);
        die();
    }

    /**
     * Change metadata status
     * 
     * @param int $id metadata id
     * @param int $metadatastate_id state id
     * 
     * @return mixed A database cursor resource on success, boolean false on failure
     */
    public function changeStatus($id, $metadatastate_id, $published = null) {

        $this->data['metadatastate_id'] = $metadatastate_id;
        switch ($metadatastate_id) {
            case sdiMetadata::INPROGRESS:
                $published = '';
                $this->data['published'] = $published;
                $archived = '';
                $this->data['archived'] = $archived;
                break;

            case sdiMetadata::ARCHIVED:
                $archived = date('Y-m-d');
                $this->data['archived'] = $archived;
                break;
        }

        $query = $this->db->getQuery(true);

        $query->update('#__sdi_metadata ');
        $query->set('metadatastate_id = ' . $metadatastate_id);
        if (isset($published)) {
            $query->set('published = ' . $query->quote($published));
        }
        if (isset($archived)) {
            $query->set('archived = ' . $query->quote($archived));
        }

        $query->where('id = ' . (int) $id);

        $this->db->setQuery($query);

        return $this->db->execute();
    }

    private function changeStatusViral($id, $metadatastate_id, $published = null) {
        $query = $this->db->getQuery(true)
                ->select('version_id as id')
                ->from('#__sdi_metadata')
                ->where('id=' . (int) $id);
        $this->db->setQuery($query);
        $version = $this->db->loadObject();

        $versions = $this->core_helpers->getChildrenVersion($version);

        try {
            try {
                $this->db->transactionStart();
            } catch (Exception $exc) {
                $this->db->connect();
                $driver_begin_transaction = $this->db->name . '_begin_transaction';
                $driver_begin_transaction($this->db->getConnection());
            }

            foreach ($versions[$version->id]->children as $children) {
                if ($children->metadatastate_id == sdiMetadata::VALIDATED) {
                    if($this->changeStatus($children->metadata_id, $metadatastate_id, $published)){
                        
                        
                        $data = array();
                        $data['id'] = $children->metadata_id;
                        $data['published'] = $published;
                        
                        $child = new sdiMetadata($children->metadata_id);
                        $childDom = $child->load();
                        $childXPath = new DOMXpath($childDom);

                        $nsdao = new SdiNamespaceDao();
                        
                        foreach ($nsdao->getAll() as $ns) {
                            $childXPath->registerNamespace($ns->prefix, $ns->uri);
                }
                        $model = $this->getModel('Metadata', 'Easysdi_catalogModel');
                        
                        $xml = $this->CreateUpdateBody($childXPath->query('/*/*')->item(0), $children->fileidentifier);
                        $model->save($data, $xml->saveXML());
                       
            }
                }
            }
            $this->db->transactionCommit();
            
            return true;
        } catch (Exception $ex) {
            $this->db->transactionRollback();
            return false;
        }
    }

    function cancel() {

        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
    }

    /**
     * Add index to xpath at a specific position
     * 
     * @param string $xpath
     * @param int $position
     * @return array
     */
    private function addIndexToXpath($xpath, $position, $index) {
        $arrayPath = array_reverse(explode('-', $xpath));

        $arrayIndex = array_slice($arrayPath, 0, $position, true) +
                array('ra' => 'ra-', 'index' => $index, 'la' => 'la') +
                array_slice($arrayPath, $position, count($arrayPath), true);

        return implode('-', array_reverse($arrayIndex, true));
    }

    private function unSerializeXpath($xpath) {
        $xpath = str_replace('-la-', '[', $xpath);
        $xpath = str_replace('-ra-', ']', $xpath);
        $xpath = str_replace('-sla-', '/', $xpath);
        $xpath = str_replace('-dp-', ':', $xpath);
        return $xpath;
    }

    /**
     * Return href for sub resource
     * 
     * @param string $guid
     * @return string 
     */
    private function getHref($guid) {

        $query = $this->db->getQuery(true);
        $query->select('m.guid ,ns.prefix, rt.fragment');
        $query->from('#__sdi_resource as r');
        $query->innerJoin('#__sdi_resourcetype as rt ON r.resourcetype_id = rt.id');
        $query->innerJoin('#__sdi_namespace as ns ON rt.fragmentnamespace_id = ns.id');
        $query->innerJoin('#__sdi_version v on v.resource_id = r.id');
        $query->innerJoin('#__sdi_metadata m on v.id = m.version_id');
        $query->where('m.guid = ' . $query->quote($guid));

        $this->db->setQuery($query);
        $result = $this->db->loadObject();

        if (isset($result)) {

            $href = JComponentHelper::getParams('com_easysdi_catalog')->get('catalogurl');
            $href.= '?request=GetRecordById&service=CSW&version=2.0.2&elementSetName=full&outputschema=csw:IsoRecord&id=' . $result->guid;
            $href .= '&fragment=' . $result->prefix . '%3A' . $result->fragment;

            return $href;
        } else {
            return '';
        }
    }

    /**
     * 
     * @param string $language
     * @param string $encoding
     * @return DOMElement[] 
     * 
     */
    private function getHeader($encoding = 'utf8') {

        $languageid = $this->ldao->getDefaultLanguage();

        $headers = array();

        $language = $this->structure->createElementNS($this->nsArray['gmd'], 'language');

        $isolanguageid = JComponentHelper::getParams('com_easysdi_catalog')->get('isolanguage', 2);
        switch ($isolanguageid) {
            case 1:
                $isolanguage = $languageid->{'iso639-1'};
                break;
            case 2:
                $isolanguage = $languageid->{'iso639-2B'};
                break;
            case 3:
                $isolanguage = $languageid->{'iso639-2T'};
                break;
            default:
                $isolanguage = $languageid->{'iso639-2B'};
                break;
        }
        $characterString = $this->structure->createElementNS($this->nsArray['gco'], 'CharacterString', $isolanguage);
        $language->appendChild($characterString);
        $headers[] = $language;

        $characterSet = $this->structure->createElementNS($this->nsArray['gmd'], 'characterSet');
        $characterSetCode = $this->structure->createElementNS($this->nsArray['gmd'], 'MD_CharacterSetCode');
        $characterSetCode->setAttribute('codeListValue', $encoding);
        $characterSetCode->setAttribute('codeList', 'http://www.isotc211.org/2005/resources/codeList.xml#MD_CharacterSetCode');
        $characterSet->appendChild($characterSetCode);

        $headers[] = $characterSet;

        $locale = $this->structure->createElementNS($this->nsArray['gmd'], 'locale');
        $characterEncoding = $this->structure->createElementNS($this->nsArray['gmd'], 'characterEncoding');
        $characterEncodingSetCode = $this->structure->createElementNS($this->nsArray['gmd'], 'MD_CharacterSetCode', strtoupper($encoding));
        $characterEncodingSetCode->setAttribute('codeListeValue', $encoding);
        $characterEncodingSetCode->setAttribute('codeList', '#MD_CharacterSetCode');
        $characterEncoding->appendChild($characterEncodingSetCode);
        $addLocale = false;
        foreach ($this->ldao->getSupported() as $key => $value) {
            switch ($isolanguageid) {
                case 1:
                    $isolanguagesupported = $value->{'iso639-1'};
                    break;
                case 2:
                    $isolanguagesupported = $value->{'iso639-2B'};
                    break;
                case 3:
                    $isolanguagesupported = $value->{'iso639-2T'};
                    break;
                default:
                    $isolanguagesupported = $value->{'iso639-2B'};
                    break;
            }
            if ($isolanguagesupported != $isolanguage) {
                $pt_locale = $this->structure->createElementNS($this->nsArray['gmd'], 'PT_Locale');
                $pt_locale->setAttribute('id', $key);

                $languageCode = $this->structure->createElementNS($this->nsArray['gmd'], 'languageCode');
                $languageCodeChild = $this->structure->createElementNS($this->nsArray['gmd'], 'LanguageCode', $value->value);
                $languageCodeChild->setAttribute('codeListValue', $isolanguagesupported);
                $languageCodeChild->setAttribute('codeList', '#LanguageCode');

                $languageCode->appendChild($languageCodeChild);

                $pt_locale->appendChild($languageCode);
                $pt_locale->appendChild($characterEncoding);

                $locale->appendChild($pt_locale);
                $addLocale = true;
            }
        }

        if ($addLocale) {
            $headers[] = $locale;
        }

        return $headers;
    }

    /**
     * @return DOMElement Description
     */
    private function getConstraint($guid) {
        $constraint = $this->structure->createElementNS($this->cswUri, 'Constraint');
        $constraint->setAttribute('version', '1.0.0');

        $filter = $this->structure->createElement('Filter');
        $filter->setAttribute('xmlns', 'http://www.opengis.net/ogc');
        $filter->setAttribute('xmlns:gml', 'http://www.opengis.net/gml');
        $propertyIsLike = $this->structure->createElement('PropertyIsLike');
        $propertyIsLike->setAttribute('wildCard', '%');
        $propertyIsLike->setAttribute('singleChar', '_');
        $propertyIsLike->setAttribute('escapeChar', '\\');

        $propertyName = $this->structure->createElement('PropertyName', JComponentHelper::getParams('com_easysdi_catalog')->get('idogcsearchfield'));
        $literal = $this->structure->createElement('Literal', $guid);

        $propertyIsLike->appendChild($propertyName);
        $propertyIsLike->appendChild($literal);

        $filter->appendChild($propertyIsLike);
        $constraint->appendChild($filter);

        return $constraint;
    }

    private function removeNoneExist() {
        $relations = $this->domXpathStr->query('descendant::*[@catalog:exist="0"]');
        $toRemove = array();
        foreach ($relations as $relation) {
            $toRemove[] = $relation;
        }

        foreach ($toRemove as $remove) {
            $remove->parentNode->removeChild($remove);
        }
    }

    private function removeEmptyListNode() {

        //Remove empty list Node with CodeListValue attribute
        $listNodes = $this->domXpathStr->query('descendant::*[@codeListValue=""]');
        $toRemove = array();
        foreach ($listNodes as $listNode) {
            $toRemove[] = $listNode->parentNode;
        }
        foreach ($toRemove as $remove) {
            $remove->parentNode->removeChild($remove);
        }

        //Remove empyt list node without CodeListValue attribute
        $lists = $this->domXpathStr->query('//*[@catalog:stereotypeId="6"]');
        foreach ($lists as $list) {
            $code = $list->childNodes->item(0)->getAttribute("codeListValue");
            if ($code == "") {
                $defaultChild = trim($list->childNodes->item(0)->nodeValue);
                if (empty($defaultChild) || $defaultChild == "")
                    $list->parentNode->removeChild($list);
            }
        }
    }

    private function removeCatalogNS() {
        $attributeNames = array('id', 'dbid', 'childtypeId', 'index', 'lowerbound', 'upperbound', 'rendertypeId', 'stereotypeId', 'relGuid', 'relid', 'maxlength', 'readonly', 'exist', 'resourcetypeId', 'relationId', 'label', 'boundingbox', 'map', 'level', 'scopeId', 'accessscopeLimitation');

        foreach ($this->domXpathStr->query('//*') as $element) {
            foreach ($attributeNames as $attributeName) {
                $element->removeAttributeNS($this->catalog_uri, $attributeName);
            }
        }
    }

    private function getGUID() {
        mt_srand((double) microtime() * 10000); //optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45); // "-"
        $uuid = substr($charid, 0, 8) . $hyphen
                . substr($charid, 8, 4) . $hyphen
                . substr($charid, 12, 4) . $hyphen
                . substr($charid, 16, 4) . $hyphen
                . substr($charid, 20, 12);

        return $uuid;
    }

    private function publishableNotification() {
        $tree = $this->getMetadataTree(array($this->data['id']));

        if ($tree !== FALSE) { //Note: else, one of the metadata returns false
            $list = $this->tree2List($tree);

            $body = JText::sprintf('COM_EASYSDI_CATALOG_METADATA_PUBLISHABLE_NOTIFICATION_MAIL_BODY', '%s', $list);

            $query = $this->db->getQuery(true);
            $query->select('urr.user_id')
                    ->from('#__sdi_user_role_resource urr')
                    ->where('urr.resource_id=' . $tree[0]->resource_id)
                    ->where('urr.role_id=2', 'AND');
            $this->db->setQuery($query);

            $users = $this->db->loadColumn();

            $notificationsenabled = JComponentHelper::getParams('com_easysdi_catalog')->get('notificationsenabled', 1);
            if ($notificationsenabled) {
                foreach ($users as $user) {
                    $sdiUser = new sdiUser($user);

                    $sdiUser->sendMail(
                            JText::_('COM_EASYSDI_CATALOG_METADATA_PUBLISHABLE_NOTIFICATION'), JText::sprintf($body, $sdiUser->juser->name)
                    );
                }
            }
        }
    }

    private function getMetadataTree($ids = array()) {
        $tree = array();

        $query = $this->db->getQuery(true);
        $query->select('m.version_id as id, m.metadatastate_id as state, r.name as rname, v.name as vname, rt.versioning, v.resource_id')
                ->from('#__sdi_metadata m')
                ->join('INNER', '#__sdi_version v ON v.id=m.version_id')
                ->join('INNER', '#__sdi_resource r ON r.id=v.resource_id')
                ->join('INNER', '#__sdi_resourcetype rt ON rt.id=r.resourcetype_id')
                ->where('m.id IN (' . implode(',', $ids) . ')');
        $this->db->setQuery($query);

        $metadatas = $this->db->loadAssocList();

        foreach ($metadatas as $metadata) {
            if ($metadata['state'] != 2)
                return false; //Note: notification shouldn't be sent

            $branch = new stdClass();
            $branch->id = $metadata['id'];
            $branch->state = $metadata['state'];
            $branch->name = $metadata['rname'];
            if ($metadata['versioning'] == 1)
                $branch->name .= ' - ' . $metadata['vname'];
            $branch->resource_id = $metadata['resource_id'];

            $query = $this->db->getQuery(true);
            $query->select('m.id')
                    ->from('#__sdi_versionlink vl')
                    ->innerJoin('#__sdi_metadata m ON m.version_id=vl.child_id')
                    ->where('parent_id=' . $branch->id);
            $this->db->setQuery($query);

            $children = $this->db->loadColumn();
            $branch->children = count($children) ? $this->getMetadataTree($children) : array();
            if ($branch->children === FALSE)
                return false; //Note: that mean's getMetadataTree returns FALSE

            $tree[] = $branch;
        }

        return $tree;
    }

    private function tree2List($tree = array()) {
        if (!count($tree))
            return "";

        $li = "<ul>";
        foreach ($tree as $branch) {
            $li .= "<li>" . $branch->name . $this->tree2List($branch->children) . "</li>";
        }
        $li .= "</ul>";

        return $li;
    }

    /**
     * 
     * @param string $guid
     * @return array Liste of translation for element guid
     */
    private function getI18nValue($guid) {
        $defaultId = JComponentHelper::getParams('com_easysdi_catalog')->get('defaultlanguage');
        $supportedIds = JComponentHelper::getParams('com_easysdi_catalog')->get('languages');

        $results = array();
        $results['supported'] = array();

        // Only if multiple languages supported
        if ($supportedIds) {
            $languageIds = implode(',', $supportedIds);
            $query = $this->db->getQuery(true);
            $query->select('t.text2, ' . $query->quoteName('iso3166-1-alpha2'));
            $query->from('#__sdi_translation t');
            $query->innerJoin('#__sdi_language l ON l.id=t.language_id');
            $query->where('t.element_guid = ' . $query->quote($guid));
            $query->where('t.language_id IN (' . $languageIds . ')');

            $this->db->setQuery($query);

            $results['supported'] = $this->db->loadObjectList('iso3166-1-alpha2');
        }

        $query = $this->db->getQuery(true);
        $query->select('t.text2, ' . $query->quoteName('iso3166-1-alpha2'));
        $query->from('#__sdi_translation t');
        $query->innerJoin('#__sdi_language l ON l.id=t.language_id');
        $query->where('t.element_guid = ' . $query->quote($guid));
        $query->where('t.language_id IN (' . $defaultId . ')');

        $this->db->setQuery($query);

        $results['default'] = $this->db->loadObject();

        return $results;
    }

}

?>
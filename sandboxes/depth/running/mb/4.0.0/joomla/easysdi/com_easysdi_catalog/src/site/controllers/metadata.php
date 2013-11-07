<?php

/**
 * @version     4.0.0
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org§> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_easysdi_catalog/tables');

require_once JPATH_COMPONENT . '/controller.php';

require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/FormHtmlGenerator.php';

require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/catalog/sdimetadata.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/catalog/cswmetadata.php';

require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/dao/SdiLanguageDao.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/dao/SdiNamespaceDao.php';

/**
 * Metadata controller class.
 */
class Easysdi_catalogControllerMetadata extends Easysdi_catalogController {

    /**
     *
     * @var JDatabaseDriver
     */
    private $db = null;

    /**
     *
     * @var JSession 
     */
    private $session;

    /**
     *
     * @var DOMDocument 
     */
    private $structure;

    /**
     *
     * @var DOMXPath 
     */
    private $domXpathStr;

    /**
     *
     * @var SdiNamespaceDao 
     */
    private $nsdao;

    /**
     *
     * @var SdiLanguageDao 
     */
    private $ldao;
    private $catalog_uri = 'http://www.easysdi.org/2011/sdi/catalog';
    private $catalog_prefix = 'catalog';
    private $cswUri = 'http://www.opengis.net/cat/csw/2.0.2';
    private $nsArray = array();

    function __construct() {
        $this->db = JFactory::getDbo();
        $this->session = JFactory::getSession();
        $this->nsdao = new SdiNamespaceDao();
        $this->ldao = new SdiLanguageDao();
        $this->structure = new DOMDocument('1.0', 'utf-8');
        $this->structure->loadXML(unserialize($this->session->get('structure')));
        $this->structure->normalizeDocument();
        $this->domXpathStr = new DOMXPath($this->structure);

        foreach ($this->nsdao->getAll() as $ns) {
            $this->nsArray[$ns->prefix] = $ns->uri;
        }

        parent::__construct();
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

        // Set the user id for the user to edit in the session.
        $app->setUserState('com_easysdi_catalog.edit.metadata.id', $editId);

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

    /**
     * Show xml preview
     */
    public function show() {
        $this->save($_POST['jform'], false);
        /** @var DOMElement */
        $update = $this->structure->getElementsByTagNameNS($this->cswUri, 'Update')->item(0);
        $this->structure->formatOutput = true;

        $response = array();
        $response['success'] = true;
        $response['xml'] = '<pre class="brush: xml">' . htmlspecialchars($this->structure->saveXML($update->firstChild)) . '</pre>';
        echo json_encode($response);
        die();
    }

    public function preview() {
        $this->save($_POST['jform'], false);
        $domExtend = new DOMDocument('1.0','utf-8');
        
        $update = $this->structure->getElementsByTagNameNS($this->cswUri, 'Update')->item(0);

        $cswm = new cswmetadata();
        $cswm->init($update->firstChild);
        $cswm->extend('', '', 'editor', true, JFactory::getLanguage()->getTag());

        $response = array();
        $response['success'] = true;
        $response['xml'] = '<div class="well">'.$cswm->applyXSL('', '', 'editor').'</div>';
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
        if (!isset($data)) {
            $data = JFactory::getApplication()->input->get('jform', array(), 'array');
        }

        $fileRepository = JPATH_BASE . '/media/' . JComponentHelper::getParams('com_easysdi_catalog')->get('linkedfilerepository');
        $fileBaseUrl = JComponentHelper::getParams('com_easysdi_catalog')->get('linkedfilebaseurl');

        //Upload file
        if (isset($_FILES['jform'])) {
            foreach ($_FILES['jform']['name'] as $key => $value) {
                if ($_FILES['jform']['name'][$key] != '') {

                    $file_guid = $this->getGUID();
                    if (move_uploaded_file($_FILES['jform']['tmp_name'][$key], $fileRepository . '/' . $file_guid . '_' . $_FILES['jform']['name'][$key])) {

                        if ($data[$key . '_filehidden'] != '') {
                            unlink($fileRepository . '/' . basename($data[$key . '_filehidden']));
                        }
                        $data[$key] = $fileBaseUrl . '/' . $file_guid . '_' . $_FILES['jform']['name'][$key];
                    }
                } else {
                    if ($data[$key . '_filetext'] == '') {
                        if ($data[$key . '_filehidden'] != '') {
                            unlink($fileRepository . '/' . basename($data[$key . '_filehidden']));
                        }
                        $data[$key] = '';
                    }
                }
            }
        }

        foreach ($this->nsdao->getAll() as $ns) {
            $this->domXpathStr->registerNamespace($ns->prefix, $ns->uri);
        }

        // Multiple list decomposer
        $dataWithoutArray = array();
        foreach ($data as $xpath => $values) {
            if (is_array($values)) {
                foreach ($values as $key => $value) {
                    $index = $key + 1;
                    $indexedXpath = str_replace('gmd-dp-keyword', 'gmd-dp-keyword-la-' . $index . '-ra-', $xpath);
                    $dataWithoutArray[$indexedXpath] = $value;
                }
            } else {
                $dataWithoutArray[$xpath] = $values;
            }
        }

        foreach ($dataWithoutArray as $xpath => $value) {
            $xpatharray = explode('#', $xpath);
            if (count($xpatharray) > 1) {
                $query = $this->unSerializeXpath($xpatharray[0]) . '[@locale="#' . $xpatharray[1] . '"]';
            } else {
                $query = $this->unSerializeXpath($xpatharray[0]);
            }
            $element = $this->domXpathStr->query($query)->item(0);
            if (isset($element)) {
                if ($element->hasAttribute('codeList')) {
                    $element->setAttribute('codeListValue', $value);
                } elseif ($element->hasAttributeNS('http://www.w3.org/1999/xlink', 'href')) {
                    $element->setAttributeNS('http://www.w3.org/1999/xlink', 'xlink:href', $this->getHref($value));
                } else {
                    $element->nodeValue = $value;
                }
            }
        }

        $root = $this->domXpathStr->query('/*')->item(0);

        foreach ($this->getHeader() as $header) {
            $root->insertBefore($header, $root->firstChild);
        }

        $smda = new sdiMetadata($data['id']);
        
        $root->insertBefore($smda->getPlatformNode($this->structure), $root->firstChild);

        $transaction = $this->structure->createElementNS($this->cswUri, 'Transaction');
        $transaction->setAttribute('service', 'CSW');
        $transaction->setAttribute('version', '2.0.2');

        $update = $this->structure->createElementNS($this->cswUri, 'Update');
        $update->appendChild($root);
        $update->appendChild($this->getConstraint($data['guid']));
        $transaction->appendChild($update);
        $this->structure->appendChild($transaction);

        $this->removeCatalogNS();


        if ($commit) {
            $this->structure->formatOutput = true;
            $xml = $this->structure->saveXML();

            
            if ($smda->update($xml)) {
                JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_CATALOGE_METADATA_SAVE_VALIDE'), 'message');
                if ($continue) {
                    $this->setRedirect(JRoute::_('index.php?view=metadata&layout=edit', false));
                } else {
                    $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
                }
            } else {
                JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_CATALOGE_METADATA_SAVE_ERROR'), 'error');
                $this->setRedirect(JRoute::_('index.php?view=metadata&layout=edit', false));
            }
        }
//        // Initialise variables.
//        $app = JFactory::getApplication();
//        $model = $this->getModel('Metadata', 'Easysdi_catalogModel');
//
//        // Get the user data.
//        $data = JFactory::getApplication()->input->get('jform', array(), 'array');
//
//        // Validate the posted data.
//        $form = $model->getForm();
//        
//        if (!$form) {
//            JError::raiseError(500, $model->getError());
//            return false;
//        }
//
//        // Validate the posted data.
//        $data = $model->validate($form, $data);
//
//        // Check for errors.
//        if ($data === false) {
//            // Get the validation messages.
//            $errors = $model->getErrors();
//
//            // Push up to three validation messages out to the user.
//            for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
//                if ($errors[$i] instanceof Exception) {
//                    $app->enqueueMessage($errors[$i]->getMessage(), 'warning');
//                } else {
//                    $app->enqueueMessage($errors[$i], 'warning');
//                }
//            }
//
//            // Save the data in the session.
//            $app->setUserState('com_easysdi_catalog.edit.metadata.data', JRequest::getVar('jform'), array());
//
//            // Redirect back to the edit screen.
//            $id = (int) $app->getUserState('com_easysdi_catalog.edit.metadata.id');
//            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_catalog&view=metadata&layout=edit&id=' . $id, false));
//            return false;
//        }
//
//        // Attempt to save the data.
//        $return = $model->save($data);
//
//        // Check for errors.
//        if ($return === false) {
//            // Save the data in the session.
//            $app->setUserState('com_easysdi_catalog.edit.metadata.data', $data);
//
//            // Redirect back to the edit screen.
//            $id = (int) $app->getUserState('com_easysdi_catalog.edit.metadata.id');
//            $this->setMessage(JText::sprintf('Save failed', $model->getError()), 'warning');
//            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_catalog&view=metadata&layout=edit&id=' . $id, false));
//            return false;
//        }
//
//
//        // Check in the profile.
//        if ($return) {
//            $model->checkin($return);
//        }
//
//        // Clear the profile id from the session.
//        $app->setUserState('com_easysdi_catalog.edit.metadata.id', null);
//
//        // Redirect to the list screen.
//        $this->setMessage(JText::_('COM_EASYSDI_CATALOG_ITEM_SAVED_SUCCESSFULLY'));
//        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
//
//        // Flush the data from the session.
//        $app->setUserState('com_easysdi_catalog.edit.metadata.data', null);
    }

    function cancel() {

        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
    }

    private function unSerializeXpath($xpath) {
        $xpath = str_replace('-la-', '[', $xpath);
        $xpath = str_replace('-ra-', ']', $xpath);
        $xpath = str_replace('-sla-', '/', $xpath);
        $xpath = str_replace('-dp-', ':', $xpath);
        return $xpath;
    }

    private function getHref($guid) {
        $query = $this->db->getQuery(true);
        $query->select('ns.`prefix`, rt.fragment');
        $query->from('#__sdi_resource as r');
        $query->innerJoin('#__sdi_resourcetype as rt ON r.resourcetype_id = rt.id');
        $query->innerJoin('#__sdi_namespace as ns ON rt.fragmentnamespace_id = ns.id');
        $query->where('r.guid = \'' . $guid . '\'');

        $this->db->setQuery($query);
        $result = $this->db->loadObject();

        $href = JComponentHelper::getParams('com_easysdi_catalog')->get('catalogurl');
        $href.= '?request=GetRecordById&service=CSW&version=2.0.2&elementSetName=full&outputschema=csw:IsoRecord&id=' . $guid;
        $href .= '&fragment=' . $result->prefix . '%3A' . $result->fragment;

        return $href;
    }

    /**
     * 
     * @param string $language
     * @param string $encoding
     * @return DOMElement[] 
     * 
     */
    private function getHeader($default = 'deu', $encoding = 'utf8') {

        $headers = array();

        $language = $this->structure->createElementNS($this->nsArray['gmd'], 'language');
        $characterString = $this->structure->createElementNS($this->nsArray['gco'], 'CharacterString', $default);
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
        foreach ($this->ldao->getAll() as $key => $value) {
            if ($value->{'iso639-2T'} != $default) {
                $pt_locale = $this->structure->createElementNS($this->nsArray['gmd'], 'PT_Locale');
                $pt_locale->setAttribute('id', $key);

                $languageCode = $this->structure->createElementNS($this->nsArray['gmd'], 'languageCode');
                $languageCodeChild = $this->structure->createElementNS($this->nsArray['gmd'], 'LanguageCode', $value->value);
                $languageCodeChild->setAttribute('codeListValue', $value->{'iso639-2T'});
                $languageCodeChild->setAttribute('codeList', '#LanguageCode');

                $languageCode->appendChild($languageCodeChild);

                $pt_locale->appendChild($languageCode);
                $pt_locale->appendChild($characterEncoding);

                $locale->appendChild($pt_locale);
            }
        }

        $headers[] = $locale;

        return $headers;
    }

    /**
     * 
     * @return DOMDocument
     */
    private function getSdiHeader($id) {

        /**
         * @todo Vérifier le nom du paramètre à remonter du core
         */
        $platformGuid = JComponentHelper::getParams('com_easysdi_core')->get('guid');

        $query = $this->db->getQuery(true);
        $query->select('v.`name` as md_lastVersion, m.guid as md_guid, m.created as md_created, m.published as md_published, ms.`value` as ms_value');
        $query->select('r.id as r_id, r.guid as r_guid, r.`alias` as r_alias, r.`name` as r_name');
        $query->select('rt.`alias` as rt_alias');
        $query->select('o.name as o_name, o.guid as o_guid');
        $query->from('#__sdi_metadata as m');
        $query->innerJoin('#__sdi_sys_metadatastate as ms ON ms.id = m.metadatastate_id');
        $query->innerJoin('#__sdi_version as v ON v.id = m.version_id');
        $query->innerJoin('#__sdi_resource as r ON r.id = v.resource_id');
        $query->innerJoin('#__sdi_organism as o ON o.id = r.organism_id');
        $query->innerJoin('#__sdi_resourcetype as rt ON rt.id = r.resourcetype_id');
        $query->where('m.id=' . $id);


        $this->db->setQuery($query);
        $result = $this->db->loadObject();

        $query = $this->db->getQuery(true);
        $query->select('o.guid, o.`name`');
        $query->from('#__sdi_accessscope as ac');
        $query->innerJoin('#__sdi_organism o ON o.id = ac.organism_id');
        $query->where('ac.entity_guid=\'' . $result->r_guid . '\'');
        $this->db->setQuery($query);
        $resultOrganisms = $this->db->loadObjectList();

        $query = $this->db->getQuery(true);
        $query->select('u.guid, ju.`name`');
        $query->from('#__sdi_accessscope as ac');
        $query->innerJoin('#__sdi_user as u ON u.id = ac.user_id');
        $query->innerJoin('#__users as ju ON ju.id = u.user_id');
        $query->where('ac.entity_guid=\'' . $result->r_guid . '\'');
        $this->db->setQuery($query);
        $resultUsers = $this->db->loadObjectList();

        $platform = $this->structure->createElementNS($this->nsArray['sdi'], 'platform');
        $platform->setAttribute('guid', $platformGuid);
        $platform->setAttribute('harvested', 'false');

        $resource = $this->structure->createElementNS($this->nsArray['sdi'], 'resource');
        $resource->setAttribute('guid', $result->r_guid);
        $resource->setAttribute('alias', $result->r_alias);
        $resource->setAttribute('name', $result->r_name);
        $resource->setAttribute('type', $result->rt_alias);
        $resource->setAttribute('organism', $result->o_guid);
        $resource->setAttribute('scope', '');

        $metadata = $this->structure->createElementNS($this->nsArray['sdi'], 'metadata');
        $metadata->setAttribute('lastVersion', $result->md_lastVersion);
        $metadata->setAttribute('guid', $result->md_guid);
        $metadata->setAttribute('created', $result->md_created);
        $metadata->setAttribute('published', $result->md_published);
        $metadata->setAttribute('state', $result->ms_value);

        $organisms = $this->structure->createElementNS($this->nsArray['sdi'], 'organisms');
        foreach ($resultOrganisms as $o) {
            $organism = $this->structure->createElementNS($this->nsArray['sdi'], 'organism');
            $organism->setAttribute('guid', $o->guid);
            $organism->setAttribute('alias', $o->name);
            $organisms->appendChild($organism);
        }

        $users = $this->structure->createElementNS($this->nsArray['sdi'], 'users');
        foreach ($resultUsers as $u) {
            $user = $this->structure->createElementNS($this->nsArray['sdi'], 'user');
            $user->setAttribute('guid', $u->guid);
            $user->setAttribute('alias', $u->name);
            $users->appendChild($user);
        }

        $resource->appendChild($organisms);
        $resource->appendChild($users);
        $resource->appendChild($metadata);
        $platform->appendChild($resource);

        return $platform;
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

    private function removeCatalogNS() {
        $attributeNames = array('id', 'dbid', 'childtypeId', 'index', 'lowerbound', 'upperbound', 'rendertypeId', 'stereotypeId', 'relGuid', 'relid', 'maxlength', 'readonly', 'exist');
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

}

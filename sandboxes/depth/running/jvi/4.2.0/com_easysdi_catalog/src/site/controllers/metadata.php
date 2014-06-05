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
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/FormUtils.php';
require_once JPATH_BASE . '/administrator/components/com_easysdi_core/libraries/easysdi/user/sdiuser.php';

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

    function __construct() {
        $this->db = JFactory::getDbo();
        $this->session = JFactory::getSession();
        $this->nsdao = new SdiNamespaceDao();
        $this->ldao = new SdiLanguageDao();
        $this->structure = new DOMDocument('1.0', 'utf-8');
        $_structure = $this->session->get('structure');
        if(!empty($_structure))
            $this->structure->loadXML(unserialize($_structure));
        $this->structure->normalizeDocument();
        $this->domXpathStr = new DOMXPath($this->structure);
        $this->data = JFactory::getApplication()->input->get('jform', array(), 'array');
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
        $import = JFactory::getApplication()->input->get('import', array(), 'array');
        if (key_exists('xml_file', $_FILES)) {
            if ($_FILES['xml_file']['type'] == 'text/xml') {
                if ($xml = file_get_contents($_FILES['xml_file']['tmp_name'])) {
                    $import['xml'] = $xml;
                }
            } else {
                JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_CATALOGE_METADATA_XML_IMPORT_ERROR'), 'error');
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
        $query->where('id = ' . (int)$_GET['id']);

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
        $this->changeStatusAndSave(sdiMetadata::PUBLISHED, FALSE);
    }

    /**
     * Change metadata status to publish
     */
    public function publish() {//var_dump($_POST);var_dump($this->data);exit();
        if (count($this->data) > 0) {
            $this->changeStatusAndSave(sdiMetadata::PUBLISHED);
        } else {
            $this->changeStatusAndUpdate(sdiMetadata::PUBLISHED);
        }
    }

    /**
     * Change metadata status to publish
     */
    public function publishAndClose() {
        $this->changeStatusAndSave(sdiMetadata::PUBLISHED, FALSE);
    }

    /**
     * Change metadata status and save
     * 
     * @param type $statusId
     */
    private function changeStatusAndSave($statusId, $continue = true) {

        if ($this->changeStatus($this->data['id'], $statusId, $this->data['published']) != FALSE) {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_CATALOGE_METADATA_CHANGE_STATUS_OK'), 'message');
            $this->save(null, true, $continue);
        } else {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_CATALOGE_METADATA_CHANGE_STATUS_ERROR'), 'error');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.edit&id=' . $this->data['id']));
        }
        
        if(sdiMetadata::VALIDATED==$statusId)
            $this->publishableNotification();
    }

    private function changeStatusAndUpdate($statusId) {
        $id = JFactory::getApplication()->input->get('id', null, 'int');
        $published = JFactory::getApplication()->input->get('published', null, 'string');
        if (isset($published)) {
            $changeStatus = $this->changeStatus($id, $statusId, $published);
        } else {
            $changeStatus = $this->changeStatus($id, $statusId);
        }

        if ($changeStatus != FALSE) {
            $this->update($id);
        } else {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_CATALOGE_METADATA_CHANGE_STATUS_ERROR'), 'error');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
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
            JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_CATALOGE_METADATA_CHANGE_STATUS_OK'), 'message');
        } else {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_CATALOGE_METADATA_CHANGE_STATUS_ERROR'), 'error');
        }
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
    }

    /**
     * 
     * @return stdClass[] result list of resource
     */
    public function searchresource() {
        $query = $this->db->getQuery(true);

        $query->select('m.id, r.`name`, v.created, m.guid, rt.`name` as rt_name, ms.`value` as status');
        $query->from('#__sdi_resource r');
        $query->innerJoin('#__sdi_resourcetype rt on r.resourcetype_id = rt.id');
        $query->innerJoin('#__sdi_version v on v.resource_id = r.id');
        $query->innerJoin('#__sdi_metadata m on m.version_id = v.id');
        $query->innerJoin('#__sdi_sys_metadatastate ms on ms.id = m.metadatastate_id');
        if ($_POST['status_id'] != '') {
            $query->where('m.metadatastate_id = ' . (int)$_POST['status_id']);
        }
        if (!empty($_POST['resourcetype_id'])) {
            $query->where('r.resourcetype_id = ' . (int)$_POST['resourcetype_id']);
        }
        if ($_POST['resource_name'] != '') {
            $query->where('r.`name` like '.$query->quote('%' . $query->escape($_POST['resource_name']) . '%'));
        }

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
        /** @var DOMElement */
        $update = $this->structure->getElementsByTagNameNS($this->cswUri, 'Update')->item(0);
        $this->structure->formatOutput = true;
        $xml = $this->structure->saveXML();

        $response = array();
        $response['success'] = true;
        $response['xml'] = '<pre class="brush: xml">' . addslashes(htmlspecialchars($this->structure->saveXML($update->firstChild))) . '</pre>';
        echo json_encode($response);
        die();
    }

    /**
     * Show xhtml preview
     */
    public function preview() {
        $this->save($_POST['jform'], false);

        $update = $this->structure->getElementsByTagNameNS($this->cswUri, 'Update')->item(0);

        $lang = JFactory::getLanguage();

        $cswm = new cswmetadata();
        $cswm->init($update->firstChild);
        $extend = $cswm->extend('', '', 'editor', 1, 'fr-FR');

        $response = array();
        $response['success'] = true;
        $response['guid'] = $_POST['jform']['guid'];

        $this->session->set($_POST['jform']['guid'], '<div class="well">' . $cswm->applyXSL('', '', 'editor', null) . '</div>');

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

        if (!isset($data)) {
            $data = $this->data;
        }

        $fileRepository = JPATH_BASE . '/media/easysdi/' . JComponentHelper::getParams('com_easysdi_catalog')->get('linkedfilerepository');
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
                } else {
                    $element->nodeValue = $value;
                }
            }
        }

        $this->structure->formatOutput = true;
        $xml = $this->structure->saveXML();

        $root = $this->domXpathStr->query('/*')->item(0);


        foreach ($this->getHeader() as $header) {
            $root->insertBefore($header, $root->firstChild);
        }

        $smda = new sdiMetadata($data['id']);

        //$root->insertBefore($smda->getPlatformNode($this->structure), $root->firstChild);
        $root->appendChild($smda->getPlatformNode($this->structure));

        $rootname = $root->nodeName;

        $this->structure->formatOutput = true;
        $xml = $this->structure->saveXML();

        $transaction = $this->structure->createElementNS($this->cswUri, 'Transaction');
        $transaction->setAttribute('service', 'CSW');
        $transaction->setAttribute('version', '2.0.2');

        $update = $this->structure->createElementNS($this->cswUri, 'Update');
        $update->appendChild($root);

        $this->structure->formatOutput = true;
        $xml = $this->structure->saveXML();

        $update->appendChild($this->getConstraint($data['guid']));

        $this->structure->formatOutput = true;
        $xml = $this->structure->saveXML();

        $transaction->appendChild($update);
        $this->structure->appendChild($transaction);

        $this->structure->formatOutput = true;
        $xml = $this->structure->saveXML();

        $this->removeNoneExist();
        $this->removeCatalogNS();

        if ($commit) {
            $this->structure->formatOutput = true;
            $xml = $this->structure->saveXML();
            
            if ($smda->update($xml)) {
                $this->saveTitle($data['guid']);
                JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_CATALOGE_METADATA_SAVE_VALIDE'), 'message');
                if ($continue) {
                    $this->setRedirect(JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.edit&id=' . $data['id']));
                } else {
                    $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
                }
            } else {
                $this->changeStatus($data['id'], $data['metadatastate_id']);
                JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_CATALOGE_METADATA_SAVE_ERROR'), 'error');
                $this->setRedirect(JRoute::_('index.php?view=metadata&layout=edit', false));
            }
        }
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
        $query->where('element_guid = '. $query->quote($guid));

        $this->db->setQuery($query);
        $this->db->execute();

        foreach ($titles as $language_id => $text1) {

            $data = new stdClass();

            $data->id = null;
            $data->guid = $this->getGUID();
            $data->created_by = $user->id;
            $data->created = date('Y-m-d H:i:s');
            $data->element_guid = $guid;
            $data->language_id = $language_id;
            $data->text1 = $text1;


            $this->db->insertObject('#__sdi_translation', $data, 'id');
        }
    }

    /**
     * Change metadata assignment
     */
    public function assign() {
        $data = JFactory::getApplication()->input->getArray();
        
        $cascade = (isset($data['assign_child']) && $data['assign_child']==1);
        
        $vids = array($data['id']);
        $date = date('Y-m-d H:i.s');
        
        if($cascade){
            $query = $this->db->getQuery(true);
            $query->select('child_id as vid')
                    ->from('#__sdi_versionlink')
                    ->where('parent_id='.$data['id']);
            $this->db->setQuery($query);
            $vids = array_merge($vids, $this->db->loadColumn());
        }
        
        $query = $this->db->getQuery(true);
        $query->select('v.guid, v.id')
                ->from('#__sdi_version v')
                ->join('LEFT', '#__sdi_metadata m ON m.version_id=v.id')
                ->where('m.metadatastate_id=1')
                ->where('v.id IN ('.implode(',',$vids).')', 'AND')
                ;
        $this->db->setQuery($query);
        $rows = $this->db->loadAssocList();
        
        $total = count($rows);
        $success = array();
        $fails = 0;
        foreach($rows as $row){
            $assignment = new stdClass();
            
            $assignment->id = null;
            $assignment->guid = $row['guid'];
            $assignment->assigned = $date;
            $assignment->assigned_by = $data['assigned_by'];
            $assignment->assigned_to = $data['assigned_to'];
            $assignment->version_id = $row['id'];
            $assignment->text = $data['assign_msg'];
            
            if($this->db->insertObject('#__sdi_assignment', $assignment, 'id'))
                $success[] = $row['id'];
            else
                $fails++;
        }
        
        if(count($success) == $total)
            JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_CATALOG_METADATA_ASSIGNED_SUCCESS'), 'success');
        elseif($fails == $total)
            JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_CATALOG_METADATA_ASSIGNED_FAILS'), 'error');
        else
            JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_CATALOG_METADATA_ASSIGNED_WARNING'), 'warning');
        
        if(count($success)){
            $byUser = new sdiUser($data['assigned_by']);
            $toUser = new sdiUser($data['assigned_to']);
            
            $li = "";
            foreach($success as $vid){
                $link = JRoute::_('index.php?option=com_easysdi_catalog&task=metadata.edit&id='.$vid, true, -1);
                $li .= "<li><a href='{$link}'>{$link}</a></li>";
            }
            
            $body = JText::sprintf('COM_EASYSDI_CATALOG_METADATA_ASSIGNED_NOTIFICATION_MAIL_BODY', $toUser->juser->name, $byUser->juser->name, count($success), nl2br($data['assign_msg']), $li);
            
            $toUser->sendMail(
                    JText::_('COM_EASYSDI_CATALOG_METADATA_ASSIGNED_NOTIFICATION'), 
                    $body
            );
        }
        
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
    }
    
    public function getRoles(){
        // Load roles for a resource
        $versionId = JFactory::getApplication()->input->get('versionId');
        
        $query = $this->db->getQuery(true);

        $query->select('su.id as user_id, u.name as username, urr.role_id, r.value as role_name')
                ->from('#__sdi_version v')
                ->join('left', '#__sdi_user_role_resource urr ON urr.resource_id=v.resource_id')
                ->join('left', '#__sdi_user su ON su.id=urr.user_id')
                ->join('left', '#__users u ON u.id=su.user_id')
                ->join('left', '#__sdi_sys_role r ON r.id=urr.role_id')
                ->where('v.id='.$versionId);

        $this->db->setQuery($query);
        $rows = $this->db->loadAssocList();

        $roles = array();
        foreach($rows as $row){
            if(!isset($roles[$row['role_id']])){
                $roles[$row['role_id']] = array(
                    'role' => $row['role_name'],
                    'users' => array()
                );
            }

            $roles[$row['role_id']]['users'][$row['user_id']] = $row['username'];
        }
        
        echo json_encode($roles);
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
        switch ($metadatastate_id) {
            case sdiMetadata::INPROGRESS:
                $published = '';
                $archived = '';
                break;

            case sdiMetadata::ARCHIVED:
                $archived = date('Y-m-d');
                break;
        }

        $query = $this->db->getQuery(true);

        $query->update('#__sdi_metadata m');
        $query->set('m.metadatastate_id = ' . $metadatastate_id);
        if (isset($published)) {
            $query->set('m.published = ' . $query->quote($published));
        }
        if (isset($archived)) {
            $query->set('m.archived = ' . $query->quote($archived));
        }
        $query->where('m.id = ' . (int)$id);

        $this->db->setQuery($query);

        return $this->db->execute();
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
        $query->select('m.guid ,ns.`prefix`, rt.fragment');
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
        $languageid = $this->ldao->getByCode(JFactory::getLanguage()->getTag());

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

    private function removeCatalogNS() {
        ;
        $attributeNames = array('id', 'dbid', 'childtypeId', 'index', 'lowerbound', 'upperbound', 'rendertypeId', 'stereotypeId', 'relGuid', 'relid', 'maxlength', 'readonly', 'exist', 'resourcetypeId', 'relationId', 'label', 'boundingbox', 'map', 'level');

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
    
    private function publishableNotification(){
        $tree = $this->getMetadataTree(array($this->data['id']));
        
        if($tree !== FALSE){ //Note: else, one of the metadata returns false
            $list = $this->tree2List($tree);
            
            $body = JText::sprintf('COM_EASYSDI_CATALOG_METADATA_PUBLISHABLE_NOTIFICATION_MAIL_BODY', '%s', $list);
            
            $query = $this->db->getQuery(true);
            $query->select('urr.user_id')
                    ->from('#__sdi_user_role_resource urr')
                    ->where('urr.resource_id='.$tree[0]->resource_id)
                    ->where('urr.role_id=2', 'AND');
            $this->db->setQuery($query);
            
            $users = $this->db->loadColumn();
            
            foreach($users as $user){
                $sdiUser = new sdiUser($user);
                
                $sdiUser->sendMail(
                        JText::_('COM_EASYSDI_CATALOG_METADATA_PUBLISHABLE_NOTIFICATION'), 
                        JText::sprintf($body, $sdiUser->juser->name)
                        );
            }
        }
    }
    
    private function getMetadataTree($ids = array()){
        $tree = array();
        
        $query = $this->db->getQuery(true);
        $query->select('m.version_id as id, m.metadatastate_id as state, r.name as rname, v.name as vname, rt.versioning, v.resource_id')
                ->from('#__sdi_metadata m')
                ->join('LEFT', '#__sdi_version v ON v.id=m.version_id')
                ->join('LEFT', '#__sdi_resource r ON r.id=v.resource_id')
                ->join('LEFT', '#__sdi_resourcetype rt ON rt.id=r.resourcetype_id')
                ->where('m.version_id IN ('.implode(',', $ids).')');
        $this->db->setQuery($query);
        
        $metadatas = $this->db->loadAssocList();
        
        foreach($metadatas as $metadata){
            if($metadata['state'] != 2)
                return false; //Note: notification shouldn't be sent
            
            $branch = new stdClass();
            $branch->id = $metadata['id'];
            $branch->state = $metadata['state'];
            $branch->name = $metadata['rname'];
            if($metadata['versioning'] == 1)
                $branch->name .= ' - '.$metadata['vname'];
            $branch->resource_id = $metadata['resource_id'];
            
            $query = $this->db->getQuery(true);
            $query->select('child_id')
                    ->from('#__sdi_versionlink')
                    ->where('parent_id='.$branch->id);
            $this->db->setQuery($query);
            
            $children = $this->db->loadColumn();
            $branch->children = count($children) ? $this->getMetadataTree($children) : array();
            if($branch->children === FALSE)
                return false; //Note: that mean's getMetadataTree returns FALSE
            
            $tree[] = $branch;
        }
        
        return $tree;
    }
    
    private function tree2List($tree = array()){
        if(!count($tree))
            return "";
        
        $li = "<ul>";
        foreach($tree as $branch){
            $li .= "<li>".$branch->name.$this->tree2List($branch->children)."</li>";
        }
        $li .= "</ul>";
        
        return $li;
    }

}

?>
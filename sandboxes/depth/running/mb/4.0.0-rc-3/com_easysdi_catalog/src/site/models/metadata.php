<?php

/**
 * @version     4.0.0
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org§> - http://www.easysdi.org
 */
// No direct access.
defined('_JEXEC') or die;

require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/FormGenerator.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/CswMerge.php';

jimport('joomla.application.component.modelform');
jimport('joomla.event.dispatcher');

/**
 * Easysdi_catalog model.
 */
class Easysdi_catalogModelMetadata extends JModelForm {

    var $_item = null;

    /**
     *
     * @var DOMDocument
     */
    var $_structure;

    /**
     *
     * @var JDatabaseDriver 
     */
    var $db = null;

    /**
     *
     * @var stdClass[] 
     */
    public $_validators = array();
    private $catalog_uri = 'http://www.easysdi.org/2011/sdi/catalog';
    private $catalog_prefix = 'catalog';

    function __construct() {
        $this->db = JFactory::getDbo();

        parent::__construct();
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since	1.6
     */
    protected function populateState() {
        $app = JFactory::getApplication('com_easysdi_catalog');

        // Load state from the request userState on edit or from the passed variable on default
        if (JFactory::getApplication()->input->get('layout') == 'edit') {
            $id = JFactory::getApplication()->getUserState('com_easysdi_catalog.edit.metadata.id');
            $import = JFactory::getApplication()->getUserState('com_easysdi_catalog.edit.metadata.import');
        } else {
            $id = JFactory::getApplication()->input->get('id');
            JFactory::getApplication()->setUserState('com_easysdi_catalog.edit.metadata.id', $id);
        }
        $this->setState('metadata.id', $id);
        $this->setState('metadata.import', $import);

        // Load the parameters.
        $params = $app->getParams();
        $params_array = $params->toArray();
        if (isset($params_array['item_id'])) {
            $this->setState('metadata.id', $params_array['item_id']);
        }
        $this->setState('params', $params);
    }

    /**
     * 
     * @return DOMDocument
     */
    public function getStructure() {
        return $this->_structure;
    }

    public function getValidators() {
        return $this->_validators;
    }

    /**
     * Method to get an ojbect.
     *
     * @param	integer	The id of the object to get.
     *
     * @return	mixed	Object on success, false on failure.
     */
    public function &getData($id = null) {
        if ($this->_item === null) {
            $this->_item = false;

            if (empty($id)) {
                $id = $this->getState('metadata.id');
            }

            $import = $this->getState('metadata.import');

            // Get a level row instance.
            $table = $this->getTable();

            // Attempt to load the row.
            if ($table->load($id)) {
                //When saving metadata, getData is called once with an empty id.  Authorization doesn't have to be checked in this case.
                if ($id) {
                    $user = sdiFactory::getSdiUser();
                    if (!$user->isEasySDI) {
                        //Not an EasySDI user = not allowed
                        JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
                        JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
                        return false;
                    }

                    if (!$user->authorizeOnMetadata($id, sdiUser::metadataeditor) || !$user->authorizeOnMetadata($id, sdiUser::metadataresponsible)) {
                        //Try to update a resource but not its resource manager
                        JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
                        JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
                        return false;
                    }
                }

                // Convert the JTable to a clean JObject.
                $query = $this->db->getQuery(true);
                $query->select('m.*, rt.profile_id');
                $query->from('#__sdi_metadata m');
                $query->innerJoin('#__sdi_version v on v.id = m.version_id');
                $query->innerJoin('#__sdi_resource r on v.resource_id = r.id');
                $query->innerJoin('#__sdi_resourcetype rt on r.resourcetype_id = rt.id');
                $query->where('m.id = ' . $id);

                $this->db->setQuery($query);
                $metadata = $this->db->loadObject();
                $metdataArray = (array) $metadata;

                $this->_item = JArrayHelper::toObject($metdataArray, 'JObject');

                if ($id) {
                    //Load the CSW metadata from local catalog
                    if (key_exists('id', $import)) {
                        $CSWmetadata = new sdiMetadata($import['id']);
                    } else {
                        $CSWmetadata = new sdiMetadata($this->_item->id);
                    }

                    if ($result = $CSWmetadata->load()) {
                        $this->_item->csw = $result;
                    }

                    // If xml is upload
                    if (key_exists('xml', $import)) {
                        $xml = new DOMDocument('1.0','utf-8');
                        $xml->loadXML($import['xml']);
                        $cswm = new CswMerge($this->_item->csw, $xml);

                        if($merged = $cswm->mergeImport($import['importref_id'])){
                            $this->_item->csw = $merged;
                        }
                    }
                    
                    // If fileidentifier is not null
                    if(key_exists('fileidentifier', $import)){
                       $cswm = new CswMerge($this->_item->csw);
                      
                       if($merged = $cswm->mergeImport($import['importref_id'], $import['fileidentifier'])){
                            $this->_item->csw = $merged;
                        }  else {
                            JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_CATALOGE_METADATA_NOT_FOUND_ERROR'), 'error');
                        }
                    }
                }
            } elseif ($error = $table->getError()) {
                $this->setError($error);
            }
        }

        return $this->_item;
    }

    public function getTable($type = 'Metadata', $prefix = 'Easysdi_catalogTable', $config = array()) {
        $this->addTablePath(JPATH_ADMINISTRATOR . '/components/com_easysdi_catalog/tables');
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to check in an item.
     *
     * @param	integer		The id of the row to check out.
     * @return	boolean		True on success, false on failure.
     * @since	1.6
     */
    public function checkin($id = null) {
        // Get the id.
        $id = (!empty($id)) ? $id : (int) $this->getState('metadata.id');

        if ($id) {

            // Initialise the table
            $table = $this->getTable();

            // Attempt to check the row in.
            if (method_exists($table, 'checkin')) {
                if (!$table->checkin($id)) {
                    $this->setError($table->getError());
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Method to check out an item for editing.
     *
     * @param	integer		The id of the row to check out.
     * @return	boolean		True on success, false on failure.
     * @since	1.6
     */
    public function checkout($id = null) {
        // Get the user id.
        $id = (!empty($id)) ? $id : (int) $this->getState('metadata.id');

        if ($id) {

            // Initialise the table
            $table = $this->getTable();

            // Get the current user object.
            $user = JFactory::getUser();

            // Attempt to check the row out.
            if (method_exists($table, 'checkout')) {
                if (!$table->checkout($user->get('id'), $id)) {
                    $this->setError($table->getError());
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Method to get the profile form.
     *
     * The base form is loaded from XML 
     * 
     * @param	array	$data		An optional array of data for the form to interogate.
     * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
     * @return	JForm	A JForm object on success, false on failure
     * @since	1.6
     */
    public function getForm($data = array(), $loadData = true) {

        $formGenerator = new FormGenerator($this->_item);

        $form = $this->loadForm('com_easysdi_catalog.metadata', $formGenerator->getForm(), array('control' => 'jform', 'load_data' => $loadData, 'file' => FALSE));

        $this->_structure = $formGenerator->structure;

        $this->buildValidators();

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return	mixed	The data for the form.
     * @since	1.6
     */
    protected function loadFormData() {
        $data = JFactory::getApplication()->getUserState('com_easysdi_catalog.edit.metadata.data', array());
        if (empty($data)) {
            $data = $this->getData();
        }

        return $data;
    }

    /**
     * Method to save the form data.
     *
     * @param	array		The form data.
     * @return	mixed		The user id on success, false on failure.
     * @since	1.6
     */
    public function save($data) {
        (empty($data['id']) ) ? $new = true : $new = false;
        $id = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('metadata.id');


        $user = sdiFactory::getSdiUser();
        if (!$user->isEasySDI) {
            //Not an EasySDI user = not allowed
            JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
            JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
            return false;
        }

        if (!empty($id) && (!$user->authorizeOnMetadata($id, sdiUser::metadataeditor) || !$user->authorizeOnMetadata($id, sdiUser::metadataresponsible))) {
            //Try to update a resource but not its resource manager
            JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
            JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
            return false;
        }

        $table = $this->getTable();
        if ($table->save($data) === true) {
            $CSWmetadata = new sdiMetadata($table->id);
            if ($new) {
                if (!$CSWmetadata->insert()) {
                    $table->delete();
                    return false;
                }
            } else {
                if (!$CSWmetadata->update()) {
                    return false;
                }
            }

            return $id;
        } else {
            return false;
        }
    }

    function delete($data) {
        $id = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('metadata.id');

        $user = sdiFactory::getSdiUser();
        if (!$user->isEasySDI) {
            //Not an EasySDI user = not allowed
            JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
            JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
            return false;
        }

        if (!$user->authorizeOnMetadata($id, sdiUser::resourcemanager)) {
            //Try to update a resource but not its resource manager
            JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
            JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
            return false;
        }

        $table = $this->getTable();
        if ($table->delete($data['id']) === true) {
            return $id;
        } else {
            return false;
        }

        return true;
    }

    /**
     * Built validators depending on pattern of stereotype and attribute.
     * @since  4.0.0
     */
    private function buildValidators_old() {
        $tmpValidators = array();

        foreach ($this->_structure as $rel) {
            if ($rel->childtype_id == SdiRelation::$ATTRIBUT) {
                $patterns = array();
                $validator = new stdClass();
                if ($rel->getAttribut_child()->getStereotype()->defaultpattern != '') {
                    $validator->name = $rel->getAttribut_child()->getStereotype()->value;
                    $patterns[] = $rel->getAttribut_child()->getStereotype()->defaultpattern;
                }

                if ($rel->getAttribut_child()->pattern != '') {
                    $validator->name = $rel->getAttribut_child()->guid;
                    $patterns[] = $rel->getAttribut_child()->pattern;
                }

                $validator->patterns = $patterns;
                if (isset($validator->name)) {
                    $tmpValidators[$validator->name] = $validator;
                }
            }
        }

        foreach ($tmpValidators as $v) {
            $js = 'document.formvalidator.setHandler(\'sdi' . $v->name . '\', function(value) {
                    ';
            $condition = '';
            for ($i = 0; $i < count($v->patterns); $i++) {
                $js .= 'regex_' . $i . ' = /' . $v->patterns[$i] . '/;
                        ';
                $condition .= 'regex_' . $i . '.test(value) && ';
            }

            $js .= 'if(' . substr($condition, 0, -4) . '){
                            return true;
                        }else{
                            return false;
                        }';

            $js .= '});
                    ';

            $this->_validators[] = $js;
        }
    }

    private function buildValidators() {
        $domXpathStr = new DOMXPath($this->_structure);

        $nsdao = new SdiNamespaceDao();
        $patterns = $this->getPatterns();

        foreach ($nsdao->getAll() as $ns) {
            $domXpathStr->registerNamespace($ns->prefix, $ns->uri);
        }

        $tmpValidator = array();
        foreach ($domXpathStr->query('//*[@catalog:childtypeId="2"]') as $attribute) {

            $guid = $attribute->getAttributeNS($this->catalog_uri, 'id');

            if (array_key_exists($guid, $patterns)) {
                $validator = new stdClass();
                $validator_pattern = array();

                if ($patterns[$guid]->stereotype_pattern != '') {
                    $validator->name = $patterns[$guid]->stereotype_name;
                    $validator_pattern[] = $patterns[$guid]->stereotype_pattern;
                }

                if ($patterns[$guid]->attribute_pattern != '') {
                    $validator->name = $patterns[$guid]->guid;
                    $validator_pattern[] = $patterns[$guid]->attribute_pattern;
                }

                $validator->patterns = $validator_pattern;
                if (isset($validator->name)) {
                    $tmpValidator[$validator->name] = $validator;
                }
            }
        }

        foreach ($tmpValidator as $v) {
            $js = 'document.formvalidator.setHandler(\'sdi' . $v->name . '\', function(value) {
                    ';
            $condition = '';
            for ($i = 0; $i < count($v->patterns); $i++) {
                $js .= 'regex_' . $i . ' = new RegExp(/' . $v->patterns[$i] . '/);
                        ';
                $condition .= 'regex_' . $i . '.test(value) && ';
            }

            $js .= 'if(' . substr($condition, 0, -4) . '){
                            return true;
                        }else{
                            return false;
                        }';

            $js .= '});
                    ';

            $this->_validators[] = $js;
        }
    }

    /**
     * 
     * @return array
     */
    private function getPatterns() {
        $query = $this->db->getQuery(true);

        $query->select('a.id, a.guid, a.pattern as attribute_pattern, s.defaultpattern as stereotype_pattern, s.`value` as stereotype_name');
        $query->from('#__sdi_relation as r');
        $query->innerJoin('#__sdi_attribute as a on r.attributechild_id = a.id');
        $query->leftJoin('#__sdi_sys_stereotype as s on a.stereotype_id = s.id');
        $query->where('r.`state` = 1');

        $this->db->setQuery($query);
        return $this->db->loadObjectList('guid');
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

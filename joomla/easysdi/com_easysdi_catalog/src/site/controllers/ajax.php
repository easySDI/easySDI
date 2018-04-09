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
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/dao/SdiNamespaceDao.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/FormUtils.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/helpers/curl.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/helpers/easysdi_file.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/helpers/easysdi_errorHelper.php';

class Easysdi_catalogControllerAjax extends Easysdi_catalogController {

    /**
     * database
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

    function __construct() {
        $this->session = JFactory::getSession();
        $this->db = JFactory::getDbo();
        $this->structure = new DOMDocument('1.0', 'utf-8');
        $this->structure->loadXML(unserialize($this->session->get('structure')));
        $this->nsdao = new SdiNamespaceDao();

        parent::__construct();
    }

    public function removeNode() {
        $this->domXpathStr = new DOMXPath($this->structure);
//print_r($this->structure->saveXML()); die();
        foreach ($this->nsdao->getAll() as $ns) {
            $this->domXpathStr->registerNamespace($ns->prefix, $ns->uri);
        }
        $query = FormUtils::unSerializeXpath($_GET['uuid']);

        $elements = $this->domXpathStr->query($query); //->item(0);

        if ($elements->length) {
            $element = $elements->item(0);
        } else { // HACK TO ALLOW FIRST KEYWORD REMOVAL
            $tabQuery = explode('/', $query);
            array_pop($tabQuery);
            $query = implode('/', $tabQuery);
            $element = $this->domXpathStr->query($query)->item(0)->childNodes->item(1);
        }

        $response = array();
        try {
            $element->parentNode->removeChild($element);
            $response['success'] = 'true';
            $this->session->set('structure', serialize($this->structure->saveXML()));
        } catch (Exception $exc) {
            $response['success'] = 'false';
            $response['message'] = $exc->getMessage();
        }

        echo json_encode($response);
        die();
    }

    /**
     * get defined boundary, filter by category
     */
    public function getBoundaryByCategory() {
        $user = JFactory::getUser();
        $default_lang = $user->getParam('language', JFactory::getLanguage());

        $name = addslashes($_GET['value']);
        $query = $this->db->getQuery(true);
        $query->select('t.text1 as option_value, b.alias, ' . $this->db->quoteName('b.name') . ', b.northbound, b.southbound, b.westbound, b.eastbound ');
        $query->from('#__sdi_boundary AS b');
        $query->innerJoin('#__sdi_boundarycategory as bc ON b.category_id = bc.id');
        $query->innerJoin('#__sdi_translation t ON b.guid = t.element_guid');
        $query->innerJoin('#__sdi_language as l ON l.id = t.language_id');
        $query->where($this->db->quoteName('bc.name') . ' = ' . $this->db->quote($name));
        $query->where('l.code = ' . $this->db->quote($default_lang));


        $this->db->setQuery($query);
        $results = $this->db->loadObjectList();

        echo json_encode($results);
        die();
    }

    /**
     * Get defined boundary, boundary name
     */
    public function getBoundaryByName() {
        if (empty($_GET['value'])) {
            return null;
            die();
        }
        //$name = addslashes($_GET['value']);
        $name = $_GET['value'];
        $query = $this->db->getQuery(true);
        $query->select('t.text1, b.alias, b.northbound, b.southbound, b.westbound, b.eastbound');
        $query->from('#__sdi_boundary AS b');
        $query->innerJoin('#__sdi_translation t ON b.guid = t.element_guid ');
        $query->where('t.text1 = ' . $this->db->quote($name));

        $this->db->setQuery($query);
        $result = $this->db->loadObject();

        echo json_encode($result);
        die();
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
        $resourcetype = $this->db->loadObjectList('id');

        echo json_encode($resourcetype);
        die();
    }

    /**
     * Return the current metadata id in session
     */
    public function getCurrentEditId() {
        $app = JFactory::getApplication();
        $response = array();
        $response['success'] = true;
        $response['id'] = $app->getUserState('com_easysdi_catalog.edit.metadata.id');
        echo json_encode($response);
        die();
    }

    /**
     * Check Url from file popup
     */
    public function checkFileUrl() {
        $curlHelper = new CurlHelper();
        $curlHelper->URLChecker(JFactory::getApplication()->input);
        die();
    }

    public function uploadFile() {

        $target_folder = JPATH_BASE . '/media/easysdi/' . JComponentHelper::getParams('com_easysdi_catalog')->get('linkedfilerepository');
        $fileBaseUrl = JComponentHelper::getParams('com_easysdi_catalog')->get('linkedfilebaseurl');



        $fu = new Easysdi_filedHelper();
        try {
            $result['files'] = $fu->upload($_FILES, $target_folder, $fileBaseUrl, true, NULL, false, NULL, array(), array(), true, $target_folder . '/thumbnails', $fileBaseUrl . '/thumbnails', 120);
            $result['status'] = 'success';
            header('Content-type: application/json');
            echo json_encode($result);
            die();
        } catch (Exception $exc) {
            header('Content-type: application/json');
            $result = array();
            $result['error'] = Easysdi_errorHelper::getAncestors($exc, true);
            $result['status'] = 'fail';
            echo json_encode($result);
            die();
        }
    }

    private function unSerializeXpath($xpath) {
        $xpath = str_replace('-la-', '[', $xpath);
        $xpath = str_replace('-ra-', ']', $xpath);
        $xpath = str_replace('-sla-', '/', $xpath);
        $xpath = str_replace('-dp-', ':', $xpath);
        return $xpath;
    }

}

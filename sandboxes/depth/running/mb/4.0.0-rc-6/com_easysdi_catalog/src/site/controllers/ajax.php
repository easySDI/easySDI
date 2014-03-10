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
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/dao/SdiNamespaceDao.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/FormUtils.php';

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

        foreach ($this->nsdao->getAll() as $ns) {
            $this->domXpathStr->registerNamespace($ns->prefix, $ns->uri);
        }
        $query = FormUtils::unSerializeXpath($_GET['uuid']);
        $element = $this->domXpathStr->query($query)->item(0);

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
        $query->select('t.text1 as option_value, b.alias, b.`name`, b.northbound, b.southbound, b.westbound, b.eastbound ');
        $query->from('#__sdi_boundary AS b');
        $query->innerJoin('#__sdi_boundarycategory as bc ON b.category_id = bc.id');
        $query->innerJoin('#__sdi_translation t ON b.guid = t.element_guid');
        $query->innerJoin('#__sdi_language as l ON l.id = t.language_id');
        $query->where('bc.`name` = \'' . $name . '\'');
        $query->where('l.code = \'' . $default_lang . '\'');
        

        $this->db->setQuery($query);
        $results = $this->db->loadObjectList();

        echo json_encode($results);
        die();
    }
    
    /**
     * Get defined boundary, boundary name
     */
    public function getBoundaryByName(){
        if(empty($_GET['value'])){
            return null;
            die();
        }
        $name = addslashes($_GET['value']);
        $query = $this->db->getQuery(true);
        $query->select('t.text1, b.alias, b.northbound, b.southbound, b.westbound, b.eastbound');
        $query->from('#__sdi_boundary AS b');
        $query->innerJoin('#__sdi_translation t ON b.guid = t.element_guid ');
        $query->where('t.text1 = \'' . $name . '\'');
        
        $this->db->setQuery($query);
        $result = $this->db->loadObject();
        
        echo json_encode($result);
        die();
    }

    private function unSerializeXpath($xpath) {
        $xpath = str_replace('-la-', '[', $xpath);
        $xpath = str_replace('-ra-', ']', $xpath);
        $xpath = str_replace('-sla-', '/', $xpath);
        $xpath = str_replace('-dp-', ':', $xpath);
        return $xpath;
    }

}
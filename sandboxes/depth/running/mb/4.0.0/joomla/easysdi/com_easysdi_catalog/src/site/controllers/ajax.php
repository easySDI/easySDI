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

/**
 * Metadata controller class.
 */
class Easysdi_catalogControllerAjax extends Easysdi_catalogController {
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
        $query = $this->unSerializeXpath($_GET['uuid']);
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

    private function unSerializeXpath($xpath) {
        $xpath = str_replace('-la-', '[', $xpath);
        $xpath = str_replace('-ra-', ']', $xpath);
        $xpath = str_replace('-sla-', '/', $xpath);
        $xpath = str_replace('-dp-', ':', $xpath);
        return $xpath;
    }

}
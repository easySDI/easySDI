<?php

/**
 * @version     4.0.0
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_contact/tables/user.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/tables/version.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/tables/resource.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_catalog/tables/metadata.php';

class cswmetadata {

    /**
     * database
     *
     * @var    JDatabaseDriver
     */
    public $db = null;

    /**
     * 
     */
    public $guid = null;

    /**
     * 
     */
    public $catalogurl = null;

    /**
     * 
     */
    public $dom = null;

    /**
     * 
     */
    public $extendeddom = null;
    
    /**
     * Easysdi_catalogTablemetadata
     *
     * @var    Easysdi_catalogTablemetadata
     */
    public $metadata = null;

    /**
     *
     * @var Easysdi_coreTableversion 
     */
    public $version = null;

    /**
     *
     * @var Easysdi_coreTableresource 
     */
    public $resource = null;

    function __construct($guid) {
        $this->guid = $guid;
        $this->db = JFactory::getDbo();
        $params = JComponentHelper::getParams('com_easysdi_catalog');
        $this->catalogurl = $params->get('catalogurl');
        $this->rootxslfile = $params->get('rootXSLfile');
    }

    /**
     * 
     */
    public function load() {
        $catalogUrlGetRecordById = $this->catalogurl . "?request=GetRecordById&service=CSW&version=2.0.2&elementSetName=full&outputschema=csw:IsoRecord&content=CORE&id=" . $this->guid;
        $response = $this->CURLRequest("GET", $catalogUrlGetRecordById);
        $doc = new DOMDocument();
        $doc->loadXML($response);

        if ($doc == false) {
            $msg = JText::_('CATALOG_METADATA_EDIT_NOMETADATA_MSG');
            JFactory::getApplication()->enqueueMessage('No such metadata in the catalog.', 'error');
            return false;
        }
        if ($doc->getElementsByTagName("ExceptionReport")->length > 0) {
            $msg = $doc->getElementsByTagName("ExceptionReport")->item(0)->nodeValue;
            JFactory::getApplication()->enqueueMessage($msg, 'error');
            return false;
        }

        $elements = $doc->getElementsByTagName('GetRecordByIdResponse');
        $this->dom = new DOMDocument('1.0', 'UTF-8');
        $children = $elements->item(0)->childNodes;
        foreach ($children as $child):
            if ($child->nodeType == XML_ELEMENT_NODE):
                $xmlContent = $this->dom->importNode($child, true);
                $this->dom->appendChild($xmlContent);
                break;
            endif;
        endforeach;

        return $doc;
    }

    /**
     * a call to this function replaces a GetRecordById request by giving directly the metadata xml content
     * @param mixed $metadata DOMDocument or DOMElement
     */
    public function init($metadata) {
        if ($metadata):
            if ($metadata instanceof DOMDocument) {
                $this->dom = $metadata;
            } else
            if ($metadata instanceof DOMElement) {

                $this->dom = new DOMDocument('1.0', 'UTF-8');
                $xmlContent = $this->dom->importNode($metadata, true);
                $this->dom->appendChild($xmlContent);
            } else {

                $this->dom = new DOMDocument('1.0', 'UTF-8');


                $xmlContent = $this->dom->importNode(dom_import_simplexml($metadata), true);
                $this->dom->appendChild($xmlContent);
            }
        endif;
    }

    public function extend($callfromJoomla, $lang) {
        //Is it an harvested metadata
        $xpath = new DomXPath($this->dom);
        $xpath->registerNamespace('sdi', 'http://www.easysdi.org/2011/sdi');
        $sdiplatform = $xpath->query('//sdi:platform');
        $isharvested = $sdiplatform->item(0)->getAttribute('harvested');

        $root = $this->dom->documentElement;

        $this->extendeddom = new DOMDocument('1.0', 'UTF-8');
        $this->extendeddom->formatOutput = true;
        $gmdroot = $this->extendeddom->importNode($root, true);

        $extendedroot = $this->extendeddom->createElement("Metadata");
        $this->extendeddom->appendChild($extendedroot);
        $extendedroot->appendChild($gmdroot);

        $extendedmetadata = $this->extendeddom->createElementNS('http://www.easysdi.org/2011/sdi', 'sdi:ex_metadata');
        $extendedmetadata->setAttribute('lang', $lang);
        $extendedmetadata->setAttribute('callfromjoomla', (int) $callfromJoomla);
        $extendedroot->appendChild($extendedmetadata);

        if ($isharvested == 'false') {
            $this->metadata = JTable::getInstance('metadata', 'Easysdi_catalogTable');
            $keys = array("guid" => $this->guid);
            $this->metadata->load($keys);
            $this->version = JTable::getInstance('version', 'Easysdi_coreTable');
            $this->version->load($this->metadata->version_id);
            $this->resource = JTable::getInstance('resource', 'Easysdi_coreTable');
            $this->resource->load($this->version->resource_id);
            
            $exresource = $this->extendeddom->createElementNS('http://www.easysdi.org/2011/sdi', 'sdi:ex_Resource');
            $exresource->setAttribute('name', $this->resource->name);
            $exresource->setAttribute('descriptionLength', '300');
            
            $exorganism = $this->extendeddom->createElementNS('http://www.easysdi.org/2011/sdi', 'sdi:ex_Organism');
            $exorganism->setAttribute('name', $this->resource->organism_id);
            
            $exlogo = $this->extendeddom->createElementNS('http://www.easysdi.org/2011/sdi', 'sdi:ex_Logo');
            $exlogo->setAttribute('path', '');
            $exlogo->setAttribute('width', '');
            $exlogo->setAttribute('height', '');
            
            $exresourcetype = $this->extendeddom->createElementNS('http://www.easysdi.org/2011/sdi', 'sdi:ex_Resourcetype');
            $exresourcetype->setAttribute('name', '');
            $exresourcetype->setAttribute('alias', '');
            
            $logo = $this->extendeddom->createElementNS('http://www.easysdi.org/2011/sdi', 'sdi:ex_Logo');
            $logo->setAttribute('path', '');
            $logo->setAttribute('width', '');
            $logo->setAttribute('height', '');
            
            $exversion = $this->extendeddom->createElementNS('http://www.easysdi.org/2011/sdi', 'sdi:ex_Version');
            $exversion->setAttribute('name', $this->version->name);
            
            $exmetadata = $this->extendeddom->createElementNS('http://www.easysdi.org/2011/sdi', 'sdi:ex_Metadata');
            $exmetadata->setAttribute('created', $this->metadata->created);
            $exmetadata->setAttribute('updated', $this->metadata->updated);
            
            $exdiffusion = $this->extendeddom->createElementNS('http://www.easysdi.org/2011/sdi', 'sdi:ex_Diffusion');
            $exdiffusion->setAttribute('isfree', 'true');
            $exdiffusion->setAttribute('isDownladable', 'true');
            $exdiffusion->setAttribute('isOrderable', 'true');
            $exdiffusion->setAttribute('file_size', '1');
            $exdiffusion->setAttribute('size_unit', 'MB');
            $exdiffusion->setAttribute('file_type', 'zip');
            
            $exmetadata->appendChild($exdiffusion);
            $exresource->appendChild($exmetadata);
            $exresource->appendChild($exversion);
            $exresourcetype->appendChild($logo);
            $exresource->appendChild($exresourcetype);
            $exorganism->appendChild($exlogo);
            $exresource->appendChild($exorganism);
            $extendedmetadata->appendChild($exresource);
        }

        return $this->extendeddom;
    }

    public function applyXSL($dom = null) {
        if (empty($dom)) {
            $dom = $this->extendeddom;
        }

        $style = new DomDocument();
        if (!$style->load(JPATH_BASE . '/media/easysdi/catalog/xsl/' . $this->rootxslfile)):
            return false;
        endif;
        $processor = new xsltProcessor();
        $processor->importStylesheet($style);
        $html = $processor->transformToDoc($dom);
        $text = $html->saveXML();
        //Workaround to avoid printf problem with text with a "%", must
        //be changed to "%%".
        $text = str_replace("%", "%%", $text);
        $text = str_replace("__ref_", "%", $text);


        return $text;
    }

    protected function CURLRequest($type, $url, $xmlBody = "") {
        // Get COOKIE as key=value
        $cookiesList = array();
        foreach ($_COOKIE as $key => $val) {
            $cookiesList[] = $key . "=" . $val;
        }
        $cookies = implode(";", $cookiesList);

        $ch = curl_init($url);
        // Configuration
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml; charset="UTF-8"', 'charset="UTF-8"'));
        curl_setopt($ch, CURLOPT_COOKIE, $cookies);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_ENCODING, "");

        // Specific POST
        if ($type == "POST") {
            curl_setopt($ch, CURLOPT_POST, 1);
            //curl_setopt($ch, CURLOPT_MUTE, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "$xmlBody");
        }
        // Specific GET
        else if ($type == "GET") {
            curl_setopt($ch, CURLOPT_POST, 0);
        }

        //User authentication
        $params = JComponentHelper::getParams('com_easysdi_contact');
        $serviceaccount_id = $params->get('serviceaccount');
        $juser = JFactory::getUser($serviceaccount_id);

        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $juser->username . ":" . $juser->password);

        $output = curl_exec($ch);
        curl_close($ch);

        return $output;
    }

}

?>

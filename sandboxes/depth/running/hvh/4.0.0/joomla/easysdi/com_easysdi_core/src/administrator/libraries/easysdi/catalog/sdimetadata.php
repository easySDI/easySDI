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

class sdiMetadata {

    /**
     * Unique metadataid
     *
     * @var    integer
     */
    public $id = null;

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

    /**
     * database
     *
     * @var    JDatabaseDriver
     */
    public $db = null;

    /**
     * 
     */
    public $catalogurl = null;
        
    /**
     * 
     */
    function __construct($metadataid = null) {
        $this->id = $metadataid;
        $this->metadata = JTable::getInstance('metadata', 'Easysdi_catalogTable');
        $this->metadata->load($this->id);
        $this->version = JTable::getInstance('version', 'Easysdi_coreTable');
        $this->version->load($this->metadata->version_id);
        $this->resource = JTable::getInstance('resource', 'Easysdi_coreTable');
        $this->resource->load($this->version->resource_id);

        $this->db = JFactory::getDbo();

        $params = JComponentHelper::getParams('com_easysdi_catalog');
        $this->catalogurl = $params->get('catalogurl');
    }

    /**
     * 
     */
    public function load() {

        $catalogUrlGetRecordById = $this->catalogurl . "?request=GetRecordById&service=CSW&version=2.0.2&elementSetName=full&outputschema=csw:IsoRecord&content=CORE&id=" . $this->metadata->guid;

        $response = $this->CURLRequest("GET", $catalogUrlGetRecordById);
        
        $doc = new DOMDocument();
        $doc->loadXML($response);

        if ($doc <> false and $doc->childNodes->item(0)->hasChildNodes()){

        }
        else if ($doc->childNodes->item(0)->nodeName == "ows:ExceptionReport") {
            $msg = $doc->childNodes->item(0)->nodeValue;
            JFactory::getApplication()->enqueueMessage($msg, 'error');
            return false;
        } 
        else {
            $msg = JText::_('CATALOG_METADATA_EDIT_NOMETADATA_MSG');
            JFactory::getApplication()->enqueueMessage('No such metadata in the catalog.', 'error');
            return false;
        }
//        $xpathResults->registerNamespace('csw', 'http://www.opengis.net/cat/csw/2.0.2');
//        $xpathResults->registerNamespace('srv', 'http://www.isotc211.org/2005/srv');
//        $xpathResults->registerNamespace('xlink', 'http://www.w3.org/1999/xlink');
//        $xpathResults->registerNamespace('gts', 'http://www.isotc211.org/2005/gts');
//
//        // Récupération des namespaces à inclure
//        $query = $this->db->getQuery(true)
//                ->select('prefix, uri')
//                ->from('#__sdi_namespace')
//                ->order('prefix');
//        $this->db->setQuery($query);
//        $namespaces = $this->db->loadObjectList();
//        foreach ($namespaces as $namespace) {
//            $xpathResults->registerNamespace($namespace->prefix, $namespace->uri);
//        }
        
        
        
        return $response;
    }

    /**
     * Insert a newly created metadata into the CSW catalog
     * 
     */
    public function insert() {
        try {
            //Get from the metadata structure, the attribute to store the metadata ID

            $query = $this->db->getQuery(true);
            $query = "SELECT a.name as name, ns.prefix as ns, 
                        CONCAT(ns.prefix, ':', a.isocode) as attribute_isocode, 
                        CONCAT(atns.prefix, ':', at.isocode) as type_isocode 
                        FROM #__sdi_profile p
                            INNER JOIN #__sdi_resourcetype rt on p.id=rt.profile_id 
                            INNER JOIN #__sdi_attribute a on a.id=p.metadataidentifier 
                            INNER JOIN #__sdi_relation rel on rel.attributechild_id=a.id
                            INNER JOIN #__sdi_sys_stereotype as at ON at.id=a.stereotype_id 
                            LEFT OUTER JOIN #__sdi_namespace ns ON a.namespace_id=ns.id 
                            LEFT OUTER JOIN #__sdi_namespace atns ON at.namespace_id=atns.id 
                        WHERE rt.id=" . $this->resource->resourcetype_id;
            $this->db->setQuery($query);
            $attributeIdentifier = $this->db->loadObject();

            //Get from the metadata structure the root classe
            $query = $this->db->getQuery(true);
            $query = "SELECT CONCAT(ns.prefix,':',c.isocode) as isocode 
                                FROM #__sdi_profile p 
                                INNER JOIN #__sdi_resourcetype rt ON p.id=rt.profile_id
                                INNER JOIN #__sdi_class c ON c.id=p.class_id  
                                LEFT OUTER JOIN #__sdi_namespace as ns ON c.namespace_id=ns.id 
                                WHERE rt.id=" . $this->resource->resourcetype_id;
            $this->db->setQuery($query);
            $rootclass = $this->db->loadObject();
        } catch (Exception $e) {
            JFactory::getApplication()->enqueueMessage('Error when building metadata CSW structure.', 'error');
            return false;
        }

        /*
         * <sdi:platform guid ="" harvested="false">
          <rsdi:esource guid="" alias="" name="" type="" organism="" scope="">
          <sdi:organisms>
          <sdi:organism guid=""/>
          </sdi:organisms>
          <sdi:users>
          <sdi:user guid=""/>
          </sdi:users>
          <sdi:metadata lastVersion="true" guid="" created="" published="" state="">
          <sdi:diffusion isFree="false" osDownloadable="false" isOrderable="true"/>
          </sdi:metadata>
          </sdi:resource>
          </sdi:platform>
         */
        //Create metadata XML
        $xmlstr = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<csw:Transaction service=\"CSW\"
			version=\"2.0.2\"
			xmlns:csw=\"http://www.opengis.net/cat/csw/2.0.2\" >
				<csw:Insert>
					<" . $rootclass->isocode . "
						xmlns:gmd=\"http://www.isotc211.org/2005/gmd\" 
						xmlns:gco=\"http://www.isotc211.org/2005/gco\" 
						xmlns:xlink=\"http://www.w3.org/1999/xlink\" 
						xmlns:gml=\"http://www.opengis.net/gml\" 
						xmlns:gts=\"http://www.isotc211.org/2005/gts\" 
						xmlns:srv=\"http://www.isotc211.org/2005/srv\"
						xmlns:ext=\"http://www.depth.ch/2008/ext\">
						
						<" . $attributeIdentifier->attribute_isocode . ">
							<" . $attributeIdentifier->type_isocode . ">" . $this->metadata->guid . "</" . $attributeIdentifier->type_isocode . ">
						</" . $attributeIdentifier->attribute_isocode . ">
					</" . $rootclass->isocode . ">
				</csw:Insert>
			</csw:Transaction>";



        $result = $this->CURLRequest("POST", $this->catalogurl, $xmlstr);

        $insertResults = new DOMDocument();
        $insertResults->loadXML($result);
        $xpathInsert = new DOMXPath($insertResults);
        $xpathInsert->registerNamespace('csw', 'http://www.opengis.net/cat/csw/2.0.2');
        $inserted = $xpathInsert->query("//csw:totalInserted")->item(0)->nodeValue;

        if ($inserted <> 1) {
            JFactory::getApplication()->enqueueMessage('Metadata insertion failed.', 'error');
            return false;
        }
        return true;
    }

    /**
     * 
     */
    public function update($xml) {
        return true;
    }

    /**
     * 
     */
    public function updateSDIElement() {
        return true;
    }

    private function CURLRequest($type, $url, $xmlBody = "") {
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

<?php

/**
 * @version     4.0.0
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/catalog/cswmetadata.php';

class sdiMetadata extends cswmetadata {

    /**
     * Unique metadataid
     *
     * @var    integer
     */
    public $id = null;

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

    // metadata state list
    const INPROGRESS = 1;
    const VALIDATED = 2;
    const PUBLISHED = 3;
    const ARCHIVED = 4;
    const TRASHED = 5;

    /**
     * 
     */
    function __construct($metadataid = null) {
        $this->id = $metadataid;
        $this->metadata = JTable::getInstance('metadata', 'Easysdi_catalogTable');
        $this->metadata->load($this->id);
        $this->guid = $this->metadata->guid;
        $this->version = JTable::getInstance('version', 'Easysdi_coreTable');
        $this->version->load($this->metadata->version_id);
        $this->resource = JTable::getInstance('resource', 'Easysdi_coreTable');
        $this->resource->load($this->version->resource_id);

        $this->db = JFactory::getDbo();

        $params = JComponentHelper::getParams('com_easysdi_catalog');
        $this->catalogurl = $params->get('catalogurl');
    }

    /**
     * Insert a newly created metadata into the CSW catalog
     * 
     * <sdi:platform guid ="" harvested="false">
      <sdi:resource guid="" alias="" name="" type="" organism="" scope="">
      <sdi:organisms>
      <sdi:organism guid=""/>
      </sdi:organisms>
      <sdi:users>
      <sdi:user guid=""/>
      </sdi:users>
      <sdi:metadata lastVersion="true" guid="" created="" published="" state="">
      <sdi:diffusion isFree="false" isDownloadable="false" isOrderable="true"/>
      </sdi:metadata>
      </sdi:resource>
      </sdi:platform>
     */
    public function insert() {

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


        //Create metadata XML
        $this->dom = new DOMDocument('1.0', 'utf-8');

        $transaction = $this->dom->createElement('csw:Transaction');
        $transaction->setAttribute('xmlns:csw', 'http://www.opengis.net/cat/csw/2.0.2/');
        $transaction->setAttribute('xmlns:ogc', 'http://www.opengis.net/ogc');
        $transaction->setAttribute('service', "CSW");
        $transaction->setAttribute('version', "2.0.2");

        $insert = $this->dom->createElement('csw:Insert');

        $root = $this->dom->createElement($rootclass->isocode);
        $root->setAttribute('xmlns:gmd', 'http://www.isotc211.org/2005/gmd');
        $root->setAttribute('xmlns:gco', 'http://www.isotc211.org/2005/gco');
        $root->setAttribute('xmlns:gml', 'http://www.opengis.net/gml');
        $root->setAttribute('xmlns:sdi', 'http://www.easysdi.org/2011/sdi');


        $identifier = $this->dom->createElement($attributeIdentifier->attribute_isocode);
        $identifiertype = $this->dom->createElement($attributeIdentifier->type_isocode, $this->metadata->guid);

        $platform = $this->getPlatformNode($this->dom);

        $identifier->appendChild($identifiertype);
        $root->appendChild($identifier);
        $root->appendChild($platform);
        $insert->appendChild($root);
        $transaction->appendChild($insert);
        $this->dom->appendChild($transaction);
        $this->dom->formatOutput = true;
        $xml = $this->dom->saveXML();

        $result = $this->CURLRequest("POST", $this->catalogurl, $xml);

        if (empty($result))
            return false;

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
        
        $reponse = $this->CURLRequest('POST', $this->catalogurl, $xml);
        $dom = new DOMDocument();
        $dom->loadXML($reponse);
        $totalUpdated = $dom->getElementsByTagNameNS('http://www.opengis.net/cat/csw/2.0.2', 'totalUpdated')->item(0);

        if (isset($totalUpdated)) {
            if ($totalUpdated->nodeValue == 1) {
                return true;
            }else{
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 
     */
    public function delete() {

        //Create metadata XML
        $this->dom = new DOMDocument('1.0', 'utf-8');

        $transaction = $this->dom->createElement('csw:Transaction');
        $transaction->setAttribute('xmlns:csw', 'http://www.opengis.net/cat/csw/2.0.2');
        $transaction->setAttribute('xmlns:ogc', 'http://www.opengis.net/ogc');
        $transaction->setAttribute('service', "CSW");
        $transaction->setAttribute('version', "2.0.2");

        $delete = $this->dom->createElement('csw:Delete');

        $constraint = $this->dom->createElement('csw:Constraint');
        $constraint->setAttribute('version', '1.0.0');

        $filter = $this->dom->createElement('ogc:Filter');
        $propertyEqual = $this->dom->createElement('ogc:PropertyIsLike');
        $propertyEqual->setAttribute('wildCard', '%');
        $propertyEqual->setAttribute('singleChar', '_');
        $propertyEqual->setAttribute('escape', '/');
        $propertyName = $this->dom->createElement('ogc:PropertyName', JComponentHelper::getParams('com_easysdi_catalog')->get('idogcsearchfield'));
        $literal = $this->dom->createElement('ogc:Literal', $this->metadata->guid);

        $propertyEqual->appendChild($propertyName);
        $propertyEqual->appendChild($literal);
        $filter->appendChild($propertyEqual);
        $constraint->appendChild($filter);
        $delete->appendChild($constraint);
        $transaction->appendChild($delete);

        $this->dom->appendChild($transaction);
        $this->dom->formatOutput = true;
        $xml = $this->dom->saveXML();
        $result = $this->CURLRequest("POST", $this->catalogurl, $xml);

        if (empty($result))
            return false;

        $insertDom = new DOMDocument();
        $insertDom->loadXML($result);
        $xpathDelete = new DOMXPath($insertDom);
        $xpathDelete->registerNamespace('csw', 'http://www.opengis.net/cat/csw/2.0.2');
        $deleted = $xpathDelete->query("//csw:totalDeleted")->item(0)->nodeValue;

        if ($deleted <> 1) {
            JFactory::getApplication()->enqueueMessage('Metadata deletion failed.', 'error');
            return false;
        }

        return true;
    }

    /**
     * Load a metadata and update the SDI elements.
     * 
     * @return Boolean 
     */
    public function updateSDIElement() {
        $dom = $this->load();

        $xpathmetadata = new DOMXPath($dom);
        $xpathmetadata->registerNamespace('gmd', 'http://www.isotc211.org/2005/gmd');
        $metadata = $xpathmetadata->query($this->getMetadataRootClass()->isocode)->item(0);

        $newdom = new DOMDocument('1.0', 'utf-8');
        $transaction = $newdom->createElement('csw:Transaction');
        $transaction->setAttribute('xmlns:csw', 'http://www.opengis.net/cat/csw/2.0.2');
        $transaction->setAttribute('xmlns:ogc', 'http://www.opengis.net/ogc');
        $transaction->setAttribute('service', "CSW");
        $transaction->setAttribute('version', "2.0.2");
        $update = $newdom->createElement('csw:Update');
        $update->appendChild($newdom->importNode($metadata, true));

        $constraint = $newdom->createElement('csw:Constraint');
        $constraint->setAttribute('version', '1.0.0');

        $filter = $newdom->createElement('ogc:Filter');
        $propertyEqual = $newdom->createElement('ogc:PropertyIsLike');
        $propertyEqual->setAttribute('wildCard', '%');
        $propertyEqual->setAttribute('singleChar', '_');
        $propertyEqual->setAttribute('escape', '/');
        $propertyName = $newdom->createElement('ogc:PropertyName', JComponentHelper::getParams('com_easysdi_catalog')->get('idogcsearchfield'));
        $literal = $newdom->createElement('ogc:Literal', $this->metadata->guid);

        $propertyEqual->appendChild($propertyName);
        $propertyEqual->appendChild($literal);
        $filter->appendChild($propertyEqual);
        $constraint->appendChild($filter);

        $update->appendChild($constraint);
        $transaction->appendChild($update);
        $newdom->appendChild($transaction);

        $platform = $this->getPlatformNode($newdom);
        $xpath = new DOMXPath($newdom);
        $xpath->registerNamespace('sdi', 'http://www.easysdi.org/2011/sdi');
        $oldplatform = $xpath->query("//sdi:platform")->item(0);
        $parentNode = $oldplatform->parentNode;
        if ($parentNode->replaceChild($platform, $oldplatform)):

            $newdom->formatOutput = true;
            $xml = $newdom->saveXML();

            $result = $this->CURLRequest("POST", $this->catalogurl, $xml);

            if (empty($result))
                return false;

            $results = new DOMDocument();
            $results->loadXML($result);
            $xpathUpdated = new DOMXPath($results);
            $xpathUpdated->registerNamespace('csw', 'http://www.opengis.net/cat/csw/2.0.2');
            $updated = $xpathUpdated->query("//csw:totalUpdated")->item(0)->nodeValue;

            if ($updated <> 1) {
                JFactory::getApplication()->enqueueMessage('Metadata update failed.', 'error');
                return false;
            }
            return true;
        else:
            return false;

        endif;
    }

    
    /**
     * 
     * @param DOMDocument $dom The "DOM" in which to add or update the platform tag.
     * @return DOMElement the platform tag.
     */
    public function getPlatformNode(DOMDocument $dom) {
        //Get the resourcetype alias
        $query = $this->db->getQuery(true);
        $query->select('alias')
                ->from('#__sdi_resourcetype')
                ->where('id = ' . $this->resource->resourcetype_id);
        $this->db->setQuery($query);
        $resourcetype = $this->db->loadResult();

        //Get the organism guid
        $query = $this->db->getQuery(true);
        $query->select('guid')
                ->from('#__sdi_organism')
                ->where('id = ' . $this->resource->organism_id);
        $this->db->setQuery($query);
        $organism = $this->db->loadResult();

        //Get the accessscope value
        $query = $this->db->getQuery(true);
        $query->select('value')
                ->from('#__sdi_sys_accessscope')
                ->where('id = ' . $this->resource->accessscope_id);
        $this->db->setQuery($query);
        $accessscope = $this->db->loadResult();

        //Get the accessscope
        $query = $this->db->getQuery(true);
        $query->select('a.organism_id, a.user_id, o.guid as  o_guid, u.guid as u_guid')
                ->from('#__sdi_accessscope a')
                ->leftJoin('#__sdi_organism o ON o.id = a.organism_id')
                ->leftJoin('#__sdi_user u ON u.id = a.user_id')
                ->where('entity_guid = "' . $this->resource->guid . '"');
        $this->db->setQuery($query);
        $accessscopes = $this->db->loadObjectList();

        //Get the infrastructureID
        $infrastructureID = JComponentHelper::getParams('com_easysdi_core')->get('infrastructureID');

        //Platform
        $platform = $dom->createElement('sdi:platform');
        $platform->setAttribute('guid', $infrastructureID);
        $platform->setAttribute('harvested', 'false');

        //Resource
        $resource = $dom->createElement('sdi:resource');
        $resource->setAttribute('guid', $this->resource->guid);
        $resource->setAttribute('alias', $this->resource->alias);
        $resource->setAttribute('name', $this->resource->name);
        $resource->setAttribute('type', $resourcetype);
        $resource->setAttribute('organism', $organism);
        $resource->setAttribute('scope', $accessscope);

        //Metadata
        $metadata = $dom->createElement('sdi:metadata');
        $metadata->setAttribute('lastVersion', 'true');
        $metadata->setAttribute('guid', $this->metadata->guid);
        $metadata->setAttribute('created', $this->metadata->created);
        $metadata->setAttribute('published', $this->metadata->published);
        $metadata->setAttribute('state', 'inprogress');

        //Diffusion
        $query = $this->db->getQuery(true)
                ->select('id, guid ,pricing_id, hasdownload, hasextraction, accessscope_id')
                ->from('#__sdi_diffusion')
                ->where('version_id = ' . $this->metadata->version_id)
                ->where('state = 1');
        $this->db->setQuery($query);
        $diffusionobj = $this->db->loadObject();
        $diffusion = $dom->createElement('sdi:diffusion');
        if (!empty($diffusionobj)):
            $isfree = ($diffusionobj->pricing_id == 1 ) ? 'true' : 'false';
            $isDownladable = ($diffusionobj->hasdownload == 1 ) ? 'true' : 'false';
            $isOrderable = ($diffusionobj->hasextraction == 1 ) ? 'true' : 'false';
            $diffusion->setAttribute('isFree', $isfree);
            $diffusion->setAttribute('isDownloadable', $isDownladable);
            $diffusion->setAttribute('isOrderable', $isOrderable);
        else:

            $diffusion->setAttribute('isFree', 'false');
            $diffusion->setAttribute('isDownloadable', 'false');
            $diffusion->setAttribute('isOrderable', 'false');
        endif;
        
        //Visualization
        $query = $this->db->getQuery(true)
                ->select('id, wmsservice_id, accessscope_id')
                ->from('#__sdi_visualization')
                ->where('version_id = ' . $this->metadata->version_id)
                ->where('state = 1');
        $this->db->setQuery($query);
        $viewobj = $this->db->loadObject();
        $view = $dom->createElement('sdi:visualization');
        if (!empty($viewobj) && !empty($viewobj->wmsservice_id)):
            $view->setAttribute('isViewable', 'true');
        else:
            $view->setAttribute('isViewable', 'false');
        endif;

        //Accessscope
        foreach ($accessscopes as $a) {
            if ($a->organism_id != null):
                if (!isset($organisms))
                    $organisms = $dom->createElement('sdi:organisms');
                $organism = $dom->createElement('sdi:organism');
                $organism->setAttribute('guid', $a->o_guid);
                $organisms->appendChild($organism);
            elseif ($a->user_id != null):
                if (!isset($users))
                    $users = $dom->createElement('sdi:users');
                $user = $dom->createElement('sdi:user');
                $user->setAttribute('guid', $a->u_guid);
                $users->appendChild($user);
            endif;
        }

        $metadata->appendChild($diffusion);
        $metadata->appendChild($view);
        if (isset($organisms))
            $resource->appendChild($organisms);
        if (isset($users))
            $resource->appendChild($users);
        $resource->appendChild($metadata);
        $platform->appendChild($resource);

        return $platform;
    }

    protected function getMetadataRootClass() {
        //Get from the metadata structure the root classe
        $query = $this->db->getQuery(true);
        $query = "SELECT CONCAT(ns.prefix,':',c.isocode) as isocode 
                                FROM #__sdi_profile p 
                                INNER JOIN #__sdi_resourcetype rt ON p.id=rt.profile_id
                                INNER JOIN #__sdi_class c ON c.id=p.class_id  
                                LEFT OUTER JOIN #__sdi_namespace as ns ON c.namespace_id=ns.id 
                                WHERE rt.id=" . $this->resource->resourcetype_id;
        $this->db->setQuery($query);
        return $this->db->loadObject();
    }

}

?>

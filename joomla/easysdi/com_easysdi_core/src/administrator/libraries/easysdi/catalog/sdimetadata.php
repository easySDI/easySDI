<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/catalog/cswmetadata.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/helpers/easysdi_catalog.php';

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

    //namespaces uri
    const sdi_uri = 'http://www.easysdi.org/2011/sdi';
    const xmlns_uri = 'http://www.w3.org/2000/xmlns/';
    const csw_uri = 'http://www.opengis.net/cat/csw/2.0.2';
    const ogc_uri = 'http://www.opengis.net/ogc';

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
    public function insert(DOMDocument $xml = null) {

        //Get from the metadata structure, the attribute to store the metadata ID
        $query = $this->db->getQuery(true);
        $query->select('a.name as name, ns.prefix as ns');
        $query->select('ns.prefix as ns_prefix, a.isocode as a_isocode');
        $query->select('atns.prefix as atns_prefix, at.isocode as at_isocode');
        $query->from('#__sdi_profile p');
        $query->innerJoin('#__sdi_resourcetype rt on p.id=rt.profile_id ');
        $query->innerJoin('#__sdi_attribute a on a.id=p.metadataidentifier');
        $query->innerJoin('#__sdi_relation rel on rel.attributechild_id=a.id');
        $query->innerJoin('#__sdi_sys_stereotype as at ON at.id=a.stereotype_id');
        $query->leftJoin('#__sdi_namespace ns ON a.namespace_id=ns.id');
        $query->leftJoin('#__sdi_namespace atns ON at.namespace_id=atns.id');
        $query->where('rt.id=' . (int) $this->resource->resourcetype_id);

        $this->db->setQuery($query);
        $attributeIdentifier = $this->db->loadObject();
        $attributeIdentifier->attribute_isocode = $attributeIdentifier->ns_prefix.':'.$attributeIdentifier->a_isocode;
        $attributeIdentifier->type_isocode = $attributeIdentifier->atns_prefix.':'.$attributeIdentifier->at_isocode;

        //Get from the metadata structure the root classe
        $query = $this->db->getQuery(true);
        $query->select($query->concatenate(array('ns.prefix', 'c.isocode'), ':') . ' as isocode, ns.uri, c.isocode as nodeName');
        $query->from('#__sdi_profile p');
        $query->innerJoin('#__sdi_resourcetype rt ON p.id=rt.profile_id');
        $query->innerJoin('#__sdi_class c ON c.id=p.class_id');
        $query->leftJoin('#__sdi_namespace as ns ON c.namespace_id=ns.id');
        $query->where('rt.id=' . (int) $this->resource->resourcetype_id);

        $this->db->setQuery($query);
        $rootclass = $this->db->loadObject();


        //Create metadata XML
        $this->dom = new DOMDocument('1.0', 'utf-8');

        $transaction = $this->dom->createElementNS(self::csw_uri, 'csw:Transaction');
        $transaction->setAttributeNS(self::xmlns_uri, 'xmlns:csw', self::csw_uri);
        $transaction->setAttributeNS(self::xmlns_uri, 'xmlns:ogc', self::ogc_uri);
        $transaction->setAttribute('service', "CSW");
        $transaction->setAttribute('version', "2.0.2");

        $insert = $this->dom->createElementNS(self::csw_uri, 'csw:Insert');

        if (!empty($xml)) {
            $root = $xml->getElementsByTagNameNS($rootclass->uri, $rootclass->nodeName);
            $insert->appendChild($this->dom->importNode($xml->documentElement, true));
        } else {
            $root = $this->dom->createElement($rootclass->isocode);
            $root->setAttributeNS(self::xmlns_uri, 'xmlns:gmd', 'http://www.isotc211.org/2005/gmd');
            $root->setAttributeNS(self::xmlns_uri, 'xmlns:gco', 'http://www.isotc211.org/2005/gco');
            $root->setAttributeNS(self::xmlns_uri, 'xmlns:gml', 'http://www.opengis.net/gml');
            $root->setAttributeNS(self::xmlns_uri, 'xmlns:sdi', 'http://www.easysdi.org/2011/sdi');


            $identifier = $this->dom->createElement($attributeIdentifier->attribute_isocode);
            $identifiertype = $this->dom->createElement($attributeIdentifier->type_isocode, $this->metadata->guid);

            $platform = $this->getPlatformNode($this->dom);

            $identifier->appendChild($identifiertype);
            $root->appendChild($identifier);
            $root->appendChild($platform);

            $insert->appendChild($root);
        }

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
            } else {
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

        $transaction = $this->dom->createElementNS(self::csw_uri, 'csw:Transaction');
        $transaction->setAttributeNS(self::xmlns_uri, 'xmlns:csw', self::csw_uri);
        $transaction->setAttributeNS(self::xmlns_uri, 'xmlns:ogc', self::ogc_uri);
        $transaction->setAttribute('service', "CSW");
        $transaction->setAttribute('version', "2.0.2");

        $delete = $this->dom->createElementNS(self::csw_uri, 'csw:Delete');

        $constraint = $this->dom->createElementNS(self::csw_uri, 'csw:Constraint');
        $constraint->setAttribute('version', '1.0.0');

        $filter = $this->dom->createElementNS(self::ogc_uri, 'ogc:Filter');
        $propertyEqual = $this->dom->createElementNS(self::ogc_uri, 'ogc:PropertyIsLike');
        $propertyEqual->setAttribute('wildCard', '%');
        $propertyEqual->setAttribute('singleChar', '_');
        $propertyEqual->setAttribute('escape', '/');
        $propertyName = $this->dom->createElementNS(self::ogc_uri, 'ogc:PropertyName', JComponentHelper::getParams('com_easysdi_catalog')->get('idogcsearchfield'));
        $literal = $this->dom->createElementNS(self::ogc_uri, 'ogc:Literal', $this->metadata->guid);

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

        if (empty($result)){
            throw new Exception ('CSW catalog doesn\'t answered', null, null);
        }

        $insertDom = new DOMDocument();
        $insertDom->loadXML($result);
        $xpathDelete = new DOMXPath($insertDom);
        $xpathDelete->registerNamespace('csw', 'http://www.opengis.net/cat/csw/2.0.2');
        $deleted = $xpathDelete->query("//csw:totalDeleted")->item(0)->nodeValue;

        if (is_null($deleted)) {
            throw new Exception ('CSW catalog doesn\'t answered', null, null);
        }
        
        if ($deleted <> 1) {
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

        $newdom = $this->wrapUpdateBlock($metadata);

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
                ->where('id = ' . (int) $this->resource->resourcetype_id);
        $this->db->setQuery($query);
        $resourcetype = $this->db->loadResult();

        //Get the organism guid
        $query = $this->db->getQuery(true);
        $query->select('guid')
                ->from('#__sdi_organism')
                ->where('id = ' . (int) $this->resource->organism_id);
        $this->db->setQuery($query);
        $organism = $this->db->loadResult();

        //Get the accessscope value
        $query = $this->db->getQuery(true);
        $query->select('value')
                ->from('#__sdi_sys_accessscope')
                ->where('id = ' . (int) $this->resource->accessscope_id);
        $this->db->setQuery($query);
        $accessscope = $this->db->loadResult();

        //Get the accessscope
        $query = $this->db->getQuery(true);
        $query->select('a.organism_id, a.user_id, a.category_id, o.guid as  o_guid, u.guid as u_guid, c.guid as c_guid')
                ->from('#__sdi_accessscope a')
                ->leftJoin('#__sdi_organism o ON o.id = a.organism_id')
                ->leftJoin('#__sdi_user u ON u.id = a.user_id')
                ->leftJoin('#__sdi_category c ON c.id = a.category_id')
                ->where('entity_guid = ' . $query->quote($this->resource->guid));
        $this->db->setQuery($query);
        $accessscopes = $this->db->loadObjectList();

        // Get the metadatastate
        $query = $this->db->getQuery(true);
        $query->select('ms.value')
                ->from('#__sdi_metadata m')
                ->innerJoin('#__sdi_sys_metadatastate ms on ms.id = m.metadatastate_id')
                ->where('m.id  = ' . (int) $this->metadata->id);
        $this->db->setQuery($query);
        $metadatastate = $this->db->loadResult();

        //Get the infrastructureID
        $infrastructureID = JComponentHelper::getParams('com_easysdi_core')->get('infrastructureID');

        //Platform
        $platform = $dom->createElementNS(self::sdi_uri, 'sdi:platform');
        $platform->setAttribute('guid', $infrastructureID);
        $platform->setAttribute('harvested', 'false');

        //Resource
        $resource = $dom->createElementNS(self::sdi_uri, 'sdi:resource');
        $resource->setAttribute('guid', $this->resource->guid);
        $resource->setAttribute('alias', $this->resource->alias);
        $resource->setAttribute('name', $this->resource->name);
        $resource->setAttribute('type', $resourcetype);
        $resource->setAttribute('organism', $organism);
        $resource->setAttribute('scope', $accessscope);

        //Metadata
        $metadata = $dom->createElementNS(self::sdi_uri, 'sdi:metadata');
        $metadata->setAttribute('guid', $this->metadata->guid);
        $metadata->setAttribute('created', $this->metadata->created);
        if(empty($this->metadata->published)){
            $metadata->setAttribute('published', '0000-00-00 00:00:00');
        }else{
            $metadata->setAttribute('published', $this->metadata->published);
        }
        
        if(empty($this->metadata->endpublished)){
            $metadata->setAttribute('endpublished', '0000-00-00 00:00:00');
        }else{
            $metadata->setAttribute('endpublished', $this->metadata->endpublished);
        }

        $metadata->setAttribute('state', $metadatastate);

        //Diffusion
        $query = $this->db->getQuery(true)
                ->select('id, guid ,pricing_id, hasdownload, hasextraction, accessscope_id')
                ->from('#__sdi_diffusion')
                ->where('version_id = ' . (int) $this->metadata->version_id)
                ->where('state = 1');
        $this->db->setQuery($query);
        $diffusionobj = $this->db->loadObject();
        $diffusion = $dom->createElementNS(self::sdi_uri, 'sdi:diffusion');
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
                ->select('id, maplayer_id, accessscope_id')
                ->from('#__sdi_visualization')
                ->where('version_id = ' . (int) $this->metadata->version_id)
                ->where('state = 1');
        $this->db->setQuery($query);
        $viewobj = $this->db->loadObject();
        $view = $dom->createElementNS(self::sdi_uri, 'sdi:visualization');
        if (!empty($viewobj) && !empty($viewobj->maplayer_id)):
            $view->setAttribute('isViewable', 'true');
        else:
            $view->setAttribute('isViewable', 'false');
        endif;

        //Accessscope
        foreach ($accessscopes as $a) {
            if ($a->organism_id != null):
                if (!isset($organisms))
                    $organisms = $dom->createElementNS(self::sdi_uri, 'sdi:organisms');
                $organism = $dom->createElementNS(self::sdi_uri, 'sdi:organism');
                $organism->setAttribute('guid', $a->o_guid);
                $organisms->appendChild($organism);
            elseif ($a->user_id != null):
                if (!isset($users))
                    $users = $dom->createElementNS(self::sdi_uri, 'sdi:users');
                $user = $dom->createElementNS(self::sdi_uri, 'sdi:user');
                $user->setAttribute('guid', $a->u_guid);
                $users->appendChild($user);
            elseif ($a->category_id != null):
                if (!isset($categories))
                    $categories = $dom->createElementNS(self::sdi_uri, 'sdi:categories');
                $category = $dom->createElementNS(self::sdi_uri, 'sdi:category');
                $category->setAttribute('guid', $a->c_guid);
                $categories->appendChild($category);                
            endif;
        }

        $metadata->appendChild($diffusion);
        $metadata->appendChild($view);
        if (isset($organisms))
            $resource->appendChild($organisms);
        if (isset($users))
            $resource->appendChild($users);
        if (isset($categories))
            $resource->appendChild($categories);        
        $resource->appendChild($metadata);
        $platform->appendChild($resource);

        return $platform;
    }

    protected function getMetadataRootClass() {
        //Get from the metadata structure the root classe
        $query = $this->db->getQuery(true);
        $query->select($query->concatenate(array('ns.prefix', 'c.isocode'), ':') . ' as isocode');
        $query->from('#__sdi_profile p');
        $query->innerJoin('#__sdi_resourcetype rt ON p.id=rt.profile_id');
        $query->innerJoin('#__sdi_class c ON c.id=p.class_id');
        $query->leftJoin('#__sdi_namespace as ns ON c.namespace_id=ns.id');
        $query->where('rt.id=' . $this->resource->resourcetype_id);

        $this->db->setQuery($query);
        return $this->db->loadObject();
    }
    
    /**
     * 
     * Wrap update transaction on xml DOMElement
     * 
     * @param DOMElement $xml
     * @return \DOMDocument
     */
    public function wrapUpdateBlock($xml){
        $newdom = new DOMDocument('1.0', 'utf-8');
        $transaction = $newdom->createElementNS(self::csw_uri, 'csw:Transaction');
        $transaction->setAttributeNS(self::xmlns_uri, 'xmlns:csw', self::csw_uri);
        $transaction->setAttributeNS(self::xmlns_uri, 'xmlns:ogc', self::ogc_uri);
        $transaction->setAttribute('service', "CSW");
        $transaction->setAttribute('version', "2.0.2");
        $update = $newdom->createElementNS(self::csw_uri, 'csw:Update');
        $update->appendChild($newdom->importNode($xml, true));

        $constraint = $newdom->createElementNS(self::csw_uri, 'csw:Constraint');
        $constraint->setAttribute('version', '1.0.0');

        $filter = $newdom->createElementNS(self::ogc_uri, 'ogc:Filter');
        $propertyEqual = $newdom->createElementNS(self::ogc_uri, 'ogc:PropertyIsLike');
        $propertyEqual->setAttribute('wildCard', '%');
        $propertyEqual->setAttribute('singleChar', '_');
        $propertyEqual->setAttribute('escape', '/');
        $propertyName = $newdom->createElementNS(self::ogc_uri, 'ogc:PropertyName', JComponentHelper::getParams('com_easysdi_catalog')->get('idogcsearchfield'));
        $literal = $newdom->createElementNS(self::ogc_uri, 'ogc:Literal', $this->metadata->guid);

        $propertyEqual->appendChild($propertyName);
        $propertyEqual->appendChild($literal);
        $filter->appendChild($propertyEqual);
        $constraint->appendChild($filter);

        $update->appendChild($constraint);
        $transaction->appendChild($update);
        $newdom->appendChild($transaction);
        
        return $newdom;
    }
    
    

}

?>

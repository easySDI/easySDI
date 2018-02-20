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

jimport('joomla.application.component.helper');

require_once JPATH_BASE . '/components/com_easysdi_catalog/controller.php';

class Easysdi_catalogControllerSitemap extends Easysdi_catalogController {
    
    private $db = null;
    
    private $XMLDoc = null;
    
    private $XMLRoot = null;
    
    private $baseLink;
    
    private $priority;
    
    private $extraParameters;
    
    public function __construct() {
        $this->db = JFactory::getDbo();
        
        $this->getBaseLink();
        
        $this->getExtraParameters();
        
        parent::__construct();
    }


    public function generateSitemap(){
        
        // getXMLDocument
        $this->getXMLDocument();
        
        // getAvailableMetadata
        $metadatas = $this->getAvailableMetadata();
        
        if(count($metadatas)){
            foreach($metadatas as $metadata)
                $this->XMLRoot->appendChild($this->getMetadataXMLNode($metadata));
        }
        
        $this->XMLDoc->appendChild($this->XMLRoot);
        $xml = $this->XMLDoc->saveXML();
        
        header('Content-Type: application/xml; charset=utf-8');
        echo $xml;
        exit();
    }
    
    private function getXMLDocument(){
        $XMLDoc = new DOMDocument('1.0', 'utf-8');
	$XMLDoc->formatOutput = true;
        
        $XMLRoot = $XMLDoc->createElement("urlset");
	$XMLRoot->setAttribute('xmlns', "http://www.sitemaps.org/schemas/sitemap/0.9");
	
        
        $this->XMLDoc = $XMLDoc;
        $this->XMLRoot = $XMLRoot;
    }
    
    private function getAvailableMetadata(){
        $resourcestype = JComponentHelper::getParams('com_easysdi_catalog')->get('sitemapresourcestype');
        if(!is_array($resourcestype))
            $resourcestype = array(0);
        
        $query = $this->db->getQuery(true);
        
        $query->select("m.guid, DATE_FORMAT(CASE WHEN m.modified >= m.created THEN m.modified ELSE m.created END,'%Y-%m-%d') as modified")
                ->from('#__sdi_metadata m')
                ->join('LEFT', '#__sdi_version v on v.id=m.version_id')
                ->join('LEFT', '#__sdi_resource r ON r.id=v.resource_id')
                ->where('m.metadatastate_id=3') // metadata must be published
                ->where('r.accessscope_id=1') // metadata must have public access scope
                ->where('r.resourcetype_id IN ('.implode(',',$resourcestype).')') // metadata must be from allowed resources types
                ;

        $this->db->setQuery($query);
        
        return $this->db->loadAssocList();
    }
    
    private function getBaseLink(){
        $this->baseLink = (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off')) ? 'https' : 'http';
        
        $this->baseLink .= '://' . $_SERVER['SERVER_NAME'] . JRoute::_('index.php?option=com_easysdi_catalog&view=catalog');
        
        $this->baseLink .=  '&view=sheet&guid=';
    }
    
    private function getExtraParameters(){
        $this->priority = JComponentHelper::getParams('com_easysdi_catalog')->get('sitemappriority');
        
        $this->extraParameters = JComponentHelper::getParams('com_easysdi_catalog')->get('sitemapextraparameters');
        if($this->extraParameters != '')
            $this->extraParameters = '&'.$this->extraParameters;
    }
    
    private function getMetadataXMLNode($metadata){
        $node = $this->XMLDoc->createElement('url');
        
        $loc = $this->XMLDoc->createElement('loc', htmlentities($this->baseLink . $metadata['guid'] . $this->extraParameters));
        $node->appendChild($loc);
        
        $lastmod = $this->XMLDoc->createElement('lastmod', $metadata['modified']);
        $node->appendChild($lastmod);
        
        $changefreq = $this->XMLDoc->createElement('changefreq', 'always');
        $node->appendChild($changefreq);
        
        $priority = $this->XMLDoc->createElement('priority', $this->priority);
        $node->appendChild($priority);
        
        return $node;
    }
    
}
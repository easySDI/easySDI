<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */


require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/dao/SdiNamespaceDao.php';
require_once JPATH_BASE . '/administrator/components/com_easysdi_core/libraries/easysdi/catalog/CurlUtils.php';

class CswMerge {
    /* import type */

    const REPLACE = 1;
    const MERGE = 2;

    /** @var JDatabaseDriver */
    private $db;

    /** @var DOMDocument */
    private $original;

    /** @var DOMDocument Domdocument to import */
    private $import;

    /** @var SdiNamespaceDao */
    private $nsdao;

    function __construct($original = '', $import = '') {
        $this->db = JFactory::getDbo();
        if (!empty($import)) {
            $this->setImport($import);
        }

        if (!empty($original)) {
            $this->setOriginal($original);
        }

        $this->nsdao = new SdiNamespaceDao();
    }

    /**
     * 
     * @param int $importref_id
     * @param string $fileidentifier
     * @return DOMDocument
     */
    public function mergeImport($importref_id = '', $fileidentifier = '', $xpaths = array()) {
        // Add static xpath for clean sdi node
        $xpath = array('xpath' => '/gmd:MD_Metadata/sdi:platform');
        $xpaths[] = JArrayHelper::toObject($xpath);

        try {
            // Import from a CSW catalog
            if (!empty($importref_id) && !empty($fileidentifier)) {
                $importref = $this->getImportRef($importref_id);
                return $this->mergeImportService($importref, $fileidentifier);
            }
            // Import from an provided document
            elseif (!empty($importref_id)) {
                $importref = $this->getImportRef($importref_id);
                return $this->mergeImportCsw($importref, $xpaths);
            }
            // import from the local catalog
            elseif (!empty($fileidentifier)) {
                return $this->mergeInheriteCsw($fileidentifier, $xpaths);
            }
        } catch (Exception $exc) {
            throw $exc;
        }
    }

    /**
     * Merge from the local catalog
     * 
     * @param string $fileidentifier
     * @return DOMDocument
     */
    private function mergeInheriteCsw($fileidentifier, $xpaths) {
        $catalog_params = JComponentHelper::getParams('com_easysdi_catalog');

        $importrefarray = array();
        $importrefarray['resourceurl'] = $catalog_params->get('catalogurl');
        $importrefarray['resourceusername'] = '';
        $importrefarray['resourcepassword'] = '';
        $importrefarray['importtype_id'] = self::MERGE;

        $importref = JArrayHelper::toObject($importrefarray);

        return $this->mergeImportService($importref, $fileidentifier, $xpaths);
    }

    /**
     * Merge from a catalog
     * 
     * @param type $importref
     * @param string $fileidentifier
     * @return boolean
     */
    private function mergeImportService($importref, $fileidentifier, $xpaths) {
        if ($response = $this->getImportCsw($fileidentifier, $importref->resourceurl, $importref->resourceusername, $importref->resourcepassword)) {
            $this->import = $response;
        } else {
            return FALSE;
        }

        $this->preserveFileidentifier($this->import, '', $this->original);

        $this->transformXml($importref);

        if (!empty($xpaths)) {
            $this->removeXpath($xpaths);
        }

        return $this->switchOnImportType($importref);
    }

    /**
     * Merge from a provided XML document
     * 
     * @param type $importref
     * @return type
     * @throws Exception
     */
    private function mergeImportCsw($importref, $xpath) {
        $this->transformXml($importref);

        try {
            $this->preserveFileidentifier($this->import, '', $this->original);
        } catch (Exception $exc) {
            throw $exc;
        }



        return $this->switchOnImportType($importref);
    }

    /**
     * Switch on import type and return the replaced or the merged DOMDocument
     * 
     * @param type $importref
     * @return DOMDocument
     */
    private function switchOnImportType($importref) {
        switch ($importref->importtype_id) {
            case self::REPLACE:
                return $this->replace();
            case self::MERGE:
                $this->merge();
                return $this->original;
        }
    }

    /**
     * 
     * @param DOMDocument $doc
     * @return DOMDocument
     */
    private function removeGetRecordBy(DOMDocument $doc) {
        $elements = $doc->getElementsByTagName('GetRecordByIdResponse');

        if ($elements->length > 0) {

            $dom = new DOMDocument('1.0', 'UTF-8');
            $children = $elements->item(0)->childNodes;
            foreach ($children as $child) {
                if ($child->nodeType == XML_ELEMENT_NODE) {
                    $xmlContent = $dom->importNode($child, true);
                    $dom->appendChild($xmlContent);
                    break;
                }
            }
            return $dom;
        } else {
            return $doc;
        }
    }

    /**
     * Add GetRecordById to import file if necessary
     */
    public function addGetRecordById($toWrap) {
        $firstChildName = $this->import->firstChild->nodeName;

        if ($firstChildName != 'csw:GetRecordByIdResponse') {
            $dom = new DOMDocument('1.0', 'utf-8');
            $dom->appendChild($dom->createElementNS('http://www.opengis.net/cat/csw/2.0.2', 'csw:GetRecordByIdResponse'));

            if ($imported = $dom->importNode($toWrap->firstChild, TRUE)) {
                $dom->firstChild->appendChild($imported);

                return $dom;
            }
        }
    }

    /**
     * Transform Xml if necessary
     * 
     * @param type $importref
     */
    private function transformXml($importref) {
        /* Transform the external xml if necessary. */
        if (!empty($importref->xsl4ext)) {
            if ($xmltransform = $this->xslProceed($this->import, $importref->xsl4ext)) {
                $this->import = $xmltransform;
            }
        }

        /* Transform the sdi xml if necessary. */
        if (!empty($importref->xsl4sdi)) {
            if ($xmltransform = $this->xslProceed($this->original, $importref->xsl4sdi)) {
                $this->original = $xmltransform;
            }
        }
    }

    /**
     * Remove xpaths from imported DOMDocument
     * 
     * @param string[] $xpaths
     */
    private function removeXpath($xpaths) {
        $domXpathImport = new DOMXPath($this->import);

        foreach ($this->nsdao->getAll() as $ns) {
            $domXpathImport->registerNamespace($ns->prefix, $ns->uri);
        }

        foreach ($xpaths as $xpath) {
            $elementsToDelete = $domXpathImport->query($xpath->xpath);

            foreach ($elementsToDelete as $elementToDelete) {
                $parent = $elementToDelete->parentNode;
                $parent->removeChild($elementToDelete);
            }
        }
    }

    /**
     * Preserve fileidentifier during import process
     * 
     * @param string $fileIdentifier Original fileidentifier
     * @param DOMDocument $original
     * @param DOMDocument $import
     * @throws Exception
     */
    public function preserveFileidentifier(DOMDocument &$import, $fileIdentifier = '', DOMDocument &$original = null) {
        $fileidentifierImportNode = $import->getElementsByTagNameNS('http://www.isotc211.org/2005/gmd', 'fileIdentifier')->item(0);

        if (!empty($fileIdentifier)) {
            $fileidentifierImportedNode = $import->createElementNS('http://www.isotc211.org/2005/gmd', 'gmd:fileIdentifier');
            $gco_character = $import->createElementNS('http://www.isotc211.org/2005/gco', 'gco:CharacterString', $fileIdentifier);
            $fileidentifierImportedNode->appendChild($gco_character);

            if (!empty($fileidentifierImportNode)) {
                $fileidentifierImportNode->parentNode->replaceChild($fileidentifierImportedNode, $fileidentifierImportNode);
            } else {
                $mdMetadata = $import->getElementsByTagNameNS('http://www.isotc211.org/2005/gmd', 'MD_Metadata')->item(0);
                $mdMetadata->appendChild($fileidentifierImportedNode);
            }
        } elseif (!empty($original)) {
            $fileidentifierOriginalNode = $original->getElementsByTagNameNS('http://www.isotc211.org/2005/gmd', 'fileIdentifier')->item(0);

            if (empty($fileidentifierOriginalNode)) {
                throw new Exception(JText::_('COM_EASYSDI_CATALOG_IMPORT_METADATA_FILEIDENTIFIER_EMPTY'));
            }

            $fileidentifierImportNode = $import->getElementsByTagNameNS('http://www.isotc211.org/2005/gmd', 'fileIdentifier')->item(0);

            $fileidentifierImportedNode = $import->importNode($fileidentifierOriginalNode, true);

            if (empty($fileidentifierImportNode)) {
                $import->firstChild->appendChild($fileidentifierImportedNode);
            } else {
                $import->firstChild->replaceChild($fileidentifierImportedNode, $fileidentifierImportNode);
            }
        }
    }

    /**
     * Replace original DOMDocument by Imported DOMDocument
     * 
     * @return DOMElement
     */
    private function replace() {
        return $this->import;
    }

    /**
     * Merge import to original
     */
    private function merge() {

        $domXpathImport = new DOMXPath($this->import);
        $domXpathOriginal = new DOMXPath($this->original);

        foreach ($this->nsdao->getAll() as $ns) {
            $domXpathImport->registerNamespace($ns->prefix, $ns->uri);
            $domXpathOriginal->registerNamespace($ns->prefix, $ns->uri);
        }

        /* @var $nodeToImport DOMElement */
        foreach ($domXpathImport->query('descendant::*') as $nodeToImport) {
           
            if (!$this->hasChild($nodeToImport)) {
                $nodesToImport = array();
                do {
                    $nodesToImport[] = $nodeToImport;

                    $nodeToImport = $nodeToImport->parentNode;
                } while ($domXpathOriginal->query($nodeToImport->getNodePath())->length == 0);

                $parent = $domXpathOriginal->query($nodeToImport->getNodePath())->item(0);
                $nodesToImport = array_reverse($nodesToImport);
                for ($i = 0; $i < count($nodesToImport); $i++) {
                    if($i==count($nodesToImport)-1){
                        $deep=true;
                    }else{
                        $deep=false;
                    }
                    
                    $importedNode = $this->original->importNode($nodesToImport[$i],$deep);
                    $parent->appendChild($importedNode);
                    $parent = $importedNode;
                }
               
            }
        }
        $this->original->normalizeDocument();
        $breakpoint = true;
    }

    /**
     * Import node at the fist share ancestor
     * 
     * @param DOMElement $node
     */
    private function getFirstShareAncestor(DOMElement $node) {
        $domXpathImport = new DOMXPath($this->import);
        $domXpathOriginal = new DOMXPath($this->original);

        foreach ($this->nsdao->getAll() as $ns) {
            $domXpathImport->registerNamespace($ns->prefix, $ns->uri);
            $domXpathOriginal->registerNamespace($ns->prefix, $ns->uri);
        }

        $ancestor = $domXpathImport->query('ancestor::*', $node);

        /* @var $child DOMElement */
        $child = NULL;
        for ($i = $ancestor->length - 1; $i >= 0; --$i) {
            $importNode = $ancestor->item($i);

            if ($originalNode = $domXpathOriginal->query($importNode->getNodePath())->item(0)) {
                $importedNode = $this->original->importNode($child, true);
                $originalNode->appendChild($importedNode);
                break;
            }

            $child = $importNode;
        }
    }

    /**
     * Check if element has child
     * 
     * @param DOMElement $node
     * @return boolean
     */
    private function hasChild(DOMElement $node) {
        if (!$node->hasChildNodes()) {
            return FALSE;
        } else {
            /* @var $child DOMElement */
            foreach ($node->getElementsByTagName('*') as $child) {
                if ($child->nodeType == XML_ELEMENT_NODE) {
                    return TRUE;
                }
            }
        }

        return FALSE;
    }

    /**
     * Tranform xml DOMDocument with xsl
     * 
     * @param DOMDocument $xml
     * @param string $xsl
     * @return DOMDocument of false if fail
     */
    private function xslProceed(DOMDocument $xml, $xsl) {
        $xsldoc = new DOMDocument('1.0', 'utf-8');
        if (!$xsldoc->load($xsl)) {
            return FALSE;
        }

        $proc = new XSLTProcessor();
        $proc->importStylesheet($xsldoc);

        if ($transforXml = $proc->transformToDoc($xml)) {
            return $transforXml;
        } else {
            return FALSE;
        }
    }

    /**
     * Retrives metadadata from service
     * 
     * @param string $fileidentifier
     * @param string $resourceUrl
     * @param string $username
     * @param string $password
     * @return DOMDocument or false if not found
     */
    private function getImportCsw($fileidentifier, $resourceUrl = '', $username = '', $password = '') {
        $url = array();
        $url[] = $resourceUrl;
        $url[] = '?request=GetRecordById&service=CSW&version=2.0.2&elementSetName=full&outputschema=csw:IsoRecord&content=CORE&';
        $url[] = 'id=' . $fileidentifier;

        $response = CurlUtils::CURLRequest(CurlUtils::GET, implode($url), $username, $password);

        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->loadXML($response);

        if ($dom->firstChild->hasChildNodes()) {
            return $this->removeGetRecordBy($dom);
        } else {
            return FALSE;
        }
    }

    /**
     * Retrieves import configuration via the id.
     * 
     * @param int $importref_id
     * @return stdClass importref
     */
    private function getImportRef($importref_id) {
        $query = $this->db->getQuery(true);
        $query->select('*');
        $query->from('#__sdi_importref AS ir');
        $query->leftJoin('#__sdi_physicalservice ps on ps.id = ir.cswservice_id');
        $query->where('ir.id = ' . (int) $importref_id);

        $this->db->setQuery($query);
        $importref = $this->db->loadObject();

        return $importref;
    }

    /**
     * 
     * @return DOMDocument
     */
    public function getOriginal() {
        return $this->original;
    }

    /**
     * 
     * @return DOMDocument
     */
    public function getImport() {
        return $this->import;
    }

    public function setOriginal(DOMDocument $original) {
        $this->original = $this->removeGetRecordBy($original);
    }

    public function setImport(DOMDocument $import) {
        $this->import = $this->removeGetRecordBy($import);
    }

}

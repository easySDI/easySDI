<?php

require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/dao/SdiNamespaceDao.php';
require_once JPATH_BASE . '/administrator/components/com_easysdi_core/libraries/easysdi/catalog/CurlUtils.php';

/**
 * Description of CswMerge
 *
 * @author Marc Battaglia <marc.battaglia@depth.ch>
 */
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

    function __construct($original, $import = '') {
        $this->db = JFactory::getDbo();
        if (!empty($import)) {
            $this->import = $import;
        }
        $this->original = $original;
        $this->nsdao = new SdiNamespaceDao();
    }

    /**
     * 
     * @param int $importref_id
     * @param string $fileidentifier
     * @return DOMDocument or false if fail
     */
    public function mergeImport($importref_id, $fileidentifier = '') {
        $importref = $this->getImportRef($importref_id);

        /* Retrieves metadata from the CSW server. */
        if (!empty($fileidentifier)) {
            if ($response = $this->getImportCsw($importref->resourceurl, $importref->resourceusername, $importref->resourcepassword, $fileidentifier)) {
                $this->import = $response;
            }  else {
                return FALSE;
            }
        }

        $firstChildName = $this->import->firstChild->nodeName;

        /* Add tag GetRecordById Response if necessary. */
        if ($firstChildName != 'csw:GetRecordByIdResponse') {
            $dom = new DOMDocument('1.0', 'utf-8');
            $dom->appendChild($dom->createElementNS('http://www.opengis.net/cat/csw/2.0.2', 'csw:GetRecordByIdResponse'));

            if ($imported = $dom->importNode($this->import->firstChild, TRUE)) {
                $dom->firstChild->appendChild($imported);

                $this->import = $dom;
            }
        }

        /* Transform the external xml if necessary. */
        if (!empty($importref->xsl4ext)) {
            if ($xmltranform = $this->xslProceed($this->import, $importref->xsl4ext)) {
                $this->import = $xmltranform;
            }
        }

        /* Transform the sdi xml if necessary. */
        if (!empty($importref->xsl4sdi)) {
            if ($xmltrasform = $this->xslProceed($this->original, $importref->xsl4sdi)) {
                $this->original = $xmltranform;
            }
        }

        /* Switch on import type */
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

        /* @var $importNode DOMNode */
        foreach ($domXpathImport->query('descendant::*', $this->import->firstChild) as $importNode) {
            if (!$this->hasChild($importNode)) {
                /* @var $originalNode DOMNode */
                if ($originalNode = $domXpathOriginal->query($importNode->getNodePath())->item(0)) {
                    $importedNode = $this->original->importNode($importNode, true);
                    $originalNode->parentNode->replaceChild($importedNode, $originalNode);
                } else {
                    $this->getFirstShareAncestor($importNode);
                }
            }
        }
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
     * @param string $resourceUrl
     * @param string $username
     * @param string $password
     * @param string $fileidentifier
     * @return DOMDocument or false if not found
     */
    private function getImportCsw($resourceUrl, $username, $password, $fileidentifier) {
        $url = array();
        $url[] = $resourceUrl;
        $url[] = '?request=GetRecordById&service=CSW&version=2.0.2&elementSetName=full&outputschema=csw:IsoRecord&content=CORE&';
        $url[] = 'id=' . $fileidentifier;

        $response = CurlUtils::CURLRequest(CurlUtils::GET, implode($url), $username, $password);

        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->loadXML($response);

        if ($dom->firstChild->hasChildNodes()) {
            return $dom;
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
        $query->where('ir.id = ' . $importref_id);

        $this->db->setQuery($query);
        $importref = $this->db->loadObject();

        return $importref;
    }

}

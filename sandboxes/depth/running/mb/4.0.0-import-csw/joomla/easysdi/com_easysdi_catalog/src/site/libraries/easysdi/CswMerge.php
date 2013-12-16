<?php

require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/dao/SdiNamespaceDao.php';

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

    function __construct($original, $import) {
        $this->db = JFactory::getDbo();
        $this->import = $import;
        $this->original = $original;
        $this->nsdao = new SdiNamespaceDao();
    }

    /**
     * @return DOMDocument Description
     */
    public function mergeImport($importref_id) {
        $importref = $this->getImportRef($importref_id);
        $firstChildName = $this->import->firstChild->nodeName;

        if ($firstChildName != 'csw:GetRecordByIdResponse') {
            $dom = new DOMDocument('1.0', 'utf-8');
            $dom->appendChild($dom->createElementNS('http://www.opengis.net/cat/csw/2.0.2', 'csw:GetRecordByIdResponse'));

            if ($imported = $dom->importNode($this->import->firstChild, TRUE)) {
                $dom->firstChild->appendChild($imported);

                $this->import = $dom;
            }
        }

        if (!empty($importref->xsl4ext)) {
            if ($xmltranform = $this->xslProceed($this->import, $importref->xsl4ext)) {
                $this->import = $xmltranform;
            }
        }

        if (!empty($importref->xsl4sdi)) {
            if ($xmltrasform = $this->xslProceed($this->original, $importref->xsl4sdi)) {
                $this->original = $xmltranform;
            }
        }

        switch ($importref->importtype_id) {
            case self::REPLACE:
                return $this->replace();
            case self::MERGE:
                $this->merge();
                return $this->original;
        }
    }

    private function replace() {
        return $this->import;
    }

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

        $this->original->formatOutput = true;
        $xml = $this->original->saveXML();
    }

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

    private function getImportRef($importref_id) {
        $query = $this->db->getQuery(true);
        $query->select('*');
        $query->from('#__sdi_importref AS i');
        $query->where('i.id = ' . $importref_id);

        $this->db->setQuery($query);
        $importref = $this->db->loadObject();

        return $importref;
    }

}

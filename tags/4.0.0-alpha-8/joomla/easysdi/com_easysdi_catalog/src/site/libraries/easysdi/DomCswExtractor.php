<?php

require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/dao/SdiLanguageDao.php';

/**
 * Description of DomCswExtractor
 *
 * @author Marc Battaglia  <marc.battaglia@depth.ch>
 */
class DomCswExtractor {

    /**
     *
     * @var DOMDocument 
     */
    private $csw;

    /**
     *
     * @var JDatabaseDriver
     */
    private $db = null;

    /**
     *
     * @var SdiLanguageDao 
     */
    private $ldao;

    public function __construct(DOMDocument $csw = null) {
        $this->csw = $csw;
        $this->db = JFactory::getDbo();
        $this->ldao = new SdiLanguageDao();
    }

    /**
     * 
     * Get the value of an attribute
     * 
     * @param SdiRelation $rel
     * @param int $index
     * 
     * @return string Value of attribute
     */
    public function getValue(SdiRelation $rel, $index) {

        switch ($rel->getAttribut_child()->getStereotype()->value) {
            case 'locale':
                return $this->getLocaleValue($rel, $index);
                break;
            case 'list':
                return $this->getListValue($rel, $index);
                break;
            case 'localechoice':
                return $this->getLocaleChoiceValue($rel, $index);
                break;
            case 'gemet':
                return '';
                break;
            case 'geographicextent':
                return '';
                break;
            case 'resource':
                return $this->getResourceValue($rel, $index);
                break;

            default:
                return $this->getIsocodeValue($rel, $index);
                break;
        }
    }

    /**
     * 
     * @param SdiRelation $parent_rel
     * @param SdiRelation $rel
     * @param int $index
     * @return string Text value for Attribute
     */
    private function getIsocodeValue(SdiRelation $rel, $index) {
        $domXpath = new DOMXPath($this->csw);

        $xpath = $this->getXpath($rel);

        foreach ($xpath['namespaces'] as $namespace) {
            $domXpath->registerNamespace($namespace->prefix, $namespace->uri);
        }

        $nodeList = $domXpath->query($xpath['text']);

        if ($nodeList) {
            $node = $nodeList->item($index);
            if ($node) {
                $value = $node->nodeValue;
                return $value;
            }
        }

        return '';
    }

    /**
     * 
     * @param SdiRelation $parent_rel
     * @param SdiRelation $rel
     * @param int $index
     * @return string
     */
    private function getListValue(SdiRelation $rel, $index) {
        $domXpath = new DOMXPath($this->csw);

        $xpath = $this->getXpath($rel, $rel->getAttribut_child()->getListeNamespace()->prefix . ':' . $rel->getAttribut_child()->type_iso . '/@codeListValue');

        foreach ($xpath['namespaces'] as $namespace) {
            $domXpath->registerNamespace($namespace->prefix, $namespace->uri);
        }

        $nodeList = $domXpath->query($xpath['text']);

        if ($nodeList) {
            $node = $nodeList->item($index);
            if ($node) {
                $value = $node->nodeValue;
                return $value;
            }
        }
    }

    private function getLocaleValue($rel, $index) {
        $domXpath = new DOMXPath($this->csw);

        $xpath = $this->getXpath($rel, 'gco:CharacterString');
        $xpathLocalized = $this->getXpath($rel, 'gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString');

        foreach ($xpath['namespaces'] as $namespace) {
            $domXpath->registerNamespace($namespace->prefix, $namespace->uri);
        }
        $domXpath->registerNamespace('gco', 'http://www.isotc211.org/2005/gco');
        $domXpath->registerNamespace('gmd', 'http://www.isotc211.org/2005/gmd');

        $value = array();

        $node = $domXpath->query($xpath['text'])->item($index);
        if (isset($node)) {
            $value[$this->getDefaultLanguage()->iso3166] = $node->nodeValue;

            foreach ($domXpath->query($xpathLocalized['text']) as $nodeLocalized) {
                $value[str_replace('#', '', $nodeLocalized->getAttribute('locale'))] = $nodeLocalized->nodeValue;
            }
        }

        return $value;
    }

    private function getLocaleChoiceValue($rel, $index) {
        $user = JFactory::getUser();
        $domXpath = new DOMXPath($this->csw);

        $xpath = $this->getXpath($rel, "gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString[@locale = '#" . $this->ldao->getByCode($user->getParam('language'))->iso3166 . "']");

        foreach ($xpath['namespaces'] as $namespace) {
            $domXpath->registerNamespace($namespace->prefix, $namespace->uri);
        }

        $value = '';

        foreach ($domXpath->query($xpath['text']) as $node) {
            $value = $node->nodeValue;
        }

        return $value;
    }

    private function getResourceValue(SdiRelation $rel, $index) {
        $domXpath = new DOMXPath($this->csw);

        $xpath = $this->getXpath($rel);

        foreach ($xpath['namespaces'] as $namespace) {
            $domXpath->registerNamespace($namespace->prefix, $namespace->uri);
        }
        $ns = new SdiNamespace(1000, 'xlink', 'http://www.w3.org/1999/xlink');

        $domXpath->registerNamespace($ns->prefix, $ns->uri);

        $node = $domXpath->query($xpath['text'])->item($index);

        preg_match('/[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}/', $node->getAttributeNodeNS($ns->uri, 'href')->value, $resource_id);

        $query = $this->db->getQuery(true);
        $query->select('r.name');
        $query->from('#__sdi_resource AS r');
        $query->where('r.guid = \'' . $resource_id[0] . '\'');


        $this->db->setQuery($query);
        $result = $this->db->loadObject();

        if (isset($result)) {
            return $result->name;
        } else {
            return '';
        }
    }

    /**
     * @param string $rel_name
     * @return int Number of relation of the type $rel_name
     */
    public function getCountRelation(SdiRelation $rel) {
        $domXpath = new DOMXPath($this->csw);

        $xpath = $this->getXpath($rel);

        foreach ($xpath['namespaces'] as $namespace) {
            $domXpath->registerNamespace($namespace->prefix, $namespace->uri);
        }

        $result = $domXpath->query($xpath['text']);

        return $result->length;
    }

    /**
     * Returns the default language from the XML response, 
     * A change prior to production.
     * 
     * @return string
     */
    private function getDefaultLanguage() {
        $xpath = new DOMXPath($this->csw);
        $xpath->registerNamespace('gmd', 'http://www.isotc211.org/2005/gmd');
        $xpath->registerNamespace('gco', 'http://www.isotc211.org/2005/gco');

        $query = '//gmd:MD_Metadata/gmd:language/gco:CharacterString';

        $node = $xpath->query($query);

        $language = $this->ldao->getByIso639($node->item(0)->nodeValue);

        return $language;
    }

    /**
     * Built the string XPath of an element to retrieve the value.
     * 
     * @param SdiRelation $rel Relation
     * @param string $stereotype A string to add to the XPath for a specific stereotype. Ex. gco:CharacterString
     * @param boolean $count If this call is for counting the number of relationship, set to true.
     * @return type array 
     */
    private function getXpath(SdiRelation $rel, $stereotype = null) {
        $namespaces = array();
        $xpath = '/';
        foreach ($rel->getXpath() as $path) {
            switch ($path->childtype_id) {
                case SdiRelation::$CLASS:
                    if (!$path->getClass_child()->isRoot) {
                        $namespaces[] = $path->getNamespace();
                        $xpath .= '/' . $path->getNamespace()->prefix . ':' . $path->name;
                    }
                    $namespaces[] = $path->getClass_child()->getNamespace();
                    $xpath .= '/' . $path->getClass_child()->getNamespace()->prefix . ':' . $path->getClass_child()->name;
                    break;
                case SdiRelation::$ATTRIBUT:
                    $namespaces[] = $path->getAttribut_child()->getNamespace();
                    $xpath .= '/' . $path->getAttribut_child()->getNamespace()->prefix . ':' . $path->getAttribut_child()->name;
                    if (!isset($stereotype)) {
                        $namespaces[] = $path->getAttribut_child()->getStereotype()->getNamespace();
                        $xpath .= '/' . $path->getAttribut_child()->getStereotype()->getNamespace()->prefix . ':' . $path->getAttribut_child()->getStereotype()->isocode;
                    } else {
                        $xpath .= '/' . $stereotype;
                    }
                    break;
                case SdiRelation::$RELATIONTYPE:

                    $namespaces[] = $path->getNamespace();
                    $xpath .= '/' . $path->getNamespace()->prefix . ':' . $path->name;


                    break;
                default:
                    break;
            }
        }


        return array('text' => $xpath, 'namespaces' => $namespaces);
    }

    /**
     * 
     * @return string SerializedXpath
     */
    public function getSerializedXpath(SdiRelation $rel, SdiRelation $lastRel = null) {
        $xpath = '';

        foreach ($rel->getXpath() as $path) {
            $xpath .= $path->id . '-' . $path->getIndex() . '_';
            switch ($path->childtype_id) {
                case SdiRelation::$CLASS:
                    $xpath .= $path->getClass_child()->id.'-'.$path->getIndex().'_';
                    break;
                case SdiRelation::$ATTRIBUT:
                    $xpath .= $path->getAttribut_child()->id.'-'.$path->getIndex().'_';
                    break;
                default:
                    break;
            }
        }

        if (isset($lastRel)) {
            $xpath .= $lastRel->id . '-' . $lastRel->getIndex() . '_';
        }

        return substr($xpath, 0, -1);
    }

}

?>

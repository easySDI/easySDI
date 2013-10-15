<?php

require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/dao/SdiLanguageDao.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/dao/SdiNamespaceDao.php';

/**
 * Description of DomCswCreator
 *
 * @author Marc Battaglia <marc.battaglia@depth.ch>
 */
class DomCswCreator {

    /**
     *
     * @var int 
     */
    private $id;
    /**
     *
     * @var string 
     */
    private $guid;
    /**
     *
     * @var SdiRelation[] 
     */
    private $relations;

    /**
     * @var DOMDocument
     */
    private $dom;

    /**
     *
     * @var DOMElement[] 
     */
    private $domElements;

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

    /**
     *
     * @var SdiNamespaceDao 
     */
    private $nsdao;

    function __construct($relations, $id, $guid) {
        $this->id = $id;
        $this->guid = $guid;
        $this->relations = $relations;
        $this->db = JFactory::getDbo();
        $this->ldao = new SdiLanguageDao();
        $this->nsdao = new SdiNamespaceDao();
        $this->dom = new DOMDocument('1.0', 'utf-8');
    }

    /**
     * 
     * @return string
     */
    public function getCsw() {

        $this->getDomElements();
        $root = current($this->domElements);

        foreach ($this->nsdao->getAll() as $ns) {
            $root->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:' . $ns->prefix, $ns->uri);
        }
        
        $root->appendChild($this->getSdiHeader());

        foreach ($this->getHeader() as $header) {
            $root->appendChild($header);
        }

        foreach ($this->domElements as $key => $element) {
            $reverseIndex = 0;
            if (array_key_exists($key, $this->relations)) {
                $rel = $this->relations[$key];
                switch ($rel->childtype_id) {
                    case SdiRelation::$RELATIONTYPE:
                    case SdiRelation::$CLASS:
                        $reverseIndex = 1;
                        break;
                    case SdiRelation::$ATTRIBUT:
                        $reverseIndex = 2;
                        break;
                }
            } else {
                $reverseIndex = 1;
            }

            if (array_key_exists($this->subXpath($key, $reverseIndex), $this->domElements)) {
                $parent = $this->domElements[$this->subXpath($key, $reverseIndex)];

                switch ($rel->childtype_id) {
                    case SdiRelation::$RELATIONTYPE:
                    case SdiRelation::$CLASS:
                        $parent->appendChild($element);
                        break;
                    case SdiRelation::$ATTRIBUT:
                        $parent->appendChild($this->setValue($element, $rel));
                        break;
                    default:
                        break;
                }
            }
        }

        $constraint = $this->dom->createElement('csw:Constraint');
        $constraint->setAttribute('version', '1.0.0');
        
        $filter = $this->dom->createElement('Filter');
        $filter->setAttribute('xmlns', 'http://www.opengis.net/ogc');
        $filter->setAttribute('xmlns:gml', 'http://www.opengis.net/gml');
        $propertyIsLike = $this->dom->createElement('PropertyIsLike');
        $propertyIsLike->setAttribute('wildCard', '%');
        $propertyIsLike->setAttribute('singleChar', '_');
        $propertyIsLike->setAttribute('escapeChar', '\\');
        
        $propertyName = $this->dom->createElement('PropertyName', 'apiso:identifier');
        $literal = $this->dom->createElement('Literal', $this->guid);
        
        $propertyIsLike->appendChild($propertyName);
        $propertyIsLike->appendChild($literal);
        
        $filter->appendChild($propertyIsLike);
        $constraint->appendChild($filter);
        
        
        $transaction = $this->dom->createElementNS('http://www.opengis.net/cat/csw/2.0.2', 'csw:Transaction');
        $transaction->setAttribute('service', 'CSW');
        $transaction->setAttribute('version', '2.0.2');
                
        $update = $this->dom->createElement('csw:Update');
        $update->appendChild($root);
        $update->appendChild($constraint);
        $transaction->appendChild($update);
        
        $this->dom->appendChild($transaction);

        $this->dom->formatOutput = true;
        $xml = $this->dom->saveXML();

        return $xml;
    }

    private function getDomElements() {

        foreach ($this->relations as $key => $rel) {

            switch ($rel->childtype_id) {
                case SdiRelation::$CLASS:
                    $elementClass = $this->dom->createElement($rel->getClass_child()->getNamespace()->prefix . ':' . $rel->getClass_child()->name);
                    //$elementClass->setAttribute('id', $key);
                    if (!$rel->getClass_child()->isRoot) {
                        $elementRelation = $this->dom->createElement($rel->getNamespace()->prefix . ':' . $rel->name);
                        //$elementRelation->setAttribute('id', $this->subXpath($key, 1));

                        $this->domElements[$this->subXpath($key, 1)] = $elementRelation;
                        $this->domElements[$key] = $elementClass;
                    } else {
                        $this->domElements[$key] = $elementClass;
                    }

                    break;

                case SdiRelation::$ATTRIBUT:
                    if ($rel->getAttribut_child()->getStereotype()->value != 'resource') {
                        $element = $this->dom->createElement($rel->getAttribut_child()->getNamespace()->prefix . ':' . $rel->getAttribut_child()->name);
                        //$element->setAttribute('id', $key);
                        $this->domElements[$key] = $element;
                    }
                    break;

                case SdiRelation::$RELATIONTYPE:
                    $element = $this->dom->createElement($rel->getNamespace()->prefix . ':' . $rel->name);
                    $element->setAttribute('xlink:show', 'embed');
                    $element->setAttribute('xlink:actuate', 'onLoad');
                    $element->setAttribute('xlink:type', 'simple');
                    $element->setAttribute('xlink:href', $this->getHref($rel));

                    //$element->setAttribute('id', $key);
                    $this->domElements[$key] = $element;
                    break;
            }
        }
    }

    /**
     * 
     * @return DOMDocument
     */
    private function getSdiHeader() {

        /**
         * @todo Vérifier le nom du paramètre à remonter du core
         */
        $platformGuid = JComponentHelper::getParams('com_easysdi_core')->get('infrastructureID');

        $query = $this->db->getQuery(true);
        $query->select('v.`name` as md_lastVersion, m.guid as md_guid, m.created as md_created, m.published as md_published, ms.`value` as ms_value');
        $query->select('r.id as r_id, r.guid as r_guid, r.`alias` as r_alias, r.`name` as r_name');
        $query->select('rt.`alias` as rt_alias');
        $query->select('o.name as o_name');
        $query->from('#__sdi_metadata as m');
        $query->innerJoin('#__sdi_sys_metadatastate as ms ON ms.id = m.metadatastate_id');
        $query->innerJoin('#__sdi_version as v ON v.id = m.version_id');
        $query->innerJoin('#__sdi_resource as r ON r.id = v.resource_id');
        $query->innerJoin('#__sdi_organism as o ON o.id = r.organism_id');
        $query->innerJoin('#__sdi_resourcetype as rt ON rt.id = r.resourcetype_id');
        $query->where('m.id=' . $this->id);


        $this->db->setQuery($query);
        $result = $this->db->loadObject();

        $query = $this->db->getQuery(true);
        $query->select('o.guid, o.`name`');
        $query->from('#__sdi_accessscope as ac');
        $query->innerJoin('#__sdi_organism o ON o.id = ac.organism_id');
        $query->where('ac.entity_guid=\'' . $result->r_guid.'\'');
        $this->db->setQuery($query);
        $resultOrganisms = $this->db->loadObjectList();

        $query = $this->db->getQuery(true);
        $query->select('u.guid, ju.`name`');
        $query->from('#__sdi_accessscope as ac');
        $query->innerJoin('#__sdi_user as u ON u.id = ac.user_id');
        $query->innerJoin('jos_users as ju ON ju.id = u.user_id');
        $query->where('ac.entity_guid=\'' . $result->r_guid.'\'');
        $this->db->setQuery($query);
        $resultUsers = $this->db->loadObjectList();

        $platform = $this->dom->createElement('sdi:platform');
        $platform->setAttribute('guid', $platformGuid);
        $platform->setAttribute('harvested', 'false');

        $resource = $this->dom->createElement('sdi:resource');
        $resource->setAttribute('guid', $result->r_guid);
        $resource->setAttribute('alias', $result->r_alias);
        $resource->setAttribute('name', $result->r_name);
        $resource->setAttribute('type', $result->rt_alias);
        $resource->setAttribute('organism', $result->o_name);
        $resource->setAttribute('scope', '');

        $metadata = $this->dom->createElement('sdi:metadata');
        $metadata->setAttribute('lastVersion', $result->md_lastVersion);
        $metadata->setAttribute('guid', $result->md_guid);
        $metadata->setAttribute('created', $result->md_created);
        $metadata->setAttribute('published', $result->md_published);
        $metadata->setAttribute('state', $result->ms_value);

        $organisms = $this->dom->createElement('sdi:organisms');
        foreach ($resultOrganisms as $o) {
            $organism = $this->dom->createElement('sdi:organism');
            $organism->setAttribute('guid', $o->guid);
            $organism->setAttribute('alias', $o->name);
            $organisms->appendChild($organism);
        }

        $users = $this->dom->createElement('sdi:users');
        foreach ($resultUsers as $u) {
            $user = $this->dom->createElement('sdi:user');
            $user->setAttribute('guid', $u->guid);
            $user->setAttribute('alias', $u->name);
            $users->appendChild($user);
        }
        
        $resource->appendChild($organisms);
        $resource->appendChild($users);
        $resource->appendChild($metadata);
        $platform->appendChild($resource);
        
        return $platform;
    }

    /**
     * 
     * @param string $language
     * @param string $encoding
     * @return DOMElement[] 
     * 
     */
    private function getHeader($default = 'deu', $encoding = 'utf8') {
        $headers = array();

        $language = $this->dom->createElement('gmd:language');
        $characterString = $this->dom->createElement('gco:CharacterString', $default);
        $language->appendChild($characterString);
        $headers[] = $language;

        $characterSet = $this->dom->createElement('gmd:characterSet');
        $characterSetCode = $this->dom->createElement('gmd:MD_CharacterSetCode');
        $characterSetCode->setAttribute('codeListValue', $encoding);
        $characterSetCode->setAttribute('codeList', 'http://www.isotc211.org/2005/resources/codeList.xml#MD_CharacterSetCode');
        $characterSet->appendChild($characterSetCode);

        $headers[] = $characterSet;

        $locale = $this->dom->createElement('gmd:locale');
        $characterEncoding = $this->dom->createElement('gmd:characterEncoding');
        $characterEncodingSetCode = $this->dom->createElement('gmd:MD_CharacterSetCode', strtoupper($encoding));
        $characterEncodingSetCode->setAttribute('codeListeValue', $encoding);
        $characterEncodingSetCode->setAttribute('codeList', '#MD_CharacterSetCode');
        $characterEncoding->appendChild($characterEncodingSetCode);
        foreach ($this->ldao->get() as $key => $value) {
            if ($value->{'iso639-2T'} != $default) {
                $pt_locale = $this->dom->createElement('gmd:PT_Locale');
                $pt_locale->setAttribute('id', $key);

                $languageCode = $this->dom->createElement('gmd:languageCode');
                $languageCodeChild = $this->dom->createElement('gmd:LanguageCode', $value->value);
                $languageCodeChild->setAttribute('codeListValue', $value->{'iso639-2T'});
                $languageCodeChild->setAttribute('codeList', '#LanguageCode');

                $languageCode->appendChild($languageCodeChild);

                $pt_locale->appendChild($languageCode);
                $pt_locale->appendChild($characterEncoding);

                $locale->appendChild($pt_locale);
            }
        }

        $headers[] = $locale;

        return $headers;
    }

    private function getHref(SdiRelation $rel) {

        $href = JComponentHelper::getParams('com_easysdi_catalog')->get('catalogurl');
        $href.= '?request=GetRecordById&service=CSW&version=2.0.2&elementSetName=full&outputschema=csw:IsoRecord&id=';
        if (array_key_exists($rel->serializedXpath . '_search', $this->relations)) {
            $href .= $this->relations[$rel->serializedXpath . '_search']->getAttribut_child()->value;
            ;
        }
        $href .= '&fragment=' . $rel->getResoucetype()->getNamespace()->prefix . '%3A' . $rel->getResoucetype()->fragment;

        return $href;
    }

    private function subXpath($key, $reverseIndex) {
        $subPath = '';

        $keys = preg_split('/_/', $key);

        for ($i = 0; $i < count($keys) - $reverseIndex; $i++) {
            $subPath .= $keys[$i] . '_';
        }

        if ($keys > 2) {
            return substr($subPath, 0, -1);
        } else {
            return false;
        }
    }

    /**
     * 
     * @param DOMElement $element
     * @param SdiRelation $rel
     * @return DOMElement Description
     */
    private function setValue(DOMElement $element, SdiRelation $rel) {
        switch ($rel->getAttribut_child()->getStereotype()->value) {
            case 'locale':
                return $this->setLocaleValue($element, $rel);
                break;
            case 'list':
                return $this->setListValue($element, $rel);
                break;
            case 'localechoice':
                return $this->setLocaleChoiceValue($element, $rel);
                break;
            case 'gemet':
                return $this->setGemetValue($element, $rel);
                break;
            case 'geographicextent':
                return $this->setGeographicextendValue($element, $rel);
                break;
            case 'resource':
                return $this->setResourceValue($element, $rel);
                break;


            default:
                return $this->setIsocodeValue($element, $rel);
                break;
        }
    }

    private function setIsocodeValue(DOMElement $element, SdiRelation $rel) {
        $stereotype = $this->dom->createElement($rel->getAttribut_child()->getStereotype()->getNamespace()->prefix . ':' . $rel->getAttribut_child()->getStereotype()->isocode, $rel->getAttribut_child()->value);
        $element->appendChild($stereotype);

        return $element;
    }

    private function setLocaleValue(DOMElement $element, SdiRelation $rel, $values = null) {
        if (!isset($values)) {
            $values = $rel->getAttribut_child()->value;
        }

        if (count($values) > 0) {

            /**
             * @todo Récupération de la langue par defaut.
             */
            $defaultLanguage = 'DE';

            $CharacterString = $this->dom->createElement('gco:CharacterString', $values[$defaultLanguage]);
            $PT_FreeText = $this->dom->createElement('gmd:PT_FreeText');

            $textGroup = $this->dom->createElement('gmd:textGroup');

            unset($values[$defaultLanguage]);

            foreach ($values as $key => $value) {
                $LocalisedCharacterString = $this->dom->createElement('gmd:LocalisedCharacterString', $value);
                $LocalisedCharacterString->setAttribute('locale', '#' . $key);

                $textGroup->appendChild($LocalisedCharacterString);
            }

            $PT_FreeText->appendChild($textGroup);

            $element->appendChild($CharacterString);
            $element->appendChild($PT_FreeText);
        }

        return $element;
    }

    private function setListValue(DOMElement $element, SdiRelation $rel) {
        $child = $this->dom->createElement($rel->getAttribut_child()->getListeNamespace()->prefix . ':' . $rel->getAttribut_child()->type_iso);
        $child->setAttribute('codeListValue', $rel->getAttribut_child()->value);
        $child->setAttribute('codeList', $rel->getAttribut_child()->codelist);
        //$child->setAttribute('id', $rel->serializedXpath);

        $element->appendChild($child);

        return $element;
    }

    private function setLocaleChoiceValue(DOMElement $element, SdiRelation $rel) {
        $guid = $rel->getAttribut_child()->value;

        $query = $this->db->getQuery(true);
        $query->select('t.text2 as text, l.`iso3166-1-alpha2` as `key`');
        $query->from('#__sdi_translation as t');
        $query->innerJoin('#__sdi_language as l ON l.id = t.language_id');
        $query->where('t.element_guid = \'' . $guid . '\'');


        $this->db->setQuery($query);
        $result = $this->db->loadObjectList('key');

        $values = array();

        foreach ($result as $key => $value) {
            $values[$key] = $value->text;
        }

        return $this->setLocaleValue($element, $rel, $values);
    }

    private function setGemetValue(DOMElement $element, SdiRelation $rel) {
        return $element;
    }

    private function setGeographicextendValue(DOMElement $element, SdiRelation $rel) {
        return $element;
    }

    private function setResourceValue(DOMElement $element, SdiRelation $rel) {

        return $element;
    }

}

?>

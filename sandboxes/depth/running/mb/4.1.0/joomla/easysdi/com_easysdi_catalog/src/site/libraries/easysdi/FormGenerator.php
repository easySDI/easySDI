<?php

require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/common/EText.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/dao/SdiLanguageDao.php';

require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/enum/EnumChildtype.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/enum/EnumRenderType.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/enum/EnumStereotype.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/dao/SdiNamespaceDao.php';

require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/FormUtils.php';

/**
 * This Class will generate a form in XML format for Joomla.
 * 
 * @version     4.0.0
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
class FormGenerator {

    /** @var JObject */
    private $item;

    /** @var sdiUser */
    private $user;

    /** @var JDatabaseDriver */
    private $db = null;

    /** @var DOMDocument */
    private $csw;

    /** @var SdiLanguageDao */
    private $ldao;

    /** @var JSession */
    private $session;

    /**  @var DOMDocument */
    private $form;

    /** @var DOMDocument */
    public $structure;

    /** @var string */
    public $ajaxXpath;

    /** @var DOMXPath */
    private $domXpathStr;

    /**  @var DOMXPath */
    private $domXpathCsw;

    /** @var string */
    private $catalog_uri = 'http://www.easysdi.org/2011/sdi/catalog';

    /** @var string */
    private $catalog_prefix = 'catalog';

    function __construct(JObject $item = null) {
        $this->db = JFactory::getDbo();
        $this->session = JFactory::getSession();
        $this->ldao = new SdiLanguageDao();
        $this->nsdao = new SdiNamespaceDao();
        $this->user = sdiFactory::getSdiUser();

        $this->form = new DOMDocument('1.0', 'utf-8');
        $this->structure = new DOMDocument('1.0', 'utf-8');

        if (isset($item)) {
            $this->item = $item;
            $this->csw = $item->csw;
            $this->setDomXpathCsw();
            $this->session->set('item', $item);
        } else {
            $this->item = $this->session->get('item');
        }
    }

    /**
     * Returns a form structure to Joomla format.
     * 
     * @return string Form structure in Joomla format
     * @version 4.0.0
     */
    public function getForm() {

        if (!isset($_GET['relid'])) {
            $query = $this->db->getQuery(true);
            $query->select('r.id, r.name, r.childtype_id');
            $query->select('c.id as class_id, c.name as class_name, c.guid as class_guid, c.isrootclass');
            $query->select('ns.id as ns_id, ns.prefix as ns_prefix, ns.uri as ns_uri');
            $query->from('#__sdi_profile AS p');
            $query->innerJoin('#__sdi_relation AS r ON p.class_id = r.parent_id');
            $query->innerJoin('#__sdi_class AS c ON c.id = r.parent_id');
            $query->innerJoin('#__sdi_namespace AS ns ON ns.id = c.namespace_id');
            $query->where('p.id = ' . (int)$this->item->profile_id);
            $query->where('c.isrootclass = '.$query->quote(true));
            $query->group('c.id');

            $this->db->setQuery($query);
            $result = $this->db->loadObject();

            $root = $this->getDomElement($result->ns_uri, $result->ns_prefix, $result->class_name, $result->class_id, EnumChildtype::$CLASS, $result->class_guid);
            foreach ($this->nsdao->getAll() as $ns) {
                $root->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:' . $ns->prefix, $ns->uri);
            }

            $root->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':exist', '1');

            
            
            $this->structure->appendChild($root);

            $this->getChildTree($root);
        } else {

            $this->structure->loadXML(unserialize($this->session->get('structure')));
            $this->setDomXpathStr();

            $parent = $this->domXpathStr->query(FormUtils::unSerializeXpath($_GET['parent_path']))->item(0);

            $query = $this->getRelationQuery();
            $query->where('r.id = ' . $_GET['relid']);

            $this->db->setQuery($query);
            $result = $this->db->loadObject();

            switch ($result->childtype_id) {
                case EnumChildtype::$CLASS:
                    $relation = $this->getDomElement($result->uri, $result->prefix, $result->name, $result->id, EnumChildtype::$RELATION, $result->guid, 1, $result->upperbound);
                    $class = $this->getDomElement($result->class_ns_uri, $result->class_ns_prefix, $result->class_name, $result->class_id, EnumChildtype::$CLASS, $result->class_guid);
                    $relation->appendChild($class);

                    $parent->appendChild($relation);
                    $root = $class;
                    $this->ajaxXpath = $relation->getNodePath();
                    
                    $this->getChildTree($root);
                    break;
                case EnumChildtype::$RELATIONTYPE:
                    $relation = $this->getDomElement($result->classass_ns_uri, $result->classass_ns_prefix, $result->name, $result->classass_id, EnumChildtype::$RELATIONTYPE, $result->guid, 1, $result->upperbound);
                    $relation->setAttributeNS('http://www.w3.org/1999/xlink', 'xlink:show', 'embed');
                    $relation->setAttributeNS('http://www.w3.org/1999/xlink', 'xlink:actuate', 'onLoad');
                    $relation->setAttributeNS('http://www.w3.org/1999/xlink', 'xlink:type', 'simple');
                    $relation->setAttributeNS('http://www.w3.org/1999/xlink', 'xlink:href', '');
                    $relation->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':resourcetypeId', $result->resourcetype_id);
                    $relation->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':relationId', $result->id);

                    $parent->appendChild($relation);
                    $root = $relation;
                    $this->ajaxXpath = $relation->getNodePath();
                    
                    $this->getChildTree($root);
                    break;
                case EnumChildtype::$ATTRIBUT:
                    $parentname = $parent->nodeName;
                    $attribute = $this->domXpathStr->query('descendant::*[@catalog:relid="' . $_GET['relid'] . '"]')->item(0);
                    $attributename = $attribute->nodeName;
                    $cloned = $attribute->cloneNode(true);
                    $parent->appendChild($cloned);
                    $parent->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':exist', '1');


                    $root = $cloned;
                    $this->ajaxXpath = $cloned->getNodePath();
                    break;
            }
            
        }

        $this->setDomXpathStr();

        if (isset($this->csw)) {
            $this->setDomXpathCsw();
            $this->mergeCsw();

            $this->csw->formatOutput = true;
            $response = $this->csw->saveXML();
        }

        $this->structure->formatOutput = true;
        $html = $this->structure->saveXML();

        $this->session->set('structure', serialize($this->structure->saveXML()));
        $form = $this->buildForm($root);
        
        return $form;
    }

    /**
     * Recursive method of constructing the tree
     * 
     * @param DOMElement $parent Current element.
     */
    private function getChildTree(DOMElement $parent, $level = 1) {

        $childs = $this->getChildNode($parent, $level);

        foreach ($childs as $child) {
            $parent->appendChild($child);

            if ($child->lastChild) {
                $element = $child->lastChild;
            } else {
                $element = $child;
            }

            switch ($element->getAttributeNS($this->catalog_uri, 'childtypeId')) {
                case EnumChildtype::$CLASS:
                    $this->getChildTree($element, $level + 1);

                    break;
                case EnumChildtype::$RELATIONTYPE:
                    $this->getChildTree($element, $level + 1);
                    break;
            }
        }
    }

    /**
     * Retrieves the children of the element passed as parameter.
     * 
     * @param DOMElement $parent
     * @return DOMElement[] Childs of parent relation.
     */
    private function getChildNode(DOMElement $parent, $level) {
        $childs = array();


        if ($parent->parentNode->nodeType == XML_ELEMENT_NODE) {
            switch ($parent->getAttributeNS($this->catalog_uri, 'childtypeId')) {
                case EnumChildtype::$RELATIONTYPE:
                    $relationExist = $parent;
                    break;
                default :
                    $relationExist = $parent->parentNode;
                    break;
            }

            $lowerbound = $relationExist->getAttributeNS($this->catalog_uri, 'lowerbound');
            $occurance = 0;
            if (isset($this->csw)) {
                $occurance = $this->domXpathCsw->query('/*' . $relationExist->getNodePath())->length;
            }

            if (!$relationExist->hasAttributeNS($this->catalog_uri, 'exist')) {

                $exist = $lowerbound + $occurance;

                if ($exist < 1) {
                    $relationExist->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':exist', '0');
                    return $childs;
                } else {
                    $relationExist->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':exist', '1');
                }
            }
        }

        $parent_id = $parent->getAttributeNS($this->catalog_uri, 'dbid');
        
        $query = $this->getRelationQuery();
        $query->where('r.parent_id = ' . $parent_id);
        $query->where('r.state = 1');
        $query->order('r.ordering');
        $query->order('r.name');

        $this->db->setQuery($query);


        foreach ($this->db->loadObjectList() as $result) {

            switch ($result->childtype_id) {
                case EnumChildtype::$CLASS:
                    $relation = $this->getDomElement($result->uri, $result->prefix, $result->name, $result->id, EnumChildtype::$RELATION, $result->guid, $result->lowerbound, $result->upperbound);
                    $class = $this->getDomElement($result->class_ns_uri, $result->class_ns_prefix, $result->class_name, $result->class_id, EnumChildtype::$CLASS, $result->class_guid, null, null, $result->class_stereotype_id);

                    $relation->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':level', $level);

                    switch ($result->class_stereotype_id) {
                        case EnumStereotype::$GEOGRAPHICEXTENT:
                            $params = array();
                            $params['stereotype_id'] = $result->class_stereotype_id;

                            foreach ($this->getStereotype((object) $params) as $st) {
                                $relation->appendChild($st);
                            }

                            break;
                        default :
                            $relation->appendChild($class);
                            break;
                    }

                    $childs[] = $relation;

                    break;

                case EnumChildtype::$ATTRIBUT:
                    $attribute = $this->getDomElement($result->attribute_ns_uri, $result->attribute_ns_prefix, $result->attribute_isocode, $result->attribute_id, EnumChildtype::$ATTRIBUT, $result->attribute_guid, $result->lowerbound, $result->upperbound, $result->stereotype_id, $result->rendertype_id);

                    foreach ($this->getStereotype($result) as $st) {
                        $attribute->appendChild($st);
                    }

                    if ($this->user->authorize($this->item->id, sdiUser::metadataeditor)) {
                        switch ($result->editorrelationscope_id) {

                            // Visible
                            case 2:
                                $attribute->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':' . 'readonly', "true");
                                break;
                            // Hidden
                            case 3:
                                $attribute->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':' . 'readonly', "true");
                                $attribute->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':rendertypeId', EnumRendertype::$HIDDEN);
                                break;
                        }
                    } else {
                        switch ($result->relationscope_id) {

                            // Visible
                            case 2:
                                $attribute->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':' . 'readonly', "true");
                                break;
                            // Hidden
                            case 3:
                                $attribute->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':' . 'readonly', "true");
                                $attribute->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':rendertypeId', EnumRendertype::$HIDDEN);
                                break;
                        }
                    }

                    $attribute->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':' . 'relGuid', $result->guid);
                    $attribute->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':' . 'relid', $result->id);
                    $attribute->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':' . 'maxlength', $result->attribute_length);

                    $childs[] = $attribute;

                    break;
                case EnumChildtype::$RELATIONTYPE:
                    $class = $this->getDomElement($result->classass_ns_uri, $result->classass_ns_prefix, $result->name, $result->classass_id, EnumChildtype::$RELATIONTYPE, $result->guid, $result->lowerbound, $result->upperbound);
                    $class->setAttributeNS('http://www.w3.org/1999/xlink', 'xlink:show', 'embed');
                    $class->setAttributeNS('http://www.w3.org/1999/xlink', 'xlink:actuate', 'onLoad');
                    $class->setAttributeNS('http://www.w3.org/1999/xlink', 'xlink:type', 'simple');
                    $class->setAttributeNS('http://www.w3.org/1999/xlink', 'xlink:href', '');
                    $class->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':resourcetypeId', $result->resourcetype_id);
                    $class->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':relationId', $result->id);

                    $childs[] = $class;
                    break;
            }
        }

        return $childs;
    }

    /**
     * This method creates a DOM element.
     * 
     * @param string $uri Namespace URI
     * @param string $prefix Namespace prefix
     * @param string $name Name of the element.
     * @param string $id Id for current element
     * @param int $childtypeId Childtype @see EnumChildtype
     * @param string $guid Guid for current element
     * @param int $lowerbound Minimum occurrence for current element.
     * @param int $upperbound Maximum occurrence for current element.
     * @param int $stereotypeId Stereotype @see EnumStereotype
     * @param int $rendertypeId Rendertype @see EnumRendertype
     * 
     * @return DOMElement 
     */
    private function getDomElement($uri, $prefix, $name, $id, $childtypeId, $guid, $lowerbound = null, $upperbound = null, $stereotypeId = null, $rendertypeId = null) {
        $element = $this->structure->createElementNS($uri, $prefix . ':' . $name);

        $element->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':dbid', $id);
        $element->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':id', $guid);
        $element->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':childtypeId', $childtypeId);
        $element->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':index', '1');

        if (isset($lowerbound)) {
            $element->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':lowerbound', $lowerbound);
        }
        if (isset($upperbound)) {
            $element->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':upperbound', $upperbound);
        }
        if (isset($rendertypeId)) {
            $element->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':rendertypeId', $rendertypeId);
        }
        if (isset($stereotypeId)) {
            $element->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':stereotypeId', $stereotypeId);
        }

        return $element;
    }

    /**
     * Returns the structure of a stereotype.
     * 
     * @param stdClass $result
     * @return DOMElement[]
     */
    private function getStereotype($result) {
        $elements = array();
        //$defaultLanguage = 'DE';
        $sdiLangue = new SdiLanguageDao();
        $languages = $sdiLangue->getSupported();


        switch ($result->stereotype_id) {

            case EnumStereotype::$LOCALE:
            case EnumStereotype::$LOCALECHOICE:
            case EnumStereotype::$GEMET:
                $characterString = $this->structure->createElementNS('http://www.isotc211.org/2005/gco', 'gco:CharacterString');
                $elements[] = $characterString;
                foreach ($languages as $key => $value) {
                    $pt_freetext = $this->structure->createElementNS('http://www.isotc211.org/2005/gmd', 'gmd:PT_FreeText');
                    $textGroup = $this->structure->createElementNS('http://www.isotc211.org/2005/gmd', 'gmd:textGroup');
                    $localisedcs = $this->structure->createElementNS('http://www.isotc211.org/2005/gmd', 'gmd:LocalisedCharacterString');
                    $localisedcs->setAttribute('locale', '#' . $key);

                    $textGroup->appendChild($localisedcs);
                    $pt_freetext->appendChild($textGroup);

                    $elements[] = $pt_freetext;
                }

                break;

            case EnumStereotype::$LIST:

                $element = $this->structure->createElementNS($result->list_ns_uri, $result->list_ns_prefix . ':' . $result->attribute_type_isocode);

                if (!empty($result->attribute_codelist)) {
                    $element->setAttribute('codeList', $result->attribute_codelist);
                    $element->setAttribute('codeListValue', '');
                }

                $elements[] = $element;
                break;

            case EnumStereotype::$GEOGRAPHICEXTENT:
                $element = $this->getExtendStereotype();
                $elements[] = $element;
                break;
            case EnumStereotype::$MAPGEOGRAPHICEXTENT:
                $elements[] = $this->structure->createElement('stereotype');
                break;
            case EnumStereotype::$FREEMAPGEOGRAPHICEXTENT:
                $elements[] = $this->structure->createElement('stereotype');
                break;

            default:
                $elements[] = $this->structure->createElementNS($result->stereotype_ns_uri, $result->stereotype_ns_prefix . ':' . $result->stereotype_isocode);
                break;
        }

        return $elements;
    }

    /**
     * Returns the structure of the stereotype "Extent".
     * 
     * @return DOMElement
     */
    private function getExtendStereotype() {
        $namspaces = array();
        foreach ($this->nsdao->getAll() as $ns) {
            $namspaces[$ns->prefix] = $ns->uri;
        }

        $EX_Extent = $this->structure->createElementNS($namspaces['gmd'], 'gmd:EX_Extent');
        $EX_Extent->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':dbid', '0');
        $EX_Extent->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':childtypeId', EnumChildtype::$CLASS);
        $EX_Extent->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':stereotypeId', EnumStereotype::$GEOGRAPHICEXTENT);

        $extentType = $this->structure->createElementNS($namspaces['sdi'], 'sdi:extentType');
        $extentType->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':childtypeId', EnumChildtype::$ATTRIBUT);
        $extentType->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':stereotypeId', EnumStereotype::$BOUNDARYCATEGORY);
        $extentType->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':rendertypeId', EnumRendertype::$LIST);
        $extentType->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':label', 'COM_EASYSDI_CATALOGE_EXTENT_TYPE');

        $description = $this->structure->createElementNS($namspaces['gmd'], 'gmd:description');
        $description->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':childtypeId', EnumChildtype::$ATTRIBUT);
        $description->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':stereotypeId', EnumStereotype::$BOUNDARY);
        $description->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':rendertypeId', EnumRendertype::$LIST);
        $description->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':label', 'COM_EASYSDI_CATALOGE_EXTENT_DESCRIPTION');

        $geographicElement = $this->structure->createElementNS($namspaces['gmd'], 'gmd:geographicElement');
        $geographicElement->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':childtypeId', EnumChildtype::$RELATION);
        $geographicElement->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':lowerbound', '3');
        $geographicElement->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':upperbound', '3');

        $geographicElement1 = $geographicElement->cloneNode();
        $geographicElement1->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':exist', '1');
        $geographicElement1->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':label', 'COM_EASYSDI_CATALOGE_EXTENT_GEOGRAPHICELEMENT');

        $EX_GeographicBoundingBox = $this->structure->createElementNS($namspaces['gmd'], 'gmd:EX_GeographicBoundingBox');
        $EX_GeographicBoundingBox->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':childtypeId', EnumChildtype::$CLASS);

        $extentTypeCode = $this->structure->createElementNS($namspaces['gmd'], 'gmd:extentTypeCode');
        $extentTypeCode->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':childtypeId', EnumChildtype::$ATTRIBUT);
        $extentTypeCode->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':rendertypeId', EnumRendertype::$HIDDEN);

        $northBoundLatitude = $this->structure->createElementNS($namspaces['gmd'], 'gmd:northBoundLatitude');
        $northBoundLatitude->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':childtypeId', EnumChildtype::$ATTRIBUT);
        $northBoundLatitude->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':rendertypeId', EnumRendertype::$TEXTBOX);
        $northBoundLatitude->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':stereotypeId', EnumStereotype::$NUMBER);
        $northBoundLatitude->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':label', 'COM_EASYSDI_CATALOGE_EXTENT_NORTHBOUNDLATITUDE');
        $northBoundLatitude->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':boundingbox', 'true');

        $southBoundLatitude = $this->structure->createElementNS($namspaces['gmd'], 'gmd:southBoundLatitude');
        $southBoundLatitude->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':childtypeId', EnumChildtype::$ATTRIBUT);
        $southBoundLatitude->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':rendertypeId', EnumRendertype::$TEXTBOX);
        $southBoundLatitude->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':stereotypeId', EnumStereotype::$NUMBER);
        $southBoundLatitude->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':label', 'COM_EASYSDI_CATALOGE_EXTENT_SOUTHBOUNDLATITUDE');
        $southBoundLatitude->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':boundingbox', 'true');

        $eastBoundLongitude = $this->structure->createElementNS($namspaces['gmd'], 'gmd:eastBoundLongitude');
        $eastBoundLongitude->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':childtypeId', EnumChildtype::$ATTRIBUT);
        $eastBoundLongitude->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':rendertypeId', EnumRendertype::$TEXTBOX);
        $eastBoundLongitude->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':stereotypeId', EnumStereotype::$NUMBER);
        $eastBoundLongitude->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':label', 'COM_EASYSDI_CATALOGE_EXTENT_EASTBOUNDLONGITUDE');
        $eastBoundLongitude->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':boundingbox', 'true');

        $westBoundLongitude = $this->structure->createElementNS($namspaces['gmd'], 'gmd:westBoundLongitude');
        $westBoundLongitude->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':childtypeId', EnumChildtype::$ATTRIBUT);
        $westBoundLongitude->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':rendertypeId', EnumRendertype::$TEXTBOX);
        $westBoundLongitude->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':stereotypeId', EnumStereotype::$NUMBER);
        $westBoundLongitude->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':label', 'COM_EASYSDI_CATALOGE_EXTENT_WESTBOUNDLONGITUDE');
        $westBoundLongitude->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':boundingbox', 'true');
        $westBoundLongitude->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':map', 'true');

        $geographicElement2 = $geographicElement->cloneNode();
        $geographicElement2->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':exist', '0');

        $EX_GeographicDescription = $this->structure->createElementNS($namspaces['gmd'], 'gmd:EX_GeographicDescription');
        $EX_GeographicDescription->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':childtypeId', EnumChildtype::$CLASS);

        $geographicIdentifier = $this->structure->createElementNS($namspaces['gmd'], 'gmd:geographicIdentifier');
        $geographicIdentifier->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':childtypeId', EnumChildtype::$RELATION);
        $geographicIdentifier->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':lowerbound', '1');
        $geographicIdentifier->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':upperbound', '1');
        $geographicIdentifier->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':exist', '0');

        $MD_Identifier = $this->structure->createElementNS($namspaces['gmd'], 'gmd:MD_Identifier');
        $MD_Identifier->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':childtypeId', EnumChildtype::$CLASS);

        $code = $this->structure->createElementNS($namspaces['gmd'], 'gmd:code');
        $code->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':childtypeId', EnumChildtype::$ATTRIBUT);
        $code->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':rendertypeId', EnumRendertype::$HIDDEN);

        $CharacterString = $this->structure->createElementNS($namspaces['gco'], 'gco:CharacterString');
        $Boolean = $this->structure->createElementNS($namspaces['gco'], 'gco:Boolean', 'true');
        $Decimal = $this->structure->createElementNS($namspaces['gco'], 'gco:Decimal');

        $extentType->appendChild($CharacterString->cloneNode());
        $description->appendChild($CharacterString->cloneNode());
        $extentTypeCode->appendChild($Boolean->cloneNode(true));
        $northBoundLatitude->appendChild($Decimal->cloneNode());
        $southBoundLatitude->appendChild($Decimal->cloneNode());
        $eastBoundLongitude->appendChild($Decimal->cloneNode());
        $westBoundLongitude->appendChild($Decimal->cloneNode());
        $code->appendChild($CharacterString->cloneNode());

        $MD_Identifier->appendChild($code);
        $geographicIdentifier->appendChild($MD_Identifier);

        $EX_GeographicBoundingBox->appendChild($extentTypeCode->cloneNode(true));
        $EX_GeographicBoundingBox->appendChild($northBoundLatitude);
        $EX_GeographicBoundingBox->appendChild($southBoundLatitude);
        $EX_GeographicBoundingBox->appendChild($eastBoundLongitude);
        $EX_GeographicBoundingBox->appendChild($westBoundLongitude);

        $EX_GeographicDescription->appendChild($extentTypeCode->cloneNode(true));
        $EX_GeographicDescription->appendChild($geographicIdentifier);

        $geographicElement1->appendChild($EX_GeographicBoundingBox);
        $geographicElement2->appendChild($EX_GeographicDescription);

        $EX_Extent->appendChild($extentType);
        $EX_Extent->appendChild($description);
        $EX_Extent->appendChild($geographicElement1);
        $EX_Extent->appendChild($geographicElement2);

        return $EX_Extent;
    }

    /**
     * This method adds the required number of occurrences of a relation.
     */
    private function mergeCsw() {
        
        foreach ($this->domXpathStr->query('//*[@catalog:childtypeId="0"]|//*[@catalog:childtypeId="2"]|//*[@catalog:childtypeId="3"]') as $relation) {
            $xpath = $relation->getNodePath();
            $nbr = $this->domXpathCsw->query('/*' . $xpath)->length;

            $hasSibling = isset($relation->nextSibling);
            if ($hasSibling) {
                $nextSibling = $relation->nextSibling;
            }
            for ($i = 1; $i < $nbr; $i++) {
                $clone = $relation->cloneNode(true);
                $clone->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':index', $i + 1);
                if ($hasSibling) {
                    $relation->parentNode->insertBefore($clone, $nextSibling);
                } else {
                    $relation->parentNode->appendChild($clone);
                }
            }
        }

        $this->getValue($this->structure->getElementsByTagNameNS('*', '*')->item(0));
    }

    /**
     * This method retrieves the value of an attribute using XPath.
     * 
     * @param DOMNode $child The current attribute.
     */
    private function getValue(DOMNode $child) {
        foreach ($child->childNodes as $node) {
            if ($node->hasChildNodes()) {
                if ($node->getAttributeNS($this->catalog_uri, 'childtypeId') == EnumChildtype::$RELATIONTYPE) {
                    $nodeCsw = $this->domXpathCsw->query('/*' . $node->getNodePath())->item(0);
                    if (isset($nodeCsw)) {
                        $node->setAttributeNS('http://www.w3.org/1999/xlink', 'xlink:href', $nodeCsw->getAttributeNS('http://www.w3.org/1999/xlink', 'href'));
                    }
                }
                $this->getValue($node);
            } else {
                $xpath = $node->getNodePath();
                $nodeCsw = $this->domXpathCsw->query('/*' . $node->getNodePath())->item(0);
                if (isset($nodeCsw)) {
                    $node->nodeValue = htmlspecialchars($nodeCsw->nodeValue);

                    if (isset($nodeCsw->attributes)) {
                        foreach ($nodeCsw->attributes as $attribute) {
                            $node->setAttribute($attribute->nodeName, $attribute->nodeValue);
                        }
                    }
                }
            }
        }
    }

    /**
     * Returns a form structure in Joomla format
     * 
     * @return string Form structure to Joomla format.
     */
    private function buildForm(DOMElement $root) {
        $form = $this->form->createElement('form');
        $form->appendChild($this->getHiddenFields());

        $fieldset = $this->form->createElement('fieldset');

        switch ($root->getAttributeNS($this->catalog_uri, 'childtypeId')) {
            case EnumChildtype::$ATTRIBUT:
            case EnumChildtype::$RELATIONTYPE:
                $query = 'descendant-or-self::*[@catalog:childtypeId="2"]|descendant-or-self::*[@catalog:childtypeId="3"]';
                break;

            default:
                $query = 'descendant::*[@catalog:childtypeId="2"]|descendant::*[@catalog:childtypeId="3"]';
                break;
        }

        foreach ($this->domXpathStr->query($query, $root) as $attribute) {
            $attributes = $this->getFormField($attribute);
            if (is_array($attributes)) {
                foreach ($attributes as $attr) {
                    $fieldset->appendChild($attr);
                }
            } else {
                $fieldset->appendChild($attributes);
            }
        }

        $form->appendChild($fieldset);
        $this->form->appendChild($form);
        $this->form->formatOutput = true;
        return $this->form->saveXML();
    }

    /**
     * Creates hidden fields added to the form.
     * 
     * @return DOMElement
     */
    private function getHiddenFields() {
        $fieldset = $this->form->createElement('fieldset');
        $fieldset->setAttribute('name', 'hidden');

        $id = $this->form->createElement('field');
        $id->setAttribute('name', 'id');
        $id->setAttribute('type', 'hidden');
        $id->setAttribute('filter', 'safehtml');

        $guid = $this->form->createElement('field');
        $guid->setAttribute('name', 'guid');
        $guid->setAttribute('type', 'hidden');
        $guid->setAttribute('filter', 'safehtml');

        $metadatastateId = $this->form->createElement('field');
        $metadatastateId->setAttribute('name', 'metadatastate_id');
        $metadatastateId->setAttribute('type', 'hidden');
        $metadatastateId->setAttribute('filter', 'safehtml');

        $published = $this->form->createElement('field');
        $published->setAttribute('name', 'published');
        $published->setAttribute('type', 'hidden');
        $published->setAttribute('filter', 'safehtml');

        $fieldset->appendChild($id);
        $fieldset->appendChild($guid);
        $fieldset->appendChild($metadatastateId);
        $fieldset->appendChild($published);

        return $fieldset;
    }

    /**
     * Returns a field corresponding to RenderType Joomla format.
     * 
     * @param DOMElement $attribute
     * @return DOMElement
     */
    private function getFormField(DOMElement $attribute) {
        $field = $this->form->createElement('field');
        switch ($attribute->getAttributeNS($this->catalog_uri, 'childtypeId')) {
            case EnumChildtype::$RELATIONTYPE:
                return $this->getRelationType($attribute);
                break;

            case EnumChildtype::$ATTRIBUT:
                switch ($attribute->getAttributeNS($this->catalog_uri, 'rendertypeId')) {
                    case EnumRendertype::$TEXTBOX:
                        switch ($attribute->getAttributeNS($this->catalog_uri, 'stereotypeId')) {
                            case EnumStereotype::$FILE:
                                return $this->getFormFileField($attribute);
                                break;

                            default:
                                return $this->getFormTextBoxField($attribute);
                                break;
                        }
                        break;
                    case EnumRendertype::$TEXTAREA:
                        return $this->getFormTextAreaField($attribute);
                        break;
                    case EnumRendertype::$CHECKBOX:
                        return $this->getFormCheckboxField($attribute);
                        break;
                    case EnumRendertype::$RADIOBUTTON:
                        return $this->getFormRadioButtonField($attribute);
                        break;
                    case EnumRendertype::$LIST:
                        return $this->getFormListField($attribute);
                        break;
                    case EnumRendertype::$DATE:
                        return $this->getFormDateField($attribute);
                        break;
                    case EnumRendertype::$DATETIME:
                        return $this->getFormDateField($attribute);
                        break;
                    case EnumRendertype::$GEMET:
                        return $this->getFormGemetField($attribute);
                        break;
                    case EnumRendertype::$HIDDEN:
                        return $this->getFormHiddenField($attribute);
                        break;
                }
                break;
            default:
                break;
        }
    }

    /**
     * Create a field of type text.
     * 
     * @param DOMElement $attribute
     * @return DOMElement
     */
    private function getFormTextBoxField(DOMElement $attribute) {
        $maxlength = $attribute->getAttributeNS($this->catalog_uri, 'maxlength');
        $readonly = $attribute->getAttributeNS($this->catalog_uri, 'readonly');
        $relId = $attribute->getAttributeNS($this->catalog_uri, 'relid');
        $guid = $attribute->getAttributeNS($this->catalog_uri, 'relGuid');
        $label = $attribute->getAttributeNS($this->catalog_uri, 'label');
        $boundingbox = $attribute->getAttributeNS($this->catalog_uri, 'boundingbox');

        $fields = array();
        $field = $this->form->createElement('field');

        $validator = $this->getValidatorClass($attribute);

        $field->setAttribute('type', 'text');
        $field->setAttribute('class', $validator);

        if ($maxlength > 0) {
            $field->setAttribute('maxlength', $maxlength);
        }
        if ($readonly) {
            $field->setAttribute('readonly', 'true');
        }

        if ($boundingbox) {
            $field->setAttribute('onchange', 'drawBB();');
        }

        $field->setAttribute('default', $this->getDefaultValue($relId, $attribute->firstChild->nodeValue));

        $field->setAttribute('name', FormUtils::serializeXpath($attribute->firstChild->getNodePath()));
        if ($guid != '') {
            $field->setAttribute('label', EText::_($guid));
        } else {
            $field->setAttribute('label', JText::_($label));
        }
        $field->setAttribute('description', EText::_($guid, 2));

        $fields[] = $field;

        foreach ($this->domXpathStr->query('*/*/*', $attribute) as $i18nChild) {
            $field = $this->form->createElement('field');

            $field->setAttribute('type', 'text');
            $field->setAttribute('class', $validator);

            if ($maxlength > 0) {
                $field->setAttribute('maxlength', $maxlength);
            }
            if ($readonly) {
                $field->setAttribute('readonly', 'true');
            }

            $field->setAttribute('default', $i18nChild->nodeValue);
            $field->setAttribute('name', FormUtils::serializeXpath($i18nChild->getNodePath()) . $i18nChild->getAttribute('locale'));
            $localeValue = str_replace('#', '', $i18nChild->getAttribute('locale'));
            $field->setAttribute('label', EText::_($guid) . ' ' . $this->ldao->getByIso3166($localeValue)->value);
            $field->setAttribute('description', EText::_($guid, 2));

            $fields[] = $field;
        }

        return $fields;
    }

    /**
     * Create a field of type textarea.
     * 
     * @param DOMElement $attribute 
     * @return DOMElement
     *
     */
    private function getFormTextAreaField(DOMElement $attribute) {
        $readonly = $attribute->getAttributeNS($this->catalog_uri, 'readonly');
        $relid = $attribute->getAttributeNS($this->catalog_uri, 'relid');
        $guid = $attribute->getAttributeNS($this->catalog_uri, 'relGuid');
        $validator = $this->getValidatorClass($attribute);

        $fields = array();
        $field = $this->form->createElement('field');

        $field->setAttribute('type', 'textarea');
        $field->setAttribute('class', $validator);
        $field->setAttribute('rows', 5);
        $field->setAttribute('cols', 5);

        if ($readonly) {
            $field->setAttribute('readonly', 'true');
        }

        $field->setAttribute('default', $this->getDefaultValue($relid, $attribute->firstChild->nodeValue));
        $field->setAttribute('name', FormUtils::serializeXpath($attribute->firstChild->getNodePath()));
        $field->setAttribute('label', EText::_($guid));
        $field->setAttribute('description', EText::_($guid, 2));

        $fields[] = $field;

        foreach ($this->domXpathStr->query('*/*/*', $attribute) as $i18nChild) {
            $field = $this->form->createElement('field');

            $field->setAttribute('type', 'textarea');
            $field->setAttribute('class', $validator);
            $field->setAttribute('rows', 5);
            $field->setAttribute('cols', 5);

            if ($readonly) {
                $field->setAttribute('readonly', 'true');
            }

            $field->setAttribute('default', $i18nChild->nodeValue);
            $field->setAttribute('name', FormUtils::serializeXpath($i18nChild->getNodePath()) . $i18nChild->getAttribute('locale'));
            $localeValue = str_replace('#', '', $i18nChild->getAttribute('locale'));
            $field->setAttribute('label', EText::_($guid) . ' ' . $this->ldao->getByIso3166($localeValue)->value);
            $field->setAttribute('description', EText::_($guid, 2));

            $fields[] = $field;
        }

        return $fields;
    }

    /**
     * Create a field of type checkboxes.
     * 
     * @param DOMElement $attribute 
     * @return DOMElement
     * @since 4.0.0
     */
    private function getFormCheckboxField(DOMElement $attribute) {
        $field = $this->form->createElement('field');

        $readonly = $attribute->getAttributeNS($this->catalog_uri, 'readonly');
        $guid = $attribute->getAttributeNS($this->catalog_uri, 'relGuid');
        $relid = $attribute->getAttributeNS($this->catalog_uri, 'relid');

        $allValues = $this->domXpathStr->query('child::*[@catalog:relid="' . $relid . '"]', $attribute->parentNode);
        $default = array();
        foreach ($allValues as $node) {
            $default[] = $node->firstChild->getAttribute('codeListValue');
        }

        $field->setAttribute('type', 'checkboxes');
        $name = FormUtils::removeIndexToXpath(FormUtils::serializeXpath($attribute->firstChild->getNodePath()));
        $field->setAttribute('name', $name);

        if ($readonly) {
            $field->setAttribute('readonly', 'true');
        }

        $field->setAttribute('label', EText::_($guid));
        $field->setAttribute('description', EText::_($guid, 2));
        $field->setAttribute('multiple', 'true');

        $field->setAttribute('default', $this->getDefaultValue($relid, implode(',', $default), true));

        $i = 1;
        foreach ($this->getAttributOptions($attribute) as $opt) {
            $option = $this->form->createElement('option', EText::_($opt->guid));
            $option->setAttribute('value', $opt->value);
            $option->setAttribute('onclick', "addOrRemoveCheckbox(this.id," . $relid . ",'" . FormUtils::serializeXpath($attribute->parentNode->getNodePath()) . "','" . FormUtils::serializeXpath($attribute->getNodePath()) . "')");

            $field->appendChild($option);
            $i++;
        }

        return $field;
    }

    /**
     * Create a field of type radio.
     * 
     * @param DOMElement $attribute
     * @return DOMElement
     * @since 4.0.0
     */
    private function getFormRadioButtonField(DOMElement $attribute) {
        $field = $this->form->createElement('field');

        $readonly = $attribute->getAttributeNS($this->catalog_uri, 'readonly');
        $guid = $attribute->getAttributeNS($this->catalog_uri, 'relGuid');
        $relid = $attribute->getAttributeNS($this->catalog_uri, 'relid');

        $field->setAttribute('type', 'radio');
        $field->setAttribute('name', FormUtils::serializeXpath($attribute->firstChild->getNodePath()));

        if ($readonly) {
            $field->setAttribute('readonly', 'true');
        }

        $field->setAttribute('label', EText::_($guid));
        $field->setAttribute('description', EText::_($guid, 2));
        $field->setAttribute('default', $this->getDefaultValue($relid, $attribute->firstChild->getAttribute('codeListValue')));

        foreach ($this->getAttributOptions($attribute) as $opt) {
            $option = $this->form->createElement('option', EText::_($opt->guid));
            $option->setAttribute('value', $opt->value);

            $field->appendChild($option);
        }

        return $field;
    }

    /**
     * Create a field of type grouplist or list.
     * 
     * @param DOMElement $attribute
     * @return DOMElement
     * @since 4.0.0
     */
    private function getFormListField(DOMElement $attribute) {
        $field = $this->form->createElement('field');

        $readonly = $attribute->getAttributeNS($this->catalog_uri, 'readonly');
        $guid = $attribute->getAttributeNS($this->catalog_uri, 'relGuid');
        $relid = $attribute->getAttributeNS($this->catalog_uri, 'relid');
        $label = $attribute->getAttributeNS($this->catalog_uri, 'label');
        $upperbound = $attribute->getAttributeNS($this->catalog_uri, 'upperbound');


        if ($upperbound > 1) {

            $name = FormUtils::removeIndexToXpath(FormUtils::serializeXpath($attribute->firstChild->getNodePath()));
            $field->setAttribute('name', $name);
            $field->setAttribute('multiple', 'true');
        } else {
            $validator = $this->getValidatorClass($attribute);
            $field->setAttribute('class', $validator);
            $field->setAttribute('name', FormUtils::serializeXpath($attribute->firstChild->getNodePath()));
        }

        if ($readonly) {
            $field->setAttribute('readonly', 'true');
        }

        $field->setAttribute('description', EText::_($guid, 2));

        foreach ($this->getAttributOptions($attribute) as $opt) {
            switch ($attribute->getAttributeNS($this->catalog_uri, 'stereotypeId')) {
                case EnumStereotype::$LOCALECHOICE:
                    $group = $this->form->createElement('group');
                    $group->setAttribute('label', EText::_($opt->guid));

                    if ($opt->guid != '') {
                        $option = $this->form->createElement('option', EText::_($opt->guid, 2));
                    } else {
                        $option = $this->form->createElement('option');
                    }

                    $option->setAttribute('value', $opt->guid);

                    $field->setAttribute('type', 'groupedlist');
                    $field->setAttribute('label', EText::_($guid));
                    $field->setAttribute('default', $this->getDefaultValue($relid, $attribute->firstChild->getAttribute('codeListValue'), true));

                    $group->appendChild($option);
                    $field->appendChild($group);
                    break;
                case EnumStereotype::$BOUNDARY:
                    $field->setAttribute('type', 'list');
                    if ($guid != '') {
                        $field->setAttribute('label', EText::_($guid));
                    } else {
                        $field->setAttribute('label', JText::_($label));
                    }

                    if ($opt->guid != '') {
                        $option = $this->form->createElement('option', EText::_($opt->guid));
                    } else {
                        $option = $this->form->createElement('option');
                    }

                    $option->setAttribute('value', $opt->name);

                    $field->appendChild($option);
                    $field->setAttribute('onchange', 'setBoundary(\'' . FormUtils::serializeXpath($attribute->parentNode->getNodePath()) . '\',this.value);');
                    break;
                case EnumStereotype::$BOUNDARYCATEGORY:
                    $field->setAttribute('type', 'list');
                    if ($guid != '') {
                        $field->setAttribute('label', EText::_($guid));
                    } else {
                        $field->setAttribute('label', JText::_($label));
                    }

                    if ($opt->guid != '') {
                        $option = $this->form->createElement('option', EText::_($opt->guid));
                    } else {
                        $option = $this->form->createElement('option');
                    }

                    $option->setAttribute('value', $opt->name);

                    $field->appendChild($option);
                    $field->setAttribute('onchange', 'filterBoundary(\'' . FormUtils::serializeXpath($attribute->parentNode->getNodePath()) . '\',this.value);');
                    break;
                case EnumStereotype::$TEXTCHOICE:
                    $field->setAttribute('type', 'list');
                    $field->setAttribute('label', EText::_($guid));
                    $field->setAttribute('default', $this->getDefaultValue($relid, $attribute->firstChild->nodeValue, true));

                    if ($opt->guid != '') {
                        $option = $this->form->createElement('option', EText::_($opt->guid));
                    } else {
                        $option = $this->form->createElement('option');
                    }
                    $option->setAttribute('value', $opt->value);

                    $field->appendChild($option);
                    break;
                default:
                    $field->setAttribute('type', 'list');
                    $field->setAttribute('label', EText::_($guid));
                    if ($attribute->firstChild->hasAttribute('codeListValue')) {
                        $field->setAttribute('default', $this->getDefaultValue($relid, $attribute->firstChild->getAttribute('codeListValue'), true));
                    } else {
                        $field->setAttribute('default', $this->getDefaultValue($relid, $attribute->firstChild->nodeValue, true));
                    }

                    if($upperbound > 1) {
                        $allValues = $this->domXpathStr->query('child::*[@catalog:relid="' . $relid . '"]', $attribute->parentNode);
                        $default = array();
                        foreach ($allValues as $node) {
                            $default[] = $node->firstChild->nodeValue;
                        }
                        $field->setAttribute('type', 'MultipleDefaultList');
                        $field->setAttribute('default', $this->getDefaultValue($relid, implode(',', $default), true));
                    }

                    if ($opt->guid != '') {
                        $option = $this->form->createElement('option', EText::_($opt->guid));
                    } else {
                        $option = $this->form->createElement('option');
                    }
                    $option->setAttribute('value', $opt->value);

                    $field->appendChild($option);
                    break;
            }
        }

        return $field;
    }

    /**
     * Create a field of type calendar.
     * 
     * @param DOMElement $attribute
     * @return DOMElement
     * @since 4.0.0
     */
    private function getFormDateField(DOMElement $attribute) {
        $field = $this->form->createElement('field');

        $readonly = $attribute->getAttributeNS($this->catalog_uri, 'readonly');
        $guid = $attribute->getAttributeNS($this->catalog_uri, 'relGuid');
        $relid = $attribute->getAttributeNS($this->catalog_uri, 'relid');

        if ($readonly) {
            $field->setAttribute('readonly', 'true');
        }

        $validator = $this->getValidatorClass($attribute);

        $field->setAttribute('name', FormUtils::serializeXpath($attribute->firstChild->getNodePath()));
        $field->setAttribute('type', 'calendar');
        $field->setAttribute('class', $validator);
        $field->setAttribute('format', '%Y-%m-%d');
        $field->setAttribute('label', EText::_($guid));
        $field->setAttribute('description', EText::_($guid, 2));

        $field->setAttribute('default', $this->getDefaultValue($relid, substr($attribute->firstChild->nodeValue, 0, 10)));


        return $field;
    }

    private function getFormDateTimeField(SdiRelation $rel) {
        //TODO: Not Implemented
    }

    /**
     * Create a field of type list specific for GEMET stereotype.
     * 
     * @param DOMElement $attribute
     * @return DOMElement
     */
    private function getFormGemetField(DOMElement $attribute) {
        $guid = $attribute->getAttributeNS($this->catalog_uri, 'relGuid');
        $label = $attribute->getAttributeNS($this->catalog_uri, 'label');

        $fields = array();

        $field = $this->form->createElement('field');

        $field->setAttribute('type', 'list');
        $field->setAttribute('multiple', 'true');
        $field->setAttribute('name', FormUtils::serializeXpath($attribute->firstChild->getNodePath()));
        if ($guid != '') {
            $field->setAttribute('label', EText::_($guid));
        } else {
            $field->setAttribute('label', JText::_($label));
        }
        $field->setAttribute('description', EText::_($guid, 2));

        $defaults = array();
        foreach ($this->domXpathStr->query('descendant::gco:CharacterString', $attribute->parentNode) as $element) {
            $defaults[] = $element->nodeValue;
        }

        foreach ($defaults as $opt) {
            $option = $this->form->createElement('option', $opt);
            $option->setAttribute('value', $opt);
            $field->appendChild($option);
        }

        $fields[] = $field;

        foreach ($this->ldao->getSupported() as $key => $lang) {
            $field = $this->form->createElement('field');

            $field->setAttribute('type', 'list');
            $field->setAttribute('multiple', 'true');
            $field->setAttribute('name', FormUtils::serializeXpath($attribute->getNodePath() . '/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString#' . $key));
            $field->setAttribute('label', EText::_($guid) . ' ' . $this->ldao->getByIso3166($key)->value);
            $field->setAttribute('description', EText::_($guid, 2));

            $defaults = array();
            foreach ($this->domXpathStr->query('*/*/*/*[@locale="#FR"]', $attribute->parentNode) as $element) {
                $defaults[] = $element->nodeValue;
            }

            foreach ($defaults as $opt) {
                $option = $this->form->createElement('option', $opt);
                $option->setAttribute('value', $opt);
                $field->appendChild($option);
            }

            $fields[] = $field;
        }

        return $fields;
    }

    /**
     * Create a field of type Hidden
     * 
     * @param DOMElement $attribute
     * @return DOMElement
     */
    private function getFormHiddenField(DOMElement $attribute) {
        $relid = $attribute->getAttributeNS($this->catalog_uri, 'relid');
        $stereotypeid = $attribute->getAttributeNS($this->catalog_uri, 'stereotypeId');

        $attributename = $attribute->nodeName;
        $field = $this->form->createElement('field');


        $field->setAttribute('name', FormUtils::serializeXpath($attribute->firstChild->getNodePath()));
        $field->setAttribute('type', 'hidden');

        switch ($stereotypeid) {
            case 6:
            case 9:
                $field->setAttribute('default', $this->getDefaultValue($relid, $attribute->firstChild->nodeValue, true));
                break;

            default:
                $field->setAttribute('default', $this->getDefaultValue($relid, $attribute->firstChild->nodeValue));
                break;
        }

        $field->setAttribute('label', 'label');

        return $field;
    }

    /**
     * Create a field of type list specific for Relationtype.
     * 
     * @param DOMElement $relationtype
     * @return DOMElement
     */
    private function getRelationType(DOMElement $relationtype) {
        $field = $this->form->createElement('field');

        $guid = $relationtype->getAttributeNS($this->catalog_uri, 'id');
        if (preg_match('/id=([a-z0-9-]*)/i', $relationtype->getAttributeNS('http://www.w3.org/1999/xlink', 'href'), $default) === 1) {
            $field->setAttribute('default', $default[1]);
        } else {
            $field->setAttribute('default', '');
        }

        $name = $relationtype->nodeName;

        $field->setAttribute('name', FormUtils::serializeXpath($relationtype->getNodePath()));
        $field->setAttribute('type', 'list');
        $field->setAttribute('label', 'Name');

        foreach ($this->getAttributOptions($relationtype) as $opt) {
            $option = $this->form->createElement('option', $opt->name);
            $option->setAttribute('value', $opt->guid);

            $field->appendChild($option);
        }

        return $field;
    }

    /**
     * Create a field of type file.
     * 
     * @param DOMElement $attribute
     * @return DOMElement
     */
    private function getFormFileField(DOMElement $attribute) {
        $fields = array();

        $maxlength = $attribute->getAttributeNS($this->catalog_uri, 'maxlength');
        $readonly = $attribute->getAttributeNS($this->catalog_uri, 'readonly');
        $guid = $attribute->getAttributeNS($this->catalog_uri, 'relGuid');

        $field = $this->form->createElement('field');

        $validator = $this->getValidatorClass($attribute);

        $field->setAttribute('type', 'file');
        $field->setAttribute('name', FormUtils::serializeXpath($attribute->firstChild->getNodePath()));
        $field->setAttribute('label', EText::_($guid));
        $field->setAttribute('class', $validator);
        $field->setAttribute('description', EText::_($guid, 2));
        $field->setAttribute('default', $attribute->firstChild->nodeValue);

        $fields[] = $field;

        $hiddenField = $this->form->createElement('field');
        $hiddenField->setAttribute('type', 'hidden');
        $hiddenField->setAttribute('name', FormUtils::serializeXpath($attribute->firstChild->getNodePath()) . '_filehidden');
        $hiddenField->setAttribute('default', $attribute->firstChild->nodeValue);

        $textField = $this->form->createElement('field');
        $textField->setAttribute('type', 'text');
        $textField->setAttribute('name', FormUtils::serializeXpath($attribute->firstChild->getNodePath()) . '_filetext');
        $textField->setAttribute('default', $attribute->firstChild->nodeValue);
        $textField->setAttribute('readonly', 'true');

        $fields[] = $textField;
        $fields[] = $hiddenField;

        return $fields;
    }

    /**
     * Retrieves the list of options for fields such list, checkbox and radio.
     * 
     * @param DOMElement $attribute
     * @return mixed List of options for list type fields, checkbox and radio.
     * @since 4.0
     */
    private function getAttributOptions(DOMElement $attribute) {
        $query = $this->db->getQuery(true);

        switch ($attribute->getAttributeNS($this->catalog_uri, 'childtypeId')) {
            case EnumChildtype::$RELATIONTYPE:
                $query->select('r.id, r.`name`,m.guid');
                $query->from('#__sdi_resource r');
                $query->innerJoin('#__sdi_version v on v.resource_id = r.id');
                $query->innerJoin('#__sdi_metadata m on m.version_id = v.id');
                $query->where('resourcetype_id = ' . (int)$attribute->getAttributeNS($this->catalog_uri, 'resourcetypeId'));
                $query->order('name ASC');

                $this->db->setQuery($query);
                $result = $this->db->loadObjectList();
                break;
            case EnumChildtype::$ATTRIBUT:
                switch ($attribute->getAttributeNS($this->catalog_uri, 'stereotypeId')) {

                    case EnumStereotype::$BOUNDARY:
                        $query->select('id, guid, name');
                        $query->from('#__sdi_boundary');
                        $query->order('name ASC');

                        $this->db->setQuery($query);
                        $result = $this->db->loadObjectList();

                        $first = array('id' => '', 'guid' => '', 'name' => '');
                        array_unshift($result, (object) $first);
                        break;

                    case EnumStereotype::$BOUNDARYCATEGORY:
                        $query->select('id, guid, name');
                        $query->from('#__sdi_boundarycategory');
                        $query->order('name ASC');

                        $this->db->setQuery($query);
                        $result = $this->db->loadObjectList();

                        $first = array('id' => '', 'guid' => '', 'name' => '');
                        array_unshift($result, (object) $first);
                        break;

                    default:
                        $query->select('id, guid, `name`, `value`');
                        $query->from('#__sdi_attributevalue');
                        $query->where('attribute_id = ' . $attribute->getAttributeNS($this->catalog_uri, 'dbid'));
                        $query->where('state = 1');
                        $query->order('ordering ASC');

                        $this->db->setQuery($query);
                        $result = $this->db->loadObjectList();
                        switch ($attribute->getAttributeNS($this->catalog_uri, 'rendertypeId')) {
                            case EnumRendertype::$CHECKBOX:
                            case EnumRendertype::$RADIOBUTTON:

                                break;

                            default:
                                $first = array('id' => '', 'guid' => '', 'name' => '', 'value' => '');
                                array_unshift($result, (object) $first);
                                break;
                        }
                        break;
                }
                break;
        }

        return $result;
    }

    /**
     * Returns the CSS classes used for validation.
     * Exemple
     * <code>class = "required validate-sdilocale"</code>
     * 
     * @param DOMElement $attribute
     * @return string
     * @since 4.0.0
     */
    private function getValidatorClass(DOMElement $attribute) {
        $validator = '';
        $guid = $attribute->getAttributeNS($this->catalog_uri, 'id');
        $patterns = $this->getPatterns();

        if ($attribute->getAttributeNS($this->catalog_uri, 'lowerbound') > 0) {
            $validator .= ' required ';
        }

        if (array_key_exists($guid, $patterns)) {
            if ($patterns[$guid]->attribute_pattern != '') {
                $validator .= ' validate-sdi' . $patterns[$guid]->guid;
            } elseif ($patterns[$guid]->stereotype_pattern != '') {
                $validator .= ' validate-sdi' . $patterns[$guid]->stereotype_name;
            }

            return $validator;
        } else {
            return '';
        }
    }

    /**
     * Get the list of the attribute and stereotype patterns.
     * 
     * @return array
     */
    private function getPatterns() {
        $query = $this->db->getQuery(true);

        $query->select('a.id, a.guid, a.pattern as attribute_pattern, s.defaultpattern as stereotype_pattern, s.`value` as stereotype_name');
        $query->from('#__sdi_relation as r');
        $query->innerJoin('#__sdi_attribute as a on r.attributechild_id = a.id');
        $query->leftJoin('#__sdi_sys_stereotype as s on a.stereotype_id = s.id');
        $query->where('r.`state` = 1');

        $this->db->setQuery($query);
        return $this->db->loadObjectList('guid');
    }

    /**
     * Unserialze the Xpath
     * 
     * @author Depth S.A.
     * @since 4.0
     * 
     * @param string $xpath
     * @return string Unserialized XPath
     */
    private function unSerializeXpath($xpath) {
        $xpath = str_replace('-la-', '[', $xpath);
        $xpath = str_replace('-ra-', ']', $xpath);
        $xpath = str_replace('-sla-', '/', $xpath);
        $xpath = str_replace('-dp-', ':', $xpath);
        return $xpath;
    }

    private function setDomXpathStr() {
        $this->domXpathStr = new DOMXPath($this->structure);
        foreach ($this->nsdao->getAll() as $ns) {
            $this->domXpathStr->registerNamespace($ns->prefix, $ns->uri);
        }
    }

    private function setDomXpathCsw() {
        $this->domXpathCsw = new DOMXPath($this->csw);
        foreach ($this->nsdao->getAll() as $ns) {
            $this->domXpathCsw->registerNamespace($ns->prefix, $ns->uri);
        }
    }

    /**
     * 
     * @return JDatabaseQuery
     */
    private function getRelationQuery() {
        $query = $this->db->getQuery(true);
        $query->select('r.name, r.id, r.ordering, r.guid, r.childtype_id, r.parent_id, r.lowerbound, r.upperbound, r.rendertype_id, r.relationscope_id, r.editorrelationscope_id');
        $query->select('c.id as class_id, c.`name` AS class_name, c.guid AS class_guid');
        $query->select('ca.id as classass_id, ca.`name` AS classass_name, ca.guid AS classass_guid');
        $query->select('a.id as attribute_id, a.`name` AS attribute_name, a.guid AS attribute_guid, a.isocode AS attribute_isocode, a.type_isocode as attribute_type_isocode, a.codelist as attribute_codelist, a.pattern as attribute_pattern, a.length as attribute_length');
        $query->select('rt.id as resourcetype_id, rt.name as resourcetype_name, rt.fragment as resourcetype_fragment');
        $query->select('st.id as stereotype_id, st.value as stereotype_value, st.isocode as stereotype_isocode, st.defaultpattern as stereotype_defaultpattern');
        $query->select('stc.id as class_stereotype_id, stc.value as class_stereotype_value, stc.isocode as class_stereotype_isocode, stc.defaultpattern as class_stereotype_defaultpattern');
        $query->select('ns.id as ns_id, ns.prefix, ns.uri');
        $query->select('nsc.id as class_ns_id, nsc.prefix as class_ns_prefix, nsc.uri as class_ns_uri');
        $query->select('nsca.id as classass_ns_id, nsca.prefix as classass_ns_prefix, nsca.uri as classass_ns_uri');
        $query->select('nsa.id as attribute_ns_id, nsa.prefix as attribute_ns_prefix, nsa.uri as attribute_ns_uri');
        $query->select('nsst.id as stereotype_ns_id, nsst.prefix as stereotype_ns_prefix, nsst.uri as stereotype_ns_uri');
        $query->select('nsstc.id as class_stereotype_ns_id, nsstc.prefix as class_stereotype_ns_prefix, nsstc.uri as class_stereotype_ns_uri');
        $query->select('nsl.id as list_ns_id, nsl.prefix as list_ns_prefix, nsl.uri as list_ns_uri');
        $query->select('nsrt.id as resourcetype_ns_id, nsrt.prefix as resourcetype_ns_prefix, nsrt.uri as resourcetype_ns_uri');
        $query->from('#__sdi_relation AS r');
        $query->innerJoin('#__sdi_relation_profile AS rp ON r.id = rp.relation_id');
        $query->leftJoin('#__sdi_class AS c ON c.id = r.classchild_id');
        $query->leftJoin('#__sdi_class AS ca ON ca.id = r.classassociation_id');
        $query->leftJoin('#__sdi_attribute AS a ON a.id = r.attributechild_id');
        $query->leftJoin('#__sdi_resourcetype AS rt ON rt.id = r.childresourcetype_id');
        $query->leftJoin('#__sdi_sys_stereotype AS st ON st.id = a.stereotype_id');
        $query->leftJoin('#__sdi_sys_stereotype AS stc ON stc.id = c.stereotype_id');
        $query->leftJoin('#__sdi_namespace AS ns ON ns.id = r.namespace_id');
        $query->leftJoin('#__sdi_namespace AS nsc ON nsc.id = c.namespace_id');
        $query->leftJoin('#__sdi_namespace AS nsca ON nsca.id = ca.namespace_id');
        $query->leftJoin('#__sdi_namespace AS nsa ON nsa.id = a.namespace_id');
        $query->leftJoin('#__sdi_namespace AS nsst ON nsst.id = st.namespace_id');
        $query->leftJoin('#__sdi_namespace AS nsstc ON nsstc.id = stc.namespace_id');
        $query->leftJoin('#__sdi_namespace AS nsl ON nsl.id = a.listnamespace_id');
        $query->leftJoin('#__sdi_namespace AS nsrt ON nsrt.id = rt.fragmentnamespace_id');
        $query->where('rp.profile_id = ' . (int)$this->item->profile_id);

        return $query;
    }

    private function getDefaultValue($relation_id, $value, $isList = false) {
        if (!empty($value)) {
            return $value;
        }

        if (empty($relation_id)) {
            return '';
        }

        $language = $this->ldao->getDefaultLanguage();

        $query = $this->db->getQuery(true);

        if ($isList) {
            $query->select('av.`value`');
            $query->from('#__sdi_relation_defaultvalue rdv');
            $query->innerJoin('#__sdi_attributevalue av on av.id = rdv.attributevalue_id');
            $query->where('rdv.relation_id = ' . (int)$relation_id);
        } else {
            $query->select('attributevalue_id, `value`');
            $query->from('#__sdi_relation_defaultvalue');
            $query->where('relation_id = ' . (int)$relation_id);
            $query->where('language_id = ' . (int)$language->id);
        }

        $this->db->setQuery($query);
        $result = $this->db->loadObject();

        if (empty($result)) {
            return '';
        } else {
            return $result->value;
        }
    }

}

?>

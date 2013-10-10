<?php

require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/po/SdiRelation.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/po/SdiClass.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/po/SdiAttribute.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/po/SdiNamespace.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/po/SdiStereotype.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/common/EText.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/DomCswExtractor.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/dao/SdiLanguageDao.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/po/SdiResourcetype.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/enum/EnumChildtype.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/enum/EnumRendertype.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/enum/EnumStereotype.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/dao/SdiNamespaceDao.php';

/**
 * @version     4.0.0
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
class FormGenerator {

    /**
     * database
     *
     * @var JDatabaseDriver
     */
    private $db = null;

    /**
     *
     * @var DOMDocument 
     */
    private $csw;

    /**
     *
     * @var SdiLanguageDao 
     */
    private $ldao;

    /**
     *
     * @var JSession 
     */
    private $session;

    /**
     *
     * @var DOMDocument 
     */
    private $form;

    /**
     *
     * @var DOMDocument 
     */
    public $structure;

    /**
     *
     * @var string 
     */
    public $ajaxXpath;

    /**
     *
     * @var DOMXPath 
     */
    private $domXpathStr;

    /**
     *
     * @var DOMXPath 
     */
    private $domXpathCsw;
    private $catalog_uri = 'http://www.easysdi.org/2011/sdi/catalog';
    private $catalog_prefix = 'catalog';

    function __construct(DOMDocument $csw = NULL) {
        $this->db = JFactory::getDbo();
        $this->session = JFactory::getSession();
        $this->ldao = new SdiLanguageDao();
        $this->nsdao = new SdiNamespaceDao();
        $this->csw = $csw;
        $this->form = new DOMDocument('1.0', 'utf-8');
        $this->structure = new DOMDocument('1.0', 'utf-8');
        if (isset($csw)) {
            $this->setDomXpathCsw();
        }
    }

    /**
     * Returns a form structure to Joomla format.
     * 
     * @return string Form structure in Joomla format
     * @since 4.0
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
            $query->where('p.id = 1');
            $query->where('c.isrootclass = true');
            $query->group('c.id');

            $this->db->setQuery($query);
            $result = $this->db->loadObject();

            $root = $this->getDomElement($result->ns_uri, $result->ns_prefix, $result->class_name, $result->class_id, EnumChildtype::$CLASS, $result->class_guid);
            foreach ($this->nsdao->getAll() as $ns) {
                $root->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:' . $ns->prefix, $ns->uri);
            }

            $this->structure->appendChild($root);

            $this->getChildTree($root);
        } else {

            $this->structure->loadXML(unserialize($this->session->get('structure')));
            $this->setDomXpathStr();

            $parent = $this->domXpathStr->query($this->unSerializeXpath($_GET['parent_path']))->item(0);

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
                    break;
                default:
                    break;
            }
            
            /*$this->structure->formatOutput = true;
            $html = $this->structure->saveXML();*/
            
            $this->getChildTree($root);  

            $this->ajaxXpath = $relation->getNodePath();
        }

        
        $this->setDomXpathStr();

        if (isset($this->csw)) {
            $this->setDomXpathCsw();
            $this->mergeCsw();
        }

        $this->session->set('structure', serialize($this->structure->saveXML()));
        $form = $this->buildForm($root);

        return $form;
    }

    /**
     * Recursive method of constructing the tree node
     * 
     * @param SdiRelation $rel Current node
     * @param int $level
     * @since 4.0
     */
    private function getChildTree(DOMElement $parent) {

        $childs = $this->getChildNode($parent);

        foreach ($childs as $child) {
            $parent->appendChild($child);

            if ($child->lastChild) {
                $element = $child->lastChild;
            } else {
                $element = $child;
            }

            switch ($element->getAttributeNS($this->catalog_uri, 'childtypeId')) {
                case EnumChildtype::$CLASS:
                    $this->getChildTree($element);

                    break;
                case EnumChildtype::$RELATIONTYPE:
                    $this->getChildTree($element);
                    break;
            }
        }
    }

    /**
     * Retrieves the child relation of the relation passed as a parameter.
     * 
     * @param SdiRelation $rel Current node
     * @return DOMElement[]
     * @since 4.0
     */
    private function getChildNode(DOMElement $parent) {
        $childs = array();
        $parentname = $parent->nodeName;
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

            $exist = $lowerbound + $occurance;

            if ($exist < 1) {
                $relationExist->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':exist', '0');
                return $childs;
            } else {
                $relationExist->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':exist', '1');
            }
        }




        $query = $this->getRelationQuery();
        $query->where('r.parent_id = ' . $parent->getAttributeNS($this->catalog_uri, 'dbid'));
        $query->where('rp.profile_id = 1');
        $query->where('r.state = 1');
        $query->order('r.ordering');
        $query->order('r.name');

        $this->db->setQuery($query);


        foreach ($this->db->loadObjectList() as $result) {

            switch ($result->childtype_id) {
                case EnumChildtype::$CLASS:
                    $relation = $this->getDomElement($result->uri, $result->prefix, $result->name, $result->id, EnumChildtype::$RELATION, $result->guid, $result->lowerbound, $result->upperbound);
                    $class = $this->getDomElement($result->class_ns_uri, $result->class_ns_prefix, $result->class_name, $result->class_id, EnumChildtype::$CLASS, $result->class_guid, null, null, $result->class_stereotype_id);

                    $relation->appendChild($class);
                    $childs[] = $relation;

                    break;

                case EnumChildtype::$ATTRIBUT:
                    $attribut = $this->getDomElement($result->attribute_ns_uri, $result->attribute_ns_prefix, $result->attribute_isocode, $result->attribute_id, EnumChildtype::$ATTRIBUT, $result->attribute_guid, null, null, $result->stereotype_id, $result->rendertype_id);

                    foreach ($this->getStereotype($result) as $st) {
                        $attribut->appendChild($st);
                    }

                    $attribut->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':' . 'relGuid', $result->guid);
                    $attribut->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':' . 'maxlength', $result->attribute_length);
                    $attribut->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':' . 'readonly', $result->attribute_issystem);

                    $childs[] = $attribut;

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
     * 
     * @param type $uri
     * @param type $prefix
     * @param type $name
     * @param type $id
     * @param type $childtypeId
     * @param type $guid
     * @param type $lowerbound
     * @param type $upperbound
     * @param type $stereotypeId
     * @param type $rendertypeId
     * 
     * @return DOMElement Description
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
     * 
     * @param type $result
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
                $characterString = $this->structure->createElement('gco:CharacterString');
                $elements[] = $characterString;
                foreach ($languages as $key => $value) {
                    $pt_freetext = $this->structure->createElement('gmd:PT_FreeText');
                    $textGroup = $this->structure->createElement('gmd:textGroup');
                    $localisedcs = $this->structure->createElement('gmd:LocalisedCharacterString');
                    $localisedcs->setAttribute('locale', '#' . $key);

                    $textGroup->appendChild($localisedcs);
                    $pt_freetext->appendChild($textGroup);

                    $elements[] = $pt_freetext;
                }

                break;

            case EnumStereotype::$LIST:
                $element = $this->structure->createElementNS($result->list_ns_uri, $result->list_ns_prefix . ':' . $result->attribute_type_isocode);
                $element->setAttribute('codeList', $result->attribute_codelist);
                $element->setAttribute('codeListValue', '');

                $elements[] = $element;
                break;

            case EnumStereotype::$GEOGRAPHICEXTENT:
                $elements[] = $this->structure->createElement('stereotype');
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

    private function mergeCsw() {

        foreach ($this->domXpathStr->query('//*[@catalog:childtypeId="0"]|//*[@catalog:childtypeId="3"]') as $relation) {
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
                $nodeCsw = $this->domXpathCsw->query('/*' . $node->getNodePath())->item(0);
                if (isset($nodeCsw)) {
                    $node->nodeValue = $nodeCsw->nodeValue;

                    foreach ($nodeCsw->attributes as $attribute) {
                        $node->setAttribute($attribute->nodeName, $attribute->nodeValue);
                    }
                }
            }
        }
    }

    /**
     * Returns a form structure in Joomla format
     * 
     * @return string Form structure to Joomla format.
     * @since 4.0
     */
    private function buildForm(DOMElement $root) {
        $form = $this->form->createElement('form');
        $form->appendChild($this->getHiddenFields());

        $fieldset = $this->form->createElement('fieldset');
        switch ($root->getAttributeNS($this->catalog_uri, 'childtypeId')) {
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
     * 
     * @return DOMElement
     */
    private function getHiddenFields() {
        $fieldset = $this->form->createElement('fieldset');
        $id = $this->form->createElement('field');
        $id->setAttribute('name', 'id');
        $id->setAttribute('type', 'hidden');
        $id->setAttribute('filter', 'safehtml');

        $guid = $this->form->createElement('field');
        $guid->setAttribute('name', 'guid');
        $guid->setAttribute('type', 'hidden');
        $guid->setAttribute('filter', 'safehtml');

        $fieldset->appendChild($id);
        $fieldset->appendChild($guid);

        return $fieldset;
    }

    /**
     * Returns a field corresponding to RenderType Joomla format.
     * 
     * @param SdiRelation $rel Current node
     * @return DOMElement Field corresponding to RenderType Joomla format.
     * @since 4.0
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
                        return $this->getFormTextBoxField($attribute);
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

                    default:
                        return $field;
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
     * @param SdiRelation $rel
     * @param DOMElement $field
     * @return DOMElement
     * @since 4.0.0
     */
    private function getFormTextBoxField(DOMElement $attribute) {
        $maxlength = $attribute->getAttributeNS($this->catalog_uri, 'maxlength');
        $readonly = $attribute->getAttributeNS($this->catalog_uri, 'readonly');
        $guid = $attribute->getAttributeNS($this->catalog_uri, 'relGuid');

        $fields = array();
        $field = $this->form->createElement('field');

        //$validator = $this->getValidatorClass($rel);

        $field->setAttribute('type', 'text');
        //$field->setAttribute('class', $validator);

        if ($maxlength > 0) {
            $field->setAttribute('maxlength', $maxlength);
        }
        if ($readonly) {
            $field->setAttribute('readonly', 'true');
        }

        $field->setAttribute('default', $attribute->firstChild->nodeValue);
        $field->setAttribute('name', $this->serializeXpath($attribute->getNodePath()));
        $field->setAttribute('label', EText::_($guid));
        $field->setAttribute('description', EText::_($guid, 2));

        $fields[] = $field;

        foreach ($this->domXpathStr->query('*/*/*', $attribute) as $i18nChild) {
            $field = $this->form->createElement('field');

            $field->setAttribute('type', 'text');
            //$field->setAttribute('class', $validator);

            if ($maxlength > 0) {
                $field->setAttribute('maxlength', $maxlength);
            }
            if ($readonly) {
                $field->setAttribute('readonly', 'true');
            }

            $field->setAttribute('default', $i18nChild->nodeValue);
            $field->setAttribute('name', $this->serializeXpath($i18nChild->getNodePath()) . $i18nChild->getAttribute('locale'));
            $field->setAttribute('label', EText::_($guid) . ' i18n');
            $field->setAttribute('description', EText::_($guid, 2));

            $fields[] = $field;
        }

        return $fields;
    }

    /**
     * Create a field of type textarea.
     * 
     * @param SdiRelation $rel
     * @param DOMElement $field
     * @return DOMElement
     * @since 4.0.0
     */
    private function getFormTextAreaField(DOMElement $attribute) {
        $readonly = $attribute->getAttributeNS($this->catalog_uri, 'readonly');
        $guid = $attribute->getAttributeNS($this->catalog_uri, 'relGuid');
        //$validator = $this->getValidatorClass($rel);

        $fields = array();
        $field = $this->form->createElement('field');

        $field->setAttribute('type', 'textarea');
        //$field->setAttribute('class', $validator);
        $field->setAttribute('rows', 5);
        $field->setAttribute('cols', 5);

        if ($readonly) {
            $field->setAttribute('readonly', 'true');
        }

        $field->setAttribute('default', $attribute->nodeValue);
        $field->setAttribute('name', $this->serializeXpath($attribute->getNodePath()));
        $field->setAttribute('label', EText::_($guid));
        $field->setAttribute('description', EText::_($guid, 2));

        $fields[] = $field;

        foreach ($this->domXpathStr->query('*/*/*', $attribute) as $i18nChild) {
            $field = $this->form->createElement('field');

            $field->setAttribute('type', 'textarea');
            //$field->setAttribute('class', $validator);
            $field->setAttribute('row', 10);
            $field->setAttribute('cols', 5);

            if ($readonly) {
                $field->setAttribute('readonly', 'true');
            }

            $field->setAttribute('default', $i18nChild->nodeValue);
            $field->setAttribute('name', $this->serializeXpath($i18nChild->getNodePath()) . $i18nChild->getAttribute('locale'));
            $field->setAttribute('label', EText::_($guid));
            $field->setAttribute('description', EText::_($guid, 2));

            $fields[] = $field;
        }

        return $fields;
    }

    /**
     * Create a field of type checkboxes.
     * 
     * @param SdiRelation $rel
     * @return string
     * @since 4.0.0
     */
    private function getFormCheckboxField(DOMElement $attribute) {
        $field = $this->form->createElement('field');

        $readonly = $attribute->getAttributeNS($this->catalog_uri, 'readonly');
        $guid = $attribute->getAttributeNS($this->catalog_uri, 'relGuid');

        $field->setAttribute('type', 'checkboxes');
        $field->setAttribute('name', $this->serializeXpath($attribute->getNodePath()));

        if ($readonly) {
            $field->setAttribute('readonly', 'true');
        }

        foreach ($this->getAttributValues($attribute) as $value) {
            $option = $this->form->createElement('option', EText::_($value->guid));
            $option->setAttribute('value', $value->value);

            $field->appendChild($option);
        }

        return $field;
    }

    /**
     * Create a field of type radio.
     * 
     * @param SdiRelation $rel
     * @return string
     * @since 4.0.0
     */
    private function getFormRadioButtonField(DOMElement $attribute) {
        $field = $this->form->createElement('field');

        $readonly = $attribute->getAttributeNS($this->catalog_uri, 'readonly');
        $guid = $attribute->getAttributeNS($this->catalog_uri, 'relGuid');

        $field->setAttribute('type', 'radio');
        $field->setAttribute('name', $this->serializeXpath($attribute->getNodePath()));

        if ($readonly) {
            $field->setAttribute('readonly', 'true');
        }

        $field->setAttribute('label', EText::_($guid));
        $field->setAttribute('description', EText::_($guid, 2));

        foreach ($this->getAttributValues($attribute) as $value) {
            $option = $this->form->createElement('option', EText::_($value->guid));
            $option->setAttribute('value', $value->value);

            $field->appendChild($option);
        }

        return $field;
    }

    /**
     * Create a field of type grouplist or list.
     * 
     * @param SdiRelation $rel
     * @return string
     * @since 4.0.0
     */
    private function getFormListField(DOMElement $attribute) {
        $field = $this->form->createElement('field');

        $readonly = $attribute->getAttributeNS($this->catalog_uri, 'readonly');
        $guid = $attribute->getAttributeNS($this->catalog_uri, 'relGuid');

        //$validator = $this->getValidatorClass($rel);

        $field->setAttribute('name', $this->serializeXpath($attribute->getNodePath()));
        //$field->setAttribute('class', $validator);
        if ($readonly) {
            $field->setAttribute('readonly', 'true');
        }

        $field->setAttribute('default', $attribute->firstChild->getAttribute('codeListValue'));
        $field->setAttribute('description', EText::_($guid, 2));

        foreach ($this->getAttributOptions($attribute) as $opt) {
            switch ($attribute->getAttributeNS($this->catalog_uri, 'stereotypeId')) {
                case EnumStereotype::$LOCALECHOICE:
                    $group = $this->form->createElement('group');
                    $group->setAttribute('label', EText::_($opt->guid));

                    $option = $this->form->createElement('option', EText::_($opt->guid, 2));
                    $option->setAttribute('value', $opt->guid);

                    $field->setAttribute('type', 'groupedlist');
                    $field->setAttribute('label', EText::_($guid));

                    $group->appendChild($option);
                    $field->appendChild($group);
                    break;
                case 'resource':
                    $field->setAttribute('type', 'list');
                    $field->setAttribute('label', 'Name');

                    $option = $this->form->createElement('option', $opt->name);
                    $option->setAttribute('value', $opt->guid);

                    $field->appendChild($option);
                    break;
                case EnumStereotype::$GEOGRAPHICEXTENT:
                    $field->setAttribute('type', 'list');
                    $field->setAttribute('label', EText::_($guid));

                    $option = $this->form->createElement('option', EText::_($opt->guid));
                    $option->setAttribute('value', $opt->name);

                    $field->appendChild($option);
                    break;
                default:
                    $field->setAttribute('type', 'list');
                    $field->setAttribute('label', EText::_($guid));

                    $option = $this->form->createElement('option', EText::_($opt->guid));
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
     * @param SdiRelation $rel
     * @return string
     * @since 4.0.0
     */
    private function getFormDateField(DOMElement $attribute) {
        $field = $this->form->createElement('field');

        $readonly = $attribute->getAttributeNS($this->catalog_uri, 'readonly');
        $guid = $attribute->getAttributeNS($this->catalog_uri, 'relGuid');

        if ($readonly) {
            $field->setAttribute('readonly', 'true');
        }

//$validator = $this->getValidatorClass($rel);

        $field->setAttribute('name', $this->serializeXpath($attribute->getNodePath()));
        $field->setAttribute('type', 'calendar');
        //$field->setAttribute('class', $validator);
        $field->setAttribute('format', '%Y-%m-%d');
        $field->setAttribute('label', EText::_($guid));
        $field->setAttribute('description', EText::_($guid, 2));

        $field->setAttribute('default', substr($attribute->nodeValue, 0, 10));


        return $field;
    }

    private function getFormDateTimeField(SdiRelation $rel) {
        // not yet implemented
    }

    private function getRelationType(DOMElement $relationtype) {
        $field = $this->form->createElement('field');

        $guid = $relationtype->getAttributeNS($this->catalog_uri, 'id');
        if (preg_match('/id=([a-z0-9-]*)/i', $relationtype->getAttributeNS('http://www.w3.org/1999/xlink', 'href'), $default) === 1) {
            $field->setAttribute('default', $default[1]);
        } else {
            $field->setAttribute('default', '');
        }

        $name = $relationtype->nodeName;

        $field->setAttribute('name', $this->serializeXpath($relationtype->getNodePath()));
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
     * Retrieves the list of options for fields such list, checkbox and radio.
     * 
     * @param SdiRelation $rel Current node
     * @return mixed List of options for list type fields, checkbox and radio.
     * @since 4.0
     */
    private function getAttributOptions(DOMElement $attribute) {
        $query = $this->db->getQuery(true);

        switch ($attribute->getAttributeNS($this->catalog_uri, 'childtypeId')) {
            case EnumChildtype::$RELATIONTYPE:
                $query->select('id, guid, name');
                $query->from('#__sdi_resource');
                $query->where('resourcetype_id = ' . $attribute->getAttributeNS($this->catalog_uri, 'resourcetypeId'));
                $query->order('name ASC');
                break;
            case EnumChildtype::$ATTRIBUT:
                switch ($attribute->getAttributeNS($this->catalog_uri, 'stereotypeId')) {

                    case EnumStereotype::$GEOGRAPHICEXTENT:
                        $query->select('id, guid, name');
                        $query->from('#__sdi_boundary');
                        $query->order('name ASC');
                        break;

                    default:
                        $query->select('id, guid, `name`, `value`');
                        $query->from('#__sdi_attributevalue');
                        $query->where('attribute_id = ' . $attribute->getAttributeNS($this->catalog_uri, 'dbid'));
                        $query->where('state = 1');
                        $query->order('ordering ASC');
                        break;
                }
                break;
        }


        $this->db->setQuery($query);

        return $this->db->loadObjectList();
    }

    /**
     * Returns the CSS classes used for validation.
     * Exemple
     * <code>class = "required validate-sdilocale"</code>
     * 
     * @param SdiRelation $rel
     * @return string
     * @since 4.0.0
     */
    private function getValidatorClass(SdiRelation $rel) {
        $validator = '';

        if ($rel->lowerbound > 0) {
            $validator .= ' required ';
        }

        if ($rel->getAttribut_child()->pattern != '') {
            $validator .= ' validate-sdi' . $rel->getAttribut_child()->guid;
        } elseif ($rel->getAttribut_child()->getStereotype()->defaultpattern != '') {
            $validator .= ' validate-sdi' . $rel->getAttribut_child()->getStereotype()->value;
        }

        //return $validator;
        return '';
    }

    /**
     * 
     * @param string $xpath
     * @return string
     */
    private function serializeXpath($xpath) {
        $xpath = str_replace('[', '-la-', $xpath);
        $xpath = str_replace(']', '-ra-', $xpath);
        $xpath = str_replace('/', '-sla-', $xpath);
        $xpath = str_replace(':', '-dp-', $xpath);
        return $xpath;
    }

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
        $query->select('r.name, r.id, r.ordering, r.guid, r.childtype_id, r.parent_id, r.lowerbound, r.upperbound, r.rendertype_id');
        $query->select('c.id as class_id, c.`name` AS class_name, c.guid AS class_guid');
        $query->select('ca.id as classass_id, ca.`name` AS classass_name, ca.guid AS classass_guid');
        $query->select('a.id as attribute_id, a.`name` AS attribute_name, a.guid AS attribute_guid, a.isocode AS attribute_isocode, a.type_isocode as attribute_type_isocode, a.codelist as attribute_codelist, a.pattern as attribute_pattern, a.length as attribute_length, a.issystem as attribute_issystem');
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

        return $query;
    }

}

?>

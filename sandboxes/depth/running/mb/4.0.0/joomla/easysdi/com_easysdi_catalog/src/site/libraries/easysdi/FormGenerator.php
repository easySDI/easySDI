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
     * @var array 
     */
    public $relations = array();

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
     * @var DomCswExtractor
     */
    private $dce;

    /**
     *
     * @var DOMDocument 
     */
    private $dom;

    function __construct(DOMDocument $csw = NULL) {
        $this->db = JFactory::getDbo();
        $this->session = JFactory::getSession();
        $this->ldao = new SdiLanguageDao();
        $this->csw = $csw;
        $this->dce = new DomCswExtractor($csw);
        $this->dom = new DOMDocument('1.0', 'utf-8');
    }

    /**
     * Returns a form structure to Joomla format.
     * 
     * @return string Form structure in Joomla format
     * @since 4.0
     */
    public function getForm(SdiRelation $rel = null) {

        if ($rel == NULL) {
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

            $rel = new SdiRelation(0, $result->class_name, SdiRelation::$CLASS);
            $rel->setNamespace(new SdiNamespace($result->ns_id, $result->ns_prefix, $result->ns_uri));
            $rel->setClass_child(new SdiClass($result->class_id, $result->class_name, $result->class_guid, $result->isrootclass, new SdiNamespace($result->ns_id, $result->ns_prefix, $result->ns_uri)));

            $rel->setXpath(array($rel));
        }

        $rel->setSerializedXpath($this->dce->getSerializedXpath($rel));
        $this->relations[$rel->getSerializedXpath()] = $rel;
        //$this->relations[$rel->id . '-0_' . $rel->getClass_child()->id . '-0'] = $rel;

        $this->getChildTree($rel);

        if (!isset($_GET['uuid'])) {
            $this->session->set('relations', serialize($this->relations));
            $this->session->set('attribute_index', 1000);
        }

        $form = $this->buildForm();
        return $form;
    }

    /**
     * Recursive method of constructing the tree node
     * 
     * @param SdiRelation $rel Current node
     * @param int $level
     * @since 4.0
     */
    private function getChildTree(SdiRelation $rel, $level = 0) {

        $rel->level = $level;

        $childs = $this->getChildNode($rel);
        foreach ($childs as $child) {
            $this->relations[$child->getSerializedXpath()] = $child;
            if ($child->childtype_id == SdiRelation::$CLASS || $child->childtype_id == SdiRelation::$RELATIONTYPE) {
                $this->getChildTree($child, $level + 1);
            }
        }
    }

    /**
     * Retrieves the child relation of the relation passed as a parameter.
     * 
     * @param SdiRelation $rel Current node
     * @return SdiRelation[] Array of the child node
     * @since 4.0
     */
    private function getChildNode(SdiRelation $rel) {

        $query = $this->db->getQuery(true);
        $query->select('r.name, r.id, r.ordering, r.guid, r.childtype_id, r.parent_id, r.lowerbound, r.upperbound, r.rendertype_id');
        $query->select('c.id as class_id, c.`name` AS class_name, c.guid AS class_guid');
        $query->select('ca.id as classass_id, ca.`name` AS classass_name, ca.guid AS classass_guid');
        $query->select('a.id as attribute_id, a.`name` AS attribute_name, a.guid AS attribute_guid, a.type_isocode as attribute_type_isocode, a.codelist as attribute_codelist, a.pattern as attribute_pattern, a.length as attribute_length, a.issystem as attribute_issystem');
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
        $query->where('r.parent_id = ' . $rel->getClass_child()->id);
        $query->where('rp.profile_id = 1');
        $query->where('r.state = 1');
        $query->order('r.ordering');
        $query->order('r.name');

        $this->db->setQuery($query);

        $relArray = array();

        if ($rel->getClass_child()->getStereotype() != null) {
            switch ($rel->getClass_child()->getStereotype()->value) {
                case 'geographicextent':
                    $indexedAttr = clone $rel;
                    $indexedAttr->childtype_id = SdiRelation::$ATTRIBUT;
                    $indexedAttr->rendertype = SdiRelation::$LIST;
                    $indexedAttr->setSerializedXpath($rel->getSerializedXpath() . '_bounding_0');
                    $attribute = new SdiAttribute($rel->id . '_bounding_0', $rel->name . '_bounding_0');
                    $indexedAttr->setAttribut_child($attribute);
                    $indexedAttr->getAttribut_child()->setStereotype($rel->getClass_child()->getStereotype());
                    $indexedAttr->getAttribut_child()->value = $this->dce->getValue($indexedAttr, $indexedAttr->getIndex());
                    $relArray[] = $indexedAttr;
                    break;
            }
        }

        foreach ($this->db->loadObjectList() as $result) {
            $newRel = new SdiRelation($result->id, $result->name, $result->childtype_id, $result->guid, $result->rendertype_id);
            $newRel->ordering = $result->ordering;
            $newRel->setParent($rel->getClass_child());
            $newRel->setNamespace(new SdiNamespace($result->ns_id, $result->prefix, $result->uri));
            $newRel->lowerbound = $result->lowerbound;
            $newRel->upperbound = $result->upperbound;

            $parentXpath = $rel->getXpath();
            $parentXpath[] = $newRel;
            $newRel->setXpath($parentXpath);

            switch ($newRel->childtype_id) {
                case SdiRelation::$CLASS:
                    $newRel->setClass_child(new SdiClass($result->class_id, $result->class_name, $result->class_guid, false, new SdiNamespace($result->class_ns_id, $result->class_ns_prefix, $result->class_ns_uri)));
                    $newRel->getClass_child()->setStereotype(new SdiStereotype($result->class_stereotype_id, $result->class_stereotype_value, $result->class_stereotype_isocode, $result->class_stereotype_defaultpattern, new SdiNamespace($result->class_stereotype_ns_id, $result->class_stereotype_ns_prefix, $result->class_stereotype_ns_uri)));
                    break;

                case SdiRelation::$ATTRIBUT:
                    $child_attribute = new SdiAttribute($result->attribute_id, $result->attribute_name, $result->attribute_guid, null, new SdiNamespace($result->attribute_ns_id, $result->attribute_ns_prefix, $result->attribute_ns_uri), null, $result->attribute_type_isocode, $result->attribute_codelist);
                    $child_attribute->setStereotype(new SdiStereotype($result->stereotype_id, $result->stereotype_value, $result->stereotype_isocode, $result->stereotype_defaultpattern, new SdiNamespace($result->stereotype_ns_id, $result->stereotype_ns_prefix, $result->stereotype_ns_uri)));
                    $child_attribute->setListeNamespace(new SdiNamespace($result->list_ns_id, $result->list_ns_prefix, $result->list_ns_uri));
                    $child_attribute->pattern = $result->attribute_pattern;
                    $child_attribute->length = $result->attribute_length;
                    $child_attribute->issystem = $result->attribute_issystem;
                    $newRel->setAttribut_child($child_attribute);
                    $newRel->level = $rel->level + 1;
                    break;

                case SdiRelation::$RELATIONTYPE:
                    $resourcetype = new SdiResourcetype($result->resourcetype_id, $result->resourcetype_name, $result->resourcetype_fragment, new SdiNamespace($result->resourcetype_ns_id, $result->resourcetype_ns_prefix, $result->resourcetype_ns_uri));
                    $newRel->setClass_child(new SdiClass($result->classass_id, $result->classass_name, $result->classass_guid, false, new SdiNamespace($result->classass_ns_id, $result->classass_ns_prefix, $result->classass_ns_uri)));
                    $newRel->setResoucetype($resourcetype);
                    $newRel->level = $rel->level + 2;
                    break;
                default:
                    break;
            }

            if (isset($this->csw)) {

                switch ($newRel->childtype_id) {

                    case SdiRelation::$CLASS:
                        $occurance = $this->dce->getCountRelation($newRel);
                        $newRel->occurance = $occurance;
                        if ($occurance == 0) {
                            $newRel->setSerializedXpath($this->dce->getSerializedXpath($newRel));
                            $newRel->isEmpty = true;
                            $relArray[] = $newRel;
                        }

                        for ($i = 0; $i < $occurance; $i++) {
                            $indexedRel = clone $newRel;
                            $indexedRel->setIndex($i);
                            $indexedRel->replaceLastPath($indexedRel);
                            $indexedRel->setSerializedXpath($this->dce->getSerializedXpath($indexedRel));
                            $relArray[] = $indexedRel;
                        }

                        break;
                    case SdiRelation::$ATTRIBUT:
                        $newRel->setIndex($rel->getIndex());
                        $newRel->getAttribut_child()->value = $this->dce->getValue($newRel, $newRel->getIndex());
                        $newRel->setSerializedXpath($this->dce->getSerializedXpath($newRel));
                        $relArray[] = $newRel;
                        break;

                    case SdiRelation::$RELATIONTYPE:
                        $occurance = $this->dce->getCountRelation($newRel);
                        $newRel->occurance = $occurance;
                        if ($occurance == 0) {
                            $newRel->setSerializedXpath($this->dce->getSerializedXpath($newRel));
                            $newRel->isEmpty = true;
                            $relArray[] = $newRel;
                        }

                        for ($i = 0; $i < $occurance; $i++) {
                            $indexedRel = clone $newRel;
                            $indexedRel->setIndex($i);
                            $indexedRel->replaceLastPath($indexedRel);
                            $indexedRel->setSerializedXpath($this->dce->getSerializedXpath($indexedRel));

                            $indexedAttr = clone $indexedRel;
                            $indexedAttr->childtype_id = SdiRelation::$ATTRIBUT;
                            $indexedAttr->rendertype = SdiRelation::$LIST;
                            $indexedAttr->setSerializedXpath($indexedRel->getSerializedXpath() . '_search_0');
                            $attribute = new SdiAttribute($indexedRel->id . '_search_0', $indexedRel->name . '_search_0');
                            $indexedAttr->setAttribut_child($attribute);
                            $indexedAttr->getAttribut_child()->setStereotype(new SdiStereotype(500, 'resource', null, null, null));
                            $indexedAttr->getAttribut_child()->value = $this->dce->getValue($indexedAttr, $indexedAttr->getIndex());


                            $relArray[] = $indexedRel;
                            $relArray[] = $indexedAttr;
                        }
                        break;
                    default:
                        break;
                }
            } else {
                switch ($newRel->childtype_id) {

                    case SdiRelation::$ATTRIBUT:
                        $newRel->getAttribut_child()->value = '';
                        $newRel->setIndex($rel->getIndex());
                        $newRel->setSerializedXpath($this->dce->getSerializedXpath($newRel));

                        if ($rel->childtype_id == SdiRelation::$RELATIONTYPE) {
                            $indexedAttr = clone $rel;
                            $indexedAttr->childtype_id = SdiRelation::$ATTRIBUT;
                            $indexedAttr->rendertype = SdiRelation::$LIST;
                            $indexedAttr->setSerializedXpath($rel->getSerializedXpath() . '_search_0');
                            $attribute = new SdiAttribute($rel->id . '_search_0', $rel->name . '_search_0');
                            $indexedAttr->setAttribut_child($attribute);
                            $indexedAttr->getAttribut_child()->setStereotype(new SdiStereotype(500, 'resource', null, null, null));

                            $relArray[] = $indexedAttr;
                        }

                        $relArray[] = $newRel;
                        break;
                }
            }
        }
        return $relArray;
    }

    /**
     * Returns a form structure in Joomla format
     * 
     * @return string Form structure to Joomla format.
     * @since 4.0
     */
    private function buildForm() {
        $form = $this->dom->createElement('form');
        $form->appendChild($this->getHiddenFields());

        $fieldset = $this->dom->createElement('fieldset');

        foreach ($this->getAttributes() as $attribute) {
            $fieldset->appendChild($this->getFormField($attribute));
        }

        $form->appendChild($fieldset);
        $this->dom->appendChild($form);
        return $this->dom->saveXML();
    }

    /**
     * 
     * @return DOMElement
     */
    private function getHiddenFields() {
        $fieldset = $this->dom->createElement('fieldset');
        $id = $this->dom->createElement('field');
        $id->setAttribute('name', 'id');
        $id->setAttribute('type', 'hidden');
        $id->setAttribute('filter', 'safehtml');

        $guid = $this->dom->createElement('field');
        $guid->setAttribute('name', 'guid');
        $guid->setAttribute('type', 'hidden');
        $guid->setAttribute('filter', 'safehtml');

        $fieldset->appendChild($id);
        $fieldset->appendChild($guid);

        return $fieldset;
    }

    /**
     * Filter the array relations by childtype Attribute
     * 
     * @return SdiRelation[]
     */
    private function getAttributes() {
        $childs = array();

        foreach ($this->relations as $rel) {
            if ($rel->childtype_id == SdiRelation::$ATTRIBUT) {
                $childs[] = $rel;
            }
        }

        return $childs;
    }

    /**
     * Returns a field corresponding to RenderType Joomla format.
     * 
     * @param SdiRelation $rel Current node
     * @return DOMElement Field corresponding to RenderType Joomla format.
     * @since 4.0
     */
    private function getFormField(SdiRelation $rel) {
        $field = $this->dom->createElement('field');

        switch ($rel->rendertype) {
            case SdiRelation::$TEXTBOX:
                return $this->getFormTextBoxField($rel, $field);
                break;
            case SdiRelation::$TEXTAREA:
                return $this->getFormTextAreaField($rel, $field);
                break;
            case SdiRelation::$CHECKBOX:
                return $this->getFormCheckboxField($rel, $field);
                break;
            case SdiRelation::$RADIOBUTTON:
                return $this->getFormRadioButtonField($rel, $field);
                break;
            case SdiRelation::$LIST:
                return $this->getFormListField($rel, $field);
                break;
            case SdiRelation::$DATE:
                return $this->getFormDateField($rel, $field);
                break;
            case SdiRelation::$DATETIME:
                return $this->getFormDateField($rel, $field);
                break;

            default:
                return $field;
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
    private function getFormTextBoxField(SdiRelation $rel, DOMElement $field) {
        $validator = $this->getValidatorClass($rel);

        $field->setAttribute('type', 'text');
        $field->setAttribute('class', $validator);

        if ($rel->getAttribut_child()->length > 0) {
            $field->setAttribute('maxlength', $rel->getAttribut_child()->length);
        }
        if ($rel->getAttribut_child()->issystem) {
            $field->setAttribute('readonly', true);
        }

        if (is_array($rel->getAttribut_child()->value)) {
            $languages = $this->ldao->get();
            $values = $rel->getAttribut_child()->value;
            foreach ($languages as $key => $langValue) {
                if (array_key_exists($key, $values)) {
                    $field->setAttribute('default', $values[$key]);
                }
                $field->setAttribute('name', $rel->getSerializedXpath() . '#' . $key);
                $field->setAttribute('label', EText::_($rel->guid) . ' (' . $languages[$key]->value . ')');
                $field->setAttribute('description', EText::_($rel->guid, 2));
            }
        } else {
            $field->setAttribute('default', $rel->getAttribut_child()->value);
            $field->setAttribute('name', $rel->getSerializedXpath());
            $field->setAttribute('label', EText::_($rel->guid));
            $field->setAttribute('description', EText::_($rel->guid, 2));
        }

        return $field;
    }

    /**
     * Create a field of type textarea.
     * 
     * @param SdiRelation $rel
     * @param DOMElement $field
     * @return DOMElement
     * @since 4.0.0
     */
    private function getFormTextAreaField(SdiRelation $rel, DOMElement $field) {
        $validator = $this->getValidatorClass($rel);

        $field->setAttribute('type', 'textarea');
        $field->setAttribute('class', $validator);
        $field->setAttribute('row', 10);
        $field->setAttribute('cols', 5);

        if ($rel->getAttribut_child()->issystem) {
            $field->setAttribute('readonly', 'true');
        }
        if (is_array($rel->getAttribut_child()->value)) {
            $languages = $this->ldao->get();
            $values = $rel->getAttribut_child()->value;
            foreach ($languages as $key => $langValue) {
                $value = '';
                if (array_key_exists($key, $values)) {
                    $field->setAttribute('default', $values[$key]);
                }
                $field->setAttribute('name', $rel->getSerializedXpath() . '#' . $key);
                $field->setAttribute('label', EText::_($rel->guid) . ' (' . $languages[$key]->value . ')');
                $field->setAttribute('description', EText::_($rel->guid, 2));

                //$field .= '<field name="' . $rel->getSerializedXpath() . '#' . $key . '" type="textarea" class="' . $validator . '" ' . $readonly . ' default="' . $value . '" label="' . EText::_($rel->guid) . ' (' . $languages[$key]->value . ')" description="' . EText::_($rel->guid, 2) . '" rows="10"  cols="5"></field>';
            }
        } else {
            $field->setAttribute('default', $rel->getAttribut_child()->value);
            $field->setAttribute('name', $rel->getSerializedXpath());
            $field->setAttribute('label', EText::_($rel->guid));
            $field->setAttribute('description', EText::_($rel->guid, 2));

            //$field .= '<field name="' . $rel->getSerializedXpath() . '" type="textarea" class="' . $validator . '" ' . $readonly . '  default="' . $rel->getAttribut_child()->value . '" label="' . EText::_($rel->guid) . '" description="' . EText::_($rel->guid, 2) . '" rows="10"  cols="5"></field>';
        }

        return $field;
    }

    /**
     * Create a field of type checkboxes.
     * 
     * @param SdiRelation $rel
     * @return string
     * @since 4.0.0
     */
    private function getFormCheckboxField(SdiRelation $rel, DOMElement $field) {
        $field->setAttribute('type', 'checkboxes');
        $field->setAttribute('name', $rel->getSerializedXpath());
        if ($rel->getAttribut_child()->issystem) {
            $field->setAttribute('readonly', true);
        }

        foreach ($this->getAttributValues($rel) as $value) {
            $option = $this->dom->createElement('option', EText::_($value->guid));
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
    private function getFormRadioButtonField(SdiRelation $rel, DOMElement $field) {
        $field->setAttribute('type', 'radio');
        $field->setAttribute('name', $rel->getSerializedXpath());
        if ($rel->getAttribut_child()->issystem) {
            $field->setAttribute('readonly', true);
        }
        $field->setAttribute('label', EText::_($rel->guid));
        $field->setAttribute('description', EText::_($rel->guid, 2));

        foreach ($this->getAttributValues($rel) as $value) {
            $option = $this->dom->createElement('option', EText::_($value->guid));
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
    private function getFormListField(SdiRelation $rel, DOMElement $field) {
        $validator = $this->getValidatorClass($rel);

        $field->setAttribute('name', $rel->getSerializedXpath());
        $field->setAttribute('class', $validator);
        if ($rel->getAttribut_child()->issystem) {
            $field->setAttribute('readonly', true);
        }
        $field->setAttribute('default', $rel->getAttribut_child()->value);
        $field->setAttribute('description', EText::_($rel->guid, 2));

        foreach ($this->getAttributOptions($rel) as $opt) {
            switch ($rel->getAttribut_child()->getStereotype()->value) {
                case 'localechoice':
                    $group = $this->dom->createElement('group');
                    $group->setAttribute('label', EText::_($opt->guid));

                    $option = $this->dom->createElement('option', EText::_($opt->guid, 2));
                    $option->setAttribute('value', $opt->guid);

                    $field->setAttribute('type', 'groupedlist');
                    $field->setAttribute('label', EText::_($rel->guid));

                    $group->appendChild($option);
                    $field->appendChild($group);
                    break;
                case 'resource':
                    $field->setAttribute('type', 'list');
                    $field->setAttribute('label', 'Name');

                    $option = $this->dom->createElement('option', $opt->name);
                    $option->setAttribute('value', $opt->guid);

                    $field->appendChild($option);
                    break;
                case 'geographicextent':
                    $field->setAttribute('type', 'list');
                    $field->setAttribute('label', EText::_($rel->guid));

                    $option = $this->dom->createElement('option', EText::_($opt->guid));
                    $option->setAttribute('value', $opt->name);

                    $field->appendChild($option);
                    break;
                default:
                    $field->setAttribute('type', 'list');
                    $field->setAttribute('label', EText::_($rel->guid));

                    $option = $this->dom->createElement('option', EText::_($opt->guid));
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
    private function getFormDateField(SdiRelation $rel, DOMElement $field) {
        $validator = $this->getValidatorClass($rel);

        $field->setAttribute('name', $rel->getSerializedXpath());
        $field->setAttribute('type', 'calendar');
        $field->setAttribute('class', $validator);
        $field->setAttribute('format', '%Y-%m-%d');
        $field->setAttribute('label', EText::_($rel->guid));
        $field->setAttribute('description', EText::_($rel->guid, 2));
        if ($rel->getAttribut_child()->value != '') {
            $field->setAttribute('default', substr($rel->getAttribut_child()->value, 0, 10));
        }

        if ($rel->getAttribut_child()->issystem) {
            $field->setAttribute('readonly', true);
        }

        return $field;
    }

    private function getFormDateTimeField(SdiRelation $rel) {
        // not yet implemented
    }

    /**
     * Retrieves the list of options for fields such list, checkbox and radio.
     * 
     * @param SdiRelation $rel Current node
     * @return mixed List of options for list type fields, checkbox and radio.
     * @since 4.0
     */
    private function getAttributOptions(SdiRelation $rel) {
        $query = $this->db->getQuery(true);

        switch ($rel->getAttribut_child()->getStereotype()->value) {
            case 'resource':
                $query->select('id, guid, name');
                $query->from('#__sdi_resource');
                $query->where('resourcetype_id = ' . $rel->getResoucetype()->id);
                $query->order('name ASC');
                break;

            case 'geographicextent':
                $query->select('id, guid, name');
                $query->from('#__sdi_boundary');
                $query->order('name ASC');
                break;

            default:
                $query->select('id, guid, `name`, `value`');
                $query->from('#__sdi_attributevalue');
                $query->where('attribute_id = ' . $rel->getAttribut_child()->id);
                $query->where('state = 1');
                $query->order('ordering ASC');
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

}

?>

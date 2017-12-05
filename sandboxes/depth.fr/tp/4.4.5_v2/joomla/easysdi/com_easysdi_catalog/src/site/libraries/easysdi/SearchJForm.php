<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/common/EText.php';

require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/enum/EnumCriteriaType.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/enum/EnumRenderType.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/SearchForm.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/dao/SdiLanguageDao.php';

class SearchJForm extends SearchForm {

    /** @var SdiLanguageDao  */
    private $ldao;

    function __construct($item) {
        parent::__construct();

        $this->item = $item;

        $this->dom->appendChild($this->dom->createElement('form'));
        $this->ldao = new SdiLanguageDao();

        $this->simple->setAttribute('addfieldpath', JPATH_COMPONENT . '/models/fields');
        $this->advanced->setAttribute('addfieldpath', JPATH_COMPONENT . '/models/fields');
        $this->hidden->setAttribute('addfieldpath', JPATH_COMPONENT . '/models/fields');
    }

    public function getForm() {

        $this->buildForm();

        foreach ($this->buildHiddenField() as $field) {
            $this->hidden->appendChild($field);
        }

        if ($this->simple->hasChildNodes()) {
            $this->dom->firstChild->appendChild($this->simple);
        }
        if ($this->advanced->hasChildNodes()) {
            $this->dom->firstChild->appendChild($this->advanced);
        }
        if ($this->hidden->hasChildNodes()) {
            $this->dom->firstChild->appendChild($this->hidden);
        }

        $this->dom->formatOutput = true;
        $form = $this->dom->saveXML();

        return $form;
    }

    private function buildHiddenField() {
        $fields = array();

        $id = $this->dom->createElement('field');
        $id->setAttribute('type', 'hidden');
        $id->setAttribute('name', 'id');

        $fields[] = $id;

        return $fields;
    }

    /**
     * Build the Joomla Form
     */
    private function buildForm() {

        $field = null;

        foreach ($this->loadSystemFields() as $searchCriteria) {
            if ($searchCriteria->id == 654) {
                $breakpoint = TRUE;
            }

            if (empty($searchCriteria->rendertype_id)) {
                $searchCriteria->rendertype_id = $searchCriteria->rel_rendertype_id;
            }

            switch ($searchCriteria->rendertype_id) {
                case EnumRendertype::$TEXTBOX:
                    $field = $this->getFormTextBoxField($searchCriteria);
                    break;
                case EnumRendertype::$TEXTAREA:
                    $field = $this->getFormTextAreaField($searchCriteria);
                    break;
                case EnumRendertype::$CHECKBOX:
                    switch ($searchCriteria->name) {
                        case 'resourcetype':
                            $field = $this->getFormCheckboxesField($searchCriteria);
                            break;
                        default :
                            $field = $this->getFormCheckboxField($searchCriteria);
                            break;
                    }
                    break;
                case EnumRendertype::$LIST:
                    $field = $this->getFormListField($searchCriteria);
                    break;
                case EnumRendertype::$DATE:
                    $field = $this->getFormDateRangeField($searchCriteria);
                    break;
                case EnumRendertype::$RADIOBUTTON:
                    $field = $this->getFormRadioField($searchCriteria);
                    break;
            }

            if (isset($field)) {
                $this->fieldsetDispatch($field, $searchCriteria);
            }
        }
    }

    private function fieldsetDispatch($field, $searchCriteria) {

        switch ($searchCriteria->searchtab_id) {
            case self::SIMPLE:
                $this->addFieldToFieldset($field, $this->simple);
                break;
            case self::ADVANCED:
                $this->addFieldToFieldset($field, $this->advanced);
                break;
            case self::HIDDEN:
                $this->addFieldToFieldset($field, $this->hidden);
                break;
            default:
                break;
        }
    }

    private function addFieldToFieldset($field, DOMElement $fieldset) {
        if (is_array($field)) {
            foreach ($field as $f) {
                $fieldset->appendChild($f);
            }
        } else {
            $fieldset->appendChild($field);
        }
    }

    private function getFormTextBoxField($searchCriteria) {
        $field = $this->dom->createElement('field');
        $field->setAttribute('type', 'text');

        $name = $this->getName($searchCriteria);

        $field->setAttribute('label', $this->getLabel($searchCriteria));
        $field->setAttribute('name', $name);
        $field->setAttribute('default', $this->getDefault($searchCriteria, $name));
        return $field;
    }

    private function getFormTextAreaField($searchCriteria) {
        $field = $this->dom->createElement('field');
        $field->setAttribute('type', 'textarea');

        $name = $this->getName($searchCriteria);

        $field->setAttribute('label', $this->getLabel($searchCriteria));
        $field->setAttribute('name', $name);
        $field->setAttribute('default', $this->getDefault($searchCriteria, $name));
        return $field;
    }

    private function getFormCheckboxField($searchCriteria) {
        $field = $this->dom->createElement('field');
        $field->setAttribute('type', 'checkbox');
        $field->setAttribute('value', 1);

        $name = $this->getName($searchCriteria);

        $field->setAttribute('label', $this->getLabel($searchCriteria));
        $field->setAttribute('name', $name);
        $field->setAttribute('default', $this->getDefault($searchCriteria, $name));
        
        //New behaviour with default values in Joomla! 3.7.x : https://github.com/joomla/joomla-cms/commit/97e4048357481d3747c2e19e4f31ab89824dfaa8
        //Force the checked attribute if value==default
        if($field->getAttribute('default') == $field->getAttribute('value')){
            $field->setAttribute('checked', 'checked');
        }

        return $field;
    }

    private function getFormCheckboxesField($searchCriteria) {
        $field = $this->dom->createElement('field');
        $field->setAttribute('type', 'inlineCheckboxes');


        $name = $this->getName($searchCriteria);

        $field->setAttribute('label', $this->getLabel($searchCriteria));
        $field->setAttribute('name', $name);
        $field->setAttribute('default', $this->getDefault($searchCriteria, $name));

        foreach ($this->getAttributOptions($searchCriteria) as $opt) {
            $option = $this->dom->createElement('option', EText::_($opt->guid));
            $option->setAttribute('value', $opt->value);
            if ($searchCriteria->name == 'resourcetype') {
                $option->setAttribute('class', 'cbx-resourcetype');
            }

            $field->appendChild($option);
        }

        return $field;
    }

    private function getFormRadioField($searchCriteria) {
        $field = $this->dom->createElement('field');
        $field->setAttribute('type', 'radio');
        $name = $this->getName($searchCriteria);

        $field->setAttribute('label', $this->getLabel($searchCriteria));
        $field->setAttribute('name', $name);
        $field->setAttribute('default', $this->getDefault($searchCriteria, $name));

        foreach ($this->getAttributOptions($searchCriteria) as $opt) {
            $option = $this->dom->createElement('option', EText::_($opt->guid, 1, JText::_($opt->name)));
            $option->setAttribute('value', $opt->value);

            $field->appendChild($option);
        }

        return $field;
    }

    private function getFormListField($searchCriteria) {
        $field = $this->dom->createElement('field');
        $field->setAttribute('type', 'multipleDefaultList');
        switch ($searchCriteria->name) {
            case 'organism':
                $field->setAttribute('multiple', 'true');
                break;
            default:
                $field->setAttribute('multiple', 'true');
                break;
        }

        $name = $this->getName($searchCriteria);

        $field->setAttribute('label', $this->getLabel($searchCriteria));
        $field->setAttribute('name', $name);
        $default = explode(',', $this->getDefault($searchCriteria, $name));
        $field->setAttribute('default', $this->getDefault($searchCriteria, $name));

        $field->appendChild($this->dom->createElement('option'));
        foreach ($this->getAttributOptions($searchCriteria) as $opt) {
            $option = $this->dom->createElement('option');
            $optionTxt = $this->dom->createTextNode(EText::_($opt->guid, 1, $opt->name));
            $option->appendChild($optionTxt);
            $option->setAttribute('value', $opt->value);

            $field->appendChild($option);
        }

        return $field;
    }

    private function getFormDateRangeField($searchCriteria) {

        $name = $this->getName($searchCriteria);

        $field = $this->dom->createElement('field');
        $field->setAttribute('type', 'fromtocalendar');
        if (isset($searchCriteria->relation_guid)) {
            $field->setAttribute('label', EText::_($searchCriteria->relation_guid));
        } else {
            $field->setAttribute('label', EText::_($searchCriteria->guid));
        }
        $field->setAttribute('name', $name);
        $field->setAttribute('format', '%Y-%m-%d');

        $field->setAttribute('default', $this->getDefault($searchCriteria, $name));

        return $field;
    }

    private function getName($searchCriteria) {
        switch ($searchCriteria->criteriatype_id) {
            case CriteriaType::System:
                return $searchCriteria->id . '_' . $searchCriteria->name;
            default:
                return $searchCriteria->id . '_' . $this->getOgcSearchFilter($searchCriteria);
        }
    }

    private function getLabel($searchCriteria) {
        if (isset($searchCriteria->relation_guid)) {
            return EText::_($searchCriteria->catalogsearchcriteriaguid);
        } else {
            return EText::_($searchCriteria->guid);
        }
    }

    private function getDefault($searchCriteria, $name) {
        $isSearch = JFactory::getApplication()->input->get('search', false, 'boolean');
        if ($isSearch) {
            return $this->getDefaultFromSession($name);
        } else {
            if (isset($searchCriteria->defaultvalue)) {
                //If rendertype is text, checkbox or radiobutton, default value is 
                //to get from the field 'defaultvalue'
                if ($searchCriteria->rendertype_id == 1 || $searchCriteria->rendertype_id == 5 || $searchCriteria->rendertype_id == 2 || $searchCriteria->rendertype_id == 3) {
                    return $searchCriteria->defaultvalue;
                }
                //Otherwise, default value is to query against the attributevalue table
                return $this->getJsonDefaultValue($searchCriteria, $searchCriteria->defaultvalue);
            } elseif (isset($searchCriteria->defaultvaluefrom)) {
                return $searchCriteria->defaultvaluefrom . ',' . $searchCriteria->defaultvalueto;
            }
        }
    }

    private function getDefaultFromSession($name) {
        if (key_exists($name, $this->data)) {
            if (is_array($this->data[$name])) {
                return implode(',', array_filter($this->data[$name]));
            } else {
                return $this->data[$name];
            }
        } else {
            return '';
        }
    }

    private function getAttributOptions($searchCriteria) {
        $query = $this->db->getQuery(true);

        switch ($searchCriteria->name) {
            case 'organism':
                $sdiUser = sdiFactory::getSdiUser();

                $query->select('DISTINCT o.id, o.guid, o.guid as value, o.name');
                $query->from('#__sdi_organism o');
                $query->innerJoin('#__sdi_resource r on o.id = r.organism_id');
                $query->innerJoin('#__sdi_resourcetype rt on r.resourcetype_id = rt.id');
                $query->innerJoin('#__sdi_catalog_resourcetype crt ON crt.resourcetype_id = rt.id');
                $query->where('crt.catalog_id = ' . $this->item->id);
                $query->order('o.name');

                //apply resource's accessscope -> avoid to show orgnaisms without public resources
                if ($sdiUser->isEasySDI) {

                    //user's organism's categories
                    $categories = $sdiUser->getMemberOrganismsCategoriesIds();
                    if (is_null($categories) || count($categories) == 0) {
                        $categories = array(0);
                    }

                    //user's organism
                    $organisms = $sdiUser->getMemberOrganisms();

                    $query->where("("
                            . "r.accessscope_id = 1 "
                            . "OR (r.accessscope_id = 2 AND (SELECT COUNT(*) FROM #__sdi_accessscope a WHERE a.category_id IN (" . implode(',', $categories) . ") AND a.entity_guid = r.guid ) > 0) "
                            . "OR (r.accessscope_id = 3 AND (SELECT COUNT(*) FROM #__sdi_accessscope a WHERE a.organism_id = " . (int) $organisms[0]->id . " AND a.entity_guid = r.guid ) = 1) "
                            . "OR (r.accessscope_id = 4 AND (SELECT COUNT(*) FROM #__sdi_accessscope a WHERE a.user_id = " . (int) $sdiUser->id . " AND a.entity_guid = r.guid ) = 1)"
                            . ")"
                    );
                } else {
                    $query->where("r.accessscope_id = 1 ");
                }

                break;
            case 'definedBoundary':

                $language = $this->ldao->getByCode(JFactory::getLanguage()->getTag());

                $params = json_decode($searchCriteria->params);
                if (!empty($params->searchboundarytype) && ($params->searchboundarytype == parent::SEARCHTYPEID)) {
                    $query->select('b.alias as value, t.text1 as name, b.guid');
                } else {
                    $query->select($query->concatenate(array('b.northbound', 'b.southbound', 'b.eastbound', 'b.westbound'), '#') . ' as value, t.text1 as name, b.guid');
                }

                $query->from('#__sdi_boundary b');
                $query->innerJoin('#__sdi_translation t on b.guid = t.element_guid');
                $query->where('t.language_id = ' . (int) $language->id);
                if (!empty($params->boundarycategory_id)) {
                    $query->where('b.category_id IN (' . implode(',', $params->boundarycategory_id) . ')');
                }
                $query->where('b.state = 1');
                $query->order('t.text1');
                break;
            case 'resourcetype':
                $query->select('t.id, t.alias as value, t.guid, t.name');
                $query->from('#__sdi_resourcetype t');
                $query->innerJoin('#__sdi_catalog_resourcetype crt ON crt.resourcetype_id = t.id');
                $query->where('crt.catalog_id = ' . $this->item->id);
                break;
            case 'versions':
                $options = array();
                $options[] = (object) array('value' => '1', 'guid' => '', 'name' => 'LAST');
                $options[] = (object) array('value' => '0', 'guid' => '', 'name' => 'ALL');
                return $options;
                break;
            default :
                $query->select('t.id, t.value, t.guid, t.name');
                $query->from('#__sdi_attributevalue t');
                $query->where('t.attribute_id = ' . $searchCriteria->attributechild_id);
                $query->order('t.ordering');
                break;
        }

        $this->db->setQuery($query);
        $options = $this->db->loadObjectList();

        return $options;
    }

    /**
     * 
     * @param stdClass $searchCriteria
     */
    private function getOgcSearchFilter($searchCriteria) {
        $language = $this->ldao->getByCode(JFactory::getLanguage()->getTag());

        $query = $this->db->getQuery(true);

        $query->select('scf.ogcsearchfilter');
        $query->from('#__sdi_searchcriteriafilter scf');
        $query->where('scf.searchcriteria_id = ' . (int) $searchCriteria->id);
        $query->where('scf.language_id = ' . (int) $language->id);

        $this->db->setQuery($query);
        $filter = $this->db->loadObject();

        return isset($filter) ? $filter->ogcsearchfilter : null;
    }

    /**
     * 
     * @param string $json
     * @return string
     */
    private function getJsonDefaultValue($searchCriteria, $json) {
        if (empty($json) && $json != '0') {
            return '';
        }
        $decode = json_decode($json, true);

        if (is_array($decode)) {
            $ids = implode(',', array_filter($decode));
        } else {
            $ids = $json;
        }

        switch ($searchCriteria->name) {
            case 'organism':
                if (empty($ids)) {
                    break;
                }
                $query = $this->db->getQuery(true);
                $query->select('guid as value');
                $query->from('#__sdi_organism');
                $query->where('id IN (' . $ids . ')');
                break;
            case 'resourcetype':
                if (empty($ids)) {
                    break;
                }
                $query = $this->db->getQuery(true);
                $query->select('alias as value');
                $query->from('#__sdi_resourcetype');
                $query->where('id IN (' . $ids . ')');
                break;
            case 'definedBoundary':
                if (empty($ids)) {
                    break;
                }
                $query = $this->db->getQuery(true);
                $params = json_decode($searchCriteria->params);
                if ($params->searchboundarytype == parent::SEARCHTYPEID) {
                    $query->select('b.alias as value');
                } else {
                    $query->select($query->concatenate(array('b.northbound', 'b.southbound', 'b.eastbound', 'b.westbound'), '#') . ' as value');
                }

                $query->from('#__sdi_boundary as b');
                $query->where('id IN (' . $ids . ')');
                break;
            case 'versions':
                $alias = 'versions';
                $query = $this->db->getQuery(true);
                $query->select('csc.defaultvalue as value');
                $query->from('#__sdi_searchcriteria sc');
                $query->innerJoin('#__sdi_catalog_searchcriteria csc on csc.searchcriteria_id = sc.id');
                $query->where('sc.alias =' . $query->quote($alias));
                $query->where('csc.catalog_id = ' . (int) $this->item->id);
                break;
            default :
                if (empty($ids)) {
                    break;
                }
                $query = $this->db->getQuery(true);
                $query->select('name as value');
                $query->from('#__sdi_attributevalue');
                $query->where('id IN (' . $ids . ')');
                break;
        }

        if (isset($query)) {
            $this->db->setQuery($query);
            $results = $this->db->loadObjectList();
            $defaults = array();
            foreach ($results as $result) {
                $defaults[] = $result->value;
            }

            return implode(',', $defaults);
        } else {
            return '';
        }
    }

}

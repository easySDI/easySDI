<?php

require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/common/EText.php';

require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/enum/EnumCriteriaType.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/enum/EnumRenderType.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/SearchForm.php';

/**
 * 
 *
 * @author Marc Battaglia <marc.battaglia@depth.ch>
 */
class SearchJForm extends SearchForm {

    function __construct($item) {
        parent::__construct();

        $this->item = $item;

        $this->dom->appendChild($this->dom->createElement('form'));

        $this->simple->setAttribute('addfieldpath', JPATH_COMPONENT . '/models/fields');
        $this->advanced->setAttribute('addfieldpath', JPATH_COMPONENT . '/models/fields');
        $this->hidden->setAttribute('addfieldpath', JPATH_COMPONENT . '/models/fields');

    }

    public function getForm() {

        $this->buildForm();

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

    /**
     * Build the Joomla Form
     */
    private function buildForm() {

        $field = null;

        foreach ($this->loadSystemFields() as $searchCriteria) {
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
        $field->setAttribute('name', $searchCriteria->name);
        $field->setAttribute('type', 'text');
        $field->setAttribute('label', EText::_($searchCriteria->guid));
        $field->setAttribute('default', $searchCriteria->defaultvalue);

        return $field;
    }

    private function getFormTextAreaField($searchCriteria) {
        $field = $this->dom->createElement('field');
        $field->setAttribute('name', $searchCriteria->name);
        $field->setAttribute('type', 'textarea');
        $field->setAttribute('label', EText::_($searchCriteria->guid));
        $field->setAttribute('default', $searchCriteria->defaultvalue);

        return $field;
    }

    private function getFormCheckboxField($searchCriteria) {
        $field = $this->dom->createElement('field');
        $field->setAttribute('name', $searchCriteria->name);
        $field->setAttribute('type', 'checkbox');
        $field->setAttribute('label', EText::_($searchCriteria->guid));
        $field->setAttribute('value', 1);
        $field->setAttribute('default', $searchCriteria->defaultvalue);

        return $field;
    }

    private function getFormCheckboxesField($searchCriteria) {
        $field = $this->dom->createElement('field');
        $field->setAttribute('name', $searchCriteria->name);
        $field->setAttribute('type', 'inlineCheckboxes');
        $field->setAttribute('label', EText::_($searchCriteria->guid));
        $field->setAttribute('default', $this->getJsonDefaultValue($searchCriteria->defaultvalue));

        foreach ($this->getAttributOptions($searchCriteria) as $opt) {
            $option = $this->dom->createElement('option', EText::_($opt->guid));
            $option->setAttribute('value', $opt->value);

            $field->appendChild($option);
        }

        return $field;
    }

    private function getFormRadioField($searchCriteria) {
        $field = $this->dom->createElement('field');
        $field->setAttribute('name', $searchCriteria->name);
        $field->setAttribute('type', 'radio');
        $field->setAttribute('label', EText::_($searchCriteria->guid));
        $field->setAttribute('default', $searchCriteria->defaultvalue);

        foreach ($this->getAttributOptions($searchCriteria) as $opt) {
            $option = $this->dom->createElement('option', EText::_($opt->guid, 1, JText::_($opt->name)));
            $option->setAttribute('value', $opt->value);

            $field->appendChild($option);
        }

        return $field;
    }

    private function getFormListField($searchCriteria) {
        $field = $this->dom->createElement('field');
        $field->setAttribute('name', $searchCriteria->name);
        $field->setAttribute('type', 'list');
        $field->setAttribute('label', EText::_($searchCriteria->guid));
        $field->setAttribute('default', $searchCriteria->defaultvalue);

        $field->appendChild($this->dom->createElement('option'));
        foreach ($this->getAttributOptions($searchCriteria) as $opt) {
            $option = $this->dom->createElement('option', EText::_($opt->guid, 1, $opt->name));
            $option->setAttribute('value', $opt->value);

            $field->appendChild($option);
        }

        return $field;
    }

    private function getFormDateRangeField($searchCriteria) {

        $field = $this->dom->createElement('field');
        $field->setAttribute('name', $searchCriteria->name);
        $field->setAttribute('type', 'fromtocalendar');
        $field->setAttribute('label', EText::_($searchCriteria->guid));
        $field->setAttribute('format', 'Y-m-d');

        $range = array($searchCriteria->defaultvaluefrom, $searchCriteria->defaultvalueto);
        $field->setAttribute('default', implode(',', $range));

        return $field;
    }
    

    private function getAttributOptions($searchCriteria) {
        $query = $this->db->getQuery(true);
        
        switch ($searchCriteria->name) {
            case 'organism':
                $query->select('t.id, t.guid, t.guid as value, t.name');
                $query->from('#__sdi_organism t');
                break;
            case 'definedBoundary':
                $query->select('t.id, t.guid, t.guid as value, t.name');
                $query->from('#__sdi_boundary t');
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
                echo '';
                break;
        }

        $this->db->setQuery($query);
        $options = $this->db->loadObjectList();

        return $options;
    }

    /**
     * 
     * @param string $json
     * @return string
     */
    private function getJsonDefaultValue($json) {
        if (empty($json)) {
            return '';
        }
        $decode = json_decode($json, true);

        if (is_array($decode)) {
            return implode(',', $decode);
        } else {
            return $decode;
        }
    }

}

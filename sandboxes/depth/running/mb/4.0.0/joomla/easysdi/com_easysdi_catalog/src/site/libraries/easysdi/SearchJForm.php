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
    
    /** @var DOMElement */
    private $hidden;
    
    function __construct() {
        parent::__construct();
        
        $this->dom->appendChild($this->dom->createElement('form'));
        
        $this->hidden = $this->dom->createElement('fieldset');
        $this->hidden->setAttribute('name', 'hidden');
    }

    public function getForm() {

        $this->buildForm();
        
        if($this->simple->hasChildNodes()){
            $this->dom->firstChild->appendChild($this->simple);
        }
        if($this->advanced->hasChildNodes()){
            $this->dom->firstChild->appendChild($this->advanced);
        }
        if($this->hidden->hasChildNodes()){
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
            
            if(isset($field)){
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

        return $field;
    }

    private function getFormCheckboxField($searchCriteria) {
        $field = $this->dom->createElement('field');
        $field->setAttribute('name', $searchCriteria->name);
        $field->setAttribute('type', 'checkbox');
        $field->setAttribute('label', EText::_($searchCriteria->guid));
        $field->setAttribute('value', 1);

        return $field;
    }

    private function getFormCheckboxesField($searchCriteria) {
        $field = $this->dom->createElement('field');
        $field->setAttribute('name', $searchCriteria->name);
        $field->setAttribute('type', 'checkboxes');
        $field->setAttribute('label', EText::_($searchCriteria->guid));

        foreach ($this->getAttributOptions($searchCriteria) as $opt) {
            $option = $this->dom->createElement('option', EText::_($opt->guid));
            $option->setAttribute('value', $opt->id);

            $field->appendChild($option);
        }

        return $field;
    }

    private function getFormRadioField($searchCriteria) {
        $field = $this->dom->createElement('field');
        $field->setAttribute('name', $searchCriteria->name);
        $field->setAttribute('type', 'radio');
        $field->setAttribute('label', EText::_($searchCriteria->guid));

        foreach ($this->getAttributOptions($searchCriteria) as $opt) {
            $option = $this->dom->createElement('option', EText::_($opt->guid));
            $option->setAttribute('value', $opt->id);

            $field->appendChild($option);
        }

        return $field;
    }

    private function getFormListField($searchCriteria) {
        $field = $this->dom->createElement('field');
        $field->setAttribute('name', $searchCriteria->name);
        $field->setAttribute('type', 'list');
        $field->setAttribute('label', EText::_($searchCriteria->guid));

        foreach ($this->getAttributOptions($searchCriteria) as $opt) {
            $option = $this->dom->createElement('option', EText::_($opt->guid));
            $option->setAttribute('value', $opt->id);

            $field->appendChild($option);
        }

        return $field;
    }

    private function getFormDateRangeField($searchCriteria) {
        $fields = array();

        $from = $this->dom->createElement('field');
        $from->setAttribute('name', 'from' . $searchCriteria->name);
        $from->setAttribute('type', 'calendar');
        $from->setAttribute('label', 'from');

        $to = $this->dom->createElement('field');
        $to->setAttribute('name', 'to' . $searchCriteria->name);
        $to->setAttribute('type', 'calendar');
        $to->setAttribute('label', 'to');

        $fields[] = $from;
        $fields[] = $to;

        return $fields;
    }

    private function getAttributOptions($searchCriteria) {
        $query = $this->db->getQuery(true);
        $query->select('id, guid, name');

        switch ($searchCriteria->name) {
            case 'organism':
                $query->from('#__sdi_organism');
                break;
            case 'definedBoundary':
                $query->from('#__sdi_boundary');
                break;
            case 'resourcetype':
                $query->from('#__sdi_resourcetype');
                break;
            case 'versions':
                $options = array();
                $options[] = (object) array('id' => 'last', 'guid' => '', 'name' => 'last');
                $options[] = (object) array('id' => 'all', 'guid' => '', 'name' => 'all');
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

}

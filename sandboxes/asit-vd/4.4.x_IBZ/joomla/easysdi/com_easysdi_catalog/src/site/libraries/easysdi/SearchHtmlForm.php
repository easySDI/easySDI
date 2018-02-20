<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */


require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/SearchForm.php';

class SearchHtmlForm extends SearchForm {

    /** @var JForm */
    private $jform;

    function __construct(JForm $jform) {
        parent::__construct();

        $this->jform = $jform;
        $this->advanced->setAttribute('style', 'display:none');
        $this->hidden->setAttribute('style', 'display:none');
    }

    public function getForm() {
        $this->buildForm();

        $this->dom->appendChild($this->hidden);

        if ($this->simple->hasChildNodes()) {
            $this->dom->appendChild($this->simple);
        }
        if ($this->advanced->hasChildNodes()) {
            $this->dom->appendChild($this->advanced);
        }

        $this->dom->formatOutput = true;
        $form = $this->dom->saveHTML();

        return $form;
    }

    private function buildForm() {
        foreach ($this->jform->getFieldset('hidden') as $field) {
            $this->hidden->appendChild($this->buildAttribute($field));
        }

        foreach ($this->jform->getFieldset('simple') as $field) {
            $this->simple->appendChild($this->buildAttribute($field));
        }

        foreach ($this->jform->getFieldset('advanced') as $field) {
            $this->advanced->appendChild($this->buildAttribute($field));
        }
    }

    private function buildAttribute($field) {
        $controlGroup = $this->dom->createElement('div');
        $controlGroup->setAttribute('class', 'control-group');

        $controlLabel = $this->dom->createElement('div');
        $controlLabel->setAttribute('class', 'control-label');

        $control = $this->dom->createElement('div');
        $control->setAttribute('class', 'controls');

        $controlLabel->appendChild($this->getLabel($field));
        $control->appendChild($this->getInput($field));

        $controlGroup->appendChild($controlLabel);
        $controlGroup->appendChild($control);

        return $controlGroup;
    }

    /**
     * Import the HTML structure of the label in a DOMElement.
     * 
     * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
     * @since 4.0
     * 
     * @param JField $field The Joomla JField
     * @return DOMElement
     */
    private function getLabel($field) {
        $labelString = $field->label;
        if(empty($labelString)){
            return $this->dom->createElement('span');
        }
        $domlocal = new DOMDocument();
        if (isset($labelString))            
            $domlocal->loadHTML($this->convert($labelString));

        $domXapth = new DOMXPath($domlocal);
        $label = $domXapth->query('/*/*/*')->item(0);

        $cloned = $label->cloneNode(TRUE);

        $imported = $this->dom->importNode($cloned, TRUE);

        return $imported;
    }

    /**
     * Import HTML structure of input field in a DOMElement.
     * 
     * @param JField $field The Joomla JField.
     * @return DOMElement
     */
    private function getInput($field) {
        $domlocal = new DOMDocument();
        $domlocal->loadHTML($this->convert($field->input));

        $domXapth = new DOMXPath($domlocal);
        $input = $domXapth->query('/*/*/*')->item(0);

        $cloned = $input->cloneNode(TRUE);

        $imported = $this->dom->importNode($cloned, TRUE);

        return $imported;
    }

    /**
     * Encode special characters into HTML entities. Unless the <> characters.
     * 
     * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
     * @since 4.0
     * 
     * @param string $text
     * @return string
     */
    private function convert($text) {
        $text = htmlentities($text, ENT_NOQUOTES, "UTF-8");
        $text = htmlspecialchars_decode($text);
        return $text;
    }

}

<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FormHtmlGenerator
 *
 * @author Administrator
 */
class FormHtmlGenerator {

    /**
     *
     * @var JForm 
     */
    private $form;

    /**
     *
     * @var SdiRelation[] 
     */
    private $classTree;

    /**
     *
     * @var SdiLanguageDao
     */
    private $ldao;

    /**
     *
     * @var DOMDocument 
     */
    private $dom;

    /**
     *
     * @var DOMElement[] 
     */
    private $domElements;

    function __construct(JForm $form, $classTree) {
        $this->form = $form;
        $this->classTree = $classTree;
        $this->ldao = new SdiLanguageDao();
        $this->dom = new DOMDocument(null, 'utf-8');
    }


    public function buildForm() {
        $this->getDomElements();

        $root = current($this->domElements);

        foreach ($this->domElements as $key => $element) {
            $reverseIndex = 2;
            if (array_key_exists($key, $this->classTree)) {
                $rel = $this->classTree[$key];
                switch ($rel->childtype_id) {
                    case SdiRelation::$RELATIONTYPE:
                        $reverseIndex = 1;
                        break;
                    case SdiRelation::$CLASS:
                        $reverseIndex = 2;
                        break;
                    case SdiRelation::$ATTRIBUT:
                        $reverseIndex = 2;
                        break;
                }
            } else {
                $reverseIndex = 1;
            }

            if ($this->subXpath($key, $reverseIndex)) {
                if (array_key_exists($this->subXpath($key, $reverseIndex), $this->domElements)) {
                    $parent = $this->domElements[$this->subXpath($key, $reverseIndex)];

                    $divInner = $parent->getElementsByTagName('div')->item(0);

                    if (!isset($divInner)) {
                        $divInner = $parent;
                    }

                    if ($rel->getIndex() == 0 && ($rel->childtype_id == SdiRelation::$CLASS || $rel->childtype_id == SdiRelation::$RELATIONTYPE)) {
                        $divInner->appendChild($this->getAction($rel));
                    }
                    $divInner->appendChild($element);
                }
            }
        }

        $this->dom->appendChild($root);

        $html = $this->dom->saveHTML();

        return $html;
    }

    /**
     * Build a array of DomElement using the same key for the array of Relation.
     */
    private function getDomElements() {
        foreach ($this->classTree as $key => $rel) {
            switch ($rel->childtype_id) {
                case SdiRelation::$RELATIONTYPE:
                case SdiRelation::$CLASS:
                    $this->domElements[$key] = $this->getFieldset($rel);
                    break;
                case SdiRelation::$ATTRIBUT:
                    $this->domElements[$key] = $this->getAttribute($rel);
                    break;
            }
        }
    }

    /**
     * Built on the action bar to add new relation instance.
     * 
     * @param SdiRelation $rel
     * @return DOMElement
     */
    private function getAction(SdiRelation $rel) {
        $aAdd = $this->dom->createElement('a');
        $aAdd->setAttribute('id', 'add-btn-' . $rel->getSerializedXpath());
        $aAdd->setAttribute('class', 'btn btn-success btn-mini add-btn');
        $aAdd->setAttribute('onclick', 'addFieldset(this.id, \'' . $this->subXpath($rel->getSerializedXpath(), 2) . '\' ,' . $rel->lowerbound . ',' . $rel->upperbound . ')');
        if ($rel->upperbound <= $rel->occurance) {
            $aAdd->setAttribute('style', 'display:none;');
        }

        $iAdd = $this->dom->createElement('i');
        $iAdd->setAttribute('class', 'icon-white icon-plus-2');

        $divOuter = $this->dom->createElement('div');
        $divOuter->setAttribute('id', 'outer-fds-' . $rel->getSerializedXpath());
        $divOuter->setAttribute('class', 'outer-' . $rel->level . ' outer-fds-' . $rel->getSerializedXpath(false));

        $divAction = $this->dom->createElement('div', EText::_($rel->guid));
        $divAction->setAttribute('class', 'action-' . $rel->level);

        $aAdd->appendChild($iAdd);
        $divAction->appendChild($aAdd);

        return $divAction;
    }

    /**
     * Constructs a fieldset corresponding to a Class.
     * 
     * @param SdiRelation $rel
     * @return DOMElement
     */
    private function getFieldset(SdiRelation $rel) {

        $aCollapse = $this->dom->createElement('a');
        $aCollapse->setAttribute('id', 'collapse-btn-' . $rel->getSerializedXpath());
        $aCollapse->setAttribute('class', 'btn btn-mini collapse-btn');
        $aCollapse->setAttribute('onclick', 'collapse(this.id)');

        $iCollapse = $this->dom->createElement('i');
        $iCollapse->setAttribute('class', 'icon-white icon-arrow-down');

        $aRemove = $this->dom->createElement('a');
        $aRemove->setAttribute('id', 'remove-btn-' . $rel->getSerializedXpath());
        $aRemove->setAttribute('class', 'btn btn-danger btn-mini pull-right');
        $aRemove->setAttribute('onclick', 'confirm(this.id, \'' . $rel->getSerializedXpath() . '\' , ' . $rel->lowerbound . ',' . $rel->upperbound . ')');

        $iRemove = $this->dom->createElement('i');
        $iRemove->setAttribute('class', 'icon-white icon-cancel-2');

        $divOuter = $this->dom->createElement('div');
        $divOuter->setAttribute('id', 'outer-fds-' . $rel->getSerializedXpath());
        $divOuter->setAttribute('class', 'outer-' . $rel->level . ' outer-fds-' . $this->subXpath($rel->getSerializedXpath(), 2));

        $fieldset = $this->dom->createElement('fieldset');
        $fieldset->setAttribute('id', 'fds-' . $rel->getSerializedXpath());

        $spanLegend = $this->dom->createElement('span', EText::_($rel->guid));
        $spanLegend->setAttribute('class', 'legend-' . $rel->level);
        $legend = $this->dom->createElement('legend');

        $divInner = $this->dom->createElement('div');
        $divInner->setAttribute('id', 'inner-fds-' . $rel->getSerializedXpath());
        $divInner->setAttribute('class', 'inner-fds');
        if (!isset($_GET['uuid'])) {
            $divInner->setAttribute('style', 'display:none;');
        }
        
        $divBottom = $this->dom->createElement('div');
        $divBottom->setAttribute('id', 'bottom-'.$this->subXpath($rel->getSerializedXpath(), 2));

        if (!$rel->getClass_child()->isRoot) {
            $aCollapse->appendChild($iCollapse);
            $legend->appendChild($aCollapse);
            $legend->appendChild($spanLegend);
            if ($rel->lowerbound < $rel->occurance) {
                $aRemove->appendChild($iRemove);
                $legend->appendChild($aRemove);
            }
            $fieldset->appendChild($legend);
            $fieldset->appendChild($divInner);
        }
        $divOuter->appendChild($fieldset);
        if($rel->getIndex() == $rel->occurance-1){
            $divOuter->appendChild($divBottom);
        }

        return $divOuter;
    }

    /**
     * Convert a string attribute in DomElement.
     * 
     * @param SdiRelation $rel
     * @return DOMElement
     */
    private function getAttribute(SdiRelation $rel) {
        $source = $this->getFieldsetBody($rel);

        $domlocal = new DOMDocument();
        $domlocal->substituteEntities = false;
        $domlocal->loadHTML($this->convert($source));
        $element = $domlocal->getElementsByTagName('div')->item(0);

        $cloned = $element->cloneNode(TRUE);

        $imported = $this->dom->importNode($cloned, TRUE);

        return $imported;
    }

    /**
     * Encode special characters into HTML entities. Unless the <> characters.
     * 
     * @param string $text
     * @return string
     */
    private function convert($text) {
        $text = htmlentities($text, ENT_NOQUOTES, "UTF-8");
        $text = htmlspecialchars_decode($text);
        return $text;
    }

    /**
     * If the parent key is found, return the key, otherwise false. 
     * 
     * @param string $key Child key
     * @param int $reverseIndex Number of levels to rise.
     * @return mixed Parent key or false 
     */
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
     * Build a string with 
     * 
     * @param SdiRelation $rel
     * @return string
     */
    private function getFieldsetBody(SdiRelation $rel) {

        $languages = $this->ldao->get();

        $html = '';

        $debug = $rel->lowerbound . ' ' . $rel->upperbound;
        $debug = $rel->level;

        switch ($rel->getAttribut_child()->getStereotype()->value) {
            case 'locale':
                foreach ($languages as $key => $value) {
                    $jfield = $this->form->getField($rel->getSerializedXpath() . '#' . $key);
                    if ($jfield) {
                        $html .= $debug . ' ' . $this->buildField($jfield, $rel->getAttribut_child()->guid);
                    } else {
                        $html .= '<div>Champ ' . $rel->getAttribut_child()->getStereotype()->value . ' ' . $rel->getSerializedXpath() . '#' . $key . 'non trouvé!</div>';
                    }
                }
                break;

            default:
                $jfield = $this->form->getField($rel->getSerializedXpath());
                if ($jfield) {
                    $html .= $debug . ' ' . $this->buildField($jfield, $rel->getAttribut_child()->guid);
                } else {
                    $html .= '<div>Champ ' . $rel->getAttribut_child()->getStereotype()->value . ' ' . $rel->getSerializedXpath() . ' non trouvé!</div>';
                }
                break;
        }

        return $html;
    }

    /**
     * 
     * @param JFile $field
     * @param string $guid
     * @return string
     */
    private function buildField($field, $guid) {
        $html = '';

        $html .= '<div><div class="control-group">
                        <div class="control-label">' . $field->label . '</div>
                        <div class="controls">' . $field->input . '</div>
                    </div>';

        $html .="<script type='text/javascript'>
                    js = jQuery.noConflict();

                    js('document').ready(function() {
                        js('#" . $field->id . "').tooltip({'trigger':'focus', 'title': '" . addslashes(EText::_($guid, 2)) . "'});
                    });
                </script></div>";

        return $html;
    }

}

?>

<?php

require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/dao/SdiNamespaceDao.php';

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
     * @var DOMDocument
     */
    private $structure;

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

    /**
     *
     * @var DOMDocument 
     */
    private $formHtml;

    /**
     *
     * @var DOMXPath 
     */
    private $domXpathStr;

    /**
     *
     * @var DOMXPath 
     */
    private $domXpathFormHtml;
    /**
     *
     * @var string 
     */
    private $ajaxXpath;
    private $catalog_uri = 'http://www.easysdi.org/2011/sdi/catalog';
    private $catalog_prefix = 'catalog';

    function __construct(JForm $form, DOMDocument $structure, $ajaxXpath = null) {
        $this->form = $form;
        $this->structure = $structure;
        $this->ldao = new SdiLanguageDao();
        $this->nsdao = new SdiNamespaceDao();
        $this->formHtml = new DOMDocument(null, 'utf-8');
        $this->ajaxXpath = $ajaxXpath;
    }

    /**
     * 
     * @return string
     */
    public function buildForm() {
        $this->domXpathStr = new DOMXPath($this->structure);
        $this->domXpathFormHtml = new DOMXPath($this->formHtml);

        foreach ($this->nsdao->getAll() as $ns) {
            $this->domXpathStr->registerNamespace($ns->prefix, $ns->uri);
        }

        if(isset($this->ajaxXpath)){
            $root = $this->domXpathStr->query($this->ajaxXpath)->item(0);
            $rootFieldset = $this->getFieldset($root);
        }  else {
            $root = $this->domXpathStr->query('/*')->item(0);
            $rootFieldset = $this->formHtml->createElement('fieldset');
        }
        
        $this->structure->formatOutput = true;
        $xml = $this->structure->saveXML();
        
        $this->formHtml->appendChild($rootFieldset);
        
        $this->recBuildForm($root, $rootFieldset);

        $this->formHtml->formatOutput = true;
        $html = $this->formHtml->saveHTML();

        return $html;
    }

    private function recBuildForm(DOMElement $parent, DOMElement $parentHtml) {
        switch ($parent->parentNode->nodeType) {
            case XML_DOCUMENT_NODE:
                $query = '*[@catalog:childtypeId="0"]|*[@catalog:childtypeId="2"]|*[@catalog:childtypeId="3"]';
                $parentInner = $parentHtml;
                break;
            case XML_ELEMENT_NODE:
                $query = '*/*[@catalog:childtypeId="0"]|*/*[@catalog:childtypeId="2"]|*/*[@catalog:childtypeId="3"]|*[@catalog:childtypeId="2"]';
                $parentInner = $parentHtml->getElementsByTagName('div')->item(0);
                break;
        }

        foreach ($this->domXpathStr->query($query, $parent) as $child) {

            switch ($child->getAttributeNS($this->catalog_uri, 'childtypeId')) {
                case EnumChildtype::$RELATION:
                    if (($child->getAttributeNS($this->catalog_uri, 'lowerbound')-$child->getAttributeNS($this->catalog_uri, 'upperbound'))!=0 && $child->getAttributeNS($this->catalog_uri, 'index') == 1) {
                        $action = $this->getAction($child);
                        $parentInner->appendChild($action);
                    }
                    $fieldset = $this->getFieldset($child);

                    $parentInner->appendChild($fieldset);

                    if ($this->domXpathStr->query('*/*[@catalog:childtypeId="0"]|*/*[@catalog:childtypeId="2"]|*[@catalog:childtypeId="2"]', $child)->length > 0) {
                        $this->recBuildForm($child, $fieldset);
                    }
                    break;
                case EnumChildtype::$ATTRIBUT:
                    
                    $fields = $this->getAttribute($child);
                    
                    foreach ($fields as $field) {
                        $parentInner->appendChild($field);
                    }
                    
                    break;
                case EnumChildtype::$RELATIONTYPE:
                    if ($child->getAttributeNS($this->catalog_uri, 'index') == 1) {
                        $action = $this->getAction($child);
                        $parentInner->appendChild($action);
                    }
                    $searchFields = $this->getAttribute($child);
                    $fieldset = $this->getFieldset($child);

                    foreach ($searchFields as $searchField) {
                        $fieldset->getElementsByTagName('div')->item(0)->appendChild($searchField);
                    }

                    $parentInner->appendChild($fieldset);

                    if ($this->domXpathStr->query('*[@catalog:childtypeId="2"]', $child)->length > 0) {
                        $this->recBuildForm($child, $fieldset);
                    }
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
    private function getAction(DOMElement $relation) {

        $lowerbound = $relation->getAttributeNS($this->catalog_uri, 'lowerbound');
        $upperbound = $relation->getAttributeNS($this->catalog_uri, 'upperbound');
        switch ($relation->getAttributeNS($this->catalog_uri, 'childtypeId')) {
            case EnumChildtype::$RELATIONTYPE:
                $relid = $relation->getAttributeNS($this->catalog_uri, 'relationId');
                break;

            default:
                $relid = $relation->getAttributeNS($this->catalog_uri, 'dbid');
                break;
        }
        

        $occurance = $this->domXpathStr->query($this->removeIndex($relation->getNodePath()))->length;

        //$debug = '[oc:' . $occurance . ' lb:' . $lowerbound . ' ub:' . $upperbound . '] [' . $relation->getNodePath() . ']';
        $debug = '[oc:' . $occurance . ' lb:' . $lowerbound . ' ub:' . $upperbound . ']';

        $aAdd = $this->formHtml->createElement('a');
        $aAdd->setAttribute('id', 'add-btn-' . $this->serializeXpath($relation->getNodePath()));
        $aAdd->setAttribute('class', 'btn btn-success btn-mini add-btn add-btn-' . $this->serializeXpath($this->removeIndex($relation->getNodePath())));
        $aAdd->setAttribute('onclick', 'addFieldset(this.id, \''. $this->serializeXpath($this->removeIndex($relation->getNodePath())) .'\',' . $relid . ', \'' . $this->serializeXpath($relation->parentNode->getNodePath()) . '\' ,' . $lowerbound . ',' . $upperbound . ')');
        
        if ($upperbound <= $occurance) {
            $aAdd->setAttribute('style', 'display:none;');
        }

        $iAdd = $this->formHtml->createElement('i');
        $iAdd->setAttribute('class', 'icon-white icon-plus-2');

        $divOuter = $this->formHtml->createElement('div');
        $divOuter->setAttribute('id', 'outer-fds-' . $this->serializeXpath($relation->getNodePath()));
        $divOuter->setAttribute('class', 'outer-level outer-fds-' . $this->serializeXpath($relation->getNodePath()));

        $divAction = $this->formHtml->createElement('div', EText::_($relation->getAttributeNS($this->catalog_uri, 'id')) . ' ' . $debug);
        $divAction->setAttribute('class', 'action-level');

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
    private function getFieldset(DOMElement $element) {
        $lowerbound = $element->getAttributeNS($this->catalog_uri, 'lowerbound');
        $upperbound = $element->getAttributeNS($this->catalog_uri, 'upperbound');
        $occurance = $this->domXpathStr->query($this->removeIndex($element->getNodePath()))->length;
        $index = $element->getAttributeNS($this->catalog_uri, 'index');

        $aCollapse = $this->formHtml->createElement('a');
        $aCollapse->setAttribute('id', 'collapse-btn-' . $this->serializeXpath($element->getNodePath()));
        $aCollapse->setAttribute('class', 'btn btn-mini collapse-btn');
        $aCollapse->setAttribute('onclick', 'collapse(this.id)');

        $iCollapse = $this->formHtml->createElement('i');
        $iCollapse->setAttribute('class', 'icon-white icon-arrow-down');

        $aRemove = $this->formHtml->createElement('a');
        $aRemove->setAttribute('id', 'remove-btn-' . $this->serializeXpath($element->getNodePath()));
        $aRemove->setAttribute('class', 'btn btn-danger btn-mini pull-right remove-btn-' . $this->serializeXpath($this->removeIndex($element->getNodePath())));
        $aRemove->setAttribute('onclick', 'confirm(this.id, \'' . $this->serializeXpath($this->removeIndex($element->getNodePath())) . '\' , ' . $lowerbound . ',' . $upperbound . ')');

        $iRemove = $this->formHtml->createElement('i');
        $iRemove->setAttribute('class', 'icon-white icon-cancel-2');

        $divOuter = $this->formHtml->createElement('div');
        $divOuter->setAttribute('id', 'outer-fds-' . $this->serializeXpath($element->getNodePath()));
        $divOuter->setAttribute('class', 'outer-' . 0 . ' outer-fds-' . $this->serializeXpath($this->removeIndex($element->getNodePath())));

        $fieldset = $this->formHtml->createElement('fieldset');
        $fieldset->setAttribute('id', 'fds-' . $this->serializeXpath($element->getNodePath()));

        $spanLegend = $this->formHtml->createElement('span', EText::_($element->getAttributeNS($this->catalog_uri, 'id')));
        $spanLegend->setAttribute('class', 'legend-' . 0);
        $legend = $this->formHtml->createElement('legend');

        $divInner = $this->formHtml->createElement('div');
        $divInner->setAttribute('id', 'inner-fds-' . $this->serializeXpath($element->getNodePath()));
        $divInner->setAttribute('class', 'inner-fds');
        if (!isset($_GET['relid'])) {
            $divInner->setAttribute('style', 'display:none;');
        }

        $divBottom = $this->formHtml->createElement('div');
        $divBottom->setAttribute('id', 'bottom-' . $this->serializeXpath($this->removeIndex($element->getNodePath())));


        $aCollapse->appendChild($iCollapse);
        $legend->appendChild($aCollapse);
        $legend->appendChild($spanLegend);
        if ($lowerbound < $occurance) {
            $aRemove->appendChild($iRemove);
            $legend->appendChild($aRemove);
        }
        $fieldset->appendChild($legend);
        $fieldset->appendChild($divInner);

        $divOuter->appendChild($fieldset);
        if ($index == $occurance) {
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
    private function getAttribute(DOMElement $attribute) {
        $source = $this->getFieldsetBody($attribute);

        $domlocal = new DOMDocument();
        $domlocal->loadHTML($this->convert($source));
        
        $html = $domlocal->saveHTML();
        
        $domXapth = new DOMXPath($domlocal);
        $elements = $domXapth->query('/*/*/*');

        $importeds = array();
        foreach ($elements as $element) {
            $cloned = $element->cloneNode(TRUE);

            $imported = $this->formHtml->importNode($cloned, TRUE);
            $importeds[] = $imported;
        }

        return $importeds;
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
     * Build a string with 
     * 
     * @param SdiRelation $rel
     * @return string
     */
    private function getFieldsetBody(DOMElement $attribute) {

        $languages = $this->ldao->getSupported();

        $html = '';

        /* $debug = $rel->lowerbound . ' ' . $rel->upperbound;
          $debug = $rel->level; */
        $debug = '';

        switch ($attribute->getAttributeNS($this->catalog_uri, 'stereotypeId')) {
            case EnumStereotype::$LOCALE:
                $nodePath = $attribute->getNodePath();
                $jfield = $this->form->getField($this->serializeXpath($nodePath));
                $html .= $debug . ' ' . $this->buildField($jfield, $attribute->getAttributeNS($this->catalog_uri, 'id'));

                foreach ($languages as $key => $value) {
                    $jfield = $this->form->getField($this->serializeXpath($attribute->getNodePath() . '/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString#' . $key));
                    if ($jfield) {
                        $html .= $debug . ' ' . $this->buildField($jfield, $attribute->getAttributeNS($this->catalog_uri, 'id'));
                    } else {
                        $html .= '<div>Champ ' . $attribute->getAttributeNS($this->catalog_uri, 'id') . 'non trouvé!</div>';
                    }
                }
                break;

            default:
                $jfield = $this->form->getField($this->serializeXpath($attribute->getNodePath()));
                if ($jfield) {
                    $html .= $debug . ' ' . $this->buildField($jfield, $attribute->getAttributeNS($this->catalog_uri, 'id'));
                } else {
                    $html .= '<div>Champ ' . $attribute->getAttributeNS($this->catalog_uri, 'id') . ' non trouvé!</div>';
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

        $html .= '<div class="control-group">
                        <div class="control-label">' . $field->label . '</div>
                        <div class="controls">' . $field->input . '</div>
                    </div>';

        $html .="<script type='text/javascript'>
                    js = jQuery.noConflict();

                    js('document').ready(function() {
                        js('#" . $field->id . "').tooltip({'trigger':'focus', 'title': '" . addslashes(EText::_($guid, 2)) . "'});
                    });
                </script>";

        return $html;
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

    /**
     * 
     * @param string $xpath
     * @return string
     */
    private function removeIndex($xpath) {
        return preg_replace('/[\[0-9\]*]/i', '', $xpath);
    }

}

?>

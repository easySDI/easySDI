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

    /**
     * Construit le formulaire HTML en ajoutant l'inclusion des fieldset
     * 
     * @author Marc Battaglia <marc.battaglia@depth.ch>
     * @return string Description
     * @since 4.0
     */
    /* public function buildForm() {

      $this->getDomElements();

      $html = '';
      $lastRel = new SdiRelation(0, 'dummy', 1);

      foreach ($this->classTree as $rel) {
      switch ($rel->childtype_id) {
      case SdiRelation::$CLASS:

      if ($rel->level > $lastRel->level || $rel->level == 0) {
      if ($lastRel->childtype_id == SdiRelation::$RELATIONTYPE) {
      $html .= $this->getFieldsetFooter($rel);
      }
      $html .= $this->getActionHeader($rel);
      $html .= $this->getFieldsetHeader($rel);
      }

      if ($rel->level < $lastRel->level) {
      if ($lastRel->childtype_id == SdiRelation::$RELATIONTYPE) {
      $html .= $this->getFieldsetFooter($rel);
      }
      for ($i = 0; $i <= $lastRel->level - $rel->level; $i++) {
      $html .= $this->getFieldsetFooter($rel);
      }
      $html .= $this->getActionHeader($rel);
      $html .= $this->getFieldsetHeader($rel);
      }

      if ($rel->level == $lastRel->level && $rel->level != 0) {
      $html .= $this->getFieldsetFooter($rel);
      $html .= $this->getActionHeader($rel);
      $html .= $this->getFieldsetHeader($rel);
      }

      $lastRel = $rel;
      break;

      case SdiRelation::$ATTRIBUT:

      $html .= $this->getFieldsetBody($rel);

      break;

      case SdiRelation::$RELATIONTYPE:
      if ($lastRel->childtype_id == SdiRelation::$RELATIONTYPE) {
      $html .= $this->getFieldsetFooter($rel);
      }
      $html .= $this->getActionHeader($rel);
      $html .= $this->getFieldsetHeader($rel);

      $lastRel = $rel;
      break;
      default:
      break;
      }
      }

      for ($i = 0; $i <= $lastRel->level; $i++) {
      $html .= $this->getFieldsetFooter($rel);
      }

      return $html;
      } */

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

    private function getAction(SdiRelation $rel) {
        $imgAdd = $this->dom->createElement('img');
        $imgAdd->setAttribute('id', 'add-btn-' . $rel->getSerializedXpath());
        $imgAdd->setAttribute('class', 'add-btn-' . $rel->getSerializedXpath(false));
        $imgAdd->setAttribute('src', JUri::base(TRUE) . '/administrator/components/com_easysdi_catalog/assets/images/circle_plus.png');
        $imgAdd->setAttribute('onclick', 'addFieldset(this.id, \'' . $rel->getSerializedXpath(false) . '\' ,' . $rel->lowerbound . ',' . $rel->upperbound . ')');
        $imgAdd->setAttribute('height', '15');
        $imgAdd->setAttribute('width', '15');
        if ($rel->upperbound <= $rel->occurance) {
            $imgAdd->setAttribute('style', 'display:none;');
        }

        $divOuter = $this->dom->createElement('div');
        $divOuter->setAttribute('id', 'outer-fds-' . $rel->getSerializedXpath());
        $divOuter->setAttribute('class', 'outer-' . $rel->level . ' outer-fds-' . $rel->getSerializedXpath(false));

        $divAction = $this->dom->createElement('div', EText::_($rel->guid));
        $divAction->setAttribute('class', 'action-' . $rel->level);

        $divAction->appendChild($imgAdd);

        return $divAction;
    }

    private function getFieldset(SdiRelation $rel) {

        $imgCollapse = $this->dom->createElement('img');
        $imgCollapse->setAttribute('id', 'collapse-btn-' . $rel->getSerializedXpath());
        $imgCollapse->setAttribute('class', 'collapse-btn');
        $imgCollapse->setAttribute('src', JUri::base(TRUE) . '/administrator/components/com_easysdi_catalog/assets/images/expand.png');
        $imgCollapse->setAttribute('onclick', 'collapse(this.id)');
        $imgCollapse->setAttribute('height', '15');
        $imgCollapse->setAttribute('width', '15');

        $imgRemove = $this->dom->createElement('img');
        $imgRemove->setAttribute('id', 'remove-btn-' . $rel->getSerializedXpath());
        $imgRemove->setAttribute('class', 'remove-btn-' . $rel->getSerializedXpath(false));
        $imgRemove->setAttribute('src', JUri::base(TRUE) . '/administrator/components/com_easysdi_catalog/assets/images/circle_minus.png');
        $imgRemove->setAttribute('onclick', 'removeFieldset(this.id, \'' . $rel->getSerializedXpath(false) . '\' , ' . $rel->lowerbound . ',' . $rel->upperbound . ')');
        $imgRemove->setAttribute('height', '15');
        $imgRemove->setAttribute('width', '15');

        $divOuter = $this->dom->createElement('div');
        $divOuter->setAttribute('id', 'outer-fds-' . $rel->getSerializedXpath());
        $divOuter->setAttribute('class', 'outer-' . $rel->level . ' outer-fds-' . $rel->getSerializedXpath(false));

        $fieldset = $this->dom->createElement('fieldset');
        $fieldset->setAttribute('id', 'fds-' . $rel->getSerializedXpath());

        $spanLegend = $this->dom->createElement('span', EText::_($rel->guid));
        $legend = $this->dom->createElement('legend');

        $divInner = $this->dom->createElement('div');
        $divInner->setAttribute('id', 'inner-fds-' . $rel->getSerializedXpath());
        $divInner->setAttribute('class', 'inner-fds');
        if (!isset($_GET['uuid'])) {
            $divInner->setAttribute('style', 'display:none;');
        }

        if (!$rel->getClass_child()->isRoot) {
            $legend->appendChild($imgCollapse);
            $legend->appendChild($spanLegend);
            if ($rel->lowerbound < $rel->occurance) {
                $legend->appendChild($imgRemove);
            }
            $fieldset->appendChild($legend);
            $fieldset->appendChild($divInner);
        }
        $divOuter->appendChild($fieldset);

        return $divOuter;
    }

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

    private function convert($text) {
        $text = htmlentities($text, ENT_NOQUOTES, "UTF-8");
        $text = htmlspecialchars_decode($text);
        return $text;
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

    private function getActionHeader(SdiRelation $rel) {
        $img_add_relation = '<img onclick="addFieldset(this.id, \'' . $rel->getSerializedXpath(false) . '\' ,' . $rel->lowerbound . ',' . $rel->upperbound . ');" class="add-btn-' . $rel->getSerializedXpath(false) . '" id="add-btn-' . $rel->getSerializedXpath() . '" src="' . JUri::base(TRUE) . '/administrator/components/com_easysdi_catalog/assets/images/circle_plus.png" height="15" width="15" style="display:none;"/>';

        $html = '';

        if ($rel->upperbound > $rel->occurance) {
            $img_add_relation = '<img onclick="addFieldset(this.id, \'' . $rel->getSerializedXpath(false) . '\' ,' . $rel->lowerbound . ',' . $rel->upperbound . ');" class="add-btn-' . $rel->getSerializedXpath(false) . '" id="add-btn-' . $rel->getSerializedXpath() . '" src="' . JUri::base(TRUE) . '/administrator/components/com_easysdi_catalog/assets/images/circle_plus.png" height="15" width="15" />';
        }

        if ($rel->getIndex() == 0 && $rel->level != 0) {
            $html .= '<div class="action-' . $rel->level . '">' . EText::_($rel->guid) . $img_add_relation . '</div>';
            $html .= '<div class="outer-fds-' . $rel->getSerializedXpath(false) . '"></div>';
        }
        return $html;
    }

    private function getFieldsetHeader(SdiRelation $rel) {

        $img_collapse = '<img onclick="collapse(this.id)" class="collapse-btn" id="collapse-btn-' . $rel->getSerializedXpath() . '" src="' . JUri::base(TRUE) . '/administrator/components/com_easysdi_catalog/assets/images/expand.png" height="15" width="15"/>';
        $img_remove_relation = '<img onclick="removeFieldset(this.id, \'' . $rel->getSerializedXpath(false) . '\' , ' . $rel->lowerbound . ',' . $rel->upperbound . ')" class="remove-btn-' . $rel->getSerializedXpath(false) . '" id="remove-btn-' . $rel->getSerializedXpath() . '" src="' . JUri::base(TRUE) . '/administrator/components/com_easysdi_catalog/assets/images/circle_minus.png" height="15" width="15"/>';

        $debug = ' ' . $rel->lowerbound . ' ' . $rel->upperbound;

        $debug .= ' ' . $rel->name;

        $debug .= ' ' . $rel->level;

        $html = '';
        $html .= '<div id="outer-fds-' . $rel->getSerializedXpath() . '" class="outer-' . $rel->level . ' outer-fds-' . $rel->getSerializedXpath(false) . '">';

        if ($rel->childtype_id == SdiRelation::$CLASS) {
            if ($rel->getClass_child()->isRoot) {
                $html .= '<fieldset>';
                $html .= '<div>';
                return $html;
            }
        }

        $actions = '';
        if ($rel->lowerbound < $rel->occurance) {
            $actions = $img_remove_relation;
        }


        $html .= '<fieldset id="fds-' . $rel->getSerializedXpath() . '"><legend> ' . $img_collapse . EText::_($rel->guid) . ' [' . $rel->getIndex() . ']' . $actions . $debug . ' </legend>';

        if (isset($_GET['uuid'])) {
            $html .= '<div class="inner-fds" id="inner-fds-' . $rel->getSerializedXpath() . '">';
        } else {
            $html .= '<div class="inner-fds" id="inner-fds-' . $rel->getSerializedXpath() . '" style="display:none;">';
        }


        return $html;
    }

    private function getFieldsetFooter(SdiRelation $rel) {
        $html = '';
        $html .= '</div>';
        $html .= '</fieldset>';
        $html .= '</div>';


        return $html;
    }

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

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

    function __construct(JForm $form, $classTree) {
        $this->form = $form;
        $this->classTree = $classTree;
        $this->ldao = new SdiLanguageDao();
    }

    /**
     * Construit le formulaire HTML en ajoutant l'inclusion des fieldset
     * 
     * @author Marc Battaglia <marc.battaglia@depth.ch>
     * @return string Description
     * @since 4.0
     */
    public function buildForm() {

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

}

?>

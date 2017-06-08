<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
class SdiToolbar {

    /** @var DOMDocument */
    private $dom;

    function __construct() {
        $this->dom = new DOMDocument('1.0', 'utf-8');

        $toolbar = $this->dom->createElement('div');
        $toolbar->setAttribute('class', 'btn-toolbar');

        $this->dom->appendChild($toolbar);
    }

    public function append($label, $id = '', $btnClass = '', $action = '', $dropdown = false, $iconClass = null) {
        $button = $this->dom->createElement('button');

        //add icon if needed
        if (isset($iconClass)) {
            $icon = $this->dom->createElement('i');
            $icon->setAttribute('class', 'icon ' . $iconClass);
            $button->appendChild($icon);
        }

        // set text, add spaces in case of icon and dropdown
        $buttonText = $this->dom->createTextNode((isset($iconClass) ? ' ' : '') . $label . ($dropdown ? ' ' : ''));
        $button->appendChild($buttonText);

        $button->setAttribute('id', $id);



        if ($dropdown) {
            $button->setAttribute('class', 'btn dropdown-toggle ' . $btnClass);
            $button->setAttribute('data-toggle', 'dropdown');
            $caret = $this->dom->createElement('span');
            $caret->setAttribute('class', 'caret');
            $button->appendChild($caret);

            $buttonGroup = $this->dom->createElement('div');
            $buttonGroup->setAttribute('class', 'btn-group');

            $ul = $this->dom->createElement('ul');
            $ul->setAttribute('class', 'dropdown-menu');
            $ul->setAttribute('role', 'menu');

            foreach ($action as $menuLabel => $menuAction) {
                if (is_array($menuAction)) {
                    $menuRel = $menuAction[1];
                    $menuAction = $menuAction[0];
                }


                $li = $this->dom->createElement('li');
                $a = $this->dom->createElement('a');
                $textFragment = $this->dom->createDocumentFragment();
                $textFragment->appendXML($menuLabel);
                $a->appendChild($textFragment);
                $a->setAttribute('onclick', 'Joomla.submitbutton(\'' . $menuAction . '\', this.rel)');
                $a->setAttribute('style', 'cursor:pointer');
                if (isset($menuRel)) {
                    $a->setAttribute('rel', $menuRel);
                }
                $li->appendChild($a);
                $ul->appendChild($li);
            }

            $buttonGroup->appendChild($button);
            $buttonGroup->appendChild($ul);
            $this->dom->firstChild->appendChild($buttonGroup);
        } else {
            $button->setAttribute('class', 'btn ' . $btnClass);
            $button->setAttribute('onclick', 'Joomla.submitbutton(\'' . $action . '\')');
            $this->dom->firstChild->appendChild($button);
        }
    }

    public function appendBtnRoute($label, $url, $btnClass = '', $id = '', $rel = '', $iconClass = null) {

        $a = $this->dom->createElement('a');

        //add icon if needed
        if (isset($iconClass)) {
            $icon = $this->dom->createElement('i');
            $icon->setAttribute('class', 'icon ' . $iconClass);
            $a->appendChild($icon);
        }

        // set text, add spaces in case of icon and dropdown
        $aText = $this->dom->createTextNode((isset($iconClass) ? ' ' : '') . $label);
        $a->appendChild($aText);

        $a->setAttribute('class', 'btn ' . $btnClass);
        $a->setAttribute('id', $id);
        $a->setAttribute('href', $url);
        $a->setAttribute('rel', $rel);

        $this->dom->firstChild->appendChild($a);
    }

    public function renderToolbar() {
        $this->dom->formatOutput = true;

        return $this->dom->saveHTML();
    }

}

?>

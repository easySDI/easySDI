<?php

JFormHelper::loadFieldClass('list');

/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_PLATFORM') or die;

class JFormFieldMultipleDefaultList extends JFormFieldList {

    protected function getInput() {
        if ($this->multiple) {
            $this->value = explode(',', $this->value);
        }

        return parent::getInput();
    }

}

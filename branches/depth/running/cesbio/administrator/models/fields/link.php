<?php
/**
 * @version     3.0.0
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.form.formfield');
 
class JFormFieldLink extends JFormField {
 
	protected $type = 'link';
 
	public function getInput() {
		return '<a href="index.php?option='.$this->element['component'].'&amp;view='.$this->element['name'].'">'.JText::_($this->element['label']).'</a>';
	}
}
<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.form.formfield');
 
class JFormFieldLink extends JFormField {
 
	protected $type = 'link';
 
	public function getInput() {
		return '<a href="index.php?option='.$this->element['component'].'&amp;view='.$this->element['name'].'">'.JText::_($this->element['label']).'</a>';
	}
}
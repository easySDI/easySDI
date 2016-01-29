<?php
/**
 * @version		4.4.0
 * @package     com_easysdi_core
 * @copyright	
 * @license		
 * @author		
 */

defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.form.formfield');
 
class JFormFieldLink extends JFormField {
 
	protected $type = 'link';
 
	public function getInput() {
		if(isset($this->element['extension']))
			return '<a href="index.php?option='.$this->element['component'].'&amp;extension='.$this->element['extension'].'">'.JText::_($this->element['label']).'</a>';
		else 
			return '<a href="index.php?option='.$this->element['component'].'&amp;view='.$this->element['name'].'">'.JText::_($this->element['label']).'</a>';
	}
}
<?php
defined('_JEXEC') or die('Restricted access');

class ADMIN_ctrlpanel {
	function ctrlPanelCore($option){
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$panels = modulePanel::loadModulePanels($db);
		
		HTML_ctrlpanel::ctrlPanelCore($option, $panels);
	}
}
?>
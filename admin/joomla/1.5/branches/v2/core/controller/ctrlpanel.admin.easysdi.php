<?php
defined('_JEXEC') or die('Restricted access');

class ADMIN_ctrlpanel {
	function ctrlPanelCore($option){
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$query = "SELECT count(*) FROM #__sdi_configuration c, #__sdi_list_module m WHERE c.module_id=m.id AND m.code='CATALOG'";
		$db->setQuery( $query );
		$catalogExist = $db->loadResult();

		$query = "SELECT count(*) FROM #__sdi_configuration c, #__sdi_list_module m WHERE c.module_id=m.id AND m.code='PROXY'";
		$db->setQuery( $query );
		$proxyExist = $db->loadResult();
		
		$query = "SELECT count(*) FROM  #__sdi_list_module m WHERE  m.code='SHOP'";
		$db->setQuery( $query );
		$shopExist = $db->loadResult();
		
		$panels = modulePanel::loadModulePanels($db);
		
		HTML_ctrlpanel::ctrlPanelCore($option, $catalogExist, $proxyExist, $shopExist,$panels);
	}
}
?>
<?php
defined('_JEXEC') or die('Restricted access');

function com_install()
{
	global  $mainframe;
	$db =& JFactory::getDBO();

	/**
	 * Check the CORE installation
	 */
	$count = 0;
	$query = "SELECT COUNT(*) FROM `#__components` WHERE `option` ='com_easysdi_core'";
	$db->setQuery( $query);
	$count = $db->loadResult();
	if ($count == 0) {
		$mainframe->enqueueMessage("Core component does not exist. Easysdi Map could not be installed. Please install core component first.","ERROR");
		return false;
	}

	/**
	 * Gets the component versions
	 */

	$db->setQuery( "SELECT version FROM #__easysdi_version where component = 'com_easysdi_map'");
	$version = $db->loadResult();
	if (!$version)
	{
		$version="1.0";
		$query =
		"
		
		";

		$db->setQuery( $query);
		if (!$db->query())		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

		/**
			* Update version
			* */
		$query="UPDATE #__easysdi_version SET version ='$version' where component = 'com_easysdi_map'";
		$db->setQuery( $query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
	}

	/**
	 * Menu creation
	 */
	$query =  "SELECT ID FROM #__components WHERE name ='Easy SDI'" ;
	$db->setQuery( $query);
	$id = $db->loadResult();
	if ($id)
	{
	}
	else
	{
		$mainframe->enqueueMessage("EASYSDI menu was not installed. Usually this menu is created during the installation of the easysdi core component. Please be sure that the easysdi_core component is installed before installing this component.","ERROR");
		return false;
	}

	$query = "DELETE FROM #__components where `option`= 'com_easysdi_map'";
	$db->setQuery( $query);
	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		return false;
	}

	$query =  "insert into #__components (parent, name,admin_menu_link, link,admin_menu_alt,`option`,admin_menu_img,params)
		values($id, 'Map','option=com_easysdi_map','option=com_easysdi_map', 'Easysdi Map','com_easysdi_map','js/ThemeOffice/component.png','')";
	$db->setQuery( $query);
	if (!$db->query())
	{
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
	}

	return true;
}
?>
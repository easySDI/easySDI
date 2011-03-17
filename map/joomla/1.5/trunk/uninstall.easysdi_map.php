<?php
defined('_JEXEC') or die('Restricted access');

function com_uninstall()
{
	global  $mainframe;
	$db =& JFactory::getDBO();
	/**
	 * Delete components
	 */
	$query = "DELETE FROM #__components where `option`= 'com_easysdi_map'";
	$db->setQuery( $query);
	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		return false;
	}
	$mainframe->enqueueMessage("The Map component for EasySdi is now removed from your system. We are sorry to see you go!","INFO");
	return true;
}
?>
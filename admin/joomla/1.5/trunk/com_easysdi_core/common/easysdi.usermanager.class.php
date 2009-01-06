<?php
defined('_JEXEC') or die('Restricted access');

class usermanager
{
	static function isEasySDIUser ($user)
	{
		$database =& JFactory::getDBO(); 
		$database->setQuery( "SELECT COUNT(*) FROM #__easysdi_community_partner WHERE user_id=".$user->id);
		$result = $database->loadResult();
		if($result == 1)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}
?>
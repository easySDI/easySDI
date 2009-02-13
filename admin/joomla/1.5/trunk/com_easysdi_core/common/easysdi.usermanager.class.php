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
	
	function hasRight($partner_id,$right){
				
		$database =& JFactory::getDBO();		
		
		$query = "SELECT count(*) FROM #__easysdi_community_actor a , #__easysdi_community_role b  WHERE a.role_id = b.role_id and partner_id = $partner_id and role_code = '$right'";
		
				
		$database->setQuery($query );
		$total = $database->loadResult();
		//echo "renaud : ".$query;
		return ($total > 0 );
	}
	
	function isUserAllowed ($user, $right)
	{
		global  $mainframe;
		
		if ($user->guest){
			$mainframe->enqueueMessage(JText::_("EASYSDI_ACCOUNT_NOT_CONNECTED"),"INFO");			
			return false;
		}
		if(!usermanager::isEasySDIUser($user))
		{
			$mainframe->enqueueMessage(JText::_("EASYSDI_NOT_CONNECTED_AS_EASYSDI_USER"),"INFO");
			return false;
		}
		
		$db =& JFactory::getDBO();
		$rowPartner = new partnerByUserId( $db );
		$rowPartner->load( $user->id );
		if(!usermanager::hasRight($rowPartner->partner_id,$right))
		{
			$mainframe->enqueueMessage(JText::_("EASYSDI_NOT_ALLOWED_TO_MANAGE_METADATA"),"INFO");
			return false;
		}
		return true;
	}
}
?>
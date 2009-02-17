<?php
defined('_JEXEC') or die('Restricted access');

class userManager
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
	
	static function hasRight($partner_id,$right){
				
		$database =& JFactory::getDBO();		
		
		$query = "SELECT count(*) 
				  FROM #__easysdi_community_actor a ,
				  	   #__easysdi_community_role b  
				  WHERE a.role_id = b.role_id 
				  and partner_id = $partner_id 
				  and role_code = '$right'";
		
				
		$database->setQuery($query );
		$total = $database->loadResult();
		//echo "renaud : ".$query;
		return ($total > 0 );
	}
	
	static function isUserAllowed ($user, $right)
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
			$database =& JFactory::getDBO();	
			$query = "SELECT role_name
					  FROM 
					  	   #__easysdi_community_role b  
					  WHERE 
					   role_code = '$right'";
			$database->setQuery($query );
			$role_name = $database->loadResult();
			$mainframe->enqueueMessage(JText::_("EASYSDI_NOT_ALLOWED_TO_MANAGE")." : ".JText::_($role_name),"INFO");
			return false;
		}
		return true;
	}
}
?>
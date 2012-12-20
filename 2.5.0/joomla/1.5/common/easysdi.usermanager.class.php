<?php
defined('_JEXEC') or die('Restricted access');

class userManager
{
	static function isEasySDIUser ($user)
	{
		$database =& JFactory::getDBO(); 
		$database->setQuery( "SELECT COUNT(*) FROM #__sdi_account WHERE user_id=".$user->id);
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
	
	
	
	static function hasRight($account_id,$right){
				
		$database =& JFactory::getDBO();		
		
		$query = "SELECT count(*) 
				  FROM #__sdi_actor a ,
				  	   #__sdi_list_role b  
				  WHERE a.role_id = b.id 
				  and account_id = $account_id 
				  and b.code = '$right'";
		
				
		$database->setQuery($query );
		$total = $database->loadResult();
		
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
		$rowAccount = new accountByUserId( $db );
		$rowAccount->load( $user->id );
		
		/*if (is_array($right))
			$rights= $right;
		else
			$rights[0]=$right;
		
		foreach ($rights as $right)
		{*/	
			if(!usermanager::hasRight($rowAccount->id,$right))
			{
				$database =& JFactory::getDBO();	
				$query = "SELECT name
						  FROM #__sdi_list_role  
						  WHERE code = '$right'";
				$database->setQuery($query );
				
				$role_name = $database->loadResult();
				
				$mainframe->enqueueMessage(JText::_("EASYSDI_NOT_ALLOWED_TO_MANAGE")." : ".JText::_($role_name),"INFO");
				return false;
			}
		//}
		return true;
	}
}
?>
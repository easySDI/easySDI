<?php
/**
 *  EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) EasySDI Community
 * For more information : www.easysdi.org
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or 
 * any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/gpl.html. 
 */

defined('_JEXEC') or die('Restricted access');

class ADMIN_systemaccount 
{
	
	function editSystemAccount ($id, $option,$code)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$account;
		if ($id)
		{
			$query = "SELECT  a.id as account_id, u.name, u.username, u.usertype 
					  FROM  #__users u INNER JOIN #__sdi_account a ON u.id = a.user_id 
					  WHERE a.id=$id";
			$query .= " LIMIT 1 ";
			$db->setQuery( $query);
			$accounts = $db->loadObjectList();
			$account = $accounts[0];
		}
		else
		{
			$query = "SELECT a.id as account_id, u.name, u.username, u.usertype 
						FROM #__sdi_systemaccount sa 
						INNER JOIN #__sdi_account a ON a.id = sa.account_id 
						INNER JOIN #__users u ON u.id = a.user_id
						WHERE sa.code='$code'";
			$query .= " LIMIT 1 ";
			$db->setQuery( $query);
			$accounts = $db->loadObjectList();
			$account = $accounts[0];
		}
		
		$query = "SELECT sa.*, u.name, u.username, u.usertype 
				  FROM #__sdi_systemaccount sa 
				  INNER JOIN #__sdi_account a ON a.id = sa.account_id
				  INNER JOIN #__users u ON u.id = a.user_id
				  WHERE sa.code='$code'";
		$query .= " LIMIT 1 ";
		$db->setQuery( $query);
		$systems_account = $db->loadObjectList();
		$system_account = $systems_account[0];
		
		if($system_account->id)
		{
			$system_account_ = new system_account($db);
			$system_account_->load($system_account->id);
			$system_account_->tryCheckOut($option,'');
		}
				
		//Get availaible easysdi account
		$db->setQuery( "SELECT a.id as value, u.name as text 
						FROM #__users u 
						INNER JOIN #__sdi_account a 
						ON u.id = a.user_id " );
		$rowsAccount = $db->loadObjectList();
		echo $db->getErrorMsg();		

		HTML_systemaccount::editSystemAccount($system_account,$account, $rowsAccount, $option,$code);
	}
	
	
	function saveSystemAccount($option)
	{
		global $mainframe;
		$db=& JFactory::getDBO(); 
				
		$system_account = new system_account ($db);
		
		if (!$system_account->bind( $_POST )) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");						
			$mainframe->redirect("index.php?option=$option&task=systemAccount" );
			exit();
		}		
				
		if (!$system_account->store()) 
		{			
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=systemAccount" );
			exit();
		}
		
		$system_account->checkin();
	}

}
?>